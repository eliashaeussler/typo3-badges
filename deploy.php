<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

namespace Deployer;

require 'recipe/symfony.php';

// Config
set('repository', 'https://github.com/eliashaeussler/typo3-badges.git');
set('keep_releases', 3);

// Hosts
host('typo3-badges.dev')
    ->set('hostname', 'cp232.sp-server.net')
    ->set('remote_user', 'eliashae')
    ->set('http_user', 'eliashae')
    ->set('writable_mode', 'chmod')
    ->set('deploy_path', '~/html/typo3-badges.dev')
    ->add('env', ['APP_ENV' => 'prod'])
;

// Tasks
task('deploy:vendors', function () {
    upload('public/assets', '{{release_path}}/public');
    upload('vendor', '{{release_path}}');
});
task('database:migrate')->disable();

after('deploy:failed', 'deploy:unlock');
