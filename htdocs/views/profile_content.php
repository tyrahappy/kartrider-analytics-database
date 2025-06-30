<?php
/**
 * Profile Management Content View
 */
?>

<div class="profile-management">
    <?php $tabCount = 3; // Update Profile, Delete Player, Register Player ?>
    <div class="tabs" data-tab-count="<?= $tabCount ?>">
        <a href="?operation=update_profile" class="tab <?= $selectedOperation === 'update_profile' ? 'active' : '' ?>">
            Update Profile
        </a>
        <a href="?operation=delete_player" class="tab <?= $selectedOperation === 'delete_player' ? 'active' : '' ?>">
            Delete Player
        </a>
        <a href="?operation=register_player" class="tab <?= $selectedOperation === 'register_player' ? 'active' : '' ?>">
            Register Player
        </a>
    </div>

    <!-- Operation Result Messages -->
    <?php if ($operationResult): ?>
        <div class="alert alert-success">
            <h4><?= htmlspecialchars($operationType) ?></h4>
            <p><?= htmlspecialchars($operationResult) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($operationError): ?>
        <div class="alert alert-error">
            <h4>Operation Failed</h4>
            <p><?= htmlspecialchars($operationError) ?></p>
        </div>
    <?php endif; ?>

    <!-- Update Profile Tab -->
    <?php if ($selectedOperation === 'update_profile'): ?>
        <div class="tab-content">
            <div class="centered-form-container">
                <h3>🔧 Update Player Profile</h3>
                <p>Update profile information for registered players.</p>
                
                <form method="POST" class="profile-form">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="player_id" id="hidden_player_id" value="">
                    
                    <div class="form-group">
                        <label for="player_search">Search Player (Username or Player ID):</label>
                        <div class="search-group">
                            <div class="search-container">
                                <input type="text" id="player_search" placeholder="Enter username or player ID..." 
                                       onkeyup="searchPlayer(this.value)" autocomplete="off">
                            </div>
                            <div id="search_status" class="search-status"></div>
                        </div>
                        <div id="search_results" class="search-results hidden"></div>
                    </div>
                    
                    <div id="player-details">
                        <div class="form-group">
                            <label for="current_info">Current Player Info:</label>
                            <div id="current_info" class="current-info">
                                <p>Search and select a player to view current information</p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_username">New Username:</label>
                            <input type="text" name="new_username" id="new_username" maxlength="50" 
                                   placeholder="Enter new username">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_email">New Email:</label>
                            <input type="email" name="new_email" id="new_email" maxlength="100" 
                                   placeholder="Enter new email address">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_profile_pic">Profile Picture URL:</label>
                            <input type="text" name="new_profile_pic" id="new_profile_pic" maxlength="255" 
                                   placeholder="Enter profile picture URL">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="update_btn" disabled>Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Register Player Tab -->
    <?php if ($selectedOperation === 'register_player'): ?>
        <div class="tab-content">
            <div class="centered-form-container">
                <h3>➕ Register New Player</h3>
                <p>Create a new registered player account with username and email.</p>
                
                <form method="POST" class="profile-form">
                    <input type="hidden" name="action" value="register_player">
                    
                    <div class="form-group">
                        <label for="username">Username: <span class="required">*</span></label>
                        <input type="text" name="username" id="username" required maxlength="50" placeholder="Choose a unique username">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email: <span class="required">*</span></label>
                        <input type="email" name="email" id="email" required maxlength="100" placeholder="Enter email address">
                    </div>
                    
                    <div class="form-group">
                        <label for="profile_pic">Profile Picture URL:</label>
                        <input type="text" name="profile_pic" id="profile_pic" maxlength="255" placeholder="default_avatar.png" value="default_avatar.png">
                    </div>
                    
                    <button type="submit" class="btn btn-success">Register Player</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Delete Player Tab -->
    <?php if ($selectedOperation === 'delete_player'): ?>
        <div class="tab-content">
            <div class="centered-form-container">
                <h3>🗑️ Delete Player</h3>
                <div class="alert alert-warning">
                    <strong>⚠️ Warning:</strong> This action will permanently delete the player and all associated records (participations, achievements, lap records). This cannot be undone!
                </div>
                
                <form method="POST" class="profile-form" onsubmit="return confirmDeletion()">
                    <input type="hidden" name="action" value="delete_player">
                    <input type="hidden" name="delete_player_id" id="hidden_delete_player_id" value="">
                    
                    <div class="form-group">
                        <label for="delete_player_search">Search Player to Delete (Username or Player ID):</label>
                        <div class="search-group">
                            <div class="search-container">
                                <input type="text" id="delete_player_search" placeholder="Enter username or player ID..." 
                                       onkeyup="searchPlayerForDeletion(this.value)" autocomplete="off">
                            </div>
                            <div id="delete_search_status" class="search-status"></div>
                        </div>
                        <div id="delete_search_results" class="search-results hidden"></div>
                    </div>
                    
                    <div id="delete_player-details" class="hidden">
                        <div class="form-group">
                            <label for="current_delete_info">Player to Delete:</label>
                            <div id="current_delete_info" class="current-info">
                                <p>Search and select a player to view deletion information</p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_delete">Type "DELETE" to confirm:</label>
                            <input type="text" name="confirm_delete" id="confirm_delete" required placeholder="Type DELETE">
                        </div>
                        
                        <button type="submit" class="btn btn-danger" id="delete_btn" disabled>Delete Player</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let searchTimeout;
