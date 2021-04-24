<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

 
$app = JFactory::getApplication();
$show_id = $app->input->getInt('id');
 
$data = $this->item;
$url = JURI::getInstance();
$show_link = $url->getScheme().'://'.$url->getHost().JRoute::_('index.php?option=com_toes&view=shows',false).'#show'.$this->item->show_id;
 
?>
<style>
span.ui-icon-circle-triangle-w{padding:0px 20px!important;}
#ui-datepicker-div{background:#c9c9c9}
</style>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<h3>Copy Show</h3>
<div class="title"><?php echo JText::_('COM_TOES_SHOW_DETAILS'); ?></div>
    <div class="clr"></div>
    <div class ="block">
        <div class="date">
            <?php
            $start_date = date('d', strtotime($data->show_start_date));
            $start_date_month = date('M', strtotime($data->show_start_date));
            $start_date_year = date('Y', strtotime($data->show_start_date));

            $end_date = date('d', strtotime($data->show_end_date));
            $end_date_month = date('M', strtotime($data->show_end_date));
            $end_date_year = date('Y', strtotime($data->show_end_date));

            echo $start_date_month.' '.$start_date;

            if ($end_date_year != $start_date_year){
                echo ' '.$start_date_year;
            }

            if ($end_date_month != $start_date_month){
                if(date('t', strtotime($data->show_start_date)) != $start_date)
                    echo ' - '.date('t', strtotime($data->show_start_date));
                if($end_date == '01')
                    echo ', ' .$end_date_month.' '.$end_date;
                else
                    echo ', ' .$end_date_month.' 01 - '.$end_date;
            } else {
                if($start_date != $end_date)
                    echo ' - ' . $start_date_month.' '.$end_date;
            }

            echo ' '.$end_date_year;

            ?>
        </div>
        
        <div class="club-name" >
            <?php echo $data->club_name; ?>
        </div>
        <div class="clr"></div>

        <div>
            <label>
                <?php echo JText::_('COM_TOES_SHOW_FORMAT'); ?>
            </label>

            <span>
                <?php 
                    $is_continuous = false;
					$is_alernative = false;
                    switch ($data->show_format)
                    {
                        case 1:
                            echo JText::_('Back to Back');
                            break;
                        case 2:
                            echo JText::_('Alternative');
							$is_alernative = true;
                            break;
                        case 3:
                            echo JText::_('Continuous');
                            $is_continuous = true;
                            break;
                }
                 ?>
            </span>
        </div>

        <div>
            <label>
                <?php echo JText::_('COM_TOES_SHOW_SHOWHALL') . ':'; ?>
            </label>
            <span>
                <?php
				if ($data->venue_name)
					echo $data->venue_name.'<br/>';
                if ($data->address_line_1)
                    echo $data->address_line_1 . '<br/>';
                if ($data->address_line_2)
                    echo $data->address_line_2 . '<br/>';
                if ($data->address_line_3)
                    echo $data->address_line_3 . '<br/>';
                if ($data->address_city)
                    echo $data->address_city . ' , ';
                if($data->address_state)
                    echo $data->address_state . ' , ';
                if($data->address_zip_code)
                    echo $data->address_zip_code;
                echo '<br/>';
                echo $data->address_country . '<br/>';
                ?>
            </span>
        </div>
        </div>
<form method="post" id="adminForm" name="adminForm"

action="<?php echo JURI::root()?>index.php?option=com_toes&task=show.copy&show_id=<?php echo $show_id?>&tmpl=component">

<?php /*
 <p>Start Date of New Show: <input name="start_date" id="start_date" type="text" id="datepicker"></p>
 */ ?> 
 <p>Start Date of New Show: 
 <?php echo JHTML::_('calendar','',"start_date","start_date", '%Y-%m-%d',array('class'=>'inputbox dt', 'size'=>'25', 'maxlength'=>'19'));?>
 </p>
 <p id="detailed_start_date"></p>


<p>
<input type="button" class="btn" value="Copy" onclick="copytheshow()" >
<input type="button" class="btn" value="Cancel" onclick="cancelcopy()" >
</p>


<input type="hidden"  value="com_toes" name="option" >
<input type="hidden"  value="show.copy" name="task" >
<input type="hidden"  value="<?php echo $show_id?>" name="show_id" id="show_id" >
 

