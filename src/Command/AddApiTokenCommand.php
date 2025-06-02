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

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;

/**
 * AddApiTokenCommand.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[AsCommand(
    name: 'api-token:add',
    description: 'Add a new API token',
)]
final class AddApiTokenCommand extends Command
{
    public function __construct(
        private readonly ApiTokenRepository $apiTokenRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $accessToken = $io->askHidden('Access token', validator: $this->validateInput(...));
        $refreshToken = $io->askHidden('Refresh token', validator: $this->validateInput(...));
        $rawExpiryDate = $io->ask('Expiry date (YYYY-MM-DD)', validator: $this->validateInput(...));
        $expiryDate = DateTimeImmutable::createFromFormat('!Y-m-d', $rawExpiryDate);

        if (false === $expiryDate) {
            $io->error('Expiry date is not valid, it must follow the input format YYYY-MM-DD. Example: 2025-12-31');

            return self::FAILURE;
        }

        $apiToken = new ApiToken();
        $apiToken->setAccessToken($accessToken);
        $apiToken->setRefreshToken($refreshToken);
        $apiToken->setExpiryDate($expiryDate);

        $this->apiTokenRepository->save($apiToken, true);

        $io->success(sprintf('Successfully added API token #%d.', $apiToken->getId()));

        return self::SUCCESS;
    }

    private function validateInput(mixed $input): string
    {
        if (!is_string($input) || '' === trim($input)) {
            throw new RuntimeException('The given input must not be empty.', 1748868669);
        }

        return $input;
    }
}
