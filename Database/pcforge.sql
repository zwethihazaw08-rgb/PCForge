-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 26, 2026 at 11:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pcforge`
--

-- --------------------------------------------------------

--
-- Table structure for table `case_box`
--

CREATE TABLE `case_box` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `max_gpu_length` int(11) DEFAULT NULL,
  `max_radiator_size` int(11) DEFAULT NULL,
  `max_cooler_height_mm` int(10) UNSIGNED DEFAULT NULL,
  `side_panel` varchar(50) DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `case_box`
--

INSERT INTO `case_box` (`id`, `name`, `price`, `size`, `color`, `brand`, `image_url`, `max_gpu_length`, `max_radiator_size`, `max_cooler_height_mm`, `side_panel`, `stock`, `status`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeCase Air ATX', 89.99, 'ATX Mid Tower', 'Black', 'ForgeCase', 'placeholder.png', 340, 360, 170, NULL, 11, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(2, 'ForgeCase Compact', 69.99, 'Micro-ATX', 'White', 'ForgeCase', 'placeholder.png', 320, 240, 155, NULL, 9, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(3, 'ForgeCase Mesh ATX', 109.99, 'ATX Mid Tower', 'Black', 'ForgeCase', 'placeholder.png', 380, 360, 175, NULL, 10, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(4, 'ForgeCase Glass ATX', 129.99, 'ATX Mid Tower', 'White', 'ForgeCase', 'placeholder.png', 370, 360, 170, NULL, 7, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(5, 'ForgeCase Mini', 59.99, 'Micro-ATX', 'Black', 'ForgeCase', 'placeholder.png', 280, 240, 150, NULL, 14, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(6, 'ForgeCase Full Tower', 179.99, 'ATX Full Tower', 'Black', 'ForgeCase', 'placeholder.png', 420, 420, 190, NULL, 5, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(7, 'ForgeCase Office M', 49.99, 'Micro-ATX', 'Gray', 'ForgeCase', 'placeholder.png', 260, 120, 145, NULL, 18, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(12, 'Lian Li O11 Dynamic EVO XL', 239.99, 'Full Tower', NULL, 'Lian Li', 'productsdata-case_box-1-transparent.png', 460, NULL, NULL, 'Tempered Glass (Front & Side)', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass (Front & Side)\nIncluded Fans: 0 (Modular bracket layout)\nMax GPU Clearance: 460 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(13, 'HYTE Y70 Touch', 359.99, 'Dual Chamber Mid-Tower', NULL, 'HYTE', 'productsdata-case_box-2-transparent.png', 390, NULL, NULL, 'Tempered Glass + Integrated 4K Touch Screen', 10, 'active', 'Form Factor: Dual Chamber Mid-Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass + Integrated 4K Touch Screen\nIncluded Fans: 0\nMax GPU Clearance: 390 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(14, 'Corsair 7000D AIRFLOW', 269.99, 'Full Tower', NULL, 'Corsair', 'productsdata-case_box-3.avif', 450, NULL, NULL, 'Tempered Glass', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 3x 140mm AirGuide\nMax GPU Clearance: 450 mm', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(15, 'Fractal Design North XL Mesh', 179.99, 'Full Tower', NULL, 'Fractal Design', 'productsdata-case_box-4.avif', 413, NULL, NULL, 'Mesh (Real Walnut/Oak Wood Front)', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Mesh (Real Walnut/Oak Wood Front)\nIncluded Fans: 3x 140mm Aspect 14 PWM\nMax GPU Clearance: 413 mm', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(16, 'Phanteks NV9 Premium Dual-Chamber', 249.99, 'Full Tower', NULL, 'Phanteks', 'productsdata-case_box-5-transparent.png', 440, NULL, NULL, 'Seamless Tempered Glass', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Seamless Tempered Glass\nIncluded Fans: 0\nMax GPU Clearance: 440 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(17, 'NZXT H9 Elite', 239.99, 'Dual-Chamber Mid-Tower', NULL, 'NZXT', 'productsdata-case_box-6-transparent.png', 435, NULL, NULL, 'Tempered Glass (Top, Front, Side)', 10, 'active', 'Form Factor: Dual-Chamber Mid-Tower\nMotherboard Support: ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass (Top, Front, Side)\nIncluded Fans: 3x 120mm F120 RGB Duo + 1x 120mm F120Q\nMax GPU Clearance: 435 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(18, 'Cooler Master Cosmos C700P Black Edition', 349.99, 'Full Tower', NULL, 'Cooler Master', 'productsdata-case_box-7.png', 490, NULL, NULL, 'Curved Tempered Glass', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Curved Tempered Glass\nIncluded Fans: 4x 140mm 1200RPM Fans\nMax GPU Clearance: 490 mm', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(19, 'ASUS ROG Hyperion GR701', 499.99, 'Full Tower', NULL, 'ASUS', 'productsdata-case_box-8-transparent.png', 460, NULL, NULL, 'Hinged Tempered Glass', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Hinged Tempered Glass\nIncluded Fans: 4x 140mm PWM Fans\nMax GPU Clearance: 460 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(20, 'Fractal Design Torrent', 189.99, 'Full Tower', NULL, 'Fractal Design', 'productsdata-case_box-9-transparent.png', 461, NULL, NULL, 'Tempered Glass', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX, SSI-EEB\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 2x 180mm + 3x 140mm Dynamic GP\nMax GPU Clearance: 461 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(21, 'Lian Li LANCOOL III RGB', 159.99, 'Mid Tower', NULL, 'Lian Li', 'productsdata-case_box-10-transparent.png', 435, NULL, NULL, 'Dual Hinged Tempered Glass', 10, 'active', 'Form Factor: Mid Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Dual Hinged Tempered Glass\nIncluded Fans: 4x 140mm ARGB PWM Fans\nMax GPU Clearance: 435 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(22, 'be quiet! Dark Base Pro 901', 299.99, 'Full Tower', NULL, 'be quiet!', 'productsdata-case_box-11-transparent.png', 495, NULL, NULL, 'Tempered Glass', 10, 'active', 'Form Factor: Full Tower\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 3x Silent Wings 4 140mm PWM\nMax GPU Clearance: 495 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(23, 'SSUPD Meshlicious', 119.99, 'Small Form Factor (SFF)', NULL, 'SSUPD', 'productsdata-case_box-12-transparent.png', 336, NULL, NULL, 'Full Mesh', 10, 'active', 'Form Factor: Small Form Factor (SFF)\nMotherboard Support: Mini-ITX\nSide Panel / Finish: Full Mesh\nIncluded Fans: 0\nMax GPU Clearance: 336 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'Corsair 5000D AIRFLOW', 174.99, 'Mid Tower', NULL, 'Corsair', 'productsdata-case_box-13.avif', 420, NULL, NULL, 'Tempered Glass', 10, 'active', 'Form Factor: Mid Tower\nMotherboard Support: ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 2x 120mm AirGuide Fans\nMax GPU Clearance: 420 mm', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(25, 'Thermaltake CTE C750 TG ARGB', 189.99, 'Full Tower (Central Thermal)', NULL, 'Thermaltake', 'productsdata-case_box-14-transparent.png', 420, NULL, NULL, 'Tempered Glass', 10, 'active', 'Form Factor: Full Tower (Central Thermal)\nMotherboard Support: E-ATX, ATX, Micro-ATX, Mini-ITX\nSide Panel / Finish: Tempered Glass\nIncluded Fans: 3x 140mm CT140 ARGB Fans\nMax GPU Clearance: 420 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(26, 'FormD T1 v2.1', 219.99, 'Small Form Factor (SFF)', NULL, 'FormD', 'productsdata-case_box-15-transparent.png', 325, NULL, NULL, 'CNC Aluminum Mesh', 10, 'active', 'Form Factor: Small Form Factor (SFF)\nMotherboard Support: Mini-ITX\nSide Panel / Finish: CNC Aluminum Mesh\nIncluded Fans: 0\nMax GPU Clearance: 325 mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `case_motherboard_support`
--

CREATE TABLE `case_motherboard_support` (
  `case_id` int(11) NOT NULL,
  `form_factor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `case_motherboard_support`
--

INSERT INTO `case_motherboard_support` (`case_id`, `form_factor`) VALUES
(1, 'ATX'),
(1, 'Micro-ATX'),
(1, 'Mini-ITX'),
(2, 'Micro-ATX'),
(2, 'Mini-ITX'),
(3, 'ATX'),
(3, 'Micro-ATX'),
(3, 'Mini-ITX'),
(4, 'ATX'),
(4, 'Micro-ATX'),
(4, 'Mini-ITX'),
(5, 'Micro-ATX'),
(5, 'Mini-ITX'),
(6, 'ATX'),
(6, 'Micro-ATX'),
(6, 'Mini-ITX'),
(7, 'Micro-ATX'),
(7, 'Mini-ITX'),
(12, 'ATX'),
(12, 'E-ATX'),
(12, 'Micro-ATX'),
(12, 'Mini-ITX'),
(13, 'ATX'),
(13, 'E-ATX'),
(13, 'Micro-ATX'),
(13, 'Mini-ITX'),
(14, 'ATX'),
(14, 'E-ATX'),
(14, 'Micro-ATX'),
(14, 'Mini-ITX'),
(15, 'ATX'),
(15, 'E-ATX'),
(15, 'Micro-ATX'),
(15, 'Mini-ITX'),
(16, 'ATX'),
(16, 'E-ATX'),
(16, 'Micro-ATX'),
(16, 'Mini-ITX'),
(17, 'ATX'),
(17, 'Micro-ATX'),
(17, 'Mini-ITX'),
(18, 'ATX'),
(18, 'E-ATX'),
(18, 'Micro-ATX'),
(18, 'Mini-ITX'),
(19, 'ATX'),
(19, 'E-ATX'),
(19, 'Micro-ATX'),
(19, 'Mini-ITX'),
(20, 'ATX'),
(20, 'E-ATX'),
(20, 'Micro-ATX'),
(20, 'Mini-ITX'),
(20, 'SSI-EEB'),
(21, 'ATX'),
(21, 'E-ATX'),
(21, 'Micro-ATX'),
(21, 'Mini-ITX'),
(22, 'ATX'),
(22, 'E-ATX'),
(22, 'Micro-ATX'),
(22, 'Mini-ITX'),
(23, 'Mini-ITX'),
(24, 'ATX'),
(24, 'Micro-ATX'),
(24, 'Mini-ITX'),
(25, 'ATX'),
(25, 'E-ATX'),
(25, 'Micro-ATX'),
(25, 'Mini-ITX'),
(26, 'Mini-ITX');

-- --------------------------------------------------------

--
-- Table structure for table `cooling`
--

CREATE TABLE `cooling` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `radiator_size_mm` int(10) UNSIGNED DEFAULT NULL,
  `height_mm` int(10) UNSIGNED DEFAULT NULL,
  `power_watts` int(10) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cooling`
--

INSERT INTO `cooling` (`id`, `name`, `price`, `type`, `size`, `radiator_size_mm`, `height_mm`, `power_watts`, `stock`, `status`, `brand`, `image_url`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeCool Tower 120', 39.99, 'Air cooler', '120 mm', NULL, 155, 5, 15, 'inactive', 'ForgeCool', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(2, 'ForgeCool Liquid 360', 109.99, 'Liquid cooler', '360 mm', 360, NULL, 8, 5, 'inactive', 'ForgeCool', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(3, 'ForgeCool Compact 92', 24.99, 'Air cooler', '92 mm', NULL, 135, 4, 20, 'inactive', 'ForgeCool', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(4, 'ForgeCool Tower 140', 59.99, 'Air cooler', '140 mm', NULL, 165, 6, 12, 'inactive', 'ForgeCool', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(5, 'ForgeCool Liquid 240', 79.99, 'Liquid cooler', '240 mm', 240, NULL, 7, 10, 'inactive', 'ForgeCool', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(6, 'ForgeCool Liquid 280', 94.99, 'Liquid cooler', '280 mm', 280, NULL, 8, 7, 'inactive', 'ForgeCool', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(7, 'ForgeCool Liquid 420', 149.99, 'Liquid cooler', '420 mm', 420, NULL, 10, 3, 'inactive', 'ForgeCool', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(12, 'Corsair iCUE Link H150i RGB', 239.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'Corsair', 'productsdata-cooling-1.avif', 'Fan RPM: 480 – 2400 RPM\nNoise Level: 10 – 37 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(13, 'NZXT Kraken Elite 360 RGB', 299.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'NZXT', 'productsdata-cooling-2-transparent.png', 'Fan RPM: 500 – 1800 RPM\nNoise Level: 17.9 – 30.6 dBA\nColor: Matte Black\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(14, 'ASUS ROG Ryujin III 360 ARGB', 349.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'ASUS', 'productsdata-cooling-3.png', 'Fan RPM: 600 – 2200 RPM\nNoise Level: 15 – 36.45 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(15, 'Noctua NH-D15 chromax.black', 119.95, 'Air cooler', NULL, NULL, NULL, NULL, 10, 'active', 'Noctua', 'productsdata-cooling-4-transparent.png', 'Fan RPM: 300 – 1500 RPM\nNoise Level: 19.2 – 24.6 dBA\nColor: Black\nRadiator Size (If Contain): None (Air Cooler)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(16, 'Lian Li GA II Trinity Performance 360', 169.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'Lian Li', 'productsdata-cooling-5-transparent.png', 'Fan RPM: 2300 – 3000 RPM\nNoise Level: 20 – 39.9 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(17, 'DeepCool LT720 WH', 139.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'DeepCool', 'productsdata-cooling-6-transparent.png', 'Fan RPM: 500 – 2250 RPM\nNoise Level: 10 – 32.9 dBA\nColor: White\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(18, 'ARCTIC Liquid Freezer III 420 A-RGB', 149.99, 'Liquid AIO cooler', '420 mm', 420, NULL, NULL, 10, 'active', 'ARCTIC', 'productsdata-cooling-7-transparent.png', 'Fan RPM: 200 – 1900 RPM\nNoise Level: 10.5 – 22.5 dBA\nColor: Black\nRadiator Size (If Contain): 420 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(19, 'MSI MAG CoreLiquid E360', 139.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'MSI', 'productsdata-cooling-8.png', 'Fan RPM: 600 – 1800 RPM\nNoise Level: 11.2 – 32.5 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(20, 'be quiet! Dark Rock Elite', 114.90, 'Air cooler', NULL, NULL, NULL, NULL, 10, 'active', 'be quiet!', 'productsdata-cooling-9-transparent.png', 'Fan RPM: 1500 – 2000 RPM\nNoise Level: 11 – 25.8 dBA\nColor: Black\nRadiator Size (If Contain): None (Air Cooler)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(21, 'Corsair iCUE H170i LCD XT', 309.99, 'Liquid AIO cooler', '420 mm', 420, NULL, NULL, 10, 'active', 'Corsair', 'productsdata-cooling-10.avif', 'Fan RPM: 550 – 1700 RPM\nNoise Level: 5 – 34.1 dBA\nColor: Black\nRadiator Size (If Contain): 420 mm (Included)', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(22, 'Thermalright Peerless Assassin 120 SE', 33.90, 'Air cooler', NULL, NULL, NULL, NULL, 10, 'active', 'Thermalright', 'productsdata-cooling-11-transparent.png', 'Fan RPM: 600 – 1550 RPM\nNoise Level: 15 – 25.6 dBA\nColor: Black / Silver\nRadiator Size (If Contain): None (Air Cooler)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(23, 'NZXT Kraken Elite 280 RGB', 259.99, 'Liquid AIO cooler', '280 mm', 280, NULL, NULL, 10, 'active', 'NZXT', 'productsdata-cooling-12-transparent.png', 'Fan RPM: 500 – 1500 RPM\nNoise Level: 19.4 – 32.1 dBA\nColor: Matte White\nRadiator Size (If Contain): 280 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'EKWB EK-Nucleus AIO CR360 Lux D-RGB', 163.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'EKWB', 'productsdata-cooling-13-transparent.png', 'Fan RPM: 550 – 2300 RPM\nNoise Level: 12 – 36 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(25, 'Phanteks Glacier One 360 T30 V2', 259.99, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'Phanteks', 'productsdata-cooling-14-transparent.png', 'Fan RPM: 1200 – 3000 RPM\nNoise Level: 11.1 – 39.7 dBA\nColor: Black\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(26, 'be quiet! Pure Loop 2 FX 360mm', 159.90, 'Liquid AIO cooler', '360 mm', 360, NULL, NULL, 10, 'active', 'be quiet!', 'productsdata-cooling-15-transparent.png', 'Fan RPM: 600 – 2500 RPM\nNoise Level: 16.8 – 34 dBA\nColor: Black / ARGB\nRadiator Size (If Contain): 360 mm (Included)', '2026-09-25 13:52:50', '2026-09-26 22:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `cooling_socket_support`
--

CREATE TABLE `cooling_socket_support` (
  `cooling_id` int(11) NOT NULL,
  `socket` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cooling_socket_support`
--

INSERT INTO `cooling_socket_support` (`cooling_id`, `socket`) VALUES
(1, 'AM4'),
(1, 'AM5'),
(1, 'LGA1700'),
(2, 'AM4'),
(2, 'AM5'),
(2, 'LGA1700'),
(3, 'AM4'),
(3, 'AM5'),
(3, 'LGA1700'),
(4, 'AM4'),
(4, 'AM5'),
(4, 'LGA1700'),
(5, 'AM4'),
(5, 'AM5'),
(5, 'LGA1700'),
(6, 'AM4'),
(6, 'AM5'),
(6, 'LGA1700'),
(7, 'AM5'),
(7, 'LGA1700'),
(12, 'AM5'),
(12, 'LGA1700'),
(13, 'AM5'),
(13, 'LGA1700'),
(14, 'AM5'),
(14, 'LGA1700'),
(15, 'AM5'),
(15, 'LGA1700'),
(16, 'AM5'),
(16, 'LGA1700'),
(17, 'AM5'),
(17, 'LGA1700'),
(18, 'AM5'),
(18, 'LGA1700'),
(19, 'AM5'),
(19, 'LGA1700'),
(20, 'AM5'),
(20, 'LGA1700'),
(21, 'AM5'),
(21, 'LGA1700'),
(22, 'AM5'),
(22, 'LGA1700'),
(23, 'AM5'),
(23, 'LGA1700'),
(24, 'AM5'),
(24, 'LGA1700'),
(25, 'AM5'),
(25, 'LGA1700'),
(26, 'AM5'),
(26, 'LGA1700');

-- --------------------------------------------------------

--
-- Table structure for table `cpu`
--

