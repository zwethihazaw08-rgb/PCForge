-- Real product import support; additive and safe to run again.
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

ALTER TABLE order_items MODIFY category enum('cpu','gpu','mb','memory','storage','psu','case_box','cooling','fans','monitor') NOT NULL;
