<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021-2025 Elias Häußler <elias@haeussler.dev>
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

namespace App\Cache;

use App\Service\ApiService;
use Override;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * RandomExtensionMetadataCacheWarmer.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 *
 * @internal
 *
 * @codeCoverageIgnore
 */
final readonly class RandomExtensionMetadataCacheWarmer implements CacheWarmerInterface
{
    public function __construct(
        private ApiService $apiService,
    ) {}

    #[Override]
    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $this->apiService->getRandomExtensionMetadata();

        return [];
    }

    #[Override]
    public function isOptional(): bool
    {
        return true;
    }
}
