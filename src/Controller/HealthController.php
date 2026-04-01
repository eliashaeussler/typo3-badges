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

namespace App\Controller;

use App\Health\HealthCheck;
use App\Health\HealthState;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route(path: '/health', methods: ['GET'])]
final readonly class HealthController
{
    /**
     * @param iterable<HealthCheck> $healthChecks
     */
    public function __construct(
        #[AutowireIterator('app.health_check')]
        private iterable $healthChecks,
    ) {}

    public function __invoke(): Response
    {
        $statusCode = 200;
        $checks = [];

        foreach ($this->healthChecks as $healthCheck) {
            try {
                $state = $healthCheck->check();
            } catch (Throwable $exception) {
                $state = HealthState::fromException($exception);
            }

            if (!$state->healthy) {
                $statusCode = 424;
            }

            $checks[$healthCheck->getName()] = $state->toArray();
        }

        return new JsonResponse($checks, $statusCode);
    }
}
