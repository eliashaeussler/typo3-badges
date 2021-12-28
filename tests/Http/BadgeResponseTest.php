<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
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

namespace App\Tests\Http;

use App\Http\BadgeResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * BadgeResponseTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgeResponseTest extends TestCase
{
    /**
     * @test
     */
    public function forExtensionReturnsResponseForExtension(): void
    {
        $subject = BadgeResponse::forExtension('foo');
        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'typo3',
            'message' => 'foo',
            'color' => 'orange',
            'isError' => false,
            'namedLogo' => 'typo3',
        ]);

        self::assertEquals($expected, $subject->create());
    }

    /**
     * @test
     */
    public function forVersionReturnsResponseForVersion(): void
    {
        $subject = BadgeResponse::forVersion('1.0.0');
        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'typo3',
            'message' => '1.0.0',
            'color' => 'orange',
            'isError' => false,
            'namedLogo' => 'typo3',
        ]);

        self::assertEquals($expected, $subject->create());
    }

    /**
     * @test
     */
    public function forDownloadsReturnsResponseForDownloads(): void
    {
        $subject = BadgeResponse::forDownloads(845760473);
        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'typo3',
            'message' => '845.8m downloads',
            'color' => 'blue',
            'isError' => false,
            'namedLogo' => 'typo3',
        ]);

        self::assertEquals($expected, $subject->create());
    }

    /**
     * @test
     */
    public function forErrorReturnsResponseOnError(): void
    {
        $subject = BadgeResponse::forError();
        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'typo3',
            'message' => 'error',
            'color' => 'red',
            'isError' => true,
            'namedLogo' => 'typo3',
        ]);

        self::assertEquals($expected, $subject->create());
    }

    /**
     * @test
     */
    public function createReturnsResponseWithDefaultValues(): void
    {
        $subject = new BadgeResponse();
        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'typo3',
            'message' => 'inspiring people to share',
            'color' => 'orange',
            'isError' => false,
            'namedLogo' => 'typo3',
        ]);

        self::assertEquals($expected, $subject->create());
    }
}
