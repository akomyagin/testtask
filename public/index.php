<?php

use App\Classes\Kernel;

require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__ . '/../src/config.php';
$kernel = new Kernel($config);
$kernel->run();
