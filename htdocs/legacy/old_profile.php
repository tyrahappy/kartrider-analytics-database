<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include configuration file
require_once 'config.php';

// Database connection
$pdo = null;
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch(PDOException $e) {
    echo "<div style='background:#f8d7da;color:#721c24;padding:20px;margin:20px;border-radius:4px;'>";
    echo "<h3>Database Connection Error:</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    $pdo = null;
}

// Handle operations
$operationResult = null;
$operationError = null;
$operationType = '';
$selectedOperation = isset($_GET['operation']) ? $_GET['operation'] : 'update_profile';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $action = $_POST['action'] ?? '';
    $selectedOperation = $action; // Update selected tab
    
    try {
        $pdo->beginTransaction();
        
        switch ($action) {
            case 'update_profile':
                $playerID = (int)($_POST['player_id'] ?? 0);
                $newUsername = trim($_POST['new_username'] ?? '');
                $newEmail = trim($_POST['new_email'] ?? '');
                $newProfilePic = trim($_POST['new_profile_pic'] ?? '');
                
                if ($playerID > 0) {
                    // Check if player is registered
                    $checkStmt = $pdo->prepare("SELECT p.PlayerID, rp.PlayerID as IsRegistered FROM Player p LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID WHERE p.PlayerID = ?");
                    $checkStmt->execute([$playerID]);
                    $player = $checkStmt->fetch();
                    
                    if (!$player) {
                        throw new Exception("Player not found.");
                    }
                    
                    if (!$player['IsRegistered']) {
                        throw new Exception("Only registered players can update their profiles.");
                    }
                    
                    $updates = [];
                    $params = [];
                    
                    // Update username and email in PlayerCredentials
                    if (!empty($newUsername) || !empty($newEmail)) {
                        if (!empty($newUsername)) {
                            $updates[] = "UserName = ?";
                            $params[] = $newUsername;
                        }
                        if (!empty($newEmail)) {
                            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                                throw new Exception("Invalid email format.");
                            }
                            $updates[] = "Email = ?";
                            $params[] = $newEmail;
                        }
                        
                        if (!empty($updates)) {
                            $params[] = $playerID;
                            $sql = "UPDATE PlayerCredentials SET " . implode(", ", $updates) . " WHERE PlayerID = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                        }
                    }
                    
                    // Update profile picture in RegisteredPlayer
                    if (!empty($newProfilePic)) {
                        $stmt = $pdo->prepare("UPDATE RegisteredPlayer SET ProfilePicture = ? WHERE PlayerID = ?");
                        $stmt->execute([$newProfilePic, $playerID]);
                    }
                    
                    // Update total races count
                    $stmt = $pdo->prepare("UPDATE Player SET TotalRaces = (SELECT COUNT(*) FROM Participation WHERE PlayerID = ?) WHERE PlayerID = ?");
                    $stmt->execute([$playerID, $playerID]);
                    
                    $pdo->commit();
                    $operationResult = "Profile updated successfully!";
                    $operationType = "Update Operation";
                }
                break;
                
            case 'delete_player':
                $playerID = (int)($_POST['delete_player_id'] ?? 0);
                $confirmDelete = $_POST['confirm_delete'] ?? '';
                
                if ($playerID > 0 && $confirmDelete === 'DELETE') {
                    // Get BEFORE deletion status - Check related records before deletion
                    $beforeStmt = $pdo->prepare("
                        SELECT 
                            'Before Deletion' as Status,
                            (SELECT COUNT(*) FROM Player WHERE PlayerID = ?) as PlayerExists,
                            (SELECT COUNT(*) FROM Participation WHERE PlayerID = ?) as ParticipationRecords,
                            (SELECT COUNT(*) FROM PlayerAchievement WHERE PlayerID = ?) as AchievementRecords,
                            (SELECT COUNT(*) FROM LapRecord lr JOIN Participation p ON lr.ParticipationID = p.ParticipationID WHERE p.PlayerID = ?) as LapRecords
                    ");
                    $beforeStmt->execute([$playerID, $playerID, $playerID, $playerID]);
                    $beforeStats = $beforeStmt->fetch();
                    
                    if ($beforeStats['PlayerExists'] == 0) {
                        throw new Exception("Player ID {$playerID} not found.");
                    }
                    
                    // Get player type and username for better reporting
                    $playerInfoStmt = $pdo->prepare("
                        SELECT 
                            CASE WHEN rp.PlayerID IS NOT NULL THEN 'Registered' ELSE 'Guest' END as PlayerType,
                            pc.UserName
                        FROM Player p 
                        LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                        LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
                        WHERE p.PlayerID = ?
                    ");
                    $playerInfoStmt->execute([$playerID]);
                    $playerInfo = $playerInfoStmt->fetch();
                    
                    // Execute cascade delete
                    $deleteStmt = $pdo->prepare("DELETE FROM Player WHERE PlayerID = ?");
                    $deleteStmt->execute([$playerID]);
                    
                    $deletedRows = $deleteStmt->rowCount();
                    
                    if ($deletedRows > 0) {
                        // Verify cascade worked - Check records after deletion
                        $afterStmt = $pdo->prepare("
                            SELECT 
                                'After Deletion' as Status,
                                (SELECT COUNT(*) FROM Player WHERE PlayerID = ?) as PlayerExists,
                                (SELECT COUNT(*) FROM Participation WHERE PlayerID = ?) as ParticipationRecords,
                                (SELECT COUNT(*) FROM PlayerAchievement WHERE PlayerID = ?) as AchievementRecords,
                                (SELECT COUNT(*) FROM LapRecord lr JOIN Participation p ON lr.ParticipationID = p.ParticipationID WHERE p.PlayerID = ?) as LapRecords
                        ");
                        $afterStmt->execute([$playerID, $playerID, $playerID, $playerID]);
                        $afterStats = $afterStmt->fetch();
                        
                        $pdo->commit();
                        
                        // Format detailed result message
                        $playerName = $playerInfo['UserName'] ? "({$playerInfo['UserName']})" : "";
                        $operationResult = "Player {$playerID} {$playerName} ({$playerInfo['PlayerType']}) deletion completed successfully!\n\n";
                        $operationResult .= "BEFORE DELETION:\n";
                        $operationResult .= "• Player Records: {$beforeStats['PlayerExists']}\n";
                        $operationResult .= "• Participation Records: {$beforeStats['ParticipationRecords']}\n";
                        $operationResult .= "• Achievement Records: {$beforeStats['AchievementRecords']}\n";
                        $operationResult .= "• Lap Records: {$beforeStats['LapRecords']}\n\n";
                        $operationResult .= "AFTER DELETION:\n";
                        $operationResult .= "• Player Records: {$afterStats['PlayerExists']} (should be 0)\n";
                        $operationResult .= "• Participation Records: {$afterStats['ParticipationRecords']} (should be 0)\n";
                        $operationResult .= "• Achievement Records: {$afterStats['AchievementRecords']} (should be 0)\n";
                        $operationResult .= "• Lap Records: {$afterStats['LapRecords']} (should be 0)\n\n";                        
                        $operationType = "Delete Operation with Cascade - Data Integrity Management";
                    } else {
                        throw new Exception("No player was deleted.");
                    }
                } else {
                    throw new Exception("Please enter 'DELETE' to confirm deletion.");
                }
                break;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $operationError = $e->getMessage();
    }
}

// Get some players for the dropdown
$players = [];
if ($pdo !== null) {
    try {
        $stmt = $pdo->query("
            SELECT p.PlayerID, 
                   CASE WHEN rp.PlayerID IS NOT NULL THEN 'Registered' ELSE 'Guest' END as PlayerType,
                   pc.UserName,
                   pc.Email,
                   COUNT(par.ParticipationID) as TotalRaces
            FROM Player p 
            LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
            LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
            LEFT JOIN Participation par ON p.PlayerID = par.PlayerID
            GROUP BY p.PlayerID, PlayerType, pc.UserName, pc.Email
            ORDER BY PlayerType DESC, p.PlayerID
        ");
        $players = $stmt->fetchAll();
    } catch (Exception $e) {
        // Ignore errors for now
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management - KartRider Database</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KartRider Database Viewer</h1>
            <p>Profile Management & Account Operations</p>
            <div class="navigation-links">
                <a href="index.php" class="nav-link">Table Viewer</a>
                <a href="queries.php" class="nav-link">Dynamic Queries</a>
                <a href="profile.php" class="nav-link active">Profile Management</a>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </div>
        </div>
        
        <div class="content">
            <div class="sidebar">
                <h3>Operations</h3>
                <ul>
                    <li>
                        <a href="?operation=update_profile" 
                           class="<?= $selectedOperation == 'update_profile' ? 'active' : '' ?>">
                            ✏️ Update Profile
                        </a>
                    </li>
                    <li>
                        <a href="?operation=delete_player" 
                           class="<?= $selectedOperation == 'delete_player' ? 'active' : '' ?>">
                            🗑️ Delete Account
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="main-content">
                <?php if ($pdo === null): ?>
                    <div class="error">Database connection failed. Please check your configuration.</div>
                <?php else: ?>
                    
                    <?php if ($selectedOperation === 'update_profile'): ?>
                        <!-- Update Profile Section -->
                        <div class="operation-section active">
                            <h2>✏️ Update Operation: Profile Management</h2>
                            <p>Update registered player profile information including username, email, and profile picture.</p>
                            
                            <form method="POST" class="operation-form">
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="form-group">
                                    <label for="player_id">Player ID:</label>
                                    <input type="number" name="player_id" id="player_id" min="1" placeholder="Enter Player ID" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_username">New Username (optional):</label>
                                    <input type="text" name="new_username" id="new_username" placeholder="Leave empty to keep current">
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_email">New Email (optional):</label>
                                    <input type="email" name="new_email" id="new_email" placeholder="Leave empty to keep current">
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_profile_pic">New Profile Picture URL (optional):</label>
                                    <input type="text" name="new_profile_pic" id="new_profile_pic" placeholder="Leave empty to keep current">
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="update-btn">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ($selectedOperation === 'delete_player'): ?>
                        <!-- Delete Account Section -->
                        <div class="operation-section danger active">
                            <h2>🗑️ Delete Operation with Cascade - Data Integrity Management</h2>
                            <p><strong>⚠️ WARNING:</strong> This will permanently delete the player and ALL related data including race participations, lap records, and achievements due to CASCADE constraints. You will see before/after status verification.</p>
                            
                            <form method="POST" class="operation-form" onsubmit="return confirmDeletion()">
                                <input type="hidden" name="action" value="delete_player">
                                
                                <div class="form-group">
                                    <label for="delete_player_id">Player ID to Delete:</label>
                                    <input type="number" name="delete_player_id" id="delete_player_id" min="1" placeholder="Enter Player ID" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_delete">Type "DELETE" to confirm:</label>
                                    <input type="text" name="confirm_delete" id="confirm_delete" placeholder="Type DELETE here" required>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="delete-btn">Execute Cascade Delete</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Operation Results -->
                    <?php if ($operationError): ?>
                        <div class="error">
                            <h3>❌ Operation Failed</h3>
                            <p><?= htmlspecialchars($operationError) ?></p>
                        </div>
                    <?php elseif ($operationResult): ?>
                        <div class="success">
                            <h3>✅ <?= htmlspecialchars($operationType) ?> Successful</h3>
                            <p><?= htmlspecialchars($operationResult) ?></p>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>KartRider Database Viewer - Profile Management Interface</p>
        </div>
    </div>

    <script>
    function confirmDeletion() {
        const playerID = document.getElementById('delete_player_id').value;
        const confirmText = document.getElementById('confirm_delete').value;
        
        if (confirmText !== 'DELETE') {
            alert('Please type "DELETE" to confirm deletion.');
            return false;
        }
        
        return confirm(`Are you absolutely sure you want to delete Player ${playerID}? This action cannot be undone and will remove all related data.`);
    }
    </script>
</body>
</html>
