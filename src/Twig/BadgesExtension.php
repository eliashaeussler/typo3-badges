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

namespace App\Twig;

use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use function preg_replace;
use function trim;

/**
 * BadgesExtension.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgesExtension extends AbstractExtension
{
    #[Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('spaceless', self::spaceless(...), ['is_safe' => ['html']]),
        ];
    }

    /**
     * @see https://github.com/twigphp/Twig/blob/v3.21.1/src/Extension/CoreExtension.php#L1226-L1236
     */
    public static function spaceless(?string $content): string
    {
        return trim((string) preg_replace('/>\s+</', '><', $content ?? ''));
    }
}