CREATE TABLE `cpu` (
  `id` int(11) NOT NULL,
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
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cpu`
--

INSERT INTO `cpu` (`id`, `name`, `short_name`, `price`, `image_url`, `brand`, `series`, `tdp`, `socket`, `memory_type`, `cores`, `threads`, `base_clock`, `stock`, `status`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeCore 7600', 'FC 7600', 219.99, 'demo-cpu.svg', 'ForgeCore', '7000 Series', 65, 'AM5', 'DDR5', '6', '12', '3.8 GHz', 12, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(2, 'ForgeCore 13600', 'FC 13600', 279.99, 'demo-cpu.svg', 'ForgeCore', '13000 Series', 125, 'LGA1700', 'DDR5', '14', '20', '3.5 GHz', 7, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(3, 'ForgeCore 7500', 'FC 7500', 179.99, 'demo-cpu.svg', 'ForgeCore', '7000 Series', 65, 'AM5', 'DDR5', '6', '12', '3.5 GHz', 15, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(4, 'ForgeCore 7700X', 'FC 7700X', 329.99, 'demo-cpu.svg', 'ForgeCore', '7000 Series', 105, 'AM5', 'DDR5', '8', '16', '4.5 GHz', 8, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(5, 'ForgeCore 7900X', 'FC 7900X', 449.99, 'demo-cpu.svg', 'ForgeCore', '7000 Series', 170, 'AM5', 'DDR5', '12', '24', '4.7 GHz', 5, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(6, 'ForgeCore 12400', 'FC 12400', 149.99, 'demo-cpu.svg', 'ForgeCore', '12000 Series', 65, 'LGA1700', 'DDR5', '6', '12', '2.5 GHz', 14, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(7, 'ForgeCore 13700', 'FC 13700', 369.99, 'demo-cpu.svg', 'ForgeCore', '13000 Series', 125, 'LGA1700', 'DDR5', '16', '24', '3.4 GHz', 6, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(8, 'ForgeCore 5600X', 'FC 5600X', 129.99, 'demo-cpu.svg', 'ForgeCore', '5000 Series', 65, 'AM4', 'DDR4', '6', '12', '3.7 GHz', 20, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(9, 'ForgeCore 5800X', 'FC 5800X', 199.99, 'demo-cpu.svg', 'ForgeCore', '5000 Series', 105, 'AM4', 'DDR4', '8', '16', '3.8 GHz', 10, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(19, 'Intel Core i9-14900KS', 'Intel Core i9-14900KS', 689.99, 'productsdata-cpu-1-transparent.png', 'Intel', NULL, 150, 'LGA1700', NULL, '24 (8P + 16E)', '32', '3.2 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.2 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 150W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(20, 'AMD Ryzen 9 7950X3D', 'AMD Ryzen 9 7950X3D', 649.00, 'productsdata-cpu-2-transparent.png', 'AMD', NULL, 120, 'AM5', NULL, '16 Cores', '32', '4.2 GHz', 10, 'active', 'Socket / Motherboard Support: AM5\nCore Count: 16 Cores / 32 Threads\nPerf Core Clock: 4.2 GHz (5.7 GHz Boost)\nMicroarchitecture: Zen 4 (3D V-Cache)\nTDP: 120W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(21, 'Intel Core i9-14900K', 'Intel Core i9-14900K', 549.99, 'productsdata-cpu-3-transparent.png', 'Intel', NULL, 125, 'LGA1700', NULL, '24 (8P + 16E)', '32', '3.2 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.0 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(22, 'AMD Ryzen 9 7950X', 'AMD Ryzen 9 7950X', 549.00, 'productsdata-cpu-4-transparent.png', 'AMD', NULL, 170, 'AM5', NULL, '16 Cores', '32', '4.5 GHz', 10, 'active', 'Socket / Motherboard Support: AM5\nCore Count: 16 Cores / 32 Threads\nPerf Core Clock: 4.5 GHz (5.7 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 170W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(23, 'Intel Core i9-13900KS', 'Intel Core i9-13900KS', 629.99, 'productsdata-cpu-5-transparent.png', 'Intel', NULL, 150, 'LGA1700', NULL, '24 (8P + 16E)', '32', '3.2 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.0 GHz Boost)\nMicroarchitecture: Raptor Lake\nTDP: 150W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'AMD Ryzen 7 7800X3D', 'AMD Ryzen 7 7800X3D', 369.00, 'productsdata-cpu-6-transparent.png', 'AMD', NULL, 120, 'AM5', NULL, '8 Cores', '16', '4.2 GHz', 10, 'active', 'Socket / Motherboard Support: AM5\nCore Count: 8 Cores / 16 Threads\nPerf Core Clock: 4.2 GHz (5.0 GHz Boost)\nMicroarchitecture: Zen 4 (3D V-Cache)\nTDP: 120W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(25, 'Intel Core i9-13900K', 'Intel Core i9-13900K', 489.99, 'productsdata-cpu-7-transparent.png', 'Intel', NULL, 125, 'LGA1700', NULL, '24 (8P + 16E)', '32', '3.0 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.0 GHz (5.8 GHz Boost)\nMicroarchitecture: Raptor Lake\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(26, 'AMD Ryzen 9 7900X3D', 'AMD Ryzen 9 7900X3D', 499.00, 'productsdata-cpu-8-transparent.png', 'AMD', NULL, 120, 'AM5', NULL, '12 Cores', '24', '4.4 GHz', 10, 'active', 'Socket / Motherboard Support: AM5\nCore Count: 12 Cores / 24 Threads\nPerf Core Clock: 4.4 GHz (5.6 GHz Boost)\nMicroarchitecture: Zen 4 (3D V-Cache)\nTDP: 120W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(27, 'Intel Core i7-14700K', 'Intel Core i7-14700K', 389.99, 'productsdata-cpu-9-transparent.png', 'Intel', NULL, 125, 'LGA1700', NULL, '20 (8P + 12E)', '28', '3.4 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 20 (8P + 12E) / 28 Threads\nPerf Core Clock: 3.4 GHz (5.6 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(28, 'AMD Ryzen 9 7900X', 'AMD Ryzen 9 7900X', 389.00, 'productsdata-cpu-10-transparent.png', 'AMD', NULL, 170, 'AM5', NULL, '12 Cores', '24', '4.7 GHz', 10, 'active', 'Socket / Motherboard Support: AM5\nCore Count: 12 Cores / 24 Threads\nPerf Core Clock: 4.7 GHz (5.6 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 170W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(29, 'Intel Core i9-14900KF', 'Intel Core i9-14900KF', 529.99, 'productsdata-cpu-11-transparent.png', 'Intel', NULL, 125, 'LGA1700', NULL, '24 (8P + 16E)', '32', '3.2 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 24 (8P + 16E) / 32 Threads\nPerf Core Clock: 3.2 GHz (6.0 GHz Boost)\nMicroarchitecture: Raptor Lake Refresh\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: None (Requires Discrete GPU)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(30, 'AMD Ryzen 9 7900', 'AMD Ryzen 9 7900', 369.00, 'productsdata-cpu-12-transparent.png', 'AMD', NULL, 65, 'AM5', NULL, '12 Cores', '24', '3.7 GHz', 10, 'active', 'Socket / Motherboard Support: AM5\nCore Count: 12 Cores / 24 Threads\nPerf Core Clock: 3.7 GHz (5.4 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 65W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(31, 'Intel Core i7-13700K', 'Intel Core i7-13700K', 349.99, 'productsdata-cpu-13-transparent.png', 'Intel', NULL, 125, 'LGA1700', NULL, '16 (8P + 8E)', '24', '3.4 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 16 (8P + 8E) / 24 Threads\nPerf Core Clock: 3.4 GHz (5.4 GHz Boost)\nMicroarchitecture: Raptor Lake\nTDP: 125W (253W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(32, 'AMD Ryzen 7 7700X', 'AMD Ryzen 7 7700X', 289.00, 'productsdata-cpu-14-transparent.png', 'AMD', NULL, 105, 'AM5', NULL, '8 Cores', '16', '4.5 GHz', 10, 'active', 'Socket / Motherboard Support: AM5\nCore Count: 8 Cores / 16 Threads\nPerf Core Clock: 4.5 GHz (5.4 GHz Boost)\nMicroarchitecture: Zen 4\nTDP: 105W\nIntegrated Graphic Card: AMD Radeon Graphics (2 Cores)', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(33, 'Intel Core i9-12900KS', 'Intel Core i9-12900KS', 419.99, 'productsdata-cpu-15-transparent.png', 'Intel', NULL, 150, 'LGA1700', NULL, '16 (8P + 8E)', '24', '3.4 GHz', 10, 'active', 'Socket / Motherboard Support: LGA 1700\nCore Count: 16 (8P + 8E) / 24 Threads\nPerf Core Clock: 3.4 GHz (5.5 GHz Boost)\nMicroarchitecture: Alder Lake\nTDP: 150W (241W Turbo)\nIntegrated Graphic Card: Intel UHD Graphics 770', '2026-09-25 13:52:50', '2026-09-26 22:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `fans`
--

CREATE TABLE `fans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `rgb` varchar(20) DEFAULT NULL,
  `power_watts` int(10) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fans`
--

INSERT INTO `fans` (`id`, `name`, `price`, `size`, `rgb`, `power_watts`, `stock`, `status`, `brand`, `image_url`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeFlow 120 Fan', 14.99, '120 mm', 'No', 3, 30, 'inactive', 'ForgeFlow', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(2, 'ForgeFlow 140 RGB Fan', 19.99, '140 mm', 'Yes', 4, 20, 'inactive', 'ForgeFlow', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(3, 'ForgeFlow 120 RGB Fan', 17.99, '120 mm', 'Yes', 4, 28, 'inactive', 'ForgeFlow', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(4, 'ForgeFlow 140 Silent Fan', 18.99, '140 mm', 'No', 3, 24, 'inactive', 'ForgeFlow', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(5, 'ForgeFlow 120 Performance Fan', 21.99, '120 mm', 'No', 5, 18, 'inactive', 'ForgeFlow', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20'),
(6, 'ForgeFlow 140 ARGB Fan', 24.99, '140 mm', 'Yes', 5, 16, 'inactive', 'ForgeFlow', 'placeholder.png', NULL, NULL, '2026-09-25 13:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `gpu`
--

CREATE TABLE `gpu` (
  `id` int(11) NOT NULL,
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
  `recommended_psu_watts` int(10) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `ports` varchar(255) DEFAULT NULL,
  `directx` varchar(50) DEFAULT NULL,
  `dlss` varchar(50) DEFAULT NULL,
  `cooling` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gpu`
--

INSERT INTO `gpu` (`id`, `name`, `short_name`, `price`, `image_url`, `vram`, `length_mm`, `boost_clock`, `power_consumption`, `tdp`, `psu`, `recommended_psu_watts`, `stock`, `status`, `ports`, `directx`, `dlss`, `cooling`, `brand`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeVision 4070', 'FV 4070', 599.99, 'demo-gpu.svg', '12 GB', 300, NULL, NULL, 200, NULL, 650, 6, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(2, 'ForgeVision 7900 XT', 'FV 7900 XT', 699.99, 'demo-gpu.svg', '20 GB', 360, NULL, NULL, 300, NULL, 750, 3, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(3, 'ForgeVision 4060', 'FV 4060', 299.99, 'demo-gpu.svg', '8 GB', 245, NULL, NULL, 115, NULL, 550, 15, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(4, 'ForgeVision 4060 Ti', 'FV 4060 Ti', 399.99, 'demo-gpu.svg', '16 GB', 270, NULL, NULL, 165, NULL, 550, 9, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(5, 'ForgeVision 4070 Super', 'FV 4070S', 649.99, 'demo-gpu.svg', '12 GB', 310, NULL, NULL, 220, NULL, 650, 7, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(6, 'ForgeVision 4080', 'FV 4080', 999.99, 'demo-gpu.svg', '16 GB', 350, NULL, NULL, 320, NULL, 750, 4, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(7, 'ForgeVision 4090 Titan', 'FV 4090', 1599.99, 'demo-gpu.svg', '24 GB', 365, NULL, NULL, 450, NULL, 850, 3, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(8, 'ForgeVision 7600 XT', 'FV 7600 XT', 329.99, 'demo-gpu.svg', '16 GB', 280, NULL, NULL, 190, NULL, 600, 11, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(9, 'ForgeVision 7800 XT', 'FV 7800 XT', 519.99, 'demo-gpu.svg', '16 GB', 325, NULL, NULL, 263, NULL, 700, 8, 'inactive', NULL, NULL, NULL, NULL, 'ForgeVision', NULL, NULL, '2026-09-25 13:52:50'),
(14, 'NVIDIA GeForce RTX 5090 FE', 'NVIDIA GeForce RTX 5090 FE', 1999.00, 'productsdata-gpu-1.png', '32 GB GDDR7', NULL, '2400 MHz', '575W', 575, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'NVIDIA', 'GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2400 MHz\nTDP / Power: 575W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(15, 'ASUS ROG Strix RTX 5090 OC', 'ASUS ROG Strix RTX 5090 OC', 2299.99, 'productsdata-gpu-2-transparent.png', '32 GB GDDR7', NULL, '2550 MHz', '600W', 600, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'ASUS', 'GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2550 MHz\nTDP / Power: 600W', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(16, 'MSI GeForce RTX 5090 SUPRIM X', 'MSI GeForce RTX 5090 SUPRIM X', 2199.99, 'productsdata-gpu-3.png', '32 GB GDDR7', NULL, '2535 MHz', '600W', 600, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'MSI', 'GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2535 MHz\nTDP / Power: 600W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(17, 'GIGABYTE RTX 5090 AORUS Xtreme', 'GIGABYTE RTX 5090 AORUS Xtreme', 2149.99, 'productsdata-gpu-4.png', '32 GB GDDR7', NULL, '2520 MHz', '600W', 600, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'GIGABYTE', 'GPU Chipset / Architecture: NVIDIA Blackwell GB202\nVRAM Size & Type: 32 GB GDDR7\nMemory Bus Width: 512-bit\nBoost Core Clock: 2520 MHz\nTDP / Power: 600W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(18, 'NVIDIA GeForce RTX 5080 FE', 'NVIDIA GeForce RTX 5080 FE', 999.00, 'productsdata-gpu-5.png', '16 GB GDDR7', NULL, '2610 MHz', '400W', 400, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'NVIDIA', 'GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2610 MHz\nTDP / Power: 400W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(19, 'ASUS ROG Strix RTX 5080 OC', 'ASUS ROG Strix RTX 5080 OC', 1199.99, 'productsdata-gpu-6.png', '16 GB GDDR7', NULL, '2730 MHz', '430W', 430, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'ASUS', 'GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2730 MHz\nTDP / Power: 430W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(20, 'MSI GeForce RTX 5080 SUPRIM X', 'MSI GeForce RTX 5080 SUPRIM X', 1149.99, 'productsdata-gpu-7-transparent.png', '16 GB GDDR7', NULL, '2715 MHz', '425W', 425, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'MSI', 'GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2715 MHz\nTDP / Power: 425W', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(21, 'NVIDIA GeForce RTX 4090 FE', 'NVIDIA GeForce RTX 4090 FE', 1599.00, 'productsdata-gpu-8-transparent.png', '24 GB GDDR6X', NULL, '2520 MHz', '450W', 450, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'NVIDIA', 'GPU Chipset / Architecture: NVIDIA Ada Lovelace AD102\nVRAM Size & Type: 24 GB GDDR6X\nMemory Bus Width: 384-bit\nBoost Core Clock: 2520 MHz\nTDP / Power: 450W', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(22, 'ASUS ROG Strix RTX 4090 OC', 'ASUS ROG Strix RTX 4090 OC', 1999.99, 'productsdata-gpu-9.png', '24 GB GDDR6X', NULL, '2640 MHz', '500W', 500, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'ASUS', 'GPU Chipset / Architecture: NVIDIA Ada Lovelace AD102\nVRAM Size & Type: 24 GB GDDR6X\nMemory Bus Width: 384-bit\nBoost Core Clock: 2640 MHz\nTDP / Power: 500W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(23, 'MSI GeForce RTX 4090 SUPRIM Liquid X', 'MSI GeForce RTX 4090 SUPRIM Liquid X', 1749.99, 'productsdata-gpu-10-transparent.png', '24 GB GDDR6X', NULL, '2640 MHz', '480W', 480, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'MSI', 'GPU Chipset / Architecture: NVIDIA Ada Lovelace AD102\nVRAM Size & Type: 24 GB GDDR6X\nMemory Bus Width: 384-bit\nBoost Core Clock: 2640 MHz\nTDP / Power: 480W', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'AMD Radeon RX 7900 XTX', 'AMD Radeon RX 7900 XTX', 929.99, 'productsdata-gpu-11.png', '24 GB GDDR6', NULL, '2500 MHz', '355W', 355, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'AMD', 'GPU Chipset / Architecture: AMD RDNA 3 Navi 31\nVRAM Size & Type: 24 GB GDDR6\nMemory Bus Width: 384-bit\nBoost Core Clock: 2500 MHz\nTDP / Power: 355W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(25, 'Sapphire NITRO+ RX 7900 XTX Vapor-X', 'Sapphire NITRO+ RX 7900 XTX Vapor-X', 1049.99, 'productsdata-gpu-12-transparent.png', '24 GB GDDR6', NULL, '2680 MHz', '420W', 420, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'Sapphire', 'GPU Chipset / Architecture: AMD RDNA 3 Navi 31\nVRAM Size & Type: 24 GB GDDR6\nMemory Bus Width: 384-bit\nBoost Core Clock: 2680 MHz\nTDP / Power: 420W', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(26, 'NVIDIA GeForce RTX 4080 Super', 'NVIDIA GeForce RTX 4080 Super', 999.00, 'productsdata-gpu-13.png', '16 GB GDDR6X', NULL, '2550 MHz', '320W', 320, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'NVIDIA', 'GPU Chipset / Architecture: NVIDIA Ada Lovelace AD103\nVRAM Size & Type: 16 GB GDDR6X\nMemory Bus Width: 256-bit\nBoost Core Clock: 2550 MHz\nTDP / Power: 320W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(27, 'ASUS TUF Gaming RTX 5080 OC', 'ASUS TUF Gaming RTX 5080 OC', 1099.99, 'productsdata-gpu-14.png', '16 GB GDDR7', NULL, '2680 MHz', '420W', 420, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'ASUS', 'GPU Chipset / Architecture: NVIDIA Blackwell GB203\nVRAM Size & Type: 16 GB GDDR7\nMemory Bus Width: 256-bit\nBoost Core Clock: 2680 MHz\nTDP / Power: 420W', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(28, 'PowerColor RED DEVIL RX 7900 XTX', 'PowerColor RED DEVIL RX 7900 XTX', 979.99, 'productsdata-gpu-15.png', '24 GB GDDR6', NULL, '2565 MHz', '395W', 395, NULL, NULL, 10, 'active', NULL, NULL, NULL, NULL, 'PowerColor', 'GPU Chipset / Architecture: AMD RDNA 3 Navi 31\nVRAM Size & Type: 24 GB GDDR6\nMemory Bus Width: 384-bit\nBoost Core Clock: 2565 MHz\nTDP / Power: 395W', '2026-09-25 13:52:50', '2026-09-25 14:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `mb`
--

CREATE TABLE `mb` (
  `id` int(11) NOT NULL,
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
  `sata_ports` int(10) UNSIGNED DEFAULT NULL,
  `nvme_slots` int(10) UNSIGNED DEFAULT NULL,
  `power_watts` int(10) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mb`
--

INSERT INTO `mb` (`id`, `name`, `price`, `socket`, `memory_type`, `chipset`, `size`, `brand`, `image_url`, `wifi`, `ram_slots`, `sata_ports`, `nvme_slots`, `power_watts`, `stock`, `status`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeBoard B650 ATX', 159.99, 'AM5', 'DDR5', 'B650', 'ATX', 'ForgeBoard', 'placeholder.png', NULL, 4, 4, 2, 50, 8, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(2, 'ForgeBoard Z790 DDR5', 229.99, 'LGA1700', 'DDR5', 'Z790', 'ATX', 'ForgeBoard', 'placeholder.png', NULL, 4, 6, 3, 55, 5, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(3, 'ForgeBoard B550 DDR4', 119.99, 'AM4', 'DDR4', 'B550', 'Micro-ATX', 'ForgeBoard', 'placeholder.png', NULL, 4, 4, 1, 45, 9, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(4, 'ForgeBoard B650M WiFi', 139.99, 'AM5', 'DDR5', 'B650', 'Micro-ATX', 'ForgeBoard', 'placeholder.png', NULL, 4, 4, 2, 45, 12, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(5, 'ForgeBoard X670 Elite', 289.99, 'AM5', 'DDR5', 'X670', 'ATX', 'ForgeBoard', 'placeholder.png', NULL, 4, 6, 4, 60, 4, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(6, 'ForgeBoard B760M DDR5', 129.99, 'LGA1700', 'DDR5', 'B760', 'Micro-ATX', 'ForgeBoard', 'placeholder.png', NULL, 4, 4, 2, 45, 14, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(7, 'ForgeBoard H610 DDR5', 89.99, 'LGA1700', 'DDR5', 'H610', 'Micro-ATX', 'ForgeBoard', 'placeholder.png', NULL, 2, 4, 1, 40, 18, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(8, 'ForgeBoard B550 ATX', 139.99, 'AM4', 'DDR4', 'B550', 'ATX', 'ForgeBoard', 'placeholder.png', NULL, 4, 6, 2, 45, 11, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(9, 'ForgeBoard A520M DDR4', 79.99, 'AM4', 'DDR4', 'A520', 'Micro-ATX', 'ForgeBoard', 'placeholder.png', NULL, 2, 4, 1, 35, 16, 'inactive', NULL, NULL, '2026-09-25 13:54:20'),
(14, 'ASUS ROG Maximus Z790 Extreme', 999.99, 'LGA1700', 'DDR5', 'Z790', 'E-ATX', 'ASUS', 'productsdata-mb-1.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / OLED Display', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(15, 'MSI MEG Z790 GODLIKE MAX', 1199.99, 'LGA1700', 'DDR5', 'Z790', 'E-ATX', 'MSI', 'productsdata-mb-2.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Dark Mirror Black', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(16, 'GIGABYTE Z790 AORUS Xtreme X', 999.00, 'LGA1700', 'DDR5', 'Z790', 'E-ATX', 'GIGABYTE', 'productsdata-mb-3-transparent.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Titanium', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(17, 'ASRock X670E Taichi Carrara', 529.99, 'AM5', 'DDR5', 'X670E', 'E-ATX', 'ASRock', 'productsdata-mb-4.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Marble White', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(18, 'ASUS ROG Crosshair X670E Extreme', 999.99, 'AM5', 'DDR5', 'X670E', 'E-ATX', 'ASUS', 'productsdata-mb-5.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / AniMe Matrix', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(19, 'MSI MEG X670E ACE', 699.99, 'AM5', 'DDR5', 'X670E', 'E-ATX', 'MSI', 'productsdata-mb-6.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Gold Accents', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(20, 'ASUS ROG Strix Z790-E Gaming WiFi II', 499.99, 'LGA1700', 'DDR5', 'Z790', 'ATX', 'ASUS', 'productsdata-mb-7.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / RGB', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(21, 'GIGABYTE X670E AORUS Xtreme', 699.00, 'AM5', 'DDR5', 'X670E', 'E-ATX', 'GIGABYTE', 'productsdata-mb-8-transparent.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: AM5\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Armor Metallic', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(22, 'ASRock Z790 Taichi Lite', 379.99, 'LGA1700', 'DDR5', 'Z790', 'E-ATX', 'ASRock', 'productsdata-mb-9.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Bronze / Black', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(23, 'ASUS ROG Hero Z790 Dark Hero', 649.99, 'LGA1700', 'DDR5', 'Z790', 'ATX', 'ASUS', 'productsdata-mb-10-transparent.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Matte Black', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'MSI MPG Z790 Carbon WiFi', 449.99, 'LGA1700', 'DDR5', 'Z790', 'ATX', 'MSI', 'productsdata-mb-11.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Carbon Black', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(25, 'GIGABYTE Z790 AORUS Master X', 549.99, 'LGA1700', 'DDR5', 'Z790', 'E-ATX', 'GIGABYTE', 'productsdata-mb-12.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: E-ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Dark Grey / Silver', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(26, 'ASUS ROG Crosshair X670E Hero', 649.99, 'AM5', 'DDR5', 'X670E', 'ATX', 'ASUS', 'productsdata-mb-13.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: AM5\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Polished Black', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(27, 'NZXT N7 Z790', 299.99, 'LGA1700', 'DDR5', 'Z790', 'ATX', 'NZXT', 'productsdata-mb-14-transparent.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: LGA 1700\nForm Factor: ATX\nMemory Max: 128 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Matte White', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(28, 'ASRock X670E PG Lightning', 259.99, 'AM5', 'DDR5', 'X670E', 'ATX', 'ASRock', 'productsdata-mb-15.png', NULL, 4, NULL, NULL, NULL, 10, 'active', 'Socket / CPU: AM5\nForm Factor: ATX\nMemory Max: 192 GB\nMemory Slots: 4\nDDR Support: DDR5\nColor: Black / Cyan', '2026-09-25 13:52:50', '2026-09-25 14:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `memory`
--

CREATE TABLE `memory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `capacity` varchar(20) DEFAULT NULL,
  `speed` varchar(20) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `latency` varchar(20) DEFAULT NULL,
  `modules` varchar(50) DEFAULT NULL,
  `module_count` int(10) UNSIGNED DEFAULT NULL,
  `power_watts` int(10) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memory`
--

INSERT INTO `memory` (`id`, `name`, `price`, `type`, `capacity`, `speed`, `brand`, `image_url`, `latency`, `modules`, `module_count`, `power_watts`, `stock`, `status`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeMemory 32 GB DDR5', 89.99, 'DDR5', '32 GB', '6000 MT/s', 'ForgeMemory', 'demo-memory.svg', 'CL30', '2 x 16 GB', 2, 10, 20, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(2, 'ForgeMemory 16 GB DDR4', 39.99, 'DDR4', '16 GB', '3200 MT/s', 'ForgeMemory', 'demo-memory.svg', 'CL16', '2 x 8 GB', 2, 10, 24, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(3, 'ForgeMemory 16 GB DDR5', 54.99, 'DDR5', '16 GB', '5600 MT/s', 'ForgeMemory', 'demo-memory.svg', 'CL36', '2 x 8 GB', 2, 8, 25, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(4, 'ForgeMemory 64 GB DDR5', 169.99, 'DDR5', '64 GB', '6000 MT/s', 'ForgeMemory', 'demo-memory.svg', 'CL32', '2 x 32 GB', 2, 12, 10, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(5, 'ForgeMemory 32 GB DDR4', 69.99, 'DDR4', '32 GB', '3600 MT/s', 'ForgeMemory', 'demo-memory.svg', 'CL18', '2 x 16 GB', 2, 10, 17, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(6, 'ForgeMemory 64 GB DDR4', 129.99, 'DDR4', '64 GB', '3200 MT/s', 'ForgeMemory', 'demo-memory.svg', 'CL16', '2 x 32 GB', 2, 12, 7, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(7, 'ForgeMemory 32 GB DDR5 Performance', 119.99, 'DDR5', '32 GB', '7200 MT/s', 'ForgeMemory', 'demo-memory.svg', 'CL34', '2 x 16 GB', 2, 12, 9, 'inactive', NULL, NULL, '2026-09-25 13:52:50'),
(12, 'G.SKILL Trident Z5 RGB 64GB (2x32GB)', 349.99, 'DDR5', '64 GB', 'DDR5-8000 MHz', 'G.SKILL', 'productsdata-memory-1-transparent.png', 'CL38-48-48-128', '2 x 32 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-8000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL38-48-48-128\nTested Voltage: 1.45V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(13, 'Corsair Dominator Titanium RGB 64GB (2x32GB)', 319.99, 'DDR5', '64 GB', 'DDR5-7200 MHz', 'Corsair', 'productsdata-memory-2.avif', 'CL34-44-44-96', '2 x 32 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-7200 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL34-44-44-96\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(14, 'TeamGroup T-Force Delta RGB 48GB (2x24GB)', 249.99, 'DDR5', '48 GB', 'DDR5-8200 MHz', 'TeamGroup', 'productsdata-memory-3-transparent.png', 'CL38-49-49-128', '2 x 24 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-8200 MHz\nTotal Capacity: 48 GB (2x24GB)\nTested Latency (CAS): CL38-49-49-128\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(15, 'G.SKILL Trident Z5 Neo RGB (AMD EXPO) 64GB', 219.99, 'DDR5', '64 GB', 'DDR5-6000 MHz', 'G.SKILL', 'productsdata-memory-4-transparent.png', 'CL30-40-40-96', '2 x 32 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL30-40-40-96\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(16, 'Corsair Vengeance RGB 96GB (2x48GB)', 379.99, 'DDR5', '96 GB', 'DDR5-6400 MHz', 'Corsair', 'productsdata-memory-5.avif', 'CL32-40-40-84', '2 x 48 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-6400 MHz\nTotal Capacity: 96 GB (2x48GB)\nTested Latency (CAS): CL32-40-40-84\nTested Voltage: 1.35V', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(17, 'G.SKILL Trident Z5 RGB 32GB (2x16GB)', 179.99, 'DDR5', '32 GB', 'DDR5-7600 MHz', 'G.SKILL', 'productsdata-memory-6-transparent.png', 'CL36-46-46-121', '2 x 16 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-7600 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL36-46-46-121\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(18, 'Kingston Fury Renegade RGB 64GB (2x32GB)', 299.99, 'DDR5', '64 GB', 'DDR5-7200 MHz', 'Kingston', 'productsdata-memory-7-transparent.png', 'CL38-44-44-105', '2 x 32 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-7200 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL38-44-44-105\nTested Voltage: 1.45V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(19, 'Corsair Dominator Platinum RGB 32GB (2x16GB)', 169.99, 'DDR5', '32 GB', 'DDR5-6000 MHz', 'Corsair', 'productsdata-memory-8-transparent.png', 'CL30-36-36-76', '2 x 16 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL30-36-36-76\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(20, 'TeamGroup T-Force XTREEM ARGB 48GB (2x24GB)', 269.99, 'DDR5', '48 GB', 'DDR5-8000 MHz', 'TeamGroup', 'productsdata-memory-9-transparent.png', 'CL38-48-48-128', '2 x 24 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-8000 MHz\nTotal Capacity: 48 GB (2x24GB)\nTested Latency (CAS): CL38-48-48-128\nTested Voltage: 1.45V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(21, 'G.SKILL Ripjaws S5 64GB (2x32GB)', 209.99, 'DDR5', '64 GB', 'DDR5-6400 MHz', 'G.SKILL', 'productsdata-memory-10-transparent.png', 'CL32-39-39-102', '2 x 32 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-6400 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL32-39-39-102\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(22, 'Corsair Vengeance 64GB (2x32GB) AMD EXPO', 214.99, 'DDR5', '64 GB', 'DDR5-6000 MHz', 'Corsair', 'productsdata-memory-11.avif', 'CL30-36-36-76', '2 x 32 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL30-36-36-76\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(23, 'Patriot Viper Xtreme 5 RGB 32GB (2x16GB)', 189.99, 'DDR5', '32 GB', 'DDR5-8000 MHz', 'Patriot', 'productsdata-memory-12-transparent.png', 'CL38-48-48-128', '2 x 16 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-8000 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL38-48-48-128\nTested Voltage: 1.45V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'ADATA XPG Lancer RGB 64GB (2x32GB)', 199.99, 'DDR5', '64 GB', 'DDR5-6000 MHz', 'ADATA', 'productsdata-memory-13-transparent.png', 'CL30-40-40-96', '2 x 32 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 64 GB (2x32GB)\nTested Latency (CAS): CL30-40-40-96\nTested Voltage: 1.35V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(25, 'Thermaltake TOUGHRAM XG RGB D5 32GB', 159.99, 'DDR5', '32 GB', 'DDR5-7200 MHz', 'Thermaltake', 'productsdata-memory-14-transparent.png', 'CL36-46-46-115', '2 x 16 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-7200 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL36-46-46-115\nTested Voltage: 1.40V', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(26, 'Crucial Pro Overclocking 32GB (2x16GB)', 104.99, 'DDR5', '32 GB', 'DDR5-6000 MHz', 'Crucial', 'productsdata-memory-15-transparent.png', 'CL36-38-38-80', '2 x 16 GB', 2, NULL, 10, 'active', 'Memory Type: DDR5\nMemory Speed: DDR5-6000 MHz\nTotal Capacity: 32 GB (2x16GB)\nTested Latency (CAS): CL36-38-38-80\nTested Voltage: 1.35V', '2026-09-25 13:52:50', '2026-09-26 22:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `monitor`
--

CREATE TABLE `monitor` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `screen_size` varchar(20) DEFAULT NULL,
  `panel_type` varchar(100) DEFAULT NULL,
  `resolution` varchar(100) DEFAULT NULL,
  `refresh_rate` varchar(50) DEFAULT NULL,
  `response_time_hdr` varchar(160) DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitor`
--

INSERT INTO `monitor` (`id`, `name`, `price`, `brand`, `image_url`, `screen_size`, `panel_type`, `resolution`, `refresh_rate`, `response_time_hdr`, `stock`, `description`, `created_at`, `updated_at`, `status`) VALUES
(1, 'ASUS ROG Swift OLED PG32UCDM', 1299.99, 'ASUS', 'productsdata-monitor-1-transparent.png', '32\"', 'QD-OLED', '3840 x 2160 (4K UHD)', '240 Hz', '0.03 ms GTG | HDR TRUE BLACK 400', 10, 'Screen Size: 32\"\nPanel Type: QD-OLED\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR TRUE BLACK 400', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(2, 'Dell Alienware AW3225QF', 1199.99, 'Dell', 'productsdata-monitor-2.avif', '32\"', 'Curved QD-OLED (1700R)', '3840 x 2160 (4K UHD)', '240 Hz', '0.03 ms GTG | Dolby Vision / HDR400', 10, 'Screen Size: 32\"\nPanel Type: Curved QD-OLED (1700R)\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | Dolby Vision / HDR400', '2026-09-25 13:52:50', '2026-09-25 14:13:08', 'active'),
(3, 'LG UltraGear 32GS95UE-B', 1399.99, 'LG', 'productsdata-monitor-3-transparent.png', '32\"', 'WOLED', '4K 240Hz / FHD 480Hz', '240 Hz / 480 Hz Dual', '0.03 ms GTG | DisplayHDR True Black 400', 10, 'Screen Size: 32\"\nPanel Type: WOLED\nResolution: 4K 240Hz / FHD 480Hz\nRefresh Rate: 240 Hz / 480 Hz Dual\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(4, 'MSI MPG 321URX QD-OLED', 949.99, 'MSI', 'productsdata-monitor-4-transparent.png', '32\"', 'QD-OLED', '3840 x 2160 (4K UHD)', '240 Hz', '0.03 ms GTG | ClearMR 13000 / HDR400', 10, 'Screen Size: 32\"\nPanel Type: QD-OLED\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | ClearMR 13000 / HDR400', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(5, 'Samsung Odyssey OLED G9 (G95SC)', 1799.99, 'Samsung', 'productsdata-monitor-5-transparent.png', '49\"', 'Curved QD-OLED (1800R)', '5120 x 1440 (Dual QHD)', '240 Hz', '0.03 ms GTG | DisplayHDR True Black 400', 10, 'Screen Size: 49\"\nPanel Type: Curved QD-OLED (1800R)\nResolution: 5120 x 1440 (Dual QHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(6, 'Samsung Odyssey Neo G9 (G95NC)', 2499.99, 'Samsung', 'productsdata-monitor-6-transparent.png', '57\"', 'Curved Mini-LED (1000R)', '7680 x 2160 (Dual UHD)', '240 Hz', '1 ms GTG | DisplayHDR 1000', 10, 'Screen Size: 57\"\nPanel Type: Curved Mini-LED (1000R)\nResolution: 7680 x 2160 (Dual UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 1 ms GTG | DisplayHDR 1000', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(7, 'ASUS ROG Swift Pro PG248QP', 899.99, 'ASUS', 'productsdata-monitor-7-transparent.png', '24.1\"', 'E-TN', '1920 x 1080 (FHD)', '540 Hz', '0.2 ms GTG | DisplayHDR 400', 10, 'Screen Size: 24.1\"\nPanel Type: E-TN\nResolution: 1920 x 1080 (FHD)\nRefresh Rate: 540 Hz\nResponse Time / HDR: 0.2 ms GTG | DisplayHDR 400', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(8, 'Dell Alienware AW2725DF', 899.99, 'Dell', 'productsdata-monitor-8-transparent.png', '27\"', 'QD-OLED', '2560 x 1440 (QHD)', '360 Hz', '0.03 ms GTG | DisplayHDR True Black 400', 10, 'Screen Size: 27\"\nPanel Type: QD-OLED\nResolution: 2560 x 1440 (QHD)\nRefresh Rate: 360 Hz\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(9, 'Gigabyte AORUS FO32U2P', 1299.99, 'GIGABYTE', 'productsdata-monitor-9-transparent.png', '32\"', 'QD-OLED (DP 2.1 Native)', '3840 x 2160 (4K UHD)', '240 Hz', '0.03 ms GTG | DisplayHDR True Black 400', 10, 'Screen Size: 32\"\nPanel Type: QD-OLED (DP 2.1 Native)\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | DisplayHDR True Black 400', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(10, 'BenQ ZOWIE XL2566K', 599.99, 'BenQ', 'productsdata-monitor-10.avif', '24.5\"', 'TN (DyAc+)', '1920 x 1080 (FHD)', '360 Hz', '0.5 ms GTG | N/A (Esports Tuned)', 10, 'Screen Size: 24.5\"\nPanel Type: TN (DyAc+)\nResolution: 1920 x 1080 (FHD)\nRefresh Rate: 360 Hz\nResponse Time / HDR: 0.5 ms GTG | N/A (Esports Tuned)', '2026-09-25 13:52:50', '2026-09-25 14:13:08', 'active'),
(11, 'LG UltraGear 27GR95QE-B', 799.99, 'LG', 'productsdata-monitor-11-transparent.png', '27\"', 'OLED', '2560 x 1440 (QHD)', '240 Hz', '0.03 ms GTG | HDR10', 10, 'Screen Size: 27\"\nPanel Type: OLED\nResolution: 2560 x 1440 (QHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR10', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(12, 'Corsair XENEON FLEX 45WQHD240', 1699.99, 'Corsair', 'productsdata-monitor-12-transparent.png', '45\"', 'Bendable OLED (Flat to 800R)', '3440 x 1440 (UWQHD)', '240 Hz', '0.03 ms GTG | HDR10', 10, 'Screen Size: 45\"\nPanel Type: Bendable OLED (Flat to 800R)\nResolution: 3440 x 1440 (UWQHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR10', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(13, 'ASUS ROG Swift PG27AQDM', 899.99, 'ASUS', 'productsdata-monitor-13-transparent.png', '27\"', 'OLED', '2560 x 1440 (QHD)', '240 Hz', '0.03 ms GTG | HDR10', 10, 'Screen Size: 27\"\nPanel Type: OLED\nResolution: 2560 x 1440 (QHD)\nRefresh Rate: 240 Hz\nResponse Time / HDR: 0.03 ms GTG | HDR10', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(14, 'Acer Predator X32 FP', 1199.99, 'Acer', 'productsdata-monitor-14-transparent.png', '32\"', 'Mini-LED IPS', '3840 x 2160 (4K UHD)', '160 Hz', '0.7 ms GTG | DisplayHDR 1000', 10, 'Screen Size: 32\"\nPanel Type: Mini-LED IPS\nResolution: 3840 x 2160 (4K UHD)\nRefresh Rate: 160 Hz\nResponse Time / HDR: 0.7 ms GTG | DisplayHDR 1000', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active'),
(15, 'Apple Studio Display - Nano-Texture', 1899.99, 'Apple', 'productsdata-monitor-15-transparent.png', '27\"', 'IPS LCD', '5120 x 2880 (5K Retina)', '60 Hz', '5 ms GTG | 600 nits Brightness', 10, 'Screen Size: 27\"\nPanel Type: IPS LCD\nResolution: 5120 x 2880 (5K Retina)\nRefresh Rate: 60 Hz\nResponse Time / HDR: 5 ms GTG | 600 nits Brightness', '2026-09-25 13:52:50', '2026-09-26 22:29:09', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(40) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(160) NOT NULL,
  `customer_email` varchar(160) NOT NULL,
  `shipping_address` varchar(262) NOT NULL,
  `shipping_city` varchar(160) NOT NULL,
  `shipping_postal_code` varchar(30) NOT NULL,
  `shipping_region` varchar(100) NOT NULL DEFAULT '',
  `shipping_country` varchar(100) NOT NULL DEFAULT '',
  `shipping_phone` varchar(30) NOT NULL DEFAULT '',
  `subtotal` decimal(12,2) NOT NULL,
  `shipping_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `payment_method` varchar(30) NOT NULL DEFAULT 'demo',
  `payment_status` enum('unpaid','paid','refunded','demo') NOT NULL DEFAULT 'demo',
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `stock_deducted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `customer_email`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `shipping_region`, `shipping_country`, `shipping_phone`, `subtotal`, `shipping_total`, `total`, `currency`, `payment_method`, `payment_status`, `status`, `stock_deducted_at`, `created_at`, `updated_at`) VALUES
(8, 'SAMPLE-DASH-202604-01', 30, 'Sample Customer 1', 'dashboard.sample.1@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 1299.98, 0.00, 1299.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-03-31 17:30:00', '2026-03-31 17:30:00'),
(9, 'SAMPLE-DASH-202604-02', 31, 'Sample Customer 2', 'dashboard.sample.2@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 669.96, 0.00, 669.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-04-10 17:30:00', '2026-04-10 17:30:00'),
(10, 'SAMPLE-DASH-202604-03', 32, 'Sample Customer 3', 'dashboard.sample.3@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 254.96, 0.00, 254.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-04-20 17:30:00', '2026-04-20 17:30:00'),
(11, 'SAMPLE-DASH-202605-01', 33, 'Sample Customer 4', 'dashboard.sample.4@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 159.98, 0.00, 159.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-04-30 17:30:00', '2026-04-30 17:30:00'),
(12, 'SAMPLE-DASH-202605-02', 34, 'Sample Customer 5', 'dashboard.sample.5@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 349.96, 0.00, 349.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-05-07 17:30:00', '2026-05-07 17:30:00'),
(13, 'SAMPLE-DASH-202605-03', 35, 'Sample Customer 6', 'dashboard.sample.6@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 309.96, 0.00, 309.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-05-15 17:30:00', '2026-05-15 17:30:00'),
(14, 'SAMPLE-DASH-202605-04', 36, 'Sample Customer 7', 'dashboard.sample.7@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 189.97, 0.00, 189.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-05-23 17:30:00', '2026-05-23 17:30:00'),
(15, 'SAMPLE-DASH-202606-01', 37, 'Sample Customer 8', 'dashboard.sample.8@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 34.98, 0.00, 34.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-05-31 17:30:00', '2026-05-31 17:30:00'),
(16, 'SAMPLE-DASH-202606-02', 30, 'Sample Customer 1', 'dashboard.sample.1@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 899.96, 0.00, 899.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-06-06 17:30:00', '2026-06-06 17:30:00'),
(17, 'SAMPLE-DASH-202606-03', 31, 'Sample Customer 2', 'dashboard.sample.2@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 1759.96, 0.00, 1759.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-06-12 17:30:00', '2026-06-12 17:30:00'),
(18, 'SAMPLE-DASH-202606-04', 32, 'Sample Customer 3', 'dashboard.sample.3@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 549.97, 0.00, 549.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-06-18 17:30:00', '2026-06-18 17:30:00'),
(19, 'SAMPLE-DASH-202606-05', 33, 'Sample Customer 4', 'dashboard.sample.4@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 184.97, 0.00, 184.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-06-24 17:30:00', '2026-06-24 17:30:00'),
(20, 'SAMPLE-DASH-202607-01', 34, 'Sample Customer 5', 'dashboard.sample.5@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 159.98, 0.00, 159.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-06-30 17:30:00', '2026-06-30 17:30:00'),
(21, 'SAMPLE-DASH-202607-02', 35, 'Sample Customer 6', 'dashboard.sample.6@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 349.96, 0.00, 349.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-07-05 17:30:00', '2026-07-05 17:30:00'),
(22, 'SAMPLE-DASH-202607-03', 36, 'Sample Customer 7', 'dashboard.sample.7@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 309.96, 0.00, 309.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-07-10 17:30:00', '2026-07-10 17:30:00'),
(23, 'SAMPLE-DASH-202607-04', 37, 'Sample Customer 8', 'dashboard.sample.8@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 189.97, 0.00, 189.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-07-15 17:30:00', '2026-07-15 17:30:00'),
(24, 'SAMPLE-DASH-202607-05', 30, 'Sample Customer 1', 'dashboard.sample.1@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 52.97, 0.00, 52.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-07-20 17:30:00', '2026-07-20 17:30:00'),
(25, 'SAMPLE-DASH-202607-06', 31, 'Sample Customer 2', 'dashboard.sample.2@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 1499.95, 0.00, 1499.95, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-07-25 17:30:00', '2026-07-25 17:30:00'),
(26, 'SAMPLE-DASH-202608-01', 32, 'Sample Customer 3', 'dashboard.sample.3@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 1299.98, 0.00, 1299.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-07-31 17:30:00', '2026-07-31 17:30:00'),
(27, 'SAMPLE-DASH-202608-02', 33, 'Sample Customer 4', 'dashboard.sample.4@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 669.96, 0.00, 669.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-08-04 17:30:00', '2026-08-04 17:30:00'),
(28, 'SAMPLE-DASH-202608-03', 34, 'Sample Customer 5', 'dashboard.sample.5@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 254.96, 0.00, 254.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-08-08 17:30:00', '2026-08-08 17:30:00'),
(29, 'SAMPLE-DASH-202608-04', 35, 'Sample Customer 6', 'dashboard.sample.6@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 229.97, 0.00, 229.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-08-13 17:30:00', '2026-08-13 17:30:00'),
(30, 'SAMPLE-DASH-202608-05', 36, 'Sample Customer 7', 'dashboard.sample.7@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 269.97, 0.00, 269.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-08-17 17:30:00', '2026-08-17 17:30:00'),
(31, 'SAMPLE-DASH-202608-06', 37, 'Sample Customer 8', 'dashboard.sample.8@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 399.95, 0.00, 399.95, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-08-22 17:30:00', '2026-08-22 17:30:00'),
(32, 'SAMPLE-DASH-202608-07', 30, 'Sample Customer 1', 'dashboard.sample.1@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 149.98, 0.00, 149.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-08-26 17:30:00', '2026-08-26 17:30:00'),
(33, 'SAMPLE-DASH-202609-01', 31, 'Sample Customer 2', 'dashboard.sample.2@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 34.98, 0.00, 34.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-08-31 17:30:00', '2026-08-31 17:30:00'),
(34, 'SAMPLE-DASH-202609-02', 32, 'Sample Customer 3', 'dashboard.sample.3@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 899.96, 0.00, 899.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-09-01 17:30:00', '2026-09-01 17:30:00'),
(35, 'SAMPLE-DASH-202609-03', 33, 'Sample Customer 4', 'dashboard.sample.4@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 1759.96, 0.00, 1759.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-09-03 17:30:00', '2026-09-03 17:30:00'),
(36, 'SAMPLE-DASH-202609-04', 34, 'Sample Customer 5', 'dashboard.sample.5@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 549.97, 0.00, 549.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-09-04 17:30:00', '2026-09-04 17:30:00'),
(37, 'SAMPLE-DASH-202609-05', 35, 'Sample Customer 6', 'dashboard.sample.6@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 184.97, 0.00, 184.97, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-09-06 17:30:00', '2026-09-06 17:30:00'),
(38, 'SAMPLE-DASH-202609-06', 36, 'Sample Customer 7', 'dashboard.sample.7@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 349.95, 0.00, 349.95, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-09-07 17:30:00', '2026-09-07 17:30:00'),
(39, 'SAMPLE-DASH-202609-07', 37, 'Sample Customer 8', 'dashboard.sample.8@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 209.98, 0.00, 209.98, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-09-09 17:30:00', '2026-09-09 17:30:00'),
(40, 'SAMPLE-DASH-202609-08', 30, 'Sample Customer 1', 'dashboard.sample.1@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 359.96, 0.00, 359.96, 'USD', 'sample_seed', 'paid', 'completed', NULL, '2026-09-11 17:30:00', '2026-09-11 17:30:00'),
(41, 'SAMPLE-DASH-202609-09', 31, 'Sample Customer 2', 'dashboard.sample.2@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 189.96, 0.00, 189.96, 'USD', 'sample_seed', 'unpaid', 'pending', NULL, '2026-09-12 17:30:00', '2026-09-12 17:30:00'),
(42, 'SAMPLE-DASH-202609-10', 32, 'Sample Customer 3', 'dashboard.sample.3@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 49.97, 0.00, 49.97, 'USD', 'sample_seed', 'unpaid', 'processing', NULL, '2026-09-14 17:30:00', '2026-09-14 17:30:00'),
(43, 'SAMPLE-DASH-202609-11', 33, 'Sample Customer 4', 'dashboard.sample.4@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 679.97, 0.00, 679.97, 'USD', 'sample_seed', 'unpaid', 'cancelled', NULL, '2026-09-15 17:30:00', '2026-09-15 17:30:00'),
(44, 'SAMPLE-DASH-202609-12', 34, 'Sample Customer 5', 'dashboard.sample.5@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 2359.95, 0.00, 2359.95, 'USD', 'sample_seed', 'unpaid', 'pending', NULL, '2026-09-17 17:30:00', '2026-09-17 17:30:00'),
(45, 'SAMPLE-DASH-202609-13', 35, 'Sample Customer 6', 'dashboard.sample.6@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 389.98, 0.00, 389.98, 'USD', 'sample_seed', 'unpaid', 'processing', NULL, '2026-09-18 17:30:00', '2026-09-18 17:30:00'),
(46, 'SAMPLE-DASH-202609-14', 36, 'Sample Customer 7', 'dashboard.sample.7@example.invalid', 'Sample address (fictional)', 'Sample City', '00000', '', '', '', 274.96, 0.00, 274.96, 'USD', 'sample_seed', 'unpaid', 'cancelled', NULL, '2026-09-20 17:30:00', '2026-09-20 17:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `category` enum('cpu','gpu','mb','memory','storage','psu','case_box','cooling','fans','monitor') NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL
) ;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `category`, `product_id`, `product_name`, `quantity`, `unit_price`, `line_total`) VALUES
(8, 8, 'gpu', 1, 'ForgeVision 4070', 1, 599.99, 599.99),
(9, 8, 'gpu', 2, 'ForgeVision 7900 XT', 1, 699.99, 699.99),
(10, 9, 'mb', 1, 'ForgeBoard B650 ATX', 2, 159.99, 319.98),
(11, 9, 'mb', 2, 'ForgeBoard Z790 DDR5', 1, 229.99, 229.99),
(12, 9, 'mb', 3, 'ForgeBoard B550 DDR4', 1, 119.99, 119.99),
(13, 10, 'memory', 1, 'ForgeMemory 32 GB DDR5', 1, 89.99, 89.99),
(14, 10, 'memory', 2, 'ForgeMemory 16 GB DDR4', 1, 39.99, 39.99),
(15, 10, 'memory', 3, 'ForgeMemory 16 GB DDR5', 1, 54.99, 54.99),
(16, 10, 'storage', 1, 'ForgeDrive 1 TB NVMe', 1, 69.99, 69.99),
(17, 11, 'storage', 1, 'ForgeDrive 1 TB NVMe', 1, 69.99, 69.99),
(18, 11, 'storage', 2, 'ForgeDrive 2 TB SATA SSD', 1, 89.99, 89.99),
(19, 12, 'psu', 1, 'ForgePower 650 W Bronze', 2, 79.99, 159.98),
(20, 12, 'psu', 2, 'ForgePower 850 W Gold', 1, 129.99, 129.99),
(21, 12, 'psu', 3, 'ForgePower 550 W Bronze', 1, 59.99, 59.99),
(22, 13, 'case_box', 1, 'ForgeCase Air ATX', 1, 89.99, 89.99),
(23, 13, 'case_box', 2, 'ForgeCase Compact', 1, 69.99, 69.99),
(24, 13, 'case_box', 3, 'ForgeCase Mesh ATX', 1, 109.99, 109.99),
(25, 13, 'cooling', 1, 'ForgeCool Tower 120', 1, 39.99, 39.99),
(26, 14, 'cooling', 1, 'ForgeCool Tower 120', 2, 39.99, 79.98),
(27, 14, 'cooling', 2, 'ForgeCool Liquid 360', 1, 109.99, 109.99),
(28, 15, 'fans', 1, 'ForgeFlow 120 Fan', 1, 14.99, 14.99),
(29, 15, 'fans', 2, 'ForgeFlow 140 RGB Fan', 1, 19.99, 19.99),
(30, 16, 'cpu', 1, 'ForgeCore 7600', 2, 219.99, 439.98),
(31, 16, 'cpu', 2, 'ForgeCore 13600', 1, 279.99, 279.99),
(32, 16, 'cpu', 3, 'ForgeCore 7500', 1, 179.99, 179.99),
(33, 17, 'gpu', 1, 'ForgeVision 4070', 1, 599.99, 599.99),
(34, 17, 'gpu', 2, 'ForgeVision 7900 XT', 1, 699.99, 699.99),
(35, 17, 'gpu', 3, 'ForgeVision 4060', 1, 299.99, 299.99),
(36, 17, 'mb', 1, 'ForgeBoard B650 ATX', 1, 159.99, 159.99),
(37, 18, 'mb', 1, 'ForgeBoard B650 ATX', 2, 159.99, 319.98),
(38, 18, 'mb', 2, 'ForgeBoard Z790 DDR5', 1, 229.99, 229.99),
(39, 19, 'memory', 1, 'ForgeMemory 32 GB DDR5', 1, 89.99, 89.99),
(40, 19, 'memory', 2, 'ForgeMemory 16 GB DDR4', 1, 39.99, 39.99),
(41, 19, 'memory', 3, 'ForgeMemory 16 GB DDR5', 1, 54.99, 54.99),
(42, 20, 'storage', 1, 'ForgeDrive 1 TB NVMe', 1, 69.99, 69.99),
(43, 20, 'storage', 2, 'ForgeDrive 2 TB SATA SSD', 1, 89.99, 89.99),
(44, 21, 'psu', 1, 'ForgePower 650 W Bronze', 2, 79.99, 159.98),
(45, 21, 'psu', 2, 'ForgePower 850 W Gold', 1, 129.99, 129.99),
(46, 21, 'psu', 3, 'ForgePower 550 W Bronze', 1, 59.99, 59.99),
(47, 22, 'case_box', 1, 'ForgeCase Air ATX', 1, 89.99, 89.99),
(48, 22, 'case_box', 2, 'ForgeCase Compact', 1, 69.99, 69.99),
(49, 22, 'case_box', 3, 'ForgeCase Mesh ATX', 1, 109.99, 109.99),
(50, 22, 'cooling', 1, 'ForgeCool Tower 120', 1, 39.99, 39.99),
(51, 23, 'cooling', 1, 'ForgeCool Tower 120', 2, 39.99, 79.98),
(52, 23, 'cooling', 2, 'ForgeCool Liquid 360', 1, 109.99, 109.99),
(53, 24, 'fans', 1, 'ForgeFlow 120 Fan', 1, 14.99, 14.99),
(54, 24, 'fans', 2, 'ForgeFlow 140 RGB Fan', 1, 19.99, 19.99),
(55, 24, 'fans', 3, 'ForgeFlow 120 RGB Fan', 1, 17.99, 17.99),
(56, 25, 'cpu', 1, 'ForgeCore 7600', 2, 219.99, 439.98),
(57, 25, 'cpu', 2, 'ForgeCore 13600', 1, 279.99, 279.99),
(58, 25, 'cpu', 3, 'ForgeCore 7500', 1, 179.99, 179.99),
(59, 25, 'gpu', 1, 'ForgeVision 4070', 1, 599.99, 599.99),
(60, 26, 'gpu', 1, 'ForgeVision 4070', 1, 599.99, 599.99),
(61, 26, 'gpu', 2, 'ForgeVision 7900 XT', 1, 699.99, 699.99),
(62, 27, 'mb', 1, 'ForgeBoard B650 ATX', 2, 159.99, 319.98),
(63, 27, 'mb', 2, 'ForgeBoard Z790 DDR5', 1, 229.99, 229.99),
(64, 27, 'mb', 3, 'ForgeBoard B550 DDR4', 1, 119.99, 119.99),
(65, 28, 'memory', 1, 'ForgeMemory 32 GB DDR5', 1, 89.99, 89.99),
(66, 28, 'memory', 2, 'ForgeMemory 16 GB DDR4', 1, 39.99, 39.99),
(67, 28, 'memory', 3, 'ForgeMemory 16 GB DDR5', 1, 54.99, 54.99),
(68, 28, 'storage', 1, 'ForgeDrive 1 TB NVMe', 1, 69.99, 69.99),
(69, 29, 'storage', 1, 'ForgeDrive 1 TB NVMe', 2, 69.99, 139.98),
(70, 29, 'storage', 2, 'ForgeDrive 2 TB SATA SSD', 1, 89.99, 89.99),
(71, 30, 'psu', 1, 'ForgePower 650 W Bronze', 1, 79.99, 79.99),
(72, 30, 'psu', 2, 'ForgePower 850 W Gold', 1, 129.99, 129.99),
(73, 30, 'psu', 3, 'ForgePower 550 W Bronze', 1, 59.99, 59.99),
(74, 31, 'case_box', 1, 'ForgeCase Air ATX', 2, 89.99, 179.98),
(75, 31, 'case_box', 2, 'ForgeCase Compact', 1, 69.99, 69.99),
(76, 31, 'case_box', 3, 'ForgeCase Mesh ATX', 1, 109.99, 109.99),
(77, 31, 'cooling', 1, 'ForgeCool Tower 120', 1, 39.99, 39.99),
(78, 32, 'cooling', 1, 'ForgeCool Tower 120', 1, 39.99, 39.99),
(79, 32, 'cooling', 2, 'ForgeCool Liquid 360', 1, 109.99, 109.99),
(80, 33, 'fans', 1, 'ForgeFlow 120 Fan', 1, 14.99, 14.99),
(81, 33, 'fans', 2, 'ForgeFlow 140 RGB Fan', 1, 19.99, 19.99),
(82, 34, 'cpu', 1, 'ForgeCore 7600', 2, 219.99, 439.98),
(83, 34, 'cpu', 2, 'ForgeCore 13600', 1, 279.99, 279.99),
(84, 34, 'cpu', 3, 'ForgeCore 7500', 1, 179.99, 179.99),
(85, 35, 'gpu', 1, 'ForgeVision 4070', 1, 599.99, 599.99),
(86, 35, 'gpu', 2, 'ForgeVision 7900 XT', 1, 699.99, 699.99),
(87, 35, 'gpu', 3, 'ForgeVision 4060', 1, 299.99, 299.99),
(88, 35, 'mb', 1, 'ForgeBoard B650 ATX', 1, 159.99, 159.99),
(89, 36, 'mb', 1, 'ForgeBoard B650 ATX', 2, 159.99, 319.98),
(90, 36, 'mb', 2, 'ForgeBoard Z790 DDR5', 1, 229.99, 229.99),
(91, 37, 'memory', 1, 'ForgeMemory 32 GB DDR5', 1, 89.99, 89.99),
(92, 37, 'memory', 2, 'ForgeMemory 16 GB DDR4', 1, 39.99, 39.99),
(93, 37, 'memory', 3, 'ForgeMemory 16 GB DDR5', 1, 54.99, 54.99),
(94, 38, 'storage', 1, 'ForgeDrive 1 TB NVMe', 2, 69.99, 139.98),
(95, 38, 'storage', 2, 'ForgeDrive 2 TB SATA SSD', 1, 89.99, 89.99),
(96, 38, 'storage', 3, 'ForgeDrive 500 GB NVMe', 1, 39.99, 39.99),
(97, 38, 'psu', 1, 'ForgePower 650 W Bronze', 1, 79.99, 79.99),
(98, 39, 'psu', 1, 'ForgePower 650 W Bronze', 1, 79.99, 79.99),
(99, 39, 'psu', 2, 'ForgePower 850 W Gold', 1, 129.99, 129.99),
(100, 40, 'case_box', 1, 'ForgeCase Air ATX', 2, 89.99, 179.98),
(101, 40, 'case_box', 2, 'ForgeCase Compact', 1, 69.99, 69.99),
(102, 40, 'case_box', 3, 'ForgeCase Mesh ATX', 1, 109.99, 109.99),
(103, 41, 'cooling', 1, 'ForgeCool Tower 120', 1, 39.99, 39.99),
(104, 41, 'cooling', 2, 'ForgeCool Liquid 360', 1, 109.99, 109.99),
(105, 41, 'cooling', 3, 'ForgeCool Compact 92', 1, 24.99, 24.99),
(106, 41, 'fans', 1, 'ForgeFlow 120 Fan', 1, 14.99, 14.99),
(107, 42, 'fans', 1, 'ForgeFlow 120 Fan', 2, 14.99, 29.98),
(108, 42, 'fans', 2, 'ForgeFlow 140 RGB Fan', 1, 19.99, 19.99),
(109, 43, 'cpu', 1, 'ForgeCore 7600', 1, 219.99, 219.99),
(110, 43, 'cpu', 2, 'ForgeCore 13600', 1, 279.99, 279.99),
(111, 43, 'cpu', 3, 'ForgeCore 7500', 1, 179.99, 179.99),
(112, 44, 'gpu', 1, 'ForgeVision 4070', 2, 599.99, 1199.98),
(113, 44, 'gpu', 2, 'ForgeVision 7900 XT', 1, 699.99, 699.99),
(114, 44, 'gpu', 3, 'ForgeVision 4060', 1, 299.99, 299.99),
(115, 44, 'mb', 1, 'ForgeBoard B650 ATX', 1, 159.99, 159.99),
(116, 45, 'mb', 1, 'ForgeBoard B650 ATX', 1, 159.99, 159.99),
(117, 45, 'mb', 2, 'ForgeBoard Z790 DDR5', 1, 229.99, 229.99),
(118, 46, 'memory', 1, 'ForgeMemory 32 GB DDR5', 2, 89.99, 179.98),
(119, 46, 'memory', 2, 'ForgeMemory 16 GB DDR4', 1, 39.99, 39.99),
(120, 46, 'memory', 3, 'ForgeMemory 16 GB DDR5', 1, 54.99, 54.99);

-- --------------------------------------------------------

--
-- Table structure for table `product_data_sources`
--

CREATE TABLE `product_data_sources` (
  `category` varchar(20) NOT NULL,
  `source_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `source_file` varchar(255) NOT NULL,
  `source_row` int(10) UNSIGNED NOT NULL,
  `source_values` longtext NOT NULL CHECK (json_valid(`source_values`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_data_sources`
--

INSERT INTO `product_data_sources` (`category`, `source_id`, `product_id`, `source_file`, `source_row`, `source_values`) VALUES
('case_box', 1, 12, 'ProductsData/Case/Case.xlsx', 8, '{\"ID\":1,\"PC Case Model Name\":\"Lian Li O11 Dynamic EVO XL\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Tempered Glass (Front & Side)\",\"Included Fans\":\"0 (Modular bracket layout)\",\"Max GPU Clearance\":\"460 mm\",\"Price (USD)\":239.99}'),
('case_box', 2, 13, 'ProductsData/Case/Case.xlsx', 9, '{\"ID\":2,\"PC Case Model Name\":\"HYTE Y70 Touch\",\"Form Factor\":\"Dual Chamber Mid-Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Tempered Glass + Integrated 4K Touch Screen\",\"Included Fans\":\"0\",\"Max GPU Clearance\":\"390 mm\",\"Price (USD)\":359.99}'),
('case_box', 3, 14, 'ProductsData/Case/Case.xlsx', 10, '{\"ID\":3,\"PC Case Model Name\":\"Corsair 7000D AIRFLOW\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Tempered Glass\",\"Included Fans\":\"3x 140mm AirGuide\",\"Max GPU Clearance\":\"450 mm\",\"Price (USD)\":269.99}'),
('case_box', 4, 15, 'ProductsData/Case/Case.xlsx', 11, '{\"ID\":4,\"PC Case Model Name\":\"Fractal Design North XL Mesh\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Mesh (Real Walnut\\/Oak Wood Front)\",\"Included Fans\":\"3x 140mm Aspect 14 PWM\",\"Max GPU Clearance\":\"413 mm\",\"Price (USD)\":179.99}'),
('case_box', 5, 16, 'ProductsData/Case/Case.xlsx', 12, '{\"ID\":5,\"PC Case Model Name\":\"Phanteks NV9 Premium Dual-Chamber\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Seamless Tempered Glass\",\"Included Fans\":\"0\",\"Max GPU Clearance\":\"440 mm\",\"Price (USD)\":249.99}'),
('case_box', 6, 17, 'ProductsData/Case/Case.xlsx', 13, '{\"ID\":6,\"PC Case Model Name\":\"NZXT H9 Elite\",\"Form Factor\":\"Dual-Chamber Mid-Tower\",\"Motherboard Support\":\"ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Tempered Glass (Top, Front, Side)\",\"Included Fans\":\"3x 120mm F120 RGB Duo + 1x 120mm F120Q\",\"Max GPU Clearance\":\"435 mm\",\"Price (USD)\":239.99}'),
('case_box', 7, 18, 'ProductsData/Case/Case.xlsx', 14, '{\"ID\":7,\"PC Case Model Name\":\"Cooler Master Cosmos C700P Black Edition\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Curved Tempered Glass\",\"Included Fans\":\"4x 140mm 1200RPM Fans\",\"Max GPU Clearance\":\"490 mm\",\"Price (USD)\":349.99}'),
('case_box', 8, 19, 'ProductsData/Case/Case.xlsx', 15, '{\"ID\":8,\"PC Case Model Name\":\"ASUS ROG Hyperion GR701\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Hinged Tempered Glass\",\"Included Fans\":\"4x 140mm PWM Fans\",\"Max GPU Clearance\":\"460 mm\",\"Price (USD)\":499.99}'),
('case_box', 9, 20, 'ProductsData/Case/Case.xlsx', 16, '{\"ID\":9,\"PC Case Model Name\":\"Fractal Design Torrent\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX, SSI-EEB\",\"Side Panel \\/ Finish\":\"Tempered Glass\",\"Included Fans\":\"2x 180mm + 3x 140mm Dynamic GP\",\"Max GPU Clearance\":\"461 mm\",\"Price (USD)\":189.99}'),
('case_box', 10, 21, 'ProductsData/Case/Case.xlsx', 17, '{\"ID\":10,\"PC Case Model Name\":\"Lian Li LANCOOL III RGB\",\"Form Factor\":\"Mid Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Dual Hinged Tempered Glass\",\"Included Fans\":\"4x 140mm ARGB PWM Fans\",\"Max GPU Clearance\":\"435 mm\",\"Price (USD)\":159.99}'),
('case_box', 11, 22, 'ProductsData/Case/Case.xlsx', 18, '{\"ID\":11,\"PC Case Model Name\":\"be quiet! Dark Base Pro 901\",\"Form Factor\":\"Full Tower\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Tempered Glass\",\"Included Fans\":\"3x Silent Wings 4 140mm PWM\",\"Max GPU Clearance\":\"495 mm\",\"Price (USD)\":299.99}'),
('case_box', 12, 23, 'ProductsData/Case/Case.xlsx', 19, '{\"ID\":12,\"PC Case Model Name\":\"SSUPD Meshlicious\",\"Form Factor\":\"Small Form Factor (SFF)\",\"Motherboard Support\":\"Mini-ITX\",\"Side Panel \\/ Finish\":\"Full Mesh\",\"Included Fans\":\"0\",\"Max GPU Clearance\":\"336 mm\",\"Price (USD)\":119.99}'),
('case_box', 13, 24, 'ProductsData/Case/Case.xlsx', 20, '{\"ID\":13,\"PC Case Model Name\":\"Corsair 5000D AIRFLOW\",\"Form Factor\":\"Mid Tower\",\"Motherboard Support\":\"ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Tempered Glass\",\"Included Fans\":\"2x 120mm AirGuide Fans\",\"Max GPU Clearance\":\"420 mm\",\"Price (USD)\":174.99}'),
('case_box', 14, 25, 'ProductsData/Case/Case.xlsx', 21, '{\"ID\":14,\"PC Case Model Name\":\"Thermaltake CTE C750 TG ARGB\",\"Form Factor\":\"Full Tower (Central Thermal)\",\"Motherboard Support\":\"E-ATX, ATX, Micro-ATX, Mini-ITX\",\"Side Panel \\/ Finish\":\"Tempered Glass\",\"Included Fans\":\"3x 140mm CT140 ARGB Fans\",\"Max GPU Clearance\":\"420 mm\",\"Price (USD)\":189.99}'),
('case_box', 15, 26, 'ProductsData/Case/Case.xlsx', 22, '{\"ID\":15,\"PC Case Model Name\":\"FormD T1 v2.1\",\"Form Factor\":\"Small Form Factor (SFF)\",\"Motherboard Support\":\"Mini-ITX\",\"Side Panel \\/ Finish\":\"CNC Aluminum Mesh\",\"Included Fans\":\"0\",\"Max GPU Clearance\":\"325 mm\",\"Price (USD)\":219.99}'),
('cooling', 1, 12, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 8, '{\"ID\":1,\"CPU Cooler Name\":\"Corsair iCUE Link H150i RGB\",\"Fan RPM\":\"480 – 2400 RPM\",\"Noise Level\":\"10 – 37 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":239.99}'),
('cooling', 2, 13, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 9, '{\"ID\":2,\"CPU Cooler Name\":\"NZXT Kraken Elite 360 RGB\",\"Fan RPM\":\"500 – 1800 RPM\",\"Noise Level\":\"17.9 – 30.6 dBA\",\"Color\":\"Matte Black\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":299.99}'),
('cooling', 3, 14, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 10, '{\"ID\":3,\"CPU Cooler Name\":\"ASUS ROG Ryujin III 360 ARGB\",\"Fan RPM\":\"600 – 2200 RPM\",\"Noise Level\":\"15 – 36.45 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":349.99}'),
('cooling', 4, 15, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 11, '{\"ID\":4,\"CPU Cooler Name\":\"Noctua NH-D15 chromax.black\",\"Fan RPM\":\"300 – 1500 RPM\",\"Noise Level\":\"19.2 – 24.6 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"None (Air Cooler)\",\"Price (USD)\":119.95}'),
('cooling', 5, 16, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 12, '{\"ID\":5,\"CPU Cooler Name\":\"Lian Li GA II Trinity Performance 360\",\"Fan RPM\":\"2300 – 3000 RPM\",\"Noise Level\":\"20 – 39.9 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":169.99}'),
('cooling', 6, 17, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 13, '{\"ID\":6,\"CPU Cooler Name\":\"DeepCool LT720 WH\",\"Fan RPM\":\"500 – 2250 RPM\",\"Noise Level\":\"10 – 32.9 dBA\",\"Color\":\"White\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":139.99}'),
('cooling', 7, 18, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 14, '{\"ID\":7,\"CPU Cooler Name\":\"ARCTIC Liquid Freezer III 420 A-RGB\",\"Fan RPM\":\"200 – 1900 RPM\",\"Noise Level\":\"10.5 – 22.5 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"420 mm (Included)\",\"Price (USD)\":149.99}'),
('cooling', 8, 19, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 15, '{\"ID\":8,\"CPU Cooler Name\":\"MSI MAG CoreLiquid E360\",\"Fan RPM\":\"600 – 1800 RPM\",\"Noise Level\":\"11.2 – 32.5 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":139.99}'),
('cooling', 9, 20, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 16, '{\"ID\":9,\"CPU Cooler Name\":\"be quiet! Dark Rock Elite\",\"Fan RPM\":\"1500 – 2000 RPM\",\"Noise Level\":\"11 – 25.8 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"None (Air Cooler)\",\"Price (USD)\":114.9}'),
('cooling', 10, 21, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 17, '{\"ID\":10,\"CPU Cooler Name\":\"Corsair iCUE H170i LCD XT\",\"Fan RPM\":\"550 – 1700 RPM\",\"Noise Level\":\"5 – 34.1 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"420 mm (Included)\",\"Price (USD)\":309.99}'),
('cooling', 11, 22, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 18, '{\"ID\":11,\"CPU Cooler Name\":\"Thermalright Peerless Assassin 120 SE\",\"Fan RPM\":\"600 – 1550 RPM\",\"Noise Level\":\"15 – 25.6 dBA\",\"Color\":\"Black \\/ Silver\",\"Radiator Size (If Contain)\":\"None (Air Cooler)\",\"Price (USD)\":33.9}'),
('cooling', 12, 23, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 19, '{\"ID\":12,\"CPU Cooler Name\":\"NZXT Kraken Elite 280 RGB\",\"Fan RPM\":\"500 – 1500 RPM\",\"Noise Level\":\"19.4 – 32.1 dBA\",\"Color\":\"Matte White\",\"Radiator Size (If Contain)\":\"280 mm (Included)\",\"Price (USD)\":259.99}'),
('cooling', 13, 24, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 20, '{\"ID\":13,\"CPU Cooler Name\":\"EKWB EK-Nucleus AIO CR360 Lux D-RGB\",\"Fan RPM\":\"550 – 2300 RPM\",\"Noise Level\":\"12 – 36 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":163.99}'),
('cooling', 14, 25, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 21, '{\"ID\":14,\"CPU Cooler Name\":\"Phanteks Glacier One 360 T30 V2\",\"Fan RPM\":\"1200 – 3000 RPM\",\"Noise Level\":\"11.1 – 39.7 dBA\",\"Color\":\"Black\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":259.99}'),
('cooling', 15, 26, 'ProductsData/CPU_Cooler/CPU_Cooler.xlsx', 22, '{\"ID\":15,\"CPU Cooler Name\":\"be quiet! Pure Loop 2 FX 360mm\",\"Fan RPM\":\"600 – 2500 RPM\",\"Noise Level\":\"16.8 – 34 dBA\",\"Color\":\"Black \\/ ARGB\",\"Radiator Size (If Contain)\":\"360 mm (Included)\",\"Price (USD)\":159.9}'),
('cpu', 1, 19, 'ProductsData/CPU/CPU.xlsx', 8, '{\"ID\":1,\"CPU Name\":\"Intel Core i9-14900KS\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"24 (8P + 16E) \\/ 32 Threads\",\"Perf Core Clock\":\"3.2 GHz (6.2 GHz Boost)\",\"Microarchitecture\":\"Raptor Lake Refresh\",\"TDP\":\"150W (253W Turbo)\",\"Integrated Graphic Card\":\"Intel UHD Graphics 770\",\"Price (USD)\":689.99}'),
('cpu', 2, 20, 'ProductsData/CPU/CPU.xlsx', 9, '{\"ID\":2,\"CPU Name\":\"AMD Ryzen 9 7950X3D\",\"Socket \\/ Motherboard Support\":\"AM5\",\"Core Count\":\"16 Cores \\/ 32 Threads\",\"Perf Core Clock\":\"4.2 GHz (5.7 GHz Boost)\",\"Microarchitecture\":\"Zen 4 (3D V-Cache)\",\"TDP\":\"120W\",\"Integrated Graphic Card\":\"AMD Radeon Graphics (2 Cores)\",\"Price (USD)\":649}'),
('cpu', 3, 21, 'ProductsData/CPU/CPU.xlsx', 10, '{\"ID\":3,\"CPU Name\":\"Intel Core i9-14900K\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"24 (8P + 16E) \\/ 32 Threads\",\"Perf Core Clock\":\"3.2 GHz (6.0 GHz Boost)\",\"Microarchitecture\":\"Raptor Lake Refresh\",\"TDP\":\"125W (253W Turbo)\",\"Integrated Graphic Card\":\"Intel UHD Graphics 770\",\"Price (USD)\":549.99}'),
('cpu', 4, 22, 'ProductsData/CPU/CPU.xlsx', 11, '{\"ID\":4,\"CPU Name\":\"AMD Ryzen 9 7950X\",\"Socket \\/ Motherboard Support\":\"AM5\",\"Core Count\":\"16 Cores \\/ 32 Threads\",\"Perf Core Clock\":\"4.5 GHz (5.7 GHz Boost)\",\"Microarchitecture\":\"Zen 4\",\"TDP\":\"170W\",\"Integrated Graphic Card\":\"AMD Radeon Graphics (2 Cores)\",\"Price (USD)\":549}'),
('cpu', 5, 23, 'ProductsData/CPU/CPU.xlsx', 12, '{\"ID\":5,\"CPU Name\":\"Intel Core i9-13900KS\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"24 (8P + 16E) \\/ 32 Threads\",\"Perf Core Clock\":\"3.2 GHz (6.0 GHz Boost)\",\"Microarchitecture\":\"Raptor Lake\",\"TDP\":\"150W (253W Turbo)\",\"Integrated Graphic Card\":\"Intel UHD Graphics 770\",\"Price (USD)\":629.99}'),
('cpu', 6, 24, 'ProductsData/CPU/CPU.xlsx', 13, '{\"ID\":6,\"CPU Name\":\"AMD Ryzen 7 7800X3D\",\"Socket \\/ Motherboard Support\":\"AM5\",\"Core Count\":\"8 Cores \\/ 16 Threads\",\"Perf Core Clock\":\"4.2 GHz (5.0 GHz Boost)\",\"Microarchitecture\":\"Zen 4 (3D V-Cache)\",\"TDP\":\"120W\",\"Integrated Graphic Card\":\"AMD Radeon Graphics (2 Cores)\",\"Price (USD)\":369}'),
('cpu', 7, 25, 'ProductsData/CPU/CPU.xlsx', 14, '{\"ID\":7,\"CPU Name\":\"Intel Core i9-13900K\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"24 (8P + 16E) \\/ 32 Threads\",\"Perf Core Clock\":\"3.0 GHz (5.8 GHz Boost)\",\"Microarchitecture\":\"Raptor Lake\",\"TDP\":\"125W (253W Turbo)\",\"Integrated Graphic Card\":\"Intel UHD Graphics 770\",\"Price (USD)\":489.99}'),
('cpu', 8, 26, 'ProductsData/CPU/CPU.xlsx', 15, '{\"ID\":8,\"CPU Name\":\"AMD Ryzen 9 7900X3D\",\"Socket \\/ Motherboard Support\":\"AM5\",\"Core Count\":\"12 Cores \\/ 24 Threads\",\"Perf Core Clock\":\"4.4 GHz (5.6 GHz Boost)\",\"Microarchitecture\":\"Zen 4 (3D V-Cache)\",\"TDP\":\"120W\",\"Integrated Graphic Card\":\"AMD Radeon Graphics (2 Cores)\",\"Price (USD)\":499}'),
('cpu', 9, 27, 'ProductsData/CPU/CPU.xlsx', 16, '{\"ID\":9,\"CPU Name\":\"Intel Core i7-14700K\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"20 (8P + 12E) \\/ 28 Threads\",\"Perf Core Clock\":\"3.4 GHz (5.6 GHz Boost)\",\"Microarchitecture\":\"Raptor Lake Refresh\",\"TDP\":\"125W (253W Turbo)\",\"Integrated Graphic Card\":\"Intel UHD Graphics 770\",\"Price (USD)\":389.99}'),
('cpu', 10, 28, 'ProductsData/CPU/CPU.xlsx', 17, '{\"ID\":10,\"CPU Name\":\"AMD Ryzen 9 7900X\",\"Socket \\/ Motherboard Support\":\"AM5\",\"Core Count\":\"12 Cores \\/ 24 Threads\",\"Perf Core Clock\":\"4.7 GHz (5.6 GHz Boost)\",\"Microarchitecture\":\"Zen 4\",\"TDP\":\"170W\",\"Integrated Graphic Card\":\"AMD Radeon Graphics (2 Cores)\",\"Price (USD)\":389}'),
('cpu', 11, 29, 'ProductsData/CPU/CPU.xlsx', 18, '{\"ID\":11,\"CPU Name\":\"Intel Core i9-14900KF\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"24 (8P + 16E) \\/ 32 Threads\",\"Perf Core Clock\":\"3.2 GHz (6.0 GHz Boost)\",\"Microarchitecture\":\"Raptor Lake Refresh\",\"TDP\":\"125W (253W Turbo)\",\"Integrated Graphic Card\":\"None (Requires Discrete GPU)\",\"Price (USD)\":529.99}'),
('cpu', 12, 30, 'ProductsData/CPU/CPU.xlsx', 19, '{\"ID\":12,\"CPU Name\":\"AMD Ryzen 9 7900\",\"Socket \\/ Motherboard Support\":\"AM5\",\"Core Count\":\"12 Cores \\/ 24 Threads\",\"Perf Core Clock\":\"3.7 GHz (5.4 GHz Boost)\",\"Microarchitecture\":\"Zen 4\",\"TDP\":\"65W\",\"Integrated Graphic Card\":\"AMD Radeon Graphics (2 Cores)\",\"Price (USD)\":369}'),
('cpu', 13, 31, 'ProductsData/CPU/CPU.xlsx', 20, '{\"ID\":13,\"CPU Name\":\"Intel Core i7-13700K\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"16 (8P + 8E) \\/ 24 Threads\",\"Perf Core Clock\":\"3.4 GHz (5.4 GHz Boost)\",\"Microarchitecture\":\"Raptor Lake\",\"TDP\":\"125W (253W Turbo)\",\"Integrated Graphic Card\":\"Intel UHD Graphics 770\",\"Price (USD)\":349.99}'),
('cpu', 14, 32, 'ProductsData/CPU/CPU.xlsx', 21, '{\"ID\":14,\"CPU Name\":\"AMD Ryzen 7 7700X\",\"Socket \\/ Motherboard Support\":\"AM5\",\"Core Count\":\"8 Cores \\/ 16 Threads\",\"Perf Core Clock\":\"4.5 GHz (5.4 GHz Boost)\",\"Microarchitecture\":\"Zen 4\",\"TDP\":\"105W\",\"Integrated Graphic Card\":\"AMD Radeon Graphics (2 Cores)\",\"Price (USD)\":289}'),
('cpu', 15, 33, 'ProductsData/CPU/CPU.xlsx', 22, '{\"ID\":15,\"CPU Name\":\"Intel Core i9-12900KS\",\"Socket \\/ Motherboard Support\":\"LGA 1700\",\"Core Count\":\"16 (8P + 8E) \\/ 24 Threads\",\"Perf Core Clock\":\"3.4 GHz (5.5 GHz Boost)\",\"Microarchitecture\":\"Alder Lake\",\"TDP\":\"150W (241W Turbo)\",\"Integrated Graphic Card\":\"Intel UHD Graphics 770\",\"Price (USD)\":419.99}'),
('gpu', 1, 14, 'ProductsData/Video_Card/GPU.xlsx', 8, '{\"ID\":1,\"GPU Name\":\"NVIDIA GeForce RTX 5090 FE\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB202\",\"VRAM Size & Type\":\"32 GB GDDR7\",\"Memory Bus Width\":\"512-bit\",\"Boost Core Clock\":\"2400 MHz\",\"TDP \\/ Power\":\"575W\",\"Price (USD)\":1999}'),
('gpu', 2, 15, 'ProductsData/Video_Card/GPU.xlsx', 9, '{\"ID\":2,\"GPU Name\":\"ASUS ROG Strix RTX 5090 OC\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB202\",\"VRAM Size & Type\":\"32 GB GDDR7\",\"Memory Bus Width\":\"512-bit\",\"Boost Core Clock\":\"2550 MHz\",\"TDP \\/ Power\":\"600W\",\"Price (USD)\":2299.99}'),
('gpu', 3, 16, 'ProductsData/Video_Card/GPU.xlsx', 10, '{\"ID\":3,\"GPU Name\":\"MSI GeForce RTX 5090 SUPRIM X\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB202\",\"VRAM Size & Type\":\"32 GB GDDR7\",\"Memory Bus Width\":\"512-bit\",\"Boost Core Clock\":\"2535 MHz\",\"TDP \\/ Power\":\"600W\",\"Price (USD)\":2199.99}'),
('gpu', 4, 17, 'ProductsData/Video_Card/GPU.xlsx', 11, '{\"ID\":4,\"GPU Name\":\"GIGABYTE RTX 5090 AORUS Xtreme\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB202\",\"VRAM Size & Type\":\"32 GB GDDR7\",\"Memory Bus Width\":\"512-bit\",\"Boost Core Clock\":\"2520 MHz\",\"TDP \\/ Power\":\"600W\",\"Price (USD)\":2149.99}'),
('gpu', 5, 18, 'ProductsData/Video_Card/GPU.xlsx', 12, '{\"ID\":5,\"GPU Name\":\"NVIDIA GeForce RTX 5080 FE\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB203\",\"VRAM Size & Type\":\"16 GB GDDR7\",\"Memory Bus Width\":\"256-bit\",\"Boost Core Clock\":\"2610 MHz\",\"TDP \\/ Power\":\"400W\",\"Price (USD)\":999}'),
('gpu', 6, 19, 'ProductsData/Video_Card/GPU.xlsx', 13, '{\"ID\":6,\"GPU Name\":\"ASUS ROG Strix RTX 5080 OC\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB203\",\"VRAM Size & Type\":\"16 GB GDDR7\",\"Memory Bus Width\":\"256-bit\",\"Boost Core Clock\":\"2730 MHz\",\"TDP \\/ Power\":\"430W\",\"Price (USD)\":1199.99}'),
('gpu', 7, 20, 'ProductsData/Video_Card/GPU.xlsx', 14, '{\"ID\":7,\"GPU Name\":\"MSI GeForce RTX 5080 SUPRIM X\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB203\",\"VRAM Size & Type\":\"16 GB GDDR7\",\"Memory Bus Width\":\"256-bit\",\"Boost Core Clock\":\"2715 MHz\",\"TDP \\/ Power\":\"425W\",\"Price (USD)\":1149.99}'),
('gpu', 8, 21, 'ProductsData/Video_Card/GPU.xlsx', 15, '{\"ID\":8,\"GPU Name\":\"NVIDIA GeForce RTX 4090 FE\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Ada Lovelace AD102\",\"VRAM Size & Type\":\"24 GB GDDR6X\",\"Memory Bus Width\":\"384-bit\",\"Boost Core Clock\":\"2520 MHz\",\"TDP \\/ Power\":\"450W\",\"Price (USD)\":1599}'),
('gpu', 9, 22, 'ProductsData/Video_Card/GPU.xlsx', 16, '{\"ID\":9,\"GPU Name\":\"ASUS ROG Strix RTX 4090 OC\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Ada Lovelace AD102\",\"VRAM Size & Type\":\"24 GB GDDR6X\",\"Memory Bus Width\":\"384-bit\",\"Boost Core Clock\":\"2640 MHz\",\"TDP \\/ Power\":\"500W\",\"Price (USD)\":1999.99}'),
('gpu', 10, 23, 'ProductsData/Video_Card/GPU.xlsx', 17, '{\"ID\":10,\"GPU Name\":\"MSI GeForce RTX 4090 SUPRIM Liquid X\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Ada Lovelace AD102\",\"VRAM Size & Type\":\"24 GB GDDR6X\",\"Memory Bus Width\":\"384-bit\",\"Boost Core Clock\":\"2640 MHz\",\"TDP \\/ Power\":\"480W\",\"Price (USD)\":1749.99}'),
('gpu', 11, 24, 'ProductsData/Video_Card/GPU.xlsx', 18, '{\"ID\":11,\"GPU Name\":\"AMD Radeon RX 7900 XTX\",\"GPU Chipset \\/ Architecture\":\"AMD RDNA 3 Navi 31\",\"VRAM Size & Type\":\"24 GB GDDR6\",\"Memory Bus Width\":\"384-bit\",\"Boost Core Clock\":\"2500 MHz\",\"TDP \\/ Power\":\"355W\",\"Price (USD)\":929.99}'),
('gpu', 12, 25, 'ProductsData/Video_Card/GPU.xlsx', 19, '{\"ID\":12,\"GPU Name\":\"Sapphire NITRO+ RX 7900 XTX Vapor-X\",\"GPU Chipset \\/ Architecture\":\"AMD RDNA 3 Navi 31\",\"VRAM Size & Type\":\"24 GB GDDR6\",\"Memory Bus Width\":\"384-bit\",\"Boost Core Clock\":\"2680 MHz\",\"TDP \\/ Power\":\"420W\",\"Price (USD)\":1049.99}'),
('gpu', 13, 26, 'ProductsData/Video_Card/GPU.xlsx', 20, '{\"ID\":13,\"GPU Name\":\"NVIDIA GeForce RTX 4080 Super\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Ada Lovelace AD103\",\"VRAM Size & Type\":\"16 GB GDDR6X\",\"Memory Bus Width\":\"256-bit\",\"Boost Core Clock\":\"2550 MHz\",\"TDP \\/ Power\":\"320W\",\"Price (USD)\":999}'),
('gpu', 14, 27, 'ProductsData/Video_Card/GPU.xlsx', 21, '{\"ID\":14,\"GPU Name\":\"ASUS TUF Gaming RTX 5080 OC\",\"GPU Chipset \\/ Architecture\":\"NVIDIA Blackwell GB203\",\"VRAM Size & Type\":\"16 GB GDDR7\",\"Memory Bus Width\":\"256-bit\",\"Boost Core Clock\":\"2680 MHz\",\"TDP \\/ Power\":\"420W\",\"Price (USD)\":1099.99}'),
('gpu', 15, 28, 'ProductsData/Video_Card/GPU.xlsx', 22, '{\"ID\":15,\"GPU Name\":\"PowerColor RED DEVIL RX 7900 XTX\",\"GPU Chipset \\/ Architecture\":\"AMD RDNA 3 Navi 31\",\"VRAM Size & Type\":\"24 GB GDDR6\",\"Memory Bus Width\":\"384-bit\",\"Boost Core Clock\":\"2565 MHz\",\"TDP \\/ Power\":\"395W\",\"Price (USD)\":979.99}'),
('mb', 1, 14, 'ProductsData/Motherboard/Motherboard.xlsx', 8, '{\"ID\":1,\"Motherboard Name\":\"ASUS ROG Maximus Z790 Extreme\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Black \\/ OLED Display\",\"Price (USD)\":999.99}'),
('mb', 2, 15, 'ProductsData/Motherboard/Motherboard.xlsx', 9, '{\"ID\":2,\"Motherboard Name\":\"MSI MEG Z790 GODLIKE MAX\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Dark Mirror Black\",\"Price (USD)\":1199.99}'),
('mb', 3, 16, 'ProductsData/Motherboard/Motherboard.xlsx', 10, '{\"ID\":3,\"Motherboard Name\":\"GIGABYTE Z790 AORUS Xtreme X\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Black \\/ Titanium\",\"Price (USD)\":999}'),
('mb', 4, 17, 'ProductsData/Motherboard/Motherboard.xlsx', 11, '{\"ID\":4,\"Motherboard Name\":\"ASRock X670E Taichi Carrara\",\"Socket \\/ CPU\":\"AM5\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Marble White\",\"Price (USD)\":529.99}'),
('mb', 5, 18, 'ProductsData/Motherboard/Motherboard.xlsx', 12, '{\"ID\":5,\"Motherboard Name\":\"ASUS ROG Crosshair X670E Extreme\",\"Socket \\/ CPU\":\"AM5\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Black \\/ AniMe Matrix\",\"Price (USD)\":999.99}'),
('mb', 6, 19, 'ProductsData/Motherboard/Motherboard.xlsx', 13, '{\"ID\":6,\"Motherboard Name\":\"MSI MEG X670E ACE\",\"Socket \\/ CPU\":\"AM5\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Black \\/ Gold Accents\",\"Price (USD)\":699.99}'),
('mb', 7, 20, 'ProductsData/Motherboard/Motherboard.xlsx', 14, '{\"ID\":7,\"Motherboard Name\":\"ASUS ROG Strix Z790-E Gaming WiFi II\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Black \\/ RGB\",\"Price (USD)\":499.99}'),
('mb', 8, 21, 'ProductsData/Motherboard/Motherboard.xlsx', 15, '{\"ID\":8,\"Motherboard Name\":\"GIGABYTE X670E AORUS Xtreme\",\"Socket \\/ CPU\":\"AM5\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Black \\/ Armor Metallic\",\"Price (USD)\":699}'),
('mb', 9, 22, 'ProductsData/Motherboard/Motherboard.xlsx', 16, '{\"ID\":9,\"Motherboard Name\":\"ASRock Z790 Taichi Lite\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Bronze \\/ Black\",\"Price (USD)\":379.99}'),
('mb', 10, 23, 'ProductsData/Motherboard/Motherboard.xlsx', 17, '{\"ID\":10,\"Motherboard Name\":\"ASUS ROG Hero Z790 Dark Hero\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Matte Black\",\"Price (USD)\":649.99}'),
('mb', 11, 24, 'ProductsData/Motherboard/Motherboard.xlsx', 18, '{\"ID\":11,\"Motherboard Name\":\"MSI MPG Z790 Carbon WiFi\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Carbon Black\",\"Price (USD)\":449.99}'),
('mb', 12, 25, 'ProductsData/Motherboard/Motherboard.xlsx', 19, '{\"ID\":12,\"Motherboard Name\":\"GIGABYTE Z790 AORUS Master X\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"E-ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Dark Grey \\/ Silver\",\"Price (USD)\":549.99}'),
('mb', 13, 26, 'ProductsData/Motherboard/Motherboard.xlsx', 20, '{\"ID\":13,\"Motherboard Name\":\"ASUS ROG Crosshair X670E Hero\",\"Socket \\/ CPU\":\"AM5\",\"Form Factor\":\"ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Polished Black\",\"Price (USD)\":649.99}'),
('mb', 14, 27, 'ProductsData/Motherboard/Motherboard.xlsx', 21, '{\"ID\":14,\"Motherboard Name\":\"NZXT N7 Z790\",\"Socket \\/ CPU\":\"LGA 1700\",\"Form Factor\":\"ATX\",\"Memory Max\":\"128 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Matte White\",\"Price (USD)\":299.99}'),
('mb', 15, 28, 'ProductsData/Motherboard/Motherboard.xlsx', 22, '{\"ID\":15,\"Motherboard Name\":\"ASRock X670E PG Lightning\",\"Socket \\/ CPU\":\"AM5\",\"Form Factor\":\"ATX\",\"Memory Max\":\"192 GB\",\"Memory Slots\":4,\"DDR Support\":\"DDR5\",\"Color\":\"Black \\/ Cyan\",\"Price (USD)\":259.99}'),
('memory', 1, 12, 'ProductsData/Memory/RAM.xlsx', 8, '{\"ID\":1,\"RAM Kit Name\":\"G.SKILL Trident Z5 RGB 64GB (2x32GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-8000 MHz\",\"Total Capacity\":\"64 GB (2x32GB)\",\"Tested Latency (CAS)\":\"CL38-48-48-128\",\"Tested Voltage\":\"1.45V\",\"Price (USD)\":349.99}'),
('memory', 2, 13, 'ProductsData/Memory/RAM.xlsx', 9, '{\"ID\":2,\"RAM Kit Name\":\"Corsair Dominator Titanium RGB 64GB (2x32GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-7200 MHz\",\"Total Capacity\":\"64 GB (2x32GB)\",\"Tested Latency (CAS)\":\"CL34-44-44-96\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":319.99}'),
('memory', 3, 14, 'ProductsData/Memory/RAM.xlsx', 10, '{\"ID\":3,\"RAM Kit Name\":\"TeamGroup T-Force Delta RGB 48GB (2x24GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-8200 MHz\",\"Total Capacity\":\"48 GB (2x24GB)\",\"Tested Latency (CAS)\":\"CL38-49-49-128\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":249.99}'),
('memory', 4, 15, 'ProductsData/Memory/RAM.xlsx', 11, '{\"ID\":4,\"RAM Kit Name\":\"G.SKILL Trident Z5 Neo RGB (AMD EXPO) 64GB\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-6000 MHz\",\"Total Capacity\":\"64 GB (2x32GB)\",\"Tested Latency (CAS)\":\"CL30-40-40-96\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":219.99}'),
('memory', 5, 16, 'ProductsData/Memory/RAM.xlsx', 12, '{\"ID\":5,\"RAM Kit Name\":\"Corsair Vengeance RGB 96GB (2x48GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-6400 MHz\",\"Total Capacity\":\"96 GB (2x48GB)\",\"Tested Latency (CAS)\":\"CL32-40-40-84\",\"Tested Voltage\":\"1.35V\",\"Price (USD)\":379.99}'),
('memory', 6, 17, 'ProductsData/Memory/RAM.xlsx', 13, '{\"ID\":6,\"RAM Kit Name\":\"G.SKILL Trident Z5 RGB 32GB (2x16GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-7600 MHz\",\"Total Capacity\":\"32 GB (2x16GB)\",\"Tested Latency (CAS)\":\"CL36-46-46-121\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":179.99}'),
('memory', 7, 18, 'ProductsData/Memory/RAM.xlsx', 14, '{\"ID\":7,\"RAM Kit Name\":\"Kingston Fury Renegade RGB 64GB (2x32GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-7200 MHz\",\"Total Capacity\":\"64 GB (2x32GB)\",\"Tested Latency (CAS)\":\"CL38-44-44-105\",\"Tested Voltage\":\"1.45V\",\"Price (USD)\":299.99}'),
('memory', 8, 19, 'ProductsData/Memory/RAM.xlsx', 15, '{\"ID\":8,\"RAM Kit Name\":\"Corsair Dominator Platinum RGB 32GB (2x16GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-6000 MHz\",\"Total Capacity\":\"32 GB (2x16GB)\",\"Tested Latency (CAS)\":\"CL30-36-36-76\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":169.99}'),
('memory', 9, 20, 'ProductsData/Memory/RAM.xlsx', 16, '{\"ID\":9,\"RAM Kit Name\":\"TeamGroup T-Force XTREEM ARGB 48GB (2x24GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-8000 MHz\",\"Total Capacity\":\"48 GB (2x24GB)\",\"Tested Latency (CAS)\":\"CL38-48-48-128\",\"Tested Voltage\":\"1.45V\",\"Price (USD)\":269.99}'),
('memory', 10, 21, 'ProductsData/Memory/RAM.xlsx', 17, '{\"ID\":10,\"RAM Kit Name\":\"G.SKILL Ripjaws S5 64GB (2x32GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-6400 MHz\",\"Total Capacity\":\"64 GB (2x32GB)\",\"Tested Latency (CAS)\":\"CL32-39-39-102\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":209.99}'),
('memory', 11, 22, 'ProductsData/Memory/RAM.xlsx', 18, '{\"ID\":11,\"RAM Kit Name\":\"Corsair Vengeance 64GB (2x32GB) AMD EXPO\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-6000 MHz\",\"Total Capacity\":\"64 GB (2x32GB)\",\"Tested Latency (CAS)\":\"CL30-36-36-76\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":214.99}'),
('memory', 12, 23, 'ProductsData/Memory/RAM.xlsx', 19, '{\"ID\":12,\"RAM Kit Name\":\"Patriot Viper Xtreme 5 RGB 32GB (2x16GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-8000 MHz\",\"Total Capacity\":\"32 GB (2x16GB)\",\"Tested Latency (CAS)\":\"CL38-48-48-128\",\"Tested Voltage\":\"1.45V\",\"Price (USD)\":189.99}'),
('memory', 13, 24, 'ProductsData/Memory/RAM.xlsx', 20, '{\"ID\":13,\"RAM Kit Name\":\"ADATA XPG Lancer RGB 64GB (2x32GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-6000 MHz\",\"Total Capacity\":\"64 GB (2x32GB)\",\"Tested Latency (CAS)\":\"CL30-40-40-96\",\"Tested Voltage\":\"1.35V\",\"Price (USD)\":199.99}'),
('memory', 14, 25, 'ProductsData/Memory/RAM.xlsx', 21, '{\"ID\":14,\"RAM Kit Name\":\"Thermaltake TOUGHRAM XG RGB D5 32GB\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-7200 MHz\",\"Total Capacity\":\"32 GB (2x16GB)\",\"Tested Latency (CAS)\":\"CL36-46-46-115\",\"Tested Voltage\":\"1.40V\",\"Price (USD)\":159.99}'),
('memory', 15, 26, 'ProductsData/Memory/RAM.xlsx', 22, '{\"ID\":15,\"RAM Kit Name\":\"Crucial Pro Overclocking 32GB (2x16GB)\",\"Memory Type\":\"DDR5\",\"Memory Speed\":\"DDR5-6000 MHz\",\"Total Capacity\":\"32 GB (2x16GB)\",\"Tested Latency (CAS)\":\"CL36-38-38-80\",\"Tested Voltage\":\"1.35V\",\"Price (USD)\":104.99}'),
('monitor', 1, 1, 'ProductsData/Monitor/Monitor.xlsx', 8, '{\"ID\":1,\"Monitor Model Name\":\"ASUS ROG Swift OLED PG32UCDM\",\"Screen Size\":\"32\\\"\",\"Panel Type\":\"QD-OLED\",\"Resolution\":\"3840 x 2160 (4K UHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | HDR TRUE BLACK 400\",\"Price (USD)\":1299.99}'),
('monitor', 2, 2, 'ProductsData/Monitor/Monitor.xlsx', 9, '{\"ID\":2,\"Monitor Model Name\":\"Dell Alienware AW3225QF\",\"Screen Size\":\"32\\\"\",\"Panel Type\":\"Curved QD-OLED (1700R)\",\"Resolution\":\"3840 x 2160 (4K UHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | Dolby Vision \\/ HDR400\",\"Price (USD)\":1199.99}'),
('monitor', 3, 3, 'ProductsData/Monitor/Monitor.xlsx', 10, '{\"ID\":3,\"Monitor Model Name\":\"LG UltraGear 32GS95UE-B\",\"Screen Size\":\"32\\\"\",\"Panel Type\":\"WOLED\",\"Resolution\":\"4K 240Hz \\/ FHD 480Hz\",\"Refresh Rate\":\"240 Hz \\/ 480 Hz Dual\",\"Response Time \\/ HDR\":\"0.03 ms GTG | DisplayHDR True Black 400\",\"Price (USD)\":1399.99}'),
('monitor', 4, 4, 'ProductsData/Monitor/Monitor.xlsx', 11, '{\"ID\":4,\"Monitor Model Name\":\"MSI MPG 321URX QD-OLED\",\"Screen Size\":\"32\\\"\",\"Panel Type\":\"QD-OLED\",\"Resolution\":\"3840 x 2160 (4K UHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | ClearMR 13000 \\/ HDR400\",\"Price (USD)\":949.99}'),
('monitor', 5, 5, 'ProductsData/Monitor/Monitor.xlsx', 12, '{\"ID\":5,\"Monitor Model Name\":\"Samsung Odyssey OLED G9 (G95SC)\",\"Screen Size\":\"49\\\"\",\"Panel Type\":\"Curved QD-OLED (1800R)\",\"Resolution\":\"5120 x 1440 (Dual QHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | DisplayHDR True Black 400\",\"Price (USD)\":1799.99}'),
('monitor', 6, 6, 'ProductsData/Monitor/Monitor.xlsx', 13, '{\"ID\":6,\"Monitor Model Name\":\"Samsung Odyssey Neo G9 (G95NC)\",\"Screen Size\":\"57\\\"\",\"Panel Type\":\"Curved Mini-LED (1000R)\",\"Resolution\":\"7680 x 2160 (Dual UHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"1 ms GTG | DisplayHDR 1000\",\"Price (USD)\":2499.99}'),
('monitor', 7, 7, 'ProductsData/Monitor/Monitor.xlsx', 14, '{\"ID\":7,\"Monitor Model Name\":\"ASUS ROG Swift Pro PG248QP\",\"Screen Size\":\"24.1\\\"\",\"Panel Type\":\"E-TN\",\"Resolution\":\"1920 x 1080 (FHD)\",\"Refresh Rate\":\"540 Hz\",\"Response Time \\/ HDR\":\"0.2 ms GTG | DisplayHDR 400\",\"Price (USD)\":899.99}'),
('monitor', 8, 8, 'ProductsData/Monitor/Monitor.xlsx', 15, '{\"ID\":8,\"Monitor Model Name\":\"Dell Alienware AW2725DF\",\"Screen Size\":\"27\\\"\",\"Panel Type\":\"QD-OLED\",\"Resolution\":\"2560 x 1440 (QHD)\",\"Refresh Rate\":\"360 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | DisplayHDR True Black 400\",\"Price (USD)\":899.99}'),
('monitor', 9, 9, 'ProductsData/Monitor/Monitor.xlsx', 16, '{\"ID\":9,\"Monitor Model Name\":\"Gigabyte AORUS FO32U2P\",\"Screen Size\":\"32\\\"\",\"Panel Type\":\"QD-OLED (DP 2.1 Native)\",\"Resolution\":\"3840 x 2160 (4K UHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | DisplayHDR True Black 400\",\"Price (USD)\":1299.99}'),
('monitor', 10, 10, 'ProductsData/Monitor/Monitor.xlsx', 17, '{\"ID\":10,\"Monitor Model Name\":\"BenQ ZOWIE XL2566K\",\"Screen Size\":\"24.5\\\"\",\"Panel Type\":\"TN (DyAc+)\",\"Resolution\":\"1920 x 1080 (FHD)\",\"Refresh Rate\":\"360 Hz\",\"Response Time \\/ HDR\":\"0.5 ms GTG | N\\/A (Esports Tuned)\",\"Price (USD)\":599.99}'),
('monitor', 11, 11, 'ProductsData/Monitor/Monitor.xlsx', 18, '{\"ID\":11,\"Monitor Model Name\":\"LG UltraGear 27GR95QE-B\",\"Screen Size\":\"27\\\"\",\"Panel Type\":\"OLED\",\"Resolution\":\"2560 x 1440 (QHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | HDR10\",\"Price (USD)\":799.99}'),
('monitor', 12, 12, 'ProductsData/Monitor/Monitor.xlsx', 19, '{\"ID\":12,\"Monitor Model Name\":\"Corsair XENEON FLEX 45WQHD240\",\"Screen Size\":\"45\\\"\",\"Panel Type\":\"Bendable OLED (Flat to 800R)\",\"Resolution\":\"3440 x 1440 (UWQHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | HDR10\",\"Price (USD)\":1699.99}'),
('monitor', 13, 13, 'ProductsData/Monitor/Monitor.xlsx', 20, '{\"ID\":13,\"Monitor Model Name\":\"ASUS ROG Swift PG27AQDM\",\"Screen Size\":\"27\\\"\",\"Panel Type\":\"OLED\",\"Resolution\":\"2560 x 1440 (QHD)\",\"Refresh Rate\":\"240 Hz\",\"Response Time \\/ HDR\":\"0.03 ms GTG | HDR10\",\"Price (USD)\":899.99}'),
('monitor', 14, 14, 'ProductsData/Monitor/Monitor.xlsx', 21, '{\"ID\":14,\"Monitor Model Name\":\"Acer Predator X32 FP\",\"Screen Size\":\"32\\\"\",\"Panel Type\":\"Mini-LED IPS\",\"Resolution\":\"3840 x 2160 (4K UHD)\",\"Refresh Rate\":\"160 Hz\",\"Response Time \\/ HDR\":\"0.7 ms GTG | DisplayHDR 1000\",\"Price (USD)\":1199.99}'),
('monitor', 15, 15, 'ProductsData/Monitor/Monitor.xlsx', 22, '{\"ID\":15,\"Monitor Model Name\":\"Apple Studio Display - Nano-Texture\",\"Screen Size\":\"27\\\"\",\"Panel Type\":\"IPS LCD\",\"Resolution\":\"5120 x 2880 (5K Retina)\",\"Refresh Rate\":\"60 Hz\",\"Response Time \\/ HDR\":\"5 ms GTG | 600 nits Brightness\",\"Price (USD)\":1899.99}'),
('psu', 1, 12, 'ProductsData/Power_Supply/PSU.xlsx', 8, '{\"ID\":1,\"Power Supply Model\":\"Corsair AX1600i 1600W Titanium\",\"Wattage\":\"1600 W\",\"Efficiency Rating\":\"80 PLUS Titanium\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":609.99}'),
('psu', 2, 13, 'ProductsData/Power_Supply/PSU.xlsx', 9, '{\"ID\":2,\"Power Supply Model\":\"Seasonic PRIME TX-1600 ATX 3.0\",\"Wattage\":\"1600 W\",\"Efficiency Rating\":\"80 PLUS Titanium\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":559.99}'),
('psu', 3, 14, 'ProductsData/Power_Supply/PSU.xlsx', 10, '{\"ID\":3,\"Power Supply Model\":\"be quiet! Dark Power Pro 13 1600W\",\"Wattage\":\"1600 W\",\"Efficiency Rating\":\"80 PLUS Titanium\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Silent Wings Frameless\",\"Price (USD)\":449.99}'),
('psu', 4, 15, 'ProductsData/Power_Supply/PSU.xlsx', 11, '{\"ID\":4,\"Power Supply Model\":\"ASUS ROG Thor 1200W Platinum II\",\"Wattage\":\"1200 W\",\"Efficiency Rating\":\"80 PLUS Platinum\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Dual Ball Bearing\",\"Price (USD)\":359.99}'),
('psu', 5, 16, 'ProductsData/Power_Supply/PSU.xlsx', 12, '{\"ID\":5,\"Power Supply Model\":\"Corsair RM1000x Shift 1000W Gold\",\"Wattage\":\"1000 W\",\"Efficiency Rating\":\"80 PLUS Gold\",\"Modularity\":\"Full Modular (Side Connect)\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":209.99}'),
('psu', 6, 17, 'ProductsData/Power_Supply/PSU.xlsx', 13, '{\"ID\":6,\"Power Supply Model\":\"MSI MEG Ai1300P PCIE5 1300W\",\"Wattage\":\"1300 W\",\"Efficiency Rating\":\"80 PLUS Platinum\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Hydro-Dynamic Bearing\",\"Price (USD)\":319.99}'),
('psu', 7, 18, 'ProductsData/Power_Supply/PSU.xlsx', 14, '{\"ID\":7,\"Power Supply Model\":\"Thermaltake Toughpower GF3 1200W\",\"Wattage\":\"1200 W\",\"Efficiency Rating\":\"80 PLUS Gold\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":229.99}'),
('psu', 8, 19, 'ProductsData/Power_Supply/PSU.xlsx', 15, '{\"ID\":8,\"Power Supply Model\":\"EVGA SuperNOVA 1300 P+ 1300W\",\"Wattage\":\"1300 W\",\"Efficiency Rating\":\"80 PLUS Platinum\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 2.52\",\"Fan Bearing\":\"Double Ball Bearing\",\"Price (USD)\":279.99}'),
('psu', 9, 20, 'ProductsData/Power_Supply/PSU.xlsx', 16, '{\"ID\":9,\"Power Supply Model\":\"Seasonic Focus GX-1000 ATX 3.0\",\"Wattage\":\"1000 W\",\"Efficiency Rating\":\"80 PLUS Gold\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":179.99}'),
('psu', 10, 21, 'ProductsData/Power_Supply/PSU.xlsx', 17, '{\"ID\":10,\"Power Supply Model\":\"Corsair HX1500i 1500W Platinum\",\"Wattage\":\"1500 W\",\"Efficiency Rating\":\"80 PLUS Platinum\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":399.99}'),
('psu', 11, 22, 'ProductsData/Power_Supply/PSU.xlsx', 18, '{\"ID\":11,\"Power Supply Model\":\"ASUS ROG Loki SFX-L 1000W Platinum\",\"Wattage\":\"1000 W\",\"Efficiency Rating\":\"80 PLUS Platinum\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"SFX-L \\/ ATX 3.0\",\"Fan Bearing\":\"Dual Ball Bearing\",\"Price (USD)\":269.99}'),
('psu', 12, 23, 'ProductsData/Power_Supply/PSU.xlsx', 19, '{\"ID\":12,\"Power Supply Model\":\"be quiet! Straight Power 12 1200W\",\"Wattage\":\"1200 W\",\"Efficiency Rating\":\"80 PLUS Platinum\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Silent Wings 135mm\",\"Price (USD)\":249.99}'),
('psu', 13, 24, 'ProductsData/Power_Supply/PSU.xlsx', 20, '{\"ID\":13,\"Power Supply Model\":\"Cooler Master V850 Gold i Multi 850W\",\"Wattage\":\"850 W\",\"Efficiency Rating\":\"80 PLUS Gold\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":159.99}'),
('psu', 14, 25, 'ProductsData/Power_Supply/PSU.xlsx', 21, '{\"ID\":14,\"Power Supply Model\":\"SilverStone Hela 1200R Platinum\",\"Wattage\":\"1200 W\",\"Efficiency Rating\":\"80 PLUS Platinum\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":289.99}'),
('psu', 15, 26, 'ProductsData/Power_Supply/PSU.xlsx', 22, '{\"ID\":15,\"Power Supply Model\":\"Super Flower Leadex VII Gold 1000W\",\"Wattage\":\"1000 W\",\"Efficiency Rating\":\"80 PLUS Gold\",\"Modularity\":\"Full Modular\",\"ATX \\/ PCIe Standard\":\"ATX 3.0 \\/ PCIe 5.0\",\"Fan Bearing\":\"Fluid Dynamic Bearing\",\"Price (USD)\":189.99}'),
('storage', 1, 13, 'ProductsData/Storage/ROM.xlsx', 8, '{\"ID\":1,\"Storage Product Name\":\"Crucial T700 4TB PCIe 5.0 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 5.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (Heatsink)\",\"Capacity\":\"4 TB\",\"Max Read Speed\":\"12,400 MB\\/s\",\"Max Write Speed\":\"11,800 MB\\/s\",\"Price (USD)\":499.99}'),
('storage', 2, 14, 'ProductsData/Storage/ROM.xlsx', 9, '{\"ID\":2,\"Storage Product Name\":\"Samsung 990 PRO 4TB PCIe 4.0 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 4.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (Heatsink)\",\"Capacity\":\"4 TB\",\"Max Read Speed\":\"7,450 MB\\/s\",\"Max Write Speed\":\"6,900 MB\\/s\",\"Price (USD)\":349.99}'),
('storage', 3, 15, 'ProductsData/Storage/ROM.xlsx', 10, '{\"ID\":3,\"Storage Product Name\":\"Corsair MP700 PRO 2TB PCIe 5.0 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 5.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (Air Cooled)\",\"Capacity\":\"2 TB\",\"Max Read Speed\":\"12,400 MB\\/s\",\"Max Write Speed\":\"10,000 MB\\/s\",\"Price (USD)\":289.99}'),
('storage', 4, 16, 'ProductsData/Storage/ROM.xlsx', 11, '{\"ID\":4,\"Storage Product Name\":\"WD_BLACK SN850X 4TB NVMe M.2 Gaming SSD\",\"Interface \\/ Bus\":\"PCIe Gen 4.0 x4, NVMe 1.4\",\"Form Factor\":\"M.2 2280 (Heatsink)\",\"Capacity\":\"4 TB\",\"Max Read Speed\":\"7,300 MB\\/s\",\"Max Write Speed\":\"6,600 MB\\/s\",\"Price (USD)\":309.99}'),
('storage', 5, 17, 'ProductsData/Storage/ROM.xlsx', 12, '{\"ID\":5,\"Storage Product Name\":\"Sabrent Rocket 5 2TB PCIe 5.0 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 5.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (Heatsink)\",\"Capacity\":\"2 TB\",\"Max Read Speed\":\"14,000 MB\\/s\",\"Max Write Speed\":\"12,000 MB\\/s\",\"Price (USD)\":339.99}'),
('storage', 6, 18, 'ProductsData/Storage/ROM.xlsx', 13, '{\"ID\":6,\"Storage Product Name\":\"Gigabyte AORUS Gen5 12000 SSD 2TB\",\"Interface \\/ Bus\":\"PCIe Gen 5.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (M.2 Thermal Guard)\",\"Capacity\":\"2 TB\",\"Max Read Speed\":\"12,000 MB\\/s\",\"Max Write Speed\":\"9,500 MB\\/s\",\"Price (USD)\":259.99}'),
('storage', 7, 19, 'ProductsData/Storage/ROM.xlsx', 14, '{\"ID\":7,\"Storage Product Name\":\"Seagate FireCuda 530 4TB NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 4.0 x4, NVMe 1.4\",\"Form Factor\":\"M.2 2280 (Heatsink)\",\"Capacity\":\"4 TB\",\"Max Read Speed\":\"7,300 MB\\/s\",\"Max Write Speed\":\"6,900 MB\\/s\",\"Price (USD)\":369.99}'),
('storage', 8, 20, 'ProductsData/Storage/ROM.xlsx', 15, '{\"ID\":8,\"Storage Product Name\":\"TeamGroup T-Force Z540 2TB PCIe 5.0 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 5.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (Graphene Heatsink)\",\"Capacity\":\"2 TB\",\"Max Read Speed\":\"12,400 MB\\/s\",\"Max Write Speed\":\"11,800 MB\\/s\",\"Price (USD)\":249.99}'),
('storage', 9, 21, 'ProductsData/Storage/ROM.xlsx', 16, '{\"ID\":9,\"Storage Product Name\":\"Kingston KC3000 4TB PCIe 4.0 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 4.0 x4, NVMe 1.4\",\"Form Factor\":\"M.2 2280\",\"Capacity\":\"4 TB\",\"Max Read Speed\":\"7,000 MB\\/s\",\"Max Write Speed\":\"7,000 MB\\/s\",\"Price (USD)\":329.99}'),
('storage', 10, 22, 'ProductsData/Storage/ROM.xlsx', 17, '{\"ID\":10,\"Storage Product Name\":\"Solidigm P44 Pro 2TB PCIe 4.0 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 4.0 x4, NVMe 1.4\",\"Form Factor\":\"M.2 2280\",\"Capacity\":\"2 TB\",\"Max Read Speed\":\"7,000 MB\\/s\",\"Max Write Speed\":\"6,500 MB\\/s\",\"Price (USD)\":169.99}'),
('storage', 11, 23, 'ProductsData/Storage/ROM.xlsx', 18, '{\"ID\":11,\"Storage Product Name\":\"Samsung 870 EVO 4TB SATA III 2.5\\\" SSD\",\"Interface \\/ Bus\":\"SATA III 6 Gb\\/s\",\"Form Factor\":\"2.5-inch Internal\",\"Capacity\":\"4 TB\",\"Max Read Speed\":\"560 MB\\/s\",\"Max Write Speed\":\"530 MB\\/s\",\"Price (USD)\":299.99}'),
('storage', 12, 24, 'ProductsData/Storage/ROM.xlsx', 19, '{\"ID\":12,\"Storage Product Name\":\"WD Gold 22TB Enterprise SATA HDD\",\"Interface \\/ Bus\":\"SATA III 6 Gb\\/s\",\"Form Factor\":\"3.5-inch HDD\",\"Capacity\":\"22 TB\",\"Max Read Speed\":\"291 MB\\/s\",\"Max Write Speed\":\"291 MB\\/s\",\"Price (USD)\":499.99}'),
('storage', 13, 25, 'ProductsData/Storage/ROM.xlsx', 20, '{\"ID\":13,\"Storage Product Name\":\"Seagate IronWolf Pro 20TB NAS HDD\",\"Interface \\/ Bus\":\"SATA III 6 Gb\\/s\",\"Form Factor\":\"3.5-inch HDD\",\"Capacity\":\"20 TB\",\"Max Read Speed\":\"285 MB\\/s\",\"Max Write Speed\":\"285 MB\\/s\",\"Price (USD)\":399.99}'),
('storage', 14, 26, 'ProductsData/Storage/ROM.xlsx', 21, '{\"ID\":14,\"Storage Product Name\":\"Crucial T500 2TB Gen4 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 4.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (Heatsink)\",\"Capacity\":\"2 TB\",\"Max Read Speed\":\"7,400 MB\\/s\",\"Max Write Speed\":\"7,000 MB\\/s\",\"Price (USD)\":154.99}'),
('storage', 15, 27, 'ProductsData/Storage/ROM.xlsx', 22, '{\"ID\":15,\"Storage Product Name\":\"Lexar NM790 4TB PCIe 4.0 NVMe M.2 SSD\",\"Interface \\/ Bus\":\"PCIe Gen 4.0 x4, NVMe 2.0\",\"Form Factor\":\"M.2 2280 (Heatsink)\",\"Capacity\":\"4 TB\",\"Max Read Speed\":\"7,400 MB\\/s\",\"Max Write Speed\":\"6,500 MB\\/s\",\"Price (USD)\":249.99}');

-- --------------------------------------------------------

--
-- Table structure for table `psu`
--

CREATE TABLE `psu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `wattage` varchar(20) DEFAULT NULL,
  `wattage_watts` int(10) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `rating` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `modularity` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `psu`
--

INSERT INTO `psu` (`id`, `name`, `price`, `wattage`, `wattage_watts`, `stock`, `status`, `rating`, `brand`, `image_url`, `modularity`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgePower 650 W Bronze', 79.99, '650 W', 650, 10, 'inactive', '80+ Bronze', 'ForgePower', 'placeholder.png', 'Semi-modular', NULL, NULL, '2026-09-25 13:54:20'),
(2, 'ForgePower 850 W Gold', 129.99, '850 W', 850, 8, 'inactive', '80+ Gold', 'ForgePower', 'placeholder.png', 'Fully modular', NULL, NULL, '2026-09-25 13:54:20'),
(3, 'ForgePower 550 W Bronze', 59.99, '550 W', 550, 16, 'inactive', '80+ Bronze', 'ForgePower', 'placeholder.png', 'Non-modular', NULL, NULL, '2026-09-25 13:54:20'),
(4, 'ForgePower 750 W Gold', 109.99, '750 W', 750, 12, 'inactive', '80+ Gold', 'ForgePower', 'placeholder.png', 'Fully modular', NULL, NULL, '2026-09-25 13:54:20'),
(5, 'ForgePower 1000 W Gold', 179.99, '1000 W', 1000, 6, 'inactive', '80+ Gold', 'ForgePower', 'placeholder.png', 'Fully modular', NULL, NULL, '2026-09-25 13:54:20'),
(6, 'ForgePower 1200 W Platinum', 249.99, '1200 W', 1200, 3, 'inactive', '80+ Platinum', 'ForgePower', 'placeholder.png', 'Fully modular', NULL, NULL, '2026-09-25 13:54:20'),
(7, 'ForgePower 450 W Bronze', 44.99, '450 W', 450, 20, 'inactive', '80+ Bronze', 'ForgePower', 'placeholder.png', 'Non-modular', NULL, NULL, '2026-09-25 13:54:20'),
(12, 'Corsair AX1600i 1600W Titanium', 609.99, '1600 W', 1600, 10, 'active', '80 PLUS Titanium', 'Corsair', 'productsdata-psu-1.avif', 'Full Modular', 'Wattage: 1600 W\nEfficiency Rating: 80 PLUS Titanium\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(13, 'Seasonic PRIME TX-1600 ATX 3.0', 559.99, '1600 W', 1600, 10, 'active', '80 PLUS Titanium', 'Seasonic', 'productsdata-psu-2-transparent.png', 'Full Modular', 'Wattage: 1600 W\nEfficiency Rating: 80 PLUS Titanium\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(14, 'be quiet! Dark Power Pro 13 1600W', 449.99, '1600 W', 1600, 10, 'active', '80 PLUS Titanium', 'be quiet!', 'productsdata-psu-3-transparent.png', 'Full Modular', 'Wattage: 1600 W\nEfficiency Rating: 80 PLUS Titanium\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Silent Wings Frameless', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(15, 'ASUS ROG Thor 1200W Platinum II', 359.99, '1200 W', 1200, 10, 'active', '80 PLUS Platinum', 'ASUS', 'productsdata-psu-4.png', 'Full Modular', 'Wattage: 1200 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Dual Ball Bearing', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(16, 'Corsair RM1000x Shift 1000W Gold', 209.99, '1000 W', 1000, 10, 'active', '80 PLUS Gold', 'Corsair', 'productsdata-psu-5.avif', 'Full Modular (Side Connect)', 'Wattage: 1000 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular (Side Connect)\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(17, 'MSI MEG Ai1300P PCIE5 1300W', 319.99, '1300 W', 1300, 10, 'active', '80 PLUS Platinum', 'MSI', 'productsdata-psu-6.png', 'Full Modular', 'Wattage: 1300 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Hydro-Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(18, 'Thermaltake Toughpower GF3 1200W', 229.99, '1200 W', 1200, 10, 'active', '80 PLUS Gold', 'Thermaltake', 'productsdata-psu-7-transparent.png', 'Full Modular', 'Wattage: 1200 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(19, 'EVGA SuperNOVA 1300 P+ 1300W', 279.99, '1300 W', 1300, 10, 'active', '80 PLUS Platinum', 'EVGA', 'productsdata-psu-8-transparent.png', 'Full Modular', 'Wattage: 1300 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 2.52\nFan Bearing: Double Ball Bearing', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(20, 'Seasonic Focus GX-1000 ATX 3.0', 179.99, '1000 W', 1000, 10, 'active', '80 PLUS Gold', 'Seasonic', 'productsdata-psu-9-transparent.png', 'Full Modular', 'Wattage: 1000 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(21, 'Corsair HX1500i 1500W Platinum', 399.99, '1500 W', 1500, 10, 'active', '80 PLUS Platinum', 'Corsair', 'productsdata-psu-10.avif', 'Full Modular', 'Wattage: 1500 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(22, 'ASUS ROG Loki SFX-L 1000W Platinum', 269.99, '1000 W', 1000, 10, 'active', '80 PLUS Platinum', 'ASUS', 'productsdata-psu-11.png', 'Full Modular', 'Wattage: 1000 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: SFX-L / ATX 3.0\nFan Bearing: Dual Ball Bearing', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(23, 'be quiet! Straight Power 12 1200W', 249.99, '1200 W', 1200, 10, 'active', '80 PLUS Platinum', 'be quiet!', 'productsdata-psu-12-transparent.png', 'Full Modular', 'Wattage: 1200 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Silent Wings 135mm', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'Cooler Master V850 Gold i Multi 850W', 159.99, '850 W', 850, 10, 'active', '80 PLUS Gold', 'Cooler Master', 'productsdata-psu-13-transparent.png', 'Full Modular', 'Wattage: 850 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(25, 'SilverStone Hela 1200R Platinum', 289.99, '1200 W', 1200, 10, 'active', '80 PLUS Platinum', 'SilverStone', 'productsdata-psu-14-transparent.png', 'Full Modular', 'Wattage: 1200 W\nEfficiency Rating: 80 PLUS Platinum\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(26, 'Super Flower Leadex VII Gold 1000W', 189.99, '1000 W', 1000, 10, 'active', '80 PLUS Gold', 'Super Flower', 'productsdata-psu-15-transparent.png', 'Full Modular', 'Wattage: 1000 W\nEfficiency Rating: 80 PLUS Gold\nModularity: Full Modular\nATX / PCIe Standard: ATX 3.0 / PCIe 5.0\nFan Bearing: Fluid Dynamic Bearing', '2026-09-25 13:52:50', '2026-09-26 22:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `saved_builds`
--

CREATE TABLE `saved_builds` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `build_name` varchar(100) DEFAULT 'My Custom Build',
  `build_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`build_data`)),
  `share_token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_builds`
--

INSERT INTO `saved_builds` (`id`, `user_id`, `build_name`, `build_data`, `share_token`, `created_at`) VALUES
(34, 1, 'MY PC 2', '{\"cpu\":8,\"mb\":9,\"memory\":2,\"gpu\":3,\"storage\":1,\"cooling\":6,\"psu\":6,\"case_box\":6}', 'ecd2b4923fabfd52a51f17fcc8176801c2754156a134985de8c0c2a5b3258a40', '2026-09-18 07:58:13'),
(35, 1, 'MY PC', '{\"cpu\":8,\"mb\":9,\"memory\":2,\"gpu\":3,\"storage\":1,\"cooling\":6,\"psu\":6,\"case_box\":6}', '1adfa93561386316328335ae55441b311bd19c3b759edbe476b1f5fdebfc9259', '2026-09-18 07:58:42'),
(36, 1, 'MY PC 2', '{\"cpu\":8,\"mb\":9,\"memory\":2,\"gpu\":3,\"storage\":1,\"cooling\":6,\"psu\":6,\"case_box\":6}', 'e6260d9e2bdcd3ac8c0442b2ffbc2e9126bc24da0a50bc46c15677a907bee632', '2026-09-18 13:38:58'),
(37, 1, 'MY PC', '{\"cpu\":8,\"mb\":9,\"memory\":2,\"gpu\":3,\"storage\":1,\"cooling\":6,\"psu\":6,\"case_box\":6}', '74452827153c772774e9065b404361435d58a2b021ac9458e587fa09e274820c', '2026-09-18 13:39:00'),
(38, 1, 'MY PC 2', '{\"cpu\":8,\"mb\":9,\"memory\":2,\"gpu\":3,\"storage\":1,\"cooling\":6,\"psu\":6,\"case_box\":6}', '6b6f3f594e0dcd427af89f128be11099e844912966f5f80edac469384e617fda', '2026-09-18 13:39:01');

-- --------------------------------------------------------

--
-- Table structure for table `storage`
--

CREATE TABLE `storage` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `interface` enum('SATA','NVMe') DEFAULT NULL,
  `power_watts` int(10) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `capacity` varchar(20) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `read_speed` varchar(20) DEFAULT NULL,
  `write_speed` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `storage`
--

INSERT INTO `storage` (`id`, `name`, `price`, `type`, `interface`, `power_watts`, `stock`, `status`, `capacity`, `brand`, `image_url`, `read_speed`, `write_speed`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ForgeDrive 1 TB NVMe', 69.99, 'SSD', 'NVMe', 5, 18, 'inactive', '1 TB', 'ForgeDrive', 'demo-storage.svg', '7000 MB/s', '6000 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(2, 'ForgeDrive 2 TB SATA SSD', 89.99, 'SSD', 'SATA', 5, 14, 'inactive', '2 TB', 'ForgeDrive', 'demo-storage.svg', '560 MB/s', '520 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(3, 'ForgeDrive 500 GB NVMe', 39.99, 'SSD', 'NVMe', 4, 25, 'inactive', '500 GB', 'ForgeDrive', 'demo-storage.svg', '3500 MB/s', '3000 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(4, 'ForgeDrive 2 TB NVMe Pro', 129.99, 'SSD', 'NVMe', 6, 13, 'inactive', '2 TB', 'ForgeDrive', 'demo-storage.svg', '7400 MB/s', '6800 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(5, 'ForgeDrive 4 TB NVMe', 249.99, 'SSD', 'NVMe', 7, 6, 'inactive', '4 TB', 'ForgeDrive', 'demo-storage.svg', '7000 MB/s', '6500 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(6, 'ForgeDrive 1 TB SATA SSD', 54.99, 'SSD', 'SATA', 4, 22, 'inactive', '1 TB', 'ForgeDrive', 'demo-storage.svg', '560 MB/s', '520 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(7, 'ForgeDrive 2 TB HDD', 49.99, 'HDD', 'SATA', 7, 20, 'inactive', '2 TB', 'ForgeDrive', 'demo-storage.svg', '180 MB/s', '170 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(8, 'ForgeDrive 4 TB HDD', 79.99, 'HDD', 'SATA', 8, 15, 'inactive', '4 TB', 'ForgeDrive', 'demo-storage.svg', '190 MB/s', '180 MB/s', NULL, NULL, '2026-09-25 13:52:50'),
(13, 'Crucial T700 4TB PCIe 5.0 NVMe M.2 SSD', 499.99, 'SSD', 'NVMe', NULL, 10, 'active', '4 TB', 'Crucial', 'productsdata-storage-1-transparent.png', '12,400 MB/s', '11,800 MB/s', 'Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 12,400 MB/s\nMax Write Speed: 11,800 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(14, 'Samsung 990 PRO 4TB PCIe 4.0 NVMe M.2 SSD', 349.99, 'SSD', 'NVMe', NULL, 10, 'active', '4 TB', 'Samsung', 'productsdata-storage-2-transparent.png', '7,450 MB/s', '6,900 MB/s', 'Interface / Bus: PCIe Gen 4.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,450 MB/s\nMax Write Speed: 6,900 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(15, 'Corsair MP700 PRO 2TB PCIe 5.0 NVMe M.2 SSD', 289.99, 'SSD', 'NVMe', NULL, 10, 'active', '2 TB', 'Corsair', 'productsdata-storage-3.avif', '12,400 MB/s', '10,000 MB/s', 'Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Air Cooled)\nCapacity: 2 TB\nMax Read Speed: 12,400 MB/s\nMax Write Speed: 10,000 MB/s', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(16, 'WD_BLACK SN850X 4TB NVMe M.2 Gaming SSD', 309.99, 'SSD', 'NVMe', NULL, 10, 'active', '4 TB', 'WD', 'productsdata-storage-4.png', '7,300 MB/s', '6,600 MB/s', 'Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,300 MB/s\nMax Write Speed: 6,600 MB/s', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(17, 'Sabrent Rocket 5 2TB PCIe 5.0 NVMe M.2 SSD', 339.99, 'SSD', 'NVMe', NULL, 10, 'active', '2 TB', 'Sabrent', 'productsdata-storage-5-transparent.png', '14,000 MB/s', '12,000 MB/s', 'Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 2 TB\nMax Read Speed: 14,000 MB/s\nMax Write Speed: 12,000 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(18, 'Gigabyte AORUS Gen5 12000 SSD 2TB', 259.99, 'SSD', 'NVMe', NULL, 10, 'active', '2 TB', 'GIGABYTE', 'productsdata-storage-6.png', '12,000 MB/s', '9,500 MB/s', 'Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (M.2 Thermal Guard)\nCapacity: 2 TB\nMax Read Speed: 12,000 MB/s\nMax Write Speed: 9,500 MB/s', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(19, 'Seagate FireCuda 530 4TB NVMe M.2 SSD', 369.99, 'SSD', 'NVMe', NULL, 10, 'active', '4 TB', 'Seagate', 'productsdata-storage-7-transparent.png', '7,300 MB/s', '6,900 MB/s', 'Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,300 MB/s\nMax Write Speed: 6,900 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(20, 'TeamGroup T-Force Z540 2TB PCIe 5.0 SSD', 249.99, 'SSD', 'NVMe', NULL, 10, 'active', '2 TB', 'TeamGroup', 'productsdata-storage-8-transparent.png', '12,400 MB/s', '11,800 MB/s', 'Interface / Bus: PCIe Gen 5.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Graphene Heatsink)\nCapacity: 2 TB\nMax Read Speed: 12,400 MB/s\nMax Write Speed: 11,800 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(21, 'Kingston KC3000 4TB PCIe 4.0 NVMe M.2 SSD', 329.99, 'SSD', 'NVMe', NULL, 10, 'active', '4 TB', 'Kingston', 'productsdata-storage-9-transparent.png', '7,000 MB/s', '7,000 MB/s', 'Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280\nCapacity: 4 TB\nMax Read Speed: 7,000 MB/s\nMax Write Speed: 7,000 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(22, 'Solidigm P44 Pro 2TB PCIe 4.0 NVMe M.2 SSD', 169.99, 'SSD', 'NVMe', NULL, 10, 'active', '2 TB', 'Solidigm', 'productsdata-storage-10.avif', '7,000 MB/s', '6,500 MB/s', 'Interface / Bus: PCIe Gen 4.0 x4, NVMe 1.4\nForm Factor: M.2 2280\nCapacity: 2 TB\nMax Read Speed: 7,000 MB/s\nMax Write Speed: 6,500 MB/s', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(23, 'Samsung 870 EVO 4TB SATA III 2.5\" SSD', 299.99, 'SSD', 'SATA', NULL, 10, 'active', '4 TB', 'Samsung', 'productsdata-storage-11-transparent.png', '560 MB/s', '530 MB/s', 'Interface / Bus: SATA III 6 Gb/s\nForm Factor: 2.5-inch Internal\nCapacity: 4 TB\nMax Read Speed: 560 MB/s\nMax Write Speed: 530 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(24, 'WD Gold 22TB Enterprise SATA HDD', 499.99, 'HDD', 'SATA', NULL, 10, 'active', '22 TB', 'WD', 'productsdata-storage-12.png', '291 MB/s', '291 MB/s', 'Interface / Bus: SATA III 6 Gb/s\nForm Factor: 3.5-inch HDD\nCapacity: 22 TB\nMax Read Speed: 291 MB/s\nMax Write Speed: 291 MB/s', '2026-09-25 13:52:50', '2026-09-25 14:13:08'),
(25, 'Seagate IronWolf Pro 20TB NAS HDD', 399.99, 'HDD', 'SATA', NULL, 10, 'active', '20 TB', 'Seagate', 'productsdata-storage-13-transparent.png', '285 MB/s', '285 MB/s', 'Interface / Bus: SATA III 6 Gb/s\nForm Factor: 3.5-inch HDD\nCapacity: 20 TB\nMax Read Speed: 285 MB/s\nMax Write Speed: 285 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(26, 'Crucial T500 2TB Gen4 NVMe M.2 SSD', 154.99, 'SSD', 'NVMe', NULL, 10, 'active', '2 TB', 'Crucial', 'productsdata-storage-14-transparent.png', '7,400 MB/s', '7,000 MB/s', 'Interface / Bus: PCIe Gen 4.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 2 TB\nMax Read Speed: 7,400 MB/s\nMax Write Speed: 7,000 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09'),
(27, 'Lexar NM790 4TB PCIe 4.0 NVMe M.2 SSD', 249.99, 'SSD', 'NVMe', NULL, 10, 'active', '4 TB', 'Lexar', 'productsdata-storage-15-transparent.png', '7,400 MB/s', '6,500 MB/s', 'Interface / Bus: PCIe Gen 4.0 x4, NVMe 2.0\nForm Factor: M.2 2280 (Heatsink)\nCapacity: 4 TB\nMax Read Speed: 7,400 MB/s\nMax Write Speed: 6,500 MB/s', '2026-09-25 13:52:50', '2026-09-26 22:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `store_settings`
--

CREATE TABLE `store_settings` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `store_name` varchar(100) NOT NULL DEFAULT 'PCForge',
  `store_email` varchar(160) NOT NULL DEFAULT '',
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `low_stock_threshold` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `maintenance_mode` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `store_settings`
--

INSERT INTO `store_settings` (`id`, `store_name`, `store_email`, `currency`, `low_stock_threshold`, `maintenance_mode`, `updated_at`) VALUES
(1, 'PCForge', '', 'USD', 5, 0, '2026-09-22 04:57:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'zwe_thiha_zaw', 'zwethihazaw08@gmail.com', '$2y$10$QzaMWv05Eo0Y0y8x6xdZtuL07aAa5Csho59yoKB2Ha8SZyf600YlW', 'admin', 'active', '2026-09-17 18:41:20'),
(2, 'Tony', 'zwethihazaw06@gmail.com', '$2y$10$V8tmqIW8fdzWzfwFYVRYye9BZ6SOXY1iOEqV0JLWmMF1182VTvaYC', 'customer', 'active', '2026-09-17 19:00:49'),
(30, 'Sample Customer 1', 'dashboard.sample.1@example.invalid', '$2y$10$je66q9f8sLyktt533SJ/O.PbxYaj9XcSfYw2Z8jfXjXGU9by7L.PK', 'customer', 'disabled', '2026-04-01 03:30:00'),
(31, 'Sample Customer 2', 'dashboard.sample.2@example.invalid', '$2y$10$d630.P4U83Yupl9xHXZawuvTxbpfN83w2yYBDlxma67mSdgqbMDxe', 'customer', 'disabled', '2026-04-01 03:30:00'),
(32, 'Sample Customer 3', 'dashboard.sample.3@example.invalid', '$2y$10$Y0ask/r2jvao0cZn0xs2BOLzEKYgNWpGAM6aEVHoetUiSMNBEH4GO', 'customer', 'disabled', '2026-04-01 03:30:00'),
(33, 'Sample Customer 4', 'dashboard.sample.4@example.invalid', '$2y$10$Ud7yWZL1cXAkEedSahmuyOFPfvaxNG5KI8lDPqxQ0XPjbgTP55xMK', 'customer', 'disabled', '2026-04-01 03:30:00'),
(34, 'Sample Customer 5', 'dashboard.sample.5@example.invalid', '$2y$10$PdEDy0ne/VvvlxS8kWrZ3OFDH8LyAArBV37yCTJ0ClM26.IV/IAaG', 'customer', 'disabled', '2026-04-01 03:30:00'),
(35, 'Sample Customer 6', 'dashboard.sample.6@example.invalid', '$2y$10$9IRz0nHkgl.Q8tR36P7X/eUE7Mnb8QJoxqXXK4SuCyDrXucznt9xm', 'customer', 'disabled', '2026-04-01 03:30:00'),
(36, 'Sample Customer 7', 'dashboard.sample.7@example.invalid', '$2y$10$dVyYOBjeu/QBNShhD6oaB.7vDukJGAq870LBhf8nPhtDIWMRmPbmS', 'customer', 'disabled', '2026-04-01 03:30:00'),
(37, 'Sample Customer 8', 'dashboard.sample.8@example.invalid', '$2y$10$A64rv4q4m9kA/Nm7CXJoIupunc7lSQEqJxWd14fE56L8.WZRXOWaW', 'customer', 'disabled', '2026-04-01 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_shipping_details`
--

CREATE TABLE `user_shipping_details` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(160) NOT NULL,
  `address_line2` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL,
  `region` varchar(100) NOT NULL DEFAULT '',
  `postal_code` varchar(30) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `case_box`
--
ALTER TABLE `case_box`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `case_motherboard_support`
--
ALTER TABLE `case_motherboard_support`
  ADD PRIMARY KEY (`case_id`,`form_factor`);

--
-- Indexes for table `cooling`
--
ALTER TABLE `cooling`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cooling_socket_support`
--
ALTER TABLE `cooling_socket_support`
  ADD PRIMARY KEY (`cooling_id`,`socket`);

--
-- Indexes for table `cpu`
--
ALTER TABLE `cpu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fans`
--
ALTER TABLE `fans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gpu`
--
ALTER TABLE `gpu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mb`
--
ALTER TABLE `mb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memory`
--
ALTER TABLE `memory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitor`
--
ALTER TABLE `monitor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `orders_user_created` (`user_id`,`created_at`),
  ADD KEY `orders_status_created` (`status`,`created_at`),
  ADD KEY `orders_created` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order` (`order_id`),
  ADD KEY `order_items_product` (`category`,`product_id`);

--
-- Indexes for table `product_data_sources`
--
ALTER TABLE `product_data_sources`
  ADD PRIMARY KEY (`category`,`source_id`),
  ADD UNIQUE KEY `source_product` (`category`,`product_id`);

--
-- Indexes for table `psu`
--
ALTER TABLE `psu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_builds`
--
ALTER TABLE `saved_builds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `share_token` (`share_token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_settings`
--
ALTER TABLE `store_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_shipping_details`
--
ALTER TABLE `user_shipping_details`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `case_box`
--
ALTER TABLE `case_box`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `cooling`
--
ALTER TABLE `cooling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `cpu`
--
ALTER TABLE `cpu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `fans`
--
ALTER TABLE `fans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `gpu`
--
ALTER TABLE `gpu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `mb`
--
ALTER TABLE `mb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `memory`
--
ALTER TABLE `memory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `monitor`
--
ALTER TABLE `monitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psu`
--
ALTER TABLE `psu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `saved_builds`
--
ALTER TABLE `saved_builds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `storage`
--
ALTER TABLE `storage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `case_motherboard_support`
--
ALTER TABLE `case_motherboard_support`
  ADD CONSTRAINT `case_motherboard_support_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case_box` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cooling_socket_support`
--
ALTER TABLE `cooling_socket_support`
  ADD CONSTRAINT `cooling_socket_support_ibfk_1` FOREIGN KEY (`cooling_id`) REFERENCES `cooling` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `saved_builds`
--
ALTER TABLE `saved_builds`
  ADD CONSTRAINT `saved_builds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_shipping_details`
--
ALTER TABLE `user_shipping_details`
  ADD CONSTRAINT `shipping_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
