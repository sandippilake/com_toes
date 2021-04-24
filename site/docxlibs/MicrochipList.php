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
				   
					$xmlWriter->save(JPATH_BASE. "/media/com_toes/DOCX/".$show_id."/microchip_list.docx");
				  
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
		$section = $phpWord->createSection(array('marginLeft' => 200, 'marginRight' => 400, 'marginTop' => 0, 'marginBottom' => 200, 'headerHeight' => 0, 'footerHeight' => 500,'pageNumberingStart'=>1));

		$header = $section->createHeader();
		
       
    
		$lineStyle = array('weight' => 1, 'width' => 700, 'height' => 5, 'color' => '#000000');
		$header = array('size' => 16, 'bold' => true);

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
		
		
		
		$section->addTitle('');
		$newheader = $section->createHeader();
		$wlineStyle = array('weight' => 1, 'width' => 700, 'height' => 10, 'color' => '#FFFFFF');
		 $styleTable = array('borderSize'=>0,'borderColor' => 'FFFFFF');
		$newheader->addLine($wlineStyle);
		 $table = $newheader->addTable($styleTable);
         $fontStyleTitle = array('size' => 16,'bold'=>true);
        $fontStyleSubTitle = array('size' => 10,'bold'=>false);
        $paragraphStyleTitle = array('spaceBefore' => 0);
         
        $table->addRow();
		$logo = JURI::root().'media/com_toes/images/paw32X32.png';
        $table->addCell(500)->addImage($logo, array('align' => 'left'));
      
       
       //$table->addCell(4000)->addText(JText::_('COM_TOES_EXHIBITORLIST_HEADER').' '.JText::_('COM_TOES_SITE_TITLE'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
      
       $cell=$table->addCell(8000);
       $cell->addText(JText::_('COM_TOES_MICROCHIP_LIST'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $cell->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $newheader->addText();
       $lineStyle = array('weight' => 1, 'width' => 710, 'height' => 0, 'color' => '#000000');
       $newheader->addLine($lineStyle);

	
		$i = 0;
		$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 10);
		$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');

		$phpWord->setDefaultFontName('Tahoma');
		$phpWord->setDefaultFontSize(14);
		$styleTable = array('borderSize'=>0,'cellMargin'=>10,'borderColor' => 'FFFFFF');
		$table = $section->addTable($styleTable);
		$exhibitors = TOESHelper::getShowExhibitors($show_id);

		if(!$exhibitors)
			echo JText::_('COM_TOES_NO_EXHIBITORS').'<br/>';


		$i = 0;
		
		
$db = JFactory::getDBO();
$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;
$whr[] = '`e`.`late_entry` = 0';

$query = TOESQueryHelper::getEntryFullViewQuery($whr);
$query->select('`cat`.`cat_id_chip_number`');

$query->group('`e`.`cat`');
$query->clear('order');
$query->order('`cprof`.`lastname`');
$db->setQuery($query);
$entries = $db->loadObjectList();


$final_entries = array();
foreach($entries as $entry)
{
    $final_entries[$entry->summary_user][] = $entry;
}

foreach($final_entries as $entries)
{
    $i = 0;
    $section->addTableStyle('table1',array('borderSize' => 6));
    $table1=$section->addTable(array('borderSize' => 6),array('keepNext'=>true,'widowControl'=>true));
    
    
    foreach($entries as $entry)
    {
        if($i == 0)
        {
				$table1->addRow(500);
				$table1->addCell(3000,array('vMerge'=>'restart','borderSize'=>1,'borderColor'=>'#000000'),array('keepNext'=>true))->addText(strtoupper($entry->lastname.' '.$entry->firstname),array('size'=>12),array('keepNext'=>true));
				$table1->addCell(5000,array('borderSize'=>1,'borderColor'=>'#000000'))->addText(strtoupper($entry->copy_cat_name),array('size'=>12));
				$table1->addCell(3000,array('borderSize'=>1,'borderColor'=>'#000000'))->addText(($entry->cat_id_chip_number?$entry->cat_id_chip_number:'-'),array('size'=>12),array('align'=>'center'));
         
        }
        else
        {
				$table1->addRow(500);
				$table1->addCell(1000,array('vMerge' => 'continue'))->addText('');
				$table1->addCell(5000,array('borderSize'=>1,'borderColor'=>'#000000'))->addText(strtoupper($entry->copy_cat_name),array('size'=>12));
				$table1->addCell(3000,array('borderSize'=>1,'borderColor'=>'#000000'))->addText(($entry->cat_id_chip_number?$entry->cat_id_chip_number:'-'),array('size'=>12),array('align'=>'center'));
          
        }
        $i++;
    }
    $section->addText('');
   
    $print_block = 2; // 2 tries max
  
}






// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if(!file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id))
    JFolder::create(JPATH_BASE. "/media/com_toes/DOCX/".$show_id, 0777);
else
	chmod (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id, 0777);

if(file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'microchip_list.docx'))
	unlink (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'microchip_list.docx');

echo write($phpWord, $writers,$show_id);
//Close and output PDF document


//============================================================+
// END OF FILE                                                
//============================================================+
