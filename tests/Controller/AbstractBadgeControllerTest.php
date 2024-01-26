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

namespace App\Tests\Controller;

use App\Badge\Provider\BadgeProviderFactory;
use App\Entity\Badge;
use App\Service\BadgeService;
use App\Tests\AbstractApiTestCase;
use App\Tests\Fixtures\AbstractBadgeControllerTestClass;
use DateTime;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * AbstractBadgeControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class AbstractBadgeControllerTest extends AbstractApiTestCase
{
    private BadgeProviderFactory $badgeProviderFactory;
    private AbstractBadgeControllerTestClass $subject;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->badgeProviderFactory = self::getContainer()->get(BadgeProviderFactory::class);
        $this->subject = new AbstractBadgeControllerTestClass();
        $this->subject->setBadgeProviderFactory($this->badgeProviderFactory);
        $this->subject->setBadgeService(
            new BadgeService($this->getMockClient(), $this->cache),
        );
    }

    #[Test]
    public function getBadgeResponseThrowsNotFoundExceptionOnUnsupportedProvider(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->subject->testGetBadgeResponse(new Request(), Badge::static(), 'foo');
    }

    #[Test]
    public function getBadgeResponseReturnsRenderedBadgeOnSvgRequestFormat(): void
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

        $request = new Request();
        $request->setRequestFormat('svg');

        $actual = $this->subject->testGetBadgeResponse($request, $badge);

        self::assertSame('foo', $actual->getContent());
        self::assertSame('baz', $actual->headers->get('Content-Type'));
        self::assertSame(Response::HTTP_OK, $actual->getStatusCode());
    }

    #[Test]
    public function getBadgeResponseReturnsResponseForDefaultProviderIfNoProviderIsGiven(): void
    {
        $badge = Badge::static();
        $expected = $this->badgeProviderFactory->get()->createResponse($badge);

        self::assertEquals($expected, $this->subject->testGetBadgeResponse(new Request(), $badge));
    }

    #[Test]
    public function getBadgeResponseReturnsResponseForRequestedProvider(): void
    {
        $badge = Badge::static();
        $expected = $this->badgeProviderFactory->get('badgen')->createResponse($badge);

        self::assertEquals(
            $expected,
            $this->subject->testGetBadgeResponse(new Request(), $badge, 'badgen'),
        );
    }

    #[Test]
    public function getBadgeResponseReturnsCachedResponse(): void
    {
        $badge = Badge::static();
        $cacheExpirationDate = new DateTime();
        $expected = $this->badgeProviderFactory->get('badgen')->createResponse($badge)
            ->setPublic()
            ->setExpires($cacheExpirationDate);
        $expected->headers->addCacheControlDirective('must-revalidate', true);

        self::assertEquals(
            $expected,
            $this->subject->testGetBadgeResponse(new Request(), $badge, 'badgen', $cacheExpirationDate),
        );
    }
}
