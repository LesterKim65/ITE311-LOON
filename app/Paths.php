<?php

/**
 * This is the Paths config file for CodeIgniter 4.
 * It contains the path constants that are used throughout the application.
 */

return [
    /*
     |--------------------------------------------------------------------------
     | System Directory
     |--------------------------------------------------------------------------
     |
     | The name of the directory that holds the application directories.
     |
     */
    'systemDirectory' => __DIR__ . '/../vendor/codeigniter4/framework/system',

    /*
     |--------------------------------------------------------------------------
     | Application Directory
     |--------------------------------------------------------------------------
     |
     | The name of the directory that holds the application directories.
     |
     */
    'applicationDirectory' => __DIR__,

    /*
     |--------------------------------------------------------------------------
     | Public Directory
     |--------------------------------------------------------------------------
     |
     | The name of the directory that holds the public files.
     |
     */
    'publicDirectory' => __DIR__ . '/../public',

    /*
     |--------------------------------------------------------------------------
     | Writable Directory
     |--------------------------------------------------------------------------
     |
     | The name of the directory that holds the writable files.
     |
     */
    'writableDirectory' => __DIR__ . '/../writable',

    /*
     |--------------------------------------------------------------------------
     | Tests Directory
     |--------------------------------------------------------------------------
     |
     | The name of the directory that holds the tests.
     |
     */
    'testsDirectory' => __DIR__ . '/../tests',

    /*
     |--------------------------------------------------------------------------
     | View Directory
     |--------------------------------------------------------------------------
     |
     | The name of the directory that holds the view files.
     |
     */
    'viewDirectory' => __DIR__ . '/Views',

    /*
     |--------------------------------------------------------------------------
     | Namespace
     |--------------------------------------------------------------------------
     |
     | The namespace for the application.
     |
     */
    'namespace' => 'App',

    /*
     |--------------------------------------------------------------------------
     | Composer Path
     |--------------------------------------------------------------------------
     |
     | The path to the composer autoload file.
     |
     */
    'composerPath' => __DIR__ . '/../vendor/autoload.php',
];
