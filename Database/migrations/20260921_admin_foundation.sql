-- PCForge admin foundation migration. MariaDB 10.4+.
-- Additive and safe to run again; no existing rows are deleted.
SET NAMES utf8mb4;

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
-- Product references span nine tables, so PHP must validate category + product_id.
-- Snapshot name and price remain valid when a product is renamed or disabled.
CREATE TABLE IF NOT EXISTS order_items (
 id int(11) NOT NULL AUTO_INCREMENT,
 order_id int(11) NOT NULL,
 category enum('cpu','gpu','mb','memory','storage','psu','case_box','cooling','fans') NOT NULL,
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

-- One row represents this store; settings UI will update it in a later stage.
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

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `cpu`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `cpu`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `gpu`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `gpu`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `mb`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `mb`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `memory`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `memory`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `storage`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `storage`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `psu`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `psu`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `case_box`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `case_box`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `cooling`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `cooling`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

-- NULL timestamps retain unknown history for existing products.
ALTER TABLE `fans`
 ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS created_at datetime DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS updated_at datetime DEFAULT NULL;
ALTER TABLE `fans`
 MODIFY COLUMN created_at datetime DEFAULT current_timestamp(),
 MODIFY COLUMN updated_at datetime DEFAULT NULL ON UPDATE current_timestamp();

