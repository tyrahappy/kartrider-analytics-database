<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/../includes/BaseController.php';

class DashboardController extends BaseController {
    
    private $selectedModule;
    private $timeFilter;
    private $playerTypeFilter;
    
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('Dashboard - KartRider Analytics');
        $this->parseParameters();
    }
    
    private function parseParameters() {
        $this->selectedModule = $_GET['module'] ?? 'player_stats';
        $this->timeFilter = $_GET['time_filter'] ?? 'all';
        $this->playerTypeFilter = $_GET['player_type'] ?? 'all';
        
        // Validate module
        $validModules = ['player_stats', 'session_analytics', 'achievements'];
        if (!in_array($this->selectedModule, $validModules)) {
            $this->selectedModule = 'player_stats';
        }
        
        // Validate time filter
        $validTimeFilters = ['all', '7days', '30days', '3months'];
        if (!in_array($this->timeFilter, $validTimeFilters)) {
            $this->timeFilter = 'all';
        }
        
        // Validate player type filter
        $validPlayerTypes = ['all', 'registered', 'guest'];
        if (!in_array($this->playerTypeFilter, $validPlayerTypes)) {
            $this->playerTypeFilter = 'all';
        }
    }
    
    public function run() {
        try {
            if (!$this->checkDatabaseConnection()) {
                $this->setError("Database connection failed. Please check your configuration.");
            }
            
            $dashboardData = $this->loadDashboardData();
            
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'selectedModule' => $this->selectedModule,
                'timeFilter' => $this->timeFilter,
                'playerTypeFilter' => $this->playerTypeFilter,
                'dashboardData' => $dashboardData,
                'controller' => $this
            ]);
            
        } catch (Exception $e) {
            $this->setError("Application Error: " . $e->getMessage());
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'selectedModule' => $this->selectedModule,
                'controller' => $this
            ]);
        }
    }
    
    private function loadDashboardData() {
        if (!$this->db->isConnected()) {
            return $this->getEmptyData();
        }
        
        try {
            $pdo = $this->db->getConnection();
            
            switch ($this->selectedModule) {
                case 'player_stats':
                    return $this->getPlayerStatistics($pdo);
                case 'session_analytics':
                    return $this->getSessionAnalytics($pdo);
                case 'achievements':
                    return $this->getAchievementData($pdo);
                default:
                    return $this->getPlayerStatistics($pdo);
            }
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    private function getPlayerStatistics($pdo) {
        $data = [];
        
        // Time filter condition
        $timeCondition = $this->getTimeCondition();
        $playerCondition = $this->getPlayerTypeCondition();
        
        // 1. Total players
        $query = "SELECT COUNT(*) as total FROM Player p" . $playerCondition;
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['total_players'] = $stmt->fetch()['total'] ?? 0;
        
        // 2. Active players (with race participation)
        $query = "
            SELECT COUNT(DISTINCT p.PlayerID) as active_players
            FROM Player p
            JOIN Participation pt ON p.PlayerID = pt.PlayerID
            JOIN Race r ON pt.RaceID = r.RaceID
            WHERE 1=1 {$timeCondition} {$playerCondition}
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['active_players'] = $stmt->fetch()['active_players'] ?? 0;
        
        // 3. Average races per player
        $query = "
            SELECT AVG(race_count) as avg_races
            FROM (
                SELECT COUNT(*) as race_count
                FROM Participation pt
                JOIN Race r ON pt.RaceID = r.RaceID
                JOIN Player p ON pt.PlayerID = p.PlayerID
                WHERE 1=1 {$timeCondition} {$playerCondition}
                GROUP BY pt.PlayerID
            ) player_races
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['avg_races_per_player'] = round($stmt->fetch()['avg_races'] ?? 0, 1);
        
        // 4. Active Rate Recent Week
        $query = "
            SELECT 
                COUNT(DISTINCT p.PlayerID) as total_players,
                COUNT(DISTINCT pt.PlayerID) as active_recent_week
            FROM Player p
            LEFT JOIN Participation pt ON p.PlayerID = pt.PlayerID
            LEFT JOIN Race r ON pt.RaceID = r.RaceID 
                AND r.RaceDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            WHERE 1=1 {$playerCondition}
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $weekData = $stmt->fetch();
        $totalPlayers = $weekData['total_players'] ?? 0;
        $activeRecentWeek = $weekData['active_recent_week'] ?? 0;
        $data['active_rate_recent_week'] = $totalPlayers > 0 ? round(($activeRecentWeek / $totalPlayers) * 100, 1) : 0;
        
        // 5. Player type distribution
        $query = "
            SELECT 
                CASE 
                    WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                    ELSE 'Guest'
                END as PlayerType,
                COUNT(*) as PlayerCount
            FROM Player p
            LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
            {$playerCondition}
            GROUP BY PlayerType
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['player_distribution'] = $stmt->fetchAll();
        
        // 6. Win rate ranking
        $query = "
            SELECT 
                COALESCE(pc.UserName, CONCAT('Guest_', p.PlayerID)) as PlayerName,
                COUNT(*) as TotalRaces,
                SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) as Wins,
                ROUND((SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as WinRate,
                CASE WHEN rp.PlayerID IS NOT NULL THEN 'Registered' ELSE 'Guest' END as PlayerType
            FROM Player p
            LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
            LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
            JOIN Participation pt ON p.PlayerID = pt.PlayerID
            JOIN Race r ON pt.RaceID = r.RaceID
            WHERE 1=1 {$timeCondition} {$playerCondition}
            GROUP BY p.PlayerID
            HAVING TotalRaces >= 3
            ORDER BY WinRate DESC, TotalRaces DESC
            LIMIT 5
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['win_rate_ranking'] = $stmt->fetchAll();
        
        // 7. Race Participation Distribution 
        $query = "
            SELECT 
                CASE 
                    WHEN race_count = 0 THEN 'No Races'
                    WHEN race_count BETWEEN 1 AND 5 THEN '1-5 Races'
                    WHEN race_count BETWEEN 6 AND 15 THEN '6-15 Races'
                    WHEN race_count BETWEEN 16 AND 30 THEN '16-30 Races'
                    WHEN race_count BETWEEN 31 AND 50 THEN '31-50 Races'
                    ELSE '50+ Races'
                END as ParticipationRange,
                COUNT(*) as PlayerCount
            FROM (
                SELECT 
                    p.PlayerID,
                    COUNT(pt.RaceID) as race_count
                FROM Player p
                LEFT JOIN Participation pt ON p.PlayerID = pt.PlayerID
                LEFT JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 {$timeCondition} {$playerCondition}
                GROUP BY p.PlayerID
            ) player_participation
            GROUP BY ParticipationRange
            ORDER BY 
                CASE ParticipationRange
                    WHEN 'No Races' THEN 1
                    WHEN '1-5 Races' THEN 2
                    WHEN '6-15 Races' THEN 3
                    WHEN '16-30 Races' THEN 4
                    WHEN '31-50 Races' THEN 5
                    WHEN '50+ Races' THEN 6
                END
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['race_participation_distribution'] = $stmt->fetchAll();
        
        return $data;
    }
    
    private function getSessionAnalytics($pdo) {
        $data = [];
        
        $timeCondition = $this->getTimeCondition();
        $playerCondition = $this->getPlayerTypeCondition();
        
        // 1. Total race count
        $query = "SELECT COUNT(*) as total_races FROM Race r WHERE 1=1 {$timeCondition}";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['total_races'] = $stmt->fetch()['total_races'] ?? 0;
        
        // 2. Average race time
        $query = "
            SELECT AVG(pt.TotalTime) as avg_race_time
            FROM Participation pt
            JOIN Race r ON pt.RaceID = r.RaceID
            JOIN Player p ON pt.PlayerID = p.PlayerID
            WHERE 1=1 {$timeCondition} {$playerCondition}
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $data['avg_race_time'] = $result ? round($result['avg_race_time'], 2) : 0;
        
        // 3. Most popular track
        $query = "
            SELECT t.TrackName, COUNT(r.RaceID) as race_count
            FROM Track t
            JOIN Race r ON t.TrackName = r.TrackName
            WHERE 1=1 {$timeCondition}
            GROUP BY t.TrackName
            ORDER BY race_count DESC
            LIMIT 1
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $data['popular_track'] = $result ? $result['TrackName'] : 'N/A';
        $data['popular_track_count'] = $result ? $result['race_count'] : 0;
        
        // 4. Most popular kart
        $query = "
            SELECT k.KartName, COUNT(pt.ParticipationID) as usage_count
            FROM Kart k
            JOIN Participation pt ON k.KartID = pt.KartID
            JOIN Race r ON pt.RaceID = r.RaceID
            JOIN Player p ON pt.PlayerID = p.PlayerID
            WHERE 1=1 {$timeCondition} {$playerCondition}
            GROUP BY k.KartID, k.KartName
            ORDER BY usage_count DESC
            LIMIT 1
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $data['popular_kart'] = $result ? $result['KartName'] : 'N/A';
        $data['popular_kart_count'] = $result ? $result['usage_count'] : 0;
        
        // 5. Track difficulty distribution
        $query = "
            SELECT 
                t.TrackDifficulty,
                COUNT(r.RaceID) as RaceCount,
                AVG(pt.TotalTime) as AvgTime
            FROM Track t
            LEFT JOIN Race r ON t.TrackName = r.TrackName
            LEFT JOIN Participation pt ON r.RaceID = pt.RaceID
            LEFT JOIN Player p ON pt.PlayerID = p.PlayerID
            WHERE 1=1 {$timeCondition} {$playerCondition}
            GROUP BY t.TrackDifficulty
            ORDER BY RaceCount DESC
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['difficulty_distribution'] = $stmt->fetchAll();
        
        // 6. Kart usage statistics
        $query = "
            SELECT 
                k.KartName,
                CASE 
                    WHEN sk.KartID IS NOT NULL THEN 'Speed'
                    WHEN ik.KartID IS NOT NULL THEN 'Item'
                    ELSE 'Standard'
                END as KartType,
                COUNT(pt.ParticipationID) as UsageCount,
                AVG(pt.TotalTime) as AvgTime,
                AVG(pt.FinishingRank) as AvgRank
            FROM Kart k
            LEFT JOIN SpeedKart sk ON k.KartID = sk.KartID
            LEFT JOIN ItemKart ik ON k.KartID = ik.KartID
            LEFT JOIN Participation pt ON k.KartID = pt.KartID
            LEFT JOIN Race r ON pt.RaceID = r.RaceID
            LEFT JOIN Player p ON pt.PlayerID = p.PlayerID
            WHERE pt.ParticipationID IS NOT NULL {$timeCondition} {$playerCondition}
            GROUP BY k.KartID, k.KartName, KartType
            ORDER BY UsageCount DESC
            LIMIT 10
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['kart_usage'] = $stmt->fetchAll();
        
        // 7. Daily race trends (last 30 days)
        $query = "
            SELECT 
                DATE(r.RaceDate) as RaceDay,
                COUNT(*) as DailyRaces,
                COUNT(DISTINCT pt.PlayerID) as UniquePlayers
            FROM Race r
            LEFT JOIN Participation pt ON r.RaceID = pt.RaceID
            LEFT JOIN Player p ON pt.PlayerID = p.PlayerID
            WHERE r.RaceDate >= DATE_SUB(NOW(), INTERVAL 30 DAY) {$playerCondition}
            GROUP BY DATE(r.RaceDate)
            ORDER BY RaceDay DESC
            LIMIT 15
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['daily_trends'] = $stmt->fetchAll();
        
        return $data;
    }
    
    private function getAchievementData($pdo) {
        $data = [];
        
        $timeCondition = $this->getTimeCondition();
        $playerCondition = $this->getPlayerTypeCondition();
        
        // Check if Achievement tables exist
        try {
            $pdo->query("SELECT 1 FROM Achievement LIMIT 1");
        } catch (Exception $e) {
            // Achievement tables don't exist, return placeholder data
            return [
                'total_achievements' => 0,
                'earned_achievements' => 0,
                'avg_achievements_per_player' => 0,
                'completion_rate' => 0,
                'rarest_achievement' => 'No achievements available',
                'achievement_popularity' => [],
                'top_achievers' => [],
                'completion_distribution' => [],
                'message' => 'Achievement system not yet implemented in database'
            ];
        }
        
        // 1. Total achievements available
        $query = "SELECT COUNT(*) as total FROM Achievement";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['total_achievements'] = $stmt->fetch()['total'] ?? 0;
        
        // 2. Total earned achievements
        $timeConditionAchievement = str_replace('r.RaceDate', 'pa.DateEarned', $timeCondition);
        $query = "SELECT COUNT(*) as earned FROM PlayerAchievement pa WHERE 1=1 {$timeConditionAchievement}";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['earned_achievements'] = $stmt->fetch()['earned'] ?? 0;
        
        // 3. Achievement completion rate
        $totalPlayers = $this->getTotalPlayerCount($pdo);
        $data['completion_rate'] = $totalPlayers > 0 && $data['total_achievements'] > 0 
            ? round(($data['earned_achievements'] / ($totalPlayers * $data['total_achievements'])) * 100, 2) 
            : 0;
        
        // 4. Average achievements per player
        $query = "
            SELECT AVG(achievement_count) as avg_achievements
            FROM (
                SELECT p.PlayerID, COUNT(pa.AchievementID) as achievement_count
                FROM Player p
                LEFT JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID
                WHERE 1=1 {$timeConditionAchievement} {$playerCondition}
                GROUP BY p.PlayerID
            ) as player_achievements
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $data['avg_achievements_per_player'] = $result ? round($result['avg_achievements'], 2) : 0;
        
        // 5. Rarest achievement (least earned)
        $query = "
            SELECT a.AchievementName, COUNT(pa.PlayerID) as earned_count
            FROM Achievement a
            LEFT JOIN PlayerAchievement pa ON a.AchievementID = pa.AchievementID
            WHERE 1=1 {$timeConditionAchievement}
            GROUP BY a.AchievementID, a.AchievementName
            ORDER BY earned_count ASC, a.AchievementName
            LIMIT 1
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $data['rarest_achievement'] = $result ? $result['AchievementName'] : 'N/A';
        $data['rarest_achievement_count'] = $result ? $result['earned_count'] : 0;
        
        // 6. Achievement popularity ranking
        $query = "
            SELECT 
                a.AchievementName,
                a.Description,
                a.PointsAwarded,
                COUNT(pa.PlayerID) as EarnedCount,
                ROUND(COUNT(pa.PlayerID) * 100.0 / (SELECT COUNT(*) FROM Player), 2) as CompletionRate
            FROM Achievement a
            LEFT JOIN PlayerAchievement pa ON a.AchievementID = pa.AchievementID
            WHERE 1=1 {$timeConditionAchievement}
            GROUP BY a.AchievementID, a.AchievementName, a.Description, a.PointsAwarded
            ORDER BY EarnedCount DESC
            LIMIT 10
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['achievement_popularity'] = $stmt->fetchAll();
        
        // 7. Top players by achievement count
        $query = "
            SELECT 
                COALESCE(pc.UserName, CONCAT('Guest_', p.PlayerID)) as PlayerName,
                COUNT(pa.AchievementID) as AchievementCount,
                SUM(a.PointsAwarded) as TotalPoints,
                CASE 
                    WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                    ELSE 'Guest'
                END as PlayerType
            FROM Player p
            LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
            LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
            LEFT JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID
            LEFT JOIN Achievement a ON pa.AchievementID = a.AchievementID
            WHERE 1=1 {$timeConditionAchievement} {$playerCondition}
            GROUP BY p.PlayerID, pc.UserName, PlayerType
            HAVING COUNT(pa.AchievementID) > 0
            ORDER BY AchievementCount DESC, TotalPoints DESC
            LIMIT 10
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['top_achievers'] = $stmt->fetchAll();
        
        // 8. Achievement completion distribution
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
                WHERE 1=1 {$timeConditionAchievement} {$playerCondition}
                GROUP BY p.PlayerID
            ) as player_achievements
            GROUP BY AchievementRange
            ORDER BY MIN(achievement_count)
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['completion_distribution'] = $stmt->fetchAll();
        
        return $data;
    }
    
    private function getTotalPlayerCount($pdo) {
        $query = "SELECT COUNT(*) as total FROM Player";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch()['total'] ?? 0;
    }
    
    private function getTimeCondition() {
        switch ($this->timeFilter) {
            case '7days':
                return " AND r.RaceDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case '30days':
                return " AND r.RaceDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case '3months':
                return " AND r.RaceDate >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
            default:
                return "";
        }
    }
    
    private function getPlayerTypeCondition() {
        switch ($this->playerTypeFilter) {
            case 'registered':
                return " AND rp.PlayerID IS NOT NULL";
            case 'guest':
                return " AND rp.PlayerID IS NULL";
            default:
                return "";
        }
    }
    
    private function getEmptyData() {
        return [
            'total_players' => 0,
            'active_players' => 0,
            'avg_races_per_player' => 0,
            'player_distribution' => [],
            'win_rate_ranking' => [],
            'track_performance' => []
        ];
    }
    
    public function getModules() {
        return [
            'player_stats' => '👥 Player Statistics',
            'session_analytics' => '🏁 Session Analytics', 
            'achievements' => '🏆 Achievements'
        ];
    }
    
    public function getTimeFilterOptions() {
        return [
            'all' => 'All Time',
            '7days' => 'Last 7 Days',
            '30days' => 'Last 30 Days',
            '3months' => 'Last 3 Months'
        ];
    }
    
    public function getPlayerTypeOptions() {
        return [
            'all' => 'All Players',
            'registered' => 'Registered Only',
            'guest' => 'Guest Only'
        ];
    }
    
    // Getters
    public function getSelectedModule() { return $this->selectedModule; }
    public function getTimeFilter() { return $this->timeFilter; }
    public function getPlayerTypeFilter() { return $this->playerTypeFilter; }
}
?>
