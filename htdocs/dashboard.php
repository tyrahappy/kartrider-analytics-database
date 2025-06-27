<?php
/**
 * Dashboard - Entry Point
 * 
 * This file serves as the entry point for the dashboard module
 * and delegates all logic to the DashboardController following MVC pattern.
 */

// Include the MVC compliant controller
require_once 'controllers/DashboardController.php';

// Run the dashboard controller
$controller = new DashboardController();
$controller->run();
?>
