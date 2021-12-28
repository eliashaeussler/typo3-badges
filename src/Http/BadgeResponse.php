<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021 Elias Häußler <elias@haeussler.dev>
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

namespace App\Http;

use App\Number\NumberFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * BadgeResponse.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgeResponse
{
    private const COLOR_MAP = [
        'extension' => 'orange',
        'version' => 'orange',
        'downloads' => 'blue',
        'error' => 'red',
    ];

    private string $label = '';
    private string $message = '';
    private string $color = '';
    private bool $isError = false;

    public static function forExtension(string $extension): self
    {
        $object = new self();
        $object->label = 'typo3';
        $object->message = $extension;
        $object->color = self::COLOR_MAP['extension'];

        return $object;
    }

    public static function forVersion(string $version): self
    {
        $object = new self();
        $object->label = 'typo3';
        $object->message = $version;
        $object->color = self::COLOR_MAP['version'];

        return $object;
    }

    public static function forDownloads(int $downloads): self
    {
        $object = new self();
        $object->label = 'typo3';
        $object->message = sprintf('%s downloads', strtolower(NumberFormatter::format($downloads)));
        $object->color = self::COLOR_MAP['downloads'];

        return $object;
    }

    public static function forError(): self
    {
        $object = new self();
        $object->label = 'typo3';
        $object->message = 'error';
        $object->color = self::COLOR_MAP['error'];
        $object->isError = true;

        return $object;
    }

    public function create(): JsonResponse
    {
        return new JsonResponse([
            'schemaVersion' => 1,
            'label' => $this->label ?: 'typo3',
            'message' => $this->message ?: 'inspiring people to share',
            'color' => $this->color ?: 'orange',
            'isError' => $this->isError,
            'namedLogo' => 'typo3',
        ]);
    }
}
