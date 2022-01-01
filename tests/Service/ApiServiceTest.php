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

namespace App\Tests\Service;

use App\Tests\AbstractApiTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * ApiServiceTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ApiServiceTest extends AbstractApiTestCase
{
    /**
     * @test
     */
    public function getExtensionMetadataReturnsMetadataFromCache(): void
    {
        $cacheIdentifier = $this->getCacheIdentifier();

        $this->cache->get($cacheIdentifier, fn () => ['foo' => 'baz']);

        self::assertSame(['foo' => 'baz'], $this->apiService->getExtensionMetadata('foo'));
        self::assertSame(0, $this->client->getRequestsCount());

        $this->cache->delete($cacheIdentifier);
    }

    /**
     * @test
     */
    public function getExtensionMetadataReturnsMetadataFromApiAndStoresResponseInCache(): void
    {
        $this->mockResponses[] = new MockResponse(json_encode(['foo' => 'baz'], JSON_THROW_ON_ERROR));

        self::assertSame(['foo' => 'baz'], $this->apiService->getExtensionMetadata('foo'));
        self::assertSame(1, $this->client->getRequestsCount());
        self::assertSame(['foo' => 'baz'], $this->cache->get($this->getCacheIdentifier(), fn () => null));
    }
}
