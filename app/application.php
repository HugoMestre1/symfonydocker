<?php

require __DIR__ . '/vendor/autoload.php';

use App\Commands\CountryCommand;
use Symfony\Component\Console\Application;

$app = new Application();

$app->add(new CountryCommand());

$app->run();