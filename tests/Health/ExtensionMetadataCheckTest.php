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

use App\Health\ExtensionMetadataCheck;
use App\Health\HealthState;
use App\Service\ApiService;
use App\Tests\AbstractApiTestCase;
use Exception;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function json_encode;

/**
 * ExtensionMetadataCheckTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[CoversClass(ExtensionMetadataCheck::class)]
final class ExtensionMetadataCheckTest extends AbstractApiTestCase
{
    private ExtensionMetadataCheck $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new ExtensionMetadataCheck($this->apiService);
    }

    #[Test]
    public function checkReturnsUnhealthyStateOnException(): void
    {
        $factory = static fn () => throw new Exception('Something went wrong');
        $apiService = new ApiService(new MockHttpClient($factory), $this->cache);
        $subject = new ExtensionMetadataCheck($apiService);

        $expected = new HealthState(false, 'Something went wrong');

        self::assertEquals($expected, $subject->check());
    }

    /**
     * @return Generator<string, array{array{0?: array<string, mixed>}, string}>
     */
    public static function checkReturnsUnhealthyStateOnInvalidExtensionMetadataResponseDataProvider(): Generator
    {
        yield 'empty response' => [
            [],
            'composer: Expected path segment 0, but is missing in response.',
        ];
        yield 'missing path segment' => [
            [
                [
                    'meta' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'composer: Expected path segment 0.meta.composer_name, but is missing in response.',
        ];
        yield 'invalid path segment' => [
            [
                [
                    'meta' => 'foo',
                ],
            ],
            'composer: Expected array at path segment 0.meta, but received string.',
        ];
        yield 'invalid value' => [
            [
                [
                    'downloads' => 100,
                    'key' => '',
                    'meta' => [
                        'composer_name' => 'foo/bar',
                    ],
                ],
            ],
            'extension: Received value is invalid.',
        ];
    }

    /**
     * @param array{0?: array<string, mixed>} $body
     */
    #[Test]
    #[DataProvider('checkReturnsUnhealthyStateOnInvalidExtensionMetadataResponseDataProvider')]
    public function checkReturnsUnhealthyStateOnInvalidExtensionMetadataResponse(array $body, string $expected): void
    {
        $this->mockResponses[] = new MockResponse(json_encode($body, JSON_THROW_ON_ERROR));

        self::assertEquals(new HealthState(false, $expected), $this->subject->check());
    }

    #[Test]
    public function checkReturnsHealthyStateOnValidExtensionMetadataResponse(): void
    {
        $this->mockResponses[] = new MockResponse(
            json_encode([
                [
                    'current_version' => [
                        'number' => '0.1.0',
                        'state' => 'beta',
                        'typo3_versions' => [13],
                    ],
                    'downloads' => 100,
                    'key' => 'bar',
                    'meta' => [
                        'composer_name' => 'foo/bar',
                    ],
                    'verified' => false,
                ],
            ],
                JSON_THROW_ON_ERROR,
            ),
        );

        $expected = new HealthState(true);

        self::assertEquals($expected, $this->subject->check());
    }
}
