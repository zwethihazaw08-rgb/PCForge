-- PCForge demonstration data.
-- Import schema.sql first, then import this file.
-- Values are fictional school-project examples, not live market prices.
SET NAMES utf8mb4;

INSERT INTO cpu
    (id, name, short_name, price, brand, series, tdp, socket, memory_type, cores, threads, base_clock, stock, status)
VALUES
    (1, 'ForgeCore 7600', 'FC 7600', 219.99, 'ForgeCore', '7000 Series', 65, 'AM5', 'DDR5', '6', '12', '3.8 GHz', 12, 'active'),
    (2, 'ForgeCore 13600', 'FC 13600', 279.99, 'ForgeCore', '13000 Series', 125, 'LGA1700', 'DDR5', '14', '20', '3.5 GHz', 7, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO mb
    (id, name, price, socket, memory_type, chipset, size, brand, ram_slots, sata_ports, nvme_slots, power_watts, stock, status)
VALUES
    (1, 'ForgeBoard B650 ATX', 159.99, 'AM5', 'DDR5', 'B650', 'ATX', 'ForgeBoard', 4, 4, 2, 50, 8, 'active'),
    (2, 'ForgeBoard Z790 DDR5', 229.99, 'LGA1700', 'DDR5', 'Z790', 'ATX', 'ForgeBoard', 4, 6, 3, 55, 5, 'active'),
    (3, 'ForgeBoard B550 DDR4', 119.99, 'AM4', 'DDR4', 'B550', 'Micro-ATX', 'ForgeBoard', 4, 4, 1, 45, 9, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO memory
    (id, name, price, type, capacity, speed, brand, latency, modules, module_count, power_watts, stock, status)
VALUES
    (1, 'ForgeMemory 32 GB DDR5', 89.99, 'DDR5', '32 GB', '6000 MT/s', 'ForgeMemory', 'CL30', '2 x 16 GB', 2, 10, 20, 'active'),
    (2, 'ForgeMemory 16 GB DDR4', 39.99, 'DDR4', '16 GB', '3200 MT/s', 'ForgeMemory', 'CL16', '2 x 8 GB', 2, 10, 24, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO gpu
    (id, name, short_name, price, brand, vram, length_mm, tdp, recommended_psu_watts, stock, status)
VALUES
    (1, 'ForgeVision 4070', 'FV 4070', 599.99, 'ForgeVision', '12 GB', 300, 200, 650, 6, 'active'),
    (2, 'ForgeVision 7900 XT', 'FV 7900 XT', 699.99, 'ForgeVision', '20 GB', 360, 300, 750, 3, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO storage
    (id, name, price, type, interface, capacity, brand, read_speed, write_speed, power_watts, stock, status)
VALUES
    (1, 'ForgeDrive 1 TB NVMe', 69.99, 'SSD', 'NVMe', '1 TB', 'ForgeDrive', '7000 MB/s', '6000 MB/s', 5, 18, 'active'),
    (2, 'ForgeDrive 2 TB SATA SSD', 89.99, 'SSD', 'SATA', '2 TB', 'ForgeDrive', '560 MB/s', '520 MB/s', 5, 14, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO psu
    (id, name, price, wattage, wattage_watts, rating, brand, modularity, stock, status)
VALUES
    (1, 'ForgePower 650 W Bronze', 79.99, '650 W', 650, '80+ Bronze', 'ForgePower', 'Semi-modular', 10, 'active'),
    (2, 'ForgePower 850 W Gold', 129.99, '850 W', 850, '80+ Gold', 'ForgePower', 'Fully modular', 8, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO case_box
    (id, name, price, size, color, brand, max_gpu_length, max_radiator_size, max_cooler_height_mm, stock, status)
VALUES
    (1, 'ForgeCase Air ATX', 89.99, 'ATX Mid Tower', 'Black', 'ForgeCase', 340, 360, 170, 11, 'active'),
    (2, 'ForgeCase Compact', 69.99, 'Micro-ATX', 'White', 'ForgeCase', 320, 240, 155, 9, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO cooling
    (id, name, price, type, size, radiator_size_mm, height_mm, power_watts, brand, stock, status)
VALUES
    (1, 'ForgeCool Tower 120', 39.99, 'Air cooler', '120 mm', NULL, 155, 5, 'ForgeCool', 15, 'active'),
    (2, 'ForgeCool Liquid 360', 109.99, 'Liquid cooler', '360 mm', 360, NULL, 8, 'ForgeCool', 5, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

INSERT INTO fans
    (id, name, price, size, rgb, brand, power_watts, stock, status)
VALUES
    (1, 'ForgeFlow 120 Fan', 14.99, '120 mm', 'No', 'ForgeFlow', 3, 30, 'active'),
    (2, 'ForgeFlow 140 RGB Fan', 19.99, '140 mm', 'Yes', 'ForgeFlow', 4, 20, 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), status = VALUES(status);

-- Verified relationships used by the compatibility engine.
INSERT IGNORE INTO case_motherboard_support (case_id, form_factor) VALUES
    (1, 'ATX'), (1, 'Micro-ATX'), (1, 'Mini-ITX'),
    (2, 'Micro-ATX'), (2, 'Mini-ITX');

INSERT IGNORE INTO cooling_socket_support (cooling_id, socket) VALUES
    (1, 'AM4'), (1, 'AM5'), (1, 'LGA1700'),
    (2, 'AM4'), (2, 'AM5'), (2, 'LGA1700');
