<?php
/**
 * Dynamic Queries Controller
 * 
 * This controller handles dynamic query execution functionality,
 * including predefined queries, custom queries, and aggregation operations.
 */

require_once 'config.php';
require_once 'includes/BaseController.php';

class QueriesController extends BaseController {
    private $selectedQuery = 'join_query';
    private $queryResult = null;
    private $queryError = null;
    private $executedQuery = '';
    
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('Dynamic Queries - KartRider Analytics');
        $this->parseParameters();
    }
    
    /**
     * Parse request parameters
     */
    private function parseParameters() {
        $this->selectedQuery = $_GET['query'] ?? 'join_query';
    }
    
    /**
     * Main execution method
     */
    public function run() {
        if (!$this->checkDatabaseConnection()) {
            $this->renderError('Database connection failed. Please check the connection settings.');
            return;
        }
        
        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        }
        
        // Prepare view data
        $viewData = [
            'pageSubtitle' => 'Execute Dynamic Database Queries',
            'contentFile' => 'views/queries_content.php',
            'selectedQuery' => $this->selectedQuery,
            'queryResult' => $this->queryResult,
            'queryError' => $this->queryError,
            'executedQuery' => $this->executedQuery,
            'controller' => $this
        ];
        
        $this->renderView('views/layout.php', $viewData);
    }
    
    /**
     * Handle POST request operations
     */
    private function handlePostRequest() {
        $queryType = $_POST['query_type'] ?? '';
        $this->selectedQuery = $queryType; // Update selected tab
        
        try {
            switch ($queryType) {
                case 'join_query':
                    $this->executeJoinQuery();
                    break;
                    
                case 'aggregation_query':
                    $this->executeAggregationQuery();
                    break;
                    
                case 'nested_query':
                    $this->executeNestedQuery();
                    break;
                    
                case 'ranking_query':
                    $this->executeRankingQuery();
                    break;
                    
                case 'custom_query':
                    $this->executeCustomQuery();
                    break;
                    
                default:
                    throw new Exception("Unknown query type: " . htmlspecialchars($queryType));
            }
            
        } catch (Exception $e) {
            $this->queryError = $e->getMessage();
        }
    }
    
    /**
     * Execute JOIN query
     */
    private function executeJoinQuery() {
        $sql = "SELECT 
pc.UserName, 
a.AchievementName, 
a.Description, 
pa.DateEarned, 
a.PointsAwarded 
FROM Player p 
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID 
JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID 
JOIN Achievement a ON pa.AchievementID = a.AchievementID 
ORDER BY a.PointsAwarded DESC, pa.DateEarned DESC 
LIMIT 10";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute();
        
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = $sql;
    }
    
    /**
     * Execute aggregation query
     */
    private function executeAggregationQuery() {
        $sql = "SELECT 
pc.UserName, 
COUNT(part.ParticipationID) as TotalRaces, 
ROUND(AVG(part.TotalTime), 2) as AvgRaceTime, 
MIN(part.FinishingRank) as BestRank, 
COUNT(CASE WHEN part.FinishingRank = 1 THEN 1 END) as TotalWins, 
COUNT(DISTINCT pa.AchievementID) as TotalAchievements 
FROM Player p 
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID 
JOIN Participation part ON p.PlayerID = part.PlayerID 
LEFT JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID 
GROUP BY p.PlayerID, pc.UserName 
HAVING COUNT(part.ParticipationID) >= 3 
ORDER BY TotalWins DESC, AvgRaceTime ASC";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute();
        
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = $sql;
    }
    
    /**
     * Execute nested/subquery
     */
    private function executeNestedQuery() {
        $sql = "SELECT 
    pc.UserName,
    WEEK(r.RaceDate, 1) as WeekNumber,
    COUNT(*) as RacesThisWeek,
    ROUND(SUM(part.TotalTime), 2) as TotalWeeklyTime,
    ROUND(AVG(part.TotalTime), 2) as AvgWeeklyTime,
    COUNT(CASE WHEN part.FinishingRank = 1 THEN 1 END) as WeeklyWins
FROM Participation part
JOIN Player p ON part.PlayerID = p.PlayerID
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
JOIN Race r ON part.RaceID = r.RaceID
WHERE r.RaceDate >= '2025-06-17'
GROUP BY part.PlayerID, pc.UserName, WEEK(r.RaceDate, 1)
HAVING COUNT(*) >= 2
ORDER BY TotalWeeklyTime DESC, AvgWeeklyTime DESC";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute();
        
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = $sql;
    }
    
    /**
     * Execute ranking query
     */
    private function executeRankingQuery() {
        $sql = "SELECT 
    pc.UserName,
    COUNT(part.ParticipationID) AS TotalRaces,
    COUNT(CASE WHEN part.FinishingRank = 1 THEN 1 END) AS FirstPlace,
    COUNT(CASE WHEN part.FinishingRank = 2 THEN 1 END) AS SecondPlace,
    COUNT(CASE WHEN part.FinishingRank = 3 THEN 1 END) AS ThirdPlace,
    ROUND(AVG(part.TotalTime), 2) AS AvgRaceTime
FROM Player p
    JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
    JOIN Participation part ON p.PlayerID = part.PlayerID
GROUP BY 
    p.PlayerID, 
    pc.UserName
ORDER BY 
    FirstPlace DESC, 
    AvgRaceTime ASC
LIMIT 5";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute();
        
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = $sql;
    }
    
    /**
     * Execute custom query
     */
    private function executeCustomQuery() {
        $customSql = trim($_POST['custom_sql'] ?? '');
        
        if (empty($customSql)) {
            throw new Exception("Please enter a SQL query to execute.");
        }
        
        // Basic security check - only allow SELECT statements
        if (!preg_match('/^\s*SELECT\s+/i', $customSql)) {
            throw new Exception("Only SELECT queries are allowed for security reasons.");
        }
        
        // Check for potentially dangerous keywords
        $dangerousKeywords = ['DELETE', 'DROP', 'INSERT', 'UPDATE', 'CREATE', 'ALTER', 'TRUNCATE'];
        foreach ($dangerousKeywords as $keyword) {
            if (stripos($customSql, $keyword) !== false) {
                throw new Exception("Query contains restricted keyword: " . $keyword);
            }
        }
        
        $stmt = $this->db->getPdo()->prepare($customSql);
        $stmt->execute();
        
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = $customSql;
    }
    
    /**
     * Get predefined query examples
     */
    public function getQueryExamples() {
        return [
            'join_query' => [
                'name' => 'JOIN Query',
                'description' => 'Display top players and their unlocked achievements by joining the Players, Achievements, and Sessions tables',
                'example' => "SELECT 
pc.UserName, 
a.AchievementName, 
a.Description, 
pa.DateEarned, 
a.PointsAwarded 
FROM Player p 
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID 
JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID 
JOIN Achievement a ON pa.AchievementID = a.AchievementID 
ORDER BY a.PointsAwarded DESC, pa.DateEarned DESC 
LIMIT 10"
            ],
            'aggregation_query' => [
                'name' => 'Aggregation Query',
                'description' => 'Compute average playtime per player and total achievements per game',
                'example' => "SELECT 
pc.UserName, 
COUNT(part.ParticipationID) as TotalRaces, 
ROUND(AVG(part.TotalTime), 2) as AvgRaceTime, 
MIN(part.FinishingRank) as BestRank, 
COUNT(CASE WHEN part.FinishingRank = 1 THEN 1 END) as TotalWins, 
COUNT(DISTINCT pa.AchievementID) as TotalAchievements 
FROM Player p 
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID 
JOIN Participation part ON p.PlayerID = part.PlayerID 
LEFT JOIN PlayerAchievement pa ON p.PlayerID = pa.PlayerID 
GROUP BY p.PlayerID, pc.UserName 
HAVING COUNT(part.ParticipationID) >= 3 
ORDER BY TotalWins DESC, AvgRaceTime ASC"
            ],
            'nested_query' => [
                'name' => 'Nested Aggregation with Group-By',
                'description' => 'Find total playtime per week grouped by player',
                'example' => "SELECT 
    pc.UserName,
    WEEK(r.RaceDate, 1) as WeekNumber,
    COUNT(*) as RacesThisWeek,
    ROUND(SUM(part.TotalTime), 2) as TotalWeeklyTime,
    ROUND(AVG(part.TotalTime), 2) as AvgWeeklyTime,
    COUNT(CASE WHEN part.FinishingRank = 1 THEN 1 END) as WeeklyWins
FROM Participation part
JOIN Player p ON part.PlayerID = p.PlayerID
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
JOIN Race r ON part.RaceID = r.RaceID
WHERE r.RaceDate >= '2025-06-17'
GROUP BY part.PlayerID, pc.UserName, WEEK(r.RaceDate, 1)
HAVING COUNT(*) >= 2
ORDER BY TotalWeeklyTime DESC, AvgWeeklyTime DESC"
            ],
            'ranking_query' => [
                'name' => 'Filtering & Ranking Query',
                'description' => 'Display the top 5 players with the highest scores dynamically',
                'example' => "SELECT 
    pc.UserName,
    COUNT(part.ParticipationID) AS TotalRaces,
    COUNT(CASE WHEN part.FinishingRank = 1 THEN 1 END) AS FirstPlace,
    COUNT(CASE WHEN part.FinishingRank = 2 THEN 1 END) AS SecondPlace,
    COUNT(CASE WHEN part.FinishingRank = 3 THEN 1 END) AS ThirdPlace,
    ROUND(AVG(part.TotalTime), 2) AS AvgRaceTime
FROM Player p
    JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
    JOIN Participation part ON p.PlayerID = part.PlayerID
GROUP BY 
    p.PlayerID, 
    pc.UserName
ORDER BY 
    FirstPlace DESC, 
    AvgRaceTime ASC
LIMIT 5"
            ],
            'custom_query' => [
                'name' => 'Custom Query',
                'description' => 'Write your own SELECT query',
                'example' => "SELECT * FROM Player LIMIT 10"
            ]
        ];
    }
    
    /**
     * Format query result for display
     */
    public function formatQueryResult($result) {
        if (empty($result)) {
            return "<p>No results found.</p>";
        }
        
        $html = "<div class='query-results'>";
        $html .= "<p><strong>Results:</strong> " . count($result) . " rows</p>";
        $html .= "<table class='results-table'>";
        
        // Table header
        $html .= "<thead><tr>";
        foreach (array_keys($result[0]) as $column) {
            $html .= "<th>" . htmlspecialchars($column) . "</th>";
        }
        $html .= "</tr></thead>";
        
        // Table body
        $html .= "<tbody>";
        foreach ($result as $row) {
            $html .= "<tr>";
            foreach ($row as $value) {
                $displayValue = $value === null ? '<em>NULL</em>' : htmlspecialchars((string)$value);
                $html .= "<td>" . $displayValue . "</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        
        $html .= "</table>";
        $html .= "</div>";
        
        return $html;
    }
}

// Run the controller
$controller = new QueriesController();
$controller->run();
?>