let selectedPlayer = null;

function searchPlayer(query) {
    clearTimeout(searchTimeout);
    const statusDiv = document.getElementById('search_status');
    const resultsDiv = document.getElementById('search_results');
    const updateBtn = document.getElementById('update_btn');
    
    if (query.trim().length < 1) {
        statusDiv.innerHTML = '';
        resultsDiv.classList.add('hidden');
        updateBtn.disabled = true;
        clearPlayerDetails();
        return;
    }
    
    statusDiv.innerHTML = '🔍 Searching...';
    
    searchTimeout = setTimeout(() => {
        // Search through available players
        const players = <?= json_encode($controller->getAllPlayers()) ?>;
        const results = players.filter(player => {
            return player.PlayerType === 'Registered' && (
                player.DisplayName.toLowerCase().includes(query.toLowerCase()) ||
                player.PlayerID.toString() === query.toString()
            );
        });
        
        if (results.length === 0) {
            statusDiv.innerHTML = '❌ No players found';
            resultsDiv.classList.add('hidden');
            updateBtn.disabled = true;
            clearPlayerDetails();
        } else if (results.length === 1) {
            // Exact match or single result
            const player = results[0];
            statusDiv.innerHTML = '✅ Player found';
            selectPlayer(player);
            resultsDiv.classList.add('hidden');
        } else {
            // Multiple results
            statusDiv.innerHTML = `📋 ${results.length} players found`;
            showSearchResults(results);
        }
    }, 300);
}

function showSearchResults(results) {
    const resultsDiv = document.getElementById('search_results');
    let html = '<div class="search-results-list">';
    
    results.forEach(player => {
        html += `
            <div class="search-result-item" onclick="selectPlayerFromResults(${player.PlayerID})">
                <div class="player-info">
                    <span class="player-id">(ID: ${player.PlayerID})</span>
                    <span class="player-name">${player.DisplayName}</span>
                </div>
                <div class="player-email">${player.Email}</div>
            </div>
        `;
    });
    
    html += '</div>';
    resultsDiv.innerHTML = html;
    resultsDiv.classList.remove('hidden');
}

function selectPlayerFromResults(playerId) {
    const players = <?= json_encode($controller->getAllPlayers()) ?>;
    const player = players.find(p => p.PlayerID == playerId);
    if (player) {
        selectPlayer(player);
        document.getElementById('search_results').classList.add('hidden');
        document.getElementById('player_search').value = player.DisplayName;
    }
}

function selectPlayer(player) {
    selectedPlayer = player;
    const statusDiv = document.getElementById('search_status');
    const updateBtn = document.getElementById('update_btn');
    const hiddenPlayerID = document.getElementById('hidden_player_id');
    
    statusDiv.innerHTML = '✅ Player verified';
    updateBtn.disabled = false;
    hiddenPlayerID.value = player.PlayerID;
    
    // Show current player information
    showCurrentPlayerInfo(player);
    
    // Pre-fill the form fields with current values
    document.getElementById('new_username').value = player.DisplayName;
    document.getElementById('new_email').value = player.Email || '';
    document.getElementById('new_profile_pic').value = player.ProfilePicture || 'default_avatar.png';
}

function showCurrentPlayerInfo(player) {
    const currentInfoDiv = document.getElementById('current_info');
    currentInfoDiv.innerHTML = `
        <div class="player-info-card">
            <h4>📋 Current Player Information</h4>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Player ID:</strong> <span>${player.PlayerID}</span>
                </div>
                <div class="info-item">
                    <strong>Username:</strong> <span>${player.DisplayName}</span>
                </div>
                <div class="info-item">
                    <strong>Email:</strong> <span>${player.Email || 'Not set'}</span>
                </div>
                <div class="info-item">
                    <strong>Profile Picture:</strong> <span>${player.ProfilePicture || 'default_avatar.png'}</span>
                </div>
            </div>
        </div>
    `;
}

function clearPlayerDetails() {
    selectedPlayer = null;
    document.getElementById('hidden_player_id').value = '';
    document.getElementById('current_info').innerHTML = '<p>Search and select a player to view current information</p>';
    document.getElementById('new_username').value = '';
    document.getElementById('new_email').value = '';
    document.getElementById('new_profile_pic').value = '';
}

