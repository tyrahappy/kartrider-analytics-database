<?php
/**
 * Dashboard Modules Inline View
 * Displays the dashboard content with navigation and modules
 */

// Get controller instance if available
$controller = $controller ?? null;
$selectedModule = $selectedModule ?? 'player_stats';
$timeFilter = $timeFilter ?? 'all';
$playerTypeFilter = $playerTypeFilter ?? 'all';
$dashboardData = $dashboardData ?? [];

// Get modules configuration
$modules = [
    'player_stats' => '👥 Player Statistics',
    'session_analytics' => '🏁 Session Analytics', 
    'achievements' => '🏆 Achievements'
];

$timeFilterOptions = [
    'all' => 'All Time',
    '7days' => 'Last 7 Days',
    '30days' => 'Last 30 Days',
    '3months' => 'Last 3 Months'
];

$playerTypeOptions = [
    'all' => 'All Players',
    'registered' => 'Registered Only',
    'guest' => 'Guest Only'
];
?>



<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="dashboard-sidebar">
        <h3>📊 Dashboard Modules</h3>
        <ul class="module-nav">
            <?php foreach ($modules as $moduleKey => $moduleName): ?>
                <li>
                    <a href="?module=<?= urlencode($moduleKey) ?>&time_filter=<?= urlencode($timeFilter) ?>&player_type=<?= urlencode($playerTypeFilter) ?>" 
                       class="<?= $selectedModule === $moduleKey ? 'active' : '' ?>">
                        <?= htmlspecialchars($moduleName) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <!-- Filters Section -->
        <div class="filter-section">
            <h4>🔍 Filters</h4>
            <form method="GET" class="dashboard-filters">
                <input type="hidden" name="module" value="<?= htmlspecialchars($selectedModule) ?>">
                
                <div class="filter-group">
                    <label for="time_filter">Time Period:</label>
                    <select name="time_filter" id="time_filter">
                        <?php foreach ($timeFilterOptions as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= $timeFilter === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="player_type">Player Type:</label>
                    <select name="player_type" id="player_type">
                        <?php foreach ($playerTypeOptions as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= $playerTypeFilter === $value ? 'selected' : '' ?>>
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
    <div class="dashboard-main">
        <!-- Header -->
        <div class="dashboard-header">
            <h2><?= htmlspecialchars($modules[$selectedModule] ?? 'Dashboard') ?></h2>
            <div class="filter-info">
                📅 <?= htmlspecialchars($timeFilterOptions[$timeFilter]) ?> | 
                👤 <?= htmlspecialchars($playerTypeOptions[$playerTypeFilter]) ?>
            </div>
        </div>

        <?php if (isset($errorMessage) && $errorMessage): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php elseif (isset($dashboardData['error'])): ?>
            <div class="alert alert-error">
                <strong>Data Error:</strong> <?= htmlspecialchars($dashboardData['error']) ?>
            </div>
        <?php elseif (empty($dashboardData)): ?>
            <div class="no-data">
                <h3>📭 No Data Available</h3>
                <p>No data found for the selected filters. Try adjusting your filter settings.</p>
                <p>🛠️ Data tools are currently unavailable</p>
            </div>
        <?php else: ?>
            <?php 
            // Display module content based on selected module
            switch ($selectedModule):
                case 'player_stats': ?>
                    <!-- Player Statistics Module -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?= number_format($dashboardData['total_players'] ?? 0) ?></h3>
                            <p>Total Players</p>
                        </div>
                        <div class="stat-card">
                            <h3><?= number_format($dashboardData['active_players'] ?? 0) ?></h3>
                            <p>Active Players</p>
                        </div>
                        <div class="stat-card">
                            <h3><?= $dashboardData['avg_races_per_player'] ?? '0.0' ?></h3>
                            <p>Avg Races/Player</p>
                        </div>
                        <div class="stat-card">
                            <h3><?= $dashboardData['active_rate_recent_week'] ?? '0.0' ?>%</h3>
                            <p>Active Rate Recent Week</p>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="charts-container">
                        <!-- Player Type Distribution Pie Chart -->
                        <?php if (!empty($dashboardData['player_distribution'])): ?>
                            <div class="chart-wrapper">
                                <h3>👥 Player Type Distribution</h3>
                                <div class="chart-container">
                                    <canvas id="playerTypeChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Race Participation Distribution Bar Chart -->
                        <?php if (!empty($dashboardData['race_participation_distribution'])): ?>
                            <div class="chart-wrapper">
                                <h3>🏁 Race Participation Distribution</h3>
                                <div class="chart-container">
                                    <canvas id="raceParticipationChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($dashboardData['win_rate_ranking'])): ?>
                        <h3>🏆 Top Players by Win Rate</h3>
                        <table class="data-table dashboard-table-centered">
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th>Type</th>
                                    <th>Total Races</th>
                                    <th>Wins</th>
                                    <th>Win Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboardData['win_rate_ranking'] as $index => $player): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($player['PlayerName']) ?></td>
                                        <td><?= htmlspecialchars($player['PlayerType']) ?></td>
                                        <td><?= number_format(is_numeric($player['TotalRaces']) ? (int)$player['TotalRaces'] : 0) ?></td>
                                        <td><?= number_format(is_numeric($player['Wins']) ? (int)$player['Wins'] : 0) ?></td>
                                        <td><?= is_numeric($player['WinRate']) ? (float)$player['WinRate'] : 0 ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <!-- Chart JavaScript -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Player Type Distribution Pie Chart
                            <?php if (!empty($dashboardData['player_distribution'])): ?>
                            const playerTypeCanvas = document.getElementById('playerTypeChart');
                            if (playerTypeCanvas) {
                                const playerTypeCtx = playerTypeCanvas.getContext('2d');
                                const playerTypeChart = new Chart(playerTypeCtx, {
                                    type: 'pie',
                                    data: {
                                        labels: [<?php 
                                            $labels = array_map(function($item) { 
                                                return "'" . addslashes($item['PlayerType']) . "'"; 
                                            }, $dashboardData['player_distribution']);
                                            echo implode(', ', $labels);
                                        ?>],
                                        datasets: [{
                                            data: [<?php 
                                                $values = array_map(function($item) { 
                                                    return $item['PlayerCount']; 
                                                }, $dashboardData['player_distribution']);
                                                echo implode(', ', $values);
                                            ?>],
                                            backgroundColor: [
                                                '#667eea',
                                                '#764ba2',
                                                '#f093fb',
                                                '#f5576c',
                                                '#4facfe',
                                                '#00f2fe'
                                            ],
                                            borderWidth: 2,
                                            borderColor: '#fff'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                                labels: {
                                                    padding: 20,
                                                    usePointStyle: true,
                                                    font: {
                                                        size: 12
                                                    }
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                                        return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                            <?php endif; ?>

                            // Race Participation Distribution Bar Chart
                            <?php if (!empty($dashboardData['race_participation_distribution'])): ?>
                            const raceParticipationCanvas = document.getElementById('raceParticipationChart');
                            if (raceParticipationCanvas) {
                                const raceParticipationCtx = raceParticipationCanvas.getContext('2d');
                                const raceParticipationChart = new Chart(raceParticipationCtx, {
                                    type: 'bar',
                                    data: {
                                        labels: [<?php 
                                            $labels = array_map(function($item) { 
                                                return "'" . addslashes($item['ParticipationRange']) . "'"; 
                                            }, $dashboardData['race_participation_distribution']);
                                            echo implode(', ', $labels);
                                        ?>],
                                        datasets: [{
                                            label: 'Number of Players',
                                            data: [<?php 
                                                $values = array_map(function($item) { 
                                                    return $item['PlayerCount']; 
                                                }, $dashboardData['race_participation_distribution']);
                                                echo implode(', ', $values);
                                            ?>],
                                            backgroundColor: 'rgba(102, 126, 234, 0.8)',
                                            borderColor: 'rgba(102, 126, 234, 1)',
                                            borderWidth: 1,
                                            borderRadius: 4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' players';
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    stepSize: 1,
                                                    callback: function(value) {
                                                        return Number.isInteger(value) ? value : '';
                                                    }
                                                },
                                                title: {
                                                    display: true,
                                                    text: 'Number of Players'
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Race Participation Range'
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                            <?php endif; ?>
                        });
                    </script>
                    <?php break;

                case 'session_analytics': ?>
                    <!-- Session Analytics Module -->
                    <?php if (isset($dashboardData['message'])): ?>
                        <div class="alert alert-info">
                            <strong>Info:</strong> <?= htmlspecialchars($dashboardData['message']) ?>
                        </div>
                    <?php else: ?>
                        <!-- Key Statistics -->
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3><?= number_format($dashboardData['total_races'] ?? 0) ?></h3>
                                <p>Total Races</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= $dashboardData['avg_race_time'] ?? '0.0' ?>s</h3>
                                <p>Avg Race Time</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= htmlspecialchars($dashboardData['popular_track'] ?? 'N/A') ?></h3>
                                <p>Most Popular Track</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= htmlspecialchars($dashboardData['popular_kart'] ?? 'N/A') ?></h3>
                                <p>Most Popular Kart</p>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="charts-container">
                            <!-- Track Difficulty Usage Pie Chart -->
                            <?php if (!empty($dashboardData['difficulty_distribution'])): ?>
                                <div class="chart-wrapper">
                                    <h3>🏁 Track Difficulty Usage</h3>
                                    <div class="chart-container">
                                        <canvas id="difficultyChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Kart Usage Statistics Bar Chart -->
                            <?php if (!empty($dashboardData['kart_usage'])): ?>
                                <div class="chart-wrapper">
                                    <h3>🏎️ Kart Usage Statistics</h3>
                                    <div class="chart-container">
                                        <canvas id="kartUsageChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Daily Race Trends -->
                        <?php if (!empty($dashboardData['daily_trends'])): ?>
                            <h3>📈 Recent Race Activity (Last 7 Days)</h3>
                            <table class="data-table dashboard-table-centered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Races</th>
                                        <th>Total Participations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dashboardData['daily_trends'] as $trend): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($trend['RaceDay']) ?></td>
                                            <td><?= number_format($trend['DailyRaces']) ?></td>
                                            <td><?= number_format($trend['TotalParticipations']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <!-- Session Analytics Charts JavaScript -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Track Difficulty Usage Pie Chart
                                <?php if (!empty($dashboardData['difficulty_distribution'])): ?>
                                const difficultyCanvas = document.getElementById('difficultyChart');
                                if (difficultyCanvas) {
                                    const difficultyCtx = difficultyCanvas.getContext('2d');
                                    const difficultyChart = new Chart(difficultyCtx, {
                                        type: 'pie',
                                        data: {
                                            labels: [<?php 
                                                $labels = array_map(function($item) { 
                                                    return "'" . addslashes($item['TrackDifficulty']) . "'"; 
                                                }, $dashboardData['difficulty_distribution']);
                                                echo implode(', ', $labels);
                                            ?>],
                                            datasets: [{
                                                data: [<?php 
                                                    $values = array_map(function($item) { 
                                                        return $item['RaceCount']; 
                                                    }, $dashboardData['difficulty_distribution']);
                                                    echo implode(', ', $values);
                                                ?>],
                                                backgroundColor: [
                                                    '#28a745', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1', '#20c997'
                                                ],
                                                borderWidth: 2,
                                                borderColor: '#fff'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    position: 'bottom',
                                                    labels: {
                                                        padding: 20,
                                                        usePointStyle: true,
                                                        font: {
                                                            size: 12
                                                        }
                                                    }
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                                            return context.label + ': ' + context.parsed.toLocaleString() + ' races (' + percentage + '%)';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                                <?php endif; ?>

                                // Kart Usage Statistics Bar Chart
                                <?php if (!empty($dashboardData['kart_usage'])): ?>
                                const kartUsageCanvas = document.getElementById('kartUsageChart');
                                if (kartUsageCanvas) {
                                    const kartUsageCtx = kartUsageCanvas.getContext('2d');
                                    const kartUsageChart = new Chart(kartUsageCtx, {
                                        type: 'bar',
                                        data: {
                                            labels: [<?php 
                                                $labels = array_map(function($item) { 
                                                    return "'" . addslashes($item['KartName']) . "'"; 
                                                }, array_slice($dashboardData['kart_usage'], 0, 10));
                                                echo implode(', ', $labels);
                                            ?>],
                                            datasets: [{
                                                label: 'Usage Count',
                                                data: [<?php 
                                                    $values = array_map(function($item) { 
                                                        return $item['UsageCount']; 
                                                    }, array_slice($dashboardData['kart_usage'], 0, 10));
                                                    echo implode(', ', $values);
                                                ?>],
                                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                                borderColor: 'rgba(54, 162, 235, 1)',
                                                borderWidth: 1,
                                                borderRadius: 4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' times';
                                                        }
                                                    }
                                                }
                                            },
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        stepSize: 1,
                                                        callback: function(value) {
                                                            return Number.isInteger(value) ? value : '';
                                                        }
                                                    },
                                                    title: {
                                                        display: true,
                                                        text: 'Usage Count'
                                                    }
                                                },
                                                x: {
                                                    title: {
                                                        display: true,
                                                        text: 'Kart Names'
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                                <?php endif; ?>
                            });
                        </script>
                    <?php endif; ?>
                    <?php break;

                case 'achievements': ?>
                    <!-- Achievements Module -->
                    <?php if (isset($dashboardData['message'])): ?>
                        <div class="alert alert-info">
                            <strong>Info:</strong> <?= htmlspecialchars($dashboardData['message']) ?>
                        </div>
                    <?php else: ?>
                        <!-- Achievement Statistics -->
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3><?= number_format($dashboardData['total_achievements'] ?? 0) ?></h3>
                                <p>Total Achievements</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= number_format($dashboardData['earned_achievements'] ?? 0) ?></h3>
                                <p>Times Earned</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= $dashboardData['avg_achievements_per_player'] ?? '0.0' ?></h3>
                                <p>Avg per Player</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= $dashboardData['completion_rate'] ?? '0.0' ?>%</h3>
                                <p>Completion Rate</p>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="charts-container">
                            <!-- Achievement Popularity Doughnut Chart -->
                            <?php if (!empty($dashboardData['achievement_popularity'])): ?>
                                <div class="chart-wrapper">
                                    <h3>🎯 Achievement Popularity</h3>
                                    <div class="chart-container">
                                        <canvas id="achievementPopularityChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Achievement Distribution Bar Chart -->
                            <?php if (!empty($dashboardData['completion_distribution'])): ?>
                                <div class="chart-wrapper">
                                    <h3>📊 Achievement Distribution</h3>
                                    <div class="chart-container">
                                        <canvas id="achievementDistributionChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Achievement Popularity Ranking -->
                        <?php if (!empty($dashboardData['achievement_popularity'])): ?>
                            <h3>🎯 Top 5 Achievement Details</h3>
                            <table class="data-table dashboard-table-centered">
                                <thead>
                                    <tr>
                                        <th>Achievement</th>
                                        <th>Description</th>
                                        <th>Points</th>
                                        <th>Times Earned</th>
                                        <th>Completion Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($dashboardData['achievement_popularity'], 0, 5) as $achievement): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($achievement['AchievementName']) ?></strong></td>
                                            <td><?= htmlspecialchars($achievement['Description']) ?></td>
                                            <td><?= number_format($achievement['PointsAwarded']) ?></td>
                                            <td><?= number_format($achievement['EarnedCount']) ?></td>
                                            <td><?= $achievement['CompletionRate'] ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <?php endif; ?>

                        <!-- Achievement Charts JavaScript -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Achievement Popularity Doughnut Chart
                                <?php if (!empty($dashboardData['achievement_popularity'])): ?>
                                const achievementPopularityCanvas = document.getElementById('achievementPopularityChart');
                                if (achievementPopularityCanvas) {
                                    const achievementPopularityCtx = achievementPopularityCanvas.getContext('2d');
                                    const achievementPopularityChart = new Chart(achievementPopularityCtx, {
                                        type: 'doughnut',
                                        data: {
                                            labels: [<?php 
                                                $labels = array_map(function($item) { 
                                                    return "'" . addslashes($item['AchievementName']) . "'"; 
                                                }, array_slice($dashboardData['achievement_popularity'], 0, 8));
                                                echo implode(', ', $labels);
                                            ?>],
                                            datasets: [{
                                                data: [<?php 
                                                    $values = array_map(function($item) { 
                                                        return $item['EarnedCount']; 
                                                    }, array_slice($dashboardData['achievement_popularity'], 0, 8));
                                                    echo implode(', ', $values);
                                                ?>],
                                                backgroundColor: [
                                                    '#667eea', '#764ba2', '#f093fb', '#f5576c',
                                                    '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'
                                                ],
                                                borderWidth: 2,
                                                borderColor: '#fff'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            cutout: '50%',
                                            plugins: {
                                                legend: {
                                                    position: 'bottom',
                                                    labels: {
                                                        padding: 15,
                                                        usePointStyle: true,
                                                        font: {
                                                            size: 11
                                                        }
                                                    }
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                                            return context.label + ': ' + context.parsed.toLocaleString() + ' times (' + percentage + '%)';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                                <?php endif; ?>

                                // Achievement Distribution Bar Chart
                                <?php if (!empty($dashboardData['completion_distribution'])): ?>
                                const achievementDistributionCanvas = document.getElementById('achievementDistributionChart');
                                if (achievementDistributionCanvas) {
                                    const achievementDistributionCtx = achievementDistributionCanvas.getContext('2d');
                                    const achievementDistributionChart = new Chart(achievementDistributionCtx, {
                                        type: 'bar',
                                        data: {
                                            labels: [<?php 
                                                $labels = array_map(function($item) { 
                                                    return "'" . addslashes($item['AchievementRange']) . "'"; 
                                                }, $dashboardData['completion_distribution']);
                                                echo implode(', ', $labels);
                                            ?>],
                                            datasets: [{
                                                label: 'Number of Players',
                                                data: [<?php 
                                                    $values = array_map(function($item) { 
                                                        return $item['PlayerCount']; 
                                                    }, $dashboardData['completion_distribution']);
                                                    echo implode(', ', $values);
                                                ?>],
                                                backgroundColor: 'rgba(118, 75, 162, 0.8)',
                                                borderColor: 'rgba(118, 75, 162, 1)',
                                                borderWidth: 1,
                                                borderRadius: 4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const total = <?php echo array_sum(array_column($dashboardData['completion_distribution'], 'PlayerCount')); ?>;
                                                            const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                                                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' players (' + percentage + '%)';
                                                        },
                                                        title: function(context) {
                                                            const label = context[0].label;
                                                            if (label === '0') return '0 Achievements';
                                                            if (label === '10+') return '10+ Achievements';
                                                            return label + ' Achievements';
                                                        }
                                                    }
                                                }
                                            },
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        stepSize: 1,
                                                        callback: function(value) {
                                                            return Number.isInteger(value) ? value : '';
                                                        }
                                                    },
                                                    title: {
                                                        display: true,
                                                        text: 'Number of Players'
                                                    }
                                                },
                                                x: {
                                                    title: {
                                                        display: true,
                                                        text: 'Number of Achievements'
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                            });
                        </script>
                    <?php endif; ?>
                    <?php break;

                case 'session_analytics': ?>
                    <!-- Session Analytics Module JavaScript -->
                    <?php if (!empty($dashboardData['difficulty_distribution']) || !empty($dashboardData['kart_usage'])): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Track Difficulty Usage Pie Chart
                                <?php if (!empty($dashboardData['difficulty_distribution'])): ?>
                                const difficultyCanvas = document.getElementById('difficultyChart');
                                if (difficultyCanvas) {
                                    const difficultyCtx = difficultyCanvas.getContext('2d');
                                    const difficultyChart = new Chart(difficultyCtx, {
                                        type: 'pie',
                                        data: {
                                            labels: [<?php 
                                                $labels = array_map(function($item) { 
                                                    return "'" . addslashes($item['TrackDifficulty']) . "'"; 
                                                }, $dashboardData['difficulty_distribution']);
                                                echo implode(', ', $labels);
                                            ?>],
                                            datasets: [{
                                                data: [<?php 
                                                    $values = array_map(function($item) { 
                                                        return $item['RaceCount']; 
                                                    }, $dashboardData['difficulty_distribution']);
                                                    echo implode(', ', $values);
                                                ?>],
                                                backgroundColor: [
                                                    '#4ade80', '#facc15', '#f97316', '#ef4444', '#8b5cf6'
                                                ],
                                                borderWidth: 2,
                                                borderColor: '#fff'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    position: 'bottom',
                                                    labels: {
                                                        padding: 15,
                                                        usePointStyle: true,
                                                        font: {
                                                            size: 11
                                                        }
                                                    }
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                                            return context.label + ': ' + context.parsed.toLocaleString() + ' races (' + percentage + '%)';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                                <?php endif; ?>

                                // Kart Usage Statistics Bar Chart
                                <?php if (!empty($dashboardData['kart_usage'])): ?>
                                const kartUsageCanvas = document.getElementById('kartUsageChart');
                                if (kartUsageCanvas) {
                                    const kartUsageCtx = kartUsageCanvas.getContext('2d');
                                    const kartUsageChart = new Chart(kartUsageCtx, {
                                        type: 'bar',
                                        data: {
                                            labels: [<?php 
                                                $labels = array_map(function($item) { 
                                                    return "'" . addslashes($item['KartName']) . "'"; 
                                                }, array_slice($dashboardData['kart_usage'], 0, 10)); // Top 10 karts
                                                echo implode(', ', $labels);
                                            ?>],
                                            datasets: [{
                                                label: 'Usage Count',
                                                data: [<?php 
                                                    $values = array_map(function($item) { 
                                                        return $item['UsageCount']; 
                                                    }, array_slice($dashboardData['kart_usage'], 0, 10));
                                                    echo implode(', ', $values);
                                                ?>],
                                                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                                                borderColor: 'rgba(99, 102, 241, 1)',
                                                borderWidth: 1,
                                                borderRadius: 4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' uses';
                                                        }
                                                    }
                                                }
                                            },
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    title: {
                                                        display: true,
                                                        text: 'Usage Count'
                                                    }
                                                },
                                                x: {
                                                    title: {
                                                        display: true,
                                                        text: 'Kart Name'
                                                    },
                                                    ticks: {
                                                        maxRotation: 45,
                                                        minRotation: 45
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                                <?php endif; ?>
                            });
                        </script>
                    <?php endif; ?>
                    <?php break;

                default: ?>
                    <div class="alert alert-warning">
                        <strong>Unknown Module:</strong> <?= htmlspecialchars($selectedModule) ?>
                    </div>
                    <?php break;
            endswitch; ?>
        <?php endif; ?>
    </div>
</div>


