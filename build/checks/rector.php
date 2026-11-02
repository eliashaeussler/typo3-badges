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

use Rector\Config\RectorConfig;

$rootPath = dirname(__DIR__, 2);

return RectorConfig::configure()
    ->withPaths([
        $rootPath.'/src',
        $rootPath.'/tests',
    ])
    ->withPhpSets(php85: true)
    ->withPreparedSets(phpunitMockToStub: true, doctrineCodeQuality: true, symfonyCodeQuality: true, symfonyConfigs: true)
    ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
    ->withSymfonyContainerXml($rootPath.'/var/cache/dev/App_KernelDevDebugContainer.xml')
;
