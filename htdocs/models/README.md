# Models Directory

## Overview

This directory contains all model files for the project, responsible for data access and business logic processing. The model layer follows the MVC architecture pattern, providing abstract interfaces for data operations.

## File Structure

```
models/
├── README.md                    # This documentation file
└── DatabaseService.php          # Database connection service (174 lines)
```

## Model Description

### Database Service

- `DatabaseService.php`: Database connection and management service
  - Database connection pool management
  - Connection status monitoring
  - Error handling and reconnection mechanisms
  - Support for multiple database types
  - Singleton pattern implementation
  - PDO-based database operations

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

### Database Service Operations

```php
// Get database service instance
$db = DatabaseService::getInstance();

// Check connection status
if ($db->isConnected()) {
    // Perform database operations
}

// Execute queries
$results = $db->fetchAll("SELECT * FROM players");
$single = $db->fetch("SELECT * FROM players WHERE id = ?", [$id]);

// Transaction handling
$db->beginTransaction();
try {
    $db->execute("INSERT INTO players (name) VALUES (?)", [$name]);
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    throw $e;
}
```

## Database Connection Features

### Connection Management

- Singleton pattern for connection pooling
- Automatic connection establishment
- Connection status monitoring
- Error handling and recovery

### Query Operations

- Prepared statement support
- Transaction management
- Result fetching methods
- Error logging and debugging

## Development Standards

### Naming Conventions

- Service class names end with `Service`
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

### Adding New Services

1. Follow singleton pattern if needed
2. Implement necessary database operations
3. Add proper error handling
4. Update related documentation

### Database Configuration

- Support for multiple database types
- Connection pool configuration
- Timeout and retry settings
- Error log recording
