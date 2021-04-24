DROP VIEW IF EXISTS `#__toes_view_exhibitor_report`;
DROP VIEW IF EXISTS `#__toes_view_count_per_ring`;
DROP VIEW IF EXISTS `#__toes_congress_summary`;
DROP VIEW IF EXISTS `#__toes_view_congress_summary`;
DROP VIEW IF EXISTS `#__toes_view_current_accepted_and_confirmed_entries_per_showday`;
DROP VIEW IF EXISTS `#__toes_view_judges_book_congress_data`;
DROP VIEW IF EXISTS `#__toes_view_judges_book_data`;
DROP VIEW IF EXISTS `#__toes_view_cat_competitive_class`; 
DROP VIEW IF EXISTS `#__toes_view_cat_competitive_class_input`;
DROP VIEW IF EXISTS `#__toes_view_exhibitor_list_basis` ;
DROP VIEW IF EXISTS `#__toes_view_for_sale_late` ;
DROP VIEW IF EXISTS `#__toes_view_Exh_Only_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_PNB_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_PNB_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_ANB_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_ANB_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_NT_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_NT_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_HHP_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_HHP_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_HHP_Kitten_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_HHP_Kitten_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_Alter_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_Alter_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_Cat_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_Cat_late` ;
DROP VIEW IF EXISTS `#__toes_view_SH_Kitten_late` ;
DROP VIEW IF EXISTS `#__toes_view_LH_Kitten_late` ;
DROP VIEW IF EXISTS `#__toes_view_for_sale` ;
DROP VIEW IF EXISTS `#__toes_view_Exh_Only` ;
DROP VIEW IF EXISTS `#__toes_view_SH_PNB` ;
DROP VIEW IF EXISTS `#__toes_view_LH_PNB` ;
DROP VIEW IF EXISTS `#__toes_view_SH_ANB` ;
DROP VIEW IF EXISTS `#__toes_view_LH_ANB` ;
DROP VIEW IF EXISTS `#__toes_view_SH_NT` ;
DROP VIEW IF EXISTS `#__toes_view_LH_NT` ;
DROP VIEW IF EXISTS `#__toes_view_SH_HHP` ;
DROP VIEW IF EXISTS `#__toes_view_LH_HHP` ;
DROP VIEW IF EXISTS `#__toes_view_SH_HHP_Kitten` ;
DROP VIEW IF EXISTS `#__toes_view_LH_HHP_Kitten` ;
DROP VIEW IF EXISTS `#__toes_view_SH_Alter` ;
DROP VIEW IF EXISTS `#__toes_view_LH_Alter` ;
DROP VIEW IF EXISTS `#__toes_view_SH_Cat` ;
DROP VIEW IF EXISTS `#__toes_view_LH_Cat` ;
DROP VIEW IF EXISTS `#__toes_view_SH_Kitten` ;
DROP VIEW IF EXISTS `#__toes_view_LH_Kitten` ;
DROP VIEW IF EXISTS `#__toes_view_catalog_numbering_basis` ;
DROP VIEW IF EXISTS `#__toes_view_cats_relations` ;
DROP VIEW IF EXISTS `#__toes_view_select_cat` ;
DROP VIEW IF EXISTS `#__toes_view_show_summaries` ;
DROP VIEW IF EXISTS `#__toes_view_show_scheduling_summaries` ;
DROP VIEW IF EXISTS `#__toes_view_catalog_ring_info`;
DROP VIEW IF EXISTS `#__toes_view_show` ;
DROP VIEW IF EXISTS `#__toes_view_full_entry` ;
DROP VIEW IF EXISTS `#__toes_view_entry` ;
DROP VIEW IF EXISTS `#__toes_view_judges` ;
DROP VIEW IF EXISTS `#__toes_view_user` ;
DROP VIEW IF EXISTS `#__toes_view_club_official` ;
DROP VIEW IF EXISTS `#__toes_view_club` ;
DROP VIEW IF EXISTS `#__toes_view_cat` ;
DROP VIEW IF EXISTS `#__toes_view_breed` ;
DROP VIEW IF EXISTS `#__toes_view_phone` ;
DROP VIEW IF EXISTS `#__toes_view_address` ;


CREATE VIEW `#__toes_view_address` AS
SELECT `addr`.`address_id`,`addr`.`address_line_1`,`addr`.`address_line_2`,`addr`.`address_line_3`,`addr`.`address_zip_code`,`addr`.`address_city`,`addr`.`address_state`,`addr`.`address_country`, `adt`.`address_type` 
FROM `#__toes_address` AS `addr` 
LEFT JOIN  `#__toes_address_type` AS `adt` ON (`addr`.`address_type` = `adt`.`address_type_id`) 
WHERE 1;


CREATE VIEW `#__toes_view_phone` AS
SELECT `ph`.`phone_id`,`ph`.`phone_international_access_code`,`ph`.`phone_area_code`,`ph`.`phone_number`,`pht`.`phone_type` FROM `#__toes_phone` AS `ph` 
LEFT JOIN  `#__toes_phone_type` AS `pht` ON (`ph`.`phone_type_id` = `pht`.`phone_type_id`) 
WHERE 1;


CREATE VIEW `#__toes_view_breed` AS
SELECT `b`.`breed_abbreviation`,`b`.`breed_name`,`b`.`breed_group`,`b`.`breed_hair_length`,`b`.`breed_color_restrictions`, `bs`.`breed_status` 
FROM `#__toes_breed` AS `b` 
LEFT JOIN  `#__toes_breed_status` AS `bs` 
ON `b`.`breed_status` = `bs`.`breed_status_id`
WHERE (`b`.`breed_organization` = 1) AND (`bs`.`breed_status_organization` = 1);


CREATE VIEW `#__toes_view_cat` AS
SELECT `c`.`cat_id`, `c`.`cat_date_of_birth`,`c`.`cat_name`,`c`.`cat_sire`,`c`.`cat_dam`,`c`.`cat_breeder`,`c`.`cat_owner`,`c`.`cat_new_trait`, `b`.`breed_name`,`b`.`breed_hair_length`,`bs`.`breed_status` 
FROM `#__toes_cat` AS `c` 
LEFT JOIN  `#__toes_breed` AS `b` ON (`c`.`cat_breed` = `b`.`breed_id`) 
LEFT JOIN  `#__toes_breed_status` AS `bs` ON (`b`.`breed_status` = `bs`.`breed_status_id`) 
LEFT JOIN  `#__toes_category` AS `ctg` ON (`c`.`cat_category` = `ctg`.`category_id`)
LEFT JOIN  `#__toes_division` AS `dvs` ON (`c`.`cat_division` = `dvs`.`division_id`)
LEFT JOIN  `#__toes_color` AS `clr` ON (`c`.`cat_color` = `clr`.`color_id`)
LEFT JOIN  `#__toes_cat_gender` AS `gdr` ON (`c`.`cat_gender` = `gdr`.`gender_id`) 
LEFT JOIN  `#__toes_cat_prefix` AS `pfx` ON (`c`.`cat_prefix` = `pfx`.`cat_prefix_id`) 
LEFT JOIN  `#__toes_cat_title` AS `ttl` ON (`c`.`cat_title` = `ttl`.`cat_title_id`) 
LEFT JOIN  `#__toes_cat_suffix` AS `sfx` ON (`c`.`cat_suffix` = `sfx`.`cat_suffix_id`)
LEFT JOIN  `#__toes_competitive_region` AS `reg` ON (`c`.`cat_competitive_region` = `reg`.`competitive_region_id`)
WHERE 1;


CREATE VIEW `#__toes_view_club` AS
SELECT `c`.`club_id`,`c`.`club_name`,`c`.`club_abbreviation`,`c`.`club_email`,`c`.`club_website`,`c`.`club_organization`,`o`.`organization_name`,`o`.`organization_abbreviation` 
FROM `#__toes_club` AS `c` 
LEFT JOIN  `#__toes_organization` AS `o` ON `c`.`club_organization` = `o`.`organization_id`
WHERE 1;


