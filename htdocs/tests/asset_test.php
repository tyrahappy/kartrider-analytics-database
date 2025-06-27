<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Test - KartRider Analytics</title>
    
    <?php
    // Include asset helper
    if (file_exists('includes/AssetHelper.php')) {
        require_once '../../includes/AssetHelper.php';
        $useHelper = true;
    } else {
        $useHelper = false;
    }
    ?>
    
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .warning { background-color: #fff3cd; color: #856404; }
        .asset-test { margin: 10px 0; }
        .asset-url { font-family: monospace; background: #f8f9fa; padding: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Asset Loading Test</h1>
    <p>This page tests if your CSS and JavaScript assets are loading correctly.</p>
    
    <!-- Test CSS Loading -->
    <div class="test-section">
        <h2>CSS Loading Test</h2>
        
        <?php if ($useHelper): ?>
            <link rel="stylesheet" href="<?= assetVersion('assets/main.css') ?>">
            <div class="asset-test">
                <strong>Using Asset Helper:</strong><br>
                <span class="asset-url"><?= htmlspecialchars(assetVersion('assets/main.css')) ?></span>
            </div>
        <?php else: ?>
            <link rel="stylesheet" href="assets/main.css">
            <div class="asset-test warning">
                <strong>Using Relative Path (may not work):</strong><br>
                <span class="asset-url">assets/main.css</span>
            </div>
        <?php endif; ?>
        
        <div id="css-test" class="asset-test">
            <p>If the main CSS is loaded, you should see styled content on the main pages.</p>
        </div>
    </div>
    
    <!-- Test JavaScript Loading -->
    <div class="test-section">
        <h2>JavaScript Loading Test</h2>
        
        <?php if ($useHelper): ?>
            <div class="asset-test">
                <strong>Tabs Script:</strong><br>
                <span class="asset-url"><?= htmlspecialchars(assetVersion('assets/tabs.js')) ?></span>
            </div>
            <div class="asset-test">
                <strong>Dashboard Script:</strong><br>
                <span class="asset-url"><?= htmlspecialchars(assetVersion('assets/dashboard.js')) ?></span>
            </div>
        <?php else: ?>
            <div class="asset-test warning">
                <strong>Using Relative Paths (may not work):</strong><br>
                <span class="asset-url">assets/tabs.js</span><br>
                <span class="asset-url">assets/dashboard.js</span>
            </div>
        <?php endif; ?>
        
        <div id="js-test" class="asset-test">
            <button onclick="testJS()">Test JavaScript</button>
            <div id="js-result"></div>
        </div>
    </div>
    
    <!-- Manual Asset Tests -->
    <div class="test-section">
        <h2>Manual Asset Tests</h2>
        <p>Click these links to test if assets load directly:</p>
        
        <div class="asset-test">
            <a href="assets/main.css" target="_blank">📄 Test main.css</a> - Should show CSS code
        </div>
        <div class="asset-test">
            <a href="assets/tabs.js" target="_blank">📄 Test tabs.js</a> - Should show JavaScript code
        </div>
        <div class="asset-test">
            <a href="assets/dashboard.js" target="_blank">📄 Test dashboard.js</a> - Should show JavaScript code
        </div>
    </div>
    
    <!-- Environment Info -->
    <div class="test-section">
        <h2>Environment Information</h2>
        <div class="asset-test">
            <strong>Server:</strong> <?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'Unknown') ?><br>
            <strong>Request URI:</strong> <?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Unknown') ?><br>
            <strong>Document Root:</strong> <?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') ?><br>
            <strong>Script Name:</strong> <?= htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'Unknown') ?>
        </div>
    </div>
    
    <!-- Navigation -->
    <div class="test-section">
        <h2>Navigation</h2>
        <a href="index.php">← Back to Main Application</a> |
        <a href="deployment_check.php?run=true">Run Deployment Check</a>
    </div>
    
    <script>
        function testJS() {
            document.getElementById('js-result').innerHTML = '<span style="color: green;">✅ JavaScript is working!</span>';
        }
        
        // Test if main application CSS classes exist
        window.onload = function() {
            // This would only work if the main CSS is loaded
            var testDiv = document.createElement('div');
            testDiv.className = 'container'; // This should be a class from your main CSS
            document.body.appendChild(testDiv);
            
            var computedStyle = window.getComputedStyle(testDiv);
            if (computedStyle.getPropertyValue('max-width') || computedStyle.getPropertyValue('margin')) {
                document.getElementById('css-test').innerHTML += '<div style="color: green;">✅ Main CSS appears to be loaded!</div>';
            } else {
                document.getElementById('css-test').innerHTML += '<div style="color: red;">❌ Main CSS may not be loaded properly</div>';
            }
            
            document.body.removeChild(testDiv);
        };
    </script>
    
    <?php if ($useHelper): ?>
        <script src="<?= assetVersion('assets/tabs.js') ?>"></script>
    <?php endif; ?>
</body>
</html>
