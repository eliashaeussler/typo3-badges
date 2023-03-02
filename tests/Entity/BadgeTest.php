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

namespace App\Tests\Entity;

use App\Entity\Badge;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * BadgeTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgeTest extends TestCase
{
    /**
     * @test
     */
    public function forExtensionReturnsBadgeForExtension(): void
    {
        $expected = new Badge(
            label: 'extension',
            message: 'foo',
            color: 'orange',
            isError: false,
        );

        self::assertEquals($expected, Badge::forExtension('foo'));
    }

    /**
     * @test
     */
    public function forVersionReturnsBadgeForVersion(): void
    {
        $expected = new Badge(
            label: 'version',
            message: '1.0.0',
            color: 'orange',
            isError: false,
        );

        self::assertEquals($expected, Badge::forVersion('1.0.0'));
    }

    /**
     * @test
     */
    public function forDownloadsReturnsBadgeForDownloads(): void
    {
        $expected = new Badge(
            label: 'downloads',
            message: '845.8m',
            color: 'blue',
            isError: false,
        );

        self::assertEquals($expected, Badge::forDownloads(845760473));
    }

    /**
     * @test
     */
    public function forTypo3VersionsReturnsErrorBadgeForEmptyTypo3VersionList(): void
    {
        $expected = Badge::forError();

        self::assertEquals($expected, Badge::forTypo3Versions([]));
    }

    /**
     * @test
     *
     * @dataProvider forTypo3VersionsReturnsBadgeForTypo3VersionsDataProvider
     *
     * @param list<int> $typo3Versions
     */
    public function forTypo3VersionsReturnsBadgeForTypo3Versions(array $typo3Versions, string $expected): void
    {
        $expected = new Badge(
            label: 'typo3',
            message: $expected,
            color: 'orange',
            isError: false,
        );

        self::assertEquals($expected, Badge::forTypo3Versions($typo3Versions));
    }

    /**
     * @test
     *
     * @dataProvider forStabilityReturnsBadgeForStabilityDataProvider
     */
    public function forStabilityReturnsBadgeForStability(string $stability, string $expectedColor): void
    {
        $expected = new Badge(
            label: 'stability',
            message: $stability,
            color: $expectedColor,
            isError: false,
        );

        self::assertEquals($expected, Badge::forStability($stability));
    }

    /**
     * @test
     */
    public function forErrorReturnsBadgeOnError(): void
    {
        $expected = new Badge(
            label: 'typo3',
            message: 'error',
            color: 'red',
            isError: true,
        );

        self::assertEquals($expected, Badge::forError());
    }

    /**
     * @test
     */
    public function getLabelReturnsLabel(): void
    {
        $subject = new Badge();

        self::assertSame('', $subject->getLabel());

        $subject = new Badge(label: 'foo');

        self::assertSame('foo', $subject->getLabel());
    }

    /**
     * @test
     */
    public function getMessageReturnsMessage(): void
    {
        $subject = new Badge();

        self::assertSame('', $subject->getMessage());

        $subject = new Badge(message: 'foo');

        self::assertSame('foo', $subject->getMessage());
    }

    /**
     * @test
     */
    public function getColorReturnsColor(): void
    {
        $subject = new Badge();

        self::assertSame('', $subject->getColor());

        $subject = new Badge(color: 'foo');

        self::assertSame('foo', $subject->getColor());
    }

    /**
     * @test
     */
    public function isErrorReturnsErrorState(): void
    {
        $subject = new Badge();

        self::assertFalse($subject->isError());

        $subject = new Badge(isError: true);

        self::assertTrue($subject->isError());
    }

    /**
     * @return \Generator<string, array{string, string}>
     */
    public function forStabilityReturnsBadgeForStabilityDataProvider(): Generator
    {
        yield 'stable' => ['stable', 'green'];
        yield 'beta' => ['beta', 'yellow'];
        yield 'alpha' => ['alpha', 'red'];
        yield 'experimental' => ['experimental', 'red'];
        yield 'test' => ['test', 'lightgrey'];
        yield 'obsolete' => ['obsolete', 'lightgrey'];
        yield 'excludeFromUpdates' => ['excludeFromUpdates', 'lightgrey'];
    }

    /**
     * @return \Generator<string, array{list<int>, string}>
     */
    public function forTypo3VersionsReturnsBadgeForTypo3VersionsDataProvider(): Generator
    {
        yield 'one version' => [[11], '11'];
        yield 'two versions' => [[10, 11], '10 & 11'];
        yield 'three versions' => [[9, 10, 11], '9, 10 & 11'];
    }
}
