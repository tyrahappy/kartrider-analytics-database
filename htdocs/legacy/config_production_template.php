<?php
/**
 * Production Configuration Template for InfinityFree
 * 
 * INSTRUCTIONS:
 * 1. Rename this file to config.php
 * 2. Replace all placeholder values with your actual InfinityFree credentials
 * 3. Upload to your InfinityFree htdocs directory
 */

// Database configuration - UPDATE THESE VALUES!
define('DB_HOST', 'YOUR_DATABASE_HOST'); // e.g., sql200.byetcluster.com
define('DB_USER', 'YOUR_DATABASE_USERNAME'); // e.g., if0_12345678
define('DB_PASS', 'YOUR_DATABASE_PASSWORD'); // Your actual password
define('DB_NAME', 'YOUR_DATABASE_NAME'); // e.g., if0_12345678_KartRiderAnalytics

// Application settings
define('SITE_NAME', 'KartRider Analytics Dashboard');
define('DEBUG_MODE', false); // NEVER set to true in production

// Timezone
date_default_timezone_set('America/New_York');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Production error handling
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set error log file path (relative to htdocs)
ini_set('error_log', dirname(__FILE__) . '/error.log');

// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Memory and execution time (within InfinityFree limits)
ini_set('memory_limit', '128M');
set_time_limit(30);
?>

<?php
/*
DEPLOYMENT CHECKLIST:

1. BEFORE UPLOADING:
   - Replace all placeholder values above with your actual InfinityFree credentials
   - Verify DEBUG_MODE is set to false
   - Test locally first

2. DATABASE SETUP:
   - Create MySQL database in InfinityFree control panel
   - Note the database host, username, password, and database name
   - Import kartrider_ddl.sql via phpMyAdmin

3. UPLOAD FILES:
   - Upload ALL files to htdocs directory via FTP
   - Maintain directory structure
   - Ensure .htaccess file is uploaded

4. POST-UPLOAD TESTING:
   - Test database connection
   - Verify all pages load correctly
   - Check for any error messages

5. SECURITY VERIFICATION:
   - Confirm sensitive files are protected
   - Test that debug information is not exposed
   - Verify error logging is working

If you encounter issues:
- Check error.log file for detailed error messages
- Verify database credentials in InfinityFree control panel
- Ensure all files were uploaded correctly
- Check InfinityFree forum for common issues
*/
?>
