-- PCForge supplied catalog: import once into an empty schema.sql installation.
-- 135 real products, USD prices, final images, and known compatibility support.
-- Stock is a snapshot of project inventory, not verified supplier stock.
-- Contains no accounts, password hashes, orders, shipping details, or saved builds.
-- Plain INSERT statements intentionally refuse conflicting IDs; never use this as a live reset.
SET NAMES utf8mb4;
SET @pcforge_previous_sql_mode = @@SESSION.sql_mode;
SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;

-- cpu: 15 rows
INSERT INTO `cpu` (`id`,`name`,`short_name`,`price`,`image_url`,`brand`,`series`,`tdp`,`socket`,`memory_type`,`cores`,`threads`,`base_clock`,`stock`,`status`,`description`) VALUES
('19','Intel Core i9-14900KS','Intel Core i9-14900KS','689.99','productsdata-cpu-1-transparent.png','Intel',NULL,'150','LGA1700',NULL,'24 (8P + 16E)','32','3.2 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.2 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 150W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770'),
('20','AMD Ryzen 9 7950X3D','AMD Ryzen 9 7950X3D','649.00','productsdata-cpu-2-transparent.png','AMD',NULL,'120','AM5',NULL,'16 Cores','32','4.2 GHz','10','active','Socket / Motherboard Support: AM5\nCore Count: 16 Cores / 32 Threads\nPerf Core Clock: 4.2 GHz (5.7 GHz Boost)\nMicroarchitecture: Zen 4 (3D V-Cache)\nTDP: 120W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)'),
('21','Intel Core i9-14900K','Intel Core i9-14900K','549.99','productsdata-cpu-3-transparent.png','Intel',NULL,'125','LGA1700',NULL,'24 (8P + 16E)','32','3.2 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.0 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770'),
('22','AMD Ryzen 9 7950X','AMD Ryzen 9 7950X','549.00','productsdata-cpu-4-transparent.png','AMD',NULL,'170','AM5',NULL,'16 Cores','32','4.5 GHz','10','active','Socket / Motherboard Support: AM5\nCore Count: 16 Cores / 32 Threads\nPerf Core Clock: 4.5 GHz (5.7 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 170W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)'),
('23','Intel Core i9-13900KS','Intel Core i9-13900KS','629.99','productsdata-cpu-5-transparent.png','Intel',NULL,'150','LGA1700',NULL,'24 (8P + 16E)','32','3.2 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.0 GHz Boost)\nMicroarchitecture: Raptor Lake\nTDP: 150W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770'),
('24','AMD Ryzen 7 7800X3D','AMD Ryzen 7 7800X3D','369.00','productsdata-cpu-6-transparent.png','AMD',NULL,'120','AM5',NULL,'8 Cores','16','4.2 GHz','10','active','Socket / Motherboard Support: AM5\nCore Count: 8 Cores / 16 Threads\nPerf Core Clock: 4.2 GHz (5.0 GHz Boost)\nMicroarchitecture: Zen 4 (3D V-Cache)\nTDP: 120W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)'),
('25','Intel Core i9-13900K','Intel Core i9-13900K','489.99','productsdata-cpu-7-transparent.png','Intel',NULL,'125','LGA1700',NULL,'24 (8P + 16E)','32','3.0 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.0 GHz (5.8 GHz Boost)\nMicroarchitecture: Raptor Lake\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770'),
('26','AMD Ryzen 9 7900X3D','AMD Ryzen 9 7900X3D','499.00','productsdata-cpu-8-transparent.png','AMD',NULL,'120','AM5',NULL,'12 Cores','24','4.4 GHz','10','active','Socket / Motherboard Support: AM5\nCore Count: 12 Cores / 24 Threads\nPerf Core Clock: 4.4 GHz (5.6 GHz Boost)\nMicroarchitecture: Zen 4 (3D V-Cache)\nTDP: 120W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)'),
('27','Intel Core i7-14700K','Intel Core i7-14700K','389.99','productsdata-cpu-9-transparent.png','Intel',NULL,'125','LGA1700',NULL,'20 (8P + 12E)','28','3.4 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 20 (8P + 12E) / 28 Threads\nPerf Core Clock: 3.4 GHz (5.6 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770'),
('28','AMD Ryzen 9 7900X','AMD Ryzen 9 7900X','389.00','productsdata-cpu-10-transparent.png','AMD',NULL,'170','AM5',NULL,'12 Cores','24','4.7 GHz','9','active','Socket / Motherboard Support: AM5\nCore Count: 12 Cores / 24 Threads\nPerf Core Clock: 4.7 GHz (5.6 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 170W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)'),
('29','Intel Core i9-14900KF','Intel Core i9-14900KF','529.99','productsdata-cpu-11-transparent.png','Intel',NULL,'125','LGA1700',NULL,'24 (8P + 16E)','32','3.2 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.0 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: None (Requires Discrete GPU)'),
('30','AMD Ryzen 9 7900','AMD Ryzen 9 7900','369.00','productsdata-cpu-12-transparent.png','AMD',NULL,'65','AM5',NULL,'12 Cores','24','3.7 GHz','10','active','Socket / Motherboard Support: AM5\nCore Count: 12 Cores / 24 Threads\nPerf Core Clock: 3.7 GHz (5.4 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 65W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)'),
('31','Intel Core i7-13700K','Intel Core i7-13700K','349.99','productsdata-cpu-13-transparent.png','Intel',NULL,'125','LGA1700',NULL,'16 (8P + 8E)','24','3.4 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 16 (8P + 8E) / 24 Threads\nPerf Core Clock: 3.4 GHz (5.4 GHz Boost)\nMicroarchitecture: Raptor Lake\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770'),
('32','AMD Ryzen 7 7700X','AMD Ryzen 7 7700X','289.00','productsdata-cpu-14-transparent.png','AMD',NULL,'105','AM5',NULL,'8 Cores','16','4.5 GHz','10','active','Socket / Motherboard Support: AM5\nCore Count: 8 Cores / 16 Threads\nPerf Core Clock: 4.5 GHz (5.4 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 105W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)'),
('33','Intel Core i9-12900KS','Intel Core i9-12900KS','419.99','productsdata-cpu-15-transparent.png','Intel',NULL,'150','LGA1700',NULL,'16 (8P + 8E)','24','3.4 GHz','10','active','Socket / Motherboard Support: LGA 1700\nCore Count: 16 (8P + 8E) / 24 Threads\nPerf Core Clock: 3.4 GHz (5.5 GHz Boost)\nMicroarchitecture: Alder Lake\nTDP: 150W (241W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770');

