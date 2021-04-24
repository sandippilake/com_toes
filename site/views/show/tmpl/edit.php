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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');
JHtml::_('formbehavior2.select2','select');

$app = JFactory::getApplication();
$id = $app->input->getInt('id');

$editor = JFactory::getEditor();

$showdays = $this->item->showdays;
$ringformats = $this->item->ringformats;
$ringtimings = $this->item->ringtimings;
$ringjudgs = $this->item->ringjudgs;

$org_show_day_ids = '';
$org_show_day_dates = '';

$list_org_show_day_ids = [];
$list_org_show_day_dates = [];
if (count($showdays)) {
    foreach ($showdays as $sd) {
        $list_org_show_day_ids[] = $sd->show_day_id;
        $list_org_show_day_dates[] = $sd->show_day_date;
    }

    $org_show_day_ids = implode(',', $list_org_show_day_ids);
    $org_show_day_dates = implode(',', $list_org_show_day_dates);
}

$org_ring_ids = '';
$org_ring_days = '';

$list_org_ring_ids = [];
$list_org_ring_days = [];

if ($this->item->rings && count($this->item->rings)) {
    foreach ($this->item->rings as $r) {
        $list_org_ring_ids[] = $r->ring_id;
        $list_org_ring_days[] = $r->ring_show_day;
    }

    $org_ring_ids = implode(',', $list_org_ring_ids);
    $org_ring_days = implode(',', $list_org_ring_days);
}

$orgDiff = new stdClass();
if (isset($this->item->show_start_date) && isset($this->item->show_end_date)) {
    //$orgStart = new DateTime($this->item->show_start_date);
    //$orgEnd = new DateTime($this->item->show_end_date);
    //$orgDiff = $orgEnd->diff($orgStart);
    //######### spider hack #########
    $diff = abs(strtotime($this->item->show_end_date) - strtotime($this->item->show_start_date));
    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $orgDiff->days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    //echo $orgDiff->days;die;
}
$params = JComponentHelper::getParams('com_toes');
$googlemapkey = $params->get('map_key');

if(TOESHelper::isAdmin())
$isAdmin = true;
else
$isAdmin = false;
$show_days = 0 ;
if($id){
$q = "select v.venue_name,a.*  from `#__toes_venue` as v JOIN `#__toes_address` as a ON v.venue_address = a.address_id 
where v.venue_id =".$this->item->show_venue;	
$db = JFactory::getDBO();	 
$db->setQuery($q);	
$venue = $db->loadObject();
//var_dump($venue );	
$db->setQuery("select count(*) from `#__toes_show_day` where `show_day_show` =".$id);
$show_days = $db->loadResult();


}else
$venue = null;
	 
