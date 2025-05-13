-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 03:31 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barterbuddies`
--

-- --------------------------------------------------------

--
-- Table structure for table `equivalence`
--

CREATE TABLE `equivalence` (
  `equiv_id` int(11) NOT NULL,
  `item_id_from` int(11) NOT NULL,
  `item_id_to` int(11) NOT NULL,
  `conversion_rate` decimal(10,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `equivalence`
--

INSERT INTO `equivalence` (`equiv_id`, `item_id_from`, `item_id_to`, `conversion_rate`) VALUES
(1, 1, 2, '0.9798'),
(2, 1, 3, '1.2312'),
(3, 1, 4, '0.7917'),
(4, 1, 5, '1.5140'),
(5, 1, 6, '2.6948'),
(6, 1, 7, '1.0709'),
(7, 1, 8, '3.3447'),
(8, 1, 9, '1.6370'),
(9, 1, 10, '1.2238'),
(10, 1, 11, '1.1317'),
(11, 1, 12, '0.8772'),
(12, 1, 13, '1.0819'),
(13, 1, 14, '1.4228'),
(14, 1, 15, '2.3473'),
(15, 1, 16, '2.6790'),
(16, 1, 17, '0.7898'),
(17, 1, 18, '0.7786'),
(18, 1, 19, '3.3915'),
(19, 1, 20, '2.1323'),
(20, 1, 21, '0.9996'),
(21, 1, 22, '1.2562'),
(22, 1, 23, '2.0238'),
(23, 1, 24, '3.3694'),
(24, 1, 25, '6.7712'),
(25, 2, 3, '1.2566'),
(26, 2, 4, '0.8080'),
(27, 2, 5, '1.5452'),
(28, 2, 6, '2.7504'),
(29, 2, 7, '1.0930'),
(30, 2, 8, '3.4137'),
(31, 2, 9, '1.6708'),
(32, 2, 10, '1.2491'),
(33, 2, 11, '1.1550'),
(34, 2, 12, '0.8953'),
(35, 2, 13, '1.1042'),
(36, 2, 14, '1.4522'),
(37, 2, 15, '2.3957'),
(38, 2, 16, '2.7343'),
(39, 2, 17, '0.8061'),
(40, 2, 18, '0.7946'),
(41, 2, 19, '3.4615'),
(42, 2, 20, '2.1763'),
(43, 2, 21, '1.0202'),
(44, 2, 22, '1.2821'),
(45, 2, 23, '2.0655'),
(46, 2, 24, '3.4389'),
(47, 2, 25, '6.9109'),
(48, 3, 4, '0.6430'),
(49, 3, 5, '1.2296'),
(50, 3, 6, '2.1887'),
(51, 3, 7, '0.8698'),
(52, 3, 8, '2.7166'),
(53, 3, 9, '1.3296'),
(54, 3, 10, '0.9940'),
(55, 3, 11, '0.9191'),
(56, 3, 12, '0.7125'),
(57, 3, 13, '0.8787'),
(58, 3, 14, '1.1556'),
(59, 3, 15, '1.9064'),
(60, 3, 16, '2.1759'),
(61, 3, 17, '0.6414'),
(62, 3, 18, '0.6324'),
(63, 3, 19, '2.7546'),
(64, 3, 20, '1.7318'),
(65, 3, 21, '0.8119'),
(66, 3, 22, '1.0203'),
(67, 3, 23, '1.6437'),
(68, 3, 24, '2.7366'),
(69, 3, 25, '5.4996'),
(70, 4, 5, '1.9123'),
(71, 4, 6, '3.4039'),
(72, 4, 7, '1.3526'),
(73, 4, 8, '4.2248'),
(74, 4, 9, '2.0678'),
(75, 4, 10, '1.5459'),
(76, 4, 11, '1.4294'),
(77, 4, 12, '1.1081'),
(78, 4, 13, '1.3666'),
(79, 4, 14, '1.7972'),
(80, 4, 15, '2.9649'),
(81, 4, 16, '3.3839'),
(82, 4, 17, '0.9976'),
(83, 4, 18, '0.9834'),
(84, 4, 19, '4.2839'),
(85, 4, 20, '2.6933'),
(86, 4, 21, '1.2626'),
(87, 4, 22, '1.5867'),
(88, 4, 23, '2.5563'),
(89, 4, 24, '4.2560'),
(90, 4, 25, '8.5528'),
(91, 5, 6, '1.7800'),
(92, 5, 7, '0.7073'),
(93, 5, 8, '2.2092'),
(94, 5, 9, '1.0813'),
(95, 5, 10, '0.8084'),
(96, 5, 11, '0.7475'),
(97, 5, 12, '0.5794'),
(98, 5, 13, '0.7146'),
(99, 5, 14, '0.9398'),
(100, 5, 15, '1.5504'),
(101, 5, 16, '1.7695'),
(102, 5, 17, '0.5216'),
(103, 5, 18, '0.5143'),
(104, 5, 19, '2.2402'),
(105, 5, 20, '1.4084'),
(106, 5, 21, '0.6603'),
(107, 5, 22, '0.8297'),
(108, 5, 23, '0.6969'),
(109, 5, 24, '2.2256'),
(110, 5, 25, '4.4725'),
(111, 6, 7, '0.3974'),
(112, 6, 8, '1.2412'),
(113, 6, 9, '0.6075'),
(114, 6, 10, '0.4541'),
(115, 6, 11, '0.4199'),
(116, 6, 12, '0.3255'),
(117, 6, 13, '0.4015'),
(118, 6, 14, '0.5280'),
(119, 6, 15, '0.8710'),
(120, 6, 16, '0.9941'),
(121, 6, 17, '0.2931'),
(122, 6, 18, '0.2889'),
(123, 6, 19, '1.2585'),
(124, 6, 20, '0.7913'),
(125, 6, 21, '0.3709'),
(126, 6, 22, '0.4661'),
(127, 6, 23, '0.7510'),
(128, 6, 24, '1.2503'),
(129, 6, 25, '2.5127'),
(130, 7, 8, '3.1234'),
(131, 7, 9, '1.5287'),
(132, 7, 10, '1.1429'),
(133, 7, 11, '1.0568'),
(134, 7, 12, '0.8192'),
(135, 7, 13, '1.0103'),
(136, 7, 14, '1.3287'),
(137, 7, 15, '2.1919'),
(138, 7, 16, '2.5017'),
(139, 7, 17, '0.7375'),
(140, 7, 18, '0.7271'),
(141, 7, 19, '3.1671'),
(142, 7, 20, '1.9912'),
(143, 7, 21, '0.9335'),
(144, 7, 22, '1.1730'),
(145, 7, 23, '1.8898'),
(146, 7, 24, '3.1465'),
(147, 7, 25, '6.3231'),
(148, 8, 9, '0.4894'),
(149, 8, 10, '0.3659'),
(150, 8, 11, '0.3383'),
(151, 8, 12, '0.2623'),
(152, 8, 13, '0.3235'),
(153, 8, 14, '0.4254'),
(154, 8, 15, '0.7018'),
(155, 8, 16, '0.8010'),
(156, 8, 17, '0.2361'),
(157, 8, 18, '0.2328'),
(158, 8, 19, '1.0140'),
(159, 8, 20, '0.6375'),
(160, 8, 21, '0.2989'),
(161, 8, 22, '0.3756'),
(162, 8, 23, '0.6051'),
(163, 8, 24, '1.0074'),
(164, 8, 25, '2.0245'),
(165, 9, 10, '0.7476'),
(166, 9, 11, '0.6913'),
(167, 9, 12, '0.5359'),
(168, 9, 13, '0.6609'),
(169, 9, 14, '0.8692'),
(170, 9, 15, '1.4338'),
(171, 9, 16, '1.6365'),
(172, 9, 17, '0.4824'),
(173, 9, 18, '0.4756'),
(174, 9, 19, '2.0717'),
(175, 9, 20, '1.3025'),
(176, 9, 21, '0.6106'),
(177, 9, 22, '0.7673'),
(178, 9, 23, '1.2362'),
(179, 9, 24, '2.0582'),
(180, 9, 25, '4.1362'),
(181, 10, 11, '0.9247'),
(182, 10, 12, '0.7168'),
(183, 10, 13, '0.8840'),
(184, 10, 14, '1.1626'),
(185, 10, 15, '1.9180'),
(186, 10, 16, '2.1890'),
(187, 10, 17, '0.6453'),
(188, 10, 18, '0.6362'),
(189, 10, 19, '2.7712'),
(190, 10, 20, '1.7423'),
(191, 10, 21, '0.8168'),
(192, 10, 22, '1.0264'),
(193, 10, 23, '1.6536'),
(194, 10, 24, '2.7532'),
(195, 10, 25, '5.5328'),
(196, 11, 12, '0.7752'),
(197, 11, 13, '0.9560'),
(198, 11, 14, '1.2573'),
(199, 11, 15, '2.0742'),
(200, 11, 16, '2.3673'),
(201, 11, 17, '0.6979'),
(202, 11, 18, '0.6880'),
(203, 11, 19, '2.9969'),
(204, 11, 20, '1.8842'),
(205, 11, 21, '0.8833'),
(206, 11, 22, '1.1100'),
(207, 11, 23, '1.7883'),
(208, 11, 24, '2.9774'),
(209, 11, 25, '5.9834'),
(210, 12, 13, '1.2333'),
(211, 12, 14, '1.6219'),
(212, 12, 15, '2.6757'),
(213, 12, 16, '3.0539'),
(214, 12, 17, '0.9003'),
(215, 12, 18, '0.8875'),
(216, 12, 19, '3.8661'),
(217, 12, 20, '2.4307'),
(218, 12, 21, '1.1395'),
(219, 12, 22, '1.4320'),
(220, 12, 23, '2.3070'),
(221, 12, 24, '3.8409'),
(222, 12, 25, '7.7188'),
(223, 13, 14, '1.3151'),
(224, 13, 15, '2.1695'),
(225, 13, 16, '2.4762'),
(226, 13, 17, '0.7300'),
(227, 13, 18, '0.7196'),
(228, 13, 19, '3.1347'),
(229, 13, 20, '1.9708'),
(230, 13, 21, '0.9239'),
(231, 13, 22, '1.1610'),
(232, 13, 23, '1.8705'),
(233, 13, 24, '3.1143'),
(234, 13, 25, '6.2585'),
(235, 14, 15, '1.6497'),
(236, 14, 16, '1.8829'),
(237, 14, 17, '0.5551'),
(238, 14, 18, '0.5472'),
(239, 14, 19, '2.3836'),
(240, 14, 20, '1.4986'),
(241, 14, 21, '0.7026'),
(242, 14, 22, '0.8829'),
(243, 14, 23, '1.4223'),
(244, 14, 24, '2.3681'),
(245, 14, 25, '4.7590'),
(246, 15, 16, '1.1413'),
(247, 15, 17, '0.3365'),
(248, 15, 18, '0.3317'),
(249, 15, 19, '1.4449'),
(250, 15, 20, '0.9084'),
(251, 15, 21, '0.4259'),
(252, 15, 22, '0.5352'),
(253, 15, 23, '0.8622'),
(254, 15, 24, '1.4355'),
(255, 15, 25, '2.8847'),
(256, 16, 17, '0.2948'),
(257, 16, 18, '0.2906'),
(258, 16, 19, '1.2660'),
(259, 16, 20, '0.7959'),
(260, 16, 21, '0.3731'),
(261, 16, 22, '0.4689'),
(262, 16, 23, '0.7554'),
(263, 16, 24, '1.2577'),
(264, 16, 25, '2.5275'),
(265, 17, 18, '0.9858'),
(266, 17, 19, '4.2944'),
(267, 17, 20, '2.6999'),
(268, 17, 21, '1.2657'),
(269, 17, 22, '1.5906'),
(270, 17, 23, '2.5625'),
(271, 17, 24, '4.2664'),
(272, 17, 25, '8.5738'),
(273, 18, 19, '4.3561'),
(274, 18, 20, '2.7387'),
(275, 18, 21, '1.2839'),
(276, 18, 22, '1.6134'),
(277, 18, 23, '2.5993'),
(278, 18, 24, '4.3277'),
(279, 18, 25, '8.6969'),
(280, 19, 20, '0.6287'),
(281, 19, 21, '0.2947'),
(282, 19, 22, '0.3704'),
(283, 19, 23, '0.5967'),
(284, 19, 24, '0.9935'),
(285, 19, 25, '1.9965'),
(286, 20, 21, '0.4688'),
(287, 20, 22, '0.5891'),
(288, 20, 23, '0.9491'),
(289, 20, 24, '1.5802'),
(290, 20, 25, '3.1755'),
(291, 21, 22, '1.2566'),
(292, 21, 23, '2.0245'),
(293, 21, 24, '3.3707'),
(294, 21, 25, '6.7738'),
(295, 22, 23, '1.6111'),
(296, 22, 24, '2.6823'),
(297, 22, 25, '5.3904'),
(298, 23, 24, '1.6649'),
(299, 23, 25, '3.3459'),
(300, 24, 25, '2.0096');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `base_value` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `description`, `base_value`) VALUES
(1, 'Beef Stroganoff Meal Kit', 'Praesent blandit lacinia erat. Vestibulum sed magna at nunc commodo placerat. Praesent blandit. Nam nulla. Integer pede justo, lacinia eget, tincidunt eget, tempus vel, pede.', '77.53'),
(2, 'Camera Lens Cleaning Kit', 'Cras non velit nec nisi vulputate nonummy. Maecenas tincidunt lacus at velit. Vivamus vel nulla eget eros elementum pellentesque. Quisque porta volutpat erat. Quisque erat eros, viverra eget, congue eget, semper rutrum, nulla. Nunc purus. Phasellus in felis. Donec semper sapien a libero.', '79.13'),
(3, 'Carrot and Celery Sticks', 'Etiam justo. Etiam pretium iaculis justo.', '62.97'),
(4, 'Balsamic Vinaigrette', 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam vel augue. Vestibulum rutrum rutrum neque. Aenean auctor gravida sem. Praesent id massa id nisl venenatis lacinia. Aenean sit amet justo. Morbi ut odio. Cras mi pede, malesuada in, imperdiet et, commodo vulputate, justo. In blandit ultrices enim.', '97.93'),
(5, 'Laptop Stand', 'In hac habitasse platea dictumst.', '51.21'),
(6, 'Toilet Paper (12 rolls)', 'Nulla ac enim. In tempor, turpis nec euismod scelerisque, quam turpis adipiscing lorem, vitae mattis nibh ligula nec sem.', '28.77'),
(7, 'Wall Planner', 'In hac habitasse platea dictumst. Etiam faucibus cursus urna. Ut tellus. Nulla ut erat id mauris vulputate elementum.', '72.40'),
(8, 'Butternut Squash Soup', 'Cras pellentesque volutpat dui. Maecenas tristique, est et tempus semper, est quam pharetra magna, ac consequat metus sapien ut nunc. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris viverra diam vitae quam. Suspendisse potenti. Nullam porttitor lacus at turpis. Donec posuere metus vitae ipsum. Aliquam non mauris. Morbi non lectus. Aliquam sit amet diam in magna bibendum imperdiet. Nullam orci pede, venenatis non, sodales sed, tincidunt eu, felis.', '23.18'),
(9, 'Lentil Soup (canned)', 'Sed ante. Vivamus tortor. Duis mattis egestas metus. Aenean fermentum. Donec ut mauris eget massa tempor convallis. Nulla neque libero, convallis eget, eleifend luctus, ultricies eu, nibh. Quisque id justo sit amet sapien dignissim vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla dapibus dolor vel est. Donec odio justo, sollicitudin ut, suscipit a, feugiat et, eros. Vestibulum ac est lacinia nisi venenatis tristique.', '47.36'),
(10, 'Grilled Vegetable Medley', 'Nunc nisl. Duis bibendum, felis sed interdum venenatis, turpis enim blandit mi, in porttitor pede justo eu massa. Donec dapibus.', '63.35'),
(11, 'Field Journal', 'Fusce consequat. Nulla nisl. Nunc nisl.', '68.51'),
(12, 'Creamy Garlic Dressing', 'Quisque erat eros, viverra eget, congue eget, semper rutrum, nulla. Nunc purus. Phasellus in felis. Donec semper sapien a libero. Nam dui. Proin leo odio, porttitor id, consequat in, consequat ut, nulla. Sed accumsan felis. Ut at dolor quis odio consequat varius.', '88.38'),
(13, 'Pasta (Linguine)', 'Vivamus in felis eu sapien cursus vestibulum.', '71.66'),
(14, 'Digital Food Thermometer', 'In eleifend quam a odio. In hac habitasse platea dictumst. Maecenas ut massa quis augue luctus tincidunt. Nulla mollis molestie lorem. Quisque ut erat.', '54.49'),
(15, 'Chocolate Fudge Brownie Mix', 'Quisque id justo sit amet sapien dignissim vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla dapibus dolor vel est. Donec odio justo, sollicitudin ut, suscipit a, feugiat et, eros. Vestibulum ac est lacinia nisi venenatis tristique.', '33.03'),
(16, 'Smart Air Purifier', 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vestibulum sagittis sapien. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam vel augue. Vestibulum rutrum rutrum neque. Aenean auctor gravida sem. Praesent id massa id nisl venenatis lacinia.', '28.94'),
(17, 'Sweet Potatoes', 'Aliquam non mauris.', '98.17'),
(18, 'Two-Tone Windbreaker', 'Nullam porttitor lacus at turpis. Donec posuere metus vitae ipsum. Aliquam non mauris. Morbi non lectus.', '99.58'),
(19, 'Oven Thermometer', 'Duis bibendum, felis sed interdum venenatis, turpis enim blandit mi, in porttitor pede justo eu massa. Donec dapibus. Duis at velit eu est congue elementum. In hac habitasse platea dictumst. Morbi vestibulum, velit id pretium iaculis, diam erat fermentum justo, nec condimentum neque sapien placerat ante. Nulla justo. Aliquam quis turpis eget elit sodales scelerisque. Mauris sit amet eros.', '22.86'),
(20, 'Classic Beef Chili', 'Duis aliquam convallis nunc. Proin at turpis a pede posuere nonummy.', '36.36'),
(21, 'Portable Air Conditioner', 'Integer a nibh. In quis justo. Maecenas rhoncus aliquam lacus. Morbi quis tortor id nulla ultrices aliquet.', '77.56'),
(22, 'Leather Biker Jacket', 'Pellentesque ultrices mattis odio. Donec vitae nisi. Nam ultrices, libero non mattis pulvinar, nulla pede ullamcorper augue, a suscipit nulla elit ac nulla.', '61.72'),
(23, 'Belted Trench Coat', 'Nam congue, risus semper porta volutpat, quam pede lobortis ligula, sit amet eleifend pede libero quis orci. Nullam molestie nibh in lectus. Pellentesque at nulla. Suspendisse potenti. Cras in purus eu magna vulputate luctus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vestibulum sagittis sapien.', '38.31'),
(24, 'Organic Italian Seasoning', 'Aenean lectus. Pellentesque eget nunc. Donec quis orci eget orci vehicula condimentum. Curabitur in libero ut massa volutpat convallis. Morbi odio odio, elementum eu, interdum eu, tincidunt in, leo. Maecenas pulvinar lobortis est. Phasellus sit amet erat.', '23.01'),
(25, 'Fitness Tracker Band', 'Donec ut mauris eget massa tempor convallis. Nulla neque libero, convallis eget, eleifend luctus, ultricies eu, nibh.', '11.45');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `partner_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`partner_id`, `user1_id`, `user2_id`) VALUES
(1, 1, 2),
(2, 6, 5),
(3, 9, 10),
(4, 8, 13),
(5, 12, 14);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `status` enum('open','matched','cancelled','completed') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `status`, `created_at`) VALUES
(18, 'completed', '2025-05-12 11:00:49'),
(22, 'completed', '2025-05-12 11:18:57');

