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
JHtml::_('behavior.modal');
JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior2.select2','select');
JHtml::_('behavior.formvalidation');
$db = JFactory::getDBO();
$params = JComponentHelper::getParams('com_toes');
$user   = JFactory::getUser();
$isAdmin = TOESHelper::isAdmin();
$groups = JAccess::getGroupsByUser($user->id);
$eouser = false; 
if($user->id){
$tica_eo_access_user_group_ids = $params->get('tica_eo_access_user_group_ids');
 
$common = array_intersect($tica_eo_access_user_group_ids , $groups);
if(count($common)>0){
$eouser = true;
}
}

if( $params->get('sync_db') && $user->authorise('toes.access_sync_options','com_toes'))
	$allowed_sync = 1;
else 
	$allowed_sync = 0;
	

$app = JFactory::getApplication();
$club_filter = $app->input->getInt('club_filter');
$state_filter = $app->input->getVar('state_filter');
$country_filter = $app->input->getVar('country_filter');
$city_filter = $app->input->getVar('city_filter','');
$region_filter = $app->input->getInt('region_filter');

$filter_applied = ($this->state->get('filter.club') || $this->state->get('filter.country') || $this->state->get('filter.state')
         || $this->state->get('filter.city') || $this->state->get('filter.entries') || $this->state->get('filter.show_date_status_filter') );

?>
<style>
select.filter{width:200px!important}
</style>
<!--
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
-->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="<?php echo JURI::root()?>components/com_toes/assets/css/jquery.fileuploader.min.css" media="all" rel="stylesheet">
<script src="<?php echo JURI::root()?>components/com_toes/assets/js/jquery.fileuploader.min.js" type="text/javascript"></script>
<link href="<?php echo JURI::root()?>components/com_toes/assets/css/jquery.fileuploader-theme-thumbnails.css" media="all" rel="stylesheet">

