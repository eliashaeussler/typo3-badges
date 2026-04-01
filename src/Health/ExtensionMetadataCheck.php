<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021-2026 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Health;

use App\Entity\Dto\ExtensionMetadata;
use App\Service\ApiService;
use Exception;

use function array_key_exists;
use function explode;
use function gettype;
use function implode;
use function is_array;
use function is_scalar;
use function sprintf;
use function trim;

/**
 * ExtensionMetadataCheck.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class ExtensionMetadataCheck implements HealthCheck
{
    public function __construct(
        private ApiService $apiService,
    ) {}

    public function check(): HealthState
    {
        try {
            $metadata = $this->apiService->getExtensionMetadata('warming', true);
        } catch (Exception $exception) {
            return HealthState::fromException($exception);
        }

        return $this->assertIfValidOrReturnUnhealthyResponse(
            $metadata,
            [
                'composer' => ['0.meta.composer_name'],
                'download' => ['0.downloads'],
                'extension' => ['0.key', self::notEmpty(...)],
                'stability' => ['0.current_version.state', self::notEmpty(...)],
                'typo3 version' => ['0.current_version.typo3_versions', self::notEmpty(...)],
                'verified' => ['0.verified'],
                'extension version' => ['0.current_version.number', self::notEmpty(...)],
            ],
        ) ?? new HealthState(true);
    }

    /**
     * @param array<string, array{0: string, 1?: callable(mixed): bool}> $checks
     */
    private function assertIfValidOrReturnUnhealthyResponse(ExtensionMetadata $metadata, array $checks): ?HealthState
    {
        foreach ($checks as $name => $check) {
            $path = $check[0];
            $validator = $check[1] ?? static fn () => true;
            $pathSegments = explode('.', $path);
            $currentPath = [];
            $reference = $metadata->getMetadata();

            foreach ($pathSegments as $pathSegment) {
                $currentPath[] = $pathSegment;

                if (!array_key_exists($pathSegment, $reference)) {
                    return new HealthState(
                        false,
                        sprintf(
                            '%s: Expected path segment %s, but is missing in response.',
                            $name,
                            implode('.', $currentPath),
                        ),
                    );
                }

                $reference = $reference[$pathSegment];

                if (!is_array($reference) && $currentPath !== $pathSegments) {
                    return new HealthState(
                        false,
                        sprintf(
                            '%s: Expected array at path segment %s, but received %s.',
                            $name,
                            implode('.', $currentPath),
                            gettype($reference),
                        ),
                    );
                }
            }

            if (!$validator($reference)) {
                return new HealthState(
                    false,
                    sprintf('%s: Received value is invalid.', $name),
                );
            }
        }

        return null;
    }

    private static function notEmpty(mixed $value): bool
    {
        if (is_array($value)) {
            return [] !== $value;
        }

        if (is_scalar($value)) {
            return '' !== trim((string) $value);
        }

        /* @phpstan-ignore empty.notAllowed */
        return !empty($value);
    }

    public function getName(): string
    {
        return 'metadata';
    }
}
