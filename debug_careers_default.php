<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$request = Illuminate\Http\Request::create('/careers', 'GET', ['company' => 1]);
$controller = new Webkul\Recruitment\Http\Controllers\CustomerJobController();
$response = $controller->index($request);
file_put_contents('debug_careers_default.html', $response->render());
