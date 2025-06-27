<?php
require_once 'config_environment.php';

echo "=== 生成最近一个月内成就获得数据 ===\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 获取所有玩家和成就
    $players = $pdo->query("SELECT PlayerID FROM Player")->fetchAll(PDO::FETCH_COLUMN);
    $achievements = $pdo->query("SELECT AchievementID FROM Achievement")->fetchAll(PDO::FETCH_COLUMN);

    if (empty($players) || empty($achievements)) {
        die("玩家或成就数据不足，无法生成测试数据。\n");
    }

    echo "找到 " . count($players) . " 个玩家\n";
    echo "找到 " . count($achievements) . " 个成就\n";

    $now = time();
    $days = 30; // 最近30天
    $achievementsPerDay = rand(3, 8); // 每天随机生成3-8个成就获得记录

    $totalGenerated = 0;

    for ($d = 0; $d < $days; $d++) {
        $achievementDate = date('Y-m-d H:i:s', strtotime("-$d day", $now));
        $dailyCount = rand(3, 8); // 每天随机3-8个成就
        
        for ($a = 0; $a < $dailyCount; $a++) {
            // 随机玩家
            $playerId = $players[array_rand($players)];
            // 随机成就
            $achievementId = $achievements[array_rand($achievements)];
            
            // 随机时间（当天内的随机时间）
            $randomHour = rand(0, 23);
            $randomMinute = rand(0, 59);
            $randomSecond = rand(0, 59);
            $achievementTime = date('Y-m-d H:i:s', strtotime("-$d day $randomHour:$randomMinute:$randomSecond", $now));
            
            // 检查是否已经存在相同的玩家-成就组合（避免重复）
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM PlayerAchievement WHERE PlayerID = ? AND AchievementID = ?");
            $checkStmt->execute([$playerId, $achievementId]);
            $exists = $checkStmt->fetchColumn() > 0;
            
            if (!$exists) {
                // 插入成就获得记录
                $stmt = $pdo->prepare("INSERT INTO PlayerAchievement (PlayerID, AchievementID, DateEarned) VALUES (?, ?, ?)");
                $stmt->execute([$playerId, $achievementId, $achievementTime]);
                $totalGenerated++;
            }
        }
    }
    
    echo "生成完成！共生成 $totalGenerated 个成就获得记录\n";
    
    // 显示一些统计信息
    $totalAchievements = $pdo->query("SELECT COUNT(*) FROM PlayerAchievement")->fetchColumn();
    echo "数据库中总成就获得记录数: $totalAchievements\n";
    
    // 显示最近7天的成就获得趋势
    echo "\n=== 最近7天成就获得趋势 ===\n";
    $trendStmt = $pdo->query("
        SELECT 
            DATE(DateEarned) as EarnedDay,
            COUNT(*) as DailyAchievements
        FROM PlayerAchievement 
        WHERE DateEarned >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(DateEarned)
        ORDER BY EarnedDay DESC
    ");
    
    $trends = $trendStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($trends as $trend) {
        echo "日期: {$trend['EarnedDay']}, 获得成就数: {$trend['DailyAchievements']}\n";
    }
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
?> 