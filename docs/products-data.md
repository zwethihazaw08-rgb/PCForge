# Product catalog

New installations use two SQL files, in order:

1. `Database/schema.sql` creates the application tables and default store settings.
2. `Database/catalog.sql` loads 135 supplied products and their known case/board and
   cooler/socket support relationships. Import it once into a fresh installation.

The catalog includes 15 products in each of nine categories: CPU, GPU, motherboard,
memory, storage, PSU, case, CPU cooling, and monitor. There were no supplied case
fans. Prices are in USD. Stock is a snapshot of this school project's inventory;
the original quantities were assumed because the supplier files contained no
inventory counts. These are not verified physical quantities.

The catalog seed contains product records only. It does not include accounts,
password hashes, orders, shipping information, saved builds, or private store
contact information. New installations start with empty account and order tables.
Use a private database export to transfer an existing installation with its users
and history. Do not import the catalog seed as a reset over an existing database.

## Images

All 135 final product image files are in `assets/images`: 90 transparent PNG
derivatives and 45 supplied images that already had transparency. The website
reads image filenames from its database. The 90 superseded original copies are
no longer tracked. Site illustrations and images referenced by historical
products remain available.

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
validates product counts, images, compatibility relationships, and empty private
tables, then drops only that temporary database. The configured database account
needs permission to create and drop databases. The running `pcforge` database is
not modified.
