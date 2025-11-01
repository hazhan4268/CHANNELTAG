<?php
/**
 * Simple test page to check if PHP is working
 */

echo "<h1>PHP Test Page</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";

echo "<h2>Required Extensions</h2>";
$extensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring', 'openssl'];
foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? '✅ Available' : '❌ Missing';
    echo "<p>{$ext}: {$status}</p>";
}

echo "<h2>File System</h2>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Project Root: " . dirname(__DIR__) . "</p>";

$paths = [
    'Install Dir' => __DIR__ . '/../install/',
    'Admin Dir' => __DIR__ . '/admin/',
    'Source Dir' => __DIR__ . '/../src/',
    'Config Dir' => __DIR__ . '/../config/',
];

foreach ($paths as $name => $path) {
    $status = is_dir($path) ? '✅ Exists' : '❌ Missing';
    echo "<p>{$name}: {$status} ({$path})</p>";
}

echo "<h2>Quick Links</h2>";
echo '<a href="/install/">Go to Installer</a><br>';
echo '<a href="/admin/debug.php">Go to Debug Page</a><br>';
echo '<a href="/admin/login.php">Go to Admin Login</a><br>';
?>