<?php
require_once 'config_environment.php';

echo "=== 生成前两周（15~21天前）比赛和参与数据 ===\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 获取所有玩家、赛车、赛道
    $players = $pdo->query("SELECT PlayerID FROM Player")->fetchAll(PDO::FETCH_COLUMN);
    $karts = $pdo->query("SELECT KartID FROM Kart")->fetchAll(PDO::FETCH_COLUMN);
    $tracks = $pdo->query("SELECT TrackName FROM Track")->fetchAll(PDO::FETCH_COLUMN);

    if (empty($players) || empty($karts) || empty($tracks)) {
        die("玩家、赛车或赛道数据不足，无法生成测试数据。\n");
    }

    $now = time();
    $racesPerDay = 3; // 每天生成3场比赛
    $minParticipants = 2;
    $maxParticipants = 6;

    for ($d = 15; $d <= 21; $d++) { // 15~21天前
        $raceDate = date('Y-m-d H:i:s', strtotime("-$d day", $now));
        for ($r = 0; $r < $racesPerDay; $r++) {
            // 随机赛道
            $track = $tracks[array_rand($tracks)];
            // 自动生成RaceName
            $raceName = "AutoRace_" . date('Ymd', strtotime("-$d day", $now)) . "_" . ($r+1);
            // 插入比赛
            $stmt = $pdo->prepare("INSERT INTO Race (RaceName, TrackName, RaceDate) VALUES (?, ?, ?)");
            $stmt->execute([$raceName, $track, $raceDate]);
            $raceId = $pdo->lastInsertId();

            // 随机参与人数
            $numParticipants = rand($minParticipants, $maxParticipants);
            $usedPlayers = [];
            for ($p = 1; $p <= $numParticipants; $p++) {
                // 随机玩家且不重复
                do {
                    $playerId = $players[array_rand($players)];
                } while (in_array($playerId, $usedPlayers));
                $usedPlayers[] = $playerId;
                // 随机赛车
                $kartId = $karts[array_rand($karts)];
                // 完成时间（秒）
                $totalTime = rand(120, 300) + rand(0, 99)/100;
                // 排名
                $finishingRank = $p;
                // 插入Participation
                $stmt2 = $pdo->prepare("INSERT INTO Participation (RaceID, PlayerID, KartID, TotalTime, FinishingRank) VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([$raceId, $playerId, $kartId, $totalTime, $finishingRank]);
            }
        }
    }
    echo "生成完成！\n";
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
} 