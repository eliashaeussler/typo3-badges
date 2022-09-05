<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2022 Elias Häußler <elias@haeussler.dev>
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

namespace App\Tests\Value;

use App\Value\NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * NumberFormatterTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class NumberFormatterTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider formatReturnsFormattedNumberDataProvider
     */
    public function formatReturnsFormattedNumber(int $number, string $expected): void
    {
        self::assertSame($expected, NumberFormatter::format($number));
    }

    /**
     * @return \Generator<string, array{int, string}>
     */
    public function formatReturnsFormattedNumberDataProvider(): \Generator
    {
        yield 'lower than 1000' => [278, '278'];
        yield 'thousands (#1)' => [2782, '2.8K'];
        yield 'thousands (#2)' => [278239, '278.2K'];
        yield 'millions (#1)' => [2782394, '2.8M'];
        yield 'millions (#2)' => [278239465, '278.2M'];
        yield 'billions (#1)' => [2782394658, '2.8B'];
        yield 'billions (#2)' => [278239465890, '278.2B'];
        yield 'trillions (#1)' => [2782394658901, '2.8T'];
        yield 'trillions (#2)' => [278239465890145, '278.2T'];
        yield 'quadrillions (#1)' => [2782394658901452, '2.8Q'];
        yield 'quadrillions (#2)' => [278239465890145231, '278.2Q'];
        yield 'higher than quadrillions' => [2782394658901452310, '2782394658901452310'];
    }
}
