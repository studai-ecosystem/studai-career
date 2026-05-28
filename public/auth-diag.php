<?php
/**
 * StudAI Career — Authentication & Database Diagnostic Tool
 * Token-protected. Do NOT expose without the token.
 */
declare(strict_types=1);

$token = $_GET['token'] ?? '';
if ($token !== 'studai-diag-2024') {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: application/json');

// Bootstrap Laravel
$appRoot = dirname(__DIR__);
require $appRoot . '/vendor/autoload.php';
$app = require_once $appRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

$pdo = null;
$dbError = null;
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        env('DB_HOST', '127.0.0.1'),
        env('DB_PORT', '3306'),
        env('DB_DATABASE', '')
    );
    $pdo = new PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
    ]);
} catch (\Exception $e) {
    $dbError = $e->getMessage();
}

$action = $_GET['action'] ?? 'status';
$result = [];

// ---- Status / info ----
if ($action === 'status' || $action === 'info') {
    $tables = [];
    $missingTables = [];
    $required = [
        'users', 'jobs', 'applications', 'resumes', 'cover_letters',
        'ai_conversations', 'negotiation_strategies', 'negotiation_sessions',
        'negotiation_messages', 'ai_credit_logs', 'user_subscriptions',
        'subscription_plans', 'profiles', 'saved_jobs',
    ];

    if ($pdo) {
        $rows = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $tables = $rows;
        $missingTables = array_values(array_diff($required, $tables));
    }

    $users = [];
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT id, name, email, account_type, created_at FROM users ORDER BY id LIMIT 20");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $users = ['error' => $e->getMessage()];
        }
    }

    $result = [
        'status'           => $dbError ? 'db_error' : 'ok',
        'db_error'         => $dbError,
        'php_version'      => PHP_VERSION,
        'laravel_env'      => env('APP_ENV', 'unknown'),
        'app_url'          => env('APP_URL', 'unknown'),
        'tables_count'     => count($tables),
        'missing_tables'   => $missingTables,
        'users'            => $users,
    ];
}

// ---- Last migrations ----
if ($action === 'migrations') {
    $migrations = [];
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT migration, batch FROM migrations ORDER BY batch DESC, migration DESC LIMIT 30");
            $migrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $migrations = ['error' => $e->getMessage()];
        }
    }
    $result = ['last_migrations' => $migrations];
}

// ---- Run pending migrations (use with caution) ----
if ($action === 'migrate') {
    if (!$pdo) {
        $result = ['error' => 'DB connection failed: ' . $dbError];
    } else {
        ob_start();
        $exitCode = null;
        try {
            $artisan = Illuminate\Support\Facades\Artisan::call('migrate', [
                '--force'          => true,
                '--no-interaction' => true,
            ]);
            $output = Artisan::output();
            $exitCode = $artisan;
        } catch (\Exception $e) {
            $output = $e->getMessage();
            $exitCode = 1;
        }
        ob_end_clean();

        // Get last 10 migrations after run
        $stmt = $pdo->query("SELECT migration, batch FROM migrations ORDER BY batch DESC, migration DESC LIMIT 10");
        $lastMigrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [
            'migrate_output'  => $output,
            'exit_code'       => $exitCode,
            'last_migrations' => $lastMigrations,
        ];
    }
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
