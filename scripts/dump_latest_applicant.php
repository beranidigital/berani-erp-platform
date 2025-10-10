<?php

$projectRoot = dirname(__DIR__);
require $projectRoot.'/vendor/autoload.php';
$app = require $projectRoot.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$row = Illuminate\Support\Facades\DB::table('recruitments_applicants')->orderByDesc('id')->first();
print_r($row);