CREATE VIEW `#__toes_view_club_official` AS
SELECT `u`.`id`,`u`.`name`,`u`.`email`, `cot`.`club_official_type`, `vc`.`club_name`, `vc`.`club_abbreviation`, `vc`.`club_website`, `vc`.`club_email`, `vc`.`club_organization`, `vc`.`organization_name`, `vc`.`organization_abbreviation` 
FROM `#__users` AS `u` 
LEFT JOIN  `#__toes_club_official_type` AS `cot` ON (`cot`.`club_official_type_id` IN (SELECT `co`.`club_official_type` FROM  `#__toes_club_official` AS `co` WHERE `u`.`id` = `co`.`user` )  ) 
LEFT JOIN  `#__toes_view_club` AS `vc` ON (`vc`.`club_id` IN (SELECT `co2`.`club` FROM  `#__toes_club_official` AS `co2` WHERE `u`.`id` = `co2`.`user` )  ) 
WHERE 1;


CREATE VIEW `#__toes_view_user` AS
SELECT `u`.`id`,`u`.`name`,`u`.`email`,`cprof`.`cb_address1` AS `address_line_1`,`cprof`.`cb_address2` AS `address_line_2`,`cprof`.`cb_address3` AS `address_line_3`,`cprof`.`cb_zip` AS `address_zip_code`,`cprof`.`cb_city` AS `address_city`,`cprof`.`cb_state` AS `address_state`,`cprof`.`cb_country` AS `address_country`, `addr`.`address_type`,`ph`.`phone_international_access_code`,`ph`.`phone_area_code`,`ph`.`phone_number`,`ph`.`phone_type` , `cprof`.`cb_phonenumber` AS `phonenumber`, `cprof`.`cb_privacy` AS `private`
FROM `#__users` AS `u` 
LEFT JOIN  `#__toes_view_address` AS `addr` ON (`addr`.`address_id` IN (SELECT `uha`.`address` FROM  `#__toes_user_has_address` AS `uha` WHERE `u`.`id` = `uha`.`user` )  ) 
LEFT JOIN  `#__toes_view_phone` AS `ph` ON (`ph`.`phone_id` IN (SELECT `uhp`.`phone` FROM  `#__toes_user_has_phone` AS `uhp` WHERE `u`.`id` = `uhp`.`user` )  ) 
LEFT JOIN  `#__comprofiler` AS `cprof` ON (`u`.`id` = `cprof`.`user_id`)
WHERE 1;


CREATE VIEW `#__toes_view_judges` AS
SELECT `ju`.`judge_id`, `vu`.`id` AS `user_id`,`vu`.`name`,`vu`.`email`,`vu`.`address_line_1`,`vu`.`address_line_2`,`vu`.`address_line_3`,`vu`.`address_zip_code`,`vu`.`address_city`,`vu`.`address_state`,`vu`.`address_country`, `vu`.`address_type`,`vu`.`phone_international_access_code`,`vu`.`phone_area_code`,`vu`.`phone_number`,`vu`.`phone_type`, `jl`.`judge_level`, `jl`.`judge_fee`, `js`.`judge_status`
FROM `#__toes_judge` AS `ju`
LEFT JOIN `#__toes_view_user` AS `vu` ON `vu`.`id` = `ju`.`user`
LEFT JOIN  `#__toes_judge_level` AS `jl` ON `jl`.`judge_level_id` = `ju`.`judge_level` 
LEFT JOIN  `#__toes_judge_status` AS `js` ON `js`.`judge_status_id` = `ju`.`judge_status`
LEFT JOIN  `#__toes_organization` AS `o` ON `o`.`organization_id` = `ju`.`judge_organization`
WHERE 1;


