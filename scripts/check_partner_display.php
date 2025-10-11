<?php

$projectRoot = dirname(__DIR__);
require $projectRoot.'/vendor/autoload.php';
$app = require $projectRoot.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$a = \Webkul\Recruitment\Models\Applicant::with(['candidate.partner'])->orderByDesc('id')->first();
if (! $a) {
    echo "No applicant found\n";
    exit;
}
echo 'partner_display: '.($a->partner_display ?? 'NULL').PHP_EOL;
echo 'applicant_properties: '.json_encode($a->applicant_properties).PHP_EOL;
