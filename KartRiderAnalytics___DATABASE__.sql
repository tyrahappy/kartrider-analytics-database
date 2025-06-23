-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 23, 2025 at 10:46 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

--
-- KartRider Analytics
--  Database Group Project - CS 5200
--  Export Date: June 2025
--
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `KartRiderAnalytics`
--
CREATE DATABASE IF NOT EXISTS `KartRiderAnalytics` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `KartRiderAnalytics`;

-- --------------------------------------------------------

--
-- Table structure for table `Achievement`
--

DROP TABLE IF EXISTS `Achievement`;
CREATE TABLE `Achievement` (
  `AchievementID` int(11) NOT NULL,
  `AchievementName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `PointsAwarded` int(11) NOT NULL DEFAULT 10
) ;

--
-- Dumping data for table `Achievement`
--

INSERT INTO `Achievement` (`AchievementID`, `AchievementName`, `Description`, `PointsAwarded`) VALUES
(1, 'First Victory', 'Win your first race', 50),
(2, 'Speed Demon', 'Complete a race in under 2 minutes', 100),
(3, 'Drift Master', 'Perform 50 perfect drifts in one race', 75),
(4, 'Comeback King', 'Win after being in last place', 150),
(5, 'Perfect Lap', 'Complete a lap without hitting walls', 60),
(6, 'Marathon Runner', 'Complete 100 races', 200),
(7, 'Elite Racer', 'Win 50 races', 300),
(8, 'Variety Player', 'Use 10 different karts', 80),
(9, 'Track Master', 'Win on every track', 500),
(10, 'Consistent Winner', 'Win 5 races in a row', 250),
(11, 'Early Bird', 'Play before 6 AM', 30),
(12, 'Night Owl', 'Play after midnight', 30),
(13, 'Social Racer', 'Race with 7 other players', 40),
(14, 'Item Expert', 'Use 100 items effectively', 90),
(15, 'Clean Racer', 'Complete 10 races without using items', 120),
(16, 'Underdog Victory', 'Win with the slowest kart', 180),
(17, 'Photo Finish', 'Win by less than 0.1 seconds', 140),
(18, 'Lap Record', 'Set the fastest lap on any track', 160),
(19, 'Triple Crown', 'Win 3 Expert tracks in a row', 400),
(20, 'Grand Master', 'Reach 1000 total races', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `GuestPlayer`
--

DROP TABLE IF EXISTS `GuestPlayer`;
CREATE TABLE `GuestPlayer` (
  `PlayerID` int(11) NOT NULL,
  `SessionID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `GuestPlayer`
--

INSERT INTO `GuestPlayer` (`PlayerID`, `SessionID`) VALUES
(26, 'GUEST_2025_06_22_001'),
(27, 'GUEST_2025_06_22_002'),
(28, 'GUEST_2025_06_23_001'),
(29, 'GUEST_2025_06_23_002'),
(30, 'GUEST_2025_06_24_001');

-- --------------------------------------------------------

--
-- Table structure for table `ItemKart`
--

DROP TABLE IF EXISTS `ItemKart`;
CREATE TABLE `ItemKart` (
  `KartID` int(11) NOT NULL,
  `ItemSlots` int(11) NOT NULL DEFAULT 2
) ;

--
-- Dumping data for table `ItemKart`
--

INSERT INTO `ItemKart` (`KartID`, `ItemSlots`) VALUES
(21, 2),
(22, 3),
(23, 2),
(24, 3),
(25, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Kart`
--

DROP TABLE IF EXISTS `Kart`;
CREATE TABLE `Kart` (
  `KartID` int(11) NOT NULL,
  `KartName` varchar(100) NOT NULL,
  `MaxSpeed` int(11) NOT NULL,
  `Handling` int(11) NOT NULL
) ;

--
-- Dumping data for table `Kart`
--

INSERT INTO `Kart` (`KartID`, `KartName`, `MaxSpeed`, `Handling`) VALUES
(1, 'Lightning X1', 180, 75),
(2, 'Thunder V2', 175, 80),
(3, 'Storm Rider', 170, 85),
(4, 'Nitro Beast', 190, 70),
(5, 'Speed Phantom', 185, 72),
(6, 'Drift Master', 165, 90),
(7, 'Turbo Hawk', 178, 77),
(8, 'Velocity Pro', 182, 74),
(9, 'Racing Spirit', 168, 82),
(10, 'Neon Runner', 172, 78),
(11, 'Cyber Bolt', 188, 71),
(12, 'Quantum Racer', 176, 79),
(13, 'Atomic Drive', 171, 81),
(14, 'Laser Beam', 184, 73),
(15, 'Chrome Speed', 169, 83),
(16, 'Diamond Dash', 177, 76),
(17, 'Platinum Wing', 173, 80),
(18, 'Golden Arrow', 181, 75),
(19, 'Silver Streak', 167, 84),
(20, 'Bronze Bullet', 164, 86),
(21, 'Item Hunter', 155, 88),
(22, 'Shield Guardian', 150, 92),
(23, 'Boost Master', 160, 85),
(24, 'Trap Setter', 152, 90),
(25, 'Power Surge', 158, 87);

-- --------------------------------------------------------

--
-- Table structure for table `KartDetails`
--

DROP TABLE IF EXISTS `KartDetails`;
CREATE TABLE `KartDetails` (
  `KartName` varchar(100) NOT NULL,
  `Manufacturer` varchar(50) NOT NULL,
  `ReleaseYear` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `KartDetails`
--

INSERT INTO `KartDetails` (`KartName`, `Manufacturer`, `ReleaseYear`) VALUES
('Atomic Drive', 'AtomicRacing', '2025'),
('Boost Master', 'ItemTech', '2024'),
('Bronze Bullet', 'MetalWorks', '2023'),
('Chrome Speed', 'ChromeTech', '2025'),
('Cyber Bolt', 'CyberTech', '2025'),
('Diamond Dash', 'LuxuryKarts', '2025'),
('Drift Master', 'DriftTech', '2023'),
('Golden Arrow', 'LuxuryKarts', '2024'),
('Item Hunter', 'ItemTech', '2024'),
('Laser Beam', 'LaserWorks', '2024'),
('Lightning X1', 'Speed Corp', '2024'),
('Neon Runner', 'NeonDrive', '2025'),
('Nitro Beast', 'TurboWorks', '2025'),
('Platinum Wing', 'LuxuryKarts', '2024'),
('Power Surge', 'ItemTech', '2024'),
('Quantum Racer', 'QuantumSpeed', '2024'),
('Racing Spirit', 'ProRacing', '2024'),
('Shield Guardian', 'ItemTech', '2025'),
('Silver Streak', 'MetalWorks', '2023'),
('Speed Phantom', 'TurboWorks', '2024'),
('Storm Rider', 'DriftTech', '2024'),
('Thunder V2', 'Speed Corp', '2025'),
('Trap Setter', 'ItemTech', '2025'),
('Turbo Hawk', 'Speed Corp', '2024'),
('Velocity Pro', 'ProRacing', '2025');

-- --------------------------------------------------------

--
-- Table structure for table `LapRecord`
--

DROP TABLE IF EXISTS `LapRecord`;
CREATE TABLE `LapRecord` (
  `ParticipationID` int(11) NOT NULL,
  `LapNumber` int(11) NOT NULL,
  `LapTime` decimal(8,3) NOT NULL
) ;

--
-- Dumping data for table `LapRecord`
--

INSERT INTO `LapRecord` (`ParticipationID`, `LapNumber`, `LapTime`) VALUES
(1, 1, 28.567),
(1, 2, 28.234),
(1, 3, 28.456),
(1, 4, 28.789),
(1, 5, 28.521),
(2, 1, 28.123),
(2, 2, 28.234),
(2, 3, 28.345),
(2, 4, 28.234),
(2, 5, 28.298),
(9, 1, 95.234),
(9, 2, 95.567),
(9, 3, 96.544),
(15, 1, 39.567),
(15, 2, 39.789),
(15, 3, 39.456),
(15, 4, 39.890),
(15, 5, 39.754),
(22, 1, 48.234),
(22, 2, 49.123),
(22, 3, 49.456),
(22, 4, 49.234),
(22, 5, 49.631);

-- --------------------------------------------------------

--
-- Table structure for table `Participation`
--

DROP TABLE IF EXISTS `Participation`;
CREATE TABLE `Participation` (
  `ParticipationID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `RaceID` int(11) NOT NULL,
  `KartID` int(11) NOT NULL,
  `FinishingRank` int(11) NOT NULL,
  `TotalTime` decimal(8,3) NOT NULL
) ;

--
-- Dumping data for table `Participation`
--

INSERT INTO `Participation` (`ParticipationID`, `PlayerID`, `RaceID`, `KartID`, `FinishingRank`, `TotalTime`) VALUES
(1, 1, 1, 1, 2, 142.567),
(2, 2, 1, 5, 1, 141.234),
(3, 3, 1, 8, 4, 145.890),
(4, 4, 1, 12, 6, 148.234),
(5, 5, 1, 15, 3, 143.789),
(6, 6, 1, 3, 5, 147.123),
(7, 7, 1, 9, 7, 149.567),
(8, 8, 1, 20, 8, 151.234),
(9, 3, 2, 4, 1, 287.345),
(10, 5, 2, 1, 3, 291.567),
(11, 7, 2, 11, 2, 289.234),
(12, 9, 2, 14, 5, 295.678),
(13, 11, 2, 2, 4, 293.456),
(14, 13, 2, 7, 6, 298.123),
(15, 2, 3, 6, 3, 198.456),
(16, 4, 3, 10, 1, 195.234),
(17, 6, 3, 13, 4, 199.567),
(18, 8, 3, 16, 2, 197.890),
(19, 10, 3, 19, 5, 201.234),
(20, 12, 3, 3, 6, 203.456),
(21, 14, 3, 8, 7, 205.678),
(22, 1, 4, 2, 4, 245.678),
(23, 3, 4, 7, 2, 241.234),
(24, 5, 4, 11, 1, 239.567),
(25, 7, 4, 14, 3, 243.890),
(26, 9, 4, 17, 5, 247.123),
(27, 15, 4, 4, 6, 249.456),
(28, 2, 5, 21, 2, 186.789),
(29, 4, 5, 22, 5, 191.234),
(30, 6, 5, 23, 3, 188.567),
(31, 8, 5, 24, 4, 189.890),
(32, 10, 5, 25, 6, 192.345),
(33, 12, 5, 1, 1, 185.456),
(34, 16, 5, 5, 7, 194.567),
(35, 26, 5, 8, 8, 196.789),
(36, 1, 6, 3, 1, 156.234),
(37, 5, 6, 9, 3, 159.567),
(38, 9, 6, 15, 2, 158.890),
(39, 13, 6, 18, 4, 161.234),
(40, 17, 6, 6, 5, 163.456),
(41, 27, 6, 12, 6, 165.678),
(42, 3, 7, 4, 2, 312.456),
(43, 7, 7, 11, 1, 309.234),
(44, 11, 7, 14, 3, 315.678),
(45, 15, 7, 2, 4, 318.890),
(46, 19, 7, 7, 5, 321.123),
(47, 2, 8, 5, 3, 201.567),
(48, 6, 8, 10, 1, 198.234),
(49, 10, 8, 16, 2, 199.890),
(50, 14, 8, 19, 4, 203.456),
(51, 18, 8, 22, 5, 205.678),
(52, 22, 8, 3, 6, 207.890),
(53, 28, 8, 8, 7, 210.123),
(54, 1, 9, 11, 3, 234.567),
(55, 5, 9, 14, 1, 231.234),
(56, 9, 9, 17, 2, 232.890),
(57, 13, 9, 1, 4, 236.456),
(58, 17, 9, 4, 5, 238.678),
(59, 21, 9, 7, 6, 240.890),
(60, 3, 10, 6, 2, 223.456),
(61, 7, 10, 12, 1, 220.234),
(62, 11, 10, 15, 4, 226.890),
(63, 15, 10, 18, 3, 225.567),
(64, 19, 10, 21, 5, 228.123),
(65, 23, 10, 2, 6, 230.456),
(66, 29, 10, 9, 7, 233.678),
(67, 2, 11, 3, 1, 134.234),
(68, 6, 11, 8, 3, 137.890),
(69, 10, 11, 13, 2, 136.567),
(70, 14, 11, 16, 4, 139.123),
(71, 18, 11, 20, 5, 141.456),
(72, 22, 11, 23, 6, 143.678),
(73, 1, 12, 4, 4, 207.890),
(74, 5, 12, 10, 2, 203.456),
(75, 9, 12, 14, 1, 201.234),
(76, 13, 12, 17, 3, 205.678),
(77, 17, 12, 1, 5, 210.123),
(78, 21, 12, 5, 6, 212.456),
(79, 25, 12, 11, 7, 214.789),
(80, 30, 12, 19, 8, 217.123),
(81, 3, 13, 2, 3, 301.678),
(82, 7, 13, 7, 1, 297.234),
(83, 11, 13, 11, 2, 299.456),
(84, 15, 13, 14, 4, 304.890),
(85, 2, 14, 6, 2, 154.567),
(86, 4, 14, 9, 1, 152.234),
(87, 8, 14, 15, 3, 156.890),
(88, 12, 14, 18, 4, 158.123),
(89, 16, 14, 21, 5, 160.456),
(90, 20, 14, 24, 6, 162.789),
(91, 1, 15, 5, 2, 237.456),
(92, 5, 15, 11, 1, 234.234),
(93, 9, 15, 14, 3, 239.678),
(94, 13, 15, 17, 4, 242.890),
(95, 17, 15, 2, 5, 245.123),
(96, 2, 16, 3, 1, 193.234),
(97, 6, 16, 8, 2, 195.567),
(98, 10, 16, 12, 3, 197.890),
(99, 14, 16, 16, 4, 200.123),
(100, 3, 17, 4, 2, 161.456),
(101, 7, 17, 10, 1, 159.234),
(102, 11, 17, 15, 3, 163.789),
(103, 15, 17, 19, 4, 166.012),
(104, 5, 18, 1, 1, 323.456),
(105, 9, 18, 7, 2, 326.789),
(106, 13, 18, 11, 3, 329.123),
(107, 17, 18, 14, 4, 332.456),
(108, 1, 19, 2, 3, 228.789),
(109, 8, 19, 9, 1, 224.456),
(110, 12, 19, 13, 2, 226.123),
(111, 16, 19, 17, 4, 231.456),
(112, 4, 20, 6, 1, 149.234),
(113, 10, 20, 12, 2, 151.567),
(114, 14, 20, 18, 3, 153.890),
(115, 18, 20, 21, 4, 156.123),
(116, 3, 21, 4, 1, 285.234),
(117, 7, 21, 11, 2, 288.567),
(118, 11, 21, 14, 3, 291.890),
(119, 15, 21, 2, 4, 294.123),
(120, 2, 22, 5, 2, 196.456),
(121, 6, 22, 10, 1, 193.234),
(122, 12, 22, 16, 3, 198.789),
(123, 20, 22, 3, 4, 201.123),
(124, 5, 23, 8, 1, 184.567),
(125, 9, 23, 13, 2, 186.890),
(126, 13, 23, 17, 3, 189.234),
(127, 17, 23, 22, 4, 191.567),
(128, 1, 24, 7, 2, 199.789),
(129, 8, 24, 12, 1, 197.456),
(130, 16, 24, 15, 3, 202.123),
(131, 24, 24, 20, 4, 204.456),
(132, 3, 25, 4, 1, 308.234),
(133, 7, 25, 11, 2, 311.567),
(134, 11, 25, 1, 3, 314.890),
(135, 19, 25, 14, 4, 317.234);

-- --------------------------------------------------------

--
-- Table structure for table `Player`
--

DROP TABLE IF EXISTS `Player`;
CREATE TABLE `Player` (
  `PlayerID` int(11) NOT NULL,
  `TotalRaces` int(11) NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `Player`
--

INSERT INTO `Player` (`PlayerID`, `TotalRaces`) VALUES
(1, 8),
(2, 8),
(3, 9),
(4, 5),
(5, 9),
(6, 7),
(7, 9),
(8, 6),
(9, 8),
(10, 6),
(11, 7),
(12, 5),
(13, 7),
(14, 5),
(15, 6),
(16, 4),
(17, 6),
(18, 3),
(19, 3),
(20, 2),
(21, 2),
(22, 2),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `PlayerAchievement`
--

DROP TABLE IF EXISTS `PlayerAchievement`;
CREATE TABLE `PlayerAchievement` (
  `PlayerID` int(11) NOT NULL,
  `AchievementID` int(11) NOT NULL,
  `DateEarned` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `PlayerAchievement`
--

INSERT INTO `PlayerAchievement` (`PlayerID`, `AchievementID`, `DateEarned`) VALUES
(5, 1, '2025-01-05 08:30:00'),
(3, 1, '2025-01-10 09:45:00'),
(1, 1, '2025-01-15 10:30:00'),
(7, 1, '2025-01-18 10:45:00'),
(2, 1, '2025-01-20 11:15:00'),
(11, 1, '2025-01-22 10:15:00'),
(6, 1, '2025-01-25 09:15:00'),
(13, 1, '2025-01-28 09:00:00'),
(9, 1, '2025-01-30 11:00:00'),
(4, 1, '2025-02-05 10:00:00'),
(8, 1, '2025-02-10 08:00:00'),
(10, 1, '2025-02-15 09:30:00'),
(12, 1, '2025-02-20 11:30:00'),
(14, 1, '2025-02-25 10:45:00'),
(3, 2, '2025-02-28 13:20:00'),
(15, 1, '2025-03-05 12:15:00'),
(5, 2, '2025-03-15 11:45:00'),
(1, 5, '2025-03-20 14:45:00'),
(9, 2, '2025-04-10 13:45:00'),
(2, 3, '2025-04-15 15:30:00'),
(5, 6, '2025-04-20 13:30:00'),
(7, 3, '2025-05-05 13:15:00'),
(6, 6, '2025-05-10 11:30:00'),
(1, 6, '2025-05-10 16:20:00'),
(3, 6, '2025-05-15 12:00:00'),
(11, 6, '2025-05-15 12:45:00'),
(4, 5, '2025-05-20 14:15:00'),
(8, 5, '2025-05-25 10:30:00'),
(13, 6, '2025-05-25 11:15:00'),
(5, 7, '2025-05-25 15:00:00'),
(9, 6, '2025-06-05 15:15:00'),
(1, 8, '2025-06-05 18:30:00'),
(14, 8, '2025-06-08 13:00:00'),
(3, 7, '2025-06-10 14:30:00'),
(11, 7, '2025-06-10 14:30:00'),
(5, 8, '2025-06-10 16:45:00'),
(16, 11, '2025-06-15 05:30:00'),
(7, 6, '2025-06-15 15:30:00'),
(2, 7, '2025-06-15 17:45:00'),
(5, 10, '2025-06-15 18:20:00'),
(17, 12, '2025-06-16 00:30:00'),
(18, 11, '2025-06-17 05:45:00'),
(19, 12, '2025-06-18 01:15:00'),
(3, 9, '2025-06-18 16:15:00'),
(20, 13, '2025-06-19 15:00:00'),
(10, 8, '2025-06-20 12:00:00'),
(21, 14, '2025-06-20 16:30:00'),
(3, 20, '2025-06-20 18:00:00'),
(5, 20, '2025-06-20 19:30:00'),
(22, 15, '2025-06-21 17:45:00'),
(7, 8, '2025-06-22 17:00:00'),
(23, 17, '2025-06-22 18:30:00'),
(24, 18, '2025-06-23 19:15:00'),
(25, 16, '2025-06-23 20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `PlayerCredentials`
--

DROP TABLE IF EXISTS `PlayerCredentials`;
CREATE TABLE `PlayerCredentials` (
  `PlayerID` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL
) ;

--
-- Dumping data for table `PlayerCredentials`
--

INSERT INTO `PlayerCredentials` (`PlayerID`, `UserName`, `Email`) VALUES
(1, 'SpeedDemon', 'speeddemon@email.com'),
(2, 'DriftKing', 'driftking@email.com'),
(3, 'NitroQueen', 'nitroqueen@email.com'),
(4, 'RaceAce', 'raceace@email.com'),
(5, 'TurboTiger', 'turbotiger@email.com'),
(6, 'LightningBolt', 'lightning@email.com'),
(7, 'PhantomRacer', 'phantom@email.com'),
(8, 'VelocityViper', 'viper@email.com'),
(9, 'ThunderStrike', 'thunder@email.com'),
(10, 'RocketMan', 'rocket@email.com'),
(11, 'SonicSpeed', 'sonic@email.com'),
(12, 'FlashDrive', 'flash@email.com'),
(13, 'BlazeRunner', 'blaze@email.com'),
(14, 'StormChaser', 'storm@email.com'),
(15, 'NeonNinja', 'neon@email.com'),
(16, 'CyberRacer', 'cyber@email.com'),
(17, 'QuantumLeap', 'quantum@email.com'),
(18, 'AtomicDrift', 'atomic@email.com'),
(19, 'LaserFocus', 'laser@email.com'),
(20, 'PulseDriver', 'pulse@email.com'),
(21, 'ChromeSpeed', 'chrome@email.com'),
(22, 'TitaniumTurbo', 'titanium@email.com'),
(23, 'DiamondDash', 'diamond@email.com'),
(24, 'PlatinumPilot', 'platinum@email.com'),
(25, 'GoldenGear', 'golden@email.com');

-- --------------------------------------------------------

--
-- Table structure for table `Race`
--

DROP TABLE IF EXISTS `Race`;
CREATE TABLE `Race` (
  `RaceID` int(11) NOT NULL,
  `RaceName` varchar(100) NOT NULL,
  `RaceDate` datetime NOT NULL DEFAULT current_timestamp(),
  `TrackName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Race`
--

INSERT INTO `Race` (`RaceID`, `RaceName`, `RaceDate`, `TrackName`) VALUES
(1, 'Morning Cup - Sunny Circuit', '2025-06-17 09:30:00', 'Sunny Circuit'),
(2, 'Expert Challenge - Rainbow Road', '2025-06-17 14:15:00', 'Rainbow Road'),
(3, 'Desert Rally', '2025-06-17 19:45:00', 'Desert Dash'),
(4, 'Ice Challenge', '2025-06-18 10:00:00', 'Ice Crystal Valley'),
(5, 'Neon Night Race', '2025-06-18 20:30:00', 'Neon City'),
(6, 'Forest Sprint', '2025-06-18 15:20:00', 'Forest Trail'),
(7, 'Volcano Extreme', '2025-06-19 11:30:00', 'Volcano Peak'),
(8, 'Ocean Classic', '2025-06-19 16:45:00', 'Ocean Drive'),
(9, 'Space Race Alpha', '2025-06-19 21:00:00', 'Space Station'),
(10, 'Temple Run', '2025-06-20 09:15:00', 'Ancient Temple'),
(11, 'Cloud Cup', '2025-06-20 13:30:00', 'Cloud Kingdom'),
(12, 'Underground GP', '2025-06-20 18:00:00', 'Underground Tunnel'),
(13, 'Dragon Challenge', '2025-06-21 10:30:00', 'Dragon Canyon'),
(14, 'Blossom Festival Race', '2025-06-21 14:45:00', 'Cherry Blossom Lane'),
(15, 'Cyber Grand Prix', '2025-06-21 20:15:00', 'Cyber Highway'),
(16, 'Mountain Mayhem', '2025-06-22 11:00:00', 'Mystic Mountain'),
(17, 'Pirate Race', '2025-06-22 15:30:00', 'Pirates Cove'),
(18, 'Storm Masters', '2025-06-22 19:00:00', 'Thunder Storm Track'),
(19, 'Crystal Cup', '2025-06-23 10:45:00', 'Crystal Caves'),
(20, 'Sunset Speedway', '2025-06-23 17:30:00', 'Sunset Beach'),
(21, 'Rainbow Return', '2025-06-23 21:15:00', 'Rainbow Road'),
(22, 'Desert Duel', '2025-06-24 09:00:00', 'Desert Dash'),
(23, 'Neon Nights II', '2025-06-24 14:30:00', 'Neon City'),
(24, 'Ocean Sprint', '2025-06-24 18:45:00', 'Ocean Drive'),
(25, 'Final Challenge', '2025-06-24 22:00:00', 'Volcano Peak');

-- --------------------------------------------------------

--
-- Table structure for table `RegisteredPlayer`
--

DROP TABLE IF EXISTS `RegisteredPlayer`;
CREATE TABLE `RegisteredPlayer` (
  `PlayerID` int(11) NOT NULL,
  `ProfilePicture` varchar(255) DEFAULT 'default_avatar.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `RegisteredPlayer`
--

INSERT INTO `RegisteredPlayer` (`PlayerID`, `ProfilePicture`) VALUES
(1, 'avatars/speed_demon.png'),
(2, 'avatars/drift_king.png'),
(3, 'avatars/nitro_queen.png'),
(4, 'default_avatar.png'),
(5, 'avatars/turbo_tiger.png'),
(6, 'avatars/lightning.png'),
(7, 'avatars/phantom.png'),
(8, 'default_avatar.png'),
(9, 'avatars/thunder.png'),
(10, 'avatars/rocket.png'),
(11, 'default_avatar.png'),
(12, 'avatars/flash.png'),
(13, 'avatars/blaze.png'),
(14, 'default_avatar.png'),
(15, 'avatars/neon.png'),
(16, 'avatars/cyber.png'),
(17, 'default_avatar.png'),
(18, 'avatars/atomic.png'),
(19, 'avatars/laser.png'),
(20, 'default_avatar.png'),
(21, 'avatars/chrome.png'),
(22, 'avatars/titanium.png'),
(23, 'default_avatar.png'),
(24, 'avatars/platinum.png'),
(25, 'avatars/golden.png');

-- --------------------------------------------------------

--
-- Table structure for table `SpeedKart`
--

DROP TABLE IF EXISTS `SpeedKart`;
CREATE TABLE `SpeedKart` (
  `KartID` int(11) NOT NULL,
  `TopSpeedBonus` int(11) NOT NULL DEFAULT 10
) ;

--
-- Dumping data for table `SpeedKart`
--

INSERT INTO `SpeedKart` (`KartID`, `TopSpeedBonus`) VALUES
(1, 25),
(2, 22),
(3, 20),
(4, 30),
(5, 28),
(6, 15),
(7, 23),
(8, 26),
(9, 18),
(10, 21),
(11, 29),
(12, 24),
(13, 20),
(14, 27),
(15, 19),
(16, 23),
(17, 21),
(18, 25),
(19, 17),
(20, 14);

-- --------------------------------------------------------

--
-- Table structure for table `Track`
--

DROP TABLE IF EXISTS `Track`;
CREATE TABLE `Track` (
  `TrackName` varchar(100) NOT NULL,
  `TrackDifficulty` enum('Easy','Medium','Hard','Expert') NOT NULL,
  `TrackLength` decimal(5,2) NOT NULL
) ;

--
-- Dumping data for table `Track`
--

INSERT INTO `Track` (`TrackName`, `TrackDifficulty`, `TrackLength`) VALUES
('Ancient Temple', 'Hard', 4.00),
('Cherry Blossom Lane', 'Easy', 2.70),
('Cloud Kingdom', 'Easy', 2.30),
('Crystal Caves', 'Hard', 4.10),
('Cyber Highway', 'Hard', 4.30),
('Desert Dash', 'Medium', 3.80),
('Dragon Canyon', 'Expert', 5.50),
('Forest Trail', 'Easy', 2.80),
('Ice Crystal Valley', 'Hard', 4.50),
('Mystic Mountain', 'Medium', 3.40),
('Neon City', 'Medium', 3.20),
('Ocean Drive', 'Medium', 3.50),
('Pirates Cove', 'Easy', 2.90),
('Rainbow Road', 'Expert', 5.20),
('Space Station', 'Hard', 4.20),
('Sunny Circuit', 'Easy', 2.50),
('Sunset Beach', 'Easy', 2.60),
('Thunder Storm Track', 'Expert', 5.90),
('Underground Tunnel', 'Medium', 3.60),
('Volcano Peak', 'Expert', 5.80);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Achievement`
--
ALTER TABLE `Achievement`
  ADD PRIMARY KEY (`AchievementID`),
  ADD UNIQUE KEY `AchievementName` (`AchievementName`);

--
-- Indexes for table `GuestPlayer`
--
ALTER TABLE `GuestPlayer`
  ADD PRIMARY KEY (`PlayerID`),
  ADD UNIQUE KEY `SessionID` (`SessionID`);

--
-- Indexes for table `ItemKart`
--
ALTER TABLE `ItemKart`
  ADD PRIMARY KEY (`KartID`);

--
-- Indexes for table `Kart`
--
ALTER TABLE `Kart`
  ADD PRIMARY KEY (`KartID`),
  ADD UNIQUE KEY `KartName` (`KartName`);

--
-- Indexes for table `KartDetails`
--
ALTER TABLE `KartDetails`
  ADD PRIMARY KEY (`KartName`);

--
-- Indexes for table `LapRecord`
--
ALTER TABLE `LapRecord`
  ADD PRIMARY KEY (`ParticipationID`,`LapNumber`);

--
-- Indexes for table `Participation`
--
ALTER TABLE `Participation`
  ADD PRIMARY KEY (`ParticipationID`),
  ADD UNIQUE KEY `unique_player_race` (`PlayerID`,`RaceID`),
  ADD KEY `RaceID` (`RaceID`),
  ADD KEY `KartID` (`KartID`),
  ADD KEY `idx_player_performance` (`PlayerID`,`FinishingRank`);

--
-- Indexes for table `Player`
--
ALTER TABLE `Player`
  ADD PRIMARY KEY (`PlayerID`);

--
-- Indexes for table `PlayerAchievement`
--
ALTER TABLE `PlayerAchievement`
  ADD PRIMARY KEY (`PlayerID`,`AchievementID`),
  ADD KEY `AchievementID` (`AchievementID`),
  ADD KEY `idx_date_earned` (`DateEarned`);

--
-- Indexes for table `PlayerCredentials`
--
ALTER TABLE `PlayerCredentials`
  ADD PRIMARY KEY (`PlayerID`),
  ADD UNIQUE KEY `UserName` (`UserName`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `Race`
--
ALTER TABLE `Race`
  ADD PRIMARY KEY (`RaceID`),
  ADD KEY `TrackName` (`TrackName`),
  ADD KEY `idx_race_date` (`RaceDate`);

--
-- Indexes for table `RegisteredPlayer`
--
ALTER TABLE `RegisteredPlayer`
  ADD PRIMARY KEY (`PlayerID`);

--
-- Indexes for table `SpeedKart`
--
ALTER TABLE `SpeedKart`
  ADD PRIMARY KEY (`KartID`);

--
-- Indexes for table `Track`
--
ALTER TABLE `Track`
  ADD PRIMARY KEY (`TrackName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Achievement`
--
ALTER TABLE `Achievement`
  MODIFY `AchievementID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Kart`
--
ALTER TABLE `Kart`
  MODIFY `KartID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Participation`
--
ALTER TABLE `Participation`
  MODIFY `ParticipationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Player`
--
ALTER TABLE `Player`
  MODIFY `PlayerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Race`
--
ALTER TABLE `Race`
  MODIFY `RaceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `GuestPlayer`
--
ALTER TABLE `GuestPlayer`
  ADD CONSTRAINT `guestplayer_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `Player` (`PlayerID`) ON DELETE CASCADE;

--
-- Constraints for table `ItemKart`
--
ALTER TABLE `ItemKart`
  ADD CONSTRAINT `itemkart_ibfk_1` FOREIGN KEY (`KartID`) REFERENCES `Kart` (`KartID`) ON DELETE CASCADE;

--
-- Constraints for table `KartDetails`
--
ALTER TABLE `KartDetails`
  ADD CONSTRAINT `kartdetails_ibfk_1` FOREIGN KEY (`KartName`) REFERENCES `Kart` (`KartName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `LapRecord`
--
ALTER TABLE `LapRecord`
  ADD CONSTRAINT `laprecord_ibfk_1` FOREIGN KEY (`ParticipationID`) REFERENCES `Participation` (`ParticipationID`) ON DELETE CASCADE;

--
-- Constraints for table `Participation`
--
ALTER TABLE `Participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `Player` (`PlayerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`RaceID`) REFERENCES `Race` (`RaceID`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_ibfk_3` FOREIGN KEY (`KartID`) REFERENCES `Kart` (`KartID`);

--
-- Constraints for table `PlayerAchievement`
--
ALTER TABLE `PlayerAchievement`
  ADD CONSTRAINT `playerachievement_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `Player` (`PlayerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `playerachievement_ibfk_2` FOREIGN KEY (`AchievementID`) REFERENCES `Achievement` (`AchievementID`) ON DELETE CASCADE;

--
-- Constraints for table `PlayerCredentials`
--
ALTER TABLE `PlayerCredentials`
  ADD CONSTRAINT `playercredentials_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `Player` (`PlayerID`) ON DELETE CASCADE;

--
-- Constraints for table `Race`
--
ALTER TABLE `Race`
  ADD CONSTRAINT `race_ibfk_1` FOREIGN KEY (`TrackName`) REFERENCES `Track` (`TrackName`) ON UPDATE CASCADE;

--
-- Constraints for table `RegisteredPlayer`
--
ALTER TABLE `RegisteredPlayer`
  ADD CONSTRAINT `registeredplayer_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `Player` (`PlayerID`) ON DELETE CASCADE;

--
-- Constraints for table `SpeedKart`
--
ALTER TABLE `SpeedKart`
  ADD CONSTRAINT `speedkart_ibfk_1` FOREIGN KEY (`KartID`) REFERENCES `Kart` (`KartID`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
