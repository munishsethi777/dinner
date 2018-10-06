-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 06, 2018 at 01:00 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 5.6.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skydining`
--

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
(3, 3, 2, 4);

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
  `gstnumber` varchar(50) NOT NULL,
  `timeslot` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`seq`, `bookedon`, `fullname`, `mobilenumber`, `emailid`, `gstnumber`, `timeslot`) VALUES
(1, '2018-10-06 00:00:00', 'Baljeet', '98745654123', 'baljeetgaheer@gmail.com', '123123124334', 2),
(2, '2018-10-07 00:00:00', 'munish', '7898978999', 'munish@gmail.com', 'a34343df444', 3),
(3, '2018-10-07 00:00:00', 's singh', '7898978999', 'munish@gmail.com', 'a34343df444', 3);

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
(3, 'Evening Snacks', 'Evening Snacks', 1250);

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
(2, '8 to 10 PM', 10, '20:00:00'),
(3, '1 to 3 PM', 10, '13:00:00'),
(4, '6 to 7 PM', 10, '18:00:00');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `bookingdetails`
--
ALTER TABLE `bookingdetails`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookingpayments`
--
ALTER TABLE `bookingpayments`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
