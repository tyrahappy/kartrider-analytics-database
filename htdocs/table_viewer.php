<?php
/**
 * Table Viewer - Entry Point
 * 
 * This file serves as the entry point for the table viewer module
 * and delegates all logic to the TableViewerController following MVC pattern.
 */

// Include the MVC compliant controller
require_once 'controllers/TableViewerController.php';

// Run the table viewer controller
$controller = new TableViewerController();
$controller->run();
?> 