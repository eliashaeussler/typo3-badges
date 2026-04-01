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

use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

use function sprintf;

/**
 * HealthState.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class HealthState
{
    public function __construct(
        public bool $healthy,
        public ?string $information = null,
    ) {}

    public static function fromResponse(ResponseInterface $response): self
    {
        try {
            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode) {
                return new self(false, sprintf('Got %d response: %s', $statusCode, $response->getContent(false)));
            }
        } catch (Throwable $exception) {
            return self::fromException($exception);
        }

        return new self(true);
    }

    public static function fromException(Throwable $exception): self
    {
        return new self(false, $exception->getMessage());
    }

    /**
     * @return array{healthy: bool, information?: string}
     */
    public function toArray(): array
    {
        $array = [
            'healthy' => $this->healthy,
        ];

        if (null !== $this->information) {
            $array['information'] = $this->information;
        }

        return $array;
    }
}
