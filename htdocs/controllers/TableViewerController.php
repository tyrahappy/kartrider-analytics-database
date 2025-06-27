<?php
/**
 * Table Viewer Controller
 */

require_once __DIR__ . '/../includes/BaseController.php';

class TableViewerController extends BaseController {
    
    private $tables;
    private $selectedTable;
    private $sortColumn;
    private $sortDirection;
    private $searchTerm;
    
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('Table Viewer - KartRider Analytics');
        $this->initializeTables();
        $this->initializeParameters();
    }
    
    private function initializeTables() {
        $this->tables = [
            // Core entities
            'Player' => ['Players', 0, true],
            'RegisteredPlayer' => ['Registered Players', 0, true],
            'GuestPlayer' => ['Guest Players', 0, true],
            
            // Kart hierarchy
            'Kart' => ['Karts', 0, true],
            'KartDetails' => ['Kart Details', 0, true],
            'SpeedKart' => ['Speed Karts', 0, true],
            'ItemKart' => ['Item Karts', 0, true],
            
            // Track and race data
            'Track' => ['Tracks', 0, true],
            'Race' => ['Races', 0, true],
            'Participation' => ['Race Participation', 0, true],
            'LapRecord' => ['Lap Records', 0, true],
            
            // Achievement system
            'Achievement' => ['Achievements', 0, true],
            'PlayerAchievement' => ['Player Achievements', 0, true]
        ];
    }
    
    private function initializeParameters() {
        $this->selectedTable = $_GET['table'] ?? 'Player';
        $this->sortColumn = $_GET['sort'] ?? '';
        $this->sortDirection = ($_GET['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
        $this->searchTerm = trim($_GET['search'] ?? '');
    }
    
    public function run() {
        try {
            if (!$this->checkDatabaseConnection()) {
                $this->setError("Database connection failed. Please check your configuration.");
            }
            
            $tableData = $this->getTableData();
            
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'selectedTable' => $this->selectedTable,
                'searchTerm' => $this->searchTerm,
                'tableData' => $tableData,
                'controller' => $this
            ]);
            
        } catch (Exception $e) {
            $this->setError("Application Error: " . $e->getMessage());
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'selectedTable' => $this->selectedTable,
                'searchTerm' => $this->searchTerm,
                'tableData' => null,
                'controller' => $this
            ]);
        }
    }
    
    public function getVisibleTables() {
        $visibleTables = [];
        foreach ($this->tables as $tableName => $tableInfo) {
            if ($tableInfo[2]) {
                $visibleTables[$tableName] = [
                    'name' => $tableInfo[0],
                    'level' => $tableInfo[1]
                ];
            }
        }
        return $visibleTables;
    }
    
    public function getSortUrl($column) {
        $newDirection = ($this->sortColumn === $column && $this->sortDirection === 'ASC') ? 'desc' : 'asc';
        $url = "?table=" . urlencode($this->selectedTable) . "&sort=" . urlencode($column) . "&dir=" . $newDirection;
        
        if (!empty($this->searchTerm)) {
            $url .= "&search=" . urlencode($this->searchTerm);
        }
        
        return $url;
    }
    
    public function getSortIndicator($column) {
        if ($this->sortColumn === $column) {
            return $this->sortDirection === 'ASC' ? ' ↑' : ' ↓';
        }
        return '';
    }
    
    private function getTableData() {
        if (!$this->db->isConnected()) {
            return [];
        }
        
        try {
            $pdo = $this->db->getConnection();
            
            // Check if table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE '{$this->selectedTable}'")->rowCount() > 0;
            
            if (!$tableExists) {
                return [];
            }
            
            // Get data with simple query
            $query = "SELECT * FROM {$this->selectedTable}";
            $params = [];
            
            if (!empty($this->searchTerm)) {
                // Simple search 
                $columns = $pdo->query("SHOW COLUMNS FROM {$this->selectedTable}")->fetchAll(PDO::FETCH_COLUMN);
                $searchConditions = [];
                foreach ($columns as $column) {
                    $searchConditions[] = "$column LIKE ?";
                    $params[] = '%' . $this->searchTerm . '%';
                }
                $query .= " WHERE " . implode(' OR ', $searchConditions);
            }
            
            if (!empty($this->sortColumn)) {
                $query .= " ORDER BY {$this->sortColumn} {$this->sortDirection}";
            }
            
            $query .= " LIMIT 100"; // Limit for performance
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $this->setError("Database Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
