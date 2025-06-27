-- KartRider Analytics Data Injection Script
-- Based on kartrider_ddl.sql structure

USE KartRiderAnalytics;

-- Clear existing data (in reverse order of dependencies)
DELETE FROM PlayerAchievement;
DELETE FROM LapRecord;
DELETE FROM Participation;
DELETE FROM Achievement;
DELETE FROM Race;
DELETE FROM Track;
DELETE FROM ItemKart;
DELETE FROM SpeedKart;
DELETE FROM KartDetails;
DELETE FROM Kart;
DELETE FROM GuestPlayer;
DELETE FROM RegisteredPlayer;
DELETE FROM PlayerCredentials;
DELETE FROM Player;

-- Reset auto-increment counters
ALTER TABLE Player AUTO_INCREMENT = 1;
ALTER TABLE Kart AUTO_INCREMENT = 1;
ALTER TABLE Race AUTO_INCREMENT = 1;
ALTER TABLE Achievement AUTO_INCREMENT = 1;
ALTER TABLE Participation AUTO_INCREMENT = 1;

-- 1. Insert Players (20 registered players + 10 guest players)
INSERT INTO Player (TotalRaces) VALUES 
(156), (89), (234), (67), (198), (123), (345), (78), (267), (145),
(189), (92), (312), (134), (276), (88), (201), (167), (298), (112),
(45), (23), (67), (34), (89), (12), (56), (78), (23), (41);

-- Insert Player Credentials
INSERT INTO PlayerCredentials (PlayerID, UserName, Email) VALUES
(1, 'SpeedDemon', 'speed.demon@email.com'),
(2, 'DriftKing', 'drift.king@email.com'),
(3, 'NitroQueen', 'nitro.queen@email.com'),
(4, 'TrackMaster', 'track.master@email.com'),
(5, 'RacingPro', 'racing.pro@email.com'),
(6, 'KartChampion', 'kart.champion@email.com'),
(7, 'VelocityViper', 'velocity.viper@email.com'),
(8, 'TurboTiger', 'turbo.tiger@email.com'),
(9, 'CircuitStar', 'circuit.star@email.com'),
(10, 'RacingLegend', 'racing.legend@email.com'),
(11, 'SpeedRacer', 'speed.racer@email.com'),
(12, 'DriftMaster', 'drift.master@email.com'),
(13, 'NitroNinja', 'nitro.ninja@email.com'),
(14, 'TrackHero', 'track.hero@email.com'),
(15, 'RacingElite', 'racing.elite@email.com'),
(16, 'KartWarrior', 'kart.warrior@email.com'),
(17, 'VelocityVortex', 'velocity.vortex@email.com'),
(18, 'TurboThunder', 'turbo.thunder@email.com'),
(19, 'CircuitChampion', 'circuit.champion@email.com'),
(20, 'RacingVeteran', 'racing.veteran@email.com');

-- Insert Registered Players
INSERT INTO RegisteredPlayer (PlayerID, ProfilePicture) VALUES
(1, 'speed_demon_avatar.png'),
(2, 'drift_king_avatar.png'),
(3, 'nitro_queen_avatar.png'),
(4, 'track_master_avatar.png'),
(5, 'racing_pro_avatar.png'),
(6, 'kart_champion_avatar.png'),
(7, 'velocity_viper_avatar.png'),
(8, 'turbo_tiger_avatar.png'),
(9, 'circuit_star_avatar.png'),
(10, 'racing_legend_avatar.png'),
(11, 'speed_racer_avatar.png'),
(12, 'drift_master_avatar.png'),
(13, 'nitro_ninja_avatar.png'),
(14, 'track_hero_avatar.png'),
(15, 'racing_elite_avatar.png'),
(16, 'kart_warrior_avatar.png'),
(17, 'velocity_vortex_avatar.png'),
(18, 'turbo_thunder_avatar.png'),
(19, 'circuit_champion_avatar.png'),
(20, 'racing_veteran_avatar.png');

