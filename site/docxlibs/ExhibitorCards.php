<?php
jimport('phpword.Autoloader');

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

Autoloader::register();
Settings::loadConfig();

$writers = array('Word2007' => 'docx');

$entry_status = array(
	'New',
	'Accepted',
	'Confirmed',
	'Confirmed & Paid'
);

		function write($phpWord, $writers,$show_id)
		{
			
			$result = '';

			// Write documents
			foreach ($writers as $writer => $extension) {
			  
			//	$result .= date('H:i:s') . " Write to {$writer} format";
				if (!is_null($extension)) {
					$xmlWriter = IOFactory::createWriter($phpWord, $writer);
				   
					$xmlWriter->save(JPATH_BASE. "/media/com_toes/DOCX/".$show_id."/exhibitorcards.docx");
				  
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
		$ftable=$footer->addTable();
		$ftable->addRow(500);
		$ftable->addCell(5000)->addPreserveText('Page {PAGE} of {NUMPAGES}.',array('size'=>10));
		$ftable->addCell(5000)->addText( date('M d, Y', time()),array('size'=>10),array('align'=>'right'));
		//$footer->addPreserveText('Page {PAGE} of {NUMPAGES}.',array('size'=>10));
		
		
		$show = TOESHelper::getShowDetails($show_id);
		
		$isContinuous = ($show->show_format == 'Continuous')?1:0;
		
		$section->addTitle('');
		$newheader = $section->createHeader();
		$wlineStyle = array('weight' => 1, 'width' => 700, 'height' => 10, 'color' => '#FFFFFF');
		$newheader->addLine($wlineStyle);
		$table1=$newheader->addTable();
		$table1->addRow(500);
		$cell1=$table1->addCell(3000);
		$cell1->addText( $show->club_name,array('size'=>14),array('align'=>'left'));
		
		$cell2=$table1->addCell(4000);
		$cell2->addText(JText::_('Exhibitor Cards'),array('size'=>14),array('align'=>'center'));
		
		$cell3=$table1->addCell(4500);
		$cell3->addText( $show->Show_location,array('size'=>14),array('align'=>'right'));
		$cell3->addText( $show->show_dates,array('size'=>14),array('align'=>'right'));
		$newheader->addText(' ',array('width'=>9000));
	
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
			echo JText::_('COM_TOES_NO_EXHIBITORS');


		$i = 0;
		
		
foreach ($exhibitors as $exhibitor) {

	$entries = TOESHelper::getEntries($exhibitor->user_id, $show_id, $entry_status);
	$placeholders = TOESHelper::getPlaceholders($exhibitor->user_id, $show_id, $entry_status);
	
	if (!$entries && !$placeholders)
		continue;		

		
		//$section = $phpWord->addSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 200, 'marginBottom' => 200, 'headerHeight' => 100, 'footerHeight' => 500,'orientation'=>'landscape'));
		/*$section->addTitle('',array('size'=>14));
		//$newheader = $section->createHeader();
		$lineStyle = array('weight' => 1, 'width' => 710, 'height' => 10, 'color' => '#000000');
		$section->addLine($lineStyle);
		$table1=$section->addTable();
		$table1->addRow(500);
		$table1->addCell(3000)->addText( $show->club_name,array('size'=>14),array('align'=>'left'));
		$table1->addCell(4000)->addText(JText::_('Exhibitor Cards'),array('size'=>14),array('align'=>'center'));
		$cell13=$table1->addCell(4500);
		$cell13->addText( $show->Show_location,array('size'=>14),array('align'=>'right'));
		$cell13->addText( $show->show_dates,array('size'=>14),array('align'=>'right'));
		$section->addText(' ');
		$lineStyle = array('weight' => 1, 'width' => 1050, 'height' => 10, 'color' => '#000000');
		$section->addLine($lineStyle);*/
	
		$table2=$section->addTable();
		$table2->addRow(500);
		$table2->addCell(5000)->addText($exhibitor->user_name,array('bold'=>true),array('align'=>'left'));
	
	
		$styleCell = array('valign' => 'center','borderTopColor'=>'000000','borderTopSize' => 10);
	
		$section->addText('');
		$table31=$section->addTable();
		$table31->addRow(300);
		$table31->addCell(1500)->addText(strip_tags(JText::_('COM_TOES_SHOW_ENTRIES_CATALOG_NUMBER_HEADER')),array('size'=>11),array('align'=>'left'));
		$table31->addCell(2000)->addText(strip_tags(JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER')),array('size'=>11),array('align'=>'center'));
		$table31->addCell(1500)->addText(strip_tags(JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER')),array('size'=>11),array('align'=>'center'));
		$table31->addCell(1500)->addText(strip_tags(JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER')),array('size'=>11),array('align'=>'center'));
		$table31->addCell(1500)->addText(strip_tags(JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER')),array('size'=>11),array('align'=>'center'));
		$table31->addCell(1500)->addText(strip_tags(JText::_('COM_TOES_SHOW_ENTRIES_FORSALE_HEADER')),array('size'=>11),array('align'=>'center'));
		$table31->addCell(1500)->addText(strip_tags(JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER')),array('size'=>11),array('align'=>'center'));


		$table3=$section->addTable();
	//$section->addLine($lineStyle);
	$flag=0;
	foreach($entries as $entry)
	{
		if($isContinuous)
			$showdays = JText::_('JALL');
		else
			$showdays = $entry->showdays;
		

		if($flag)
		{
			$styleCell=array();
		}
		$flag=1;
		
		$table3->addRow(1000);
		$table3->addCell(1500,$styleCell)->addText($entry->catalog_number,array('size'=>11),array('align'=>'left'));
		$table3->addCell(2000,$styleCell)->addText( $entry->cat_prefix_abbreviation . ' ' . $entry->cat_title_abbreviation . ' ' . $entry->copy_cat_name . ' ' . $entry->cat_suffix_abbreviation,array('size'=>12),array('align'=>'center'));
		$table3->addCell(1500,$styleCell)->addText($showdays,array('size'=>11),array('align'=>'center'));
		$table3->addCell(1500,$styleCell)->addText($entry->Show_Class,array('size'=>11),array('align'=>'center'));
		$table3->addCell(1500,$styleCell)->addText(($entry->exhibition_only ? JText::_('JYES') : JText::_('JNO')),array('size'=>11),array('align'=>'center','size'=>14));
		$table3->addCell(1000,$styleCell)->addText(($entry->for_sale ? JText::_('JYES') : JText::_('JNO')),array('size'=>11),array('align'=>'center','size'=>14));
		$table3->addCell(1500,$styleCell)->addText( ($entry->congress)?$entry->congress:'-',array('size'=>11),array('align'=>'center','size'=>14));
	

		
		

	}
	
	foreach($placeholders as $placeholder)
	{
		if($isContinuous)
			$showdays = JText::_('JALL');
		else
			$showdays = $placeholder->showdays;

		
		$table3->addRow(1000);
		$table3->addCell(1500)->addText('-',null,array('align'=>'left'));
		$table3->addCell(2000)->addText(JText::_('COM_TOES_PLACEHOLDER'),array('size'=>11),array('align'=>'center'));
		$table3->addCell(1500)->addText($showdays);
		$table3->addCell(1500)->addText('-',null,array('align'=>'center'));
		$table3->addCell(1500)->addText('-',null,array('align'=>'center'));
		$table3->addCell(1500)->addText('-',null,array('align'=>'center'));
		$table3->addCell(1500)->addText('-',null,array('align'=>'center'));
		
		
	}
	
	$section->addText(' ',null,array('pageBreakBefore'=>true));
}


// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if(!file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id))
    JFolder::create(JPATH_BASE. "/media/com_toes/DOCX/".$show_id, 0777);
else
	chmod (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id, 0777);

if(file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'exhibitorcards.docx'))
	unlink (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'exhibitorcards.docx');

echo write($phpWord, $writers,$show_id);
//Close and output PDF document


//============================================================+
// END OF FILE                                                
//============================================================+
