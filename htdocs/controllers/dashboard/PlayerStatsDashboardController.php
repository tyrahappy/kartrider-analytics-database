<?php
/**
 * Player Statistics Dashboard Controller
 */

require_once __DIR__ . '/../../includes/BaseController.php';

class PlayerStatsDashboardController extends BaseController {
    
    private $timeFilter;
    private $playerTypeFilter;
    private $cacheEnabled = true;
    private $cacheTime = 300; // 5 minutes cache
    
    public function __construct($timeFilter = 'all', $playerTypeFilter = 'all') {
        parent::__construct();
        $this->timeFilter = $timeFilter;
        $this->playerTypeFilter = $playerTypeFilter;
    }
    
    public function getPlayerStatistics($pdo) {
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
            // For time-filtered queries, start from Race table (smaller dataset)
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
            // For all-time, use participation directly
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
            // Simplest possible approach to avoid JOIN issues
            if ($this->timeFilter !== 'all') {
                $dateFilter = $this->getDateFilterValue();
                
                // Get total participations in time range
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
                
                // Get unique players in time range
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
                // All time - use simplest queries
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
            // Get total players first
            $totalPlayers = $this->executeSimpleCount($pdo, "SELECT COUNT(*) as count FROM Player");
            
            if ($totalPlayers === 0) return 0;
            
            // Get active players in last 7 days using safest approach
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
            // First, let's check if we have any data at all
            $checkQuery = "SELECT COUNT(*) as total FROM Participation LIMIT 1";
            $stmt = $pdo->prepare($checkQuery);
            $stmt->execute();
            $totalParticipations = $stmt->fetch()['total'] ?? 0;
            
            if ($totalParticipations == 0) {
                error_log("No participation data found in database");
                return $this->getDefaultWinRateRanking();
            }
            
            // Use ultra-simple query approach for InfinityFree
            $timeCondition = "";
            if ($this->timeFilter !== 'all') {
                $timeCondition = "AND r.RaceDate >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END";
            }
            
            // Step 1: Get basic player participation data
            $basicQuery = "
                SELECT 
                    pt.PlayerID,
                    COUNT(pt.ParticipationID) as TotalRaces,
                    SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) as Wins
                FROM Participation pt
                INNER JOIN Race r ON pt.RaceID = r.RaceID
                WHERE 1=1 {$timeCondition}
                GROUP BY pt.PlayerID
                HAVING TotalRaces >= 2
                ORDER BY (SUM(CASE WHEN pt.FinishingRank = 1 THEN 1 ELSE 0 END) / COUNT(pt.ParticipationID)) DESC, TotalRaces DESC
                LIMIT 10
            ";
            
            error_log("Executing basic win rate query: " . $basicQuery);
            $stmt = $pdo->prepare($basicQuery);
            $stmt->execute();
            $basicResults = $stmt->fetchAll();
            
            if (empty($basicResults)) {
                error_log("No basic results found for win rate ranking");
                return $this->getDefaultWinRateRanking();
            }
            
            // Step 2: Get player details for top players
            $playerIds = array_column($basicResults, 'PlayerID');
            $playerIdsStr = implode(',', array_map('intval', $playerIds));
            
            $playerDetailsQuery = "
                SELECT 
                    p.PlayerID,
                    COALESCE(pc.UserName, CONCAT('Guest_', p.PlayerID)) as PlayerName,
                    CASE WHEN rp.PlayerID IS NOT NULL THEN 'Registered' ELSE 'Guest' END as PlayerType
                FROM Player p
                LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
                LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                WHERE p.PlayerID IN ({$playerIdsStr})
                LIMIT 10
            ";
            
            error_log("Executing player details query for IDs: " . $playerIdsStr);
            $stmt = $pdo->prepare($playerDetailsQuery);
            $stmt->execute();
            $playerDetails = $stmt->fetchAll();
            
            // Combine the results
            $results = [];
            foreach ($basicResults as $basic) {
                $playerDetail = null;
                foreach ($playerDetails as $detail) {
                    if ($detail['PlayerID'] == $basic['PlayerID']) {
                        $playerDetail = $detail;
                        break;
                    }
                }
                
                if ($playerDetail) {
                    $winRate = $basic['TotalRaces'] > 0 ? round(($basic['Wins'] / $basic['TotalRaces']) * 100, 1) : 0;
                    $results[] = [
                        'PlayerID' => (int)$basic['PlayerID'],
                        'PlayerName' => $playerDetail['PlayerName'],
                        'PlayerType' => $playerDetail['PlayerType'],
                        'TotalRaces' => (int)$basic['TotalRaces'],
                        'Wins' => (int)$basic['Wins'],
                        'WinRate' => (float)$winRate
                    ];
                }
            }
            
            // Sort by win rate and limit to top 5
            usort($results, function($a, $b) {
                if ($a['WinRate'] != $b['WinRate']) {
                    return $b['WinRate'] <=> $a['WinRate'];
                }
                return $b['TotalRaces'] <=> $a['TotalRaces'];
            });
            
            $results = array_slice($results, 0, 5);
            
            error_log("Win rate ranking results: " . json_encode($results));
            return !empty($results) ? $results : $this->getDefaultWinRateRanking();
            
        } catch (Exception $e) {
            error_log("Win rate ranking query failed: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->getDefaultWinRateRanking();
        }
    }
    
    private function getDefaultWinRateRanking() {
        return [
            [
                'PlayerID' => 0,
                'PlayerName' => 'No player data available',
                'PlayerType' => 'N/A',
                'TotalRaces' => 0,
                'Wins' => 0,
                'WinRate' => 0
            ]
        ];
    }
    
    private function getRaceParticipationDistribution($pdo) {
        try {
            // Use a simplified approach with direct aggregation
            $timeCondition = "";
            if ($this->timeFilter !== 'all') {
                $timeCondition = "AND r.RaceDate >= CASE 
                    WHEN '{$this->timeFilter}' = '7days' THEN DATE_SUB(NOW(), INTERVAL 7 DAY)
                    WHEN '{$this->timeFilter}' = '30days' THEN DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHEN '{$this->timeFilter}' = '3months' THEN DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    ELSE '1900-01-01'
                END";
            }
            
            // Get participation counts in manageable chunks
            $participationCounts = [];
            
            // Count players with 0 races (total players - players with races)
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
            
            // Get distribution for active players only
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
            'total_players' => 0,
            'active_players' => 0,
            'avg_races_per_player' => 0,
            'player_distribution' => [],
            'win_rate_ranking' => [],
            'race_participation_distribution' => [],
            'message' => 'No data available or query limits exceeded'
        ];
    }
    
    public function getFallbackData() {
        return [
            'total_players' => 'Limited',
            'active_players' => 'Limited', 
            'avg_races_per_player' => 'Limited',
            'active_rate_recent_week' => 0,
            'player_distribution' => [['PlayerType' => 'Data Limited', 'PlayerCount' => 0]],
            'win_rate_ranking' => [
                [
                    'PlayerName' => 'Player rankings temporarily unavailable',
                    'PlayerType' => 'Registered',
                    'TotalRaces' => 0,
                    'Wins' => 0,
                    'WinRate' => 0
                ]
            ],
            'race_participation_distribution' => [],
            'message' => 'Some statistics are limited due to hosting restrictions. Please check back later.'
        ];
    }
}
?> 