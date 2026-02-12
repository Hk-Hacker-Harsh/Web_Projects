-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql300.infinityfree.com
-- Generation Time: Feb 12, 2026 at 08:02 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41027982_ecoproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image_name`, `title`, `link`, `status`, `created_at`) VALUES
(1, '1770047495_1.png', '', NULL, 1, '2026-02-02 15:51:35'),
(2, '1770047516_2.png', '', NULL, 1, '2026-02-02 15:51:56'),
(3, '1770047524_3.png', '', NULL, 1, '2026-02-02 15:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(5, 'Tech', '2026-02-01 03:22:36'),
(6, 'Fashion', '2026-02-01 03:22:41'),
(7, 'Hoddy', '2026-02-01 03:22:44'),
(8, 'Learning', '2026-02-01 03:22:48'),
(9, 'Fruits', '2026-02-01 03:23:00'),
(10, 'Vegetables', '2026-02-01 03:23:05'),
(11, 'Accessories', '2026-02-01 03:23:11'),
(12, 'Others', '2026-02-01 03:23:14'),
(1, 'Electronics', '2026-02-03 04:52:12'),
(2, 'Apparel', '2026-02-03 04:52:12'),
(3, 'Home & Garden', '2026-02-03 04:52:12');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `first_name`, `last_name`, `email`, `subject`, `message`, `created_at`) VALUES
(2, 'Harsh', 'K.', 'codingwithme7@gmail.com', 'General Inquiry', 'what you do??', '2026-02-01 04:41:56');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `expiry` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `value`, `expiry`) VALUES
(2, 'SAVE10', 'percentage', '10.00', '2026-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'cod',
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_address`, `payment_method`, `status`, `created_at`) VALUES
(6, 11, '3498.00', NULL, 'cod', 'delivered', '2026-02-01 04:32:02'),
(7, 11, '2499.00', NULL, 'cod', 'shipped', '2026-02-01 04:34:24'),
(8, 11, '199.00', NULL, 'cod', 'delivered', '2026-02-01 04:38:14'),
(9, 12, '718.20', NULL, 'cod', 'pending', '2026-02-01 05:26:38'),
(10, 13, '89.10', NULL, 'cod', 'pending', '2026-02-01 05:27:28'),
(25, 11, '45.00', NULL, 'stripe', 'paid', '2026-02-08 13:25:29'),
(24, 11, '12.00', NULL, 'stripe', 'paid', '2026-02-08 12:17:02'),
(23, 11, '1395.00', NULL, 'cod', 'pending', '2026-02-08 12:02:52'),
(15, 4, '10.80', NULL, 'stripe', 'paid', '2026-02-03 03:42:42'),
(17, 4, '12.00', NULL, 'cod', 'pending', '2026-02-03 03:44:57'),
(21, 4, '999.00', NULL, 'stripe', 'paid', '2026-02-03 05:02:12'),
(19, 4, '11.00', NULL, 'stripe', 'paid', '2026-02-03 03:47:09'),
(22, 11, '40.50', NULL, 'cod', 'pending', '2026-02-08 11:49:01'),
(26, 11, '45.00', 'Jaipur, Rajasthan 302012', 'cod', 'pending', '2026-02-08 13:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 8, 16, 1, '199.00'),
(2, 9, 20, 1, '499.00'),
(3, 9, 22, 1, '299.00'),
(4, 10, 21, 1, '99.00'),
(21, 26, 103, 1, '0.00'),
(20, 25, 103, 1, '0.00'),
(11, 15, 22, 1, '0.00'),
(19, 24, 22, 1, '0.00'),
(13, 17, 22, 1, '0.00'),
(18, 22, 103, 1, '0.00'),
(15, 19, 20, 1, '0.00'),
(17, 21, 100, 1, '999.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `vendor_id`, `category_id`, `name`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(1, 6, 5, 'Lenovo V15 Intel Core i3 13th Gen (16GB RAM/512GB SSD/Windows 11 Home', ' Processor: 13th Gen Intel Core I3-1315U processor | 6 Cores | 8 Threads | Speed Upto 4.5 Ghz | 10MB Cache | Memory: 16GB DDR4 RAM 3200 MHz, dual-channel capable upgradable up to 16GB | Storage: 512GB SSD M.2 upgradable up to 1TB\r\nOperating System: Preloaded Windows 11 Home SL with Lifetime Validity | Pre-installed software: Microsoft Office Home 2024\r\nDisplay: 15.6\" screen with (1920x1080) FHD Antiglare, 250nits display | Graphics: Intel UHD Graphics comes with DirectX 12.1 enables amazing graphics| Monitor Supports: Supports up to 3 independent displays\r\nPorts: 1x USB 2.0 | 1x USB 3.2 Gen 1 | 1x USB-C 3.2 Gen 1 (support data transfer, Power Delivery (20V only) and DisplayPort 1.2) | 1x HDMI 1.4b | 1x Ethernet RJ-45 (LAN Port) | 1x Headphone/microphone combo jack (3.5mm) | 1x Power Connector ', '200.00', 100, '1769916413_lap.jpg', '2026-02-01 03:26:53'),
(10, 6, 5, 'OMEN Laptop 13th Gen Intel Core I3-1315U processor | Speed Upto 4.5 Ghz | 16GB DDR4 RAM | 512GB SSD | Windows 11', 'AMD Ryzenâ„¢ 9 processor\r\nWindows 11 Home\r\n40.6 cm (16) diagonal, 2K, IPS, anti-glare, Low Blue Light, 165 Hz, 400 nits display\r\nNVIDIAÂ® GeForce RTXâ„¢ 5060 8GB\r\n24 GB DDR5-5200 RAM (Upgradeable)\r\n1 TB SSD Hard Drive', '350.00', 10, '1769916548_lap2.png', '2026-02-01 03:29:08'),
(11, 7, 6, 'ADRO Hoodies for Men | Printed Hoodie for Men | Cotton Hoodie | Mens Hoodies | Sweatshirt for Men | Hooded Hoodie', 'The Adro 100% Cotton Printed Hoodie for Men is a versatile and stylish piece that combines comfort with modern design. Perfect for any casual occasion, it is a must-have for every man\'s wardrobe. Material: Made from 100% high-quality cotton, this hoodie ensures a soft and breathable feel against the skin, making it perfect for year-round wear.\r\n\r\nHood: Equipped with an adjustable drawstring hood that provides additional warmth and a customizable fit, ensuring comfort in cooler weather. Pockets: Includes a spacious kangaroo pocket in the front, offering practical storage space for your essentials and a cozy place to warm your hands.', '15.00', 50, '1769917646_fas1.jpg', '2026-02-01 03:47:26'),
(12, 7, 6, 'Krystal D\'souza in Paula Blazer Set', 'The burgundy coord set features a chic sleeveless blazer paired with a matching crop top and a stylish skirt. The blazer offers a tailored silhouette, perfect for layering, while the crop top adds a contemporary touch. The coordinating skirt completes the ensemble, creating a sleek and polished look.', '25.00', 10, '1769917709_fas2.jpg', '2026-02-01 03:48:29'),
(13, 7, 6, 'The weight-free layer. The bomber comes with a ribbed hem and a classy metal zipper. ', 'Light & packable\r\nQuick-dry fabric\r\n4-way stretch\r\nClassic metal zipper\r\nRibbed hems\r\nComposition: Elastomultiester blend', '35.00', 15, '1769917872_fas3.jpg', '2026-02-01 03:51:12'),
(14, 5, 1, 'Sony PlayStationÂ®5 Digital Edition (slim) Console Video Game ', ' Integrated I/O: The custom integration of the PS5 console\'s systems lets creators pull data from the SSD so quickly that they can design games in ways never before possible. Ray Tracing: Immerse yourself in worlds with a new level of realism as rays of light are individually simulated, creating true-to-life shadows and reflections in supported PS5 games. 4K-TV Gaming: Play your favorite PS5 games on your stunning 4K TV.\r\n\r\nUp to 120fps with 120Hz output: Enjoy smooth and fluid high frame rate gameplay at up to 120fps for compatible games, with support for 120Hz output on 4K displays. HDR Technology: With an HDR TV, supported PS5 games display an unbelievably vibrant and lifelike range of colors. Tempest 3D AudioTech: Immerse yourself in soundscapes where it feels as if the sound comes from every direction. Your surroundings truly come alive with Tempest 3D AudioTech in supported games. ', '499.00', 15, '1769918399_PS.jpg', '2026-02-01 03:59:59'),
(15, 5, 9, 'Kiwi Green | Qty 3', 'All images are for representational purposes only. It is advised that you read the batch and manufacturing details, directions for use, allergen information, health and nutritional claims (wherever applicable), and other details mentioned on the label before consuming the product. For combo items, individual prices can be viewed on the page.', '9.00', 100, '1769918544_fru1.jpeg', '2026-02-01 04:02:24'),
(16, 5, 9, 'Tender Coconut', 'All images are for representational purposes only. It is advised that you read the batch and manufacturing details, directions for use, allergen information, health and nutritional claims (wherever applicable), and other details mentioned on the label before consuming the product. For combo items, individual prices can be viewed on the page.', '9.00', 99, '1769918603_fru2.jpeg', '2026-02-01 04:03:23'),
(17, 5, 10, 'Beans French | Qty 250g', 'Savor their mild flavor, perfect for salads, stir-fries, and side dishes that highlight their vibrant green color and crunch', '5.00', 100, '1769918776_veg1.jpeg', '2026-02-01 04:06:16'),
(18, 5, 10, 'Broccoli Florets (Approx. 200 g - 400 g) ', 'Broccoli is a nutritious and delicious vegetable that can be added to a diet in many ways. It can be eaten raw, as a side dish, or as an ingredient in various recipes. It can also be roasted, steamed, grilled, sauteed, stir-fried, or served as a soup. There is really no wrong way to eat broccoli, so feel free to get creative and try out classic recipes or invent your own!. So, go ahead, buy Broccoli online now!', '4.00', 50, '1769918879_veg2.webp', '2026-02-01 04:07:59'),
(19, 5, 7, 'Official Arduino Engineering Kit R2 with Arduino Nano', 'The Official Arduino Engineering Kit Rev2 is a comprehensive hands-on educational tool designed to teach and reinforce key concepts in control systems, mechatronics, and programming with MATLAB and Simulink. \r\n\r\nThe Arduino Nano kit includes all the necessary hardware and learning resources to build three innovative projects: a self-balancing motorcycle, a webcam-controlled rover, and a drawing robot.\r\n\r\nIt is ideal for engineering students, educators, and hobbyists alike, offering structured online tutorials and step-by-step instructions that support both individual and collaborative learningâ€”whether in classrooms or remote environments.\r\n\r\nTailored for integration into academic curricula, the Arduino Engineering Kit Rev 2 fosters experimentation and creativity while helping learners build strong foundational and advanced engineering skills.', '25.00', 100, '1769919056_hobb1.png', '2026-02-01 04:10:56'),
(20, 6, 11, '3 in 1 Charger for Amazon Fire Max 11 Original Adopter', ' ã€Certifiedã€‘: Premium construction and circuitry with standard, UL, FCC, CE, & RoHS certification protect your devices. Built with high-grade fireproof PC materials. The upgraded wall charger makes the power output more stable.\r\n\r\nã€Lightweight Durableã€‘: Compact and Easy to carry, Heat resistant and Anti-throw design, portable, stylish, easy to store. Simply plug in the USB cable, and plug the adapter into the wall. Suitable use for home, travel, office, and business trip, and 3-port design which meet your daily charging needs. ', '11.00', 99, '1769921593_acc1.jpg', '2026-02-01 04:53:13'),
(21, 5, 8, 'Oxford Mini English Dictionary | Extra Help with Spelling, Grammar and Vocabulary', 'Oxford English Mini Dictionary is small easy to carry and includes all words you need for everyday use.In the seventh edition definitions are much clearer and accessible than ever before.It provides extra help with spelling grammar and vocabulary.', '8.00', 99, '1769921767_learn1.jpg', '2026-02-01 04:56:07'),
(22, 6, 12, 'Kids Dancing Cactus Musical Toys', 'Type: Musical Toys\r\nColour: Green, whiteAge Group: 4 Year plus\r\nSet Contains:1 Musical Toy\r\nAssembly Required: No\r\nOperation Mode: Manual\r\n\r\nSize & Fit\r\n\r\nDimensions: 30.48 cm x 10 cm (Length x Width )\r\n\r\nMaterial & Care\r\n\r\nPlush \r\nHandle with Care', '12.00', 49, '1769921941_other1.jpg', '2026-02-01 04:59:01'),
(102, 7, 2, 'Cotton T-Shirt', '100% organic cotton', '19.99', 200, '1770095598_tshirts.webp', '2026-02-03 04:52:12'),
(103, 5, 3, 'Coffee Maker', 'Brews up to 12 cups', '45.00', 28, '1770095516_coffee.jpg', '2026-02-03 04:52:12'),
(100, 6, 5, 'Dummy Pro Laptop', 'Perfect for testing orders', '499.00', 10, '1770095633_laptop.jpg', '2026-02-03 05:02:12'),
(101, 6, 1, 'Smartphone X', 'Latest model with 5G', '199.99', 50, '1770095619_smartphone.jpg', '2026-02-03 04:52:12');

-- --------------------------------------------------------

--
-- Table structure for table `product_variations`
--

CREATE TABLE `product_variations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variation_name` varchar(50) DEFAULT NULL,
  `variation_value` varchar(50) DEFAULT NULL,
  `price_modifier` decimal(10,2) DEFAULT 0.00,
  `stock_qty` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product_variations`
--

INSERT INTO `product_variations` (`id`, `product_id`, `variation_name`, `variation_value`, `price_modifier`, `stock_qty`) VALUES
(65, 1, 'Storage', '512 GB', '15.00', 15),
(64, 1, 'Storage', '256 GB', '0.00', 10),
(45, 101, 'RAM', '8GB', '10.00', 10),
(44, 101, 'RAM', '4 GB', '0.00', 10),
(52, 12, 'Size', 'M', '0.00', 0),
(51, 12, 'Size', 'S', '0.00', 0),
(49, 102, 'Color', 'Black', '0.00', 10),
(50, 102, 'Color', 'Yellow', '0.00', 10),
(48, 102, 'Color', 'Off White', '0.00', 10),
(46, 102, 'Size', 'XL', '10.00', 10),
(47, 102, 'Size', 'L', '5.00', 10),
(43, 101, 'RAM', '16GB', '15.00', 10),
(42, 101, 'Storage', '64 GB', '0.00', 10),
(41, 101, 'Storage', '128', '30.00', 10),
(53, 12, 'Size', 'L', '5.00', 0),
(54, 12, 'Size', 'XL', '5.00', 0),
(55, 12, 'Size', 'XXL', '5.00', 0),
(56, 11, 'Size', 'M', '0.00', 0),
(57, 11, 'Size', 'L', '0.00', 0),
(58, 11, 'Size', 'XL', '5.00', 0),
(59, 10, 'Storage', '256 GB', '0.00', 0),
(60, 10, 'Storage', '512 GB', '20.00', 0),
(61, 10, 'OS', 'Windows 11', '0.00', 0),
(62, 10, 'OS', 'Ubuntu', '0.00', 0),
(63, 10, 'Storage', '1 TB', '30.00', 0),
(66, 1, 'Storage', '1 TB', '20.00', 5);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(9, 16, 11, 5, 'Excellent Delivery... Nice Product', '2026-02-01 04:41:23'),
(10, 14, 4, 4, 'Expensive\r\n', '2026-02-01 05:39:25'),
(13, 100, 4, 5, 'This is a test review for the dummy product!', '2026-02-03 05:02:12');

-- --------------------------------------------------------

--
-- Table structure for table `seo_settings`
--

CREATE TABLE `seo_settings` (
  `id` int(11) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `seo_settings`
--

INSERT INTO `seo_settings` (`id`, `meta_title`, `meta_description`, `meta_keywords`, `updated_at`) VALUES
(1, 'HK Store', 'Your one-stop shop for everything.', 'ecommerce, shop, online store', '2026-02-02 16:56:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','vendor','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `avatar`) VALUES
(4, 'Harsh Khandal', 'harsh@example.com', '$2y$10$SSOmSRu6AKb8xPs8R286Yeo9oWH6iWp4br2CQp8Z5Vbv2SFrrmu8a', 'admin', '2026-02-01 02:59:39', 'user_4_1770049618.jpg'),
(5, 'Customer ', 'customer@ecoproject.com', '$2y$10$SSOmSRu6AKb8xPs8R286Yeo9oWH6iWp4br2CQp8Z5Vbv2SFrrmu8a', 'customer', '2026-02-03 02:18:35', NULL),
(6, 'Admin', 'admin@ecoproject.com', '$2y$10$r4L8mwN.7yBb4jRFqjvVu.9OHXVcV19si1/gXXlSxD5VJPIvmlfza', 'admin', '2026-02-01 03:03:30', NULL),
(8, 'Vendor1', 'vendor1@hkstore.com', '$2y$10$RU3hC1f8SVbqPPF532begen3WS8k27L.SfmAFmFVuTdQeAKYmiJbK', 'vendor', '2026-02-01 03:08:46', NULL),
(9, 'Vendor2', 'vendor2@hkstore.com', '$2y$10$IU/t6sMsSQVOCbkw.RIvquHjuM5HH3En5EhsJBf7D4LQ/yDtjxgU6', 'vendor', '2026-02-01 03:08:46', NULL),
(10, 'Vendor3', 'vendor3@hkstore.com', '$2y$10$W.SPu8bx.1W4VcoMS7v/K.SEuQY9pJ6FE3M1quTcW85qQJPWBhC5K', 'vendor', '2026-02-01 03:08:46', NULL),
(11, 'Customer1', 'customer1@gmail.com', '$2y$10$yvE/.FyzyIP4xqqAqZsqq.u/UBv4TZZMiUQHVdcmOopN94m3u2Npu', 'customer', '2026-02-01 03:08:46', NULL),
(12, 'Customer2', 'customer2@gmail.com', '$2y$10$Z.KeidUDRDfI2L99cSRAaeP6C1OUeI7kTYKbySZtDLVEACssuED5m', 'customer', '2026-02-01 03:08:46', NULL),
(13, 'Customer3', 'customer3@gmail.com', '$2y$10$8Is8dH3dyd7Thq/Naimape6fw1Hi1Tcf/Mlc9lTRB5S67MlPo.uOW', 'customer', '2026-02-01 03:08:46', NULL),
(14, 'Customer4', 'customer4@gmail.com', '$2y$10$6cW2mqQVmC8zGZ7ZkbraQuttM1VmYXTGCpTxd/z1ySxdPRLrEhdKO', 'customer', '2026-02-01 03:08:46', NULL),
(1, 'John Doe', 'john@example.com', 'hashed_pass_1', 'customer', '2026-02-03 04:52:12', NULL),
(2, 'Jane Smith', 'jane@example.com', 'hashed_pass_2', 'customer', '2026-02-03 04:52:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','pending','suspended') DEFAULT 'pending'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `user_id`, `name`, `status`) VALUES
(5, 8, 'Vendor1 Official Store', 'active'),
(6, 9, 'Vendor2 Tech Hub', 'active'),
(7, 10, 'Vendor3 Fashion House', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(2, 4, 18, '2026-02-03 04:46:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_settings`
--
ALTER TABLE `seo_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `seo_settings`
--
ALTER TABLE `seo_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
