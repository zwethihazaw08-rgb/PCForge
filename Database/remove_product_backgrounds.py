"""Create transparent catalog derivatives, retaining original product pixels.

Run with .tools/background-removal/Scripts/python.exe. Source images are never
overwritten. Review the previews before applying Database/import_products_data.php.
"""
from pathlib import Path
from hashlib import sha256
import argparse
import json
import os

ROOT = Path(__file__).resolve().parents[1]
os.environ["REMBG_HOME"] = str(ROOT / ".tools" / "rembg-models")
os.environ["OMP_NUM_THREADS"] = "4"

import numpy as np
from PIL import Image, ImageDraw, ImageOps
from rembg import new_session, remove
from scipy.ndimage import binary_propagation, distance_transform_edt, label

# These photos have a plain white surround. Flooding only border-connected
# white protects monitor stands, packaging, and detached badges that a model
# can mistakenly classify as background. White text inside products is retained.
WHITE_SURROUND = {"cpu-2", "cpu-4", "cpu-6", "cpu-8", "cpu-11", "memory-4",
                  "gpu-2", "gpu-8", "monitor-1", "monitor-3", "monitor-4",
                  "monitor-5", "monitor-6", "monitor-7", "monitor-8", "monitor-9",
                  "monitor-11", "monitor-12", "monitor-13", "monitor-15"}
DETAILED_MASKS = {"cooling-2", "cooling-4", "cooling-11", "cooling-12", "cooling-13",
                  "gpu-7", "gpu-10"}
BACKGROUND_OPENINGS = {"monitor-7": [(112, 145)],
                       "monitor-9": [(325, 445), (325, 494)],
                       "monitor-15": [(316, 334)]}
# Reviewed source coordinates: the Ryzen box's shadow is outside its six-sided
# outline, and the silver cooler fins need restoring after model segmentation.
OUTLINES = {"cpu-8": ((447, 447), [(70, 76), (285, 32), (382, 69),
                                  (386, 330), (287, 338), (66, 329)])}
RESTORE_REGIONS = {"cooling-11": ((554, 554), [(468, 80), (535, 88), (538, 330),
                                            (526, 396), (469, 404)])}


def reviewed_polygon(size, specification):
    expected_size, points = specification
    if size != expected_size:
        raise ValueError("Reviewed source dimensions changed")
    mask = Image.new("L", (size[0] * 4, size[1] * 4))
    ImageDraw.Draw(mask).polygon([(x * 4, y * 4) for x, y in points], fill=255)
    return mask.resize(size, Image.Resampling.LANCZOS)