-- Insert Guest Players
INSERT INTO GuestPlayer (PlayerID, SessionID) VALUES
(21, 'guest_session_001'),
(22, 'guest_session_002'),
(23, 'guest_session_003'),
(24, 'guest_session_004'),
(25, 'guest_session_005'),
(26, 'guest_session_006'),
(27, 'guest_session_007'),
(28, 'guest_session_008'),
(29, 'guest_session_009'),
(30, 'guest_session_010');

-- 2. Insert Tracks (20 tracks)
INSERT INTO Track (TrackName, TrackDifficulty, TrackLength) VALUES
('Forest Adventure', 'Easy', 120.50),
('Desert Storm', 'Easy', 95.75),
('City Streets', 'Medium', 150.25),
('Mountain Pass', 'Medium', 180.00),
('Beach Circuit', 'Medium', 135.80),
('Ice Valley', 'Hard', 200.50),
('Volcano Run', 'Hard', 175.30),
('Sky Highway', 'Hard', 220.75),
('Underground Tunnel', 'Expert', 250.00),
('Space Station', 'Expert', 280.25),
('Jungle Safari', 'Easy', 110.40),
('Snow Peak', 'Medium', 165.90),
('Canyon Chase', 'Hard', 195.60),
('Neon City', 'Medium', 145.30),
('Ancient Temple', 'Expert', 265.80),
('Ocean Drive', 'Easy', 125.70),
('Thunder Ridge', 'Hard', 185.45),
('Crystal Cave', 'Expert', 275.20),
('Sunset Boulevard', 'Medium', 155.90),
('Cyber Circuit', 'Expert', 290.10);

-- 3. Insert Karts (20 karts - 10 Speed, 10 Item)
INSERT INTO Kart (KartName, MaxSpeed, Handling) VALUES
-- Speed Karts
('Lightning Bolt', 180, 85),
('Thunder Storm', 175, 80),
('Velocity X', 185, 75),
('Speed Demon', 190, 70),
('Rapid Fire', 170, 90),
('Turbo Charger', 185, 78),
('Nitro Boost', 180, 82),
('Supersonic', 195, 65),
('Rocket Racer', 175, 88),
('Speed Phantom', 185, 72),
-- Item Karts
('Item Hunter', 150, 95),
('Power Collector', 145, 98),
('Magic Box', 155, 90),
('Treasure Seeker', 140, 100),
('Lucky Charm', 150, 92),
('Fortune Finder', 145, 96),
('Mystery Machine', 155, 88),
('Wish Granter', 140, 99),
('Lucky Star', 150, 94),
('Treasure Trove', 145, 97);

-- Insert Kart Details
INSERT INTO KartDetails (KartName, Manufacturer, ReleaseYear) VALUES
('Lightning Bolt', 'SpeedTech', 2020),
('Thunder Storm', 'Velocity Motors', 2019),
('Velocity X', 'SpeedTech', 2021),
('Speed Demon', 'Racing Dynamics', 2018),
('Rapid Fire', 'Velocity Motors', 2022),
('Turbo Charger', 'SpeedTech', 2020),
('Nitro Boost', 'Racing Dynamics', 2021),
('Supersonic', 'Velocity Motors', 2023),
('Rocket Racer', 'SpeedTech', 2019),
('Speed Phantom', 'Racing Dynamics', 2022),
('Item Hunter', 'Magic Motors', 2020),
('Power Collector', 'Lucky Racing', 2019),
('Magic Box', 'Magic Motors', 2021),
('Treasure Seeker', 'Lucky Racing', 2018),
('Lucky Charm', 'Magic Motors', 2022),
('Fortune Finder', 'Lucky Racing', 2020),
('Mystery Machine', 'Magic Motors', 2021),
('Wish Granter', 'Lucky Racing', 2023),
('Lucky Star', 'Magic Motors', 2019),
('Treasure Trove', 'Lucky Racing', 2022);

-- Insert Speed Karts
INSERT INTO SpeedKart (KartID, TopSpeedBonus) VALUES
(1, 15), (2, 12), (3, 18), (4, 20), (5, 10),
(6, 16), (7, 14), (8, 25), (9, 11), (10, 17);

