<?php
/**
 * Queries Controller
 */

require_once __DIR__ . '/../includes/BaseController.php';

class QueriesController extends BaseController {
    
    private $selectedQuery;
    private $queryResult;
    private $queryError;
    private $executedQuery;
    
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('Dynamic Queries - KartRider Analytics');
        $this->selectedQuery = $_GET['query'] ?? 'join_query';
        $this->queryResult = null;
        $this->queryError = null;
        $this->executedQuery = '';
    }
    
    public function run() {
        try {
            if (!$this->checkDatabaseConnection()) {
                $this->setError("Database connection failed. Please check your configuration.");
            }
            
            // Handle POST operations
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->executeQuery();
            }
            
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'selectedQuery' => $this->selectedQuery,
                'queryResult' => $this->queryResult,
                'queryError' => $this->queryError,
                'executedQuery' => $this->executedQuery,
                'controller' => $this
            ]);
            
        } catch (Exception $e) {
            $this->setError("Application Error: " . $e->getMessage());
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'selectedQuery' => $this->selectedQuery,
                'controller' => $this
            ]);
        }
    }
    
    /**
     * Execute the selected query
     */
    private function executeQuery() {
        $queryType = $_POST['query_type'] ?? '';
        $this->selectedQuery = $queryType;
        
        try {
            $pdo = $this->db->getConnection();
            
            switch ($queryType) {
                case 'join_query':
                    $this->executeJoinQuery($pdo);
                    break;
                case 'aggregation_query':
                    $this->executeAggregationQuery($pdo);
                    break;
                case 'nested_aggregation':
                    $this->executeNestedAggregation($pdo);
                    break;
                case 'filtering_ranking':
                    $this->executeFilteringRanking($pdo);
                    break;
                case 'advanced_analysis':
                    $this->executeAdvancedAnalysis($pdo);
                    break;
                default:
                    throw new Exception("Invalid query type.");
            }
            
        } catch (Exception $e) {
            $this->queryError = $e->getMessage();
        }
    }
    
    /**
     * Execute join query
     */
    private function executeJoinQuery($pdo) {
        $examples = $this->getQueryExamples();
        $sql = $examples['join_query']['example'];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = "Top Players and Their Achievements (Join Query)";
    }
    
    /**
     * Execute aggregation query
     */
    private function executeAggregationQuery($pdo) {
        $examples = $this->getQueryExamples();
        $sql = $examples['aggregation_query']['example'];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = "Player Statistics (Aggregation Query)";
    }
    
    /**
     * Execute nested aggregation query
     */
    private function executeNestedAggregation($pdo) {
        $examples = $this->getQueryExamples();
        $sql = $examples['nested_aggregation']['example'];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = "Weekly Player Analysis (Nested Aggregation)";
    }
    
    /**
     * Execute filtering and ranking query
     */
    private function executeFilteringRanking($pdo) {
        $examples = $this->getQueryExamples();
        $sql = $examples['filtering_ranking']['example'];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = "Player Rankings by Win Rate";
    }
    
    /**
     * Execute advanced analysis query
     */
    private function executeAdvancedAnalysis($pdo) {
        // 获取用户输入的自定义SQL
        $customSQL = $_POST['custom_sql'] ?? '';
        
        // 如果没有自定义SQL，使用默认示例
        if (empty($customSQL)) {
            $examples = $this->getQueryExamples();
            $customSQL = $examples['advanced_analysis']['example'];
        }
        
        // 安全检查
        $validationResult = $this->validateCustomSQL($customSQL);
        if (!$validationResult['valid']) {
            throw new Exception("SQL Validation Error: " . $validationResult['error']);
        }
        
        $stmt = $pdo->prepare($customSQL);
        $stmt->execute();
        $this->queryResult = $stmt->fetchAll();
        $this->executedQuery = "Custom Query Results";
    }
    
    /**
     * 验证自定义SQL查询的安全性
     */
    private function validateCustomSQL($sql) {
        $sql = trim($sql);
        
        // 检查是否为空
        if (empty($sql)) {
            return ['valid' => false, 'error' => 'SQL query cannot be empty'];
        }
        
        // 转换为小写进行检查
        $sqlLower = strtolower($sql);
        
        // 检查是否以SELECT开头
        if (!preg_match('/^\s*select\s/', $sqlLower)) {
            return ['valid' => false, 'error' => 'Only SELECT queries are allowed'];
        }
        
        // 禁止的关键词检查
        $forbiddenKeywords = [
            'insert', 'update', 'delete', 'drop', 'create', 'alter', 
            'truncate', 'grant', 'revoke', 'exec', 'execute', 
            'sp_', 'xp_', 'declare', 'cursor', 'procedure'
        ];
        
        foreach ($forbiddenKeywords as $keyword) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $sql)) {
                return ['valid' => false, 'error' => "Forbidden keyword detected: $keyword"];
            }
        }
        
        // 检查是否包含分号（防止多语句执行）
        if (substr_count($sql, ';') > 1 || (substr_count($sql, ';') == 1 && !preg_match('/;\s*$/', $sql))) {
            return ['valid' => false, 'error' => 'Multiple statements are not allowed'];
        }
        
        // 检查注释注入
        if (preg_match('/(\/\*|\*\/|--|\#)/', $sql)) {
            return ['valid' => false, 'error' => 'SQL comments are not allowed'];
        }
        
        return ['valid' => true, 'error' => ''];
    }
    
    public function getQueryTypes() {
        return [
            'join_query' => 'Join Query',
            'aggregation_query' => 'Aggregation Query',
            'nested_aggregation' => 'Nested Aggregation + Group-By',
            'filtering_ranking' => 'Filtering & Ranking Query',
            'advanced_analysis' => 'Custom Query'
        ];
    }
    
    public function getQueryExamples() {
        return [
            'join_query' => [
                'name' => 'Join Query - Top Players and Their Achievements',
                'description' => 'Display top players and their unlocked achievements by joining the Players, Achievements, and Sessions tables.',
                'example' => 'SELECT 
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
LIMIT 10;'
            ],
            'aggregation_query' => [
                'name' => 'Aggregation Query - Average Playtime and Achievement Statistics',
                'description' => 'Compute average playtime per player and total achievements per game.',
                'example' => 'SELECT 
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
ORDER BY TotalWins DESC, AvgRaceTime ASC;'
            ],
            'nested_aggregation' => [
                'name' => 'Nested Aggregation + Group-By - Weekly Playtime Analysis',
                'description' => 'Find total playtime per week grouped by player for the last 30 days.',
                'example' => 'SELECT 
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
WHERE r.RaceDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY part.PlayerID, pc.UserName, WEEK(r.RaceDate, 1)
HAVING COUNT(*) >= 2
ORDER BY TotalWeeklyTime DESC, AvgWeeklyTime DESC;'
            ],
            'filtering_ranking' => [
                'name' => 'Filtering & Ranking Query - Top 5 Players',
                'description' => 'Display the top 5 players with the highest scores dynamically.',
                'example' => 'SELECT 
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
LIMIT 5;'
            ],
            'advanced_analysis' => [
                'name' => 'Custom Query - Write Your Own SQL',
                'description' => 'Enter your own SELECT query. ',
                'example' => 'SELECT t.TrackName,
       COUNT(r.RaceID) as TotalRaces,
       ROUND(AVG(part.TotalTime), 2) as AvgRaceTime,
       MIN(part.TotalTime) as BestTime,
       COUNT(DISTINCT part.PlayerID) as UniqueRacers
FROM Track t
JOIN Race r ON t.TrackID = r.TrackID
JOIN Participation part ON r.RaceID = part.RaceID
GROUP BY t.TrackID, t.TrackName
ORDER BY TotalRaces DESC'
            ]
        ];
    }
}
?>
