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

use App\Health\HealthState;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * HealthStateTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[CoversClass(HealthState::class)]
final class HealthStateTest extends TestCase
{
    #[Test]
    public function fromResponseReturnsHealthyStateOnSuccessfulResponse(): void
    {
        $response = self::createResponse();

        $expected = new HealthState(true);

        self::assertEquals($expected, HealthState::fromResponse($response));
    }

    #[Test]
    public function fromResponseReturnsUnhealthyStateOnErroneousResponse(): void
    {
        $response = self::createResponse(404, 'Something went wrong.');

        $expected = new HealthState(false, 'Got 404 response: Something went wrong.');

        self::assertEquals($expected, HealthState::fromResponse($response));
    }

    #[Test]
    public function fromResponseReturnsUnhealthyStateOnException(): void
    {
        $exception = new Exception('Something went wrong.');
        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willThrowException($exception);

        $expected = new HealthState(false, 'Something went wrong.');

        self::assertEquals($expected, HealthState::fromResponse($response));
    }

    #[Test]
    public function fromExceptionReturnsUnhealthyState(): void
    {
        $exception = new Exception('Something went wrong.');

        $expected = new HealthState(false, 'Something went wrong.');

        self::assertEquals($expected, HealthState::fromException($exception));
    }

    #[Test]
    public function toArrayReturnsArrayRepresentation(): void
    {
        $subject = new HealthState(true, 'Everything fine');

        $expected = [
            'healthy' => true,
            'information' => 'Everything fine',
        ];

        self::assertSame($expected, $subject->toArray());
    }

    private static function createResponse(int $statusCode = 200, string $body = ''): ResponseInterface
    {
        $response = new MockResponse($body, ['http_code' => $statusCode]);

        return new MockHttpClient($response)->request('GET', 'https://example.com');
    }
}
