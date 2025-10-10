<?php

/**
 * CodeIgniter 4
 *
 * An open source application development framework for PHP
 *
 * This is the main entry point for the application.
 */

define('CI_START', microtime(true));

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 *
 * This process sets up the path constants, loads the framework,
 * and gets the application ready to run.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../app/Config/Boot/boot.php';

$app->run();
