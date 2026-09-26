-- PCForge: select an empty database in phpMyAdmin, then import this file.
-- CREATE TABLE IF NOT EXISTS does not upgrade existing tables.
-- After this schema, import catalog.sql once to load the supplied products.
-- Existing installations should keep their database; no reset is needed.
-- NULL means unknown. Do not treat missing specifications as compatible.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer', 'admin') NOT NULL DEFAULT 'customer',
  `status` enum('active', 'disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `user_shipping_details` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(160) NOT NULL,
  `address_line2` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL,
  `region` varchar(100) NOT NULL DEFAULT '',
  `postal_code` varchar(30) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `shipping_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `case_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `max_gpu_length` int(11) DEFAULT NULL,
  `max_radiator_size` int(11) DEFAULT NULL,
  `max_cooler_height_mm` int unsigned DEFAULT NULL,
  `side_panel` varchar(50) DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `cooling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `radiator_size_mm` int unsigned DEFAULT NULL,
  `height_mm` int unsigned DEFAULT NULL,
  `power_watts` int unsigned DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `cpu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `brand` varchar(50) DEFAULT NULL,
  `series` varchar(50) DEFAULT NULL,
  `tdp` int(11) DEFAULT NULL,
  `socket` varchar(50) DEFAULT NULL,
  `memory_type` varchar(10) DEFAULT NULL,
  `cores` varchar(50) DEFAULT NULL,
  `threads` varchar(50) DEFAULT NULL,
  `base_clock` varchar(50) DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `fans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `rgb` varchar(20) DEFAULT NULL,
  `power_watts` int unsigned DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `gpu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `vram` varchar(50) DEFAULT NULL,
  `length_mm` int(11) DEFAULT NULL,
  `boost_clock` varchar(50) DEFAULT NULL,
  `power_consumption` varchar(50) DEFAULT NULL,
  `tdp` int(11) DEFAULT NULL,
  `psu` varchar(50) DEFAULT NULL,
  `recommended_psu_watts` int unsigned DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  `ports` varchar(255) DEFAULT NULL,
  `directx` varchar(50) DEFAULT NULL,
  `dlss` varchar(50) DEFAULT NULL,
  `cooling` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `mb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `socket` varchar(50) DEFAULT NULL,
  `memory_type` varchar(10) DEFAULT NULL,
  `chipset` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `wifi` varchar(20) DEFAULT NULL,
  `ram_slots` int(2) DEFAULT NULL,
  `sata_ports` int unsigned DEFAULT NULL,
  `nvme_slots` int unsigned DEFAULT NULL,
  `power_watts` int unsigned DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `memory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `capacity` varchar(20) DEFAULT NULL,
  `speed` varchar(20) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `latency` varchar(20) DEFAULT NULL,
  `modules` varchar(50) DEFAULT NULL,
  `module_count` int unsigned DEFAULT NULL,
  `power_watts` int unsigned DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `psu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `wattage` varchar(20) DEFAULT NULL,
  `wattage_watts` int unsigned DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  `rating` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `modularity` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- build_data stores component IDs and quantities, not authoritative prices.
-- PHP must validate the JSON structure and look up current product records.
-- Generate share_token with bin2hex(random_bytes(32)) in PHP.
CREATE TABLE IF NOT EXISTS `saved_builds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `build_name` varchar(100) DEFAULT 'My Custom Build',
  `build_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`build_data`)),
  `share_token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `share_token` (`share_token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `saved_builds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `interface` enum('SATA', 'NVMe') DEFAULT NULL,
  `power_watts` int unsigned DEFAULT NULL,
  `stock` int unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  `capacity` varchar(20) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `read_speed` varchar(20) DEFAULT NULL,
  `write_speed` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Record only verified support. No rows means unknown, not unsupported.
-- Use consistent names: ATX, Micro-ATX, Mini-ITX; AM4, AM5, LGA1700, etc.
CREATE TABLE IF NOT EXISTS `case_motherboard_support` (
  `case_id` int(11) NOT NULL,
  `form_factor` varchar(50) NOT NULL,
  PRIMARY KEY (`case_id`, `form_factor`),
  FOREIGN KEY (`case_id`) REFERENCES `case_box` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `cooling_socket_support` (
  `cooling_id` int(11) NOT NULL,
  `socket` varchar(50) NOT NULL,
  PRIMARY KEY (`cooling_id`, `socket`),
  FOREIGN KEY (`cooling_id`) REFERENCES `cooling` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS orders (
 id int(11) NOT NULL AUTO_INCREMENT,
 order_number varchar(40) NOT NULL,
 user_id int(11) DEFAULT NULL,
 customer_name varchar(160) NOT NULL,
 customer_email varchar(160) NOT NULL,
 shipping_address varchar(262) NOT NULL,
 shipping_city varchar(160) NOT NULL,
 shipping_postal_code varchar(30) NOT NULL,
 shipping_region varchar(100) NOT NULL DEFAULT '',
 shipping_country varchar(100) NOT NULL DEFAULT '',
 shipping_phone varchar(30) NOT NULL DEFAULT '',
 subtotal decimal(12,2) NOT NULL,
 shipping_total decimal(12,2) NOT NULL DEFAULT 0.00,
 total decimal(12,2) NOT NULL,
 currency char(3) NOT NULL DEFAULT 'USD',
 payment_method varchar(30) NOT NULL DEFAULT 'demo',
 payment_status enum('unpaid','paid','refunded','demo') NOT NULL DEFAULT 'demo',
 status enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
 stock_deducted_at datetime DEFAULT NULL,
 created_at timestamp NOT NULL DEFAULT current_timestamp(),
 updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 PRIMARY KEY (id),
 UNIQUE KEY order_number (order_number),
 KEY orders_user_created (user_id, created_at),
 KEY orders_status_created (status, created_at),
 KEY orders_created (created_at),
 CONSTRAINT orders_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
 CONSTRAINT orders_amounts_check CHECK (subtotal >= 0 AND shipping_total >= 0 AND total = subtotal + shipping_total)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Each row is one component, including components purchased within a build.
-- Product references span ten tables, so PHP must validate category + product_id.
-- Snapshot name and price remain valid when a product is renamed or disabled.
CREATE TABLE IF NOT EXISTS order_items (
 id int(11) NOT NULL AUTO_INCREMENT,
 order_id int(11) NOT NULL,
 category enum('cpu','gpu','mb','memory','storage','psu','case_box','cooling','fans','monitor') NOT NULL,
 product_id int(11) NOT NULL,
 product_name varchar(255) NOT NULL,
 quantity int unsigned NOT NULL,
 unit_price decimal(10,2) NOT NULL,
 line_total decimal(12,2) NOT NULL,
 PRIMARY KEY (id),
 KEY order_items_order (order_id),
 KEY order_items_product (category, product_id),
 CONSTRAINT order_items_order_fk FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
 CONSTRAINT order_items_amounts_check CHECK (quantity > 0 AND unit_price >= 0 AND line_total = quantity * unit_price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- One row represents this store; administrators can update it through Settings.
CREATE TABLE IF NOT EXISTS store_settings (
 id tinyint unsigned NOT NULL,
 store_name varchar(100) NOT NULL DEFAULT 'PCForge',
 store_email varchar(160) NOT NULL DEFAULT '',
 currency char(3) NOT NULL DEFAULT 'USD',
 low_stock_threshold int unsigned NOT NULL DEFAULT 5,
 maintenance_mode tinyint unsigned NOT NULL DEFAULT 0,
 updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 PRIMARY KEY (id),
 CONSTRAINT store_settings_singleton CHECK (id = 1),
 CONSTRAINT store_settings_maintenance CHECK (maintenance_mode IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO store_settings (id) SELECT 1 WHERE NOT EXISTS (SELECT 1 FROM store_settings WHERE id = 1);

CREATE TABLE IF NOT EXISTS monitor (
 id int(11) NOT NULL AUTO_INCREMENT,
 name varchar(255) NOT NULL,
 price decimal(10,2) DEFAULT NULL,
 brand varchar(50) DEFAULT NULL,
 image_url varchar(255) DEFAULT 'placeholder.png',
 screen_size varchar(20) DEFAULT NULL,
 panel_type varchar(100) DEFAULT NULL,
 resolution varchar(100) DEFAULT NULL,
 refresh_rate varchar(50) DEFAULT NULL,
 response_time_hdr varchar(160) DEFAULT NULL,
 stock int unsigned DEFAULT NULL,
 description text DEFAULT NULL,
 created_at datetime DEFAULT current_timestamp(),
 updated_at datetime DEFAULT NULL ON UPDATE current_timestamp(),
 status enum('active','inactive') NOT NULL DEFAULT 'active',
 PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Source IDs are scoped to a category, never reused as existing catalog IDs.
CREATE TABLE IF NOT EXISTS product_data_sources (
 category varchar(20) NOT NULL,
 source_id int unsigned NOT NULL,
 product_id int(11) NOT NULL,
 source_file varchar(255) NOT NULL,
 source_row int unsigned NOT NULL,
 source_values longtext NOT NULL CHECK (json_valid(source_values)),
 PRIMARY KEY (category, source_id),
 UNIQUE KEY source_product (category, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