CREATE VIEW `#__toes_view_entry` AS
SELECT  `e`.`entry_id`, `e`.`summary`,
`es`.`summary_user`,
`u`.`email`, `cprof`.`firstname`, `cprof`.`lastname`, `cprof`.`cb_phonenumber` AS `phonenumber`,
`es`.`summary_benching_request`,`es`.`summary_grooming_space`, `es`.`summary_single_cages`, `es`.`summary_double_cages`, `es`.`summary_personal_cages`, `es`.`summary_remarks`, `es`.`summary_total_fees`, `es`.`summary_fees_paid`,    
`e`.`status`, `estat`.`entry_status`, `e`.`entry_participates_AM`, `e`.`entry_participates_PM`,
`e`.`cat`, `e`.`show_day`, `e`.`copy_cat_name`, `e`.`copy_cat_prefix`,  `pfx`.`cat_prefix_abbreviation`,  `pfx`.`cat_prefix`,  
`e`.`copy_cat_title`,  `ttl`.`cat_title_abbreviation`,  `ttl`.`cat_title`,  
`e`.`copy_cat_suffix`,  `sfx`.`cat_suffix_abbreviation`,  `sfx`.`cat_suffix`,  GREATEST(
DATE_FORMAT(`sd`.`show_day_date`, '%Y') - DATE_FORMAT(`e`.`copy_cat_date_of_birth`, '%Y') - (DATE_FORMAT(`sd`.`show_day_date`, '00-%m-%d') < DATE_FORMAT(`e`.`copy_cat_date_of_birth`, '00-%m-%d')), 0) AS age_years,
IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
)
AS `age_months`
,
IF(
GREATEST(
DATE_FORMAT(`show_day_date`, '%Y') - DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') - (DATE_FORMAT(`show_day_date`, '00-%m-%d') < DATE_FORMAT(`copy_cat_date_of_birth`, '00-%m-%d')), 0) >0,
FALSE,
(IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
) < 8) AND
(IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
) >= 4)
) AS `is_kitten`
,
IF(
GREATEST(
DATE_FORMAT(`show_day_date`, '%Y') - DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') - (DATE_FORMAT(`show_day_date`, '00-%m-%d') < DATE_FORMAT(`copy_cat_date_of_birth`, '00-%m-%d')), 0) >0,
TRUE,

IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`copy_cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`copy_cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`copy_cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
) >= 8
) AS `is_adult`
,
`e`.`copy_cat_breed`, `b`.`breed_abbreviation`,  `b`.`breed_name`, `b`.`breed_hair_length`,  `bs`.`breed_status`,  
IF (`b`.`breed_name` LIKE 'Household%', TRUE, FALSE) AS is_HHP,
`e`.`copy_cat_hair_length`, `hl`.`cat_hair_length_abbreviation`,
`e`.`copy_cat_category`, `ctg`.`category`,  
`e`.`copy_cat_division`, `dvs`.`division_name`,
`e`.`copy_cat_color`, `clr`.`color_name`,
`e`.`copy_cat_date_of_birth`, `e`.`copy_cat_registration_number`,
`e`.`copy_cat_gender`, `gdr`.`gender_short_name`, `gdr`.`gender_name`,
IF( (`gdr`.`gender_name` = 'Female Spay') OR (`gdr`.`gender_name` = 'Male Neuter'), TRUE, FALSE) AS is_alter,
`e`.`copy_cat_id_chip_number`,
`e`.`copy_cat_new_trait`, `e`.`copy_cat_sire_name`, `e`.`copy_cat_dam_name`, `e`.`copy_cat_breeder_name`, `e`.`copy_cat_owner_name`, `e`.`copy_cat_lessee_name`, `e`.`copy_cat_agent_name`,
`e`.`copy_cat_competitive_region`,  `rgn`.`competitive_region_abbreviation`,
`rgn`.`competitive_region_name`,
`e`.`exhibition_only`,
`e`.`for_sale`,
`e`.`copy_cat_sire`,
`e`.`copy_cat_dam`,
`e`.`copy_cat_breeder`,
`e`.`copy_cat_owner`,
`sd`.`show_day_id`, `sd`.`show_day_show`, `sd`.`show_day_date`,
`s`.`show_id`, `s`.`show_start_date`, `s`.`show_end_date`,
`s`.`show_venue`, `sv`.`venue_name`,
`va`.`address_line_1`, `va`.`address_line_2`, `va`.`address_line_3`,
`va`.`address_city`, `va`.`address_state`, `va`.`address_zip_code`, `va`.`address_country`,
`s`.`show_flyer`, `s`.`show_motto`,
`s`.`show_format` as `show_format_id`, `sf`.`show_format`,
`s`.`show_published`,
`s`.`show_status` as `show_status_id`, `ss`.`show_status`,
`e`.`late_entry` , `e`.`catalog_number`, `e`.`entry_date_created`,
(
(`e`.`copy_cat_name` = `cat`.`cat_name`) AND
(`e`.`copy_cat_prefix` = `cat`.`cat_prefix`) AND
(`e`.`copy_cat_title` = `cat`.`cat_title`) AND
(`e`.`copy_cat_suffix` = `cat`.`cat_suffix`) AND
(`e`.`copy_cat_sire_name`= `cat`.`cat_sire`) AND
(`e`.`copy_cat_dam_name` = `cat_dam`) AND
(`e`.`copy_cat_breeder_name` = `cat`.`cat_breeder`) AND
(`e`.`copy_cat_owner_name`= `cat`.`cat_owner`) AND
(`e`.`copy_cat_lessee_name` = `cat`.`cat_lessee`) ) AS `minor_differences`,
(
(`e`.`copy_cat_breed` = `cat`.`cat_breed`) AND
(`e`.`copy_cat_category`= `cat`.`cat_category`) AND
(`e`.`copy_cat_division`=`cat`.`cat_division`) AND
(`e`.`copy_cat_color` = `cat`.`cat_color`) AND
(`e`.`copy_cat_hair_length` = `cat`.`cat_hair_length`) AND
(`e`.`copy_cat_date_of_birth`=`cat`.`cat_date_of_birth`) AND
(`e`.`copy_cat_gender` = `cat`.`cat_gender`) AND
(`e`.`copy_cat_registration_number`) AND
(`e`.`copy_cat_new_trait` = `cat`.`cat_new_trait`) AND
(`e`.`copy_cat_competitive_region` = `cat`.`cat_competitive_region`) ) AS `major_differences`



FROM `#__toes_entry` AS `e`
LEFT JOIN `#__toes_summary` AS `es` ON (`e`.`summary` = `es`.`summary_id`)
LEFT JOIN `#__users`  AS `u` ON (`es`.`summary_user` = `u`.`id`)
LEFT JOIN `#__comprofiler`  AS `cprof` ON (`es`.`summary_user` = `cprof`.`user_id`)
LEFT JOIN `#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)
LEFT JOIN `#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)
LEFT JOIN `#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)
LEFT JOIN `#__toes_breed` AS `b` ON (`e`.`copy_cat_breed` = `b`.`breed_id`)
LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND `show_day_date` BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
LEFT JOIN `#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
LEFT JOIN `#__toes_cat_hair_length` AS `hl` ON (`e`.`copy_cat_hair_length` = `hl`.`cat_hair_length_id`)
LEFT JOIN `#__toes_category` AS `ctg` ON (`e`.`copy_cat_category` = `ctg`.`category_id`)
LEFT JOIN `#__toes_division` AS `dvs` ON (`e`.`copy_cat_division` = `dvs`.`division_id`)
LEFT JOIN `#__toes_color` AS `clr` ON (`e`.`copy_cat_color` = `clr`.`color_id`)
LEFT JOIN `#__toes_cat_gender` AS `gdr` ON (`e`.`copy_cat_gender` = `gdr`.`gender_id`)
LEFT JOIN `#__toes_cat_prefix` AS `pfx` ON (`e`.`copy_cat_prefix` = `pfx`.`cat_prefix_id`)
LEFT JOIN `#__toes_cat_title` AS `ttl` ON (`e`.`copy_cat_title` = `ttl`.`cat_title_id`)
LEFT JOIN `#__toes_cat_suffix` AS `sfx` ON (`e`.`copy_cat_suffix` = `sfx`.`cat_suffix_id`)
LEFT JOIN `#__toes_competitive_region` AS `rgn` ON (`e`.`copy_cat_competitive_region` = `rgn`.`competitive_region_id`)
LEFT JOIN `#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)
LEFT JOIN `#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)
LEFT JOIN `#__toes_show_format` AS `sf` ON (`s`.`show_format` = `sf`.`show_format_id`)
LEFT JOIN `#__toes_show_status` AS `ss` ON (`s`.`show_status` = `ss`.`show_status_id`)
LEFT JOIN `#__toes_cat` AS `cat` ON (`e`.`cat` = `cat`.`cat_id`)
WHERE 1;


CREATE VIEW `#__toes_view_full_entry` AS
SELECT 
    IF ( `exhibition_only` = TRUE, 'Ex Only',
        IF ( `breed_status` = 'Non Championship',  #this means HHP
            IF ( `is_HHP` = TRUE,    # this is the only non championship class
                IF ( `is_kitten` = TRUE,  # HHP Kitten
                    IF ( `cat_hair_length_abbreviation` = 'LH', 'LH HHP Kitten', 'SH HHP Kitten'),
                    IF ( `is_adult` = TRUE,   #HHP
                        IF ( `cat_hair_length_abbreviation` = 'LH', 'LH HHP', 'SH HHP'),
                        IF (`age_months`>=3,'Ex Only','Not allowed in Show Hall - Minimum age is 3 months')
                    )
                ),
                'ERROR - 2 - Please check view <Full Entry>'
            ),
            IF ( `breed_status` = 'Championship',
                IF ( `copy_cat_new_trait`,
                    IF(`is_kitten` OR `is_adult`,IF ( `cat_hair_length_abbreviation` = 'LH', 'LH NT', 'SH NT'),IF (`age_months`>=3,'Ex Only','Not allowed in Show Hall - Minimum age is 3 months')),
                    IF ( `is_kitten` = TRUE, 
                        IF ( `cat_hair_length_abbreviation` = 'LH', 'LH Kitten', 'SH Kitten'),
                        IF ( `is_adult` = TRUE, 
                            IF ( `cat_hair_length_abbreviation` = 'LH', 
                                IF( (`gender_short_name`='M') OR (`gender_short_name`='F'),  'LH Cat', 'LH Alter') ,
                                IF( (`gender_short_name`='M') OR (`gender_short_name`='F'),  'SH Cat', 'SH Alter')
                               ),
                            IF (`age_months`>=3,'Ex Only','Not allowed in Show Hall - Minimum age is 3 months')
                        )
                    )
                ),
                IF( `breed_status` = 'Advanced New Breed',
                    IF(`is_kitten` OR `is_adult`,IF( `cat_hair_length_abbreviation` = 'LH', 'LH ANB', 'SH ANB'),IF (`age_months`>=3,'Ex Only','Not allowed in Show Hall - Minimum age is 3 months')),
                    IF( `breed_status` = 'Preliminary New Breed',
                        IF(`is_kitten` OR `is_adult`,IF( `cat_hair_length_abbreviation` = 'LH', 'LH PNB', 'SH PNB'),IF (`age_months`>=3,'Ex Only','Not allowed in Show Hall - Minimum age is 3 months')),
                        'Error - 4 - Please check view <Full Entry>'
                    )
                )
            )
        )
    ) AS `Show_Class`, 
`entry_id`, `summary`, `summary_user`, `email`, `firstname`, `lastname`, `phonenumber`, `summary_benching_request`, `summary_grooming_space`, `summary_single_cages`, `summary_double_cages`, `summary_personal_cages`, `summary_remarks`, `summary_total_fees`, `summary_fees_paid`, `status`, `entry_status`, `cat`, `show_day`, `copy_cat_name`, `copy_cat_prefix`, `cat_prefix_abbreviation`, `cat_prefix`, `copy_cat_title`, `cat_title_abbreviation`, `cat_title`, `copy_cat_suffix`, `cat_suffix_abbreviation`, `cat_suffix`, `age_years`, `age_months`, `is_kitten`, `is_adult`, `copy_cat_breed`, `breed_abbreviation`, `breed_name`, `breed_hair_length`, `breed_status`, `is_HHP`, `copy_cat_hair_length`, `cat_hair_length_abbreviation`, `copy_cat_category`, `category`, `copy_cat_division`, `division_name`, `copy_cat_color`, `color_name`, `copy_cat_date_of_birth`, `copy_cat_registration_number`, `copy_cat_gender`, `gender_short_name`, `gender_name`, `is_alter`, `copy_cat_id_chip_number`,`copy_cat_new_trait`, `copy_cat_sire_name`, `copy_cat_dam_name`, `copy_cat_breeder_name`, `copy_cat_owner_name`, `copy_cat_lessee_name`, `copy_cat_agent_name`, `copy_cat_competitive_region`, `competitive_region_abbreviation`, `competitive_region_name`, `exhibition_only`, `for_sale`, `copy_cat_sire`, `copy_cat_dam`, `copy_cat_breeder`, `copy_cat_owner`, `show_day_id`, `show_day_show`, `show_day_date`, `show_id`, `show_start_date`, `show_end_date`, `show_venue`, `venue_name`, `address_line_1`, `address_line_2`, `address_line_3`, `address_city`, `address_state`, `address_zip_code`, `address_country`, `show_flyer`, `show_motto`, `show_format_id`, `show_format`, `show_published`, `show_status_id`, `show_status`, `late_entry` , `catalog_number`, `entry_date_created`, `entry_participates_AM`, `entry_participates_PM`
FROM `#__toes_view_entry` WHERE 1
ORDER BY `Show_Class` ASC, `breed_name` ASC, `copy_cat_category` ASC, `copy_cat_division` ASC, `copy_cat_color` ASC, `cat` ASC;

CREATE VIEW `#__toes_view_show` AS
SELECT `s`.`show_id` ,`s`.`show_start_date` , `s`.`show_end_date`, `s`.`show_uses_toes`, `s`.`show_flyer`, `s`.`show_comments`, 
`s`.`show_bring_your_own_cages`,`s`.`catalog_runs` , `s`.`show_extra_text_for_confirmation`, `s`.`show_currency_used`, `s`.`show_paper_size`,  
`s`.`show_cost_per_entry`, `s`.`show_total_cost`, `s`.`show_use_club_entry_clerk_address`, `s`.`show_email_address_entry_clerk`, `s`.`show_use_club_show_manager_address`, `s`.`show_email_address_show_manager`, 
`ve`.`venue_id`, `ve`.`venue_name`, `va`.`address_line_1`, `va`.`address_line_2`, `va`.`address_line_3`, `va`.`address_zip_code`, `va`.`address_city`, `va`.`address_state`, `va`.`address_country`, 
`s`.`show_motto` , `sf`.`show_format`, `ss`.`show_status`, `or`.`organization_abbreviation`, 
`s`.`show_published`, `c`.`club_id`, `c`.`club_abbreviation`, `c`.`club_name`, CONCAT(monthname(`s`.`show_start_date`) ,' ', cast(year(`s`.`show_start_date`) AS CHAR)) AS `show_month`,
CONCAT_WS( ' ', `va`.`address_city` , `va`.`address_state` , `va`.`address_country` ) AS Show_location,
IF ( DATE_FORMAT(`s`.`show_start_date`,'%Y')=DATE_FORMAT(`s`.`show_end_date`,'%Y'),
     IF ( DATE_FORMAT(`s`.`show_start_date`,'%b')=DATE_FORMAT(`s`.`show_end_date`,'%b'),
          CONCAT( DATE_FORMAT(`s`.`show_start_date`,'%e'),'-',DATE_FORMAT(`s`.`show_end_date`,'%e'),' ',DATE_FORMAT(`s`.`show_start_date`,'%b'),' ',DATE_FORMAT(`s`.`show_start_date`,'%Y') ),
          CONCAT_WS (' - ',DATE_FORMAT(`s`.`show_start_date`,'%e %b'), DATE_FORMAT(`s`.`show_end_date`,'%e %b %Y') )
     )
    ,
    CONCAT_WS ( ' – ', 
                          DATE_FORMAT(`s`.`show_start_date`, '%e %b %Y' ),
                          DATE_FORMAT(`s`.`show_end_date` , '%e %b %Y' )
              )
    ) AS `show_dates`,
`cr`.`competitive_region_abbreviation`
FROM `#__toes_show` AS `s` 
LEFT JOIN `#__toes_venue` AS `ve` ON `ve`.`venue_id`=`s`.`show_venue` 
LEFT JOIN `#__toes_address` AS `va` ON `va`.`address_id` = `ve`.`venue_address` 
LEFT JOIN `#__toes_show_format` AS `sf` ON `sf`.`show_format_id`=`s`.`show_format` 
LEFT JOIN `#__toes_show_status` AS `ss` ON `ss`.`show_status_id`=`s`.`show_status` 
LEFT JOIN `#__toes_organization` AS `or` ON `or`.`organization_id`=`s`.`show_organization` 
LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `s`.`show_id` 
LEFT JOIN `#__toes_club` AS `c` ON `c`.`club_id` = `cos`.`club`
LEFT JOIN `#__toes_competitive_region` AS `cr` ON (`cr`.`competitive_region_id` = `c`.`club_competitive_region`);


CREATE VIEW `#__toes_view_catalog_ring_info` AS
SELECT `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation` 
FROM `#__toes_ring` AS `r`
LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`) 
WHERE (`r`.`ring_format` = 1) OR (`r`.`ring_format` = 2)
ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`;


#CREATE VIEW `#__toes_view_show_scheduling_summaries` AS
#SELECT `sc`.`show_class` , `fe`.`breed_name`, COUNT( `fe`.`entry_id` ) AS `cat_count` , `fe`.`show_day_date` , `fe`.`show_day` AS `show_day_id`, `s`.`Show_location`, `c`.`club_name` , `c`.`club_abbreviation` ,
#`s`.`show_dates`,
#    `s`.`show_id` 
#FROM `#__toes_show_class` AS `sc` 
#LEFT JOIN `#__toes_view_full_entry` AS `fe` ON `sc`.`show_class` = `fe`.`Show_Class` 
#LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `fe`.`show_id` 
#LEFT JOIN `#__toes_club` AS `c` ON `c`.`club_id` = `cos`.`club` 
#LEFT JOIN `#__toes_view_show` AS `s` ON `s`.`show_id` = `fe`.`show_id`
#WHERE (`fe`.`entry_status` = 'New') OR (`fe`.`entry_status` = 'Accepted') OR (`fe`.`entry_status` = 'Confirmed') OR (`fe`.`entry_status` = 'Confirmed & Paid')
#GROUP BY `fe`.`Show_Class` ,  `fe`.`breed_name`, `fe`.`show_day_date` 
#ORDER BY `sc`.`show_class_id` ASC , `fe`.`breed_name` ASC, `fe`.`show_day_date` ASC;

CREATE VIEW `#__toes_view_show_scheduling_summaries` AS
SELECT `fe`.`Show_Class` as `show_class`,`fe`.`breed_name`, `fe`.`show_id`, `fe`.`show_day_id`, `fe`.`show_day_date`, COUNT(`fe`.`entry_id`) AS `cat_count`,`Show_location` , `club_name` , `club_abbreviation` , `show_dates`
FROM `#__toes_view_full_entry` as `fe`
LEFT JOIN `#__toes_view_show` AS `s` ON `s`.`show_id` = `fe`.`show_id`
WHERE (`fe`.`entry_status` = 'New') OR (`fe`.`entry_status` = 'Accepted') OR (`fe`.`entry_status` = 'Confirmed') OR (`fe`.`entry_status` = 'Confirmed & Paid')
GROUP BY `fe`.`Show_Class`,`fe`.`breed_name`, `fe`.`show_day`
ORDER BY `fe`.`Show_Class` ASC,`fe`.`breed_name`, `fe`.`show_day` ASC;

#CREATE VIEW `#__toes_view_show_summaries` AS
#SELECT `show_class` , SUM(`cat_count`) AS `cat_count` , `show_day_date` , `show_day_id` , `Show_location` , `club_name` , `club_abbreviation` , `show_dates` , `show_id`
#FROM `#__toes_view_show_scheduling_summaries`
#WHERE 1
#GROUP BY `show_id` , `show_class`, `show_day_date`
#ORDER BY `show_id` , `show_class`, `show_day_date` ;

CREATE VIEW `#__toes_view_show_summaries` AS
SELECT `fe`.`Show_Class` as `show_class`, `fe`.`show_id`, `fe`.`show_day_id`, `fe`.`show_day_date`, COUNT(`fe`.`entry_id`) AS `cat_count`,`Show_location` , `club_name` , `club_abbreviation` , `show_dates`
FROM `#__toes_view_full_entry` as `fe`
LEFT JOIN `#__toes_view_show` AS `s` ON `s`.`show_id` = `fe`.`show_id`
WHERE (`fe`.`entry_status` = 'New') OR (`fe`.`entry_status` = 'Accepted') OR (`fe`.`entry_status` = 'Confirmed') OR (`fe`.`entry_status` = 'Confirmed & Paid')
GROUP BY `fe`.`Show_Class`, `fe`.`show_day`
ORDER BY `fe`.`Show_Class` ASC, `fe`.`show_day` ASC;




CREATE VIEW `#__toes_view_select_cat` AS
SELECT CONCAT_WS(' - ',`c`.`cat_name`,`rn`.`cat_registration_number`) 
FROM `#__toes_cat` AS `c`
LEFT JOIN `#__toes_cat_registration_number` AS `rn`ON (`c`.`cat_id` = `rn`.`cat_registration_number_cat`) 
WHERE (`rn`.`cat_registration_number_organization` = 1);


CREATE VIEW `#__toes_view_cats_relations` AS
SELECT `c`.`cat_date_of_birth`,`c`.`cat_name`,`c`.`cat_sire`,`c`.`cat_dam`,`c`.`cat_breeder`,`c`.`cat_owner`,`c` .`cat_new_trait`, `b`.`breed_name`,`b`.`breed_hair_length`,`bs`.`breed_status`, `cuct`.`cat_user_connection_type`, `usr`.`name`, `usr`.`username` FROM `#__toes_cat` AS `c`
LEFT JOIN `#__toes_breed` AS `b` ON (`c`.`cat_breed` = `b`.`breed_id`)
LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND NOW() BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
LEFT JOIN `#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
LEFT JOIN `#__toes_category` AS `ctg` ON (`c`.`cat_category` = `ctg`.`category_id`)
LEFT JOIN `#__toes_division` AS `dvs` ON (`c`.`cat_division` = `dvs`.`division_id`)
LEFT JOIN `#__toes_color` AS `clr` ON (`c`.`cat_color` = `clr`.`color_id`)
LEFT JOIN `#__toes_cat_gender` AS `gdr` ON (`c`.`cat_gender` = `gdr`.`gender_id`)
LEFT JOIN `#__toes_cat_prefix` AS `pfx` ON (`c`.`cat_prefix` = `pfx`.`cat_prefix_id`)
LEFT JOIN `#__toes_cat_title` AS `ttl` ON (`c`.`cat_title` = `ttl`.`cat_title_id`)
LEFT JOIN `#__toes_cat_suffix` AS `sfx` ON (`c`.`cat_suffix` = `sfx`.`cat_suffix_id`)
LEFT JOIN `#__toes_competitive_region` AS `reg` ON (`c`.`cat_competitive_region` = `reg`.`competitive_region_id`)
LEFT JOIN `#__toes_cat_relates_to_user` AS `crtu` ON (`c`.`cat_id` = `crtu`.`of_cat`)
LEFT JOIN `#__toes_cat_user_connection_type` AS `cuct` ON (`crtu`.`cat_user_connection_type` = `cuct`.`cat_user_connection_type_id`)
LEFT JOIN `#__users` AS `usr` ON (`crtu`.`person_is` = `usr`.`id`) WHERE 1;


CREATE VIEW `#__toes_view_catalog_numbering_basis` AS
SELECT DISTINCT `e`.`show_id` , `e`.`show_day`, `e`.`entry_id`, `e`.`cat` , `sc`.`show_class` ,  `e`.`breed_name` , 
CONCAT(`category`,' ',`division_name`, ' Division') AS `catalog_division`,`e`.`color_name` , `e`.`catalog_number`, 
CONCAT_WS(' ', 
TRIM(
CONCAT(IF(`e`.`cat_prefix_abbreviation`=NULL,'',CONCAT(`e`.`cat_prefix_abbreviation`,' ')), IF(`e`.`cat_title_abbreviation`,'',CONCAT(`e`.`cat_title_abbreviation`,' ')),IF(`e`.`cat_suffix_abbreviation`,'',`e`.`cat_suffix_abbreviation`))
)
 ,`e`.`copy_cat_name`) AS `catalog_cat_name`, 
CONCAT(`age_years`,'.',`age_months`,' ',`gender_short_name`) AS `catalog_age_and_gender`, IF(`copy_cat_registration_number`=NULL,'',`copy_cat_registration_number`) AS `catalog_registration_number`, `e`.`copy_cat_id_chip_number` AS `catalog_id_chip_number`,
UPPER(DATE_FORMAT(`copy_cat_date_of_birth`,'%b %d, %Y')) AS `catalog_birthdate`,
TRIM(
CONCAT(IF(`e`.`cat_prefix_abbreviation`=NULL,'',CONCAT(`e`.`cat_prefix_abbreviation`,' ')), IF(`e`.`cat_title_abbreviation`,'',CONCAT(`e`.`cat_title_abbreviation`,' ')),IF(`e`.`cat_suffix_abbreviation`,'',`e`.`cat_suffix_abbreviation`))
) AS `catalog_awards`, 
`e`.`copy_cat_sire_name` AS `catalog_sire`, `e`.`copy_cat_dam_name` AS `catalog_dam`, 
IF(`e`.`copy_cat_breeder_name`=`e`.`copy_cat_owner_name`,CONCAT('B/O: ',`e`.`copy_cat_breeder_name`),CONCAT('B: ', `e`.`copy_cat_breeder_name`)) AS `catalog_breeder`,
IF(`e`.`copy_cat_breeder_name`=`e`.`copy_cat_owner_name`,NULL,CONCAT('O: ',`e`.`copy_cat_owner_name`)) AS `catalog_owner`, 
IF(`e`.`copy_cat_lessee_name`=NULL,NULL,IF(`e`.`copy_cat_lessee_name`='',NULL,CONCAT('L: ',`e`.`copy_cat_lessee_name`))) AS `catalog_lessee`, 
IF(`e`.`copy_cat_agent_name`=NULL,NULL,IF(`e`.`copy_cat_agent_name`='',NULL,CONCAT('A: ',`e`.`copy_cat_agent_name`))) AS `catalog_agent`,
`e`.`competitive_region_abbreviation` AS `catalog_region`,
`e`.`cat_hair_length_abbreviation` AS `hair_length_abbreviation`,
`e`.`summary_user` , `e`.`firstname` , `e`.`lastname` , `c`.`cb_address1` , `c`.`cb_address2` , `c`.`cb_address3` , `c`.`cb_city` , `c`.`cb_zip` , `c`.`cb_state` , `c`.`cb_country` , `e`.`status` , `e`.`entry_status` , `e`.`copy_cat_category` , `e`.`copy_cat_division` , 
`e`.`copy_cat_color` ,   `e`.`late_entry` , `e`.`address_city`, `e`.`address_state`,`e`.`address_country`, `club`.`club_name`, `club`.`club_abbreviation`, `e`.`for_sale`, `e`.`age_years`, `e`.`age_months`, `e`.`gender_short_name` AS `cat_gender_abbreviation`, `e`.`breed_status`, `e`.`entry_participates_AM`, `e`.`entry_participates_PM`
FROM `#__toes_view_full_entry` AS `e`
LEFT JOIN `#__toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`
LEFT JOIN `#__comprofiler` AS `c` ON `c`.`user_id` = `e`.`summary_user`
LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`
LEFT JOIN `#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`
WHERE (`sc`.`show_class_id` >0) AND (`sc`.`show_class_id` <= 17) AND
( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') )
ORDER BY `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC , `e`.`copy_cat_color` ASC, `e`.`cat` ASC;


CREATE VIEW `#__toes_view_LH_Kitten` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='LH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_SH_Kitten` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='SH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Championship') 
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_Cat` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='LH Cat') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'M') OR (`cat_gender_abbreviation` = 'F')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_SH_Cat` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='SH Cat') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'M') OR (`cat_gender_abbreviation` = 'F')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_Alter` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='LH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_Alter` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='SH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_HHP_Kitten` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='LH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_HHP_Kitten` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='SH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_HHP` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='LH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_HHP` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  ( NOT `late_entry`) AND ((`show_class`='SH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_NT` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='LH NT') AND ( NOT `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_NT` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='SH NT') AND ( NOT `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_ANB` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='LH ANB') AND ( NOT `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_ANB` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='SH ANB') AND ( NOT `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_PNB` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='LH PNB') AND ( NOT `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_PNB` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='SH PNB') AND ( NOT `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_Exh_Only` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='Ex Only') AND ( NOT `late_entry`) AND ( NOT `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_for_sale` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='Ex Only') AND ( NOT `late_entry`) AND ( `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;



CREATE VIEW `#__toes_view_LH_Kitten_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='LH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_SH_Kitten_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='SH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Championship') 
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_Cat_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='LH Cat') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'M') OR (`cat_gender_abbreviation` = 'F')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_SH_Cat_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='SH Cat') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'M') OR (`cat_gender_abbreviation` = 'F')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_Alter_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='LH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_Alter_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='SH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_HHP_Kitten_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='LH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_HHP_Kitten_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='SH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_HHP_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='LH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_HHP_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE  (`late_entry`) AND ((`show_class`='SH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )
                                ) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;



CREATE VIEW `#__toes_view_LH_NT_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='LH NT') AND (  `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_NT_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='SH NT') AND (  `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_ANB_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='LH ANB') AND (  `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_ANB_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='SH ANB') AND (  `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_LH_PNB_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='LH PNB') AND (  `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_SH_PNB_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='SH PNB') AND (  `late_entry`) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_Exh_Only_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='Ex Only') AND (  `late_entry`) AND ( NOT `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`))
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;

CREATE VIEW `#__toes_view_for_sale_late` AS
SELECT `show_id`, `show_day`, `entry_id`, `cat`, `show_class`, `breed_name`, `catalog_division`, `color_name`, `catalog_number`, `catalog_cat_name`, `catalog_age_and_gender`, `catalog_registration_number`, `catalog_birthdate`, `catalog_awards`, `catalog_sire`, `catalog_dam`, `catalog_breeder`, `catalog_owner`, `catalog_lessee`, `catalog_agent`, `catalog_region`, `copy_cat_category`,`copy_cat_division`,`copy_cat_color`,`entry_status`,`late_entry`, `summary_user`,`firstname`,`lastname` 
FROM `#__toes_view_catalog_numbering_basis` 
WHERE (`show_class`='Ex Only') AND (  `late_entry`) AND ( `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)) 
GROUP BY
   `show_id`, `cat` 
ORDER BY 
   `breed_name` ASC, 
   `copy_cat_category` ASC,
   `copy_cat_division` ASC,
   `copy_cat_color` ASC,
   `cat` ASC;


CREATE VIEW `#__toes_view_exhibitor_list_basis` AS
SELECT `summary_user` AS `User` ,
CONCAT_WS( ', ', `lastname` , `firstname` ) AS `Exhibitor` ,
CONCAT_WS( ' ', `cb_address1` , `cb_address2` , `cb_address3` ) AS `Address` ,
CONCAT_WS( ' ', `cb_city` , `cb_zip` , `cb_state` ) AS `City` ,
`cb_country` AS `Country` ,
GROUP_CONCAT( DISTINCT(`catalog_number`) ORDER BY CAST( `catalog_number` AS UNSIGNED ) ) AS `Entries` ,
`show_id` , `late_entry` , CONCAT_WS(' ', `address_city` , `address_state` , `address_country`) AS Show_location ,
`club_name` , `club_abbreviation`,
IF ( DATE_FORMAT(MIN(`sd`.`show_day_date`),'%Y')=DATE_FORMAT(MAX(`sd`.`show_day_date`),'%Y'),
IF ( DATE_FORMAT(MIN(`sd`.`show_day_date`),'%b')=DATE_FORMAT(MAX(`sd`.`show_day_date`),'%b'),
CONCAT( DATE_FORMAT(MIN(`sd`.`show_day_date`),'%e'),'- ',DATE_FORMAT(MIN(`sd`.`show_day_date`),'%e'),' ',DATE_FORMAT(MIN(`sd`.`show_day_date`),'%b'),' ',DATE_FORMAT(MIN(`sd`.`show_day_date`),'%Y') ),
CONCAT_WS (' - ',DATE_FORMAT(MIN(`sd`.`show_day_date`),'%e %b'), DATE_FORMAT(MAX(`sd`.`show_day_date`),'%e %b %Y') )
) ,
CONCAT_WS ( ' - ', DATE_FORMAT( MIN( `sd`.`show_day_date` ), '%e %b %Y' ), DATE_FORMAT(MAX( `sd`.`show_day_date` ) , '%e %b %Y' )
) ) AS `show_dates`
FROM `#__toes_view_catalog_numbering_basis` 
LEFT JOIN `#__toes_show_day` AS `sd` ON `sd`.`show_day_show` = `show_id` 
GROUP BY `show_id` , `late_entry` , `summary_user` ORDER BY `lastname` ASC , `firstname` ASC;


CREATE VIEW `#__toes_view_cat_competitive_class_input` AS
SELECT  
`cat`.`cat_id`, `sd`.`show_day_id`, `cat`.`cat_date_of_birth`,
GREATEST(
DATE_FORMAT(`sd`.`show_day_date`, '%Y') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%Y') - (DATE_FORMAT(`sd`.`show_day_date`, '00-%m-%d') < DATE_FORMAT(`cat`.`cat_date_of_birth`, '00-%m-%d')), 0) AS age_years,
IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`cat`.`cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat`.`cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat`.`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat`.`cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`cat`.`cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat`.`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
)
AS `age_months`
,
IF(
GREATEST(
DATE_FORMAT(`show_day_date`, '%Y') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%Y') - (DATE_FORMAT(`show_day_date`, '00-%m-%d') < DATE_FORMAT(`cat`.`cat_date_of_birth`, '00-%m-%d')), 0) >0,
FALSE,
(IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
) < 8) AND
(IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
) >= 4)
) AS `is_kitten`
,
IF(
GREATEST(
DATE_FORMAT(`show_day_date`, '%Y') - DATE_FORMAT(`cat_date_of_birth`, '%Y') - (DATE_FORMAT(`show_day_date`, '00-%m-%d') < DATE_FORMAT(`cat_date_of_birth`, '00-%m-%d')), 0) >0,
TRUE,

IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
      IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
          DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
      ),
      IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
          ),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')+12,12),
              MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
          )
      )
  ),
  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
      IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
          IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
              DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
          ),
          0
      ),
      0
  )
) >= 8
) AS `is_adult`
,
`cat`.`cat_breed`, `b`.`breed_abbreviation`,  `b`.`breed_name`, `b`.`breed_hair_length`,  `bs`.`breed_status`,  
IF (`b`.`breed_name` LIKE 'Household%', TRUE, FALSE) AS is_HHP,
`cat`.`cat_hair_length`, `hl`.`cat_hair_length_abbreviation`,
`cat`.`cat_category`, `ctg`.`category`,  
`cat`.`cat_division`, `dvs`.`division_name`,
`cat`.`cat_color`, `clr`.`color_name`,
`cat`.`cat_gender`, `gdr`.`gender_short_name`, `gdr`.`gender_name`,
IF( (`gdr`.`gender_name` = 'Female Spay') OR (`gdr`.`gender_name` = 'Male Neuter'), TRUE, FALSE) AS is_alter,
`cat`.`cat_new_trait`,
`cat`.`cat_competitive_region`,  `rgn`.`competitive_region_abbreviation`,
`rgn`.`competitive_region_name`,
`cat`.`cat_sire`,
`cat`.`cat_dam`,
`cat`.`cat_breeder`,
`cat`.`cat_owner`,
`cat`.`cat_lessee`,
`sd`.`show_day_show`, `sd`.`show_day_date`



