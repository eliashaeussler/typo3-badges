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

namespace App\Badge\Provider;

use App\Entity\Badge;
use App\Enums\Color;
use Nyholm\Psr7\Uri;
use Override;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

use function array_map;

/**
 * BadgenBadgeProvider.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgenBadgeProvider implements BadgeProvider
{
    use RoutingTrait;

    public const string IDENTIFIER = 'badgen';

    private const string BADGE_URL_PATTERN = 'https://badgen.net/badge/{subject}/{status}/{color}';
    private const string ENDPOINT_URL_PATTERN = 'https://badgen.net/https/{host}/{path}';

    public function __construct(
        RouterInterface $router,
    ) {
        $this->router = $router;
    }

    #[Override]
    public static function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    #[Override]
    public static function getName(): string
    {
        return 'Badgen';
    }

    #[Override]
    public function createResponse(Badge $badge): Response
    {
        return new JsonResponse([
            'subject' => $badge->getLabel(),
            'status' => $badge->getMessage(),
            'color' => $this->getColorValue($badge->getColor()),
        ]);
    }

    #[Override]
    public function generateUriForRoute(Route|string $route, array $routeParameters = []): string
    {
        // Enforce provider parameter
        $routeParameters['provider'] = self::IDENTIFIER;

        if ($route instanceof Route) {
            $routeName = $this->identifyRoute($route);
        } else {
            $routeName = $route;
        }

        $appUrl = new Uri($this->router->generate($routeName, $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL));

        return strtr(self::ENDPOINT_URL_PATTERN, [
            '{host}' => $appUrl->getHost().(null !== $appUrl->getPort() ? ':'.$appUrl->getPort() : ''),
            '{path}' => ltrim($appUrl->getPath(), '/'),
        ]);
    }

    #[Override]
    public function generateUriForBadge(Badge $badge): string
    {
        $urlParameters = [
            '{subject}' => $badge->getLabel(),
            '{status}' => $badge->getMessage(),
            '{color}' => $this->getColorValue($badge->getColor()),
        ];

        return strtr(self::BADGE_URL_PATTERN, array_map(rawurlencode(...), $urlParameters));
    }

    /**
     * @codeCoverageIgnore
     */
    #[Override]
    public function getUrlPattern(): string
    {
        return self::ENDPOINT_URL_PATTERN;
    }

    #[Override]
    public function getProviderUrl(): string
    {
        return 'https://badgen.net';
    }

    private function getColorValue(Color $color): string
    {
        return match ($color) {
            Color::Blue => 'blue',
            Color::Gray => 'grey',
            Color::Green => 'green',
            Color::Orange => 'orange',
            Color::Red => 'red',
            Color::Yellow => 'yellow',
        };
    }
}
