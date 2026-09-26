# ProductsData catalog

The local catalog uses 135 supplied products: 15 each in CPU, GPU, motherboard,
memory, storage, PSU, case, CPU cooler, and monitor. Prices are imported in USD.
The original spreadsheets and images remain unchanged.

Sample Forge products are inactive. Their IDs and specifications remain available
for historical orders and saved builds. Imports receive new database IDs. No users,
orders, shipping details, store settings, or saved builds are deleted or rewritten.
There are no supplied case fans, so that category has no active products.

The spreadsheets contain no inventory. On 2026-09-25, at the user's request to
set availability, unknown stock for the 135 imported products was initialized to
10 units each for this school project. These quantities are assumptions, not
verified physical inventory. They can be changed in Admin > Inventory. The
`20260925_project_stock.sql` migration initializes only unknown stock, preserves
existing quantities (including zero), and does not reactivate sample products.
Unspecified compatibility fields also remain NULL, including GPU lengths,
motherboard storage-slot counts, cooler heights, and case radiator limits. The
builder reports unknown checks where those specifications are needed. Case board
support comes from each case row. Cooler AM5/LGA1700 support comes from the cooler
workbook's subtitle. No other socket support is inferred.

Normalized specifications populate the existing catalog fields. Every original
row is retained in `product_data_sources.source_values`; full source specification
text is also displayed under Product details. RAM images are matched explicitly
to product families, including the shared Trident Z5 RGB image for both capacities.
Monitors are available in the catalog, comparison, cart, and admin pages; they are
not required for the eight-step PC builder.

## Repeat the import

From the project directory, use Python with openpyxl and Pillow installed:

```powershell
python Database/prepare_products_data.py
C:/xampp/php/php.exe Database/import_products_data.php --dry-run
C:/xampp/php/php.exe Database/import_products_data.php --apply
C:/xampp/php/php.exe Database/import_products_data.php --verify
```

Preparation validates all 15 source IDs per category, prices, image matches, and
image decoding. A damaged workbook ZIP index can be read in memory only when all
local entries pass size and CRC validation. Source files are never repaired in place.

Each apply automatically saves a full SQL backup in `Database/backups`, protected
from HTTP access by `.htaccess`. Keep the first `before-products-data-*.sql` backup
to restore the original catalog. Import that backup into an empty recovery database
first if a rollback is needed. Backup files contain account and order data and
should stay private.

The additive migration creates monitor and source-mapping tables and extends order
categories. Product writes run in a transaction. Reimports match category + source
ID, preserve database IDs, stock and publication status, and update supplied fields,
images and compatibility relationships. They do not remove products absent from a
future dataset; changed layouts/counts intentionally require review.

Use `schema.sql` for fresh databases, then this import. `sample_data.sql`,
`demo_images.sql`, and `seed_dashboard.php` are legacy demo fixtures; do not run them
to initialize the real catalog.

## Transparent product images

At the user's request, the 90 opaque product photos have transparent PNG
derivatives named `assets/images/productsdata-<category>-<source-id>-transparent.png`.
The other 45 product images already had transparency and are retained. Originals
in ProductsData and the initial asset copies remain untouched.

`remove_product_backgrounds.py` uses local [rembg](https://github.com/danielgatis/rembg)
U2Net and BiRefNet general lite models to create alpha masks. For selected plain-white
photos, a border-connected background mask preserves packaging, monitor stands, and
badges that the models missed. Detailed cooler and GPU photos use BiRefNet presets.
Reviewed source-coordinate masks remove a box shadow, restore reflective cooler
fins, and clear the openings in monitor stands; these are recorded in the script.
The script retains original RGB pixels and dimensions; it does not generate new
product imagery. Cutouts are reviewed alongside their source images on a dark surface.
`product_image_overrides.json` records source and output hashes. The regular importer
checks and applies these overrides so reimporting does not restore white backgrounds.

The isolated runtime and model cache are in `.tools`, blocked from HTTP access.
To regenerate and validate the derivatives:

```powershell
.tools/background-removal/Scripts/python.exe Database/remove_product_backgrounds.py
C:/xampp/php/php.exe Database/import_products_data.php --dry-run
C:/xampp/php/php.exe Database/import_products_data.php --apply
.tools/background-removal/Scripts/python.exe tests/product-images.py --http
```

Review the contact sheets in `.tools/background-review` before applying changed
cutouts. The script supports `--only gpu-7 gpu-10` and `--force` for targeted reruns,
plus `--model birefnet-general-lite` for more detailed masks where needed.