FROM `#__toes_cat` AS `cat`
LEFT JOIN `#__toes_show_day` AS `sd` ON (`cat`.`cat_date_of_birth` < `sd`.`show_day_date`)
LEFT JOIN `#__toes_breed` AS `b` ON (`cat`.`cat_breed` = `b`.`breed_id`)
LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND `show_day_date` BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
LEFT JOIN `#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
LEFT JOIN `#__toes_cat_hair_length` AS `hl` ON (`cat`.`cat_hair_length` = `hl`.`cat_hair_length_id`)
LEFT JOIN `#__toes_category` AS `ctg` ON (`cat`.`cat_category` = `ctg`.`category_id`)
LEFT JOIN `#__toes_division` AS `dvs` ON (`cat`.`cat_division` = `dvs`.`division_id`)
LEFT JOIN `#__toes_color` AS `clr` ON (`cat`.`cat_color` = `clr`.`color_id`)
LEFT JOIN `#__toes_cat_gender` AS `gdr` ON (`cat`.`cat_gender` = `gdr`.`gender_id`)
LEFT JOIN `#__toes_cat_prefix` AS `pfx` ON (`cat`.`cat_prefix` = `pfx`.`cat_prefix_id`)
LEFT JOIN `#__toes_cat_title` AS `ttl` ON (`cat`.`cat_title` = `ttl`.`cat_title_id`)
LEFT JOIN `#__toes_cat_suffix` AS `sfx` ON (`cat`.`cat_suffix` = `sfx`.`cat_suffix_id`)
LEFT JOIN `#__toes_competitive_region` AS `rgn` ON (`cat`.`cat_competitive_region` = `rgn`.`competitive_region_id`)
WHERE (`sd`.`show_day_id` > 0)  AND (`cat_id` > 0) ; 


