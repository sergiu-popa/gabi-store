<?php

namespace Tests;

use App\Factory\UserFactory;
use Symfony\Component\Dotenv\Dotenv;
use Zenstruck\Foundry\Test\TestState;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

TestState::addGlobalState(function () {
    UserFactory::new()->create();
});
