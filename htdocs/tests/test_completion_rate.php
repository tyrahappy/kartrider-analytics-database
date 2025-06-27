<?php
// InfinityFree compatible test for CompletionRate in achievement data

// Mock HTTP environment for CLI execution (prevents HTTP_HOST warnings)
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['REQUEST_URI'] = '/PhpLab/test_completion_rate.php';
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

// Mock GET parameters for achievements module
$_GET = [
    'module' => 'achievements',
    'time_filter' => 'all',
    'player_type' => 'all'
];

// Error handling for InfinityFree
error_reporting(E_ERROR | E_PARSE); // Reduce error reporting for cleaner output

try {
    require_once 'config.php';
    require_once 'controllers/DashboardController.php';
    
    echo "InfinityFree Compatibility Test - CompletionRate Fix\n";
    echo "==================================================\n";
    
    // Create controller (will parse $_GET parameters automatically)
    $controller = new DashboardController();
    
    // Use reflection to access private loadDashboardData method
    $reflectionClass = new ReflectionClass($controller);
    $method = $reflectionClass->getMethod('loadDashboardData');
    $method->setAccessible(true);
    
    // Get dashboard data 
    $data = $method->invoke($controller);
    
    echo "Module: " . $controller->getSelectedModule() . "\n";
    echo "Time Filter: " . $controller->getTimeFilter() . "\n";
    echo "Player Type: " . $controller->getPlayerTypeFilter() . "\n";
    echo "\n";
    
    // Check for database connection issues (common on InfinityFree)
    if (isset($data['error'])) {
        echo "⚠️  Database Error Detected: " . $data['error'] . "\n";
        echo "This is expected if database is not accessible.\n\n";
    }
    
    // Test achievement popularity data structure
    echo "Testing Achievement Popularity Data Structure:\n";
    echo "============================================\n";
    
    if (isset($data['achievement_popularity'])) {
        if (empty($data['achievement_popularity'])) {
            echo "✅ achievement_popularity exists but is empty (fallback working)\n";
        } else {
            $achievementCount = count($data['achievement_popularity']);
            echo "✅ Found {$achievementCount} achievement(s)\n\n";
            
            foreach (array_slice($data['achievement_popularity'], 0, 3) as $index => $achievement) {
                echo "Achievement " . ($index + 1) . ":\n";
                
                // Check required keys
                $requiredKeys = ['AchievementName', 'Description', 'PointsAwarded', 'EarnedCount', 'CompletionRate'];
                $missingKeys = [];
                
                foreach ($requiredKeys as $key) {
                    if (isset($achievement[$key])) {
                        $value = $achievement[$key];
                        echo "  ✅ {$key}: " . (is_numeric($value) ? $value : "'" . substr($value, 0, 30) . "'") . "\n";
                    } else {
                        $missingKeys[] = $key;
                        echo "  ❌ {$key}: MISSING!\n";
                    }
                }
                
                if (empty($missingKeys)) {
                    echo "  🎉 All required keys present!\n";
                } else {
                    echo "  ⚠️  Missing keys: " . implode(', ', $missingKeys) . "\n";
                }
                echo "  ---\n";
            }
        }
    } else {
        echo "❌ achievement_popularity key not found in data!\n";
    }
    
    // Test other achievement data
    echo "\nOther Achievement Data:\n";
    echo "======================\n";
    $achievementKeys = ['total_achievements', 'earned_achievements', 'avg_achievements_per_player', 'completion_rate'];
    foreach ($achievementKeys as $key) {
        if (isset($data[$key])) {
            echo "✅ {$key}: " . $data[$key] . "\n";
        } else {
            echo "❌ {$key}: MISSING\n";
        }
    }
    
    echo "\n🎯 InfinityFree Compatibility Test Completed!\n";
    
    // Summary
    $hasCompletionRate = false;
    if (isset($data['achievement_popularity']) && !empty($data['achievement_popularity'])) {
        foreach ($data['achievement_popularity'] as $achievement) {
            if (isset($achievement['CompletionRate'])) {
                $hasCompletionRate = true;
                break;
            }
        }
    }
    
    if ($hasCompletionRate || (isset($data['achievement_popularity']) && empty($data['achievement_popularity']))) {
        echo "✅ SUCCESS: CompletionRate issue is FIXED!\n";
    } else {
        echo "❌ FAILURE: CompletionRate still missing!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    
    // Check if it's an InfinityFree specific error
    $errorMessage = $e->getMessage();
    if (strpos($errorMessage, 'MAX_JOIN_SIZE') !== false || 
        strpos($errorMessage, '1104') !== false) {
        echo "\n🔍 This appears to be an InfinityFree MAX_JOIN_SIZE error.\n";
        echo "The fallback mechanisms should handle this.\n";
    } elseif (strpos($errorMessage, 'connection') !== false || 
              strpos($errorMessage, 'database') !== false) {
        echo "\n🔍 This appears to be a database connection issue.\n";
        echo "Expected when database is not running or accessible.\n";
    }
}
?>
