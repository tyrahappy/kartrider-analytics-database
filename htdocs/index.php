<?php
// Include configuration file
require_once 'config.php';

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'"); // Set character encoding
} catch(PDOException $e) {
    // Don't show detailed error information in production
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die("Connection failed: " . $e->getMessage());
    } else {
        die("Database connection error. Please try again later.");
    }
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .content {
            display: flex;
            min-height: 600px;
        }
        
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
            padding: 20px;
        }
        
        .sidebar h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar li {
            margin-bottom: 10px;
        }
        
        .sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        
        .sidebar a.active {
            background-color: #667eea;
            color: white;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-x: auto;
        }
        
        .table-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }
        
        .table-header h2 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .record-count {
            color: #666;
            font-size: 0.9em;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #667eea;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
        }
    </style>
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
                try {
                    // Check if table exists
                    $tableExists = $pdo->query("SHOW TABLES LIKE '$selectedTable'")->rowCount() > 0;
                    
                    if (!$tableExists) {
                        echo '<div class="error">Table not found: ' . htmlspecialchars($selectedTable) . '</div>';
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
                    echo '<div class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
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