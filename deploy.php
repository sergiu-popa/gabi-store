<?php

namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'magazin');

// Project repository
set('repository', 'git@gitlab.com:popadevs/gabi-store.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('shared_dirs', ['var/log', 'var/sessions']);
set('shared_files', ['.env']);
set('writable_dirs', ['var']);
set('migrations_config', '');

// Hosts

host('89.33.25.175')
    ->user('deployer')
    ->identityFile('~/.ssh/id_rsa')
    ->port(22)
    ->set('deploy_path', '/home/admin/web/magazin.respiro.ro');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');
