<?php
jimport('phpword.Autoloader');

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

Autoloader::register();
Settings::loadConfig();

$writers = array('Word2007' => 'docx');


		function write($phpWord, $writers,$show_id)
		{
			
			$result = '';

			// Write documents
			foreach ($writers as $writer => $extension) {
			  
			//	$result .= date('H:i:s') . " Write to {$writer} format";
				if (!is_null($extension)) {
					$xmlWriter = IOFactory::createWriter($phpWord, $writer);
				   
					$xmlWriter->save(JPATH_BASE. "/media/com_toes/DOCX/".$show_id."/catalog.docx");
				  
				} else {
					$result .= ' ... NOT DONE!';
				}
			   
				//$result .= EOL;
			}

		  

			return 1;
		}


		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->addFontStyle('rStyle', array('bold' => true, 'italic' => true, 'size' => 16, 'allCaps' => true, 'doubleStrikethrough' => true));
		//$phpWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceAfter' => 100));
		$phpWord->addTitleStyle(1, array('bold' => true), array('spaceAfter' => 240));

		$db = JFactory::getDbo();
		$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
		$db->setQuery($query);
		$page_format = $db->loadResult();
		$page_format = $page_format?$page_format:'A4';

		$sectionSettings = array(
		//'orientation' => 'landscape',
		//'pageSizeW'=>308.4,
		//'pageSizeH'=>487.04,
		'paper'=>$page_format,
		'marginLeft'=>100,
		'marginRight'=>100
		);

		$section = $phpWord->createSection($sectionSettings);