<div id="toes">
<div id="loader" class="loader">
    <span id="loader-container">
        <img id="loader-img" src="media/com_toes/images/loading.gif" alt="" />
        <?php echo JText::_('COM_TOES_LOADING'); ?>
    </span>
    <div id="progress-box">
        <span id="progress-bar">&nbsp;</span>
        <br/>
        <span id="progress-count">
            <label id="progress-count-processed">?</label> / <label id="progress-count-total">?</label>
        </span>
    </div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_toes&view=shows'); ?>" method="post" name="adminForm" id="show-list-form">
    
	<div class="action-buttons" >
		<?php if($user->authorise('toes.add_show','com_toes')) :?>
			<input class="add button button-4" type="button" name="add" value="<?php echo JText::_('COM_TOES_ADD_SHOW'); ?>" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=show&layout=edit');?>'"/>
		<?php endif; ?>
		<?php if($allowed_sync) : ?>
			<input class="add button button-4" type="button" name="sync" value="<?php echo JText::_('COM_TOES_SYNC_COMMON_DATA'); ?>" onclick="confirm_sync();"/>
		<?php endif; ?>	
	</div>
    
    <div class="title">
        <?php echo JText::_('COM_TOES_FILTER'); ?>
    </div>
    
    <span id="hide-doc-span" style="float:left;<?php echo ($filter_applied)?'':'display: none;'?>" class="hasTip" title="<?php echo JText::_('COM_TOES_HIDE_FILTER'); ?>">
        <a href ="javascript:void(0);" onclick="toggle_filter('close');" >
            <i class="fa fa-minus-circle"></i> 
        </a>
    </span>
    <span id="open-doc-span" style="float:left;<?php echo ($filter_applied)?'display: none;':''?>" class="hasTip" title="<?php echo JText::_('COM_TOES_OPEN_FILTER'); ?>">
        <a href ="javascript:void(0);" onclick="toggle_filter('open');" >
            <i class="fa fa-plus-circle"></i> 
        </a>
    </span>            
    <div class="clr"></div>

    <div class="filter-block" style="padding:0;<?php echo ($filter_applied)?'':'display: none;'?>">
        <?php /* <label class="lbl" style="float:left;">
          <?php echo JText::_('COM_TOES_FILTER'); ?> :
          </label> */ ?>
        <div class="filter-field" >
			<?php 
			$query = "SELECT `club_id` AS `key`, `club_name` AS `value`  FROM #__toes_club  ORDER BY club_name";
			$db->setQuery($query);
			$clubs = $db->loadObjectList();
			
			?>
			<?php if(count($clubs)>0){?>
			<select name="club_filter" id="club_filter" class="filter-selectlist" onchange="this.form.submit();">
			<option value="">Select club</option>
			<?php foreach($clubs as $c){
			$sel = ($club_filter == $c->key)?'SELECTED':'';
				
			?>
			<option value="<?php echo $c->key;?>" <?php echo $sel;?> ><?php echo $c->value;?></option>	
			<?php } ?>
			</select>
			<?php } ?>
			<?php /*
            <input type="text" id="club_filter_name" name="club_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_CLUB'); ?>" value="<?php echo @$this->state->get('filter.club_name');?>">
            <input type="hidden" id="club_filter" name="club_filter" value="<?php echo @$this->state->get('filter.club');?>" >
            */ ?> 
        </div>
        <div class="filter-field" >
			<?php 
			 
			$query = "SELECT distinct(address_country) FROM #__toes_address where TRIM(address_country) <> ''  ORDER BY address_country";
			$db->setQuery($query);
			$countries = $db->loadColumn();
			 
			?>
			<?php if(count($countries)>0){?>
			<select name="country_filter" id="country_filter" class="filter-selectlist" onchange="this.form.submit();">
			<option value="">Select country</option>
			<?php foreach($countries as $c){
			$sel = ($country_filter == $c)?'SELECTED':'';
				
			?>
			<option value="<?php echo $c;?>" <?php echo $sel;?> ><?php echo $c;?></option>	
			<?php } ?>
			</select>
			<?php } ?>
			<? /*
            <input type="text" id="country_filter_name" name="country_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_COUNTRY'); ?>" value="<?php echo @$this->state->get('filter.country_name');?>">
            <input type="hidden" id="country_filter" name="country_filter" value="<?php echo @$this->state->get('filter.country');?>" >
            */ ?> 
        </div>
        <div class="filter-field" >
			<?php 
			 
			$query = "SELECT distinct(address_state) FROM #__toes_address ";
			$state_where = [];
			$state_where[] = "TRIM(address_state) <> ''";
			$state_where[] = "TRIM(address_state) <> '0'";
			 
			if($country_filter) {
				$state_where[] = " address_country = ".$db->Quote($country_filter);
			}
		if(count($state_where) > 0){
		$query .= " where  ".implode(" AND ",$state_where);		
		}
		$query .= " ORDER BY address_state";
		$db->setQuery($query);
        $states = $db->loadColumn();
         
			?>
			<?php if(count($states)>0){?>
			<select name="state_filter" id="state_filter" class="filter-selectlist" onchange="this.form.submit();">
			<option value="">Select state</option>
			<?php foreach($states as $s){
			$sel = ($state_filter == $s)?'SELECTED':'';
				
			?>
			<option value="<?php echo $s;?>" <?php echo $sel;?> ><?php echo $s;?></option>	
			<?php } ?>
			</select>
			<?php } ?>
			<?php /*
            <input type="text" id="state_filter_name" name="state_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_STATE'); ?>" value="<?php echo @$this->state->get('filter.state_name');?>">
            <input type="hidden" id="state_filter" name="state_filter" value="<?php echo @$this->state->get('filter.state');?>" >
            */ ?>
        </div>
        <div class="filter-field" >
		<?php 
		
		
        $query = "SELECT distinct(address_city) FROM #__toes_address ";
		$city_where = [];
		$city_where[] = "TRIM(address_city) <> ''";
		$city_where[] = "TRIM(address_city) <> '0'";
			if($state_filter) {
				$city_where[] = " address_state = ".$db->Quote($state_filter);
			}
			if($country_filter) {
				$city_where[] = " address_country = ".$db->Quote($country_filter);
			}
		if(count($city_where) > 0){
		$query .= " where  ".implode(" AND ",$city_where);		
		}
		$query .= " ORDER BY address_city";
		 
		$db->setQuery($query);
        $cities = $db->loadColumn();
		 
	
			?>
			<?php if(count($cities)>0){?>
			<select name="city_filter" id="city_filter" class="filter-selectlist" onchange="this.form.submit();">
			<option value="">Select city</option>
			<?php foreach($cities as $c){
			$sel = ($city_filter == $c)?'SELECTED':'';
				
			?>
			<option value="<?php echo $c;?>" <?php echo $sel;?> ><?php echo $c;?></option>	
			<?php } ?>
			</select>
			<?php } ?>
			
			<?php /*
            <input type="text" id="city_filter_name" name="city_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_CITY'); ?>" value="<?php echo @$this->state->get('filter.city_name');?>">
            <input type="hidden" id="city_filter" name="city_filter" value="<?php echo @$this->state->get('filter.city');?>" >
            */ ?> 
        </div>
        
        
        <div class="filter-field" >
       <?php 
		$query = "SELECT `competitive_region_id` AS `key`, `competitive_region_name` AS `value`  FROM 
		#__toes_competitive_region where  competitive_region_id IN(select distinct(club_competitive_region) 
		from `#__toes_club`)  ORDER BY competitive_region_name";
		$db->setQuery($query);
		$regions = $db->loadObjectList();
		 
		 
	
			?>
			<?php if(count($regions)>0){?>
			<select name="region_filter" id="region_filter" class="filter-selectlist" onchange="this.form.submit();">
			<option value="">Select region</option>
			<?php foreach($regions as $r){
			$sel = ($region_filter == $r->key)?'SELECTED':'';
				
			?>
			<option value="<?php echo $r->key;?>" <?php echo $sel;?> ><?php echo $r->value;?></option>	
			<?php } ?>
			</select>
			<?php } ?>
			
			<?php /*
            <input type="text" id="city_filter_name" name="city_filter_name" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SELECT_CITY'); ?>" value="<?php echo @$this->state->get('filter.city_name');?>">
            <input type="hidden" id="city_filter" name="city_filter" value="<?php echo @$this->state->get('filter.city');?>" >
            */ ?>  
        
        </div>
        
        <div class="clr"></div>
        <br/>
        
        <div class="filter-field" >
            <select class="filter-selectlist" name="filter_my_entries" id="filter_my_entries" style="width: 230px;">
                <option value="0" <?php echo ($this->state->get('filter.entries') == 0)?'selected="selected"':'';?> ><?php echo JText::_('COM_TOES_SHOW_ENTRIES_FILTER_ALL'); ?></option>
                <option value="1" <?php echo ($this->state->get('filter.entries') == 1)?'selected="selected"':'';?> ><?php echo JText::_('COM_TOES_SHOW_ENTRIES_FILTER_SELECTED'); ?></option>
                <option value="2" <?php echo ($this->state->get('filter.entries') == 2)?'selected="selected"':'';?> ><?php echo JText::_('COM_TOES_SHOW_ENTRIES_FILTER_FOLLOWING'); ?></option>
            </select>
            <?php /*
            <label for="entries_filter" class="lbl">
                <?php echo JText::_('COM_TOES_SHOW_ENTRIES_FILTER'); ?> :
            </label>
            &nbsp;&nbsp;
            <input type="checkbox" class="filter-checkbox" name="entries_filter" id="entries_filter" <?php if ($this->state->get('filter.entries')) echo 'checked="checked"'; ?> />
            <input type="hidden" id="filter_my_entries" name="filter_my_entries" value="<?php echo $this->state->get('filter.entries'); ?>" />
             */ ?>
        </div>
        <div class="filter-field" >
            <select class="filter-selectlist" name="show_date_status_filter" id="show_date_status_filter" >
                <option <?php echo ($this->state->get('filter.show_date_status_filter') == 'all')?'selected="selected"':'';?> value="all"><?php echo JText::_('COM_TOES_ALL_SHOWS'); ?></option>
                <option <?php echo ($this->state->get('filter.show_date_status_filter') == 'past')?'selected="selected"':'';?> value="past"><?php echo JText::_('COM_TOES_PAST_SHOWS'); ?></option>
                <option <?php echo ($this->state->get('filter.show_date_status_filter') == '')?'selected="selected"':'';?> value=""><?php echo JText::_('COM_TOES_FUTURE_SHOWS'); ?></option>
            </select>
        </div>
        
        <div class="filter-field" >
	        <input type="button" name="reset_filters" value="<?php echo JText::_('COM_TOES_RESET_FILTER');?>" />
        </div>
        
        <div class="clr"></div>
        <br/>
    </div>
