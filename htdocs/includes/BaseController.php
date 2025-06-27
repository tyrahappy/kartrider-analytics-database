<?php
/**
 * Base Controller - Common functionality for all controllers
 * 
 * This class provides common functionality that can be shared
 * across all controllers in the application.
 */

require_once __DIR__ . '/DatabaseService.php';

class BaseController {
    protected $db;
    protected $pageTitle = 'KartRider Analytics';
    protected $errorMessage = null;
    protected $successMessage = null;
    
    public function __construct() {
        $this->db = DatabaseService::getInstance();
        $this->initializeErrorReporting();
    }
    
    /**
     * Initialize error reporting
     */
    private function initializeErrorReporting() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
    
    /**
     * Check if database is connected
     */
    protected function checkDatabaseConnection() {
        return $this->db->isConnected();
    }
    
    /**
     * Set page title
     */
    protected function setPageTitle($title) {
        $this->pageTitle = $title;
    }
    
    /**
     * Set error message
     */
    protected function setError($message) {
        $this->errorMessage = $message;
    }
    
    /**
     * Set success message
     */
    protected function setSuccess($message) {
        $this->successMessage = $message;
    }
    
    /**
     * Render a view with data
     */
    protected function renderView($viewFile, $data = []) {
        // Add common data
        $data['pageTitle'] = $this->pageTitle;
        $data['errorMessage'] = $this->errorMessage;
        $data['successMessage'] = $this->successMessage;
        $data['controller'] = $this;
        
        // Extract data for view
        extract($data);
        
        // Include view file
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View file not found: $viewFile");
        }
    }
    
    /**
     * Render error page
     */
    protected function renderError($message, $viewFile = 'views/error.php') {
        $this->setError($message);
        $this->renderView($viewFile);
    }
    
    /**
     * Redirect to another page
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Validate CSRF token (for future security enhancement)
     */
    protected function validateCSRF($token) {
        // TODO: Implement CSRF protection
        return true;
    }
    
    /**
     * Sanitize input data
     */
    protected function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email format
     */
    protected function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Generate navigation links
     */
    public function getNavigationLinks() {
        return [
            'index.php' => 'Table Viewer',
            'queries.php' => 'Dynamic Queries',
            'profile.php' => 'Profile Management',
            'dashboard.php' => 'Dashboard'
        ];
    }
    
    /**
     * Get current page name
     */
    protected function getCurrentPage() {
        return basename($_SERVER['PHP_SELF']);
    }
    
    /**
     * Check if current page is active
     */
    public function isActivePage($page) {
        return $this->getCurrentPage() === $page;
    }
}
?>
