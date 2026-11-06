<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$publicDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if (str_ends_with($publicDir, '/public') && ! str_starts_with($_SERVER['REQUEST_URI'] ?? '', $publicDir.'/')) {
    $_SERVER['SCRIPT_NAME'] = substr($publicDir, 0, -7).'/index.php';
}

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
