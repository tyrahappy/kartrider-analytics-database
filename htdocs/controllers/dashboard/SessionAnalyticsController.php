<?php
/**
 * Session Analytics Controller
 */

require_once __DIR__ . '/../../includes/BaseController.php';

class SessionAnalyticsController extends BaseController {
    
    private $timeFilter;
    private $playerTypeFilter;
    private $cacheEnabled = true;
    private $cacheTime = 300; // 5 minutes cache
    
    public function __construct($timeFilter = 'all', $playerTypeFilter = 'all') {
        parent::__construct();
        $this->timeFilter = $timeFilter;
        $this->playerTypeFilter = $playerTypeFilter;
    }
    
    public function getSessionAnalytics($pdo) {
        $data = [];
        
        $timeCondition = $this->getTimeCondition();
        $playerCondition = $this->getPlayerTypeCondition();
        
        // 1. Total race count - Optimized
        if ($this->timeFilter !== 'all') {
            $query = "
                SELECT COUNT(*) as total_races 
                FROM Race r 
                WHERE r.RaceDate >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END
                LIMIT 1
            ";
        } else {
            $query = "SELECT COUNT(*) as total_races FROM Race LIMIT 1";
        }
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['total_races'] = $stmt->fetch()['total_races'] ?? 0;
        
        // 2. Average race time - Optimized
        $data['avg_race_time'] = $this->calculateAvgRaceTime($pdo);
        
        // 3. Most popular track - Simplified
        $data = array_merge($data, $this->getMostPopularTrack($pdo));
        
        // 4. Most popular kart - Simplified  
        $data = array_merge($data, $this->getMostPopularKart($pdo));
        
        // 5. Track difficulty distribution - Optimized
        $data['difficulty_distribution'] = $this->getTrackDifficultyDistribution($pdo);
        
        // 6. Kart usage statistics - Limited
        $data['kart_usage'] = $this->getKartUsageStats($pdo);
        
        // 7. Daily race trends - Limited to recent data
        $data['daily_trends'] = $this->getDailyRaceTrends($pdo);
        
        return $data;
    }
    
