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

use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\CountOnNullRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony34\Rector\Closure\ContainerGetNameToTypeInTestsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $rectorConfig->symfonyContainerXml(__DIR__.'/var/cache/dev/App_KernelDevDebugContainer.xml');

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SymfonyLevelSetList::UP_TO_SYMFONY_62,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_100,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);

    $rectorConfig->skip([
        AddLiteralSeparatorToNumberRector::class,
        AnnotationToAttributeRector::class => [
            __DIR__.'/src/Badge/Provider/BadgenBadgeProvider.php',
            __DIR__.'/src/Badge/Provider/ShieldsBadgeProvider.php',
            __DIR__.'/src/Cache/RandomExtensionMetadataCacheWarmer.php',
        ],
        ContainerGetNameToTypeInTestsRector::class,
        CountOnNullRector::class => [
            __DIR__.'/tests/Controller/HomepageControllerTest.php',
        ],
    ]);
};
