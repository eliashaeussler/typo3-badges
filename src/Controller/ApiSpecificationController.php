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

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * ApiSpecificationController.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Route(
    path: '/spec',
    methods: ['GET'],
)]
final class ApiSpecificationController
{
    public function __invoke(): Response
    {
        $specification = $this->loadSpecification();
        $json = \json_encode($specification, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_NUMERIC_CHECK);

        return JsonResponse::fromJsonString($json);
    }

    /**
     * @return array<string, mixed>
     */
    private function loadSpecification(): array
    {
        $apiSpecificationPath = dirname(__DIR__, 2).'/spec/typo3-badges.oas3.yaml';
        $fileContents = Yaml::parseFile($apiSpecificationPath);

        if (!is_array($fileContents)) {
            throw new BadRequestHttpException('Unable to load API specification tile.');
        }

        return $fileContents;
    }
}
