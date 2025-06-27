<?php
/**
 * Local Development Diagnostic
 * 
 * This page helps diagnose issues in local development environment.
 * Access via: http://localhost/PhpLab/local_test.php
 */

// Only allow on localhost
if ($_SERVER['HTTP_HOST'] !== 'localhost' && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false) {
    die('This diagnostic page is only available on localhost.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Development Test - KartRider Analytics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .pass { background-color: #d4edda; color: #155724; }
        .fail { background-color: #f8d7da; color: #721c24; }
        .warning { background-color: #fff3cd; color: #856404; }
        .info { background-color: #d1ecf1; color: #0c5460; }
        h1 { color: #333; }
        .result { margin: 10px 0; padding: 10px; border-radius: 3px; }
        code { background: #f8f9fa; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🏠 Local Development Diagnostic</h1>
    <p>Testing your local XAMPP environment...</p>

    <!-- PHP Environment Test -->
    <div class="test-section">
        <h2>PHP Environment</h2>
        
        <?php
        $phpVersion = phpversion();
        if (version_compare($phpVersion, '7.4', '>=')) {
            echo '<div class="result pass">✅ PHP Version: ' . $phpVersion . ' (Good)</div>';
        } else {
            echo '<div class="result warning">⚠️ PHP Version: ' . $phpVersion . ' (May have issues)</div>';
        }
        
        $requiredExtensions = ['pdo', 'pdo_mysql', 'json'];
        foreach ($requiredExtensions as $ext) {
            if (extension_loaded($ext)) {
                echo '<div class="result pass">✅ Extension ' . $ext . ' loaded</div>';
            } else {
                echo '<div class="result fail">❌ Extension ' . $ext . ' missing</div>';
            }
        }
        ?>
    </div>

    <!-- Configuration Test -->
    <div class="test-section">
        <h2>Configuration Test</h2>
        
        <?php
        try {
            require_once '../../config.php';
            echo '<div class="result pass">✅ config.php loaded successfully</div>';
            
            echo '<div class="result info">📋 Database Configuration:</div>';
            echo '<div class="result info">Host: ' . DB_HOST . '</div>';
            echo '<div class="result info">User: ' . DB_USER . '</div>';
            echo '<div class="result info">Database: ' . DB_NAME . '</div>';
            echo '<div class="result info">Debug Mode: ' . (DEBUG_MODE ? 'ON' : 'OFF') . '</div>';
            
        } catch (Exception $e) {
            echo '<div class="result fail">❌ Config error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <!-- Database Connection Test -->
    <div class="test-section">
        <h2>Database Connection Test</h2>
        
        <?php
        try {
            require_once '../../includes/DatabaseService.php';
            $db = DatabaseService::getInstance();
            
            if ($db->isConnected()) {
                echo '<div class="result pass">✅ Database connected successfully</div>';
                
                // Test basic query
                try {
                    $tables = $db->fetchAll("SHOW TABLES");
                    echo '<div class="result pass">✅ Found ' . count($tables) . ' tables in database</div>';
                    
                    if (count($tables) > 0) {
                        echo '<div class="result info">📋 Tables: ';
                        foreach ($tables as $table) {
                            echo current($table) . ' ';
                        }
                        echo '</div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="result warning">⚠️ Query test failed: ' . $e->getMessage() . '</div>';
                }
            } else {
                echo '<div class="result fail">❌ Database connection failed</div>';
            }
        } catch (Exception $e) {
            echo '<div class="result fail">❌ Database service error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <!-- File System Test -->
    <div class="test-section">
        <h2>File System Test</h2>
        
        <?php
        $criticalFiles = [
            'config.php',
            'index.php',
            'includes/DatabaseService.php',
            'includes/BaseController.php',
            'views/layout.php',
            'assets/style.css',
            'assets/tabs.js'
        ];
        
        foreach ($criticalFiles as $file) {
            if (file_exists($file)) {
                if (is_readable($file)) {
                    echo '<div class="result pass">✅ ' . $file . ' exists and readable</div>';
                } else {
                    echo '<div class="result warning">⚠️ ' . $file . ' exists but not readable</div>';
                }
            } else {
                echo '<div class="result fail">❌ ' . $file . ' missing</div>';
            }
        }
        ?>
    </div>

    <!-- Asset Helper Test -->
    <div class="test-section">
        <h2>Asset Helper Test</h2>
        
        <?php
        try {
            require_once '../../includes/AssetHelper.php';
            echo '<div class="result pass">✅ AssetHelper loaded</div>';
            
            if (function_exists('getBaseUrl')) {
                $baseUrl = getBaseUrl();
                echo '<div class="result info">Base URL: ' . htmlspecialchars($baseUrl) . '</div>';
                
                if (function_exists('asset')) {
                    $testAsset = asset('assets/style.css');
                    echo '<div class="result info">Test Asset URL: ' . htmlspecialchars($testAsset) . '</div>';
                }
            }
        } catch (Exception $e) {
            echo '<div class="result warning">⚠️ AssetHelper issue: ' . $e->getMessage() . '</div>';
            echo '<div class="result info">💡 This is OK - fallback to simple paths will work</div>';
        }
        ?>
    </div>

    <!-- Server Environment -->
    <div class="test-section">
        <h2>Server Environment</h2>
        
        <div class="result info">
            <strong>HTTP Host:</strong> <?= htmlspecialchars($_SERVER['HTTP_HOST']) ?><br>
            <strong>Script Name:</strong> <?= htmlspecialchars($_SERVER['SCRIPT_NAME']) ?><br>
            <strong>Document Root:</strong> <?= htmlspecialchars($_SERVER['DOCUMENT_ROOT']) ?><br>
            <strong>Request URI:</strong> <?= htmlspecialchars($_SERVER['REQUEST_URI']) ?><br>
            <strong>Server Software:</strong> <?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="test-section">
        <h2>Quick Actions</h2>
        
        <p>
            <a href="index.php">🏠 Go to Main Application</a> |
            <a href="asset_test.php">🎨 Test Assets</a> |
            <a href="deployment_check.php?run=true">🚀 Deployment Check</a>
        </p>
        
        <div class="result info">
            <strong>💡 If you see errors:</strong><br>
            1. Make sure XAMPP is running (Apache + MySQL)<br>
            2. Check that you're accessing via <code>http://localhost/PhpLab/</code><br>
            3. Verify the database exists and has data<br>
            4. Check PHP error logs in XAMPP control panel
        </div>
    </div>

</body>
</html>
