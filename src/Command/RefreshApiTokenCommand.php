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

namespace App\Command;

use App\Repository\ApiTokenRepository;
use App\Service\ApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * RefreshApiTokenCommand.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[AsCommand(
    name: 'api-token:refresh',
    description: 'Refreshes the current API token',
)]
final class RefreshApiTokenCommand extends Command
{
    public function __construct(
        private readonly ApiTokenRepository $apiTokenRepository,
        private readonly ApiService $apiService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $expiredApiTokens = $this->apiTokenRepository->findExpired();

        if ([] === $expiredApiTokens) {
            $io->success('All API tokens are up to date.');

            return self::SUCCESS;
        }

        foreach ($expiredApiTokens as $apiToken) {
            $this->apiService->refreshApiToken($apiToken);

            $io->success(sprintf('Successfully refreshed API token #%d.', $apiToken->getId()));
        }

        return self::SUCCESS;
    }
}
