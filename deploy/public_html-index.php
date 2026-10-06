<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Front controller — paths point into ../nexo, NOT ../
|--------------------------------------------------------------------------
| The application lives in a sibling directory (nexo/) rather than inside
| this document root, so every path below carries an extra /nexo segment.
|
| Do NOT replace this file by copying nexo/public/index.php over it. That
| copy is correct only while it sits inside nexo/public/; here its relative
| paths resolve one tree too high, vendor/autoload.php is not found, and the
| whole site answers 500 with an empty body before Laravel can log a thing.
| That is exactly what happened on 6 October 2026.
*/

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../nexo/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../nexo/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../nexo/bootstrap/app.php')
    ->handleRequest(Request::capture());
