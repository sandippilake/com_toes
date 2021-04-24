<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template styles list controller class.
 * @package	Joomla
 * @subpackage	com_toes
 */
 
 
class TOESControllerInvoiceshows extends JControllerAdmin {

    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'invoiceshows', $prefix = 'ToesModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));        
        return $model;
    }

	 

	 

	public function sendinvoice_bak() {
    	$app = JFactory::getApplication();
    	$db = JFactory::getDBO();
    	$start_date = '2019-01-01';
		 
		 
		$query = $db->getQuery(true);
		 
		$query->select("s.*,v.venue_name,c.club_name");
		$query->from("`#__toes_show` as s");
		$query->join("INNER","`#__toes_venue` as v ON  s.show_venue = v.venue_id"); 
		$query->join("INNER","`#__toes_club_organizes_show` as cs ON  s.show_id = cs.show "); 
		$query->join("INNER","`#__toes_club` as c ON  c.club_id = cs.club"); 
		$query->where("s.show_uses_toes = 1");
		$query->where("s.show_start_date >=".$db->Quote($start_date)); 
		$query->where("s.eo_notified_to_invoice_this_show = 0");
		$rows = $db->setQuery($query)->loadObjectList();
		if(!count($rows))
		$app->redirect('index.php?option=com_toes&view=invoiceshows','No data');
		
		$page_format = $page_format ? $page_format : PDF_PAGE_FORMAT;
		// create new PDF document
		$pdf = new TCPDF('L', PDF_UNIT, $page_format, true, 'UTF-8', false);
 
		// set document information
		$pdf->SetCreator("TICA");
		$pdf->SetTitle(JText::_('COM_TOES_TREASURER'));

		 
		$font_size = $pdf->pixelsToUnits('18');
		//$pdf->SetFont('freesans', '', 12);
		$pdf->SetFont ('helvetica', '', $font_size , '', 'default', true );

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
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

		$pdf->footer_text = date('M d, Y', time());
		
		//
		$pdf->SetFillColor(255, 255, 255);
		
		
		$pdf->AddPage();

		 
		$pdf->writeHTML($header_block, true, false, false, false, '');

		$list_block = '<table width="100%">
							<tr>
								';

		$list_block .= '<td width="7%" align="left">'.JText::_('START_DATE').'</td>';
		$list_block .= '<td width="7%" align="left">'.JText::_('END_DATE').'</td>';
		$list_block .= '<td width="43%" align="left">' . JText::_('CLUB') . '</td>';
		$list_block .= '<td width="43%" align="left">' . JText::_('VENUE') . '</td>';		 
		$list_block .= '</tr><tr>';
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $list_block, 0, true, false, false, false, '');
		//$pdf->Line(5, $pdf->GetY(), 0, $pdf->GetY());

		
		foreach($rows as $row){
		$list_block = '<tr><td width="7%">'.JHTML::_('date',$row->show_start_date,'d/m/Y').'</td>
		<td width="7%">'.JHTML::_('date',$row->show_end_date,'d/m/Y').'</td>
		<td width="43%">'.$row->club_name.'</td>
		<td width="43%">'.$row->venue_name.'</td></tr>';
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $list_block, 0, true, false, false, false, '');
	
		}
		$list_block = '</table>';
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $list_block, 0, true, false, false, false, '');
		//$pdf->Ln(2);
		//
		$pdf->lastPage();
    	$pdf->Output( 'invoiceshows.pdf', 'I');
         
        $app->close();
	}
	public function sendinvoice() {
    	$app = JFactory::getApplication();
    	
    	$params = JComponentHelper::getParams('com_toes');
    	$account_email = $params->get('email_id_send_show_list_tobe_invoiced');
    	
    	$Itemid = $app->input->getInt('Itemid');
    	$db = JFactory::getDBO();
    	$start_date = '2019-01-01';
		 
		 
		$query = $db->getQuery(true);
		 
		$query->select("s.*,v.venue_name,c.club_name");
		$query->from("`#__toes_show` as s");
		$query->join("INNER","`#__toes_venue` as v ON  s.show_venue = v.venue_id"); 
		$query->join("INNER","`#__toes_club_organizes_show` as cs ON  s.show_id = cs.show "); 
		$query->join("INNER","`#__toes_club` as c ON  c.club_id = cs.club"); 
		$query->where("s.show_uses_toes = 1");
		$query->where("s.show_start_date >=".$db->Quote($start_date)); 
		$query->where("s.eo_notified_to_invoice_this_show = 0");
		$query->order("s.show_start_date ASC");
		$rows = $db->setQuery($query)->loadObjectList();
		if(!count($rows))
		$app->redirect('index.php?option=com_toes&view=invoiceshows','No data');
		
		
		$timepart = time();
		if(!defined('DS'))
		define('DS',DIRECTORY_SEPARATOR);
		
		echo $csvpath = JPATH_ROOT.DS.'media'.DS.'com_toes'.DS.'invoice_csv'.DS;
		
		 
		if(!file_exists($csvpath)){
		jimport('joomla.filesystem.folder');
		JFolder::create($csvpath,0777);	
		//mkdir($csvpath);
		
		 
		}
		$delimiter = ',';
		//create a file pointer
		$f = fopen($csvpath.$timepart.'.csv', 'w+');			
		// title			 
		fputcsv($f,array('Show ID','Start date','End date','Club','Venue'),$delimiter);	
		
 		 
		foreach($rows as $row){		 
		fputcsv($f,array($row->show_id,JHTML::_('date',$row->show_start_date,'d/m/Y'),JHTML::_('date',$row->show_end_date,'d/m/Y'),
		$row->club_name,$row->venue_name),$delimiter);			 
		}
		fclose($f);
		// prepare email and attach this csv and send
		
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);

		$mailer->setSender($sender);
		//$mailer->addRecipient('accounting@tica.org');
		$mailer->addRecipient('sandip.pilake@gmail.com');
		$mailer->addRecipient('erwin@e-ware.be');
		$mailer->addRecipient($account_email);
		
		$mailer->isHtml(true);
		$mailer->Encoding = 'base64';
		
		$body   = JText::_('COM_TOES_SEND_INVOICE_SHOWS_BODY');
		$mailer->setSubject(JText::_('COM_TOES_SEND_INVOICE_SHOWS_SUBJECT'));
		$mailer->setBody($body);
		// Optional file attached
		$mailer->addAttachment($csvpath.$timepart.'.csv');
		$send = $mailer->Send();
		
		 
		if ( $send !== true ) {
			
	        $app->redirect('index.php?option=com_toes&view=invoiceshows',JText::_('COM_TOES_ERROR_EMAIL_INVOICE_SHOW_SEND'));

			 
		} else {
			
			$db->setQuery("UPDATE `#__toes_show` SET `eo_notified_to_invoice_this_show` = 1 where  show_start_date >=".$db->Quote($start_date)." AND `eo_notified_to_invoice_this_show` =0 ")->execute();
			
			$app->redirect('index.php?option=com_toes&view=invoiceshows&Itemid='.$Itemid.'&timepart='.$timepart,JText::_('COM_TOES_EMAIL_INVOICE_SHOW_SEND_SUCCESS'));

			 
		}
		
		
		
         
        
	}

	 
}