    private function calculateAvgRaceTime($pdo) {
        try {
            if ($this->timeFilter !== 'all') {
                $query = "
                    SELECT AVG(pt.TotalTime) as avg_race_time
                    FROM Participation pt
                    INNER JOIN Race r ON pt.RaceID = r.RaceID
                    WHERE r.RaceDate >= CASE 
                        WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                        WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                        WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                        ELSE '1900-01-01'
                    END
                    AND pt.TotalTime IS NOT NULL
                    LIMIT 1
                ";
            } else {
                $query = "SELECT AVG(TotalTime) as avg_race_time FROM Participation WHERE TotalTime IS NOT NULL LIMIT 1";
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? round($result['avg_race_time'], 2) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function getMostPopularTrack($pdo) {
        try {
            if ($this->timeFilter !== 'all') {
                $query = "
                    SELECT t.TrackName, COUNT(r.RaceID) as race_count
                    FROM Race r
                    INNER JOIN Track t ON r.TrackName = t.TrackName
                    WHERE r.RaceDate >= CASE 
                        WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                        WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                        WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                        ELSE '1900-01-01'
                    END
                    GROUP BY t.TrackName
                    ORDER BY race_count DESC
                    LIMIT 1
                ";
            } else {
                $query = "
                    SELECT r.TrackName, COUNT(*) as race_count
                    FROM Race r
                    GROUP BY r.TrackName
                    ORDER BY race_count DESC
                    LIMIT 1
                ";
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return [
                'popular_track' => $result ? $result['TrackName'] : 'N/A',
                'popular_track_count' => $result ? $result['race_count'] : 0
            ];
        } catch (Exception $e) {
            return ['popular_track' => 'N/A', 'popular_track_count' => 0];
        }
    }
    
    private function getMostPopularKart($pdo) {
        try {
            if ($this->timeFilter !== 'all') {
                $query = "
                    SELECT k.KartName, COUNT(pt.ParticipationID) as usage_count
                    FROM Participation pt
                    INNER JOIN Race r ON pt.RaceID = r.RaceID
                    INNER JOIN Kart k ON pt.KartID = k.KartID
                    WHERE r.RaceDate >= CASE 
                        WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                        WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                        WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                        ELSE '1900-01-01'
                    END
                    GROUP BY k.KartID, k.KartName
                    ORDER BY usage_count DESC
                    LIMIT 1
                ";
            } else {
                $query = "
                    SELECT k.KartName, COUNT(pt.ParticipationID) as usage_count
                    FROM Participation pt
                    INNER JOIN Kart k ON pt.KartID = k.KartID
                    GROUP BY k.KartID, k.KartName
                    ORDER BY usage_count DESC
                    LIMIT 1
                ";
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return [
                'popular_kart' => $result ? $result['KartName'] : 'N/A',
                'popular_kart_count' => $result ? $result['usage_count'] : 0
            ];
        } catch (Exception $e) {
            return ['popular_kart' => 'N/A', 'popular_kart_count' => 0];
        }
    }
    
    private function getTrackDifficultyDistribution($pdo) {
        try {
            // Debug: First check total race count
            $debugQuery = "SELECT COUNT(*) as total_races FROM Race";
            $debugStmt = $pdo->prepare($debugQuery);
            $debugStmt->execute();
            $totalRaces = $debugStmt->fetch()['total_races'] ?? 0;
            error_log("DEBUG: Total races in database: " . $totalRaces);
            
            // Debug: Check Track table count
            $trackCountQuery = "SELECT COUNT(*) as track_count FROM Track";
            $trackCountStmt = $pdo->prepare($trackCountQuery);
            $trackCountStmt->execute();
            $trackCount = $trackCountStmt->fetch()['track_count'] ?? 0;
            error_log("DEBUG: Total tracks in database: " . $trackCount);
            
            // Debug: Check sample Race track names
            $raceTrackQuery = "SELECT DISTINCT TrackName FROM Race LIMIT 5";
            $raceTrackStmt = $pdo->prepare($raceTrackQuery);
            $raceTrackStmt->execute();
            $raceTrackNames = $raceTrackStmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("DEBUG: Sample Race track names: " . json_encode($raceTrackNames));
            
            // Debug: Check sample Track table names
            $trackNameQuery = "SELECT TrackName, TrackDifficulty FROM Track LIMIT 5";
            $trackNameStmt = $pdo->prepare($trackNameQuery);
            $trackNameStmt->execute();
            $trackNames = $trackNameStmt->fetchAll();
            error_log("DEBUG: Sample Track table data: " . json_encode($trackNames));
            
            // Simple and efficient approach - direct JOIN query
            $timeCondition = "";
            $params = [];
            
            if ($this->timeFilter !== 'all') {
                $timeCondition = " WHERE r.RaceDate >= ?";
                $params[] = $this->getDateFilterValue();
                
                // Debug: Check filtered race count
                $filteredQuery = "SELECT COUNT(*) as filtered_races FROM Race r" . $timeCondition;
                $filteredStmt = $pdo->prepare($filteredQuery);
                $filteredStmt->execute($params);
                $filteredRaces = $filteredStmt->fetch()['filtered_races'] ?? 0;
                error_log("DEBUG: Filtered races for {$this->timeFilter}: " . $filteredRaces);
            }
            
            // Debug: Check Track table integrity
            $trackDebugQuery = "
                SELECT 
                    TrackName, 
                    TrackDifficulty,
                    COUNT(*) as track_count 
                FROM Track 
                GROUP BY TrackName, TrackDifficulty 
                HAVING track_count > 1
            ";
            $trackDebugStmt = $pdo->prepare($trackDebugQuery);
            $trackDebugStmt->execute();
            $duplicateTracks = $trackDebugStmt->fetchAll();
            if (!empty($duplicateTracks)) {
                error_log("DEBUG: Found duplicate tracks: " . json_encode($duplicateTracks));
            }
            
            // Main query with debug info - use DISTINCT to avoid duplicate counting
            $query = "
                SELECT 
                    t.TrackDifficulty,
                    COUNT(DISTINCT r.RaceID) as RaceCount,
                    0 as AvgTime
                FROM Race r
                INNER JOIN Track t ON r.TrackName = t.TrackName
                {$timeCondition}
                GROUP BY t.TrackDifficulty 
                HAVING RaceCount > 0
                ORDER BY RaceCount DESC 
                LIMIT 5
            ";
            
            error_log("DEBUG: Executing query: " . str_replace(["\n", "\t"], [" ", ""], $query));
            error_log("DEBUG: With params: " . json_encode($params));
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            error_log("DEBUG: Query results: " . json_encode($results));
            
            // Ensure numeric values and clean data
            foreach ($results as &$result) {
                $result['RaceCount'] = (int)$result['RaceCount'];
                $result['AvgTime'] = $result['AvgTime'] ? (float)$result['AvgTime'] : 0;
            }
            
            // Return results or simple fallback
            return !empty($results) ? $results : $this->getSimpleTrackDifficulties();
            
        } catch (Exception $e) {
            error_log("Track difficulty query failed: " . $e->getMessage());
            return $this->getSimpleTrackDifficulties();
        }
    }
    
    private function getSimpleTrackDifficulties() {
        return [
            ['TrackDifficulty' => 'Easy', 'RaceCount' => 0, 'AvgTime' => 0],
            ['TrackDifficulty' => 'Medium', 'RaceCount' => 0, 'AvgTime' => 0],
            ['TrackDifficulty' => 'Hard', 'RaceCount' => 0, 'AvgTime' => 0],
        ];
    }
    
    private function getDateFilterValue() {
        switch ($this->timeFilter) {
            case '7days':
                return date('Y-m-d H:i:s', strtotime('-7 days'));
            case '30days':
                return date('Y-m-d H:i:s', strtotime('-30 days'));
            case '3months':
                return date('Y-m-d H:i:s', strtotime('-3 months'));
            default:
                return '1900-01-01 00:00:00';
        }
    }
    
    private function getKartUsageStats($pdo) {
        try {
            // Ultra-safe approach - separate queries to avoid complex JOINs
            if ($this->timeFilter !== 'all') {
                // Get recent participations first
                $dateFilter = $this->getDateFilterValue();
                $recentParticipationsQuery = "
                    SELECT pt.KartID, COUNT(*) as count
                    FROM Participation pt
                    INNER JOIN Race r ON pt.RaceID = r.RaceID
                    WHERE r.RaceDate >= ?
                    GROUP BY pt.KartID
                    ORDER BY count DESC
                    LIMIT 5
                ";
                $stmt = $pdo->prepare($recentParticipationsQuery);
                $stmt->execute([$dateFilter]);
                $kartCounts = $stmt->fetchAll();
            } else {
                $allTimeQuery = "
                    SELECT KartID, COUNT(*) as count
                    FROM Participation
                    GROUP BY KartID
                    ORDER BY count DESC
                    LIMIT 5
                ";
                $stmt = $pdo->prepare($allTimeQuery);
                $stmt->execute();
                $kartCounts = $stmt->fetchAll();
            }
            
            $results = [];
            foreach ($kartCounts as $kartData) {
                $kartId = $kartData['KartID'];
                $usageCount = $kartData['count'];
                
                // Get kart details safely
                $kartInfo = $this->getKartInfo($pdo, $kartId);
                $avgTime = $this->getKartAvgTime($pdo, $kartId);
                $avgRank = $this->getKartAvgRank($pdo, $kartId);
                
                $results[] = [
                    'KartName' => $kartInfo['name'],
                    'KartType' => $kartInfo['type'],
                    'UsageCount' => $usageCount,
                    'AvgTime' => $avgTime,
                    'AvgRank' => $avgRank
                ];
            }
            
            return !empty($results) ? $results : $this->getDefaultKartStats();
            
        } catch (Exception $e) {
            error_log("Kart usage query failed: " . $e->getMessage());
            return $this->getDefaultKartStats();
        }
    }
    
    private function getKartInfo($pdo, $kartId) {
        try {
            $query = "
                SELECT 
                    k.KartName,
                    CASE 
                        WHEN sk.KartID IS NOT NULL THEN 'Speed'
                        WHEN ik.KartID IS NOT NULL THEN 'Item'
                        ELSE 'Standard'
                    END as KartType
                FROM Kart k
                LEFT JOIN SpeedKart sk ON k.KartID = sk.KartID
                LEFT JOIN ItemKart ik ON k.KartID = ik.KartID
                WHERE k.KartID = ?
                LIMIT 1
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$kartId]);
            $result = $stmt->fetch();
            
            return [
                'name' => $result ? $result['KartName'] : 'Unknown Kart',
                'type' => $result ? $result['KartType'] : 'Standard'
            ];
        } catch (Exception $e) {
            return ['name' => 'Unknown Kart', 'type' => 'Standard'];
        }
    }
    
    private function getKartAvgTime($pdo, $kartId) {
        try {
            $query = "SELECT AVG(TotalTime) as avg FROM Participation WHERE KartID = ? AND TotalTime IS NOT NULL LIMIT 100";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$kartId]);
            $result = $stmt->fetch();
            return $result ? round($result['avg'], 2) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function getKartAvgRank($pdo, $kartId) {
        try {
            $query = "SELECT AVG(FinishingRank) as avg FROM Participation WHERE KartID = ? LIMIT 100";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$kartId]);
            $result = $stmt->fetch();
            return $result ? round($result['avg'], 1) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function getDefaultKartStats() {
        return [
            [
                'KartName' => 'Default Kart',
                'KartType' => 'Standard',
                'UsageCount' => 0,
                'AvgTime' => 0,
                'AvgRank' => 0
            ]
        ];
    }
    
    private function getDailyRaceTrends($pdo) {
        try {
            // Fixed to last 7 days
            $daysLimit = 7;
            
            $query = "
                SELECT 
                    DATE(r.RaceDate) as RaceDay,
                    COUNT(*) as DailyRaces,
                    COUNT(pt.ParticipationID) as TotalParticipations
                FROM Race r
                LEFT JOIN Participation pt ON r.RaceID = pt.RaceID
                WHERE r.RaceDate >= DATE_SUB(NOW(), INTERVAL {$daysLimit} DAY)
                GROUP BY DATE(r.RaceDate)
                ORDER BY RaceDay DESC
                LIMIT {$daysLimit}
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
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
            'total_races' => 0,
            'avg_race_time' => 0,
            'popular_track' => 'N/A',
            'popular_track_count' => 0,
            'popular_kart' => 'N/A',
            'popular_kart_count' => 0,
            'difficulty_distribution' => [],
            'kart_usage' => [],
            'daily_trends' => [],
            'message' => 'No data available or query limits exceeded'
        ];
    }
    
    public function getFallbackData() {
        return [
            'total_races' => 'Limited',
            'avg_race_time' => 'Limited',
            'popular_track' => 'Limited',
            'popular_track_count' => 0,
            'popular_kart' => 'Limited',
            'popular_kart_count' => 0,
            'difficulty_distribution' => [
                ['TrackDifficulty' => 'Easy', 'RaceCount' => 1, 'AvgTime' => 180.5],
                ['TrackDifficulty' => 'Medium', 'RaceCount' => 1, 'AvgTime' => 210.3],
                ['TrackDifficulty' => 'Hard', 'RaceCount' => 1, 'AvgTime' => 245.7],
                ['TrackDifficulty' => 'Data Limited', 'RaceCount' => 0, 'AvgTime' => 0]
            ],
            'kart_usage' => [
                ['KartName' => 'Default Kart', 'KartType' => 'Standard', 'UsageCount' => 0, 'AvgTime' => 0, 'AvgRank' => 0]
            ],
            'daily_trends' => [],
            'message' => 'Session analytics temporarily limited due to hosting restrictions.'
        ];
    }
    
    // Optimization methods
    private function optimizeForInfinityFree($pdo) {
        try {
            // Set ultra-conservative settings for InfinityFree
            $pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
            $pdo->exec("SET SESSION optimizer_search_depth = 0");
            $pdo->exec("SET SESSION max_join_size = 100000"); // Very conservative
            // 移除 query_cache_limit - 这是全局变量，不能使用 SET SESSION
            $pdo->exec("SET SESSION max_execution_time = 10"); // 10 seconds max
            
            return true;
        } catch (Exception $e) {
            // If we can't set these, we're probably on a restricted host
            error_log("InfinityFree optimization failed: " . $e->getMessage());
            return false;
        }
    }
}
?> 