//$section = $phpWord->addSection();
		$header = $section->createHeader();
      
        $fontStyleTitle = array('size' => 16,'bold'=>true);
        $fontStyleSubTitle = array('size' => 10,'bold'=>false);
        $paragraphStyleTitle = array('spaceBefore' => 0);
        $styleTable = array('borderSize'=>0,'borderColor' => 'FFFFFF');
		$lineStyle = array('weight' => 1, 'width' => 700, 'height' => 0, 'color' => 'b2a68b');
        $table = $header->addTable($styleTable);
       
        $table->addRow();
		$logo = JURI::root().'media/com_toes/images/paw32X32.png';
        $table->addCell(500)->addImage($logo, array('align' => 'left'));
      
       
       //$table->addCell(4000)->addText(JText::_('COM_TOES_EXHIBITORLIST_HEADER').' '.JText::_('COM_TOES_SITE_TITLE'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
      
       $cell=$table->addCell(4000);
       $cell->addText(JText::_('COM_TOES_EXHIBITOR_LIST'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $cell->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $header->addText();
		$lineStyle = array('weight' => 1, 'width' => 710, 'height' => 0, 'color' => 'b2a68b');
       $header->addLine($lineStyle);

$skip_division_best = array(
	'LH PNB',
	'SH PNB',
	'LH ANB',
	'SH ANB',
	'LH HHP Kitten',
	'SH HHP Kitten',
	'Ex Only',
	'For Sale'
);

$skip_breed_best = array(
	'LH HHP',
	'SH HHP',
	'LH HHP Kitten',
	'SH HHP Kitten',
	'Ex Only',
	'For Sale'
);

$file = JRequest::getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Process started";
$data = array(
	'total' => '?',
	'processed' => '?',
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

$db = JFactory::getDBO();

$time = time();

$query = " CREATE TABLE `j1temp_toes_entry_{$show_id}_{$time}` 
        SELECT `e`.`entry_id`, `e`.`summary`,
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



        FROM `j25_toes_entry` AS `e`
        LEFT JOIN `j25_toes_summary` AS `es` ON (`e`.`summary` = `es`.`summary_id`)
        LEFT JOIN `j25_users`  AS `u` ON (`es`.`summary_user` = `u`.`id`)
        LEFT JOIN `j25_comprofiler`  AS `cprof` ON (`es`.`summary_user` = `cprof`.`user_id`)
        LEFT JOIN `j25_toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)
        LEFT JOIN `j25_toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)
        LEFT JOIN `j25_toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)
        LEFT JOIN `j25_toes_breed` AS `b` ON (`e`.`copy_cat_breed` = `b`.`breed_id`)
        LEFT JOIN `j25_toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND `show_day_date` BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
        LEFT JOIN `j25_toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
        LEFT JOIN `j25_toes_cat_hair_length` AS `hl` ON (`e`.`copy_cat_hair_length` = `hl`.`cat_hair_length_id`)
        LEFT JOIN `j25_toes_category` AS `ctg` ON (`e`.`copy_cat_category` = `ctg`.`category_id`)
        LEFT JOIN `j25_toes_division` AS `dvs` ON (`e`.`copy_cat_division` = `dvs`.`division_id`)
        LEFT JOIN `j25_toes_color` AS `clr` ON (`e`.`copy_cat_color` = `clr`.`color_id`)
        LEFT JOIN `j25_toes_cat_gender` AS `gdr` ON (`e`.`copy_cat_gender` = `gdr`.`gender_id`)
        LEFT JOIN `j25_toes_cat_prefix` AS `pfx` ON (`e`.`copy_cat_prefix` = `pfx`.`cat_prefix_id`)
        LEFT JOIN `j25_toes_cat_title` AS `ttl` ON (`e`.`copy_cat_title` = `ttl`.`cat_title_id`)
        LEFT JOIN `j25_toes_cat_suffix` AS `sfx` ON (`e`.`copy_cat_suffix` = `sfx`.`cat_suffix_id`)
        LEFT JOIN `j25_toes_competitive_region` AS `rgn` ON (`e`.`copy_cat_competitive_region` = `rgn`.`competitive_region_id`)
        LEFT JOIN `j25_toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)
        LEFT JOIN `j25_toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)
        LEFT JOIN `j25_toes_show_format` AS `sf` ON (`s`.`show_format` = `sf`.`show_format_id`)
        LEFT JOIN `j25_toes_show_status` AS `ss` ON (`s`.`show_status` = `ss`.`show_status_id`)
        LEFT JOIN `j25_toes_cat` AS `cat` ON (`e`.`cat` = `cat`.`cat_id`)
        WHERE `s`.`show_id` = {$show_id}";

$db->setQuery($query);
$db->query();

$query = " CREATE TABLE `j1temp_toes_full_entry_{$show_id}_{$time}` 
        SELECT 
            IF ( `exhibition_only` = TRUE, IF ((`age_years`=0) AND (`age_months`< 3),'Not allowed in Show Hall - Minimum age is 3 months','Ex Only'),
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
        FROM `j1temp_toes_entry_{$show_id}_{$time}` WHERE 1
        ORDER BY `Show_Class` ASC, `breed_name` ASC, `copy_cat_category` ASC, `copy_cat_division` ASC, `copy_cat_color` ASC, `cat` ASC;
";

$db->setQuery($query);
$db->query();

$query = "SELECT DISTINCT `e`.`show_id` , `e`.`show_day`, `e`.`entry_id`, `e`.`cat` , `sc`.`show_class` ,  `e`.`breed_name` , 
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
        FROM `j1temp_toes_full_entry_{$show_id}_{$time}` AS `e`
        LEFT JOIN `j25_toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`
        LEFT JOIN `j25_comprofiler` AS `c` ON `c`.`user_id` = `e`.`summary_user`
        LEFT JOIN `j25_toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`
        LEFT JOIN `j25_toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`
        WHERE `e`.`late_entry` = 0  AND (`sc`.`show_class_id` >0) AND (`sc`.`show_class_id` <= 17) AND
        ( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') )
        ORDER BY `sc`.`show_class_id` ASC , `e`.`breed_name` ASC , `e`.`copy_cat_category` ASC , `e`.`copy_cat_division` ASC , `e`.`copy_cat_color` ASC, `e`.`cat` ASC;
        ";

//$query = "SELECT * FROM `#__toes_view_catalog_numbering_basis` WHERE `late_entry` = 0 AND `show_id` = {$show_id}";
$db->setQuery($query);
$entries = $db->loadObjectList();

$query = "SELECT `show_day_id`, `show_day_date` FROM `#__toes_show_day` WHERE `show_day_show` = {$show_id} ORDER BY `show_day_date`";
$db->setQuery($query);
$show_days = $db->loadObjectList();

//$query = "SELECT r.*, rf.ring_format AS format FROM `#__toes_view_catalog_ring_info` AS r LEFT JOIN `#__toes_ring_format` AS rf ON rf.`ring_format_id` = r.`ring_format` WHERE r.`ring_show` = {$show_id}";
$query = "SELECT `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation`, `rf`.`ring_format` AS format 
FROM `j25_toes_ring` AS `r`
LEFT JOIN `j25_toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`) 
LEFT JOIN `j25_toes_ring_format` AS `rf` ON `rf`.`ring_format_id` = `r`.`ring_format` 
WHERE `r`.`ring_show` = {$show_id} AND ((`r`.`ring_format` = 1) OR (`r`.`ring_format` = 2))
ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`;
";
$db->setQuery($query);
$rings = $db->loadObjectList();

foreach ($rings as $ring) {
	$show_day_rings[$ring->ring_show_day][] = $ring;
}

$show = TOESHelper::getShowDetails($show_id);

/*if ($show->page_ortientation == 'Automatic') {
	if (count($show_days) == 3)
		$page_orientation = 'L';
	else
		$page_orientation = PDF_PAGE_ORIENTATION;
}
else if ($show->page_ortientation == 'Landscape')
	$page_orientation = 'L';
else
	$page_orientation = PDF_PAGE_ORIENTATION;*/

$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format ? $page_format : 'A4';

// create new PDF document
//$pdf = new MYPDF($page_orientation, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
/*$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_CATALOG'));
*/
$params = JComponentHelper::getParams('com_toes');

// set default header data
$header_logo = '../../../media/com_toes/images/paw32X32.jpg';

/*if($params->get('use_logo_for_pdf')) {
	$pdf->SetHeaderData($header_logo, 10, JText::_('COM_TOES_CATALOG'), JText::_('COM_TOES_WEBSITE'));
} else { 
	$pdf->SetHeaderData('', 10, JText::_('COM_TOES_CATALOG'), JText::_('COM_TOES_WEBSITE'));
}*/

$font_size = ($show->show_catalog_font_size) ? (int) $show->show_catalog_font_size : 10;
//$pdf->font_size = $font_size;

$is_continuous = ($show->show_format == 'Continuous') ? true : false;
$is_alernative = ($show->show_format == 'Alternative') ? true : false;

$session_rings = array();
if ($is_alernative) {
	foreach ($show_days as $showday) {
		$session_rings[$showday->show_day_id]['AM'] = TOESHelper::getShowdayRings($showday->show_day_id, 1);
		$session_rings[$showday->show_day_id]['PM'] = TOESHelper::getShowdayRings($showday->show_day_id, 2);
	}
}

//$pdf->footer_text = $show->club_name . ' - ' . $show->Show_location . ' - ' . $show->show_dates;

$breed_title_color = $division_title_color = $bod_color = $bob_color = '#000000';

if ($show->show_colored_catalog) {
	if ($params->get('breed_title_color'))
		$breed_title_color = $params->get('breed_title_color');
	if ($params->get('division_title_color'))
		$division_title_color = $params->get('division_title_color');
	if ($params->get('bod_color'))
		$bod_color = $params->get('bod_color');
	if ($params->get('bob_color'))
		$bob_color = $params->get('bob_color');
}

// set header and footer fonts
/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 5, 10);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
//$pdf->setLanguageArray($l);

$pdf->setPrintHeader(FALSE);

// ---------------------------------------------------------
// add a page
$pdf->AddPage();*/

$previous_class = '';
$previous_breed = '';
$previous_division = '';
$previous_color = '';
$previous_catalog_number = '';

$previous_breed_entries = 1;
$previous_division_entries = 1;

$show_day_entries = array();
$catalog_numbers = array();

$temp_entries = array();
$showday_entries = array();
foreach ($entries as $entry) {
	$showday_entries[$entry->show_day][$entry->catalog_number] = $entry;
	$show_day_entries[$entry->show_day][$entry->catalog_number] = $entry->catalog_number;
	if (!in_array($entry->catalog_number, $catalog_numbers)) {
		$catalog_numbers[] = $entry->catalog_number;
		$temp_entries[] = $entry;
	}
}
$entries = $temp_entries;

$cur = 0;
$total = count($catalog_numbers);
$processed = 0;
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

$available_classes = array();
	$section=$phpWord->addSection();
if (count($show_days) == 2) {
	foreach ($entries as $entry) {
		if ($previous_class != $entry->show_class) {
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;
			
			//if ($previous_class != '')
				//$pdf->AddPage();

			$header_logo = JURI::root() . 'media/com_toes/images/paw32X32.jpg';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;">
									<tr><td style="width:4%">';
			if($params->get('use_logo_for_pdf')) {
				$show_class_block .='<img src="' . $header_logo . '" />';
			} else {
				$show_class_block .=' ';
			}
			//error_reporting(E_ALL);
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-size:40px; font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span style="font-size:35px">' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:70px">' . strtoupper($entry->show_class) . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			/*$show_class_block='<table width="100%"><tr><td ><span style="font-size:40px; font-weight:bold;color:#000">vaishali</span></td>
										  <td style="font-size:40px; font-weight:bold;">dubal</td>
										  </tr></table>';*/
			/*$pdf->SetFont('ptsanscaption', '', $font_size + 2);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');*/
		$section->addText();	
		$section->addText();
		$section->addText();
		//\PhpOffice\PhpWord\Shared\Html::addHtml($section,$show_class_block,true);
			\PhpOffice\PhpWord\Shared\Html::addHtml($section, '<div style="position:ralative;page-break-inside: avoid;page-break-before:always; clear: both;page-break-after:always">
			<div>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span>
			<span style="font-size:40px; font-weight:bold;color:#000">vaishali</span></div>
			<div style="font-size:40px; font-weight:bold;">dubal</div>
										  </div>');
			
			
			$judge_block = '<table width="100%">
                                <tr>
                            ';
			if ($show->show_format != 'Alternative') {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr>
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[0]->show_day_date)))) . '</td>
                                        </tr>
                                        <tr>
                                            ';
				foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
					$judge_block .= '<td align="left">' . $first_show_day->judge_abbreviation . '</td>
                                    ';
				}
				$judge_block .= '       </tr>
                                    </table>
                                </td>
                                ';
			} else {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr>
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                        </tr>
                                        <tr>
                                            ';
				foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
					$judge_block .= '<td colspan="2" align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $first_show_day->judge_abbreviation . '</td>
                                    ';
				}
				$judge_block .= '       </tr>
                                    </table>
                                </td>
                                ';
			}

			$judge_block .= '<td width="40%" align="center">&nbsp;</td>
                                    ';

			if ($show->show_format != 'Alternative') {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr>
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[1]->show_day_date)))) . '</td>
                                        </tr>
                                        <tr>
                                            ';

				foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
					$judge_block .= '<td align="right">' . $second_show_day->judge_abbreviation . '</td>
                                            ';
				}
				$judge_block .= '</tr>
                                    </table>
                                </td>
                                ';
			} else {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr>
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                        </tr>
                                        <tr>
                                            ';

				foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
					$judge_block .= '<td colspan="2" align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $second_show_day->judge_abbreviation . '</td>
                                            ';
				}
				$judge_block .= '</tr>
                                    </table>
                                </td>
                                ';
			}
			$judge_block .= '</tr>
                        </table>';

			if ($entry->show_class != 'Ex Only') {
				//\PhpOffice\PhpWord\Shared\Html::addHtml($section,$judge_block,true);
				/*$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln();*/
			}

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
			
			$log = "Processing ".$entry->show_class.".... ";
			
		}

		if ($previous_catalog_number != $entry->catalog_number) {
			$entry_block = '<table width="100%">
                                <tr>
                            ';
			$entry_block .= '<td width="30%" align="right">
                                <table width="100%">
                                    <tr>
                                        ';
			if ($entry->show_class != 'Ex Only') {
				foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
					if ($show->show_format != 'Alternative') {
						if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number]))
							$entry_block .= '<td align="left" valign="top">____</td>';
						else
							$entry_block .= '<td align="left" valign="top">&nbsp;</td>';
					}
					else {
						if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number])) {
							if (($first_show_day->ring_timing == 1 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($first_show_day->ring_timing == 2 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_PM))
								$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">____</td>';
							else
								$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
						} else
							$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
					}
				}
			}
			else {
				$entry_block .= '<td></td>';
			}
			$entry_block .= '       </tr>
                                </table>
                            </td>
                            ';

			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

			$entry_block .= '<td width="40%" align="center">
                                <table width="100%">
                                    ';

			$entry_block .= '<tr>
                                <td width="9%" align="left" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_number . '</td>
                                <td width="69%" align="left" >' . ( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name) ) . '</td>
                                <td width="13%" align="right" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_age_and_gender . '</td>
                                <td width="9%" align="right" valign="top" rowspan="' . ($isNotHHP ? 5 : 3) . '">' . $entry->catalog_number . '</td>
                            </tr>';

			if (trim($entry->catalog_registration_number) == '')
				$reg_number = JText::_('PENDING');
			else
				$reg_number = $entry->catalog_registration_number;

			$entry_block .= '<tr>
                                <td align="left" >' . $reg_number . '&nbsp;&nbsp;&nbsp;' . JText::_('COM_TOES_CATALOG_BORN') . '&nbsp;' . $entry->catalog_birthdate . '</td>
                            </tr>';
			if ($isNotHHP) {
				$entry_block .= '<tr>
                                    <td align="left" >' . strtoupper($entry->catalog_sire) . '</td>
                                </tr>';
				$entry_block .= '<tr>
                                    <td align="left" >' . strtoupper($entry->catalog_dam) . '</td>
                                </tr>';
			}
			$entry_block .= '<tr>
                                <td align="left" valign="bottom" >' . $entry->catalog_region . '</td>
                                <td align="left" >' .
					($entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '<br/>' : '')
					. ($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '<br/>' : '')
					. ($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '<br/>' : '')
					. ($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '')
					. '</td>
                                <td align="right" valign="bottom">' . $entry->catalog_region . '</td>
                            </tr>';

			$entry_block .= '   </table>
                            </td>
                            ';

			$entry_block .= '<td width="30%" align="right">
                                <table width="100%">
                                    <tr>
                                        ';
			if ($entry->show_class != 'Ex Only') {
				foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
					if ($show->show_format != 'Alternative') {
						if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number]))
							$entry_block .= '<td align="right" valign="top">____</td>';
						else
							$entry_block .= '<td align="right" valign="top">&nbsp;</td>';
					}
					else {
						if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number])) {
							if (($second_show_day->ring_timing == 1 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($second_show_day->ring_timing == 2 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_PM))
								$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">____</td>';
							else
								$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
						} else
							$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
					}
				}
			} else
				$entry_block .= '<td></td>';

			$entry_block .= '       </tr>
                                </table>
                            </td>
                            ';
			$entry_block .= '</tr>
                        </table>';
		}

		//$pdf->startTransaction();
		//$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		/*while ($print_block > 0) {

			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$previous_breed_entries = 1;
					$previous_division_entries = 1;
					$breed_block = '<div style="text-align:center; font-weight:bold;color:' . $breed_title_color . '; text-decoration:underline;">' . strtoupper($entry->breed_name) . '</div>';

					$pdf->SetFont('ptsans', 'b', $font_size + 4);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_breed_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
					$previous_division_entries = 1;
					$breed_block = '<div style="text-align:center;color:' . $division_title_color . '; text-decoration:underline;">' . strtoupper($entry->catalog_division) . '</div>';

					$pdf->SetFont('ptsans', '', $font_size + 2);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_division_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$breed_block = '<div style="text-align:center; font-weight:bold;">' . strtoupper($entry->color_name) . '</div>';

					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			$next = $cur + 1;
			if (in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($entries) ||
					( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
				$best_division_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_division_entries; $i++) {
					$best_division_block .= '<tr style="line-height: 150%;">
                                    ';

					$best_division_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$best_division_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                        ';
					}
					$best_division_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';

					if (!in_array($entry->show_class, $skip_breed_best) && ($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name))
						$best_division_block .= '<td style="color:' . $bob_color . ';" width="40%" align="center">' . JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i) . '</td>
                                                ';
					else
						$best_division_block .= '<td style="color:' . $bod_color . ';" width="40%" align="center">' . JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i) . '</td>
                                                ';

					$best_division_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$best_division_block .= '<td colspan="2" align="' . (($show->show_format == 'Alternative' && $second_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                                ';
					}
					$best_division_block .= '</tr>
                                        </table>
                                    </td>
                                    ';

					$best_division_block .= '</tr>';
					if ($i == 3)
						break;
				}

				$best_division_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_division_block, true, false, false, false, '');
				$pdf->ln(1);
			}

			if (in_array($entry->show_class, $skip_breed_best)) {
				$previous_breed_entries = 0;
			} else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {
				$best_breed_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_breed_entries; $i++) {
					$best_breed_block .= '<tr style="line-height: 150%;">
                                    ';

					$best_breed_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$best_breed_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                                ';
					}
					$best_breed_block .= '</tr>
                                        </table>
                                    </td>
                                    ';

					$best_breed_block .= '<td style="color:' . $bob_color . ';" width="40%" align="center">' . JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i) . '</td>
                                            ';

					$best_breed_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$best_breed_block .= '<td colspan="2" align="' . (($show->show_format == 'Alternative' && $second_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                                ';
					}
					$best_breed_block .= '</tr>
                                        </table>
                                    </td>
                                    ';

					$best_breed_block .= '</tr>';
					if ($i == 3)
						break;
				}
				$best_breed_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_breed_block, true, false, false, false, '');

				$pdf->ln(1);
			}
			$pdf->ln(1);

// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}

					if ($previous_division != $entry->catalog_division) {
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;

					$processed++;
					$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
					$data = array(
						'total' => $total,
						'processed' => $processed,
						'log' => $log
					);
					fputs($fp, serialize($data));
					fclose($fp);
				}

				$print_block = 0;
			} else {
				$previous_breed_entries--;
				$previous_division_entries--;
// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_logo = JURI::root() . 'media/com_toes/images/paw32X32.jpg';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				if($params->get('use_logo_for_pdf')) {
					$show_class_block .='<img src="' . $header_logo . '" />';
				} else {
					$show_class_block .=' ';
				}
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-size:40px; font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span style="font-size:35px">' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:70px">' . strtoupper($entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', $font_size + 2);
				$pdf->writeHTML($show_class_block, true, false, false, false, '');

				$judge_block = '<table width="100%">
                                    <tr>
                                ';

				if ($show->show_format != 'Alternative') {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                <td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[0]->show_day_date)))) . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td align="left">' . $first_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				} else {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
												<td align="left" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
												<td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $first_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				}

				$judge_block .= '<td width="40%" align="center">&nbsp;</td>
                                        ';

				if ($show->show_format != 'Alternative') {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                <td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[1]->show_day_date)))) . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td align="right">' . $second_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				} else {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
												<td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
												<td align="right" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                            </tr>
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $second_show_day->judge_abbreviation . '</td>
                                                ';
					}
					$judge_block .= '</tr>
                                        </table>
                                    </td>
                                    ';
				}

				$judge_block .= '</tr>
                            </table>';

				if ($entry->show_class != 'Ex Only') {
					$pdf->SetFont('ptsansnarrow', '', $font_size);
					$pdf->writeHTML($judge_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<div style="text-align:center; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</div>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<div style="text-align:center; color:' . $breed_title_color . '; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</div>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_breed = $entry->breed_name;
				}

				$block_page = $pdf->getPage();
				--$print_block;
			}
		} */ // end while print_block
		//$pdf->ln(1);
		$cur++; 
	} 
} else {
	/*foreach ($entries as $entry) {
		if ($previous_class != $entry->show_class) {
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;

			if ($previous_class != '')
				$pdf->AddPage();

			$header_logo = JURI::root() . 'media/com_toes/images/paw32X32.jpg';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			if($params->get('use_logo_for_pdf')) {
				$show_class_block .='<img src="' . $header_logo . '" />';
			} else {
				$show_class_block .=' ';
			}
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-size:40px; font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span style="font-size:35px">' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:70px">' . strtoupper($entry->show_class) . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			$pdf->SetFont('ptsanscaption', '', $font_size + 2);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');

			$judge_block = '<table width="100%">
			  <tr>
			  ';

			$judge_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">&nbsp;</td>
			  ';

			if ($show->show_format != 'Alternative') {
				foreach ($show_days as $show_day) {
					if (count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
			  <table>
			  <tr>
			  <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . '</td>
			  </tr>
			  <tr>
			  ';
					foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						$judge_block .= '<td align="left">' . $pr_show_day->judge_abbreviation . '</td>
			  ';
					}
					$judge_block .= '       </tr>
			  </table>
			  </td>
			  ';
				}
			} else {
				foreach ($show_days as $show_day) {
					if (count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
			  <table>
			  <tr>
			  <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM' : '&nbsp;') . '</td>
			  <td align="right" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM' : '&nbsp;') . '</td>
			  </tr>
			  <tr>
			  ';
					foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $pr_show_day->judge_abbreviation . '</td>
			  ';
					}
					$judge_block .= '       </tr>
			  </table>
			  </td>
			  ';
				}
			}

			$judge_block .= '</tr>
			  </table>';
			if ($entry->show_class != 'Ex Only') {
				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln();
			}

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
		}

		if ($previous_catalog_number != $entry->catalog_number) {
			$entry_block = '<table width="100%">
		  <tr>
		  ';

			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

			$entry_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">
		  <table>
		  ';

			$entry_block .= '<tr>
		  <td width="67%" align="left" >' . ( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name) ) . '</td>
		  <td width="13%" align="right" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_age_and_gender . '</td>
		  <td width="10%" align="right" valign="top" rowspan="' . ($isNotHHP ? 5 : 3) . '">' . $entry->catalog_number . '</td>
		  </tr>';

			if (trim($entry->catalog_registration_number) == '')
				$reg_number = JText::_('PENDING');
			else
				$reg_number = $entry->catalog_registration_number;

			$entry_block .= '<tr>
		  <td align="left" >' . strtoupper($reg_number) . '&nbsp;&nbsp;&nbsp;' . JText::_('COM_TOES_CATALOG_BORN') . '&nbsp;' . $entry->catalog_birthdate . '</td>
		  </tr>';
			if ($isNotHHP) {
				$entry_block .= '<tr>
		  <td align="left" >' . strtoupper($entry->catalog_sire) . '</td>
		  </tr>';
				$entry_block .= '<tr>
		  <td align="left" >' . strtoupper($entry->catalog_dam) . '</td>
		  </tr>';
			}
			$entry_block .= '<tr>
		  <td align="left" >' .
					($entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '<br/>' : '')
					. ($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '<br/>' : '')
					. ($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '<br/>' : '')
					. ($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '')
					. '</td>
		  <td align="right">' . $entry->catalog_region . '</td>
		  </tr>';

			$entry_block .= '   </table>
		  </td>
		  ';
			if ($entry->show_class != 'Ex Only') {
				if ($show->show_format != 'Alternative') {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
		  <table width="100%">
		  <tr>
		  ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							if (isset($show_day_entries[$show_day->show_day_id][$entry->catalog_number]))
								$entry_block .= '<td align="left" valign="top">____</td>';
							else
								$entry_block .= '<td align="left" valign="top">&nbsp;</td>';
						}
						$entry_block .= '       </tr>
		  </table>
		  </td>
		  ';
					}
				}
				else {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
		  <table width="100%">
		  <tr>
		  ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							if (isset($show_day_entries[$show_day->show_day_id][$entry->catalog_number])) {
								if (($pr_show_day->ring_timing == 1 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($pr_show_day->ring_timing == 2 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_PM))
									$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">____</td>';
								else
									$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
							} else
								$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
						}
						$entry_block .= '       </tr>
		  </table>
		  </td>
		  ';
					}
				}
			} else
				$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '"></td>';

			$entry_block .= '</tr>
		  </table>';
		}

		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {
			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$previous_breed_entries = 1;
					$previous_division_entries = 1;
					$pdf->SetFont('ptsans', 'b', $font_size + 4);
					$breed_block = '<span style="text-align:left; font-weight:bold;color:' . $breed_title_color . '; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->breed_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_breed_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
					$previous_division_entries = 1;
					$pdf->SetFont('ptsans', '', $font_size + 2);
					$breed_block = '<span style="text-align:left;color:' . $division_title_color . '; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->catalog_division) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_division_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left; font-weight:bold; padding:5px 0;">' . strtoupper($entry->color_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			$next = $cur + 1;
			if (in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($entries) ||
					( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
				$best_division_block = '<table width="100%">
		  ';
				for ($i = 1; $i <= $previous_division_entries; $i++) {
					$best_division_block .= '<tr style="line-height: 150%;">
		  ';
					if (!in_array($entry->show_class, $skip_breed_best) && ($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name))
						$best_division_block .= '<td style="color:' . $bob_color . ';" width="' . (count($show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i) . '</td>
		  ';
					else
						$best_division_block .= '<td style="color:' . $bod_color . ';" width="' . (count($show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i) . '</td>
		  ';
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$best_division_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
		  <table>
		  <tr>
		  ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$best_division_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
		  ';
						}
						$best_division_block .= '       </tr>
		  </table>
		  </td>
		  ';
					}

					$best_division_block .= '</tr>';
					if ($i == 3)
						break;
				}

				$best_division_block .= '
		  </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_division_block, true, false, false, false, '');
				$pdf->ln(1);
			}

			if (in_array($entry->show_class, $skip_breed_best)) {
				$previous_breed_entries = 0;
			} else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {
				$best_breed_block = '<table width="100%">
		  ';
				for ($i = 1; $i <= $previous_breed_entries; $i++) {
					$best_breed_block .= '<tr style="line-height: 150%;">
		  ';

					$best_breed_block .= '<td style="color:' . $bob_color . ';" width="' . (count($show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i) . '</td>
		  ';

					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$best_breed_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
		  <table>
		  <tr>
		  ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$best_breed_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
		  ';
						}
						$best_breed_block .= '       </tr>
		  </table>
		  </td>
		  ';
					}

					$best_breed_block .= '</tr>';
					if ($i == 3)
						break;
				}
				$best_breed_block .= '
		  </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_breed_block, true, false, false, false, '');
				$pdf->ln(1);
			}
			$pdf->ln(1);

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}

					if ($previous_division != $entry->catalog_division) {
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;

					$processed++;
					$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
					$data = array(
						'total' => $total,
						'processed' => $processed,
						'log' => $log
					);
					fputs($fp, serialize($data));
					fclose($fp);
				}

				$print_block = 0;
			} else {
				$previous_breed_entries--;
				$previous_division_entries--;
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_logo = JURI::root() . 'media/com_toes/images/paw32X32.jpg';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				if($params->get('use_logo_for_pdf')) {
					$show_class_block .='<img src="' . $header_logo . '" />';
				} else {
					$show_class_block .=' ';
				}
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-size:40px; font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span style="font-size:35px">' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:70px">' . strtoupper($entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', $font_size + 2);
				$pdf->writeHTML($show_class_block, true, false, false, false, '');

				$judge_block = '<table width="100%">
		  <tr>
		  ';

				$judge_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">&nbsp;</td>
		  ';

				if ($show->show_format != 'Alternative') {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
		  <table width="100%">
		  <tr>
		  <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . '</td>
		  </tr>
		  <tr>
		  ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td align="left">' . $pr_show_day->judge_abbreviation . '</td>
		  ';
						}
						$judge_block .= '       </tr>
		  </table>
		  </td>
		  ';
					}
				} else {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
		  <table>
		  <tr>
		  <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM' : '&nbsp;') . '</td>
		  <td align="right" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM' : '&nbsp;') . '</td>
		  </tr>
		  <tr>
		  ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td colspan="2" align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $pr_show_day->judge_abbreviation . '</td>
		  ';
						}
						$judge_block .= '       </tr>
		  </table>
		  </td>
		  ';
					}
				}

				$judge_block .= '</tr>
		  </table>';

				if ($entry->show_class != 'Ex Only') {
					$pdf->SetFont('ptsansnarrow', '', $font_size);
					$pdf->writeHTML($judge_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left;color:' . $breed_title_color . '; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_breed = $entry->breed_name;
				}

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
		$cur++;
	}*/
}

$file = JRequest::getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Processing Congress Entries....";
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);


$query = "DROP TABLE `j1temp_toes_entry_{$show_id}_{$time}`, `j1temp_toes_full_entry_{$show_id}_{$time}`;";
$db->setQuery($query);
$db->query();


// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if(!file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id))
    JFolder::create(JPATH_BASE. "/media/com_toes/DOCX/".$show_id, 0777);
else
	chmod (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id, 0777);

if(file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'exibitor_list.docx'))
	unlink (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'exibitor_list.docx');

echo write($phpWord, $writers,$show_id);

//============================================================+
// END OF FILE
//============================================================+
