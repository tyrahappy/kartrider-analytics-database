# Views Directory

## Overview

This directory contains all view files for the project, responsible for page display and user interface. The view layer follows the MVC architecture pattern, providing HTML templates and frontend interactions.

## File Structure

```
views/
├── README.md                    # This documentation file
├── layout.php                   # Main layout template (134 lines)
├── error.php                    # Error page template (114 lines)
├── dashboard_modules_inline.php # Dashboard inline modules (1002 lines)
├── profile_content.php          # Player profile content (320 lines)
├── queries_content.php          # Query tools content (105 lines)
└── table_viewer_content.php     # Table viewer content (113 lines)
```

## View Description

### Layout Templates

- `layout.php`: Main layout file

  - Defines basic page structure
  - Includes header, navigation, content area, footer
  - Handles CSS and JavaScript resource loading
  - Supports responsive design

- `error.php`: Error page template
  - Unified error message display
  - User-friendly error prompt interface
  - Provides return and help links

### Content Templates

- `dashboard_modules_inline.php`: Dashboard module content

  - Player statistics module display
  - Session analytics module display
  - Achievement system module display
  - Supports dynamic data loading

- `profile_content.php`: Player profile page content

  - Personal statistics information display
  - History record list
  - Achievement display area
  - Data visualization charts

- `queries_content.php`: Query tools page content

  - SQL query interface
  - Query result display
  - Data export functionality
  - Query history records

- `table_viewer_content.php`: Table viewer content
  - Database table structure display
  - Data browsing interface
  - Pagination and sorting functionality
  - Data filtering tools

## Design Principles

### Separation of Concerns

- Views are only responsible for display logic
- Do not contain business logic processing
- Data is passed through controllers

### Reusability

- Component-based design
- Supports template inheritance
- Easy to maintain and extend

### User Experience

- Responsive design
- Intuitive interface layout
- Good interactive feedback

## Template Syntax

### Basic Syntax

```php
<!-- Variable output -->
<?php echo $variable; ?>

<!-- Conditional statements -->
<?php if ($condition): ?>
    <!-- Content -->
<?php endif; ?>

<!-- Loop iteration -->
<?php foreach ($array as $item): ?>
    <!-- Loop content -->
<?php endforeach; ?>
```

### Layout Inheritance

```php
<!-- In child template -->
<?php $this->renderView('layout.php', $data); ?>
```

## Frontend Resources

### CSS Files

- `assets/style.css`: Main stylesheet
- Responsive design support
- Modern UI components

### JavaScript Files

- `assets/dashboard.js`: Dashboard interaction logic
- `assets/tabs.js`: Tab functionality
- AJAX data loading
- Dynamic chart updates

## Data Passing

### Controller to View

```php
// In controller
$this->renderView('template.php', [
    'data' => $data,
    'user' => $user
]);
```

### Data Access in Views

```php
<!-- In view -->
<?php echo $data['key']; ?>
<?php echo $user->name; ?>
```

## Development Standards

### Naming Conventions

- File names use lowercase and underscores
- Content files end with `_content.php`
- Layout files use descriptive names

### Code Organization

- Keep templates concise
- Avoid complex PHP logic
- Use appropriate indentation and comments

### Performance Optimization

- Reduce database queries
- Use caching mechanisms
- Optimize resource loading

## Extension Guide

### Adding New Views

1. Create new template file
2. Add rendering logic in controller
3. Update related styles and scripts
4. Test page functionality

### Theme Customization

- Support for multiple theme switching
- Configurable color schemes
- Flexible layout options
