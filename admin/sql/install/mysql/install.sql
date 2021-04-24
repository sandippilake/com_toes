-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Apr 22, 2016 at 03:41 PM
-- Server version: 5.5.43-0+deb7u1
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test_server_j31`
--

-- --------------------------------------------------------

--
-- Table structure for table `#__toes`
--

CREATE TABLE IF NOT EXISTS `#__toes` (
  `id` int(11) NOT NULL,
  `greeting` varchar(25) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_address`
--

CREATE TABLE IF NOT EXISTS `#__toes_address` (
  `address_id` int(11) NOT NULL,
  `address_line_1` varchar(64) DEFAULT NULL,
  `address_line_2` varchar(64) DEFAULT NULL,
  `address_line_3` varchar(64) DEFAULT NULL,
  `address_zip_code` varchar(10) DEFAULT NULL,
  `address_city` varchar(64) DEFAULT NULL,
  `address_state` varchar(30) DEFAULT NULL,
  `address_country` varchar(64) DEFAULT NULL,
  `address_type` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_address_type`
--

CREATE TABLE IF NOT EXISTS `#__toes_address_type` (
  `address_type_id` tinyint(4) NOT NULL,
  `address_type` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_breed`
--

CREATE TABLE IF NOT EXISTS `#__toes_breed` (
  `breed_id` int(11) NOT NULL,
  `breed_abbreviation` varchar(5) NOT NULL,
  `breed_name` varchar(255) NOT NULL,
  `breed_group` varchar(255) NOT NULL,
  `breed_hair_length` int(11) NOT NULL DEFAULT '1',
  `breed_status` int(11) DEFAULT '0',
  `breed_color_restrictions` tinyint(1) DEFAULT '0',
  `breed_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_breed_category`
--

CREATE TABLE IF NOT EXISTS `#__toes_breed_category` (
  `id` int(3) NOT NULL,
  `breed` int(2) DEFAULT NULL,
  `category` int(1) DEFAULT NULL,
  `allowed` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_breed_category_division`
--

CREATE TABLE IF NOT EXISTS `#__toes_breed_category_division` (
  `id` int(3) DEFAULT NULL,
  `breed` int(2) DEFAULT NULL,
  `category` int(1) DEFAULT NULL,
  `division` int(1) DEFAULT NULL,
  `allowed` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_breed_category_division_color`
--

CREATE TABLE IF NOT EXISTS `#__toes_breed_category_division_color` (
  `breed_category_division_color_id` int(11) NOT NULL,
  `breed` int(11) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `division` int(11) DEFAULT NULL,
  `color` int(11) DEFAULT NULL,
  `organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_breed_division`
--

CREATE TABLE IF NOT EXISTS `#__toes_breed_division` (
  `id` int(3) DEFAULT NULL,
  `breed` int(2) DEFAULT NULL,
  `division` int(1) DEFAULT NULL,
  `allowed` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_breed_has_status`
--

CREATE TABLE IF NOT EXISTS `#__toes_breed_has_status` (
  `breed_has_status_id` int(11) NOT NULL,
  `breed_has_status_breed` int(11) NOT NULL,
  `breed_has_status_status` int(11) NOT NULL,
  `breed_has_status_since` date NOT NULL DEFAULT '2012-05-01',
  `breed_has_status_until` date NOT NULL DEFAULT '2099-12-31'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_breed_status`
--

CREATE TABLE IF NOT EXISTS `#__toes_breed_status` (
  `breed_status_id` int(11) NOT NULL,
  `breed_status` varchar(50) NOT NULL,
  `breed_status_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat` (
  `cat_id` int(11) NOT NULL,
  `cat_breed` int(11) DEFAULT NULL,
  `cat_hair_length` int(11) NOT NULL DEFAULT '1',
  `cat_category` int(11) DEFAULT NULL,
  `cat_division` int(11) DEFAULT NULL,
  `cat_color` int(11) DEFAULT NULL,
  `cat_date_of_birth` date DEFAULT NULL,
  `cat_gender` int(11) DEFAULT NULL,
  `cat_prefix` int(11) DEFAULT NULL,
  `cat_title` int(11) DEFAULT NULL,
  `cat_name` varchar(255) DEFAULT NULL,
  `cat_suffix` int(11) DEFAULT NULL,
  `cat_sire` varchar(255) DEFAULT NULL,
  `cat_dam` varchar(255) DEFAULT NULL,
  `cat_breeder` varchar(255) DEFAULT NULL,
  `cat_owner` varchar(255) DEFAULT NULL,
  `cat_lessee` varchar(255) DEFAULT NULL,
  `cat_competitive_region` int(11) DEFAULT NULL,
  `cat_new_trait` tinyint(1) DEFAULT NULL,
  `cat_id_chip_number` varchar(64) DEFAULT NULL,
  `cat_record_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_catalog_page_orientation`
--

CREATE TABLE IF NOT EXISTS `#__toes_catalog_page_orientation` (
  `id` int(11) NOT NULL,
  `page_ortientation` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_category`
--

CREATE TABLE IF NOT EXISTS `#__toes_category` (
  `category_id` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `category_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_category_division_color`
--

CREATE TABLE IF NOT EXISTS `#__toes_category_division_color` (
  `id` int(4) DEFAULT NULL,
  `category` int(1) DEFAULT NULL,
  `division` int(1) DEFAULT NULL,
  `color_name` varchar(41) DEFAULT NULL,
  `allowed` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_cat_connection_type`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_cat_connection_type` (
  `cat_cat_connection_type_id` int(11) NOT NULL,
  `cat_to_cat_connection_type` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_gender`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_gender` (
  `gender_id` int(11) NOT NULL,
  `gender_short_name` varchar(1) DEFAULT NULL,
  `gender_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_hair_length`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_hair_length` (
  `cat_hair_length_id` int(11) NOT NULL,
  `cat_hair_length_abbreviation` varchar(2) NOT NULL,
  `cat_hair_length` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_prefix`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_prefix` (
  `cat_prefix_id` int(11) NOT NULL,
  `cat_prefix_abbreviation` varchar(50) DEFAULT NULL,
  `cat_prefix` varchar(50) DEFAULT NULL,
  `cat_prefix_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_registration_number`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_registration_number` (
  `cat_registration_number_id` int(11) NOT NULL,
  `cat_registration_number` varchar(20) NOT NULL,
  `cat_registration_number_organization` int(11) NOT NULL DEFAULT '1',
  `cat_registration_number_cat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_relates_to_cat`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_relates_to_cat` (
  `cat_1_is` int(11) NOT NULL,
  `of_cat_2` int(11) NOT NULL,
  `cat_cat_connection_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_relates_to_user`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_relates_to_user` (
  `of_cat` int(11) NOT NULL,
  `person_is` int(11) NOT NULL,
  `cat_user_connection_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_suffix`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_suffix` (
  `cat_suffix_id` int(11) NOT NULL,
  `cat_suffix_abbreviation` varchar(2) DEFAULT NULL,
  `cat_suffix` varchar(50) DEFAULT NULL,
  `cat_suffix_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_title`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_title` (
  `cat_title_id` int(11) NOT NULL,
  `cat_title_abbreviation` varchar(6) DEFAULT NULL,
  `cat_title` varchar(50) DEFAULT NULL,
  `cat_title_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_cat_user_connection_type`
--

CREATE TABLE IF NOT EXISTS `#__toes_cat_user_connection_type` (
  `cat_user_connection_type_id` int(11) NOT NULL,
  `cat_user_connection_type` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_club`
--

CREATE TABLE IF NOT EXISTS `#__toes_club` (
  `club_id` int(11) NOT NULL,
  `club_name` varchar(255) NOT NULL,
  `club_abbreviation` varchar(255) NOT NULL,
  `club_website` varchar(255) DEFAULT NULL,
  `club_email` varchar(255) DEFAULT NULL,
  `club_paypal` varchar(128) DEFAULT NULL,
  `club_iban` varchar(30) DEFAULT NULL,
  `club_bic` varchar(13) DEFAULT NULL,
  `club_account_holder_name` varchar(128) DEFAULT NULL,
  `club_account_holder_address` varchar(128) DEFAULT NULL,
  `club_account_holder_zip` varchar(10) DEFAULT NULL,
  `club_account_holder_city` varchar(64) DEFAULT NULL,
  `club_account_holder_state` varchar(10) DEFAULT NULL,
  `club_account_holder_country` varchar(64) DEFAULT NULL,
  `club_organization` int(11) NOT NULL DEFAULT '1',
  `club_competitive_region` int(11) DEFAULT NULL,
  `club_cost_per_entry` float NOT NULL DEFAULT '0.25',
  `club_on_toes_bad_debt_list` tinyint(1) NOT NULL DEFAULT '0',
  `club_email_for_paypal_invoices` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_club_official`
--

CREATE TABLE IF NOT EXISTS `#__toes_club_official` (
  `club_official_id` int(11) NOT NULL,
  `club` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `club_official_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_club_official_type`
--

CREATE TABLE IF NOT EXISTS `#__toes_club_official_type` (
  `club_official_type_id` int(11) NOT NULL,
  `club_official_type` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_club_organizes_show`
--

CREATE TABLE IF NOT EXISTS `#__toes_club_organizes_show` (
  `club` int(11) NOT NULL,
  `show` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_color`
--

CREATE TABLE IF NOT EXISTS `#__toes_color` (
  `color_id` int(11) NOT NULL,
  `color_category` int(11) NOT NULL DEFAULT '0',
  `color_division` int(11) NOT NULL DEFAULT '0',
  `color_name` varchar(50) NOT NULL,
  `color_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_color_helper_ems_color`
--

CREATE TABLE IF NOT EXISTS `#__toes_color_helper_ems_color` (
  `ems_code` varchar(4) DEFAULT NULL,
  `color` varchar(18) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_color_status`
--

CREATE TABLE IF NOT EXISTS `#__toes_color_status` (
  `color_status_id` int(11) NOT NULL,
  `color_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_competitive_region`
--

CREATE TABLE IF NOT EXISTS `#__toes_competitive_region` (
  `competitive_region_id` int(11) NOT NULL,
  `competitive_region_abbreviation` varchar(50) DEFAULT NULL,
  `competitive_region_name` varchar(50) DEFAULT NULL,
  `competitive_region_regional_director` int(11) NOT NULL,
  `organization` int(11) NOT NULL DEFAULT '1',
  `competitive_region_confirmation_by_rd_needed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress` (
  `congress_id` int(11) NOT NULL,
  `congress_name` varchar(64) NOT NULL DEFAULT '-',
  `congress_breed_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_gender_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_new_trait_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_hair_length_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_category_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_division_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_color_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_title_switch` tinyint(1) NOT NULL DEFAULT '0',
  `congress_manual_select_switch` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_breed`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_breed` (
  `congress_breed_id` int(11) NOT NULL,
  `congress_breed_congress` int(11) NOT NULL,
  `congress_breed_breed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_category`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_category` (
  `congress_category_id` int(11) NOT NULL,
  `congress_category_congress` int(11) NOT NULL,
  `congress_category_category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_color`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_color` (
  `congress_color_id` int(11) NOT NULL,
  `congress_color_congress` int(11) NOT NULL,
  `congress_color_color` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_color_wildcard`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_color_wildcard` (
  `congress_color_wildcard_id` int(11) NOT NULL,
  `congress_color_wildcard_congress` int(11) NOT NULL,
  `congress_color_wildcard_wildcard` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_competitive_class`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_competitive_class` (
  `congress_competitive_class_id` int(11) NOT NULL,
  `congress_competitive_class_congress` int(11) NOT NULL,
  `congress_competitive_class_competitive_class` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_division`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_division` (
  `congress_division_id` int(11) NOT NULL,
  `congress_division_congress` int(11) NOT NULL,
  `congress_division_division` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_gender`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_gender` (
  `congress_gender_id` int(11) NOT NULL,
  `congress_gender_congress` int(11) NOT NULL,
  `congress_gender_gender` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_hair_length`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_hair_length` (
  `congress_hair_length_id` int(11) NOT NULL,
  `congress_hair_length_congress` int(11) NOT NULL,
  `congress_hair_length_hair_length` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_summary_am_session`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_summary_am_session` (
  `ring_id` int(11) NOT NULL,
  `ring_show_day` int(11) NOT NULL,
  `ring_show` int(11) NOT NULL,
  `ring_number` int(11) NOT NULL,
  `ring_name` varchar(64) NOT NULL,
  `Count` bigint(21) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_summary_pm_session`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_summary_pm_session` (
  `ring_id` int(11) NOT NULL,
  `ring_show_day` int(11) NOT NULL,
  `ring_show` int(11) NOT NULL,
  `ring_number` int(11) NOT NULL,
  `ring_name` varchar(64) NOT NULL,
  `Count` bigint(21) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_congress_title`
--

CREATE TABLE IF NOT EXISTS `#__toes_congress_title` (
  `congress_title_id` int(11) NOT NULL,
  `congress_title_congress` int(11) NOT NULL,
  `congress_title_title` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_country`
--

CREATE TABLE IF NOT EXISTS `#__toes_country` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `alpha_2` varchar(2) NOT NULL DEFAULT '',
  `alpha_3` varchar(3) NOT NULL DEFAULT '',
  `competitive_region` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_country_is_part_of_region`
--

CREATE TABLE IF NOT EXISTS `#__toes_country_is_part_of_region` (
  `country_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `since` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_division`
--

CREATE TABLE IF NOT EXISTS `#__toes_division` (
  `division_id` int(11) NOT NULL,
  `division_name` varchar(50) NOT NULL,
  `division_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_entry`
--

CREATE TABLE IF NOT EXISTS `#__toes_entry` (
  `entry_id` int(11) NOT NULL,
  `summary` int(11) DEFAULT NULL,
  `cat` int(11) DEFAULT NULL,
  `show_day` int(11) DEFAULT NULL,
  `entry_participates_AM` tinyint(1) NOT NULL DEFAULT '0',
  `entry_participates_PM` tinyint(1) NOT NULL DEFAULT '0',
  `for_sale` tinyint(1) NOT NULL DEFAULT '0',
  `exhibition_only` tinyint(1) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `copy_cat_name` varchar(255) DEFAULT NULL,
  `copy_cat_prefix` int(11) DEFAULT NULL,
  `copy_cat_title` int(11) DEFAULT NULL,
  `copy_cat_suffix` int(11) DEFAULT NULL,
  `copy_cat_breed` int(11) DEFAULT NULL,
  `copy_cat_hair_length` int(11) NOT NULL,
  `copy_cat_category` int(11) DEFAULT NULL,
  `copy_cat_division` int(11) DEFAULT NULL,
  `copy_cat_color` int(11) DEFAULT NULL,
  `copy_cat_date_of_birth` date DEFAULT NULL,
  `copy_cat_registration_number` varchar(255) DEFAULT NULL,
  `copy_cat_gender` int(11) DEFAULT NULL,
  `copy_cat_id_chip_number` varchar(64) DEFAULT NULL,
  `copy_cat_new_trait` int(1) DEFAULT NULL,
  `copy_cat_sire_name` varchar(255) DEFAULT NULL,
  `copy_cat_dam_name` varchar(255) DEFAULT NULL,
  `copy_cat_breeder_name` varchar(255) DEFAULT NULL,
  `copy_cat_owner_name` varchar(255) DEFAULT NULL,
  `copy_cat_competitive_region` int(11) DEFAULT NULL,
  `copy_cat_sire` int(11) DEFAULT NULL,
  `copy_cat_dam` int(11) DEFAULT NULL,
  `copy_cat_breeder` int(11) DEFAULT NULL,
  `copy_cat_owner` int(11) DEFAULT NULL,
  `copy_cat_agent_name` varchar(255) DEFAULT NULL,
  `copy_cat_lessee_name` varchar(255) DEFAULT NULL,
  `late_entry` tinyint(1) NOT NULL DEFAULT '1',
  `catalog_number` varchar(4) DEFAULT NULL,
  `entry_date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_entry_participates_in_congress`
--

CREATE TABLE IF NOT EXISTS `#__toes_entry_participates_in_congress` (
  `entry_id` int(11) NOT NULL,
  `congress_id` int(11) NOT NULL COMMENT 'this is the ring_id of the congress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_entry_refusal_reason`
--

CREATE TABLE IF NOT EXISTS `#__toes_entry_refusal_reason` (
  `entry_refusal_reason_id` int(11) NOT NULL,
  `entry_refusal_reason_entry` int(11) NOT NULL,
  `entry_refusal_reason_` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entry_refusal_reason_reason` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_entry_status`
--

CREATE TABLE IF NOT EXISTS `#__toes_entry_status` (
  `entry_status_id` int(11) NOT NULL,
  `entry_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_font_size`
--

CREATE TABLE IF NOT EXISTS `#__toes_font_size` (
  `font_size_id` int(11) NOT NULL,
  `font_size_size_value` int(11) NOT NULL,
  `font_size_size_name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_judge`
--

CREATE TABLE IF NOT EXISTS `#__toes_judge` (
  `judge_abbreviation` varchar(3) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `judge_status` int(11) NOT NULL,
  `judge_organization` int(11) NOT NULL DEFAULT '1',
  `judge_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_judge_level`
--

CREATE TABLE IF NOT EXISTS `#__toes_judge_level` (
  `judge_level_id` int(11) NOT NULL,
  `judge_level` varchar(255) NOT NULL,
  `judge_fee` float DEFAULT NULL,
  `judge_level_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_judge_status`
--

CREATE TABLE IF NOT EXISTS `#__toes_judge_status` (
  `judge_status_id` int(11) NOT NULL,
  `judge_status` varchar(45) NOT NULL,
  `judge_status_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_link_requests`
--

CREATE TABLE IF NOT EXISTS `#__toes_link_requests` (
  `link_request_id` int(11) NOT NULL,
  `link_request_user` int(11) NOT NULL,
  `link_request_cat` int(11) NOT NULL,
  `link_request_role` int(11) NOT NULL,
  `link_request_code` varchar(128) NOT NULL,
  `link_request_expiration` date NOT NULL,
  `allow_access` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_log_entries`
--

CREATE TABLE IF NOT EXISTS `#__toes_log_entries` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `time_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_log_placeholders`
--

CREATE TABLE IF NOT EXISTS `#__toes_log_placeholders` (
  `id` int(11) NOT NULL,
  `placeholder_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `time_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_log_shows`
--

CREATE TABLE IF NOT EXISTS `#__toes_log_shows` (
  `id` int(11) NOT NULL,
  `show_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `time_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_log_summaries`
--

CREATE TABLE IF NOT EXISTS `#__toes_log_summaries` (
  `id` int(11) NOT NULL,
  `summary_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `time_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_organization`
--

CREATE TABLE IF NOT EXISTS `#__toes_organization` (
  `organization_id` int(11) NOT NULL,
  `organization_name` varchar(64) NOT NULL,
  `organization_abbreviation` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_organization_has_official`
--

CREATE TABLE IF NOT EXISTS `#__toes_organization_has_official` (
  `organization_has_official_id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `organization_official_type` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_organization_official_type`
--

CREATE TABLE IF NOT EXISTS `#__toes_organization_official_type` (
  `organization_official_type_id` int(11) NOT NULL,
  `organization_official_type` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_paper_size`
--

CREATE TABLE IF NOT EXISTS `#__toes_paper_size` (
  `paper_size_id` int(11) NOT NULL,
  `paper_size` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_pattern`
--

CREATE TABLE IF NOT EXISTS `#__toes_pattern` (
  `id` int(2) DEFAULT NULL,
  `pattern_name` varchar(17) DEFAULT NULL,
  `pattern_series` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_phone`
--

CREATE TABLE IF NOT EXISTS `#__toes_phone` (
  `phone_id` int(11) NOT NULL,
  `phone_international_access_code` varchar(5) DEFAULT NULL,
  `phone_area_code` varchar(5) DEFAULT NULL,
  `phone_number` varchar(45) NOT NULL,
  `phone_type_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_phone_type`
--

CREATE TABLE IF NOT EXISTS `#__toes_phone_type` (
  `phone_type_id` tinyint(4) NOT NULL,
  `phone_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_placeholder`
--

CREATE TABLE IF NOT EXISTS `#__toes_placeholder` (
  `placeholder_id` int(11) NOT NULL,
  `placeholder_show` int(11) NOT NULL,
  `placeholder_exhibitor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_placeholder_day`
--

CREATE TABLE IF NOT EXISTS `#__toes_placeholder_day` (
  `placeholder_day_id` int(11) NOT NULL,
  `placeholder_day_placeholder` int(11) NOT NULL,
  `placeholder_day_showday` int(11) NOT NULL,
  `placeholder_participates_AM` tinyint(1) NOT NULL DEFAULT '1',
  `placeholder_participates_PM` tinyint(1) NOT NULL DEFAULT '1',
  `placeholder_day_placeholder_status` int(11) NOT NULL,
  `placeholder_day_date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_ring`
--

CREATE TABLE IF NOT EXISTS `#__toes_ring` (
  `ring_id` int(11) NOT NULL,
  `ring_show_day` int(11) NOT NULL,
  `ring_format` int(11) NOT NULL DEFAULT '1',
  `ring_judge` int(11) NOT NULL DEFAULT '0',
  `ring_show` int(11) NOT NULL,
  `ring_organization` int(11) NOT NULL DEFAULT '1',
  `ring_number` int(11) NOT NULL DEFAULT '1',
  `ring_name` varchar(64) NOT NULL,
  `ring_timing` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_ring_format`
--

CREATE TABLE IF NOT EXISTS `#__toes_ring_format` (
  `ring_format_id` int(11) NOT NULL,
  `ring_format` varchar(255) NOT NULL,
  `ring_format_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_ring_timing`
--

CREATE TABLE IF NOT EXISTS `#__toes_ring_timing` (
  `ring_timing_id` int(11) NOT NULL,
  `timing` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_shaded_tabby_colors`
--

CREATE TABLE IF NOT EXISTS `#__toes_shaded_tabby_colors` (
  `id` int(2) DEFAULT NULL,
  `color_name` varchar(18) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show`
--

CREATE TABLE IF NOT EXISTS `#__toes_show` (
  `show_id` int(11) NOT NULL,
  `show_start_date` date NOT NULL,
  `show_end_date` date NOT NULL,
  `show_uses_toes` tinyint(1) NOT NULL DEFAULT '1',
  `show_venue` int(11) NOT NULL,
  `show_flyer` text,
  `show_motto` varchar(255) DEFAULT NULL,
  `show_comments` varchar(8192) DEFAULT NULL,
  `show_bring_your_own_cages` tinyint(1) NOT NULL DEFAULT '0',
  `show_display_counts` tinyint(1) NOT NULL DEFAULT '0',
  `show_use_waiting_list` tinyint(4) NOT NULL DEFAULT '0',
  `show_extra_text_for_confirmation` varchar(8192) DEFAULT NULL,
  `show_format` int(11) NOT NULL DEFAULT '1',
  `show_published` tinyint(1) NOT NULL DEFAULT '0',
  `show_status` int(11) NOT NULL DEFAULT '1',
  `show_organization` int(11) NOT NULL DEFAULT '1',
  `catalog_runs` int(11) NOT NULL DEFAULT '0',
  `show_lock_catalog` tinyint(1) NOT NULL DEFAULT '0',
  `show_lock_late_pages` tinyint(1) NOT NULL DEFAULT '0',
  `show_paper_size` int(11) NOT NULL DEFAULT '1',
  `show_currency_used` varchar(10) NOT NULL DEFAULT 'USD',
  `show_cost_total_entries` int(11) NOT NULL DEFAULT '0',
  `show_cost_ex_only_entries` int(11) NOT NULL DEFAULT '0',
  `show_maximum_cost` float NOT NULL DEFAULT '55',
  `show_cost_fixed_rebate` float NOT NULL DEFAULT '0',
  `show_cost_procentual_rebate` int(11) NOT NULL DEFAULT '0',
  `show_cost_invoice_date` date DEFAULT NULL,
  `show_cost_amount_paid` float NOT NULL DEFAULT '0',
  `show_cost_per_entry` float NOT NULL DEFAULT '0.25',
  `show_total_cost` float NOT NULL DEFAULT '0',
  `show_use_club_entry_clerk_address` tinyint(4) NOT NULL DEFAULT '0',
  `show_email_address_entry_clerk` varchar(128) DEFAULT NULL,
  `show_use_club_show_manager_address` tinyint(4) NOT NULL DEFAULT '0',
  `show_email_address_show_manager` varchar(128) DEFAULT NULL,
  `show_is_regional` tinyint(1) NOT NULL DEFAULT '0',
  `show_is_annual` tinyint(1) NOT NULL DEFAULT '0',
  `show_licensed` tinyint(1) NOT NULL DEFAULT '0',
  `show_time_submitted_to_calendar` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `show_print_division_title_in_judges_books` tinyint(1) NOT NULL DEFAULT '0',
  `show_print_extra_lines_for_bod_and_bob_in_judges_book` tinyint(1) DEFAULT '0',
  `show_print_extra_line_at_end_of_color_class_in_judges_book` tinyint(1) DEFAULT '0',
  `show_catalog_font_size` int(11) NOT NULL DEFAULT '1',
  `show_colored_catalog` tinyint(1) NOT NULL DEFAULT '0',
  `show_catalog_cat_names_bold` tinyint(1) NOT NULL DEFAULT '0',
  `show_catalog_page_orientation` int(11) NOT NULL DEFAULT '1',
  `show_allow_exhibitor_cancellation` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show_changes`
--

CREATE TABLE IF NOT EXISTS `#__toes_show_changes` (
  `show_changes_id` int(11) NOT NULL,
  `show_changes_show` int(11) NOT NULL,
  `show_changes_dates_changed` tinyint(1) NOT NULL DEFAULT '0',
  `show_changes_location_changed` tinyint(1) NOT NULL DEFAULT '0',
  `show_changes_show_status` tinyint(1) NOT NULL DEFAULT '0',
  `show_changes_show_format_changed` tinyint(1) NOT NULL DEFAULT '0',
  `show_changes_judges_changed` tinyint(1) NOT NULL DEFAULT '0',
  `show_changes_rings_changed` tinyint(1) NOT NULL DEFAULT '0',
  `show_changes_description_changed` tinyint(1) NOT NULL DEFAULT '0',
  `show_changes_last_changed_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `show_changes_last_changed_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show_class`
--

CREATE TABLE IF NOT EXISTS `#__toes_show_class` (
  `show_class_id` int(11) NOT NULL,
  `show_class` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show_day`
--

CREATE TABLE IF NOT EXISTS `#__toes_show_day` (
  `show_day_id` int(11) NOT NULL,
  `show_day_show` int(11) NOT NULL,
  `show_day_date` datetime DEFAULT NULL,
  `show_day_cat_limit` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show_format`
--

CREATE TABLE IF NOT EXISTS `#__toes_show_format` (
  `show_format_id` int(11) NOT NULL,
  `show_format` varchar(255) NOT NULL,
  `show_format_organization` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show_has_official`
--

CREATE TABLE IF NOT EXISTS `#__toes_show_has_official` (
  `show` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `show_official_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show_official_type`
--

CREATE TABLE IF NOT EXISTS `#__toes_show_official_type` (
  `show_official_type_id` int(11) NOT NULL,
  `show_official_type` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_show_status`
--

CREATE TABLE IF NOT EXISTS `#__toes_show_status` (
  `show_status_id` int(11) NOT NULL,
  `show_status` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_silver_colors`
--

CREATE TABLE IF NOT EXISTS `#__toes_silver_colors` (
  `id` int(2) DEFAULT NULL,
  `color_name` varchar(18) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_smoke_colors`
--

CREATE TABLE IF NOT EXISTS `#__toes_smoke_colors` (
  `A` varchar(2) DEFAULT NULL,
  `B` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_solid_colors`
--

CREATE TABLE IF NOT EXISTS `#__toes_solid_colors` (
  `id` int(2) DEFAULT NULL,
  `color_name` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_states_per_country`
--

CREATE TABLE IF NOT EXISTS `#__toes_states_per_country` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `country_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `abbreviation` varchar(3) NOT NULL,
  `competitive_region` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_summary`
--

CREATE TABLE IF NOT EXISTS `#__toes_summary` (
  `summary_id` int(11) NOT NULL,
  `summary_user` int(11) DEFAULT NULL,
  `summary_show` int(11) DEFAULT NULL,
  `summary_benching_request` varchar(255) DEFAULT NULL,
  `summary_grooming_space` tinyint(1) DEFAULT NULL,
  `summary_single_cages` int(6) UNSIGNED NOT NULL DEFAULT '0',
  `summary_double_cages` int(6) UNSIGNED NOT NULL DEFAULT '0',
  `summary_personal_cages` tinyint(1) DEFAULT NULL,
  `summary_remarks` text,
  `summary_total_fees` float NOT NULL DEFAULT '0',
  `summary_fees_paid` float NOT NULL DEFAULT '0',
  `summary_status` int(11) NOT NULL DEFAULT '1',
  `summary_benching_area` varchar(10) DEFAULT NULL,
  `summary_entry_clerk_note` varchar(2048) DEFAULT NULL,
  `summary_entry_clerk_private_note` varchar(2048) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_summary_status`
--

CREATE TABLE IF NOT EXISTS `#__toes_summary_status` (
  `summary_status_id` int(11) NOT NULL,
  `summary_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_tabby_colors`
--

CREATE TABLE IF NOT EXISTS `#__toes_tabby_colors` (
  `id` int(2) DEFAULT NULL,
  `color_name` varchar(27) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_tortie_colors`
--

CREATE TABLE IF NOT EXISTS `#__toes_tortie_colors` (
  `id` int(1) DEFAULT NULL,
  `color_name` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_user_has_address`
--

CREATE TABLE IF NOT EXISTS `#__toes_user_has_address` (
  `user` int(11) NOT NULL,
  `address` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_user_has_phone`
--

CREATE TABLE IF NOT EXISTS `#__toes_user_has_phone` (
  `user` int(11) NOT NULL,
  `phone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_user_subcribed_to_show`
--

CREATE TABLE IF NOT EXISTS `#__toes_user_subcribed_to_show` (
  `user_subcribed_to_show_id` int(11) NOT NULL,
  `user_subcribed_to_show_user` int(11) NOT NULL,
  `user_subcribed_to_show_show` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__toes_venue`
--

CREATE TABLE IF NOT EXISTS `#__toes_venue` (
  `venue_id` int(11) NOT NULL,
  `venue_name` varchar(255) NOT NULL,
  `venue_website` varchar(255) DEFAULT NULL,
  `venue_address` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `#__toes`
--
ALTER TABLE `#__toes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_address`
--
ALTER TABLE `#__toes_address`
  ADD PRIMARY KEY (`address_id`,`address_type`),
  ADD KEY `fk_address_type_idx` (`address_type`);

--
-- Indexes for table `#__toes_address_type`
--
ALTER TABLE `#__toes_address_type`
  ADD PRIMARY KEY (`address_type_id`);

--
-- Indexes for table `#__toes_breed`
--
ALTER TABLE `#__toes_breed`
  ADD PRIMARY KEY (`breed_id`),
  ADD UNIQUE KEY `breed_short_name_unique` (`breed_abbreviation`,`breed_organization`),
  ADD UNIQUE KEY `breed_name_unique` (`breed_name`,`breed_organization`),
  ADD KEY `fk_breed_status_idx` (`breed_status`),
  ADD KEY `fk_breed_organization__organization_idx` (`breed_organization`);

--
-- Indexes for table `#__toes_breed_category`
--
ALTER TABLE `#__toes_breed_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `#__toes_breed_category_division_color`
--
ALTER TABLE `#__toes_breed_category_division_color`
  ADD PRIMARY KEY (`breed_category_division_color_id`),
  ADD KEY `fk_breed_idx` (`breed`),
  ADD KEY `fk_breed_category_division_color_organization__organization_idx` (`category`),
  ADD KEY `fk_color_idx` (`color`),
  ADD KEY `fk_organization_idx` (`organization`),
  ADD KEY `fk_division_idx` (`division`),
  ADD KEY `unique_combination` (`breed`,`category`,`division`,`color`,`organization`);

--
-- Indexes for table `#__toes_breed_has_status`
--
ALTER TABLE `#__toes_breed_has_status`
  ADD PRIMARY KEY (`breed_has_status_id`);

--
-- Indexes for table `#__toes_breed_status`
--
ALTER TABLE `#__toes_breed_status`
  ADD PRIMARY KEY (`breed_status_id`),
  ADD UNIQUE KEY `breed_status_unique` (`breed_status`,`breed_status_organization`),
  ADD KEY `fk_breed_status_organization__organization_idx` (`breed_status_organization`);

--
-- Indexes for table `#__toes_cat`
--
ALTER TABLE `#__toes_cat`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `fk_cat__gender_idx` (`cat_gender`),
  ADD KEY `fk_cat__breed_idx` (`cat_breed`),
  ADD KEY `fk_cat__category_idx` (`cat_category`),
  ADD KEY `fk_cat__division_idx` (`cat_division`),
  ADD KEY `fk_cat__color_idx` (`cat_color`),
  ADD KEY `fk_cat__prefix_idx` (`cat_prefix`),
  ADD KEY `fk_cat__title_idx` (`cat_title`),
  ADD KEY `fk_cat__suffix_idx` (`cat_suffix`),
  ADD KEY `idx_cat_name` (`cat_name`);

--
-- Indexes for table `#__toes_catalog_page_orientation`
--
ALTER TABLE `#__toes_catalog_page_orientation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_category`
--
ALTER TABLE `#__toes_category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `fk_category_organization__organization_idx` (`category_organization`);

--
-- Indexes for table `#__toes_cat_cat_connection_type`
--
ALTER TABLE `#__toes_cat_cat_connection_type`
  ADD PRIMARY KEY (`cat_cat_connection_type_id`),
  ADD UNIQUE KEY `cat_to_cat_connection_type_UNIQUE` (`cat_to_cat_connection_type`);

--
-- Indexes for table `#__toes_cat_gender`
--
ALTER TABLE `#__toes_cat_gender`
  ADD PRIMARY KEY (`gender_id`);

--
-- Indexes for table `#__toes_cat_hair_length`
--
ALTER TABLE `#__toes_cat_hair_length`
  ADD PRIMARY KEY (`cat_hair_length_id`),
  ADD UNIQUE KEY `cat_hair_length_abbreviation` (`cat_hair_length_abbreviation`,`cat_hair_length`);

--
-- Indexes for table `#__toes_cat_prefix`
--
ALTER TABLE `#__toes_cat_prefix`
  ADD PRIMARY KEY (`cat_prefix_id`),
  ADD UNIQUE KEY `status_short_name_unique` (`cat_prefix_abbreviation`,`cat_prefix_organization`),
  ADD UNIQUE KEY `status_name_unique` (`cat_prefix`,`cat_prefix_organization`),
  ADD KEY `fk_cat_prefix_organization__organization_idx` (`cat_prefix_organization`);

--
-- Indexes for table `#__toes_cat_registration_number`
--
ALTER TABLE `#__toes_cat_registration_number`
  ADD PRIMARY KEY (`cat_registration_number_id`),
  ADD UNIQUE KEY `cat_organization_unique` (`cat_registration_number_cat`,`cat_registration_number_organization`),
  ADD UNIQUE KEY `registration_number_organization_unique` (`cat_registration_number`,`cat_registration_number_organization`),
  ADD KEY `fk_cat_idx` (`cat_registration_number_cat`),
  ADD KEY `fk_cat_registration_number_organization__organization_idx` (`cat_registration_number_organization`);

--
-- Indexes for table `#__toes_cat_relates_to_cat`
--
ALTER TABLE `#__toes_cat_relates_to_cat`
  ADD PRIMARY KEY (`cat_1_is`,`of_cat_2`,`cat_cat_connection_type`),
  ADD KEY `fk_cat_1_idx` (`cat_1_is`),
  ADD KEY `fk_cat_user_connection_type_idx` (`cat_cat_connection_type`),
  ADD KEY `fk_cat_relates_to_cat__cat_2_idx` (`of_cat_2`);

--
-- Indexes for table `#__toes_cat_relates_to_user`
--
ALTER TABLE `#__toes_cat_relates_to_user`
  ADD PRIMARY KEY (`of_cat`,`person_is`,`cat_user_connection_type`),
  ADD KEY `fk_cat_relates_to_user__user_idx` (`person_is`),
  ADD KEY `fk_cat_relates_to_user__cat_idx` (`of_cat`),
  ADD KEY `fk_cat_relates_to_user__connection_type_idx` (`cat_user_connection_type`);

--
-- Indexes for table `#__toes_cat_suffix`
--
ALTER TABLE `#__toes_cat_suffix`
  ADD PRIMARY KEY (`cat_suffix_id`),
  ADD UNIQUE KEY `suffix_short_name_unique` (`cat_suffix_abbreviation`,`cat_suffix_organization`),
  ADD UNIQUE KEY `suffix_name_unique` (`cat_suffix`,`cat_suffix_organization`),
  ADD KEY `fk_cat_suffix_organization__organization_idx` (`cat_suffix_organization`);

--
-- Indexes for table `#__toes_cat_title`
--
ALTER TABLE `#__toes_cat_title`
  ADD PRIMARY KEY (`cat_title_id`),
  ADD UNIQUE KEY `title_name_unique` (`cat_title`,`cat_title_organization`),
  ADD UNIQUE KEY `title_short_name_unique` (`cat_title_organization`,`cat_title_abbreviation`),
  ADD KEY `fk_cat_title_organization__organization_idx` (`cat_title_organization`);

--
-- Indexes for table `#__toes_cat_user_connection_type`
--
ALTER TABLE `#__toes_cat_user_connection_type`
  ADD PRIMARY KEY (`cat_user_connection_type_id`),
  ADD UNIQUE KEY `role_UNIQUE` (`cat_user_connection_type`);

--
-- Indexes for table `#__toes_club`
--
ALTER TABLE `#__toes_club`
  ADD PRIMARY KEY (`club_id`),
  ADD UNIQUE KEY `name_UNIQUE` (`club_name`),
  ADD UNIQUE KEY `abbreviation_UNIQUE` (`club_abbreviation`),
  ADD KEY `fk_club__organization_idx` (`club_organization`),
  ADD KEY `club_competitive_region` (`club_competitive_region`),
  ADD KEY `club_on_toes_bad_debt_list` (`club_on_toes_bad_debt_list`);

--
-- Indexes for table `#__toes_club_official`
--
ALTER TABLE `#__toes_club_official`
  ADD PRIMARY KEY (`club_official_id`,`club`,`user`,`club_official_type`),
  ADD KEY `fk_club_official__user_idx` (`user`),
  ADD KEY `fk_club_official__club_idx` (`club`),
  ADD KEY `fk_club_official__official_type_idx` (`club_official_type`);

--
-- Indexes for table `#__toes_club_official_type`
--
ALTER TABLE `#__toes_club_official_type`
  ADD PRIMARY KEY (`club_official_type_id`),
  ADD UNIQUE KEY `club_official_type_UNIQUE` (`club_official_type`);

--
-- Indexes for table `#__toes_club_organizes_show`
--
ALTER TABLE `#__toes_club_organizes_show`
  ADD PRIMARY KEY (`club`,`show`),
  ADD KEY `club_idx` (`club`),
  ADD KEY `show_idx` (`show`);

--
-- Indexes for table `#__toes_color`
--
ALTER TABLE `#__toes_color`
  ADD PRIMARY KEY (`color_id`),
  ADD UNIQUE KEY `color_unique` (`color_name`,`color_organization`),
  ADD KEY `fk_color_organization__organization_idx` (`color_organization`);

--
-- Indexes for table `#__toes_color_status`
--
ALTER TABLE `#__toes_color_status`
  ADD PRIMARY KEY (`color_status_id`),
  ADD UNIQUE KEY `color_status` (`color_status`);

--
-- Indexes for table `#__toes_competitive_region`
--
ALTER TABLE `#__toes_competitive_region`
  ADD PRIMARY KEY (`competitive_region_id`);

--
-- Indexes for table `#__toes_congress`
--
ALTER TABLE `#__toes_congress`
  ADD PRIMARY KEY (`congress_id`),
  ADD UNIQUE KEY `congress_id` (`congress_id`),
  ADD KEY `congress_name` (`congress_name`),
  ADD KEY `congress_new_trait_switch` (`congress_new_trait_switch`);

--
-- Indexes for table `#__toes_congress_breed`
--
ALTER TABLE `#__toes_congress_breed`
  ADD PRIMARY KEY (`congress_breed_id`),
  ADD UNIQUE KEY `unique_congress_breed` (`congress_breed_congress`,`congress_breed_breed`);

--
-- Indexes for table `#__toes_congress_category`
--
ALTER TABLE `#__toes_congress_category`
  ADD PRIMARY KEY (`congress_category_id`),
  ADD UNIQUE KEY `unique_congress_category` (`congress_category_congress`,`congress_category_category`);

--
-- Indexes for table `#__toes_congress_color`
--
ALTER TABLE `#__toes_congress_color`
  ADD PRIMARY KEY (`congress_color_id`),
  ADD UNIQUE KEY `unique_congress_color` (`congress_color_congress`,`congress_color_color`);

--
-- Indexes for table `#__toes_congress_color_wildcard`
--
ALTER TABLE `#__toes_congress_color_wildcard`
  ADD PRIMARY KEY (`congress_color_wildcard_id`),
  ADD KEY `congress_color_wildcard_congress` (`congress_color_wildcard_congress`);

--
-- Indexes for table `#__toes_congress_competitive_class`
--
ALTER TABLE `#__toes_congress_competitive_class`
  ADD PRIMARY KEY (`congress_competitive_class_id`),
  ADD KEY `congress_competitive_class_congress` (`congress_competitive_class_congress`,`congress_competitive_class_competitive_class`);

--
-- Indexes for table `#__toes_congress_division`
--
ALTER TABLE `#__toes_congress_division`
  ADD PRIMARY KEY (`congress_division_id`),
  ADD UNIQUE KEY `unique_congress_division` (`congress_division_congress`,`congress_division_division`);

--
-- Indexes for table `#__toes_congress_gender`
--
ALTER TABLE `#__toes_congress_gender`
  ADD PRIMARY KEY (`congress_gender_id`),
  ADD UNIQUE KEY `unique_congress_gender` (`congress_gender_congress`,`congress_gender_gender`);

--
-- Indexes for table `#__toes_congress_hair_length`
--
ALTER TABLE `#__toes_congress_hair_length`
  ADD PRIMARY KEY (`congress_hair_length_id`),
  ADD UNIQUE KEY `unique_congress_hair_length` (`congress_hair_length_congress`,`congress_hair_length_hair_length`);

--
-- Indexes for table `#__toes_congress_title`
--
ALTER TABLE `#__toes_congress_title`
  ADD PRIMARY KEY (`congress_title_id`),
  ADD UNIQUE KEY `unique_congress_title` (`congress_title_congress`,`congress_title_title`);

--
-- Indexes for table `#__toes_country`
--
ALTER TABLE `#__toes_country`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region` (`competitive_region`);

--
-- Indexes for table `#__toes_country_is_part_of_region`
--
ALTER TABLE `#__toes_country_is_part_of_region`
  ADD KEY `country_id` (`country_id`,`region_id`);

--
-- Indexes for table `#__toes_division`
--
ALTER TABLE `#__toes_division`
  ADD PRIMARY KEY (`division_id`),
  ADD KEY `fk_division_organization__organization_idx` (`division_organization`);

--
-- Indexes for table `#__toes_entry`
--
ALTER TABLE `#__toes_entry`
  ADD PRIMARY KEY (`entry_id`),
  ADD KEY `fb_join_fk_entry_INDEX` (`summary`),
  ADD KEY `entry_date_created` (`entry_date_created`),
  ADD KEY `copy_cat_id_chip_number` (`copy_cat_id_chip_number`),
  ADD KEY `entry_participates_AM` (`entry_participates_AM`,`entry_participates_PM`);

--
-- Indexes for table `#__toes_entry_participates_in_congress`
--
ALTER TABLE `#__toes_entry_participates_in_congress`
  ADD PRIMARY KEY (`entry_id`,`congress_id`);

--
-- Indexes for table `#__toes_entry_refusal_reason`
--
ALTER TABLE `#__toes_entry_refusal_reason`
  ADD PRIMARY KEY (`entry_refusal_reason_id`),
  ADD KEY `entry_refusal_reason_entry` (`entry_refusal_reason_entry`,`entry_refusal_reason_`);

--
-- Indexes for table `#__toes_entry_status`
--
ALTER TABLE `#__toes_entry_status`
  ADD PRIMARY KEY (`entry_status_id`);

--
-- Indexes for table `#__toes_font_size`
--
ALTER TABLE `#__toes_font_size`
  ADD PRIMARY KEY (`font_size_id`),
  ADD UNIQUE KEY `font_size_size_value` (`font_size_size_value`,`font_size_size_name`);

--
-- Indexes for table `#__toes_judge`
--
ALTER TABLE `#__toes_judge`
  ADD PRIMARY KEY (`judge_id`,`user`,`judge_organization`,`judge_status`,`judge_level`),
  ADD UNIQUE KEY `person_organization_unique` (`judge_organization`,`user`),
  ADD KEY `fk_user_idx` (`user`),
  ADD KEY `fk_judge__organization_idx` (`judge_organization`),
  ADD KEY `fk_judge_level_idx` (`judge_level`);

--
-- Indexes for table `#__toes_judge_level`
--
ALTER TABLE `#__toes_judge_level`
  ADD PRIMARY KEY (`judge_level_id`),
  ADD UNIQUE KEY `status_UNIQUE` (`judge_level`),
  ADD KEY `fk_judge_level__organization_idx` (`judge_level_organization`);

--
-- Indexes for table `#__toes_judge_status`
--
ALTER TABLE `#__toes_judge_status`
  ADD PRIMARY KEY (`judge_status_id`),
  ADD UNIQUE KEY `status_UNIQUE` (`judge_status`),
  ADD KEY `fk_organization_idx` (`judge_status_organization`);

--
-- Indexes for table `#__toes_link_requests`
--
ALTER TABLE `#__toes_link_requests`
  ADD PRIMARY KEY (`link_request_id`),
  ADD UNIQUE KEY `link_request_code` (`link_request_code`),
  ADD KEY `link_request_user` (`link_request_user`,`link_request_cat`,`link_request_role`,`link_request_expiration`);

--
-- Indexes for table `#__toes_log_entries`
--
ALTER TABLE `#__toes_log_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_log_placeholders`
--
ALTER TABLE `#__toes_log_placeholders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_log_shows`
--
ALTER TABLE `#__toes_log_shows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_log_summaries`
--
ALTER TABLE `#__toes_log_summaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_organization`
--
ALTER TABLE `#__toes_organization`
  ADD PRIMARY KEY (`organization_id`),
  ADD UNIQUE KEY `organization_name` (`organization_name`),
  ADD UNIQUE KEY `organization_abbreviation` (`organization_abbreviation`);

--
-- Indexes for table `#__toes_organization_has_official`
--
ALTER TABLE `#__toes_organization_has_official`
  ADD PRIMARY KEY (`organization_has_official_id`);

--
-- Indexes for table `#__toes_organization_official_type`
--
ALTER TABLE `#__toes_organization_official_type`
  ADD PRIMARY KEY (`organization_official_type_id`),
  ADD UNIQUE KEY `role_UNIQUE` (`organization_official_type`);

--
-- Indexes for table `#__toes_paper_size`
--
ALTER TABLE `#__toes_paper_size`
  ADD PRIMARY KEY (`paper_size_id`),
  ADD UNIQUE KEY `paper_size` (`paper_size`);

--
-- Indexes for table `#__toes_phone`
--
ALTER TABLE `#__toes_phone`
  ADD PRIMARY KEY (`phone_id`,`phone_type_id`),
  ADD KEY `fk_phone_type_idx` (`phone_type_id`);

--
-- Indexes for table `#__toes_phone_type`
--
ALTER TABLE `#__toes_phone_type`
  ADD PRIMARY KEY (`phone_type_id`);

--
-- Indexes for table `#__toes_placeholder`
--
ALTER TABLE `#__toes_placeholder`
  ADD PRIMARY KEY (`placeholder_id`),
  ADD KEY `placeholder_show` (`placeholder_show`,`placeholder_exhibitor`);

--
-- Indexes for table `#__toes_placeholder_day`
--
ALTER TABLE `#__toes_placeholder_day`
  ADD PRIMARY KEY (`placeholder_day_id`),
  ADD KEY `placeholder_day_placeholder` (`placeholder_day_placeholder`,`placeholder_day_showday`),
  ADD KEY `placeholder_day_placeholder_status` (`placeholder_day_placeholder_status`),
  ADD KEY `placeholder_day_date_created` (`placeholder_day_date_created`),
  ADD KEY `placeholder_participates_AM` (`placeholder_participates_AM`,`placeholder_participates_PM`);

--
-- Indexes for table `#__toes_ring`
--
ALTER TABLE `#__toes_ring`
  ADD PRIMARY KEY (`ring_id`,`ring_show_day`,`ring_format`,`ring_judge`,`ring_show`,`ring_organization`),
  ADD KEY `fk_ring__show_idx` (`ring_show`),
  ADD KEY `fk_ring__show_day_idx` (`ring_show_day`),
  ADD KEY `fk_ring__ring_format_idx` (`ring_format`),
  ADD KEY `fk_ring__organization_idx` (`ring_organization`);

--
-- Indexes for table `#__toes_ring_format`
--
ALTER TABLE `#__toes_ring_format`
  ADD PRIMARY KEY (`ring_format_id`),
  ADD UNIQUE KEY `format_UNIQUE` (`ring_format`),
  ADD KEY `fk_ring_format__organization_idx` (`ring_format_organization`);

--
-- Indexes for table `#__toes_ring_timing`
--
ALTER TABLE `#__toes_ring_timing`
  ADD PRIMARY KEY (`ring_timing_id`);

--
-- Indexes for table `#__toes_show`
--
ALTER TABLE `#__toes_show`
  ADD PRIMARY KEY (`show_id`,`show_venue`,`show_status`,`show_format`,`show_organization`),
  ADD KEY `fk_show_status_idx` (`show_status`),
  ADD KEY `fk_show_format_idx` (`show_format`),
  ADD KEY `fk_venue_idx` (`show_venue`),
  ADD KEY `fk_show__organization_idx` (`show_organization`),
  ADD KEY `show_uses_toes` (`show_uses_toes`),
  ADD KEY `show_catalog_font_size` (`show_catalog_font_size`);

--
-- Indexes for table `#__toes_show_changes`
--
ALTER TABLE `#__toes_show_changes`
  ADD PRIMARY KEY (`show_changes_id`),
  ADD KEY `show_changes_last_changed_by` (`show_changes_last_changed_by`),
  ADD KEY `show_changes_show` (`show_changes_show`),
  ADD KEY `show_changes_last_changed_on` (`show_changes_last_changed_on`);

--
-- Indexes for table `#__toes_show_class`
--
ALTER TABLE `#__toes_show_class`
  ADD PRIMARY KEY (`show_class_id`),
  ADD UNIQUE KEY `show_class` (`show_class`);

--
-- Indexes for table `#__toes_show_day`
--
ALTER TABLE `#__toes_show_day`
  ADD PRIMARY KEY (`show_day_id`,`show_day_show`),
  ADD KEY `fk_show_day__show_idx` (`show_day_show`);

--
-- Indexes for table `#__toes_show_format`
--
ALTER TABLE `#__toes_show_format`
  ADD PRIMARY KEY (`show_format_id`,`show_format_organization`,`show_format`),
  ADD UNIQUE KEY `show_format_organization_unique` (`show_format`,`show_format_organization`),
  ADD KEY `fk_show_format__organization_idx` (`show_format_organization`);

--
-- Indexes for table `#__toes_show_has_official`
--
ALTER TABLE `#__toes_show_has_official`
  ADD PRIMARY KEY (`show`,`user`,`show_official_type`),
  ADD KEY `user_idx` (`user`),
  ADD KEY `fk_show_has_official__official_type_idx` (`show_official_type`),
  ADD KEY `fk_show_has_official__show_idx` (`show`);

--
-- Indexes for table `#__toes_show_official_type`
--
ALTER TABLE `#__toes_show_official_type`
  ADD PRIMARY KEY (`show_official_type_id`),
  ADD UNIQUE KEY `role_UNIQUE` (`show_official_type`);

--
-- Indexes for table `#__toes_show_status`
--
ALTER TABLE `#__toes_show_status`
  ADD PRIMARY KEY (`show_status_id`),
  ADD UNIQUE KEY `show_status_UNIQUE` (`show_status`);

--
-- Indexes for table `#__toes_states_per_country`
--
ALTER TABLE `#__toes_states_per_country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `#__toes_summary`
--
ALTER TABLE `#__toes_summary`
  ADD PRIMARY KEY (`summary_id`);

--
-- Indexes for table `#__toes_summary_status`
--
ALTER TABLE `#__toes_summary_status`
  ADD PRIMARY KEY (`summary_status_id`),
  ADD UNIQUE KEY `summary_status` (`summary_status`);

--
-- Indexes for table `#__toes_user_has_address`
--
ALTER TABLE `#__toes_user_has_address`
  ADD PRIMARY KEY (`user`,`address`),
  ADD KEY `fk_user_has_address_address__address_idx` (`address`),
  ADD KEY `fk_user_has_address_comprofiler__user_idx` (`user`);

--
-- Indexes for table `#__toes_user_has_phone`
--
ALTER TABLE `#__toes_user_has_phone`
  ADD PRIMARY KEY (`user`,`phone`),
  ADD KEY `fk_user_has_phone_phone__phone_idx` (`phone`),
  ADD KEY `fk_user_has_phone_users__user` (`user`);

--
-- Indexes for table `#__toes_user_subcribed_to_show`
--
ALTER TABLE `#__toes_user_subcribed_to_show`
  ADD PRIMARY KEY (`user_subcribed_to_show_id`),
  ADD UNIQUE KEY `user_show_unique` (`user_subcribed_to_show_user`,`user_subcribed_to_show_show`),
  ADD KEY `user_subscribed_to_show_user` (`user_subcribed_to_show_user`,`user_subcribed_to_show_show`);

--
-- Indexes for table `#__toes_venue`
--
ALTER TABLE `#__toes_venue`
  ADD PRIMARY KEY (`venue_id`,`venue_address`),
  ADD UNIQUE KEY `name_UNIQUE` (`venue_name`),
  ADD KEY `fk_address_idx` (`venue_address`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `#__toes`
--
ALTER TABLE `#__toes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `#__toes_address`
--
ALTER TABLE `#__toes_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_address_type`
--
ALTER TABLE `#__toes_address_type`
  MODIFY `address_type_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `#__toes_breed`
--
ALTER TABLE `#__toes_breed`
  MODIFY `breed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;
--
-- AUTO_INCREMENT for table `#__toes_breed_category_division_color`
--
ALTER TABLE `#__toes_breed_category_division_color`
  MODIFY `breed_category_division_color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268653;
--
-- AUTO_INCREMENT for table `#__toes_breed_has_status`
--
ALTER TABLE `#__toes_breed_has_status`
  MODIFY `breed_has_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;
--
-- AUTO_INCREMENT for table `#__toes_breed_status`
--
ALTER TABLE `#__toes_breed_status`
  MODIFY `breed_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `#__toes_cat`
--
ALTER TABLE `#__toes_cat`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_catalog_page_orientation`
--
ALTER TABLE `#__toes_catalog_page_orientation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `#__toes_category`
--
ALTER TABLE `#__toes_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `#__toes_cat_cat_connection_type`
--
ALTER TABLE `#__toes_cat_cat_connection_type`
  MODIFY `cat_cat_connection_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `#__toes_cat_gender`
--
ALTER TABLE `#__toes_cat_gender`
  MODIFY `gender_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `#__toes_cat_hair_length`
--
ALTER TABLE `#__toes_cat_hair_length`
  MODIFY `cat_hair_length_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `#__toes_cat_prefix`
--
ALTER TABLE `#__toes_cat_prefix`
  MODIFY `cat_prefix_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `#__toes_cat_registration_number`
--
ALTER TABLE `#__toes_cat_registration_number`
  MODIFY `cat_registration_number_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_cat_suffix`
--
ALTER TABLE `#__toes_cat_suffix`
  MODIFY `cat_suffix_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `#__toes_cat_title`
--
ALTER TABLE `#__toes_cat_title`
  MODIFY `cat_title_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `#__toes_cat_user_connection_type`
--
ALTER TABLE `#__toes_cat_user_connection_type`
  MODIFY `cat_user_connection_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `#__toes_club`
--
ALTER TABLE `#__toes_club`
  MODIFY `club_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_club_official`
--
ALTER TABLE `#__toes_club_official`
  MODIFY `club_official_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_club_official_type`
--
ALTER TABLE `#__toes_club_official_type`
  MODIFY `club_official_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `#__toes_color`
--
ALTER TABLE `#__toes_color`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4638;
--
-- AUTO_INCREMENT for table `#__toes_color_status`
--
ALTER TABLE `#__toes_color_status`
  MODIFY `color_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `#__toes_competitive_region`
--
ALTER TABLE `#__toes_competitive_region`
  MODIFY `competitive_region_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `#__toes_congress_breed`
--
ALTER TABLE `#__toes_congress_breed`
  MODIFY `congress_breed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_category`
--
ALTER TABLE `#__toes_congress_category`
  MODIFY `congress_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_color`
--
ALTER TABLE `#__toes_congress_color`
  MODIFY `congress_color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_color_wildcard`
--
ALTER TABLE `#__toes_congress_color_wildcard`
  MODIFY `congress_color_wildcard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_competitive_class`
--
ALTER TABLE `#__toes_congress_competitive_class`
  MODIFY `congress_competitive_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_division`
--
ALTER TABLE `#__toes_congress_division`
  MODIFY `congress_division_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_gender`
--
ALTER TABLE `#__toes_congress_gender`
  MODIFY `congress_gender_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_hair_length`
--
ALTER TABLE `#__toes_congress_hair_length`
  MODIFY `congress_hair_length_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_congress_title`
--
ALTER TABLE `#__toes_congress_title`
  MODIFY `congress_title_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_country`
--
ALTER TABLE `#__toes_country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;
--
-- AUTO_INCREMENT for table `#__toes_division`
--
ALTER TABLE `#__toes_division`
  MODIFY `division_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `#__toes_entry`
--
ALTER TABLE `#__toes_entry`
  MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_entry_refusal_reason`
--
ALTER TABLE `#__toes_entry_refusal_reason`
  MODIFY `entry_refusal_reason_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_judge`
--
ALTER TABLE `#__toes_judge`
  MODIFY `judge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_judge_level`
--
ALTER TABLE `#__toes_judge_level`
  MODIFY `judge_level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `#__toes_judge_status`
--
ALTER TABLE `#__toes_judge_status`
  MODIFY `judge_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `#__toes_link_requests`
--
ALTER TABLE `#__toes_link_requests`
  MODIFY `link_request_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `#__toes_log_entries`
--
ALTER TABLE `#__toes_log_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_log_placeholders`
--
ALTER TABLE `#__toes_log_placeholders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_log_shows`
--
ALTER TABLE `#__toes_log_shows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_log_summaries`
--
ALTER TABLE `#__toes_log_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_organization`
--
ALTER TABLE `#__toes_organization`
  MODIFY `organization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `#__toes_organization_has_official`
--
ALTER TABLE `#__toes_organization_has_official`
  MODIFY `organization_has_official_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_organization_official_type`
--
ALTER TABLE `#__toes_organization_official_type`
  MODIFY `organization_official_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `#__toes_paper_size`
--
ALTER TABLE `#__toes_paper_size`
  MODIFY `paper_size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `#__toes_phone`
--
ALTER TABLE `#__toes_phone`
  MODIFY `phone_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `#__toes_phone_type`
--
ALTER TABLE `#__toes_phone_type`
  MODIFY `phone_type_id` tinyint(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `#__toes_placeholder`
--
ALTER TABLE `#__toes_placeholder`
  MODIFY `placeholder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_placeholder_day`
--
ALTER TABLE `#__toes_placeholder_day`
  MODIFY `placeholder_day_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_ring`
--
ALTER TABLE `#__toes_ring`
  MODIFY `ring_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_ring_format`
--
ALTER TABLE `#__toes_ring_format`
  MODIFY `ring_format_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `#__toes_ring_timing`
--
ALTER TABLE `#__toes_ring_timing`
  MODIFY `ring_timing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `#__toes_show`
--
ALTER TABLE `#__toes_show`
  MODIFY `show_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_show_changes`
--
ALTER TABLE `#__toes_show_changes`
  MODIFY `show_changes_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_show_class`
--
ALTER TABLE `#__toes_show_class`
  MODIFY `show_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `#__toes_show_day`
--
ALTER TABLE `#__toes_show_day`
  MODIFY `show_day_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_show_format`
--
ALTER TABLE `#__toes_show_format`
  MODIFY `show_format_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `#__toes_show_official_type`
--
ALTER TABLE `#__toes_show_official_type`
  MODIFY `show_official_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `#__toes_show_status`
--
ALTER TABLE `#__toes_show_status`
  MODIFY `show_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `#__toes_states_per_country`
--
ALTER TABLE `#__toes_states_per_country`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `#__toes_summary`
--
ALTER TABLE `#__toes_summary`
  MODIFY `summary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_summary_status`
--
ALTER TABLE `#__toes_summary_status`
  MODIFY `summary_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `#__toes_user_subcribed_to_show`
--
ALTER TABLE `#__toes_user_subcribed_to_show`
  MODIFY `user_subcribed_to_show_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `#__toes_venue`
--
ALTER TABLE `#__toes_venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=371;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
