# SQLite to MySQL Migration Guide - DB-1

## Overview
This document details the migration from SQLite to MySQL for the CMS Portal application.

## Changes Made

### 1. Configuration Updates

#### `config/database.php`
- **Changed**: Default database connection from `sqlite` to `mysql`
- **Line 21**: `'default' => env('DB_CONNECTION', 'mysql'),`

#### `.env.example`
- **Changed**: Database connection settings
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=cms_portal
  DB_USERNAME=root
  DB_PASSWORD=
  ```

#### `.env` (Project file)
- **Updated**: Same database configuration as `.env.example`

---

## Migration Compatibility Analysis

### Migrations Reviewed: 14 files
All migrations use Laravel's **database-agnostic Schema Builder**, ensuring full compatibility with both SQLite and MySQL.

### Migration Files:
1. ✅ `0001_01_01_000000_create_users_table.php`
2. ✅ `0001_01_01_000001_create_cache_table.php`
3. ✅ `0001_01_01_000002_create_jobs_table.php`
4. ✅ `2026_04_28_101602_add_role_to_users_table.php`
5. ✅ `2026_04_28_101718_create_categories_table.php`
6. ✅ `2026_04_28_101838_create_tags_table.php`
7. ✅ `2026_04_28_101950_create_posts_table.php`
8. ✅ `2026_04_28_102105_create_post_tag_table.php`
9. ✅ `2026_04_28_102202_create_pages_table.php`
10. ✅ `2026_04_28_102253_create_media_table.php`
11. ✅ `2026_04_28_102333_create_settings_table.php`
12. ✅ `2026_05_04_160000_create_activity_logs_table.php`
13. ✅ `2026_05_04_170000_add_new_roles_to_users_table.php`
14. ✅ `2026_05_05_100000_add_custom_permissions_to_users_table.php`

### Key Compatibility Findings:

| Feature | SQLite | MySQL | Status |
|---------|--------|-------|--------|
| `$table->id()` | INTEGER | BIGINT UNSIGNED | ✅ Compatible |
| `$table->foreignId()` | INTEGER | BIGINT UNSIGNED | ✅ Compatible |
| `enum()` columns | Supported | Supported | ✅ Compatible |
| `json()` columns | Supported | Supported | ✅ Compatible |
| Foreign key constraints | Supported | Supported | ✅ Compatible |
| onDelete('cascade') | Supported | Supported | ✅ Compatible |
| nullOnDelete() | Supported | Supported | ✅ Compatible |
| Timestamps | Supported | Supported | ✅ Compatible |

### SQLite-Specific Code Found: **NONE**
- No raw SQL queries
- No database-specific functions
- No explicit `autoincrement` directives
- All queries use Laravel's Schema Builder abstraction layer

**Conclusion**: No code modifications needed. All migrations will work seamlessly with MySQL.

---

## Setup Instructions for MySQL

### Prerequisites
- MySQL Server (5.7+ or 8.0+) installed and running
- MySQL user with create database privileges

### Step 1: Create MySQL Database

```bash
# Using MySQL CLI (replace 'cms_portal_test' if using different database name)
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS cms_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF
```

Or using phpMyAdmin/DBeaver GUI:
```sql
CREATE DATABASE cms_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Update `.env` File

Verify your `.env` file has the correct MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms_portal
DB_USERNAME=root
DB_PASSWORD=
```

Update `DB_PASSWORD` if your MySQL user has a password.

### Step 3: Install Composer Dependencies

```bash
composer install
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Run Migrations

```bash
php artisan migrate
```

Expected output:
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table.php
Migrated:  0001_01_01_000000_create_users_table.php (XXms)
...
```

### Step 6: Seed Database (Optional)

```bash
php artisan db:seed
```

This creates the default admin user:
- **Email**: `admin@cms.com`
- **Password**: `Admin@123`

---

## Testing: Fresh Migration & Seeding

To test the complete migration setup with a fresh database:

```bash
php artisan migrate:fresh --seed
```

This command will:
1. Drop all tables
2. Re-run all migrations
3. Seed the database with test data
4. Verify all 14 migrations execute without errors

### Expected Output:
```
Dropped all tables successfully.
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table.php
Migrated:  0001_01_01_000000_create_users_table.php (XXms)
Migrating: 0001_01_01_000001_create_cache_table.php
Migrated:  0001_01_01_000001_create_cache_table.php (XXms)
...
[All 14 migrations completed]
Seeding: Database\Seeders\DatabaseSeeder
Seeded:  Database\Seeders\DatabaseSeeder (XXms)
Database seeding completed successfully.
```

---

## Troubleshooting

### Error: "SQLSTATE[HY000] [1045] Access denied for user"
**Solution**: Verify MySQL credentials in `.env` file match your MySQL user.

### Error: "SQLSTATE[HY000] [2002] No such file or directory"
**Solution**: Ensure MySQL server is running:
```bash
# macOS with Homebrew
brew services start mysql

# Linux with systemctl
sudo systemctl start mysql

# Windows
# Start MySQL from Services or MySQL Workbench
```

### Error: "SQLSTATE[HY000] [1064] You have an error in your SQL syntax"
**Solution**: This should not occur as all migrations use Laravel's Schema Builder. If it does:
1. Run `php artisan migrate:rollback`
2. Verify all migrations are syntactically correct

### Error: "SQLSTATE[23000] Integrity constraint violation"
**Solution**: If foreign key constraints fail:
1. Run `php artisan migrate:fresh` to start fresh
2. Check that all foreign key relationships are properly defined

---

## Rollback to SQLite (If Needed)

If you need to revert to SQLite, follow these steps:

### 1. Update `.env`:
```env
DB_CONNECTION=sqlite
```

### 2. Run migrations:
```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```

---

## Summary of Changes

| File | Change | Impact |
|------|--------|--------|
| `config/database.php` | Default connection: sqlite → mysql | ✅ High |
| `.env.example` | Added MySQL configuration | ✅ High |
| `.env` | Updated to MySQL configuration | ✅ High |
| Migrations | None (all compatible) | ✅ No changes needed |
| Application code | None required | ✅ No changes needed |

---

## References

- [Laravel Database Configuration](https://laravel.com/docs/11.x/database)
- [Laravel Migrations](https://laravel.com/docs/11.x/migrations)
- [MySQL Character Sets](https://dev.mysql.com/doc/refman/8.0/en/charset-applications.html)

---

**Last Updated**: May 5, 2026  
**Task**: DB-1 - Switch from SQLite to MySQL  
**Status**: ✅ Complete
