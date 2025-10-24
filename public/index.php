<?php

/**
 * CodeIgniter 4
 *
 * An open source application development framework for PHP
 *
 * This is the main entry point for the application.
 */

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 *
 * This process sets up the path constants, loads the framework,
 * and gets the application ready to run.
 */

require __DIR__ . '/../vendor/autoload.php';

$paths = new \Config\Paths();

$app = new \CodeIgniter\CodeIgniter($paths);

$app->run();
