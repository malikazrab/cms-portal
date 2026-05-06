# DB-2: Menus & Menu Items Tables

## Task Completion Summary

Created two new migration files and corresponding Eloquent models for managing menus and menu items (hierarchical navigation structure).

### Files Created

#### 1. Migration: Menus Table
**File**: [database/migrations/2026_05_05_create_menus_table.php](database/migrations/2026_05_05_create_menus_table.php)

**Schema**:
```php
Schema::create('menus', function (Blueprint $table) {
    $table->id();                                           // BIGINT UNSIGNED
    $table->string('name', 150);                           // VARCHAR(150)
    $table->string('slug', 150)->unique();                 // VARCHAR(150), unique
    $table->tinyInteger('is_default')->default(0)->index();// TINYINT(1), indexed
    $table->foreignId('created_by')                        // BIGINT UNSIGNED, FK
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();
    $table->timestamps();                                  // created_at, updated_at
});
```

**Columns**:
| Column | Type | Details |
|--------|------|---------|
| id | BIGINT UNSIGNED | Primary key, auto-increment |
| name | VARCHAR(150) | Menu display name, not null |
| slug | VARCHAR(150) | Unique slug for the menu |
| is_default | TINYINT(1) | Default 0; indexed for fast lookup |
| created_by | BIGINT UNSIGNED | FK to users.id, set null on delete |
| created_at / updated_at | TIMESTAMPS | Standard Laravel timestamps |

**Indexes**:
- INDEX on `is_default` — Fast lookup of the default menu

---

#### 2. Migration: Menu Items Table
**File**: [database/migrations/2026_05_05_create_menu_items_table.php](database/migrations/2026_05_05_create_menu_items_table.php)

**Schema**:
```php
Schema::create('menu_items', function (Blueprint $table) {
    $table->id();                                          // BIGINT UNSIGNED
    $table->foreignId('menu_id')                           // BIGINT UNSIGNED, FK
        ->constrained('menus')
        ->onDelete('cascade');
    $table->foreignId('parent_id')                         // BIGINT UNSIGNED, FK
        ->nullable()
        ->constrained('menu_items')
        ->nullOnDelete();
    $table->string('label', 200);                          // VARCHAR(200)
    $table->string('url', 500)->nullable();                // VARCHAR(500), nullable
    $table->foreignId('page_id')                           // BIGINT UNSIGNED, FK
        ->nullable()
        ->constrained('pages')
        ->nullOnDelete();
    $table->unsignedSmallInteger('sort_order')             // SMALLINT UNSIGNED
        ->default(0);
    $table->timestamps();                                  // created_at, updated_at

    // Composite index for ordered item fetch
    $table->index(['menu_id', 'sort_order']);
    // Index for fast child lookup
    $table->index('parent_id');
});
```

**Columns**:
| Column | Type | Details |
|--------|------|---------|
| id | BIGINT UNSIGNED | Primary key, auto-increment |
| menu_id | BIGINT UNSIGNED | FK to menus.id, cascade delete |
| parent_id | BIGINT UNSIGNED | FK to menu_items.id, nullable, set null on delete |
| label | VARCHAR(200) | Display label for the nav item |
| url | VARCHAR(500) | External URL (nullable if page_id is set) |
| page_id | BIGINT UNSIGNED | FK to pages.id, nullable, set null on delete |
| sort_order | SMALLINT UNSIGNED | Default 0, used for drag-drop ordering |
| created_at / updated_at | TIMESTAMPS | Standard Laravel timestamps |

**Indexes**:
- Composite INDEX on `(menu_id, sort_order)` — Optimized for ordered item fetch
- INDEX on `parent_id` — Fast child lookup for hierarchical queries

---

#### 3. Eloquent Model: Menu
**File**: [app/Models/Menu.php](app/Models/Menu.php)

**Key Features**:
- Fillable attributes: `name`, `slug`, `is_default`, `created_by`
- Cast `is_default` as boolean for convenience
- Relationships:
  - `creator()` — BelongsTo User (created_by)
  - `items()` — HasMany MenuItem
  - `itemsOrdered()` — HasMany MenuItem ordered by sort_order
  - `topLevelItems()` — HasMany for root-level items only

