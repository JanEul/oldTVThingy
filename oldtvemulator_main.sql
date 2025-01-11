-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 11, 2025 at 10:44 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oldtvemulator_main`
--

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

CREATE TABLE `channels` (
  `channel` tinyint(1) NOT NULL,
  `channelName` varchar(128) NOT NULL,
  `hasAds` tinyint(1) NOT NULL DEFAULT 0,
  `adsAmount` tinyint(2) NOT NULL DEFAULT 8,
  `ident_season` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channels_seasons`
--

CREATE TABLE `channels_seasons` (
  `cs_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `season_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laststates`
--

CREATE TABLE `laststates` (
  `id` int(11) NOT NULL,
  `lastChannel` tinyint(4) NOT NULL,
  `lastMuted` tinyint(1) NOT NULL,
  `lastVolume` tinyint(4) NOT NULL,
  `lastVisit` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `queue_id` int(11) NOT NULL,
  `channel_id` tinyint(3) NOT NULL,
  `show_id` int(11) NOT NULL,
  `playTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
  `season_id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `season_number` varchar(64) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE `series` (
  `series_id` int(11) NOT NULL,
  `series_name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE `shows` (
  `show_id` int(11) NOT NULL,
  `season_id` smallint(3) DEFAULT NULL,
  `fileName` varchar(64) NOT NULL,
  `show_name` varchar(256) NOT NULL DEFAULT '',
  `duration` smallint(7) NOT NULL DEFAULT 0,
  `episode` smallint(3) DEFAULT NULL,
  `isAd` tinyint(1) NOT NULL DEFAULT 0,
  `adType` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = Regular, 1 = Start Ident | 2 = End Ident',
  `releaseDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `reconverted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`channel`);

--
-- Indexes for table `channels_seasons`
--
ALTER TABLE `channels_seasons`
  ADD PRIMARY KEY (`cs_id`),
  ADD UNIQUE KEY `channel_id` (`channel_id`,`season_id`);

--
-- Indexes for table `laststates`
--
ALTER TABLE `laststates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD UNIQUE KEY `channel_id_2` (`channel_id`,`playTime`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `playTime` (`playTime`),
  ADD KEY `show_id` (`show_id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`season_id`),
  ADD KEY `series_id` (`series_id`);

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`series_id`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`show_id`),
  ADD UNIQUE KEY `season_id_2` (`season_id`,`episode`),
  ADD KEY `isAd` (`isAd`),
  ADD KEY `season_id` (`season_id`),
  ADD KEY `adType` (`adType`),
  ADD KEY `isAd_2` (`isAd`,`adType`),
  ADD KEY `episode` (`episode`),
  ADD KEY `releaseDate` (`releaseDate`),
  ADD KEY `endDate` (`endDate`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `channels_seasons`
--
ALTER TABLE `channels_seasons`
  MODIFY `cs_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laststates`
--
ALTER TABLE `laststates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `season_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `series_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
  MODIFY `show_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
