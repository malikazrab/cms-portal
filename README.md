# CMS Portal

CMS Portal is a Laravel 13 content management system for creating public pages, blog posts, shared site sections, and media assets from an authenticated admin panel.

This project includes:

- A public website with a configurable home page
- A separate blog listing and single-post pages
- A page builder for pages, header content, and footer content
- Post management with categories, tags, SEO fields, and featured images
- A media library for uploaded files
- Settings for site identity and public layout assignment
- Admin and editor roles

## ⚡ Quick Start

**First time setup?** Follow the [SETUP.md](./SETUP.md) guide for complete installation instructions.

**Quick reference:**
```bash
composer install && npm install
touch database/database.sqlite
php artisan migrate && php artisan db:seed
npm run dev &
php artisan serve
# Login with: admin@cms.com / Admin@123
```

## 1. Tech Stack

- PHP `8.3+`
- Laravel `13`
- Laravel Breeze for authentication
- SQLite by default
- Vite
- Tailwind CSS
- Alpine.js
- Mews Purifier for sanitizing stored HTML

## 2. Project Purpose

The goal of this project is to provide a lightweight CMS where an authenticated user can:

- Create and manage posts
- Create and manage custom pages
- Build public-facing page content visually
- Choose which page acts as the site home page
- Edit shared header and footer content through the same page builder
- Manage uploaded media and featured images

## 3. Main Features

### Public Website

- `/` shows the configured home page
- `/blog` shows published blog posts
- `/blog/{slug}` shows a single published post
- `/pages/{slug}` shows a published custom page
- `/media/{path}` serves uploaded public files through Laravel

### Admin Panel

- Admin dashboard with post/page summary
- Post CRUD
- Page CRUD
- Media library
- Category management
- Site settings
- Shared header/footer/home section management

### Content Features

- SEO title and description fields
- Slug generation
- Featured image upload
- Category and tag assignment for posts
- Draft, published, and archived post states
- Draft and published page states

## 4. Roles and Access

The app uses a `role` column on the `users` table.

Supported roles:

- `admin`
- `editor`

Users with either `admin` or `editor` can access the `/admin` area through `AdminMiddleware`.

After login, users are redirected to:

- `/admin/dashboard`

## 5. Folder Overview

### Backend

- `app/Http/Controllers`
  Handles admin actions, public rendering, media, settings, and auth flow.
- `app/Http/Requests`
  Contains form validation for posts and pages.
- `app/Models`
  Contains the Eloquent models for users, posts, pages, categories, tags, media, and settings.
- `app/Http/Middleware/AdminMiddleware.php`
  Restricts admin routes to `admin` and `editor`.

### Frontend

- `resources/views/admin`
  Admin panel pages
- `resources/views/public`
  Public site templates
- `resources/views/layouts`
  Shared Blade layouts

### Data

- `database/migrations`
  Database schema definitions
- `database/database.sqlite`
  Default local SQLite database

### Assets and Build

- `resources/css`
- `resources/js`
- `vite.config.js`
- `package.json`

## 6. Database Structure

Main tables:

- `users`
- `categories`
- `tags`
- `posts`
- `post_tag`
- `pages`
- `media`
- `settings`
- plus Laravel system tables for cache, jobs, sessions, and password resets

### Posts

Important fields:

- `title`
- `slug`
- `content`
- `excerpt`
- `featured_image`
- `status`
- `category_id`
- `meta_title`
- `meta_description`
- `published_at`

### Pages

Important fields:

- `title`
- `slug`
- `content`
- `status`
- `template`
- `meta_title`
- `meta_description`

### Settings

Stored as key/value pairs, including:

- `site_name`
- `site_description`
- `posts_per_page`
- `admin_email`
- `home_page_id`
- `header_page_id`
- `footer_page_id`

## 7. Installation From Scratch

### Requirements

- PHP `8.3+`
- Composer
- Node.js and npm
- SQLite enabled in PHP

### Setup Steps

1. Clone the project.
2. Open the project folder.
3. Install PHP dependencies.
4. Install frontend dependencies.
5. Create the environment file.
6. Generate the app key.
7. Create or confirm the SQLite database file exists.
8. Run migrations.
9. Start the backend server.
10. Start the Vite dev server.

### Commands

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

If you want the project’s combined local workflow:

```bash
composer run dev
```

This runs:

- Laravel server
- queue listener
- Laravel logs watcher
- Vite dev server

## 8. Environment Notes

The project currently uses SQLite by default in local development.

Check `.env` for:

```env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
```

If `APP_URL` changes, restart the local PHP process so generated URLs stay correct.

## 9. Authentication Flow

Authentication is provided by Laravel Breeze.

Relevant routes:

- `/login`
- `/register`
- `/forgot-password`
- `/profile`

After successful login, the app redirects to:

- `admin.dashboard`

## 10. First-Time Usage

### Create a User

Register a user through:

- `/register`

### Access Admin

The default role from migration is:

- `editor`

Editors are allowed into the admin area, so a fresh registered user can access:

- `/admin/dashboard`

## 11. Admin Workflow

### Dashboard

The dashboard shows:

- total posts
- published posts
- draft posts
- total pages
- published pages
- draft pages
- latest posts
- latest pages

### Posts

Posts support:

- title
- slug
- content
- excerpt
- featured image
- status
- category
- tags
- SEO title
- SEO description

Featured images are stored on the `public` disk under:

- `posts/images`

### Pages

Pages are created in a visual builder at:

