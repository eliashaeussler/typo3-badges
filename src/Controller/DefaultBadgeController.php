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

namespace App\Controller;

use App\Entity\Badge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * DefaultBadgeController.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Route(
    path: '/badge/typo3/{provider?}',
    name: 'badge.default',
    options: [
        'title' => 'TYPO3',
        'description' => 'Get JSON data for a generic TYPO3 badge.',
    ],
    methods: ['GET'],
    priority: -255,
)]
final class DefaultBadgeController extends AbstractBadgeController
{
    public function __invoke(Request $request, string $provider = null): Response
    {
        return $this->getBadgeResponse(new Badge(), $provider);
    }
}