</form>
 <script>
	 /*
  jQuery( function() {
    jQuery( "#start_date" ).datepicker({ dateFormat: 'yy-mm-dd',minDate: '+1m' });
  } );
  */
   
  function copytheshow(){
	               
	  console.log(jQuery('input#start_date').val());
	  if(!jQuery('input#start_date').val()){
		alert('Please select start date');  
		 return;
	  }
	var date1 = new Date(jQuery('input#start_date').val()); 
	var date2 = new Date(); 
	  
	// To calculate the time difference of two dates 
	var Difference_In_Time = date1.getTime() - date2.getTime(); 
	  
	// To calculate the no. of days between two dates 
	var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
	if(Difference_In_Days <= 0){
	alert('Start date of the show is less than or equal to the current date. This is not allowed.');	
	return;	
	}
	
	if(Difference_In_Days <= 30){
		
	alert('Start date of the show is less than 30 days from the current date. This is not allowed. See Standing Rule 202.4.6.1.');	
	return;	
	}
	var notification_messsage = "<h3>Show Conflict</h3><p> Please note that there is another show scheduled on at least one of the dates of the show with a distance less than 500 miles (800 km) from your current show location. This is possible, but requires approval from the other club as well as the respective Regional Director(s).</p><p>How would you like to proceed?</p>";
     
	 
	jQuery.ajax({
		method: "POST",
		url: "index.php?option=com_toes&task=show.checkradiusresultshowcopy&tmpl=component",	
		data: {start_date:jQuery('input#start_date').val(),copy_show_id: jQuery('input#show_id').val()},
		success:function(data){
				console.log(data);
				 
				if(data=='2')
				{		
					var warningMsg = '';
					warningMsg = notification_messsage+ "<br/><br/><p>Option A:&nbsp;<?php echo JText::_('COM_TOES_SHOW_CHANGE_SHOW_DATE_AND_LOCATION'); ?></p> <P>Option B:&nbsp;<?php echo JText::_('COM_TOES_SHOW_REQUEST_APPROVAL_FROM_OTHER_CLUB_AND_REGINAL_DIRECTOR'); ?></P>";
					//console.log(warningMsg);
					if(warningMsg)
					{
						new jBox('Confirm',{
							content: warningMsg,
							width: '400px',
							cancelButton : 'OPTION-A',
							confirmButton: 'OPTION-B',
							cancel: function() {
							//window.location = '<?php echo $show_link?>';
								
							},
							confirm: function() {
								
								jQuery('form#adminForm').submit();
						
						}
						
						}).open();
					}
				}
				else if(data == '1')
				{					 
					jQuery('form#adminForm').submit();
				}else if(data == '-1')
				{	
					alert('Not Authorized');
					//jQuery('form#adminForm').submit();
				}
				
	}	
	});
	//jQuery('form#adminForm').submit();
	  
  }
  
  function copytheshow_bak(){
	  if(!jQuery('input#start_date').val()){
		alert('Please select start date');  
		 return;
	  }
	var date1 = new Date(jQuery('input#start_date').val()); 
	var date2 = new Date(); 
	  
	// To calculate the time difference of two dates 
	var Difference_In_Time = date1.getTime() - date2.getTime(); 
	  
	// To calculate the no. of days between two dates 
	var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
	
	if(Difference_In_Days <= 30){
		
	alert('Start date of the show is less than 30 days from the current date. This is not allowed. See Standing Rule 202.4.6.1.');	
	return;	
	} 
  
	/* 
	var selecteddate  = datepicker.parseDate(jQuery('#start_date').val());
	var currentdate = new Date(); 
	var diff_in_days = DateDiff.inDays(currentdate,selecteddate);
	if(diff_in_days <= 30){
	alert('Start date of the show is less than 30 days from the current date. This is not allowed. See Standing Rule 202.4.6.1.');
	jQuery('#start_date').val('');
	return;	
	 
	}
	*/
	  
	  jQuery('form#copyform').submit();
	  
  }
  function cancelcopy(){
	//window.parent.SqueezeBox.close();  
	window.location = '<?php echo $show_link?>';
	  
  }
  </script>


 
