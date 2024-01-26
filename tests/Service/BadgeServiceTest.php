<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021-2024 Elias Häußler <elias@haeussler.dev>
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

namespace App\Tests\Service;

use App\Badge\Provider\BadgeProviderFactory;
use App\Entity\Badge;
use App\Service\BadgeService;
use App\Tests\AbstractApiTestCase;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * BadgeServiceTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgeServiceTest extends AbstractApiTestCase
{
    private BadgeProviderFactory $badgeProviderFactory;
    private BadgeService $subject;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->badgeProviderFactory = self::getContainer()->get(BadgeProviderFactory::class);
        $this->subject = new BadgeService($this->getMockClient(), $this->cache);
    }

    #[Test]
    public function getReturnsBadgeResponseFromCache(): void
    {
        $badge = Badge::static();
        $badgeProvider = $this->badgeProviderFactory->get();
        $cacheIdentifier = $this->getCacheIdentifier('badge_response', [
            'url' => $badgeProvider->generateUriForBadge($badge),
        ]);

        $this->cache->get($cacheIdentifier, static fn () => [
            'body' => 'foo',
            'headers' => [
                'content-type' => ['baz'],
            ],
        ]);

        $actual = $this->subject->get($badge, $badgeProvider);

        self::assertSame('foo', $actual->getBody());
        self::assertSame(['content-type' => ['baz']], $actual->getHeaders());
        self::assertSame(0, $this->mockClient?->getRequestsCount());

        $this->cache->delete($cacheIdentifier);
    }

    #[Test]
    public function getReturnsBadgeResponseFromApiAndStoresResponseInCache(): void
    {
        $this->mockResponses[] = new MockResponse('foo', [
            'response_headers' => [
                'foo' => ['baz'],
            ],
        ]);

        $badge = Badge::static();
        $badgeProvider = $this->badgeProviderFactory->get();
        $cacheIdentifier = $this->getCacheIdentifier('badge_response', [
            'url' => $badgeProvider->generateUriForBadge($badge),
        ]);

        $actual = $this->subject->get($badge, $badgeProvider);

        self::assertSame('foo', $actual->getBody());
        self::assertSame(
            [
                'foo' => ['baz'],
            ],
            $actual->getHeaders(),
        );
        self::assertSame(1, $this->mockClient?->getRequestsCount());
        self::assertSame(
            [
                'body' => 'foo',
                'headers' => [
                    'foo' => ['baz'],
                ],
            ],
            $this->cache->get($cacheIdentifier, fn () => null),
        );
    }
}
