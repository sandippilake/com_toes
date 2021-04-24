<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
 
 
$Itemid = JFactory::getApplication()->input->getInt('Itemid');
$timepart = JFactory::getApplication()->input->getInt('timepart');

$invoicesent = (int) $this->state->get('filter.invoicesent');
 
?>


<?php if($timepart){?>
<p><?php echo JText::_('COM_TOES_HERE_IS_CSV_FILE_EMAILED_FOR_INVOICES')?></p>
<a href="<?php echo JURI::root()?>media/com_toes/invoice_csv/<?php echo $timepart?>.csv" target="_blank">
<i class='far fa-file-excel'></i>
</a>	
	<br/>
	<br/>
<?php } ?>
 
   <?php  if($invoicesent > 0){
	  }else if($invoicesent < 0){
	  }else{ ?>
		<form name="invoiceform" id="invoiceform" method="POST" action="index.php?option=com_toes&task=invoiceshows.sendinvoice&tmpl=component&$Itemid=<?php echo $Itemid?>">
		
	</form>
	
	<div style="clear:both"></div>  
		  
	<?php 	}
	   
	   
	   
	   ?>
	
 

 

<form action="<?php echo JRoute::_('index.php?option=com_toes&view=invoiceshows&Itemid='.$Itemid); ?>" 
method="post"       name="adminForm" id="adminForm">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this,'options' => array('filtersHidden' =>0)));
		?>
      
		
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
		
	
 		<div style="clear:both"></div>
 		<?php if($invoicesent == 0){?>
 		<input type="button" id="send_btn" value="<?php echo JText::_('COM_TOES_SEND_INVOICE')?>"	 
 		<div style="clear:both"></div>	 
		<?php } ?>
 	<table class="table table-striped">
		<thead>
			<tr>				 
 				<th>Show ID</th>
 				<th>Start date</th>
 				<th>End date</th>
 				<th>Club</th>
 				<th>Venue</th>
 				<th>Invoice sent</th> 				 
		</thead>
		<tfoot>
				<tr>
					<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>						 
					</td>
				</tr>
		</tfoot>
		<tbody>
			<?php 
			
				foreach($this->items as $i => $item): 
				 
			
			?>
			<tr class="row<?php echo $i % 2; ?>">				 
				<td><?php echo  $item->show_id?></td>
				<td><?php echo JHTML::_('date',$item->show_start_date,'d/m/Y');?></td>
				<td><?php echo JHTML::_('date',$item->show_end_date,'d/m/Y');?></td>
				<td><?php echo $item->club_name;?></td>
				<td><?php echo $item->venue_name;?></td>
				<td><?php if($item->eo_notified_to_invoice_this_show)echo 'Yes';else echo 'No';?></td> 
			</tr>
			<?php endforeach; ?>
			 
		</tbody>
	</table>
	
	    <?php endif; ?>
	
		 
	   <?php echo JHtml::_('form.token'); ?>
		 
</form> 

<script>
jQuery('#send_btn').on('click',function(){
	
	jQuery('form#invoiceform').submit();
	
});
</script>

 
 
