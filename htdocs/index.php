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
    // For InfinityFree, the host might be different
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Test connection
    $pdo->query("SELECT 1");
    
} catch(PDOException $e) {
    // Show detailed error for debugging
    echo "<div style='background:#f8d7da;color:#721c24;padding:20px;margin:20px;border-radius:4px;'>";
    echo "<h3>Database Connection Error:</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Host:</strong> " . htmlspecialchars(DB_HOST) . "</p>";
    echo "<p><strong>Database:</strong> " . htmlspecialchars(DB_NAME) . "</p>";
    echo "<p><strong>User:</strong> " . htmlspecialchars(DB_USER) . "</p>";
    echo "<h4>Common solutions for InfinityFree:</h4>";
    echo "<ul>";
    echo "<li>Check if your database credentials are correct in config.php</li>";
    echo "<li>Make sure the database name includes your account prefix (e.g., 'if0_12345678_KartRiderAnalytics')</li>";
    echo "<li>Verify that your database host is correct (usually 'sql200.byetcluster.com' or similar)</li>";
    echo "<li>Ensure your database exists and tables are created</li>";
    echo "</ul>";
    echo "</div>";
    
    // Show a basic page even if database fails
    $pdo = null;
}

// Define all table names
$tables = [
    'Player' => 'Players',
    'PlayerCredentials' => 'Player Credentials',
    'RegisteredPlayer' => 'Registered Players',
    'GuestPlayer' => 'Guest Players',
    'Track' => 'Tracks',
    'Kart' => 'Karts',
    'KartDetails' => 'Kart Details',
    'SpeedKart' => 'Speed Karts',
    'ItemKart' => 'Item Karts',
    'Race' => 'Races',
    'Achievement' => 'Achievements',
    'Participation' => 'Race Participations',
    'LapRecord' => 'Lap Records',
    'PlayerAchievement' => 'Player Achievements'
];

// Get selected table from URL parameter
$selectedTable = isset($_GET['table']) ? $_GET['table'] : 'Player';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KartRider Database Viewer</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KartRider Database Viewer</h1>
            <p>Simple Table Data Display</p>
        </div>
        
        <div class="content">
            <div class="sidebar">
                <h3>Tables</h3>
                <ul>
                    <?php foreach($tables as $tableName => $displayName): ?>
                        <li>
                            <a href="?table=<?= $tableName ?>" 
                               class="<?= $selectedTable == $tableName ? 'active' : '' ?>">
                                <?= $displayName ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="main-content">
                <?php
                if ($pdo === null) {
                    echo '<div class="error">Database connection failed. Please check the error message above.</div>';
                } else {
                    try {
                        // Check if table exists
                        $tableExists = $pdo->query("SHOW TABLES LIKE '$selectedTable'")->rowCount() > 0;
                        
                        if (!$tableExists) {
                            echo '<div class="error">Table not found: ' . htmlspecialchars($selectedTable) . '</div>';
                            echo '<div class="no-data">';
                            echo '<p>Available tables in database:</p>';
                            try {
                                $tablesQuery = $pdo->query("SHOW TABLES");
                                $allTables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);
                                if (!empty($allTables)) {
                                    echo '<ul style="text-align: left; display: inline-block;">';
                                    foreach ($allTables as $table) {
                                        echo '<li>' . htmlspecialchars($table) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p>No tables found in database.</p>';
                                }
                            } catch (Exception $e) {
                                echo '<p>Could not retrieve table list.</p>';
                            }
                            echo '</div>';
                        } else {
                        // Get table data
                        $query = "SELECT * FROM $selectedTable";
                        $stmt = $pdo->query($query);
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $rowCount = count($data);
                        
                        // Get column information
                        $columnsQuery = "SHOW COLUMNS FROM $selectedTable";
                        $columnsStmt = $pdo->query($columnsQuery);
                        $columns = $columnsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        
                        <div class="table-header">
                            <h2><?= $tables[$selectedTable] ?? $selectedTable ?></h2>
                            <div class="record-count">Total Records: <?= $rowCount ?></div>
                        </div>
                        
                        <?php if ($rowCount > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <?php foreach($columns as $column): ?>
                                            <th><?= htmlspecialchars($column['Field']) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data as $row): ?>
                                        <tr>
                                            <?php foreach($columns as $column): ?>
                                                <td>
                                                    <?php
                                                    $value = $row[$column['Field']];
                                                    if ($value === null) {
                                                        echo '<em style="color: #999;">NULL</em>';
                                                    } elseif (strlen($value) > 50) {
                                                        echo htmlspecialchars(substr($value, 0, 50)) . '...';
                                                    } else {
                                                        echo htmlspecialchars($value);
                                                    }
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="no-data">
                                <p>No data found in this table.</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        }
                    } catch (PDOException $e) {
                        echo '<div class="error">Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    } catch (Exception $e) {
                        echo '<div class="error">General Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                }
                ?>
            </div>
        </div>
        
        <div class="footer">
            <p>KartRider Database Viewer - CS 5200 Project</p>
        </div>
    </div>
</body>
</html>