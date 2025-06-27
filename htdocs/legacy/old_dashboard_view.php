<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KartRider Analytics Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KartRider Analytics Dashboard</h1>
            <p>Interactive Data Visualization and Statistical Analysis</p>
            <div class="navigation-links">
                <a href="index.php" class="nav-link">Table Viewer</a>
                <a href="queries.php" class="nav-link">Dynamic Queries</a>
                <a href="profile.php" class="nav-link">Profile Management</a>
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
            </div>
        </div>
        
        <div class="content">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3>Dashboard Modules</h3>
                <ul>
                    <?php 
                    $modules = $controller->getModulesConfig();
                    foreach ($modules as $moduleKey => $moduleInfo): 
                    ?>
                        <li>
                            <a href="?module=<?= $moduleKey ?>&time_filter=<?= $timeFilter ?>&player_type=<?= $playerTypeFilter ?>" 
                               class="<?= $selectedModule == $moduleKey ? 'active' : '' ?>">
                                <?= htmlspecialchars($moduleInfo['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <!-- Filters Section -->
                <div class="filter-section">
                    <h4>Filters</h4>
                    <form method="GET" class="dashboard-filters">
                        <input type="hidden" name="module" value="<?= $selectedModule ?>">
                        
                        <div class="filter-group">
                            <label for="time_filter">Time Period:</label>
                            <select name="time_filter" id="time_filter">
                                <?php 
                                $timeOptions = $controller->getTimeFilterOptions();
                                foreach ($timeOptions as $value => $label): 
                                ?>
                                    <option value="<?= $value ?>" <?= $timeFilter === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="player_type">Player Type:</label>
                            <select name="player_type" id="player_type">
                                <?php 
                                $playerOptions = $controller->getPlayerTypeOptions();
                                foreach ($playerOptions as $value => $label): 
                                ?>
                                    <option value="<?= $value ?>" <?= $playerTypeFilter === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="filter-btn">Apply Filters</button>
                    </form>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <?php if (isset($errorMessage)): ?>
                    <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
                
                <?php elseif ($dashboardData === null): ?>
                    <div class="module-placeholder">
                        <h2>🚧 Module Under Development</h2>
                        <p>The selected module (<?= htmlspecialchars($selectedModule) ?>) is currently being developed.</p>
                        <p>Please select one of the available modules to see the implemented dashboard features.</p>
                    </div>
                
                <?php elseif (isset($dashboardData['error'])): ?>
                    <div class="error">Error loading data: <?= htmlspecialchars($dashboardData['error']) ?></div>
                
                <?php else: ?>
                    <!-- Module Content -->
                    <div class="dashboard-header">
                        <h2><?= $pageTitle ?></h2>
                        <div class="filter-info"><?= $filterInfo ?></div>
                    </div>
                    
                    <?php 
                    // Include the appropriate module view
                    $moduleViewFile = "views/dashboard_" . $selectedModule . ".php";
                    if (file_exists($moduleViewFile)) {
                        include $moduleViewFile;
                    } else {
                        // Fallback to inline module rendering
                        include 'views/dashboard_modules_inline.php';
                    }
                    ?>
                    
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>KartRider Analytics Dashboard - CS 5200 Project</p>
        </div>
    </div>
    
    <!-- Include JavaScript for charts -->
    <script src="assets/dashboard.js"></script>
    
    <!-- Initialize charts with data -->
    <script>
        <?php if ($dashboardData && !isset($dashboardData['error'])): ?>
            // Pass data to JavaScript
            window.dashboardData = <?= json_encode($dashboardData) ?>;
            window.selectedModule = '<?= $selectedModule ?>';
            
            // Initialize charts based on module
            if (typeof initializeDashboardCharts === 'function') {
                initializeDashboardCharts(window.selectedModule, window.dashboardData);
            }
        <?php endif; ?>
    </script>
</body>
</html>
