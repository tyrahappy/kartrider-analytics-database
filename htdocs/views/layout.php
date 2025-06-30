<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'KartRider Analytics') ?></title>
    
    <?php
    // Try to include asset helper, fallback to simple paths if not available
    $useAssetHelper = false;
    try {
        require_once __DIR__ . '/../includes/AssetHelper.php';
        $useAssetHelper = true;
    } catch (Exception $e) {
        // Fallback to simple relative paths
    }
    
    // Determine asset path based on environment
    if ($useAssetHelper && function_exists('assetVersion')) {
        $cssPath = assetVersion('assets/main.css');
        $jsPath = assetVersion('assets/tabs.js');
    } else {
        // Simple fallback for local development
        $cssPath = 'assets/main.css';
        $jsPath = 'assets/tabs.js';
    }
    ?>
    
    <link rel="stylesheet" href="<?= htmlspecialchars($cssPath) ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <?php if (strpos($css, 'http') === 0): ?>
                <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
            <?php elseif ($useAssetHelper && function_exists('assetVersion')): ?>
                <link rel="stylesheet" href="<?= assetVersion($css) ?>">
            <?php else: ?>
                <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>KartRider Analytics</h1>
            <p><?= htmlspecialchars($pageSubtitle ?? 'Database Management and Analytics Platform') ?></p>
            
            <!-- Navigation Links -->
            <div class="navigation-links">
                <?php 
                // Define navigation links
                $navLinks = [
                    'index.php' => 'Home',
                    'table_viewer.php' => 'Table Viewer',
                    'queries.php' => 'Dynamic Queries', 
                    'profile.php' => 'Player Profiles',
                    'dashboard.php' => 'Data Analytics'
                ];
                
                // Get current page for active state
                $currentPage = basename($_SERVER['PHP_SELF']);
                
                foreach ($navLinks as $url => $label): 
                    $activeClass = ($currentPage == $url) ? 'active' : '';
                ?>
                    <a href="<?= htmlspecialchars($url) ?>" class="nav-link <?= $activeClass ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Message Area -->
        <?php if (isset($errorMessage) && $errorMessage): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($successMessage) && $successMessage): ?>
            <div class="alert alert-success">
                <strong>Success:</strong> <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>
        
        <!-- Main Content -->
        <div class="content">
            <?php 
            $currentPage = basename($_SERVER['PHP_SELF']);
            
            // Include appropriate content based on page
            if ($currentPage == 'table_viewer.php') {
                include __DIR__ . '/table_viewer_content.php';
            } elseif ($currentPage == 'profile.php') {
                include __DIR__ . '/profile_content.php';
            } elseif ($currentPage == 'queries.php') {
                include __DIR__ . '/queries_content.php';
            } elseif ($currentPage == 'dashboard.php') {
                include __DIR__ . '/dashboard_content.php';
            } elseif (isset($contentFile) && file_exists($contentFile)) {
                include $contentFile;
            } else {
                echo $content ?? '';
            }
            ?>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>KartRider Analytics - CS 5200 Project</p>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="<?= htmlspecialchars($jsPath) ?>"></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <?php if (strpos($js, 'http') === 0): ?>
                <script src="<?= htmlspecialchars($js) ?>"></script>
            <?php elseif ($useAssetHelper && function_exists('assetVersion')): ?>
                <script src="<?= assetVersion($js) ?>"></script>
            <?php else: ?>
                <script src="<?= htmlspecialchars($js) ?>"></script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($inlineJS)): ?>
        <script><?= $inlineJS ?></script>
    <?php endif; ?>
</body>
</html>
