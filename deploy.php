<?php

namespace Deployer;

require 'recipe/symfony.php';

// Project name
set('application', 'gabi-store');

// Project repository
set('repository', 'git@github.com:sergiu-popa/gabi-store.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('shared_dirs', ['var/log', 'var/sessions']);
set('shared_files', ['.env', 'var/data.db',]);
set('writable_dirs', ['var']);
set('migrations_config', '');

// Hosts
host('164.90.236.77')
    ->setRemoteUser('admin')
    ->setIdentityFile('~/.ssh/id_rsa')
    ->setPort(22)
    ->set('http_user', 'www-data')
    ->set('deploy_path', '/home/admin/web/magazin.imno.dev');

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

task('deploy:assets:install', function () {
    run('cd {{release_path}} && bin/console assets:install --symlink');
});

after('deploy:vendors', 'deploy:assets:install');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
