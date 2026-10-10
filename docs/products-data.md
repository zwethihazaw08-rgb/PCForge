# Product catalog

New installations use two SQL files, in order:

1. `Database/schema.sql` creates the application tables and default store settings.
2. `Database/catalog.sql` loads 335 products, public web source records, and known case/board and
   cooler/socket support relationships. Import it once into a fresh installation.

The catalog includes 35 products in each of nine categories (CPU, GPU, motherboard,
memory, storage, PSU, case, CPU cooling, and monitor) plus 20 case fans. The original
135 supplied products are preserved; the October 9, 2026 expansion adds 200 products.
All prices use USD. Original inventory quantities are project assumptions; the new
products use `NULL` stock, displayed as unconfirmed availability. An administrator
can set actual stock through Admin > Inventory.

The catalog seed contains product records only. It does not include accounts,
password hashes, orders, shipping information, saved builds, or private store
contact information. New installations start with empty account and order tables.
Use a private database export to transfer an existing installation with its users
and history. Do not import the catalog seed as a reset over an existing database.

## Images

All 335 catalog image files are in `assets/images`. The original 135 consist of
90 transparent PNG derivatives and 45 supplied images. The 200 new transparent PNGs use the
`web-20261009-` prefix and are real product photos from Computer Lounge's public
product listings, downloaded locally at up to 900 pixels wide. The existing local
background-removal tool creates alpha masks while preserving every original RGB
pixel and the image dimensions. Cutouts are reviewed on a dark background. The site does not
need to contact the retailer to render them. Each new image has its source URL and
SHA-256 recorded in `Database/web_catalog.json`, together with the original file
hash and processing method. Original downloads remain in the ignored local
`.tools/catalog-expansion/originals` cache.

## October 2026 web expansion

The reviewed manifest is [`Database/web_catalog.json`](../Database/web_catalog.json).
Each of its 200 entries includes the exact product title, retailer URL/SKU, image
URL and hash, price provenance, retrieval date, normalized fields, and any additional
manufacturer specification sources. Source facts came from
[Computer Lounge](https://computerlounge.co.nz/collections/pc-components), with
processor specifications checked against AMD and Intel and selected cooler and case
specifications checked against manufacturer pages/manuals linked in the manifest.
Descriptions summarize factual specifications rather than copying retailer marketing.
Product photos remain the property of their respective owners.

The new prices are NZ retail listing snapshots including GST, converted to USD using
[Frankfurter's October 8, 2026 rate](https://api.frankfurter.dev/v1/2026-10-08?base=NZD&symbols=USD):
1 NZD = 0.55891 USD, rounded to cents. No supplier stock quantities were inferred.
Unknown technical fields remain `NULL`. A missing support list means unknown;
case support lists are omitted where only partial evidence was available. Clearance
and mounting can also depend on hardware revision, layout, and mounting accessories.

### Upgrade an existing installation

```powershell
C:/xampp/php/php.exe Database/import_web_catalog.php
C:/xampp/php/php.exe Database/import_web_catalog.php --apply
```

The first command validates the manifest, images, and conflicts without writing.
The second adds the batch in one transaction. It uses product IDs 1001–1020 in each
category and source IDs 2026100901–2026100920. Conflicting IDs/names abort the entire
batch instead of replacing existing records. Repeat imports skip recorded sources
and preserve later admin edits, including price, name, stock, and status. Public
provenance is stored in `product_data_sources`; no private tables are touched.
Fresh installations receive the same entries from `catalog.sql` and do not need
this upgrade command.

If this batch was imported before background removal, refresh its original photos:

```powershell
C:/xampp/php/php.exe Database/import_web_catalog.php --refresh-images
C:/xampp/php/php.exe Database/import_web_catalog.php --refresh-images --apply
```

This updates only images whose filenames and recorded hashes match the original
batch. Custom administrator images and all other product fields are preserved.
The first command previews the changes; repeating the applied update is safe.

```powershell
C:/xampp/php/php.exe tests/web-catalog-import.php
```

This isolated-database test checks the dry run, 20 additions per category, atomic
rollback on conflicts, preservation of existing products/admin edits, and repeat
imports without duplicates.

## Original source material

The initial import came from spreadsheets and photographs in `ProductsData`.
The original files, preparation scripts, import manifests, image models, old
migrations, and full database dumps are retained on the maintainer's PC and
ignored by Git. They are not needed to install or run a clone of the website.

The existing local database retains its original import provenance in
`product_data_sources` and its inactive sample products for historical references.
Removing the original files from Git does not change that database. New catalog
installations preserve imported product IDs, descriptions, final image filenames,
and known compatibility data without shipping the original spreadsheets.

Future product edits can be made through Admin > Products and Admin > Inventory.
The bundled SQL is an installation snapshot, so it does not automatically change
when an administrator edits a running database.

## Verify a fresh installation

```powershell
C:/xampp/php/php.exe tests/catalog-install.php
```

This check creates a randomly named temporary database, imports both SQL files,
validates 335 products and image files, manifest agreement, source records,
compatibility relationships, repeat-import behavior, and empty private
tables, then drops only that temporary database. The configured database account
needs permission to create and drop databases. The running `pcforge` database is
not modified.
