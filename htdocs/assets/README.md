# Assets Directory

This directory contains all static assets for the KartRider Analytics application.

## Files

### CSS Files

- `main.css` - Main stylesheet that imports all modular CSS files
- `base.css` - Base styles (reset, typography, containers)
- `layout.css` - Layout styles (sidebar, main content, dashboard layout)
- `components.css` - Component styles (buttons, forms, tables, alerts)
- `tabs.css` - Tab navigation system styles
- `dashboard.css` - Dashboard-specific styles (stats cards, charts)
- `utilities.css` - Utility classes and helper styles
- `responsive.css` - Responsive design and media queries
- `style.css` - Legacy main stylesheet (deprecated, use main.css instead)

### JavaScript Files

- `tabs.js` - Tab navigation functionality
- `dashboard.js` - Dashboard-specific JavaScript functionality

## CSS Architecture

The CSS has been modularized for better maintainability:

1. **main.css** - Entry point that imports all other CSS files
2. **base.css** - Foundation styles (reset, typography, basic layout)
3. **layout.css** - Page structure and layout components
4. **components.css** - Reusable UI components
5. **tabs.css** - Tab navigation system
6. **dashboard.css** - Dashboard-specific styles
7. **utilities.css** - Helper classes and utilities
8. **responsive.css** - Media queries and responsive design

## Usage

Include `main.css` in your HTML to load all styles:

```html
<link rel="stylesheet" href="assets/main.css" />
```

## Development

When making changes to styles:

1. Edit the appropriate modular CSS file
2. Test the changes
3. The changes will be automatically included via `main.css`

## Structure

This directory contains:

- CSS files for styling the application
- JavaScript files for interactive functionality
- SQL files for database setup and sample data

## File Descriptions

### CSS Files

- `main.css`: Main stylesheet that imports all modular CSS files
- `base.css`: Base styles including reset, typography, and containers
- `layout.css`: Layout styles for sidebar, main content, and dashboard
- `components.css`: Component styles for buttons, forms, tables, and alerts
- `tabs.css`: Tab navigation system styles
- `dashboard.css`: Dashboard-specific styles for stats cards and charts
- `utilities.css`: Utility classes and helper styles
- `responsive.css`: Responsive design and media queries
- `style.css`: Legacy main stylesheet (deprecated)

### JavaScript Files

- `tabs.js`: Tab navigation functionality
- `dashboard.js`: Dashboard-specific JavaScript functionality

### SQL Files

- `kartrider_data.sql`: Example data for database import
- `kartrider_ddl.sql`: Database schema definition
