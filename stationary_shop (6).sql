-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2026 at 05:45 PM
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
-- Database: `stationary_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `total_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `total_price`, `total_quantity`) VALUES
(1, 16, 16.50, 3),
(3, 27, 0.00, 0),
(4, 23, 28.00, 7);

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `price`) VALUES
(13, 1, 2, 3, 16.50),
(27, 4, 12, 6, 21.00),
(28, 4, 11, 1, 7.00);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Pen'),
(2, 'Notebook'),
(3, 'Sticky Note');

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `membership_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `discount_rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership`
--

INSERT INTO `membership` (`membership_id`, `name`, `discount_rate`) VALUES
(1, 'Normal', 0),
(2, 'Silver', 5),
(3, 'Gold', 10);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orders_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `voucher_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orders_id`, `user_id`, `total_price`, `order_date`, `status`, `shipping_fee`, `recipient_name`, `phone`, `address_line1`, `address_line2`, `postal_code`, `city`, `state`, `voucher_code`, `discount_amount`) VALUES
(1, 16, 57.50, '2026-04-04', 'Pending', 5.00, '', '', '', '', '', '', '', '', 0.00),
(2, 16, 10.50, '2026-04-04', 'Pending', 5.00, '', '', '', '', '', '', '', '', 0.00),
(3, 27, 12.00, '2026-04-08', 'Pending', 5.00, '', '', '', '', '', '', '', '', 0.00),
(4, 23, 8.50, '2026-04-09', 'Pending', 5.00, '', '', '', '', '', '', '', '', 0.00),
(5, 23, 49.00, '2026-04-11', 'Pending', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', '', '11200', 'Tanjong Bungah', 'Pulau Pinang', '', 0.00),
(6, 23, 10.50, '2026-04-11', 'Pending', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', '', '11200', 'Tanjong Bungah', 'Pulau Pinang', '', 0.00),
(7, 23, 8.50, '2026-04-11', 'Pending', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', '', '11200', 'Tanjong Bungah', 'Pulau Pinang', '', 0.00),
(8, 23, 12.00, '2026-04-11', 'Pending', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', '', '11200', 'Tanjong Bungah', 'Pulau Pinang', NULL, 0.00),
(9, 23, 8.50, '2026-04-11', 'Pending', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', '', '11200', 'Tanjong Bungah', 'Pulau Pinang', NULL, 0.00),
(10, 23, 8.50, '2026-04-11', 'Paid', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', '', '11200', 'Tanjong Bungah', 'Pulau Pinang', NULL, 0.00),
(11, 23, 26.00, '2026-04-11', 'Paid', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', '', '11200', 'Tanjong Bungah', 'Pulau Pinang', NULL, 0.00),
(12, 23, 8.50, '2026-04-11', 'Paid', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', 'Granito,99, Lorong Lembah Permai 3', '11200', 'Tanjong Bungah', 'Pulau Pinang', NULL, 0.00),
(13, 23, 8.50, '2026-04-11', 'Paid', 5.00, 'Ivynghuyxing', '01137410151', 'Granito,99, Lorong Lembah Permai 3', 'Granito,99, Lorong Lembah Permai 3', '11200', 'Tanjong Bungah', 'Pulau Pinang', NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders_item`
--

