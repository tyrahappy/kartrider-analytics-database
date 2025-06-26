<?php
// Database configuration
// For InfinityFree, update these values:
// Host is usually something like 'sql200.byetcluster.com'
// Database name includes your account prefix like 'if0_12345678_KartRiderAnalytics'
// Username includes your account prefix like 'if0_12345678'
define('DB_HOST', 'localhost'); // Change to your InfinityFree database host
define('DB_USER', 'root'); // Change to your InfinityFree database username
define('DB_PASS', ''); // Change to your InfinityFree database password
define('DB_NAME', 'KartRiderAnalytics'); // Change to your InfinityFree database name

// Application settings
define('SITE_NAME', 'KartRider Analytics Dashboard');
define('DEBUG_MODE', true); // Set to false in production

// Timezone
date_default_timezone_set('America/New_York');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Error reporting (disable in production)
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>