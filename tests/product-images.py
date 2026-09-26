"""Validate cutout transparency, original product pixels, and optional HTTP delivery."""
from pathlib import Path
from hashlib import sha256
import argparse
import json
import urllib.request

import numpy as np
from PIL import Image

root = Path(__file__).resolve().parents[1]
parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument("--http", action="store_true")
args = parser.parse_args()
manifest = json.loads((root / "Database/products_data.json").read_text(encoding="utf-8"))
overrides = json.loads((root / "Database/product_image_overrides.json").read_text(encoding="utf-8"))
changed = 0
for product in manifest["products"]:
    key = f"{product['category']}-{product['source_id']}"
    source = root / product["image_source"]
    assert sha256(source.read_bytes()).hexdigest() == product["image_sha256"], key
    original = Image.open(source).convert("RGBA")
    override = overrides.get(key)
    if override:
        path = root / "assets/images" / override["image_url"]
        output = Image.open(path).convert("RGBA")
        assert output.size == original.size, f"Changed dimensions: {key}"
        a = np.asarray(output)
        assert np.array_equal(a[:, :, :3], np.asarray(original)[:, :, :3]), f"Changed product pixels: {key}"
        assert np.mean(a[:, :, 3] == 0) > 0.01, f"No real transparency: {key}"
        assert np.mean(a[:, :, 3] >= 128) > 0.01, f"Lost product: {key}"
        assert sha256(path.read_bytes()).hexdigest() == override["sha256"], key
        changed += 1
    else:
        assert original.getchannel("A").getextrema()[0] < 255, f"Opaque source not processed: {key}"
        path = root / "assets/images" / product["values"]["image_url"]
        assert sha256(path.read_bytes()).hexdigest() == product["image_sha256"], key
    if args.http:
        with urllib.request.urlopen("http://localhost/PCForge/assets/images/" + path.name, timeout=15) as response:
            assert response.status == 200 and response.headers.get("Content-Type", "").startswith("image/"), key
            assert sha256(response.read()).hexdigest() == sha256(path.read_bytes()).hexdigest(), key
print(f"PASS {changed} true-alpha cutouts with unchanged RGB pixels and dimensions; {len(manifest['products']) - changed} existing transparent images preserved.")
if args.http:
    print("PASS all 135 catalog images served correctly over HTTP.")
