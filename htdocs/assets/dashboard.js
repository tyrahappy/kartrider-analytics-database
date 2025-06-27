/**
 * KartRider Dashboard JavaScript
 * 
 * This file contains all client-side JavaScript logic for the dashboard,
 * including Chart.js initialization and interactive features.
 */

// Global variables for chart instances
let dashboardCharts = {};

/**
 * Initialize dashboard charts based on module and data
 */
function initializeDashboardCharts(module, data) {
    // Clear existing charts
    Object.values(dashboardCharts).forEach(chart => {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
    });
    dashboardCharts = {};

    // Initialize charts based on module
    switch (module) {
        case 'player_stats':
            initializePlayerStatsCharts(data);
            break;
        case 'session_analytics':
            initializeSessionAnalyticsCharts(data);
            break;
        case 'achievements':
            initializeAchievementsCharts(data);
            break;
        case 'race_performance':
            initializeRacePerformanceCharts(data);
            break;
    }
}

/**
 * Initialize Player Statistics charts
 */
function initializePlayerStatsCharts(data) {
    // Player Type Distribution Pie Chart
    if (data.player_distribution && document.getElementById('playerTypeChart')) {
        const playerTypeLabels = data.player_distribution.map(item => item.PlayerType);
        const playerTypeCounts = data.player_distribution.map(item => item.PlayerCount);

        dashboardCharts.playerType = new Chart(document.getElementById('playerTypeChart'), {
            type: 'pie',
            data: {
                labels: playerTypeLabels,
                datasets: [{
                    data: playerTypeCounts,
                    backgroundColor: ['#3498db', '#e74c3c'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Race Participation Distribution Bar Chart
    if (data.race_participation_distribution && document.getElementById('raceParticipationChart')) {
        const raceRangeLabels = data.race_participation_distribution.map(item => item.RaceRange);
        const raceRangeCounts = data.race_participation_distribution.map(item => item.PlayerCount);

        dashboardCharts.raceParticipation = new Chart(document.getElementById('raceParticipationChart'), {
            type: 'bar',
            data: {
                labels: raceRangeLabels,
                datasets: [{
                    label: 'Number of Players',
                    data: raceRangeCounts,
                    backgroundColor: '#2ecc71',
                    borderColor: '#27ae60',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

/**
 * Initialize Session Analytics charts
 */
function initializeSessionAnalyticsCharts(data) {
    // Track Difficulty Distribution
    if (data.difficulty_distribution && document.getElementById('difficultyChart')) {
        const difficultyLabels = data.difficulty_distribution.map(item => item.TrackDifficulty);
        const difficultyCounts = data.difficulty_distribution.map(item => item.RaceCount);

        dashboardCharts.difficulty = new Chart(document.getElementById('difficultyChart'), {
            type: 'doughnut',
            data: {
                labels: difficultyLabels,
                datasets: [{
                    data: difficultyCounts,
                    backgroundColor: ['#2ecc71', '#f39c12', '#e74c3c', '#9b59b6'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Daily Race Trends
    if (data.daily_trends && document.getElementById('dailyTrendsChart')) {
        const trendLabels = data.daily_trends.map(item => item.RaceDay);
        const trendCounts = data.daily_trends.map(item => item.DailyRaces);

        dashboardCharts.dailyTrends = new Chart(document.getElementById('dailyTrendsChart'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Daily Races',
                    data: trendCounts,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

/**
 * Initialize Achievements charts
 */
function initializeAchievementsCharts(data) {
    // Achievement Completion Distribution
    if (data.completion_distribution && document.getElementById('completionChart')) {
        const completionLabels = data.completion_distribution.map(item => item.AchievementRange);
        const completionCounts = data.completion_distribution.map(item => item.PlayerCount);

        dashboardCharts.completion = new Chart(document.getElementById('completionChart'), {
            type: 'bar',
            data: {
                labels: completionLabels,
                datasets: [{
                    label: 'Number of Players',
                    data: completionCounts,
                    backgroundColor: '#f39c12',
                    borderColor: '#e67e22',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

/**
 * Initialize Race Performance charts
 */
function initializeRacePerformanceCharts(data) {
    // Finishing Position Distribution
    if (data.rank_distribution && document.getElementById('rankChart')) {
        const rankLabels = data.rank_distribution.map(item => 'Position ' + item.FinishingRank);
        const rankCounts = data.rank_distribution.map(item => item.RankCount);

        dashboardCharts.rank = new Chart(document.getElementById('rankChart'), {
            type: 'bar',
            data: {
                labels: rankLabels,
                datasets: [{
                    label: 'Number of Finishes',
                    data: rankCounts,
                    backgroundColor: ['#f1c40f', '#95a5a6', '#cd7f32', '#3498db', '#e74c3c', '#9b59b6', '#2ecc71', '#e67e22'],
                    borderColor: '#2c3e50',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

/**
 * Utility function to format numbers
 */
function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

/**
 * Utility function to format time
 */
function formatTime(seconds) {
    if (seconds < 60) {
        return seconds.toFixed(2) + 's';
    }
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toFixed(2).padStart(5, '0')}`;
}

/**
 * Initialize interactive features when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function () {
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add loading states to filter form
    const filterForm = document.querySelector('.dashboard-filters');
    if (filterForm) {
        filterForm.addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.textContent = 'Loading...';
                submitBtn.disabled = true;
            }
        });
    }

    // Add table sorting functionality
    initializeTableSorting();

    // Add responsive table features
    initializeResponsiveTables();
});

/**
 * Initialize table sorting functionality
 */
function initializeTableSorting() {
    const tables = document.querySelectorAll('.dashboard-table');

    tables.forEach(table => {
        const headers = table.querySelectorAll('th');

        headers.forEach((header, index) => {
            // Skip action columns or non-sortable columns
            if (header.classList.contains('no-sort')) return;

            header.style.cursor = 'pointer';
            header.classList.add('sortable');

            header.addEventListener('click', function () {
                sortTable(table, index);
            });
        });
    });
}

/**
 * Sort table by column index
 */
function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const header = table.querySelectorAll('th')[columnIndex];

    // Determine current sort direction
    const currentDir = header.getAttribute('data-sort-dir') || 'asc';
    const newDir = currentDir === 'asc' ? 'desc' : 'asc';

    // Clear all sort indicators
    table.querySelectorAll('th').forEach(th => {
        th.removeAttribute('data-sort-dir');
        th.classList.remove('sort-asc', 'sort-desc');
    });

    // Set new sort direction
    header.setAttribute('data-sort-dir', newDir);
    header.classList.add('sort-' + newDir);

    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();

        // Try to parse as numbers
        const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
        const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));

        let comparison = 0;
        if (!isNaN(aNum) && !isNaN(bNum)) {
            comparison = aNum - bNum;
        } else {
            comparison = aValue.localeCompare(bValue);
        }

        return newDir === 'asc' ? comparison : -comparison;
    });

    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Initialize responsive table features
 */
function initializeResponsiveTables() {
    const tables = document.querySelectorAll('.dashboard-table');

    tables.forEach(table => {
        // Add wrapper for horizontal scrolling on mobile
        if (!table.parentElement.classList.contains('table-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('table-wrapper');
            table.parentElement.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}

/**
 * Chart color schemes
 */
const colorSchemes = {
    primary: ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#e67e22', '#34495e'],
    success: ['#27ae60', '#2ecc71', '#16a085', '#1abc9c'],
    warning: ['#f39c12', '#e67e22', '#d35400', '#f1c40f'],
    info: ['#3498db', '#2980b9', '#5dade2', '#85c1e9'],
    danger: ['#e74c3c', '#c0392b', '#ec7063', '#f1948a']
};

/**
 * Get color scheme for charts
 */
function getColorScheme(type = 'primary', count = 1) {
    const scheme = colorSchemes[type] || colorSchemes.primary;
    if (count <= scheme.length) {
        return scheme.slice(0, count);
    }

    // Generate additional colors if needed
    const colors = [...scheme];
    while (colors.length < count) {
        colors.push(...scheme);
    }
    return colors.slice(0, count);
}
