<?php
/**
 * InfinityFree Production Readiness Test
 * Tests all dashboard modules for InfinityFree hosting compatibility
 */

// Mock web environment for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost'; // Use localhost for database testing
    $_SERVER['REQUEST_URI'] = '/dashboard.php';
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

// Suppress warnings that might occur on InfinityFree
error_reporting(E_ERROR | E_PARSE);

function testModule($moduleName, $timeFilter = 'all', $playerType = 'all') {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing Module: {$moduleName}\n";
    echo "Time Filter: {$timeFilter} | Player Type: {$playerType}\n";
    echo str_repeat("=", 50) . "\n";
    
    try {
        // Set parameters
        $_GET = [
            'module' => $moduleName,
            'time_filter' => $timeFilter,
            'player_type' => $playerType
        ];
        
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController();
        
        // Use reflection to test data loading
        $reflectionClass = new ReflectionClass($controller);
        $method = $reflectionClass->getMethod('loadDashboardData');
        $method->setAccessible(true);
        
        $startTime = microtime(true);
        $data = $method->invoke($controller);
        $executionTime = microtime(true) - $startTime;
        
        echo "⏱️  Execution time: " . round($executionTime * 1000, 2) . "ms\n";
        
        if (isset($data['error'])) {
            echo "⚠️  Database Error: " . $data['error'] . "\n";
            return false;
        }
        
        // Test specific module requirements
        switch ($moduleName) {
            case 'player_stats':
                return testPlayerStats($data);
            case 'session_analytics':
                return testSessionAnalytics($data);
            case 'achievements':
                return testAchievements($data);
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        
        // Check for InfinityFree specific errors
        if (strpos($e->getMessage(), 'MAX_JOIN_SIZE') !== false) {
            echo "🔍 MAX_JOIN_SIZE error detected - fallback should handle this\n";
        }
        return false;
    }
}

function testPlayerStats($data) {
    $requiredKeys = ['total_players', 'active_players', 'avg_races_per_player', 'active_rate_recent_week'];
    $passed = true;
    
    foreach ($requiredKeys as $key) {
        if (isset($data[$key])) {
            echo "✅ {$key}: " . $data[$key] . "\n";
        } else {
            echo "❌ Missing: {$key}\n";
            $passed = false;
        }
    }
    
    // Test array structures
    if (isset($data['player_distribution']) && is_array($data['player_distribution'])) {
        echo "✅ player_distribution: " . count($data['player_distribution']) . " entries\n";
    } else {
        echo "❌ player_distribution missing or invalid\n";
        $passed = false;
    }
    
    return $passed;
}

function testSessionAnalytics($data) {
    $requiredKeys = ['total_races', 'avg_race_time', 'popular_track', 'popular_kart'];
    $passed = true;
    
    foreach ($requiredKeys as $key) {
        if (isset($data[$key])) {
            echo "✅ {$key}: " . $data[$key] . "\n";
        } else {
            echo "❌ Missing: {$key}\n";
            $passed = false;
        }
    }
    
    // Test kart usage with AvgTime and AvgRank
    if (isset($data['kart_usage']) && is_array($data['kart_usage'])) {
        echo "✅ kart_usage: " . count($data['kart_usage']) . " entries\n";
        if (!empty($data['kart_usage'])) {
            $firstKart = $data['kart_usage'][0];
            if (isset($firstKart['AvgTime']) && isset($firstKart['AvgRank'])) {
                echo "✅ Kart data includes AvgTime and AvgRank\n";
            } else {
                echo "❌ Kart data missing AvgTime or AvgRank\n";
                $passed = false;
            }
        }
    }
    
    return $passed;
}

function testAchievements($data) {
    $requiredKeys = ['total_achievements', 'earned_achievements', 'completion_rate'];
    $passed = true;
    
    foreach ($requiredKeys as $key) {
        if (isset($data[$key])) {
            echo "✅ {$key}: " . $data[$key] . "\n";
        } else {
            echo "❌ Missing: {$key}\n";
            $passed = false;
        }
    }
    
    // Critical test: CompletionRate in achievement_popularity
    if (isset($data['achievement_popularity']) && is_array($data['achievement_popularity'])) {
        $count = count($data['achievement_popularity']);
        echo "✅ achievement_popularity: {$count} entries\n";
        
        if ($count > 0) {
            $hasCompletionRate = true;
            foreach ($data['achievement_popularity'] as $achievement) {
                if (!isset($achievement['CompletionRate'])) {
                    $hasCompletionRate = false;
                    break;
                }
            }
            
            if ($hasCompletionRate) {
                echo "🎉 ALL achievements have CompletionRate!\n";
            } else {
                echo "❌ Some achievements missing CompletionRate\n";
                $passed = false;
            }
        }
    } else {
        echo "❌ achievement_popularity missing or invalid\n";
        $passed = false;
    }
    
    return $passed;
}

// Main test execution
echo "🚀 InfinityFree Production Readiness Test\n";
echo "==========================================\n";

$modules = ['player_stats', 'session_analytics', 'achievements'];
$timeFilters = ['all', '7days', '30days'];
$results = [];

foreach ($modules as $module) {
    foreach ($timeFilters as $timeFilter) {
        $testKey = "{$module}_{$timeFilter}";
        $results[$testKey] = testModule($module, $timeFilter);
    }
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 TEST SUMMARY\n";
echo str_repeat("=", 60) . "\n";

$passed = 0;
$total = count($results);

foreach ($results as $test => $result) {
    $status = $result ? "✅ PASS" : "❌ FAIL";
    echo "{$status} {$test}\n";
    if ($result) $passed++;
}

$percentage = round(($passed / $total) * 100, 1);
echo "\n🎯 Results: {$passed}/{$total} tests passed ({$percentage}%)\n";

if ($percentage >= 90) {
    echo "🎉 EXCELLENT! Ready for InfinityFree deployment!\n";
} elseif ($percentage >= 75) {
    echo "👍 GOOD! Minor issues may exist but mostly ready.\n";
} else {
    echo "⚠️  ATTENTION NEEDED! Significant issues detected.\n";
}

echo "\n💡 InfinityFree Deployment Tips:\n";
echo "- Upload all files including fallback mechanisms\n";
echo "- Test on InfinityFree subdomain first\n";
echo "- Monitor for MAX_JOIN_SIZE errors\n";
echo "- Enable caching if performance issues occur\n";
?>
