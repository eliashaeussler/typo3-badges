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

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

/**
 * HomepageControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class HomepageControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function controllerReturnsHomepage(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/');

        $router = self::getContainer()->get('router');
        assert($router instanceof Router);
        $allRoutes = $router->getRouteCollection()->all();
        $badgeRoutes = array_filter($allRoutes, fn (Route $route) => str_starts_with($route->getPath(), '/badge/'));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'TYPO3 Badges');
        self::assertCount(count($badgeRoutes), $crawler->filter('.endpoint'));
    }
}
