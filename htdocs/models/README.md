# Models Directory

## Overview

This directory contains all model files for the project, responsible for data access and business logic processing. The model layer follows the MVC architecture pattern, providing abstract interfaces for data operations.

## File Structure

```
models/
├── README.md                    # This documentation file
├── BaseModel.php                # Base model class
├── PlayerModel.php              # Player data model
├── RaceModel.php                # Race data model
└── AchievementModel.php         # Achievement data model
```

## Model Description

### Base Model

- `BaseModel.php`: Base class for all models
  - Provides database connection management
  - Defines common CRUD operations
  - Includes error handling mechanisms

### Business Models

- `PlayerModel.php`: Player-related data operations

  - Player information queries and updates
  - Player statistics data processing
  - Player profile management

- `RaceModel.php`: Race-related data operations

  - Race record queries
  - Race statistics calculations
  - Track data analysis

- `AchievementModel.php`: Achievement-related data operations
  - Achievement information management
  - Achievement completion statistics
  - Achievement leaderboard processing

## Design Principles

### Data Abstraction

- Encapsulate database operation details
- Provide concise data access interfaces
- Support multiple database types

### Business Logic

- Handle complex business logic in the model layer
- Ensure data consistency and integrity
- Provide data validation functionality

### Performance Optimization

- Implement query caching mechanisms
- Optimize database query statements
- Support batch operations

## Usage

### Basic Operations

```php
// Create model instance
$playerModel = new PlayerModel();

// Query data
$players = $playerModel->getAllPlayers();

// Update data
$playerModel->updatePlayer($playerId, $data);

// Delete data
$playerModel->deletePlayer($playerId);
```

### Inheriting Base Class

```php
class CustomModel extends BaseModel {
    public function customMethod() {
        // Custom business logic
    }
}
```

## Database Table Mapping

### Player-Related Tables

- `Player`: Basic player information
- `PlayerCredentials`: Player authentication information
- `RegisteredPlayer`: Registered player information

### Race-Related Tables

- `Race`: Race records
- `Participation`: Participation records
- `Track`: Track information
- `Kart`: Kart information

### Achievement-Related Tables

- `Achievement`: Achievement definitions
- `PlayerAchievement`: Player achievement records

## Development Standards

### Naming Conventions

- Model class names end with `Model`
- Method names use camelCase
- Database field names use snake_case

### Error Handling

- Unified exception handling mechanisms
- Detailed error log recording
- User-friendly error message returns

### Code Comments

- Classes and methods must have comments
- Complex logic requires detailed explanations
- Parameters and return values must be clearly annotated

## Extension Guide

### Adding New Models

1. Inherit from `BaseModel` class
2. Implement necessary CRUD methods
3. Add business logic methods
4. Update related documentation

### Database Migration

- Model changes require synchronized database structure
- Provide data migration scripts
- Maintain backward compatibility
