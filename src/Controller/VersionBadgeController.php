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

namespace App\Controller;

use App\Entity\Badge;
use App\Service\ApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * VersionBadgeController.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Route(
    path: '/badge/{extension}/version/{provider?}.{_format}',
    name: 'badge.version',
    requirements: [
        'extension' => '[a-z0-9_]+',
        '_format' => 'json|svg',
    ],
    options: [
        'title' => 'Latest version',
        'description' => 'Get JSON data for current extension version.',
    ],
    methods: ['GET'],
    format: 'json',
)]
final class VersionBadgeController extends AbstractBadgeController
{
    public function __construct(
        private readonly ApiService $apiService,
    ) {}

    public function __invoke(Request $request, string $extension, ?string $provider = null): Response
    {
        $extensionMetadata = $this->apiService->getExtensionMetadata($extension);
        $version = $extensionMetadata[0]['current_version']['number']
            ?? throw new BadRequestHttpException('Invalid API response.');

        return $this->getBadgeResponse(
            $request,
            Badge::forVersion($version),
            $provider,
            $extensionMetadata->getExpiryDate(),
        );
    }
}