-- gpu: 15 rows
INSERT INTO `gpu` (`id`,`name`,`short_name`,`price`,`image_url`,`vram`,`length_mm`,`boost_clock`,`power_consumption`,`tdp`,`psu`,`recommended_psu_watts`,`stock`,`status`,`ports`,`directx`,`dlss`,`cooling`,`brand`,`description`) VALUES
('14','NVIDIA GeForce RTX 5090 FE','NVIDIA GeForce RTX 5090 FE','1999.00','productsdata-gpu-1.png','32 GB GDDR7',NULL,'2400 MHz','575W','575',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'NVIDIA','GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2400 MHz\nTDP / Power: 575W'),
('15','ASUS ROG Strix RTX 5090 OC','ASUS ROG Strix RTX 5090 OC','2299.99','productsdata-gpu-2-transparent.png','32 GB GDDR7',NULL,'2550 MHz','600W','600',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'ASUS','GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2550 MHz\nTDP / Power: 600W'),
('16','MSI GeForce RTX 5090 SUPRIM X','MSI GeForce RTX 5090 SUPRIM X','2199.99','productsdata-gpu-3.png','32 GB GDDR7',NULL,'2535 MHz','600W','600',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'MSI','GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2535 MHz\nTDP / Power: 600W'),
('17','GIGABYTE RTX 5090 AORUS Xtreme','GIGABYTE RTX 5090 AORUS Xtreme','2149.99','productsdata-gpu-4.png','32 GB GDDR7',NULL,'2520 MHz','600W','600',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'GIGABYTE','GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2520 MHz\nTDP / Power: 600W'),
('18','NVIDIA GeForce RTX 5080 FE','NVIDIA GeForce RTX 5080 FE','999.00','productsdata-gpu-5.png','16 GB GDDR7',NULL,'2610 MHz','400W','400',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'NVIDIA','GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2610 MHz\nTDP / Power: 400W'),
('19','ASUS ROG Strix RTX 5080 OC','ASUS ROG Strix RTX 5080 OC','1199.99','productsdata-gpu-6.png','16 GB GDDR7',NULL,'2730 MHz','430W','430',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'ASUS','GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2730 MHz\nTDP / Power: 430W'),
('20','MSI GeForce RTX 5080 SUPRIM X','MSI GeForce RTX 5080 SUPRIM X','1149.99','productsdata-gpu-7-transparent.png','16 GB GDDR7',NULL,'2715 MHz','425W','425',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'MSI','GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2715 MHz\nTDP / Power: 425W'),
('21','NVIDIA GeForce RTX 4090 FE','NVIDIA GeForce RTX 4090 FE','1599.00','productsdata-gpu-8-transparent.png','24 GB GDDR6X',NULL,'2520 MHz','450W','450',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'NVIDIA','GPU Chipset / Architecture: NVIDIA Ada Lovelace AD102\nVRAM Size & Type: 24 GB GDDR6X\nMemory Bus Width: 384-bit\nBoost Core Clock: 2520 MHz\nTDP / Power: 450W'),
('22','ASUS ROG Strix RTX 4090 OC','ASUS ROG Strix RTX 4090 OC','1999.99','productsdata-gpu-9.png','24 GB GDDR6X',NULL,'2640 MHz','500W','500',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'ASUS','GPU Chipset / Architecture: NVIDIA Ada Lovelace AD102\nVRAM Size & Type: 24 GB GDDR6X\nMemory Bus Width: 384-bit\nBoost Core Clock: 2640 MHz\nTDP / Power: 500W'),
('23','MSI GeForce RTX 4090 SUPRIM Liquid X','MSI GeForce RTX 4090 SUPRIM Liquid X','1749.99','productsdata-gpu-10-transparent.png','24 GB GDDR6X',NULL,'2640 MHz','480W','480',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'MSI','GPU Chipset / Architecture: NVIDIA Ada Lovelace AD102\nVRAM Size & Type: 24 GB GDDR6X\nMemory Bus Width: 384-bit\nBoost Core Clock: 2640 MHz\nTDP / Power: 480W'),
('24','AMD Radeon RX 7900 XTX','AMD Radeon RX 7900 XTX','929.99','productsdata-gpu-11.png','24 GB GDDR6',NULL,'2500 MHz','355W','355',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'AMD','GPU Chipset / Architecture: AMD RDNA 3 Navi 31\nVRAM Size & Type: 24 GB GDDR6\nMemory Bus Width: 384-bit\nBoost Core Clock: 2500 MHz\nTDP / Power: 355W'),
('25','Sapphire NITRO+ RX 7900 XTX Vapor-X','Sapphire NITRO+ RX 7900 XTX Vapor-X','1049.99','productsdata-gpu-12-transparent.png','24 GB GDDR6',NULL,'2680 MHz','420W','420',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'Sapphire','GPU Chipset / Architecture: AMD RDNA 3 Navi 31\nVRAM Size & Type: 24 GB GDDR6\nMemory Bus Width: 384-bit\nBoost Core Clock: 2680 MHz\nTDP / Power: 420W'),
('26','NVIDIA GeForce RTX 4080 Super','NVIDIA GeForce RTX 4080 Super','999.00','productsdata-gpu-13.png','16 GB GDDR6X',NULL,'2550 MHz','320W','320',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'NVIDIA','GPU Chipset / Architecture: NVIDIA Ada Lovelace AD103\nVRAM Size & Type: 16 GB GDDR6X\nMemory Bus Width: 256-bit\nBoost Core Clock: 2550 MHz\nTDP / Power: 320W'),
('27','ASUS TUF Gaming RTX 5080 OC','ASUS TUF Gaming RTX 5080 OC','1099.99','productsdata-gpu-14.png','16 GB GDDR7',NULL,'2680 MHz','420W','420',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'ASUS','GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2680 MHz\nTDP / Power: 420W'),
('28','PowerColor RED DEVIL RX 7900 XTX','PowerColor RED DEVIL RX 7900 XTX','979.99','productsdata-gpu-15.png','24 GB GDDR6',NULL,'2565 MHz','395W','395',NULL,NULL,'10','active',NULL,NULL,NULL,NULL,'PowerColor','GPU Chipset / Architecture: AMD RDNA 3 Navi 31\nVRAM Size & Type: 24 GB GDDR6\nMemory Bus Width: 384-bit\nBoost Core Clock: 2565 MHz\nTDP / Power: 395W');

