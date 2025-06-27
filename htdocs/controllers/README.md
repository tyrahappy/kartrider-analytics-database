# Controllers Directory

## Overview

This directory contains all controller files for the project, following the MVC architecture pattern. Controllers are responsible for handling user requests, business logic, and data processing.

## File Structure

```
controllers/
├── README.md                    # This documentation file
├── DashboardController.php      # Main dashboard controller (279 lines)
├── PlayerStatsController.php    # Independent player statistics controller (569 lines)
├── ProfileController.php        # Player profile controller (331 lines)
├── QueriesController.php        # Query tools controller (313 lines)
├── TableViewerController.php    # Table viewer controller (160 lines)
└── dashboard/                   # Dashboard sub-modules directory
    ├── README.md                # Dashboard modules documentation
    ├── PlayerStatsDashboardController.php # Player statistics module (416 lines)
    ├── SessionAnalyticsController.php # Session analytics module (495 lines)
    └── AchievementDashboardController.php # Achievements module (404 lines)
```

## Controller Categories

### Dashboard Modules (Refactored)

- `DashboardController.php`: Main controller, responsible for routing and cache management
- `dashboard/PlayerStatsDashboardController.php`: Player statistics module
- `dashboard/SessionAnalyticsController.php`: Session analytics module
- `dashboard/AchievementDashboardController.php`: Achievements module

### Independent Feature Controllers

- `PlayerStatsController.php`: Independent player statistics functionality
- `ProfileController.php`: Player profile management
- `QueriesController.php`: Database query tools
- `TableViewerController.php`: Table data viewer

## Architecture Design

### Inheritance Hierarchy

```
BaseController (includes/BaseController.php)
├── DashboardController
├── PlayerStatsController
├── ProfileController
├── QueriesController
├── TableViewerController
└── dashboard/
    ├── PlayerStatsDashboardController
    ├── SessionAnalyticsController
    └── AchievementDashboardController
```

### Modular Design

- **Main Controller**: Responsible for parameter parsing, cache management, and route distribution
- **Sub-controllers**: Focus on specific module business logic
- **Independent Controllers**: Handle standalone feature pages

## Feature Description

### Dashboard Modules

- **Player Statistics**: Player count, activity levels, win rate rankings, participation distribution
- **Session Analytics**: Race statistics, track analysis, kart usage, daily trends
- **Achievement System**: Achievement tracking, completion rates, leaderboards, distribution statistics

### Independent Features

- **Player Profiles**: Personal statistics, history records, achievement display
- **Query Tools**: Custom SQL queries, data export
- **Table Viewer**: Database table structure viewing, data browsing

## Design Principles

### Single Responsibility

- Each controller is responsible for only one specific function
- Avoid functional coupling for easier maintenance and testing

### Modularity

- Related functions are organized in the same controller
- Supports independent development and testing

### Extensibility

- Easy to add new controllers and features
- Supports plugin architecture

## Usage

### Basic Usage

```php
// Main controller
$dashboard = new DashboardController();
$dashboard->run();

// Sub-controller
$playerStats = new PlayerStatsDashboardController('all', 'all');
$data = $playerStats->getPlayerStatistics($pdo);
```

### Parameter Passing

- Time filters: `all`, `7days`, `30days`, `3months`
- Player types: `all`, `registered`, `guest`

## Performance Optimization

- Query caching mechanism
- InfinityFree hosting optimization
- Error handling and fallback mechanisms
- Query limits and timeout handling

## Development Standards

- Follow PSR-4 autoloading standards
- Use unified error handling mechanisms
- Maintain complete code comments
- Support multi-environment configuration

## Directory Description

### dashboard/ Subdirectory

Contains the three core sub-modules of Dashboard, each responsible for specific data analysis functions. For detailed documentation, please refer to `dashboard/README.md`.

### Modular Advantages

- **Clear Structure**: Separation of main controller and sub-modules
- **Easy Maintenance**: Each module can be developed and tested independently
- **Code Reusability**: Sub-modules can be called by other controllers
- **High Extensibility**: Easy to add new analysis modules
