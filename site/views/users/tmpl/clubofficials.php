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

$document = JFactory::getDocument();
$document->addScript('components/com_toes/assets/pagination/jquery.twbsPagination.js',"text/javascript", true);

$this->order = $app->input->get('club_order','club_name');
$this->order_dir = $app->input->get('club_order_dir','asc');
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

    function changeclub()
    {
    	var formData = jQuery("#club_officials_form").serialize(); 
        jQuery.ajax({
            url: 'index.php?option=com_toes&view=users&layout=clubofficials&tmpl=component&'+formData,
            type: 'get'
        }).done( function(responseText){
			jQuery('#loader').hide();
			var result = jQuery(responseText).find('#flt_club_of');
            jQuery('#flt_club_of').html(result);
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
		var dir = jQuery("#club_order_dir").val();
		if(dir == 'asc') {
			dir = 'desc';
		} else {
			dir = 'asc';
		}
		
		jQuery("#club_order").val(col);
		jQuery("#club_order_dir").val(dir);
		
    	var formData = jQuery("#club_officials_form").serialize(); 
        jQuery.ajax({
            url: 'index.php?option=com_toes&view=users&layout=clubofficials&tmpl=component&'+formData,
            type: 'get'
        }).done( function(responseText){
			jQuery('#loader').hide();
			var result = jQuery(responseText).find('#flt_club_of');
            jQuery('#flt_club_of').html(result);
		
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
	
	<div class="pull-right"> 
		<input class="button button-4" type="button" value="<?php echo JText::_('COM_TOES_ADD'); ?>" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=user&official=club'); ?>'"> 
	</div>
	<div class ="outerdiv pull-left">
		<form action="<?php echo JRoute::_('index.php?option=com_toes&view=users'); ?>" method="post" name="club_officials_form" id="club_officials_form">	
			<div class ="fistouter">
				<div class="block-title" ><?php echo JText::_('COM_TOES_CLUB_OFFICIALS'); ?> </div>
				<div class="clr"></div>
			</div>

			<div class ="seconouter">
				<div class="filter-block">
					<div class="filter-field" >
						<label for="club_filter" class="lbl">
							<?php echo JText::_('COM_TOES_FILTER'); ?> :
						</label>
						<input type="text" id="club_filter_name" name="club_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_CLUB'); ?>" >
						<input type="hidden" id="club_filter" name="club_filter" value="" >

						<input type="text" id="club_filter_user_name" name="club_filter_user_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_CLUB_USER'); ?>" >
						<input type="hidden" id="club_user_filter" name="club_user_filter" value="" >
						
						<input type="button" name="reset_club_filters" value="<?php echo JText::_('COM_TOES_RESET_FILTER');?>" />
					</div>

					<div class="clr"></div>
				</div>
				<div class="clr"><br/></div>

				<div class="seconouter-row hidden-phone" >
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
						<a href="javascript:void(0);" onclick="sort('club_name');"><?php echo JText::_('COM_TOES_CLUB'); ?></a>
						<i class="sort_icon fa fa-sort-asc" id="club_name_sort_icon"></i>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
						<a href="javascript:void(0);" onclick="sort('roll');"><?php echo JText::_('COM_TOES_ROLE'); ?></a>
						<i class="sort_icon fa fa-sort-asc" id="roll_sort_icon"></i>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
						<a href="javascript:void(0);" onclick="sort('lastname');"><?php echo JText::_('COM_TOES_NAME'); ?></a>
						<i class="sort_icon fa" id="lastname_sort_icon"></i>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
						<a href="javascript:void(0);" onclick="sort('firstname');"><?php echo JText::_('COM_TOES_FIRST_NAME'); ?></a>
						<i class="sort_icon fa" id="firstname_sort_icon"></i>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
						<?php echo JText::_('COM_TOES_STATE'); ?></div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8"></div>
					<div class="clr"></div>
				</div>

				<div name="flt_club_of" id="flt_club_of">
					<?php
					if(count($this->ClubOfficial) > 20) {
						echo '<div class="club-page-content" id="club-page-1">';
					}
					$page = 2;
					$i = 1;
					foreach ($this->ClubOfficial as $data) {
						//var_dump($data);
						if($i%20 == 0) {
							echo '</div><div class="club-page-content" id="club-page-'.$page.'" style="display: none;">';
							$page++;
						}
						?>

						<div class="seconouter-row-col">
							<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_CLUB'); ?></div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->club; ?></div>
							<div class="visible-phone clr"></div>
							<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_ROLE'); ?></div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->roll; ?></div>
							<div class="visible-phone clr"></div>
							<?php /**/?>
							<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_NAME'); ?></div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->lastname; ?></div>
							<div class="visible-phone clr"></div>
							<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_FIRST_NAME'); ?></div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->firstname; ?></div>
							<div class="visible-phone clr"></div>
							<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_STATE'); ?></div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo ($data->cb_state?$data->cb_state.'-' :'').$data->cb_country; ?></div>
							<?php /**/ ?> 
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" >
								<span class="hasTip" title="<?php echo JText::_('COM_TOES_DELETE'); ?>">
									<a href="javascript::void();" onclick="removeofficial('<?php echo $data->user_id; ?>','<?php echo $data->roll_id; ?>','<?php echo $data->official_id; ?>','0','club','<?php echo JText::sprintf('REMOVE_OFFICIAL_QUESTION', $data->roll, $data->firstname, $data->lastname, $data->uname) ?>');" class="cancel-show" >
										<i class="fa fa-trash large"></i>
									</a>
								</span>
								<span class="hasTip" title="<?php echo JText::_('COM_TOES_EDIT'); ?>">
									<a href="<?php echo JRoute::_('index.php?option=com_toes&view=user&official=club&layout=club&user_id='.$data->user_id.'&roll='.$data->roll_id.'&club_id='.$data->official_id); ?>" class="edit-show" >
										<i class="fa fa-edit large"></i>
									</a>
								</span>
							</div>
							<div class="clr"></div>
						</div>
						<?php
						$i++;
					}

					if(count($this->ClubOfficial) > 20) {
						echo '</div>';
					}

					?>
					<div class="pagination col-lg-12" id="club-pagination"></div>
					<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery('#club-pagination').twbsPagination({
								totalPages: <?php echo $page-1 ;?>,
								paginationClass: '',
								first: 'Start',
								prev: 'Prev',
								next: 'Next',
								last: 'End',
								visiblePages: 10, 
								nextClass: 'pagination-next',
								prevClass: 'pagination-prev',
								lastClass: 'pagination-end',
								firstClass: 'pagination-start',
								pageClass: 'pagenav',
								onPageClick: function (event, page) {
									jQuery('.club-page-content').hide();
									jQuery('#club-page-'+page).show();
								}
							});
						});
					</script>	  
					<div class="clr"></div>
					<input type="hidden" id="club_order" name="club_order" value="<?php echo $this->order; ?>" />
					<input type="hidden" id="club_order_dir" name="club_order_dir" value="<?php echo $this->order_dir; ?>" />
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		
      	jQuery('input[name="reset_club_filters"]').on('click', function(e){
      		
	        jQuery('#club_filter_name').val('');
	        jQuery('#club_filter').val('');
        	
            var formData = jQuery('#club_officials_form').serialize(); 
	        jQuery.ajax({
	            url: 'index.php?option=com_toes&view=users&layout=clubofficials&tmpl=component&'+formData,
	            type: 'get'
	        }).done( function(responseText){
				jQuery('#loader').hide();
				var result = jQuery(responseText).find('#flt_club_of');
				jQuery('#flt_club_of').html(result);
	        }).fail(function(){
				jQuery('#loader').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});            
			jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
			jQuery('#loader').css('padding-top', (myHeight/2)+'px');
			jQuery('#loader').show();
        });
		
	    //########### club list for club officials ################
		jQuery( "#club_filter_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=users.getclublist&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#club_filter" ).val(ui.item.key);
		  	jQuery( "#club_filter_name" ).val(ui.item.value);
		  	changeclub();
		  }
		});    

		//########### club list for club officials ################
		jQuery( "#club_filter_user_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=users.getclubuserslist&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#club_user_filter" ).val(ui.item.key);
		  	jQuery( "#club_user_filter_name" ).val(ui.item.value);
		  	changeclub();
		  }
		});    
	});
</script>
