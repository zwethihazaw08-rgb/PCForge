# PCForge

PCForge is a PHP and MariaDB PC component catalog, compatibility builder, comparison tool, shopping cart, demo checkout, customer account area, and administrator workspace. It is designed for a local XAMPP deployment and a school project; no real payments are processed.

## Features

- Catalog browsing for CPUs, GPUs, motherboards, memory, storage, power supplies, cases, CPU cooling, and monitors.
- Product detail pages with normalized specifications and source descriptions.
- Eight-step PC builder with compatibility checks and power guidance.
- Compare up to three products from the same category.
- Cart and demo checkout with stock validation and order snapshots.
- Password login, optional Google sign-in, saved builds, and profile settings.
- Admin dashboard for products, inventory, users, orders, reports, and store settings.
- Optional Groq-powered build assistant. The site works without it.
- Light and dark themes with responsive desktop and mobile layouts.

## Requirements

- Apache and MariaDB/MySQL, such as XAMPP.
- PHP 8.2 or newer with PDO MySQL, cURL, and mbstring enabled.
- A database named `pcforge`.
- Python 3.11 or newer only when rebuilding the supplied product manifest or image cutouts.

The current local configuration uses Apache on port 80 and MariaDB on port 3307. Change [`config/database.php`](config/database.php) if the destination machine uses different settings.

## Local setup

Copy the project folder to the web root, keeping the folder name `PCForge` unless you also change the base path in [`includes/functions.php`](includes/functions.php). Start Apache and MariaDB, create an empty `pcforge` database, and import [`Database/schema.sql`](Database/schema.sql).

The supplied catalog can then be loaded from the `ProductsData` source files:

```powershell
python Database/prepare_products_data.py
C:/xampp/php/php.exe Database/import_products_data.php --dry-run
C:/xampp/php/php.exe Database/import_products_data.php --apply
C:/xampp/php/php.exe Database/import_products_data.php --verify
```

To use the same assumed project availability shown in the current catalog, run [`Database/migrations/20260925_project_stock.sql`](Database/migrations/20260925_project_stock.sql) after the product import. The source spreadsheets contain no inventory counts, so imported products are initialized to 10 units as a project assumption.

Open:

```text
http://localhost/PCForge/
```

The admin workspace is at `/PCForge/admin/dashboard.php`. It requires an active account with the `admin` role.

## Real product data

The `ProductsData` folder is the source catalog, not a live database connection. [`Database/prepare_products_data.py`](Database/prepare_products_data.py) validates the workbooks and images and writes [`Database/products_data.json`](Database/products_data.json). [`Database/import_products_data.php`](Database/import_products_data.php) maps the manifest into the category tables and records the original spreadsheet row in `product_data_sources`.

The current catalog contains 135 supplied products: 15 each in nine categories. The old sample products remain in the database as inactive historical records. No supplied case-fan data was available. Re-running the importer matches category plus source ID, preserves database IDs, stock, and publication status, and updates the supplied fields.

Product images used by the site are stored in `assets/images`. The 90 opaque supplied photos have transparent PNG derivatives, while 45 supplied images were already transparent. Originals in `ProductsData` are retained. The site does not need the local `.tools` image-processing runtime to display these derivatives.

## Environment configuration

Keep credentials outside the repository. [`config/environment.example`](config/environment.example) lists the variable names used by the application.

Google sign-in uses:

```text
PCFORGE_GOOGLE_CLIENT_ID
PCFORGE_GOOGLE_CLIENT_SECRET
PCFORGE_GOOGLE_REDIRECT_URI
```

The redirect URI must exactly match the callback registered in Google Cloud Console, for example:

```text
https://example.com/PCForge/auth/google-callback.php
```

The optional AI assistant uses:

```text
GROQ_API_KEY
GROQ_MODEL
```

For XAMPP, set these in private Apache/PHP configuration and restart Apache. Never place API keys in PHP, JavaScript, HTML, SQL exports, or the public project directory. See [`docs/ai-assistant.md`](docs/ai-assistant.md) for the Groq setup and limits.

## Moving or restoring the database

The current database contains product data, users, orders, saved builds, and settings. Export it separately and import that private SQL dump into the destination MariaDB database. The current dump is intentionally ignored by Git because it contains private records.

For a fresh database containing only the supplied catalog, use `schema.sql` followed by the importer commands above. For a complete restoration, import the private database export instead of using the legacy `sample_data.sql` fixtures.

## Validation

Useful checks include:

```powershell
C:/xampp/php/php.exe -l product.php
C:/xampp/php/php.exe Database/import_products_data.php --verify
.tools/background-removal/Scripts/python.exe tests/product-images.py --http
C:/xampp/php/php.exe tests/order-workflow.php
C:/xampp/php/php.exe tests/admin-database.php
C:/xampp/php/php.exe tests/admin-workflows.php
```

The HTTP workflow tests require Apache and MariaDB to be running. Do not run integration tests against a production database.

## Git and deployment notes

The repository should contain source code, `ProductsData`, catalog assets, and import metadata. `.gitignore` excludes the local image-processing runtime, database backups, and the private current database export. Keep a current database export outside Git.

Bootstrap CSS and JavaScript are loaded from jsDelivr, so the default site requires internet access for the full styling and navigation experience. The optional Google and Groq integrations also require outbound HTTPS access.

For local network sharing, see [`SHARING.md`](SHARING.md). For a public deployment, use HTTPS, configure the database credentials and environment variables on the server, update the Google callback URI, and use a proper web-server account instead of the local XAMPP root account.