- `/admin/pages/create`

The builder stores JSON into the `pages.content` column for structured page rendering.

### Media

The media library:

- uploads files to the `public` disk
- stores metadata in the `media` table
- allows deleting files and records

### Categories

Categories can be created and deleted from:

- `/admin/categories`

### Settings

Settings allow you to control:

- site name
- site description
- posts per page
- admin email
- selected home page
- selected header section
- selected footer section

## 12. Public Site Flow

### Home Page

`PublicController@home` resolves the home page in this order:

1. the page ID stored in `home_page_id`
2. a published page with slug `home`
3. the first published page in the database

If no published page exists, the default fallback public home view is shown.

### Blog

`/blog` lists published posts only.

It uses:

- `posts_per_page` from settings
- search filtering by title, excerpt, and content

### Single Post

`/blog/{slug}` renders a published post with:

- featured image
- category
- content
- SEO metadata

### Public Pages

`/pages/{slug}` renders a published page using the builder content structure.

### Header and Footer

Header and footer are managed through settings:

- `header_page_id`
- `footer_page_id`

Those sections are normal pages rendered inside the shared public layout.

## 13. File Upload Behavior

### Featured Images

Featured images for posts are validated in:

- `app/Http/Requests/StorePostRequest.php`

Current accepted formats:

- `jpg`
- `jpeg`
- `png`
- `webp`
- `gif`
- `avif`

Current Laravel validation size limit:

- `100MB`

Current PHP upload limit also needs to allow the same or larger size.

If uploads fail, check:

- `upload_max_filesize`
- `post_max_size`
- selected file extension
- actual file MIME type

### Public Image Delivery

Post featured images are served through:

- `/media/{path}`

This avoids local symlink dependency issues in some Windows environments.

## 14. Important Project Files

### Routes

- [routes/web.php](routes/web.php)

### Controllers

- [app/Http/Controllers/PostController.php](app/Http/Controllers/PostController.php)
- [app/Http/Controllers/PageController.php](app/Http/Controllers/PageController.php)
- [app/Http/Controllers/PublicController.php](app/Http/Controllers/PublicController.php)
- [app/Http/Controllers/MediaController.php](app/Http/Controllers/MediaController.php)
- [app/Http/Controllers/SettingController.php](app/Http/Controllers/SettingController.php)

### Models

- [app/Models/Post.php](app/Models/Post.php)
- [app/Models/Page.php](app/Models/Page.php)
- [app/Models/Media.php](app/Models/Media.php)
- [app/Models/Setting.php](app/Models/Setting.php)
- [app/Models/User.php](app/Models/User.php)

### Validation

- [app/Http/Requests/StorePostRequest.php](app/Http/Requests/StorePostRequest.php)
- [app/Http/Requests/StorePageRequest.php](app/Http/Requests/StorePageRequest.php)

### Helpers

- [app/Helpers/helpers.php](app/Helpers/helpers.php)

Contains:

- `generateSlug()`

## 15. How Slugs Work

Slug generation is supported through:

- `generateSlug()` helper
- `SlugController`
- validation rules on `posts.slug` and `pages.slug`

Slug uniqueness is enforced at both:

- validation level
- database level

## 16. SEO Support

Both posts and pages support:

- `meta_title`
- `meta_description`

These values are rendered into the public page `<title>` and description tags when provided.

## 17. Security Notes

- Admin routes are protected by authentication and role middleware.
- HTML post/page content is sanitized with `Mews\Purifier\Facades\Purifier`.
- Public pages only show published posts and published pages.

## 18. Testing and Quality

Useful commands:

```bash
php artisan test
php artisan route:list
php artisan view:clear
php artisan config:clear
```

Frontend build:

```bash
npm run build
```

## 19. Common Local Commands

### Start development

```bash
composer run dev
```

### Run only backend

```bash
php artisan serve
```

### Run only frontend

```bash
npm run dev
```

### Clear cached views

```bash
php artisan view:clear
```

### Show all routes

```bash
php artisan route:list
```

## 20. Known Implementation Notes

- The page builder stores structured JSON in the page content field.
- Old plain HTML page content is still wrapped and loaded in the builder when possible.
- Media uploads in the media library currently use a separate `10MB` validation limit in `MediaController`.
- Post featured-image upload limits are separate from media library upload limits.
- Public file delivery now uses a Laravel route instead of depending only on `public/storage`.

## 21. Recommended Next Improvements

- Add seeders for an initial admin/editor user
- Add automated tests for admin CRUD and public rendering
- Move repeated validation messages fully to English for consistency
- Normalize model formatting in a few files
- Extract the page builder JavaScript out of the Blade file into dedicated assets
- Add image optimization and thumbnail generation

## 22. A-to-Z Usage Summary

1. Install Composer and npm dependencies.
2. Configure `.env`.
3. Generate the app key.
4. Run database migrations.
5. Start Laravel and Vite.
6. Register a user.
7. Log in.
8. Open `/admin/dashboard`.
9. Create categories and tags.
10. Create posts.
11. Upload featured images.
12. Create pages in the builder.
13. Open settings.
14. Assign the home page.
15. Create or edit header and footer sections.
16. Publish content.
17. Visit the public home page.
18. Visit `/blog` and test single posts.
19. Adjust settings and SEO metadata.
20. Build or deploy when ready.

## 23. License

This repository is based on Laravel and follows the project’s installed dependencies and their licenses. Review package licenses if you plan to distribute the application.
