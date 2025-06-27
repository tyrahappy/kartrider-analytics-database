<?php
/**
 * Dashboard Controller - Main Controller
 */

require_once __DIR__ . '/../includes/BaseController.php';
require_once __DIR__ . '/dashboard/PlayerStatsDashboardController.php';
require_once __DIR__ . '/dashboard/SessionAnalyticsController.php';
require_once __DIR__ . '/dashboard/AchievementDashboardController.php';

class DashboardController extends BaseController {
    
    private $selectedModule;
    private $timeFilter;
    private $playerTypeFilter;
    private $cacheEnabled = true; // Enable simple caching
    private $cacheTime = 300; // 5 minutes cache
    
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
            
            // Try to enable query cache for InfinityFree optimization
            try {
                $pdo->exec("SET SESSION query_cache_type = ON");
                $pdo->exec("SET SESSION query_cache_size = 1048576"); // 1MB
            } catch (Exception $e) {
                // Ignore if not supported
            }
            
            // Check cache first
            $cacheKey = $this->getCacheKey();
            $cachedData = $this->getFromCache($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
            
            // Optimize for InfinityFree if applicable
            $this->optimizeForInfinityFree($pdo);
            
            // Generate fresh data using appropriate controller
            $data = $this->generateFreshData($pdo);
            
            // Store in cache
            $this->storeInCache($cacheKey, $data);
            
            return $data;
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    private function generateFreshData($pdo) {
        // Try to optimize for InfinityFree first
        $this->optimizeForInfinityFree($pdo);
        
        try {
            // Set a reasonable timeout for the entire operation
            set_time_limit(30);
            
            switch ($this->selectedModule) {
                case 'player_stats':
                    $controller = new PlayerStatsDashboardController($this->timeFilter, $this->playerTypeFilter);
                    return $controller->getPlayerStatistics($pdo);
                case 'session_analytics':
                    $controller = new SessionAnalyticsController($this->timeFilter, $this->playerTypeFilter);
                    return $controller->getSessionAnalytics($pdo);
                case 'achievements':
                    $controller = new AchievementDashboardController($this->timeFilter, $this->playerTypeFilter);
                    return $controller->getAchievementData($pdo);
                default:
                    $controller = new PlayerStatsDashboardController($this->timeFilter, $this->playerTypeFilter);
                    return $controller->getPlayerStatistics($pdo);
            }
        } catch (Exception $e) {
            // Enhanced error detection for InfinityFree
            if ($this->isInfinityFreeError($e->getMessage())) {
                error_log("InfinityFree limits exceeded for module: " . $this->selectedModule);
                error_log("Error details: " . $e->getMessage());
                return $this->getFallbackData($this->selectedModule);
            }
            
            // For other errors, return empty data with error message
            error_log("General dashboard error: " . $e->getMessage());
            $emptyData = $this->getEmptyData();
            $emptyData['error'] = 'Data temporarily unavailable due to server limitations';
            $emptyData['debug_info'] = defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : '';
            return $emptyData;
        }
    }
    
    private function getCacheKey() {
        return "dashboard_{$this->selectedModule}_{$this->timeFilter}_{$this->playerTypeFilter}";
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
            // Cache expired, delete file
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
    
    // Add method to handle InfinityFree specific optimizations
    private function optimizeForInfinityFree($pdo) {
        try {
            // Set ultra-conservative settings for InfinityFree
            $pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
            $pdo->exec("SET SESSION optimizer_search_depth = 0");
            $pdo->exec("SET SESSION max_join_size = 100000"); // Very conservative
            $pdo->exec("SET SESSION max_execution_time = 10"); // 10 seconds max
            
            return true;
        } catch (Exception $e) {
            // If we can't set these, we're probably on a restricted host
            error_log("InfinityFree optimization failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Enhanced error detection for InfinityFree
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
    
    // Fallback method for when complex queries fail
    public function getFallbackData($module) {
        switch ($module) {
            case 'player_stats':
                $controller = new PlayerStatsDashboardController($this->timeFilter, $this->playerTypeFilter);
                return $controller->getFallbackData();
            case 'session_analytics':
                $controller = new SessionAnalyticsController($this->timeFilter, $this->playerTypeFilter);
                return $controller->getFallbackData();
            case 'achievements':
                $controller = new AchievementDashboardController($this->timeFilter, $this->playerTypeFilter);
                return $controller->getFallbackData();
            default:
                return $this->getEmptyData();
        }
    }
    
    private function getEmptyData() {
        return [
            'message' => 'No data available or query limits exceeded'
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
