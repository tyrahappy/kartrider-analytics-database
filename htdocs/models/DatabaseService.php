<?php
/**
 * Database Service - Shared database connection and common operations
 * 
 * This class provides a centralized database connection and common
 * database operations that can be shared across all modules.
 */

class DatabaseService {
    private static $instance = null;
    private $pdo = null;
    private $isConnected = false;
    
    private function __construct() {
        // Include config if not already loaded
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/../config.php';
        }
        $this->connect();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseService();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Test connection
            $this->pdo->query("SELECT 1");
            $this->isConnected = true;
            
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Database connected successfully to " . DB_HOST);
            }
            
        } catch(PDOException $e) {
            $this->pdo = null;
            $this->isConnected = false;
            
            // Log error details for debugging
            $errorMsg = "Database connection error: " . $e->getMessage();
            error_log($errorMsg);
            
            // In production, show generic error
            if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
                throw new Exception('Database connection failed. Please check your configuration.');
            } else {
                throw new Exception($errorMsg);
            }
        }
    }
    
    /**
     * Check if database is connected
     */
    public function isConnected() {
        return $this->isConnected;
    }
    
    /**
     * Get PDO instance
     */
    public function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Get PDO connection instance (alias for getPDO)
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Execute a prepared statement
     */
    public function execute($sql, $params = []) {
        if (!$this->isConnected) {
            throw new Exception('Database not connected');
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Fetch all results from a query
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Fetch single result from a query
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        if ($this->isConnected) {
            return $this->pdo->beginTransaction();
        }
        return false;
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        if ($this->isConnected) {
            return $this->pdo->commit();
        }
        return false;
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        if ($this->isConnected) {
            return $this->pdo->rollback();
        }
        return false;
    }
    
    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        if ($this->isConnected) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }
    
    /**
     * Get database connection error information for debugging
     */
    public function getConnectionError() {
        return [
            'host' => DB_HOST,
            'database' => DB_NAME,
            'user' => DB_USER
        ];
    }
}
?>
