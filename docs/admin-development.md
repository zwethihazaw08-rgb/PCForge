# PCForge admin development

## Current implementation

All requested routes are implemented and linked in the sidebar. The table below is the feature-by-feature guide; full source is in the named files. No build process is needed.

Local runtime: Apache listens on port 80. XAMPP MariaDB uses port **3307** because the separate Windows MySQL80 service occupies 3306. `config/database.php` and XAMPP's `mysql/bin/my.ini` agree on 3307; the previous INI is backed up as `my.ini.pcforge-before-3307.bak`. The MySQL80 service was left unchanged. If moving this project to a different machine, set the database port and credentials for that machine.

XAMPP phpMyAdmin also targets port 3307; its original `config.inc.php` is backed up as `config.inc.php.pcforge-before-3307.bak`. Validation passed for all admin routes and nine product types, real HTTP create/edit/soft-delete, image acceptance/rejection, CSRF and output escaping, inventory conflicts, shipping defaults and order snapshots, checkout persistence, stock completion/idempotence, and isolated rollback cases. Desktop light/dark layouts and the 390px mobile sidebar were visually checked. Disposable records and preview files were removed after the checks.

| Stage | Files | Queries, behavior and connections |
| --- | --- | --- |
| 1. Access/layout | `includes/auth.php`, `includes/admin-header.php`, `includes/admin-sidebar.php`, `includes/admin-navbar.php`, `includes/footer.php`, `assets/css/admin.css`, `assets/js/admin.js` | Database-backed active admin role check; shared shell, CSRF logout, flash messages, persisted light/dark mode, mobile offcanvas and no-JavaScript navigation fallback. Password and Google sign-in default to the dashboard for admins. |
| 2. Dashboard | `admin/dashboard.php`, `assets/js/admin-charts.js` | Aggregate counts over the nine component tables using `UNION ALL`; counts of customers, builds and orders; paid revenue grouped by currency. Limited recent orders, products and low-stock lists link to details. Chart.js displays monthly paid revenue, statuses and category distribution, with accessible text alternatives. Unknown historical product creation dates are not invented. |
| 3. Product listing | `admin/products.php`, `includes/admin-functions.php`, `includes/catalog.php` | Prepared search over names, brand and existing short-name/model fields; GET category, brand, status, stock and price filters; 20 rows per page with validated offsets. Whitelisted source tables only. |
| 4. Product forms/details | `admin/product-add.php`, `admin/product-edit.php`, `admin/product-view.php`, `admin/product-delete.php`, `includes/admin-product-form.php` | Form fields derive from `SHOW COLUMNS` on whitelisted component tables. Insert/update validates nullable fields, enum choices, lengths, nonnegative integers and decimal prices. Support lists update the existing cooler/socket and case/form-factor tables transactionally. Product deletion sets `status=inactive`, preserving references. A review page and confirmation modal precede disabling. |
| 5. Inventory/categories | `admin/inventory.php`, `admin/categories.php` | Stock update accepts nonnegative integers and compares previous stock before saving, rejecting stale forms. Low stock uses the saved threshold; NULL is unknown, not zero. Categories are fixed schema types; each category links to products/add and can bulk activate or disable its products after confirmation. There is no arbitrary table creation UI. |
| 6. Users | `admin/users.php`, `admin/user-view.php` | Paginated name/email search, activation/disable actions, and self-disable protection. Detail selects no password column and links recent orders/builds plus their full filtered lists. Shipping details use the migrated table. |
| 7. Orders | `admin/orders.php`, `admin/order-view.php`, `includes/orders.php`, `checkout.php`, `cart.php` | Checkout saves persistent demo orders and historical item names/prices, using current database prices, validated quantities and stock. Aggregates repeated components across cart lines. Pending → processing → completed; pending/processing → cancelled. Completion locks the order, conditionally deducts each component's stock, and marks the deduction within one transaction. Failure rolls back every deduction. Repeated completion does not deduct again. Completed/cancelled orders cannot reopen. New cart additions clear the previous session confirmation. |
| 8. Builds | `admin/builds.php`, `admin/build-view.php`, `includes/admin-builds.php` | Paginated owner/build list; validated IDs in saved JSON select current components through the category whitelist. Reuses existing compatibility functions and support relationships. Current price, incomplete data, power estimates and recommended PSU are labelled explicitly. Repeated component/support reads are cached within the request. Customer builds are read-only. |
| 9. Reports | `admin/reports.php` | Validated inclusive date ranges use `created_at >= start AND created_at < day_after_end`, preserving index use. Order counts, paid revenue/average grouped by currency, monthly paid sales, most purchased category, top completed-order component quantities and current low-stock links. Demo payments are excluded from revenue and explicitly included in fulfillment unit counts. |
| 10. Settings | `admin/settings.php`, `includes/catalog.php`, `includes/functions.php`, `includes/footer.php` | Single-row prepared settings update. Store name/email appear in the public footer; currency affects shared price formatting and is snapshotted into new orders; threshold drives inventory/dashboard/report warnings. Maintenance mode rejects customer pages with HTTP 503 before their handlers run, while login and admin recovery remain available. Currency changes do not convert existing catalog prices; old orders retain their currency. |

### Security and operational limits

All admin entry points authenticate before reads or writes. Writes verify CSRF and use prepared statements; SQL table/column identifiers are obtained from fixed categories and their real schema, never request text. Dynamic HTML is escaped. Successful writes redirect with flash messages. Database exceptions are logged, while users get clean errors. Image uploads accept only matching JPG/JPEG, PNG or WebP extensions and MIME types, verify image structure, enforce 3 MB, and generate random filenames. Upload names cannot choose server paths. HTML can still submit without JavaScript; JavaScript adds modals, charts, theme switching and image preview.

