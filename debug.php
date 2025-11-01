<?php
/**
 * Debug page for admin panel
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Panel Debug</h1>";
echo "<style>body{font-family: Arial; margin: 20px;} h2{color: #333; border-bottom: 2px solid #ccc; padding-bottom: 5px;} .success{color: green;} .error{color: red;} .info{background: #f0f0f0; padding: 10px; margin: 10px 0;}</style>";

// Test 1: PHP Version and Extensions
echo "<h2>1. PHP Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";

$required_extensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring', 'openssl'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? '<span class="success">✓ Loaded</span>' : '<span class="error">✗ Missing</span>';
    echo "{$ext}: {$status}<br>";
}

// Test 2: File Structure
echo "<h2>2. File Structure</h2>";
$critical_files = [
    __DIR__ . '/../../src/autoload.php',
    __DIR__ . '/../../src/Bootstrap.php',
    __DIR__ . '/../../src/Config.php',
    __DIR__ . '/../../src/Controllers/AuthController.php',
    __DIR__ . '/../../src/Controllers/AdminController.php',
    __DIR__ . '/../../templates/admin/login.php',
    __DIR__ . '/../../templates/admin/layout.php',
];

foreach ($critical_files as $file) {
    $status = file_exists($file) ? '<span class="success">✓ Exists</span>' : '<span class="error">✗ Missing</span>';
    echo basename($file) . ": {$status}<br>";
}

// Test 3: Autoloader
echo "<h2>3. Autoloader Test</h2>";
try {
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        echo '<span class="success">✓ Composer autoloader loaded</span><br>';
    } else {
        require_once __DIR__ . '/../../src/autoload.php';
        echo '<span class="success">✓ Simple autoloader loaded</span><br>';
    }
} catch (\Exception $e) {
    echo '<span class="error">✗ Autoloader error: ' . htmlspecialchars($e->getMessage()) . '</span><br>';
}

// Test 4: Bootstrap
echo "<h2>4. Bootstrap Test</h2>";
try {
    require_once __DIR__ . '/../../src/Bootstrap.php';
    require_once __DIR__ . '/../../src/Config.php';
    \TelegramBot\Bootstrap::init();
    echo '<span class="success">✓ Bootstrap initialized</span><br>';
} catch (\Exception $e) {
    echo '<span class="error">✗ Bootstrap error: ' . htmlspecialchars($e->getMessage()) . '</span><br>';
}

// Test 5: Installation Status
echo "<h2>5. Installation Status</h2>";
$envPath = __DIR__ . '/../../config/.env.php';
if (file_exists($envPath)) {
    echo '<span class="success">✓ .env.php exists</span><br>';
    
    try {
        $config = \TelegramBot\Config::load();
        echo '<span class="success">✓ Config loaded</span><br>';
        echo "DB Name: " . htmlspecialchars($config['DB_NAME'] ?? 'N/A') . "<br>";
        echo "Bot Token: " . (isset($config['BOT_TOKEN']) && !empty($config['BOT_TOKEN']) ? "Set ✓" : "Not set ✗") . "<br>";
        
        // Check if actually installed
        $isInstalled = \TelegramBot\Config::isInstalled();
        echo "Installation Status: " . ($isInstalled ? '<span class="success">✓ Installed</span>' : '<span class="error">✗ Not Installed</span>') . "<br>";
        
    } catch (\Exception $e) {
        echo '<span class="error">✗ Config error: ' . htmlspecialchars($e->getMessage()) . '</span><br>';
    }
} else {
    echo '<span class="error">✗ .env.php does not exist - System not installed</span><br>';
    echo '<div class="info">Please run the installer first: <a href="/install/">Go to Installer</a></div>';
}

// Test 6: Database (only if config exists)
if (file_exists($envPath)) {
    echo "<h2>6. Database Test</h2>";
    try {
        $db = \TelegramBot\Config::getDb();
        echo '<span class="success">✓ Database connected</span><br>';
        
        // Test each table
        $tables = ['settings', 'channels', 'tags', 'posts', 'admins', 'install_flag'];
        foreach ($tables as $table) {
            try {
                $stmt = $db->query("SELECT COUNT(*) as cnt FROM {$table}");
                $result = $stmt->fetch();
                echo "{$table} table: " . ($result['cnt'] ?? 0) . " rows ✓<br>";
            } catch (\Exception $e) {
                echo '<span class="error">' . $table . ' table: ERROR - ' . $e->getMessage() . '</span><br>';
            }
        }
    } catch (\Exception $e) {
        echo '<span class="error">✗ Database error: ' . htmlspecialchars($e->getMessage()) . '</span><br>';
    }
}

// Test 7: Controllers (only if installed)
if (file_exists($envPath)) {
    echo "<h2>7. Controllers Test</h2>";
    try {
        require_once __DIR__ . '/../../src/Controllers/AuthController.php';
        echo '<span class="success">✓ AuthController loaded</span><br>';
        
        $auth = new \TelegramBot\Controllers\AuthController();
        echo '<span class="success">✓ AuthController created</span><br>';
        
        require_once __DIR__ . '/../../src/Controllers/AdminController.php';
        echo '<span class="success">✓ AdminController loaded</span><br>';
        
        $admin = new \TelegramBot\Controllers\AdminController();
        echo '<span class="success">✓ AdminController created</span><br>';
    } catch (\Exception $e) {
        echo '<span class="error">✗ Controller error: ' . htmlspecialchars($e->getMessage()) . '</span><br>';
        echo "File: " . htmlspecialchars($e->getFile()) . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
    }
}

// Test 8: Session
echo "<h2>8. Session Test</h2>";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo '<span class="success">✓ Session started</span><br>';
    echo "Session ID: " . session_id() . "<br>";
    echo "Admin ID in session: " . (isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 'Not set') . "<br>";
} catch (\Exception $e) {
    echo '<span class="error">✗ Session error: ' . htmlspecialchars($e->getMessage()) . '</span><br>';
}

echo "<hr>";
echo "<h2>Next Steps</h2>";
if (!file_exists($envPath)) {
    echo '<div class="info"><strong>Action Required:</strong> System is not installed. <a href="/install/">Run the installer</a></div>';
} else {
    echo '<div class="info">System appears to be configured. Try <a href="/admin/login.php">logging in</a></div>';
}

echo "<hr>";
echo "<h2>Quick Actions</h2>";
echo '<a href="/install/">Go to Installer</a> | ';
echo '<a href="/admin/login.php">Go to Login</a> | ';
echo '<a href="/admin/">Go to Admin Dashboard</a>';
