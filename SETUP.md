# Setup Instructions for Developers

After cloning this project, follow these steps to get the application running:

## Step 1: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

## Step 2: Environment Setup

```bash
# Copy environment file (if .env doesn't exist)
cp .env.example .env
```

**Important:** The `.env` file is already configured and includes the `APP_KEY`. No changes are needed unless you're using a different database.

## Step 3: Create & Migrate Database

```bash
# Create MySQL database (if not already created)
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS cms_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF

# Run all migrations
php artisan migrate
```

This creates all required tables in the MySQL database.

**Note**: If you need to switch back to SQLite, see the [MIGRATION_TO_MYSQL.md](MIGRATION_TO_MYSQL.md) file for instructions.

## Step 4: Seed Database with Default User

```bash
# Seed the database with default admin user
php artisan db:seed
```

This creates the default admin account with these credentials:
- **Email:** `admin@cms.com`
- **Password:** `Admin@123`

## Step 5: Build Frontend Assets

```bash
# Build Vite assets for development
npm run dev

# OR for production
npm run build
```

## Step 6: Start Development Server

```bash
# Start Laravel development server (runs on http://localhost:8000)
php artisan serve
```

The application should now be accessible at `http://localhost:8000`

## Step 7: Login

1. Navigate to `http://localhost:8000/login`
2. Use these credentials:
   - **Email:** `admin@cms.com`
   - **Password:** `Admin@123`
3. After login, you'll be redirected to `/admin/dashboard`

---

## Troubleshooting

### "SQLSTATE[HY000] [1045] Access denied for user"
Verify MySQL credentials in `.env` file:
```bash
# Check DB_USERNAME, DB_PASSWORD, and DB_HOST
cat .env | grep DB_
```

### "SQLSTATE[HY000] [2002] No such file or directory"
MySQL server is not running. Start it:
```bash
# Windows: Use Services or MySQL Workbench
# macOS: brew services start mysql
# Linux: sudo systemctl start mysql
```

### Migrations fail
Try running with fresh database:
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Still can't login?
1. Verify the user exists: `php artisan tinker` → `User::all()`
2. Check database connection: `php artisan tinker` → `DB::connection()->getPDO()`

### Need to use SQLite instead?
See [MIGRATION_TO_MYSQL.md](MIGRATION_TO_MYSQL.md#rollback-to-sqlite-if-needed)
2. Re-seed the database: `php artisan db:seed --force`
3. Check `.env` file has correct `DB_CONNECTION=sqlite`

---

## Development Workflow

### Watch for CSS/JS changes
```bash
npm run dev
```

### Run tests
```bash
./vendor/bin/phpunit
```

### Generate new migration
```bash
php artisan make:migration migration_name
```

