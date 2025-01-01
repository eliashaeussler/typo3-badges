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

namespace App\Tests\Badge\Provider;

use App\Badge\Provider\BadgenBadgeProvider;
use App\Badge\Provider\BadgeProvider;
use App\Badge\Provider\BadgeProviderFactory;
use App\Badge\Provider\ShieldsBadgeProvider;
use App\Exception\InvalidProviderException;
use Generator;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * BadgeProviderFactoryTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgeProviderFactoryTest extends KernelTestCase
{
    private BadgeProviderFactory $subject;

    #[Override]
    protected function setUp(): void
    {
        $this->subject = new BadgeProviderFactory(
            new ServiceLocator([
                'badgen' => fn (): BadgenBadgeProvider => self::getContainer()->get(BadgenBadgeProvider::class),
                'shields' => fn (): ShieldsBadgeProvider => self::getContainer()->get(ShieldsBadgeProvider::class),
            ]),
        );
    }

    #[Test]
    public function getReturnsDefaultBadgeResponseProvider(): void
    {
        self::assertInstanceOf(ShieldsBadgeProvider::class, $this->subject->get());
    }

    #[Test]
    public function getThrowsExceptionIfGivenTypeIsNotSupported(): void
    {
        $this->expectException(InvalidProviderException::class);
        $this->expectExceptionMessage('The provider "foo" is not supported.');
        $this->expectExceptionCode(1641195602);

        $this->subject->get('foo');
    }

    /**
     * @param class-string<BadgeProvider> $expected
     */
    #[Test]
    #[DataProvider('getReturnsDefaultBadgeResponseProviderDataProvider')]
    public function getReturnsBadgeResponseProvider(string $type, string $expected): void
    {
        self::assertInstanceOf($expected, $this->subject->get($type));
    }

    #[Test]
    public function getAllReturnsAllBadgeResponseProviders(): void
    {
        $actual = $this->subject->getAll();

        self::assertCount(2, $actual);
        self::assertInstanceOf(BadgenBadgeProvider::class, $actual['badgen'] ?? null);
        self::assertInstanceOf(ShieldsBadgeProvider::class, $actual['shields'] ?? null);
    }

    /**
     * @return Generator<string, array{string, class-string<BadgeProvider>}>
     */
    public static function getReturnsDefaultBadgeResponseProviderDataProvider(): Generator
    {
        yield 'shields' => ['shields', ShieldsBadgeProvider::class];
        yield 'badgen' => ['badgen', BadgenBadgeProvider::class];
    }
}
