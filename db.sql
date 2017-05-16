-- phpMyAdmin SQL Dump
-- version 4.6.4deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 16, 2017 at 10:33 PM
-- Server version: 5.7.18-0ubuntu0.16.10.1
-- PHP Version: 7.0.15-0ubuntu0.16.10.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(5) NOT NULL,
  `course_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `course_name`) VALUES
(3, 'B-Tech'),
(5, 'M-Tech');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `hod` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department_name`, `hod`) VALUES
(9, 'computer science', 'hoduser');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `uid` int(255) NOT NULL,
  `browser` varchar(40) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `cookieid` varchar(20) NOT NULL,
  `os` varchar(11) NOT NULL,
  `dateandtime` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`uid`, `browser`, `ip`, `cookieid`, `os`, `dateandtime`) VALUES
(1, 'Chrome', '::1', 'uzGn_eEyRVv4', 'Linux', '2017-05-15 05:09:44pm'),
(1, 'Chrome', '::1', 'N068x1eLFOta', 'Linux', '2017-05-15 05:32:01pm'),
(2, 'Chrome', '::1', 'ep41mtwWIO2F', 'Linux', '2017-05-15 06:40:51pm'),
(1, 'Chrome', '::1', 'pBfxL3izobJm', 'Linux', '2017-05-15 07:29:18pm'),
(22, 'Chrome', '::1', 'Y_n7fjFG6H9d', 'Linux', '2017-05-15 07:37:19pm'),
(1, 'Chrome', '::1', 'I4qRcbWXnfwT', 'Linux', '2017-05-15 07:38:00pm'),
(2, 'Chrome', '::1', 'tUNEd5GJbQ_c', 'Linux', '2017-05-15 07:39:02pm'),
(1, 'Chrome', '::1', 'drUtX7GEsnoT', 'Linux', '2017-05-15 08:17:36pm'),
(23, 'Chrome', '::1', 'FGW28eQ6RoPO', 'Linux', '2017-05-15 08:18:00pm'),
(1, 'Chrome', '::1', 'PSxntQFZV6Au', 'Linux', '2017-05-15 08:54:28pm'),
(23, 'Chrome', '::1', 'Y_6E4HPCrdcl', 'Linux', '2017-05-15 08:55:04pm'),
(1, 'Chrome', '::1', 'OZa3_17RdQsG', 'Linux', '2017-05-15 09:14:50pm'),
(2, 'Chrome', '::1', 'ZAPblFUxqCzo', 'Linux', '2017-05-15 10:07:01pm'),
(1, 'Chrome', '::1', 'C7cHajlRAXB2', 'Linux', '2017-05-15 10:07:11pm'),
(2, 'Chrome', '::1', 'sWuRv6ZQBSDa', 'Linux', '2017-05-15 10:43:28pm'),
(2, 'Chrome', '::1', 'fxbgvCeLSqmc', 'Linux', '2017-05-15 10:43:33pm'),
(2, 'Chrome', '::1', 'p3xBOrFdPWAL', 'Linux', '2017-05-15 10:43:43pm'),
(1, 'Chrome', '::1', 'l_6GzCAkDoMj', 'Linux', '2017-05-15 10:44:27pm'),
(1, 'Chrome', '::1', 'ID0vrN7j_xBW', 'Linux', '2017-05-15 10:57:17pm'),
(1, 'Chrome', '::1', '8MkLrq3IGwxO', 'Linux', '2017-05-16 01:56:19pm'),
(24, 'Chrome', '::1', 'wBlfHRmSjYV_', 'Linux', '2017-05-16 01:58:20pm'),
(2, 'Chrome', '::1', '9601kflBrenp', 'Linux', '2017-05-16 02:31:02pm'),
(2, 'Chrome', '::1', '0cqlpDA3ymnr', 'Linux', '2017-05-16 02:40:22pm'),
(1, 'Chrome', '::1', 'EsVRbFW3DxcJ', 'Linux', '2017-05-16 02:40:27pm'),
(23, 'Chrome', '::1', 'ApU9xbekBJNr', 'Linux', '2017-05-16 02:40:35pm'),
(24, 'Chrome', '::1', 'z_vduN8IDKW4', 'Linux', '2017-05-16 03:07:28pm'),
(1, 'Chrome', '::1', 'wQBIo7zVZim_', 'Linux', '2017-05-16 08:13:55pm'),
(24, 'Chrome', '::1', 'pedIRDLujmEG', 'Linux', '2017-05-16 08:15:48pm'),
(23, 'Chrome', '::1', 'X_0baeKkVW5i', 'Linux', '2017-05-16 08:23:21pm'),
(24, 'Chrome', '::1', 'XwzbGUdIkEsr', 'Linux', '2017-05-16 08:28:13pm');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `uid` int(225) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `salt` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`uid`, `email`, `password`, `salt`) VALUES
(1, 'adminuser', 'fb3e078f203f922dc1bc14a1803df39eb7c385e0', 'yZmeHcDfiS9v'),
(2, 'kannan', 'adc77d3153093e1b1d7b3f0d46dcf59318b49dac', 'TJ6UGIgXycpS'),
(21, 'studentuser', 'ce55e79969d108bed2956f290f64910ad32fff4b', 'WPZVaAdGzgMY'),
(22, 'parentuser', '07a4c8ec694d0153a8688e4a7ff95f30ad793a9a', '67JWMBNmi_IL'),
(23, 'hoduser', '5a738fb3ce6c839efa4d8ab9727eda0f7c3aa8e0', 'TmqO9P6oULEv'),
(24, 'princialuser', 'c91c9eea71fb23eda548058140757c6f0a6907e3', 'TMQeVpc8WDmb');

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `permissionid` int(5) NOT NULL,
  `groupname` varchar(20) NOT NULL,
  `prio` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`permissionid`, `groupname`, `prio`) VALUES
(1, 'admin', 0),
(2, 'principal', 5),
(3, 'hod', 10),
(4, 'staff_advisor', 15),
(5, 'teacher', 20),
(6, 'student', 25),
(7, 'parent', 30);

-- --------------------------------------------------------

--
-- Table structure for table `setup`
--

CREATE TABLE `setup` (
  `collegename` varchar(100) NOT NULL,
  `collegeshortname` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setup`
--

INSERT INTO `setup` (`collegename`, `collegeshortname`) VALUES
('Sree Chitra Thirunal College of Engineering', 'S.C.T.C.E');

-- --------------------------------------------------------

--
-- Table structure for table `userpermission`
--

CREATE TABLE `userpermission` (
  `uid` int(225) NOT NULL,
  `permissionid` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userpermission`
--

INSERT INTO `userpermission` (`uid`, `permissionid`) VALUES
(1, 1),
(1, 2),
(2, 4),
(16, 1),
(17, 1),
(19, 1),
(21, 6),
(22, 7),
(23, 3),
(23, 4),
(23, 5),
(24, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_name` (`course_name`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD UNIQUE KEY `permissionid` (`permissionid`),
  ADD UNIQUE KEY `permissionid_2` (`permissionid`,`groupname`);

--
-- Indexes for table `setup`
--
ALTER TABLE `setup`
  ADD UNIQUE KEY `collegename` (`collegename`,`collegeshortname`);

--
-- Indexes for table `userpermission`
--
ALTER TABLE `userpermission`
  ADD PRIMARY KEY (`uid`,`permissionid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `uid` int(225) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
