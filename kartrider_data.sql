-- Sample Data Population for KartRider Analytics Database
USE KartRiderAnalytics;

-- Disable foreign key checks temporarily for easier data insertion
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE PlayerAchievement;
TRUNCATE TABLE LapRecord;
TRUNCATE TABLE Participation;
TRUNCATE TABLE Achievement;
TRUNCATE TABLE Race;
TRUNCATE TABLE Track;
TRUNCATE TABLE ItemKart;
TRUNCATE TABLE SpeedKart;
TRUNCATE TABLE KartDetails;
TRUNCATE TABLE Kart;
TRUNCATE TABLE GuestPlayer;
TRUNCATE TABLE RegisteredPlayer;
TRUNCATE TABLE PlayerCredentials;
TRUNCATE TABLE Player;

-- 1. Insert Players (30 players: 25 registered, 5 guests)
INSERT INTO Player (PlayerID, TotalRaces) VALUES
(1, 245), (2, 189), (3, 567), (4, 89), (5, 1234),
(6, 456), (7, 789), (8, 234), (9, 678), (10, 345),
(11, 890), (12, 123), (13, 456), (14, 789), (15, 234),
(16, 567), (17, 890), (18, 123), (19, 456), (20, 789),
(21, 345), (22, 678), (23, 901), (24, 234), (25, 567),
(26, 12), (27, 8), (28, 15), (29, 5), (30, 20);

-- Insert Player Credentials for registered players
INSERT INTO PlayerCredentials (PlayerID, UserName, Email) VALUES
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

-- Insert Registered Players
INSERT INTO RegisteredPlayer (PlayerID, ProfilePicture) VALUES
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

-- Insert Guest Players
INSERT INTO GuestPlayer (PlayerID, SessionID) VALUES
(26, 'GUEST_2025_06_22_001'),
(27, 'GUEST_2025_06_22_002'),
(28, 'GUEST_2025_06_23_001'),
(29, 'GUEST_2025_06_23_002'),
(30, 'GUEST_2025_06_24_001');

-- 2. Insert Tracks (20 tracks with varied difficulties)
INSERT INTO Track (TrackName, TrackDifficulty, TrackLength) VALUES
('Sunny Circuit', 'Easy', 2.5),
('Rainbow Road', 'Expert', 5.2),
('Desert Dash', 'Medium', 3.8),
('Ice Crystal Valley', 'Hard', 4.5),
('Neon City', 'Medium', 3.2),
('Forest Trail', 'Easy', 2.8),
('Volcano Peak', 'Expert', 5.8),
('Ocean Drive', 'Medium', 3.5),
('Space Station', 'Hard', 4.2),
('Ancient Temple', 'Hard', 4.0),
('Cloud Kingdom', 'Easy', 2.3),
('Underground Tunnel', 'Medium', 3.6),
('Dragon Canyon', 'Expert', 5.5),
('Cherry Blossom Lane', 'Easy', 2.7),
('Cyber Highway', 'Hard', 4.3),
('Mystic Mountain', 'Medium', 3.4),
('Pirates Cove', 'Easy', 2.9),
('Thunder Storm Track', 'Expert', 5.9),
('Crystal Caves', 'Hard', 4.1),
('Sunset Beach', 'Easy', 2.6);

-- 3. Insert Karts (25 karts)
INSERT INTO Kart (KartID, KartName, MaxSpeed, Handling) VALUES
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

-- Insert Kart Details
INSERT INTO KartDetails (KartName, Manufacturer, ReleaseYear) VALUES
('Lightning X1', 'Speed Corp', 2024),
('Thunder V2', 'Speed Corp', 2025),
('Storm Rider', 'DriftTech', 2024),
('Nitro Beast', 'TurboWorks', 2025),
('Speed Phantom', 'TurboWorks', 2024),
('Drift Master', 'DriftTech', 2023),
('Turbo Hawk', 'Speed Corp', 2024),
('Velocity Pro', 'ProRacing', 2025),
('Racing Spirit', 'ProRacing', 2024),
('Neon Runner', 'NeonDrive', 2025),
('Cyber Bolt', 'CyberTech', 2025),
('Quantum Racer', 'QuantumSpeed', 2024),
('Atomic Drive', 'AtomicRacing', 2025),
('Laser Beam', 'LaserWorks', 2024),
('Chrome Speed', 'ChromeTech', 2025),
('Diamond Dash', 'LuxuryKarts', 2025),
('Platinum Wing', 'LuxuryKarts', 2024),
('Golden Arrow', 'LuxuryKarts', 2024),
('Silver Streak', 'MetalWorks', 2023),
('Bronze Bullet', 'MetalWorks', 2023),
('Item Hunter', 'ItemTech', 2024),
('Shield Guardian', 'ItemTech', 2025),
('Boost Master', 'ItemTech', 2024),
('Trap Setter', 'ItemTech', 2025),
('Power Surge', 'ItemTech', 2024);

