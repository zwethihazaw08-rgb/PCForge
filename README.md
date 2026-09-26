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

The current local configuration uses Apache on port 80 and MariaDB on port 3307. Change [`config/database.php`](config/database.php) if the destination machine uses different settings.

## Local setup

Clone into your web root. Use a shallow clone to download the current version without the old source images and database exports retained in Git history:

```powershell
cd C:/xampp/htdocs
git clone --depth 1 https://github.com/zwethihazaw08-rgb/PCForge.git
```

Keep the folder name `PCForge` unless you also change the base path in [`includes/functions.php`](includes/functions.php). Start Apache and MariaDB and update [`config/database.php`](config/database.php) to match your database port and credentials.

In phpMyAdmin, create an empty `pcforge` database, select it, and import these two files in order:

1. [`Database/schema.sql`](Database/schema.sql) — application tables and default settings.
2. [`Database/catalog.sql`](Database/catalog.sql) — the 135 supplied products, image references, and known compatibility support.

Import the catalog only once into a fresh installation. No Python, original spreadsheets, image processing, or additional SQL files are required. Existing installations should keep their database; these files are not an upgrade or a reset.

Open:

```text
http://localhost/PCForge/
```

The admin workspace is at `/PCForge/admin/dashboard.php`. It requires an active account with the `admin` role. Fresh installations contain no accounts; after registering your own account, assign its `role` to `admin` in phpMyAdmin if you need administrator access.

## Real product data

The catalog contains 15 products in each of nine categories. No supplied case-fan data was available. Prices are in USD. Stock is a snapshot of the project's inventory, originally based on assumed quantities because the source spreadsheets contained no inventory counts. It does not represent verified supplier availability.

The website reads products from MariaDB and final images from `assets/images`. All 135 final images are included: 90 transparent PNG derivatives and 45 images that already had transparency. The original `ProductsData` folder, duplicate photos, import tools, and local `.tools` runtime are excluded from the current repository files and are not required to run the website.

Use Admin > Products and Admin > Inventory to edit the running catalog. The bundled SQL is an installation snapshot; admin edits do not automatically update it. See [`docs/products-data.md`](docs/products-data.md) for details.

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

Email/password registration sends an email verification code. Configure PHP's mail delivery on the destination machine; for XAMPP this includes `php.ini` and `sendmail/sendmail.ini`. Set `PCFORGE_MAIL_FROM` to your sender address. See [`config/mail.php`](config/mail.php) for the application mail settings.

## Moving or restoring the database

To transfer an existing installation with its users, orders, saved builds, and settings, export its database separately and import that private SQL dump into the destination MariaDB database. Full exports are excluded from the current repository files because they contain private records.

For a fresh installation containing only the supplied catalog, use `schema.sql` followed by `catalog.sql`. For a complete restoration, use your private database export instead of these setup files.

## Validation

Useful checks include:

```powershell
C:/xampp/php/php.exe -l product.php
C:/xampp/php/php.exe tests/catalog-install.php
C:/xampp/php/php.exe tests/order-workflow.php
C:/xampp/php/php.exe tests/admin-database.php
C:/xampp/php/php.exe tests/admin-workflows.php
```

The catalog installation check creates a randomly named temporary database, imports both SQL files, checks products and images, and drops that temporary database. It does not modify the configured database. The configured database user needs permission to create and drop databases.

The other workflow tests require MariaDB, and HTTP tests also require Apache. Run them against a development installation, not a production database.

## Git and deployment notes

The current repository includes application code, final catalog images, documentation, and the two setup SQL files. `.gitignore` excludes original source material, preparation tools, obsolete SQL files, database backups, and private exports. Keep your backups outside Git.

Files removed from the current version may still exist in older Git commits. The shallow clone command above avoids downloading that old history; downloading the current branch as a ZIP also includes only current files.

Bootstrap CSS and JavaScript are loaded from jsDelivr, so the default site requires internet access for the full styling and navigation experience. The optional Google and Groq integrations also require outbound HTTPS access.

For local network sharing, see [`SHARING.md`](SHARING.md). For a public deployment, use HTTPS, configure the database credentials and environment variables on the server, update the Google callback URI, and use a proper web-server account instead of the local XAMPP root account.
