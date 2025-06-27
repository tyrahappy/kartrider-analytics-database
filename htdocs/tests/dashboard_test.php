<?php
/**
 * Dashboard Test Page
 * Quick test for dashboard functionality
 */

// Enable error display
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .test { margin: 10px 0; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Dashboard Functionality Test</h1>
    
    <?php
    try {
        echo "<div class='test'>";
        echo "<h3>Testing Database Connection</h3>";
        
        require_once '../../config.php';
        require_once '../../includes/DatabaseService.php';
        
        $db = DatabaseService::getInstance();
        if ($db->isConnected()) {
            echo "<div class='success'>✅ Database connected</div>";
            
            // Test simple queries like the optimized dashboard
            $pdo = $db->getPDO();
            
            echo "<h3>Testing Simple Queries:</h3>";
            
            // Test player count
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Player");
            $stmt->execute();
            $playerCount = $stmt->fetch()['total'];
            echo "<div class='success'>✅ Player count: $playerCount</div>";
            
            // Test race count
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Race");
            $stmt->execute();
            $raceCount = $stmt->fetch()['total'];
            echo "<div class='success'>✅ Race count: $raceCount</div>";
            
            // Test participation count
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Participation");
            $stmt->execute();
            $participationCount = $stmt->fetch()['total'];
            echo "<div class='success'>✅ Participation count: $participationCount</div>";
            
            echo "<h3>Testing Dashboard Controller:</h3>";
            
            require_once 'controllers/DashboardController.php';
            
            // Test each module
            $modules = ['player_stats', 'session_analytics', 'achievements'];
            foreach ($modules as $module) {
                echo "<div style='margin: 10px 0;'>";
                echo "<strong>Testing module: $module</strong><br>";
                
                try {
                    $controller = new DashboardController();
                    $_GET['module'] = $module;
                    
                    // Use reflection to call loadDashboardData directly
                    $reflection = new ReflectionClass($controller);
                    $method = $reflection->getMethod('loadDashboardData');
                    $method->setAccessible(true);
                    $data = $method->invoke($controller);
                    
                    if (isset($data['error'])) {
                        echo "<div class='error'>❌ Module error: " . htmlspecialchars($data['error']) . "</div>";
                    } else {
                        echo "<div class='success'>✅ Module '$module' loaded successfully</div>";
                        if (is_array($data) && count($data) > 0) {
                            echo "<div class='success'>  📊 Data keys: " . implode(', ', array_keys($data)) . "</div>";
                        }
                    }
                } catch (Exception $e) {
                    echo "<div class='error'>❌ Module '$module' failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
                echo "</div>";
            }
            
            echo "<h3>Testing Full Dashboard Render:</h3>";
            
            try {
                $controller = new DashboardController();
                $_GET['module'] = 'player_stats';
                
                ob_start();
                $controller->run();
                $output = ob_get_clean();
                
                if (strpos($output, 'MAX_JOIN_SIZE') !== false) {
                    echo "<div class='error'>❌ Still getting MAX_JOIN_SIZE error</div>";
                } elseif (strpos($output, 'Fatal error') !== false) {
                    echo "<div class='error'>❌ Fatal error in dashboard render</div>";
                } else {
                    echo "<div class='success'>✅ Dashboard rendered successfully without errors</div>";
                    echo "<div class='success'>  📏 Output length: " . strlen($output) . " characters</div>";
                }
            } catch (Exception $e) {
                echo "<div class='error'>❌ Dashboard render failed: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            
        } else {
            echo "<div class='error'>❌ Database connection failed</div>";
        }
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<div class='error'>File: " . htmlspecialchars($e->getFile()) . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    }
    ?>
    
    <h2>Next Steps:</h2>
    <ul>
        <li><a href="dashboard.php">Test Full Dashboard</a></li>
        <li><a href="db_optimize.php?run=optimize">Run Database Optimization</a></li>
        <li><a href="index.php">Back to Main App</a></li>
    </ul>
    
    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa;">
        <strong>For InfinityFree deployment:</strong><br>
        1. Upload the optimized files<br>
        2. Run the database optimization script<br>
        3. Test dashboard functionality<br>
        4. Delete test files for security
    </div>
</body>
</html>
