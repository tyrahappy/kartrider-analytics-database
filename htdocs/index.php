<?php
/**
 * KartRider Analytics - Main Entry Point
 * 
 * This is the main landing page with welcome message and feature grid.
 */

require_once 'includes/BaseController.php';

class MainController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('KartRider Analytics - Welcome');
    }
    
    public function run() {
        $this->renderView(__DIR__ . '/views/layout.php', [
            'content' => $this->getMainContent()
        ]);
    }
    
    private function getMainContent() {
        return '
        <div class="main-landing">
            <div class="landing-container">
                <!-- Left Welcome Section -->
                <div class="welcome-section">
                    <div class="welcome-content">
                        <h1 class="welcome-title">Welcome to KartRider Analytics</h1>
                        <p class="welcome-subtitle">Professional Kart Racing Data Analytics Platform</p>
                        <div class="welcome-description">
                            <p>This is a comprehensive kart racing game data analytics system built with PHP and MySQL, providing complete data management, query analysis, and visualization capabilities.</p>
                            <p>Through an intuitive interface, you can easily access and manage game data, generate detailed statistical reports, and gain deep insights into player behavior and game performance.</p>
                        </div>
                        <div class="welcome-features">
                            <div class="feature-item">
                                <span class="feature-icon">📊</span>
                                <span>Real-time Data Analytics</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">🔍</span>
                                <span>Advanced Query Functions</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">📈</span>
                                <span>Visualization Charts</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">👤</span>
                                <span>Player Profile Management</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Features Grid -->
                <div class="features-section">
                    <div class="features-grid">
                        <a href="table_viewer.php" class="feature-card">
                            <div class="feature-icon">📋</div>
                            <h3>Table Viewer</h3>
                            <p>Browse and manage all tables in the database with search, sort, and filter capabilities</p>
                            <div class="feature-arrow">→</div>
                        </a>
                        
                        <a href="queries.php" class="feature-card">
                            <div class="feature-icon">🔍</div>
                            <h3>Dynamic Queries</h3>
                            <p>Execute custom SQL queries, analyze complex data relationships, and generate detailed reports</p>
                            <div class="feature-arrow">→</div>
                        </a>
                        
                        <a href="profile.php" class="feature-card">
                            <div class="feature-icon">👤</div>
                            <h3>Player Profiles</h3>
                            <p>Manage player information, view detailed statistics, and track achievements and race records</p>
                            <div class="feature-arrow">→</div>
                        </a>
                        
                        <a href="dashboard.php" class="feature-card">
                            <div class="feature-icon">📊</div>
                            <h3>Data Analytics Dashboard</h3>
                            <p>View real-time statistical charts, analyze trends, and monitor key performance indicators</p>
                            <div class="feature-arrow">→</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .main-landing {
            min-height: calc(100vh - 200px);
            padding: 2rem 0;
        }
        
        .landing-container {
            display: flex;
            gap: 3rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .welcome-section {
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .welcome-content {
            max-width: 500px;
        }
        
        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .welcome-subtitle {
            font-size: 1.3rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
            font-weight: 500;
        }
        
        .welcome-description {
            margin-bottom: 2rem;
        }
        
        .welcome-description p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .welcome-features {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #495057;
        }
        
        .feature-icon {
            font-size: 1.2rem;
        }
        
        .features-section {
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            width: 100%;
        }
        
        .feature-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 2rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:hover {
            border-color: #007bff;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.15);
        }
        
        .feature-card .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.8rem;
        }
        
        .feature-card p {
            color: #6c757d;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .feature-arrow {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.2rem;
            color: #007bff;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover .feature-arrow {
            opacity: 1;
        }
        
        @media (max-width: 1024px) {
            .landing-container {
                flex-direction: column;
                gap: 2rem;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .landing-container {
                padding: 0 1rem;
            }
            
            .welcome-title {
                font-size: 1.8rem;
            }
            
            .feature-card {
                padding: 1.5rem;
            }
        }
        </style>
        ';
    }
}

// 运行主控制器
$controller = new MainController();
$controller->run();
?>
