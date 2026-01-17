<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Create request
$request = Request::capture();

// Set up kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Handle the request to bootstrap the application
$response = $kernel->handle($request);

// Check for migration token (security)
$migrationToken = env('MIGRATION_TOKEN');
$providedToken = $request->header('X-Migration-Token') ?? $request->query('token');

if ($migrationToken && $providedToken !== $migrationToken) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized: Invalid migration token'
    ], JSON_PRETTY_PRINT);
    exit;
}

// Run seeders
try {
    $artisan = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    
    // Run seeders
    $exitCode = $artisan->call('db:seed', ['--force' => true]);
    $output = $artisan->output();
    
    if ($exitCode === 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Seeders completed successfully',
            'output' => $output
        ], JSON_PRETTY_PRINT);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Seeding failed',
            'output' => $output,
            'exit_code' => $exitCode
        ], JSON_PRETTY_PRINT);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Seeding failed',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}

$kernel->terminate($request, $response);

