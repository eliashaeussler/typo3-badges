<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
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

namespace App\Asset\VersionStrategy;

use SebastianFeldmann\Git\Repository;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * GitVersionStrategy.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class GitVersionStrategy implements VersionStrategyInterface
{
    private string $format;
    private ?string $revision;

    public function __construct(
        string $format = null,
    ) {
        $this->format = $format ?: '%s?v=%s';
        $this->revision = $this->loadRevisionFromGitRepository() ?? $this->loadRevisionFromFile();
    }

    public function getVersion(string $path): string
    {
        if (null === $this->revision) {
            return '';
        }

        return substr($this->revision, 0, 7);
    }

    public function applyVersion(string $path): string
    {
        $version = $this->getVersion($path);

        if ('' === $version) {
            return $path;
        }

        return sprintf($this->format, $path, $version);
    }

    private function loadRevisionFromGitRepository(): ?string
    {
        $repository = new Repository();
        $info = $repository->getInfoOperator();

        try {
            return $info->getCurrentCommitHash() ?: null;
        } catch (\Exception) {
            return null;
        }
    }

    private function loadRevisionFromFile(): ?string
    {
        $revisionFile = dirname(__DIR__, 3).'/REVISION';

        if (file_exists($revisionFile)) {
            return file_get_contents($revisionFile) ?: null;
        }

        return null;
    }
}