</form>
    <div class ="outerdiv">
        <div class="fistouter">
            <div class="fieldblank" >
                <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_CALENDAR'); ?></div>
            </div>
            <div class="clr"></div>
        </div>

        <div class ="seconouter">
            
            <div class="show-list">
                <?php
                $sm = '';
                foreach ($this->shows as $data) {

					if($data->show_status == 'Cancelled')
						$cancelled_class = '-cancelled';
					else
						$cancelled_class = '';
                    if ($sm != $data->show_month) {
                ?>
                    <div class="month-heading"> 
                        <?php 
                            $sm = $data->show_month;
                            $current_month = date('m', strtotime($sm));
                            echo $sm; 
                        ?> 
                    </div>
                <?php
                    }
                ?>
                    <div class="show-entry <?php if($data->show_status == 'Planned' || $data->show_status == 'Rejected' || $data->show_status == 'Cancelled') echo 'grey_entry'; ?>">
                        <div class="date" >
                            <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="view-show-details<?php echo $cancelled_class;?>">
                                <?php 
                                    $start_date = date('d', strtotime($data->show_start_date));
                                    $start_date_month = date('M', strtotime($data->show_start_date));

                                    $end_date = date('d', strtotime($data->show_end_date));
                                    $end_date_month = date('M', strtotime($data->show_end_date));

                                    echo $start_date;

                                    if ($end_date_month != $start_date_month){
                                        if(date('t',strtotime($data->show_start_date)) != $start_date)
                                            echo ' - '.date('t',strtotime($data->show_start_date));
                                        if($end_date == '01')
                                            echo ', ' .$end_date_month.' '.$end_date;
                                        else
                                            echo ', ' .$end_date_month.' 01 - '.$end_date;
                                    } else {
                                        if($start_date != $end_date)
                                            echo ' - ' . $end_date;
                                    }

                                ?>
                            </a>
                        </div>
                        <div class="address" >
                            <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="view-show-details<?php echo $cancelled_class;?>">
                                <?php 
                                    if($data->address_city)
                                        echo $data->address_city.', ';
                                    if($data->address_state)
                                        echo $data->address_state.', ';
                                    if($data->address_country)
                                        echo $data->address_country; 
                                
                                    if($data->show_is_annual) 
                                        echo '<p style="font-style: normal;display: inline-block; padding: 0 10px; margin: 0 10px;" class="info">'.JText::_('COM_TOES_ANNUAL_TAG').'</p>';
                                    else if($data->show_is_regional) 
                                        echo '<p style="font-style: normal;display: inline-block; padding: 0 10px; margin: 0 10px;" class="info">'.JText::_('COM_TOES_REGIONAL_TAG').'</p>';
                                    ?>
                            </a>
                        </div>
                        <div class="club-name" >
                            <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="view-show-details<?php echo $cancelled_class;?>">
                                <?php echo $data->club_name; ?>
                            </a>
                        </div>
                        <div class="action-buttons" >
                            <?php
                                $is_rd = false;
                                //$region = TOESHelper::getRegionDetails(TOESHelper::getClubDetails($data->club_id)->club_competitive_region);

                                if($user->id && TOESHelper::is_regionaldirector($user->id, $data->competitive_region_id))
                                    $is_rd = true;
                            ?>
                            
                            <?php if($data->show_status != 'Cancelled'): ?>
                                <span class="hasTip" title="<?php echo JText::_('VIEW_SHOW_DETAILS');?>">
                                    <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="view-show-details">
                                        <i class="far fa-file-alt"></i> 
                                    </a>
                                </span>

                                <?php if($data->show_status != 'Held' && $user->id): ?>
                                    <?php if(TOESHelper::isSubscribedToShow($user->id, $data->show_id)) :?>
                                        <span class="hasTip" title="<?php echo JText::_('COM_TOES_UNSUBSCRIBE_FROM_THIS_SHOW');?>">
                                            <a href="javascript:void(0)" rel="<?php echo $user->id.';'.$data->show_id; ?>" class="unsubscribe-show">
                                                <i class="fa fa-ban"></i> 
                                            </a>
                                        </span>
                                    <?php else: ?>
                                        <span class="hasTip" title="<?php echo JText::_('COM_TOES_SUBSCRIBE_TO_THIS_SHOW');?>">
                                            <a href="javascript:void(0)" rel="<?php echo $user->id.';'.$data->show_id; ?>" class="subscribe-show">
                                                <i class="fa fa-info"></i>
                                            </a>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            
                                <?php if($data->show_uses_toes == '1'): ?>
                                    <?php if( $isAdmin || ( ( $data->show_status == 'Held' || $data->show_status == 'Open' || $data->show_status == 'Closed' || $data->show_status == 'Scored')
                                            && ( TOESHelper::is_clubowner($user->id, $data->club_id) || TOESHelper::is_showofficial($user->id, $data->show_id) ) ) ): ?>
                                        <span class="hasTip" title="<?php echo JText::_('COM_TOES_ENTRY_CLERK_VIEW');?>">
                                            <a href="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&id='.$data->show_id); ?>" class="enter-show">
                                                <i class="fas fa-file-alt"></i> 
                                            </a>
                                        </span>
                                    <?php endif;?>
                                    
                                    <?php if(($eouser || $isAdmin) && (int)$data->show_uses_toes ): ?>                                     
                                        <span class="hasTip" title="<?php echo JText::_('COM_TOES_EXECUTIVE_OFFICE_VIEW');?>">
                                            <a href="<?php echo JRoute::_('index.php?option=com_toes&view=executiveoffice&id='.$data->show_id); ?>" class="enter-show">
                                                <i class="fas fa-file-alt"></i> 
                                            </a>
                                        </span>
                                    <?php endif;?>

									<?php if( $data->show_status == 'Approved' || $data->show_status == 'Closed'): ?>
										<?php if( $isAdmin || TOESHelper::is_clubowner($user->id, $data->club_id) || TOESHelper::is_showmanager($user->id, $data->show_id) ): ?>
											<span class="hasTip" title="<?php echo JText::_('OPEN_SHOW_FOR_ENTRIES');?>">
												<a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>;<?php echo $data->show_status; ?>" class="open-show-entries">
													<i class="fa fa-unlock"></i> 
												</a>
											</span>
										<?php endif; ?>
									<?php elseif($data->show_status == 'Open'): ?>
										<?php if( $isAdmin || TOESHelper::is_clubowner($user->id, $data->club_id) || TOESHelper::is_showofficial($user->id, $data->show_id) ): ?>
											<span class="hasTip" title="<?php echo JText::_('CLOSE_SHOW_FOR_ENTRIES');?>">
												<a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="close-show-entries">
													<i class="fa fa-lock"></i> 
												</a>
											</span> 
										<?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php //echo "<pre>";var_dump($data->show_status);echo "</pre>";?>
                                <?php // if( ($isAdmin || $is_rd) && ($data->show_status == 'Planned' || $data->show_status == 'Rejected') && ( (@$data->current_show_id && $data->conf_showid == @$data->current_show_id) && $data->rd_approval == '1' && $data->sc_approval == '1' || (!array_key_exists("conflicted_shows",$data) ) )): ?>
                                <?php if( ($isAdmin || $is_rd) && ($data->show_status == 'Planned' || $data->show_status == 'Rejected') && ($data->rd_approval == '1' && $data->sc_approval == '1' || (!array_key_exists("conflicted_shows",$data) ) )): ?>
                                 
                                    <?php if((int)$data->rd_approval > 0){?>
									   <i class="fa fa-check"></i> 
									   <?php }else{ ?>
										<span class="hasTip" title="<?php echo JText::_('APPROVE_SHOW');?>">
											<a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="approve-show">
												<i class="fa fa-check"></i> 
											</a>
										</span>
                                       <?php }   ?>
                                    <?php $btnshown = 0;
										/*
                                        if(count($data->conflicts) ){?>
                                        <?php 
                                            foreach($data->conflicts as $rd){
                                                if($rd->existing_show_region_rd_user_id == $user->id && !$rd->existing_show_region_rd_approved){ 
                                                    $btnshown = 1;?>
                                                    <span class="hasTip" title="<?php echo JText::_('APPROVE_SHOW');?>">
                                                        <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="approve-show" id="e_rd-<?php echo $data->show_id.'-'.$rd->id; ?>">
                                                            <i class="fa fa-check"></i> 
                                                        </a>
                                                    </span>
                                                <?php if($btnshown) break;
                                                }   ?>
                                                <?php if(!$btnshown){
                                                    if($rd->conflicting_show_region_rd_user_id == $user->id && !$rd->conflicting_show_region_rd_approved){ 
                                                        $btnshown = 1;?>
                                                            <span class="hasTip" title="<?php echo JText::_('APPROVE_SHOW');?>">
                                                                <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="approve-show" id="c_rd-<?php echo $data->show_id.'-'.$rd->id; ?>">
                                                                    <i class="fa fa-check"></i> 
                                                                </a>
                                                            </span>
                                                        <?php if($btnshown) break;
                                                        }   ?>
                                                <?php } ?>
                                        <?php } ?>
                                        <?php // foreach($data->conflicts as $c_rd){
                                           // } ?>
                                    <?php } 
                                    */
                                    ?>
                                <?php endif; ?> 

                                    <?php
                                    /* if(count($data->existing_club_official) ){?>
                                        <?php if(!$btnshown){
                                            foreach($data->existing_club_official as $e_co){
                                                $str = "|".$user->id."|";
                                                if(strpos($e_co->existing_club_official_user_ids, $str) !== false && !$e_co->existing_club_official_approved){ 
                                                    $btnshown = 1;?>
                                                    <span class="hasTip" title="<?php echo JText::_('APPROVE_SHOW');?>">
                                                        <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="approve-show" id="e_co-<?php echo $data->show_id.'-'.$e_co->id; ?>">
                                                            <i class="fa fa-check"></i> 
                                                        </a>
                                                    </span>
                                                <?php if($btnshown) break;
                                                }   ?>
                                        <?php }
                                            } ?>
                                    <?php } 
                                    */ 
                                    ?>


								<?php /*
                                <?php //var_dump(isset($data->conflicted_shows));?>
                                <?php //  var_dump($data->existing_club_official);?>
                                <?php if( ($isAdmin || $is_rd) && ($data->show_status == 'Planned' || $data->show_status == 'Approved' ) && (!isset($data->conflicted_shows)) ): ?>
                                   <!--  <span class="hasTip" title="<?php echo JText::_('REJECT_SHOW');?>">
                                        <a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="reject-show">
                                            <i class="fas fa-trash-alt"></i> 
                                        </a>
                                    </span> -->
                                <?php endif; ?>
                                */ ?>
                                <?php if($data->show_status != 'Held' && ($isAdmin || TOESHelper::is_clubowner($user->id, $data->club_id) || TOESHelper::is_showmanager($user->id, $data->show_id))) :?>
                                    <span class="hasTip" title="<?php echo JText::_('EDIT_SHOW');?>">
                                        <a href="<?php echo JRoute::_('index.php?option=com_toes&view=show&layout=edit&id='.$data->show_id);?>" rel="<?php echo $data->show_id.';'.$data->club_id; ?>" class="edit-show" >
                                            <i class="fa fa-edit"></i> 
                                        </a>
                                    </span>
                                    <?php if($data->show_status != 'Rejected' && $data->show_status != 'Held') :?>
                                        <span class="hasTip" title="<?php echo JText::_('CANCEL_SHOW');?>">
                                            <a href="javascript:void(0)" rel="<?php echo $data->show_id.';'.$data->club_id; ?>" class="cancel-show" >
                                                <i class="fas fa-trash-alt"></i> 
                                            </a>
                                        </span>
                                    <?php endif; ?>
										
									<?php 
									$entries = TOESHelper::getShowEntriesCount($data->show_id);
									
									if(!$entries) :
									?>
										<span class="hasTip" title="<?php echo JText::_('DELETE_SHOW');?>">
											<a href="javascript:void(0)" rel="<?php echo $data->show_id; ?>" class="delete-show" >
												<i class="fa fa-trash"></i> 
											</a>
										</span>
									<?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
								<?php // ?>
                                <?php if($data->show_uses_toes == '1' && ( $isAdmin || TOESHelper::is_clubowner($user->id, $data->club_id) || TOESHelper::is_showofficial($user->id, $data->show_id) ) &&  $data->show_status == 'Cancelled'): ?>
									<span class="hasTip" title="<?php echo JText::_('COM_TOES_ENTRY_CLERK_VIEW');?>">
										<a href="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&id='.$data->show_id); ?>" class="enter-show">
											<i class="fas fa-file-alt"></i> 
										</a>
									</span>
								<?php endif;?>
                                <?php // ?>
                                <?php echo JText::_('COM_TOES_SHOW_CANCELLED');?>
                                
                            <?php endif; ?>
                            <?php //spider ?>
  
                            
                        </div>
                        <div class="clr"></div>
                        <div id="show-<?php echo $data->show_id; ?>" class="show-details"></div>
                        <div class="clr"></div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
<!-- </form> -->
</div>
<?php 
$session = JFactory::getSession();
$add_show = 0;
if($session->has('add_show')) 
{
    $add_show = $session->get('add_show');
    $session->clear('add_show');
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>


<style>
span.ui-icon-circle-triangle-w{padding:0px 20px!important;}
#ui-datepicker-div{background:#c9c9c9}
</style>
<!--
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
-->
<script type="text/javascript">
    //  jQuery('.clubofficialapprove-show').on('click',function(){
		 
	// 	//alert('It is here');
	// 	//return;
		
	// 	var show_id = jQuery(this).attr('rel');
	// 	rel = show_id.split(';');
	// 	var formdata = jQuery('#approveform').serialize();
	// 	jQuery.ajax({
	// 	method: "POST",
	// 	url: "index.php?option=com_toes&task=shows.conflicted_clubofficialapproveshow&show_id="+rel[0]+"&conf_showid="+rel[1]+"&tmpl=component",	
	// 	type: 'post',
	// 	data: formdata,
	// 	success:function(data){
	// 			if(data == 1)
	// 			{		
	// 				window.location.href = "index.php?option=com_toes&view=shows";
	// 			}						
	// 	}	
	// 	});	
		
	// });
    
    
    jQuery('.approve-show').on('click',function(){
		var showid = jQuery(this).attr('rel');
		var	rel = showid;
		var id = jQuery(this).attr('id');
		if(id){
		
        var details = jQuery(this).attr('id').split('-');
        var data = {'show_id':details[1],'approver':details[0],'conflict_id':details[2]};
        // alert(details);
        if(jQuery('#show-'+rel+':visible').length){
            jQuery('.show-details').html('');
            jQuery('.show-details').hide();
            return;
        }
        
        jQuery('.show-details').html('');
        jQuery('.show-details').hide();
        
        jQuery('#show-'+rel).show();
        jQuery('#show-'+rel).html('<img alt="loading..." src="media/com_toes/images/loading.gif" />');
        
        jQuery.ajax({
            url: '<?php echo JUri::root();?>index.php?option=com_toes&view=show&layout=approveconflict&id='+rel+'&tmpl=component',
            type: 'post',
            data: data,
        }).done(function(responseText){
            responseText = responseText.trim();
            jQuery('#show-'+rel).html(responseText);
        });
        
		}else{
			//var rel = jQuery(this).attr('rel');
           
            //alert('in else');
            //return ;
            jQuery.ajax({
                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.updateStatus&status=Approved&id='+rel,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText == 1)
                    location.reload();
                else
                    jbox_alert(responseText);
            });
			
			
			
		}
        
        
	});
	
    jQuery(document).on('click','.approve-conflict',function(){
		var showid = jQuery(this).attr('rel');
        rel = showid;
        var details = jQuery(this).attr('id').split('-');
        var data = {'approver':details[0],'conflicting_show_id':details[1],'existing_show_id':details[2],'conflict_id':details[3]};
        // alert(details);
        new jBox('Confirm',{
	        // content: '<form method="post" id="approveform" name="approveForm"><?php echo JText::_("COM_TOES_REASON"); ?> <textarea name="reason" id="reason"></textarea><input type="hidden" name="show_id" value="'+rel+'"/></form>',
            content: '<form method="post" id="approveform" name="approveForm"></form>',
	        width: '500px',
	        cancelButton : "<?php echo JText::_('REJECT'); ?>",
	        confirmButton: "<?php echo JText::_('APPROVE'); ?>",
	        cancel: function(){
				
			},
	        confirm: function() {
                var formdata = jQuery('#approveform').serialize();
                console.log(formdata);
	            jQuery.ajax({
	                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.approveconflictedshow&tmpl=component',
	                type: 'post',
	                data: data,
	            }).done(function(responseText){
                    console.log(responseText);
                    if(responseText == 1)
                        location.reload();
                        // jQuery('.approve-show').trigger('click');
	            });
	        }
	    }).open();  
	});

    jQuery(document).on('click','.reject-conflict',function(){
		var showid = jQuery(this).attr('rel');
        rel = showid;
        var details = jQuery(this).attr('id').split('-');
        var data = {'approver':details[0],'conflicting_show_id':details[1],'existing_show_id':details[2],'conflict_id':details[3]};
        // alert(details);
        new jBox('Confirm',{
	        // content: '<form method="post" id="rejectform" name="rejectForm"><?php echo JText::_("COM_TOES_REASON"); ?> <textarea name="reason" id="reason"></textarea><input type="hidden" name="show_id" value="'+rel+'"/></form>',
            content: '<form method="post" id="rejectform" name="rejectForm"></form>',
	        width: '500px',
	        cancelButton : "cancel",
	        confirmButton: "ok",
	        cancel: function(){
				
			},
	        confirm: function() {
                var formdata = jQuery('#rejectform').serialize();
                console.log(formdata);
	            jQuery.ajax({
	                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.rejectconflictedshow&tmpl=component',
	                type: 'post',
	                data: data,
	            }).done(function(responseText){
                    console.log(responseText);
                    if(responseText == 1)
                        location.reload();
                        // jQuery('.approve-show').trigger('click');
	            });
	        }
	    }).open();  
	});
	
	
    var add_show;
    var user_id = <?php echo $user->id;?>;
    <?php if($add_show): ?>
        var add_show = <?php echo $add_show;?>;
    <?php endif; ?>

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

    function confirm_sync()
    {
	 	new jBox('Confirm',{
	        content: "<?php echo JText::_('COM_TOES_CONFIRM_SYNC_COMMON_DATA'); ?>",
	        width: '500px',
	        cancelButton : "<?php echo JText::_('JNO'); ?>",
	        confirmButton: "<?php echo JText::_('JYES'); ?>",
	        confirm: function() {
	            jQuery.ajax({
	                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.sync_db&action=common',
	                type: 'post',
	            }).done(function(responseText){
					responseText = responseText.trim();
                	jQuery('#loader').hide();
                    if(responseText == 1)
                        location.reload();
                    else
                        jbox_alert(responseText);
	            });
	
	            jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
	            jQuery('#loader').css('padding-top', (myHeight/2)+'px');
	    		jQuery('#progress-box').hide();
	            jQuery('#loader').show();
	        }
	    }).open(); 
    }
    
    jQuery(document).ready(function(){

        if(add_show)  {
            jQuery('.show-details').html('');
            jQuery('.show-details').hide();
            
            jQuery('#show-'+add_show).show();
            jQuery('#show-'+add_show).html('<img alt="loading..." src="media/com_toes/images/loading.gif" />');
            
            jQuery.ajax({
                url: '<?php echo JUri::root();?>index.php?option=com_toes&view=show&layout=short&id='+add_show+'&tmpl=component',
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#show-'+add_show).html( responseText);
                add_new_entry(add_show,'add-entry-div-'+user_id,'');
            });
        }  else if(window.location.hash){
            if(window.location.hash.indexOf('#show') != -1)
            {
                var rel = parseInt(window.location.hash.replace('#show',''));
                if(!isNaN(rel) && jQuery('#show-'+rel).length )
                {
                	if(jQuery('#show-'+rel+':visible').length)
                    {
                        jQuery('.show-details').html('');
                        jQuery('.show-details').hide();
                        return;
                    }

                    jQuery('.show-details').html('');
                    jQuery('.show-details').hide();

                    jQuery('#show-'+rel).show();
                    jQuery('#show-'+rel).html('<img alt="loading..." src="media/com_toes/images/loading.gif" />');

                    jQuery.ajax({
                        url: '<?php echo JUri::root();?>index.php?option=com_toes&view=show&layout=short&id='+rel+'&tmpl=component',
                        type: 'post',
                    }).done(function(responseText){
						responseText = responseText.trim();
                        jQuery('#show-'+rel).html( responseText);
                        jQuery('#show-'+rel).scrollIntoView();
                    });
                }
            }
        }
        
        jQuery('.view-show-details').on('click',function(){
            var rel = jQuery(this).attr('rel');
            
            if(jQuery('#show-'+rel+':visible').length)
            {
                jQuery('.show-details').html('');
                jQuery('.show-details').hide();
                return;
            }
            
            jQuery('.show-details').html('');
            jQuery('.show-details').hide();
            
            jQuery('#show-'+rel).show();
            jQuery('#show-'+rel).html('<img alt="loading..." src="media/com_toes/images/loading.gif" />');
            
            jQuery.ajax({
                url: '<?php echo JUri::root();?>index.php?option=com_toes&view=show&layout=short&id='+rel+'&tmpl=component',
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#show-'+rel).html(responseText);
            });
        });
        
        jQuery('.unsubscribe-show').on('click',function(){
            var rel = jQuery(this).attr('rel');
            rel = rel.split(';');
            
            jQuery.ajax({
                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=show.unsubscribe&show_id='+rel[1]+'&user_id='+rel[0]+'&tmpl=component',
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText == 1)
                {
                    jbox_notice("<?php echo JText::_('COM_TOES_UNSUBSCRIBED_SUCCESSFULLY')?>", 'green');
                }
                else
                {
                    jbox_notice("<?php echo JText::_('COM_TOES_UNSUBSCRIBED_UNSUCCESSFULLY')?>", 'red');
                }
                setInterval(location.reload(), 3000);
            });
        });        
        
        jQuery('.subscribe-show').on('click',function(){
            var rel = jQuery(this).attr('rel');
            rel = rel.split(';');
            
            jQuery.ajax({
                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=show.subscribe&show_id='+rel[1]+'&user_id='+rel[0]+'&tmpl=component',
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText == 1)
                {
                    jbox_notice("<?php echo JText::_('COM_TOES_SUBSCRIBED_SUCCESSFULLY')?>", 'green');
                }
                else
                {
                    jbox_notice("<?php echo JText::_('COM_TOES_SUBSCRIBED_UNSUCCESSFULLY')?>", 'red');
                }
                setInterval(location.reload(), 3000);
            });
        });        

        jQuery('.filter-checkbox').on('change', function(){
            if(this.checked)
                jQuery('#filter_my_entries').val(1);
            else
                jQuery('#filter_my_entries').val(0);

            jQuery('#show-list-form').submit();
        });
        
        jQuery('.filter-selectlist').on('change', function(){
            if(this.get('id') == 'country_filter')
            {
                jQuery('#state_filter').val(0);
                jQuery('#city_filter').val(0);
            }
            
            if(this.get('id') == 'state_filter')
                jQuery('#city_filter').val(0);
            
            jQuery('#show-list-form').submit();
        });


        jQuery('.reject-show').on('click',function(){
            var rel = jQuery(this).attr('rel');
            
            jQuery.ajax({
                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.updateStatus&status=Rejected&id='+rel,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText == 1)
                    location.reload();
                else
                    jbox_alert(responseText);
            });
        });
		
		if(jQuery('.delete-show').length)
		{
			jQuery('.delete-show').on('click',function(){
				var rel = jQuery(this).attr('rel');

			 	new jBox('Confirm',{
			        content: "<?php echo JText::_('COM_TOES_DELETE_SHOW_FROM_CALENDAR_YES_NO'); ?>",
			        width: '500px',
			        cancelButton : "<?php echo JText::_('COM_TOES_DELETE_SHOW_NO'); ?>",
			        confirmButton: "<?php echo JText::_('COM_TOES_DELETE_SHOW_YES'); ?>",
			        confirm: function() {
			            jQuery.ajax({
			                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.delete&id='+rel,
			                type: 'post',
			            }).done(function(responseText){
							responseText = responseText.trim();
			                if(responseText == 1)
			                    location.reload();
			                else
			                    jbox_alert(responseText);
			            });
			            
			        }
			    }).open(); 
			});
		}
		
        jQuery('.open-show-entries').on('click',function(){
            var rel = jQuery(this).attr('rel');
            rel = rel.split(';');
            
            if(rel[1] == 'Approved')
            {
 			 	new jBox('Confirm',{
			        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_OPEN_SHOW'); ?>",
			        width: '500px',
			        cancelButton : "<?php echo JText::_('COM_TOES_CONFIRM_TO_OPEN_SHOW_REJECT_TEXT'); ?>",
			        confirmButton: "<?php echo JText::_('COM_TOES_CONFIRM_TO_OPEN_SHOW_ACCEPT_TEXT'); ?>",
			        confirm: function() {
                        jQuery.ajax({
                            url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.updateStatus&status=Open&id='+rel[0],
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            if(responseText == 1)
                                location.reload();
                            else
                                jbox_alert(responseText);
                        });
                    }
                }).open(); 
            }
            else
            {            
                jQuery.ajax({
                    url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.updateStatus&status=Open&id='+rel[0],
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    if(responseText == 1)
                        location.reload();
                    else
                        jbox_alert(responseText);
                });
            }
        });
        jQuery('.close-show-entries').on('click',function(){
            var rel = jQuery(this).attr('rel');

		 	new jBox('Confirm',{
		        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_CLOSE_SHOW'); ?>",
		        width: '500px',
		        cancelButton : NO_BUTTON,
		        confirmButton: YES_BUTTON,
		        confirm: function() {
                    jQuery.ajax({
                        url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.updateStatus&status=Closed&id='+rel,
                        type: 'post',
                    }).done(function(responseText){
						responseText = responseText.trim();
                        if(responseText == 1)
                        {
                            jQuery.ajax({
                                url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.getEntriesNeedsConfirmation&id='+rel,
                                type: 'post',
                            }).done(function(responseText){
								responseText = responseText.trim();
                                if(responseText == 1)
                                {
                                    jbox_alert("<?php echo JText::_('COM_TOES_NEED_TO_CONFIRM_ENTRIES_WARNING'); ?>");
                                }
                                setInterval(location.reload(),2000);
                            });
                        }
                        else
                            jbox_alert(responseText);
                    });
                }
            }).open();            

        });

        jQuery('.cancel-show').on('click',function(){
            var rel = jQuery(this).attr('rel');
            var ids = rel.split(';');
            
		 	new jBox('Confirm',{
		        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_CANCEL_SHOW'); ?>",
		        width: '400px',
		        cancelButton : NO_BUTTON,
		        confirmButton: YES_BUTTON,
		        confirm: function() {
 				    jQuery.ajax({
				        url: '<?php echo JUri::root();?>index.php?option=com_toes&task=shows.updateStatus&status=Cancelled&id='+ids[0]+'&club_id='+ids[1],
				        type: 'post',
				    }).done( function(responseText){
						responseText = responseText.trim();
				        if(responseText == 1)
				            location.reload();
				        else
				            jbox_alert(responseText);
					});
				}
            }).open();            
        });
    });
    
    function close_show()    {
        jQuery('.show-details').html('');
        jQuery('.show-details').hide();
    }
        
    function toggle_filter(action) {
        if(action == 'close')
        {
            jQuery('#hide-doc-span').hide();
            jQuery('#open-doc-span').show();
            jQuery('.filter-block').hide();
        }
        else
        {
            jQuery('#hide-doc-span').show();
            jQuery('#open-doc-span').hide();
            jQuery('.filter-block').show();
        }
    }
    
    jQuery(document).ready(function(){

      	jQuery('input[name="reset_filters"]').on('click', function(e){
      		
	        jQuery('#club_filter_name').val('');
	        jQuery('#club_filter').val('');
	        jQuery('#country_filter_name').val('');
	        jQuery('#country_filter').val('');
	        jQuery('#state_filter_name').val('');
	        jQuery('#state_filter').val('');
	        jQuery('#city_filter_name').val('');
	        jQuery('#city_filter').val('');
	        
	        jQuery('#filter_my_entries').val('');
	        jQuery('#show_date_status_filter').val('');
        	
            jQuery('#show-list-form').submit();         
        });


		jQuery( "#club_filter_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=shows.getClubs&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#club_filter" ).val(ui.item.key);
		  	jQuery( "#club_filter_name" ).val(ui.item.value);
		  	
		  	jQuery('#show-list-form').submit();
		  }
		});    
	
	 	jQuery( "#country_filter_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=shows.getCountries&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#country_filter" ).val(ui.item.key);
		  	jQuery( "#country_filter_name" ).val(ui.item.value);
		  	
		    jQuery('#state_filter').val(0);
	        jQuery('#city_filter').val(0);
	            
	        jQuery('#show-list-form').submit();
		  }
		});    
	
		jQuery( "#state_filter_name" ).autocomplete({
		  source: function( request, response ) {
	        jQuery.ajax({
	          url: 'index.php?option=com_toes&task=shows.getStates&tmpl=component',
	          dataType: "json",
	          data: {
	            term: request.term, 
            	country_id: jQuery( "#country_filter" ).val()
	          }
	        }).done(function( data ) {
	            response( data );
          	});
	      },
		  select: function( event, ui ) {
		  	jQuery( "#state_filter" ).val(ui.item.key);
		  	jQuery( "#state_filter_name" ).val(ui.item.value);

	        jQuery('#city_filter').val(0);
	        jQuery('#show-list-form').submit();
		  }
		}); 
	
		jQuery( "#city_filter_name" ).autocomplete({
		  source: function( request, response ) {
	        jQuery.ajax({
	          url: 'index.php?option=com_toes&task=shows.getCities&tmpl=component',
	          dataType: "json",
	          data: {
	            term: request.term, 
            	state_id: jQuery( "#state_filter" ).val(),
            	country_id: (!jQuery( "#state_filter" ).val()?jQuery( "#country_filter" ).val():0)
	          }
	        }).done(function( data ) {
	            response( data );
          	});
	      },
		  select: function( event, ui ) {
		  	jQuery( "#city_filter" ).val(ui.item.key);
		  	jQuery( "#city_filter_name" ).val(ui.item.value);
	        
	        jQuery('#show-list-form').submit();
		  }
		}); 
	});
	
	
	/*
	jQuery(document).on('click','a.add_document_type_btn',function(){
	//alert(jQuery('div#document_container select.dtype').length);
	//jQuery(this).nextAll().remove();	
	//jQuery('div#document_container').append(jQuery('div#document_type_block').html());
	
	if(!jQuery('div#document_container select.dtype:last').val()){
	jQuery('div#document_container').append(jQuery('div#document_type_block').html());	
	}
	 
	 
	 
	var selectedtypes = new Array;
	jQuery('input.document_type_id').each(function(){
		 selectedtypes.push(jQuery(this).val());		
	});
	console.log(selectedtypes);
	jQuery('select.dtype:last > option').each(function(){
	if(jQuery.inArray(jQuery(this).attr('value'),selectedtypes)!= -1)
	jQuery(this).remove();	
		
	});
	
		
	//jQuery(this).remove();	
	});
	*/
	//
	
	function getdocumenttypeids(){
	jQuery('input#document_type_ids').val('');
	jQuery('ul#checklist_documents li').each(function(){
		jQuery(this).find('i.fa-times').remove() ;
		jQuery(this).find('i.fa-check').remove() ;
		jQuery(this).prepend(timesicon) ;			
	});
		
		
	var document_type_ids = new Array;
	jQuery('div.dtdiv div.del_document_type').each(function(){
		var document_type = jQuery(this).attr('data-id');
		if(	document_type_ids.indexOf(document_type) == '-1')
		document_type_ids.push(document_type);
		//jQuery('select#document_types option').attr('disabled',false);
		jQuery('select#document_types option').each(function(){
			if(jQuery(this).attr('value') == document_type ){
				jQuery(this).attr('disabled',true);
			}
		});
			
		jQuery('ul#checklist_documents li#checklist_'+document_type).find('i.fa-times').remove() ;
		jQuery('ul#checklist_documents li#checklist_'+document_type).prepend(checkicon) ;
			
		jQuery('select#document_types').val('0');
	});	
	 console.log(document_type_ids);
	jQuery('input#document_type_ids').val(document_type_ids.join(','));	
}
	
	jQuery(document).on('change','select.dtype',function(){
	
	 
	var processed_document_type = new Array;
	
	console.log(processed_document_type);
	var document_type = jQuery(this).val();
	if(!document_type)return;
	if(processed_document_type.indexOf(document_type) == '-1'){
	
	console.log(document_type);
	jQuery(this).closest('div.fieldbg').hide();
	processed_document_type.push(document_type);
	//if(parseInt(document_type)>0){
		jQuery.ajax({
			url:'<?php echo JURI::root()?>index.php?option=com_toes&view=cat&layout=raw&format=raw&id='+parseInt(document_type),	
			method : 'GET',
			success : function(str){
				
				//var div = '<div id=""></div>';
				jQuery('div#document_container').append(str);	
				
				if(tica_organization_document_type_ids_array.in_array(document_type)){
					jQuery('select#organization_'+document_type).val('0');
					//alert(jQuery('select#organization_'+document_type).val());
					jQuery('select#organization_'+document_type).closest('div.docrow').hide();
				}
				  
				 
				jQuery('div#document_container input.document_file').each(function(){
					var id = '';
					if(!jQuery(this).hasClass('converted')){
						id = jQuery(this).attr('id');
						var doc_no = id.replace('document_','');
						jQuery(this).fileuploader({
							fileMaxSize:5,
							limit:1,
							enableApi: true,
							upload: {
								// upload URL {String}
								url: 'index.php?option=com_toes&task=entry.imageupload',
								// upload data {null, Object}
								// you can also change this Object in beforeSend callback
								// example: { option_1: 'yes', option_2: 'ok' }
								data:  { doc_no: doc_no, cat_id:jQuery('input#cat_id').val()},
								type: 'POST',
								enctype: 'multipart/form-data',
								start: false,
								synchron: true,
								chunk: false,
								beforeSend: function(item, listEl, parentEl, newInputEl, inputEl) {
									item.upload.data.org_id = jQuery('#organization_'+doc_no).val();
									return true;
								},
								onSuccess: function(data, item, listEl, parentEl, newInputEl, inputEl, textStatus, jqXHR) {
									item.html.find('.column-actions').append(
										'<a class="fileuploader-action fileuploader-action-remove fileuploader-action-success" title="Remove"><i></i></a>'
										);
									console.log(data);
									// console.log(id);
									var data = JSON.parse(data);
									if(data.isSuccess){
										var doc_name = data.files[0].name;
										console.log(doc_name);
										jQuery('#doc_'+doc_no).val(doc_name);
									}
									setTimeout(function() {
										item.html.find('.progress-bar2').fadeOut(400);
									}, 400);
								}
							}
						});	
						jQuery(this).addClass('converted');	
					}
					jQuery('ul#checklist_documents li#checklist_'+document_type).remove('i.fa-cross') ;
					jQuery('ul#checklist_documents li#checklist_'+document_type).prepend(checkicon) ;
					getdocumenttypeids();
				});
				
				jQuery('div.fileuploader-input-button').remove();
				jQuery('select#organization_'+document_type).select2();
				 
			}
		});
	 
	jQuery(this).attr('disabled',true);
	jQuery('div#document_container  a.add_document_type_btn').remove();
	}
});
function copyshow(show_id){
	
	window.location = 'index.php?option=com_toes&view=show&layout=copyshowconfirm&id='+show_id;
	return;
	/*
	if(confirm('Do you really want to copy this show?'))
	window.location = 'index.php?option=com_toes&task=show.copy&show_id='+show_id;	
	}
	*/
	new jBox('Confirm',{
		content: 'Do you really want to copy this show?',
		width: '400px',
		cancelButton : 'OPTION-A',
		confirmButton: 'OPTION-B',
		cancel: function() {
			// do nothing			
		},
		confirm: function() {
			
			window.location = 'index.php?option=com_toes&view=show&layout=copyshowconfirm&id='+show_id;

	}

	}).open();
 
	
	  
}