-- mb: 15 rows
INSERT INTO `mb` (`id`,`name`,`price`,`socket`,`memory_type`,`chipset`,`size`,`brand`,`image_url`,`wifi`,`ram_slots`,`sata_ports`,`nvme_slots`,`power_watts`,`stock`,`status`,`description`) VALUES
('14','ASUS ROG Maximus Z790 Extreme','999.99','LGA1700','DDR5','Z790','E-ATX','ASUS','productsdata-mb-1.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / OLED Display'),
('15','MSI MEG Z790 GODLIKE MAX','1199.99','LGA1700','DDR5','Z790','E-ATX','MSI','productsdata-mb-2.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Dark Mirror Black'),
('16','GIGABYTE Z790 AORUS Xtreme X','999.00','LGA1700','DDR5','Z790','E-ATX','GIGABYTE','productsdata-mb-3-transparent.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Titanium'),
('17','ASRock X670E Taichi Carrara','529.99','AM5','DDR5','X670E','E-ATX','ASRock','productsdata-mb-4.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Marble White'),
('18','ASUS ROG Crosshair X670E Extreme','999.99','AM5','DDR5','X670E','E-ATX','ASUS','productsdata-mb-5.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / AniMe Matrix'),
('19','MSI MEG X670E ACE','699.99','AM5','DDR5','X670E','E-ATX','MSI','productsdata-mb-6.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Gold Accents'),
('20','ASUS ROG Strix Z790-E Gaming WiFi II','499.99','LGA1700','DDR5','Z790','ATX','ASUS','productsdata-mb-7.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / RGB'),
('21','GIGABYTE X670E AORUS Xtreme','699.00','AM5','DDR5','X670E','E-ATX','GIGABYTE','productsdata-mb-8-transparent.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Armor Metallic'),
('22','ASRock Z790 Taichi Lite','379.99','LGA1700','DDR5','Z790','E-ATX','ASRock','productsdata-mb-9.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Bronze / Black'),
('23','ASUS ROG Hero Z790 Dark Hero','649.99','LGA1700','DDR5','Z790','ATX','ASUS','productsdata-mb-10-transparent.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Matte Black'),
('24','MSI MPG Z790 Carbon WiFi','449.99','LGA1700','DDR5','Z790','ATX','MSI','productsdata-mb-11.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Carbon Black'),
('25','GIGABYTE Z790 AORUS Master X','549.99','LGA1700','DDR5','Z790','E-ATX','GIGABYTE','productsdata-mb-12.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Dark Grey / Silver'),
('26','ASUS ROG Crosshair X670E Hero','649.99','AM5','DDR5','X670E','ATX','ASUS','productsdata-mb-13.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: AM5\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Polished Black'),
('27','NZXT N7 Z790','299.99','LGA1700','DDR5','Z790','ATX','NZXT','productsdata-mb-14-transparent.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 128 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Matte White'),
('28','ASRock X670E PG Lightning','259.99','AM5','DDR5','X670E','ATX','ASRock','productsdata-mb-15.png',NULL,'4',NULL,NULL,NULL,'10','active','Socket / CPU: AM5\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Cyan');

-- memory: 15 rows
INSERT INTO `memory` (`id`,`name`,`price`,`type`,`capacity`,`speed`,`brand`,`image_url`,`latency`,`modules`,`module_count`,`power_watts`,`stock`,`status`,`description`) VALUES
('12','G.SKILL Trident Z5 RGB 64GB (2x32GB)','349.99','DDR5','64 GB','DDR5-8000 MHz','G.SKILL','productsdata-memory-1-transparent.png','CL38-48-48-128','2 x 32 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-8000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL38-48-48-128\nTested Voltage: 1.45V'),
('13','Corsair Dominator Titanium RGB 64GB (2x32GB)','319.99','DDR5','64 GB','DDR5-7200 MHz','Corsair','productsdata-memory-2.avif','CL34-44-44-96','2 x 32 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-7200 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL34-44-44-96\nTested Voltage: 1.40V'),
('14','TeamGroup T-Force Delta RGB 48GB (2x24GB)','249.99','DDR5','48 GB','DDR5-8200 MHz','TeamGroup','productsdata-memory-3-transparent.png','CL38-49-49-128','2 x 24 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-8200 MHz\nTotal Capacity: 48 GB (2x24GB)\nTested Latency (CAS): CL38-49-49-128\nTested Voltage: 1.40V'),
('15','G.SKILL Trident Z5 Neo RGB (AMD EXPO) 64GB','219.99','DDR5','64 GB','DDR5-6000 MHz','G.SKILL','productsdata-memory-4-transparent.png','CL30-40-40-96','2 x 32 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL30-40-40-96\nTested Voltage: 1.40V'),
('16','Corsair Vengeance RGB 96GB (2x48GB)','379.99','DDR5','96 GB','DDR5-6400 MHz','Corsair','productsdata-memory-5.avif','CL32-40-40-84','2 x 48 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-6400 MHz\nTotal Capacity: 96 GB (2x48GB)\nTested Latency (CAS): CL32-40-40-84\nTested Voltage: 1.35V'),
('17','G.SKILL Trident Z5 RGB 32GB (2x16GB)','179.99','DDR5','32 GB','DDR5-7600 MHz','G.SKILL','productsdata-memory-6-transparent.png','CL36-46-46-121','2 x 16 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-7600 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL36-46-46-121\nTested Voltage: 1.40V'),
('18','Kingston Fury Renegade RGB 64GB (2x32GB)','299.99','DDR5','64 GB','DDR5-7200 MHz','Kingston','productsdata-memory-7-transparent.png','CL38-44-44-105','2 x 32 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-7200 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL38-44-44-105\nTested Voltage: 1.45V'),
('19','Corsair Dominator Platinum RGB 32GB (2x16GB)','169.99','DDR5','32 GB','DDR5-6000 MHz','Corsair','productsdata-memory-8-transparent.png','CL30-36-36-76','2 x 16 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL30-36-36-76\nTested Voltage: 1.40V'),
('20','TeamGroup T-Force XTREEM ARGB 48GB (2x24GB)','269.99','DDR5','48 GB','DDR5-8000 MHz','TeamGroup','productsdata-memory-9-transparent.png','CL38-48-48-128','2 x 24 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-8000 MHz\nTotal Capacity: 48 GB (2x24GB)\nTested Latency (CAS): CL38-48-48-128\nTested Voltage: 1.45V'),
('21','G.SKILL Ripjaws S5 64GB (2x32GB)','209.99','DDR5','64 GB','DDR5-6400 MHz','G.SKILL','productsdata-memory-10-transparent.png','CL32-39-39-102','2 x 32 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-6400 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL32-39-39-102\nTested Voltage: 1.40V'),
('22','Corsair Vengeance 64GB (2x32GB) AMD EXPO','214.99','DDR5','64 GB','DDR5-6000 MHz','Corsair','productsdata-memory-11.avif','CL30-36-36-76','2 x 32 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL30-36-36-76\nTested Voltage: 1.40V'),
('23','Patriot Viper Xtreme 5 RGB 32GB (2x16GB)','189.99','DDR5','32 GB','DDR5-8000 MHz','Patriot','productsdata-memory-12-transparent.png','CL38-48-48-128','2 x 16 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-8000 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL38-48-48-128\nTested Voltage: 1.45V'),
('24','ADATA XPG Lancer RGB 64GB (2x32GB)','199.99','DDR5','64 GB','DDR5-6000 MHz','ADATA','productsdata-memory-13-transparent.png','CL30-40-40-96','2 x 32 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL30-40-40-96\nTested Voltage: 1.35V'),
('25','Thermaltake TOUGHRAM XG RGB D5 32GB','159.99','DDR5','32 GB','DDR5-7200 MHz','Thermaltake','productsdata-memory-14-transparent.png','CL36-46-46-115','2 x 16 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-7200 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL36-46-46-115\nTested Voltage: 1.40V'),
('26','Crucial Pro Overclocking 32GB (2x16GB)','104.99','DDR5','32 GB','DDR5-6000 MHz','Crucial','productsdata-memory-15-transparent.png','CL36-38-38-80','2 x 16 GB','2',NULL,'10','active','Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL36-38-38-80\nTested Voltage: 1.35V');

