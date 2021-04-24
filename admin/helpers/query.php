<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * TOES query component helper.
 *
 * @package	Joomla.Administrator
 * @subpackage	com_toes
 */
class TOESQueryHelper {
	
	public static function getCatViewQuery($where = array()){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`c`.`cat_id`,`c`.`cat_breed`,`c`.`cat_hair_length`,`c`.`cat_category`");
		$query->select("`c`.`cat_division`,`c`.`cat_color`,`c`.`cat_date_of_birth`,`c`.`cat_gender`");
		$query->select("`c`.`cat_prefix`,`c`.`cat_title`,`c`.`cat_name`,`c`.`cat_suffix`,`c`.`cat_sire`");
		$query->select("`c`.`cat_dam`,`c`.`cat_breeder`,`c`.`cat_owner`,`c`.`cat_lessee`");
		$query->select("`c`.`cat_competitive_region`,`c`.`cat_new_trait`,`c`.`cat_id_chip_number`,`c`.`cat_record_added`");
		$query->select("`b`.`breed_name`,`b`.`breed_abbreviation`");
		$query->select("`cat`.`category`,`d`.`division_name`,`clr`.`color_name`");
		$query->select("`bs`.`breed_status`, `crn`.`cat_registration_number`");
		$query->select("`tcp`.`cat_prefix` AS `cat_prefix_name`,`tcp`.`cat_prefix_abbreviation`");
		$query->select("`tcs`.`cat_suffix` AS `cat_suffix_name`,`tcs`.`cat_suffix_abbreviation`");
		$query->select("`t`.`cat_title` AS `cat_title_name`, `t`.`cat_title_abbreviation`");
		$query->select("`tcg`.`gender_name`,`tcg`.`gender_short_name`");
		$query->select("`cr`.`competitive_region_name`,`cr`.`competitive_region_abbreviation`");
		$query->select("`chl`.`cat_hair_length_abbreviation` AS `breed_hair_length`");
		
		$query->from("`#__toes_cat` AS `c`");
		$query->join("left","`#__toes_breed` as b ON `c`.`cat_breed` = `b`.`breed_id`");
		$query->join("left","`#__toes_category` as `cat` ON `c`.`cat_category` = `cat`.`category_id`");
		$query->join("left","`#__toes_division` as `d` ON `c`.`cat_division` = `d`.`division_id`");
		$query->join("left","`#__toes_color` as `clr` ON `c`.`cat_color` = `clr`.`color_id`");
		$query->join("left","`#__toes_breed_has_status` as `bhs` ON ( `bhs`.`breed_has_status_breed` = `b`.`breed_id` AND NOW() BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until` )");
		$query->join("left","`#__toes_breed_status` as `bs` ON `bs`.`breed_status_id` = `bhs`.`breed_has_status_status`");
		$query->join("left","`#__toes_cat_registration_number` AS `crn` ON `c`.cat_id = `crn`.`cat_registration_number_cat`");
		$query->join("left","`#__toes_cat_prefix` as `tcp` ON `c`.cat_prefix = `tcp`.`cat_prefix_id`");
		$query->join("left","`#__toes_cat_suffix` as `tcs` ON `c`.cat_suffix = `tcs`.`cat_suffix_id`");
		$query->join("left","`#__toes_cat_title` AS `t` ON t.cat_title_id = `c`.`cat_title`");
		$query->join("left","`#__toes_cat_gender` as `tcg` ON `c`.cat_gender = `tcg`.`gender_id`");
		$query->join("left","`#__toes_competitive_region` AS `cr` ON `c`.`cat_competitive_region` = `cr`.`competitive_region_id`");
		$query->join("left","`#__toes_cat_hair_length` as `chl` ON `c`.`cat_hair_length` = `chl`.`cat_hair_length_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getShowViewQuery($where = array()){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`s`.`show_id`,`s`.`show_start_date`,`s`.`show_end_date`,`s`.`show_uses_toes`");
		$query->select("`s`.`show_flyer`,`s`.`show_comments`,`s`.`show_bring_your_own_cages`,`s`.`catalog_runs`,`s`.`show_motto`");
		$query->select("`s`.`show_extra_text_for_confirmation`,`s`.`show_currency_used`,`s`.`show_paper_size`,`s`.`show_published`");
		$query->select("`s`.`show_cost_per_entry`,`s`.`show_total_cost`,`s`.`show_use_club_entry_clerk_address`");
		$query->select("`s`.`show_email_address_entry_clerk`,`s`.`show_use_club_show_manager_address`,`s`.`show_email_address_show_manager`");
		$query->select("`s`.`show_use_waiting_list`,`s`.`show_display_counts`");
		$query->select("`s`.`show_lock_catalog`,`s`.`show_lock_late_pages`,`s`.`show_cost_total_entries`");
		$query->select("`s`.`show_cost_ex_only_entries`,`s`.`show_maximum_cost`,`s`.`show_cost_fixed_rebate`");
		$query->select("`s`.`show_cost_procentual_rebate`,`s`.`show_cost_invoice_date`,`s`.`show_cost_amount_paid`");
		$query->select("`s`.`show_is_regional`,`s`.`show_is_annual`,`s`.`show_print_extra_lines_for_bod_and_bob_in_judges_book`");
		$query->select("`s`.`show_print_extra_line_at_end_of_color_class_in_judges_book`,`s`.`show_licensed`");
		$query->select("`s`.`show_catalog_font_size`,`s`.`show_colored_catalog`,`s`.`show_catalog_cat_names_bold`");
		$query->select("`s`.`show_print_division_title_in_judges_books`,`cp`.`page_ortientation`,`s`.`show_allow_exhibitor_cancellation`");

		$query->select("concat(monthname(`s`.`show_start_date`),' ',cast(year(`s`.`show_start_date`) as char charset utf8)) AS `show_month`");
		$query->select("if(
							(date_format(`s`.`show_start_date`,'%Y') = date_format(`s`.`show_end_date`,'%Y')),
							if((date_format(`s`.`show_start_date`,'%b') = date_format(`s`.`show_end_date`,'%b')),
								concat(date_format(`s`.`show_start_date`,'%e'),'-',date_format(`s`.`show_end_date`,'%e'),' ',date_format(`s`.`show_start_date`,'%b'),' ',date_format(`s`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b'),date_format(`s`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b %Y'),date_format(`s`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");

		$query->select("`ve`.`venue_id`,`ve`.`venue_name`,`va`.`address_line_1`,`va`.`address_line_2`,`va`.`address_line_3`");
		$query->select("`va`.`address_zip_code`,`va`.`address_city` AS `address_city`,`va`.`address_state` AS `address_state`,`va`.`address_country` AS `address_country`,`va`.`address_latitude`,`va`.`address_longitude`");
		$query->select("concat_ws(' ',`va`.`address_city`, `va`.`address_state`, `va`.`address_country`) AS `Show_location`");
		//$query->select("concat_ws(' ',`city`.`name`, `state`.`name`, `cntry`.`name`) AS `Show_location`");
		//,`city`.`name` AS `address_city`,`state`.`name` AS `address_state`,`cntry`.`name` AS `address_country`
		$query->select("`sf`.`show_format`,`ss`.`show_status`,`or`.`organization_abbreviation`");
		$query->select("`c`.`club_id`,`c`.`club_abbreviation`,`c`.`club_name`");
		$query->select("`cr`.`competitive_region_id`,`cr`.`competitive_region_abbreviation`");
		
		
		$query->from("`#__toes_show` AS `s`");
		$query->join("left","`#__toes_venue` AS `ve` ON `ve`.`venue_id` = `s`.`show_venue`");
		$query->join("left","`#__toes_address` AS `va` ON `va`.`address_id` = `ve`.`venue_address`");
		//$query->join("left","`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left","`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left","`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left","`#__toes_show_format` AS `sf` ON `sf`.`show_format_id` = `s`.`show_format`");
		$query->join("left","`#__toes_show_status` AS `ss` ON `ss`.`show_status_id` = `s`.`show_status`");
		$query->join("left","`#__toes_organization` AS `or` ON `or`.`organization_id` = `s`.`show_organization`");
		$query->join("left","`#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `s`.`show_id`");
		$query->join("left","`#__toes_club` AS `c` ON `c`.`club_id` = `cos`.`club`");
		$query->join("left","`#__toes_competitive_region` AS `cr` ON `cr`.`competitive_region_id` = `c`.`club_competitive_region`");
		$query->join("left","`#__toes_catalog_page_orientation` AS `cp` ON `cp`.`id` = `s`.`show_catalog_page_orientation`");
		
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getUserViewQuery($where = array()){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`u`.`id`,`u`.`name`,`u`.`email`");
		$query->select("`cprof`.`cb_address1`,`cprof`.`cb_address2`,`cprof`.`cb_address3`, `cprof`.`cb_zip`");
		//$query->select(" `cprofstate`.`name` AS `address_state`,`cprofcntry`.`name` AS `address_country`");
		$query->select("`cprof`.`cb_city` AS `address_city`, `cprof`.`cb_state` AS `address_state`,`cprof`.`cb_country` AS `address_country`");
		$query->select("`cprof`.`cb_phonenumber` AS `phonenumber`, `cprof`.`cb_privacy` AS `private`, `cprof`.`cb_tica_region`");
				
		$query->from("`#__users` AS `u`");
		$query->join("left", "`#__comprofiler`  AS `cprof` ON `u`.`id` = `cprof`.`user_id`");
		//$query->join("left", "`#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `cprof`.`cb_country`");//`cprofcity`.`name` AS `address_city`,
		//$query->join("left", "`#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `cprof`.`cb_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `cprof`.`cb_city`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getJudgesViewQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`ju`.`judge_id`, `jl`.`judge_level`, `jl`.`judge_fee`, `js`.`judge_status`");
		$query->select("`u`.`id` AS `user_id`,`u`.`name`,`u`.`email`");
		$query->select("`cprof`.`cb_address1`,`cprof`.`cb_address2`,`cprof`.`cb_address3`, `cprof`.`cb_zip`");
		//$query->select("`cprofcity`.`name` AS `address_city`, `cprofstate`.`name` AS `address_state`,`cprofcntry`.`name` AS `address_country`");
		$query->select("`cprof`.`cb_city` AS `address_city`, `cprof`.`cb_state` AS `address_state`,`cprof`.`cb_country` AS `address_country`");
				
		$query->from("`#__toes_judge` AS `ju`");
		$query->join("left", "`#__users`  AS `u` ON `ju`.`user` = `u`.`id`");
		$query->join("left", "`#__comprofiler`  AS `cprof` ON `ju`.`user` = `cprof`.`user_id`");
		//$query->join("left", "`#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `cprof`.`cb_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `cprof`.`cb_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `cprof`.`cb_city`");
		$query->join("left", "`#__toes_judge_level` `jl` ON `jl`.`judge_level_id` = `ju`.`judge_level`");
		$query->join("left", "`#__toes_judge_status` AS `js` ON `js`.`judge_status_id` = `ju`.`judge_status`");
		$query->join("left", "`#__toes_organization` AS `o` ON `o`.`organization_id` = `ju`.`judge_organization`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getEntryViewQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`e`.`entry_id`, `e`.`summary`,`es`.`summary_user`, `u`.`email`, `cprof`.`firstname`, `cprof`.`lastname`, `cprof`.`cb_phonenumber` AS `phonenumber`, "
			. "`es`.`summary_benching_request`,`es`.`summary_grooming_space`, `es`.`summary_single_cages`, `es`.`summary_double_cages`, `es`.`summary_personal_cages`, "
			. "`es`.`summary_remarks`, `es`.`summary_total_fees`, `es`.`summary_fees_paid`, `e`.`status`, `estat`.`entry_status`, `e`.`entry_participates_AM`, `e`.`entry_participates_PM`, "
			. "`e`.`cat`, `e`.`show_day`, `e`.`copy_cat_name`, `e`.`copy_cat_prefix`,  `pfx`.`cat_prefix_abbreviation`,  `pfx`.`cat_prefix`, `e`.`copy_cat_title`,  `ttl`.`cat_title_abbreviation`, "
			. "`ttl`.`cat_title`, `e`.`copy_cat_suffix`,  `sfx`.`cat_suffix_abbreviation`,  `sfx`.`cat_suffix`, `e`.`entry_age_years` AS `age_years`,`e`.`entry_age_months` AS `age_months`, "
			. "`e`.`copy_cat_breed`, `b`.`breed_abbreviation`,  `b`.`breed_name`, `b`.`breed_hair_length`,  `bs`.`breed_status`,  `e`.`copy_cat_hair_length`, `hl`.`cat_hair_length_abbreviation`, "
			. "`e`.`copy_cat_category`, `ctg`.`category`, `e`.`copy_cat_division`, `dvs`.`division_name`, `e`.`copy_cat_color`, `clr`.`color_name`, `e`.`copy_cat_date_of_birth`,"
			. "`e`.`copy_cat_registration_number`, `e`.`copy_cat_gender`, `gdr`.`gender_short_name`, `gdr`.`gender_name`, `e`.`copy_cat_id_chip_number`, `e`.`copy_cat_new_trait`, "
			. "`e`.`copy_cat_sire_name`, `e`.`copy_cat_dam_name`, `e`.`copy_cat_breeder_name`, `e`.`copy_cat_owner_name`, `e`.`copy_cat_lessee_name`, `e`.`copy_cat_agent_name`, "
			. "`e`.`copy_cat_competitive_region`,  `rgn`.`competitive_region_abbreviation`, `rgn`.`competitive_region_name`, `e`.`exhibition_only`, `e`.`for_sale`, `e`.`copy_cat_sire`, "
			. "`e`.`copy_cat_dam`, `e`.`copy_cat_breeder`, `e`.`copy_cat_owner`, `sd`.`show_day_id`, `sd`.`show_day_show`, `sd`.`show_day_date`, `s`.`show_id`, `s`.`show_start_date`, "
			. "`s`.`show_end_date`, `s`.`show_venue`, `sv`.`venue_name`, `va`.`address_line_1`, `va`.`address_line_2`, `va`.`address_line_3`,  "
			. "`va`.`address_zip_code`,  `s`.`show_flyer`, `s`.`show_motto`, `s`.`show_format` as `show_format_id`, `sf`.`show_format`, `s`.`show_published`, "
			. "`s`.`show_status` as `show_status_id`, `ss`.`show_status`, `e`.`late_entry` , `e`.`catalog_number`, `e`.`entry_date_created`, "
			. "`va`.`address_city`, `va`.`address_state`, `va`.`address_country`");
		//`cntry`.`name` AS `address_country`,`city`.`name` AS `address_city`, `state`.`name` AS `address_state`,
		$query->select("(
							(`e`.`copy_cat_name` = `cat`.`cat_name`) AND
							(`e`.`copy_cat_prefix` = `cat`.`cat_prefix`) AND
							(`e`.`copy_cat_title` = `cat`.`cat_title`) AND
							(`e`.`copy_cat_suffix` = `cat`.`cat_suffix`) AND
							(`e`.`copy_cat_sire_name`= `cat`.`cat_sire`) AND
							(`e`.`copy_cat_dam_name` = `cat_dam`) AND
							(`e`.`copy_cat_breeder_name` = `cat`.`cat_breeder`) AND
							(`e`.`copy_cat_owner_name`= `cat`.`cat_owner`) AND
							(`e`.`copy_cat_lessee_name` = `cat`.`cat_lessee`) 
						) AS `minor_differences`");
						
		$query->select("(
							(`e`.`copy_cat_breed` = `cat`.`cat_breed`) AND
							(`e`.`copy_cat_category`= `cat`.`cat_category`) AND
							(`e`.`copy_cat_division`=`cat`.`cat_division`) AND
							(`e`.`copy_cat_color` = `cat`.`cat_color`) AND
							(`e`.`copy_cat_hair_length` = `cat`.`cat_hair_length`) AND
							(`e`.`copy_cat_date_of_birth`=`cat`.`cat_date_of_birth`) AND
							(`e`.`copy_cat_gender` = `cat`.`cat_gender`) AND
							(`e`.`copy_cat_registration_number`) AND
							(`e`.`copy_cat_new_trait` = `cat`.`cat_new_trait`) AND
							(`e`.`copy_cat_competitive_region` = `cat`.`cat_competitive_region`) 
						) AS `major_differences`");
		
		$query->select("IF( (`gdr`.`gender_name` = 'Female Spay') OR (`gdr`.`gender_name` = 'Male Neuter'), TRUE, FALSE) AS `is_alter`");
		$query->select("IF (`b`.`breed_name` LIKE 'Household%', TRUE, FALSE) AS `is_HHP`");
		
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,false,true) AS is_kitten");
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,true,false) AS is_adult");
		
		$query->select('e.entry_show, e.entry_show_class,`sc`.`show_class` AS `Show_Class`');
				
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_summary` AS `es` ON (`e`.`summary` = `es`.`summary_id`)");
		$query->join("left", "`#__users`  AS `u` ON (`es`.`summary_user` = `u`.`id`)");
		$query->join("left", "`#__comprofiler`  AS `cprof` ON (`es`.`summary_user` = `cprof`.`user_id`)");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_breed` AS `b` ON (`e`.`copy_cat_breed` = `b`.`breed_id`)");
		$query->join("left", "`#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND `show_day_date` BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)");
		$query->join("left", "`#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)");
		$query->join("left", "`#__toes_cat_hair_length` AS `hl` ON (`e`.`copy_cat_hair_length` = `hl`.`cat_hair_length_id`)");
		$query->join("left", "`#__toes_category` AS `ctg` ON (`e`.`copy_cat_category` = `ctg`.`category_id`)");
		$query->join("left", "`#__toes_division` AS `dvs` ON (`e`.`copy_cat_division` = `dvs`.`division_id`)");
		$query->join("left", "`#__toes_color` AS `clr` ON (`e`.`copy_cat_color` = `clr`.`color_id`)");
		$query->join("left", "`#__toes_cat_gender` AS `gdr` ON (`e`.`copy_cat_gender` = `gdr`.`gender_id`)");
		$query->join("left", "`#__toes_cat_prefix` AS `pfx` ON (`e`.`copy_cat_prefix` = `pfx`.`cat_prefix_id`)");
		$query->join("left", "`#__toes_cat_title` AS `ttl` ON (`e`.`copy_cat_title` = `ttl`.`cat_title_id`)");
		$query->join("left", "`#__toes_cat_suffix` AS `sfx` ON (`e`.`copy_cat_suffix` = `sfx`.`cat_suffix_id`)");
		$query->join("left", "`#__toes_competitive_region` AS `rgn` ON (`e`.`copy_cat_competitive_region` = `rgn`.`competitive_region_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_format` AS `sf` ON (`s`.`show_format` = `sf`.`show_format_id`)");
		$query->join("left", "`#__toes_show_status` AS `ss` ON (`s`.`show_status` = `ss`.`show_status_id`)");
		$query->join("left", "`#__toes_cat` AS `cat` ON (`e`.`cat` = `cat`.`cat_id`)");
		$query->join("left", "`#__toes_show_class` AS `sc` ON `e`.`entry_show_class` = `sc`.`show_class_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getEntryFullViewQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		$query->select("`e`.`entry_id`, `e`.`summary`,`es`.`summary_user`, `u`.`email`, `cprof`.`firstname`, `cprof`.`lastname`, `cprof`.`cb_phonenumber` AS `phonenumber`, "
			. "`es`.`summary_benching_request`,`es`.`summary_grooming_space`, `es`.`summary_single_cages`, `es`.`summary_double_cages`, `es`.`summary_personal_cages`, "
			. "`es`.`summary_remarks`, `es`.`summary_total_fees`, `es`.`summary_fees_paid`, `e`.`status`, `estat`.`entry_status`, `e`.`entry_participates_AM`, `e`.`entry_participates_PM`, "
			. "`e`.`cat`, `e`.`show_day`, `e`.`copy_cat_name`, `e`.`copy_cat_prefix`,  `pfx`.`cat_prefix_abbreviation`,  `pfx`.`cat_prefix`, `e`.`copy_cat_title`,  `ttl`.`cat_title_abbreviation`, "
			. "`ttl`.`cat_title`, `e`.`copy_cat_suffix`,  `sfx`.`cat_suffix_abbreviation`,  `sfx`.`cat_suffix`, `e`.`entry_age_years` AS `age_years`,`e`.`entry_age_months` AS `age_months`, "
			. "`e`.`copy_cat_breed`, `b`.`breed_abbreviation`,  `b`.`breed_name`, `b`.`breed_hair_length`,  `bs`.`breed_status`,  `e`.`copy_cat_hair_length`, `hl`.`cat_hair_length_abbreviation`, "
			. "`e`.`copy_cat_category`, `ctg`.`category`, `e`.`copy_cat_division`, `dvs`.`division_name`, `e`.`copy_cat_color`, `clr`.`color_name`, `e`.`copy_cat_date_of_birth`,"
			. "`e`.`copy_cat_registration_number`, `e`.`copy_cat_gender`, `gdr`.`gender_short_name`, `gdr`.`gender_name`, `e`.`copy_cat_id_chip_number`, `e`.`copy_cat_new_trait`, "
			. "`e`.`copy_cat_sire_name`, `e`.`copy_cat_dam_name`, `e`.`copy_cat_breeder_name`, `e`.`copy_cat_owner_name`, `e`.`copy_cat_lessee_name`, `e`.`copy_cat_agent_name`, "
			. "`e`.`copy_cat_competitive_region`,  `rgn`.`competitive_region_abbreviation`, `rgn`.`competitive_region_name`, `e`.`exhibition_only`, `e`.`for_sale`, `e`.`copy_cat_sire`, "
			. "`e`.`copy_cat_dam`, `e`.`copy_cat_breeder`, `e`.`copy_cat_owner`, `sd`.`show_day_id`, `sd`.`show_day_show`, `sd`.`show_day_date`, `s`.`show_id`, `s`.`show_start_date`, "
			. "`s`.`show_end_date`, `s`.`show_venue`, `sv`.`venue_name`, `va`.`address_line_1`, `va`.`address_line_2`, `va`.`address_line_3`, "
			. "`va`.`address_zip_code`,  `s`.`show_flyer`, `s`.`show_motto`, `s`.`show_format` as `show_format_id`, `sf`.`show_format`, `s`.`show_published`, "
			. "`s`.`show_status` as `show_status_id`, `ss`.`show_status`, `e`.`late_entry` , `e`.`catalog_number`, `e`.`entry_date_created`, "
			. "`va`.`address_city`, `va`.`address_state`, `va`.`address_country`");
		//`city`.`name` AS `address_city`, `state`.`name` AS `address_state`, `cntry`.`name` AS `address_country`,
		
		$query->select("(
							(`e`.`copy_cat_name` = `cat`.`cat_name`) AND
							(`e`.`copy_cat_prefix` = `cat`.`cat_prefix`) AND
							(`e`.`copy_cat_title` = `cat`.`cat_title`) AND
							(`e`.`copy_cat_suffix` = `cat`.`cat_suffix`) AND
							(`e`.`copy_cat_sire_name`= `cat`.`cat_sire`) AND
							(`e`.`copy_cat_dam_name` = `cat_dam`) AND
							(`e`.`copy_cat_breeder_name` = `cat`.`cat_breeder`) AND
							(`e`.`copy_cat_owner_name`= `cat`.`cat_owner`) AND
							(`e`.`copy_cat_lessee_name` = `cat`.`cat_lessee`) 
						) AS `minor_differences`");
						
		$query->select("(
							(`e`.`copy_cat_breed` = `cat`.`cat_breed`) AND
							(`e`.`copy_cat_category`= `cat`.`cat_category`) AND
							(`e`.`copy_cat_division`=`cat`.`cat_division`) AND
							(`e`.`copy_cat_color` = `cat`.`cat_color`) AND
							(`e`.`copy_cat_hair_length` = `cat`.`cat_hair_length`) AND
							(`e`.`copy_cat_date_of_birth`=`cat`.`cat_date_of_birth`) AND
							(`e`.`copy_cat_gender` = `cat`.`cat_gender`) AND
							(`e`.`copy_cat_registration_number`) AND
							(`e`.`copy_cat_new_trait` = `cat`.`cat_new_trait`) AND
							(`e`.`copy_cat_competitive_region` = `cat`.`cat_competitive_region`) 
						) AS `major_differences`");
		
		$query->select("IF( (`gdr`.`gender_name` = 'Female Spay') OR (`gdr`.`gender_name` = 'Male Neuter'), TRUE, FALSE) AS `is_alter`");
		$query->select("IF (`b`.`breed_name` LIKE 'Household%', TRUE, FALSE) AS `is_HHP`");
		
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,false,true) AS is_kitten");
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,true,false) AS is_adult");
		
		$query->select('e.entry_show, e.entry_show_class,`sc`.`show_class`, `sc`.`show_class` AS `Show_Class`');
				
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_summary` AS `es` ON (`e`.`summary` = `es`.`summary_id`)");
		$query->join("left", "`#__users`  AS `u` ON (`es`.`summary_user` = `u`.`id`)");
		$query->join("left", "`#__comprofiler`  AS `cprof` ON (`es`.`summary_user` = `cprof`.`user_id`)");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_breed` AS `b` ON (`e`.`copy_cat_breed` = `b`.`breed_id`)");
		$query->join("left", "`#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND `show_day_date` BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)");
		$query->join("left", "`#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)");
		$query->join("left", "`#__toes_cat_hair_length` AS `hl` ON (`e`.`copy_cat_hair_length` = `hl`.`cat_hair_length_id`)");
		$query->join("left", "`#__toes_category` AS `ctg` ON (`e`.`copy_cat_category` = `ctg`.`category_id`)");
		$query->join("left", "`#__toes_division` AS `dvs` ON (`e`.`copy_cat_division` = `dvs`.`division_id`)");
		$query->join("left", "`#__toes_color` AS `clr` ON (`e`.`copy_cat_color` = `clr`.`color_id`)");
		$query->join("left", "`#__toes_cat_gender` AS `gdr` ON (`e`.`copy_cat_gender` = `gdr`.`gender_id`)");
		$query->join("left", "`#__toes_cat_prefix` AS `pfx` ON (`e`.`copy_cat_prefix` = `pfx`.`cat_prefix_id`)");
		$query->join("left", "`#__toes_cat_title` AS `ttl` ON (`e`.`copy_cat_title` = `ttl`.`cat_title_id`)");
		$query->join("left", "`#__toes_cat_suffix` AS `sfx` ON (`e`.`copy_cat_suffix` = `sfx`.`cat_suffix_id`)");
		$query->join("left", "`#__toes_competitive_region` AS `rgn` ON (`e`.`copy_cat_competitive_region` = `rgn`.`competitive_region_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_format` AS `sf` ON (`s`.`show_format` = `sf`.`show_format_id`)");
		$query->join("left", "`#__toes_show_status` AS `ss` ON (`s`.`show_status` = `ss`.`show_status_id`)");
		$query->join("left", "`#__toes_cat` AS `cat` ON (`e`.`cat` = `cat`.`cat_id`)");
		$query->join("left", "`#__toes_show_class` AS `sc` ON `e`.`entry_show_class` = `sc`.`show_class_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		$query->order("`sc`.`show_class_id` ASC");
		$query->order("`b`.`breed_name` ASC");
		$query->order("`ctg`.`category_id` ASC");
		$query->order("`dvs`.`division_id` ASC");
		$query->order("`clr`.`color_id` ASC");
		$query->order("`e`.`cat` ASC");
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
		
	}
	
	public static function getCatalogNumberingbasisQuery($where = array()){
		
		$query = self::getEntryFullViewQuery();
		
		//$query->select("`sc`.`show_class`");
		$query->select("CONCAT(`ctg`.`category`,' ',`dvs`.`division_name`, ' Division') AS `catalog_division`");
		$query->select("CONCAT_WS(' ', TRIM(CONCAT(IF(`pfx`.`cat_prefix_abbreviation`=NULL,'',CONCAT(`pfx`.`cat_prefix_abbreviation`,' ')), IF(`ttl`.`cat_title_abbreviation`,'',CONCAT(`ttl`.`cat_title_abbreviation`,' ')),IF(`sfx`.`cat_suffix_abbreviation`,'',`sfx`.`cat_suffix_abbreviation`))),`e`.`copy_cat_name`) AS `catalog_cat_name` ");
		$query->select("CONCAT(`e`.`entry_age_years`,'.',`e`.`entry_age_months`,' ',`gdr`.`gender_short_name`) AS `catalog_age_and_gender`");
		$query->select("IF(`e`.`copy_cat_registration_number`=NULL,'',`e`.`copy_cat_registration_number`) AS `catalog_registration_number`, `e`.`copy_cat_id_chip_number` AS `catalog_id_chip_number`");
		$query->select("UPPER(DATE_FORMAT(`e`.`copy_cat_date_of_birth`,'%b %d, %Y')) AS `catalog_birthdate`");
		$query->select("TRIM( CONCAT(IF(`pfx`.`cat_prefix_abbreviation`=NULL,'',CONCAT(`pfx`.`cat_prefix_abbreviation`,' ')), IF(`ttl`.`cat_title_abbreviation`,'',CONCAT(`ttl`.`cat_title_abbreviation`,' ')),IF(`sfx`.`cat_suffix_abbreviation`,'',`sfx`.`cat_suffix_abbreviation`)) ) AS `catalog_awards` ");
		$query->select("`e`.`copy_cat_sire_name` AS `catalog_sire`, `e`.`copy_cat_dam_name` AS `catalog_dam`");
		$query->select("IF(`e`.`copy_cat_breeder_name`=`e`.`copy_cat_owner_name`,CONCAT('B/O: ',`e`.`copy_cat_breeder_name`),CONCAT('B: ', `e`.`copy_cat_breeder_name`)) AS `catalog_breeder`");
		$query->select("IF(`e`.`copy_cat_breeder_name`=`e`.`copy_cat_owner_name`,NULL,CONCAT('O: ',`e`.`copy_cat_owner_name`)) AS `catalog_owner`");
		$query->select("IF(`e`.`copy_cat_lessee_name`=NULL,NULL,IF(`e`.`copy_cat_lessee_name`='',NULL,CONCAT('L: ',`e`.`copy_cat_lessee_name`))) AS `catalog_lessee`");
		$query->select("IF(`e`.`copy_cat_agent_name`=NULL,NULL,IF(`e`.`copy_cat_agent_name`='',NULL,CONCAT('A: ',`e`.`copy_cat_agent_name`))) AS `catalog_agent`");
		$query->select("`rgn`.`competitive_region_abbreviation` AS `catalog_region`, `hl`.`cat_hair_length_abbreviation` AS `hair_length_abbreviation`");
		//$query->select("`cprof`.`cb_address1` , `cprof`.`cb_address2` , `cprof`.`cb_address3` , `cprofcity`.`name` AS `cb_city` , `cprof`.`cb_zip` , `cprofstate`.`name` AS `cb_state` , `cprofcntry`.`name` AS `cb_country`");
		$query->select("`cprof`.`cb_address1` , `cprof`.`cb_address2` , `cprof`.`cb_address3` , `cprof`.`cb_city` , `cprof`.`cb_zip` , `cprof`.`cb_state` , `cprof`.`cb_country`");
		$query->select("`club`.`club_name`, `club`.`club_abbreviation`");
		$query->select("`gdr`.`gender_short_name` AS `cat_gender_abbreviation`");

		//$query->join("left", "`#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `cprof`.`cb_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `cprof`.`cb_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `cprof`.`cb_city`");
		$query->join("left","`#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`entry_show`");
		$query->join("left","`#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`");
		
		$query->where("`sc`.`show_class_id` >0");
		$query->where("`sc`.`show_class_id` <= 17");
		$query->where("`estat`.`entry_status` IN ('Accepted', 'Confirmed', 'Confirmed & Paid')");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}

		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getShowSummariesQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		$query->select("`sc`.`show_class`, `s`.`show_id`, `sd`.`show_day_id`, `sd`.`show_day_date`, COUNT(`e`.`entry_id`) AS `cat_count`, `c`.`club_name` , `c`.`club_abbreviation`");
		//$query->select("concat_ws(' ',`city`.`name`,`state`.`name`,`cntry`.`name`) AS `Show_location`");
		$query->select("concat_ws(' ',`va`.`address_city`,`va`.`address_state`,`va`.`address_country`) AS `Show_location`");
		$query->select("if(
							(date_format(`s`.`show_start_date`,'%Y') = date_format(`s`.`show_end_date`,'%Y')),
							if((date_format(`s`.`show_start_date`,'%b') = date_format(`s`.`show_end_date`,'%b')),
								concat(date_format(`s`.`show_start_date`,'%e'),'-',date_format(`s`.`show_end_date`,'%e'),' ',date_format(`s`.`show_start_date`,'%b'),' ',date_format(`s`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b'),date_format(`s`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b %Y'),date_format(`s`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");
		
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_class` AS `sc` ON (`e`.`entry_show_class` = `sc`.`show_class_id`)");
		$query->join("left", "`#__toes_club_organizes_show` AS `cos` ON (`cos`.`show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_club` AS `c` ON (`c`.`club_id` = `cos`.`club`)");

		$query->where("((`estat`.`entry_status` = 'New') OR (`estat`.`entry_status` = 'Accepted') OR (`estat`.`entry_status` = 'Confirmed') OR (`estat`.`entry_status` = 'Confirmed & Paid'))");
		$query->group("`e`.`show_day`,`sc`.`show_class`");
		$query->order("`sc`.`show_class` ASC, `e`.`show_day` ASC");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getShowSummariesAMSessionQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("`sc`.`show_class`, `s`.`show_id`, `sd`.`show_day_id`, `sd`.`show_day_date`, COUNT(`e`.`entry_id`) AS `cat_count`, `c`.`club_name` , `c`.`club_abbreviation`");
		//$query->select("concat_ws(' ',`city`.`name`,`state`.`name`,`cntry`.`name`) AS `Show_location`");
		$query->select("concat_ws(' ',`va`.`address_city`,`va`.`address_state`,`va`.`address_country`) AS `Show_location`");
		$query->select("if(
							(date_format(`s`.`show_start_date`,'%Y') = date_format(`s`.`show_end_date`,'%Y')),
							if((date_format(`s`.`show_start_date`,'%b') = date_format(`s`.`show_end_date`,'%b')),
								concat(date_format(`s`.`show_start_date`,'%e'),'-',date_format(`s`.`show_end_date`,'%e'),' ',date_format(`s`.`show_start_date`,'%b'),' ',date_format(`s`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b'),date_format(`s`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b %Y'),date_format(`s`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");
		
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_class` AS `sc` ON (`e`.`entry_show_class` = `sc`.`show_class_id`)");
		$query->join("left", "`#__toes_club_organizes_show` AS `cos` ON (`cos`.`show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_club` AS `c` ON (`c`.`club_id` = `cos`.`club`)");

		$query->where("`e`.`entry_participates_AM` = 1");
		$query->where("((`estat`.`entry_status` = 'New') OR (`estat`.`entry_status` = 'Accepted') OR (`estat`.`entry_status` = 'Confirmed') OR (`estat`.`entry_status` = 'Confirmed & Paid'))");
		$query->group("`sc`.`show_class`, `e`.`show_day`");
		$query->order("`sc`.`show_class` ASC, `e`.`show_day` ASC");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getShowSummariesPMSessionQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("`sc`.`show_class`, `s`.`show_id`, `sd`.`show_day_id`, `sd`.`show_day_date`, COUNT(`e`.`entry_id`) AS `cat_count`, `c`.`club_name` , `c`.`club_abbreviation`");
		//$query->select("concat_ws(' ',`city`.`name`,`state`.`name`,`cntry`.`name`) AS `Show_location`");
		$query->select("concat_ws(' ',`va`.`address_city`,`va`.`address_state`,`va`.`address_country`) AS `Show_location`");
		$query->select("if(
							(date_format(`s`.`show_start_date`,'%Y') = date_format(`s`.`show_end_date`,'%Y')),
							if((date_format(`s`.`show_start_date`,'%b') = date_format(`s`.`show_end_date`,'%b')),
								concat(date_format(`s`.`show_start_date`,'%e'),'-',date_format(`s`.`show_end_date`,'%e'),' ',date_format(`s`.`show_start_date`,'%b'),' ',date_format(`s`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b'),date_format(`s`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b %Y'),date_format(`s`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");
		
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_class` AS `sc` ON (`e`.`entry_show_class` = `sc`.`show_class_id`)");
		$query->join("left", "`#__toes_club_organizes_show` AS `cos` ON (`cos`.`show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_club` AS `c` ON (`c`.`club_id` = `cos`.`club`)");

		$query->where("`e`.`entry_participates_PM` = 1");
		$query->where("((`estat`.`entry_status` = 'New') OR (`estat`.`entry_status` = 'Accepted') OR (`estat`.`entry_status` = 'Confirmed') OR (`estat`.`entry_status` = 'Confirmed & Paid'))");
		$query->group("`sc`.`show_class`, `e`.`show_day`");
		$query->order("`sc`.`show_class` ASC, `e`.`show_day` ASC");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getCongressSummaryQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`ring_id` , `ring_show_day` , `ring_show` , `ring_number` , `ring_name` , COUNT( `entry_id` ) AS `Count`");
		$query->from("`#__toes_ring`");
		$query->join("left","`#__toes_entry_participates_in_congress` ON ( `ring_id` = `congress_id` )");
		$query->where("`ring_format` = 3");
		$query->group("`ring_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getCongressSummaryAMSessionQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		$query->select("`r`.`ring_id` AS `ring_id`,`r`.`ring_show_day` AS `ring_show_day`,`r`.`ring_show` AS `ring_show`,`r`.`ring_number` AS `ring_number`");
		$query->select("`r`.`ring_name` AS `ring_name`,count(distinct `p`.`entry_id`) AS `Count`");
		$query->from("`#__toes_ring` AS `r`");
		$query->join("left","`#__toes_entry_participates_in_congress` AS `p` ON ( `r`.`ring_id` = `p`.`congress_id` )");
		$query->join("left","`#__toes_entry` AS `e` ON (`p`.`entry_id` = `e`.`entry_id`)");
		$query->where("`r`.`ring_format` = 3");
		$query->where("`e`.`entry_participates_AM` = 1");
		$query->group("`r`.`ring_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}

		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getCongressSummaryPMSessionQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		$query->select("`r`.`ring_id` AS `ring_id`,`r`.`ring_show_day` AS `ring_show_day`,`r`.`ring_show` AS `ring_show`,`r`.`ring_number` AS `ring_number`");
		$query->select("`r`.`ring_name` AS `ring_name`,count(distinct `p`.`entry_id`) AS `Count`");
		$query->from("`#__toes_ring` AS `r`");
		$query->join("left","`#__toes_entry_participates_in_congress` AS `p` ON ( `r`.`ring_id` = `p`.`congress_id` )");
		$query->join("left","`#__toes_entry` AS `e` ON (`p`.`entry_id` = `e`.`entry_id`)");
		$query->where("`r`.`ring_format` = 3");
		$query->where("`e`.`entry_participates_PM` = 1");
		$query->group("`r`.`ring_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getJudgesBookData($where = array()) {
		
		$query = self::getEntryFullViewQuery();
		$query->select("CONCAT(`b`.`breed_abbreviation`,' - ',`clr`.`color_name`) AS `judges_book_color`, `sc`.`show_class`");
		$query->select("CONCAT(`e`.`entry_age_years`,'.',`e`.`entry_age_months`,' ', `gdr`.`gender_short_name`) AS `judges_book_age_and_gender`");
		$query->select("CONCAT(`ctg`.`category`,' ',`dvs`.`division_name`, ' Division') AS `catalog_division`");
		$query->where("`sc`.`show_class_id` > 0");
		$query->where("`sc`.`show_class_id` < 17");
		$query->where("( (`estat`.`entry_status` = 'Accepted') OR(`estat`.`entry_status` = 'Confirmed') OR (`estat`.`entry_status` = 'Confirmed & Paid') )");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getJudgesBookCongressData($where = array()) {
		
		$query = self::getEntryFullViewQuery();
		$query->select("CONCAT(`b`.`breed_abbreviation`,' - ',`clr`.`color_name`) AS `judges_book_color`, `sc`.`show_class`");
		$query->select("CONCAT(`e`.`entry_age_years`,'.',`e`.`entry_age_months`,' ', `gdr`.`gender_short_name`) AS `judges_book_age_and_gender`");
		$query->select("CONCAT(`ctg`.`category`,' ',`dvs`.`division_name`, ' Division') AS `catalog_division`");
		$query->select("`cong`.`ring_name`, `cong`.`ring_id`, `cong`.`ring_show_day` AS `show_day`");
		$query->join("left", "`#__toes_entry_participates_in_congress` AS `epic` ON `epic`.`entry_id` = `e`.`entry_id`");
		$query->join("left", "`#__toes_ring` AS `cong` ON `epic`.`congress_id` = `cong`.`ring_id`");
		$query->where("`sc`.`show_class_id` > 0");
		$query->where("`sc`.`show_class_id` <= 17");
		$query->where("`cong`.`ring_format` = 3");
		$query->where("`estat`.`entry_status` IN ('Accepted','Confirmed','Confirmed & Paid')");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getSummaryAndEntriesPerDayPerExhibitorQuery($where = array()) {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('distinct(`s`.`summary_user`) as user_id, IF(c.lastname IS NOT NULL OR c.firstname IS NOT NULL, CONCAT_WS( " ", c.firstname,c.lastname), u.name) AS `exhibitor`');

		$query->select('s.summary_id, s.summary_user, s.summary_show, s.summary_single_cages, s.summary_double_cages, s.summary_benching_request');
		$query->select('s.summary_grooming_space, s.summary_personal_cages, s.summary_remarks, s.summary_total_fees');
		$query->select('s.summary_fees_paid, s.summary_benching_area, s.summary_entry_clerk_note, s.summary_entry_clerk_private_note');

		$query->select('COUNT(`e`.`cat`) AS `entries_per_day`');
		$query->select('`e`.`show_day`, `sd`.`show_day_date`');
			
		$query->from('`#__toes_summary` AS `s`');
		$query->join('left', '`#__comprofiler` AS `c` ON `c`.`id` = `s`.`summary_user`');
		$query->join('left', '`#__users` AS `u` ON `u`.`id` = `c`.`id`');
		$query->join('left', '`#__toes_entry` AS `e` ON `e`.`summary` = `s`.`summary_id`');
		$query->join('left', '`#__toes_entry_status` AS `es` ON `es`.`entry_status_id` = `e`.`status`');
		$query->join('left', '`#__toes_show_day` AS `sd` ON `sd`.`show_day_id` = `e`.`show_day`');

		$query->where('`es`.`entry_status` IN ("Accepted", "Confirmed", "Confirmed & Paid")');
		$query->group('`s`.`summary_show`,`c`.`lastname`, `c`.`firstname`, `e`.`show_day`');
		$query->order('`s`.`summary_show` ASC,`c`.`lastname` ASC, `c`.`firstname` ASC, `e`.`show_day` ASC');
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getPlaceholdersPerDayPerExhibitorQuery($where = array()) {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('distinct(`s`.`summary_user`) as user_id, IF(c.lastname IS NOT NULL OR c.firstname IS NOT NULL, CONCAT_WS( " ", c.firstname, c.lastname), u.name) AS `exhibitor`');

		$query->select('s.summary_id, s.summary_user, s.summary_show, s.summary_single_cages, s.summary_double_cages, s.summary_benching_request');
		$query->select('s.summary_grooming_space, s.summary_personal_cages, s.summary_remarks, s.summary_total_fees');
		$query->select('s.summary_fees_paid, s.summary_benching_area, s.summary_entry_clerk_note, s.summary_entry_clerk_private_note');

		$query->select('`pd`.`placeholder_day_showday` AS `placeholder_day_showday`,`sd`.`show_day_date` AS `show_day_date`');
		$query->select('count(`pd`.`placeholder_day_id`) AS `placeholders_per_day`,`p`.`placeholder_exhibitor` AS `placeholder_exhibitor`');
		$query->select('`p`.`placeholder_show` AS `placeholder_show`');
			
		$query->from('`#__toes_summary` AS `s`');
		$query->join('left', '`#__comprofiler` AS `c` ON `c`.`id` = `s`.`summary_user`');
		$query->join('left', '`#__users` AS `u` ON `u`.`id` = `c`.`id`');
		$query->join('left', '`#__toes_placeholder` AS `p` ON `s`.`summary_id` = `p`.`placeholder_summary`');
		$query->join('left', '`#__toes_placeholder_day` AS `pd` ON `pd`.`placeholder_day_placeholder` = `p`.`placeholder_id`');
		$query->join('left', '`#__toes_show_day` AS `sd` ON `sd`.`show_day_id` = `pd`.`placeholder_day_showday`');
		$query->join('left', '`#__toes_entry_status` AS `es` ON `es`.`entry_status_id` = `pd`.`placeholder_day_placeholder_status`');

		$query->where('`es`.`entry_status` IN ("Accepted", "Confirmed", "Confirmed & Paid")');
		$query->group('`p`.`placeholder_show`,`c`.`lastname`,`c`.`firstname`,`pd`.`placeholder_day_showday`');
		$query->order('`p`.`placeholder_show` ASC,`c`.`lastname` ASC,`c`.`firstname` ASC,`pd`.`placeholder_day_showday` ASC');
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
		
	}

	public static function getExhibitorListBasisQuery($where = array()) {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('`s`.`summary_user` AS `User`, IF(`cprof`.`lastname` IS NOT NULL OR `cprof`.`firstname` IS NOT NULL, CONCAT_WS( " ", `cprof`.`lastname` , `cprof`.`firstname`), u.name ) AS `Exhibitor`, `u`.`email`');
		$query->select('CONCAT_WS( " ", `cprof`.`cb_address1` , `cprof`.`cb_address2` , `cprof`.`cb_address3` ) AS `Address`');
		$query->select('CONCAT_WS( " ", `cprof`.`cb_city` , `cprof`.`cb_zip` , `cprof`.`cb_state` ) AS `City`');
		//$query->select('`cprofcntry`.`name` AS `Country`');
		$query->select('`cprof`.`cb_country` AS `Country`');
		$query->select('GROUP_CONCAT( DISTINCT(`e`.`catalog_number`) ORDER BY CAST( `e`.`catalog_number` AS UNSIGNED ) ) AS `Entries` ');
		$query->select('`show`.`show_id` , `e`.`late_entry` ');//, concat_ws(" ",`city`.`name`,`state`.`name`,`cntry`.`name`) AS Show_location
		$query->select("if(
							(date_format(`show`.`show_start_date`,'%Y') = date_format(`show`.`show_end_date`,'%Y')),
							if((date_format(`show`.`show_start_date`,'%b') = date_format(`show`.`show_end_date`,'%b')),
								concat(date_format(`show`.`show_start_date`,'%e'),'-',date_format(`show`.`show_end_date`,'%e'),' ',date_format(`show`.`show_start_date`,'%b'),' ',date_format(`show`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`show`.`show_start_date`,'%e %b'),date_format(`show`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`show`.`show_start_date`,'%e %b %Y'),date_format(`show`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");
			
		$query->from('`#__toes_summary` AS `s`');
		$query->join('left', '`#__comprofiler` AS `cprof` ON `cprof`.`id` = `s`.`summary_user`');
		$query->join('left', '`#__users` AS `u` ON `u`.`id` = `cprof`.`id`');
		//$query->join("left", "`#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `cprof`.`cb_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `cprof`.`cb_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `cprof`.`cb_city`");
		$query->join('left', '`#__toes_entry` AS `e` ON `e`.`summary` = `s`.`summary_id`');
		$query->join('left', '`#__toes_entry_status` AS `es` ON `es`.`entry_status_id` = `e`.`status`');
		$query->join('left', '`#__toes_show_day` AS `sd` ON `sd`.`show_day_id` = `e`.`show_day`');
		$query->join('left', '`#__toes_show` AS `show` ON `show`.`show_id` = `e`.`entry_show`');
		$query->join("left", "`#__toes_venue` AS `sv` ON (`show`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");

		$query->group('`show`.`show_id` , `e`.`late_entry` , `s`.`summary_user`');
		$query->order('`cprof`.`lastname` ASC , `cprof`.`firstname` ASC');
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getCompetativeClassConditionsForNumbering($show_class){
		$condition = '';
		switch ($show_class) {
			case 'LH_Kitten':
				$condition = "((`show_class`='LH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'LH') AND
                                                            (`entry_age_years`=0) AND
                                                            (`entry_age_months`<8) AND
                                                            (`entry_age_months`>=4) AND
                                                            (`bs`.`breed_status` = 'Championship')
                                                           ))";
				break;
			case 'SH_Kitten':
				$condition = "((`show_class`='SH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'SH') AND
                                                            (`entry_age_years`=0) AND
                                                            (`entry_age_months`<8) AND
                                                            (`entry_age_months`>=4) AND
                                                            (`bs`.`breed_status` = 'Championship') 
                                                           ))";
				break;
			case 'LH_Cat':
				$condition = "((`show_class`='LH Cat') OR ((`show_class`='Ex Only') AND 
															(`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
																			LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
																			WHERE `r`.`ring_show_day` = `show_day`)
															) AND
															(`cat_hair_length_abbreviation` = 'LH') AND
															((`entry_age_years` >0) OR(`entry_age_months`>=8)) AND
															((`gdr`.`gender_short_name` = 'M') OR (`gdr`.`gender_short_name` = 'F')) AND
															(`bs`.`breed_status` = 'Championship')
														   ))";
				break;
			case 'SH_Cat':
				$condition = "((`show_class`='SH Cat') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'SH') AND
                                                            ((`entry_age_years` >0) OR(`entry_age_months`>=8)) AND
                                                            ((`gdr`.`gender_short_name` = 'M') OR (`gdr`.`gender_short_name` = 'F')) AND
                                                            (`bs`.`breed_status` = 'Championship')
                                                           ))";
				break;
			case 'LH_Alter':
				$condition = "((`show_class`='LH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'LH') AND
                                                            ((`entry_age_years` >0) OR(`entry_age_months`>=8)) AND
                                                            ((`gdr`.`gender_short_name` = 'N') OR (`gdr`.`gender_short_name` = 'S')) AND
                                                            (`bs`.`breed_status` = 'Championship')
                                                           ))";
				break;
			case 'SH_Alter':
				$condition = "((`show_class`='SH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'SH') AND
                                                            ((`entry_age_years` >0) OR(`entry_age_months`>=8)) AND
                                                            ((`gdr`.`gender_short_name` = 'N') OR (`gdr`.`gender_short_name` = 'S')) AND
                                                            (`bs`.`breed_status` = 'Championship')
                                                           ))";
				break;
			case 'LH_HHP_Kitten':
				$condition = "((`show_class`='LH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'LH') AND
                                                            (`entry_age_years`=0) AND
                                                            (`entry_age_months`<8) AND
                                                            (`entry_age_months`>=4) AND
                                                            (`bs`.`breed_status` = 'Non Championship')
                                                           ))";
				break;
			case 'LH_HHP':
				$condition = "((`show_class`='LH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'LH') AND
                                                            ((`entry_age_years` >0) OR(`entry_age_months`>=8)) AND
                                                            ((`gdr`.`gender_short_name` = 'N') OR (`gdr`.`gender_short_name` = 'S')) AND
                                                            (`bs`.`breed_status` = 'Non Championship')
                                                           ))";
				break;
			case 'SH_HHP_Kitten':
				$condition = "((`show_class`='SH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'SH') AND
                                                            (`entry_age_years`=0) AND
                                                            (`entry_age_months`<8) AND
                                                            (`entry_age_months`>=4) AND
                                                            (`bs`.`breed_status` = 'Non Championship')
                                                           ))";
				break;
			case 'SH_HHP':
				$condition = "((`show_class`='SH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`cat_hair_length_abbreviation` = 'SH') AND
                                                            ((`entry_age_years` >0) OR(`entry_age_months`>=8)) AND
                                                            ((`gdr`.`gender_short_name` = 'N') OR (`gdr`.`gender_short_name` = 'S')) AND
                                                            (`bs`.`breed_status` = 'Non Championship')
                                                           ))";
				break;
			case 'LH_PNB':
				$condition = "(`show_class`='LH PNB')";
				break;
			case 'SH_PNB':
				$condition = "(`show_class`='SH PNB')";
				break;
			case 'LH_ANB':
				$condition = "(`show_class`='LH ANB')";
				break;
			case 'SH_ANB':
				$condition = "(`show_class`='SH ANB')";
				break;
			case 'LH_NT':
				$condition = "(`show_class`='LH NT')";
				break;
			case 'SH_NT':
				$condition = "(`show_class`='SH NT')";
				break;
			case 'Exh_Only':
				$condition = "((`show_class`='Ex Only') AND ( NOT `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)))";
				break;
			case 'for_sale':
				$condition = "((`show_class`='Ex Only') AND ( `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)))";
				break;
		}
		return $condition;
	}
	
	public static function getCatlogRingInfoQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('`r`.`ring_id`, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`');
		$query->select('`r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation`');
		
		$query->from('`#__toes_ring` AS `r`');
		$query->join('left','`#__toes_judge` AS `j` ON `j`.`judge_id` = `r`.`ring_judge`');
		
		$query->where('(`r`.`ring_format` = 1 OR `r`.`ring_format` = 2)');
		$query->order('`r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`');
			
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
}
