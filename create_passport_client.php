<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a password grant client if one doesn't exist
if (!Laravel\Passport\Client::where('password_client', 1)->exists()) {
    $client = new Laravel\Passport\Client();
    $client->name = 'Password Grant Client';
    $client->secret = Illuminate\Support\Str::random(40);
    $client->redirect = 'http://realestate.localhost';
    $client->personal_access_client = false;
    $client->password_client = true;
    $client->revoked = false;
    $client->save();

    echo "Password grant client created successfully!\n";
    echo 'Client ID: '.$client->id."\n";
    echo 'Client Secret: '.$client->plainSecret."\n";
} else {
    $client = Laravel\Passport\Client::where('password_client', 1)->first();
    echo "Password grant client already exists!\n";
    echo 'Client ID: '.$client->id."\n";
    if ($client->secret) {
        echo "Client has a secret already set.\n";
    } else {
        $client->secret = Illuminate\Support\Str::random(40);
        $client->save();
        echo 'New Client Secret: '.$client->plainSecret."\n";
    }
}
