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

namespace App\Tests\Entity\Dto;

use App\Entity\Dto\ExtensionMetadata;
use PHPUnit\Framework\TestCase;

/**
 * ExtensionMetadataTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ExtensionMetadataTest extends TestCase
{
    private \DateTime $expiryDate;
    private ExtensionMetadata $subject;

    protected function setUp(): void
    {
        $this->expiryDate = new \DateTime();
        $this->subject = new ExtensionMetadata(['foo' => 'baz'], $this->expiryDate);
    }

    /**
     * @test
     */
    public function getMetadataReturnsExtensionMetadata(): void
    {
        self::assertSame(['foo' => 'baz'], $this->subject->getMetadata());
    }

    /**
     * @test
     */
    public function getExpiryDateReturnsExpiryDate(): void
    {
        self::assertSame($this->expiryDate, $this->subject->getExpiryDate());
    }

    /**
     * @test
     */
    public function subjectCanBeAccessedAsArray(): void
    {
        // offsetExists()
        self::assertTrue(isset($this->subject['foo']));
        self::assertFalse(isset($this->subject['baz']));

        // offsetGet()
        self::assertSame('baz', $this->subject['foo']);
        self::assertNull($this->subject['baz']);

        // offsetSet()
        $this->subject['baz'] = 'foo';
        self::assertSame('foo', $this->subject['baz']);

        // offsetUnset()
        unset($this->subject['baz']);
        self::assertFalse(isset($this->subject['baz']));
    }
}
