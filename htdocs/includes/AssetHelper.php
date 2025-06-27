<?php
/**
 * Asset Helper Functions
 * 
 * This file provides helper functions for generating correct asset URLs
 * across different hosting environments and directory structures.
 */

/**
 * Get the base URL for the application
 */
function getBaseUrl() {
    static $baseUrl = null;
    
    if ($baseUrl === null) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        
        // Get the directory path of the current script
        $scriptPath = $_SERVER['SCRIPT_NAME'];
        $basePath = rtrim(dirname($scriptPath), '/');
        
        // Handle cases where the app is in the root directory
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        // Special handling for localhost/XAMPP
        if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false) {
            // For localhost, ensure we include the project directory
            if (strpos($scriptPath, '/PhpLab/') !== false) {
                $basePath = '/PhpLab';
            }
        }
        
        $baseUrl = $protocol . $host . $basePath;
    }
    
    return $baseUrl;
}

/**
 * Generate asset URL
 */
function asset($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    return getBaseUrl() . '/' . $path;
}

/**
 * Generate CSS link tag
 */
function css($path) {
    return '<link rel="stylesheet" href="' . htmlspecialchars(asset($path)) . '">';
}

/**
 * Generate JS script tag
 */
function js($path) {
    return '<script src="' . htmlspecialchars(asset($path)) . '"></script>';
}

/**
 * Generate image URL
 */
function img($path) {
    return asset($path);
}

/**
 * Check if a file exists in the assets directory
 */
function assetExists($path) {
    $fullPath = __DIR__ . '/' . ltrim($path, '/');
    return file_exists($fullPath);
}

/**
 * Get asset with version parameter for cache busting
 */
function assetVersion($path) {
    // Get the correct file path relative to the project root
    $projectRoot = dirname(__DIR__); // Go up one level from includes/
    $fullPath = $projectRoot . '/' . ltrim($path, '/');
    $version = file_exists($fullPath) ? filemtime($fullPath) : time();
    return asset($path) . '?v=' . $version;
}
?>
