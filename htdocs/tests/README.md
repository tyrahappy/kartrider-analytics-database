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

## Test Categories

### Core Functionality Tests

- `test_refactored_dashboard.php`: Verifies Dashboard functionality after refactoring
- `dashboard_test.php`: Dashboard core functionality test
- `test_complete_dashboard.php`: Complete dashboard integration test

### Data Tests

- `test_avg_race_time.php`: Average race time calculation test
- `test_player_distribution.php`: Player distribution statistics test
- `test_completion_rate.php`: Achievement completion rate test

### Environment Tests

- `local_test.php`: Local development environment test
- `infinityfree_test.php`: InfinityFree free hosting compatibility test
- `smart_config_test.php`: Smart configuration system test

### Debugging Tools

- `debug.php`: General debugging utility
- `simple_test.php`: Simple functionality verification
- `test_fixes.php`: Fix verification tests

## Usage

### Run All Tests

```bash
cd assets/tests
php test_refactored_dashboard.php
```

### Run Specific Tests

```bash
php dashboard_test.php
php infinityfree_test.php
```

### Debug Mode

```bash
php debug.php
```

## Test Description

- All test files can be run independently
- Test results will be displayed in the browser
- Includes detailed error information and debugging information
- Supports configuration testing for different environments

## Notes

- Ensure database connection is normal before running tests
- Some tests may require specific database data
- Please be cautious when running test files in production environment
