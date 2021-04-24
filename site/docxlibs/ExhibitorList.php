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
				   
					$xmlWriter->save(JPATH_BASE. "/media/com_toes/DOCX/".$show_id."/exibitor_list.docx");
				  
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

		$header = $section->createHeader();
      
        $fontStyleTitle = array('size' => 16,'bold'=>true);
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
       $cell->addText(JText::_('COM_TOES_EXHIBITOR_LIST'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $cell->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
       $header->addText();
      $lineStyle = array('weight' => 1, 'width' => 710, 'height' => 0, 'color' => '#000000');
       $header->addLine($lineStyle);
     // $header->addLine($lineStyle);
      
       // $header->addText('MY HEADER TITLE', $fontStyleTitle, $paragraphStyleTitle);


//$section=$phpWord->addSection();
$header = array('size' => 16, 'bold' => true);

$db = JFactory::getDBO();
//$query = "SELECT `Exhibitor` , `Address` , `City` , `Country` , `Entries` , `show_id` , `late_entry` FROM `#__toes_view_exhibitor_list_basis` WHERE (`late_entry` = 0) AND (`show_id` = ".$show_id.")";

$query = "SELECT CONCAT_WS( ', ', `cb`.`lastname` , `cb`.`firstname` ) AS `Exhibitor` ,
GROUP_CONCAT( DISTINCT(`e`.`catalog_number`) ORDER BY CAST( `e`.`catalog_number` AS UNSIGNED ) ) AS `Entries`
FROM `#__toes_entry` AS `e`
LEFT JOIN `j35_toes_entry_status` AS `es` ON `e`.`status` = `es`.`entry_status_id`
LEFT JOIN `#__toes_summary` AS `s` ON `s`.`summary_id` = `e`.`summary`
LEFT JOIN `#__comprofiler` AS `cb` ON `cb`.`user_id` = `s`.`summary_user` 
WHERE `e`.`late_entry` = 0 AND `e`.`entry_show` = $show_id AND ( (`es`.`entry_status` = 'Accepted') OR(`es`.`entry_status` = 'Confirmed') OR (`es`.`entry_status` = 'Confirmed & Paid') )
GROUP BY `e`.`entry_show`, `e`.`summary` ORDER BY `cb`.`lastname` ASC , `cb`.`firstname` ASC;";


$db->setQuery($query);
$exhibitor_list = $db->loadObjectList();

//var_dump($exhibitor_list);die;
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
$i = 0;
$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 10);
$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');


//$phpWord->addTableStyle('Fancy Table', $styleTable, $styleFirstRow);

$phpWord->setDefaultFontName('Tahoma');
$phpWord->setDefaultFontSize(14);
$styleTable = array('borderSize'=>0,'cellMargin'=>10,'borderColor' => 'FFFFFF');
$table = $section->addTable($styleTable);

if($exhibitor_list)
{      
foreach($exhibitor_list as $exhibitor)
{
	  $table->addRow(500);

    if($i%2)
        $cellstyle=array('bgColor'=>'e2e2e5','borderColor' => 'e2e2e5','borderSize'=>0);
    else
        $cellstyle=array('bgColor'=>'FFFFFF','borderColor' => 'FFFFFF','borderSize'=>0);
        
  $table->addCell(5800,$cellstyle)->addText(strtoupper($exhibitor->Exhibitor),array('color'=>'0000000'),array('valign'=>'left','align'=>'left',));
  $table->addCell(5800,$cellstyle)->addText($exhibitor->Entries,array('color'=>'000000'),array('valign'=>'right','align'=>'right'));

	$i++;
}

}
		$table->addRow(300);
		$table->addCell(9000,array('gridspan'=>2))->addText('');	
		$table->addRow(300);
		$table->addCell(9000)->addText(JText::_('COM_TOES_TOTAL_NUMBER_OF_EXHIBITORS_MASTER'),null,array('align'=>'center'));	
		$table->addCell(2000)->addText($i,null,array('align'=>'right'));


//$section->addText("Table with colspan and rowspan", $header);



// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if(!file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id))
    JFolder::create(JPATH_BASE. "/media/com_toes/DOCX/".$show_id, 0777);
else
	chmod (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id, 0777);

if(file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'exibitor_list.docx'))
	unlink (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'exibitor_list.docx');

echo write($phpWord, $writers,$show_id);
//Close and output PDF document


//============================================================+
// END OF FILE                                                
//============================================================+
