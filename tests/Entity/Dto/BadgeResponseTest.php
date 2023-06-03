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

namespace App\Tests\Entity\Dto;

use App\Entity\Dto\BadgeResponse;
use DateTime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BadgeResponseTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgeResponseTest extends TestCase
{
    private DateTime $expiryDate;
    private BadgeResponse $subject;

    protected function setUp(): void
    {
        $this->expiryDate = new DateTime();
        $this->subject = new BadgeResponse('foo', ['content-type' => ['baz']], $this->expiryDate);
    }

    #[Test]
    public function getBodyReturnsResponseBody(): void
    {
        self::assertSame('foo', $this->subject->getBody());
    }

    #[Test]
    public function getHeadersReturnsResponseHeaders(): void
    {
        self::assertSame(['content-type' => ['baz']], $this->subject->getHeaders());
    }

    #[Test]
    public function getHeaderReturnsSingleResponseHeaderOrEmptyArray(): void
    {
        self::assertSame(['baz'], $this->subject->getHeader('Content-Type'));
        self::assertSame([], $this->subject->getHeader('foo'));
    }

    #[Test]
    public function getExpiryDateReturnsExpiryDate(): void
    {
        self::assertSame($this->expiryDate, $this->subject->getExpiryDate());
    }
}
