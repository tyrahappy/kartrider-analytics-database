<?php
/**
 * Achievement Dashboard Controller
 */

require_once __DIR__ . '/../../includes/BaseController.php';

class AchievementDashboardController extends BaseController {
    
    private $timeFilter;
    private $playerTypeFilter;
    private $cacheEnabled = true;
    private $cacheTime = 300; // 5 minutes cache
    
    public function __construct($timeFilter = 'all', $playerTypeFilter = 'all') {
        parent::__construct();
        $this->timeFilter = $timeFilter;
        $this->playerTypeFilter = $playerTypeFilter;
    }
    
    public function getAchievementData($pdo) {
        $data = [];
        
        $timeCondition = $this->getTimeCondition();
        $playerCondition = $this->getPlayerTypeCondition();
        
        // Check if Achievement tables exist first
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
        
        // 1. Total achievements available - Simple count
        $query = "SELECT COUNT(*) as total FROM Achievement LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['total_achievements'] = $stmt->fetch()['total'] ?? 0;
        
        // 2. Total earned achievements - Optimized
        $data['earned_achievements'] = $this->getEarnedAchievementsCount($pdo);
        
        // 3. Calculate completion rate
        $totalPlayers = $this->getTotalPlayerCount($pdo);
        $data['completion_rate'] = $totalPlayers > 0 && $data['total_achievements'] > 0 
            ? round(($data['earned_achievements'] / ($totalPlayers * $data['total_achievements'])) * 100, 2) 
            : 0;
        
        // 4. Average achievements per player - Simplified
        $data['avg_achievements_per_player'] = $this->getAvgAchievementsPerPlayer($pdo);
        
        // 5. Rarest achievement - Limited query
        $data = array_merge($data, $this->getRarestAchievement($pdo));
        
        // 6. Achievement popularity ranking - Limited to top 5
        $achievement_popularity = $this->getAchievementPopularity($pdo);
        
        // Ensure each entry has all required keys including CompletionRate
        if (empty($achievement_popularity)) {
            $data['achievement_popularity'] = [
                [
                    'AchievementName' => 'No Data Available',
                    'Description' => 'Achievement data temporarily limited',
                    'PointsAwarded' => 0,
                    'EarnedCount' => 0,
                    'CompletionRate' => 0
                ]
            ];
        } else {
            // Ensure all entries have the CompletionRate key
            foreach ($achievement_popularity as &$achievement) {
                if (!isset($achievement['CompletionRate'])) {
                    $achievement['CompletionRate'] = 0;
                }
            }
            $data['achievement_popularity'] = $achievement_popularity;
        }
        
        // 7. Top achievers - Limited to top 5
        $data['top_achievers'] = $this->getTopAchievers($pdo);
        
        // 8. Achievement completion distribution - Simplified
        $data['completion_distribution'] = $this->getAchievementCompletionDistribution($pdo);
        
        return $data;
    }
    
