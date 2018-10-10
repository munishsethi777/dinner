-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 10, 2018 at 06:55 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 5.6.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dinner`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `seq` bigint(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `isenable` tinyint(4) NOT NULL,
  `createdon` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`seq`, `username`, `name`, `password`, `isenable`, `createdon`) VALUES
(1, 'admin', 'Administrator', '123', 1, '2018-10-08 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bookingdetails`
--

CREATE TABLE `bookingdetails` (
  `seq` int(11) NOT NULL,
  `bookingseq` int(11) NOT NULL,
  `menuseq` int(11) NOT NULL,
  `members` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bookingdetails`
--

INSERT INTO `bookingdetails` (`seq`, `bookingseq`, `menuseq`, `members`) VALUES
(1, 1, 1, 3),
(2, 2, 2, 4),
(3, 3, 2, 4),
(4, 7, 1, 2),
(5, 7, 2, 2),
(6, 8, 1, 1),
(7, 8, 2, 1),
(8, 9, 1, 1),
(9, 9, 2, 1),
(10, 10, 1, 2),
(11, 10, 2, 4),
(12, 11, 1, 2),
(13, 11, 2, 2),
(14, 12, 1, 3),
(15, 12, 2, 0),
(16, 13, 1, 3),
(17, 14, 3, 1),
(18, 15, 3, 3),
(19, 16, 1, 2),
(20, 17, 1, 2),
(21, 18, 1, 2),
(22, 19, 1, 2),
(23, 20, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `bookingpayments`
--

CREATE TABLE `bookingpayments` (
  `seq` int(11) NOT NULL,
  `bookingseq` int(11) NOT NULL,
  `paidon` datetime NOT NULL,
  `transactionid` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `seq` int(11) NOT NULL,
  `bookedon` datetime NOT NULL,
  `fullname` varchar(20) NOT NULL,
  `mobilenumber` varchar(12) NOT NULL,
  `emailid` varchar(50) NOT NULL,
  `gstnumber` varchar(50) DEFAULT NULL,
  `timeslot` int(11) NOT NULL,
  `bookingdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`seq`, `bookedon`, `fullname`, `mobilenumber`, `emailid`, `gstnumber`, `timeslot`, `bookingdate`) VALUES
(1, '2018-10-06 00:00:00', 'Baljeet', '98745654123', 'baljeetgaheer@gmail.com', '123123124334', 2, '0000-00-00 00:00:00'),
(2, '2018-10-07 00:00:00', 'munish', '7898978999', 'munish@gmail.com', 'a34343df444', 3, '0000-00-00 00:00:00'),
(3, '2018-10-07 00:00:00', 's singh', '7898978999', 'munish@gmail.com', 'a34343df444', 3, '0000-00-00 00:00:00'),
(4, '2018-10-09 08:52:34', '12', '12', '12', NULL, 2, '0000-00-00 00:00:00'),
(5, '2018-10-09 08:57:38', '12', '12', '12', NULL, 2, '0000-00-00 00:00:00'),
(6, '2018-10-09 08:58:08', '12', '12', '12', NULL, 2, '0000-00-00 00:00:00'),
(7, '2018-10-09 08:59:38', '12', '12', '12', NULL, 2, '0000-00-00 00:00:00'),
(8, '2018-10-09 00:00:00', 'w', 'wew', 'wew', NULL, 2, '0000-00-00 00:00:00'),
(9, '2018-10-09 00:00:00', 'w', 'wew', 'wew', NULL, 2, '0000-00-00 00:00:00'),
(10, '2018-10-09 00:00:00', '121', '121212121212', 'dd@wds.com', NULL, 2, '0000-00-00 00:00:00'),
(11, '2018-10-09 00:00:00', '12', '12', '12', NULL, 3, '0000-00-00 00:00:00'),
(12, '2018-10-09 00:00:00', '1', '1', '1', NULL, 3, '0000-00-00 00:00:00'),
(13, '2018-10-09 00:00:00', '1', '1', '1', NULL, 3, '0000-00-00 00:00:00'),
(14, '2018-10-09 00:00:00', 'adsfds', 'dsfdsfdsf', 'dsfdsfsdf', NULL, 4, '0000-00-00 00:00:00'),
(15, '2018-10-09 00:00:00', '1', '1', '1', NULL, 4, '0000-00-00 00:00:00'),
(16, '2018-10-10 06:47:21', 'Munish Sethi', '9814600356', 'munishsethi777@gmail.com', NULL, 2, '2018-10-10 00:00:00'),
(17, '2018-10-10 06:47:21', 'Munish Sethi', '9814600356', 'munishsethi777@gmail.com', NULL, 2, '2018-10-10 00:00:00'),
(18, '2018-10-10 06:47:46', 'Munish Sethi', '9814600356', 'munishsethi777@gmail.com', NULL, 2, '2018-10-10 00:00:00'),
(19, '2018-10-10 06:50:39', 'Munish Sethi', '9814600356', 'munishsethi777@gmail.com', NULL, 2, '2018-10-10 00:00:00'),
(20, '2018-10-10 06:51:54', 'Munish Sethi', '9814600356', 'munishsethi777@gmail.com', NULL, 2, '2018-10-10 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `seq` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`seq`, `title`, `description`, `rate`) VALUES
(1, 'Veg', 'Veg', 2000),
(2, 'Non Veg', 'Non Veg', 3000),
(3, 'Desserts', 'Desserts', 1250);

-- --------------------------------------------------------

--
-- Table structure for table `menutimeslots`
--

CREATE TABLE `menutimeslots` (
  `seq` int(11) NOT NULL,
  `menuseq` int(11) NOT NULL,
  `timeslotsseq` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menutimeslots`
--

INSERT INTO `menutimeslots` (`seq`, `menuseq`, `timeslotsseq`) VALUES
(2, 1, 3),
(3, 1, 2),
(4, 2, 2),
(5, 2, 3),
(6, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `timeslots`
--

CREATE TABLE `timeslots` (
  `seq` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `seats` int(11) NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timeslots`
--

INSERT INTO `timeslots` (`seq`, `title`, `seats`, `time`) VALUES
(2, '6:00PM to 8:00 PM', 10, '18:00:00'),
(3, '9:00PM to 11:00PM', 10, '21:00:00'),
(4, '11:00PM to 12:00PM', 10, '23:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`seq`);

--
-- Indexes for table `bookingdetails`
--
ALTER TABLE `bookingdetails`
  ADD PRIMARY KEY (`seq`);

--
-- Indexes for table `bookingpayments`
--
ALTER TABLE `bookingpayments`
  ADD PRIMARY KEY (`seq`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`seq`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`seq`);

--
-- Indexes for table `menutimeslots`
--
ALTER TABLE `menutimeslots`
  ADD PRIMARY KEY (`seq`);

--
-- Indexes for table `timeslots`
--
ALTER TABLE `timeslots`
  ADD PRIMARY KEY (`seq`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `seq` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookingdetails`
--
ALTER TABLE `bookingdetails`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `bookingpayments`
--
ALTER TABLE `bookingpayments`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menutimeslots`
--
ALTER TABLE `menutimeslots`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `timeslots`
--
ALTER TABLE `timeslots`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
