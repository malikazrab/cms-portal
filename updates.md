# Updates

## 2026-05-03

- Dashboard now shows page/post totals, published/draft counts, quick create links, and the latest pages/posts with edit links.
- Pages list now shows the public `/pages/{slug}` URL and links published pages in a new tab.
- Posts list now shows the public `/blog/{slug}` URL and links published posts in a new tab.
- Page edit now opens the same drag-and-drop builder used for creating pages, with the saved builder JSON loaded back into the canvas.
- Page builder save now updates an existing page when editing and creates a new page when creating.
- Page builder now includes a short in-editor note explaining how to add new widgets one by one.
- Public page renderer now handles the existing `social` and `contactform` builder widgets.
- Post edit now labels the slug as the URL slug and displays the resulting public post URL.
- Post validation now accepts the existing `archived` status option shown in the edit form.

## Verification

- `npm run build` completed successfully.
- PHP syntax checks passed for the changed PHP files using `C:\Users\BM MOBILE\php83\php.exe`.
- `php artisan route:list` and `php artisan serve` could not run because the PHP binary on PATH is PHP 7.4.2, while the installed Composer dependencies require PHP >= 8.4.0.

## Latest Terminal Error

- The terminal is currently using `C:\xampp\php\php.exe`, which is PHP 7.4.2.
- Running Artisan with PHP 7.4.2 fails with `Parse error: syntax error, unexpected '|', expecting variable (T_VARIABLE)` in `vendor/laravel/prompts/src/helpers.php`.
- Running Artisan with the available PHP 8.3 binary also fails because Composer requires PHP >= 8.4.0.
- Laravel log also shows `Call to undefined method ReflectionProperty::isVirtual()` in Symfony VarDumper, which is another symptom of running dependencies that expect PHP 8.4+.
- Fix: install PHP 8.4+ and place it before XAMPP PHP in the Windows PATH, then confirm with `php -v` before running `php artisan serve`.

## PHP Update

- Installed portable PHP 8.4.20 at `C:\Users\BM MOBILE\php84`.
- Created `C:\Users\BM MOBILE\php84\php.ini` and enabled common Laravel extensions: `curl`, `fileinfo`, `gd`, `intl`, `mbstring`, `mysqli`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `sqlite3`, and `zip`.
- Updated the user PATH so `C:\Users\BM MOBILE\php84` is before `C:\xampp\php` for new terminals.
- Verified `php artisan route:list --except-vendor` works with PHP 8.4.20.
- Started Laravel on `http://127.0.0.1:8000` and verified `/up` returns HTTP 200.

## SortDirection Error Fix

- Error seen on `/login`: `Class "SortDirection" not found` from `vendor/laravel/framework/src/Illuminate/Collections/Collection.php`.
- Cause: Laravel 13.7 references the PHP 8.6 `SortDirection` enum. The Symfony PHP 8.6 polyfill was installed, but Composer's generated autoload classmap did not include the polyfill stub yet.
- Fix: ran `composer dump-autoload` using PHP 8.4.20, which added `SortDirection` to Composer's classmap from `vendor/symfony/polyfill-php86/Resources/stubs/SortDirection.php`.
- Restarted Laravel on `http://127.0.0.1:8000`.
- Verified `http://127.0.0.1:8000/login` now returns HTTP 200.