function copyshow_bak(show_id){
	/*
	if(confirm('Do you really want to copy this show?'))
	window.location = 'index.php?option=com_toes&task=show.copy&show_id='+show_id;
	
	}
	*/
	
	/*
	SqueezeBox.initialize({});					
	SqueezeBox.open('index.php?option=com_toes&view=show&layout=copyshowconfirm&show_id='+show_id+'&tmpl=component','iframe',650,400);
	*/
	var myModal = new jBox('Confirm', {
	content: jQuery('#copy_show_'+show_id),
	width: '400px',
	cancelButton : 'Cancel',
	confirmButton: 'Copy Show',
	cancel: function() {
	
				
			},
			confirm: function() {
			if(!jQuery('#start_date').val())
			alert('Please select start date');
			else
			copytheshow(jQuery('#copy_show_id').val());		
		}
	});
	 
	myModal.open();	
	jQuery( "#start_date" ).datepicker({dateFormat: 'yy-mm-dd',minDate: '+1m',
    showOn: 'button',
    //dateFormat: "dd M yy",
    /*
    onSelect: function(dateText){
    jQuery('#start_date_formatted').html(new Date(Date.parse(dateText)).format("dd M yyyy"));
    }
    */
    onSelect: function(dateText, inst){
		var theDate = new Date(Date.parse(jQuery(this).datepicker('getDate')));
		var dateFormatted = jQuery.datepicker.formatDate('dd M yy', theDate);
		jQuery("#start_date_formatted").text(dateFormatted);
	}
    /*
    onSelect: function(dateText) {
        console.log(this.value);
    }  
    */
}).next('button').button({
    icons: {
        primary: 'ui-icon-calendar'
    }, text:false
});	 
		 

}
function copytheshow(){
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
	//
	jQuery.ajax({
		method: "POST",
		url: "index.php?option=com_toes&task=show.checkradiusresultshowcopy&tmpl=component",	
		data: {start_date:jQuery('input#start_date').val(),copy_show_id: jQuery('input#copy_show_id').val()},
		success:function(data){
				console.log(data);
				 
				if(data=='2')
				{		
					var warningMsg = '';
					warningMsg += "<br/><br/><p>Option A:<?php echo JText::_('COM_TOES_SHOW_CHANGE_SHOW_DATE_AND_LOCATION'); ?></p> <P>Option B :<?php echo JText::_('COM_TOES_SHOW_REQUEST_APPROVAL_FROM_OTHER_CLUB_AND_REGINAL_DIRECTOR'); ?></P>";
					//console.log(warningMsg);
					if(warningMsg)
					{
						new jBox('Confirm',{
							content: warningMsg,
							width: '400px',
							cancelButton : 'OPTION-A',
							confirmButton: 'OPTION-B',
							cancel: function() {
								
								
							},
							confirm: function() {
								
								jQuery('form#copyform').submit();
						
						}
						
						}).open();
					}
				}
				else if(data == '1')
				{					 
					jQuery('form#copyform').submit();
				}else if(data == '-1')
				{	
					alert('Not Authorized');
					//jQuery('form#copyform').submit();
				}
				
	}	
	});
	//
	  
	  
	  
  }
 
	
</script>    

