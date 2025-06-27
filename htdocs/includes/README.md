# 核心类库目录

## 概述

此目录包含项目的核心类库文件，提供基础功能和通用服务。这些文件被项目中的其他组件广泛使用，是系统的基础架构。

## 文件结构

```
includes/
├── README.md                    # 本说明文件
├── BaseController.php           # 基础控制器类（148行）
├── DatabaseService.php          # 数据库服务类（174行）
└── AssetHelper.php              # 资源助手类（91行）
```

## 核心类说明

### 基础控制器

- `BaseController.php`: 所有控制器的基类
  - 提供通用的控制器功能
  - 处理页面标题和错误信息
  - 管理数据库连接
  - 提供视图渲染方法

### 数据库服务

- `DatabaseService.php`: 数据库连接和管理服务
  - 数据库连接池管理
  - 连接状态监控
  - 错误处理和重连机制
  - 支持多种数据库类型

### 资源助手

- `AssetHelper.php`: 静态资源管理助手
  - CSS 和 JavaScript 文件管理
  - 资源版本控制
  - 缓存优化
  - 资源路径解析

## 设计原则

### 单一职责

- 每个类只负责一个特定功能
- 避免功能耦合
- 便于测试和维护

### 可重用性

- 提供通用的基础功能
- 支持多种使用场景
- 易于扩展和定制

### 稳定性

- 经过充分测试
- 向后兼容
- 错误处理完善

## 使用方法

### 基础控制器

```php
class CustomController extends BaseController {
    public function __construct() {
        parent::__construct();
        $this->setPageTitle('页面标题');
    }

    public function run() {
        // 业务逻辑
        $this->renderView('template.php', $data);
    }
}
```

### 数据库服务

```php
// 获取数据库连接
$db = new DatabaseService();
$pdo = $db->getConnection();

// 检查连接状态
if ($db->isConnected()) {
    // 执行数据库操作
}
```

### 资源助手

```php
// 获取CSS文件路径
$cssPath = AssetHelper::getCssPath('style.css');

// 获取JavaScript文件路径
$jsPath = AssetHelper::getJsPath('script.js');
```

## 继承关系

### 控制器继承链

```
BaseController
├── DashboardController
├── PlayerStatsDashboardController
├── SessionAnalyticsController
├── AchievementDashboardController
├── PlayerStatsController
├── ProfileController
├── QueriesController
└── TableViewerController
```

### 服务依赖关系

```
DatabaseService (数据库连接)
    ↓
BaseController (基础功能)
    ↓
具体控制器 (业务逻辑)
```

## 配置说明

### 数据库配置

- 支持多种数据库类型
- 连接池配置
- 超时和重试设置
- 错误日志记录

### 资源配置

- 静态资源路径
- 缓存策略
- 版本控制
- 压缩选项

## 错误处理

### 异常处理

- 统一的异常类型
- 详细的错误信息
- 错误日志记录
- 用户友好的错误提示

### 调试支持

- 调试模式开关
- 详细的调试信息
- 性能监控
- 错误追踪

## 性能优化

### 数据库优化

- 连接池管理
- 查询缓存
- 连接复用
- 超时处理

### 资源优化

- 文件压缩
- 缓存机制
- 懒加载
- 版本控制

## 扩展说明

### 添加新服务

1. 创建新的服务类
2. 实现必要的接口
3. 添加错误处理
4. 更新相关文档

### 修改现有类

- 保持向后兼容
- 添加适当的注释
- 更新相关测试
- 记录变更日志

## 开发规范

### 代码规范

- 遵循 PSR 标准
- 完整的注释
- 统一的命名规范
- 适当的错误处理

### 测试要求

- 单元测试覆盖
- 集成测试
- 性能测试
- 兼容性测试
