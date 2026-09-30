<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021-2026 Elias Häußler <elias@haeussler.dev>
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

namespace App\Value;

use function array_key_exists;
use function intval;

/**
 * NumberFormatter.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class NumberFormatter
{
    /**
     * @see https://gist.github.com/gcphost/589d915e2c2f67270cc9bdade732b77f
     *
     * @return non-empty-string
     */
    public static function format(int $number): string
    {
        if ($number < 1000) {
            return (string) $number;
        }

        $unit = intval(log($number, 1000));
        $units = ['', 'K', 'M', 'B', 'T', 'Q'];
        $value = round($number / 1000 ** $unit, 1);

        // Rounding may carry over to the next unit, e.g. 999999 => 1000.0K => 1M
        if ($value >= 1000) {
            ++$unit;
            $value = round($number / 1000 ** $unit, 1);
        }

        if (array_key_exists($unit, $units)) {
            $decimals = $value === floor($value) ? 0 : 1;

            return number_format($value, $decimals, '.', '').$units[$unit];
        }

        return (string) $number;
    }
}