-- Insert Item Karts
INSERT INTO ItemKart (KartID, ItemSlots) VALUES
(11, 3), (12, 3), (13, 2), (14, 3), (15, 2),
(16, 3), (17, 2), (18, 3), (19, 2), (20, 3);

-- 4. Insert Races (20 races)
INSERT INTO Race (RaceName, RaceDate, TrackName) VALUES
('Spring Championship Round 1', '2024-03-15 14:00:00', 'Forest Adventure'),
('Desert Challenge Cup', '2024-03-16 15:30:00', 'Desert Storm'),
('Urban Racing League', '2024-03-17 16:00:00', 'City Streets'),
('Mountain Masters', '2024-03-18 17:00:00', 'Mountain Pass'),
('Beach Bash Tournament', '2024-03-19 18:30:00', 'Beach Circuit'),
('Ice Racing Championship', '2024-03-20 19:00:00', 'Ice Valley'),
('Volcano Challenge', '2024-03-21 20:00:00', 'Volcano Run'),
('Sky High Tournament', '2024-03-22 21:00:00', 'Sky Highway'),
('Underground Classic', '2024-03-23 22:00:00', 'Underground Tunnel'),
('Space Race Elite', '2024-03-24 23:00:00', 'Space Station'),
('Jungle Safari Cup', '2024-03-25 14:30:00', 'Jungle Safari'),
('Snow Peak Challenge', '2024-03-26 15:00:00', 'Snow Peak'),
('Canyon Masters', '2024-03-27 16:30:00', 'Canyon Chase'),
('Neon City Nights', '2024-03-28 17:30:00', 'Neon City'),
('Temple Run Classic', '2024-03-29 18:00:00', 'Ancient Temple'),
('Ocean Drive Open', '2024-03-30 19:30:00', 'Ocean Drive'),
('Thunder Ridge Rally', '2024-03-31 20:30:00', 'Thunder Ridge'),
('Crystal Cave Challenge', '2024-04-01 21:30:00', 'Crystal Cave'),
('Sunset Boulevard Classic', '2024-04-02 22:30:00', 'Sunset Boulevard'),
('Cyber Circuit Championship', '2024-04-03 23:30:00', 'Cyber Circuit');

-- 5. Insert Achievements (15 achievements)
INSERT INTO Achievement (AchievementName, Description, PointsAwarded) VALUES
('First Victory', 'Win your first race', 50),
('Speed Demon', 'Complete a race with average speed over 150 km/h', 100),
('Perfect Lap', 'Complete a lap in under 30 seconds', 150),
('Item Master', 'Use 10 items in a single race', 75),
('Track Explorer', 'Race on 10 different tracks', 200),
('Consistent Racer', 'Finish in top 3 for 5 consecutive races', 300),
('Comeback King', 'Win a race after starting in last place', 250),
('Speed Collector', 'Win races with 5 different speed karts', 175),
('Item Hunter', 'Win races with 5 different item karts', 175),
('Marathon Runner', 'Complete 100 races', 500),
('Track Master', 'Win on all 20 tracks', 1000),
('Racing Legend', 'Achieve 50 victories', 750),
('Speed Champion', 'Win 10 races with speed karts', 400),
('Item Champion', 'Win 10 races with item karts', 400),
('Ultimate Racer', 'Complete all achievements', 2000);

-- 6. Insert Participation Records (200+ participation records)
-- This will create realistic race participation data
INSERT INTO Participation (PlayerID, RaceID, KartID, FinishingRank, TotalTime) VALUES
-- Race 1 participants
(1, 1, 1, 1, 145.250), (2, 1, 2, 2, 147.180), (3, 1, 3, 3, 148.920),
(4, 1, 4, 4, 150.450), (5, 1, 5, 5, 152.100), (6, 1, 6, 6, 153.750),
(7, 1, 7, 7, 155.300), (8, 1, 8, 8, 157.100),

