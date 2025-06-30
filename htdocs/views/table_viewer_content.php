<?php
/**
 * Table Viewer Content View
 */
?>

<div class="table-viewer-management">
    <!-- Table Tabs -->
    <?php $tabCount = count($controller->getVisibleTables()); ?>
    <div class="tabs" data-tab-count="<?= $tabCount ?>">
        <?php foreach ($controller->getVisibleTables() as $tableName => $tableInfo): ?>
            <a href="?table=<?= urlencode($tableName) ?>" 
               class="tab level-<?= $tableInfo['level'] ?> <?= $selectedTable === $tableName ? 'active' : '' ?>">
                <?= htmlspecialchars($tableInfo['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Search Section (only show when table is selected) -->
    <?php if ($selectedTable): ?>
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="hidden" name="table" value="<?= htmlspecialchars($selectedTable) ?>">
                <div class="search-group">
                    <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" 
                           placeholder="Search in <?= htmlspecialchars($selectedTable) ?>..." class="search-input">
                    <button type="submit" class="search-btn">🔍 Search</button>
                    <?php if ($searchTerm): ?>
                        <a href="?table=<?= urlencode($selectedTable) ?>" class="clear-btn">✕ Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="tab-content <?= !$selectedTable ? 'centered-content' : '' ?>">
        <?php if (!$selectedTable): ?>
            <div class="welcome-message">
                <h2>📊 Table Viewer</h2>
                <p>Select a table from the tabs above to view its contents.</p>
                <div class="features">
                    <h3>Features:</h3>
                    <ul>
                        <li>🔍 Search within table data</li>
                        <li>📈 Sort by any column</li>
                        <li>👁️ View table relationships</li>
                        <li>🔒 Security-filtered sensitive data</li>
                    </ul>
                </div>
            </div>
            
        <?php elseif (empty($tableData)): ?>
            <div class="no-data">
                <h2>Table: <?= htmlspecialchars($selectedTable) ?></h2>
                <?php if ($searchTerm): ?>
                    <p>No results found for "<?= htmlspecialchars($searchTerm) ?>"</p>
                    <a href="?table=<?= urlencode($selectedTable) ?>" class="btn">Clear Search</a>
                <?php else: ?>
                    <p>No data available in this table.</p>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <div class="table-section">
                <div class="table-header">
                    <h2>Table: <?= htmlspecialchars($selectedTable) ?></h2>
                    <div class="table-info">
                        <span class="record-count"><?= count($tableData) ?> records</span>
                        <?php if ($searchTerm): ?>
                            <span class="search-info">
                                (filtered by "<?= htmlspecialchars($searchTerm) ?>")
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($tableData[0]) as $column): ?>
                                    <th>
                                        <a href="<?= $controller->getSortUrl($column) ?>" class="sort-link">
                                            <?= htmlspecialchars($column) ?>
                                            <?= $controller->getSortIndicator($column) ?>
                                        </a>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tableData as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?= htmlspecialchars($value ?? 'NULL') ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (count($tableData) >= 100): ?>
                    <div class="pagination-info">
                        <p>⚠️ Showing first 100 records for performance. Use search to find specific data.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
