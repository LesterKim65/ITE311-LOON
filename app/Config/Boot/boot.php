<?php

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 *
 * This process sets up the path constants, loads the framework,
 * and gets the application ready to run.
 */

define('CI_START', microtime(true));

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../Paths.php';

$app = new \CodeIgniter\CodeIgniter($app);

return $app;
