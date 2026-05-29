<?php
/**
 * Route/page smoke test (fast, incremental).
 * Logs in as a jobseeker and GETs every parameter-fillable GET route.
 * Writes results to smoke-results.txt as it goes. AI-heavy routes are skipped
 * to keep runtime reasonable (they are exercised separately).
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Mimic production: lazy-loading prevention & strict mode are local-only in this
// app, so disable them here to surface only genuine production-equivalent failures.
\Illuminate\Database\Eloquent\Model::preventLazyLoading(false);
\Illuminate\Database\Eloquent\Model::shouldBeStrict(false);

$out = fopen(__DIR__ . '/smoke-results.txt', 'w');
$w = function (string $s) use ($out) { fwrite($out, $s . PHP_EOL); fflush($out); };

$sampleParams = [
    'id' => '17', 'job' => '17', 'jobId' => '17', 'roundId' => '1', 'roundid' => '1',
    'application' => '46', 'user' => '24', 'slug' => 'test', 'token' => 'test',
    'uuid' => '1', 'plan' => '1', 'session' => '1', 'category' => 'tech',
];

// Skip prefixes that trigger long synchronous AI generation.
$aiHeavy = [
    'jobs/{jobId}/rounds', 'interview', 'career-coach', 'negotiation/chatbot',
    'agent/', 'resume/generate', 'cover-letter',
];

$jobseekerId = optional(App\Models\User::where('email', 'jobseeker@studai.com')->first())->id ?? 24;

$ok = $redir = $client = $server = $skipped = 0;
$serverList = []; $clientList = [];

foreach (Route::getRoutes() as $route) {
    if (!in_array('GET', $route->methods(), true)) continue;
    $uri = $route->uri();

    if (str_starts_with($uri, 'api/') || str_starts_with($uri, '_') || str_starts_with($uri, 'sanctum') ||
        str_starts_with($uri, 'livewire') || str_starts_with($uri, 'storage') || str_starts_with($uri, 'telescope') ||
        str_starts_with($uri, 'horizon') || $uri === '{fallbackPlaceholder}') { $skipped++; continue; }

    foreach ($aiHeavy as $p) { if (str_starts_with($uri, $p)) { $skipped++; continue 2; } }

    $path = $uri; $unfilled = false;
    if (preg_match_all('/\{(\w+)\??\}/', $uri, $m)) {
        foreach ($m[1] as $param) {
            if (isset($sampleParams[$param])) {
                $path = preg_replace('/\{' . $param . '\??\}/', $sampleParams[$param], $path);
            } elseif (str_contains($uri, '{' . $param . '?}')) {
                $path = preg_replace('/\/?\{' . $param . '\?\}/', '', $path);
            } else { $unfilled = true; }
        }
    }
    if ($unfilled) { $skipped++; continue; }

    Auth::loginUsingId($jobseekerId);
    try {
        $request = Request::create('/' . ltrim($path, '/'), 'GET');
        $request->setLaravelSession($app['session']->driver());
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        if ($status >= 500)     { $server++; $serverList[] = "$status  /$path  ($uri)"; $w("5xx $status /$path ($uri)"); }
        elseif ($status >= 400) { $client++; if (!in_array($status,[401,403,404,419],true)) { $clientList[] = "$status  /$path  ($uri)"; $w("4xx $status /$path ($uri)"); } }
        elseif ($status >= 300) { $redir++; }
        else                    { $ok++; }
    } catch (\Throwable $e) {
        $server++; $serverList[] = "EXC  /$path -> " . get_class($e) . ': ' . $e->getMessage();
        $w("EXC /$path -> " . get_class($e) . ': ' . $e->getMessage());
    }
}

$w('');
$w('==== SUMMARY ====');
$w("OK 2xx:       $ok");
$w("Redirect 3xx: $redir");
$w("Client 4xx:   $client (excl 401/403/404/419 shown above)");
$w("Server 5xx:   $server");
$w("Skipped:      $skipped");
$w('');
$w('---- SERVER ERRORS ----');
foreach ($serverList as $r) $w($r);
if (!$serverList) $w('(none)');
$w('---- UNEXPECTED CLIENT ERRORS ----');
foreach ($clientList as $r) $w($r);
if (!$clientList) $w('(none)');
fclose($out);
echo "DONE ok=$ok redir=$redir client=$client server=$server skipped=$skipped" . PHP_EOL;
