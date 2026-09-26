-- Run once on existing PCForge databases; safe to run again.
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