**Usage Example**:
```php
$menu = Menu::find(1);
$allItems = $menu->items()->get();
$topItems = $menu->topLevelItems()->get(); // Only parent items
$orderedItems = $menu->itemsOrdered()->get();
$creator = $menu->creator; // Get user who created it
```

---

#### 4. Eloquent Model: MenuItem
**File**: [app/Models/MenuItem.php](app/Models/MenuItem.php)

**Key Features**:
- Fillable attributes: `menu_id`, `parent_id`, `label`, `url`, `page_id`, `sort_order`
- Cast `sort_order` as integer
- Relationships:
  - `menu()` — BelongsTo Menu
  - `parent()` — BelongsTo MenuItem (self-referential, for hierarchy)
  - `children()` — HasMany MenuItem ordered by sort_order
  - `descendants()` — Alias for children() with recursive support
  - `page()` — BelongsTo Page

- **Computed Attributes**:
  - `resolved_url` — Returns page URL if page_id is set, otherwise custom url
  
- **Helper Methods**:
  - `isRootItem()` — Check if this is a root level item (parent_id is null)
  - `hasChildren()` — Check if item has child items
  - `getDepthLevel()` — Get the hierarchical depth (0 for root, 1+ for nested)

**Usage Example**:
```php
$item = MenuItem::find(1);

// Get related objects
$menu = $item->menu; // Parent menu
$parent = $item->parent; // Parent menu item
$children = $item->children()->get(); // Direct children
$relatedPage = $item->page; // Linked page if any

// Get resolved URL
$url = $item->resolved_url; // page route or custom url

// Check hierarchy
if ($item->isRootItem()) { ... }
if ($item->hasChildren()) { ... }
$depth = $item->getDepthLevel();
```

---

## Features & Design

### Hierarchical Menu Structure
- Self-referential `parent_id` FK allows unlimited nesting levels
- `sort_order` column enables drag-and-drop reordering
- `topLevelItems()` and `children()` helpers simplify navigation building

### Flexible Item Linking
- Items can link to:
  1. **Pages** via `page_id` (internal pages)
  2. **Custom URLs** via `url` column (external links)
  3. **No target** (parent items with only child items)

### Default Menu
- `is_default` flag identifies primary navigation menu
- Indexed for fast lookup in template rendering

### Audit Trail
- `created_by` FK tracks who created the menu
- `created_at` / `updated_at` timestamps for version history

---

## Performance Optimizations

| Index | Purpose | Query Examples |
|-------|---------|-----------------|
| `menus(is_default)` | Fast default menu lookup | `Menu::where('is_default', 1)->first()` |
| `menu_items(menu_id, sort_order)` | Ordered item fetch | `MenuItem::where('menu_id', $id)->orderBy('sort_order')->get()` |
| `menu_items(parent_id)` | Child item lookup | `MenuItem::where('parent_id', $id)->get()` |

---

## Migration Compatibility

✅ **MySQL Compatible**: All features use standard MySQL syntax
- Foreign key constraints with cascade/null on delete
- BIGINT UNSIGNED for IDs
- SMALLINT UNSIGNED for sort_order
- VARCHAR with length specifications
- Standard timestamps

✅ **Laravel Best Practices**:
- Uses Eloquent relationships instead of raw queries
- Fillable mass assignment protection
- Attribute casting for type safety
- Self-referential foreign keys for hierarchy

---

## Next Steps

1. **Run migration**:
   ```bash
   php artisan migrate
   ```

2. **Create MenuController and MenuItemController** for CRUD operations

3. **Build menu management UI** in admin panel

4. **Create view components** for rendering menus:
   ```php
   // Example: Render menu in blade template
   <nav>
       @foreach ($menu->topLevelItems as $item)
           <a href="{{ $item->resolved_url }}">{{ $item->label }}</a>
           @if ($item->hasChildren())
               @include('components.menu-children', ['items' => $item->children])
           @endif
       @endforeach
   </nav>
   ```

5. **Add menu management to admin dashboard**

---

**Date**: May 5, 2026  
**Task**: DB-2 - Menus & Menu Items Tables  
**Status**: ✅ Complete