CREATE TABLE `orders_item` (
  `orders_item_id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_item`
--

INSERT INTO `orders_item` (`orders_item_id`, `orders_id`, `product_id`, `price`, `quantity`) VALUES
(1, 1, 1, 10.50, 5),
(2, 2, 2, 5.50, 1),
(3, 3, 3, 7.00, 1),
(4, 4, 4, 3.50, 1),
(5, 5, 2, 5.50, 8),
(6, 6, 2, 5.50, 1),
(7, 7, 4, 3.50, 1),
(8, 8, 3, 7.00, 1),
(9, 9, 4, 3.50, 1),
(10, 10, 4, 3.50, 1),
(11, 11, 3, 7.00, 3),
(12, 12, 4, 3.50, 1),
(13, 13, 4, 3.50, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `category_id`, `price`, `stock_quantity`, `image`, `description`, `is_active`) VALUES
(1, 'm&g pen', 1, 10.50, 0, 'pen.jpg', 'pen set', 0),
(2, 'faber castle blue pen', 1, 5.50, 0, 'fabercastle.jpg', 'blue pen', 0),
(3, 'BLACK PEN', 1, 7.00, 0, '1775587341_69d5500d6fa7f.png', 'BLACK PEN', 0),
(4, 'red pen', 1, 3.50, 4, '1775625463_69d5e4f7e235a.png', 'red pen', 0),
(5, 'Journal Notebook', 2, 7.50, 7, '1775626179_69d5e7c3121ca.jpg', '', 0),
(6, 'Sticky Notes', 3, 3.50, 10, '1775741582_69d7aa8e4257e.jpg', '', 0),
(7, 'Sticky Notes', 1, 3.50, 1, '69d7b9154925c.jpg', '', 0),
(8, 'Sticky Notes', 3, 7.00, 8, '69da631d7fa74.jpg', '', 0),
(9, 'Sticky Notes', 3, 7.00, 6, '69da6508c9d54.jpg', '', 0),
(10, 'Sticky Notes', 3, 7.00, 5, '69da65c944efc.jpg', '', 0),
(11, 'Notebook', 1, 7.00, 7, '69da67388ba19.jpg', '', 1),
(12, 'Sticky Notes', 3, 3.50, 8, '69da676ad4452.jpg', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `token`
--

INSERT INTO `token` (`id`, `user_id`, `expire`) VALUES
('8c4e8b41cd0513b5d06cdd81453a87ccab787606', 27, '2026-04-08 14:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` char(1) NOT NULL,
  `profile_photo` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL,
  `membership_id` int(11) NOT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT 0,
  `verification_code` varchar(10) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `password`, `email`, `gender`, `profile_photo`, `role`, `membership_id`, `valid`, `verification_code`, `email_verified`, `reset_token`, `reset_expiry`) VALUES
(3, 'ivy', 'b800a78d23640812af5326e3f8331652d392b5ab', 'ngivy0912@gmail.com', '', '', '', 1, 1, NULL, 0, '6324c70034b03d22bae0b21a977510bf83f2707e14f3ed9e273fd0eb4a7e42cf', '2026-04-11 22:43:41'),
(4, 'ivyng', 'e5529d75e3', 'ngivy091207@gmail.com', '', '', '', 1, 0, NULL, 0, NULL, NULL),
(5, 'kk', '86bd78fd52', 'kk@gmail.com', '', '', '', 1, 0, NULL, 0, NULL, NULL),
(6, 'kkv', 'e5529d75e3', 'kkv@gmail.com', '', '', '', 1, 0, NULL, 0, NULL, NULL),
(7, 'xixixi', '3d4f2bf07d', 'xixi912@gmail.com', '', '', '', 1, 0, NULL, 0, NULL, NULL),
(8, 'xixixika', '3d4f2bf07d', 'xixika912@gmail.com', '', '', '', 1, 0, NULL, 0, NULL, NULL),
(9, 'ivykk', 'e5529d75e3', 'ngivy0912222@gmail.com', '', '69cbbb1654368.jpg', 'customer', 1, 0, NULL, 0, NULL, NULL),
(10, 'ivyxixxi', 'e5529d75e3', 'ivyxixi@gmail.com', '', '69cbbba4555fd.jpg', 'customer', 1, 0, NULL, 0, NULL, NULL),
(11, 'joey', 'joey0912', 'joey0912@gmail.com', 'F', '', 'admin', 0, 0, NULL, 0, NULL, NULL),
(12, 'joeyho', 'a34a83e20d1aa79c85212383b379df4ee5a27e35', 'joey091207@gmail.com', '', '69cc9070475f5.jpg', 'admin', 1, 0, NULL, 0, NULL, NULL),
(13, 'admin', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'admin@cozyhub.com', 'M', 'admin.jpg', 'admin', 1, 1, NULL, 0, NULL, NULL),
(14, 'hojoey', 'a34a83e20d1aa79c85212383b379df4ee5a27e35', 'joeyho0912@gmail.com', 'F', '69cdf9c98b0f5.jpg', 'customer', 1, 0, NULL, 0, NULL, NULL),
(15, 'kim', 'e2dfb8abed6c38ae10436dd1cc3d1c3bf0f33303', 'kim@gmail.com', 'F', '69cdfc23cfea5.jpg', 'customer', 1, 0, NULL, 0, NULL, NULL),
(16, 'jj', '228ae89845cb317a2d113537ebe3a573380995c8', 'jj@gmail.com', 'M', '69cf503244c16.jpg', 'admin', 1, 1, NULL, 0, NULL, NULL),
(17, 'star', 'a189fceeb515f5f2dc44f911432d9e871afd34f4', 'star@gmail.com', 'F', '69d0d0e7875c4.jpg', 'customer', 1, 0, NULL, 0, NULL, NULL),
(20, 'candy', 'b4806e05e14ca5c3298270fc6428906a629d287b', 'candy@gmail.com', '', '69d4e5b8c8878.jpg', 'customer', 1, 0, NULL, 0, NULL, NULL),
(21, 'cindy', 'b4806e05e14ca5c3298270fc6428906a629d287b', 'cindy@gmail.com', '', '69d4e6179e3b8.jpg', 'customer', 1, 1, NULL, 0, NULL, NULL),
(23, 'ivysakura', '53c4ab4e40b89229f21177e95a4441b3123e7757', 'ivynghuyxingivy@gmail.com', 'F', '69d54e351ea9b.jpg', 'customer', 1, 1, NULL, 1, NULL, NULL),
(24, 'sakuragege', 'e5529d75e36c74a493c29cae8bbb6aafdd596979', 'hojouyee@gmail.com', 'M', '69d54e9f84b39.jpg', 'admin', 1, 1, NULL, 1, NULL, NULL),
(27, 'xingxingxiu', 'e5529d75e36c74a493c29cae8bbb6aafdd596979', 'ngivy808@gmail.com', 'M', '69d5ea2c5bd20.jpg', 'customer', 1, 1, NULL, 1, NULL, NULL),
(29, 'sakuragegestar', 'e5529d75e36c74a493c29cae8bbb6aafdd596979', 'ngoswald72@gmail.com', 'F', '69d77d3377dfe.jpg', 'customer', 1, 0, '207072', 0, NULL, NULL),
(30, 'sakurage', 'e5529d75e36c74a493c29cae8bbb6aafdd596979', 'ngoswald73@gmail.com', 'F', '69d77dd134af8.jpg', 'customer', 1, 0, '673130', 0, NULL, NULL),
(31, 'sakurastar', 'e5529d75e36c74a493c29cae8bbb6aafdd596979', 'ngivy367@gmail.com', 'F', '69d77e1b3f7bf.jpg', 'customer', 1, 1, NULL, 1, NULL, NULL),
(32, 'ivystarstar', 'e5529d75e36c74a493c29cae8bbb6aafdd596979', 'ngivy0912@gmal.com', 'M', '69da42e2dff17.jpg', 'customer', 1, 0, '630052', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `verification_tokens`
--

CREATE TABLE `verification_tokens` (
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_tokens`
--

INSERT INTO `verification_tokens` (`token`, `user_id`, `expire`) VALUES
('e933ee03316a4f5276defae779e0788f3fc978f7', 18, '2026-04-06 22:11:10'),
('f086fc76af8d2f645e968ecad828dc405695195f', 19, '2026-04-06 22:13:31'),
('f4baaa969b611ac34ed682d80ba85e77ec1893d8', 20, '2026-04-07 20:08:40');

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `voucher_id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `started_date` date NOT NULL,
  `expired_date` date NOT NULL,
  `usage_limit` int(11) NOT NULL,
  `minimum_purchase_amount` decimal(10,2) NOT NULL,
  `usage_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `cust_id fk` (`user_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id fk` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`membership_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orders_id`),
  ADD KEY `cust_id` (`user_id`);

--
-- Indexes for table `orders_item`
--
ALTER TABLE `orders_item`
  ADD PRIMARY KEY (`orders_item_id`),
  ADD KEY `orders_id` (`orders_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`orders_id`),
  ADD KEY `voucher_id` (`voucher_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `membership_id` (`membership_id`);

--
-- Indexes for table `verification_tokens`
--
ALTER TABLE `verification_tokens`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`voucher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `membership`
--
ALTER TABLE `membership`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orders_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders_item`
--
ALTER TABLE `orders_item`
  MODIFY `orders_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cust_id fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_id` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `product_id fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `cust_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `orders_item`
--
ALTER TABLE `orders_item`
  ADD CONSTRAINT `orders_id` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`orders_id`),
  ADD CONSTRAINT `product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `order_id` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`orders_id`),
  ADD CONSTRAINT `voucher_id` FOREIGN KEY (`voucher_id`) REFERENCES `voucher` (`voucher_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
