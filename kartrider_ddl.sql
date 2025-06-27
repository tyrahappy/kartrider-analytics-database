-- Create Database
CREATE DATABASE IF NOT EXISTS KartRiderAnalytics;
USE KartRiderAnalytics;

-- Drop existing tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS PlayerAchievement;
DROP TABLE IF EXISTS LapRecord;
DROP TABLE IF EXISTS Participation;
DROP TABLE IF EXISTS Achievement;
DROP TABLE IF EXISTS Race;
DROP TABLE IF EXISTS Track;
DROP TABLE IF EXISTS ItemKart;
DROP TABLE IF EXISTS SpeedKart;
DROP TABLE IF EXISTS KartDetails;
DROP TABLE IF EXISTS Kart;
DROP TABLE IF EXISTS GuestPlayer;
DROP TABLE IF EXISTS RegisteredPlayer;
DROP TABLE IF EXISTS PlayerCredentials;
DROP TABLE IF EXISTS Player;

-- 1. Player Hierarchy Tables

-- Player base table
CREATE TABLE Player (
    PlayerID INT PRIMARY KEY AUTO_INCREMENT,
    TotalRaces INT NOT NULL DEFAULT 0,
    CONSTRAINT chk_total_races CHECK (TotalRaces >= 0)
);

-- Player credentials (for BCNF normalization)
CREATE TABLE PlayerCredentials (
    PlayerID INT PRIMARY KEY,
    UserName VARCHAR(50) UNIQUE NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    CONSTRAINT chk_email CHECK (Email LIKE '%@%.%')
);

-- Registered players (ISA relationship)
CREATE TABLE RegisteredPlayer (
    PlayerID INT PRIMARY KEY,
    ProfilePicture VARCHAR(255) DEFAULT 'default_avatar.png',
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE
);

-- Guest players (ISA relationship) - SessionID now UNIQUE
CREATE TABLE GuestPlayer (
    PlayerID INT PRIMARY KEY,
    SessionID VARCHAR(100) UNIQUE NOT NULL,
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE
);

-- 2. Track Information

CREATE TABLE Track (
    TrackName VARCHAR(100) PRIMARY KEY,
    TrackDifficulty ENUM('Easy', 'Medium', 'Hard', 'Expert') NOT NULL,
    TrackLength DECIMAL(5,2) NOT NULL,
    CONSTRAINT chk_track_length CHECK (TrackLength > 0)
);

-- 3. Kart Hierarchy Tables

-- Kart base table
CREATE TABLE Kart (
    KartID INT PRIMARY KEY AUTO_INCREMENT,
    KartName VARCHAR(100) UNIQUE NOT NULL,
    MaxSpeed INT NOT NULL,
    Handling INT NOT NULL,
    CONSTRAINT chk_max_speed CHECK (MaxSpeed BETWEEN 50 AND 200),
    CONSTRAINT chk_handling CHECK (Handling BETWEEN 1 AND 100)
);

-- Kart details (for BCNF normalization) - Added ON DELETE CASCADE
CREATE TABLE KartDetails (
    KartName VARCHAR(100) PRIMARY KEY,
    Manufacturer VARCHAR(50) NOT NULL,
    ReleaseYear YEAR NOT NULL,
    FOREIGN KEY (KartName) REFERENCES Kart(KartName) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Speed karts (ISA relationship)
CREATE TABLE SpeedKart (
    KartID INT PRIMARY KEY,
    TopSpeedBonus INT NOT NULL DEFAULT 10,
    FOREIGN KEY (KartID) REFERENCES Kart(KartID) ON DELETE CASCADE,
    CONSTRAINT chk_speed_bonus CHECK (TopSpeedBonus BETWEEN 5 AND 30)
);

-- Item karts (ISA relationship) - Modified constraint and default
CREATE TABLE ItemKart (
    KartID INT PRIMARY KEY,
    ItemSlots INT NOT NULL DEFAULT 2,
    FOREIGN KEY (KartID) REFERENCES Kart(KartID) ON DELETE CASCADE,
    CONSTRAINT chk_item_slots CHECK (ItemSlots BETWEEN 1 AND 3)
);

-- 4. Race Information

CREATE TABLE Race (
    RaceID INT PRIMARY KEY AUTO_INCREMENT,
    RaceName VARCHAR(100) NOT NULL,
    RaceDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    TrackName VARCHAR(100) NOT NULL,
    FOREIGN KEY (TrackName) REFERENCES Track(TrackName) ON UPDATE CASCADE,
    INDEX idx_race_date (RaceDate)
);

-- 5. Achievement Information

CREATE TABLE Achievement (
    AchievementID INT PRIMARY KEY AUTO_INCREMENT,
    AchievementName VARCHAR(100) UNIQUE NOT NULL,
    Description TEXT,
    PointsAwarded INT NOT NULL DEFAULT 10,
    CONSTRAINT chk_points CHECK (PointsAwarded > 0)
);

-- 6. Participation Information

CREATE TABLE Participation (
    ParticipationID INT PRIMARY KEY AUTO_INCREMENT,
    PlayerID INT NOT NULL,
    RaceID INT NOT NULL,
    KartID INT NOT NULL,
    FinishingRank INT NOT NULL,
    TotalTime DECIMAL(8,3) NOT NULL,
    UNIQUE KEY unique_player_race (PlayerID, RaceID),
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (RaceID) REFERENCES Race(RaceID) ON DELETE CASCADE,
    FOREIGN KEY (KartID) REFERENCES Kart(KartID),
    CONSTRAINT chk_rank CHECK (FinishingRank BETWEEN 1 AND 8),
    CONSTRAINT chk_time CHECK (TotalTime > 0),
    INDEX idx_player_performance (PlayerID, FinishingRank)
);

-- 7. Lap Records

CREATE TABLE LapRecord (
    ParticipationID INT NOT NULL,
    LapNumber INT NOT NULL,
    LapTime DECIMAL(6,3) NOT NULL,
    PRIMARY KEY (ParticipationID, LapNumber),
    FOREIGN KEY (ParticipationID) REFERENCES Participation(ParticipationID) ON DELETE CASCADE,
    CONSTRAINT chk_lap_number CHECK (LapNumber BETWEEN 1 AND 5),
    CONSTRAINT chk_lap_time CHECK (LapTime > 0)
);

-- 8. Player Achievements

CREATE TABLE PlayerAchievement (
    PlayerID INT NOT NULL,
    AchievementID INT NOT NULL,
    DateEarned DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (PlayerID, AchievementID),
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (AchievementID) REFERENCES Achievement(AchievementID) ON DELETE CASCADE,
    INDEX idx_date_earned (DateEarned)
);