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

use EliasHaeussler\PHPStanConfig;

return PHPStanConfig\Config\Config::create(__DIR__)
    ->in(
        'bin',
        'config',
        'public',
        'src',
        'tests',
    )
    ->withBleedingEdge([
        'internalTag' => false,
    ])
    ->withSet(static function (PHPStanConfig\Set\SymfonySet $set) {
        $set->withContainerXmlPath('var/cache/dev/App_KernelDevDebugContainer.xml');
    })
    ->level(8)
    ->toArray()
;