-- storage: 15 rows
INSERT INTO `storage` (`id`,`name`,`price`,`type`,`interface`,`power_watts`,`stock`,`status`,`capacity`,`brand`,`image_url`,`read_speed`,`write_speed`,`description`) VALUES
('13','Crucial T700 4TB PCIe 5.0 NVMe M.2 SSD','499.99','SSD','NVMe',NULL,'10','active','4 TB','Crucial','productsdata-storage-1-transparent.png','12,400 MB/s','11,800 MB/s','Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 12,400 MB/s\nMax Write Speed: 11,800 MB/s'),
('14','Samsung 990 PRO 4TB PCIe 4.0 NVMe M.2 SSD','349.99','SSD','NVMe',NULL,'10','active','4 TB','Samsung','productsdata-storage-2-transparent.png','7,450 MB/s','6,900 MB/s','Interface / Bus: PCIe Gen 4.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,450 MB/s\nMax Write Speed: 6,900 MB/s'),
('15','Corsair MP700 PRO 2TB PCIe 5.0 NVMe M.2 SSD','289.99','SSD','NVMe',NULL,'10','active','2 TB','Corsair','productsdata-storage-3.avif','12,400 MB/s','10,000 MB/s','Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Air Cooled)\nCapacity: 2 TB\nMax Read Speed: 12,400 MB/s\nMax Write Speed: 10,000 MB/s'),
('16','WD_BLACK SN850X 4TB NVMe M.2 Gaming SSD','309.99','SSD','NVMe',NULL,'10','active','4 TB','WD','productsdata-storage-4.png','7,300 MB/s','6,600 MB/s','Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,300 MB/s\nMax Write Speed: 6,600 MB/s'),
('17','Sabrent Rocket 5 2TB PCIe 5.0 NVMe M.2 SSD','339.99','SSD','NVMe',NULL,'10','active','2 TB','Sabrent','productsdata-storage-5-transparent.png','14,000 MB/s','12,000 MB/s','Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 2 TB\nMax Read Speed: 14,000 MB/s\nMax Write Speed: 12,000 MB/s'),
('18','Gigabyte AORUS Gen5 12000 SSD 2TB','259.99','SSD','NVMe',NULL,'10','active','2 TB','GIGABYTE','productsdata-storage-6.png','12,000 MB/s','9,500 MB/s','Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (M.2 Thermal Guard)\nCapacity: 2 TB\nMax Read Speed: 12,000 MB/s\nMax Write Speed: 9,500 MB/s'),
('19','Seagate FireCuda 530 4TB NVMe M.2 SSD','369.99','SSD','NVMe',NULL,'10','active','4 TB','Seagate','productsdata-storage-7-transparent.png','7,300 MB/s','6,900 MB/s','Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,300 MB/s\nMax Write Speed: 6,900 MB/s'),
('20','TeamGroup T-Force Z540 2TB PCIe 5.0 SSD','249.99','SSD','NVMe',NULL,'10','active','2 TB','TeamGroup','productsdata-storage-8-transparent.png','12,400 MB/s','11,800 MB/s','Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Graphene Heatsink)\nCapacity: 2 TB\nMax Read Speed: 12,400 MB/s\nMax Write Speed: 11,800 MB/s'),
('21','Kingston KC3000 4TB PCIe 4.0 NVMe M.2 SSD','329.99','SSD','NVMe',NULL,'10','active','4 TB','Kingston','productsdata-storage-9-transparent.png','7,000 MB/s','7,000 MB/s','Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280\nCapacity: 4 TB\nMax Read Speed: 7,000 MB/s\nMax Write Speed: 7,000 MB/s'),
('22','Solidigm P44 Pro 2TB PCIe 4.0 NVMe M.2 SSD','169.99','SSD','NVMe',NULL,'10','active','2 TB','Solidigm','productsdata-storage-10.avif','7,000 MB/s','6,500 MB/s','Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280\nCapacity: 2 TB\nMax Read Speed: 7,000 MB/s\nMax Write Speed: 6,500 MB/s'),
('23','Samsung 870 EVO 4TB SATA III 2.5\" SSD','299.99','SSD','SATA',NULL,'10','active','4 TB','Samsung','productsdata-storage-11-transparent.png','560 MB/s','530 MB/s','Interface / Bus: SATA III 6 Gb/s\nForm Factor: 2.5-inch Internal\nCapacity: 4 TB\nMax Read Speed: 560 MB/s\nMax Write Speed: 530 MB/s'),
('24','WD Gold 22TB Enterprise SATA HDD','499.99','HDD','SATA',NULL,'10','active','22 TB','WD','productsdata-storage-12.png','291 MB/s','291 MB/s','Interface / Bus: SATA III 6 Gb/s\nForm Factor: 3.5-inch HDD\nCapacity: 22 TB\nMax Read Speed: 291 MB/s\nMax Write Speed: 291 MB/s'),
('25','Seagate IronWolf Pro 20TB NAS HDD','399.99','HDD','SATA',NULL,'10','active','20 TB','Seagate','productsdata-storage-13-transparent.png','285 MB/s','285 MB/s','Interface / Bus: SATA III 6 Gb/s\nForm Factor: 3.5-inch HDD\nCapacity: 20 TB\nMax Read Speed: 285 MB/s\nMax Write Speed: 285 MB/s'),
('26','Crucial T500 2TB Gen4 NVMe M.2 SSD','154.99','SSD','NVMe',NULL,'10','active','2 TB','Crucial','productsdata-storage-14-transparent.png','7,400 MB/s','7,000 MB/s','Interface / Bus: PCIe Gen 4.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 2 TB\nMax Read Speed: 7,400 MB/s\nMax Write Speed: 7,000 MB/s'),
('27','Lexar NM790 4TB PCIe 4.0 NVMe M.2 SSD','249.99','SSD','NVMe',NULL,'10','active','4 TB','Lexar','productsdata-storage-15-transparent.png','7,400 MB/s','6,500 MB/s','Interface / Bus: PCIe Gen 4.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,400 MB/s\nMax Write Speed: 6,500 MB/s');

