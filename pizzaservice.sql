-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2019 at 01:15 AM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pizzaservice`
--

-- --------------------------------------------------------

--
-- Table structure for table `angebot`
--

CREATE TABLE `angebot` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `bild` varchar(50) NOT NULL DEFAULT '',
  `preis` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `angebot`
--

INSERT INTO `angebot` (`id`, `name`, `bild`, `preis`) VALUES
(1, 'Margherita', 'images/pizza.png', 450),
(2, 'Papperoni', 'images/pizza.png', 550),
(3, 'Salami', 'images/pizza.png', 590);

-- --------------------------------------------------------

--
-- Table structure for table `angebot_bestellung`
--

CREATE TABLE `angebot_bestellung` (
  `id` int(11) UNSIGNED NOT NULL,
  `angebot_id` int(11) UNSIGNED NOT NULL,
  `bestellung_id` int(11) UNSIGNED NOT NULL,
  `status` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `angebot_bestellung`
--

INSERT INTO `angebot_bestellung` (`id`, `angebot_id`, `bestellung_id`, `status`) VALUES
(31, 1, 21, 2),
(38, 1, 26, 1),
(39, 2, 26, 0),
(40, 3, 26, 1),
(41, 3, 26, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bestellung`
--

CREATE TABLE `bestellung` (
  `id` int(11) UNSIGNED NOT NULL,
  `adresse` varchar(50) NOT NULL DEFAULT '',
  `zeitpunkt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bestellung`
--

INSERT INTO `bestellung` (`id`, `adresse`, `zeitpunkt`, `status`) VALUES
(21, 'MÃ¼ller , Alfred Messel Weg 8', '2019-12-03 23:43:47', 2),
(26, 'MÃ¼ller', '2019-12-04 01:07:30', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `angebot`
--
ALTER TABLE `angebot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `angebot_bestellung`
--
ALTER TABLE `angebot_bestellung`
  ADD PRIMARY KEY (`id`),
  ADD KEY `angebot_id` (`angebot_id`),
  ADD KEY `bestellung_id` (`bestellung_id`);

--
-- Indexes for table `bestellung`
--
ALTER TABLE `bestellung`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `angebot`
--
ALTER TABLE `angebot`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `angebot_bestellung`
--
ALTER TABLE `angebot_bestellung`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `bestellung`
--
ALTER TABLE `bestellung`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `angebot_bestellung`
--
ALTER TABLE `angebot_bestellung`
  ADD CONSTRAINT `angebot_bestellung_ibfk_1` FOREIGN KEY (`angebot_id`) REFERENCES `angebot` (`id`),
  ADD CONSTRAINT `angebot_bestellung_ibfk_2` FOREIGN KEY (`bestellung_id`) REFERENCES `bestellung` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
