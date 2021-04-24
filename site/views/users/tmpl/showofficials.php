<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$user = JFactory::getUser();

$this->order = $app->input->get('show_order','club_name');
$this->order_dir = $app->input->get('show_order_dir','asc');

?>

<script type="text/javascript">
    var myWidth;
    var myHeight;

    if( typeof( window.innerWidth ) == 'number' ) { 
        //Non-IE 
        myWidth = window.innerWidth;
        myHeight = window.innerHeight; 
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) { 
        //IE 6+ in 'standards compliant mode' 
        myWidth = document.documentElement.clientWidth; 
        myHeight = document.documentElement.clientHeight; 
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) { 
        //IE 4 compatible 
        myWidth = document.body.clientWidth; 
        myHeight = document.body.clientHeight; 
    }            

	function removeofficial(user_id,roll_id,official_id,region_id,official,question)
    {
 		new jBox('Confirm',{
	        content: question,
	        width: '400px',
	        cancelButton : NO_BUTTON,
	        confirmButton: YES_BUTTON,
	        confirm: function() {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=users.delete&id='+user_id+'&official='+official+'&roll_id='+roll_id+'&official_id='+official_id+'&region='+region_id+'&tmpl=component',
                    type: 'get'
                }).done( function(responseText){
					jQuery('#loader').hide();
                    if(responseText == 1)
                        location.reload(true);
                }).fail(function(){
					jQuery('#loader').hide();
					jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
				});
				jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
				jQuery('#loader').css('padding-top', (myHeight/2)+'px');
				jQuery('#loader').show();
            }
        }).open();   
    }
	
    function changeshow()
    {
        var formData = jQuery("#adminForm").serialize(); 
        jQuery.ajax({
            url: 'index.php?option=com_toes&view=users&layout=showofficials&tmpl=component&'+formData,
            type: 'get'
        }).done( function(responseText){
			jQuery('#loader').hide();
			var result = jQuery(responseText).find('#flt_show_of');
			jQuery('#flt_show_of').html(result);
        }).fail(function(){
			jQuery('#loader').hide();
			jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
		});
		jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#loader').show();
    }

    function sort(col)
    {
		var dir = jQuery("#show_order_dir").val();
		if(dir == 'asc') {
			dir = 'desc';
		} else {
			dir = 'asc';
		}
		
		jQuery("#show_order").val(col);
		jQuery("#show_order_dir").val(dir);
		
    	var formData = jQuery("#adminForm").serialize(); 
        jQuery.ajax({
            url: 'index.php?option=com_toes&view=users&layout=showofficials&tmpl=component&'+formData,
            type: 'get'
        }).done( function(responseText){
			jQuery('#loader').hide();
			var result = jQuery(responseText).find('#flt_show_of');
            jQuery('#flt_show_of').html(result);
		
			jQuery('.sort_icon').removeClass('fa-sort-asc');
			jQuery('.sort_icon').removeClass('fa-sort-desc');

			jQuery('#'+col+'_sort_icon').addClass('fa-sort-'+dir);
        }).fail(function(){
			jQuery('#loader').hide();
			jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
		});
		
		jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#loader').show();
    }
