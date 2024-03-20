<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021-2024 Elias Häußler <elias@haeussler.dev>
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

namespace Deployer;

require 'recipe/common.php';
require 'contrib/rsync.php';

// Config
set('application', 'typo3-badges');
set('keep_releases', 3);

// Rsync
set('rsync_src', __DIR__);
set('rsync', [
    'exclude' => [
        '/.github',
        '/.git',
        '/assets',
        '/bin/phpunit',
        '/tests',
        '/var',
        '/.babelrc',
        '/.depcheckrc.json',
        '/.editorconfig',
        '/.env.test',
        '/.eslintrc.js',
        '/.gitattributes',
        '/.gitignore',
        '/.php-cs-fixer.dist.php',
        '.stylelintrc.json',
        'CODEOWNERS',
        'deploy.php',
        'phpstan.php',
        'phpunit.xml',
        'postcss.config.js',
        'rector.php',
        'renovate.json',
        'tailwind.config.js',
        'webpack.config.js',
    ],
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'az',
    'options' => ['delete', 'delete-after', 'force'],
    'timeout' => 3600,
]);
set('shared_files', [
    '.env.local',
]);
set('shared_dirs', [
    'var/log',
]);

// Symfony
set('bin/console', '{{bin/php}} {{release_or_current_path}}/bin/console');

// Hosts
host('production')
    ->set('hostname', 'cp232.sp-server.net')
    ->set('remote_user', 'eliashae')
    ->set('http_user', 'eliashae')
    ->set('writable_mode', 'chmod')
    ->set('deploy_path', '~/html/typo3-badges.dev')
    ->add('env', ['APP_ENV' => 'prod'])
;
host('dev')
    ->set('hostname', 'cp232.sp-server.net')
    ->set('remote_user', 'eliashae')
    ->set('http_user', 'eliashae')
    ->set('writable_mode', 'chmod')
    ->set('deploy_path', '~/html/pre.typo3-badges.dev')
    ->add('shared_files', ['.htaccess'])
    ->add('env', ['APP_ENV' => 'prod', 'APP_DEBUG' => '1'])
;

// Symfony tasks
task('symfony:cache:clear', function () { run('{{bin/console}} cache:clear'); });
task('symfony:cache:warmup', function () { run('{{bin/console}} cache:warmup'); });
task('symfony', [
    'symfony:cache:clear',
    'symfony:cache:warmup',
]);

// Main deploy task
task('deploy', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
    'deploy:writable',
    'deploy:symlink',
    'symfony',
    'deploy:unlock',
    'deploy:cleanup',
    'deploy:success',
])->desc('Deploy your project');

// Unlock after failed
after('deploy:failed', 'deploy:unlock');
