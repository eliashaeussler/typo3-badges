<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2022 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Tests;

use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * AbstractApiTestCase.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
abstract class AbstractApiTestCase extends KernelTestCase
{
    use MockClientTrait;

    protected CacheInterface $cache;
    protected ApiService $apiService;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->cache = self::getContainer()->get(CacheInterface::class);
        $this->apiService = new ApiService($this->getMockClient(), $this->cache);
    }

    /**
     * @param array<string, string> $options
     */
    protected function getCacheIdentifier(string $key, array $options = []): string
    {
        return hash('sha512', $key.'_'.json_encode($options, JSON_THROW_ON_ERROR));
    }
}
