<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
Illuminate\Support\Facades\DB::flushQueryLog();
Illuminate\Support\Facades\DB::enableQueryLog();
$request = Illuminate\Http\Request::create('/careers', 'GET', ['company' => 999]);
$controller = new Webkul\Recruitment\Http\Controllers\CustomerJobController;
$response = $controller->index($request);
file_put_contents('debug_careers_company.html', $response->render());
file_put_contents('debug_careers_company_queries.json', json_encode(Illuminate\Support\Facades\DB::getQueryLog(), JSON_PRETTY_PRINT));
