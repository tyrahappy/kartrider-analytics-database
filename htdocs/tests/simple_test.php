<!DOCTYPE html>
<html>
<head>
    <title>Simple Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .pass { color: green; }
        .fail { color: red; }
    </style>
</head>
<body>
    <h1>Simple Test Page</h1>
    
    <?php
    // Enable error display
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    echo "<p class='pass'>✅ PHP is working</p>";
    
    // Test basic file access
    if (file_exists('config.php')) {
        echo "<p class='pass'>✅ config.php exists</p>";
        try {
            require_once '../../config.php';
            echo "<p class='pass'>✅ config.php loaded</p>";
            echo "<p>Debug mode: " . (DEBUG_MODE ? 'ON' : 'OFF') . "</p>";
        } catch (Exception $e) {
            echo "<p class='fail'>❌ Config error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='fail'>❌ config.php missing</p>";
    }
    
    // Test CSS file
    if (file_exists('assets/style.css')) {
        echo "<p class='pass'>✅ CSS file exists</p>";
    } else {
        echo "<p class='fail'>❌ CSS file missing</p>";
    }
    
    // Test database
    if (file_exists('includes/DatabaseService.php')) {
        echo "<p class='pass'>✅ DatabaseService exists</p>";
        try {
            require_once '../../includes/DatabaseService.php';
            $db = DatabaseService::getInstance();
            if ($db->isConnected()) {
                echo "<p class='pass'>✅ Database connected</p>";
            } else {
                echo "<p class='fail'>❌ Database not connected</p>";
            }
        } catch (Exception $e) {
            echo "<p class='fail'>❌ Database error: " . $e->getMessage() . "</p>";
        }
    }
    ?>
    
    <h2>Navigation</h2>
    <p>
        <a href="debug.php">Debug Page</a> |
        <a href="local_test.php">Local Test</a> |
        <a href="index.php">Try Main App</a>
    </p>
    
    <h2>Manual Tests</h2>
    <p>Test these URLs manually:</p>
    <ul>
        <li><a href="assets/style.css" target="_blank">CSS File</a></li>
        <li><a href="assets/tabs.js" target="_blank">JS File</a></li>
    </ul>
</body>
</html>
