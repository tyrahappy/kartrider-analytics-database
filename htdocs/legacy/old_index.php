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

// Define all table names with hierarchy structure
// Format: 'table_name' => ['display_name', 'indent_level', 'is_visible']
$tables = [
    // Player-related tables
    'Player' => ['Players', 0, true],
    'PlayerCredentials' => ['Player Credentials', 0, false], // Hidden - sensitive data
    'RegisteredPlayer' => ['Registered Players', 0, true], // ISA relationship
    'GuestPlayer' => ['Guest Players', 0, true], // ISA relationship
    // Kart-related tables
    'Kart' => ['Karts', 0, true],
    'SpeedKart' => ['Speed Karts', 0, true], // ISA relationship
    'ItemKart' => ['Item Karts', 0, true], // ISA relationship
    'KartDetails' => ['Kart Details', 0, true], // Generalization
    // Race-related tables
    'Race' => ['Races', 0, true],
    'Participation' => ['Race Participations', 0, true],
    'LapRecord' => ['Lap Records', 0, true],
    'Track' => ['Tracks', 0, true],
    // Achievement-related tables
    'Achievement' => ['Achievements', 0, true],
    'PlayerAchievement' => ['Player Achievements', 0, true]
];

// Filter out hidden tables and create display array
$visibleTables = [];
foreach ($tables as $tableName => $tableInfo) {
    if ($tableInfo[2]) { // is_visible
        $visibleTables[$tableName] = $tableInfo[0]; // display_name
    }
}

// Get selected table from URL parameter
$selectedTable = isset($_GET['table']) ? $_GET['table'] : 'Player';

// Security check: prevent access to hidden tables
if (isset($tables[$selectedTable]) && !$tables[$selectedTable][2]) {
    // Redirect to default table if trying to access hidden table
    header("Location: ?table=Player");
    exit;
}