</script>
<div id="toes">
	<div id="loader" class="loader">
		<span id="loader-container">
			<img id="loader-img" src="media/com_toes/images/loading.gif" alt="" />
			<?php echo JText::_('COM_TOES_LOADING'); ?>
		</span>
	</div>

	<div style="text-align: center;">
		<?php if ($user->authorise('toes.manage_org_officials','com_toes')): ?>
		<span class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=users'); ?>">
				<?php echo JText::_('COM_TOES_ORGANIZATION_OFFICIALS'); ?>
			</a>
		</span>
		<?php endif; ?>
		<?php if ($user->authorise('toes.manage_club_officials','com_toes')): ?>
		<span class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=users&layout=clubofficials'); ?>">
				<?php echo JText::_('COM_TOES_CLUB_OFFICIALS'); ?>
			</a>
		</span>
		<?php endif; ?>
		<?php if ($user->authorise('toes.manage_show_officials','com_toes')): ?>
		<span class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=users&layout=showofficials'); ?>">
				<?php echo JText::_('COM_TOES_SHOW_OFFICIALS'); ?>
			</a>
		</span>
		<?php endif; ?>
	</div>
	<div class="clr"></div>
	<br/>	
	
	<?php if ($user->authorise('toes.manage_show_officials','com_toes')): ?>
		<div class="pull-right"> 
			<input class="button button-4" type="button" value="<?php echo JText::_('COM_TOES_ADD'); ?>" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=user&official=show'); ?>'"> 
		</div>
		<div id="show_officials_div" class="pull-left">
			<form action="<?php echo JRoute::_('index.php?option=com_toes&view=users'); ?>" method="post" name="adminForm" id="adminForm">	
				<div class ="outerdiv">
					<div class ="fistouter">
						<div class="block-title" ><?php echo JText::_('COM_TOES_SHOW_OFFICIALS'); ?></div>
						<div class="clr"></div>
					</div>

					<div class ="seconouter">
						<div class="filter-block">
							<div class="filter-field" >
								<label for="show_club_filter" class="lbl">
									<?php echo JText::_('COM_TOES_FILTER'); ?> :
								</label>

								<input type="text" id="show_club_filter_name" name="show_club_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_CLUB'); ?>"  >
								<input type="hidden" id="show_club_filter" name="show_club_filter" value="" >

								<input type="text" id="user_filter_name" name="user_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_SHOW_USER'); ?>"  >
								<input type="hidden" id="user_filter" name="user_filter" value="" >

								<input type="text" id="show_location_filter_name" name="show_location_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_LOCATION'); ?>" >
								<input type="hidden" id="show_location_filter" name="show_location_filter" value="" >

								<select class="filter-selectlist" name="show_roll_filter" id="show_roll_filter" onchange="changeshow();" >
									<option value=""><?php echo JText::_('COM_TOES_SELECT_ROLL'); ?></option>
									<?php
									foreach ($this->showrolllist as $srl) {
										echo '<option value="' . $srl->value . '">' . $srl->text . '</option>';
									}
									?>
								</select>
								<select class="filter-selectlist" name="show_date_status_filter" id="show_date_status_filter" onchange="changeshow();" >
									<option value=""><?php echo JText::_('COM_TOES_ALL_SHOWS'); ?></option>
									<option value="future"><?php echo JText::_('COM_TOES_FUTURE_SHOWS'); ?></option>
									<option value="past"><?php echo JText::_('COM_TOES_PAST_SHOWS'); ?></option>
								</select>
								<select class="filter-selectlist" name="show_status_filter" id="show_status_filter" onchange="changeshow();" >
									<option value=""><?php echo JText::_('COM_TOES_SELECT_STATUS'); ?></option>
									<?php
									foreach ($this->show_statuslist as $ssl) {
										echo '<option value="' . $ssl->value . '">' . $ssl->text . '</option>';
									}
									?>
								</select>

								<input type="button" name="reset_show_filters" value="<?php echo JText::_('COM_TOES_RESET_FILTER');?>" />
							</div>

							<div class="clr"></div>
						</div>
						<div class="clr"><br/></div>

						<div class="seconouter-row hidden-phone" >
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
								<a href="javascript:void(0);" onclick="sort('club_name');"><?php echo JText::_('COM_TOES_CLUB'); ?></a>
								<i class="sort_icon fa fa-sort-asc" id="club_name_sort_icon"></i>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8"><?php echo JText::_('COM_TOES_LOCATION'); ?> </div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
								<a href="javascript:void(0);" onclick="sort('show_start_date');"><?php echo JText::_('COM_TOES_DATES'); ?></a>
								<i class="sort_icon fa" id="show_start_date_sort_icon"></i>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
								<a href="javascript:void(0);" onclick="sort('roll');"><?php echo JText::_('COM_TOES_ROLE'); ?></a>
								<i class="sort_icon fa" id="roll_sort_icon"></i>
							</div>
							<div class="col-lg-1 col-md-1 col-sm-1 col-xs-8">
								<a href="javascript:void(0);" onclick="sort('lastname');"><?php echo JText::_('COM_TOES_NAME'); ?></a>
								<i class="sort_icon fa" id="lastname_sort_icon"></i>
							</div>
							<div class="col-lg-1 col-md-1 col-sm-1 col-xs-8">
								<a href="javascript:void(0);" onclick="sort('firstname');"><?php echo JText::_('COM_TOES_FIRST_NAME'); ?></a>
								<i class="sort_icon fa" id="firstname_sort_icon"></i>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8"></div>
						</div>
						<div class="clr"></div>
						<div name="flt_show_of" id="flt_show_of">

							<?php
							foreach ($this->ShowOfficial as $data) {
								?>
								<div class="seconouter-row-col">

									<div class="visible-phone clr"></div>
									<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_CLUB'); ?></div>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->club; ?></div>
									<div class="visible-phone clr"></div>
									<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_LOCATION'); ?></div>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" >
										<?php
										echo $data->venue_name;

										if ($data->address_city) {
											echo ', ' . $data->address_city;
										}
										if ($data->address_state) {
											echo ', ' . $data->address_state;
										}
										if ($data->address_country) {
											echo ', ' . $data->address_country;
										}
										?>
									</div>
									<div class="visible-phone clr"></div>
									<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_DATES'); ?></div>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" >
										<?php
										$start_date = date('d', strtotime($data->show_start_date));
										$start_date_month = date('M', strtotime($data->show_start_date));
										$start_date_year = date('Y', strtotime($data->show_start_date));

										$end_date = date('d', strtotime($data->show_end_date));
										$end_date_month = date('M', strtotime($data->show_end_date));
										$end_date_year = date('Y', strtotime($data->show_end_date));

										echo $start_date_month . ' ' . $start_date;

										if ($end_date_year != $start_date_year) {
											echo ' ' . $start_date_year;
										}

										if ($end_date_month != $start_date_month) {
											if (date('t', strtotime($data->show_start_date)) != $start_date)
												echo ' - ' . date('t', strtotime($data->show_start_date));
											if ($end_date == '01')
												echo ', ' . $end_date_month . ' ' . $end_date;
											else
												echo ', ' . $end_date_month . ' 01 - ' . $end_date;
										} else {
											echo ' - ' . $start_date_month . ' ' . $end_date;
										}

										echo ', ' . $end_date_year;


										//echo $data->show_start_date.' - '.$data->show_end_date; 
										?>
									</div>
									<div class="visible-phone clr"></div>
									<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_ROLE'); ?></div>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->roll; ?></div>
									<div class="visible-phone clr"></div>
									<?php /* */ ?>
									<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_NAME'); ?></div>
									<div class="col-lg-1 col-md-1 col-sm-1 col-xs-8" ><?php echo $data->lastname; ?></div>
									<div class="visible-phone clr"></div>
									<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_FIRST_NAME'); ?></div>
									<div class="col-lg-1 col-md-1 col-sm-1 col-xs-8" ><?php echo $data->firstname; ?></div>
									<div class="visible-phone clr"></div>
									<?php /**/ ?> 
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" >
										<span class="hasTip" title="<?php echo JText::_('COM_TOES_DELETE'); ?>">
											<a href="javascript::void();" onclick="removeofficial('<?php echo $data->user_id; ?>','<?php echo $data->roll_id; ?>','<?php echo $data->official_id; ?>','0','show','<?php echo JText::sprintf('REMOVE_OFFICIAL_QUESTION', $data->roll, $data->firstname, $data->lastname, $data->uname) ?>');" class="cancel-show" >
												<i class="fa fa-trash large"></i>
											</a>
										</span>
										<span class="hasTip" title="<?php echo JText::_('COM_TOES_EDIT'); ?>">
											<a href="<?php echo JRoute::_('index.php?option=com_toes&view=user&official=show&layout=show&user_id='.$data->user_id.'&roll='.$data->roll_id.'&show_id='.$data->show_id); ?>" class="edit-show" >
												<i class="fa fa-edit large"></i>
											</a>
										</span>
									</div>
									<div class="clr"></div>
								</div>
								<div class="clr"></div>
								<?php
							}
							?>

							<div class="pagination col-lg-12">               
								<?php echo $this->pagination->getPagesLinks();?>
							</div>
							<input type="hidden" name="limitstart" value="<?php echo $this->pagination->limitstart; ?>">
							<input type="hidden" id="show_order" name="show_order" value="<?php echo $this->order; ?>" />
							<input type="hidden" id="show_order_dir" name="show_order_dir" value="<?php echo $this->order_dir; ?>" />
						</div>
						<div class="clr"></div>
					</div>
				</div>
			</form>
		</div>
		<div class="clr"></div>
	<?php endif; ?>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		
		//option A
        jQuery("#adminForm").on('submit', function(e){
            e.preventDefault();
            var formData = jQuery(this).serialize(); 
	        jQuery.ajax({
	            url: 'index.php?option=com_toes&view=users&layout=showofficials&tmpl=component&'+formData,
	            type: 'get'
	        }).done( function(responseText){
				var result = jQuery(responseText).find('#flt_show_of');
				jQuery('#flt_show_of').html(result);
	        });            
        });
        
        jQuery('input[name="reset_show_filters"]').on('click', function(e){
	        jQuery('#show_club_filter_name').val('');
	        jQuery('#show_club_filter').val('');
	        jQuery('#show_location_filter_name').val('');
	        jQuery('#show_location_filter').val('');
	        jQuery('#show_roll_filter').val('');
	        jQuery('#show_status_filter').val('');
	        jQuery('#show_date_status_filter').val('');
        	
            var formData = jQuery(this).serialize(); 
	        jQuery.ajax({
	            url: 'index.php?option=com_toes&view=users&layout=showofficials&tmpl=component&'+formData,
	            type: 'get'
	        }).done( function(responseText){
				jQuery('#loader').hide();
				var result = jQuery(responseText).find('#flt_show_of');
				jQuery('#flt_show_of').html(result);
	        }).fail(function(){
				jQuery('#loader').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});            
			jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
			jQuery('#loader').css('padding-top', (myHeight/2)+'px');
			jQuery('#loader').show();
        });

	    //########### club list for show officials ################
	 	jQuery( "#show_club_filter_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=users.getclublist&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#show_club_filter" ).val(ui.item.key);
		  	jQuery( "#show_club_filter_name" ).val(ui.item.value);
		  	changeshow();
		  }
		});    

	    //########### club list for show officials ################
	 	jQuery( "#user_filter_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=users.getshowuserlist&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#user_filter" ).val(ui.item.key);
		  	jQuery( "#user_filter_name" ).val(ui.item.value);
		  	changeshow();
		  }
		});    

	    //########### location list for show officials ################
		jQuery( "#show_location_filter_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=users.getlocationlist&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#show_location_filter" ).val(ui.item.key);
		  	jQuery( "#show_location_filter_name" ).val(ui.item.value);
		  	changeshow();
		  }
		});    
	});
</script>