?>
<style>
a.modal{position:relative!important;display:block!important;}
input[type="text"]:disabled{background-color:#eeefe9!important;}
.button-pressed{background:black!important;color:grey!important;}
#edit_venue_div{max-width:220px;}
    </style>

<!--link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500"-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googlemapkey;?>&libraries=places&callback=initAutocomplete"
        async defer></script>

<script type="text/javascript">
    
   /* Element.implement({
        append: function(newhtml) {
            return this.adopt(new Element('div', {html: newhtml}).getChildren());
        }
    });   */ 
    
    var tba_judge_id = 172;
    
<?php if (isset($orgDiff->days)): ?>
        var diff = <?php echo $orgDiff->days; ?>;
<?php endif; ?>
    
    var notification_messsage = "<h3>Change Show Date</h3>";
    notification_messsage += "<?php echo JText::_('COM_TOES_SHOW_CHANGE_DATE_NOTIFICATION_MESSAGE'); ?><br/><br/>";
<?php
if (isset($this->item->show_status) && $this->item->show_status != 1)
    echo 'notification_messsage += "' . JText::_('COM_TOES_SHOW_APPROVED_SHOW_NOTIFICATION_MESSAGE') . '"';
?>

    var cur_sd = '';
    var cur_ed = '';

    var org_start_date = '<?php echo isset($this->item->show_start_date) ? $this->item->show_start_date : '0000-00-00' ?>';
    var org_start_date_int = org_start_date.split('-').join('');
    var org_end_date = '<?php echo isset($this->item->show_end_date) ? $this->item->show_end_date : '0000-00-00' ?>';
    var org_end_date_int = org_end_date.split('-').join('');

    var prev_start_date = org_start_date;
    
    var is_date_change_approved = false;
    
    jQuery(document).ready(function(){
      for(var c=1; c < jQuery('#count_rings').val(); c++)
        {
            set_judges(c);
			set_ring_clerks(c);
        }
        
        checkFormat();
    });
	
	var prev_judge_text = '';
	var prev_judge_id = '';
	var prev_set = false;

	var prev_ring_clerk_text = '';
	var prev_ring_clerk_id = '';
	var prev_ring_clerk_set = false;

	function prev_judge_check(index, evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode

		if(charCode == 27 && prev_judge_id && prev_judge_text)
		{
			jQuery('#ring_judge'+index).val(prev_judge_text);
			jQuery('#ring_judge_id'+index).val(prev_judge_id);
			prev_set = false;
			return true;
		}

		if(charCode == 16 || charCode == 17 || charCode == 18 || charCode == 20 ||
				charCode == 9 || charCode == 13 || charCode == 93 || charCode == 36 ||
				charCode == 33 || charCode == 34 || charCode == 35 || (charCode >= 112 && charCode <= 123) ||
				(charCode == 65 && evt.ctrlKey === true))
			return true;

		if(prev_set)
			return true;

		prev_judge_text = jQuery('#ring_judge'+index).val();
		prev_judge_id = jQuery('#ring_judge_id'+index).val();
		jQuery('#ring_judge_id'+index).val('');
		prev_set = true;

		return true;
	}

	function clear_prev_judge()
	{
		prev_judge_text = '';
		prev_judge_id = '';
		prev_set = false;
	}

	function prev_ring_clerk_check(index, evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode

		if(charCode == 27 && prev_ring_clerk_id && prev_ring_clerk_text)
		{
			jQuery('#ring_clerk'+index).val(prev_ring_clerk_text);
			jQuery('#ring_clerk_id'+index).val(prev_ring_clerk_id);
			prev_ring_clerk_set = false;
			return true;
		}

		if(charCode == 16 || charCode == 17 || charCode == 18 || charCode == 20 ||
				charCode == 9 || charCode == 13 || charCode == 93 || charCode == 36 ||
				charCode == 33 || charCode == 34 || charCode == 35 || (charCode >= 112 && charCode <= 123) ||
				(charCode == 65 && evt.ctrlKey === true))
			return true;

		if(prev_ring_clerk_set)
			return true;

		prev_ring_clerk_text = jQuery('#ring_clerk'+index).val();
		prev_ring_clerk_id = jQuery('#ring_clerk_id'+index).val();
		jQuery('#ring_clerk_id'+index).val('');
		prev_ring_clerk_set = true;

		return true;
	}

	function clear_prev_ring_clerk()
	{
		prev_ring_clerk_text = '';
		prev_ring_clerk_id = '';
		prev_ring_clerk_set = false;
	}

    function addring()
    {
        var c = jQuery('#count_rings').val();
        var d = parseInt(c)+parseInt(1);

        var tbody = jQuery('#rings');

        var row = document.createElement('div');
        row.id = 'ring'+c;
        row.className = 'rings';
        tbody.append(row);
        
        var is_alternative = (jQuery('#show_format option:selected').text() == 'Alternative')?1:0;
        var show_format = jQuery('#show_format option:selected').text();

        var start_date = jQuery('#show_start_date').val();
        var end_date = jQuery('#show_end_date').val();

        jQuery.ajax({
            url: 'index.php?option=com_toes&task=show.getRingDayDropdown',
            data:'start_date='+start_date+'&end_date='+end_date+'&cnt='+c+'&show_format='+show_format,
            async: false,
            type: 'post',
        }).done(function(responseText){
            if(responseText != '')
            {
                var ringdata = '<div class="field-ringvalue ring_numbers">\n\
		                        	<div class="field-ringtext ring_numbers hidden-desktop">\n\
						                <?php echo JText::_('COM_TOES_SHOW_RING_NUMBER'); ?>\n\
						                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_NUMBER_HELP'); ?>">&nbsp;</span>\n\
						            </div>\n\
                                    <input name="ring_number[]" id="ring_number'+c+'" type="text" size="10" value="" >\n\
                                </div>\n\
                                <div class="field-ringvalue ring_show_day">\n\
						            <div class="field-ringtext ringdays-header hidden-desktop">\n\
						                <?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY'); ?>\n\
						                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY_HELP'); ?>">&nbsp;</span>\n\
						            </div>\n\
                                    '+responseText+'\n\
                                </div>\n\
                                <div class="field-ringvalue ringtimings" '+(is_alternative?'':'style="display:none"')+'>\n\
				 		            <div class="field-ringtext ringtimings-header hidden-desktop">\n\
						                <?php echo JText::_('COM_TOES_SHOW_RING_TIMING'); ?>\n\
						                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_TIMING_HELP'); ?>">&nbsp;</span>\n\
						            </div>\n\
                                    <select name="ring_timings[]" id="ring_timings'+c+'" '+(is_alternative?'':'style="display:none"')+'>\n\
                                        <?php
                                        foreach ($ringtimings as $rt) {
                                            echo '<option value="' . $rt->value . '">' . $rt->text . '</option>';
                                        }
                                        ?>\n\
                                    </select>\n\
                                </div>\n\
                                <div class="field-ringvalue ring_formats">\n\
						            <div class="field-ringtext ring_formats hidden-desktop">\n\
						                <?php echo JText::_('COM_TOES_SHOW_RING_FORMAT'); ?>\n\
						                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_FORMAT_HELP'); ?>">&nbsp;</span>\n\
						            </div>\n\
                                    <select name="ring_formats[]" id="ring_formats'+c+'" onchange="changeCongressVisibility('+c+',this.value);">\n\
                                        <?php
                                        foreach ($ringformats as $rs) {
                                            echo '<option value="' . $rs->value . '">' . $rs->text . '</option>';
                                        }
                                        ?>\n\
                                    </select>\n\
                                </div>\n\
                                <div class="field-ringvalue ring_judges">\n\
						             <div class="field-ringtext ring_judges hidden-desktop">\n\
						                <?php echo JText::_('COM_TOES_SHOW_RING_JUDGE'); ?>\n\
						                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_JUDGE_HELP'); ?>">&nbsp;</span>\n\
						            </div>\n\
                                    <input name="ring_judge[]" id="ring_judge'+c+'" type="text" size="10" value="" onkeydown="prev_judge_check('+c+', event);" onblur="clear_prev_judge();checkJudge('+c+');" >\n\
                                </div>\n\
                                <div class="field-ringvalue ring_clerks">\n\
						             <div class="field-ringtext ring_clerks hidden-desktop">\n\
						                <?php echo JText::_('COM_TOES_SHOW_RING_CLERK'); ?>\n\
						                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CLERK_HELP'); ?>">&nbsp;</span>\n\
						            </div>\n\
                                    <input name="ring_clerk[]" id="ring_clerk'+c+'" type="text" size="10" value="" onkeydown="prev_ring_clerk_check('+c+', event);" onblur="clear_prev_ring_clerk();checkRingClerk('+c+');" >\n\
                                </div>\n\
                                <div class="field-ringvalue ring_congress">\n\
						            <div class="field-ringtext hidden-desktop">\n\
						                <?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME'); ?>\n\
						                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME_HELP'); ?>">&nbsp;</span>\n\
						            </div>\n\
                                    <input style="visibility: hidden;" name="ring_congress_name[]" id="ring_congress_name'+c+'" type="text" size="10" value="" >\n\
                                    <a style="visibility: hidden;" id="ring_congress_name'+c+'_criteria" href="javascript:void(0);" onclick="define_filter_criteria('+c+',0)">\n\
                                        <i class="fa fa-edit"></i>\n\
                                    </a>\n\
                                </div>\n\
                                <span style="cursor:pointer;" onclick="removering('+c+');"><i style="margin-top: 12px;" class="fa fa-remove"></i></span>\n\
                                <input type="hidden" value="'+c+'" name="ring_index[]"/>\n\
                                <input type="hidden" value="0" id="ring_id'+c+'" name="ring_id[]"/>\n\
                                <input type="hidden" id="ring_judge_id'+c+'" name="ring_judge_id[]" value=""/>\n\
                                <input type="hidden" id="ring_clerk_id'+c+'" name="ring_clerk_id[]" value=""/>\n\
                                <div class="clr"></div>'; 
                	jQuery('#ring'+c).html(ringdata);	
                	jQuery('#ring_show_day_'+c+'').select2();
                	jQuery('#ring_timings'+c+'').select2();
                	jQuery('#ring_formats'+c+'').select2();
                }	
            });

            set_judges(c);
			set_ring_clerks(c);
			isUsingTICAapp();

            jQuery('#count_rings').val(d);
        }

        var DateDiff = {
            inDays: function(d1, d2) {
                var t2 = d2.getTime();
                var t1 = d1.getTime();

                return parseInt((t2-t1)/(24*3600*1000));
            }
        }
        
        function toggle_cat_limit_inputs()
        {
            if(!jQuery('#show_format').val())
                return;
            
            var start_date = jQuery('#show_start_date').val();
            var end_date = jQuery('#show_end_date').val();
            if(!start_date || !end_date)return;

            var d1 = parseDate(start_date);
            var d2 = parseDate(end_date);
            var diff = DateDiff.inDays(d1, d2);
            
            var weekday=new Array(7);
            weekday[0]="Sun";
            weekday[1]="Mon";
            weekday[2]="Tue";
            weekday[3]="Wed";
            weekday[4]="Thu";
            weekday[5]="Fri";
            weekday[6]="Sat";
            
            var dflt = 500;
            if(jQuery('#show_format option:selected').text() == 'Continuous')
            {
                diff = 0;
                dflt = 500;
            }
            else if(jQuery('#show_format option:selected').text() == 'Alternative')
            {
                dflt = 125;
            }
            else
            {
                dflt = 250;
            }


            if(diff > 2)
                diff = 2;

            var myDate = d1;
            myDate.setDate(myDate.getDate()-1);
            for(var i=0;i<3;i++)
            {
                if(i<=diff)
                {
                    if(jQuery('#show_day_cat_limit_'+i).val() == 0)
                    {
                        jQuery('#show_day_cat_limit_'+i).val(dflt)
                    }
                    myDate.setDate(myDate.getDate()+1);
                    jQuery('#cat_limit_lable_'+i).text(weekday[myDate.getDay()]);
                    jQuery('#cat_limit_span_'+i).show();
                }
                else
                {
                    jQuery('#cat_limit_span_'+i).hide();
                }
            }            
        }

        function changeRingdays()
        {
            var start_date = jQuery('#show_start_date').val();
            var end_date = jQuery('#show_end_date').val();

            toggle_cat_limit_inputs();

            jQuery('div.ring_show_day').each(function(){

				var ring_show_day_select_div = jQuery(this);
                var id = ring_show_day_select_div.find('select').attr('id');
                if(id) {
	                id = id.split('ring_show_day_');
	                var c = id[1];
	
	                jQuery.ajax({
	                    url: 'index.php?option=com_toes&task=show.getRingDayDropdown',
	                    data:'start_date='+start_date+'&end_date='+end_date+'&org_start_date='+prev_start_date+'&show_day_date='+jQuery(this).find('select').val()+'&cnt='+c,
	                    type: 'post',
	                }).done(function(responseText){
	                    if(responseText != '')
	                    {
	                        ring_show_day_select_div.html(responseText);
					    	jQuery('#ring_show_day_'+c).select2();
	                    }	
	                });
                }
            });
        }

        function removering(id)
        {	
            jQuery('#rings #ring'+id).remove();
        }	

		// google maps api
		var latitude;
		var longitude;
		var placeSearch, autocomplete;
		var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        administrative_area_level_2: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };
      
      function initAutocomplete() {
      
			autocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */(document.getElementById('search_address')),
				{types: ['geocode']});
			autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
			var place = autocomplete.getPlace();
			console.log(place);
			
			document.getElementById('venue_name').value = '';
			document.getElementById('lat').value = '';
			document.getElementById('lng').value = '';
		   if(addressType = 'name') 
			{
				var placename = place.name;
				if(placename)
				{
					document.getElementById('venue_name').value = placename;
				}
			}
		   if(addressType = 'lat') 
			{
				var lat = place.geometry.location.lat();
				if(lat)
				{
					document.getElementById('lat').value = lat;
				}
			}
		   if(addressType = 'lng') 
			{
				var lng = place.geometry.location.lng();
				if(lng)
				{
					document.getElementById('lng').value = lng;
				}
			}
			document.getElementById('address_line_1').value = '';
			document.getElementById('address_line_2').value = '';
			document.getElementById('address_line_3').value = '';
			document.getElementById('address_city_name').value = '';
			document.getElementById('address_country_name').value = '';
			document.getElementById('address_state_name').value = '';
			document.getElementById('address_zip_code').value = '';
		  
			for (var i = 0; i < place.address_components.length; i++) {
				
			  var addressType = place.address_components[i].types[0];			
				if(addressType == 'street_number') 
				{
						var address1 = place.address_components[i].short_name;
						
						if(address1)
						{
							document.getElementById('address_line_1').value = address1;
						}	
				}
				if(addressType == 'route') 
				{
						var route = place.address_components[i].long_name;
						if(!address1)
						{
							address1 = '';
						}
						var address = address1 + ' ' + route;
						if(address)
						{
							document.getElementById('address_line_1').value = address;
						}	
				}
				if(addressType == 'locality') 
				{
						var city = place.address_components[i].short_name;
				
						document.getElementById('address_city_name').value = city;
				}
				if(addressType == 'sublocality_level_1') 
				{
						var address2 = place.address_components[i].short_name;
				
						document.getElementById('address_line_2').value = address2;
				}
				if(addressType == 'sublocality_level_2') 
				{
						var address3 = place.address_components[i].short_name;
				
						document.getElementById('address_line_3').value = address3;
				}
				if(addressType == 'country') 
				{
						var country = place.address_components[i].long_name;
				
						document.getElementById('address_country_name').value = country;
				}
				if(addressType == 'administrative_area_level_1') 
				{
						var state = place.address_components[i].long_name;
				
						document.getElementById('address_state_name').value = state;
				}
				if(addressType == 'postal_code') 
				{
						var zipcode = place.address_components[i].long_name;
				
						document.getElementById('address_zip_code').value = zipcode;
				}
			}
			edit_venue_on = !edit_venue_on;
			 if(edit_venue_on)
			 jQuery('#edit_venue_btn').addClass('button-pressed');	
			 else
			 jQuery('#edit_venue_btn').removeClass('button-pressed');
			 jQuery('#search_address').attr('disabled',!edit_venue_on);	
			 jQuery('#edit_venue').attr('checked',true);
		
      }
      function geolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
         
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
      }


        function autofillvenuedata()
        {
            var venue = jQuery('#venue_name').val();
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=show.set_venuedata&venue='+venue+'&tmpl=component',
                type: 'post',
                onRequest: function(){},
            }).done(function(responseText){
                if(responseText != '')
                {
                    var n= responseText.split('|');

                    jQuery("#address_line_1").val(n[0]);
                    jQuery("#address_line_2").val(n[1]);
                    jQuery("#address_line_3").val(n[2]);
                    jQuery("#address_zip_code").val(n[3]);
                    jQuery("#postal_code").val(n[3]);
                    jQuery("#address_city_name").val(n[4]);
                    jQuery("#address_city").val(n[5]);
                    jQuery("#address_state_name").val(n[6]);
                    jQuery("#administrative_area_level_1").val(n[6]);
                    jQuery("#address_state").val(n[7]);
                    jQuery("#address_country_name").val(n[8]);
                    jQuery("#address_country").val(n[9]);
                    jQuery("#lat").val(n[10]);
                    jQuery("#lng").val(n[11]);

					/*
                    jQuery("#address_line_1").prop('readonly','readonly');
                    jQuery("#address_line_2").prop('readonly','readonly');
                    jQuery("#address_line_3").prop('readonly','readonly');
                    jQuery("#address_zip_code").prop('readonly','readonly');
                    jQuery("#address_city_name").prop('readonly','readonly');
                    jQuery("#address_state_name").prop('readonly','readonly');
                    jQuery("#address_country_name").prop('readonly','readonly');
                    */
                }	
                else
                {
					jQuery("#address_line_1").val('');
                    jQuery("#address_line_2").val('');
                    jQuery("#address_line_3").val('');
                    jQuery("#address_zip_code").val('');
                    jQuery("#postal_code").val('');
                    jQuery("#address_city_name").val('');
                    jQuery("#address_city").val('');
                    jQuery("#address_state_name").val('');
                    jQuery("#administrative_area_level_1").val('');
                    jQuery("#address_state").val('');
                    jQuery("#address_country_name").val('');
                    jQuery("#address_country").val('');
                    jQuery("#lat").val('');
                    jQuery("#lng").val('');
					/*
                    jQuery("#address_line_1").prop('readonly','');
                    jQuery("#address_line_2").prop('readonly','');
                    jQuery("#address_line_3").prop('readonly','');
                    jQuery("#address_zip_code").prop('readonly','');
                    jQuery("#address_city_name").prop('readonly','');
                    jQuery("#address_state_name").prop('readonly','');
                    jQuery("#address_country_name").prop('readonly','');
                    jQuery("#country").prop('readonly','');
                    */
                }
            });
        }

        function setuserofficial(x)
        {		
            var crosscheckusername = jQuery('#crosscheckusername').val();	
            if(crosscheckusername != '')
            {	
                var c = jQuery('#countusername_'+x).val();
                var d = parseInt(c)+parseInt(1);

                var curval=jQuery('#crosscheckusername').val();	
                if(curval=='' || curval=='Type to search user')
                    return;	

                flag = false;
                jQuery('input[name^=username_'+x+']').each(function(){
                    if(curval == jQuery(this).val())
                    {
                        jbox_notice("<?php echo JText::_('USER_IS_ALREADY_PRESENT') ?>",'green');
                        flag = true;
                    }
                });

                if(flag == false)
                {
                    var username = jQuery('#crosscheckusername').val().split('(');
                    username = username[1].split(')');
                    username = username[0];

                    var str='<label class="addedusername_'+x+'" id="subusername_'+x+''+c+'">'+curval+'<span class="username_'+x+'Remove" onclick="removeusername('+c+','+x+');">x</span>'+'</label>';

                    jQuery('#username_'+x+'place').html( jQuery('#username_'+x+'place').html() + str);

                    jQuery('#innerusername_'+x).append('<input type="hidden" value="'+username+'" name="username_'+x+'[]" id="username_'+x+'data'+c+'"/>');
                    jQuery('#countusername_'+x).val(d);
                    jQuery('#username').val('Type to search user');
                    jQuery('#username').css('color', "#999");
                }
            }
            else
            {
                jbox_alert("<?php echo JText::_('COM_TOES_NONREGISTERED_SHOWUSER_ALERT'); ?>");
            }

            jQuery('#crosscheckusername').val('');	
        }

        function removeusername(id,x)
        {
            jQuery('#innerusername_'+x+' #username_'+x+'data'+id).remove();

            jQuery('#username_'+x+'place #subusername_'+x+''+id).remove();
        }

        // parse a date in yyyy-mm-dd format
        function parseDate(input) {
		//alert(input );
		  if(input && input!= 'YYYY-MM-DD'){	
    	  if(input.length) {
            var parts = input.match(/(\d+)/g);
            // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
            if(parts.length > 0)
            return new Date(parts[0], parts[1]-1, parts[2]); // months are 0-based
            else
            return false;
          } else {
          	return false;
          }
		  }else
		  return false;
        }    

        jQuery(document).ready(function(){
            document.formvalidator.setHandler('date', function (value) {
                var exp =/^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/;
                return new RegExp(exp).test(value);
            });

			document.formvalidator.setHandler('startdate', function (value) {
                var exp =/^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/;
				/*
                if(!is_date_change_approved && org_start_date != value){
                    if(org_start_date == '0000-00-00' || confirm(notification_messsage)) {
                        is_date_change_approved = true;
                    }
                    else
                        jQuery('#show_start_date').val(org_start_date);
                }
                */
                console.log('is_date_change_approved startdate:'+is_date_change_approved);
                if(!is_date_change_approved && org_start_date != value){
                new jBox('Confirm',{
					content: notification_messsage + "<br/>"+"Do you want to change the show date?",
					width: '400px',
					closeOnClick: 'box',
					cancelButton : NO_BUTTON,
					confirmButton: YES_BUTTON,
					cancel: function(){
						jQuery('#show_start_date').val(org_start_date);
					},
					confirm: function() {
						is_date_change_approved = true;						 
					}
				}).open();
				}
                
                
				if(jQuery('#show_start_date').val() && jQuery('#show_start_date').val() != 'YYYY-MM-DD'){
                var date = parseDate(jQuery('#show_start_date').val());
                jQuery('#detailed_start_date').html(date.toDateString());
				}
                return new RegExp(exp).test(value);
            });

            document.formvalidator.setHandler('enddate', function (value) {
                var exp =/^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/;

				/*
                if(!is_date_change_approved && org_end_date != value){
                    if(org_end_date == '0000-00-00' || confirm(notification_messsage)) {
                        is_date_change_approved = true;
                    }
                    else
                        jQuery('#show_end_date').val(org_end_date);
                }
                */
                console.log('is_date_change_approved enddate:'+is_date_change_approved);
                
                if(!is_date_change_approved && org_end_date != value){
                new jBox('Confirm',{
					content: notification_messsage + "<br/>"+"Do you want to change the show date?",
					width: '400px',
					closeOnClick: 'box',
					cancelButton : NO_BUTTON,
					confirmButton: YES_BUTTON,
					cancel: function(){
						jQuery('#show_start_date').val(org_end_date);
					},
					confirm: function() {
						is_date_change_approved = true;						 
					}
				}).open();
				}
				 

                var show_start_date = jQuery('#show_start_date').val().split('-');
                var show_end_date = value.split('-');

                var start_date = show_start_date[0]+show_start_date[1]+show_start_date[2];
                var end_date = show_end_date[0]+show_end_date[1]+show_end_date[2];
                
				if(jQuery('#show_end_date').val() && jQuery('#show_end_date').val() != 'YYYY-MM-DD' ){
                var date = parseDate(jQuery('#show_end_date').val());
                jQuery('#detailed_end_date').html(date.toDateString());
				}
                if(new RegExp(exp).test(value))
                {
                    if(!start_date || start_date > end_date)
                    {
                        return false;
                    }
                    return true;
                }
                else
                    return false;
            });
        });

        function cancelForm(form)
        {
            form.task.value = '';
            form.submit();
        }    
        
        function validate_showform_new(form,action){
			
			   if(venue_changed && venue.address_id != '0'){
				/*
				var confirm_venue_changed = confirm('You have changed the venue information without selecting a new location from the Search Address field. Is the location you chose previously still valid?');
				if(!confirm_venue_changed){
					jQuery('.venue_field').val('');
					jQuery('#search_address').focus();
					return;					
				}
				*/
				new jBox('Confirm',{
		        content: 'You have changed the venue information without selecting a new location from the Search Address field. Is the location you chose previously still valid?',
		        width: '400px',
		        cancelButton : NO_BUTTON,
		        confirmButton: YES_BUTTON,
		        cancel: function(){
					jQuery('#search_address').val('');
					jQuery('.venue_field').val('');
					jQuery('#search_address').focus();			
					return;				 				
				},
		        confirm: function() {
			        validate_showform(form,action);
				}
				}).open();
			}else
				validate_showform(form,action);
			
			
			
		}

        function validate_showform(form,action)
        {
            var errMsg = '';
             
			
            var validator = document.formvalidator;
            var still_required = '';

            if(!validator.validate('#club'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_CLUB_NAME'); ?>";
            }
            if(!validator.validate('#show_start_date'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_DATES_START'); ?>";
            }
            if(!validator.validate('#show_end_date'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_DATES_END'); ?>";
            }
            if(!validator.validate('#show_format'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_FORMAT_NAME'); ?>";
            }
            if(!validator.validate('#show_currency_used'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_CURRENCY_USED'); ?>";
            }
             
            if(!validator.validate('#venue_name'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_VENUE_NAME'); ?>";
            }
            /**/
            
            if(!show_id && !validator.validate('#search_address'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_SEARCH_ADDRESS'); ?>";
            }
            if(!jQuery('#lat').val() || !jQuery('#lng').val() )
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_SEARCH_ADDRESS_LAT_LNG_NOT_SET'); ?>";
            }   
            //
                
            /**/
            if(!validator.validate('#address_line_1'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_VENUE_ADDRESS1'); ?>";
            }
             
            if(!validator.validate('#address_city'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_VENUE_CITY'); ?>";
            }
            if(!validator.validate('#address_zip_code'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_VENUE_ZIP'); ?>";
            }
            if(!validator.validate('#address_country'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_VENUE_COUNTRY'); ?>";
            }
            
            if(jQuery('#show_cost_per_entry').length && !validator.validate('#show_cost_per_entry'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_COST_PER_ENTRY'); ?>";
            }
            if(jQuery('#show_total_cost').length && !validator.validate('#show_total_cost'))
            {
                still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_TOTAL_COST'); ?>";
            }
            
            if(jQuery('#show_use_club_entry_clerk_address').is(':checked'))
            {
                if(!validator.validate('#show_email_address_entry_clerk'))
                {
                    still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_NO_VALID_CLUB_ENTRY_CLERK_EMAIL_ADDRESS'); ?>";
                }
            }
            
            if(jQuery('#show_use_club_show_manager_address').is(':checked'))
            {
                if(!validator.validate('#show_email_address_show_manager'))
                {
                    still_required += "<br/>* <?php echo JText::_('COM_TOES_SHOW_NO_VALID_CLUB_SHOW_MANAGER_EMAIL_ADDRESS'); ?>";
                }
            }

			if(jQuery('#request_show_license').is(':checked'))
            {
				var first_show = (jQuery('#rsl_first_show').is(':checked'))?1:0;
			    var ab_rings = jQuery('#rsl_ab_rings').val().toString();
			    var sp_rings = jQuery('#rsl_sp_rings').val().toString();
			    var congress_rings = jQuery('#rsl_congress_rings').val().toString();

				var name = jQuery('#rsl_ship_name').val().toString();
			    var address = jQuery('#rsl_ship_address').val().toString();
			    var city = jQuery('#rsl_ship_city').val().toString();
			    var zip = jQuery('#rsl_ship_zip').val().toString();
			    var state = jQuery('#rsl_ship_state').val().toString();
			    var country = jQuery('#rsl_ship_country').val().toString();
			    var include_show_supplies = (jQuery('#rsl_include_show_supplies').is(':checked'))?1:0;

			    var insurance_info = jQuery('#rsl_insurance_info').val().toString();
			    var total_fee = jQuery('#rsl_total_fee').val().toString();
			    var license_fee = jQuery('#rsl_license_fee').val().toString();
			    var anual_award_fee = jQuery('#rsl_anual_award_fee').val().toString();
			    
				if(!validNumber(ab_rings))
					errMsg += '<?php echo JText::_('COM_TOES_RSL_INVALID_AB_RINGS');?><br/>';
				if(!validNumber(sp_rings))
					errMsg += '<?php echo JText::_('COM_TOES_RSL_INVALID_SP_RINGS');?><br/>';
				if(!validNumber(congress_rings))
					errMsg += '<?php echo JText::_('COM_TOES_RSL_INVALID_CONGRESS_RINGS');?><br/>';

				if(include_show_supplies)
				{
				    if(name.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_NAME');?>';
				    if(address.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_ADDRESS');?>';
				    if(city.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_CITY');?>';
				    if(zip.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_ZIP');?>';
				    if(country.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_COUNTRY');?>';
				}

				if(!validNumber(total_fee))
					errMsg += '<?php echo JText::_('COM_TOES_RSL_INVALID_TOTAL_FEE');?><br/>';
				if(!validNumber(license_fee))
					errMsg += '<?php echo JText::_('COM_TOES_RSL_INVALID_LICENSE_FEE');?><br/>';
				if(!validNumber(anual_award_fee))
					errMsg += '<?php echo JText::_('COM_TOES_RSL_INVALID_ANUAL_AWARD_FEE');?><br/>';
			}

            if(still_required)
                errMsg += "<?php echo JText::_('COM_TOES_REQUIRED_FIELD'); ?>" + still_required + "<br/><br/>";

            
            var regex = new RegExp("^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|www\\.){1}([0-9A-Za-z-\\.@:%_\+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?");

            if(jQuery('#show_flyer').val() && !regex.test(jQuery('#show_flyer').val()))
            {
                errMsg += "<?php echo JText::_('COM_TOES_SHOW_FLYER_ERROR_HELP'); ?><br/><br/>";
            }

            if( validator.validate('#show_start_date') && validator.validate('#show_end_date') )
            {
                var start_time = parseDate(jQuery('#show_start_date').val()).getTime();
                var end_time = parseDate(jQuery('#show_end_date').val()).getTime();

                if( parseInt((end_time-start_time)/(24*3600*1000)) > 3 )
                {
                    errMsg += "<?php echo JText::_('COM_TOES_SHOW_DAYS_SHOULD_BE_3_OR_LESS'); ?><br/><br/>";
                }
                
                var d1 = parseDate(jQuery('#show_start_date').val());
                var d2 = parseDate(jQuery('#show_end_date').val());
                var diff = DateDiff.inDays(d1, d2);

                if(jQuery('#show_format option:selected').text() == 'Continuous')
                    diff = 0;

                for(var i=0;i<3;i++)
                {
                    if(i<=diff)
                    {
                        if(isNaN(jQuery('#show_day_cat_limit_'+i).val()))
                        {
                            errMsg += "<?php echo JText::_('COM_TOES_CAT_LIMIT_SHOULD_BE_NUMBER'); ?> "+ (i+1) +"<br/><br/>";
                        }
                    }
                }
            }

            var show_days = new Array();
            var show_day_rings = new Array();
            var judges = new Array();
            var show_day_judges = new Array();
            var total_rings = 0;

            var is_alternative = (jQuery('#show_format option:selected').text() == 'Alternative')?1:0;
            var is_continuous = (jQuery('#show_format option:selected').text() == 'Continuous')?1:0;
            
            if(is_continuous)
            {
                jQuery('input[name^=ring_index]').each(function(){
                    var i = jQuery(this).val();
                   jQuery('#ring_show_day_'+i).val(jQuery('#show_start_date').val());
                });
            }
            
            var ring_number_error = '';

            jQuery('input[name^=ring_index]').each(function(){
                var i = jQuery(this).val();
                if(jQuery('#ring_show_day_'+i).val() && jQuery('#ring_formats'+i).val() && jQuery('#ring_judge'+i).val())
                {
                    if(isNaN(jQuery('#ring_number'+i).val()))
                        ring_number_error = true;
                    
                    if(show_days.indexOf(jQuery('#ring_show_day_'+i).val()) == -1)
                        show_days.push(jQuery('#ring_show_day_'+i).val());

                    if(jQuery('#ring_formats'+i).val() != 3)
                    {
                        if(jQuery('#ring_show_day_'+i).val() in show_day_rings)
                            show_day_rings[jQuery('#ring_show_day_'+i).val()] = show_day_rings[jQuery('#ring_show_day_'+i).val()] + 1;
                        else 
                            show_day_rings[jQuery('#ring_show_day_'+i).val()] = 1;

                        if(jQuery('#ring_show_day_'+i).val() in show_day_judges)
                        {
                            if(jQuery('#ring_judge_id'+i).val() in show_day_judges[jQuery('#ring_show_day_'+i).val()])
                                show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge_id'+i).val()] = show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge_id'+i).val()] + 1;
                            else 
                                show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge_id'+i).val()] = 1;
                        }
                        else
                        {
                            show_day_judges[jQuery('#ring_show_day_'+i).val()] = new Array();
                            show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge_id'+i).val()] = 1;
                        }

                        if(jQuery('#ring_judge_id'+i).val() in judges)
                            judges[jQuery('#ring_judge_id'+i).val()] = judges[jQuery('#ring_judge_id'+i).val()] + 1;
                        else 
                            judges[jQuery('#ring_judge_id'+i).val()] = 1;

                        total_rings++;
                    }
                }
            });
            
            if(ring_number_error)
                errMsg += "<?php echo JText::_('COM_TOES_SHOW_NUMERIC_RING_NUMBERS'); ?><br/><br/>";

            if(total_rings > 20)
            {
                errMsg += "<?php echo sprintf(JText::_('COM_TOES_SHOW_SHOULD_HAS_RINGS_MAX'), '20'); ?><br/><br/>";
            }

            for (var day in show_day_rings) {
                if(show_days.indexOf(day) != -1)
                {
					if(!is_continuous)
					{
						if(is_alternative || (jQuery('#show_start_date').val() == jQuery('#show_end_date').val()))
						{
							if(show_day_rings[day] > 10) {
								errMsg += "<?php echo sprintf(JText::_('COM_TOES_SHOW_SHOULD_HAS_RINGS_MAX_PER_DAY'), '10'); ?><br/><br/>";
								break;
							}
						}
						else if(show_day_rings[day] > 8)
						{
							errMsg += "<?php echo sprintf(JText::_('COM_TOES_SHOW_SHOULD_HAS_RINGS_MAX_PER_DAY'), '8'); ?><br/><br/>";
							break;
						}
					}
				}
            }

            for (var day in show_day_judges) {
                if(show_days.indexOf(day) != -1)
                {
                    for (var judge_id in show_day_judges[day]) {
                        if(!isNaN(judge_id))
                        {
                            if(judge_id != tba_judge_id && show_day_judges[day][judge_id] > 2)
                            {
                                errMsg += "<?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_TWO_RINGS_MAX'); ?><br/><br/>";
                                break;
                            }
                        }
                    }
                }
            }

            for (var judge_id in judges) {
                if(!isNaN(judge_id))
                {
                    if(judge_id != tba_judge_id && show_days.length == 1 && judges[judge_id] > 2)
                    {
                        errMsg += "<?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_TWO_RINGS_MAX'); ?><br/><br/>";
                        break;
                    }
                    if(judge_id != tba_judge_id && show_days.length == 2 && judges[judge_id] > 3)
                    {
                        errMsg += "<?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_THREE_RINGS_MAX'); ?><br/><br/>";
                        break;
                    }
                    if(judge_id != tba_judge_id && show_days.length == 3 && judges[judge_id] > 4)
                    {
                        errMsg += "<?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_FOUR_RINGS_MAX'); ?><br/><br/>";
                        break;
                    }
                }
            }

            var users = new Array();
            <?php foreach ($this->showofficialtypes as $so): ?>
                typeid = <?php echo $so->show_official_type_id; ?>;
                jQuery('input[name^=username_'+typeid+']').each(function(){
                    users.push(jQuery(this).val());
                });
            <?php endforeach; ?>
        
        if(users.length == 0)
        {
            errMsg += "<?php echo JText::_('COM_TOES_NEED_ATLEAST_ONE_SHOW_OFFICIAL'); ?><br/><br/>";
        }

        if(errMsg)
        {
            jbox_alert(errMsg);
            return false;
        }
        
         
        
        var congress_filter_criteria_error = '';
        jQuery('input[name^=ring_index]').each(function(){
            var i = jQuery(this).val();
            if(jQuery('#ring_show_day_'+i).val() && jQuery('#ring_formats'+i).val() && jQuery('#ring_judge'+i).val())
            {
                if(jQuery('#ring_formats'+i).val() == 3)
                {
                    jQuery.ajax({
                        url: 'index.php?option=com_toes&task=entry.validateCriteria',
                        data: 'index='+i+'&ring_id='+jQuery('#ring_id'+i).val(),
                        type: 'post',
                        async: false,
                    }).done(function(responseText){
                        if(responseText != 1)
                        {
                            congress_filter_criteria_error += '<?php echo JText::_('COM_TOES_SPECIFY_FILTER_CRITERIA_FOR_CONGRESS')?>'+jQuery('#ring_congress_name'+i).val()+'<br/>';
                        }
                    });
                }
            }
        });
        
        if(congress_filter_criteria_error)
        {
            jbox_alert(congress_filter_criteria_error);
            return false;
        }
        
        var congress_rings = new Array();
        var congress_names = new Array();
        jQuery('input[name^=ring_index]').each(function(){
            var i = jQuery(this).val();
            if(jQuery('#ring_show_day_'+i).val() && jQuery('#ring_formats'+i).val() && jQuery('#ring_judge'+i).val())
            {
                if(jQuery('#ring_formats'+i).val() == 3)
                {
                    var ring = new Array();
                    ring.push(i);
                    ring.push(jQuery('#ring_id'+i).val());
                    ring.push(jQuery('#ring_congress_name'+i).val());
                    
                    if(congress_names.indexOf(jQuery('#ring_congress_name'+i).val()) == -1)
                        congress_names.push(jQuery('#ring_congress_name'+i).val());
                    
                    congress_rings.push(ring);
                }
            }
        });        
        
        if(congress_rings.length != congress_names.length)
        {
            var congress_filter_match_criteria_error = '';
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.matchCriteriaforsameCongress',
                data: 'rings='+congress_rings,
                type: 'post',
                async: false,
            }).done(function(responseText){
                if(responseText != 1)
                {
                    congress_filter_match_criteria_error += '<?php echo JText::_('COM_TOES_SPECIFY_SAME_FILTER_CRITERIA_FOR_CONGRESS')?><br/>';
                }
            });
            if(congress_filter_match_criteria_error)
            {
                jbox_alert(congress_filter_match_criteria_error);
                return false;
            }
        }

        var judges = new Array();
        var show_day_judges = new Array();
        var warningMsg = '';
        
        jQuery('input[name^=ring_index]').each(function(){
            var i = jQuery(this).val();
            if(jQuery('#ring_show_day_'+i).val() && jQuery('#ring_formats'+i).val() && jQuery('#ring_judge'+i).val())
            {
                if(jQuery('#ring_show_day_'+i).val() in show_day_judges)
                {
                    if(jQuery('#ring_judge'+i).val() in show_day_judges[jQuery('#ring_show_day_'+i).val()])
                        show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge'+i).val()] = show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge'+i).val()] + 1;
                    else 
                        show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge'+i).val()] = 1;
                }
                else
                {
                    show_day_judges[jQuery('#ring_show_day_'+i).val()] = new Array();
                    show_day_judges[jQuery('#ring_show_day_'+i).val()][jQuery('#ring_judge'+i).val()] = 1;
                }

                if(jQuery('#ring_judge'+i).val() in judges)
                    judges[jQuery('#ring_judge'+i).val()] = judges[jQuery('#ring_judge'+i).val()] + 1;
                else 
                    judges[jQuery('#ring_judge'+i).val()] = 1;
            }
        });

        for (var day in show_day_judges) {
            if(show_days.indexOf(day) != -1)
            {
                for (var judge_id in show_day_judges[day]) {
                    if(!isNaN(judge_id))
                    {
                        if(judge_id != tba_judge_id && show_day_judges[day][judge_id] > 2)
                        {
                            warningMsg += "<br/><br/><?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_TWO_RINGS_MAX_WARNING'); ?>";
                            break;
                        }
                    }
                }
            }
        }
        
        for (var judge_id in judges) {
            if(!isNaN(judge_id))
            {
                if(judge_id != tba_judge_id && show_days.length === 1 && judges[judge_id] > 2)
                {
                    warningMsg += "<br/><br/><?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_TWO_RINGS_MAX_WARNING'); ?>";
                    break;
                }
                if(judge_id != tba_judge_id && show_days.length === 2 && judges[judge_id] > 3)
                {
                    warningMsg += "<br/><br/><?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_THREE_RINGS_MAX_WARNING'); ?>";
                    break;
                }
                if(judge_id != tba_judge_id && show_days.length === 3 && judges[judge_id] > 4)
                {
                    warningMsg += "<br/><br/><?php echo JText::_('COM_TOES_SHOW_JUDGE_SHOULD_HAS_FOUR_RINGS_MAX_WARNING'); ?>";
                    break;
                }
            }
        }
        
        if(warningMsg)
        {
			new jBox('Confirm',{
		        content: warningMsg,
		        width: '400px',
		        cancelButton : NO_BUTTON,
		        confirmButton: YES_BUTTON,
		        confirm: function() {
			        form.submit();
			}
            }).open();
        }
        else
		{
			if(action == 1)
				jQuery('#return_on_page').val('1');
			else
				jQuery('#return_on_page').val('0');
            //form.submit();
		}
		// conflict show 
		<?php
			$app = JFactory::getApplication();
			
			$lat = $app->input->get('lat');
		?>
		var str = jQuery('#adminForm').serialize();
	
		jQuery.ajax({
			method: "POST",
			url: "index.php?option=com_toes&task=show.checkradiusresultshow&tmpl=component",	
			data: {data:str},
			success:function(data){
					//console.log(data);
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
									
									var frm = document.adminForm; 
									frm.submit();
							
							}
							
							}).open();
						}
					}
					else
					{
						form.submit();
					}
					
		}	
		});
    }

    function checkFormat()
    {
        if(jQuery('#show_format option:selected').text() == 'Alternative')
        {
            jQuery('.ringdays-header').show();
            jQuery('.ringtimings-header').show();
            jQuery(".ring_show_day").show();
            jQuery(".ringtimings").show();
			
			jQuery("#show_use_waiting_list").prop('checked',0);
			jQuery("#show_use_waiting_list").prop('disabled',1);
        }
        else if(jQuery('#show_format option:selected').text() == 'Continuous')
        {
            jQuery('.ringdays-header').hide();
            jQuery('.ringtimings-header').hide();
            jQuery(".ring_show_day").hide();
            jQuery(".ringtimings").hide();
			jQuery("#show_use_waiting_list").prop('disabled',0);
        }
        else
        {
            jQuery('.ringdays-header').show();
            jQuery('.ringtimings-header').hide();
            jQuery(".ring_show_day").show();
            jQuery(".ringtimings").hide();
			jQuery("#show_use_waiting_list").prop('disabled',0);
        }
        toggle_cat_limit_inputs();
    }    
</script>

<?php
$document = JFactory::getDocument();

$document->addScript('components/com_toes/assets/calendar/js/jscal2.js');
$document->addScript('components/com_toes/assets/calendar/js/unicode-letter.js');
$document->addScript('components/com_toes/assets/calendar/js/lang/nl.js');
$document->addScript('components/com_toes/assets/calendar/js/lang/en.js');

?>
<link rel="stylesheet" href="components/com_toes/assets/calendar/css/jscal2.css" />
<link rel="stylesheet" href="components/com_toes/assets/calendar/css/border-radius.css" />
<link rel="stylesheet" href="components/com_toes/assets/calendar/css/win2k/win2k.css" />

<style type="text/css">
    <?php
    foreach ($this->showofficialtypes as $so) {
        echo '
                .addedusername_' . $so->show_official_type_id . ' { background: none repeat scroll 0 0 #637193; border-radius: 4px 4px 4px 4px; clear: none; color: #FFFFFF; float: left; font-family: "arial"; font-size: 12px; font-weight: normal; line-height: 12px; list-style: none outside none; margin: 2px 1px 1px; padding: 2px 5px 4px; }
                .username_' . $so->show_official_type_id . 'Remove { color: #FFFFFF; cursor: pointer; font-size: 12px;  padding-left: 4px; }
                #username_' . $so->show_official_type_id . 'place { padding: 5px 6px;}
                ';
    }
    ?>
</style>
<div id="toes">
<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_toes&view=shows'); ?>" method="post" class="form-validate" enctype="multipart/form-data"> 

    <div class="action-buttons" >
        <input class="save button button-4" type="button" onclick="validate_showform(this.form,1)" value="<?php echo JText::_('COM_TOES_SAVE_AND_STAY_ON_EDIT_SHOW_FORM'); ?>" />
        <input class="save button button-4" type="button" onclick="validate_showform(this.form,2)" value="<?php echo JText::_('COM_TOES_SAVE_AND_BACK_TO_CALENDAR'); ?>" />
        <input class="cancel button button-red" type="button" name="cancel" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" onclick="cancelForm(this.form);"/>
    </div>

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_CLUB'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="club" id="club-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_CLUB_NAME'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <?php echo $this->clubslist; ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_CLUB_NAME_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="clr"></div>
        <br/>	
    </div>	

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_DATES'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_start_date" id="show_start_date-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_DATES_START'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" name="show_start_date" id="show_start_date" value="<?php echo isset($this->item->show_start_date) ? $this->item->show_start_date : 'YYYY-MM-DD' ?>" size="15"
                       class="required validate-startdate" <?php echo (!isset($this->item->show_start_date)) ? 'style="color: #999;"' : '' ?>
                       onfocus="cur_sd=this.value;if(this.value=='YYYY-MM-DD'){ this.value='';this.style.color='#000';}" 
                       onblur="if(cur_sd!=this.value && jQuery('#show_end_date').val());changeRingdays();if(this.value==''){this.value='YYYY-MM-DD';this.style.color='#999';}" 
                       />
				<i style="cursor:pointer;" class="fa fa-calendar" title="Click To Select Date" name="start_date_selector" id="start_date_selector"></i>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_DATES_START_HELP'); ?>">&nbsp;</span>
                <span id="detailed_start_date"></span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_end_date" id="show_end_date-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_DATES_END'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" name="show_end_date" id="show_end_date" value="<?php echo isset($this->item->show_end_date) ? $this->item->show_end_date : 'YYYY-MM-DD' ?>" size="15"
                       class="required validate-enddate" <?php echo (!isset($this->item->show_end_date)) ? 'style="color: #999;"' : '' ?>
                       onfocus="cur_ed=this.value;if(this.value=='YYYY-MM-DD'){ this.value='';this.style.color='#000';}" 
                       onblur="if(cur_ed!=this.value);changeRingdays();if(this.value==''){this.value='YYYY-MM-DD';this.style.color='#999';}" 
                       />
				<i style="cursor:pointer;" class="fa fa-calendar" title="Click To Select Date" name="end_date_selector" id="end_date_selector"></i>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_DATES_END_HELP'); ?>">&nbsp;</span>
                <span id="detailed_end_date"></span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_uses_toes" id="show_uses_toes-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_USES_TOES'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_uses_toes" name="show_uses_toes"  <?php if (@$this->item->show_uses_toes) echo 'checked="checked"'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_USES_TOES_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_uses_ticapp" id="show_uses_ticapp-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_USES_TICAPP'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_uses_ticapp" name="show_uses_ticapp"  <?php if (@$this->item->show_uses_ticapp) echo 'checked="checked"'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_USES_TICAPP_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_is_regional" id="show_is_regional-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_IS_REGIONAL_AWARDS_SHOW'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_is_regional" name="show_is_regional"  <?php if (@$this->item->show_is_regional) echo 'checked="checked"'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_IS_REGIONAL_AWARDS_SHOW_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_is_annual" id="show_is_annual-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_IS_ANNUAL_AWARDS_SHOW'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_is_annual" name="show_is_annual"  <?php if (@$this->item->show_is_annual) echo 'checked="checked"'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_IS_ANNUAL_AWARDS_SHOW_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        
        
        <div class="clr"></div>
        <br/>		
    </div>		

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_FORMAT'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_format" id="show_format-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_FORMAT_NAME'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <?php echo $this->showformatslist; ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_FORMAT_NAME_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_max_days" id="show_max_days-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_MAX_CAT_LIMIT'); ?>
                </label>
            </div>
            <div class="form-input" >
                <div style="text-align: center;float: left;padding: 5px;">
                    <span id="cat_limit_span_0" style="<?php echo isset($this->item->showdays[0])?'':'display:none;';?>">
                        <label id="cat_limit_lable_0"><?php echo isset($this->item->showdays[0])?(date('D',  strtotime($this->item->showdays[0]->show_day_date))):JText::_('DAY').' 1'; ?></label>
                        <br/>
                        <input style="width:40px !important;text-align: center;" type="text" name="show_day_cat_limit_0" id="show_day_cat_limit_0" value="<?php echo isset($this->item->showdays[0])?$this->item->showdays[0]->show_day_cat_limit:0; ?>" />
                    </span>
                </div>
                <div style="text-align: center;;float: left;padding: 5px;">
                    <span id="cat_limit_span_1" style="<?php echo isset($this->item->showdays[1])?'':'display:none;';?>">
                        <label id="cat_limit_lable_1"><?php echo isset($this->item->showdays[1])?(date('D',  strtotime($this->item->showdays[1]->show_day_date))):JText::_('DAY').' 2'; ?></label>
                        <br/>
                        <input style="width:40px !important;text-align: center;" type="text" name="show_day_cat_limit_1" id="show_day_cat_limit_1" value="<?php echo isset($this->item->showdays[1])?$this->item->showdays[1]->show_day_cat_limit:0; ?>" />
                    </span>
                </div>
                <div style="text-align: center;;float: left;padding: 5px;">
                    <span id="cat_limit_span_2" style="<?php echo isset($this->item->showdays[2])?'':'display:none;';?>">
                        <label id="cat_limit_lable_2"><?php echo isset($this->item->showdays[2])?(date('D',  strtotime($this->item->showdays[2]->show_day_date))):JText::_('DAY').' 3'; ?></label>
                        <br/>
                        <input style="width:40px !important;text-align: center;" type="text" name="show_day_cat_limit_2" id="show_day_cat_limit_2" value="<?php echo isset($this->item->showdays[2])?$this->item->showdays[2]->show_day_cat_limit:0; ?>" />
                    </span>
                </div>
            </div>
            <div class="clr"></div>
        </div>

        <div class="clr"></div>
        <br/>	
    </div>		

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_FLYER_SECTION_TITLE'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_motto" id="club-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_MOTTO'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" name="show_motto" id="show_motto" value="<?php echo isset($this->item->show_motto)?$this->item->show_motto:''; ?>" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_MOTTO_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="" for="show_comments" id="club-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_COMMENTS'); ?>
                </label>
            </div>
            <div class="form-input editor-div" >
                <?php /* <textarea style="width:450px;" rows="07" name="show_comments" id="show_comments"><?php echo @$this->item->show_comments; ?></textarea> */ ?>
                <?php echo $editor->display('show_comments', @$this->item->show_comments, '90%', '200', '10', '50', false); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_COMMENTS_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_display_counts" id="show_display_counts-lbl">
                    <?php echo JText::_('COM_TOES_DISPLAY_COUNTS_FOR_THIS_SHOW'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_display_counts" name="show_display_counts"  <?php if (@$this->item->show_display_counts) echo 'checked="checked"'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_DISPLAY_COUNTS_FOR_THIS_SHOW_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_use_waiting_list" id="show_use_waiting_list-lbl">
                    <?php echo JText::_('COM_TOES_USE_WAITING_LIST'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_use_waiting_list" name="show_use_waiting_list"  <?php if (@$this->item->show_format != 2 && @$this->item->show_use_waiting_list) echo 'checked="checked"'; ?> <?php if (@$this->item->show_format == 2) echo 'disabled=""'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_USE_WAITING_LIST_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_allow_exhibitor_cancellation" id="show_allow_exhibitor_cancellation-lbl">
                    <?php echo JText::_('COM_TOES_ALLOW_EXHIBITOR_CANCELLATIONS'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_allow_exhibitor_cancellation" name="show_allow_exhibitor_cancellation"  <?php if (@$this->item->show_allow_exhibitor_cancellation) echo 'checked="checked"'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_ALLOW_EXHIBITOR_CANCELLATIONS_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_bring_your_own_cages" id="show_bring_your_own_cages-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_BRING_YOUR_OWN_CAGES'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_bring_your_own_cages" name="show_bring_your_own_cages"  <?php if (@$this->item->show_bring_your_own_cages) echo 'checked="checked"'; ?> />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_BRING_YOUR_OWN_CAGES_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>        

        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_flyer" id="club-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_FLYER_FIELD_TITLE'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" name="show_flyer" id="show_flyer" value="<?php echo isset($this->item->show_flyer) ? $this->item->show_flyer : ''; ?>" />
                <span class="hasTip" title="<?php echo JText::_('COM_TOES_SHOW_FLYER_HELP'); ?>"><i class="fa fa-info-circle"></i></span>
                <?php if (isset($this->item->show_flyer)): ?>
                    <a href="<?php echo TOESHelper::addhttp($this->item->show_flyer); ?>" target="_blank">
                        <?php echo $this->item->show_flyer; ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="clr"></div>
        </div>
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label class="" for="show_extra_text_for_confirmation" id="club-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_EXTRA_INFORMATION'); ?>
                </label>
            </div>
            <div class="form-input editor-div" >
                <?php /* <textarea style="width:450px;" rows="07" name="show_extra_text_for_confirmation" id="show_extra_text_for_confirmation"><?php echo @$this->item->show_extra_text_for_confirmation; ?></textarea> */ ?>
                <?php echo $editor->display('show_extra_text_for_confirmation', @$this->item->show_extra_text_for_confirmation, '90%', '200', '10', '50', false); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_EXTRA_INFORMATION_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="show_currency_used" id="club-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_CURRENCY_USED'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input class="required" type="text" name="show_currency_used" id="show_currency_used" value="<?php echo isset($this->item->show_currency_used)?$this->item->show_currency_used:''; ?>" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_CURRENCY_USED_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="clr"></div>
        <br/>	
    </div>	

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_VENUE'); ?></div>
        </div>
        <div class="clr"></div>
    </div>
    


	<?php //if($this->item->show_id){?>
	
	<?php /*
    <div id="edit_venue_div">        
            <input type="checkbox" id="edit_venue" name="edit_venue" value="1"/>Edit Venue         
        <div class="clr"></div>
    </div>
    */ ?>
    <div id="edit_venue_div">        
            <input type="button" id="edit_venue_btn" class="button" value="Edit Venue"/>         
            <div style="display:none">
            <input type="checkbox" id="edit_venue" name="edit_venue" value="1"/>
            </div>      
        <div class="clr"></div>
    </div>
    <?php //} ?>

    <div class="seconouter">
        <br/>
        <div class="fieldbg" >
            <div class="form-label" >
                <label>                    
                    <span class=""></span>
                </label>
            </div>
            <div class="form-input" id="venue_map_div"> 
				<a  target="_blank" href="index.php?option=com_toes&view=show&layout=map&venue_id=<?php echo $this->item->show_venue;?>&tmpl=component">
					<i class="fa fa-info-circle"></i>&nbsp;View show venue location in Google Maps
				</a>		
				<div class="clr"></div>
			</div>
		</div>
        <br/>
		
		<div class="fieldbg" >
            <div class="form-label" >
                <label  for="search_address" id="search_address-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_SEARCH_ADDRESS'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" id="locationField" >
				<input value="<?php echo $this->item->venue_name?>" class="required venue_field" id="search_address"  placeholder="Enter your address" onFocus="geolocate()" type="text" aria-required="true" required="required"></input>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_SEARCH_ADDRESS_HELP'); ?>">&nbsp;</span>
                <a href="<?php echo JURI::root()?>resources/step-by-step-instructions?view=kb&kbartid=32" target="_blank">
                <i class="fa fa-info-circle"></i>&nbsp;How do I use this field?
                </a>
            </div>
            <div class="clr"></div>
        </div>

		
        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="venue_name" id="venue_name-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_NAME'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" size="30" class="required venue_field" value="<?php echo @$this->item->venue_name ?>" id="venue_name" name="venue_name" aria-required="true" required="required" >
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_NAME_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="address_line_1" id="venue_address1-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_ADDRESS1'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" size="30" class="required venue_field"  value="<?php echo @$this->item->address_line_1 ?>" id="address_line_1" name="address_line_1" aria-required="true" required="required" >
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_ADDRESS1_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="address_line_2" id="venue_address2-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_ADDRESS2'); ?>
                    <span class="star">&nbsp;</span>
                </label>
            </div>
            <div class="form-input" >
                <?php /*<input type="text" size="30" readonly="readonly" value="<?php echo @$this->item->address_line_2 ?>" id="address_line_2" name="address_line_2"  >*/ ?>
                <input class="venue_field" type="text" size="30"  value="<?php echo @$this->item->address_line_2 ?>" id="address_line_2" name="address_line_2"  >
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_ADDRESS2_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="address_line_3" id="venue_address3-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_ADDRESS3'); ?>
                    <span class="star">&nbsp;</span>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" class="venue_field" size="30"  value="<?php echo @$this->item->address_line_3 ?>" id="address_line_3" name="address_line_3"  >
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_ADDRESS3_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="address_country_name" id="venue_country-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_COUNTRY'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <?php /*<input type="text" size="30" class="required" readonly="readonly" value="<?php echo @$this->item->address_country_name ?>" id="address_country_name" name="address_country_name" >*/ ?>
                <input type="text" size="30" class="required venue_field"  value="<?php echo @$this->item->address_country ?>" id="address_country_name" name="address_country_name" >
                <input type="hidden" id="address_country" name="address_country" value="<?php echo @$this->item->address_country ?>" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_COUNTRY_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

		<?php /*
			if(@$this->item->country_uses_states == 1) {
				$state_style = '';
			} else {
				$state_style = 'style="display:none"';
			}*/
			
		?>
		
        <div class="fieldbg state_div" <?php //echo $state_style;?> >
            <div class="form-label" >
                <label for="address_state_name" id="venue_state-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_STATE'); ?>
                </label>
            </div>
            <div class="form-input" >
                <?php /*<input type="text" size="30" readonly="readonly" value="<?php echo @$this->item->address_state_name ?>" id="address_state_name" name="address_state_name" >*/ ?>
                <input class="venue_field" type="text" size="30"  value="<?php echo @$this->item->address_state ?>" id="address_state_name" name="address_state_name" >
                <input type="hidden" id="address_state" name="address_state" value="<?php echo @$this->item->address_state ?>"/>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_STATE_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="address_city_name" id="venue_city-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_CITY'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <?php /*<input type="text" size="30" class="required" readonly="readonly" value="<?php echo @$this->item->address_city_name ?>" id="address_city_name" name="address_city_name" >*/ ?>
                <input type="text" size="30" class="required venue_field" value="<?php echo @$this->item->address_city ?>" id="address_city_name" name="address_city_name" >
                <input type="hidden" id="address_city" name="address_city" value="<?php echo @$this->item->address_city ?>"/>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_CITY_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="address_zip_code" id="venue_zip-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_VENUE_ZIP'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input type="text" size="30" class="required venue_field" value="<?php echo @$this->item->address_zip_code ?>" id="address_zip_code" name="address_zip_code" >
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_VENUE_ZIP_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>
        <div class="" >
			<?php //var_dump($this->item); ?>
			 <input type="hidden" size="30" class="required"  value="<?php echo @$this->item->address_latitude; ?>" id="lat" name="lat" >
			 <input type="hidden" size="30" class="required"  value="<?php echo @$this->item->address_longitude; ?>" id="lng" name="lng" >
			
            <div class="clr"></div>
           
        </div>

        <div class="clr"></div>
        <br/>	
    </div>		

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_RINGS'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>
        <div id="rings">
            <div class="field-ringtext ring_numbers hidden-phone">
                <?php echo JText::_('COM_TOES_SHOW_RING_NUMBER'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_NUMBER_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="field-ringtext ring_show_day ringdays-header hidden-phone">
                <?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="field-ringtext ringtimings ringtimings-header hidden-phone">
                <?php echo JText::_('COM_TOES_SHOW_RING_TIMING'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_TIMING_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="field-ringtext ring_formats hidden-phone">
                <?php echo JText::_('COM_TOES_SHOW_RING_FORMAT'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_FORMAT_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="field-ringtext ring_judges hidden-phone">
                <?php echo JText::_('COM_TOES_SHOW_RING_JUDGE'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_JUDGE_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="field-ringtext ring_clerks hidden-phone">
                <?php echo JText::_('COM_TOES_SHOW_RING_CLERK'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CLERK_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="field-ringtext ring_congress hidden-phone">
                <?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
            <br/>

            <?php
            $x = 1;
            if (@$this->item->rings) {
                foreach (@$this->item->rings as $ring) {
                    ?>	
                    <div id="ring<?php echo $x; ?>" class="rings">
                        <div class="field-ringvalue ring_numbers">
                        	<div class="field-ringtext ring_numbers hidden-desktop">
				                <?php echo JText::_('COM_TOES_SHOW_RING_NUMBER'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_NUMBER_HELP'); ?>">&nbsp;</span>
				            </div>
                            <input name="ring_number[]" id="ring_number<?php echo $x; ?>" type="text" size="10" value="<?php echo $ring->ring_number; ?>" >
                        </div>
                        <div class="field-ringvalue ring_show_day">
				            <div class="field-ringtext ringdays-header hidden-desktop">
				                <?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY_HELP'); ?>">&nbsp;</span>
				            </div>
                            <select name="ring_show_day[]" id="ring_show_day_<?php echo $x; ?>">
                                <option value=""><?php echo JText::_('COM_TOES_SELECT'); ?></option>
                                <?php foreach ($showdays as $showDay): ?>
                                    <option value="<?php echo date('Y-m-d', strtotime($showDay->show_day_date)); ?>" <?php echo ($ring->ring_show_day == $showDay->show_day_id) ? 'selected="selected"' : '' ?>><?php echo date('l', strtotime($showDay->show_day_date)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field-ringvalue ringtimings">
		 		            <div class="field-ringtext ringtimings-header hidden-desktop">
				                <?php echo JText::_('COM_TOES_SHOW_RING_TIMING'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_TIMING_HELP'); ?>">&nbsp;</span>
				            </div>
                           <select name="ring_timings[]" id="ring_timings<?php echo $x; ?>" >
                                <?php
                                foreach ($ringtimings as $rs) {
                                    $sel = '';
                                    if ($rs->value == $ring->ring_timing)
                                        $sel = 'selected="selected"';

                                    echo '<option value="' . $rs->value . '" ' . $sel . '>' . $rs->text . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="field-ringvalue ring_formats">
				            <div class="field-ringtext ring_formats hidden-desktop">
				                <?php echo JText::_('COM_TOES_SHOW_RING_FORMAT'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_FORMAT_HELP'); ?>">&nbsp;</span>
				            </div>
                            <select name="ring_formats[]" id="ring_formats<?php echo $x; ?>" onchange="changeCongressVisibility(<?php echo $x; ?>,this.value);">
                                <?php
                                foreach ($ringformats as $rs) {
                                    $sel = '';
                                    if ($rs->value == $ring->ring_format)
                                        $sel = 'selected="selected"';

                                    echo '<option value="' . $rs->value . '" ' . $sel . '>' . $rs->text . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="field-ringvalue ring_judges">
				             <div class="field-ringtext ring_judges hidden-desktop">
				                <?php echo JText::_('COM_TOES_SHOW_RING_JUDGE'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_JUDGE_HELP'); ?>">&nbsp;</span>
				            </div>
                            <input name="ring_judge[]" id="ring_judge<?php echo $x; ?>" type="text" size="10" value="<?php echo $ring->ring_judge_name; ?>" onkeydown="prev_judge_check('<?php echo $x;?>', event);" onblur="clear_prev_judge();checkJudge(<?php echo $x;?>);" />
                            <?php /*
                              <select name="ring_judge[]" id="ring_judge<?php echo $x; ?>" >
                              <?php
                              foreach ($ringjudgs as $rj) {
                              $sel = '';
                              if ($rj->value == $ring->ring_judge)
                              $sel = 'selected="selected"';

                              echo '<option value="' . $rj->value . '" ' . $sel . '>' . $rj->text . '</option>';
                              }
                              ?>
                              </select>
                             */ ?>
                        </div>
                        <div class="field-ringvalue ring_clerks">
				             <div class="field-ringtext ring_clerks hidden-desktop">
				                <?php echo JText::_('COM_TOES_SHOW_RING_CLERK'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CLERK_HELP'); ?>">&nbsp;</span>
				            </div>
                            <input name="ring_clerk[]" id="ring_clerk<?php echo $x; ?>" type="text" size="10" value="<?php echo $ring->ring_clerk_name; ?>" onkeydown="prev_ring_clerk_check('<?php echo $x;?>', event);" onblur="clear_prev_ring_clerk();checkRingClerk(<?php echo $x;?>);" /><br/>
							<div class="field-ringtext">
				                <?php echo JText::_('COM_TOES_SHOW_RING_CLERK_ACCESS_CODE'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CLERK_ACCESS_CODE_HELP'); ?>">&nbsp;</span>
				            </div>
							<input type="text" disabled readonly name="ring_clerk_access_code[]" id="ring_clerk_access_code<?php echo $x; ?>" value="<?php echo $ring->ring_clerk_access_code; ?>" />
                        </div>
                        <div class="field-ringvalue ring_congress">
				            <div class="field-ringtext hidden-desktop">
				                <?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME'); ?>
				                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME_HELP'); ?>">&nbsp;</span>
				            </div>
                            <input style="<?php echo ($ring->ring_format != 3) ? 'visibility: hidden;' : ''; ?>" name="ring_congress_name[]" id="ring_congress_name<?php echo $x; ?>" type="text" size="10" value="<?php echo $ring->ring_name ?>" >
                            <a style="<?php echo ($ring->ring_format != 3) ? 'visibility: hidden;' : ''; ?>" id="ring_congress_name<?php echo $x; ?>_criteria"  href="javascript:void(0);" onclick="define_filter_criteria(<?php echo $x; ?>,<?php echo $ring->ring_id; ?>)">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>
                        <span style="cursor: pointer;" onclick="removering(<?php echo $x; ?>);"><i style="margin-top: 12px;" class="fa fa-remove"></i></span>
                        <input type="hidden" value="<?php echo $x; ?>" name="ring_index[]"/>
                        <input type="hidden" value="<?php echo $ring->ring_id; ?>" id="ring_id<?php echo $x; ?>" name="ring_id[]"/>
                        <input type="hidden" id="ring_judge_id<?php echo $x; ?>" name="ring_judge_id[]" value="<?php echo $ring->ring_judge; ?>"/>
						<input type="hidden" id="ring_clerk_id<?php echo $x; ?>" name="ring_clerk_id[]" value="<?php echo $ring->ring_clerk; ?>"/>
                        <div class="clr"></div>
                    </div>

                    <?php
                    $x++;
                }
            }
            ?>

            <div id="ring<?php echo $x; ?>" class="rings">	
		        <div class="field-ringvalue ring_numbers">
                	<div class="field-ringtext ring_numbers hidden-desktop">
		                <?php echo JText::_('COM_TOES_SHOW_RING_NUMBER'); ?>
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_NUMBER_HELP'); ?>">&nbsp;</span>
		            </div>
                    <input name="ring_number[]" id="ring_number<?php echo $x; ?>" type="text" size="10" value="" >
                </div>
                <div class="field-ringvalue ring_show_day">
		            <div class="field-ringtext ringdays-header hidden-desktop">
		                <?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY'); ?>
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_SHOWDAY_HELP'); ?>">&nbsp;</span>
		            </div>
                    <select name="ring_show_day[]" id="ring_show_day_<?php echo $x; ?>">
                        <option value=""><?php echo JText::_('COM_TOES_SELECT'); ?></option>
                        <?php foreach ($showdays as $showDay): ?>
                            <option value="<?php echo date('Y-m-d', strtotime($showDay->show_day_date)); ?>"><?php echo date('l', strtotime($showDay->show_day_date)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field-ringvalue ringtimings">
		            <div class="field-ringtext ringtimings-header hidden-desktop">
		                <?php echo JText::_('COM_TOES_SHOW_RING_TIMING'); ?>
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_TIMING_HELP'); ?>">&nbsp;</span>
		            </div>
                    <select name="ring_timings[]" id="ring_timings<?php echo $x; ?>">
                        <?php
                        foreach ($ringtimings as $rs) {
                            echo '<option value="' . $rs->value . '" >' . $rs->text . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="field-ringvalue ring_formats">
		            <div class="field-ringtext ring_formats hidden-desktop">
		                <?php echo JText::_('COM_TOES_SHOW_RING_FORMAT'); ?>
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_FORMAT_HELP'); ?>">&nbsp;</span>
		            </div>
                    <select name="ring_formats[]" id="ring_formats<?php echo $x; ?>" onchange="changeCongressVisibility(<?php echo $x; ?>,this.value);" >
                        <?php
                        foreach ($ringformats as $rs) {
                            echo '<option value="' . $rs->value . '">' . $rs->text . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="field-ringvalue ring_judges">
		             <div class="field-ringtext ring_judges hidden-desktop">
		                <?php echo JText::_('COM_TOES_SHOW_RING_JUDGE'); ?>
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_JUDGE_HELP'); ?>">&nbsp;</span>
		            </div>
                   <input name="ring_judge[]" id="ring_judge<?php echo $x; ?>" type="text" size="10" value="" onkeydown="prev_judge_check('<?php echo $x;?>', event);" onblur="clear_prev_judge();checkJudge(<?php echo $x;?>);" >
                    <?php /*
                      <select name="ring_judge[]" id="ring_judge<?php echo $x; ?>" >
                      <?php
                      foreach ($ringjudgs as $rj) {
                      echo '<option value="' . $rj->value . '">' . $rj->text . '</option>';
                      }
                      ?>
                      </select>
                     */ ?>
                </div>
				<div class="field-ringvalue ring_clerks">
					 <div class="field-ringtext ring_clerks hidden-desktop">
						<?php echo JText::_('COM_TOES_SHOW_RING_CLERK'); ?>
						<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CLERK_HELP'); ?>">&nbsp;</span>
					</div>
					<input name="ring_clerk[]" id="ring_clerk<?php echo $x; ?>" type="text" size="10" value="" onkeydown="prev_ring_clerk_check('<?php echo $x;?>', event);" onblur="clear_prev_ring_clerk();checkRingClerk(<?php echo $x;?>);" />
				</div>
                <div class="field-ringvalue  ring_congress">
		            <div class="field-ringtext hidden-desktop">
		                <?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME'); ?>
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_RING_CONGRESS_NAME_HELP'); ?>">&nbsp;</span>
		            </div>
                    <input style="visibility: hidden;" name="ring_congress_name[]" id="ring_congress_name<?php echo $x; ?>" type="text" size="10" value="" >
                    <a style="visibility: hidden;" id="ring_congress_name<?php echo $x; ?>_criteria"  href="javascript:void(0);" onclick="define_filter_criteria(<?php echo $x; ?>,0)">
                        <i class="fa fa-edit"></i>
                    </a>
                </div>
                <span style="cursor: pointer;" onclick="removering(<?php echo $x; ?>);"><i style="margin-top: 12px;" class="fa fa-remove"></i></span>
                <input type="hidden" value="<?php echo $x; ?>" name="ring_index[]"/>
                <input type="hidden" value="0" id="ring_id<?php echo $x; ?>" name="ring_id[]"/>
                <input type="hidden" id="ring_judge_id<?php echo $x; ?>" name="ring_judge_id[]" value=""/>
				<input type="hidden" id="ring_clerk_id<?php echo $x; ?>" name="ring_clerk_id[]" value=""/>
                <div class="clr"></div>
            </div>
        </div>
        
        <div style="padding-left: 35px;">
            <input type="hidden" id="count_rings" name="count_rings" value="<?php echo $x + 1; ?>">
            <br/>
            <input class="button button-4" type="button" name="add_ring" value="<?php echo JText::_('COM_TOES_ADD_RING') ?>" onclick="addring();"/>
        </div>
        <div class="clr"></div>
        <br/>
    </div>

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_JUDGES_BOOK_FINISHING'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>
        <div class="fieldbg" >
            <div class="form-label" >
                <label for=show_print_division_title_in_judges_books id="show_print_division_title_in_judges_books-lbl">
                    <?php echo JText::_('COM_TOES_PRINT_DIVISION_NAMES_IN_JUDGES_BOOKS'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_print_division_title_in_judges_books" name="show_print_division_title_in_judges_books"  <?php if (@$this->item->show_print_division_title_in_judges_books) echo 'checked="checked"'; ?> value="1" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_PRINT_DIVISION_NAMES_IN_JUDGES_BOOKS_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_print_extra_lines_for_bod_and_bob_in_judges_book" id="show_print_extra_lines_for_bod_and_bob_in_judges_book-lbl">
                    <?php echo JText::_('COM_TOES_ADD_EXTRA_LINES_FOR_BOD_AND_BOB_IN_JUDGES_BOOKS'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_print_extra_lines_for_bod_and_bob_in_judges_book" name="show_print_extra_lines_for_bod_and_bob_in_judges_book"  <?php if (@$this->item->show_print_extra_lines_for_bod_and_bob_in_judges_book) echo 'checked="checked"'; ?> value="1" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_ADD_EXTRA_LINES_FOR_BOD_AND_BOB_IN_JUDGES_BOOKS_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_print_extra_line_at_end_of_color_class_in_judges_book" id="show_print_extra_line_at_end_of_color_class_in_judges_book-lbl">
                    <?php echo JText::_('COM_TOES_ADD_EXTRA_LINE_AT_THE_END_OF_A_COLOR_CLASS_IN_JUDGES_BOOKS'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_print_extra_line_at_end_of_color_class_in_judges_book" name="show_print_extra_line_at_end_of_color_class_in_judges_book"  <?php if (@$this->item->show_print_extra_line_at_end_of_color_class_in_judges_book) echo 'checked="checked"'; ?> value="1" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_ADD_EXTRA_LINE_AT_THE_END_OF_A_COLOR_CLASS_IN_JUDGES_BOOKS_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        <br/>
    </div>

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_CATALOG_FINISHING'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_catalog_font_size" id="show_catalog_font_size-lbl">
                    <?php echo JText::_('COM_TOES_FONT_SIZE'); ?>
                </label>
            </div>
            <div class="form-input" >
                <?php echo $this->fontsizelist; ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_FONT_SIZE_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_colored_catalog" id="show_colored_catalog-lbl">
                    <?php echo JText::_('COM_TOES_COLORED_CATALOGS'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_colored_catalog" name="show_colored_catalog"  <?php if (@$this->item->show_colored_catalog) echo 'checked="checked"'; ?> value="1" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_COLORED_CATALOGS_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_catalog_cat_names_bold" id="show_catalog_cat_names_bold-lbl">
                    <?php echo JText::_('COM_TOES_CAT_NAMES_BOLD'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_catalog_cat_names_bold" name="show_catalog_cat_names_bold"  <?php if (@$this->item->show_catalog_cat_names_bold) echo 'checked="checked"'; ?> value="1" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_CAT_NAMES_BOLD_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_catalog_page_orientation" id="show_catalog_page_orientation-lbl">
                    <?php echo JText::_('COM_TOES_CATALOG_PAGE_ORIENTATION'); ?>
                </label>
            </div>
            <div class="form-input" >
                <?php echo $this->pageorientationlist; ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_CATALOG_PAGE_ORIENTATION_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        <br/>
    </div>
    
    <?php if(@$this->item->show_id): ?>
    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_LICENSE_BLOCK_TITLE'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/>
        <div class="fieldbg" >
            <div class="form-label" >
                <label id="show_licensed-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_LICENSE_STATUS'); ?>
                </label>
            </div>
            <div class="form-input" >
            	<input type="hidden" name="show_licensed" id="show_licensed" value="<?php echo @$this->item->show_licensed; ?>" />
                <?php echo (@$this->item->show_licensed)?JText::_('COM_TOES_SHOW_LICENSED'):JText::_('COM_TOES_SHOW_NOT_LICENSED'); ?>
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_LICENSE_STATUS_HELP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>
        
        <?php if(!@$this->item->show_licensed): ?>
        
        <div id="request_show_license_div">
	        <div class="fieldbg" >
	            <div class="form-label" >
	                <label for="request_show_license" id="request_show_license-lbl">
	                    <?php echo JText::_('COM_TOES_REQUEST_SHOW_LICENSE'); ?>
	                </label>
	            </div>
	            <div class="form-input" >
	                <input type="checkbox" id="request_show_license" name="request_show_license" />
	                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_REQUEST_SHOW_LICENSE_HELP'); ?>">&nbsp;</span>
	            </div>
	            <div class="clr"></div>
	        </div>  
	        <div class="clr"></div>
	
	        <div id="show_license_application_div" style="display: none;">
		        <div class="fieldbg" >
		            <div class="form-label" >
		                <label for="rsl_first_show" id="rsl_first_show-lbl">
		                    <?php echo JText::_('COM_TOES_RSL_FIRST_SHOW'); ?>
		                </label>
		            </div>
		            <div class="form-input" >
		                <input type="checkbox" id="rsl_first_show" name="rsl_first_show" value="1" />
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_FIRST_SHOW_HELP'); ?>">&nbsp;</span>
		            </div>
		            <div class="clr"></div>
		        </div>  
		        <div class="clr"></div>
		        
		        <div class="fieldbg" >
		            <div class="form-label" >
		                <label for="rsl_ab_rings" id="rsl_ab_rings-lbl">
		                    <?php echo JText::_('COM_TOES_RSL_AB_RINGS'); ?>
		                </label>
		            </div>
		            <div class="form-input" >
		                <input type="text" id="rsl_ab_rings" name="rsl_ab_rings" />
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_AB_RINGS_HELP'); ?>">&nbsp;</span>
		            </div>
		            <div class="clr"></div>
		        </div>  
		        <div class="clr"></div>
		        
		        <div class="fieldbg" >
		            <div class="form-label" >
		                <label for="rsl_sp_rings" id="rsl_sp_rings-lbl">
		                    <?php echo JText::_('COM_TOES_RSL_SP_RINGS'); ?>
		                </label>
		            </div>
		            <div class="form-input" >
		                <input type="text" id="rsl_sp_rings" name="rsl_sp_rings" />
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_SP_RINGS_HELP'); ?>">&nbsp;</span>
		            </div>
		            <div class="clr"></div>
		        </div>  
		        <div class="clr"></div>
		        
		        <div class="fieldbg" >
		            <div class="form-label" >
		                <label for="rsl_congress_rings" id="rsl_congress_rings-lbl">
		                    <?php echo JText::_('COM_TOES_RSL_CONGRESS_RINGS'); ?>
		                </label>
		            </div>
		            <div class="form-input" >
		                <input type="text" id="rsl_congress_rings" name="rsl_congress_rings" />
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_CONGRESS_RINGS_HELP'); ?>">&nbsp;</span>
		            </div>
		            <div class="clr"></div>
		        </div>  
		        <div class="clr"></div>
		        
		        <div class="fieldbg" >
		            <div class="form-label" >
		                <label for="rsl_include_show_supplies" id="rsl_include_show_supplies-lbl">
		                    <?php echo JText::_('COM_TOES_RSL_INCLUDE_SHOW_SUPPLIES'); ?>
		                </label>
		            </div>
		            <div class="form-input" >
		                <input type="checkbox" id="rsl_include_show_supplies" name="rsl_include_show_supplies" checked="checked" />
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_INCLUDE_SHOW_SUPPLIES_HELP'); ?>">&nbsp;</span>
		            </div>
		            <div class="clr"></div>
		        </div>  
		        <div class="clr"></div>
		        
		        <div id="shipping_info_div">
		        	<h5><?php echo JText::_('COM_TOES_RSL_SHIPPING_INFO');?></h5>
		        	
		        	<div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_ship_name" id="rsl_ship_name-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_SHIP_NAME'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_ship_name" name="rsl_ship_name" />
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_SHIP_NAME_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
			        
			        <div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_ship_address" id="rsl_ship_address-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_SHIP_ADDRESS'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_ship_address" name="rsl_ship_address" />
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_SHIP_ADDRESS_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
			        
			        <div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_ship_city" id="rsl_ship_city-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_SHIP_CITY'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_ship_city" name="rsl_ship_city" />
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_SHIP_CITY_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
			        
			        <div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_ship_zip" id="rsl_ship_zip-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_SHIP_ZIP'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_ship_zip" name="rsl_ship_zip" />
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_SHIP_ZIP_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
			        
			        <div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_ship_state" id="rsl_ship_state-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_SHIP_STATE'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_ship_state" name="rsl_ship_state" />
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_SHIP_STATE_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
			        
			        <div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_ship_country" id="rsl_ship_country-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_SHIP_COUNTRY'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_ship_country" name="rsl_ship_country" />
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_SHIP_COUNTRY_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
		        </div>
		        
		        <div class="fieldbg" >
		            <div class="form-label" >
		                <label for="rsl_insurance_info" id="rsl_insurance_info-lbl">
		                    <?php echo JText::_('COM_TOES_RSL_INSURANCE_INFO'); ?>
		                </label>
		            </div>
		            <div class="form-input" >
		                <textarea id="rsl_insurance_info" name="rsl_insurance_info"></textarea>
		                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_INSURANCE_INFO_HELP'); ?>">&nbsp;</span>
		            </div>
		            <div class="clr"></div>
		        </div>  
		        <div class="clr"></div>
		        
		        <div id="total_fees_div">
		        	<h5><?php echo JText::_('COM_TOES_RSL_FEES');?></h5>
			        <div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_total_fee" id="rsl_total_fee-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_TOTAL_FEES'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_total_fee" name="rsl_total_fee" /> USD 
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_TOTAL_FEES_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
			        
		        	<div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_license_fee" id="rsl_license_fee-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_LICENSE_FEES'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_license_fee" name="rsl_license_fee" /> USD 
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_LICENSE_FEES_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
			        
			        <div class="fieldbg" >
			            <div class="form-label" >
			                <label for="rsl_anual_award_fee" id="rsl_anual_award_fee-lbl">
			                    <?php echo JText::_('COM_TOES_RSL_ANUAL_AWARDS_FEES'); ?>
			                </label>
			            </div>
			            <div class="form-input" >
			                <input type="text" id="rsl_anual_award_fee" name="rsl_anual_award_fee" /> USD 
			                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_RSL_ANUAL_AWARDS_FEES_HELP'); ?>">&nbsp;</span>
			            </div>
			            <div class="clr"></div>
			        </div>  
			        <div class="clr"></div>
		        </div>
		        <?php /*
			    <div>
			        <input class="save button button-4" type="button" onclick="validate_rsl_application()" value="<?php echo JText::_('COM_TOES_SEND_SHOW_LICENSE_APPLICATION'); ?>" />
			        <input class="cancel button button-red" type="button" name="cancel" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" onclick="cancel_rsl_application();"/>
			    </div>
			    <br/>
				*/ ?>
	        </div>
		</div>
        
        <script type="text/javascript">
			
			
			

	        function validNumber(e) {
	            var filter = /^\d+$/;
	            return String(e).search (filter) != -1;
	        }

	        function cancel_rsl_application()
	        {
	        	jQuery('#request_show_license').prop('checked',0);
	        	jQuery('#show_license_application_div').hide();
	        }
        
			function validate_rsl_application()
			{
				var error = '';
				var still_required = '';

				var first_show = (jQuery('#rsl_first_show').is(':checked'))?1:0;
			    var ab_rings = jQuery('#rsl_ab_rings').val().toString();
			    var sp_rings = jQuery('#rsl_sp_rings').val().toString();
			    var congress_rings = jQuery('#rsl_congress_rings').val().toString();

				var name = jQuery('#rsl_ship_name').val().toString();
			    var address = jQuery('#rsl_ship_address').val().toString();
			    var city = jQuery('#rsl_ship_city').val().toString();
			    var zip = jQuery('#rsl_ship_zip').val().toString();
			    var state = jQuery('#rsl_ship_state').val().toString();
			    var country = jQuery('#rsl_ship_country').val().toString();
			    var include_show_supplies = (jQuery('#rsl_include_show_supplies').is(':checked'))?1:0;

			    var insurance_info = jQuery('#rsl_insurance_info').val().toString();
			    var total_fee = jQuery('#rsl_total_fee').val().toString();
			    var license_fee = jQuery('#rsl_license_fee').val().toString();
			    var anual_award_fee = jQuery('#rsl_anual_award_fee').val().toString();
			    
				if(!validNumber(ab_rings))
					error += '<?php echo JText::_('COM_TOES_RSL_INVALID_AB_RINGS');?><br/>';
				if(!validNumber(sp_rings))
					error += '<?php echo JText::_('COM_TOES_RSL_INVALID_SP_RINGS');?><br/>';
				if(!validNumber(congress_rings))
					error += '<?php echo JText::_('COM_TOES_RSL_INVALID_CONGRESS_RINGS');?><br/>';

				if(include_show_supplies)
				{
				    if(name.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_NAME');?>';
				    if(address.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_ADDRESS');?>';
				    if(city.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_CITY');?>';
				    if(zip.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_ZIP');?>';
				    if(country.trim() == "")
				        still_required += '<br/>* <?php echo JText::_('COM_TOES_RSL_SHIP_COUNTRY');?>';
				}

				if(!validNumber(total_fee))
					error += '<?php echo JText::_('COM_TOES_RSL_INVALID_TOTAL_FEE');?><br/>';
				if(!validNumber(license_fee))
					error += '<?php echo JText::_('COM_TOES_RSL_INVALID_LICENSE_FEE');?><br/>';
				if(!validNumber(anual_award_fee))
					error += '<?php echo JText::_('COM_TOES_RSL_INVALID_ANUAL_AWARD_FEE');?><br/>';

				if(still_required)
					error = "<?php echo JText::_('COM_TOES_STILL_REQUIRED');?>"+still_required+"<br/><br/>"+error;

				if(error)
					jbox_alert(error);
				else
				{
					insurance_info = Base64.encode(insurance_info);
					
					var post = 'id=<?php echo $this->item->show_id;?>';
					post += '&rsl_first_show='+first_show;
					post += '&rsl_ab_rings='+ab_rings;
					post += '&rsl_sp_rings='+sp_rings;
					post += '&rsl_congress_rings='+congress_rings;
					post += '&rsl_include_show_supplies='+include_show_supplies;
					post += '&rsl_ship_name='+name;
					post += '&rsl_ship_address='+address;
					post += '&rsl_ship_city='+city;
					post += '&rsl_ship_zip='+zip;
					post += '&rsl_ship_state='+state;
					post += '&rsl_ship_country='+country;
					post += '&rsl_insurance_info='+insurance_info;
					post += '&rsl_total_fee='+total_fee;
					post += '&rsl_license_fee='+license_fee;
					post += '&rsl_anual_award_fee='+anual_award_fee;

					jQuery.ajax({
	                    url: 'index.php?option=com_toes&task=show.sendShowLicenseApplication',
	                    data:post,
	                    type: 'post',
	                }).done(function(responseText){
                        if(responseText == 1)
                        {
                        	jQuery('#show_licensed').val(1);
                        	jQuery('#show_license_application_div').show();
                        	jbox_alert("<?php echo JText::_('COM_TOES_SHOW_LICENSE_APPLICATION_SENT')?>");
                        }
                        else
                        {
	                        jbox_alert(responseText);
                        }
	                });
				}
			}
        
        	jQuery('#request_show_license').on('click',function(){
            	if(jQuery('#request_show_license').is(':checked'))
            	{
            		var ab_rings = 0;
            		var sp_rings = 0;
                    var congress_rings = 0;
                    jQuery('input[name^=ring_index]').each(function(){
                        var i = jQuery(this).val();
                        if(jQuery('#ring_show_day_'+i).val() && jQuery('#ring_formats'+i).val() && jQuery('#ring_judge'+i).val())
                        {
                            if(jQuery('#ring_formats'+i).val() == 1)
                                ab_rings++;

                        	if(jQuery('#ring_formats'+i).val() == 2)
                                sp_rings++;

                        	if(jQuery('#ring_formats'+i).val() == 3)
                                congress_rings++;
                        }
                    });  

					jQuery('#rsl_ab_rings').val(ab_rings);
					jQuery('#rsl_sp_rings').val(sp_rings);
					jQuery('#rsl_congress_rings').val(congress_rings);

					var license_fee = ab_rings*15 + sp_rings*15 + congress_rings*10 ;
					var anual_award_fee = ab_rings*20 + sp_rings*20 ;
					jQuery('#rsl_license_fee').val( license_fee );
					jQuery('#rsl_anual_award_fee').val( anual_award_fee );
					jQuery('#rsl_total_fee').val( license_fee + anual_award_fee );
                	
                	jQuery('#show_license_application_div').show();
            	}
            	else
            		jQuery('#show_license_application_div').hide();
			});
        </script>
		
		<?php endif; ?>

		<br/>
    </div>
    <?php endif; ?>
    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_OFFICIALS'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter">
        <br/><br/>	
        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_use_club_entry_clerk_address" id="show_use_club_entry_clerk_address-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_USE_CLUB_ENTRY_CLERK_ADDRESS'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_use_club_entry_clerk_address" name="show_use_club_entry_clerk_address"  <?php if (@$this->item->show_use_club_entry_clerk_address) echo 'checked="checked"'; ?> value="1" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_USE_CLUB_ENTRY_CLERK_ADDRESS_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>  
        <div class="clr"></div>

        <div class="fieldbg" id="show_entry_clerk_email" style="<?php if (@!$this->item->show_use_club_entry_clerk_address) echo 'display:none'; ?>" >
            <div class="form-label" >
                <label for="show_email_address_entry_clerk" id="show_email_address_entry_clerk-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_EMAIL_ADDRESS_ENTRY_CLERK'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input class="required validate-email" type="text" name="show_email_address_entry_clerk" id="show_email_address_entry_clerk" value="<?php echo isset($this->item->show_email_address_entry_clerk) ? $this->item->show_email_address_entry_clerk : ''; ?>" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_EMAIL_ADDRESS_ENTRY_CLERK_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>
        <div class="clr"></div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label for="show_use_club_show_manager_address" id="show_use_club_entry_clerk_address-lbl">
                    <?php echo JText::_('COM_TOES_USE_CLUB_SHOW_MANAGER_ADDRESS'); ?>
                </label>
            </div>
            <div class="form-input" >
                <input type="checkbox" id="show_use_club_show_manager_address" name="show_use_club_show_manager_address"  <?php if (@$this->item->show_use_club_show_manager_address) echo 'checked="checked"'; ?> value="1" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_USE_CLUB_SHOW_MANAGER_ADDRESS_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>   
        <div class="clr"></div>

        <div class="fieldbg" id="show_show_manager_email" style="<?php if (@!$this->item->show_use_club_show_manager_address) echo 'display:none'; ?>" >
            <div class="form-label" >
                <label for="show_email_address_show_manager" id="show_email_address_show_manager-lbl">
                    <?php echo JText::_('COM_TOES_SHOW_EMAIL_ADDRESS_SHOW_MANAGER'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input class="required validate-email" type="text" name="show_email_address_show_manager" id="show_email_address_show_manager" value="<?php echo isset($this->item->show_email_address_show_manager) ? $this->item->show_email_address_show_manager : ''; ?>" />
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_EMAIL_ADDRESS_SHOW_MANAGER_TOOLTIP'); ?>">&nbsp;</span>
            </div>
            <div class="clr"></div>
        </div>
        <div class="clr"></div>

        <br/><br/>
        <div>
            <div class="form-input">
                <input type="text" id="username" style="color: #999;" value="Type to search user" onblur="if(this.value==''){this.value='Type to search user'; this.style.color='#999';}" onfocus="if(this.value=='Type to search user'){ this.value='';this.style.color='#000';}" >
                <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_USERNAME_SEARCH_HELP'); ?>">&nbsp;</span>
            </div>
			<div class="clr"></div>
            <div>
                <?php foreach ($this->showofficialtypes as $so) { ?>
                    <div class="show-users-link-div">
                        <input class=" button button-blue" onclick="setuserofficial('<?php echo $so->show_official_type_id; ?>');" type="button" value="<?php echo JText::_('ADD_USER_AS') . $so->show_official_type; ?>" />
                    </div>
                    <div class="show-users-name-div">
                        <label class="required" for="<?php echo $so->show_official_type_id; ?>" id="<?php echo $so->show_official_type_id; ?>-lbl">
                            <?php echo $so->show_official_type; ?>
                            <span class="star">&nbsp;*</span>
                        </label>
                        <div id="innerusername_<?php echo $so->show_official_type_id; ?>"></div>
                        <div  id="username_<?php echo $so->show_official_type_id; ?>place"></div>
                        <input type="hidden" id="countusername_<?php echo $so->show_official_type_id; ?>" name="countusername_<?php echo $so->show_official_type_id; ?>" value="1">
                    </div>
                    <div class="clr"></div>
                    <br/>
                <?php } ?>
            </div>
        </div>
        <div class="clr"></div>
        <br/>
    </div>	

    <?php if(TOESHelper::isAdmin() && isset($this->item->show_id)) :?>
        <div class="fistouter">
            <div class="fieldblank" >
                <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_INVOICE'); ?></div>
            </div>
            <div class="clr"></div>
        </div>

        <div class="seconouter">
            <br/>
            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_cost_per_entry" id="show_cost_per_entry-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_COST_PER_ENTRY'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_cost_per_entry" id="show_cost_per_entry" type="text" size="10" value="<?php echo @$this->item->show_cost_per_entry; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_COST_PER_ENTRY_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_total_cost" id="show_total_cost-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_TOTAL_COST'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_total_cost" id="show_total_cost" type="text" size="10" value="<?php echo @$this->item->show_total_cost; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_TOTAL_COST_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_cost_total_entries" id="show_cost_total_entries-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_COST_TOTAL_ENTRIES'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_cost_total_entries" id="show_cost_total_entries" type="text" size="10" value="<?php echo @$this->item->show_cost_total_entries; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_COST_TOTAL_ENTRIES_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_cost_ex_only_entries" id="show_cost_ex_only_entries-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_COST_EXONLY_ENTRIES'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_cost_ex_only_entries" id="show_cost_ex_only_entries" type="text" size="10" value="<?php echo @$this->item->show_cost_ex_only_entries; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_COST_EXONLY_ENTRIES_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_maximum_cost" id="show_maximum_cost-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_MAXIMUM_COST'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_maximum_cost" id="show_maximum_cost" type="text" size="10" value="<?php echo @$this->item->show_maximum_cost; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_MAXIMUM_COST_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_cost_fixed_rebate" id="show_cost_fixed_rebate-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_FIXED_REBATE'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_cost_fixed_rebate" id="show_cost_fixed_rebate" type="text" size="10" value="<?php echo @$this->item->show_cost_fixed_rebate; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_FIXED_REBATE_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_cost_procentual_rebate" id="show_cost_procentual_rebate-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_PROCENTUAL_REBATE'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_cost_procentual_rebate" id="show_cost_procentual_rebate" type="text" size="10" value="<?php echo @$this->item->show_cost_procentual_rebate; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_PROCENTUAL_REBATE_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_cost_invoice_date" id="show_cost_invoice_date-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_COST_INVOICE_DATE'); ?>
                    </label>
                </div>
                <div class="form-input" >
					<input type="text" name="show_cost_invoice_date" id="show_cost_invoice_date" value="<?php echo isset($this->item->show_cost_invoice_date) ? $this->item->show_cost_invoice_date : 'YYYY-MM-DD' ?>" size="15"
						   class="validate-date" <?php echo (!isset($this->item->show_cost_invoice_date)) ? 'style="color: #999;"' : '' ?>
						   onfocus="if(this.value=='YYYY-MM-DD'){ this.value='';this.style.color='#000';}"
						   onblur="if(this.value==''){this.value='YYYY-MM-DD';this.style.color='#999';}"
						   />
					<i style="cursor:pointer;" class="fa fa-calendar" title="Click To Select Date" name="show_cost_invoice_date_selector" id="show_cost_invoice_date_selector"></i>
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_COST_INVOICE_DATE_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="fieldbg" >
                <div class="form-label" >
                    <label class="required" for="show_cost_amount_paid" id="show_cost_amount_paid-lbl">
                        <?php echo JText::_('COM_TOES_SHOW_COST_AMOUNT_PAID'); ?>
                    </label>
                </div>
                <div class="form-input" >
                    <input class="validate-numeric" name="show_cost_amount_paid" id="show_cost_amount_paid" type="text" size="10" value="<?php echo @$this->item->show_cost_amount_paid; ?>"  >
                    <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SHOW_COST_AMOUNT_PAID_HELP'); ?>">&nbsp;</span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="clr"></div>
            <br/>	
        </div>
    <?php endif; ?>
    
    <br/>
    <div class="action-buttons" >
		<input type="hidden" name="return_on_page" id="return_on_page" value="0" />
        <input class="save button button-4" type="button" onclick="validate_showform_new(this.form,1)" value="<?php echo JText::_('COM_TOES_SAVE_AND_STAY_ON_EDIT_SHOW_FORM'); ?>" />
        <input class="save button button-4" type="button" onclick="validate_showform_new(this.form,2)" value="<?php echo JText::_('COM_TOES_SAVE_AND_BACK_TO_CALENDAR'); ?>" />
        <input class="cancel button button-red" type="button" name="cancel" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" onclick="cancelForm(this.form);"/>
    </div>

    <?php echo JHtml::_('form.token'); ?>

    <input type="hidden" name="crosscheckusername" id="crosscheckusername" value="" />

    <!--#####################################-->
    <input type="hidden" name="org_show_day_ids" id="org_show_day_ids" value="<?php echo $org_show_day_ids; ?>" />
    <input type="hidden" name="org_show_day_dates" id="org_show_day_dates" value="<?php echo $org_show_day_dates; ?>" />

    <input type="hidden" name="org_ring_ids" id="org_ring_ids" value="<?php echo $org_ring_ids; ?>" />
    <input type="hidden" name="org_ring_days" id="org_ring_days" value="<?php echo $org_ring_days; ?>" />
    <!--#####################################-->  

    <input type="hidden" name="date_change_accepted" id="date_change_accepted" value="0" />
    <input type="hidden" name="org_start_date" id="org_start_date" value="<?php echo isset($this->item->show_start_date) ? $this->item->show_start_date : '0000-00-00' ?>" />
    <input type="hidden" name="org_end_date" id="org_end_date" value="<?php echo isset($this->item->show_end_date) ? $this->item->show_end_date : '0000-00-00' ?>" />
    
    <input type="hidden" name="pev_start_date" id="prev_start_date" value="<?php echo isset($this->item->show_start_date) ? $this->item->show_start_date : '0000-00-00' ?>" />
    
    <input type="hidden" name="option" value="com_toes" />
    <input type="hidden" name="view" value="shows" />
    <input type="hidden" name="task" value="show.save" />
    <input type="hidden" name="id" value="<?php echo @$this->item->show_id; ?>" />
    <div class="clr"></div>
</form>
</div>

<script type="text/javascript">
	var isAdmin;
	<?php if($isAdmin){?>
		isAdmin = true;
	<?php }else {?>
		isAdmin = false;
	<?php } ?>
	var show_id = '<?php echo $this->item->show_id?>';
			console.log(show_id);
	var venue ={address_id:0};		
	<?php if(@$venue){ ?>
		venue = <?php echo json_encode($venue);?>;
	<?php }else{ ?>
	<?php } ?>
	console.log(venue.address_id);
	var venue_changed = false;
	var show_days_count = parseInt('<?php echo $show_days?>');
	console.log(show_days_count);
	var edit_venue_on = false;
	 
			
			
			jQuery(document).on('ready',function(){
			if(show_id){
			jQuery('#edit_venue_div').show();
			jQuery('#venue_map_div').show();
			jQuery('.venue_field').attr('disabled',true);
			jQuery('#search_address').attr('disabled',true);
			}else{
				 
			jQuery('#edit_venue_div').hide();
			jQuery('#venue_map_div').hide();
			jQuery('#edit_venue').attr('checked',true);
			jQuery('#edit_venue_btn').addClass('button-pressed');
			}
			/*
			jQuery('#edit_venue').on('click',function(){
				 jQuery('.venue_field').attr('disabled',!jQuery(this).attr('checked'));
				 jQuery('#search_address').attr('disabled',!jQuery(this).attr('checked'));				
			});
			*/
			jQuery('#edit_venue_btn').on('click',function(){
				if(confirm('Are you sure that you want to edit venue? Current venue information will be cleared and you will have to enter new venue information.')){ 
				
				  edit_venue_on = !edit_venue_on;
				 jQuery('.venue_field').attr('disabled',!edit_venue_on);
				 jQuery('#search_address').attr('disabled',!edit_venue_on);	
				 
				 if(edit_venue_on)
				 jQuery(this).addClass('button-pressed');	
				 else
				 jQuery(this).removeClass('button-pressed');
				 jQuery('#search_address').val('');
				 jQuery('.venue_field').val('');
				 
				 
				 
				}
			});
			
			
			//
			jQuery('.venue_field').on('change',function(){
				/*
				 if(show_id)
				 venue_changed = true;			
				 */
			});			
			
			});
    
    jQuery('#show_use_club_entry_clerk_address').on('click',function(){
        if(jQuery('#show_use_club_entry_clerk_address').is(':checked'))
            jQuery('#show_entry_clerk_email').show();
        else 
            jQuery('#show_entry_clerk_email').hide();
    });
    
    jQuery('#show_use_club_show_manager_address').on('click',function(){
        if(jQuery('#show_use_club_show_manager_address').is(':checked'))
            jQuery('#show_show_manager_email').show();
        else 
            jQuery('#show_show_manager_email').hide();
    });
	
	function checkJudge(x)
	{
		if(jQuery('#ring_judge'+x).val() != '' && !jQuery('#ring_judge_id'+x).val())
		{
			jbox_alert("<?php echo JText::_('COM_TOES_PLEASE_SELECT_JUDGE_FROM_LIST');?>");
			jQuery('#ring_judge'+x).val('');
			jQuery('#ring_judge'+x).focus();
		}
	}
	
	function checkRingClerk(x)
	{
		if(jQuery('#ring_clerk'+x).val() != '' && !jQuery('#ring_clerk_id'+x).val())
		{
			jbox_alert("<?php echo JText::_('COM_TOES_PLEASE_SELECT_RING_CLERK_FROM_LIST');?>");
			jQuery('#ring_clerk'+x).val('');
			jQuery('#ring_clerk'+x).focus();
		}
	}
	
    function changeCongressVisibility(index, val)
    {
		//jQuery('#ring_judge_id'+index).val('');
		//jQuery('#ring_judge'+index).val('');
		//jQuery('#ring_judge'+index).focus();

        if(val == '3')
        {
            jQuery('#ring_congress_name'+index).css('visibility','visible');
            jQuery('#ring_congress_name'+index+'_criteria').css('visibility','visible');
        }
        else
        {
            jQuery('#ring_congress_name'+index).css('visibility','hidden');
            jQuery('#ring_congress_name'+index+'_criteria').css('visibility','hidden');
        }
    }
	
	function isUsingTICAapp() {
		if(jQuery('#show_uses_ticapp').is(':checked')) {
			jQuery( ".ring_clerks" ).show();
		} else {
			jQuery( ".ring_clerks" ).hide();
		}
	}

	jQuery().ready(function(){
		// sandy hack for start date , check days since current days if user is not admin
		var showid = '<?php echo $id?>';
		if(!showid){
		if(!isAdmin){
		jQuery('#show_start_date').on('change',function(){
			if(jQuery('#show_start_date').val()){
			//alert(jQuery('#show_start_date').val());	
			var selecteddate  = parseDate(jQuery('#show_start_date').val());
			var currentdate = new Date(); 
			var diff_in_days = DateDiff.inDays(currentdate,selecteddate);
			//alert('diff_in_days:'+diff_in_days);
			if(diff_in_days <= 30){
			jbox_alert('Start date of the show is less than 30 days from the current date. This is not allowed. See Standing Rule 202.4.6.1.');
			jQuery('#show_start_date').val('');
			jQuery('#show_start_date').focus();
			return;	
			}
			}
		});	
		
		}
		}
		
		
		
		//
		
		
		
		
		isUsingTICAapp();
		jQuery('#show_uses_ticapp').on('click', isUsingTICAapp);
		
		/*
		jQuery( "#venue_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=show.getvenues&tmpl=component',
		  select: function( event, ui ) {
		  	//jQuery( "#user_id" ).val(ui.item.key);
		  	jQuery( "#venue_name" ).val(ui.item.value);
		  	autofillvenuedata();
		  }
		});    
		*/
	    
		jQuery( "#username" ).autocomplete({
		  source: 'index.php?option=com_toes&task=show.getUsers&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#crosscheckusername" ).val(ui.item.key);
		  	jQuery( "#username" ).val(ui.item.value);
		  }
		});   
		 
		
		
	});

    function set_judges(x) 
    {
		jQuery( "#ring_judge"+x ).autocomplete({
		  source: 'index.php?option=com_toes&task=show.getJudges&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( '#ring_judge_id'+x ).val(ui.item.key);
		  	jQuery( '#ring_judge'+x ).val(ui.item.value);
		  }
		});    
    }

    function set_ring_clerks(x) 
    {
		jQuery( "#ring_clerk"+x ).autocomplete({
		  source: 'index.php?option=com_toes&task=show.getRingClerks&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( '#ring_clerk_id'+x ).val(ui.item.key);
		  	jQuery( '#ring_clerk'+x ).val(ui.item.value);
		  }
		});    
    }
	
    var today = <?php echo date('Y') . date('m') . date('d') ?>;

    var prev_end_date = org_end_date;

	<?php if(TOESHelper::isAdmin() && isset($this->item->show_id)) :?>
	invoice_cal = new Calendar({
        inputField: "show_cost_invoice_date",
        dateFormat: "%Y-%m-%d",
        //min:Calendar.dateToInt(today),
        trigger: "show_cost_invoice_date_selector",
        bottomBar: false,
        onFocus: function() {
        },
        onSelect: function() {
			jQuery('#show_cost_invoice_date').css('color', '#000');
			this.hide();
        }
    });
	<?php endif; ?>

    end_cal = new Calendar({
        inputField: "show_end_date",
        dateFormat: "%Y-%m-%d",
        min:Calendar.dateToInt(today),
        trigger: "end_date_selector",
        bottomBar: false,
        onFocus: function() {
            
        },
        onSelect: function() {
            if(org_end_date_int != this.selection.get())
            {
                if(is_date_change_approved || org_end_date == '0000-00-00')
                {
                    is_date_change_approved = true;
                    jQuery('#date_change_accepted').val('1');
                    jQuery('#show_end_date').css('color', '#000');
                    document.formvalidator.validate('#show_end_date');
                    prev_end_date = end_cal.selection.print("%Y-%m-%d",end_cal.selection.get());
                    this.hide();
                    changeRingdays();
                }
                else
                {
                    this.hide();
                    if(!is_date_change_approved)
					new jBox('Confirm',{
				        content: notification_messsage + "<br/>"+"Do you want to change the show date?",
				        width: '400px',
				        closeOnClick: 'box',
				        cancelButton : NO_BUTTON,
				        confirmButton: YES_BUTTON,
				        cancel: function(){
				        	jQuery('#show_end_date').val(org_end_date);
				        },
				        confirm: function() {
				            is_date_change_approved = true;
				            jQuery('#date_change_accepted').val('1');
				            jQuery('#show_end_date').css('color', '#000');
				            document.formvalidator.validate('#show_end_date');
				            prev_end_date = end_cal.selection.print("%Y-%m-%d",end_cal.selection.get());
				            changeRingdays();
			           }
                    }).open();
                }
            }
            else
            {
                this.hide();
                if(prev_end_date != end_cal.selection.print("%Y-%m-%d",end_cal.selection.get()))
                    changeRingdays();
            }
        }
    });        

    start_cal = new Calendar({
        inputField: "show_start_date",
        dateFormat: "%Y-%m-%d",
        trigger: "start_date_selector",
        min:Calendar.dateToInt(today),
        bottomBar: false,
        onSelect: function() {
            if(org_start_date_int != this.selection.get())
            {
				// sandy hack to check 30 day rule  - Standing Rule 202.4.6.1 - if user is not admin
				if(!isAdmin){
				var selecteddate  = parseDate(jQuery('#show_start_date').val());
				var currentdate = new Date(); 
				var diff_in_days = DateDiff.inDays(currentdate,selecteddate);
				if(diff_in_days <= 30){
				jbox_alert('Start date of the show is less than 30 days from the current date. This is not allowed. See Standing Rule 202.4.6.1.');
				jQuery('#show_start_date').val('');
				return;	
				} 
				} 
				//
				
                if(is_date_change_approved || org_start_date == '0000-00-00')
                {	
                    is_date_change_approved = true;
                    jQuery('#date_change_accepted').val('1');
                    jQuery('#show_start_date').css('color', '#000');
                    document.formvalidator.validate('#show_start_date');
                    var date = Calendar.intToDate(start_cal.selection.get());
                    
                    //prev_start_date = start_cal.selection.print("%Y-%m-%d",start_cal.selection.getLastDate());
                    prev_start_date = jQuery('#prev_start_date').val();
                    jQuery('#prev_start_date').val(start_cal.selection.print("%Y-%m-%d",start_cal.selection.get()));

                    if(jQuery('#show_end_date').val() && jQuery('#show_end_date').val() != 'YYYY-MM-DD')
                    {
                        var d1 = parseDate(prev_start_date);
                        var d2 = parseDate(jQuery('#show_end_date').val());
                        var diff = DateDiff.inDays(d1, d2);
                        var end_date = Calendar.intToDate(start_cal.selection.get());
                        end_date.setDate(end_date.getDate()+diff);
                        var edate = end_date.getDate();
                        if(edate<10)
                            edate = '0'+edate;
                        var month = end_date.getMonth()+1;
                        if(month<10)
                            month = '0'+month;
                        jQuery('#show_end_date').val(end_date.getFullYear()+'-'+month+'-'+edate);
                        document.formvalidator.validate('#show_end_date');
                        end_cal.moveTo(end_date, true);
                    }
                    else
                        end_cal.moveTo(Calendar.intToDate(start_cal.selection.get()), true);

                    end_cal.args.min = Calendar.intToDate(start_cal.selection.get());
                    //end_cal.moveTo(date, true);
                    var myDate = parseDate(jQuery('#prev_start_date').val());;
                    myDate.setDate(myDate.getDate()+2);
                    end_cal.args.max = myDate;

                    this.hide();
                    if(jQuery('#show_end_date').val())
                        changeRingdays();
                }
                else
                {
					
                    this.hide();
                    if(!is_date_change_approved)
					new jBox('Confirm',{
				        content: notification_messsage + "<br/>"+"Do you want to change the show date?",
				        width: '400px',
				        closeOnClick: 'box',
				        cancelButton : NO_BUTTON,
				        confirmButton: YES_BUTTON,
				        cancel: function(){
				        	jQuery('#show_start_date').val(org_start_date);
				        },
				        confirm: function() {
                            //this.close();
                            is_date_change_approved = true;
                            jQuery('#date_change_accepted').val('1');
                            jQuery('#show_start_date').css('color', '#000');
                            document.formvalidator.validate('#show_start_date');
                            //prev_start_date = start_cal.selection.print("%Y-%m-%d",start_cal.selection.getLastDate());
                            prev_start_date = jQuery('#prev_start_date').val();
                            jQuery('#prev_start_date').val(start_cal.selection.print("%Y-%m-%d",start_cal.selection.get()));
                            
                            if(jQuery('#show_end_date').val() && jQuery('#show_end_date').val() != 'YYYY-MM-DD')
                            {
                                var d1 = parseDate(prev_start_date);
                                var d2 = parseDate(jQuery('#show_end_date').val());
                                var diff = DateDiff.inDays(d1, d2);
                                var end_date = Calendar.intToDate(start_cal.selection.get());
                                end_date.setDate(end_date.getDate()+diff);
                                var edate = end_date.getDate();
                                if(edate<10)
                                    edate = '0'+edate;
                                var month = end_date.getMonth()+1;
                                if(month<10)
                                    month = '0'+month;
                                jQuery('#show_end_date').val(end_date.getFullYear()+'-'+month+'-'+edate);
                                document.formvalidator.validate('#show_end_date');
                                end_cal.moveTo(end_date, true);
                            }
                            else
                                end_cal.moveTo(Calendar.intToDate(start_cal.selection.get()), true);
                            
                            end_cal.args.min = Calendar.intToDate(start_cal.selection.get());
                            /*end_cal.moveTo(date, true);*/
                            var myDate = parseDate(jQuery('#prev_start_date').val());;
                            myDate.setDate(myDate.getDate()+2);
                            end_cal.args.max = myDate;
                            if(jQuery('#show_end_date').val())
                                changeRingdays();
                        }
                    }).open();
                }
                // show_end_date
                end_cal.moveTo(Calendar.intToDate(start_cal.selection.get()), true);
                
                
            }
            else
            {
                this.hide();
                if(start_cal.selection.print("%Y-%m-%d",start_cal.selection.get()) != prev_start_date)
                    changeRingdays();
            }
        }
    });        

    function setuserofficial_onload(user,x)
    {
        var c=jQuery('#countusername_'+x).val();
        var d=parseInt(c)+parseInt(1);
		
        var curval='';
        
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+user,
            type: 'post',
            async: false,
        }).done(function(responseText){
            if(responseText != '0')
            {
                curval = responseText+' ('+user+')';
            }
        });
        	
        if(curval=='')
            return;	
        var str = '<label class="addedusername_'+x+'" id="subusername_'+x+''+c+'">'+curval+'<span class="username_'+x+'Remove" onclick="removeusername('+c+','+x+');">x</span>'+'</label>';

        jQuery('#username_'+x+'place').html(jQuery('#username_'+x+'place').html() + str);

        jQuery('#innerusername_'+x).append('<input type="hidden" value="'+user+'" name="username_'+x+'[]" id="username_'+x+'data'+c+'"/>');
        jQuery('#countusername_'+x).val(d);
        user = '';
    }
    
    <?php if(isset($this->item->show_id) && $this->item->show_id) : ?>
                    
        var date = parseDate(jQuery('#show_start_date').val());
        jQuery('#detailed_start_date').html(date.toDateString());

        var date = parseDate(jQuery('#show_end_date').val());
        jQuery('#detailed_end_date').html(date.toDateString());
            
    <?php endif; ?>


	<?php
    if(isset($this->item->show_id)) {
	    foreach ($this->showofficialtypes as $so) {
	        if (count($so->showofficial)) {
	            foreach ($so->showofficial as $sso) { 
	            ?>
	                setuserofficial_onload('<?php echo $sso->showofficial; ?>','<?php echo $so->show_official_type_id; ?>');
	            <?php
	            }
	        }
	    }
	    ?>
	    autofillvenuedata();
	    <?php
	}
	?>
</script>
