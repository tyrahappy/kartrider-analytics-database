<?php
/**
 * KartRider Analytics Dashboard - Data Service Layer
 * 
 * This file contains all data retrieval and processing logic for the dashboard.
 * It provides a clean interface for the controller to access dashboard data
 * without mixing business logic with presentation logic.
 */

// Include configuration file
require_once 'config.php';

/**
 * Dashboard Data Service Class
 * 
 * Handles all database operations and data processing for the dashboard
 */
class DashboardDataService {
    private $pdo;
    
    public function __construct() {
        $this->initializeDatabase();
    }
    
    /**
     * Initialize database connection
     */
    private function initializeDatabase() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            $this->pdo->query("SELECT 1");
            
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    /**
     * Check if database connection is established
     */
    public function isConnected() {
        return $this->pdo !== null;
    }
    
    // ================================
    // UTILITY METHODS
    // ================================
    
    /**
     * Generate time filter SQL condition
     */
    private function getTimeCondition($timeFilter, $dateColumn = 'r.RaceDate') {
        switch ($timeFilter) {
            case '7days':
                return " AND $dateColumn >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case '30days':
                return " AND $dateColumn >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case '3months':
                return " AND $dateColumn >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
            default:
                return '';
        }
    }
    
    /**
     * Generate player type filter SQL condition
     */
    private function getPlayerTypeCondition($playerTypeFilter) {
        switch ($playerTypeFilter) {
            case 'registered':
                return " AND EXISTS (SELECT 1 FROM RegisteredPlayer rp WHERE rp.PlayerID = p.PlayerID)";
            case 'guest':
                return " AND NOT EXISTS (SELECT 1 FROM RegisteredPlayer rp WHERE rp.PlayerID = p.PlayerID)";
            default:
                return '';
        }
    }
    
    // ================================
    // DATA RETRIEVAL METHODS
    // ================================
    
