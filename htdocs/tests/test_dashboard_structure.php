<?php
/**
 * Dashboard结构测试文件
 * 验证新的模块化结构是否正常工作
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Dashboard 结构测试</h1>";
echo "<p>测试时间: " . date('Y-m-d H:i:s') . "</p>";

// 测试1: 检查文件是否存在
echo "<h2>1. 文件结构检查</h2>";
$files_to_check = [
    'controllers/DashboardController.php',
    'controllers/dashboard/PlayerStatsDashboardController.php',
    'controllers/dashboard/SessionAnalyticsController.php',
    'controllers/dashboard/AchievementDashboardController.php',
    'controllers/dashboard/README.md'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ {$file} - 存在<br>";
    } else {
        echo "❌ {$file} - 不存在<br>";
    }
}

// 测试2: 检查类是否可以加载
echo "<h2>2. 类加载测试</h2>";

try {
    // 加载基础类
    require_once 'includes/BaseController.php';
    echo "✅ BaseController 加载成功<br>";
    
    // 加载主控制器
    require_once 'controllers/DashboardController.php';
    echo "✅ DashboardController 加载成功<br>";
    
    // 加载子模块
    require_once 'controllers/dashboard/PlayerStatsDashboardController.php';
    echo "✅ PlayerStatsDashboardController 加载成功<br>";
    
    require_once 'controllers/dashboard/SessionAnalyticsController.php';
    echo "✅ SessionAnalyticsController 加载成功<br>";
    
    require_once 'controllers/dashboard/AchievementDashboardController.php';
    echo "✅ AchievementDashboardController 加载成功<br>";
    
} catch (Exception $e) {
    echo "❌ 类加载失败: " . $e->getMessage() . "<br>";
}

// 测试3: 检查类是否可以实例化
echo "<h2>3. 类实例化测试</h2>";

try {
    // 测试主控制器
    $dashboard = new DashboardController();
    echo "✅ DashboardController 实例化成功<br>";
    
    // 测试子模块
    $playerStats = new PlayerStatsDashboardController('all', 'all');
    echo "✅ PlayerStatsDashboardController 实例化成功<br>";
    
    $sessionAnalytics = new SessionAnalyticsController('all', 'all');
    echo "✅ SessionAnalyticsController 实例化成功<br>";
    
    $achievements = new AchievementDashboardController('all', 'all');
    echo "✅ AchievementDashboardController 实例化成功<br>";
    
} catch (Exception $e) {
    echo "❌ 类实例化失败: " . $e->getMessage() . "<br>";
}

// 测试4: 检查方法是否存在
echo "<h2>4. 方法存在性检查</h2>";

$methods_to_check = [
    'PlayerStatsDashboardController' => ['getPlayerStatistics', 'getFallbackData'],
    'SessionAnalyticsController' => ['getSessionAnalytics', 'getFallbackData'],
    'AchievementDashboardController' => ['getAchievementData', 'getFallbackData']
];

foreach ($methods_to_check as $class => $methods) {
    $reflection = new ReflectionClass($class);
    foreach ($methods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "✅ {$class}::{$method}() - 存在<br>";
        } else {
            echo "❌ {$class}::{$method}() - 不存在<br>";
        }
    }
}

// 测试5: 检查目录结构
echo "<h2>5. 目录结构检查</h2>";

$directories_to_check = [
    'controllers',
    'controllers/dashboard',
    'models',
    'views',
    'includes',
    'assets',
    'assets/tests',
    'assets/legacy'
];

foreach ($directories_to_check as $dir) {
    if (is_dir($dir)) {
        $file_count = count(scandir($dir)) - 2; // 减去 . 和 ..
        echo "✅ {$dir} - 存在 ({$file_count} 个文件)<br>";
    } else {
        echo "❌ {$dir} - 不存在<br>";
    }
}

// 测试6: 检查README文件
echo "<h2>6. 文档完整性检查</h2>";

$readme_files = [
    'README.md',
    'controllers/README.md',
    'controllers/dashboard/README.md',
    'models/README.md',
    'views/README.md',
    'includes/README.md',
    'assets/README.md',
    'assets/tests/README.md',
    'assets/legacy/README.md'
];

foreach ($readme_files as $readme) {
    if (file_exists($readme)) {
        $size = filesize($readme);
        echo "✅ {$readme} - 存在 ({$size} 字节)<br>";
    } else {
        echo "❌ {$readme} - 不存在<br>";
    }
}

echo "<h2>测试完成</h2>";
echo "<p>如果所有项目都显示 ✅，说明新的Dashboard结构整理成功！</p>";

// 显示当前目录结构
echo "<h2>当前目录结构</h2>";
echo "<pre>";
function listDirectory($dir, $indent = '') {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                echo $indent . "📁 " . $file . "\n";
                listDirectory($path, $indent . '  ');
            } else {
                echo $indent . "📄 " . $file . "\n";
            }
        }
    }
}

listDirectory('.');
echo "</pre>";
?> 