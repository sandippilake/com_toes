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
				   
					$xmlWriter->save(JPATH_BASE. "/media/com_toes/DOCX/".$show_id."/master_exibitor_list.docx");
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
		'marginRight'=>100,
		'headerHeight'=>10
		
		);

		//$section = $phpWord->createSection($sectionSettings);
		$section = $phpWord->createSection(array('marginLeft' => 200, 'marginRight' => 400, 'marginTop' => 0, 'marginBottom' => 200, 'headerHeight' => 50, 'footerHeight' => 500,'pageNumberingStart'=>1));

		$header = $section->createHeader();
		
       
    
		$lineStyle = array('weight' => 1, 'width' => 700, 'height' => 5, 'color' => '#000000');
		//$header = array('size' => 16, 'bold' => true);

		 $footer = $section->createFooter();
		 $footer->addLine($lineStyle);
		 $show = TOESHelper::getShowDetails($show_id);
			if($show)
				{
					$text = $show->club_name.' - '.$show->Show_location.' - '.$show->show_dates;
				}
			else
					$text='';
       $footer->addText($text,array('size' => 10,'italic'=>true));
		//$footer->addPreserveText('Page {PAGE} of {NUMPAGES}.',array('size'=>10));
		
		
		$header = $section->createHeader();
      
        $fontStyleTitle = array('size' => 14,'bold'=>true);
        $fontStyleSubTitle = array('size' => 10,'bold'=>false);
        $paragraphStyleTitle = array('spaceBefore' => 0);
        $styleTable = array('borderSize'=>0,'borderColor' => 'FFFFFF');
		$lineStyle = array('weight' => 1, 'width' => 700, 'height' => 0, 'color' => '#b2a68b');
        $table = $header->addTable($styleTable);
       
        $table->addRow();
		$logo = JURI::root().'media/com_toes/images/paw32X32.png';
        $table->addCell(500)->addImage($logo, array('align' => 'left'));
      
       
       //$table->addCell(4000)->addText(JText::_('COM_TOES_EXHIBITORLIST_HEADER').' '.JText::_('COM_TOES_SITE_TITLE'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
      
       $cell=$table->addCell(4000);
       $cell->addText(JText::_('COM_TOES_MASTER_EXHIBITOR_LIST'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $cell->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $header->addText();
       $lineStyle = array('weight' => 1, 'width' => 710, 'height' => 0, 'color' => '#000000');
       $header->addLine($lineStyle);

	
		$i = 0;
		$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 10);
		$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');

		$phpWord->setDefaultFontName('Tahoma');
		$phpWord->setDefaultFontSize(14);
		$styleTable = array('borderSize'=>0,'cellMargin'=>10,'borderColor' => 'FFFFFF');
		$table = $section->addTable($styleTable);
		
		
		$phpWord->setDefaultFontName('Tahoma');
		$phpWord->setDefaultFontSize(12);
		$db = JFactory::getDBO();
		//$query = "SELECT `Exhibitor` , `Address` , `City` , `Country` , `Entries` , `show_id` , `late_entry` FROM `#__toes_view_exhibitor_list_basis` WHERE (`late_entry` = 0) AND (`show_id` = $show_id)";
		//$query = "SELECT `Exhibitor` , `Address` , `City` , `Country` , `Entries` , `show_id` , `late_entry` FROM `#__toes_view_exhibitor_list_basis` WHERE (`show_id` = $show_id)";
		$query = "SELECT CONCAT_WS( ', ', `cb`.`lastname` , `cb`.`firstname` ) AS `Exhibitor` ,
		GROUP_CONCAT( DISTINCT(`e`.`catalog_number`) ORDER BY CAST( `e`.`catalog_number` AS UNSIGNED ) ) AS `Entries`,
		CONCAT_WS( ' ', `cb`.`cb_address1` , `cb`.`cb_address2` , `cb`.`cb_address3` ) AS `Address` ,
		CONCAT_WS( ' ', `cb`.`cb_city` , `cb`.`cb_zip` , `cb`.`cb_state` ) AS `City` ,
		`cb`.`cb_country` AS `Country` 
		FROM `#__toes_entry` AS `e`
		LEFT JOIN `j35_toes_entry_status` AS `es` ON `e`.`status` = `es`.`entry_status_id`
		LEFT JOIN `#__toes_summary` AS `s` ON `s`.`summary_id` = `e`.`summary`
		LEFT JOIN `#__comprofiler` AS `cb` ON `cb`.`user_id` = `s`.`summary_user` 
		WHERE `e`.`entry_show` = $show_id AND ( (`es`.`entry_status` = 'Accepted') OR(`es`.`entry_status` = 'Confirmed') OR (`es`.`entry_status` = 'Confirmed & Paid') )
		GROUP BY `e`.`entry_show`, `e`.`summary` ORDER BY `cb`.`lastname` ASC , `cb`.`firstname` ASC;";

		$db->setQuery($query);
		$exhibitor_list = $db->loadObjectList();
		error_reporting(E_ALL);
		$i = 0;
		
		if($exhibitor_list)
		{
			foreach($exhibitor_list as $exhibitor)
			{
				
				$address = '';
				if($exhibitor->Address)
					$address .= $exhibitor->Address.',  ';
				if($exhibitor->City)
					$address .= $exhibitor->City.',  ';
				if($exhibitor->Country)
					$address .= $exhibitor->Country;
				  
				  if($i%2)
							$color='e2e2e5';
				   else
							$color='FFFFFF';
					 $cellstyle=array('bgColor'=>'FFFFFF','borderColor' => 'FFFFFF','borderSize'=>0);

				  
				  $table->addRow(500); 
				  $table->addCell(9000, array('bgColor'=>$color,'borderColor' => $color,'borderSize'=>0))->addText(strtoupper($exhibitor->Exhibitor));  
				  $table->addCell(2000,array('bgColor'=>$color,'borderColor' => $color,'borderSize'=>0))->addText( $exhibitor->Entries,null,array('align'=>'right','valign'=>'right'));
				  $table->addRow(500);
				  $table->addCell(9000,array('bgColor'=>$color,'borderColor' => $color,'borderSize'=>0,'gridspan'=>2))->addText( "         ".strtoupper($address));  
				  $table->addRow(300);
				  $table->addCell(9000,array('gridspan'=>2))->addText('');	
			   

				$i++;
			}
		}

		$table->addRow(300);
		$table->addCell(9000,array('gridspan'=>2))->addText('');	
		$table->addRow(300);
		$table->addCell(9000)->addText(JText::_('COM_TOES_TOTAL_NUMBER_OF_EXHIBITORS_MASTER'),null,array('align'=>'center'));	
		$table->addCell(2000)->addText($i,null,array('align'=>'right'));



// ---------------------------------------------------------

jimport('joomla.filesystem.folder');
//echo JFolder::exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id);
if(!JFolder::exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id))
    JFolder::create(JPATH_BASE. "/media/com_toes/DOCX/".$show_id, 0755);
else
	chmod (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id, 0755);
//echo "hi";die;
if(file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'master_exibitor_list.docx'))
	unlink (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'master_exibitor_list.docx');

echo write($phpWord, $writers,$show_id);
//Close and output PDF document


//============================================================+
// END OF FILE                                                
//============================================================+
