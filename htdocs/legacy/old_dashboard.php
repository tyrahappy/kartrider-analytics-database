<?php
/**
 * KartRider Analytics Dashboard - Entry Point
 * 
 * This file serves as the entry point for the dashboard module
 * and delegates all logic to the DashboardController following MVC pattern.
 */

// Include the MVC compliant controller
require_once 'controllers/DashboardController.php';

// Run the dashboard controller
    
    /**
     * Parse and validate request parameters
     */
    private function parseParameters() {
        $this->selectedModule = isset($_GET['module']) ? $_GET['module'] : 'player_stats';
        $this->timeFilter = isset($_GET['time_filter']) ? $_GET['time_filter'] : 'all';
        $this->playerTypeFilter = isset($_GET['player_type']) ? $_GET['player_type'] : 'all';
        
        // Validate module
        $validModules = ['player_stats', 'session_analytics', 'achievements', 'race_performance'];
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
    
    /**
     * Main execution method
     */
    public function run() {
        // Check database connection
        if (!$this->dataService->isConnected()) {
            $this->renderError('Database connection failed. Please check the connection settings.');
            return;
        }
        
        // Get dashboard data
        $dashboardData = $this->getDashboardData();
        
        // Prepare view data
        $viewData = [
            'controller' => $this,
            'selectedModule' => $this->selectedModule,
            'timeFilter' => $this->timeFilter,
            'playerTypeFilter' => $this->playerTypeFilter,
            'dashboardData' => $dashboardData,
            'pageTitle' => $this->getPageTitle(),
            'filterInfo' => $this->getFilterInfo()
        ];
        
        // Render view
        $this->renderView($viewData);
    }
    
    /**
     * Get dashboard data based on selected module
     */
    private function getDashboardData() {
        try {
            switch ($this->selectedModule) {
                case 'player_stats':
                    return $this->dataService->getPlayerStatistics($this->timeFilter, $this->playerTypeFilter);
                case 'session_analytics':
                    return $this->dataService->getSessionAnalytics($this->timeFilter, $this->playerTypeFilter);
                case 'achievements':
                    return $this->dataService->getAchievementData($this->timeFilter, $this->playerTypeFilter);
                case 'race_performance':
                    return $this->dataService->getRacePerformance($this->timeFilter, $this->playerTypeFilter);
                default:
                    return null;
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get page title based on selected module
     */
    private function getPageTitle() {
        $titles = [
            'player_stats' => '📊 Player Statistics',
            'session_analytics' => '🏁 Session Analytics',
            'achievements' => '🏆 Achievement Analysis',
            'race_performance' => '🚗 Race Performance'
        ];
        
        return $titles[$this->selectedModule] ?? 'Dashboard';
    }
    
    /**
     * Get filter information text
     */
    private function getFilterInfo() {
        $info = 'Showing data for: <strong>' . ucfirst(str_replace('_', ' ', $this->playerTypeFilter)) . ' players</strong>';
        
        if ($this->timeFilter !== 'all') {
            $timeText = ucfirst(str_replace('days', ' days', str_replace('months', ' months', $this->timeFilter)));
            $info .= ' | Time period: <strong>' . $timeText . '</strong>';
        }
        
        return $info;
    }
    
    /**
     * Render the dashboard view
     */
    private function renderView($viewData) {
        // Extract variables for view
        extract($viewData);
        
        // Include the view template
        include 'dashboard_view.php';
    }
    
    /**
     * Render error page
     */
    private function renderError($message) {
        $viewData = [
            'controller' => $this,
            'selectedModule' => $this->selectedModule,
            'timeFilter' => $this->timeFilter,
            'playerTypeFilter' => $this->playerTypeFilter,
            'dashboardData' => null,
            'pageTitle' => 'Dashboard Error',
            'filterInfo' => '',
            'errorMessage' => $message
        ];
        
        $this->renderView($viewData);
    }
    
    /**
     * Get modules configuration for navigation
     */
    public function getModulesConfig() {
        return [
            'player_stats' => [
                'name' => '📊 Player Statistics',
                'icon' => '📊'
            ],
            'session_analytics' => [
                'name' => '🏁 Session Analytics',
                'icon' => '🏁'
            ],
            'achievements' => [
                'name' => '🏆 Achievements',
                'icon' => '🏆'
            ],
            'race_performance' => [
                'name' => '🚗 Race Performance',
                'icon' => '🚗'
            ]
        ];
    }
    
    /**
     * Get time filter options
     */
    public function getTimeFilterOptions() {
        return [
            'all' => 'All Time',
            '7days' => 'Last 7 Days',
            '30days' => 'Last 30 Days',
            '3months' => 'Last 3 Months'
        ];
    }
    
    /**
     * Get player type filter options
     */
    public function getPlayerTypeOptions() {
        return [
            'all' => 'All Players',
            'registered' => 'Registered Only',
            'guest' => 'Guest Only'
        ];
    }
    
    // Getters for view access
    public function getSelectedModule() { return $this->selectedModule; }
    public function getTimeFilter() { return $this->timeFilter; }
    public function getPlayerTypeFilter() { return $this->playerTypeFilter; }
}

// ================================
// EXECUTION
// ================================

// Create and run controller
$controller = new DashboardController();
$controller->run();
?>