This is a demo store, with no payment provider. The admin cannot manufacture a real payment by changing fulfillment status. Paid revenue remains zero until genuine paid order records exist. Stock is checked at checkout but reserved/deducted only when completing an order, as requested; competing pending orders can require inventory adjustment before completion. Category controls manage the existing component types, rather than creating new schema tables. Product removal is deliberately soft-only.

### Running checks

```text
C:\xampp\php\php.exe tests/order-workflow.php
C:\xampp\php\php.exe tests/admin-access.php
C:\xampp\php\php.exe tests/admin-database.php
C:\xampp\php\php.exe tests/admin-workflows.php
C:\xampp\php\php.exe tests/profile-settings.php
```

`order-workflow.php` uses in-memory SQLite to test totals, aggregated quantities, snapshots, rollback and repeat completion. `admin-access.php` and `admin-database.php` use local transaction-scoped fixtures. `admin-workflows.php` requires running XAMPP Apache/MySQL and uses the real HTTP handlers with disposable records; a `finally` cleanup removes only records created by that run. It checks all routes, each product category, CSRF, XSS, inventory, self-disable protection, checkout and order completion. Do not run integration checks against production. A failed database connection must be resolved in `config/database.php` before the live suite can run.

## Foundation reference

## Stage 1: authentication and shared layout

Implemented in this stage:

| File | Responsibility |
| --- | --- |
| `includes/auth.php` | Admin guard and one-time flash messages. Reuses existing authentication and CSRF helpers. |
| `includes/admin-header.php` | Guarded document header, Bootstrap, Icons, early theme selection, shared navigation, main content opening. |
| `includes/admin-sidebar.php` | Desktop sidebar and mobile offcanvas; current page indication and CSRF-protected logout. |
| `includes/admin-navbar.php` | Breadcrumb, storefront link, and accessible theme button. |
| `includes/footer.php` | Small admin footer when `$adminLayout` is set; existing public footer otherwise. |
| `admin/index.php` | Protected redirect to dashboard. |
| `admin/dashboard.php` | Live dashboard with statistics, charts, and related record links. |
| `assets/css/admin.css` | Scoped monochrome admin styles, responsive layout, focus indicators, reduced motion. |
| `assets/js/admin.js` | Theme toggle with localStorage fallback. |
| `tests/admin-access.php` | Transactional checks for customer rejection, disabled-account rejection, and admin rendering. |

The files contain the complete implementation; no build process is required.

### Access and queries

Open `/PCForge/admin/dashboard.php`. A guest goes to the existing login page with an admin return destination. An active customer receives HTTP 403. An active admin can enter. `auth_user()` runs the existing prepared query:

```sql
SELECT id, username, email, role
FROM users
WHERE id = :id AND status = 'active'
LIMIT 1;
```

The primary key locates the account; the status check rejects disabled accounts. The role is read from the database, not trusted from session or form data. The existing login helper regenerates the session ID. Both password and Google login use this helper. Dashboard HTML is marked `no-store`.

An existing account was promoted at the owner's request. No default admin is seeded. For another installation, choose the specific existing account before granting access. This is an explicit one-account setup operation, not a schema migration:

```sql
-- Replace the example email with the exact account you intend to promote.
UPDATE users SET role = 'admin'
WHERE email = 'your-admin@example.com' AND status = 'active';
```

No accounts are automatically promoted and no default password is created.

### Adding each subsequent page

Start handlers before sending HTML:

```php
<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
// POST actions: csrf_verify(), validate input, prepared write,
// flash_set(), then redirect() before including the header.
$pageTitle = 'Products';
require __DIR__ . '/../includes/admin-header.php';
?>
<!-- Escaped page content -->
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
```

The header also checks authorization as a defensive fallback. All writes must use the guard before processing POST. Every sidebar section now links to an implemented route. Mobile navigation uses Bootstrap offcanvas with a no-JavaScript fallback; theme controls are hidden when JavaScript is unavailable.

### Schema findings

Inspected `Database/schema.sql` and the live `pcforge` table list. Existing component tables are `cpu`, `gpu`, `mb`, `memory`, `storage`, `psu`, `case_box`, `cooling`, and `fans`. Use these exact names through a whitelist. Compatibility support uses `case_motherboard_support` and `cooling_socket_support`. Saved builds reference `users` and store component IDs in JSON.

**Database update applied:** `Database/migrations/20260921_admin_foundation.sql` adds `orders`, `order_items`, and `store_settings`. Order items preserve purchased names, quantities, and unit/line prices; orders preserve delivery details, totals, currency, and payment/order status. `stock_deducted_at` prevents repeated stock deductions. Product references span existing component tables and are validated by the PHP whitelist. Checkout and admin settings now use these tables.

The admin migration also adds descriptions and creation/update timestamps to all nine component tables. Existing product timestamps remain NULL because their history is unknown; new products get a creation timestamp, and subsequent updates set the update timestamp. The shipping migration `Database/migrations/20260919_user_shipping_details.sql` is applied, enabling the existing profile shipping form and checkout defaults. `Database/schema.sql` includes these structures for fresh installations. Both migrations can be rerun on the current MariaDB 10.4 installation. Existing row counts were verified unchanged; `tests/admin-database.php` verifies shipping and order inserts and constraints inside a rolled-back transaction.

All ten stages have implementation files. See the current implementation table and verification notes above.

### Verification

```text
C:\xampp\php\php.exe tests/admin-access.php
```

This uses transaction-scoped random test accounts and rolls them back. It never promotes an existing account. Also check the guest HTTP redirect and run PHP lint on changed files.