def white_surround_mask(original, key):
    rgb = np.asarray(original)[:, :, :3].astype(np.int16)
    minimum = rgb.min(axis=2)
    neutral = np.ptp(rgb, axis=2) <= 20
    white = (minimum >= 235) & neutral
    border = np.zeros(white.shape, dtype=bool)
    border[0, :] = border[-1, :] = True
    border[:, 0] = border[:, -1] = True
    for x, y in BACKGROUND_OPENINGS.get(key, []):
        border[y, x] = True
    background = binary_propagation(border & white, mask=white)
    foreground = ~background
    # Discard isolated pale JPEG speckles outside the product silhouette.
    components, _ = label(foreground)
    sizes = np.bincount(components.ravel())
    speckles = (sizes[components] <= 6) & neutral & (minimum > 175)
    foreground[speckles] = False
    alpha = np.where(foreground, 255, 0).astype(np.uint8)
    # Feather only the pale outermost pixel; interior labels stay opaque.
    edge = foreground & (distance_transform_edt(foreground) <= 1.5) & neutral & (minimum > 175)
    alpha[edge] = np.clip((255 - minimum[edge]) * 255 / 80, 0, 255).astype(np.uint8)
    return Image.fromarray(alpha)


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--only", nargs="*", help="Product keys, e.g. gpu-7 gpu-10")
    parser.add_argument("--force", action="store_true")
    parser.add_argument("--model", default="u2net", choices=["u2net", "birefnet-general-lite"])
    args = parser.parse_args()
    manifest = json.loads((ROOT / "Database/products_data.json").read_text(encoding="utf-8"))
    override_path = ROOT / "Database/product_image_overrides.json"
    overrides = json.loads(override_path.read_text(encoding="utf-8")) if override_path.exists() else {}
    session = None
    session_model = None
    processed = []
    for product in manifest["products"]:
        key = f"{product['category']}-{product['source_id']}"
        if args.only and key not in args.only:
            continue
        original_path = ROOT / product["image_source"]
        source_hash = sha256(original_path.read_bytes()).hexdigest()
        if source_hash != product["image_sha256"]:
            raise ValueError("Source changed; prepare the import again: " + key)
        original = Image.open(original_path).convert("RGBA")
        if original.getchannel("A").getextrema()[0] < 255:
            continue
        filename = f"productsdata-{key}-transparent.png"
        target = ROOT / "assets/images" / filename
        previous = overrides.get(key, {})
        if not args.force and previous.get("source_sha256") == source_hash and target.exists() and sha256(target.read_bytes()).hexdigest() == previous.get("sha256"):
            processed.append((key, target))
            continue
        if key in OUTLINES:
            mask = reviewed_polygon(original.size, OUTLINES[key])
            method = "reviewed product outline; original RGB pixels"
        elif key in WHITE_SURROUND:
            mask = white_surround_mask(original, key)
            method = "border-connected white surround; original RGB pixels"
        else:
            model = "birefnet-general-lite" if key in DETAILED_MASKS else args.model
            if session is None or session_model != model:
                session = None
                session = new_session(model, providers=["CPUExecutionProvider"])
                session_model = model
            mask = remove(original.convert("RGB"), session=session, only_mask=True)
            method = f"{model} alpha mask; original RGB pixels"
        initial_alpha = np.asarray(mask.convert("L"))
        if np.mean(initial_alpha <= 8) < 0.01 or np.mean(initial_alpha >= 128) < 0.01:
            # Give tightly cropped products breathing room for segmentation only.
            # Crop the mask back; never resize or pad the saved product pixels.
            border = max(32, round(max(original.size) * 0.12))
            padded = ImageOps.expand(original.convert("RGB"), border, fill="white")
            padded_mask = remove(padded, session=session, only_mask=True)
            mask = padded_mask.crop((border, border, border + original.width, border + original.height))
        if mask.size != original.size:
            raise ValueError("Unexpected mask size: " + key)
        alpha = np.asarray(mask.convert("L")).copy()
        if key in RESTORE_REGIONS:
            alpha = np.maximum(alpha, np.asarray(reviewed_polygon(original.size, RESTORE_REGIONS[key])))
            method += "; reviewed reflective metal region restored"
        # Remove imperceptible mask haze; retain soft antialiased product edges.
        alpha[alpha <= 8] = 0
        alpha[alpha >= 247] = 255
        if np.mean(alpha == 0) < 0.01 or np.mean(alpha >= 128) < 0.01:
            raise ValueError("Segmentation requires review: " + key)
        result = original.copy()
        result.putalpha(Image.fromarray(alpha))
        result.save(target, optimize=True)
        # RGB channels must stay byte-for-byte identical, including text/branding.
        if not np.array_equal(np.asarray(result)[:, :, :3], np.asarray(original)[:, :, :3]):
            raise ValueError("Product RGB data changed: " + key)
        overrides[key] = {"image_url": filename, "source_sha256": source_hash,
                          "sha256": sha256(target.read_bytes()).hexdigest(),
                          "method": method,
                          "transparent_fraction": round(float(np.mean(alpha == 0)), 4)}
        temporary = override_path.with_suffix(".tmp")
        temporary.write_text(json.dumps(overrides, indent=2) + "\n", encoding="utf-8")
        temporary.replace(override_path)
        processed.append((key, target))
        print(f"Prepared {key}: {original.width}x{original.height}, {np.mean(alpha == 0):.0%} transparent", flush=True)
    # Contact sheets allow every result to be reviewed against a dark background.
    preview_dir = ROOT / ".tools/background-review"
    preview_dir.mkdir(parents=True, exist_ok=True)
    for start in range(0, len(processed), 20):
        batch = processed[start:start + 20]
        sheet = Image.new("RGB", (1500, 310 * ((len(batch) + 4) // 5)), "#202020")
        draw = ImageDraw.Draw(sheet)
        for i, (key, path) in enumerate(batch):
            im = Image.open(path)
            thumb = ImageOps.contain(im, (280, 270))
            x, y = (i % 5) * 300, (i // 5) * 310
            sheet.paste(thumb, (x + (300 - thumb.width) // 2, y + (270 - thumb.height) // 2), thumb)
            draw.text((x + 10, y + 282), key, fill="white")
        sheet.save(preview_dir / f"review-{start // 20 + 1}.jpg", quality=92)
    print(f"Ready: {len(processed)} transparent derivatives; {len(overrides)} total overrides. Originals unchanged.", flush=True)


if __name__ == "__main__":
    main()