    private function getEarnedAchievementsCount($pdo) {
        try {
            if ($this->timeFilter !== 'all') {
                $query = "
                    SELECT COUNT(*) as earned 
                    FROM PlayerAchievement pa 
                    WHERE pa.DateEarned >= CASE 
                        WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                        WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                        WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                        ELSE '1900-01-01'
                    END
                    LIMIT 1
                ";
            } else {
                $query = "SELECT COUNT(*) as earned FROM PlayerAchievement LIMIT 1";
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetch()['earned'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function getAvgAchievementsPerPlayer($pdo) {
        try {
            // Simple calculation: total achievements / total players
            $totalAchievements = $this->getEarnedAchievementsCount($pdo);
            $totalPlayers = $this->getTotalPlayerCount($pdo);
            
            return $totalPlayers > 0 ? round($totalAchievements / $totalPlayers, 2) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function getRarestAchievement($pdo) {
        try {
            $timeCondition = "";
            if ($this->timeFilter !== 'all') {
                $timeCondition = " AND pa.DateEarned >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END";
            }
            
            $query = "
                SELECT a.AchievementName, COUNT(pa.PlayerID) as earned_count
                FROM Achievement a
                LEFT JOIN PlayerAchievement pa ON a.AchievementID = pa.AchievementID {$timeCondition}
                GROUP BY a.AchievementID, a.AchievementName
                ORDER BY earned_count ASC, a.AchievementName
                LIMIT 1
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return [
                'rarest_achievement' => $result ? $result['AchievementName'] : 'N/A',
                'rarest_achievement_count' => $result ? $result['earned_count'] : 0
            ];
        } catch (Exception $e) {
            return ['rarest_achievement' => 'N/A', 'rarest_achievement_count' => 0];
        }
    }
    
    private function getAchievementPopularity($pdo) {
        try {
            $timeCondition = "";
            if ($this->timeFilter !== 'all') {
                $timeCondition = " AND pa.DateEarned >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END";
            }
            
            // First get total player count for completion rate calculation
            $totalPlayersQuery = "SELECT COUNT(DISTINCT PlayerID) as total_players FROM Player LIMIT 1";
            $totalStmt = $pdo->prepare($totalPlayersQuery);
            $totalStmt->execute();
            $totalPlayers = $totalStmt->fetch()['total_players'] ?? 1;
            
            // Simplified query without subquery
            $query = "
                SELECT 
                    a.AchievementName,
                    a.Description,
                    a.PointsAwarded,
                    COUNT(pa.PlayerID) as EarnedCount
                FROM Achievement a
                LEFT JOIN PlayerAchievement pa ON a.AchievementID = pa.AchievementID {$timeCondition}
                GROUP BY a.AchievementID, a.AchievementName, a.Description, a.PointsAwarded
                ORDER BY EarnedCount DESC
                LIMIT 5
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            // Add CompletionRate calculation to each result
            foreach ($results as &$result) {
                $earnedCount = intval($result['EarnedCount'] ?? 0);
                $result['CompletionRate'] = $totalPlayers > 0 ? 
                    round(($earnedCount / $totalPlayers) * 100, 1) : 0;
            }
            
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function getTopAchievers($pdo) {
        try {
            $timeCondition = "";
            if ($this->timeFilter !== 'all') {
                $timeCondition = " AND pa.DateEarned >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END";
            }
            
            // Start from PlayerAchievement (smaller table) and join outward
            $query = "
                SELECT 
                    COALESCE(pc.UserName, CONCAT('Guest_', p.PlayerID)) as PlayerName,
                    COUNT(pa.AchievementID) as AchievementCount,
                    SUM(a.PointsAwarded) as TotalPoints,
                    CASE 
                        WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                        ELSE 'Guest'
                    END as PlayerType
                FROM PlayerAchievement pa
                INNER JOIN Player p ON pa.PlayerID = p.PlayerID
                INNER JOIN Achievement a ON pa.AchievementID = a.AchievementID
                LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
                LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                WHERE 1=1 {$timeCondition}
                GROUP BY p.PlayerID, pc.UserName, (rp.PlayerID IS NOT NULL)
                HAVING AchievementCount > 0
                ORDER BY AchievementCount DESC, TotalPoints DESC
                LIMIT 5
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function getAchievementCompletionDistribution($pdo) {
        try {
            // Simplified approach - get basic distribution ranges
            $ranges = [
                '0' => 0,
                '1-2' => 0,
                '3-5' => 0,
                '6-10' => 0,
                '10+' => 0
            ];
            
            // Get total players
            $totalPlayers = $this->getTotalPlayerCount($pdo);
            
            // Get players with achievements count
            $timeCondition = "";
            if ($this->timeFilter !== 'all') {
                $timeCondition = " WHERE pa.DateEarned >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END";
            }
            
            $query = "
                SELECT 
                    COUNT(pa.AchievementID) as achievement_count,
                    COUNT(*) as player_count
                FROM PlayerAchievement pa
                {$timeCondition}
                GROUP BY pa.PlayerID
                LIMIT 1000
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            $playersWithAchievements = 0;
            foreach ($results as $result) {
                $count = $result['achievement_count'];
                $playersWithAchievements++;
                
                if ($count >= 1 && $count <= 2) $ranges['1-2']++;
                elseif ($count >= 3 && $count <= 5) $ranges['3-5']++;
                elseif ($count >= 6 && $count <= 10) $ranges['6-10']++;
                elseif ($count > 10) $ranges['10+']++;
            }
            
            // Players with 0 achievements
            $ranges['0'] = max(0, $totalPlayers - $playersWithAchievements);
            
            // Convert to expected format
            $distribution = [];
            foreach ($ranges as $range => $count) {
                if ($count > 0) {
                    $distribution[] = [
                        'AchievementRange' => $range,
                        'PlayerCount' => $count
                    ];
                }
            }
            
            return $distribution;
            
        } catch (Exception $e) {
            return [['AchievementRange' => 'Data unavailable', 'PlayerCount' => 0]];
        }
    }
    
    private function getTotalPlayerCount($pdo) {
        $query = "SELECT COUNT(*) as total FROM Player";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch()['total'] ?? 0;
    }
    
    private function getTimeCondition() {
        // Return empty for 'all' to avoid unnecessary WHERE clauses
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
        // Simplified player type conditions
        switch ($this->playerTypeFilter) {
            case 'registered':
                return " AND rp.PlayerID IS NOT NULL";
            case 'guest':
                return " AND rp.PlayerID IS NULL";
            default:
                return "";
        }
    }
    
    public function getEmptyData() {
        return [
            'total_achievements' => 0,
            'earned_achievements' => 0,
            'avg_achievements_per_player' => 0,
            'completion_rate' => 0,
            'rarest_achievement' => 'N/A',
            'rarest_achievement_count' => 0,
            'achievement_popularity' => [],
            'top_achievers' => [],
            'completion_distribution' => [],
            'message' => 'No achievement data available'
        ];
    }
    
    public function getFallbackData() {
        return [
            'total_achievements' => 0,
            'earned_achievements' => 0,
            'avg_achievements_per_player' => 0,
            'completion_rate' => 0,
            'rarest_achievement' => 'System Limited',
            'rarest_achievement_count' => 0,
            'achievement_popularity' => [
                [
                    'AchievementName' => 'No Data Available',
                    'Description' => 'Achievement data temporarily limited',
                    'PointsAwarded' => 0,
                    'EarnedCount' => 0,
                    'CompletionRate' => 0
                ]
            ],
            'top_achievers' => [],
            'completion_distribution' => [],
            'message' => 'Achievement data temporarily unavailable due to hosting limitations.'
        ];
    }
}
?> 