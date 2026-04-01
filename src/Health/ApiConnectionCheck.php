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

namespace App\Health;

use App\Service\ApiService;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * ApiConnectionCheck.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class ApiConnectionCheck implements HealthCheck
{
    public function __construct(
        private ApiService $apiService,
    ) {}

    public function check(): HealthState
    {
        try {
            $pingResponse = $this->apiService->sendPing();
        } catch (ExceptionInterface $exception) {
            return HealthState::fromException($exception);
        }

        return HealthState::fromResponse($pingResponse);
    }

    public function getName(): string
    {
        return 'connection';
    }
}
