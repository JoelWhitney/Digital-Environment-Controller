-- phpMyAdmin SQL Dump
-- version 4.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 12, 2016 at 08:00 PM
-- Server version: 5.5.42-37.1
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `abconet1_GROWMASTER5000`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `aID` int(11) NOT NULL,
  `controlOutlet` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  `insertTime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `manual_overrides`
--

CREATE TABLE IF NOT EXISTS `manual_overrides` (
  `moID` int(11) NOT NULL,
  `controlOutlet` int(11) NOT NULL,
  `startTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  `action` int(11) NOT NULL,
  `type` enum('Lights','Temperature','Humidity','pH','Moisture','CO2') NOT NULL,
  `insertTime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `measurands`
--

CREATE TABLE IF NOT EXISTS `measurands` (
  `mID` int(11) NOT NULL,
  `sensorPin` int(11) NOT NULL,
  `value` double NOT NULL,
  `insertTime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=22397 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `sID` int(11) NOT NULL,
  `sensorPin` int(11) NOT NULL,
  `controlOutlet` int(11) NOT NULL,
  `operator` enum('>=','<=') NOT NULL,
  `value` double NOT NULL,
  `setting` enum('0','1') NOT NULL,
  `insertTime` datetime NOT NULL,
  `type` enum('Lights','CO2','Temperature','pH','Humidity','Moisture','Other') NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`aID`);

--
-- Indexes for table `manual_overrides`
--
ALTER TABLE `manual_overrides`
  ADD PRIMARY KEY (`moID`);

--
-- Indexes for table `measurands`
--
ALTER TABLE `measurands`
  ADD PRIMARY KEY (`mID`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`sID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`), ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `aID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `manual_overrides`
--
ALTER TABLE `manual_overrides`
  MODIFY `moID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `measurands`
--
ALTER TABLE `measurands`
  MODIFY `mID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `sID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
