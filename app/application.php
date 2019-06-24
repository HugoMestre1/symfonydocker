<?php

require __DIR__ . '/vendor/autoload.php';

use App\Commands\CountryCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;

$app       = new Application();
$container = new Container(); // For injectable the container for get the doctrine service
$app->add(new CountryCommand($container));

$app->run();