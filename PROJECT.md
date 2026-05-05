# CMS Portal - Complete Project Documentation

**Last Updated:** May 5, 2026  
**PHP Version:** 8.4+  
**Laravel Version:** 13  
**Database:** SQLite (default)

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Tech Stack](#tech-stack)
3. [Database Schema](#database-schema)
4. [Project Folder Structure](#project-folder-structure)
5. [Features & Functionality](#features--functionality)
6. [User Roles & Permissions](#user-roles--permissions)
7. [API Routes](#api-routes)
8. [Configuration Files](#configuration-files)
9. [Key Services & Helpers](#key-services--helpers)
10. [Security Features](#security-features)
11. [Quick Start](#quick-start)
12. [Common Workflows](#common-workflows)

---

## Project Overview

**CMS Portal** is a lightweight, modern content management system built with Laravel 13. It allows authenticated users to create and manage:

- **Blog Posts** - with categories, tags, featured images, SEO metadata, and multiple statuses (draft, published, archived)
- **Custom Pages** - with drag-and-drop page builder for visual content creation
- **Public Website** - configurable home page, blog listing, individual pages
- **Media Library** - organized file uploads with access tracking
- **Site Settings** - identity, header/footer content, home page configuration
- **User Management** - multi-role permission system with admin, editor, post editor, and page editor roles
- **Activity Logging** - track all user actions for audit purposes

The system provides a complete admin panel (`/admin`) for content management and a public-facing website with:
- Home page (`/`)
- Blog listing (`/blog`)
- Individual posts (`/blog/{slug}`)
- Custom pages (`/pages/{slug}`)
- Media serving (`/media/{path}`)

---

## Tech Stack

### Backend
- **PHP 8.4+** - Server-side language
- **Laravel 13** - Web application framework
- **Laravel Breeze** - Authentication scaffolding
- **Mews Purifier** - HTML sanitization for stored content
- **Eloquent ORM** - Database abstraction layer

### Frontend
- **Vite** - Build tool and dev server
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight reactive JavaScript
- **Blade Templates** - Server-side templating

### Database
- **SQLite** - Default lightweight database (configurable to MySQL/PostgreSQL)

### Development Tools
- **Composer** - PHP dependency management
- **npm** - JavaScript dependency management
- **PHPUnit** - Testing framework
- **Laravel Artisan** - CLI commands

---

## Database Schema

### Tables Overview

```
users (authentication & roles)
├── posts (blog content)
│   ├── categories (post grouping)
│   └── tags (post labeling)
│
├── pages (custom pages)
├── media (file uploads)
├── settings (site configuration)
├── activity_logs (audit trail)
│
└── sessions (authentication sessions)
```

### Table Definitions

#### **1. `users` Table**
Stores authenticated users with roles and permissions.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `name` | STRING | User full name |
| `email` | STRING | Unique email address |
| `email_verified_at` | TIMESTAMP | Email verification timestamp (nullable) |
| `password` | STRING | Hashed password |
| `role` | ENUM | Role assignment: `admin`, `editor`, `post_editor`, `page_editor` |
| `custom_permissions` | JSON | Additional custom permissions (nullable) |
| `remember_token` | STRING | Remember me token (nullable) |
| `created_at` | TIMESTAMP | Account creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Relationships:**
- Has many `posts` (user_id foreign key)
- Has many `pages` (user_id foreign key)
- Has many `media` (user_id foreign key)
- Has many `activity_logs` (user_id foreign key)

---

#### **2. `posts` Table**
Blog posts with SEO metadata and publishing controls.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `user_id` | BIGINT | Foreign key to `users` table (cascade delete) |
| `title` | STRING(255) | Post title |
| `slug` | STRING(255) | Unique URL slug |
| `content` | LONGTEXT | HTML content (sanitized via Mews Purifier) |
| `excerpt` | TEXT | Short summary (nullable) |
| `featured_image` | STRING(255) | Image file path (nullable) |
| `status` | ENUM | `draft`, `published`, `archived` (default: `draft`) |
| `category_id` | BIGINT | Foreign key to `categories` (set null on delete) |
| `meta_title` | STRING(255) | SEO title (nullable) |
| `meta_description` | TEXT | SEO description (nullable) |
| `published_at` | TIMESTAMP | Publication timestamp (nullable, set when published) |
| `created_at` | TIMESTAMP | Creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Relationships:**
- Belongs to `user` (author)
- Belongs to `category` (optional)
- Belongs to many `tags` (through `post_tag` pivot table)

**Status Lifecycle:**
- `draft` → `published` (when first published or scheduled)
- `published` → `archived` (when no longer active)
- `draft` → `archived` (manual archival)

---

#### **3. `categories` Table**
Post grouping and classification.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `name` | STRING(100) | Category name |
| `slug` | STRING(100) | Unique URL slug |
| `description` | TEXT | Category description (nullable) |
| `created_at` | TIMESTAMP | Creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Relationships:**
- Has many `posts`

---

#### **4. `tags` Table**
Flexible post labeling and filtering.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `name` | STRING(100) | Tag name |
| `slug` | STRING(100) | Unique URL slug |
| `created_at` | TIMESTAMP | Creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Relationships:**
- Belongs to many `posts` (through `post_tag` pivot table)

---

#### **5. `post_tag` Table (Pivot)**
Many-to-many relationship between posts and tags.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `post_id` | BIGINT | Foreign key to `posts` (cascade delete) |
| `tag_id` | BIGINT | Foreign key to `tags` (cascade delete) |
| `created_at` | TIMESTAMP | Association creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

---

#### **6. `pages` Table**
Custom static/dynamic pages with visual builder support.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `user_id` | BIGINT | Foreign key to `users` table (cascade delete) |
| `title` | STRING(255) | Page title |
| `slug` | STRING(255) | Unique URL slug |
| `content` | LONGTEXT | Page content (JSON from page builder or HTML) |
| `status` | ENUM | `draft`, `published` (default: `draft`) |
| `template` | STRING(100) | Template name/type (nullable) |
| `meta_title` | STRING(255) | SEO title (nullable) |
| `meta_description` | TEXT | SEO description (nullable) |
| `created_at` | TIMESTAMP | Creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Relationships:**
- Belongs to `user` (author/creator)

**Special Pages:**
- Home page can be configured via settings
- Header section (shared across site)
- Footer section (shared across site)

---

#### **7. `media` Table**
File uploads management and tracking.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `user_id` | BIGINT | Foreign key to `users` (cascade delete) |
| `file_name` | STRING(255) | Original filename |
| `file_path` | STRING(500) | Storage path (within `storage/app/public/`) |
| `file_type` | STRING(50) | MIME type (e.g., `image/jpeg`, `application/pdf`) |
| `file_size` | INTEGER | Size in bytes |
| `created_at` | TIMESTAMP | Upload timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Relationships:**
- Belongs to `user` (uploader)

**Storage Location:**
- Files stored in `storage/app/public/` (publicly accessible)
- Served through `/media/{path}` route with Laravel routing

---

#### **8. `settings` Table**
Key-value store for site configuration.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `key` | STRING | Unique setting key |
| `value` | TEXT | Setting value (nullable) |
| `created_at` | TIMESTAMP | Creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Common Settings Keys:**
- `site_name` - Website name
- `site_description` - Site tagline/description
- `home_page_id` - ID of page to display as home
- `posts_per_page` - Pagination limit
- `header_content` - Shared header HTML
- `footer_content` - Shared footer HTML

**Access Pattern:**
```php
// Get setting with fallback
$value = Setting::getValue('site_name', 'Default CMS');

// Set setting (creates or updates)
Setting::setValue('site_name', 'My CMS');
```

---

#### **9. `activity_logs` Table**
Audit trail for user actions and system events.

| Column | Type | Details |
|--------|------|---------|
| `id` | BIGINT | Primary key, auto-increment |
| `user_id` | BIGINT | Foreign key to `users` (nullable on delete) |
| `action` | STRING(150) | Action type (e.g., `post.created`, `page.updated`) |
| `description` | STRING(255) | Human-readable action description (nullable) |
| `subject_type` | STRING | Polymorphic model class (nullable) |
| `subject_id` | BIGINT | Polymorphic model ID (nullable) |
| `properties` | JSON | Additional metadata/changes (nullable) |
| `ip_address` | STRING(45) | Client IP address (nullable, IPv6 compatible) |
| `user_agent` | TEXT | Browser/client information (nullable) |
| `created_at` | TIMESTAMP | Action timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

**Relationships:**
- Belongs to `user` (who performed action)
- Polymorphic relationship to any model (`subject_type`/`subject_id`)

**Recorded Actions:**
- `post.created` - When post is created
- `post.updated` - When post is modified
- `post.deleted` - When post is deleted
- `page.created` - When page is created
- `page.updated` - When page is modified
- `page.deleted` - When page is deleted
- `user.*` - User management actions
- `category.*` - Category management actions

**Note:** Admin user actions are NOT logged (for performance)

---

#### **10. `password_reset_tokens` Table**
Temporary tokens for password reset functionality.

| Column | Type | Details |
|--------|------|---------|
| `email` | STRING | Primary key (user email) |
| `token` | STRING | Password reset token |
| `created_at` | TIMESTAMP | Token creation timestamp |

---

#### **11. `sessions` Table**
Laravel session storage.

| Column | Type | Details |
|--------|------|---------|
| `id` | STRING | Primary key (session ID) |
| `user_id` | BIGINT | Foreign key to `users` (nullable) |
| `ip_address` | STRING(45) | Client IP address |
| `user_agent` | TEXT | Browser information |
| `payload` | LONGTEXT | Serialized session data |
| `last_activity` | INTEGER | Timestamp of last activity |

---

### Database Relationships Diagram

```
┌─────────────────┐
│     USERS       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email (UQ)      │
│ password        │
│ role            │◄──────┐
│ custom_perm     │       │ (has many)
│ created_at      │       │
└─────────────────┘       │
        │                 │
        │ (1:Many)        │
        ├──────────────────┼──────────────┐
        │                  │              │
        ▼                  ▼              ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│    POSTS     │  │    PAGES     │  │    MEDIA     │
├──────────────┤  ├──────────────┤  ├──────────────┤
│ id (PK)      │  │ id (PK)      │  │ id (PK)      │
│ user_id (FK) │  │ user_id (FK) │  │ user_id (FK) │
│ category_id  │  │ title        │  │ file_name    │
│ title        │  │ slug (UQ)    │  │ file_path    │
│ slug (UQ)    │  │ content      │  │ file_type    │
│ content      │  │ status       │  │ file_size    │
│ status       │  │ template     │  │ created_at   │
│ featured_img │  │ meta_title   │  └──────────────┘
│ published_at │  │ created_at   │
└──────────────┘  └──────────────┘
        │
        │ (N:1)
        ▼
┌──────────────────┐
│   CATEGORIES     │
├──────────────────┤
│ id (PK)          │
│ name             │
│ slug (UQ)        │
│ description      │
└──────────────────┘

POSTS ◄──────────► TAGS (Many-to-Many via post_tag table)
        │
        ▼
┌──────────────────┐
│     POST_TAG     │ (Pivot Table)
├──────────────────┤
│ id (PK)          │
│ post_id (FK)     │
│ tag_id (FK)      │
└──────────────────┘

POSTS/PAGES/USERS ◄──────────► ACTIVITY_LOGS (Audit Trail)
```

---

## Project Folder Structure

### Root Level Files

| File | Purpose |
|------|---------|
| `artisan` | Laravel CLI command file |
| `composer.json` | PHP dependencies definition |
| `package.json` | JavaScript/Node dependencies definition |
| `phpunit.xml` | PHPUnit testing configuration |
| `postcss.config.js` | PostCSS configuration for Tailwind |
| `tailwind.config.js` | Tailwind CSS configuration |
| `vite.config.js` | Vite build configuration |
| `README.md` | Project introduction |
| `PROJECT.md` | This comprehensive documentation |
| `updates.md` | Historical changelog and updates |
| `report.md` | Project report/progress tracking |
| `work.md` | Development notes and TODOs |

### `/app` - Application Core Logic

```
app/
├── Helpers/
│   └── helpers.php              # Global helper functions
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── UserController.php    # User CRUD for admin panel
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php
│   │   │   ├── ConfirmablePasswordController.php
│   │   │   ├── EmailVerificationNotificationController.php
│   │   │   ├── EmailVerificationPromptController.php
│   │   │   ├── NewPasswordController.php
│   │   │   ├── PasswordController.php
│   │   │   ├── PasswordResetLinkController.php
│   │   │   ├── RegisteredUserController.php
│   │   │   └── VerifyEmailController.php
│   │   ├── ActivityLogController.php     # View audit logs
│   │   ├── CategoryController.php        # Category CRUD
│   │   ├── Controller.php                # Base controller
│   │   ├── DashboardController.php       # Admin dashboard
│   │   ├── MediaController.php           # Media upload/delete
│   │   ├── PageController.php            # Page CRUD with builder
│   │   ├── PostController.php            # Post CRUD
│   │   ├── ProfileController.php         # User profile settings
│   │   ├── PublicController.php          # Public website rendering
│   │   ├── RoleManagementController.php  # Role & permission management
│   │   ├── SettingController.php         # Site settings
│   │   ├── SlugController.php            # Auto slug generation
│   │   └── UserManagementController.php  # User management CRUD
│   │
│   ├── Middleware/
│   │   ├── AdminMiddleware.php           # Restrict to admin/editor roles
│   │   ├── Authenticate.php              # Authentication check
│   │   ├── EncryptCookies.php            # Cookie encryption
│   │   ├── LogUserActivity.php           # Activity logging (excludes admins)
│   │   ├── PermissionMiddleware.php      # Permission-based access control
│   │   ├── PreventRequestsDuringMaintenance.php
│   │   ├── RedirectIfAuthenticated.php
│   │   ├── TrimStrings.php
│   │   ├── TrustHosts.php
│   │   ├── TrustProxies.php
│   │   └── VerifyCsrfToken.php
│   │
│   └── Requests/
│       ├── ProfileUpdateRequest.php      # Profile form validation
│       ├── StorePageRequest.php          # Page creation/update validation
│       ├── StorePostRequest.php          # Post creation/update validation
│       └── UserFormRequest.php           # User management validation
│
├── Models/
│   ├── ActivityLog.php          # Audit log model (polymorphic)
│   ├── Category.php             # Post category model
│   ├── Media.php                # File upload model
│   ├── Page.php                 # Custom page model
│   ├── Post.php                 # Blog post model
│   ├── Setting.php              # Site settings model (key-value store)
│   ├── Tag.php                  # Post tag model
│   └── User.php                 # User authentication model (with roles/permissions)
│
├── Policies/
│   └── PostPolicy.php           # Authorization policy for posts
│
├── Providers/
│   ├── AppServiceProvider.php   # Application service provider
│   ├── AuthServiceProvider.php  # Authentication & authorization setup
│   └── BroadcastServiceProvider.php
│
└── Services/
    └── ActivityLogger.php       # Service for logging user activities
```

### `/bootstrap` - Application Bootstrap

```
bootstrap/
├── app.php                      # Bootstrap application instance
├── cache/
│   ├── packages.php             # Cached package list
│   └── services.php             # Cached service providers
└── providers.php                # Cached providers list
```

### `/config` - Configuration Files

```
config/
├── app.php                      # Application configuration (name, timezone, etc.)
├── auth.php                     # Authentication guards & password reset
├── cache.php                    # Cache driver configuration
├── database.php                 # Database connections
├── filesystems.php              # Storage disk configuration
├── logging.php                  # Log channels
├── mail.php                     # Email configuration
├── purifier.php                 # Mews Purifier HTML sanitization config
├── queue.php                    # Queue job configuration
├── services.php                 # Third-party service credentials
├── session.php                  # Session driver configuration
└── ...
```

### `/database` - Database Files

```
database/
├── factories/
│   └── UserFactory.php          # User factory for seeding/testing
│
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2026_04_28_101602_add_role_to_users_table.php
│   ├── 2026_04_28_101718_create_categories_table.php
│   ├── 2026_04_28_101838_create_tags_table.php
│   ├── 2026_04_28_101950_create_posts_table.php
│   ├── 2026_04_28_102105_create_post_tag_table.php
│   ├── 2026_04_28_102202_create_pages_table.php
│   ├── 2026_04_28_102253_create_media_table.php
│   ├── 2026_04_28_102333_create_settings_table.php
│   ├── 2026_05_04_160000_create_activity_logs_table.php
│   ├── 2026_05_04_170000_add_new_roles_to_users_table.php
│   └── 2026_05_05_100000_add_custom_permissions_to_users_table.php
│
└── seeders/
    └── DatabaseSeeder.php       # Database seeding script
```

### `/public` - Public Web Root

```
public/
├── index.php                    # Application entry point
├── robots.txt                   # Search engine directives
├── storage/                     # Symlink to storage/app/public
├── build/                       # Vite build output
│   ├── manifest.json            # Asset manifest
│   └── ...
└── ...
```

### `/resources` - Frontend Assets & Views

```
resources/
├── css/
│   ├── app.css                  # Main Tailwind CSS entry
│   └── ...
│
├── js/
│   ├── app.js                   # Main JavaScript entry
│   ├── bootstrap.js             # JavaScript bootstrap
│   └── ...
│
└── views/
    ├── admin/                   # Admin panel templates
    │   ├── activity-logs/       # Activity log viewing
    │   ├── categories/          # Category management
    │   ├── dashboard.blade.php  # Admin dashboard
    │   ├── media/               # Media library interface
    │   ├── pages/               # Page create/edit
    │   ├── posts/               # Post create/edit
    │   ├── roles/               # Role & permission management
    │   ├── settings/            # Site settings interface
    │   └── users/               # User management CRUD
    │
    ├── auth/                    # Authentication templates
    │   ├── confirm-password.blade.php
    │   ├── forgot-password.blade.php
    │   ├── login.blade.php
    │   ├── register.blade.php
    │   ├── reset-password.blade.php
    │   └── verify-email.blade.php
    │
    ├── components/              # Reusable Blade components
    │
    ├── layouts/
    │   ├── admin.blade.php      # Admin panel layout (sidebar, nav)
    │   ├── app.blade.php        # Main layout
    │   ├── guest.blade.php      # Guest/auth layout
    │   ├── navigation.blade.php # Navigation component
    │   └── public.blade.php     # Public website layout
    │
    ├── public/                  # Public website templates
    │   ├── blog.blade.php       # Blog listing page
    │   ├── post.blade.php       # Single post page
    │   ├── page.blade.php       # Single custom page
    │   ├── home.blade.php       # Home page
    │   └── ...
    │
    ├── dashboard.blade.php      # Dashboard view
    ├── profile/                 # User profile pages
    └── welcome.blade.php        # Welcome/landing page
```

### `/routes` - Route Definitions

```
routes/
├── auth.php                     # Authentication routes (login, register, etc.)
├── console.php                  # Console/Artisan routes
└── web.php                      # Web application routes (admin & public)
```

**Route File Breakdown:**

- **`web.php`**: Main route file containing:
  - `/admin/*` - Admin panel routes (protected by `admin.auth` middleware)
  - `/` - Public website routes
  - `/profile` - User profile routes

- **`auth.php`**: Breeze authentication routes:
  - `/login`, `/register`, `/password/reset`, etc.

### `/storage` - Runtime Storage

```
storage/
├── app/
│   └── public/                  # Publicly accessible storage
│       ├── posts/images/        # Post featured images
│       ├── media/               # User uploaded files
│       └── ...
│
├── framework/
│   ├── cache/                   # Application cache files
│   ├── sessions/                # Session storage
│   ├── views/                   # Compiled Blade templates
│   └── ...
│
└── logs/
    ├── laravel.log              # Application error log
    ├── activity.log             # User activity log
    └── ...
```

### `/tests` - Testing

```
tests/
├── TestCase.php                 # Base test case class
├── Feature/                     # Feature tests (API, routes)
└── Unit/                        # Unit tests (models, services)
```

### `/vendor` - Composer Dependencies

```
vendor/
├── autoload.php                 # Composer autoloader
├── laravel/                     # Laravel framework & packages
├── symfony/                     # Symfony components
├── mews/                        # Mews Purifier HTML sanitizer
└── ...
```

---

## Features & Functionality

### 1. Public Website

#### Home Page
- Displays configured home page (set via settings)
- Can be any published page
- Route: `/`

#### Blog Section
- **Blog Listing** (`/blog`)
  - Shows all published posts
  - Pagination support
  - Filterable by category/tag

- **Single Post** (`/blog/{slug}`)
  - Full post content (sanitized HTML)
  - Author information
  - Category and tags display
  - Featured image
  - Publish date and metadata

#### Pages
- **Custom Pages** (`/pages/{slug}`)
  - Displays published custom pages
  - Supports drag-and-drop content via page builder
  - Header/footer sections shared across site

#### Media Serving
- **Media Files** (`/media/{path}`)
  - Serves uploaded media files
  - Rate-limited to prevent abuse

### 2. Admin Dashboard

#### Dashboard Features
- **Summary Cards**
  - Total posts (published/draft counts)
  - Total pages (published/draft counts)
  - Quick create links for posts and pages

- **Recent Activity**
  - Latest pages with edit links
  - Latest posts with edit links
  - Activity timeline

#### Post Management
- Create posts with title, content, excerpt, featured image
- Assign categories and tags
- Three statuses: draft, published, archived
- SEO title and meta description
- Auto-slug generation
- Rich HTML editor with sanitization
- Preview public URL before publishing

#### Page Management
- Create/edit custom pages
- Drag-and-drop page builder for visual content creation
- Support for widgets (buttons, text, social, contact forms, etc.)
- Template selection
- Status control (draft/published)
- SEO fields

#### Media Library
- Upload and organize files
- Delete uploaded files
- Track uploader and upload date
- Serve files through `/media/{path}`

#### Category Management
- Create/edit/delete post categories
- Auto-slug generation
- Descriptions for organization

#### Settings Management
- Site name and description
- Set home page
- Edit header/footer content using page builder
- Post pagination settings

#### User Management
- Create/edit/delete users
- Assign roles (admin, editor, post_editor, page_editor)
- Set custom permissions per user
- View admin users list (admins not listed)
- Password management

#### Roles & Permissions
- View all defined roles
- See permission breakdown by role
- Visual indicators (Full/Partial/No Access)
- Detailed role permission pages

#### Activity Logs
- View audit trail of all user actions
- Filter by user, action, date
- Track changes to posts/pages
- See IP address and user agent
- *Admin actions are excluded from logging*

### 3. Content Management Features

#### Rich Text Editing
- HTML content editor
- Auto-sanitization via Mews Purifier
- SEO metadata (title, description)
- Featured image upload/selection

#### Page Builder
- Drag-and-drop interface for page creation
- Pre-built widgets
- JSON-based content storage
- Edit saved pages with builder loaded

#### Media Management
- Direct file upload to media library
- Automatic storage in `storage/app/public/`
- MIME type tracking
- File size tracking

---

## User Roles & Permissions

### Role Hierarchy

#### **Admin**
- Full system access (`*`)
- All permissions granted
- Can create/edit/delete any content
- Can manage users and roles
- Can modify site settings
- **Not tracked in activity logs** (for performance)

#### **Editor**
- Full content management access
- Permissions:
  - `admin.access` - Access admin panel
  - `dashboard.view` - View dashboard
  - `posts.view`, `posts.create`, `posts.update`, `posts.delete`
  - `pages.view`, `pages.create`, `pages.update`, `pages.delete`
  - `media.view`, `media.upload`, `media.delete`
  - `categories.view`, `categories.create`
  - `activity.view` - View activity logs
- Cannot manage users or roles

#### **Post Editor**
- Posts and media only
- Permissions:
  - `admin.access`
  - `dashboard.view`
  - `posts.view`, `posts.create`, `posts.update`, `posts.delete`
  - `media.view`, `media.upload`, `media.delete`
  - `categories.view`
- No page/user management access

#### **Page Editor**
- Pages and media only
- Permissions:
  - `admin.access`
  - `dashboard.view`
  - `pages.view`, `pages.create`, `pages.update`, `pages.delete`
  - `media.view`, `media.upload`, `media.delete`
- No post management access

### Custom Permissions

Users can have additional custom permissions beyond their role:
- Stored in `users.custom_permissions` JSON column
- Evaluated in addition to role permissions
- Can grant specific permissions to users
- Example: Grant `settings.manage` to specific editor for branding updates

### Permission Checking

```php
// Check if user has permission
if ($user->hasPermission('posts.create')) {
    // Allow action
}

// Get all effective permissions (role + custom)
$permissions = $user->getEffectivePermissions();

// Get permissions for a role
$editorPerms = User::getRolePermissions('editor');

// Get all available permissions in system
$allPerms = User::getAllPermissions();
```

---

## API Routes

### Authentication Routes

```
POST   /login                           - Submit login
POST   /logout                          - Logout user
POST   /register                        - Create new account
GET    /forgot-password                 - Password reset form
POST   /forgot-password                 - Request password reset email
GET    /reset-password/{token}          - Password reset form
POST   /reset-password                  - Submit password reset
POST   /verify-email/{id}/{hash}        - Verify email (if enabled)
```

### Public Website Routes

```
GET    /                                - Home page
GET    /blog                            - Blog listing
GET    /blog/{slug}                     - Single blog post
GET    /pages/{slug}                    - Custom page
GET    /media/{path}                    - Serve media file
```

### Admin Routes (Protected by `admin.auth` middleware)

#### Dashboard
```
GET    /admin/dashboard                 - Dashboard overview
```

#### Posts
```
GET    /admin/posts                     - List all posts
GET    /admin/posts/create              - Post creation form
POST   /admin/posts                     - Store new post
GET    /admin/posts/{post}/edit         - Post edit form
PUT    /admin/posts/{post}              - Update post
DELETE /admin/posts/{post}              - Delete post
```

#### Pages
```
GET    /admin/pages                     - List all pages
GET    /admin/pages/create              - Page creation form
POST   /admin/pages                     - Store new page
GET    /admin/pages/{page}/edit         - Page edit form
PUT    /admin/pages/{page}              - Update page
DELETE /admin/pages/{page}              - Delete page
```

#### Media
```
GET    /admin/media                     - Media library
POST   /admin/media/upload              - Upload file
DELETE /admin/media/{medium}            - Delete file
```

#### Categories
```
GET    /admin/categories                - List categories
POST   /admin/categories                - Create category
DELETE /admin/categories/{category}     - Delete category
```

#### Settings
```
GET    /admin/settings                  - Site settings form
POST   /admin/settings                  - Update settings
GET    /admin/settings/sections/{section} - Edit header/footer
```

#### Users
```
GET    /admin/users                     - List users (admins hidden)
GET    /admin/users/create              - User creation form
POST   /admin/users                     - Create user
GET    /admin/users/{user}/edit         - User edit form
PUT    /admin/users/{user}              - Update user
PATCH  /admin/users/{user}/role         - Update user role
DELETE /admin/users/{user}              - Delete user
```

#### Roles
```
GET    /admin/roles                     - List all roles
GET    /admin/roles/{role}              - Role details
```

#### Activity Logs
```
GET    /admin/activity-logs             - View activity log
```

#### Utilities
```
GET|POST /admin/slug                    - Generate slug from title
```

#### Profile
```
GET    /profile                         - User profile edit
PATCH  /profile                         - Update profile
DELETE /profile                         - Delete profile
```

---

## Configuration Files

### `config/app.php`
- Application name (`APP_NAME`)
- Timezone (`APP_TIMEZONE`)
- Locale (`APP_LOCALE`)
- Key encryption (`APP_KEY`)

### `config/database.php`
- Database connection (SQLite by default)
- Connection settings
- Path: `database/database.sqlite`

### `config/auth.php`
- Authentication guard (web)
- Password reset settings
- Session configuration

### `config/filesystems.php`
- Storage disk configuration
- `public` disk: `storage/app/public` (publicly accessible)
- `local` disk: `storage/app` (private)

### `config/purifier.php`
- Mews Purifier HTML sanitization configuration
- Allowed HTML tags and attributes
- Prevents XSS attacks on stored content

### `tailwind.config.js`
- Tailwind CSS theme configuration
- Custom colors, fonts, spacing

### `vite.config.js`
- Vite build tool configuration
- Entry points: CSS and JS
- Asset refresh on change

### `.env` (Environment Variables)

Key variables:
```
APP_NAME=CMS Portal
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

MAIL_DRIVER=log
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=sync
```

---

## Key Services & Helpers

### ActivityLogger Service (`app/Services/ActivityLogger.php`)

Logs user actions for audit trail.

```php
ActivityLogger::log(
    action: 'post.created',
    subject: $post,
    description: 'Post created',
    properties: ['title' => $post->title],
    user: $user
);
```

**Features:**
- Automatic IP address capture
- User agent logging
- Admin users excluded
- Polymorphic subject tracking
- JSON properties storage

### Middleware

#### `AdminMiddleware`
- Restricts `/admin` routes to users with `admin` or `editor` roles
- Redirects unauthorized users

#### `PermissionMiddleware`
- Validates user has required permission
- Usage: `middleware('permission:posts.create')`
- Throws 403 if unauthorized

#### `LogUserActivity`
- Automatically logs all requests (except admins)
- Captures route, method, IP, user agent
- Stores in `activity_logs` table and `activity.log` file

### Form Request Validation

#### `StorePostRequest`
- Validates title, slug, content required
- Validates slug unique (except current post)
- Validates category exists (if provided)
- Validates status is valid enum

#### `StorePageRequest`
- Similar validation for pages
- Template validation (if provided)

#### `UserFormRequest`
- Email unique validation
- Password confirmation on create
- Password optional on update

### Blade Components & Layouts

#### `layouts/admin.blade.php`
- Admin panel main layout
- Sidebar navigation
- Top navigation bar
- Flash message display

#### `layouts/public.blade.php`
- Public website layout
- Header and footer sections
- Responsive design

---

## Security Features

### 1. Authentication & Authorization
- **Laravel Breeze** - Secure authentication scaffolding
- **Hashed passwords** - Bcrypt password hashing
- **Session management** - Secure session handling
- **CSRF protection** - Token-based form protection
- **Permission-based access control** - Route & action level authorization

### 2. Input Sanitization
- **Mews Purifier** - HTML content sanitization
- Prevents XSS attacks through stored HTML
- Whitelist approach to allowed tags/attributes
- Applied on post/page content storage

### 3. Data Protection
- **Environment variables** - Sensitive config isolated
- **Database encryption** - Support for encrypted connections
- **File permissions** - Storage directories privately accessible
- **Rate limiting** - Public media routes throttled (`60,1` per minute)

### 4. Activity Logging
- **Audit trail** - Complete action history
- **IP tracking** - Source address logged
- **User agent tracking** - Device/browser information
- **Admin exclusion** - High-privilege actions not logged

### 5. File Upload Security
- **MIME type checking** - Verify file types
- **Storage isolation** - Files stored outside public web root (except `public` disk)
- **Unique file paths** - Prevent filename collisions/overwrites
- **Access control** - Media deletion restricted to uploader/admin

### 6. User Management Security
- **Self-protection** - Users cannot modify their own account
- **Admin hiding** - Admin users hidden from user list
- **Password requirements** - Secure password policies
- **Role validation** - Only valid roles assignable

---

## Quick Start

### Prerequisites
- PHP 8.4+
- Composer
- Node.js & npm
- Git (optional)

### Installation

1. **Clone/Setup Repository**
```bash
cd cms-portal-new
```

2. **Install PHP Dependencies**
```bash
composer install
```

3. **Install JavaScript Dependencies**
```bash
npm install
```

4. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Database Setup**
```bash
php artisan migrate
php artisan db:seed
```

6. **Build Assets**
```bash
npm run build
# or for development with watch
npm run dev
```

7. **Start Development Server**
```bash
php artisan serve
```

Visit `http://localhost:8000`

### First Login
- Default credentials from seeder or manually created
- Navigate to `/admin` for admin panel
- Login with created credentials

### Create First Content
1. Go to `/admin/posts` → Create Post
2. Or `/admin/pages` → Create Page
3. Set home page via `/admin/settings`

---

## Common Workflows

### Create a New Blog Post

1. Go to `/admin/posts/create`
2. Fill in:
   - **Title** - Post headline
   - **Slug** - Auto-generated URL slug (editable)
   - **Content** - Rich HTML content
   - **Excerpt** - Short summary
   - **Category** - Post category
   - **Tags** - Multiple tag selection
   - **Featured Image** - Upload or select from media
   - **SEO Title** - Meta title for search engines
   - **SEO Description** - Meta description
   - **Status** - draft/published/archived
3. Click "Create Post"
4. Post appears at `/blog/{slug}` when published

### Create a Custom Page

1. Go to `/admin/pages/create`
2. Use page builder to add content:
   - Drag-and-drop widgets
   - Save changes (auto-saves builder JSON)
3. Fill in:
   - **Title** - Page title
   - **Slug** - URL slug
   - **Status** - draft/published
   - **SEO fields** - Meta title/description
4. Click "Create Page"
5. Page accessible at `/pages/{slug}` when published

### Set Home Page

1. Go to `/admin/settings`
2. Select published page as home
3. Save
4. Home page now displays at `/`

### Create New User

1. Go to `/admin/users`
2. Click "Create User"
3. Fill in:
   - **Name** - User full name
   - **Email** - Unique email
   - **Password** - Strong password
   - **Role** - admin/editor/post_editor/page_editor
4. Click "Create User"
5. User can now login

### Change User Permissions

#### Change Role
1. Go to `/admin/users`
2. Click user to edit
3. Change role dropdown
4. Click "Update Role"

#### Add Custom Permissions
1. Go to `/admin/roles` to see available permissions
2. Edit user in `/admin/users/{user}/edit`
3. Add custom permissions (JSON array)
4. Save

### Upload Media

1. Go to `/admin/media`
2. Click "Upload Files"
3. Select files from computer
4. Wait for upload to complete
5. Files appear in library and can be used in posts/pages

### View Activity Logs

1. Go to `/admin/activity-logs`
2. Browse user actions:
   - Filter by user
   - Filter by action type
   - Sort by date
3. View detailed changes in JSON properties

### Configure Site Settings

1. Go to `/admin/settings`
2. Update:
   - **Site Name** - Used in page titles
   - **Home Page** - Select published page
   - **Header Content** - Shared site header (built with page builder)
   - **Footer Content** - Shared site footer (built with page builder)
3. Save changes
4. Changes reflected across public website

---

## Database Migration Timeline

| Date | Migration | Purpose |
|------|-----------|---------|
| 0001-01-01 | Create users/sessions/cache | Laravel foundation tables |
| 2026-04-28 10:16 | Add role to users | Initial role support (admin/editor) |
| 2026-04-28 10:17 | Create categories | Post categorization |
| 2026-04-28 10:18 | Create tags | Post tagging |
| 2026-04-28 10:19 | Create posts | Blog post content |
| 2026-04-28 10:21 | Create post_tag | Many-to-many posts/tags |
| 2026-04-28 10:22 | Create pages | Custom pages |
| 2026-04-28 10:22 | Create media | File uploads |
| 2026-04-28 10:23 | Create settings | Site configuration |
| 2026-05-04 16:00 | Create activity_logs | Audit trail |
| 2026-05-04 17:00 | Add new roles to users | post_editor, page_editor roles |
| 2026-05-05 10:00 | Add custom_permissions | Per-user custom permissions |

---

## Environment Requirements

### Minimum
- PHP 8.4.0
- SQLite 3
- 256MB RAM
- 100MB disk space

### Recommended
- PHP 8.4.20+
- MySQL 8.0 or PostgreSQL 13+
- 512MB+ RAM
- 1GB+ disk space
- HTTPS enabled in production

### PHP Extensions Required
- `curl` - HTTP requests
- `fileinfo` - File type detection
- `gd` - Image processing
- `intl` - Internationalization
- `mbstring` - Multi-byte string handling
- `mysqli` - MySQL driver
- `openssl` - Encryption
- `pdo_mysql` - MySQL PDO driver
- `pdo_sqlite` - SQLite PDO driver
- `sqlite3` - SQLite database
- `zip` - ZIP file handling

---

## Performance Tips

### Caching
- Use file/database caching for settings
- Cache published pages
- Cache category/tag lists

### Queries
- Use eager loading (`with()`) for relationships
- Limit pagination (default 15 per page)
- Index frequently queried columns (slugs, user_id)

### Assets
- Minify CSS/JS via Vite (`npm run build`)
- Use CDN for public assets
- Compress images for featured_image fields

### Logging
- Rotate activity logs regularly
- Archive old logs monthly
- Consider external logging in production

---

## Troubleshooting

### PHP Version Issue
```
Error: Parse error: syntax error, unexpected '|'
```
**Solution:** Ensure PHP 8.4+ is active
```bash
php -v
composer install
php artisan serve
```

### Missing SortDirection Enum
```
Error: Class "SortDirection" not found
```
**Solution:** Rebuild Composer autoloader
```bash
composer dump-autoload
php artisan serve
```

### Asset Not Found Errors
```
Error: Resource not found in Vite manifest
```
**Solution:** Rebuild assets
```bash
npm run build
```

### Database Lock Errors (SQLite)
```
Error: database is locked
```
**Solution:** Ensure single PHP process, or switch to MySQL/PostgreSQL

### Permission Denied Errors
```
Error: Permission denied on storage/logs
```
**Solution:** Fix storage directory permissions
```bash
chmod -R 775 storage/bootstrap/cache
```

---

## Development Commands

### Artisan Commands

```bash
# Database
php artisan migrate               # Run pending migrations
php artisan migrate:rollback     # Rollback last migration batch
php artisan migrate:fresh        # Drop & recreate database
php artisan db:seed              # Seed database with test data

# Cache
php artisan cache:clear          # Clear application cache
php artisan config:cache         # Cache configuration
php artisan view:cache           # Cache Blade views
php artisan optimize             # Optimize application

# Code Quality
php artisan tinker               # Interactive CLI for testing
php artisan route:list           # List all routes
php artisan make:model Name      # Generate new model
php artisan make:controller Name # Generate new controller
php artisan make:migration Name  # Generate new migration

# Testing
php artisan test                 # Run tests
php artisan test --filter=TestName

# Production
php artisan key:generate         # Generate APP_KEY
```

### NPM Commands

```bash
npm install                      # Install dependencies
npm run dev                      # Start development server
npm run build                    # Build for production
npm update                       # Update dependencies
npm audit fix                    # Fix security vulnerabilities
```

---

## File Size Reference

Typical database size after initial seeding:
- Empty database: ~50KB
- With 50 posts/pages: ~500KB
- With 1000 posts/pages: ~5MB
- With 1000 activity logs: ~200KB

Storage locations:
- `storage/app/public/posts/images/` - Post featured images
- `storage/app/public/media/` - User uploaded files
- `storage/logs/` - Application and activity logs
- `bootstrap/cache/` - Framework cache files

---

## Support & Documentation

- **Laravel Documentation:** https://laravel.com/docs
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Alpine.js:** https://alpinejs.dev/
- **Mews Purifier:** https://github.com/mewebstudio/Purifier
- **Project Issues:** See `work.md` for current tasks

---

## License & Credits

- Built with **Laravel 13**
- Authentication via **Laravel Breeze**
- CSS with **Tailwind CSS**
- JavaScript via **Alpine.js**
- HTML sanitization via **Mews Purifier**

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-05-05 | Initial complete project documentation |
| - | 2026-05-04 | Activity logging, new roles, custom permissions |
| - | 2026-05-03 | Enhanced dashboard, page builder improvements |
| - | 2026-04-28 | Initial project setup with posts, pages, media |

---

**End of Documentation**

For questions or clarifications, refer to the code comments, controller implementations, or database migrations for specific implementation details.
