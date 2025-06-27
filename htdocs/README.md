# KartRider Analytics

KartRider Analytics is a web-based data analytics and management platform for kart racing game data. Built with PHP and MySQL, it provides comprehensive tools for data browsing, querying, visualization, and player management.

## Features

- Modern, responsive web interface
- Table viewer with search, sort, and filter
- Dynamic SQL query execution and analysis
- Player profile management and statistics
- Interactive data analytics dashboard with charts
- Modular MVC architecture for easy maintenance

## Directory Structure

```
PhpLab/
├── index.php                # Main landing page (welcome + feature grid)
├── table_viewer.php         # Table viewer entry point
├── dashboard.php            # Data analytics dashboard entry point
├── queries.php              # Dynamic queries entry point
├── profile.php              # Player profiles entry point
├── config.php               # Main configuration file
├── config_environment.php   # Environment-specific config
├── assets/                  # Static assets (CSS, JS, SQL)
├── controllers/             # MVC controllers
├── includes/                # Core includes and helpers
├── models/                  # Data models
├── views/                   # View templates
├── docs/                    # Project documentation
├── legacy/                  # Legacy/backup files
├── tests/                   # Test and verification scripts
└── README.md                # Project overview (this file)
```

## Quick Start

1. Clone the repository to your web server directory.
2. Import the database schema from `assets/kartrider_ddl.sql` and sample data from `assets/kartrider_data.sql`.
3. Configure database connection in `config.php` and `config_environment.php`.
4. Access `index.php` in your browser to start using the platform.

## Documentation

See the `docs/` directory for detailed documentation and refactoring notes.

## License

This project is for educational and research purposes only.