CREATE VIEW  `#__toes_view_cat_competitive_class` AS
SELECT 
    IF ( TRUE,  #(`exhibition_only` = TRUE) OR (`exhibition_only` = FALSE), 
        IF ( `breed_status` = 'Non Championship',  #this means HHP
            IF ( `is_HHP` = TRUE,    # this is the only non championship class
                IF ( `is_kitten` = TRUE,  # HHP Kitten
                    IF ( `cat_hair_length_abbreviation` = 'LH', 'LH HHP Kitten', 'SH HHP Kitten'),
                    IF ( `is_adult` = TRUE,   #HHP
                        IF ( `cat_hair_length_abbreviation` = 'LH', 'LH HHP', 'SH HHP'),
                        IF (`age_months`>=3,'ERROR – 1 - Please check view <cat_competitive_class>','Not allowed in Show Hall - Minimum age is 3 months')
                    )
                ),
                'ERROR - 2 - Please check view <cat_competitive_class>'
            ),
            IF ( `breed_status` = 'Championship',
                IF ( `cat_new_trait`,
                    IF ( `cat_hair_length_abbreviation` = 'LH', 'LH NT', 'SH NT'),
                    IF ( `is_kitten` = TRUE, 
                        IF ( `cat_hair_length_abbreviation` = 'LH', 'LH Kitten', 'SH Kitten'),
                        IF ( `is_adult` = TRUE, 
                            IF ( `cat_hair_length_abbreviation` = 'LH', 
                                IF( (`gender_short_name`='M') OR (`gender_short_name`='F'),  'LH Cat', 'LH Alter') ,
                                IF( (`gender_short_name`='M') OR (`gender_short_name`='F'),  'SH Cat', 'SH Alter')
                               ),
                            IF (`age_months`>=3,'Error - 3 - Please check view <cat_competitive_class>','Not allowed in Show Hall - Minimum age is 3 months')
                        )
                    )
                ),
                IF( `breed_status` = 'Advanced New Breed',
                    IF( `cat_hair_length_abbreviation` = 'LH', 'LH ANB', 'SH ANB'),
                    IF( `breed_status` = 'Preliminary New Breed',
                        IF( `cat_hair_length_abbreviation` = 'LH', 'LH PNB', 'SH PNB'),
                        'Error - 4 - Please check view <cat_competitive_class>'
                    )
                )
            )
        ),
    'Error 5 - Please have the administrator check view cat_competitive_class'
    ) AS `Show_Class`, 
