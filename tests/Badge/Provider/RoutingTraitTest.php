<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
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

namespace App\Tests\Badge\Provider;

use App\Tests\Fixtures\RoutingTraitTestClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * RoutingTraitTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class RoutingTraitTest extends KernelTestCase
{
    private RouterInterface $router;
    private RoutingTraitTestClass $subject;

    protected function setUp(): void
    {
        $this->router = self::getContainer()->get('router');
        $this->subject = new RoutingTraitTestClass($this->router);
    }

    /**
     * @test
     */
    public function identifyRouteThrowsExceptionIfRouteCannotBeFound(): void
    {
        $route = new Route('/foo');

        $this->expectException(RouteNotFoundException::class);
        $this->expectErrorMessage('Unable to find route with path "/foo"!');
        $this->expectExceptionCode(1641203175);

        $this->subject->testIdentifyRoute($route);
    }

    /**
     * @test
     */
    public function identifyRouteReturnsNameOfGivenRoute(): void
    {
        $route = new Route('/foo');
        $this->router->getRouteCollection()->add('foo', $route);

        self::assertSame('foo', $this->subject->testIdentifyRoute($route));
    }
}
