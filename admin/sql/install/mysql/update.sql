-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2016 at 02:19 PM
-- Server version: 5.5.42
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `itica_j35`
--

-- --------------------------------------------------------

-- Adding indes to summary table
ALTER TABLE `#__toes_summary` ADD INDEX `smry_show` (`summary_show`);

--
-- Table structure for table `#__toes_cat_images`
--

CREATE TABLE `#__toes_cat_images` (
  `cat_img_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `file_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__toes_regnumber_formats`
--

CREATE TABLE `#__toes_regnumber_formats` (
  `rnf_id` int(11) NOT NULL,
  `regformat` varchar(10) NOT NULL,
  `type` int(1) NOT NULL,
  `published` int(1) NOT NULL DEFAULT '1', 
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__toes_regnumber_formats`
--

INSERT INTO `#__toes_regnumber_formats` (`rnf_id`, `regformat`, `type`, `published`) VALUES
(1, 'SH', 2, 1),
(2, 'LH', 2, 1),
(3, 'SBT', 3, 1),
(4, 'SBV', 3, 1),
(5, 'SBP', 3, 1),
(6, 'AOP', 3, 1),
(7, 'BOP', 3, 1),
(8, 'COP', 3, 1),
(9, '01T', 3, 1),
(10, '02T', 3, 1),
(11, '01V', 3, 1),
(12, '02V', 3, 1),
(13, '03V', 3, 1),
(14, 'A1P', 3, 1),
(15, 'B1P', 3, 1),
(16, 'C1P', 3, 1),
(17, 'A2P', 3, 1),
(18, 'B2P', 3, 1),
(19, 'C2P', 3, 1),
(20, 'A3P', 3, 1),
(21, 'B3P', 3, 1),
(22, 'C3P', 3, 1),
(23, 'SBN', 3, 1),
(24, 'SBS', 3, 1),
(25, '01P', 3, 1),
(26, '02P', 3, 1),
(27, '03P', 3, 1),
(28, '03T', 3, 1);

-- Alter table `#__toes_placeholder`
  
ALTER TABLE `#__toes_placeholder` ADD `placeholder_summary` INT NOT NULL AFTER `placeholder_exhibitor`;

-- update table `#__toes_placeholder`

UPDATE `#__toes_placeholder` AS p LEFT JOIN #__toes_summary AS s ON p.`placeholder_show`= s.summary_show AND p.`placeholder_exhibitor`= s.summary_user
SET p.`placeholder_summary`= s.summary_id
WHERE 1


--
-- Table structure for table `#__toes_mail_templates`
--

CREATE TABLE `#__toes_mail_templates` (
  `tmpl_id` int(11) NOT NULL,
  `tmpl_name` varchar(255) NOT NULL,
  `action_name` varchar(255) NOT NULL,
  `smtp_id` int(11) NOT NULL,
  `mail_subject` tinytext NOT NULL,
  `mail_body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_paypal_invoice_detail`
--

CREATE TABLE `#__toes_paypal_invoice_detail` (
  `id` int(11) NOT NULL,
  `show_id` int(11) NOT NULL,
  `billing_email` varchar(255) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `invoice_status` varchar(50) NOT NULL,
  `created_date` datetime NOT NULL,
  `paid_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_smtp_accounts`
--

CREATE TABLE `#__toes_smtp_accounts` (
  `smtp_id` int(2) NOT NULL,
  `smtp_name` varchar(50) NOT NULL,
  `smtp_auth` varchar(50) NOT NULL,
  `smtp_host` varchar(255) NOT NULL,
  `smtp_user` varchar(50) NOT NULL,
  `smtp_pass` varchar(50) NOT NULL,
  `smtp_secure` varchar(4) NOT NULL,
  `smtp_port` int(4) NOT NULL,
  `published` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `#__toes_regnumber_formats`
--
ALTER TABLE `#__toes_regnumber_formats`
  ADD PRIMARY KEY (`rnf_id`);

--
-- Indexes for table `#__toes_mail_templates`
--
ALTER TABLE `#__toes_mail_templates`
  ADD PRIMARY KEY (`tmpl_id`);

--
-- Indexes for table `#__toes_paypal_invoice_detail`
--
ALTER TABLE `#__toes_paypal_invoice_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_smtp_accounts`
--
ALTER TABLE `#__toes_smtp_accounts`
  ADD PRIMARY KEY (`smtp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `#__toes_regnumber_formats`
--
ALTER TABLE `#__toes_regnumber_formats`
  MODIFY `rnf_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `#__toes_mail_templates`
--
ALTER TABLE `#__toes_mail_templates`
  MODIFY `tmpl_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `#__toes_paypal_invoice_detail`
--
ALTER TABLE `#__toes_paypal_invoice_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `#__toes_smtp_accounts`
--
ALTER TABLE `#__toes_smtp_accounts`
  MODIFY `smtp_id` int(2) NOT NULL AUTO_INCREMENT;