# Tests Directory

This directory contains all test and verification scripts for the KartRider Analytics project.

## Structure

- `test_refactor.php` - Refactoring test page, verifies refactored structure and interface.
- `english_verification.php` - Verifies that all user-facing content is in English.
- `asset_test.php` - Asset loading and helper test.
- `dashboard_test.php` - Dashboard module structure and data test.
- `test_dashboard_structure.php` - Dashboard structure and layout test.
- `test_refactored_dashboard.php` - Refactored dashboard test.
- `test_fixes.php` - Tests for bug fixes.
- `test_completion_rate.php` - Completion rate calculation test.
- `test_daily_trends.php` - Daily trends and analytics test.
- `generate_achievement_data.php` - Script to generate fake achievement data for testing.
- `generate_fake_race_data.php` - Script to generate fake race data for testing.
- `check_achievement_table.php` - Checks achievement table structure and data.
- `infinityfree_test.php` - Tests for InfinityFree hosting compatibility.
- `local_test.php` - Local environment test.
- `simple_test.php` - Simple test script.
- `smart_config_test.php` - Smart config and environment test.
- `debug.php` - Debugging utility script.

## Usage

Run any test file directly in the browser or via CLI to verify specific functionality or data integrity.

## 测试分类

### 核心功能测试

- `test_refactored_dashboard.php`: 验证 Dashboard 重构后的功能
- `dashboard_test.php`: 仪表板核心功能测试
- `test_complete_dashboard.php`: 完整仪表板集成测试

### 数据测试

- `test_avg_race_time.php`: 平均比赛时间计算测试
- `test_player_distribution.php`: 玩家分布统计测试
- `test_completion_rate.php`: 成就完成率测试

### 环境测试

- `local_test.php`: 本地开发环境测试
- `infinityfree_test.php`: InfinityFree 免费主机兼容性测试
- `smart_config_test.php`: 智能配置系统测试

### 调试工具

- `debug.php`: 通用调试工具
- `simple_test.php`: 简单功能验证
- `test_fixes.php`: 修复验证测试

## 使用方法

### 运行所有测试

```bash
cd assets/tests
php test_refactored_dashboard.php
```

### 运行特定测试

```bash
php dashboard_test.php
php infinityfree_test.php
```

### 调试模式

```bash
php debug.php
```

## 测试说明

- 所有测试文件都可以独立运行
- 测试结果会显示在浏览器中
- 包含详细的错误信息和调试信息
- 支持不同环境的配置测试

## 注意事项

- 运行测试前请确保数据库连接正常
- 某些测试可能需要特定的数据库数据
- 生产环境请谨慎运行测试文件
