<?php
// Quick test for the fixed dashboard components

// Mock environment
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['REQUEST_URI'] = '/test.php';
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

error_reporting(E_ERROR | E_PARSE);

function testDashboardComponent($module, $timeFilter = 'all') {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing: {$module} (filter: {$timeFilter})\n";
    echo str_repeat("=", 50) . "\n";
    
    $_GET = [
        'module' => $module,
        'time_filter' => $timeFilter,
        'player_type' => 'all'
    ];
    
    try {
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController();
        
        $reflectionClass = new ReflectionClass($controller);
        $method = $reflectionClass->getMethod('loadDashboardData');
        $method->setAccessible(true);
        
        $data = $method->invoke($controller);
        
        if ($module === 'player_stats') {
            echo "🏆 Win Rate Ranking:\n";
            if (isset($data['win_rate_ranking']) && !empty($data['win_rate_ranking'])) {
                echo "✅ Found " . count($data['win_rate_ranking']) . " entries\n";
                foreach ($data['win_rate_ranking'] as $i => $player) {
                    echo "  " . ($i+1) . ". " . $player['PlayerName'] . " (" . $player['PlayerType'] . ")\n";
                }
            } else {
                echo "❌ No win rate ranking data\n";
            }
        }
        
        if ($module === 'session_analytics') {
            echo "🏁 Track Difficulty Distribution:\n";
            if (isset($data['difficulty_distribution']) && !empty($data['difficulty_distribution'])) {
                echo "✅ Found " . count($data['difficulty_distribution']) . " difficulties\n";
                foreach ($data['difficulty_distribution'] as $diff) {
                    echo "  " . $diff['TrackDifficulty'] . ": " . $diff['RaceCount'] . " races (avg: " . $diff['AvgTime'] . "s)\n";
                }
            } else {
                echo "❌ No difficulty distribution data\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "🚀 Testing Dashboard Component Fixes\n";
echo "===================================\n";

// Test player stats
testDashboardComponent('player_stats', 'all');
testDashboardComponent('player_stats', '7days');

// Test session analytics
testDashboardComponent('session_analytics', 'all');
testDashboardComponent('session_analytics', '30days');

echo "\n🎯 Test completed!\n";
?>
