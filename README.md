# kartrider-analytics-database

![Database Version](https://img.shields.io/badge/Database-MySQL%208.0%2B-blue)
![Normalization](https://img.shields.io/badge/Normalization-3NF%2FBCNF-green)
![Data Quality](https://img.shields.io/badge/Data%20Quality-Production%20Ready-brightgreen)

## 📊 Project Overview

**KartRider Analytics** is a comprehensive database design for a kart racing game analytics system. This project demonstrates advanced database normalization techniques, implementing **Third Normal Form (3NF)** and **Boyce-Codd Normal Form (BCNF)** standards to ensure data integrity and optimal performance.

### 🎯 Key Features

- **Fully Normalized Schema**: Eliminates redundancy through proper 3NF/BCNF decomposition
- **Comprehensive Data Model**: Covers players, races, karts, tracks, achievements, and performance analytics
- **Rich Sample Data**: 30+ players, 25+ races, 25 karts, 20 tracks with realistic distributions
- **Performance Optimized**: Strategic indexing for query optimization
- **Constraint Complete**: Full referential integrity with foreign keys and check constraints

## 🗃️ Database Architecture

### Core Entity Relationships

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

### 📋 Table Structure

| **Player Hierarchy** | **Racing Core** | **Game Assets** |
|---------------------|----------------|-----------------|
| `Player` | `Race` | `Kart` |
| `PlayerCredentials` | `Participation` | `KartDetails` |
| `RegisteredPlayer` | `LapRecord` | `SpeedKart` |
| `GuestPlayer` | `Track` | `ItemKart` |
| `PlayerAchievement` | | `Achievement` |

## 🚀 Quick Start

### Prerequisites
- MySQL 8.0+ or MariaDB 10.4+
- phpMyAdmin (optional, for web interface)
- Minimum 50MB database storage

### Installation

1. **Clone or Download** the SQL file
   ```bash
   # If using Git
   git clone https://github.com/yourusername/kartrider-analytics-database.git
   
   # Or download the SQL file directly
   wget https://raw.githubusercontent.com/yourusername/kartrider-analytics-database/main/KartRiderAnalytics___DATABASE__.sql
   ```

2. **Import Database**
   ```bash
   # Command line method
   mysql -u username -p < KartRiderAnalytics___DATABASE__.sql
   
   # Or use phpMyAdmin: Import → Choose File → Execute
   ```

3. **Verify Installation**
   ```sql
   USE KartRiderAnalytics;
   SHOW TABLES;
   
   -- Check data counts
   SELECT 
       'Players' as Entity, COUNT(*) as Count FROM Player
   UNION ALL
   SELECT 'Races', COUNT(*) FROM Race
   UNION ALL
   SELECT 'Participations', COUNT(*) FROM Participation;
   ```

## 📊 Sample Queries & Analytics

### 🏆 Player Performance Analysis
```sql
-- Top 10 Players by Average Finishing Position
SELECT 
    pc.UserName,
    p.TotalRaces,
    ROUND(AVG(part.FinishingRank), 2) as AvgRank,
    COUNT(part.ParticipationID) as RacesCompleted
FROM Player p
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
JOIN Participation part ON p.PlayerID = part.PlayerID
GROUP BY p.PlayerID
HAVING RacesCompleted >= 5
ORDER BY AvgRank ASC
LIMIT 10;
```

### 🏎️ Kart Performance Statistics
```sql
-- Kart Win Rates and Usage
SELECT 
    k.KartName,
    kd.Manufacturer,
    COUNT(p.ParticipationID) as TimesUsed,
    SUM(CASE WHEN p.FinishingRank = 1 THEN 1 ELSE 0 END) as Wins,
    ROUND(
        SUM(CASE WHEN p.FinishingRank = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(p.ParticipationID), 
        2
    ) as WinRate
FROM Kart k
JOIN KartDetails kd ON k.KartName = kd.KartName
JOIN Participation p ON k.KartID = p.KartID
GROUP BY k.KartID
HAVING TimesUsed >= 3
ORDER BY WinRate DESC;
```

### 🏁 Track Difficulty Analysis
```sql
-- Average Completion Times by Track Difficulty
SELECT 
    t.TrackDifficulty,
    COUNT(DISTINCT t.TrackName) as TrackCount,
    ROUND(AVG(p.TotalTime), 2) as AvgCompletionTime,
    ROUND(AVG(t.TrackLength), 2) as AvgTrackLength
FROM Track t
JOIN Race r ON t.TrackName = r.TrackName
JOIN Participation p ON r.RaceID = p.RaceID
GROUP BY t.TrackDifficulty
ORDER BY AvgCompletionTime;
```

### 🎖️ Achievement Progress
```sql
-- Player Achievement Leaderboard
SELECT 
    pc.UserName,
    COUNT(pa.AchievementID) as AchievementsEarned,
    SUM(a.PointsAwarded) as TotalPoints,
    MAX(pa.DateEarned) as LastAchievement
FROM PlayerCredentials pc
JOIN PlayerAchievement pa ON pc.PlayerID = pa.PlayerID
JOIN Achievement a ON pa.AchievementID = a.AchievementID
GROUP BY pc.PlayerID
ORDER BY TotalPoints DESC
LIMIT 15;
```

## 🔧 Database Features

### 🏗️ Normalization Implementation

**3NF Decomposition Examples:**
- **Player** → **PlayerCredentials**: Separated to eliminate transitive dependencies (UserName → Email)
- **Race** → **Track**: Removed partial dependencies (TrackName → TrackDifficulty, TrackLength)
- **Kart** → **KartDetails**: Isolated manufacturer information to prevent update anomalies

### 🛡️ Data Integrity Constraints

```sql
-- Ranking Constraints
FinishingRank BETWEEN 1 AND 8

-- Performance Constraints  
TotalTime > 0 AND LapTime > 0

-- Referential Integrity
FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE
```

### ⚡ Performance Optimization

- **Primary Indexes**: All tables have optimized primary keys
- **Foreign Key Indexes**: Automatic indexing on all foreign key relationships
- **Composite Indexes**: `idx_player_performance (PlayerID, FinishingRank)`
- **Query Optimization**: Indexed on frequently queried columns (RaceDate, DateEarned)

## 📈 Data Distribution & Insights

### Player Activity Levels
- **Casual Players** (1-3 races): 40% of user base
- **Regular Players** (4-8 races): 45% of user base  
- **Hardcore Players** (9+ races): 15% of user base

### Track Difficulty Distribution
- **Easy Tracks**: 25% (Average completion: ~150 seconds)
- **Medium Tracks**: 30% (Average completion: ~200 seconds)
- **Hard Tracks**: 25% (Average completion: ~250 seconds)
- **Expert Tracks**: 20% (Average completion: ~310 seconds)

### Kart Type Analysis
- **Speed Karts**: 80% of fleet (High speed, moderate handling)
- **Item Karts**: 20% of fleet (Moderate speed, item advantages)

## 🔍 Advanced Analytics Capabilities

### Business Intelligence Queries
- Player retention analysis
- Kart balancing metrics
- Track popularity trends
- Achievement completion rates
- Performance progression tracking

### Reporting Capabilities
- Daily/Weekly/Monthly race summaries
- Player skill progression reports
- Kart usage and effectiveness analysis
- Track difficulty calibration data

## 🛠️ Technical Specifications

| **Component** | **Details** |
|---------------|-------------|
| **Database Engine** | MySQL 8.0+ / MariaDB 10.4+ |
| **Character Set** | UTF8MB4 (Full Unicode support) |
| **Storage Engine** | InnoDB (ACID compliance) |
| **Total Tables** | 12 normalized tables |
| **Sample Records** | 650+ realistic data entries |
| **Indexes** | 15+ optimized indexes |
| **Constraints** | 25+ integrity constraints |

## 📄 Documentation

### File Structure
```
KartRiderAnalytics___DATABASE__.sql    # Complete database dump
├── Schema Definition                   # Table structures & constraints
├── Sample Data                        # Realistic test data (30+ players)
├── Indexes                           # Performance optimization
└── Foreign Key Constraints           # Referential integrity
```

### Key Design Decisions
1. **Player Hierarchy**: Supports both registered and guest players
2. **Kart Specialization**: Speed vs Item kart types with unique attributes
3. **Track Normalization**: Separate track properties from race instances
4. **Achievement System**: Flexible point-based reward system
5. **Performance Tracking**: Detailed lap-by-lap race analytics

## 🤝 Contributing

This database design follows academic database design principles and is suitable for:
- Database design coursework and projects
- Racing game development
- Analytics system prototyping
- SQL learning and practice

## 📜 License

This project is created for educational purposes as part of CS 5200 Database Systems coursework.

---

**Export Information:**
- **Generated**: June 23, 2025
- **phpMyAdmin Version**: 5.2.1
- **Server**: MariaDB 10.4.28
- **Export Type**: Complete structure and data

## 📞 Support

For questions about the database design or implementation:
- Review the normalization analysis documentation
- Check the sample queries for usage examples
- Examine constraint definitions for business rules

---

*This database demonstrates production-ready design patterns suitable for high-performance gaming analytics platforms.*
