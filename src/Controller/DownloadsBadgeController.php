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

namespace App\Controller;

use App\Http\BadgeResponse;
use App\Service\ApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * DownloadsBadgeController.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Route(
    path: '/badge/{extension}/downloads',
    requirements: ['extension' => '[a-z0-9_]+'],
    methods: ['GET']
)]
final class DownloadsBadgeController
{
    public function __construct(
        private ApiService $apiService,
    ) {
    }

    public function __invoke(string $extension): Response
    {
        try {
            $apiResponse = $this->apiService->getExtensionMetadata($extension);
            $downloads = $apiResponse[0]['downloads'] ?? null;
        } catch (\Exception) {
            return BadgeResponse::forError()->create();
        }

        if (null !== $downloads) {
            return BadgeResponse::forDownloads($downloads)->create();
        }

        return BadgeResponse::forError()->create();
    }
}