`cat_id`, `show_day_id`
FROM `#__toes_view_cat_competitive_class_input` WHERE 1;



CREATE VIEW `#__toes_view_judges_book_data` AS
SELECT DISTINCT `e`.`show_id` ,`e`.`show_day`, `sc`.`show_class`, 
CONCAT(`e`.`breed_abbreviation`,' - ',`e`.`color_name`) AS `judges_book_color`, 
CONCAT(`age_years`,'.',`age_months`,' ',`gender_short_name`) AS `judges_book_age_and_gender`, `e`.`catalog_number`,
`e`.`breed_abbreviation` , CONCAT(`category`,' ',`division_name`, ' Division') AS `catalog_division`  , `e`.`breed_name`
FROM `#__toes_view_full_entry` AS `e`
LEFT JOIN `#__toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`
LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`
LEFT JOIN `#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`
WHERE (`sc`.`show_class_id` >0) AND (`sc`.`show_class_id` < 17) AND
( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') )
ORDER BY `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC , `e`.`copy_cat_color` ASC, `e`.`cat` ASC;


CREATE VIEW `#__toes_view_judges_book_congress_data` AS
SELECT DISTINCT `e`.`show_id` ,`cong`.`ring_show_day` AS `show_day` , `sc`.`show_class`,
CONCAT(`e`.`breed_abbreviation`,' - ',`e`.`color_name`) AS `judges_book_color`, 
CONCAT(`age_years`,'.',`age_months`,' ',`gender_short_name`) AS `judges_book_age_and_gender`, `e`.`catalog_number`, `e`.`breed_name`,
`e`.`breed_abbreviation` , CONCAT(`category`,' ',`division_name`, ' Division') AS `catalog_division` , `cong`.`ring_name`, `cong`.`ring_id`
FROM `#__toes_view_full_entry` AS `e`
LEFT JOIN `#__toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`
LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`
LEFT JOIN `#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`
LEFT JOIN `#__toes_entry_participates_in_congress` AS `epic` ON `epic`.`entry_id` = `e`.`entry_id`
LEFT JOIN `#__toes_ring` AS `cong` ON `epic`.`congress_id` = `cong`.`ring_id`
WHERE (`sc`.`show_class_id` >0) AND (`sc`.`show_class_id` <= 17) AND (`cong`.`ring_format` = 3) AND
( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') )
ORDER BY `cong`.`ring_id`, `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC , `e`.`copy_cat_color` ASC, `e`.`cat` ASC;


CREATE VIEW `#__toes_view_current_accepted_and_confirmed_entries_per_showday` AS
SELECT COUNT(`entry_id`) AS `nr_of_entries`, `show_day` FROM `#__toes_entry` 
LEFT JOIN `#__toes_entry_status` AS `es` ON (`es`.`entry_status_id` = `status`)
WHERE (`status`= 2) OR ( `status`= 5) OR ( `status`= 7)
GROUP BY `show_day`
ORDER BY `show_day`;


#TEMP WORK TO BE USED FOR CREATION OF THE ENTRY LIST (XLS/CSV/...)
DROP VIEW IF EXISTS `#__toes_view_summary_and_entries_per_day_per_exhibitor`;
DROP VIEW IF EXISTS `#__toes_view_summary_per_show_per_user_all_days_consolidated`;
DROP VIEW IF EXISTS `#__toes_view_temp3`;

CREATE VIEW `#__toes_view_summary_and_entries_per_day_per_exhibitor` AS
SELECT 
`s`.`summary_show`, CONCAT(`c`.`firstname`, ' ',`c`.`lastname`) AS `exhibitor`, `s`.`summary_user`, `e`.`show_day`, `sd`.`show_day_date`,  COUNT(`e`.`cat`) AS `entries_per_day`, `c`.`lastname`, `c`.`firstname`, `s`.`summary_benching_request`, `s`.`summary_grooming_space`, `s`.`summary_single_cages`, `s`.`summary_double_cages`, `s`.`summary_personal_cages`, `s`.`summary_remarks`, `s`.`summary_total_fees`, `s`.`summary_fees_paid`, `s`.`summary_status`, `e`.`cat`, `e`.`for_sale`, `e`.`exhibition_only`, `e`.`status` ,
`show`.`show_start_date`, `show`.`show_end_date`, `show`.`venue_name`, `show`.`address_city`, `show`.`address_state`, `show`.`address_country`, `show`.`show_motto`, `show`.`show_format`, `show`.`club_abbreviation`, `show`.`club_name`, `show`.`show_month`, `show`.`Show_location`, `show`.`show_dates`
FROM `#__toes_summary` AS `s`
LEFT JOIN `#__toes_entry` AS `e` ON (`e`.`summary` = `s`.`summary_id`)
LEFT JOIN `#__comprofiler` AS `c` ON (`s`.`summary_user` = `c`.`user_id`)
LEFT JOIN `#__toes_view_show` AS `show` ON (`show`.`show_id` = `s`.`summary_show`)
LEFT JOIN `#__toes_show_day` AS `sd` ON (`sd`.`show_day_id` = `e`.`show_day`)
WHERE 1
GROUP BY `s`.`summary_show`, `c`.`lastname`, `c`.`firstname`, `e`.`show_day`
ORDER BY `s`.`summary_show`, `c`.`lastname`, `c`.`firstname`, `e`.`show_day`;


CREATE VIEW `#__toes_view_summary_per_show_per_user_all_days_consolidated` AS
SELECT `summary_show`, `exhibitor`, `lastname`, `firstname`, 
(SELECT `entries_per_day` FROM `#__toes_view_summary_and_entries_per_day_per_exhibitor` AS `t1` WHERE (`t`.`summary_show`=`t1`.`summary_show`) AND ( `t`.`exhibitor`=`t1`.`exhibitor`) AND (MIN(`t`.`show_day`)=`t1`.`show_day`)) AS `entries_day1`,
(SELECT `entries_per_day` FROM `#__toes_view_summary_and_entries_per_day_per_exhibitor` AS `t1` WHERE (`t`.`summary_show`=`t1`.`summary_show`) AND ( `t`.`exhibitor`=`t1`.`exhibitor`) AND (MIN(`t`.`show_day`)+1=`t1`.`show_day`)) AS `entries_day2`,
(SELECT `entries_per_day` FROM `#__toes_view_summary_and_entries_per_day_per_exhibitor` AS `t1` WHERE (`t`.`summary_show`=`t1`.`summary_show`) AND ( `t`.`exhibitor`=`t1`.`exhibitor`) AND (MIN(`t`.`show_day`)+2=`t1`.`show_day`)) AS `entries_day3`,
`summary_single_cages`, `summary_double_cages`, `summary_personal_cages`,`summary_grooming_space`,`summary_total_fees`, `summary_fees_paid`
FROM `#__toes_view_summary_and_entries_per_day_per_exhibitor` AS `t` WHERE 1
GROUP BY  `t`.`summary_show`, `t`.`exhibitor`
ORDER BY `summary_show`,`lastname`, `firstname`;


CREATE VIEW `#__toes_view_temp3` AS
SELECT `t`.`exhibitor`, `t`.`summary_show` AS `show`, `t`.`show_day` AS `Day`,COUNT(`t`.`cat`) AS `#cats`,
`t`.`summary_single_cages` AS `single cages`, `t`.`summary_double_cages` AS `double cages`, 
IF(`t`.`summary_personal_cages`=1,'Yes','No') AS `Personal cages`,`t`.`summary_grooming_space` AS `Grooming Space`,
`t`.`summary_total_fees` AS `Total`, `t`.`summary_fees_paid` AS `Paid`, `t`.`summary_benching_request` AS `benching`,  
`t`.`summary_remarks` AS `remarks`,  `ss`.`summary_status` FROM `#__toes_view_summary_and_entries_per_day_per_exhibitor` AS `t`
LEFT JOIN `#__toes_summary_status` AS `ss` ON `ss`.`summary_status_id`=`t`.`summary_status`
WHERE 1 
GROUP BY `t`.`exhibitor`, `t`.`summary_show`,`t`.`show_day`
ORDER BY `t`.`show_day`,`t`.`summary_show`,  `t`.`exhibitor`;


#idea about manually deciding on page breaks when printing judges books on pre-printed forms
DROP VIEW IF EXISTS `#__toes_view_cats_per_color`;
DROP VIEW IF EXISTS `#__toes_view_cats_per_division`;
DROP VIEW IF EXISTS `#__toes_view_cats_per_breed`;

CREATE VIEW `#__toes_view_cats_per_color` AS
SELECT `e`.`show_id` , `e`.`show_day`, `sc`.`show_class` ,  `e`.`breed_name` , 
CONCAT(`category`,' ',`division_name`, ' Division') AS `catalog_division`,`e`.`color_name` , COUNT(`e`.`entry_id`) AS `cats_in_this_color`
FROM `#__toes_view_full_entry` AS `e`
LEFT JOIN `#__toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`
LEFT JOIN `#__comprofiler` AS `c` ON `c`.`user_id` = `e`.`summary_user`
LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`
LEFT JOIN `#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`
WHERE (`sc`.`show_class_id` >0) AND (`sc`.`show_class_id` <= 17) AND
( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') ) AND
( `e`.`show_id` = 5)
GROUP BY `e`.`show_day`, `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC , `e`.`copy_cat_color` ASC
ORDER BY `e`.`show_day`, `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC , `e`.`copy_cat_color` ASC, `e`.`cat` ASC;

CREATE VIEW `#__toes_view_cats_per_division` AS
SELECT `e`.`show_id` , `e`.`show_day`, `sc`.`show_class` ,  `e`.`breed_name` , 
CONCAT(`category`,' ',`division_name`, ' Division') AS `catalog_division`, COUNT(`e`.`entry_id`) AS `cats_in_this_division`
FROM `#__toes_view_full_entry` AS `e`
LEFT JOIN `#__toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`
LEFT JOIN `#__comprofiler` AS `c` ON `c`.`user_id` = `e`.`summary_user`
LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`
LEFT JOIN `#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`
WHERE (`sc`.`show_class_id` >0) AND (`sc`.`show_class_id` <= 17) AND
( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') ) AND
( `e`.`show_id` = 5)
GROUP BY `e`.`show_day`, `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC  
ORDER BY `e`.`show_day`, `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC , `e`.`cat` ASC;

CREATE VIEW `#__toes_view_cats_per_breed` AS
SELECT `e`.`show_id` , `e`.`show_day`, `sc`.`show_class` ,  `e`.`breed_name` , COUNT(`e`.`entry_id`) AS `cats_in_this_breed` 
FROM `#__toes_view_full_entry` AS `e`
LEFT JOIN `#__toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`
LEFT JOIN `#__comprofiler` AS `c` ON `c`.`user_id` = `e`.`summary_user`
LEFT JOIN `#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`
LEFT JOIN `#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`
WHERE (`sc`.`show_class_id` >0) AND (`sc`.`show_class_id` <= 17) AND
( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') ) AND
( `e`.`show_id` = 5)
GROUP BY `e`.`show_day`, `sc`.`show_class_id` ASC , `e`.`breed_name` ASC    
ORDER BY `e`.`show_day`, `sc`.`show_class_id` ASC , `e`.`breed_name` ASC, `e`.`cat` ASC;

CREATE VIEW `#__toes_congress_summary` AS
SELECT `ring_id` , `ring_show_day` , `ring_show` , `ring_number` , `ring_name` , COUNT( `entry_id` ) AS `Count`
FROM `#__toes_ring`
LEFT JOIN `#__toes_entry_participates_in_congress` ON ( `ring_id` = `congress_id` )
WHERE (
`ring_format` =3
)
GROUP BY `ring_id`;


CREATE VIEW `#__toes_view_count_per_ring` AS
SELECT `r`.`ring_id`, `r`.`ring_show` , `r`.`ring_show_day` , `r`.`ring_timing` ,`r`.`ring_judge` , `j`.`judge_abbreviation` , COUNT( `cats`.`entry_id` ) AS `count`
FROM `#__toes_ring` AS `r`
LEFT JOIN `#__toes_judge` AS `j` ON ( `j`.`judge_id` = `r`.`ring_judge` )
LEFT JOIN `#__toes_view_catalog_numbering_basis` AS `cats` ON ( `cats`.`show_day` = `r`.`ring_show_day` )
WHERE 
(
 (
  (`r`.`ring_format` =1) OR (`r`.`ring_format` =2)
 ) 
  OR 
 (
  (`r`.`ring_format` = 3) AND (`cats`.`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` WHERE `congress_id`= `r`.`ring_id` ))
 )
)
AND
(`cats`.`show_class` <> 'Ex Only')
GROUP BY `r`.`ring_show` , `r`.`ring_show_day`, `r`.`ring_timing` , `r`.`ring_id`, `r`.`ring_judge`
ORDER BY  `r`.`ring_show` , `r`.`ring_judge`, `r`.`ring_show_day`, `r`.`ring_timing`  ;



CREATE VIEW `#__toes_view_exhibitor_report` AS
SELECT `e`.`Exhibitor`, `e`.`Address`, `e`.`City`, `e`.`Country`, `e`.`Show_location`, `e`.`club_name` , `u`.`email`, `e`.`show_id`
FROM `#__toes_view_exhibitor_list_basis` as `e`
LEFT JOIN #__users AS `u` ON (`e`.`User` = `u`.`id`)
WHERE `e`.`show_id`>0;