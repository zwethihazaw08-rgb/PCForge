# PCForge

PCForge is a PHP and MariaDB PC component catalog, compatibility builder, comparison tool, shopping cart, demo checkout, customer account area, and administrator workspace. It is designed for a local XAMPP deployment and a school project; no real payments are processed.

## Features

- Catalog browsing for CPUs, GPUs, motherboards, memory, storage, power supplies, cases, CPU cooling, case fans, and monitors.
- Product detail pages with normalized specifications and source descriptions.
- Eight-step PC builder with compatibility checks and power guidance.
- Compare up to three products from the same category.
- Direct Add to cart from prebuilts, saved builds, and the custom builder.
- Demo checkout with stock validation, order snapshots, and printable receipts with QR codes.
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
2. [`Database/catalog.sql`](Database/catalog.sql) — 335 products, image references, public source records, and known compatibility support.

Import the catalog only once into a fresh installation. No Python, original spreadsheets, image processing, or additional SQL files are required. Existing installations should keep their database; these files are not an upgrade or a reset.

Open:

```text
http://localhost/PCForge/
```

The admin workspace is at `/PCForge/admin/dashboard.php`. It requires an active account with the `admin` role. Fresh installations contain no accounts; after registering your own account, assign its `role` to `admin` in phpMyAdmin if you need administrator access.

## Checkout and receipt QR codes

Complete builds add all eight components to the cart as one build; changing the
cart quantity changes the quantity of every included component. Prebuilt and
saved-build buttons do not replace the current builder selection. Incomplete
saved builds must be completed before adding them. Prices and stock are checked
again when placing the demo order, and no real money is charged.

After checkout, **View / print receipt** opens a receipt with the purchased
component names, quantities, prices, delivery details, and zero amount charged.
Use **Print / save PDF** to keep a copy. The QR code opens that same receipt and
requires the customer to sign in with the account that placed the order.

QR codes use the locally bundled, MIT-licensed
[Project Nayuki QR Code generator v1.8.0](https://github.com/nayuki/QR-Code-generator/releases/tag/v1.8.0).
No QR service, API key, Composer install, or database migration is required.
The library's license and source details are in `assets/js/vendor/`.
For scanning from a phone, set `PCFORGE_PUBLIC_URL` in Apache/PHP to a reachable
LAN or shared site URL, for example `http://192.168.10.105/PCForge`, then restart
Apache and create/display the receipt again. This checkout also sets the value in
the root `.htaccess`; update that value to match your network. If it is left blank,
the QR uses the current browser address; a `localhost` link only works on the same
computer. See [local network sharing](#local-network-sharing) below.

## Real product data

The catalog contains 335 products: 35 in each of nine original categories and 20 case fans. The October 9, 2026 expansion adds 20 real products per category, with locally stored retailer photos and recorded source URLs. Prices are in USD. New prices are reference snapshots converted from Computer Lounge NZD listings (including GST), using 1 NZD = 0.55891 USD on October 8, 2026. They are not live US retail quotes. New stock quantities remain unconfirmed; original inventory quantities are project assumptions.

The website reads products from MariaDB and final images from `assets/images`. All 335 catalog image files are included: the original 135 images plus 200 product photos downloaded for the expansion. The original `ProductsData` folder, duplicate photos, one-time preparation tools, and local `.tools` runtime are excluded from the current repository files and are not required to run the website. The repeatable web catalog importer and its reviewed manifest are included for existing installations.

For an existing installation, run `C:/xampp/php/php.exe Database/import_web_catalog.php` to preview the 200 additions, then run the same command with `--apply`. This repeatable upgrade preserves existing products and subsequent admin edits. Fresh installs already receive the additions from `catalog.sql`.

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

Run the isolated checks first, then the database and HTTP suites as needed:

```powershell
C:/xampp/php/php.exe tests/run.php
C:/xampp/php/php.exe tests/run.php --database
C:/xampp/php/php.exe tests/run.php --http
```

The default suite uses in-memory SQLite and test doubles. The database suite requires MariaDB; its catalog checks create and drop temporary databases, while access and relationship checks roll back their fixtures. HTTP tests also require a running development site and remove their own disposable records.

Use `--all` to run all ten scripts. See [`tests/README.md`](tests/README.md) for extensions, database permissions, individual scripts, and `PCFORGE_TEST_URL`.

## Project layout

| Path | Purpose |
| --- | --- |
| Root PHP pages | Storefront, account, builder, cart, and checkout routes. |
| `admin/` | Administrator routes. |
| `auth/` | Google sign-in handlers. |
| `includes/` | Shared PHP logic and page fragments. |
| `assets/` | Stylesheets, browser scripts, and final product images. |
| `config/` | Database and integration configuration. |
| `Database/` | Installation SQL and the repeatable catalog upgrade. |
| `docs/` | Catalog, admin, and AI implementation notes. |
| `tests/` | CLI checks and the test runner. |

No frontend build step or Composer installation is required. `.editorconfig`
defines formatting for future edits; generated caches and test previews are
excluded by `.gitignore`.

## Local network sharing

On a trusted local network, open `http://<computer-LAN-IP>/PCForge/` from another
device on the same network. Apache must be running and reachable through the
computer's firewall. Set `PCFORGE_PUBLIC_URL` to that same address for receipt
QR codes, and update it when the computer's LAN address changes.

## Git and deployment notes

The current repository includes application code, final catalog images, documentation, the two setup SQL files, and the web catalog upgrade importer/manifest. `.gitignore` excludes original source material, preparation tools, obsolete SQL files, database backups, and private exports. Keep your backups outside Git.

Files removed from the current version may still exist in older Git commits. The shallow clone command above avoids downloading that old history; downloading the current branch as a ZIP also includes only current files.

Bootstrap CSS and JavaScript are loaded from jsDelivr, so the default site requires internet access for the full styling and navigation experience. The optional Google and Groq integrations also require outbound HTTPS access.

For a public deployment, use HTTPS, configure the database credentials and environment variables on the server, update the Google callback URI, and use a proper web-server account instead of the local XAMPP root account.
