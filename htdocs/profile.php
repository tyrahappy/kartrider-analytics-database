<?php
/**
 * Profile Management - Entry Point
 * 
 * This file serves as the entry point for the profile management module
 * and delegates all logic to the ProfileController following MVC pattern.
 */

// Include the MVC compliant controller
require_once 'controllers/ProfileController.php';

// Run the profile controller
$controller = new ProfileController();
$controller->run();
?>
