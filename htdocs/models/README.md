# 模型目录

## 概述

此目录包含项目的所有模型文件，负责数据访问和业务逻辑处理。模型层遵循 MVC 架构模式，提供数据操作的抽象接口。

## 文件结构

```
models/
├── README.md                    # 本说明文件
├── BaseModel.php                # 基础模型类
├── PlayerModel.php              # 玩家数据模型
├── RaceModel.php                # 比赛数据模型
└── AchievementModel.php         # 成就数据模型
```

## 模型说明

### 基础模型

- `BaseModel.php`: 所有模型的基类
  - 提供数据库连接管理
  - 定义通用的 CRUD 操作
  - 包含错误处理机制

### 业务模型

- `PlayerModel.php`: 玩家相关数据操作

  - 玩家信息查询和更新
  - 玩家统计数据处理
  - 玩家档案管理

- `RaceModel.php`: 比赛相关数据操作

  - 比赛记录查询
  - 比赛统计计算
  - 赛道数据分析

- `AchievementModel.php`: 成就相关数据操作
  - 成就信息管理
  - 成就完成情况统计
  - 成就排行榜处理

## 设计原则

### 数据抽象

- 封装数据库操作细节
- 提供简洁的数据访问接口
- 支持多种数据库类型

### 业务逻辑

- 在模型层处理复杂业务逻辑
- 确保数据一致性和完整性
- 提供数据验证功能

### 性能优化

- 实现查询缓存机制
- 优化数据库查询语句
- 支持批量操作

## 使用方法

### 基本操作

```php
// 创建模型实例
$playerModel = new PlayerModel();

// 查询数据
$players = $playerModel->getAllPlayers();

// 更新数据
$playerModel->updatePlayer($playerId, $data);

// 删除数据
$playerModel->deletePlayer($playerId);
```

### 继承基类

```php
class CustomModel extends BaseModel {
    public function customMethod() {
        // 自定义业务逻辑
    }
}
```

## 数据库表对应

### 玩家相关表

- `Player`: 玩家基本信息
- `PlayerCredentials`: 玩家认证信息
- `RegisteredPlayer`: 注册玩家信息

### 比赛相关表

- `Race`: 比赛记录
- `Participation`: 参与记录
- `Track`: 赛道信息
- `Kart`: 卡丁车信息

### 成就相关表

- `Achievement`: 成就定义
- `PlayerAchievement`: 玩家成就记录

## 开发规范

### 命名规范

- 模型类名以`Model`结尾
- 方法名使用驼峰命名法
- 数据库字段名使用下划线命名法

### 错误处理

- 统一的异常处理机制
- 详细的错误日志记录
- 友好的错误信息返回

### 代码注释

- 类和方法必须有注释
- 复杂逻辑需要详细说明
- 参数和返回值要明确标注

## 扩展说明

### 添加新模型

1. 继承`BaseModel`类
2. 实现必要的 CRUD 方法
3. 添加业务逻辑方法
4. 更新相关文档

### 数据库迁移

- 模型变更需要同步数据库结构
- 提供数据迁移脚本
- 保持向后兼容性
