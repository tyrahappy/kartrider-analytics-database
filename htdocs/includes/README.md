# Core Library Directory

## Overview

This directory contains the core library files for the project, providing basic functionality and common services. These files are widely used by other components in the project and form the foundation architecture of the system.

## File Structure

```
includes/
├── README.md                    # This documentation file
├── BaseController.php           # Base controller class (148 lines)
├── DatabaseService.php          # Database service class (174 lines)
└── AssetHelper.php              # Asset helper class (91 lines)
```

## Core Class Description

### Base Controller

- `BaseController.php`: Base class for all controllers
  - Provides common controller functionality
  - Handles page titles and error messages
  - Manages database connections
  - Provides view rendering methods

### Database Service

- `DatabaseService.php`: Database connection and management service
  - Database connection pool management
  - Connection status monitoring
  - Error handling and reconnection mechanisms
  - Support for multiple database types

### Asset Helper

- `AssetHelper.php`: Static asset management helper
  - CSS and JavaScript file management
  - Asset version control
  - Cache optimization
  - Asset path resolution

## Design Principles

### Single Responsibility

- Each class is responsible for only one specific function
- Avoid functional coupling
- Easy to test and maintain

### Reusability

- Provides common basic functionality
- Supports multiple usage scenarios
- Easy to extend and customize

### Stability

- Fully tested
- Backward compatible
- Comprehensive error handling

## Usage

### Base Controller

```php
class CustomController extends BaseController {
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('Page Title');
    }

    public function run() {
        // Business logic
        $this->renderView('template.php', $data);
    }
}
```

### Database Service

```php
// Get database connection
$db = new DatabaseService();
$pdo = $db->getConnection();

// Check connection status
if ($db->isConnected()) {
    // Perform database operations
}
```

### Asset Helper

```php
// Get CSS file path
$cssPath = AssetHelper::getCssPath('style.css');

// Get JavaScript file path
$jsPath = AssetHelper::getJsPath('script.js');
```

## Inheritance Hierarchy

### Controller Inheritance Chain

```
BaseController
├── DashboardController
├── PlayerStatsDashboardController
├── SessionAnalyticsController
├── AchievementDashboardController
├── PlayerStatsController
├── ProfileController
├── QueriesController
└── TableViewerController
```

### Service Dependencies

```
DatabaseService (Database connection)
    ↓
BaseController (Basic functionality)
    ↓
Specific Controllers (Business logic)
```

## Configuration

### Database Configuration

- Support for multiple database types
- Connection pool configuration
- Timeout and retry settings
- Error log recording

### Asset Configuration

- Static asset paths
- Cache strategies
- Version control
- Compression options

## Error Handling

### Exception Handling

- Unified exception types
- Detailed error messages
- Error log recording
- User-friendly error prompts

### Debug Support

- Debug mode toggle
- Detailed debug information
- Performance monitoring
- Error tracking

## Performance Optimization

### Database Optimization

- Connection pool management
- Query caching
- Connection reuse
- Timeout handling

### Asset Optimization

- File compression
- Caching mechanisms
- Lazy loading
- Version control

## Extension Guide

### Adding New Services

1. Create a new service class
2. Implement necessary interfaces
3. Add error handling
4. Update related documentation

### Modifying Existing Classes

- Maintain backward compatibility
- Add appropriate comments
- Update related tests
- Record change logs

## Development Standards

### Code Standards

- Follow PSR standards
- Complete comments
- Unified naming conventions
- Appropriate error handling

### Testing Requirements

- Unit test coverage
- Integration testing
- Performance testing
- Compatibility testing
