<?php
/**
 * CSS Modular Test Page
 * 
 * This page tests the new modular CSS architecture
 */

$pageTitle = 'CSS Modular Test';
$pageSubtitle = 'Testing the new modular CSS structure';

// Include the layout
include __DIR__ . '/../views/layout.php';
?>

<div class="main-content">
    <h2>CSS Modular Architecture Test</h2>
    
    <div class="alert alert-info">
        <h4>Test Instructions</h4>
        <p>This page is used to test whether the new modular CSS architecture works correctly. If all styles display properly, it means the CSS splitting was successful.</p>
    </div>

    <!-- Test base styles -->
    <div class="query-section">
        <h3>Base Styles Test (base.css)</h3>
        <p>This is a test of base styles, including fonts, colors, spacing, etc.</p>
        <div class="success">
            <h3>Success Message Style</h3>
            <p>This is a test of success message styles.</p>
        </div>
        <div class="error">
            This is a test of error message styles.
        </div>
    </div>

    <!-- Test component styles -->
    <div class="operation-section">
        <h3>Component Styles Test (components.css)</h3>
        <p>Testing button, form, table, and other component styles.</p>
        
        <div class="form-group">
            <label>Test Input Field:</label>
            <input type="text" placeholder="Please enter content">
            <button class="query-btn">Query Button</button>
        </div>
        
        <div class="form-group">
            <button class="update-btn">Update Button</button>
            <button class="delete-btn">Delete Button</button>
        </div>
    </div>

    <!-- Test tab system -->
    <div class="tab-content">
        <h3>Tab System Test (tabs.css)</h3>
        <div class="tabs" data-tab-count="3">
            <a href="#" class="tab active">Tab 1</a>
            <a href="#" class="tab">Tab 2</a>
            <a href="#" class="tab">Tab 3</a>
        </div>
        <div class="tab-content">
            <p>Tab content area test.</p>
        </div>
    </div>

    <!-- Test dashboard styles -->
    <div class="dashboard-container">
        <div class="dashboard-sidebar">
            <h3>Sidebar Test</h3>
            <ul class="module-nav">
                <li><a href="#" class="active">Module 1</a></li>
                <li><a href="#">Module 2</a></li>
                <li><a href="#">Module 3</a></li>
            </ul>
        </div>
        <div class="dashboard-main">
            <h3>Dashboard Main Content Test</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>1,234</h3>
                    <p>Total Players</p>
                </div>
                <div class="stat-card">
                    <h3>567</h3>
                    <p>Today's Races</p>
                </div>
                <div class="stat-card">
                    <h3>89</h3>
                    <p>Active Users</p>
                </div>
                <div class="stat-card">
                    <h3>12</h3>
                    <p>New Registrations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Test table styles -->
    <div class="table-wrapper">
        <h3>Table Styles Test (utilities.css)</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                    <th>Column 3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Data 1</td>
                    <td>Data 2</td>
                    <td>Data 3</td>
                </tr>
                <tr>
                    <td>Data 4</td>
                    <td>Data 5</td>
                    <td>Data 6</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Test responsive design -->
    <div class="centered-form-container">
        <h3>Responsive Design Test (responsive.css)</h3>
        <p>Resize the browser window to test responsive design.</p>
        <div class="form-group">
            <label>Responsive Input Field:</label>
            <input type="text" placeholder="Will resize on small screens">
        </div>
    </div>

    <div class="results-section">
        <h3>Results Area Test</h3>
        <p>This is a test of results area styles.</p>
        <table class="results-table">
            <thead>
                <tr>
                    <th>Result 1</th>
                    <th>Result 2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Test Result 1</td>
                    <td>Test Result 2</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="alert alert-success">
        <h4>✅ Test Complete</h4>
        <p>If all styles display correctly, the CSS modularization was successful!</p>
    </div>
</div> 