-- Insert Speed Karts (first 20 are speed karts)
INSERT INTO SpeedKart (KartID, TopSpeedBonus) VALUES
(1, 25), (2, 22), (3, 20), (4, 30), (5, 28),
(6, 15), (7, 23), (8, 26), (9, 18), (10, 21),
(11, 29), (12, 24), (13, 20), (14, 27), (15, 19),
(16, 23), (17, 21), (18, 25), (19, 17), (20, 14);

-- Insert Item Karts (last 5 are item karts) - Modified to comply with new constraint (1-3)
INSERT INTO ItemKart (KartID, ItemSlots) VALUES
(21, 2), (22, 3), (23, 2), (24, 3), (25, 1);

-- 4. Insert Achievements (20 achievements)
INSERT INTO Achievement (AchievementID, AchievementName, Description, PointsAwarded) VALUES
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

-- 5. Insert Races (25 races around June 23, 2025)
INSERT INTO Race (RaceID, RaceName, RaceDate, TrackName) VALUES
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

-- 6. Insert Participation records (200+ records)
-- Race 1 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(1, 1, 1, 2, 142.567),
(2, 1, 5, 1, 141.234),
(3, 1, 8, 4, 145.890),
(4, 1, 12, 6, 148.234),
(5, 1, 15, 3, 143.789),
(6, 1, 3, 5, 147.123),
(7, 1, 9, 7, 149.567),
(8, 1, 20, 8, 151.234);

-- Race 2 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(3, 2, 4, 1, 287.345),
(5, 2, 1, 3, 291.567),
(7, 2, 11, 2, 289.234),
(9, 2, 14, 5, 295.678),
(11, 2, 2, 4, 293.456),
(13, 2, 7, 6, 298.123);

-- Race 3 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(2, 3, 6, 3, 198.456),
(4, 3, 10, 1, 195.234),
(6, 3, 13, 4, 199.567),
(8, 3, 16, 2, 197.890),
(10, 3, 19, 5, 201.234),
(12, 3, 3, 6, 203.456),
(14, 3, 8, 7, 205.678);

-- Race 4 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(1, 4, 2, 4, 245.678),
(3, 4, 7, 2, 241.234),
(5, 4, 11, 1, 239.567),
(7, 4, 14, 3, 243.890),
(9, 4, 17, 5, 247.123),
(15, 4, 4, 6, 249.456);

-- Race 5 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(2, 5, 21, 2, 186.789),
(4, 5, 22, 5, 191.234),
(6, 5, 23, 3, 188.567),
(8, 5, 24, 4, 189.890),
(10, 5, 25, 6, 192.345),
(12, 5, 1, 1, 185.456),
(16, 5, 5, 7, 194.567),
(26, 5, 8, 8, 196.789);

-- Continue with more races...
-- Race 6 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(1, 6, 3, 1, 156.234),
(5, 6, 9, 3, 159.567),
(9, 6, 15, 2, 158.890),
(13, 6, 18, 4, 161.234),
(17, 6, 6, 5, 163.456),
(27, 6, 12, 6, 165.678);

-- Race 7 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(3, 7, 4, 2, 312.456),
(7, 7, 11, 1, 309.234),
(11, 7, 14, 3, 315.678),
(15, 7, 2, 4, 318.890),
(19, 7, 7, 5, 321.123);

-- Race 8 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(2, 8, 5, 3, 201.567),
(6, 8, 10, 1, 198.234),
(10, 8, 16, 2, 199.890),
(14, 8, 19, 4, 203.456),
(18, 8, 22, 5, 205.678),
(22, 8, 3, 6, 207.890),
(28, 8, 8, 7, 210.123);

-- Race 9 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(1, 9, 11, 3, 234.567),
(5, 9, 14, 1, 231.234),
(9, 9, 17, 2, 232.890),
(13, 9, 1, 4, 236.456),
(17, 9, 4, 5, 238.678),
(21, 9, 7, 6, 240.890);

-- Race 10 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(3, 10, 6, 2, 223.456),
(7, 10, 12, 1, 220.234),
(11, 10, 15, 4, 226.890),
(15, 10, 18, 3, 225.567),
(19, 10, 21, 5, 228.123),
(23, 10, 2, 6, 230.456),
(29, 10, 9, 7, 233.678);

