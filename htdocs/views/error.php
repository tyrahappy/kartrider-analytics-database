<?php
/**
 * Error View Template
 */
?>

<div class="error-container">
    <div class="error-content">
        <h2>🚨 System Error</h2>
        
        <?php if (isset($errorMessage)): ?>
            <div class="error-message">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!DatabaseService::getInstance()->isConnected()): ?>
            <div class="database-error">
                <h3>Database Connection Failed</h3>
                
                <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
                    <?php $connInfo = DatabaseService::getInstance()->getConnectionError(); ?>
                    <div class="connection-details">
                        <p><strong>Host:</strong> <?= htmlspecialchars($connInfo['host']) ?></p>
                        <p><strong>Database:</strong> <?= htmlspecialchars($connInfo['database']) ?></p>
                        <p><strong>User:</strong> <?= htmlspecialchars($connInfo['user']) ?></p>
                    </div>
                <?php else: ?>
                    <p>Unable to connect to the database. Please contact the administrator.</p>
                <?php endif; ?>
                
                <h4>Common Solutions:</h4>
                <ul>
                    <li>Check if your database credentials are correct in config.php</li>
                    <li>Make sure the database name includes your account prefix (for InfinityFree)</li>
                    <li>Verify that your database host is correct</li>
                    <li>Ensure your database exists and tables are created</li>
                    <li>Check if the database server is running</li>
                    <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'production'): ?>
                    <li>For InfinityFree: Verify your database credentials in the control panel</li>
                    <li>Ensure you're using the correct database prefix (e.g., if0_12345678_)</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="error-actions">
            <a href="index.php" class="btn btn-primary">🏠 Go Home</a>
            <a href="javascript:history.back()" class="btn btn-secondary">↩️ Go Back</a>
        </div>
    </div>
</div>

<style>
.error-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
}

.error-content {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 2rem;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 1rem;
    border-radius: 4px;
    margin: 1rem 0;
}

.database-error {
    background: #fff3cd;
    color: #856404;
    padding: 1rem;
    border-radius: 4px;
    margin: 1rem 0;
}

.connection-details {
    background: rgba(0,0,0,0.1);
    padding: 1rem;
    border-radius: 4px;
    margin: 1rem 0;
}

.error-actions {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 4px;
    display: inline-block;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}
</style>
