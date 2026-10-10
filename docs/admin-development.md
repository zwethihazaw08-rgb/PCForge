# PCForge admin development

## Current implementation

Admin routes are linked in the sidebar. The table below maps each feature to its implementation. No build process is needed.

The default local configuration uses Apache on port 80 and MariaDB on port 3307. Set the database port and credentials in `config/database.php` for your installation.

| Feature | Files | Queries, behavior and connections |
| --- | --- | --- |
| 1. Access/layout | `includes/auth.php`, `includes/admin-header.php`, `includes/admin-sidebar.php`, `includes/admin-navbar.php`, `includes/footer.php`, `assets/css/admin.css`, `assets/js/admin.js` | Database-backed active admin role check; shared shell, CSRF logout, flash messages, persisted light/dark mode, mobile offcanvas and no-JavaScript navigation fallback. Password and Google sign-in default to the dashboard for admins. |
| 2. Dashboard | `admin/dashboard.php`, `assets/js/admin-charts.js` | Aggregate counts over the ten component tables using `UNION ALL`; counts of customers, builds and orders; paid revenue grouped by currency. Limited recent orders, products and low-stock lists link to details. Chart.js displays monthly paid revenue, statuses and category distribution, with accessible text alternatives. Unknown historical product creation dates are not invented. |
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

```powershell
php tests/run.php
php tests/run.php --database
php tests/run.php --http
```

See [`tests/README.md`](../tests/README.md) for suite requirements, fixture cleanup,
and the configurable HTTP base URL. Individual scripts can still be run directly.
The database and HTTP suites are for development installations.

## Adding an admin page

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

The header also checks authorization. `auth_user()` reads the active account's
role from the database, and login regenerates the session ID. New installations
seed no accounts or default passwords; register an account before assigning its
`admin` role as described in the [setup guide](../README.md#local-setup).

## Schema reference

`component_categories()` in `includes/catalog.php` is the shared whitelist for
`cpu`, `gpu`, `mb`, `memory`, `storage`, `psu`, `case_box`, `cooling`, `fans`, and
`monitor`. Use it when constructing queries rather than accepting table names
from requests. Compatibility support lives in `case_motherboard_support` and
`cooling_socket_support`.

`Database/schema.sql` defines accounts, shipping details, saved builds, orders,
order items, store settings, and component tables. Order items retain purchased
names and prices; orders retain delivery and currency snapshots.
`stock_deducted_at` prevents repeat stock deductions. Product timestamps may be
NULL when their original history is unknown.

Fresh installations import `Database/schema.sql` followed by
`Database/catalog.sql`. See the [catalog guide](products-data.md) for existing
installation upgrades and source provenance. Historical migrations stay local
and are excluded from Git.
