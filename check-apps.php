<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$user = \Illuminate\Support\Facades\DB::table("users")->where("email","companay@gmail.com")->first();
if (!$user) { echo "NOT FOUND\n"; exit; }
echo "account_type: " . $user->account_type . "\n";
foreach(["password","password123","Password123","admin123","123456","secret"] as $p) {
    $ok = \Illuminate\Support\Facades\Hash::check($p, $user->password) ? "YES" : "no";
    echo "  check \"$p\": $ok\n";
}
\Illuminate\Support\Facades\DB::table("users")->where("email","companay@gmail.com")->update(["password"=>\Illuminate\Support\Facades\Hash::make("password123")]);
echo "Reset to: password123\n";