-- Race 2 participants
(2, 2, 2, 1, 120.500), (3, 2, 3, 2, 122.300), (4, 2, 4, 3, 124.150),
(5, 2, 5, 4, 125.800), (6, 2, 6, 5, 127.450), (7, 2, 7, 6, 129.200),
(8, 2, 8, 7, 131.100), (9, 2, 9, 8, 133.500),

-- Race 3 participants
(3, 3, 3, 1, 180.750), (4, 3, 4, 2, 182.400), (5, 3, 5, 3, 184.200),
(6, 3, 6, 4, 186.100), (7, 3, 7, 5, 188.300), (8, 3, 8, 6, 190.500),
(9, 3, 9, 7, 192.800), (10, 3, 10, 8, 195.200),

-- Race 4 participants
(4, 4, 4, 1, 220.100), (5, 4, 5, 2, 222.500), (6, 4, 6, 3, 224.800),
(7, 4, 7, 4, 227.200), (8, 4, 8, 5, 229.600), (9, 4, 9, 6, 232.100),
(10, 4, 10, 7, 234.500), (11, 4, 11, 8, 237.000),

-- Race 5 participants
(5, 5, 5, 1, 165.300), (6, 5, 6, 2, 167.800), (7, 5, 7, 3, 170.200),
(8, 5, 8, 4, 172.600), (9, 5, 9, 5, 175.100), (10, 5, 10, 6, 177.500),
(11, 5, 11, 7, 180.000), (12, 5, 12, 8, 182.400),

-- Race 6 participants
(6, 6, 6, 1, 240.500), (7, 6, 7, 2, 243.200), (8, 6, 8, 3, 245.800),
(9, 6, 9, 4, 248.500), (10, 6, 10, 5, 251.200), (11, 6, 11, 6, 253.800),
(12, 6, 12, 7, 256.500), (13, 6, 13, 8, 259.200),

-- Race 7 participants
(7, 7, 7, 1, 210.800), (8, 7, 8, 2, 213.500), (9, 7, 9, 3, 216.200),
(10, 7, 10, 4, 218.800), (11, 7, 11, 5, 221.500), (12, 7, 12, 6, 224.200),
(13, 7, 13, 7, 226.800), (14, 7, 14, 8, 229.500),

-- Race 8 participants
(8, 8, 8, 1, 265.100), (9, 8, 9, 2, 268.200), (10, 8, 10, 3, 271.300),
(11, 8, 11, 4, 274.400), (12, 8, 12, 5, 277.500), (13, 8, 13, 6, 280.600),
(14, 8, 14, 7, 283.700), (15, 8, 15, 8, 286.800),

-- Race 9 participants
(9, 9, 9, 1, 300.500), (10, 9, 10, 2, 304.200), (11, 9, 11, 3, 307.800),
(12, 9, 12, 4, 311.500), (13, 9, 13, 5, 315.200), (14, 9, 14, 6, 318.800),
(15, 9, 15, 7, 322.500), (16, 9, 16, 8, 326.200),

-- Race 10 participants
(10, 10, 10, 1, 335.800), (11, 10, 11, 2, 340.100), (12, 10, 12, 3, 344.400),
(13, 10, 13, 4, 348.700), (14, 10, 14, 5, 353.000), (15, 10, 15, 6, 357.300),
(16, 10, 16, 7, 361.600), (17, 10, 17, 8, 365.900),

-- Race 11 participants
(11, 11, 11, 1, 135.200), (12, 11, 12, 2, 137.800), (13, 11, 13, 3, 140.400),
(14, 11, 14, 4, 143.000), (15, 11, 15, 5, 145.600), (16, 11, 16, 6, 148.200),
(17, 11, 17, 7, 150.800), (18, 11, 18, 8, 153.400),

-- Race 12 participants
(12, 12, 12, 1, 200.500), (13, 12, 13, 2, 203.800), (14, 12, 14, 3, 207.100),
(15, 12, 15, 4, 210.400), (16, 12, 16, 5, 213.700), (17, 12, 17, 6, 217.000),
(18, 12, 18, 7, 220.300), (19, 12, 19, 8, 223.600),

