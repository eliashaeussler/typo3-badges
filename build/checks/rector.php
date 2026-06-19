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

use EliasHaeussler\RectorConfig\Config\Config;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Symfony\Symfony34\Rector\Closure\ContainerGetNameToTypeInTestsRector;

return static function (RectorConfig $rectorConfig): void {
    $rootPath = dirname(__DIR__, 2);

    $rectorConfig->symfonyContainerXml($rootPath.'/var/cache/dev/App_KernelDevDebugContainer.xml');

    Config::create($rectorConfig)
        ->in(
            $rootPath.'/src',
            $rootPath.'/tests',
        )
        ->withSymfony()
        ->withPHPUnit()
        ->skip(AnnotationToAttributeRector::class, [
            $rootPath.'/src/Badge/Provider/BadgenBadgeProvider.php',
            $rootPath.'/src/Badge/Provider/ShieldsBadgeProvider.php',
            $rootPath.'/src/Cache/RandomExtensionMetadataCacheWarmer.php',
        ])
        ->skip(ContainerGetNameToTypeInTestsRector::class, [
            $rootPath.'/tests/Badge/Provider/BadgenBadgeProviderTest.php',
            $rootPath.'/tests/Badge/Provider/RoutingTraitTest.php',
            $rootPath.'/tests/Badge/Provider/ShieldsBadgeProviderTest.php',
            $rootPath.'/tests/Controller/HomepageControllerTest.php',
        ])
        ->apply()
    ;
};
