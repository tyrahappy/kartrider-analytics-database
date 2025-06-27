<?php
/**
 * Test script to verify intelligent environment configuration
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Smart Config Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #e8f5e8; border-left: 4px solid #4caf50; }
        .warning { background: #fff3e0; border-left: 4px solid #ff9800; }
        .error { background: #ffebee; border-left: 4px solid #f44336; }
    </style>
</head>
<body>
    <h1>🧪 Smart Configuration Test</h1>
    
    <?php
    try {
        include 'config.php';
        echo '<div class="info success">';
        echo '<h3>✅ Configuration Loaded Successfully</h3>';
        echo '<p><strong>Environment:</strong> ' . ENVIRONMENT . '</p>';
        echo '<p><strong>Database Host:</strong> ' . DB_HOST . '</p>';
        echo '<p><strong>Database Name:</strong> ' . DB_NAME . '</p>';
        echo '<p><strong>Debug Mode:</strong> ' . (DEBUG_MODE ? 'Enabled' : 'Disabled') . '</p>';
        echo '<p><strong>Current URL:</strong> ' . $_SERVER['HTTP_HOST'] . '</p>';
        echo '</div>';
        
        // Test database connection
        try {
            require_once 'includes/DatabaseService.php';
            $db = DatabaseService::getInstance();
            $pdo = $db->getPDO();
            echo '<div class="info success">';
            echo '<h3>✅ Database Connection Test</h3>';
            echo '<p>Successfully connected to database!</p>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="info error">';
            echo '<h3>❌ Database Connection Failed</h3>';
            echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="info error">';
        echo '<h3>❌ Configuration Error</h3>';
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>
    
    <div class="info">
        <h3>📋 Environment Detection Logic</h3>
        <p>The system automatically detects:</p>
        <ul>
            <li><strong>Development:</strong> localhost, 127.0.0.1, *.local domains</li>
            <li><strong>Production:</strong> InfinityFree domains (.atspace.cc, .rf.gd, .byethost, etc.)</li>
        </ul>
    </div>
    
    <div class="info">
        <h3>🔧 Configuration Details</h3>
        <p><strong>Local Development:</strong></p>
        <ul>
            <li>Database: localhost/KartRiderAnalytics</li>
            <li>Debug Mode: Enabled</li>
            <li>Error Display: Enabled</li>
        </ul>
        
        <p><strong>Production (InfinityFree):</strong></p>
        <ul>
            <li>Database: sql106.infinityfree.com/if0_39323420_KartRiderAnalytics</li>
            <li>Debug Mode: Disabled</li>
            <li>Error Display: Disabled</li>
            <li>Security Headers: Enabled</li>
        </ul>
    </div>
    
    <p><a href="index.php">← Back to Main Application</a></p>
</body>
</html>
