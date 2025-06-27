# Dashboard 子模块目录

## 概述

此目录包含 Dashboard 的三个核心子模块，每个模块负责特定的数据分析功能。这些模块被主`DashboardController`调用，提供模块化的数据处理能力。

## 文件结构

```
dashboard/
├── README.md                           # 本说明文件
├── PlayerStatsDashboardController.php  # 玩家统计模块（416行）
├── SessionAnalyticsController.php      # 会话分析模块（495行）
└── AchievementDashboardController.php  # 成就系统模块（404行）
```

## 模块说明

### 1. PlayerStatsDashboardController.php

**功能**: 玩家统计数据分析

- **主要指标**:

  - 总玩家数量
  - 活跃玩家数量
  - 平均比赛次数
  - 最近一周活跃率
  - 玩家类型分布
  - 胜率排行榜
  - 比赛参与度分布

- **核心方法**:
  - `getPlayerStatistics($pdo)`: 获取玩家统计数据
  - `calculateAvgRacesPerPlayer($pdo)`: 计算平均比赛次数
  - `getWinRateRanking($pdo)`: 获取胜率排行榜
  - `getPlayerDistribution($pdo)`: 获取玩家分布

### 2. SessionAnalyticsController.php

**功能**: 会话和比赛分析

- **主要指标**:

  - 比赛总数和趋势
  - 赛道使用统计
  - 卡丁车使用分析
  - 每日比赛趋势
  - 比赛时长分析
  - 参与度统计

- **核心方法**:
  - `getSessionAnalytics($pdo)`: 获取会话分析数据
  - `getRaceStatistics($pdo)`: 获取比赛统计
  - `getTrackUsage($pdo)`: 获取赛道使用情况
  - `getKartUsage($pdo)`: 获取卡丁车使用情况

### 3. AchievementDashboardController.php

**功能**: 成就系统分析

- **主要指标**:

  - 成就完成情况
  - 成就完成率
  - 成就排行榜
  - 成就分布统计
  - 玩家成就进度

- **核心方法**:
  - `getAchievementData($pdo)`: 获取成就数据
  - `getAchievementCompletion($pdo)`: 获取成就完成情况
  - `getAchievementRanking($pdo)`: 获取成就排行榜
  - `getAchievementDistribution($pdo)`: 获取成就分布

## 设计原则

### 模块化设计

- 每个模块独立处理特定领域的数据
- 清晰的职责分离
- 便于独立开发和测试

### 统一接口

- 所有模块都提供`getXXXData($pdo)`方法
- 统一的错误处理机制
- 一致的返回数据格式

### 性能优化

- 针对 InfinityFree 等免费主机优化
- 查询限制和超时处理
- 缓存机制支持

## 使用方法

### 在主控制器中调用

```php
// 玩家统计模块
$playerStats = new PlayerStatsDashboardController($timeFilter, $playerTypeFilter);
$playerData = $playerStats->getPlayerStatistics($pdo);

// 会话分析模块
$sessionAnalytics = new SessionAnalyticsController($timeFilter, $playerTypeFilter);
$sessionData = $sessionAnalytics->getSessionAnalytics($pdo);

// 成就模块
$achievements = new AchievementDashboardController($timeFilter, $playerTypeFilter);
$achievementData = $achievements->getAchievementData($pdo);
```

### 参数说明

- `$timeFilter`: 时间过滤器 ('all', '7days', '30days', '3months')
- `$playerTypeFilter`: 玩家类型过滤器 ('all', 'registered', 'guest')
- `$pdo`: 数据库连接对象

## 数据格式

### 统一返回格式

```php
[
    'module_name' => '数据名称',
    'data' => [
        // 具体数据内容
    ],
    'error' => null, // 错误信息（如果有）
    'message' => '提示信息'
]
```

### 错误处理

- 所有模块都包含`getFallbackData()`方法
- 提供降级数据当查询失败时
- 详细的错误日志记录

## 扩展说明

### 添加新模块

1. 创建新的控制器文件
2. 实现必要的数据获取方法
3. 在主控制器中添加引用
4. 更新相关文档

### 修改现有模块

- 保持接口兼容性
- 更新相关测试
- 记录变更日志

## 性能考虑

### 查询优化

- 使用 LIMIT 限制结果集
- 避免复杂的 JOIN 操作
- 利用索引优化查询

### 缓存策略

- 支持查询结果缓存
- 缓存时间可配置
- 缓存键基于参数生成

### 错误恢复

- 提供降级数据
- 友好的错误提示
- 自动重试机制
