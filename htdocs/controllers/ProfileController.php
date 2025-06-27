<?php
/**
 * Profile Controller
 * 
 * Handles profile management functionality
 * following MVC architecture pattern.
 */

require_once __DIR__ . '/../includes/BaseController.php';

class ProfileController extends BaseController {
    
    private $selectedOperation;
    private $operationResult;
    private $operationError;
    private $operationType;
    
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('Profile Management - KartRider Analytics');
        $this->selectedOperation = isset($_GET['operation']) ? $_GET['operation'] : 'update_profile';
        $this->operationResult = null;
        $this->operationError = null;
        $this->operationType = '';
    }
    
    /**
     * Main run method
     */
    public function run() {
        try {
            if (!$this->checkDatabaseConnection()) {
                $this->setError("Database connection failed. Please check your configuration.");
                $this->renderProfileView();
                return;
            }
            
            // Handle POST operations
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleOperation();
            }
            
            $this->renderProfileView();
            
        } catch (Exception $e) {
            $this->setError("Application Error: " . $e->getMessage());
            $this->renderProfileView();
        }
    }
    
    /**
     * Handle POST operations
     */
    private function handleOperation() {
        $action = $_POST['action'] ?? '';
        $this->selectedOperation = $action;
        
        try {
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();
            
            switch ($action) {
                case 'update_profile':
                    $this->updateProfile($pdo);
                    break;
                case 'register_player':
                    $this->registerPlayer($pdo);
                    break;
                case 'delete_player':
                    $this->deletePlayer($pdo);
                    break;
                default:
                    throw new Exception("Invalid operation.");
            }
            
            $pdo->commit();
            
        } catch (Exception $e) {
            if (isset($pdo)) {
                $pdo->rollback();
            }
            $this->operationError = $e->getMessage();
        }
    }
    
    /**
     * Update player profile
     */
    private function updateProfile($pdo) {
        $playerID = (int)($_POST['player_id'] ?? 0);
        $newUsername = trim($_POST['new_username'] ?? '');
        $newEmail = trim($_POST['new_email'] ?? '');
        $newProfilePic = trim($_POST['new_profile_pic'] ?? '');
        
        if ($playerID <= 0) {
            throw new Exception("Player ID is required. Please search and select a player first.");
        }
        
        // Validate that at least one field is being updated
        if (empty($newUsername) && empty($newEmail) && empty($newProfilePic)) {
            throw new Exception("At least one field must be provided for update.");
        }
        
        // Check if player exists and is registered
        $checkStmt = $pdo->prepare("
            SELECT p.PlayerID, rp.PlayerID as IsRegistered, pc.UserName, pc.Email, rp.ProfilePicture 
            FROM Player p 
            LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID 
            LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID 
            WHERE p.PlayerID = ?
        ");
        $checkStmt->execute([$playerID]);
        $player = $checkStmt->fetch();
        
        if (!$player) {
            throw new Exception("Player not found.");
        }
        
        if (!$player['IsRegistered']) {
            throw new Exception("Only registered players can update their profiles.");
        }
        
        $updatedFields = [];
        
        // Update credentials if provided and different from current
        if (!empty($newUsername) || !empty($newEmail)) {
            $updates = [];
            $params = [];
            
            if (!empty($newUsername) && $newUsername !== $player['UserName']) {
                // Check if new username already exists
                $checkUsernameStmt = $pdo->prepare("SELECT PlayerID FROM PlayerCredentials WHERE UserName = ? AND PlayerID != ?");
                $checkUsernameStmt->execute([$newUsername, $playerID]);
                if ($checkUsernameStmt->fetch()) {
                    throw new Exception("Username '{$newUsername}' is already taken.");
                }
                $updates[] = "UserName = ?";
                $params[] = $newUsername;
                $updatedFields[] = "Username";
            }
            
            if (!empty($newEmail) && $newEmail !== $player['Email']) {
                if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Invalid email format.");
                }
                // Check if new email already exists
                $checkEmailStmt = $pdo->prepare("SELECT PlayerID FROM PlayerCredentials WHERE Email = ? AND PlayerID != ?");
                $checkEmailStmt->execute([$newEmail, $playerID]);
                if ($checkEmailStmt->fetch()) {
                    throw new Exception("Email '{$newEmail}' is already in use.");
                }
                $updates[] = "Email = ?";
                $params[] = $newEmail;
                $updatedFields[] = "Email";
            }
            
            if (!empty($updates)) {
                $params[] = $playerID;
                $sql = "UPDATE PlayerCredentials SET " . implode(", ", $updates) . " WHERE PlayerID = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
        }
        
        // Update profile picture if provided and different from current
        if (!empty($newProfilePic) && $newProfilePic !== $player['ProfilePicture']) {
            $stmt = $pdo->prepare("UPDATE RegisteredPlayer SET ProfilePicture = ? WHERE PlayerID = ?");
            $stmt->execute([$newProfilePic, $playerID]);
            $updatedFields[] = "Profile Picture";
        }
        
        // Update total races count
        $stmt = $pdo->prepare("UPDATE Player SET TotalRaces = (SELECT COUNT(*) FROM Participation WHERE PlayerID = ?) WHERE PlayerID = ?");
        $stmt->execute([$playerID, $playerID]);
        
        if (empty($updatedFields)) {
            $this->operationResult = "No changes were made (all values were the same as current).";
        } else {
            $this->operationResult = "Profile updated successfully! Updated: " . implode(", ", $updatedFields);
        }
        $this->operationType = "Update Profile";
    }
    
    /**
     * Register new player
     */
    private function registerPlayer($pdo) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $profilePic = trim($_POST['profile_pic'] ?? 'default_avatar.png');
        
        if (empty($username)) {
            throw new Exception("Username is required.");
        }
        
        if (empty($email)) {
            throw new Exception("Email is required.");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        
        // Check if username already exists
        $checkStmt = $pdo->prepare("SELECT PlayerID FROM PlayerCredentials WHERE UserName = ?");
        $checkStmt->execute([$username]);
        if ($checkStmt->fetch()) {
            throw new Exception("Username already exists.");
        }
        
        // Check if email already exists
        $checkStmt = $pdo->prepare("SELECT PlayerID FROM PlayerCredentials WHERE Email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            throw new Exception("Email already exists.");
        }
        
        // Create player
        $stmt = $pdo->prepare("INSERT INTO Player (TotalRaces) VALUES (0)");
        $stmt->execute();
        $playerID = $pdo->lastInsertId();
        
        // Create registered player
        $stmt = $pdo->prepare("INSERT INTO RegisteredPlayer (PlayerID, ProfilePicture) VALUES (?, ?)");
        $stmt->execute([$playerID, $profilePic]);
        
        // Create credentials (without password)
        $stmt = $pdo->prepare("INSERT INTO PlayerCredentials (PlayerID, UserName, Email) VALUES (?, ?, ?)");
        $stmt->execute([$playerID, $username, $email]);
        
        $this->operationResult = "Player registered successfully! Player ID: " . $playerID;
        $this->operationType = "Register Player";
    }
    
    /**
     * Delete player
     */
    private function deletePlayer($pdo) {
        $playerID = (int)($_POST['delete_player_id'] ?? 0);
        
        if ($playerID <= 0) {
            throw new Exception("Invalid Player ID.");
        }
        
        // Check if player exists
        $checkStmt = $pdo->prepare("SELECT PlayerID FROM Player WHERE PlayerID = ?");
        $checkStmt->execute([$playerID]);
        if (!$checkStmt->fetch()) {
            throw new Exception("Player not found.");
        }
        
        // Delete in proper order (foreign key constraints)
        $tables = ['PlayerCredentials', 'RegisteredPlayer', 'GuestPlayer', 'PlayerAchievement', 'Participation', 'Player'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE PlayerID = ?");
            $stmt->execute([$playerID]);
        }
        
        $this->operationResult = "Player and all related data deleted successfully!";
        $this->operationType = "Delete Operation";
    }
     /**
     * Get all players data
     */
    public function getAllPlayers() {
        if (!$this->db->isConnected()) {
            return [];
        }

        try {
            $sql = "SELECT 
                        p.PlayerID,
                        COALESCE(pc.UserName, CONCAT('Guest_', gp.SessionID), 'Unknown') as DisplayName,
                        pc.Email,
                        p.TotalRaces,
                        CASE 
                            WHEN rp.PlayerID IS NOT NULL THEN 'Registered'
                            WHEN gp.PlayerID IS NOT NULL THEN 'Guest'
                            ELSE 'Unknown'
                        END as PlayerType,
                        rp.ProfilePicture
                    FROM Player p
                    LEFT JOIN RegisteredPlayer rp ON p.PlayerID = rp.PlayerID
                    LEFT JOIN GuestPlayer gp ON p.PlayerID = gp.PlayerID
                    LEFT JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
                    ORDER BY p.PlayerID";
            
            return $this->db->fetchAll($sql);
            
        } catch (Exception $e) {
            error_log("Error fetching players: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Render the profile view
     */
    private function renderProfileView() {
        try {
            $allPlayers = $this->getAllPlayers();
            
            // Prepare data for view
            $data = [
                'selectedOperation' => $this->selectedOperation,
                'operationResult' => $this->operationResult,
                'operationError' => $this->operationError,
                'operationType' => $this->operationType,
                'allPlayers' => $allPlayers,
                'controller' => $this
            ];
            
            // Use parent's renderView with layout
            $this->renderView(__DIR__ . '/../views/layout.php', $data);
            
        } catch (Exception $e) {
            // If rendering fails, show error
            $this->setError("Profile Page Error: " . $e->getMessage());
            $this->renderView(__DIR__ . '/../views/layout.php', [
                'selectedOperation' => $this->selectedOperation,
                'operationResult' => null,
                'operationError' => $this->errorMessage,
                'operationType' => '',
                'allPlayers' => [],
                'controller' => $this
            ]);
        }
    }
}
