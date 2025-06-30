<?php
/**
 * Database Service - Shared database connection and common operations
 * 
 * This class provides a centralized database connection and common operations,
 * which can be shared by all modules to simplify database operations.
 */

class DatabaseService {
    // Static instance for Singleton pattern, ensures only one DB connection globally
    private static $instance = null;
    // PDO object, actual database connection handle
    private $pdo = null;
    // Flag to indicate if the database is connected
    private $isConnected = false;
    
    /**
     * Private constructor. Loads DB config and establishes connection.
     */
    private function __construct() {
        // Include config if not already loaded
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/../config.php';
        }
        $this->connect(); // Establish DB connection
    }
    
    /**
     * Get singleton instance
     * @return DatabaseService
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseService();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection using PDO and set parameters
     */
    private function connect() {
        try {
            // Build DSN (Data Source Name)
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Default fetch as associative array
                PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" // Set charset
            ];
            
            // Create PDO object and connect to DB
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Test connection
            $this->pdo->query("SELECT 1");
            $this->isConnected = true;
            
            // Log connection success if DEBUG_MODE is enabled
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Database connected successfully to " . DB_HOST);
            }
            
        } catch(PDOException $e) {
            // On connection failure, log error
            $this->pdo = null;
            $this->isConnected = false;
            
            // Log detailed error
            $errorMsg = "Database connection error: " . $e->getMessage();
            error_log($errorMsg);
            
            // Show generic error in production, detailed error in development
            if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
                throw new Exception('Database connection failed. Please check your configuration.');
            } else {
                throw new Exception($errorMsg);
            }
        }
    }
    
    /**
     * Check if database is connected
     * @return bool
     */
    public function isConnected() {
        return $this->isConnected;
    }
    
    /**
     * Get PDO instance
     * @return PDO|null
     */
    public function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Get PDO connection instance (alias)
     * @return PDO|null
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Execute a prepared SQL statement
     * @param string $sql SQL statement
     * @param array $params Parameters array
     * @return PDOStatement
     * @throws Exception
     */
    public function execute($sql, $params = []) {
        if (!$this->isConnected) {
            throw new Exception('Database not connected');
        }
        
        $stmt = $this->pdo->prepare($sql); // Prepare statement
        $stmt->execute($params); // Execute
        return $stmt;
    }
    
    /**
     * Fetch all results from a query
     * @param string $sql SQL statement
     * @param array $params Parameters array
     * @return array Result array
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Fetch single result from a query
     * @param string $sql SQL statement
     * @param array $params Parameters array
     * @return array|false Single result or false
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Begin transaction
     * @return bool
     */
    public function beginTransaction() {
        if ($this->isConnected) {
            return $this->pdo->beginTransaction();
        }
        return false;
    }
    
    /**
     * Commit transaction
     * @return bool
     */
    public function commit() {
        if ($this->isConnected) {
            return $this->pdo->commit();
        }
        return false;
    }
    
    /**
     * Rollback transaction
     * @return bool
     */
    public function rollback() {
        if ($this->isConnected) {
            return $this->pdo->rollback();
        }
        return false;
    }
    
    /**
     * Get last inserted auto-increment ID
     * @return string|false
     */
    public function lastInsertId() {
        if ($this->isConnected) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }
    
    /**
     * Get database connection config info (for debugging)
     * @return array
     */
    public function getConnectionError() {
        return [
            'host' => DB_HOST, // Database host
            'database' => DB_NAME, // Database name
            'user' => DB_USER // Username
        ];
    }
}
?>
