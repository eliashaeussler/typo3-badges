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

namespace App\Tests\Controller;

use App\Badge\Provider\BadgeProviderFactory;
use App\Controller\VerifiedBadgeController;
use App\Tests\AbstractApiTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * VerifiedBadgeControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class VerifiedBadgeControllerTest extends AbstractApiTestCase
{
    private VerifiedBadgeController $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new VerifiedBadgeController($this->apiService);
        $this->subject->setBadgeProviderFactory(self::getContainer()->get(BadgeProviderFactory::class));
    }

    #[Test]
    public function controllerThrowsBadRequestExceptionIfApiResponseIsInvalid(): void
    {
        $this->mockResponses[] = new MockResponse(json_encode(['foo' => 'baz'], JSON_THROW_ON_ERROR));

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Invalid API response.');

        ($this->subject)(new Request(), 'foo');
    }

    #[Test]
    #[DataProvider('controllerReturnsBadgeForGivenExtensionDataProvider')]
    public function controllerReturnsBadgeForGivenExtension(
        bool $state,
        string $expectedMessage,
        string $expectedColor,
    ): void {
        $this->mockResponses[] = new MockResponse(json_encode([
            [
                'verified' => $state,
            ],
        ], JSON_THROW_ON_ERROR));

        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'typo3',
            'message' => $expectedMessage,
            'color' => $expectedColor,
            'isError' => false,
            'namedLogo' => 'typo3',
        ]);

        self::assertSame(
            $expected->getContent(),
            ($this->subject)(new Request(), 'foo')->getContent(),
        );
    }

    /**
     * @return Generator<string, array{bool, string, string}>
     */
    public static function controllerReturnsBadgeForGivenExtensionDataProvider(): Generator
    {
        yield 'verified' => [true, 'verified', 'green'];
        yield 'not verified' => [false, 'not verified', 'lightgrey'];
    }
}