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

namespace App\Tests\Controller;

use App\Service\ApiService;
use App\Tests\MockClientTrait;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Routing\Route;
use Symfony\Contracts\Cache\CacheInterface;

use function count;

/**
 * HomepageControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class HomepageControllerTest extends WebTestCase
{
    use MockClientTrait;

    private KernelBrowser $client;

    #[Override]
    protected function setUp(): void
    {
        $this->mockClient = $this->getMockClient();
        $this->client = self::createClient();

        $container = self::getContainer();
        $container->set(ApiService::class, new ApiService($this->mockClient, $container->get(CacheInterface::class)));
    }

    #[Test]
    public function controllerReturnsHomepage(): void
    {
        $this->mockResponses[] = new MockResponse(json_encode([
            'extensions' => [
                [
                    'key' => 'foo',
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $crawler = $this->client->request('GET', '/');
        $allRoutes = self::getContainer()->get('router')->getRouteCollection()->all();
        $badgeRoutes = array_filter($allRoutes, fn (Route $route) => str_starts_with($route->getPath(), '/badge/'));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'TYPO3 Badges');
        self::assertCount(count($badgeRoutes), $crawler->filter('.badge-endpoint'));
    }
}
