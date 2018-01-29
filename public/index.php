<?php

// Set timezone
date_default_timezone_set('Europe/Amsterdam');

// Record the start time of the application
define('APPLICATION_START', microtime(true));

// Load dependencies
require(__DIR__ . '/../vendor/autoload.php');

// Create a new app instance
$app = new \App\Application(__DIR__.'/../');

// Run the app
$app->run();

