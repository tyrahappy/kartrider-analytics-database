# 控制器目录

## 概述

此目录包含项目的所有控制器文件，采用 MVC 架构模式。控制器负责处理用户请求、业务逻辑和数据处理。

## 文件结构

```
controllers/
├── README.md                    # 本说明文件
├── DashboardController.php      # 主仪表板控制器（279行）
├── PlayerStatsController.php    # 玩家统计独立控制器（569行）
├── ProfileController.php        # 玩家档案控制器（331行）
├── QueriesController.php        # 查询工具控制器（313行）
├── TableViewerController.php    # 表格查看器控制器（160行）
└── dashboard/                   # Dashboard子模块目录
    ├── README.md                # Dashboard模块说明
    ├── PlayerStatsDashboardController.php # 玩家统计模块（416行）
    ├── SessionAnalyticsController.php # 会话分析模块（495行）
    └── AchievementDashboardController.php # 成就模块（404行）
```

## 控制器分类

### Dashboard 模块（重构后）

- `DashboardController.php`: 主控制器，负责路由和缓存管理
- `dashboard/PlayerStatsDashboardController.php`: 玩家统计模块
- `dashboard/SessionAnalyticsController.php`: 会话分析模块
- `dashboard/AchievementDashboardController.php`: 成就模块

### 独立功能控制器

- `PlayerStatsController.php`: 独立的玩家统计功能
- `ProfileController.php`: 玩家档案管理
- `QueriesController.php`: 数据库查询工具
- `TableViewerController.php`: 表格数据查看器

## 架构设计

### 继承关系

```
BaseController (includes/BaseController.php)
├── DashboardController
├── PlayerStatsController
├── ProfileController
├── QueriesController
├── TableViewerController
└── dashboard/
    ├── PlayerStatsDashboardController
    ├── SessionAnalyticsController
    └── AchievementDashboardController
```

### 模块化设计

- **主控制器**: 负责参数解析、缓存管理和路由分发
- **子控制器**: 专注于特定模块的业务逻辑
- **独立控制器**: 处理独立功能页面

## 功能说明

### Dashboard 模块

- **玩家统计**: 玩家数量、活跃度、胜率排名、参与度分布
- **会话分析**: 比赛统计、赛道分析、卡丁车使用、每日趋势
- **成就系统**: 成就追踪、完成率、排行榜、分布统计

### 独立功能

- **玩家档案**: 个人统计、历史记录、成就展示
- **查询工具**: 自定义 SQL 查询、数据导出
- **表格查看**: 数据库表结构查看、数据浏览

## 设计原则

### 单一职责

- 每个控制器只负责一个特定功能
- 避免功能耦合，便于维护和测试

### 模块化

- 相关功能组织在同一个控制器中
- 支持独立开发和测试

### 可扩展性

- 易于添加新的控制器和功能
- 支持插件式架构

## 使用方法

### 基本调用

```php
// 主控制器
$dashboard = new DashboardController();
$dashboard->run();

// 子控制器
$playerStats = new PlayerStatsDashboardController('all', 'all');
$data = $playerStats->getPlayerStatistics($pdo);
```

### 参数传递

- 时间过滤器: `all`, `7days`, `30days`, `3months`
- 玩家类型: `all`, `registered`, `guest`

## 性能优化

- 查询缓存机制
- InfinityFree 主机优化
- 错误处理和回退机制
- 查询限制和超时处理

## 开发规范

- 遵循 PSR-4 自动加载规范
- 使用统一的错误处理机制
- 保持代码注释完整
- 支持多环境配置

## 目录说明

### dashboard/ 子目录

包含 Dashboard 的三个核心子模块，每个模块负责特定的数据分析功能。详细说明请参考 `dashboard/README.md`。

### 模块化优势

- **清晰的结构**: 主控制器和子模块分离
- **易于维护**: 每个模块独立开发和测试
- **代码复用**: 子模块可以被其他控制器调用
- **扩展性强**: 便于添加新的分析模块
