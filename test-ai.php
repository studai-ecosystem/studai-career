<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test Tharini's sessions
$tharini = \App\Models\User::where('email', 'tharini@gmail.com')->first();
if ($tharini) {
    echo "=== THARINI (ID: {$tharini->id}) ===" . PHP_EOL;
    $sessions = \App\Models\CareerCoachSession::where('user_id', $tharini->id)->latest()->limit(5)->get();
    foreach ($sessions as $s) {
        echo "Session {$s->id}: {$s->title} [{$s->status}] msgs:{$s->message_count}" . PHP_EOL;
    }
    $activeSession = $sessions->where('status', 'active')->first();
    if ($activeSession) {
        echo PHP_EOL . "Testing sendMessage for session {$activeSession->id}..." . PHP_EOL;
        $service = app(\App\Services\AI\CareerCoachService::class);
        $service->forUser($tharini);
        try {
            $msg = $service->sendMessage($activeSession, 'yes, tell me more', false);
            echo 'SUCCESS: ' . substr($msg->content, 0, 200) . PHP_EOL;
        } catch (\Exception $e) {
            echo 'FAIL: ' . $e->getMessage() . PHP_EOL;
            echo $e->getFile() . ':' . $e->getLine() . PHP_EOL;
        }
    }
}
echo PHP_EOL . "=== Original test ===" . PHP_EOL;


$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$key = config('ai.azure.api_key');
$endpoint = config('ai.azure.endpoint');
$deployment = config('ai.azure.deployment_id');
$apiVersion = config('ai.azure.api_version');
$url = rtrim($endpoint, '/') . "/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";

echo "Testing Azure OpenAI..." . PHP_EOL;
echo "URL: " . $url . PHP_EOL;

try {
    $response = \Illuminate\Support\Facades\Http::timeout(15)
        ->withHeaders(['api-key' => $key, 'Content-Type' => 'application/json'])
        ->post($url, [
            'messages' => [['role' => 'user', 'content' => 'Say "hello" in one word only.']],
            'max_completion_tokens' => 20,
        ]);

    echo "Status: " . $response->status() . PHP_EOL;
    $data = $response->json();
    echo "Response: " . ($data['choices'][0]['message']['content'] ?? 'NO CONTENT') . PHP_EOL;
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}

// Test a Livewire message send
echo PHP_EOL . "Testing CareerCoachService..." . PHP_EOL;
try {
    $user = \App\Models\User::first();
    $session = \App\Models\CareerCoachSession::where('user_id', $user->id)
        ->where('status', 'active')
        ->first();
    
    if (!$session) {
        echo "No active session found" . PHP_EOL;
    } else {
        echo "Session ID: " . $session->id . PHP_EOL;
        $service = app(\App\Services\AI\CareerCoachService::class);
        $service->forUser($user);
        $msg = $service->sendMessage($session, 'test', false);
        echo "SUCCESS! Response: " . substr($msg->content, 0, 150) . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "CareerCoachService ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