function searchPlayerForDeletion(query) {
    clearTimeout(searchTimeout);
    const statusDiv = document.getElementById('delete_search_status');
    const resultsDiv = document.getElementById('delete_search_results');
    const deleteBtn = document.getElementById('delete_btn');
    
    if (query.trim().length < 1) {
        statusDiv.innerHTML = '';
        resultsDiv.classList.add('hidden');
        deleteBtn.disabled = true;
        clearPlayerDetailsForDeletion();
        return;
    }
    
    statusDiv.innerHTML = '🔍 Searching...';
    
    searchTimeout = setTimeout(() => {
        // Search through available players
        const players = <?= json_encode($controller->getAllPlayers()) ?>;
        const results = players.filter(player => {
            return player.PlayerType === 'Registered' && (
                player.DisplayName.toLowerCase().includes(query.toLowerCase()) ||
                player.PlayerID.toString() === query.toString()
            );
        });
        
        if (results.length === 0) {
            statusDiv.innerHTML = '❌ No players found';
            resultsDiv.classList.add('hidden');
            deleteBtn.disabled = true;
            clearPlayerDetailsForDeletion();
        } else if (results.length === 1) {
            // Exact match or single result
            const player = results[0];
            statusDiv.innerHTML = '✅ Player found';
            selectPlayerForDeletion(player);
            resultsDiv.classList.add('hidden');
        } else {
            // Multiple results
            statusDiv.innerHTML = `📋 ${results.length} players found`;
            showSearchResultsForDeletion(results);
        }
    }, 300);
}

function showSearchResultsForDeletion(results) {
    const resultsDiv = document.getElementById('delete_search_results');
    let html = '<div class="search-results-list">';
    
    results.forEach(player => {
        html += `
            <div class="search-result-item" onclick="selectPlayerFromResultsForDeletion(${player.PlayerID})">
                <div class="player-info">
                    <span class="player-id">(ID: ${player.PlayerID})</span>
                    <span class="player-name">${player.DisplayName}</span>
                </div>
                <div class="player-email">${player.Email}</div>
            </div>
        `;
    });
    
    html += '</div>';
    resultsDiv.innerHTML = html;
    resultsDiv.classList.remove('hidden');
}

function selectPlayerFromResultsForDeletion(playerId) {
    const players = <?= json_encode($controller->getAllPlayers()) ?>;
    const player = players.find(p => p.PlayerID == playerId);
    if (player) {
        selectPlayerForDeletion(player);
        document.getElementById('delete_search_results').classList.add('hidden');
        document.getElementById('delete_player_search').value = player.DisplayName;
    }
}

function selectPlayerForDeletion(player) {
    selectedPlayer = player;
    const statusDiv = document.getElementById('delete_search_status');
    const deleteBtn = document.getElementById('delete_btn');
    const hiddenPlayerID = document.getElementById('hidden_delete_player_id');
    const deleteDetails = document.getElementById('delete_player-details');
    
    statusDiv.innerHTML = '✅ Player verified';
    deleteBtn.disabled = false;
    hiddenPlayerID.value = player.PlayerID;
    
    // Show the deletion details container
    deleteDetails.classList.remove('hidden');
    
    // Show current player information
    showCurrentPlayerInfoForDeletion(player);
}

function showCurrentPlayerInfoForDeletion(player) {
    const currentInfoDiv = document.getElementById('current_delete_info');
    currentInfoDiv.innerHTML = `
        <div class="player-info-card">
            <h4>📋 Current Player Information</h4>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Player ID:</strong> <span>${player.PlayerID}</span>
                </div>
                <div class="info-item">
                    <strong>Username:</strong> <span>${player.DisplayName}</span>
                </div>
                <div class="info-item">
                    <strong>Email:</strong> <span>${player.Email || 'Not set'}</span>
                </div>
                <div class="info-item">
                    <strong>Profile Picture:</strong> <span>${player.ProfilePicture || 'default_avatar.png'}</span>
                </div>
            </div>
        </div>
    `;
}

function clearPlayerDetailsForDeletion() {
    selectedPlayer = null;
    document.getElementById('hidden_delete_player_id').value = '';
    document.getElementById('current_delete_info').innerHTML = '<p>Search and select a player to view deletion information</p>';
    document.getElementById('confirm_delete').value = '';
    document.getElementById('delete_player-details').classList.add('hidden');
}

function confirmDeletion() {
    const hiddenPlayerID = document.getElementById('hidden_delete_player_id');
    const confirmText = document.getElementById('confirm_delete').value;
    
    if (!hiddenPlayerID.value) {
        alert('Please search and select a player to delete.');
        return false;
    }
    
    if (confirmText !== 'DELETE') {
        alert('Please type "DELETE" to confirm deletion.');
        return false;
    }
    
    const playerSearch = document.getElementById('delete_player_search').value;
    return confirm(`Are you absolutely sure you want to delete ${playerSearch}?\n\nThis will permanently remove all associated records.\n\nThis action cannot be undone!`);
}
</script>