-- Race 11 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(2, 11, 3, 1, 134.234),
(6, 11, 8, 3, 137.890),
(10, 11, 13, 2, 136.567),
(14, 11, 16, 4, 139.123),
(18, 11, 20, 5, 141.456),
(22, 11, 23, 6, 143.678);

-- Race 12 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(1, 12, 4, 4, 207.890),
(5, 12, 10, 2, 203.456),
(9, 12, 14, 1, 201.234),
(13, 12, 17, 3, 205.678),
(17, 12, 1, 5, 210.123),
(21, 12, 5, 6, 212.456),
(25, 12, 11, 7, 214.789),
(30, 12, 19, 8, 217.123);

-- Race 13 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(3, 13, 2, 3, 301.678),
(7, 13, 7, 1, 297.234),
(11, 13, 11, 2, 299.456),
(15, 13, 14, 4, 304.890);

-- Race 14 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(2, 14, 6, 2, 154.567),
(4, 14, 9, 1, 152.234),
(8, 14, 15, 3, 156.890),
(12, 14, 18, 4, 158.123),
(16, 14, 21, 5, 160.456),
(20, 14, 24, 6, 162.789);

-- Race 15 participants
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
(1, 15, 5, 2, 237.456),
(5, 15, 11, 1, 234.234),
(9, 15, 14, 3, 239.678),
(13, 15, 17, 4, 242.890),
(17, 15, 2, 5, 245.123);

-- Continue with remaining races to ensure variety...
-- Race 16-25 participants (abbreviated for space, but following similar patterns)
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
-- Race 16
(2, 16, 3, 1, 193.234),
(6, 16, 8, 2, 195.567),
(10, 16, 12, 3, 197.890),
(14, 16, 16, 4, 200.123),
-- Race 17
(3, 17, 4, 2, 161.456),
(7, 17, 10, 1, 159.234),
(11, 17, 15, 3, 163.789),
(15, 17, 19, 4, 166.012),
-- Race 18
(5, 18, 1, 1, 323.456),
(9, 18, 7, 2, 326.789),
(13, 18, 11, 3, 329.123),
(17, 18, 14, 4, 332.456),
-- Race 19
(1, 19, 2, 3, 228.789),
(8, 19, 9, 1, 224.456),
(12, 19, 13, 2, 226.123),
(16, 19, 17, 4, 231.456),
-- Race 20
(4, 20, 6, 1, 149.234),
(10, 20, 12, 2, 151.567),
(14, 20, 18, 3, 153.890),
(18, 20, 21, 4, 156.123),
-- Race 21
(3, 21, 4, 1, 285.234),
(7, 21, 11, 2, 288.567),
(11, 21, 14, 3, 291.890),
(15, 21, 2, 4, 294.123),
-- Race 22
(2, 22, 5, 2, 196.456),
(6, 22, 10, 1, 193.234),
(12, 22, 16, 3, 198.789),
(20, 22, 3, 4, 201.123),
-- Race 23
(5, 23, 8, 1, 184.567),
(9, 23, 13, 2, 186.890),
(13, 23, 17, 3, 189.234),
(17, 23, 22, 4, 191.567),
-- Race 24
(1, 24, 7, 2, 199.789),
(8, 24, 12, 1, 197.456),
(16, 24, 15, 3, 202.123),
(24, 24, 20, 4, 204.456),
-- Race 25
(3, 25, 4, 1, 308.234),
(7, 25, 11, 2, 311.567),
(11, 25, 1, 3, 314.890),
(19, 25, 14, 4, 317.234);

-- 7. Insert Lap Records (sample for some participations)
-- For participation 1 (Player 1, Race 1)
INSERT INTO LapRecord (ParticipationID, LapNumber, LapTime) VALUES
(1, 1, 28.567),
(1, 2, 28.234),
(1, 3, 28.456),
(1, 4, 28.789),
(1, 5, 28.521);

-- For participation 2 (Player 2, Race 1)
INSERT INTO LapRecord (ParticipationID, LapNumber, LapTime) VALUES
(2, 1, 28.123),
(2, 2, 28.234),
(2, 3, 28.345),
(2, 4, 28.234),
(2, 5, 28.298);

-- For participation 9 (Player 3, Race 2) - Rainbow Road has 3 laps
INSERT INTO LapRecord (ParticipationID, LapNumber, LapTime) VALUES
(9, 1, 95.234),
(9, 2, 95.567),
(9, 3, 96.544);

-- For participation 15 (Player 2, Race 3)
INSERT INTO LapRecord (ParticipationID, LapNumber, LapTime) VALUES
(15, 1, 39.567),
(15, 2, 39.789),
(15, 3, 39.456),
(15, 4, 39.890),
(15, 5, 39.754);