-- psu: 15 rows
INSERT INTO `psu` (`id`,`name`,`price`,`wattage`,`wattage_watts`,`stock`,`status`,`rating`,`brand`,`image_url`,`modularity`,`description`) VALUES
('12','Corsair AX1600i 1600W Titanium','609.99','1600 W','1600','10','active','80 PLUS Titanium','Corsair','productsdata-psu-1.avif','Full Modular','Wattage: 1600 W\nEfficiency Rating: 80 PLUS Titanium\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('13','Seasonic PRIME TX-1600 ATX 3.0','559.99','1600 W','1600','10','active','80 PLUS Titanium','Seasonic','productsdata-psu-2-transparent.png','Full Modular','Wattage: 1600 W\nEfficiency Rating: 80 PLUS Titanium\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('14','be quiet! Dark Power Pro 13 1600W','449.99','1600 W','1600','10','active','80 PLUS Titanium','be quiet!','productsdata-psu-3-transparent.png','Full Modular','Wattage: 1600 W\nEfficiency Rating: 80 PLUS Titanium\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Silent Wings Frameless'),
('15','ASUS ROG Thor 1200W Platinum II','359.99','1200 W','1200','10','active','80 PLUS Platinum','ASUS','productsdata-psu-4.png','Full Modular','Wattage: 1200 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Dual Ball Bearing'),
('16','Corsair RM1000x Shift 1000W Gold','209.99','1000 W','1000','10','active','80 PLUS Gold','Corsair','productsdata-psu-5.avif','Full Modular (Side Connect)','Wattage: 1000 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular (Side Connect)\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('17','MSI MEG Ai1300P PCIE5 1300W','319.99','1300 W','1300','10','active','80 PLUS Platinum','MSI','productsdata-psu-6.png','Full Modular','Wattage: 1300 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Hydro-Dynamic Bearing'),
('18','Thermaltake Toughpower GF3 1200W','229.99','1200 W','1200','10','active','80 PLUS Gold','Thermaltake','productsdata-psu-7-transparent.png','Full Modular','Wattage: 1200 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('19','EVGA SuperNOVA 1300 P+ 1300W','279.99','1300 W','1300','10','active','80 PLUS Platinum','EVGA','productsdata-psu-8-transparent.png','Full Modular','Wattage: 1300 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 2.52\nFan Bearing: Double Ball Bearing'),
('20','Seasonic Focus GX-1000 ATX 3.0','179.99','1000 W','1000','10','active','80 PLUS Gold','Seasonic','productsdata-psu-9-transparent.png','Full Modular','Wattage: 1000 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('21','Corsair HX1500i 1500W Platinum','399.99','1500 W','1500','10','active','80 PLUS Platinum','Corsair','productsdata-psu-10.avif','Full Modular','Wattage: 1500 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('22','ASUS ROG Loki SFX-L 1000W Platinum','269.99','1000 W','1000','10','active','80 PLUS Platinum','ASUS','productsdata-psu-11.png','Full Modular','Wattage: 1000 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: SFX-L / ATX 3.0\nFan Bearing: Dual Ball Bearing'),
('23','be quiet! Straight Power 12 1200W','249.99','1200 W','1200','10','active','80 PLUS Platinum','be quiet!','productsdata-psu-12-transparent.png','Full Modular','Wattage: 1200 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Silent Wings 135mm'),
('24','Cooler Master V850 Gold i Multi 850W','159.99','850 W','850','10','active','80 PLUS Gold','Cooler Master','productsdata-psu-13-transparent.png','Full Modular','Wattage: 850 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('25','SilverStone Hela 1200R Platinum','289.99','1200 W','1200','10','active','80 PLUS Platinum','SilverStone','productsdata-psu-14-transparent.png','Full Modular','Wattage: 1200 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing'),
('26','Super Flower Leadex VII Gold 1000W','189.99','1000 W','1000','10','active','80 PLUS Gold','Super Flower','productsdata-psu-15-transparent.png','Full Modular','Wattage: 1000 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing');

-- case_box: 15 rows
INSERT INTO `case_box` (`id`,`name`,`price`,`size`,`color`,`brand`,`image_url`,`max_gpu_length`,`max_radiator_size`,`max_cooler_height_mm`,`side_panel`,`stock`,`status`,`description`) VALUES
('12','Lian Li O11 Dynamic EVO XL','239.99','Full Tower',NULL,'Lian Li','productsdata-case_box-1-transparent.png','460',NULL,NULL,'Tempered Glass (Front & Side)','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass (Front & Side)\nIncluded Fans: 0 (Modular bracket layout)\nMax GPU Clearance: 460 mm'),
('13','HYTE Y70 Touch','359.99','Dual Chamber Mid-Tower',NULL,'HYTE','productsdata-case_box-2-transparent.png','390',NULL,NULL,'Tempered Glass + Integrated 4K Touch Screen','10','active','Form Factor: Dual Chamber Mid-Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass + Integrated 4K Touch Screen\nIncluded Fans: 0\nMax GPU Clearance: 390 mm'),
('14','Corsair 7000D AIRFLOW','269.99','Full Tower',NULL,'Corsair','productsdata-case_box-3.avif','450',NULL,NULL,'Tempered Glass','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 3x 140mm AirGuide\nMax GPU Clearance: 450 mm'),
('15','Fractal Design North XL Mesh','179.99','Full Tower',NULL,'Fractal Design','productsdata-case_box-4.avif','413',NULL,NULL,'Mesh (Real Walnut/Oak Wood Front)','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Mesh (Real Walnut/Oak Wood Front)\nIncluded Fans: 3x 140mm Aspect 14 PWM\nMax GPU Clearance: 413 mm'),
('16','Phanteks NV9 Premium Dual-Chamber','249.99','Full Tower',NULL,'Phanteks','productsdata-case_box-5-transparent.png','440',NULL,NULL,'Seamless Tempered Glass','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Seamless Tempered Glass\nIncluded Fans: 0\nMax GPU Clearance: 440 mm'),
('17','NZXT H9 Elite','239.99','Dual-Chamber Mid-Tower',NULL,'NZXT','productsdata-case_box-6-transparent.png','435',NULL,NULL,'Tempered Glass (Top, Front, Side)','10','active','Form Factor: Dual-Chamber Mid-Tower\nMotherboard Support: ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass (Top, Front, Side)\nIncluded Fans: 3x 120mm F120 RGB Duo + 1x 120mm F120Q\nMax GPU Clearance: 435 mm'),
('18','Cooler Master Cosmos C700P Black Edition','349.99','Full Tower',NULL,'Cooler Master','productsdata-case_box-7.png','490',NULL,NULL,'Curved Tempered Glass','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Curved Tempered Glass\nIncluded Fans: 4x 140mm 1200RPM Fans\nMax GPU Clearance: 490 mm'),
('19','ASUS ROG Hyperion GR701','499.99','Full Tower',NULL,'ASUS','productsdata-case_box-8-transparent.png','460',NULL,NULL,'Hinged Tempered Glass','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Hinged Tempered Glass\nIncluded Fans: 4x 140mm PWM Fans\nMax GPU Clearance: 460 mm'),
('20','Fractal Design Torrent','189.99','Full Tower',NULL,'Fractal Design','productsdata-case_box-9-transparent.png','461',NULL,NULL,'Tempered Glass','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX, SSI-EEB\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 2x 180mm + 3x 140mm Dynamic GP\nMax GPU Clearance: 461 mm'),
('21','Lian Li LANCOOL III RGB','159.99','Mid Tower',NULL,'Lian Li','productsdata-case_box-10-transparent.png','435',NULL,NULL,'Dual Hinged Tempered Glass','10','active','Form Factor: Mid Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Dual Hinged Tempered Glass\nIncluded Fans: 4x 140mm ARGB PWM Fans\nMax GPU Clearance: 435 mm'),
('22','be quiet! Dark Base Pro 901','299.99','Full Tower',NULL,'be quiet!','productsdata-case_box-11-transparent.png','495',NULL,NULL,'Tempered Glass','10','active','Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 3x Silent Wings 4 140mm PWM\nMax GPU Clearance: 495 mm'),
('23','SSUPD Meshlicious','119.99','Small Form Factor (SFF)',NULL,'SSUPD','productsdata-case_box-12-transparent.png','336',NULL,NULL,'Full Mesh','10','active','Form Factor: Small Form Factor (SFF)\nMotherboard Support: Mini-ITX\nSide Panel / Finish: Full Mesh\nIncluded Fans: 0\nMax GPU Clearance: 336 mm'),
('24','Corsair 5000D AIRFLOW','174.99','Mid Tower',NULL,'Corsair','productsdata-case_box-13.avif','420',NULL,NULL,'Tempered Glass','10','active','Form Factor: Mid Tower\nMotherboard Support: ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 2x 120mm AirGuide Fans\nMax GPU Clearance: 420 mm'),
('25','Thermaltake CTE C750 TG ARGB','189.99','Full Tower (Central Thermal)',NULL,'Thermaltake','productsdata-case_box-14-transparent.png','420',NULL,NULL,'Tempered Glass','10','active','Form Factor: Full Tower (Central Thermal)\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 3x 140mm CT140 ARGB Fans\nMax GPU Clearance: 420 mm'),
('26','FormD T1 v2.1','219.99','Small Form Factor (SFF)',NULL,'FormD','productsdata-case_box-15-transparent.png','325',NULL,NULL,'CNC Aluminum Mesh','10','active','Form Factor: Small Form Factor (SFF)\nMotherboard Support: Mini-ITX\nSide Panel / Finish: CNC Aluminum Mesh\nIncluded Fans: 0\nMax GPU Clearance: 325 mm');

-- cooling: 15 rows
INSERT INTO `cooling` (`id`,`name`,`price`,`type`,`size`,`radiator_size_mm`,`height_mm`,`power_watts`,`stock`,`status`,`brand`,`image_url`,`description`) VALUES
('12','Corsair iCUE Link H150i RGB','239.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','Corsair','productsdata-cooling-1.avif','Fan RPM: 480 – 2400 RPM\nNoise Level: 10 – 37 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)'),
('13','NZXT Kraken Elite 360 RGB','299.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','NZXT','productsdata-cooling-2-transparent.png','Fan RPM: 500 – 1800 RPM\nNoise Level: 17.9 – 30.6 dBA\nColor: Matte Black\nRadiator Size (If Contain): 360 mm (Included)'),
('14','ASUS ROG Ryujin III 360 ARGB','349.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','ASUS','productsdata-cooling-3.png','Fan RPM: 600 – 2200 RPM\nNoise Level: 15 – 36.45 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)'),
('15','Noctua NH-D15 chromax.black','119.95','Air cooler',NULL,NULL,NULL,NULL,'10','active','Noctua','productsdata-cooling-4-transparent.png','Fan RPM: 300 – 1500 RPM\nNoise Level: 19.2 – 24.6 dBA\nColor: Black\nRadiator Size (If Contain): None (Air Cooler)'),
('16','Lian Li GA II Trinity Performance 360','169.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','Lian Li','productsdata-cooling-5-transparent.png','Fan RPM: 2300 – 3000 RPM\nNoise Level: 20 – 39.9 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)'),
('17','DeepCool LT720 WH','139.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','DeepCool','productsdata-cooling-6-transparent.png','Fan RPM: 500 – 2250 RPM\nNoise Level: 10 – 32.9 dBA\nColor: White\nRadiator Size (If Contain): 360 mm (Included)'),
('18','ARCTIC Liquid Freezer III 420 A-RGB','149.99','Liquid AIO cooler','420 mm','420',NULL,NULL,'10','active','ARCTIC','productsdata-cooling-7-transparent.png','Fan RPM: 200 – 1900 RPM\nNoise Level: 10.5 – 22.5 dBA\nColor: Black\nRadiator Size (If Contain): 420 mm (Included)'),
('19','MSI MAG CoreLiquid E360','139.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','MSI','productsdata-cooling-8.png','Fan RPM: 600 – 1800 RPM\nNoise Level: 11.2 – 32.5 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)'),
('20','be quiet! Dark Rock Elite','114.90','Air cooler',NULL,NULL,NULL,NULL,'10','active','be quiet!','productsdata-cooling-9-transparent.png','Fan RPM: 1500 – 2000 RPM\nNoise Level: 11 – 25.8 dBA\nColor: Black\nRadiator Size (If Contain): None (Air Cooler)'),
('21','Corsair iCUE H170i LCD XT','309.99','Liquid AIO cooler','420 mm','420',NULL,NULL,'10','active','Corsair','productsdata-cooling-10.avif','Fan RPM: 550 – 1700 RPM\nNoise Level: 5 – 34.1 dBA\nColor: Black\nRadiator Size (If Contain): 420 mm (Included)'),
('22','Thermalright Peerless Assassin 120 SE','33.90','Air cooler',NULL,NULL,NULL,NULL,'10','active','Thermalright','productsdata-cooling-11-transparent.png','Fan RPM: 600 – 1550 RPM\nNoise Level: 15 – 25.6 dBA\nColor: Black / Silver\nRadiator Size (If Contain): None (Air Cooler)'),
('23','NZXT Kraken Elite 280 RGB','259.99','Liquid AIO cooler','280 mm','280',NULL,NULL,'10','active','NZXT','productsdata-cooling-12-transparent.png','Fan RPM: 500 – 1500 RPM\nNoise Level: 19.4 – 32.1 dBA\nColor: Matte White\nRadiator Size (If Contain): 280 mm (Included)'),
('24','EKWB EK-Nucleus AIO CR360 Lux D-RGB','163.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','EKWB','productsdata-cooling-13-transparent.png','Fan RPM: 550 – 2300 RPM\nNoise Level: 12 – 36 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)'),
('25','Phanteks Glacier One 360 T30 V2','259.99','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','Phanteks','productsdata-cooling-14-transparent.png','Fan RPM: 1200 – 3000 RPM\nNoise Level: 11.1 – 39.7 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)'),
('26','be quiet! Pure Loop 2 FX 360mm','159.90','Liquid AIO cooler','360 mm','360',NULL,NULL,'10','active','be quiet!','productsdata-cooling-15-transparent.png','Fan RPM: 600 – 2500 RPM\nNoise Level: 16.8 – 34 dBA\nColor: Black / ARGB\nRadiator Size (If Contain): 360 mm (Included)');

-- monitor: 15 rows
INSERT INTO `monitor` (`id`,`name`,`price`,`brand`,`image_url`,`screen_size`,`panel_type`,`resolution`,`refresh_rate`,`response_time_hdr`,`stock`,`description`,`status`) VALUES
('1','ASUS ROG Swift OLED PG32UCDM','1299.99','ASUS','productsdata-monitor-1-transparent.png','32\"','QD-OLED','3840 x 2160 (4K UHD)','240 Hz','0.03 ms GTG | HDR TRUE BLACK 400','10','Screen Size: 32\"\nPanel Type: QD-OLED\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR TRUE BLACK 400','active'),
('2','Dell Alienware AW3225QF','1199.99','Dell','productsdata-monitor-2.avif','32\"','Curved QD-OLED (1700R)','3840 x 2160 (4K UHD)','240 Hz','0.03 ms GTG | Dolby Vision / HDR400','10','Screen Size: 32\"\nPanel Type: Curved QD-OLED (1700R)\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | Dolby Vision / HDR400','active'),
('3','LG UltraGear 32GS95UE-B','1399.99','LG','productsdata-monitor-3-transparent.png','32\"','WOLED','4K 240Hz / FHD 480Hz','240 Hz / 480 Hz Dual','0.03 ms GTG | DisplayHDR True Black 400','10','Screen Size: 32\"\nPanel Type: WOLED\nResolution: 4K 240Hz / FHD 480Hz\nRefresh Rate: 240 Hz / 480 Hz Dual\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400','active'),
('4','MSI MPG 321URX QD-OLED','949.99','MSI','productsdata-monitor-4-transparent.png','32\"','QD-OLED','3840 x 2160 (4K UHD)','240 Hz','0.03 ms GTG | ClearMR 13000 / HDR400','10','Screen Size: 32\"\nPanel Type: QD-OLED\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | ClearMR 13000 / HDR400','active'),
('5','Samsung Odyssey OLED G9 (G95SC)','1799.99','Samsung','productsdata-monitor-5-transparent.png','49\"','Curved QD-OLED (1800R)','5120 x 1440 (Dual QHD)','240 Hz','0.03 ms GTG | DisplayHDR True Black 400','10','Screen Size: 49\"\nPanel Type: Curved QD-OLED (1800R)\nResolution: 5120 x 1440 (Dual QHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400','active'),
('6','Samsung Odyssey Neo G9 (G95NC)','2499.99','Samsung','productsdata-monitor-6-transparent.png','57\"','Curved Mini-LED (1000R)','7680 x 2160 (Dual UHD)','240 Hz','1 ms GTG | DisplayHDR 1000','10','Screen Size: 57\"\nPanel Type: Curved Mini-LED (1000R)\nResolution: 7680 x 2160 (Dual UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 1 ms GTG | DisplayHDR 1000','active'),
('7','ASUS ROG Swift Pro PG248QP','899.99','ASUS','productsdata-monitor-7-transparent.png','24.1\"','E-TN','1920 x 1080 (FHD)','540 Hz','0.2 ms GTG | DisplayHDR 400','10','Screen Size: 24.1\"\nPanel Type: E-TN\nResolution: 1920 x 1080 (FHD)\nRefresh Rate: 540 Hz\nResponse Time / HDR: 0.2 ms GTG | DisplayHDR 400','active'),
('8','Dell Alienware AW2725DF','899.99','Dell','productsdata-monitor-8-transparent.png','27\"','QD-OLED','2560 x 1440 (QHD)','360 Hz','0.03 ms GTG | DisplayHDR True Black 400','10','Screen Size: 27\"\nPanel Type: QD-OLED\nResolution: 2560 x 1440 (QHD)\nRefresh Rate: 360 Hz\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400','active'),
('9','Gigabyte AORUS FO32U2P','1299.99','GIGABYTE','productsdata-monitor-9-transparent.png','32\"','QD-OLED (DP 2.1 Native)','3840 x 2160 (4K UHD)','240 Hz','0.03 ms GTG | DisplayHDR True Black 400','10','Screen Size: 32\"\nPanel Type: QD-OLED (DP 2.1 Native)\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400','active'),
('10','BenQ ZOWIE XL2566K','599.99','BenQ','productsdata-monitor-10.avif','24.5\"','TN (DyAc+)','1920 x 1080 (FHD)','360 Hz','0.5 ms GTG | N/A (Esports Tuned)','10','Screen Size: 24.5\"\nPanel Type: TN (DyAc+)\nResolution: 1920 x 1080 (FHD)\nRefresh Rate: 360 Hz\nResponse Time / HDR: 0.5 ms GTG | N/A (Esports Tuned)','active'),
('11','LG UltraGear 27GR95QE-B','799.99','LG','productsdata-monitor-11-transparent.png','27\"','OLED','2560 x 1440 (QHD)','240 Hz','0.03 ms GTG | HDR10','10','Screen Size: 27\"\nPanel Type: OLED\nResolution: 2560 x 1440 (QHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR10','active'),
('12','Corsair XENEON FLEX 45WQHD240','1699.99','Corsair','productsdata-monitor-12-transparent.png','45\"','Bendable OLED (Flat to 800R)','3440 x 1440 (UWQHD)','240 Hz','0.03 ms GTG | HDR10','10','Screen Size: 45\"\nPanel Type: Bendable OLED (Flat to 800R)\nResolution: 3440 x 1440 (UWQHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR10','active'),
('13','ASUS ROG Swift PG27AQDM','899.99','ASUS','productsdata-monitor-13-transparent.png','27\"','OLED','2560 x 1440 (QHD)','240 Hz','0.03 ms GTG | HDR10','10','Screen Size: 27\"\nPanel Type: OLED\nResolution: 2560 x 1440 (QHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR10','active'),
('14','Acer Predator X32 FP','1199.99','Acer','productsdata-monitor-14-transparent.png','32\"','Mini-LED IPS','3840 x 2160 (4K UHD)','160 Hz','0.7 ms GTG | DisplayHDR 1000','10','Screen Size: 32\"\nPanel Type: Mini-LED IPS\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 160 Hz\nResponse Time / HDR: 0.7 ms GTG | DisplayHDR 1000','active'),
('15','Apple Studio Display - Nano-Texture','1899.99','Apple','productsdata-monitor-15-transparent.png','27\"','IPS LCD','5120 x 2880 (5K Retina)','60 Hz','5 ms GTG | 600 nits Brightness','10','Screen Size: 27\"\nPanel Type: IPS LCD\nResolution: 5120 x 2880 (5K Retina)\nRefresh Rate: 60 Hz\nResponse Time / HDR: 5 ms GTG | 600 nits Brightness','active');

-- case_motherboard_support: 53 rows
INSERT INTO `case_motherboard_support` (`case_id`,`form_factor`) VALUES
('12','ATX'),
('12','E-ATX'),
('12','Micro-ATX'),
('12','Mini-ITX'),
('13','ATX'),
('13','E-ATX'),
('13','Micro-ATX'),
('13','Mini-ITX'),
('14','ATX'),
('14','E-ATX'),
('14','Micro-ATX'),
('14','Mini-ITX'),
('15','ATX'),
('15','E-ATX'),
('15','Micro-ATX'),
('15','Mini-ITX'),
('16','ATX'),
('16','E-ATX'),
('16','Micro-ATX'),
('16','Mini-ITX'),
('17','ATX'),
('17','Micro-ATX'),
('17','Mini-ITX'),
('18','ATX'),
('18','E-ATX'),
('18','Micro-ATX'),
('18','Mini-ITX'),
('19','ATX'),
('19','E-ATX'),
('19','Micro-ATX'),
('19','Mini-ITX'),
('20','ATX'),
('20','E-ATX'),
('20','Micro-ATX'),
('20','Mini-ITX'),
('20','SSI-EEB'),
('21','ATX'),
('21','E-ATX'),
('21','Micro-ATX'),
('21','Mini-ITX'),
('22','ATX'),
('22','E-ATX'),
('22','Micro-ATX'),
('22','Mini-ITX'),
('23','Mini-ITX'),
('24','ATX'),
('24','Micro-ATX'),
('24','Mini-ITX'),
('25','ATX'),
('25','E-ATX'),
('25','Micro-ATX'),
('25','Mini-ITX'),
('26','Mini-ITX');

-- cooling_socket_support: 30 rows
INSERT INTO `cooling_socket_support` (`cooling_id`,`socket`) VALUES
('12','AM5'),
('12','LGA1700'),
('13','AM5'),
('13','LGA1700'),
('14','AM5'),
('14','LGA1700'),
('15','AM5'),
('15','LGA1700'),
('16','AM5'),
('16','LGA1700'),
('17','AM5'),
('17','LGA1700'),
('18','AM5'),
('18','LGA1700'),
('19','AM5'),
('19','LGA1700'),
('20','AM5'),
('20','LGA1700'),
('21','AM5'),
('21','LGA1700'),
('22','AM5'),
('22','LGA1700'),
('23','AM5'),
('23','LGA1700'),
('24','AM5'),
('24','LGA1700'),
('25','AM5'),
('25','LGA1700'),
('26','AM5'),
('26','LGA1700');

COMMIT;
SET SESSION sql_mode = @pcforge_previous_sql_mode;
