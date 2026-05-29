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

namespace App\Badge\Provider;

use App\Exception\InvalidProviderException;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

use function iterator_to_array;

/**
 * BadgeProviderFactory.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class BadgeProviderFactory
{
    /**
     * @param ServiceLocator<BadgeProvider> $providers
     */
    public function __construct(
        #[AutowireLocator('badge.provider')]
        private ServiceLocator $providers,
    ) {}

    /**
     * @throws InvalidProviderException
     */
    public function get(string $name): BadgeProvider
    {
        if ($this->providers->has($name)) {
            return $this->providers->get($name);
        }

        throw InvalidProviderException::create($name);
    }

    /**
     * @return array<string, BadgeProvider>
     */
    public function getAll(): array
    {
        return iterator_to_array($this->providers);
    }
}
