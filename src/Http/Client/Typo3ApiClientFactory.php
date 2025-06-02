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

namespace App\Http\Client;

use App\Exception\MissingApiTokenException;
use App\Repository\ApiTokenRepository;
use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Typo3ApiClientFactory.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class Typo3ApiClientFactory
{
    private const string BASE_URI = 'https://extensions.typo3.org';

    public function __construct(
        private ApiTokenRepository $apiTokenRepository,
        private HttpClientInterface $client,
        #[Autowire('%env(TYPO3_API_USERNAME)%')]
        #[SensitiveParameter]
        private string $username,
        #[Autowire('%env(TYPO3_API_PASSWORD)%')]
        #[SensitiveParameter]
        private string $password,
    ) {}

    /**
     * @throws MissingApiTokenException
     */
    public function getWithToken(): HttpClientInterface
    {
        $apiToken = $this->apiTokenRepository->findLatestToken()
            ?? throw MissingApiTokenException::create()
        ;

        return $this->client->withOptions([
            'auth_bearer' => $apiToken->getAccessToken(),
            'base_uri' => self::BASE_URI,
        ]);
    }

    public function getForAuthentication(): HttpClientInterface
    {
        return $this->client->withOptions([
            'auth_basic' => [$this->username, $this->password],
            'base_uri' => self::BASE_URI,
        ]);
    }
}
