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

namespace App\Tests\Controller;

use App\Badge\Provider\BadgeProviderFactory;
use App\Entity\Badge;
use App\Tests\Fixtures\AbstractBadgeControllerTestClass;
use DateTime;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * AbstractBadgeControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class AbstractBadgeControllerTest extends KernelTestCase
{
    private BadgeProviderFactory $badgeProviderFactory;
    private AbstractBadgeControllerTestClass $subject;

    protected function setUp(): void
    {
        $this->badgeProviderFactory = self::getContainer()->get(BadgeProviderFactory::class);
        $this->subject = new AbstractBadgeControllerTestClass();
        $this->subject->setBadgeProviderFactory($this->badgeProviderFactory);
    }

    #[Test]
    public function getBadgeResponseThrowsNotFoundExceptionOnUnsupportedProvider(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->subject->testGetBadgeResponse(Badge::static(), 'foo');
    }

    #[Test]
    public function getBadgeResponseReturnsResponseForDefaultProviderIfNoProviderIsGiven(): void
    {
        $badge = Badge::static();
        $expected = $this->badgeProviderFactory->get()->createResponse($badge);

        self::assertEquals($expected, $this->subject->testGetBadgeResponse($badge));
    }

    #[Test]
    public function getBadgeResponseReturnsResponseForRequestedProvider(): void
    {
        $badge = Badge::static();
        $expected = $this->badgeProviderFactory->get('badgen')->createResponse($badge);

        self::assertEquals($expected, $this->subject->testGetBadgeResponse($badge, 'badgen'));
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

        self::assertEquals($expected, $this->subject->testGetBadgeResponse($badge, 'badgen', $cacheExpirationDate));
    }
}