    /**
     * Get player statistics data
     */
    public function getPlayerStatistics($timeFilter = 'all', $playerTypeFilter = 'all') {
        if (!$this->isConnected()) return null;
        
        try {
            $data = [];
            $timeCondition = $this->getTimeCondition($timeFilter);
            $playerTypeCondition = $this->getPlayerTypeCondition($playerTypeFilter);
            
            // 1. Total players
            $query = "SELECT COUNT(*) as total_players FROM Player p WHERE 1=1" . $playerTypeCondition;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['total_players'] = $stmt->fetch()['total_players'];
            
            // 2. Player type distribution
            $query = "
                SELECT 
                    CASE 
                        WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                        ELSE 'Guest'
                    END as PlayerType,
                    COUNT(*) as PlayerCount
                FROM Player p
                LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                WHERE 1=1" . $playerTypeCondition . "
                GROUP BY PlayerType
                ORDER BY PlayerCount DESC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['player_distribution'] = $stmt->fetchAll();
            
            // 3. Active players (players who participated in races)
            $query = "
                SELECT COUNT(DISTINCT p.PlayerID) as active_players
                FROM Player p
                JOIN Participation pt ON p.PlayerID = pt.PlayerID
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition" . $playerTypeCondition;
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['active_players'] = $stmt->fetch()['active_players'];
            
            // 4. Average races per player
            $query = "
                SELECT AVG(race_count) as avg_races
                FROM (
                    SELECT p.PlayerID, COUNT(pt.ParticipationID) as race_count
                    FROM Player p
                    LEFT JOIN Participation pt ON p.PlayerID = pt.PlayerID
                    LEFT JOIN Race r ON pt.RaceID = r.RaceID
                    WHERE 1=1 $timeCondition" . $playerTypeCondition . "
                    GROUP BY p.PlayerID
                ) as player_races
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['avg_races_per_player'] = $result ? round($result['avg_races'], 2) : 0;
            
            // 5. Player win rate ranking (top 5)
            $query = "
                SELECT 
                    COALESCE(pc.UserName, CONCAT('Guest_', gp.SessionID)) as PlayerName,
                    COUNT(pt.ParticipationID) as TotalRaces,
                    SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) as Wins,
                    ROUND(SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(pt.ParticipationID), 2) as WinRate,
                    CASE 
                        WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                        ELSE 'Guest'
                    END as PlayerType
                FROM Player p
                LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
                LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                LEFT JOIN GuestPlayer gp ON p.PlayerID = gp.PlayerID
                JOIN Participation pt ON p.PlayerID = pt.PlayerID
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
            ";
            
            if ($playerTypeFilter === 'registered') {
                $query .= " AND rp.PlayerID IS NOT NULL";
            } elseif ($playerTypeFilter === 'guest') {
                $query .= " AND rp.PlayerID IS NULL";
            }
            
            $query .= "
                GROUP BY p.PlayerID, pc.UserName, gp.SessionID, PlayerType
                HAVING COUNT(pt.ParticipationID) >= 3
                ORDER BY WinRate DESC, TotalRaces DESC
                LIMIT 5
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['win_rate_ranking'] = $stmt->fetchAll();
            
            // 6. Race participation distribution
            $query = "
                SELECT 
                    CASE 
                        WHEN race_count = 0 THEN '0 races'
                        WHEN race_count BETWEEN 1 AND 5 THEN '1-5 races'
                        WHEN race_count BETWEEN 6 AND 10 THEN '6-10 races'
                        WHEN race_count BETWEEN 11 AND 20 THEN '11-20 races'
                        ELSE '20+ races'
                    END as RaceRange,
                    COUNT(*) as PlayerCount
                FROM (
                    SELECT p.PlayerID, COUNT(pt.ParticipationID) as race_count
                    FROM Player p
                    LEFT JOIN Participation pt ON p.PlayerID = pt.PlayerID
                    LEFT JOIN Race r ON pt.RaceID = r.RaceID
                    WHERE 1=1 $timeCondition" . $playerTypeCondition . "
                    GROUP BY p.PlayerID
                ) as player_races
                GROUP BY RaceRange
                ORDER BY MIN(race_count)
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['race_participation_distribution'] = $stmt->fetchAll();
            
            return $data;
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get session analytics data
     */
    public function getSessionAnalytics($timeFilter = 'all', $playerTypeFilter = 'all') {
        if (!$this->isConnected()) return null;
        
        try {
            $data = [];
            $timeCondition = $this->getTimeCondition($timeFilter);
            
            // 1. Total race count
            $query = "SELECT COUNT(*) as total_races FROM Race r WHERE 1=1 $timeCondition";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['total_races'] = $stmt->fetch()['total_races'];
            
            // 2. Average race time
            $query = "
                SELECT AVG(pt.TotalTime) as avg_race_time
                FROM Participation pt
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['avg_race_time'] = $result ? round($result['avg_race_time'], 2) : 0;
            
            // 3. Most popular track
            $query = "
                SELECT t.TrackName, COUNT(r.RaceID) as race_count
                FROM Track t
                JOIN Race r ON t.TrackName = r.TrackName
                WHERE 1=1 $timeCondition
                GROUP BY t.TrackName
                ORDER BY race_count DESC
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['popular_track'] = $result ? $result['TrackName'] : 'N/A';
            
            // 4. Most popular kart
            $query = "
                SELECT k.KartName, COUNT(pt.ParticipationID) as usage_count
                FROM Kart k
                JOIN Participation pt ON k.KartID = pt.KartID
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
                GROUP BY k.KartID, k.KartName
                ORDER BY usage_count DESC
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['popular_kart'] = $result ? $result['KartName'] : 'N/A';
            
            // 5. Track usage statistics
            $query = "
                SELECT 
                    t.TrackName,
                    t.TrackDifficulty,
                    COUNT(r.RaceID) as RaceCount,
                    AVG(pt.TotalTime) as AvgTime,
                    MIN(pt.TotalTime) as BestTime
                FROM Track t
                LEFT JOIN Race r ON t.TrackName = r.TrackName
                LEFT JOIN Participation pt ON r.RaceID = pt.RaceID
                WHERE 1=1 $timeCondition
                GROUP BY t.TrackName, t.TrackDifficulty
                ORDER BY RaceCount DESC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['track_statistics'] = $stmt->fetchAll();
            
            // 6. Track difficulty distribution
            $query = "
                SELECT 
                    t.TrackDifficulty,
                    COUNT(r.RaceID) as RaceCount
                FROM Track t
                LEFT JOIN Race r ON t.TrackName = r.TrackName
                WHERE 1=1 $timeCondition
                GROUP BY t.TrackDifficulty
                ORDER BY RaceCount DESC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['difficulty_distribution'] = $stmt->fetchAll();
            
            // 7. Kart usage statistics
            $query = "
                SELECT 
                    k.KartName,
                    CASE 
                        WHEN sk.KartID IS NOT NULL THEN 'Speed'
                        WHEN ik.KartID IS NOT NULL THEN 'Item'
                        ELSE 'Standard'
                    END as KartType,
                    COUNT(pt.ParticipationID) as UsageCount,
                    AVG(pt.TotalTime) as AvgTime
                FROM Kart k
                LEFT JOIN SpeedKart sk ON k.KartID = sk.KartID
                LEFT JOIN ItemKart ik ON k.KartID = ik.KartID
                LEFT JOIN Participation pt ON k.KartID = pt.KartID
                LEFT JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
                GROUP BY k.KartID, k.KartName, KartType
                ORDER BY UsageCount DESC
                LIMIT 10
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['kart_usage'] = $stmt->fetchAll();
            
            // 8. Daily race trends (last 30 days)
            $query = "
                SELECT 
                    DATE(r.RaceDate) as RaceDay,
                    COUNT(*) as DailyRaces
                FROM Race r
                WHERE r.RaceDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(r.RaceDate)
                ORDER BY RaceDay
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['daily_trends'] = $stmt->fetchAll();
            
            return $data;
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get achievement data
     */
    public function getAchievementData($timeFilter = 'all', $playerTypeFilter = 'all') {
        if (!$this->isConnected()) return null;
        
        try {
            $data = [];
            $timeCondition = $this->getTimeCondition($timeFilter, 'pa.DateEarned');
            $playerTypeCondition = $this->getPlayerTypeCondition($playerTypeFilter);
            
            // 1. Total achievements
            $query = "SELECT COUNT(*) as total FROM Achievement";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['total_achievements'] = $stmt->fetch()['total'];
            
            // 2. Total earned achievements
            $query = "SELECT COUNT(*) as earned FROM PlayerAchievement pa WHERE 1=1 $timeCondition";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['earned_achievements'] = $stmt->fetch()['earned'];
            
            // 3. Average achievements per player
            $query = "
                SELECT AVG(achievement_count) as avg_achievements
                FROM (
                    SELECT p.PlayerID, COUNT(pa.AchievementID) as achievement_count
                    FROM Player p
                    LEFT JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID
                    WHERE 1=1 $timeCondition" . $playerTypeCondition . "
                    GROUP BY p.PlayerID
                ) as player_achievements
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['avg_achievements_per_player'] = $result ? round($result['avg_achievements'], 2) : 0;
            
            // 4. Rarest achievement (least earned)
            $query = "
                SELECT a.AchievementName, COUNT(pa.PlayerID) as earned_count
                FROM Achievement a
                LEFT JOIN PlayerAchievement pa ON a.AchievementID = pa.AchievementID
                WHERE 1=1 $timeCondition
                GROUP BY a.AchievementID, a.AchievementName
                ORDER BY earned_count ASC, a.AchievementName
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['rarest_achievement'] = $result ? $result['AchievementName'] : 'N/A';
            
            // 5. Achievement popularity ranking
            $query = "
                SELECT 
                    a.AchievementName,
                    a.Description,
                    a.PointsAwarded,
                    COUNT(pa.PlayerID) as EarnedCount,
                    ROUND(COUNT(pa.PlayerID) * 100.0 / (SELECT COUNT(*) FROM Player), 2) as CompletionRate
                FROM Achievement a
                LEFT JOIN PlayerAchievement pa ON a.AchievementID = pa.AchievementID
                WHERE 1=1 $timeCondition
                GROUP BY a.AchievementID, a.AchievementName, a.Description, a.PointsAwarded
                ORDER BY EarnedCount DESC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['achievement_popularity'] = $stmt->fetchAll();
            
            // 6. Top players by achievement count
            $query = "
                SELECT 
                    COALESCE(pc.UserName, CONCAT('Guest_', gp.SessionID)) as PlayerName,
                    COUNT(pa.AchievementID) as AchievementCount,
                    SUM(a.PointsAwarded) as TotalPoints,
                    CASE 
                        WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                        ELSE 'Guest'
                    END as PlayerType
                FROM Player p
                LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
                LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                LEFT JOIN GuestPlayer gp ON p.PlayerID = gp.PlayerID
                LEFT JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID
                LEFT JOIN Achievement a ON pa.AchievementID = a.AchievementID
                WHERE 1=1 $timeCondition
            ";
            
            if ($playerTypeFilter === 'registered') {
                $query .= " AND rp.PlayerID IS NOT NULL";
            } elseif ($playerTypeFilter === 'guest') {
                $query .= " AND rp.PlayerID IS NULL";
            }
            
            $query .= "
                GROUP BY p.PlayerID, pc.UserName, gp.SessionID, PlayerType
                HAVING COUNT(pa.AchievementID) > 0
                ORDER BY AchievementCount DESC, TotalPoints DESC
                LIMIT 10
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['top_achievers'] = $stmt->fetchAll();
            
            // 7. Achievement completion distribution
            $query = "
                SELECT 
                    CASE 
                        WHEN achievement_count = 0 THEN '0 achievements'
                        WHEN achievement_count BETWEEN 1 AND 2 THEN '1-2 achievements'
                        WHEN achievement_count BETWEEN 3 AND 5 THEN '3-5 achievements'
                        WHEN achievement_count BETWEEN 6 AND 10 THEN '6-10 achievements'
                        ELSE '10+ achievements'
                    END as AchievementRange,
                    COUNT(*) as PlayerCount
                FROM (
                    SELECT p.PlayerID, COUNT(pa.AchievementID) as achievement_count
                    FROM Player p
                    LEFT JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID
                    WHERE 1=1 $timeCondition" . $playerTypeCondition . "
                    GROUP BY p.PlayerID
                ) as player_achievements
                GROUP BY AchievementRange
                ORDER BY MIN(achievement_count)
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['completion_distribution'] = $stmt->fetchAll();
            
            return $data;
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get race performance data
     */
    public function getRacePerformance($timeFilter = 'all', $playerTypeFilter = 'all') {
        if (!$this->isConnected()) return null;
        
        try {
            $data = [];
            $timeCondition = $this->getTimeCondition($timeFilter);
            
            // 1. Average finish time
            $query = "
                SELECT AVG(pt.TotalTime) as avg_time
                FROM Participation pt
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['avg_finish_time'] = $result ? round($result['avg_time'], 2) : 0;
            
            // 2. Best lap record
            $query = "
                SELECT MIN(lr.LapTime) as best_lap
                FROM LapRecord lr
                JOIN Participation pt ON lr.ParticipationID = pt.ParticipationID
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['best_lap_time'] = $result ? $result['best_lap'] : 0;
            
            // 3. Most competitive race (smallest time gap)
            $query = "
                SELECT 
                    r.RaceName,
                    (MAX(pt.TotalTime) - MIN(pt.TotalTime)) as TimeGap
                FROM Race r
                JOIN Participation pt ON r.RaceID = pt.RaceID
                WHERE 1=1 $timeCondition
                GROUP BY r.RaceID, r.RaceName
                HAVING COUNT(pt.ParticipationID) >= 2
                ORDER BY TimeGap ASC
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            $data['most_competitive_race'] = $result ? $result['RaceName'] : 'N/A';
            $data['competitive_gap'] = $result ? round($result['TimeGap'], 2) : 0;
            
            // 4. Total participations
            $query = "
                SELECT COUNT(*) as total_participations
                FROM Participation pt
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['total_participations'] = $stmt->fetch()['total_participations'];
            
            // 5. Finishing position distribution
            $query = "
                SELECT 
                    pt.FinishingRank,
                    COUNT(*) as RankCount
                FROM Participation pt
                JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 $timeCondition
                GROUP BY pt.FinishingRank
                ORDER BY pt.FinishingRank
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['rank_distribution'] = $stmt->fetchAll();
            
            // 6. Best lap records by track
            $query = "
                SELECT 
                    t.TrackName,
                    MIN(lr.LapTime) as BestLap,
                    COALESCE(pc.UserName, CONCAT('Guest_', gp.SessionID)) as PlayerName
                FROM Track t
                LEFT JOIN Race r ON t.TrackName = r.TrackName
                LEFT JOIN Participation pt ON r.RaceID = pt.RaceID
                LEFT JOIN LapRecord lr ON pt.ParticipationID = lr.ParticipationID
                LEFT JOIN Player p ON pt.PlayerID = p.PlayerID
                LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
                LEFT JOIN GuestPlayer gp ON p.PlayerID = gp.PlayerID
                WHERE 1=1 $timeCondition
                GROUP BY t.TrackName
                HAVING MIN(lr.LapTime) IS NOT NULL
                ORDER BY BestLap ASC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['track_records'] = $stmt->fetchAll();
            
            // 7. Performance by kart type
            $query = "
                SELECT 
                    CASE 
                        WHEN sk.KartID IS NOT NULL THEN 'Speed'
                        WHEN ik.KartID IS NOT NULL THEN 'Item'
                        ELSE 'Standard'
                    END as KartType,
                    AVG(pt.TotalTime) as AvgTime,
                    COUNT(*) as UsageCount,
                    AVG(pt.FinishingRank) as AvgRank
                FROM Participation pt
                JOIN Race r ON pt.RaceID = r.RaceID
                JOIN Kart k ON pt.KartID = k.KartID
                LEFT JOIN SpeedKart sk ON k.KartID = sk.KartID
                LEFT JOIN ItemKart ik ON k.KartID = ik.KartID
                WHERE 1=1 $timeCondition
                GROUP BY KartType
                ORDER BY AvgTime ASC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data['kart_performance'] = $stmt->fetchAll();
            
            return $data;
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

// For backward compatibility, create global variables and functions if needed
// (This allows existing code that doesn't use the class to still work)
if (!class_exists('DashboardDataService')) {
    // Global instance for backward compatibility
    $dashboardDataService = new DashboardDataService();
    
    // Wrapper functions for backward compatibility
    function getPlayerStatistics($pdo, $timeFilter = 'all', $playerTypeFilter = 'all') {
        global $dashboardDataService;
        return $dashboardDataService->getPlayerStatistics($timeFilter, $playerTypeFilter);
    }
    
    function getSessionAnalytics($pdo, $timeFilter = 'all', $playerTypeFilter = 'all') {
        global $dashboardDataService;
        return $dashboardDataService->getSessionAnalytics($timeFilter, $playerTypeFilter);
    }
    
    function getAchievementData($pdo, $timeFilter = 'all', $playerTypeFilter = 'all') {
        global $dashboardDataService;
        return $dashboardDataService->getAchievementData($timeFilter, $playerTypeFilter);
    }
    
    function getRacePerformance($pdo, $timeFilter = 'all', $playerTypeFilter = 'all') {
        global $dashboardDataService;
        return $dashboardDataService->getRacePerformance($timeFilter, $playerTypeFilter);
    }
}
?>
