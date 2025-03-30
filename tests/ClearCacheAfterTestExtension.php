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

namespace App\Tests;

use Override;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ClearCacheAfterTestExtension.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ClearCacheAfterTestExtension implements Extension, FinishedSubscriber
{
    #[Override]
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber($this);
    }

    #[Override]
    public function notify(Finished $event): void
    {
        new Filesystem()->remove(__DIR__.'/../var/cache/test');
    }
}
