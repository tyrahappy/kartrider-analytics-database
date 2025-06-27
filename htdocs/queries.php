<?php
/**
 * Dynamic Queries - Entry Point
 * 
 * This file serves as the entry point for the dynamic queries module
 * and delegates all logic to the QueriesController following MVC pattern.
 */

// Include the MVC compliant controller
require_once 'controllers/QueriesController.php';

// Run the queries controller
$controller = new QueriesController();
$controller->run();
?>
