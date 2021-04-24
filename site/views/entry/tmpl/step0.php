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
$params = JComponentHelper::getParams('com_toes');
$googlemapkey = $params->get('map_key');

?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googlemapkey;?>&libraries=places&callback=initAutocomplete"
        async defer></script>
<div id="select-user">
    <h3><?php echo JText::_('NEW_ENTRY') ?></h3>

    <label class="label"><?php echo JText::_("COM_TOES_SELECT_USER"); ?></label>
    <div>
		<input type="text" size="30" value="" id="user_name" name="user_name" />
		<input type="hidden" name="user_id" id="user_id" value="" />

    </div>

    <br/>	
    <a href="javascript:void(0);" onclick="add_new_user();"><?php echo JText::_('COM_TOES_ADD_NEW_USER'); ?></a>
    <br/>	

    <div class="fieldbg" >
        <input type="hidden" value="entry" name="source" id="source" />
        <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
        <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
        <input type="hidden" value="<?php echo @$this->entry->show_id; ?>" name="show_id" id="show_id" />
        <input type="hidden" id="add_entry_user" name="add_entry_user" value="<?php echo @$this->entry->user_id; ?>" />
        <input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
        <input onclick="next_step('step0');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
    </div>
</div>

<div id="new-user" style="display: none;">
    <h3><?php echo JText::_("COM_TOES_ADD_NEW_USER"); ?></h3>
    <div class="clr"></div>
    <label for="firstname" class="label"><?php echo JText::_("COM_TOES_USER_FIRST_NAME"); ?></label>
    <span>
        <input type="text" name="firstname" id="firstname" value="" />
    </span>
    <div class="clr"></div>
    <label for="lastname" class="label"><?php echo JText::_("COM_TOES_USER_LAST_NAME"); ?></label>
    <span>
        <input type="text" name="lastname" id="lastname" value="" />
    </span>
    <div class="clr"></div>
    <label for="username" class="label"><?php echo JText::_("COM_TOES_USER_USERNAME"); ?></label>
    <span>
        <input type="text" name="username" id="username" value="" />
    </span>
    <div class="clr"></div>
    <label for="email" class="label"><?php echo JText::_("COM_TOES_USER_EMAIL"); ?></label>
    <span>
        <input type="text" name="email" id="email" value="" />
    </span>
    <div class="clr"></div>
    <label for="search_address" class="label"><?php echo JText::_("COM_TOES_SEARCH_ADDRESS"); ?></label>
    <span>
        <input type="text" name="search_address" id="search_address" value="" />
    </span>
    <div class="clr"></div>
    <label for="address1" class="label"><?php echo JText::_("COM_TOES_USER_ADDRESS1"); ?></label>
    <span>
        <input type="text" name="address1" id="address1" value="" />
    </span>
    <div class="clr"></div>
    <label for="address2" class="label"><?php echo JText::_("COM_TOES_USER_ADDRESS2"); ?></label>
    <span>
        <input type="text" name="address2" id="address2" value="" />
    </span>
    <div class="clr"></div>
    <label for="address3" class="label"><?php echo JText::_("COM_TOES_USER_ADDRESS3"); ?></label>
    <span>
        <input type="text" name="address3" id="address3" value="" />
    </span>
    <div class="clr"></div>
    <label for="country" class="label"><?php echo JText::_("COM_TOES_USER_COUNTRY"); ?></label>
    <span>
        <input type="text" name="country_name" id="country_name" value="" />
    </span>
    <div class="clr"></div>
	<div class="state_div">
		<label for="state" class="label"><?php echo JText::_("COM_TOES_USER_STATE"); ?></label>
		<span>
			<input type="text" name="state_name" id="state_name" value="" />
		</span>
	</div>
    <div class="clr"></div>
    <label for="city" class="label"><?php echo JText::_("COM_TOES_USER_CITY"); ?></label>
    <span>
        <input type="text" name="city_name" id="city_name" value="" />
    </span>
    <div class="clr"></div>
    <label for="zip" class="label"><?php echo JText::_("COM_TOES_USER_ZIP"); ?></label>
    <span>
        <input type="text" name="zip" id="zip" value="" />
    </span>
    <div class="clr"></div>
    <label for="phonenumber" class="label"><?php echo JText::_("COM_TOES_USER_PHONENUMBER"); ?></label>
    <span>
        <input type="text" name="phonenumber" id="phonenumber" value="" />
    </span>
    <div class="clr"></div>
    <label for="phonenumber" class="label"><?php echo JText::_("COM_TOES_SELECT_COMPETITIVE_REGION"); ?></label>
    <span>
		<?php echo JHtml::_('select.genericlist', $this->regions, 'tica_region', 'style="width:200px;"', 'key', 'value'); ?>
    </span>
    <div class="clr"></div>
    <input onclick="cancel_new_user();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <input onclick="save_new_user();" type="button" name="button" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
	<input type="hidden" name="country" id="country" value="" />
	<input type="hidden" name="state" id="state" value="" />
	<input type="hidden" name="city" id="city" value="" />
    <input type="hidden" name="lat" id="lat" />
    <input type="hidden" name="lng" id="lng" />
</div>

<script type="text/javascript" >
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
        
        document.getElementById('lat').value = '';
        document.getElementById('lng').value = '';
        // if(addressType = 'name') 
        // {
        //     var placename = place.name;
        //     if(placename)
        //     {
        //         document.getElementById('venue_name').value = placename;
        //     }
        // }
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

        document.getElementById('address1').value = '';
        document.getElementById('address2').value = '';
        document.getElementById('address3').value = '';
        document.getElementById('city_name').value = '';
        document.getElementById('country_name').value = '';
        document.getElementById('state_name').value = '';
        document.getElementById('zip').value = '';
        
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];			
            if(addressType == 'street_number') 
            {
                var address1 = place.address_components[i].short_name;
                
                if(address1)
                {
                    document.getElementById('address1').value = address1;
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
                    document.getElementById('address1').value = address;
                }	
            }
            if(addressType == 'locality') 
            {
                var city = place.address_components[i].short_name;
        
                document.getElementById('city_name').value = city;
            }
            if(addressType == 'sublocality_level_1') 
            {
                var address2 = place.address_components[i].short_name;
        
                document.getElementById('address2').value = address2;
            }
            if(addressType == 'sublocality_level_2') 
            {
                var address3 = place.address_components[i].short_name;
        
                document.getElementById('address3').value = address3;
            }
            if(addressType == 'country') 
            {
                var country = place.address_components[i].long_name;
        
                document.getElementById('country_name').value = country;
            }
            if(addressType == 'administrative_area_level_1') 
            {
                var state = place.address_components[i].long_name;
        
                document.getElementById('state_name').value = state;
            }
            if(addressType == 'postal_code') 
            {
                var zipcode = place.address_components[i].long_name;
        
                document.getElementById('zip').value = zipcode;
            }
        }
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

</script>
