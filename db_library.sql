-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 21, 2024 at 01:54 AM
-- Server version: 10.10.2-MariaDB
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `BookID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `majorID` int(10) NOT NULL,
  `Author` varchar(100) NOT NULL,
  `ISBN` varchar(20) DEFAULT NULL,
  `PublicationYear` int(11) DEFAULT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `createDate` date NOT NULL,
  PRIMARY KEY (`BookID`),
  UNIQUE KEY `ISBN` (`ISBN`),
  KEY `idx_book_title` (`Title`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

DROP TABLE IF EXISTS `borrowings`;
CREATE TABLE IF NOT EXISTS `borrowings` (
  `BorrowID` int(11) NOT NULL AUTO_INCREMENT,
  `BorrowerType` enum('ນັກສຶກສາ','ອາຈານ') NOT NULL,
  `BorrowerID` int(11) NOT NULL,
  `BookID` int(11) DEFAULT NULL,
  `BorrowDate` date NOT NULL,
  `ReturnDate` date DEFAULT NULL,
  `Status` enum('ຢືມ','ສົ່ງຄືນ','ເກີນກຳນົດ') NOT NULL,
  PRIMARY KEY (`BorrowID`),
  KEY `BookID` (`BookID`),
  KEY `idx_borrow_date` (`BorrowDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brorows`
--

DROP TABLE IF EXISTS `brorows`;
CREATE TABLE IF NOT EXISTS `brorows` (
  `brrowID` int(10) NOT NULL AUTO_INCREMENT,
  `borrowingID` int(10) NOT NULL,
  `memberID` int(10) NOT NULL,
  `borrowdate` date NOT NULL,
  PRIMARY KEY (`brrowID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `DepartmentID` int(11) NOT NULL AUTO_INCREMENT,
  `DepartmentName` varchar(100) NOT NULL,
  PRIMARY KEY (`DepartmentID`),
  UNIQUE KEY `DepartmentName` (`DepartmentName`),
  KEY `idx_department_name` (`DepartmentName`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`DepartmentID`, `DepartmentName`) VALUES
(1, 'ບໍລິຫານຈັດຕັ້ງພະນັກງານ'),
(2, 'ວິຊາການ');

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

DROP TABLE IF EXISTS `majors`;
CREATE TABLE IF NOT EXISTS `majors` (
  `MajorID` int(11) NOT NULL AUTO_INCREMENT,
  `MajorName` varchar(100) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`MajorID`),
  KEY `DepartmentID` (`DepartmentID`),
  KEY `idx_major_name` (`MajorName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

DROP TABLE IF EXISTS `memberships`;
CREATE TABLE IF NOT EXISTS `memberships` (
  `MembershipID` int(11) NOT NULL AUTO_INCREMENT,
  `MemberType` enum('ນັກສຶກສາ','ອາຈານ') NOT NULL,
  `MemberID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Status` enum('ຍັງໃຊ້ງານຢູ່','ໝົດອາຍຸ','ຖືກລະງັບ') NOT NULL,
  PRIMARY KEY (`MembershipID`),
  UNIQUE KEY `MemberType` (`MemberType`,`MemberID`),
  KEY `fk_teacher_membership` (`MemberID`),
  KEY `idx_membership_status` (`Status`),
  KEY `idx_membership_dates` (`StartDate`,`EndDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `StudentID` int(11) NOT NULL AUTO_INCREMENT,
  `StudentCode` varchar(20) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `MajorID` int(11) DEFAULT NULL,
  `ContactInfo` varchar(255) DEFAULT NULL,
  `year` varchar(100) NOT NULL,
  `system` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  PRIMARY KEY (`StudentID`),
  UNIQUE KEY `StudentCode` (`StudentCode`),
  KEY `MajorID` (`MajorID`),
  KEY `idx_student_code` (`StudentCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE IF NOT EXISTS `teachers` (
  `TeacherID` int(11) NOT NULL AUTO_INCREMENT,
  `FullName` varchar(100) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `ContactInfo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`TeacherID`),
  KEY `DepartmentID` (`DepartmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `ContactInfo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `FullName`, `ContactInfo`) VALUES
(1, 'a', 'a', 'admin', '00123');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
