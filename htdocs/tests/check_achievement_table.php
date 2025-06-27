<?php
require_once 'config_environment.php';

echo "=== 检查PlayerAchievement表结构 ===\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 检查表结构
    $stmt = $pdo->query("DESCRIBE PlayerAchievement");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "PlayerAchievement表字段:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']}) - {$column['Null']} - {$column['Key']}\n";
    }
    
    // 检查是否有数据
    $count = $pdo->query("SELECT COUNT(*) FROM PlayerAchievement")->fetchColumn();
    echo "\n当前记录数: $count\n";
    
    if ($count > 0) {
        echo "\n样本数据:\n";
        $sample = $pdo->query("SELECT * FROM PlayerAchievement LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sample as $row) {
            print_r($row);
        }
    }
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
?> 