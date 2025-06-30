<?php
/**
 * Dynamic Queries Content View
 */
$queryExamples = $controller->getQueryExamples();
$queryTypes = $controller->getQueryTypes();
?>

<div class="queries-management">
    <?php $tabCount = count($queryExamples); ?>
    <div class="tabs" data-tab-count="<?= $tabCount ?>">
        <?php foreach ($queryExamples as $key => $example): ?>
            <a href="?query=<?= $key ?>" 
               class="tab <?= $selectedQuery === $key ? 'active' : '' ?>"
               title="<?= htmlspecialchars($queryTypes[$key]) ?>">
                <?= htmlspecialchars($queryTypes[$key]) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Query Result Messages -->
    <?php if ($queryError): ?>
        <div class="alert alert-error">
            <h4>Query Error</h4>
            <p><?= htmlspecialchars($queryError) ?></p>
        </div>
    <?php endif; ?>

    <!-- Query Form -->
    <div class="tab-content">
        <?php $currentExample = $queryExamples[$selectedQuery] ?? $queryExamples['join_query']; ?>
        
        <h3><?= htmlspecialchars($currentExample['name']) ?></h3>
        <p><?= htmlspecialchars($currentExample['description']) ?></p>
        
        <form method="POST">
            <input type="hidden" name="query_type" value="<?= htmlspecialchars($selectedQuery) ?>">
            
            <?php if ($selectedQuery === 'advanced_analysis'): ?>
                <div class="help-text">
                    <strong>Security Note:</strong> Only SELECT queries are allowed. 
                    No INSERT, UPDATE, DELETE, or DDL operations permitted.<br>
                    <strong>Available Tables:</strong> Player, PlayerCredentials, Participation, Race, Track, Achievement, PlayerAchievement
                </div>
                
                <div class="form-group">
                    <textarea name="custom_sql" id="custom_sql" rows="12" placeholder="<?= htmlspecialchars($currentExample['example']) ?>" required>SELECT pc.UserName, COUNT(*) as TotalRaces
FROM Player p
JOIN PlayerCredentials pc ON p.PlayerID = pc.PlayerID
JOIN Participation part ON p.PlayerID = part.PlayerID
GROUP BY p.PlayerID, pc.UserName
ORDER BY TotalRaces DESC</textarea>
                </div>
            <?php else: ?>
                <div class="query-preview">
                    <h4>Query to be executed:</h4>
                    <pre><?= htmlspecialchars($currentExample['example']) ?></pre>
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn btn-primary">Execute Query</button>
        </form>
    </div>

    <!-- Query Results -->
    <?php if ($queryResult !== null): ?>
        <div class="results-section">
            <h3>Query Results (<?= is_array($queryResult) && !empty($queryResult) ? count($queryResult) : '0' ?> rows)</h3>
            
            <?php if (is_array($queryResult) && !empty($queryResult)): ?>
                <div class="table-container">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($queryResult[0]) as $column): ?>
                                    <th><?= htmlspecialchars($column) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($queryResult as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td>
                                            <?php if ($value === null): ?>
                                                <em style="color: #999;">NULL</em>
                                            <?php else: ?>
                                                <?= htmlspecialchars((string)$value) ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p>No results found.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
