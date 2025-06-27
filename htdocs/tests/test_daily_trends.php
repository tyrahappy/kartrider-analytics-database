<?php
require_once 'config_environment.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== 数据库连接测试 ===\n";
    echo "环境: " . ENVIRONMENT . "\n";
    echo "数据库: " . DB_HOST . "/" . DB_NAME . "\n";
    
    // 检查总比赛数
    $stmt = $pdo->query("SELECT COUNT(*) as total_races FROM Race");
    $totalRaces = $stmt->fetch()['total_races'];
    echo "总比赛数: $totalRaces\n";
    
    // 检查最近7天的比赛数
    $stmt = $pdo->query("SELECT COUNT(*) as recent_races FROM Race WHERE RaceDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $recentRaces = $stmt->fetch()['recent_races'];
    echo "最近7天比赛数: $recentRaces\n";
    
    // 检查最近7天的每日趋势
    $query = "
        SELECT 
            DATE(r.RaceDate) as RaceDay,
            COUNT(*) as DailyRaces,
            COUNT(pt.ParticipationID) as TotalParticipations
        FROM Race r
        LEFT JOIN Participation pt ON r.RaceID = pt.RaceID
        WHERE r.RaceDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(r.RaceDate)
        ORDER BY RaceDay DESC
        LIMIT 7
    ";
    
    $stmt = $pdo->query($query);
    $dailyTrends = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n=== 最近7天每日趋势 ===\n";
    if (empty($dailyTrends)) {
        echo "没有找到最近7天的数据\n";
    } else {
        foreach ($dailyTrends as $trend) {
            echo "日期: {$trend['RaceDay']}, 比赛数: {$trend['DailyRaces']}, 参与次数: {$trend['TotalParticipations']}\n";
        }
    }
    
    // 检查一些样本比赛日期
    echo "\n=== 样本比赛日期 ===\n";
    $stmt = $pdo->query("SELECT RaceDate FROM Race ORDER BY RaceDate DESC LIMIT 5");
    $sampleDates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($sampleDates as $date) {
        echo "比赛日期: $date\n";
    }
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
?> 