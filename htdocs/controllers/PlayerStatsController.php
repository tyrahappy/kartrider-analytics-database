<?php

require_once __DIR__ . '/BaseController.php';

class PlayerStatsController extends BaseController {
    
    private $timeFilter;
    private $playerTypeFilter;
    private $cacheEnabled = true;
    private $cacheTime = 300; // 5 minutes cache
    
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('Player Statistics - KartRider Analytics');
        $this->parseParameters();
    }
    
    private function parseParameters() {
        $this->timeFilter = $_GET['time_filter'] ?? 'all';
        $this->playerTypeFilter = $_GET['player_type'] ?? 'all';
        
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
                $this->setError("Database connection failed");
                $this->renderView(__DIR__ . '/../views/layout.php', [
                    'controller' => $this
                ]);
                return;
            }
            
            $playerStatsData = $this->loadPlayerStatsData();
            
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'controller' => $this,
                'data' => $playerStatsData
            ]);
            
        } catch (Exception $e) {
            $this->setError("Application Error: " . $e->getMessage());
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'controller' => $this
            ]);
        }
    }
    
    private function loadPlayerStatsData() {
        if (!$this->db->isConnected()) {
            return $this->getEmptyData();
        }
        
        try {
            $pdo = $this->db->getConnection();
            
            // Check cache first
            $cacheKey = $this->getCacheKey();
            $cachedData = $this->getFromCache($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
            
            // Optimize for InfinityFree if applicable
            $this->optimizeForInfinityFree($pdo);
            
            // Generate fresh data
            $data = $this->getPlayerStatistics($pdo);
            
            // Store in cache
            $this->storeInCache($cacheKey, $data);
            
            return $data;
            
        } catch (Exception $e) {
            error_log("Player stats error: " . $e->getMessage());
            
            if ($this->isInfinityFreeError($e->getMessage())) {
                return $this->getFallbackPlayerStatsData();
            }
            
            $emptyData = $this->getEmptyData();
            $emptyData['error'] = 'Data temporarily unavailable due to server limitations';
            return $emptyData;
        }
    }
    
    private function getPlayerStatistics($pdo) {
        $data = [];
        
        // Time filter condition
        $timeCondition = $this->getTimeCondition();
        $playerCondition = $this->getPlayerTypeCondition();
        
        // 1. Total players - Simple count with LIMIT for safety
        $query = "SELECT COUNT(*) as total FROM Player p WHERE 1=1" . str_replace('rp.', 'p.', $playerCondition) . " LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['total_players'] = $stmt->fetch()['total'] ?? 0;
        
        // 2. Active players - Optimized with direct join and stricter conditions
        if ($this->timeFilter !== 'all') {
            $query = "
                SELECT COUNT(DISTINCT pt.PlayerID) as active_players
                FROM Race r
                INNER JOIN Participation pt ON r.RaceID = pt.RaceID
                WHERE r.RaceDate >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END
                LIMIT 1
            ";
        } else {
            $query = "SELECT COUNT(DISTINCT PlayerID) as active_players FROM Participation LIMIT 1";
        }
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data['active_players'] = $stmt->fetch()['active_players'] ?? 0;
        
        // 3. Average races per player - Simplified calculation
        $data['avg_races_per_player'] = $this->calculateAvgRacesPerPlayer($pdo);
        
        // 4. Active Rate Recent Week - Simplified separate queries
        $data['active_rate_recent_week'] = $this->calculateActiveRateRecentWeek($pdo);
        
        // 5. Player type distribution - Simplified
        $data['player_distribution'] = $this->getPlayerDistribution($pdo);
        
        // 6. Win rate ranking - Optimized with strict limits
        $data['win_rate_ranking'] = $this->getWinRateRanking($pdo);
        
        // 7. Race Participation Distribution - Simplified approach
        $data['race_participation_distribution'] = $this->getRaceParticipationDistribution($pdo);
        
        return $data;
    }
    
    private function calculateAvgRacesPerPlayer($pdo) {
        try {
            if ($this->timeFilter !== 'all') {
                $dateFilter = $this->getDateFilterValue();
                
                $participationQuery = "
                    SELECT COUNT(*) as total
                    FROM Participation pt
                    INNER JOIN Race r ON pt.RaceID = r.RaceID
                    WHERE r.RaceDate >= ?
                    LIMIT 1
                ";
                $stmt = $pdo->prepare($participationQuery);
                $stmt->execute([$dateFilter]);
                $totalParticipations = $stmt->fetch()['total'] ?? 0;
                
                $playersQuery = "
                    SELECT COUNT(DISTINCT pt.PlayerID) as unique_players
                    FROM Participation pt
                    INNER JOIN Race r ON pt.RaceID = r.RaceID
                    WHERE r.RaceDate >= ?
                    LIMIT 1
                ";
                $stmt = $pdo->prepare($playersQuery);
                $stmt->execute([$dateFilter]);
                $uniquePlayers = $stmt->fetch()['unique_players'] ?? 0;
            } else {
                $totalParticipations = $this->executeSimpleCount($pdo, "SELECT COUNT(*) as count FROM Participation");
                $uniquePlayers = $this->executeSimpleCount($pdo, "SELECT COUNT(DISTINCT PlayerID) as count FROM Participation");
            }
            
            return $uniquePlayers > 0 ? round($totalParticipations / $uniquePlayers, 1) : 0;
            
        } catch (Exception $e) {
            error_log("Average races calculation failed: " . $e->getMessage());
            return 0;
        }
    }
    
    private function calculateActiveRateRecentWeek($pdo) {
        try {
            $totalPlayers = $this->executeSimpleCount($pdo, "SELECT COUNT(*) as count FROM Player");
            
            if ($totalPlayers === 0) return 0;
            
            $weekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
            $activeQuery = "
                SELECT COUNT(DISTINCT pt.PlayerID) as active_count
                FROM Participation pt
                INNER JOIN Race r ON pt.RaceID = r.RaceID
                WHERE r.RaceDate >= ?
                LIMIT 1
            ";
            $stmt = $pdo->prepare($activeQuery);
            $stmt->execute([$weekAgo]);
            $activeCount = $stmt->fetch()['active_count'] ?? 0;
            
            return round(($activeCount / $totalPlayers) * 100, 1);
            
        } catch (Exception $e) {
            error_log("Active rate calculation failed: " . $e->getMessage());
            return 0;
        }
    }
    
    private function getPlayerDistribution($pdo) {
        try {
            $query = "
                SELECT 
                    CASE 
                        WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                        ELSE 'Guest'
                    END as PlayerType,
                    COUNT(*) as PlayerCount
                FROM Player p
                LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                GROUP BY (rp.PlayerID IS NOT NULL)
                LIMIT 2
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function getWinRateRanking($pdo) {
        try {
            // Simplified approach using only Participation table to avoid complex JOINs
            $timeCondition = "";
            $params = [];
            
            if ($this->timeFilter !== 'all') {
                $timeCondition = " WHERE r.RaceDate >= ?";
                $params[] = $this->getDateFilterValue();
            }
            
            // Get top performers by win rate using minimal JOINs
            $query = "
                SELECT 
                    pt.PlayerID,
                    COUNT(*) as TotalRaces,
                    SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) as Wins,
                    ROUND((SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as WinRate
                FROM Participation pt
                INNER JOIN Race r ON pt.RaceID = r.RaceID
                {$timeCondition}
                GROUP BY pt.PlayerID
                HAVING TotalRaces >= 2
                ORDER BY WinRate DESC, TotalRaces DESC
                LIMIT 5
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            // Post-process to get player info safely
            $finalResults = [];
            foreach ($results as $result) {
                $playerInfo = $this->getPlayerNameSafely($pdo, $result['PlayerID']);
                $playerType = $this->getPlayerTypeSafely($pdo, $result['PlayerID']);
                
                $finalResults[] = [
                    'PlayerName' => $playerInfo,
                    'PlayerType' => $playerType,
                    'TotalRaces' => $result['TotalRaces'],
                    'Wins' => $result['Wins'],
                    'WinRate' => $result['WinRate']
                ];
            }
            
            return !empty($finalResults) ? $finalResults : $this->getSimpleDefaultWinRateRanking();
            
        } catch (Exception $e) {
            error_log("Win rate ranking query failed: " . $e->getMessage());
            return $this->getSimpleDefaultWinRateRanking();
        }
    }
    
    private function getPlayerNameSafely($pdo, $playerId) {
        try {
            $query = "SELECT UserName FROM PlayerCredentials WHERE PlayerID = ? LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$playerId]);
            $result = $stmt->fetch();
            return $result ? $result['UserName'] : "Guest_" . $playerId;
        } catch (Exception $e) {
            return "Player_" . $playerId;
        }
    }
    
    private function getPlayerTypeSafely($pdo, $playerId) {
        try {
            $query = "SELECT PlayerID FROM RegisteredPlayer WHERE PlayerID = ? LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$playerId]);
            return $stmt->fetch() ? 'Registered' : 'Guest';
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
    
    private function getSimpleDefaultWinRateRanking() {
        return [
            [
                'PlayerName' => 'Player rankings',
                'PlayerType' => 'Registered',
                'TotalRaces' => 'temporarily',
                'Wins' => 'unavailable due to',
                'WinRate' => 'server limitations'
            ]
        ];
    }
    
    private function getRaceParticipationDistribution($pdo) {
        try {
            $timeCondition = "";
            if ($this->timeFilter !== 'all') {
                $timeCondition = "AND r.RaceDate >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END";
            }
            
            $participationCounts = [];
            
            // Count players with 0 races
            $totalPlayersQuery = "SELECT COUNT(*) as total FROM Player LIMIT 1";
            $stmt = $pdo->prepare($totalPlayersQuery);
            $stmt->execute();
            $totalPlayers = $stmt->fetch()['total'] ?? 0;
            
            $activePlayersQuery = "
                SELECT COUNT(DISTINCT pt.PlayerID) as active
                FROM Participation pt
                INNER JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 {$timeCondition}
                LIMIT 1
            ";
            $stmt = $pdo->prepare($activePlayersQuery);
            $stmt->execute();
            $activePlayers = $stmt->fetch()['active'] ?? 0;
            
            $noRacePlayers = $totalPlayers - $activePlayers;
            if ($noRacePlayers > 0) {
                $participationCounts[] = ['ParticipationRange' => 'No Races', 'PlayerCount' => $noRacePlayers];
            }
            
            // Get distribution for active players
            $query = "
                SELECT 
                    CASE 
                        WHEN race_count BETWEEN 1 AND 5 THEN '1-5 Races'
                        WHEN race_count BETWEEN 6 AND 15 THEN '6-15 Races'
                        WHEN race_count BETWEEN 16 AND 30 THEN '16-30 Races'
                        WHEN race_count BETWEEN 31 AND 50 THEN '31-50 Races'
                        ELSE '50+ Races'
                    END as ParticipationRange,
                    COUNT(*) as PlayerCount
                FROM (
                    SELECT pt.PlayerID, COUNT(pt.RaceID) as race_count
                    FROM Participation pt
                    INNER JOIN Race r ON pt.RaceID = r.RaceID
                    WHERE 1=1 {$timeCondition}
                    GROUP BY pt.PlayerID
                    LIMIT 1000
                ) player_participation
                GROUP BY ParticipationRange
                LIMIT 5
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $activeDistribution = $stmt->fetchAll();
            
            return array_merge($participationCounts, $activeDistribution);
            
        } catch (Exception $e) {
            return [['ParticipationRange' => 'Data unavailable', 'PlayerCount' => 0]];
        }
    }
    
    // Helper methods
    private function executeSimpleCount($pdo, $query) {
        try {
            $stmt = $pdo->prepare($query . " LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? $result['count'] : 0;
        } catch (Exception $e) {
            return 0;
        }
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
            'race_participation_distribution' => [],
            'message' => 'No data available or query limits exceeded'
        ];
    }
    
    // Cache methods
    private function getCacheKey() {
        return "player_stats_{$this->timeFilter}_{$this->playerTypeFilter}";
    }
    
    private function getFromCache($key) {
        if (!$this->cacheEnabled) return null;
        
        $cacheFile = sys_get_temp_dir() . '/' . md5($key) . '.cache';
        
        if (file_exists($cacheFile)) {
            $data = unserialize(file_get_contents($cacheFile));
            if ($data && isset($data['timestamp']) && 
                (time() - $data['timestamp']) < $this->cacheTime) {
                return $data['data'];
            }
            @unlink($cacheFile);
        }
        
        return null;
    }
    
    private function storeInCache($key, $data) {
        if (!$this->cacheEnabled) return;
        
        $cacheFile = sys_get_temp_dir() . '/' . md5($key) . '.cache';
        $cacheData = [
            'timestamp' => time(),
            'data' => $data
        ];
        
        @file_put_contents($cacheFile, serialize($cacheData));
    }
    
    // Optimization methods
    private function optimizeForInfinityFree($pdo) {
        try {
            $pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
            $pdo->exec("SET SESSION optimizer_search_depth = 0");
            $pdo->exec("SET SESSION max_join_size = 100000");
            $pdo->exec("SET SESSION max_execution_time = 10");
            return true;
        } catch (Exception $e) {
            error_log("InfinityFree optimization failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function isInfinityFreeError($errorMessage) {
        $infinityFreeErrors = [
            'MAX_JOIN_SIZE',
            '1104',
            'query exceeded',
            'resource limit',
            'connection timeout',
            'too many connections',
            'exceeded maximum execution time'
        ];
        
        foreach ($infinityFreeErrors as $error) {
            if (strpos(strtolower($errorMessage), strtolower($error)) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private function getFallbackPlayerStatsData() {
        return [
            'total_players' => 'Limited',
            'active_players' => 'Limited', 
            'avg_races_per_player' => 'Limited',
            'active_rate_recent_week' => 0,
            'player_distribution' => [['PlayerType' => 'Data Limited', 'PlayerCount' => 0]],
            'win_rate_ranking' => [
                [
                    'PlayerName' => 'Player rankings',
                    'PlayerType' => 'temporarily',
                    'TotalRaces' => 'unavailable due to',
                    'Wins' => 'server',
                    'WinRate' => 'limitations'
                ]
            ],
            'race_participation_distribution' => [],
            'message' => 'Some statistics are limited due to hosting restrictions. Please check back later.'
        ];
    }
    
    // Getters
    public function getTimeFilter() { return $this->timeFilter; }
    public function getPlayerTypeFilter() { return $this->playerTypeFilter; }
    
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
}