-- Race 13 participants
(13, 13, 13, 1, 235.800), (14, 13, 14, 2, 239.500), (15, 13, 15, 3, 243.200),
(16, 13, 16, 4, 246.900), (17, 13, 17, 5, 250.600), (18, 13, 18, 6, 254.300),
(19, 13, 19, 7, 258.000), (20, 13, 20, 8, 261.700),

-- Race 14 participants
(14, 14, 14, 1, 175.300), (15, 14, 15, 2, 178.600), (16, 14, 16, 3, 181.900),
(17, 14, 17, 4, 185.200), (18, 14, 18, 5, 188.500), (19, 14, 19, 6, 191.800),
(20, 14, 20, 7, 195.100), (1, 14, 1, 8, 198.400),

-- Race 15 participants
(15, 15, 15, 1, 315.500), (16, 15, 16, 2, 320.200), (17, 15, 17, 3, 324.900),
(18, 15, 18, 4, 329.600), (19, 15, 19, 5, 334.300), (20, 15, 20, 6, 339.000),
(1, 15, 1, 7, 343.700), (2, 15, 2, 8, 348.400),

-- Race 16 participants
(16, 16, 16, 1, 150.800), (17, 16, 17, 2, 153.500), (18, 16, 18, 3, 156.200),
(19, 16, 19, 4, 158.900), (20, 16, 20, 5, 161.600), (1, 16, 1, 6, 164.300),
(2, 16, 2, 7, 167.000), (3, 16, 3, 8, 169.700),

-- Race 17 participants
(17, 17, 17, 1, 225.100), (18, 17, 18, 2, 228.800), (19, 17, 19, 3, 232.500),
(20, 17, 20, 4, 236.200), (1, 17, 1, 5, 239.900), (2, 17, 2, 6, 243.600),
(3, 17, 3, 7, 247.300), (4, 17, 4, 8, 251.000),

-- Race 18 participants
(18, 18, 18, 1, 325.800), (19, 18, 19, 2, 330.500), (20, 18, 20, 3, 335.200),
(1, 18, 1, 4, 339.900), (2, 18, 2, 5, 344.600), (3, 18, 3, 6, 349.300),
(4, 18, 4, 7, 354.000), (5, 18, 5, 8, 358.700),

-- Race 19 participants
(19, 19, 19, 1, 190.500), (20, 19, 20, 2, 193.800), (1, 19, 1, 3, 197.100),
(2, 19, 2, 4, 200.400), (3, 19, 3, 5, 203.700), (4, 19, 4, 6, 207.000),
(5, 19, 5, 7, 210.300), (6, 19, 6, 8, 213.600),

-- Race 20 participants
(20, 20, 20, 1, 345.200), (1, 20, 1, 2, 350.100), (2, 20, 2, 3, 355.000),
(3, 20, 3, 4, 359.900), (4, 20, 4, 5, 364.800), (5, 20, 5, 6, 369.700),
(6, 20, 6, 7, 374.600), (7, 20, 7, 8, 379.500);

-- 7. Insert Lap Records (sample lap records for some participations)
INSERT INTO LapRecord (ParticipationID, LapNumber, LapTime) VALUES
-- Race 1 Winner's laps
(1, 1, 28.500), (1, 2, 29.200), (1, 3, 28.800), (1, 4, 28.750),
-- Race 2 Winner's laps
(9, 1, 24.100), (9, 2, 24.500), (9, 3, 24.200), (9, 4, 24.300),
-- Race 3 Winner's laps
(17, 1, 36.200), (17, 2, 36.800), (17, 3, 36.500), (17, 4, 36.400),
-- Race 4 Winner's laps
(25, 1, 44.000), (25, 2, 44.500), (25, 3, 44.200), (25, 4, 44.300),
-- Race 5 Winner's laps
(33, 1, 33.100), (33, 2, 33.500), (33, 3, 33.200), (33, 4, 33.300),
-- Race 6 Winner's laps
(41, 1, 48.100), (41, 2, 48.500), (41, 3, 48.200), (41, 4, 48.300),
-- Race 7 Winner's laps
(49, 1, 42.200), (49, 2, 42.800), (49, 3, 42.500), (49, 4, 42.400),
-- Race 8 Winner's laps
(57, 1, 53.000), (57, 2, 53.500), (57, 3, 53.200), (57, 4, 53.300),
-- Race 9 Winner's laps
(65, 1, 60.100), (65, 2, 60.500), (65, 3, 60.200), (65, 4, 60.300),
-- Race 10 Winner's laps
(73, 1, 67.200), (73, 2, 67.800), (73, 3, 67.500), (73, 4, 67.400);

