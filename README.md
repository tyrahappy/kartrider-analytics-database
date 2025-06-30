
# KartRider Analytics Database & Web Platform

![Database Version](https://img.shields.io/badge/Database-MySQL%208.0%2B-blue)
![Normalization](https://img.shields.io/badge/Normalization-3NF%2FBCNF-green)
![Web Platform](https://img.shields.io/badge/Web-PHP%2BMySQL-lightgrey)
![Status](https://img.shields.io/badge/Status-Complete-brightgreen)

## Project Overview

**KartRider Analytics** is a full-stack database management and analytics web platform for a kart racing game. The project includes a relational database in **MySQL** and an interactive web application built with **PHP**, supporting player management, dynamic SQL queries, and game performance analytics.

It demonstrates best practices in:
- Database design (3NF/BCNF normalization)
- CRUD operations (Create, Read, Update, Delete with cascade)
- Analytical query building
- Dashboard data visualization

## Project Structure

```
kartrider-analytics-database-main/
├── CodeBase.pdf                     # Project codebase documentation
├── README.md                         # Project overview and setup instructions
├── kartrider_data.sql                # SQL file for sample data population
├── kartrider_ddl.sql                 # SQL file for database schema (DDL)

├── htdocs/                           # Web application root directory
│   ├── README.md                     # Application structure documentation
│   ├── config.php                    # Main configuration file (database connection, paths)
│   ├── config_environment.php        # Environment-specific settings
│   ├── dashboard.php                 # Dashboard entry point (analytics interface)
│   ├── index.php                     # Main landing page (homepage + navigation)
│   ├── profile.php                   # Profile management (CRUD operations)
│   ├── queries.php                   # Dynamic query interface
│   ├── table_viewer.php              # Database table viewer

│   ├── assets/                       # Static assets (CSS, JavaScript, SQL)
│   │   ├── README.md                 # Assets folder documentation
│   │   ├── dashboard.js              # Dashboard interactive features (JS)
│   │   ├── style.css                 # Global CSS styling
│   │   └── tabs.js                   # Tab navigation interactivity

│   ├── controllers/                  # MVC controllers handling backend logic
│   │   ├── DashboardController.php   # Main controller for dashboard routing
│   │   ├── PlayerStatsController.php # Handles player statistics page backend logic
│   │   ├── ProfileController.php     # CRUD operations for player profiles
│   │   ├── QueriesController.php     # Dynamic SQL query execution backend
│   │   ├── TableViewerController.php # Backend for table viewer interface
│   │   ├── README.md                 # Controllers module documentation
│   │   ├── dashboard/                # Sub-controllers for dashboard modules
│   │   │   ├── AchievementDashboardController.php  # Achievement analytics controller
│   │   │   ├── PlayerStatsDashboardController.php  # Player statistics analytics controller
│   │   │   ├── SessionAnalyticsController.php      # Session analytics controller
│   │   │   └── README.md                           # Dashboard controllers documentation

│   ├── docs/                         # Project documentation
│   │   ├── README.md                  # Documentation folder overview
│   │   ├── REFACTOR_README.md         # Refactoring logs and notes
│   │   ├── Conceptual_Design.pdf      # Conceptual ERD design document
│   │   ├── Logical_Schema.pdf         # Relational schema design document
│   │   ├── Normalization_Steps.pdf    # Database normalization step-by-step
│   │   └── Final_Analytical_Report.pdf# Final analytical report with dashboard insights

│   ├── includes/                     # Core backend services and utilities
│   │   ├── AssetHelper.php            # Helper for static asset management
│   │   ├── BaseController.php         # Base class for all controllers
│   │   ├── DatabaseService.php        # Database connection service
│   │   └── README.md                  # Includes folder documentation

│   ├── legacy/                       # Backup files and deprecated versions
│   │   ├── DashboardController_backup.php  # Backup of the dashboard controller
│   │   ├── config_original_backup.php      # Backup of the original config
│   │   └── README.md                       # Legacy folder documentation

│   ├── models/                        # MVC data models representing database tables
│   │   ├── AchievementModel.php        # Data model for achievements
│   │   ├── BaseModel.php               # Base model class for shared functions
│   │   ├── PlayerModel.php             # Data model for players
│   │   ├── RaceModel.php               # Data model for races and sessions
│   │   └── README.md                   # Models folder documentation

│   ├── tests/                          # Test scripts and verification
│   │   └── test_session_analytics_fix.php # Test for session analytics module
```

## Database Design

### Entity-Relationship (ER) Summary

```
Player (1:1) ↔ PlayerCredentials
Player (1:1) ↔ RegisteredPlayer/GuestPlayer
Player (1:M) → Participation (M:1) ← Race
Participation (1:M) → LapRecord
Kart (1:1) ↔ KartDetails
Kart (1:1) ↔ SpeedKart/ItemKart
Track (1:M) → Race
Achievement (M:M) ↔ Player (via PlayerAchievement)
```

### Table Categories

| **Player Management**     | **Game Activities**   | **Game Assets**   |
|---------------------------|------------------------|-------------------|
| Player                    | Race                   | Kart              |
| PlayerCredentials         | Participation          | KartDetails       |
| RegisteredPlayer/GuestPlayer | LapRecord           | SpeedKart/ItemKart|
| PlayerAchievement         |                        | Track, Achievement|

### Key Design Features
- Fully normalized (3NF/BCNF)
- Foreign keys with **ON DELETE CASCADE**
- Check constraints for data validity (e.g., `FinishingRank BETWEEN 1 AND 8`)
- Strategic indexes for performance

## Web Functionalities

### Profile Management (CRUD)
- Register new player
- Update player profile (username, email, profile picture)
- Delete player with cascade (removes participation, achievements)

### Dynamic Query Module
- **Join Query** – Top players with achievements
- **Aggregation Query** – Average playtime & total achievements
- **Nested Group-By** – Weekly playtime analysis
- **Filtering & Ranking** – Top 5 players by win count
- **Custom Query** – Free SQL SELECT with safety constraints

### Dashboard Analytics
- **Player Stats:** player counts, active rates, type distributions
- **Session Stats:** race participation, kart usage, track difficulty
- **Achievement Stats:** progress, rare achievements, completion rates

## Installation Guide

### Prerequisites
- MySQL 8.0+ or MariaDB 10.4+
- Apache + PHP (XAMPP, MAMP, or PHP server)
- phpMyAdmin (optional)

### 1.Quick Setup
1. Import database schema:
   ```bash
   mysql -u username -p < kartrider_ddl.sql
   mysql -u username -p < kartrider_data.sql
   ```
2. Configure `db_connection.php`:
   ```php
   $conn = new mysqli('localhost', 'root', 'password', 'KartRiderAnalytics');
   ```
3. Deploy `htdocs/` to your PHP server root.
4. Open browser: `http://localhost/dashboard.php`

### 2.Repository Setup
bash# Clone the repository
git clone https://github.com/tyrahappy/kartrider-analytics-database.git
cd kartrider-analytics-database

## Sample SQL Queries

### Player Performance
```sql
SELECT pc.UserName, COUNT(*) AS TotalWins
FROM Participation p
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
WHERE p.FinishingRank = 1
GROUP BY pc.UserName
ORDER BY TotalWins DESC
LIMIT 5;
```

### Kart Usage
```sql
SELECT k.KartName, COUNT(*) AS UsageCount
FROM Participation p
JOIN Kart k ON p.KartID = k.KartID
GROUP BY k.KartName
ORDER BY UsageCount DESC
LIMIT 5;
```

### Race Distribution by Difficulty
```sql
SELECT t.TrackDifficulty, COUNT(*) AS RaceCount
FROM Race r
JOIN Track t ON r.TrackName = t.TrackName
GROUP BY t.TrackDifficulty;
```
## Additional Features

Table Browser: Complete database exploration with search/sort
Interactive Filters: Time period and demographic filtering
Real-time Updates: Designed live‑update dashboard architecture, with plans to integrate intelligent caching for performance
Security: Enterprise-grade SQL injection prevention

## Dashboard Preview

| Module        | Key Metrics                                |
|----------------|--------------------------------------------|
| Player Stats  | Total players, active players, win rates  |
| Session Stats | Race counts, kart usage, track popularity |
| Achievement   | Total achievements, rarest, completion %  |

> With filters by time range and player type.

## Performance Metris

### Database Statistcics

Tables: 14 fully normalized tables
Records: 377+ comprehensive test records
Queries: 25+ optimized analytical queries
Performance: Sub-second response times with indexing
Relationships: Complex multi-table joins with referential integrity

### Application Metrics

Controllers: 8 specialized MVC controllers
Features: Complete CRUD + 5 analytics modules
Visualizations: 15+ interactive charts and tables
Code Quality: 2,500+ lines of documented, production-ready code
Security: Zero SQL injection vulnerabilities

## Conclusion

This project fulfills all core requirements of a database-driven web application with CRUD, dynamic querying, and interactive dashboards. It deepens understanding of relational databases, SQL optimization, and data visualization in a full-stack environment.

## Support & Resources

### Project Links

Live Application: https://kartrider.kesug.com
Source Repository: https://github.com/tyrahappy/kartrider-analytics-database.git
Documentation: Comprehensive README files in each directory
Academic Context: CS 5200 Database Management Systems
