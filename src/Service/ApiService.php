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

namespace App\Service;

use App\Entity\Dto\ExtensionMetadata;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * ApiService.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ApiService
{
    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private int $cacheExpirationPeriod = 3600,
    ) {
    }

    public function getExtensionMetadata(string $extension): ExtensionMetadata
    {
        $apiPath = $this->buildApiPath('/extension/{extension}', ['extension' => $extension]);

        // Fetch extension metadata from cache or external API
        $extensionMetadata = $this->cache->get(
            $this->calculateCacheIdentifier('typo3_api.extension_metadata', ['apiPath' => $apiPath]),
            fn (ItemInterface $item) => $this->sendRequestAndCacheResponse($apiPath, $item),
            null,
            $cacheMetadata,
        );

        // Define cache expiry date from cache metadata
        if (isset($cacheMetadata[ItemInterface::METADATA_EXPIRY])) {
            $timestamp = (int) $cacheMetadata[ItemInterface::METADATA_EXPIRY];
            $expiryDate = \DateTime::createFromFormat('U', (string) $timestamp) ?: null;
        } else {
            $expiryDate = null;
        }

        return new ExtensionMetadata($extensionMetadata, $expiryDate);
    }

    /**
     * @return array<int|string, mixed>
     */
    private function sendRequestAndCacheResponse(string $path, ItemInterface $item): array
    {
        $response = $this->client->request('GET', $path);
        $responseArray = $response->toArray();

        $item->expiresAfter($this->cacheExpirationPeriod);
        $item->set($responseArray);

        return $responseArray;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function buildApiPath(string $endpoint, array $parameters = []): string
    {
        $replacePairs = array_combine(
            array_map(fn (string $parameter): string => '{'.trim($parameter, '{}').'}', array_keys($parameters)),
            array_values($parameters),
        );

        return '/api/v1/'.ltrim(strtr($endpoint, $replacePairs), '/');
    }

    /**
     * @param array<string, string> $options
     */
    private function calculateCacheIdentifier(string $key, array $options = []): string
    {
        return hash('sha512', $key.'_'.json_encode($options));
    }
}