-- --------------------------------------------------------

--
-- Table structure for table `trade_confirmation`
--

CREATE TABLE `trade_confirmation` (
  `confirmation_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `submitted_hash` tinyint(1) DEFAULT NULL,
  `submitted_item` tinyint(1) DEFAULT NULL,
  `sent_hash` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trade_confirmation`
--

INSERT INTO `trade_confirmation` (`confirmation_id`, `match_id`, `user_id`, `submitted_hash`, `submitted_item`, `sent_hash`) VALUES
(14, 8, 10, 1, 1, 1),
(15, 8, 9, 1, 1, 1),
(16, 8, 1, 1, 1, 1),
(17, 8, 2, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `trade_details`
--

CREATE TABLE `trade_details` (
  `post_id` int(11) NOT NULL,
  `item_offered` int(11) NOT NULL,
  `quantity_offered` int(11) DEFAULT NULL,
  `item_desired` int(11) NOT NULL,
  `quantity_desired` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trade_details`
--

INSERT INTO `trade_details` (`post_id`, `item_offered`, `quantity_offered`, `item_desired`, `quantity_desired`, `completed_at`) VALUES
(18, 23, 2, 2, 1, '2025-05-13 01:28:24'),
(22, 2, 1, 23, 2, '2025-05-13 01:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `trade_match`
--

CREATE TABLE `trade_match` (
  `match_id` int(11) NOT NULL,
  `hash_code` varchar(255) DEFAULT NULL,
  `hash_user1` varchar(255) DEFAULT NULL,
  `hash_user2` varchar(255) DEFAULT NULL,
  `status` enum('in progress','completed','canceled') NOT NULL,
  `post1_id` int(11) DEFAULT NULL,
  `post2_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trade_match`
--

INSERT INTO `trade_match` (`match_id`, `hash_code`, `hash_user1`, `hash_user2`, `status`, `post1_id`, `post2_id`) VALUES
(8, '4a4115fe498171ca', '4a4115fe', '498171ca', 'completed', 22, 18);

-- --------------------------------------------------------

--
-- Table structure for table `trade_members`
--

CREATE TABLE `trade_members` (
  `post_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `poster_has_items` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trade_members`
--

INSERT INTO `trade_members` (`post_id`, `created_by`, `poster_has_items`) VALUES
(18, 1, 1),
(22, 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','suspended') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `address`, `is_admin`, `created_at`, `status`) VALUES
(1, 'James Tanner', 'jamest@man.com', '$2y$10$ig80RdKKMjsGxu1R7f1fZ.n5LtK9WIgIBbWsf26MJripP9yVHbxd2', '1923843717', '123 W Test Rd', NULL, '2025-04-16 21:45:32', 'approved'),
(2, 'John Jay', 'johnjay@test.com', '$2y$10$mugeOpA7ZwYrvRS1zdEd5.fcsmvD46nTdeeT6Hwx0J6Wht5Swe1uq', '3938174275', '213 Testing Ave', NULL, '2025-04-16 21:59:18', 'approved'),
(3, 'root admin', 'root@one.com', '$2y$10$7fD7wmfo9iWfEkRyxw66H.c4XBFbeEnV0w7V2D2AxUOgPtcRkebaO', '2039854710', '412 S Admin Rd', 1, '2025-04-16 22:10:57', 'approved'),
(5, 'hannah smith', 'hs@test.com', '$2y$10$TVZLMXH3MBt.xO6VZAJQDOLIh9vLaJ5wPOM0pdsIdEOfd/hK7G20e', '9310482742', '501 N Trial Run Way', NULL, '2025-04-17 09:44:23', 'approved'),
(6, 'Jayden Harlen', 'jaylen@test.com', '$2y$10$A2AOtdGXM5DWeEP.SaXp.uFbNiQazXbS.xfKVFCjU6Nuq4NebfuWC', '2940192584', '102 Testing Rd', 0, '2025-04-17 09:47:53', 'approved'),
(7, 'Jane Vandal', 'jvandal@root.com', '$2y$10$D7BNln8zxA7w0zyzN8CjHufQXhwxbiKkapEpT4d8.b6pdiUhd.HaG', NULL, NULL, 1, '2025-04-17 16:18:10', 'approved'),
(8, 'Shea Kanon', 'sheabutter@test.com', '$2y$10$HhTUdC1eWHwCRQA.IV.cFuoDXphmpSvo5c8WfDbt7bQ183YYWXg5C', '2084731628', '123 W Checking Ct', NULL, '2025-04-17 16:36:44', 'approved'),
(9, 'Thomas Schirach', 'tschirach@test.com', '$2y$10$1rShKaKQ7IkbjQPb2NJlYO3XA8KFznQxSJBvlKpWKQBxSHyrBPLce', '3819274039', '4892 W Shipping Yard Ln', NULL, '2025-04-17 16:38:46', 'approved'),
(10, 'Wendy Scone', 'pastryfood@test.com', '$2y$10$6sJ7g4qNGiEwCrnC19OgIei47Yd0k/iA5PaDuoRLHo16xyKMcJnrC', '3819472182', '10 N False Rd', NULL, '2025-04-17 16:40:50', 'approved'),
(11, 'Emma Neal', 'emneal1@trial.com', '$2y$10$uJnPENoYYXtYFeP4nTNNlOD6ondTJzARgZs29elj2zj6vsgBvV6IC', '2931743822', '1 W Fake St', NULL, '2025-04-17 18:12:10', 'approved'),
(12, 'Holden Caulfield', 'holden@test.com', '$2y$10$8zkYueBBAV.9IAsT102BY.vk98TCSGQbXTJ56ohzn1JykvFoKn3s6', '3851928374', '500 Tester Ave', NULL, '2025-04-17 18:13:17', 'approved'),
(13, 'Tod Herzog', 'todd@test.com', '$2y$10$s6bCfN/aTzGYEFOwrURgvOjMaqTRcIESSDh/7.DL33EDiCs0kdvOS', '1284763912', '123 Testing Way', NULL, '2025-04-17 18:35:07', 'approved'),
(14, 'shadlorex Isthebest', 'shaddieisbaddie@gmail.com', '$2y$10$FoXyE7HXaVYc7WwMP9huDuAVeUgbSETSSK01N.VTjt34dbsB5JhFe', '1564832105', '2305 W Best St', NULL, '2025-04-18 02:25:25', 'approved'),
(15, 'Taylor Lawrence', 'taylaw@test.com', '$2y$10$vAgCTqG4zpoucPyv1rH7M.IV.O730xpRbcvLExgnzyrC.M7YY/3Ua', '2019384752', '124 Testing Dr', NULL, '2025-05-11 06:24:47', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equivalence`
--
ALTER TABLE `equivalence`
  ADD PRIMARY KEY (`equiv_id`),
  ADD KEY `item_id_from` (`item_id_from`),
  ADD KEY `item_id_to` (`item_id_to`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`partner_id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `trade_confirmation`
--
ALTER TABLE `trade_confirmation`
  ADD PRIMARY KEY (`confirmation_id`),
  ADD KEY `match_id` (`match_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trade_details`
--
ALTER TABLE `trade_details`
  ADD KEY `post_id` (`post_id`),
  ADD KEY `item_offered` (`item_offered`),
  ADD KEY `item_desired` (`item_desired`);

--
-- Indexes for table `trade_match`
--
ALTER TABLE `trade_match`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `fk_post1` (`post1_id`),
  ADD KEY `fk_post2` (`post2_id`);

--
-- Indexes for table `trade_members`
--
ALTER TABLE `trade_members`
  ADD KEY `post_id` (`post_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equivalence`
--
ALTER TABLE `equivalence`
  MODIFY `equiv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `trade_confirmation`
--
ALTER TABLE `trade_confirmation`
  MODIFY `confirmation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `trade_match`
--
ALTER TABLE `trade_match`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `equivalence`
--
ALTER TABLE `equivalence`
  ADD CONSTRAINT `equivalence_ibfk_1` FOREIGN KEY (`item_id_from`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `equivalence_ibfk_2` FOREIGN KEY (`item_id_to`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `partners`
--
ALTER TABLE `partners`
  ADD CONSTRAINT `partners_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partners_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `trade_confirmation`
--
ALTER TABLE `trade_confirmation`
  ADD CONSTRAINT `trade_confirmation_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `trade_match` (`match_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trade_confirmation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `trade_details`
--
ALTER TABLE `trade_details`
  ADD CONSTRAINT `trade_details_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trade_details_ibfk_2` FOREIGN KEY (`item_offered`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trade_details_ibfk_3` FOREIGN KEY (`item_desired`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `trade_match`
--
ALTER TABLE `trade_match`
  ADD CONSTRAINT `fk_post1` FOREIGN KEY (`post1_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_post2` FOREIGN KEY (`post2_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE;

--
-- Constraints for table `trade_members`
--
ALTER TABLE `trade_members`
  ADD CONSTRAINT `trade_members_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trade_members_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