// Get sorting and filtering parameters
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : '';
$sortDirection = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterColumn = isset($_GET['filter_col']) ? $_GET['filter_col'] : '';
$filterValue = isset($_GET['filter_val']) ? trim($_GET['filter_val']) : '';
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
            <p>Interactive Table Data Display with Hierarchical Structure</p>
            <div class="navigation-links">
                <a href="index.php" class="nav-link active">Table Viewer</a>
                <a href="queries.php" class="nav-link">Dynamic Queries</a>
                <a href="profile.php" class="nav-link">Profile Management</a>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </div>
            <div class="security-notice">
                <small>🔒 Sensitive tables (e.g., Player Credentials) are hidden for security</small>
            </div>
        </div>
        
        <div class="content">
            <div class="sidebar">
                <h3>Tables</h3>
                <ul>
                    <?php foreach($tables as $tableName => $tableInfo): ?>
                        <?php if ($tableInfo[2]): // Only show visible tables ?>
                            <li class="indent-level-<?= $tableInfo[1] ?>">
                                <a href="?table=<?= $tableName ?>" 
                                   class="<?= $selectedTable == $tableName ? 'active' : '' ?>">
                                    <?= $tableInfo[0] ?>
                                </a>
                            </li>
                        <?php endif; ?>
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
                        // Get column information first
                        $columnsQuery = "SHOW COLUMNS FROM $selectedTable";
                        $columnsStmt = $pdo->query($columnsQuery);
                        $columns = $columnsStmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Build the SQL query with filtering and sorting
                        $query = "SELECT * FROM $selectedTable";
                        $whereConditions = [];
                        $params = [];
                        
                        // Add search filter (searches across all columns)
                        if (!empty($searchTerm)) {
                            $searchConditions = [];
                            foreach ($columns as $column) {
                                $searchConditions[] = $column['Field'] . " LIKE ?";
                                $params[] = '%' . $searchTerm . '%';
                            }
                            $whereConditions[] = '(' . implode(' OR ', $searchConditions) . ')';
                        }
                        
                        // Add column-specific filter
                        if (!empty($filterColumn) && !empty($filterValue)) {
                            $whereConditions[] = $filterColumn . " LIKE ?";
                            $params[] = '%' . $filterValue . '%';
                        }
                        
                        // Add WHERE clause if we have conditions
                        if (!empty($whereConditions)) {
                            $query .= " WHERE " . implode(' AND ', $whereConditions);
                        }
                        
                        // Add sorting
                        if (!empty($sortColumn)) {
                            // Validate sort column exists
                            $validColumns = array_column($columns, 'Field');
                            if (in_array($sortColumn, $validColumns)) {
                                $query .= " ORDER BY $sortColumn $sortDirection";
                            }
                        }
                        
                        // Execute query
                        $stmt = $pdo->prepare($query);
                        $stmt->execute($params);
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $rowCount = count($data);
                        
                        // Get total count without filters for display
                        $totalQuery = "SELECT COUNT(*) as total FROM $selectedTable";
                        $totalStmt = $pdo->query($totalQuery);
                        $totalCount = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
                        ?>
                        
                        <div class="table-header">
                            <h2><?= isset($tables[$selectedTable]) ? $tables[$selectedTable][0] : $selectedTable ?></h2>
                            <div class="record-count">
                                Showing <?= $rowCount ?> of <?= $totalCount ?> records
                                <?php if (!empty($searchTerm) || !empty($filterValue)): ?>
                                    (filtered)
                                <?php endif; ?>
                            </div>
                            
                            <!-- Filter and Search Forms -->
                            <div class="filter-controls">
                                <!-- Global Search -->
                                <form method="GET" class="search-form">
                                    <input type="hidden" name="table" value="<?= htmlspecialchars($selectedTable) ?>">
                                    <?php if (!empty($sortColumn)): ?>
                                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sortColumn) ?>">
                                        <input type="hidden" name="dir" value="<?= htmlspecialchars($sortDirection) ?>">
                                    <?php endif; ?>
                                    <?php if (!empty($filterColumn) && !empty($filterValue)): ?>
                                        <input type="hidden" name="filter_col" value="<?= htmlspecialchars($filterColumn) ?>">
                                        <input type="hidden" name="filter_val" value="<?= htmlspecialchars($filterValue) ?>">
                                    <?php endif; ?>
                                    <div class="search-box">
                                        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" 
                                               placeholder="Search across all columns...">
                                        <button type="submit">Search</button>
                                        <?php if (!empty($searchTerm)): ?>
                                            <a href="?table=<?= htmlspecialchars($selectedTable) ?><?= !empty($sortColumn) ? '&sort=' . htmlspecialchars($sortColumn) . '&dir=' . htmlspecialchars($sortDirection) : '' ?><?= (!empty($filterColumn) && !empty($filterValue)) ? '&filter_col=' . htmlspecialchars($filterColumn) . '&filter_val=' . htmlspecialchars($filterValue) : '' ?>" class="clear-btn">Clear Search</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                                
                                <!-- Column Filter -->
                                <form method="GET" class="filter-form">
                                    <input type="hidden" name="table" value="<?= htmlspecialchars($selectedTable) ?>">
                                    <?php if (!empty($sortColumn)): ?>
                                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sortColumn) ?>">
                                        <input type="hidden" name="dir" value="<?= htmlspecialchars($sortDirection) ?>">
                                    <?php endif; ?>
                                    <?php if (!empty($searchTerm)): ?>
                                        <input type="hidden" name="search" value="<?= htmlspecialchars($searchTerm) ?>">
                                    <?php endif; ?>
                                    <div class="column-filter">
                                        <select name="filter_col" onchange="toggleFilterInput(this);">
                                            <option value="">Filter by column...</option>
                                            <?php foreach($columns as $column): ?>
                                                <option value="<?= htmlspecialchars($column['Field']) ?>" 
                                                        <?= $filterColumn === $column['Field'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($column['Field']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="text" name="filter_val" value="<?= htmlspecialchars($filterValue) ?>" 
                                               placeholder="Filter value..." <?= empty($filterColumn) ? 'disabled' : '' ?> id="filter-val-input">
                                        <button type="submit">Filter</button>
                                        <?php if (!empty($filterColumn) && !empty($filterValue)): ?>
                                            <a href="?table=<?= htmlspecialchars($selectedTable) ?><?= !empty($sortColumn) ? '&sort=' . htmlspecialchars($sortColumn) . '&dir=' . htmlspecialchars($sortDirection) : '' ?><?= !empty($searchTerm) ? '&search=' . htmlspecialchars($searchTerm) : '' ?>" class="clear-btn">Clear Filter</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                                
                                <!-- Clear All Filters -->
                                <?php if (!empty($searchTerm) || (!empty($filterColumn) && !empty($filterValue))): ?>
                                    <div class="clear-all">
                                        <a href="?table=<?= htmlspecialchars($selectedTable) ?><?= !empty($sortColumn) ? '&sort=' . htmlspecialchars($sortColumn) . '&dir=' . htmlspecialchars($sortDirection) : '' ?>" class="clear-all-btn">Clear All Filters</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($rowCount > 0): ?>
                            <table id="data-table">
                                <thead>
                                    <tr>
                                        <?php foreach($columns as $column): ?>
                                            <th>
                                                <?php
                                                // Build sort URL
                                                $newDirection = ($sortColumn === $column['Field'] && $sortDirection === 'ASC') ? 'desc' : 'asc';
                                                $sortUrl = "?table=" . htmlspecialchars($selectedTable) . 
                                                          "&sort=" . htmlspecialchars($column['Field']) . 
                                                          "&dir=" . $newDirection;
                                                
                                                // Preserve current filters
                                                if (!empty($searchTerm)) {
                                                    $sortUrl .= "&search=" . urlencode($searchTerm);
                                                }
                                                if (!empty($filterColumn) && !empty($filterValue)) {
                                                    $sortUrl .= "&filter_col=" . urlencode($filterColumn) . "&filter_val=" . urlencode($filterValue);
                                                }
                                                ?>
                                                <a href="<?= $sortUrl ?>" class="sort-link">
                                                    <?= htmlspecialchars($column['Field']) ?>
                                                    <?php if ($sortColumn === $column['Field']): ?>
                                                        <span class="sort-indicator">
                                                            <?= $sortDirection === 'ASC' ? '↑' : '↓' ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </a>
                                            </th>
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
    
    <script>
    function toggleFilterInput(selectElement) {
        const filterInput = document.getElementById('filter-val-input');
        if (selectElement.value === '') {
            filterInput.disabled = true;
            filterInput.value = '';
        } else {
            filterInput.disabled = false;
            filterInput.focus();
        }
    }
    </script>
</body>
</html>