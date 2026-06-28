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

use ShipMonk\ComposerDependencyAnalyser;

$configuration = new ComposerDependencyAnalyser\Config\Configuration();
$configuration->ignoreErrorsOnPackages(
    [
        'sentry/sentry-symfony',
        'symfony/apache-pack',
        'symfony/asset',
        'symfony/console',
        'symfony/css-selector',
        'symfony/dotenv',
        'symfony/flex',
        'symfony/runtime',
        'symfony/security-bundle',
        'symfony/web-link',
        'symfony/web-profiler-bundle',
        'symfony/webpack-encore-bundle',
    ],
    [
        ComposerDependencyAnalyser\Config\ErrorType::UNUSED_DEPENDENCY,
    ],
);
$configuration->ignoreErrorsOnPackages(
    [
        'symfony/http-client',
    ],
    [
        ComposerDependencyAnalyser\Config\ErrorType::PROD_DEPENDENCY_ONLY_IN_DEV,
    ],
);

return $configuration;
