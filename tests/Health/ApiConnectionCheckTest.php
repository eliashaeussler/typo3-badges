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

namespace App\Tests\Health;

use App\Health\ApiConnectionCheck;
use App\Health\HealthState;
use App\Service\ApiService;
use App\Tests\AbstractApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * ApiConnectionCheckTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[CoversClass(ApiConnectionCheck::class)]
final class ApiConnectionCheckTest extends AbstractApiTestCase
{
    private ApiConnectionCheck $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new ApiConnectionCheck($this->apiService);
    }

    #[Test]
    public function checkReturnsUnhealthyStateOnErroneousPing(): void
    {
        $this->mockResponses[] = new MockResponse('Sorry, server is down', ['http_code' => 500]);

        $expected = new HealthState(false, 'Got 500 response: Sorry, server is down');

        self::assertEquals($expected, $this->subject->check());
    }

    #[Test]
    public function checkReturnsHealthyStateOnSuccessfulPing(): void
    {
        $this->mockResponses[] = new MockResponse('Hello World', ['http_code' => 200]);

        $expected = new HealthState(true);

        self::assertEquals($expected, $this->subject->check());
    }

    #[Test]
    public function checkReturnsUnhealthyStateOnException(): void
    {
        $factory = static fn () => throw new TransportException('Something went wrong');
        $apiService = new ApiService(new MockHttpClient($factory), $this->cache);
        $subject = new ApiConnectionCheck($apiService);

        $expected = new HealthState(false, 'Something went wrong');

        self::assertEquals($expected, $subject->check());
    }
}
