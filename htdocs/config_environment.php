<?php
/**
 * Environment Configuration Manager
 * This file helps manage different configurations for local development and production
 */

// Detect environment based on server characteristics
function detectEnvironment() {
    // Check if we're running from command line
    if (php_sapi_name() === 'cli') {
        // For CLI, check if we can connect to localhost
        return 'development';
    }
    
    // Check if HTTP_HOST is available
    if (!isset($_SERVER['HTTP_HOST'])) {
        // Default to development if no HTTP_HOST
        return 'development';
    }
    
    // Check if we're on InfinityFree or similar hosting
    if (strpos($_SERVER['HTTP_HOST'], '.atspace.cc') !== false || 
        strpos($_SERVER['HTTP_HOST'], '.rf.gd') !== false ||
        strpos($_SERVER['HTTP_HOST'], '.byethost') !== false ||
        strpos($_SERVER['HTTP_HOST'], '.infinityfree') !== false ||
        strpos($_SERVER['HTTP_HOST'], '.ezyro.com') !== false) {
        return 'production';
    }
    
    // Check for localhost or local development
    if ($_SERVER['HTTP_HOST'] === 'localhost' || 
        strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
        strpos($_SERVER['HTTP_HOST'], '.local') !== false) {
        return 'development';
    }
    
    // Default to production for safety
    return 'production';
}

$environment = detectEnvironment();

if ($environment === 'production') {
    // Production configuration (InfinityFree)
    define('DB_HOST', 'sql106.infinityfree.com');
    define('DB_USER', 'if0_39323420');
    define('DB_PASS', 'sRuS8CQFKqrMn6');
    define('DB_NAME', 'if0_39323420_KartRiderAnalytics');
    define('DEBUG_MODE', false);
    define('ENVIRONMENT', 'production');
} else {
    // Development configuration (Local XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('DB_NAME', 'KartRiderAnalytics');
    define('DEBUG_MODE', true);
    define('ENVIRONMENT', 'development');
}

// Common configuration
define('SITE_NAME', 'KartRider Analytics Dashboard');

// Timezone
date_default_timezone_set('America/New_York');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Error reporting based on environment
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', 'error.log');
}

// Security headers for production
if (ENVIRONMENT === 'production') {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    // XSS protection
    header('X-XSS-Protection: 1; mode=block');
}
?>
