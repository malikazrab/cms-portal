# Further Work Report

Source document: `CMS_Portal_Sprint_Plan.docx`  
Project: `cms-portal-new`  
Date: 2026-04-30

## Current Status

The CMS foundation is mostly in place. The project now runs on PHP 8.4, Composer dependencies are installed, admin routes register correctly, and the test suite passes.

The `v3.html` page builder has been integrated as the admin page editor at:

`resources/views/admin/pages/create.blade.php`

It is not just copied as a static file. Its Save and Publish buttons now submit to Laravel through `admin.pages.store`, and pages created from it are saved with the template value `v3-builder`.

## Completed From Sprint Plan

- Laravel project structure exists in `cms-portal-new`.
- Authentication scaffolding exists.
- Admin middleware exists for `admin` and `editor` roles.
- Core CMS migrations exist for users, posts, pages, categories, tags, media, settings, and post tags.
- Core Eloquent models exist.
- Admin routes exist for dashboard, posts, pages, media, categories, settings, and slug generation.
- Public routes exist for home, blog post, and static page URLs.
- Admin views exist for:
  - Dashboard
  - Posts list/create/edit
  - Pages list/create/edit
  - Media library
  - Categories
  - Settings
- Public views exist for:
  - Home/blog listing
  - Single post
  - Static page
- `PublicController` exists.
- `MediaController` supports index, upload, and destroy.
- `SlugController` exists and is registered.
- HTMLPurifier is installed and used for safer content rendering.
- PHP upgraded to 8.4.20.
- `php artisan route:list --name=admin` works.
- `php artisan test` passes.

## Important Remaining Work

### 1. Finish Media Workflow

The sprint plan expects a full media picker experience. Current media upload/list/delete exists, but the editor workflow still needs polish.

Needed:

- Run and confirm `php artisan storage:link`.
- Add reusable media picker modal with Alpine.js.
- Allow selecting uploaded images from the media library inside post/page forms.
- Add thumbnails, file size display, and delete confirmation UI.
- Add bulk delete for media items.
- Restrict media upload validation to image types if only images are required.

### 2. Improve Post Editor

The plan expects a WYSIWYG editor for blog posts. The current post create/edit screens are functional, but still basic.

Needed:

- Add TinyMCE, Trix, or another editor to post content fields.
- Create `resources/js/editor.js` if using bundled assets.
- Add toolbar options for headings, bold, italic, lists, links, and images.
- Add meta description character counter.
- Add collapsible SEO panel.
- Add featured image picker instead of only direct upload.
- Add tag autocomplete or a cleaner tag multi-select.

### 3. Improve Page Editing After v3 Creation

The v3 builder create screen is integrated, but editing an existing v3 page currently shows the raw JSON content.

Needed:

- Add edit support for v3 builder pages.
- Load existing builder JSON back into the v3 editor.
- Keep a fallback raw editor only for non-builder pages.
- Add preview mode for saved pages before publish if needed.

### 4. Public Frontend Polish

Public views exist, but the sprint plan expects a more complete public frontend.

Needed:

- Create `resources/views/layouts/public.blade.php`.
- Move repeated public HTML into the public layout.
- Add site header, navigation, and footer.
- Improve blog card design with featured image, category badge, author, and date.
- Add dynamic meta title and description through layout sections/stacks.
- Verify static pages created by v3 builder render cleanly on desktop and mobile.

### 5. Admin UI Polish

The admin panel works, but the sprint plan asks for a polished WordPress-style experience.

Needed:

- Add active sidebar link styling.
- Add reusable components:
  - Alert
  - Button
  - Badge
- Add success toast notifications.
- Add Alpine.js confirmation modals for deletes.
- Improve dashboard stat cards using total posts, published posts, draft posts, and total pages.
- Check mobile responsiveness for dashboard, tables, editor, and media library.

### 6. Permissions And Security

Some security pieces are started, but should be reviewed carefully before final delivery.

Needed:

- Confirm `PostPolicy` is enforced in controllers or routes.
- Confirm editors cannot delete other users' posts.
- Confirm all admin routes require both `auth` and `admin.auth`.
- Add custom `404` and `500` error pages.
- Review all `{!! !!}` output and sanitize before rendering.
- Confirm upload validation cannot accept unsafe files.

### 7. Data And Setup

The sprint plan expects a repeatable setup for another developer.

Needed:

- Confirm `DatabaseSeeder` creates:
  - Admin user: `admin@cms.com`
  - Password: `Admin@123`
  - Optional test categories/tags/posts
- Confirm `.env.example` has all needed keys and no secrets.
- Review and update `README.md` with:
  - Setup steps
  - PHP 8.4 requirement
  - Composer/NPM install steps
  - Migration and seed commands
  - Login credentials
  - Route summary

### 8. Testing Checklist

Run these flows manually after the remaining features are added:

- Admin login.
- Create category.
- Upload media.
- Create post with image and publish it.
- View published post on public blog.
- Edit post and update content.
- Delete post and confirm image cleanup.
- Create v3 page and publish it.
- View v3 page publicly.
- Edit v3 page and confirm builder content loads correctly.
- Try `/blog/non-existent-slug` and confirm custom 404.
- Login as editor and confirm permissions.
- Test dashboard and editor on mobile width.

## Suggested Priority Order

1. Run `php artisan storage:link` and finish media picker.
2. Add WYSIWYG editor for posts.
3. Add v3 builder edit support.
4. Add public layout and polish public pages.
5. Add admin UI polish and reusable components.
6. Add custom error pages and permission enforcement.
7. Update README and seed data.
8. Run full manual user-flow testing.

## Notes Before Deleting `cms-portal-old`

Do not delete `cms-portal-old` until:

- v3 create and edit flows are tested.
- Media upload and picker are tested.
- Admin post/page CRUD is tested.
- Public blog and public page rendering are tested.
- A backup or Git commit exists for `cms-portal-new`.

After those checks pass, `cms-portal-old` can be archived or deleted.
