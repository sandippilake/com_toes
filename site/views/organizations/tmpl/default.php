<?php
// no direct access
defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework');
//JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior.chosen', 'select'); 
//var_dump($this->items);


/*
$row = JTable::getInstance('Organization','ToesTable');
$row->load(1);
var_dump($row);
*/

$hidden = false;

?>
<style>
 table { 
  width: 100%; 
  border-collapse: collapse; 
}
/* Zebra striping */
tr:nth-of-type(odd) { 
  background: #eee; 
}
th { 
  background: #333; 
  color: white; 
  font-weight: bold; 
}
td, th { 
  padding: 6px; 
  border: 1px solid #ccc; 
  text-align: left; 
}


@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

	/* Force table to not be like tables anymore */
	table, thead,tfoot, tbody, th, td, tr { 
		display: block; 
	}
	
	/* Hide table headers (but not display: none;, for accessibility) */
	thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	
	tr { border: 1px solid #ccc; }
	
	td { 
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #eee; 
		position: relative;
		padding-left: 48%!important; 
		margin-left:8px!important;
		font-size:60%;
	}
	
	td:before { 
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
	}
	
	/*
	Label the data
	*/
	td:nth-of-type(1):before { content: "<?php echo JText::_("COM_TOES_REGISTRATION_AFFILIATION")?>"; }
	td:nth-of-type(2):before { content: "<?php echo JText::_("COM_TOES_REGISTRATION_ORGANIZATION_NAME")?>"; }
	td:nth-of-type(3):before { content: "<?php echo JText::_("COM_TOES_REGISTRATION_ORGANIZATION_ABBREVIATION")?>"; } 
	
	
}

div.pagination span{margin:0px 5px!important;}
  
 
</style>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

<p style="margin-bottom:20px">
<a href="index.php?option=com_toes&view=organization&layout=edit">
	<i class="fa fa-plus" aria-hidden="true"></i>
	Add organization</a>
</p>	
	
<div class="form-horizontal">
<form action="<?php echo JRoute::_('index.php?option=com_toes&view=organizations'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this,'options' => array('filtersHidden' =>$hidden))); ?>
	 
	<table class="table" width="100%"  >
		<thead>
			<tr>
				 
 
				<th class='center'>
				<?php echo JHTML::_( 'grid.sort',  JText::_("COM_TOES_REGISTRATION_AFFILIATION") , 'recognized_registration_organization_affiliation', $this->sortDirection, $this->sortColumn); ?>	
					
				</th>
				<th class='center'>
				<?php echo JHTML::_( 'grid.sort',  JText::_("COM_TOES_REGISTRATION_ORGANIZATION_NAME") , 'recognized_registration_organization_name', $this->sortDirection, $this->sortColumn); ?>	
					
				</th>
				<th class='center'>
				<?php echo JHTML::_( 'grid.sort',  JText::_("COM_TOES_REGISTRATION_ORGANIZATION_ABBREVIATION") , 'recognized_registration_organization_abbreviation', $this->sortDirection, $this->sortColumn); ?>	
					
				</th>
				
				
				 		 
				 
			</tr>
		</thead>
		
		<tbody>
			<?php foreach ($this->items as $i => $item) :?>
				<tr class="row<?php echo $i % 2; ?>">
					 
					 
					<td class='center'>
							<?php echo $this->escape($item->recognized_registration_organization_affiliation); ?>
					</td> 
					 
					<td class='center'>
						<?php echo  $item->recognized_registration_organization_name; ?><br/>
						<a href="index.php?option=com_toes&view=organization&layout=edit&id=<?php echo $item->recognized_registration_organization_id?>">
						<i class="fa fa-edit" aria-hidden="true"></i>		
						</a>
						<a href="index.php?option=com_toes&task=organization.remove&id=<?php echo $item->recognized_registration_organization_id?>">
						<i class="fa fa-trash" aria-hidden="true"></i>	
						</a>
					</td> 
					<td class='center'>
						<?php echo $item->recognized_registration_organization_abbreviation;?>	 
					</td>
					 
				</tr>
			<?php endforeach; ?>
		</tbody>
		 
					
				 
	</table>
	
 <?php echo $this->pagination->getListFooter(); ?>
	
	 
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>    
</div>
<script>
	
	jQuery('button.hasTooltip').attr('title','');


</script>
