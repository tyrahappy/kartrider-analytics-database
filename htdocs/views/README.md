# 视图目录

## 概述

此目录包含项目的所有视图文件，负责页面展示和用户界面。视图层遵循 MVC 架构模式，提供 HTML 模板和前端交互。

## 文件结构

```
views/
├── README.md                    # 本说明文件
├── layout.php                   # 主布局模板（134行）
├── error.php                    # 错误页面模板（114行）
├── dashboard_modules_inline.php # 仪表板内联模块（1002行）
├── profile_content.php          # 玩家档案内容（320行）
├── queries_content.php          # 查询工具内容（105行）
└── table_viewer_content.php     # 表格查看器内容（113行）
```

## 视图说明

### 布局模板

- `layout.php`: 主布局文件

  - 定义页面基本结构
  - 包含头部、导航、内容区域、底部
  - 处理 CSS 和 JavaScript 资源加载
  - 支持响应式设计

- `error.php`: 错误页面模板
  - 统一的错误信息展示
  - 友好的错误提示界面
  - 提供返回和帮助链接

### 内容模板

- `dashboard_modules_inline.php`: 仪表板模块内容

  - 玩家统计模块展示
  - 会话分析模块展示
  - 成就系统模块展示
  - 支持动态数据加载

- `profile_content.php`: 玩家档案页面内容

  - 个人统计信息展示
  - 历史记录列表
  - 成就展示区域
  - 数据可视化图表

- `queries_content.php`: 查询工具页面内容

  - SQL 查询界面
  - 查询结果展示
  - 数据导出功能
  - 查询历史记录

- `table_viewer_content.php`: 表格查看器内容
  - 数据库表结构展示
  - 数据浏览界面
  - 分页和排序功能
  - 数据筛选工具

## 设计原则

### 分离关注点

- 视图只负责展示逻辑
- 不包含业务逻辑处理
- 通过控制器传递数据

### 可重用性

- 组件化设计
- 支持模板继承
- 便于维护和扩展

### 用户体验

- 响应式设计
- 直观的界面布局
- 良好的交互反馈

## 模板语法

### 基本语法

```php
<!-- 变量输出 -->
<?php echo $variable; ?>

<!-- 条件判断 -->
<?php if ($condition): ?>
    <!-- 内容 -->
<?php endif; ?>

<!-- 循环遍历 -->
<?php foreach ($array as $item): ?>
    <!-- 循环内容 -->
<?php endforeach; ?>
```

### 布局继承

```php
<!-- 在子模板中 -->
<?php $this->renderView('layout.php', $data); ?>
```

## 前端资源

### CSS 文件

- `assets/style.css`: 主样式文件
- 响应式设计支持
- 现代化 UI 组件

### JavaScript 文件

- `assets/dashboard.js`: 仪表板交互逻辑
- `assets/tabs.js`: 标签页功能
- AJAX 数据加载
- 动态图表更新

## 数据传递

### 控制器到视图

```php
// 在控制器中
$this->renderView('template.php', [
    'data' => $data,
    'user' => $user
]);
```

### 视图中的数据访问

```php
<!-- 在视图中 -->
<?php echo $data['key']; ?>
<?php echo $user->name; ?>
```

## 开发规范

### 命名规范

- 文件名使用小写和下划线
- 内容文件以`_content.php`结尾
- 布局文件使用描述性名称

### 代码组织

- 保持模板简洁
- 避免复杂的 PHP 逻辑
- 使用适当的缩进和注释

### 性能优化

- 减少数据库查询
- 使用缓存机制
- 优化资源加载

## 扩展说明

### 添加新视图

1. 创建新的模板文件
2. 在控制器中添加渲染逻辑
3. 更新相关样式和脚本
4. 测试页面功能

### 主题定制

- 支持多主题切换
- 可配置的颜色方案
- 灵活的布局选项