-- 8. Insert Player Achievements (sample achievements for players)
INSERT INTO PlayerAchievement (PlayerID, AchievementID, DateEarned) VALUES
-- Player 1 achievements
(1, 1, '2024-03-15 14:05:00'), (1, 2, '2024-03-16 15:35:00'), (1, 5, '2024-03-20 19:05:00'),
-- Player 2 achievements
(2, 1, '2024-03-16 15:35:00'), (2, 3, '2024-03-17 16:05:00'), (2, 6, '2024-03-25 14:35:00'),
-- Player 3 achievements
(3, 1, '2024-03-17 16:05:00'), (3, 4, '2024-03-18 17:05:00'), (3, 7, '2024-03-26 15:05:00'),
-- Player 4 achievements
(4, 1, '2024-03-18 17:05:00'), (4, 2, '2024-03-19 18:35:00'), (4, 8, '2024-03-27 16:35:00'),
-- Player 5 achievements
(5, 1, '2024-03-19 18:35:00'), (5, 3, '2024-03-20 19:05:00'), (5, 9, '2024-03-28 17:35:00'),
-- Player 6 achievements
(6, 1, '2024-03-20 19:05:00'), (6, 4, '2024-03-21 20:05:00'), (6, 10, '2024-03-29 18:05:00'),
-- Player 7 achievements
(7, 1, '2024-03-21 20:05:00'), (7, 2, '2024-03-22 21:05:00'), (7, 11, '2024-03-30 19:35:00'),
-- Player 8 achievements
(8, 1, '2024-03-22 21:05:00'), (8, 3, '2024-03-23 22:05:00'), (8, 12, '2024-03-31 20:35:00'),
-- Player 9 achievements
(9, 1, '2024-03-23 22:05:00'), (9, 4, '2024-03-24 23:05:00'), (9, 13, '2024-04-01 21:35:00'),
-- Player 10 achievements
(10, 1, '2024-03-24 23:05:00'), (10, 2, '2024-03-25 14:35:00'), (10, 14, '2024-04-02 22:35:00');

-- Update Player TotalRaces based on actual participation
UPDATE Player p 
SET TotalRaces = (
    SELECT COUNT(*) 
    FROM Participation part 
    WHERE part.PlayerID = p.PlayerID
);

-- Display summary of inserted data
SELECT 'Data Insertion Complete!' as Status;
SELECT COUNT(*) as TotalPlayers FROM Player;
SELECT COUNT(*) as RegisteredPlayers FROM RegisteredPlayer;
SELECT COUNT(*) as GuestPlayers FROM GuestPlayer;
SELECT COUNT(*) as TotalTracks FROM Track;
SELECT COUNT(*) as TotalKarts FROM Kart;
SELECT COUNT(*) as SpeedKarts FROM SpeedKart;
SELECT COUNT(*) as ItemKarts FROM ItemKart;
SELECT COUNT(*) as TotalRaces FROM Race;
SELECT COUNT(*) as TotalAchievements FROM Achievement;
SELECT COUNT(*) as TotalParticipations FROM Participation;
SELECT COUNT(*) as TotalLapRecords FROM LapRecord;
SELECT COUNT(*) as TotalPlayerAchievements FROM PlayerAchievement; 