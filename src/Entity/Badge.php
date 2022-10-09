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

namespace App\Entity;

use App\Value\NumberFormatter;

/**
 * Badge.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Badge
{
    private const COLOR_MAP = [
        'extension' => 'orange',
        'version' => 'orange',
        'downloads' => 'blue',
        'typo3' => 'orange',
        'error' => 'red',
        'stability_stable' => 'green',
        'stability_beta' => 'yellow',
        'stability_alpha' => 'red',
        'stability_experimental' => 'red',
        'stability_test' => 'lightgrey',
        'stability_obsolete' => 'lightgrey',
        'stability_excludeFromUpdates' => 'lightgrey',
    ];

    public function __construct(
        private readonly string $label = '',
        private readonly string $message = '',
        private readonly string $color = '',
        private readonly bool $isError = false,
    ) {
    }

    public static function forExtension(string $extension): self
    {
        return new self(
            label: 'typo3',
            message: $extension,
            color: self::COLOR_MAP['extension'],
        );
    }

    public static function forVersion(string $version): self
    {
        return new self(
            label: 'typo3',
            message: $version,
            color: self::COLOR_MAP['version'],
        );
    }

    public static function forDownloads(int $downloads): self
    {
        return new self(
            label: 'typo3',
            message: sprintf('%s downloads', strtolower(NumberFormatter::format($downloads))),
            color: self::COLOR_MAP['downloads'],
        );
    }

    /**
     * @param list<int> $typo3Versions
     */
    public static function forTypo3Versions(array $typo3Versions): self
    {
        if ([] === $typo3Versions) {
            return self::forError();
        }

        sort($typo3Versions);

        $lastValue = array_pop($typo3Versions);
        $message = implode(' & ', array_filter([
            implode(', ', $typo3Versions) ?: null,
            $lastValue,
        ]));

        return new self(
            label: 'typo3',
            message: $message,
            color: self::COLOR_MAP['typo3'],
        );
    }

    public static function forStability(string $stability): self
    {
        return new self(
            label: 'typo3',
            message: $stability,
            color: self::COLOR_MAP['stability_'.$stability] ?? 'orange',
        );
    }

    public static function forError(): self
    {
        return new self(
            label: 'typo3',
            message: 'error',
            color: self::COLOR_MAP['error'],
            isError: true,
        );
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isError(): bool
    {
        return $this->isError;
    }
}
