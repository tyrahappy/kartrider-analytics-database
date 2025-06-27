<?php
// Simple error diagnostic - shows actual PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Error Diagnostic</h1>";
echo "<p>Testing configuration load...</p>";

try {
    echo "<p>✅ PHP is working</p>";
    
    // Test config
    echo "<p>Testing config.php...</p>";
    require_once '../../config.php';
    echo "<p>✅ Config loaded successfully</p>";
    
    // Test database service
    echo "<p>Testing DatabaseService...</p>";
    require_once '../../includes/DatabaseService.php';
    echo "<p>✅ DatabaseService loaded</p>";
    
    $db = DatabaseService::getInstance();
    if ($db->isConnected()) {
        echo "<p>✅ Database connected</p>";
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
    
    // Test asset helper
    echo "<p>Testing AssetHelper...</p>";
    try {
        require_once '../../includes/AssetHelper.php';
        echo "<p>✅ AssetHelper loaded</p>";
        
        if (function_exists('getBaseUrl')) {
            $baseUrl = getBaseUrl();
            echo "<p>Base URL: " . htmlspecialchars($baseUrl) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>⚠️ AssetHelper error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Test controller
    echo "<p>Testing TableViewerController...</p>";
    require_once '../../controllers/TableViewerController.php';
    echo "<p>✅ TableViewerController loaded</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<p><a href='index.php'>Try Main Page</a></p>";
?>
