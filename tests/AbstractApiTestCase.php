<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021 Elias Häußler <elias@haeussler.dev>
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
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * AbstractApiTestCase.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
abstract class AbstractApiTestCase extends KernelTestCase
{
    protected MockHttpClient $client;
    protected CacheInterface $cache;
    protected ApiService $apiService;

    /**
     * @var array<MockResponse>
     */
    protected array $mockResponses = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $this->client = new MockHttpClient($this->getMockResponses());
        $this->cache = self::getContainer()->get(CacheInterface::class);
        $this->apiService = new ApiService($this->client, $this->cache);
    }

    /**
     * @return \Generator<MockResponse>
     */
    protected function getMockResponses(): \Generator
    {
        yield from $this->mockResponses;
    }

    protected function getCacheIdentifier(): string
    {
        return sha1('typo3_api.extension_metadata_{"apiPath":"\/api\/v1\/extension\/foo"}');
    }
}
