<?php
/**
 * Smart Environment Configuration
 * This configuration automatically detects the environment and switches settings accordingly
 * 
 * - Local development (XAMPP): Uses localhost database, enables debugging
 * - Production (InfinityFree): Uses production database, disables debugging, adds security
 */

// Include the intelligent environment configuration
require_once __DIR__ . '/config_environment.php';

// Additional configuration can be added here if needed
?>