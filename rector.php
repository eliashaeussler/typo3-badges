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

use EliasHaeussler\RectorConfig\Config\Config;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Symfony\Symfony34\Rector\Closure\ContainerGetNameToTypeInTestsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->symfonyContainerXml(__DIR__.'/var/cache/dev/App_KernelDevDebugContainer.xml');

    Config::create($rectorConfig)
        ->in(
            __DIR__.'/src',
            __DIR__.'/tests',
        )
        ->withSymfony()
        ->withPHPUnit()
        ->skip(AnnotationToAttributeRector::class, [
            __DIR__.'/src/Badge/Provider/BadgenBadgeProvider.php',
            __DIR__.'/src/Badge/Provider/ShieldsBadgeProvider.php',
            __DIR__.'/src/Cache/RandomExtensionMetadataCacheWarmer.php',
        ])
        ->skip(ContainerGetNameToTypeInTestsRector::class, [
            __DIR__.'/tests/Badge/Provider/BadgenBadgeProviderTest.php',
            __DIR__.'/tests/Badge/Provider/RoutingTraitTest.php',
            __DIR__.'/tests/Badge/Provider/ShieldsBadgeProviderTest.php',
            __DIR__.'/tests/Controller/HomepageControllerTest.php',
        ])
        ->apply()
    ;
};
