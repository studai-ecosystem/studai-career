<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = App\Models\User::select('id','email','account_type','name')->get();
foreach($users as $u) {
    echo $u->id.' | '.$u->name.' | '.$u->email.' | '.$u->account_type.PHP_EOL;
}
