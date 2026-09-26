-- Demo product images for the homepage carousel and catalogue previews.
-- Import after schema.sql. These are local PCForge illustrations, not product photos.
SET NAMES utf8mb4;

UPDATE cpu SET image_url = 'demo-cpu.svg' WHERE status = 'active';
UPDATE gpu SET image_url = 'demo-gpu.svg' WHERE status = 'active';
UPDATE memory SET image_url = 'demo-memory.svg' WHERE status = 'active';
UPDATE storage SET image_url = 'demo-storage.svg' WHERE status = 'active';