-- Add more lap records for variety
INSERT INTO LapRecord (ParticipationID, LapNumber, LapTime) VALUES
-- Participation 22 (Player 1, Race 4)
(22, 1, 48.234),
(22, 2, 49.123),
(22, 3, 49.456),
(22, 4, 49.234),
(22, 5, 49.631);

-- 8. Insert Player Achievements
INSERT INTO PlayerAchievement (PlayerID, AchievementID, DateEarned) VALUES
-- Player 1 achievements
(1, 1, '2025-01-15 10:30:00'),
(1, 5, '2025-03-20 14:45:00'),
(1, 6, '2025-05-10 16:20:00'),
(1, 8, '2025-06-05 18:30:00'),
-- Player 2 achievements
(2, 1, '2025-01-20 11:15:00'),
(2, 3, '2025-04-15 15:30:00'),
(2, 7, '2025-06-15 17:45:00'),
-- Player 3 achievements
(3, 1, '2025-01-10 09:45:00'),
(3, 2, '2025-02-28 13:20:00'),
(3, 6, '2025-05-15 12:00:00'),
(3, 7, '2025-06-10 14:30:00'),
(3, 9, '2025-06-18 16:15:00'),
(3, 20, '2025-06-20 18:00:00'),
-- Player 4 achievements
(4, 1, '2025-02-05 10:00:00'),
(4, 5, '2025-05-20 14:15:00'),
-- Player 5 achievements  
(5, 1, '2025-01-05 08:30:00'),
(5, 2, '2025-03-15 11:45:00'),
(5, 6, '2025-04-20 13:30:00'),
(5, 7, '2025-05-25 15:00:00'),
(5, 8, '2025-06-10 16:45:00'),
(5, 10, '2025-06-15 18:20:00'),
(5, 20, '2025-06-20 19:30:00'),
-- Player 6-10 achievements
(6, 1, '2025-01-25 09:15:00'),
(6, 6, '2025-05-10 11:30:00'),
(7, 1, '2025-01-18 10:45:00'),
(7, 3, '2025-05-05 13:15:00'),
(7, 6, '2025-06-15 15:30:00'),
(7, 8, '2025-06-22 17:00:00'),
(8, 1, '2025-02-10 08:00:00'),
(8, 5, '2025-05-25 10:30:00'),
(9, 1, '2025-01-30 11:00:00'),
(9, 2, '2025-04-10 13:45:00'),
(9, 6, '2025-06-05 15:15:00'),
(10, 1, '2025-02-15 09:30:00'),
(10, 8, '2025-06-20 12:00:00'),
-- Player 11-15 achievements
(11, 1, '2025-01-22 10:15:00'),
(11, 6, '2025-05-15 12:45:00'),
(11, 7, '2025-06-10 14:30:00'),
(12, 1, '2025-02-20 11:30:00'),
(13, 1, '2025-01-28 09:00:00'),
(13, 6, '2025-05-25 11:15:00'),
(14, 1, '2025-02-25 10:45:00'),
(14, 8, '2025-06-08 13:00:00'),
(15, 1, '2025-03-05 12:15:00'),
-- Night owl and early bird achievements
(16, 11, '2025-06-15 05:30:00'),
(17, 12, '2025-06-16 00:30:00'),
(18, 11, '2025-06-17 05:45:00'),
(19, 12, '2025-06-18 01:15:00'),
-- Recent achievements (during race week)
(20, 13, '2025-06-19 15:00:00'),
(21, 14, '2025-06-20 16:30:00'),
(22, 15, '2025-06-21 17:45:00'),
(23, 17, '2025-06-22 18:30:00'),
(24, 18, '2025-06-23 19:15:00'),
(25, 16, '2025-06-23 20:00:00');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Update total races for players based on participation
UPDATE Player p
SET TotalRaces = (
    SELECT COUNT(*)
    FROM Participation par
    WHERE par.PlayerID = p.PlayerID
);

-- Verify data insertion
SELECT 'Players' as TableName, COUNT(*) as RecordCount FROM Player
UNION ALL
SELECT 'Tracks', COUNT(*) FROM Track
UNION ALL
SELECT 'Karts', COUNT(*) FROM Kart
UNION ALL
SELECT 'Races', COUNT(*) FROM Race
UNION ALL
SELECT 'Participations', COUNT(*) FROM Participation
UNION ALL
SELECT 'Achievements', COUNT(*) FROM Achievement
UNION ALL
SELECT 'Player Achievements', COUNT(*) FROM PlayerAchievement
UNION ALL
SELECT 'Lap Records', COUNT(*) FROM LapRecord;