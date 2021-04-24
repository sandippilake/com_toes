/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//site_root="https://www.i-tica.com/";
site_root = SiteURL;

var myWidth;
var myHeight;
var congress_filter_modal = '';

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

if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function (obj, fromIndex) {
    if (fromIndex == null) {
        fromIndex = 0;
    } else if (fromIndex < 0) {
        fromIndex = Math.max(0, this.length + fromIndex);
    }
    for (var i = fromIndex, j = this.length; i < j; i++) {
        if (this[i] === obj)
            return i;
    }
    return -1;
  };
}

/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
		return string;
	}
};

jQuery.fn.scrollIntoView = function() {
	if(this.length) {
		var top = this.offset().top;
		jQuery('html,body').animate({scrollTop: top },'slow');
	}
};

function approve_entry(ele)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entryclerk.updateStatus&status=Accepted&entry_id='+ids[0],
        type: 'post'
	}).done(function(responseText){
		responseText = responseText.trim();
        if(responseText === '1')
        {
            if(jQuery('#show-'+ids[1]).length)
            {
                jQuery('.show-details').html('');
                jQuery('.show-details').hide();

                jQuery('#show-'+ids[1]).show();
                jQuery('#show-'+ids[1]).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                jQuery.ajax({
                    url: 'index.php?option=com_toes&view=show&layout=short&id='+ids[1]+'&tmpl=component',
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#show-'+ids[1]).html(responseText);
                    jQuery('#show-'+ids[1]).scrollIntoView();
                });
            }
            else
                location.reload();
        }
        else
            jbox_alert(responseText);
    });
}

function reenter_entry(ele)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entryclerk.updateStatus&status=Accepted&entry_id='+ids[0],
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        if(responseText === '1')
        {
            if(jQuery('#show-'+ids[1]).length)
            {
                jQuery('.show-details').html('');
                jQuery('.show-details').hide();

                jQuery('#show-'+ids[1]).show();
                jQuery('#show-'+ids[1]).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                jQuery.ajax({
                    url: 'index.php?option=com_toes&view=show&layout=short&id='+ids[1]+'&tmpl=component',
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#show-'+ids[1]).html(responseText);
                    jQuery('#show-'+ids[1]).scrollIntoView();
                });
            }
            else
                location.reload();
        }
        else
            jbox_alert(responseText);
    });
}

function cancel_entry(ele,confirm_question)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

	new jBox('Confirm',{
        content: confirm_question,
        width: '400px',
        cancelButton : NO_BUTTON,
        confirmButton: YES_BUTTON,
        confirm: function() {
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entryclerk.updateStatus&status=Cancelled&entry_id='+ids[0],
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText === '1')
                {
                    if(jQuery('#show-'+ids[1]).length)
                    {
                        jQuery('.show-details').html('');
                        jQuery('.show-details').hide();

                        jQuery('#show-'+ids[1]).show();
                        jQuery('#show-'+ids[1]).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                        jQuery.ajax({
                            url: 'index.php?option=com_toes&view=show&layout=short&id='+ids[1]+'&tmpl=component',
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            jQuery('#show-'+ids[1]).html(responseText);
                            jQuery('#show-'+ids[1]).scrollIntoView();
                        });
                    }
                    else
                        location.reload();
                }
                else
                    jbox_alert(responseText);
        	});
        }
    }).open();    
}

function delete_entry(ele,confirm_question)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

 	new jBox('Confirm',{
        content: confirm_question,
        width: '400px',
        cancelButton : NO_BUTTON,
        confirmButton: YES_BUTTON,
        confirm: function() {
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.delete_entry&entry_id='+ids[0],
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText === '1')
                {
					location.reload();
                }
                else
                    jbox_alert(responseText);
        	});
		},    
    }).open();
}

function delete_placeholder(ele,confirm_question)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

 	new jBox('Confirm',{
        content: confirm_question,
        width: '400px',
        cancelButton : NO_BUTTON,
        confirmButton: YES_BUTTON,
        confirm: function() {
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=placeholder.delete_placeholder&placeholder_day_id='+ids[0],
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText === '1')
                {
					location.reload();
                }
                else
                    jbox_alert(responseText);
            });
    	}
    }).open();
}

function cancel_edit_summary()
{
    jQuery('.edit-summary-div').html('');
}

function cancel_edit_fees()
{
    jQuery('.edit-fees-div').html('');
}

function edit_fees(id)
{
    jQuery('.edit-fees-div').html('');
    jQuery('#edit-fees-'+id+'-div').html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');
    jQuery.ajax({
        url: 'index.php?option=com_toes&view=entry&layout=edit_fees&tmpl=component',
        data: 'summary_id='+id,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#edit-fees-'+id+'-div').html(responseText);
        jQuery('#edit-fees-'+id+'-div').scrollIntoView();
    });
}

function save_fees(summary_id)
{
    var summary_benching_area = jQuery('input[name=summary_benching_area_'+summary_id+']').val();
    var summary_total_fees = jQuery('input[name=summary_total_fees_'+summary_id+']').val();
    var summary_fees_paid = jQuery('input[name=summary_fees_paid_'+summary_id+']').val();

	var summary_entry_clerk_note = jQuery('textarea[name=summary_entry_clerk_note_'+summary_id+']').val();
	var summary_entry_clerk_private_note = jQuery('textarea[name=summary_entry_clerk_private_note_'+summary_id+']').val();
    
	var exp = /^(\d+)(,|.\d{0,2})?$/;

    if(String(summary_total_fees).search (exp) == -1)
	{
		jbox_alert(PLEASE_ENTER_VALID_NUMBER_FOR_TOTAL_FEE);
		return false;
	}
    if(String(summary_fees_paid).search (exp) == -1)
	{
		jbox_alert(PLEASE_ENTER_VALID_NUMBER_FOR_FEE_PAID);
		return false;
	}
    
	summary_total_fees = String(summary_total_fees).replace(',','.');
	summary_fees_paid = String(summary_fees_paid).replace(',','.');
	
	summary_benching_area = Base64.encode(String(summary_benching_area));
	summary_entry_clerk_note = Base64.encode(String(summary_entry_clerk_note));
	summary_entry_clerk_private_note = Base64.encode(String(summary_entry_clerk_private_note));
	
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.save_fees&tmpl=component',
        data: 'summary_total_fees='+summary_total_fees+'&summary_fees_paid='+summary_fees_paid+'&summary_benching_area='+summary_benching_area+'&summary_entry_clerk_note='+summary_entry_clerk_note+'&summary_entry_clerk_private_note='+summary_entry_clerk_private_note+'&summary_id='+summary_id,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
		if(responseText === '1')
			location.reload();
		else
            jbox_alert(responseText);
    }).fail(function(text){
        jbox_alert(text);
    });
	return true;
}

function edit_summary(id)
{
    jQuery('.edit-summary-div').html('');
    jQuery('#edit-summary-'+id+'-div').html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');
    jQuery.ajax({
        url: 'index.php?option=com_toes&view=entry&layout=edit_summary&tmpl=component',
        data: 'summary_id='+id,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#edit-summary-'+id+'-div').html(responseText);
        jQuery('#edit-summary-'+id+'-div').scrollIntoView();
    });
}

function save_summary()
{
    var single_cages = parseInt(jQuery('input[name=summary_single_cages]').val());
    var double_cages = parseInt(jQuery('input[name=summary_double_cages]').val());
    var personal_cage = jQuery('select[name=summary_personal_cages]').val();
    var grooming_space = jQuery('select[name=summary_grooming_space]').val();
    var benching_request = jQuery('textarea[name=summary_benching_request]').val();
    var remark = jQuery('textarea[name=summary_remarks]').val();
    var summary_id = jQuery('input[name=summary_id]').val();
    var show_id = jQuery('input[name=show_id]').val();
    var summary_user = parseInt(jQuery('input[name=summary_user]').val());
    var current_user = parseInt(jQuery('input[name=current_user]').val());
    
	if(isNaN(single_cages))
	{
		jbox_alert(PLEASE_ENTER_VALID_SINGLE_CAGES);
		return false;
	}
	if(isNaN(double_cages))
	{
		jbox_alert(PLEASE_ENTER_VALID_DOUBLE_CAGES);
		return false;
	}
	if(single_cages <=0 && double_cages <= 0)
	{
		jbox_alert(NO_SPACES_SELECTED);
		return false;
	}
	
	remark = Base64.encode(String(remark));
	benching_request = Base64.encode(String(benching_request));
	
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.save_summary&tmpl=component',
        data: 'single_cages='+single_cages+'&double_cages='+double_cages+'&personal_cage='+personal_cage+'&grooming_space='+grooming_space+'&benching_request='+benching_request+'&remark='+remark+'&summary_id='+summary_id,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        if(summary_user === current_user)
        {
            if(jQuery('#show-'+show_id).length)
            {
                jQuery('.show-details').html('');
                jQuery('.show-details').hide();

                jQuery('#show-'+show_id).show();
                jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                jQuery.ajax({
                    url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#show-'+show_id).html(responseText);
                    jQuery('#show-'+show_id).scrollIntoView();
                });
            }
            else
                location.reload();
        }
        else
            location.reload();
    }).fail(function(text){
        jbox_alert(text);
    });
	return true;
}

function add_new_entry(id, parent_div, isNew)
{ 
    jQuery('.add-entry-div').html('');
    
    jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

    if(jQuery('.add-placeholder-div').length)
        jQuery('.add-placeholder-div').html('');
    
    if(jQuery('.cat-details').length)
        jQuery('.cat-details').html('');

	var data = '';
	if(isNew)
		data = 'step=step1&clear_session=1&show_id='+id+'&parent_div='+parent_div+'&type=new';
	else
		data = 'step=step1&show_id='+id+'&parent_div='+parent_div+'&type=';

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
        data: data,
        type: 'post',
	}).done(function(responseText){
		responseText = responseText.trim();
	    jQuery('#'+parent_div).html(responseText);
	    jQuery('#'+parent_div).scrollIntoView();
	});
}

function edit_entry(id, parent_div)
{
    id = id.split(';');
    
    var cat_id = id[0];
    var show_id = id[1];
    var user_id = id[2];
    
    jQuery('.add-entry-div').html('');
    
    jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

    if(jQuery('.add-placeholder-div').length)
        jQuery('.add-placeholder-div').html('');
    
    if(jQuery('.cat-details').length)
        jQuery('.cat-details').html('');

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
        data: 'step=step2&clear_session=1&edit=1&user_id='+user_id+'&cat_id='+cat_id+'&show_id='+show_id+'&parent_div='+parent_div+'&type=edit',
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#'+parent_div).html(responseText);
        jQuery('#'+parent_div).scrollIntoView();
    });
}

function cancel_edit_entry(){
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.cancel_edit_entry&tmpl=component',
        type: 'post',
	}).done(function(responseText){
		responseText = responseText.trim();
		location.reload();
	});
    
    /*
    var show_id = parseInt(jQuery('input[name=show_id]').val());
    jQuery('.show-details').html('');
    jQuery('.show-details').hide();

    if(jQuery('#show-'+show_id).length)
    {
        jQuery('#show-'+show_id).show();
        jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

        jQuery.ajax({
            url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#show-'+show_id).html(responseText);
            jQuery('#show-'+show_id).scrollIntoView();
        });
    }
    else 
    {
        location.reload();
    }
    */
}

function participate_in_congress(val)
{
    var type = jQuery('input[name=type]').val();
    var parent_div = jQuery('#parent_div').val();
    var user_id = jQuery('#add_entry_user').val();
    var congress = '';
    var edit = '';
    if(val)
    {
        if(jQuery('#congress_count').val() > 0)
        {
            jQuery('#participate_in_congress_div').hide();
            jQuery('#select_congress_div').show();
        }
        else
        {
            congress = Array();
            jQuery('input[name^=congress]:checked').each(function(){
                congress.push(jQuery(this).val());
            });

            congress = congress.join(',');
            edit = parseInt(jQuery('input[name=edit]').val());
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'congress='+congress+'&user_id='+user_id+'&step=step5&edit='+edit+'&participate_in_congress=1&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
        }
    }
    else
    {
        congress = '';
        edit = parseInt(jQuery('input[name=edit]').val());
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
            data: 'congress='+congress+'&user_id='+user_id+'&step=step5&edit='+edit+'&participate_in_congress=0&parent_div='+parent_div+'&type='+type,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#'+parent_div).html(responseText);
            jQuery('#'+parent_div).scrollIntoView();
        });
    }
}

function next_step(step)
{
    var type = jQuery('input[name=type]').val();
    var parent_div = jQuery('#parent_div').val();
    var user_id = jQuery('#add_entry_user').val();
    var edit = 0;
    switch(step)
    {
        case 'step0':
            var selected_user_id = parseInt(jQuery('input[name=user_id]').val());
            if(selected_user_id)
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                    data: 'user_id='+selected_user_id+'&step=step1'+'&parent_div='+parent_div+'&type='+type,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html(responseText);
                    jQuery('#'+parent_div).scrollIntoView();
                });
            }
            else
            {
                jbox_alert(PLEASE_SELECT_USER);
            }
            break;
        case 'step1':
            var cat_id = parseInt(jQuery('input[name=cat_id]:checked').val());
            if(cat_id)
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                    data: 'cat_id='+cat_id+'&user_id='+user_id+'&step=step2'+'&parent_div='+parent_div+'&type='+type,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html(responseText);
                    jQuery('#'+parent_div).scrollIntoView();
                });
            }
            else
            {
                jbox_alert(PLEASE_SELECT_CAT);
            }
            break;
        case 'step2':
            var showdays = Array();
            jQuery('input[name^=showday]:checked').each(function(){
                showdays.push(jQuery(this).val());
            });

            var entry_for_AM = Array();
            jQuery('input[name^=entry_participates_AM]:checked').each(function(){
                entry_for_AM.push(jQuery(this).val());
            });

            var entry_for_PM = Array();
            jQuery('input[name^=entry_participates_PM]:checked').each(function(){
                entry_for_PM.push(jQuery(this).val());
            });

            showdays = showdays.join(',');
			entry_for_AM = entry_for_AM.join(',');
			entry_for_PM = entry_for_PM.join(',');
            edit = parseInt(jQuery('input[name=edit]').val());
            if(showdays)
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                    data: 'showdays='+showdays+'&entry_for_AM='+entry_for_AM+'&entry_for_PM='+entry_for_PM+'&user_id='+user_id+'&step=step3&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html(responseText);
                    jQuery('#'+parent_div).scrollIntoView();
                });
            }
            else
            {
                jbox_alert(PLEASE_SELECT_SHOWDAYS);
            }
            break;
        case 'step3':
            //var exh_only = parseInt(jQuery('input[name=exh_only]:checked').val());
            var exh_only = jQuery('select[name=exh_only]').val();
            var for_sale = parseInt(jQuery('input[name=for_sale]:checked').val());
            var agent_name = jQuery('input[name=agent_name]').val();
            edit = parseInt(jQuery('input[name=edit]').val());

           /* if(isNaN(exh_only))
                exh_only=0;
            else
                exh_only=1;*/

            if(isNaN(for_sale))
                for_sale=0;
            else
                for_sale=1;
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'exh_only='+exh_only+'&for_sale='+for_sale+'&agent_name='+agent_name+'&user_id='+user_id+'&step=step4&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
            break;
        case 'step4':
            var congress = Array();
            jQuery('input[name^=congress]:checked').each(function(){
                congress.push(jQuery(this).val());
            });

            congress = congress.join(',');
            edit = parseInt(jQuery('input[name=edit]').val());
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'congress='+congress+'&user_id='+user_id+'&step=step5&edit='+edit+'&participate_in_congress=1'+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
            break;
        case 'step5' :
			var accept_rule = parseInt(jQuery('input[name=accept_rule]:checked').val());
			
            if(isNaN(accept_rule))
			{
				jbox_alert(ACCEPT_TICA_SHOW_RULES_ERROR);
				break;
			}
			
			jQuery('#save_btn').prop('disabled',1);
			
            var single_cages = parseInt(jQuery('input[name=summary_single_cages]').val());
            var double_cages = parseInt(jQuery('input[name=summary_double_cages]').val());
            var personal_cage = jQuery('select[name=summary_personal_cages]').val();
            var grooming_space = jQuery('select[name=summary_grooming_space]').val();
            var benching_request = jQuery('textarea[name=summary_benching_request]').val();
            var remark = jQuery('textarea[name=summary_remarks]').val();
            var show_id = parseInt(jQuery('input[name=show_id]').val());
            var summary_user = parseInt(jQuery('input[name=summary_user]').val());
            var current_user = parseInt(jQuery('input[name=current_user]').val());
            edit = parseInt(jQuery('input[name=edit]').val());
            
			if(isNaN(single_cages))
			{
				jbox_alert(PLEASE_ENTER_VALID_SINGLE_CAGES);
				break;
			}
			
			if(isNaN(double_cages))
			{
				jbox_alert(PLEASE_ENTER_VALID_DOUBLE_CAGES);
				break;
			}
			
			if(single_cages <=0 && double_cages <= 0)
			{
				jbox_alert(NO_SPACES_SELECTED);
				break;
			}
			
			remark = Base64.encode(String(remark));
			benching_request = Base64.encode(String(benching_request));
			
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'single_cages='+single_cages+'&double_cages='+double_cages+'&personal_cage='+personal_cage+'&grooming_space='+grooming_space+'&benching_request='+benching_request+'&remark='+remark+'&step=final&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText !== '1')
                {
                    jbox_alert(responseText);
                } else {
					if(jQuery('#show-'+show_id).length)
					{
						jQuery('.show-details').html('');
						jQuery('.show-details').hide();

						jQuery('#show-'+show_id).show();
						jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

						jQuery.ajax({
							url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
							type: 'post',
						}).done(function(responseText){
							responseText = responseText.trim();
							jQuery('#show-'+show_id).html(responseText);
							jQuery('#show-'+show_id).scrollIntoView();
						});
					} else {
						location.reload();
					}
				}
            });
            break;
    }
}

function previous_step(step)
{
    var show_id = parseInt(jQuery('input[name=show_id]').val());
    var type = jQuery('input[name=type]').val();
    var user_id = jQuery('#add_entry_user').val();
    var parent_div = jQuery('#parent_div').val();
    var edit = 0;
    switch(step)
    {
        case 'step0':
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'step=init'+'&parent_div='+parent_div,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html('');
                if(jQuery('#show-'+show_id).length)
                {
                    jQuery('.show-details').html('');
                    jQuery('.show-details').hide();

                    jQuery('#show-'+show_id).show();
                    jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                    jQuery.ajax({
                        url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
                        type: 'post',
                    }).done(function(responseText){
						responseText = responseText.trim();
                        jQuery('#show-'+show_id).html(responseText);
                        jQuery('#show-'+show_id).scrollIntoView();
                    });
                    
                }
            });
            break;
        case 'step1':
            if(type == "third_party")
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                    data: 'step=step0&dir=prev&user_id='+user_id+'&parent_div='+parent_div+'&type='+type,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html(responseText);
                    jQuery('#'+parent_div).scrollIntoView();
                });
            }
            else
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                    data: 'step=init'+'&parent_div='+parent_div,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html('');
                    if(jQuery('#show-'+show_id).length)
                    {
                        jQuery('.show-details').html('');
                        jQuery('.show-details').hide();

                        jQuery('#show-'+show_id).show();
                        jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                        jQuery.ajax({
                            url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            jQuery('#show-'+show_id).html(responseText);
                            jQuery('#show-'+show_id).scrollIntoView();
                        });

                    }
                });
            }
            break;
        case 'step2':
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'step=step1&dir=prev&user_id='+user_id+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
            break;
        case 'step3':
            edit = parseInt(jQuery('input[name=edit]').val());
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'step=step2&dir=prev&user_id='+user_id+'&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
            break;
        case 'step4':
            edit = parseInt(jQuery('input[name=edit]').val());
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'step=step3&dir=prev&user_id='+user_id+'&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
            break;
        case 'step5' :
            edit = parseInt(jQuery('input[name=edit]').val());
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                data: 'step=step4&dir=prev&user_id='+user_id+'&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
            break;
    }
}

function jbox_alert(text)
{
    new jBox('Confirm',{
        content: '<div style="min-height:50px;min-width:150px;text-align:center">'+text+'</div>',
        closeOnClick: 'box',
        confirmButton: OK_BUTTON,
        confirm: function() {}
    }).open();
}

function jbox_notice(text, color_code)
{
    new jBox('Notice',{
        color: color_code,
        content: text
    });
}

function copy_new_field(field,value)
{
    if(field == 'copy_cat_new_trait')
    {
		if(value == '0')
			jQuery('#'+field).prop('checked',false);
		else
			jQuery('#'+field).prop('checked',true);
        changeCategory();
        changeDivision();
        changeColor();
    }
    else
        jQuery('#'+field).val(value);
}

function view_entry_details(ele,parent_div,exhibitor)
{
    var rel = jQuery(ele).attr('rel');

    /*if(jQuery('.add-entry-div').length)
        jQuery('.add-entry-div').html('');*/

    if(jQuery('#'+parent_div+':visible').length)
    {
        jQuery('.cat-details').html('');
        jQuery('.cat-details').hide();
        return;
    }

    jQuery('.cat-details').hide();
    jQuery('.cat-details').html('');

    jQuery('#'+parent_div).show();
    jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

    jQuery.ajax({
        url: 'index.php?option=com_toes&view=entry&layout=details&id='+rel+'&tmpl=component&exhibitor='+exhibitor+'&parent_div='+parent_div,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#'+parent_div).html(responseText);
        jQuery('#'+parent_div).scrollIntoView();
        changeCategory();
        changeDivision();
        changeColor();
    });
}

function save_cat_detail_changes(entry_id, cat_id)
{
    var data = '';

    data += "&breed="+jQuery('#cat_breed').val();
	
	var new_trait = parseInt(jQuery('input[name=cat_new_trait]:checked').val());
	if(isNaN(new_trait))
		new_trait=0;
	else
		new_trait=1;	
    data += "&new_trait="+new_trait;
    data += "&category="+jQuery('#cat_category').val();
    data += "&division="+jQuery('#cat_division').val();
    data += "&color="+jQuery('#cat_color').val();
	
	var cat_name = jQuery('#cat_name').val();
	cat_name = Base64.encode(String(cat_name));
    data += "&cat_name="+cat_name;

	data += "&gender="+jQuery('#cat_gender').val();
	data += "&hairlength="+jQuery('#cat_hair_length').val();
    data += "&rgnnumber="+jQuery('#cat_registration_number').val();
    data += "&dob="+jQuery('#cat_date_of_birth').val();
    data += "&prefix="+jQuery('#cat_prefix').val();
    data += "&title="+jQuery('#cat_title').val();
    data += "&suffix="+jQuery('#cat_suffix').val();

	var sire_name = jQuery('#cat_sire').val();
	sire_name = Base64.encode(String(sire_name));
    data += "&sire="+sire_name;

	var dam_name = jQuery('#cat_dam').val();
	dam_name = Base64.encode(String(dam_name));
    data += "&dam="+dam_name;

	var breeder_name = jQuery('#cat_breeder').val();
	breeder_name = Base64.encode(String(breeder_name));
    data += "&breeder="+breeder_name;

	var owner_name = jQuery('#cat_owner').val();
	owner_name = Base64.encode(String(owner_name));
    data += "&owner="+owner_name;

	var lessee_name = jQuery('#cat_lessee').val();
	lessee_name = Base64.encode(String(lessee_name));
    data += "&lessee="+lessee_name;
	
    data += "&region="+jQuery('#cat_competitive_region').val();
    
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=cat.saveChangedDetails&tmpl=component',
        data: 'entry_id='+entry_id+'&cat_id='+cat_id+data,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#loader').hide();
        
        if(responseText !== '1')
        {
            jbox_alert(responseText.replace('Error: ',''));
        }
        else
        {
        	new jBox('Confirm',{
		        content: "Cat changes saved successfully!!",
		        cancelButton : NO_BUTTON,
		        confirmButton: YES_BUTTON,
		        confirm: function() {
		            window.location = site_root+'index.php?option=com_toes&view=cats';
		        }, 
		        cancel: function() {
		            window.location = site_root+'index.php?option=com_toes&view=cats'; 
		        } 
			}).open();
        }
    });

    jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
    jQuery('#loader').css('padding-top', (myHeight/2)+'px');
    jQuery('#loader').show();
}


function cancel_cat_detail_changes(entry_id, cat_id)
{
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=cat.cancelChangedDetails&tmpl=component',
        data: 'entry_id='+entry_id+'&cat_id='+cat_id,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#loader').hide();
        
        if(responseText !== '1')
        {
            jbox_alert(responseText.replace('Error: ',''));
        }
        else
        {
        	new jBox('Confirm',{
		        content: "Cat changes discarded.",
		        cancelButton : NO_BUTTON,
		        confirmButton: YES_BUTTON,
		        confirm: function() {
		            window.location = site_root+'index.php?option=com_toes&view=cats';
		        }, 
		        cancel: function() {
		            window.location = site_root+'index.php?option=com_toes&view=cats';
		        } 
			}).open();
        }
    });

    jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
    jQuery('#loader').css('padding-top', (myHeight/2)+'px');
    jQuery('#loader').show();
}

function save_entry_details(entry_id, cat_id, show_id)
{
    var data = '';

    var parent_div = jQuery('#parent_div').val();

    data += "&breed="+jQuery('#copy_cat_breed').val();
	
	var new_trait = parseInt(jQuery('input[name=copy_cat_new_trait]:checked').val());
	if(isNaN(new_trait))
		new_trait=0;
	else
		new_trait=1;	
    data += "&new_trait="+new_trait;
    data += "&category="+jQuery('#copy_cat_category').val();
    data += "&division="+jQuery('#copy_cat_division').val();
    data += "&color="+jQuery('#copy_cat_color').val();
	
	var cat_name = jQuery('#copy_cat_name').val();
	cat_name = Base64.encode(String(cat_name));
    data += "&cat_name="+cat_name;

	data += "&gender="+jQuery('#copy_cat_gender').val();
	data += "&hairlength="+jQuery('#copy_cat_hair_length').val();
    data += "&rgnnumber="+jQuery('#copy_cat_registration_number').val();
    data += "&dob="+jQuery('#copy_cat_date_of_birth').val();
    data += "&prefix="+jQuery('#copy_cat_prefix').val();
    data += "&title="+jQuery('#copy_cat_title').val();
    data += "&suffix="+jQuery('#copy_cat_suffix').val();

	var sire_name = jQuery('#copy_cat_sire_name').val();
	sire_name = Base64.encode(String(sire_name));
    data += "&sire="+sire_name;

	var dam_name = jQuery('#copy_cat_dam_name').val();
	dam_name = Base64.encode(String(dam_name));
    data += "&dam="+dam_name;

	var breeder_name = jQuery('#copy_cat_breeder_name').val();
	breeder_name = Base64.encode(String(breeder_name));
    data += "&breeder="+breeder_name;

	var owner_name = jQuery('#copy_cat_owner_name').val();
	owner_name = Base64.encode(String(owner_name));
    data += "&owner="+owner_name;

	var lessee_name = jQuery('#copy_cat_lessee_name').val();
	lessee_name = Base64.encode(String(lessee_name));
    data += "&lessee="+lessee_name;

	var agent_name = jQuery('#copy_cat_agent_name').val();
	agent_name = Base64.encode(String(agent_name));
    data += "&agent="+agent_name;
	
    data += "&region="+jQuery('#copy_cat_competitive_region').val();
    
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.saveEntryDetails&tmpl=component',
        data: 'show_id='+show_id+'&cat_id='+cat_id+data,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#loader').hide();
        
        if(responseText !== '1')
        {
            if(responseText.indexOf('Error: ') !== -1)
            {
                jbox_alert(responseText.replace('Error: ',''));
            }
            else
            {
                var congress_ids = responseText.replace('Congress: ','');
                var html = '<input value="'+YES_BUTTON+'" type="button" onClick="participateInCongress(\''+show_id+'\',\''+cat_id+'\',\''+congress_ids+'\',\''+parent_div+'\')"/> &nbsp;';
                html += '<input value="'+NO_BUTTON+'"  type="button" onClick="jQuery(\'#\'+\''+parent_div+'\').html(\'\');location.reload();"/>';

                jQuery('#'+parent_div).html('<div><br/><br/>'+COM_TOES_SHOULD_CAT_PARTICIPATE_IN_CONGRESS+'<br/><br/>'+html+'<br/><br/></div>');
				jQuery('#'+parent_div).scrollIntoView();
            }
        }
        else
        {
            if(jQuery('.cat-details').length)
            {
                jQuery('.cat-details').hide();
                jQuery('.cat-details').html('');
            }
			
            jQuery('#'+parent_div).show();
            jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

            jQuery.ajax({
                url: 'index.php?option=com_toes&view=entry&layout=details&id='+entry_id+'&tmpl=component&parent_div='+parent_div,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
        }
    });

    jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
    jQuery('#loader').css('padding-top', (myHeight/2)+'px');
    jQuery('#loader').show();
}

function cancel_entry_details()
{
    jQuery('.cat-details').html('');
    jQuery('.cat-details').hide();
    return;
}

function changeCategory()
{
    var breed = 0;
    if(!jQuery('#copy_cat_new_trait').is(':checked'))
        breed = jQuery('#copy_cat_breed').val();

    var category = jQuery('#copy_cat_category').val();
    var exhibitor = jQuery('#exhibitor').val();

    var myElement = jQuery('#cat_category');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changecatlist&breed='+breed+'&category='+category+'&exhibitor='+exhibitor+'&tmpl=component',
        type: 'get',
        async: false,
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
        changeDivision();
    }).fail(function(){
    });
}

function changeDivision()
{
    var breed = 0;
    if(!jQuery('#copy_cat_new_trait').is(':checked'))
        breed = jQuery('#copy_cat_breed').val();

    var category = jQuery('#copy_cat_category').val();
    var division = jQuery('#copy_cat_division').val();

    var exhibitor = jQuery('#exhibitor').val();

    var myElement = jQuery('#cat_division');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changedivisionlist&breed='+breed+'&category='+category+'&division='+division+'&exhibitor='+exhibitor+'&tmpl=component',
        type: 'get',
        async: false,
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
        changeColor();
    }).fail(function(){
    });
}

function changeColor()
{
    var breed = 0;
    if(!jQuery('#copy_cat_new_trait').is(':checked'))
        breed = jQuery('#copy_cat_breed').val();

    var category = jQuery('#copy_cat_category').val();
    var division = jQuery('#copy_cat_division').val();
    var color = jQuery('#copy_cat_color').val();

    var exhibitor = jQuery('#exhibitor').val();

    var myElement = jQuery('#cat_color');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changecolorlist&breed='+breed+'&category='+category+'&division='+division+'&color='+color+'&exhibitor='+exhibitor+'&tmpl=component',
        type: 'get',
        async: false,
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
    }).fail(function(){
    });
}


function changehairlength()
{
    var breed = 0;
    if(!jQuery('#copy_cat_new_trait').is(':checked'))
        breed = jQuery('#copy_cat_breed').val();
    var hairlength = jQuery('#copy_cat_hair_length').val();

    var myElement = jQuery('#cat_hairlength');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changehairlength&breed='+breed+'&hairlength='+hairlength+'&tmpl=component',
        type: 'get',
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
    }).fail(function(){
    });
}

function changeCatCategory()
{
    var breed = 0;
    if(!jQuery('#cat_new_trait').is(':checked'))
        breed = jQuery('#cat_breed').val();

    var category = jQuery('#cat_category').val();
    var exhibitor = jQuery('#exhibitor').val();

    var myElement = jQuery('#cat_category_div');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changecatlist&breed='+breed+'&category='+category+'&exhibitor='+exhibitor+'&tmpl=component',
        type: 'get',
        async: false,
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
        changeCatDivision();
    }).fail(function(){
    });
}

function changeCatDivision()
{
    var breed = 0;
    if(!jQuery('#cat_new_trait').is(':checked'))
        breed = jQuery('#cat_breed').val();

    var category = jQuery('#cat_category').val();
    var division = jQuery('#cat_division').val();

    var exhibitor = jQuery('#exhibitor').val();

    var myElement = jQuery('#cat_division_div');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changedivisionlist&breed='+breed+'&category='+category+'&division='+division+'&exhibitor='+exhibitor+'&tmpl=component',
        type: 'get',
        async: false,
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
        changeCatColor();
    }).fail(function(){
    });
}

function changeCatColor()
{
    var breed = 0;
    if(!jQuery('#cat_new_trait').is(':checked'))
        breed = jQuery('#cat_breed').val();

    var category = jQuery('#cat_category').val();
    var division = jQuery('#cat_division').val();
    var color = jQuery('#cat_color_div').val();

    var exhibitor = jQuery('#exhibitor').val();

    var myElement = jQuery('#cat_color');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changecolorlist&breed='+breed+'&category='+category+'&division='+division+'&color='+color+'&exhibitor='+exhibitor+'&tmpl=component',
        type: 'get',
        async: false,
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
    }).fail(function(){
    });
}


function changeCathairlength()
{
    var breed = 0;
    if(!jQuery('#cat_new_trait').is(':checked'))
        breed = jQuery('#cat_breed').val();
    var hairlength = jQuery('#cat_hair_length').val();

    var myElement = jQuery('#cat_hairlength');
    var myRequest = jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.changehairlength&breed='+breed+'&hairlength='+hairlength+'&tmpl=component',
        type: 'get',
        onRequest: function(){},
    }).done(function(responseText){
		responseText = responseText.trim();
        myElement.html(responseText);
    }).fail(function(){
    });
}

//Placeholders
function add_new_placeholder(show_id, parent_div)
{
    var user_id = jQuery('#add_user').val();

    jQuery('.add-placeholder-div').html('');
    
    jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');
    
    if(jQuery('.add-entry-div').length)
    jQuery('.add-entry-div').html('');
    
    if(jQuery('.cat-details').length)
        jQuery('.cat-details').html('');
    
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
        data: 'step=step1&clear_session=1&user_id='+user_id+'&show_id='+show_id+'&type=new'+'&parent_div='+parent_div,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#'+parent_div).html(responseText);
        jQuery('#'+parent_div).scrollIntoView();
    });
}

function edit_placeholder(id,user_id, parent_div)
{
    jQuery('.add-placeholder-div').html('');

    jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');
    
    if(jQuery('.add-entry-div').length)
    jQuery('.add-entry-div').html('');
    
    if(jQuery('.cat-details').length)
        jQuery('.cat-details').html('');

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
        data: 'step=step1&clear_session=1&edit=1&placeholder_id='+id+'&type=edit'+'&parent_div='+parent_div,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#'+parent_div).html(responseText);
        jQuery('#'+parent_div).scrollIntoView();
    });
}

function cancel_edit_placeholder(){
    
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=placeholder.cancel_edit_placeholder&tmpl=component',
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        location.reload();
    });

    /*var show_id = jQuery('#show_id').val();
    jQuery('.show-details').html('');
    jQuery('.show-details').hide();

    if(jQuery('#show-'+show_id).length)
    {
        jQuery('#show-'+show_id).show();
        jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

        jQuery.ajax({
            url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
            type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#show-'+show_id).html(responseText);
                jQuery('#show-'+show_id).scrollIntoView();
            });
    }
    else
    {
        location.reload();
    }*/
}

function placeholder_next_step(step)
{
    var parent_div = jQuery('#parent_div').val();
    var type = jQuery('input[name=type]').val();
    var user_id = jQuery('#user_id').val();
    var show_id = jQuery('#show_id').val();
    switch(step)
    {
        case 'step0':
            var selected_user_id = parseInt(jQuery('input[name=user_id]').val());
            if(selected_user_id)
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                    data: 'show_id='+show_id+'&user_id='+selected_user_id+'&step=step1'+'&parent_div='+parent_div+'&type='+type,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html(responseText);
                    jQuery('#'+parent_div).scrollIntoView();
                });
            }
            else
            {
                jbox_alert(PLEASE_SELECT_USER);
            }
            break;
        case 'step1':
            var showdays = Array();
            jQuery('input[name^=showday]:checked').each(function(ele){
                showdays.push(jQuery(this).val());
            });

            var placeholder_for_AM = Array();
            jQuery('input[name^=placeholder_participates_AM]:checked').each(function(){
                placeholder_for_AM.push(jQuery(this).val());
            });

            var placeholder_for_PM = Array();
            jQuery('input[name^=placeholder_participates_PM]:checked').each(function(){
                placeholder_for_PM.push(jQuery(this).val());
            });
            
            showdays = showdays.join(',');
			placeholder_for_AM = placeholder_for_AM.join(',');
			placeholder_for_PM = placeholder_for_PM.join(',');
            if(showdays)
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                    data: 'showdays='+showdays+'&placeholder_for_AM='+placeholder_for_AM+'&placeholder_for_PM='+placeholder_for_PM+'&user_id='+user_id+'&step=step2&parent_div='+parent_div+'&type='+type,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html(responseText);
                    jQuery('#'+parent_div).scrollIntoView();
                });
            }
            else
            {
                jbox_alert(PLEASE_SELECT_SHOWDAYS);
            }
            break;
        case 'step2':
			var accept_rule = parseInt(jQuery('input[name=accept_rule]:checked').val());
			
            if(isNaN(accept_rule))
			{
				jbox_alert(ACCEPT_TICA_SHOW_RULES_ERROR);
				break;
			}
			
			jQuery('#save_btn').prop('disabled',1);
			
            var single_cages = parseInt(jQuery('input[name=summary_single_cages]').val());
            var double_cages = parseInt(jQuery('input[name=summary_double_cages]').val());
            var personal_cage = jQuery('select[name=summary_personal_cages]').val();
            var grooming_space = jQuery('select[name=summary_grooming_space]').val();
            var benching_request = jQuery('textarea[name=summary_benching_request]').val();
            var remark = jQuery('textarea[name=summary_remarks]').val();
            var show_id = parseInt(jQuery('input[name=show_id]').val());
            var summary_user = parseInt(jQuery('input[name=summary_user]').val());
            var current_user = parseInt(jQuery('input[name=current_user]').val());
            edit = parseInt(jQuery('input[name=edit]').val());
            
			if(isNaN(single_cages))
			{
				jbox_alert(PLEASE_ENTER_VALID_SINGLE_CAGES);
				break;
			}
			
			if(isNaN(double_cages))
			{
				jbox_alert(PLEASE_ENTER_VALID_DOUBLE_CAGES);
				break;
			}
			
			if(single_cages <=0 && double_cages <= 0)
			{
				jbox_alert(NO_SPACES_SELECTED);
				break;
			}
			
			remark = Base64.encode(String(remark));
			benching_request = Base64.encode(String(benching_request));
			
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                data: 'single_cages='+single_cages+'&double_cages='+double_cages+'&personal_cage='+personal_cage+'&grooming_space='+grooming_space+'&benching_request='+benching_request+'&remark='+remark+'&step=final&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText !== '1')
                {
                    jbox_alert(responseText);
                }
                if(jQuery('#show-'+show_id).length)
                {
                    jQuery('.show-details').html('');
                    jQuery('.show-details').hide();

                    jQuery('#show-'+show_id).show();
                    jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                    jQuery.ajax({
                        url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
                        type: 'post',
                    }).done(function(responseText){
						responseText = responseText.trim();
                        jQuery('#show-'+show_id).html(responseText);
                        jQuery('#show-'+show_id).scrollIntoView();
                    });
                }
                else
                    location.reload();
            });
            break;
    }
}

function placeholder_previous_step(step)
{
    var show_id = jQuery('#show_id').val();
    var type = jQuery('#type').val();
    var user_id = jQuery('#user_id').val();
    var parent_div = jQuery('#parent_div').val();
    switch(step)
    {
        case 'step0':
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                data: 'step=init'+'&parent_div='+parent_div,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html('');
                if(jQuery('#show-'+show_id).length)
                {
                    jQuery('.show-details').html('');
                    jQuery('.show-details').hide();

                    jQuery('#show-'+show_id).show();
                    jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                    jQuery.ajax({
                        url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
                        type: 'post',
                    }).done(function(responseText){
						responseText = responseText.trim();
                        jQuery('#show-'+show_id).html(responseText);
                        jQuery('#show-'+show_id).scrollIntoView();
                    });
                    
                }
            });
            break;
        case 'step1':
            if(type == "third_party")
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                    data: 'step=step0&dir=prev&user_id='+user_id+'&parent_div='+parent_div+'&type='+type,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html(responseText);
                    jQuery('#'+parent_div).scrollIntoView();
                });
            }
            else
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                    data: 'step=init'+'&parent_div='+parent_div,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#'+parent_div).html('');
                    if(jQuery('#show-'+show_id).length)
                    {
                        jQuery('.show-details').html('');
                        jQuery('.show-details').hide();

                        jQuery('#show-'+show_id).show();
                        jQuery('#show-'+show_id).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                        jQuery.ajax({
                            url: 'index.php?option=com_toes&view=show&layout=short&id='+show_id+'&tmpl=component',
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            jQuery('#show-'+show_id).html(responseText);
                            jQuery('#show-'+show_id).scrollIntoView();
                        });
                    }
                });
            }
            break;
        case 'step2' :
            edit = parseInt(jQuery('input[name=edit]').val());
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                data: 'step=step1&dir=prev&user_id='+user_id+'&edit='+edit+'&parent_div='+parent_div+'&type='+type,
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                jQuery('#'+parent_div).html(responseText);
                jQuery('#'+parent_div).scrollIntoView();
            });
            break;
    }
}

function approve_placeholder(ele)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=placeholder.updateStatus&status=Accepted&day_id='+ids[0],
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        if(responseText === '1')
        {
            if(jQuery('#show-'+ids[1]).length)
            {
                jQuery('.show-details').html('');
                jQuery('.show-details').hide();

                jQuery('#show-'+ids[1]).show();
                jQuery('#show-'+ids[1]).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                jQuery.ajax({
                    url: 'index.php?option=com_toes&view=show&layout=short&id='+ids[1]+'&tmpl=component',
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#show-'+ids[1]).html(responseText);
                    jQuery('#show-'+ids[1]).scrollIntoView();
                });
            }
            else
                location.reload();
        }
        else
            jbox_alert(responseText);
    });
}

function reenter_placeholder(ele)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=placeholder.updateStatus&status=Accepted&day_id='+ids[0],
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        if(responseText === '1')
        {
            if(jQuery('#show-'+ids[1]).length)
            {
                jQuery('.show-details').html('');
                jQuery('.show-details').hide();

                jQuery('#show-'+ids[1]).show();
                jQuery('#show-'+ids[1]).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                jQuery.ajax({
                    url: 'index.php?option=com_toes&view=show&layout=short&id='+ids[1]+'&tmpl=component',
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    jQuery('#show-'+ids[1]).html(responseText);
                    jQuery('#show-'+ids[1]).scrollIntoView();
                });
            }
            else
                location.reload();
        }
        else
            jbox_alert(responseText);
    });
}

function cancel_placeholder(ele,confirm_question)
{
    var rel = jQuery(ele).attr('rel');
    var ids = rel.split(';');

	new jBox('Confirm',{
        content: confirm_question,
        width: '400px',
        cancelButton : NO_BUTTON,
        confirmButton: YES_BUTTON,
        confirm: function() {
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=placeholder.updateStatus&status=Cancelled&day_id='+ids[0],
                type: 'post',
            }).done(function(responseText){
				responseText = responseText.trim();
                if(responseText === '1')
                {
                    if(jQuery('#show-'+ids[1]).length)
                    {
                        jQuery('.show-details').html('');
                        jQuery('.show-details').hide();

                        jQuery('#show-'+ids[1]).show();
                        jQuery('#show-'+ids[1]).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

                        jQuery.ajax({
                            url: 'index.php?option=com_toes&view=show&layout=short&id='+ids[1]+'&tmpl=component',
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            jQuery('#show-'+ids[1]).html(responseText);
                            jQuery('#show-'+ids[1]).scrollIntoView();
                        });
                    }
                    else
                        location.reload();
                }
                else
                    jbox_alert(responseText);
            });
        }
    }).open();    
}

function convert_placeholder(id, show_id, user_id, parent_div)
{
    jQuery('.add-entry-div').html('');
    
    jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');

    if(jQuery('.add-placeholder-div').length)
        jQuery('.add-placeholder-div').html('');
    
    if(jQuery('.cat-details').length)
        jQuery('.cat-details').html('');
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
        data: 'step=step1&clear_session=1&user_id='+user_id+'&show_id='+show_id+'&placeholder_id='+id+'&parent_div='+parent_div,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#'+parent_div).html(responseText);
        jQuery('#'+parent_div).scrollIntoView();
    });
}

function define_filter_criteria(ring_index, ring_id)
{
	jQuery.ajax({
		url: site_root+'index.php?option=com_toes&view=entry&layout=congress_filters&tmpl=component&index='+ring_index+'&ring_id='+ring_id,
		type: 'post'
	}).done(function(responseText){
		responseText = responseText.trim();
		congress_filter_modal = new jBox('Modal',{
			ajax: {
				url: site_root+'index.php?option=com_toes&view=entry&layout=congress_filters&tmpl=component&index='+ring_index+'&ring_id='+ring_id,
				setContent: true, 
				reload: true,
				spinner: true
			},
			closeOnEsc: false,
			closeOnClick: false,
			closeButton: true,
            maxHeight: '400px',
			maxWidth: '600px',
			responsiveWidth: '600px',
            isolateScroll: true,
			onCloseComplete: function(){
				this.destroy();
			}
		}).open();
	});
}

function copy_field(source,target)
{
    if(jQuery('#'+target+'_value').val())
    {
        var selected_values = jQuery('#'+target+'_value').val().split(',');
        if(selected_values.indexOf(jQuery('#'+source).val()) != -1)
        {
            jbox_alert(ALREADY_SELECTED);
            return;
        }
    }
    if(jQuery('#'+source).val())
    {
        var new_text = jQuery('#'+source+' option:selected').text();

        var str = '<span class="data_lable" id="'+target+'_'+jQuery('#'+source).val()+'">'+new_text+'&nbsp;&nbsp;<span class="'+target+'_remove" onclick="removevalue(\''+target+'\','+jQuery('#'+source).val()+');"><i class="fa fa-remove"></i></span>'+'</span>';
        jQuery('#'+target+'_place').html(jQuery('#'+target+'_place').html() + str);

        if(jQuery('#'+target+'_value').val())
            jQuery('#'+target+'_value').val(jQuery('#'+target+'_value').val()+','+jQuery('#'+source).val());
        else
            jQuery('#'+target+'_value').val(jQuery('#'+source).val());
    }
}

function copy_color_wildcard(source,target,default_val)
{
    if(jQuery('#'+target+'_value').val())
    {
        var selected_values = jQuery('#'+target+'_value').val().split(',');
        if(selected_values.indexOf(jQuery('#'+source).val()) != -1)
        {
            jbox_alert(ALREADY_SELECTED);
            return;
        }
    }
    
    var cwd_text = jQuery('#'+source).val().trim();
    
    if(cwd_text && cwd_text != default_val)
    {
        var count = jQuery('#count_'+target).val();
        var index = count++;

        var new_text = jQuery('#'+source).val();

        var str = '<label class="data_lable" rel="'+new_text+'" id="'+target+'_'+index+'">%'+new_text+'%&nbsp;&nbsp;<span class="'+target+'_remove" onclick="removewildcard(\''+target+'\',\''+index+'\');"><i class="fa fa-remove"></i></span>'+'</label>';
        jQuery('#'+target+'_place').html(jQuery('#'+target+'_place').html() + str);

        if(jQuery('#'+target+'_value').val())
            jQuery('#'+target+'_value').val(jQuery('#'+target+'_value').val() + ',' + cwd_text);
        else
            jQuery('#'+target+'_value').val( cwd_text);

        jQuery('#count_'+target).val(count);
        jQuery('#'+source).val('');
    }
}

function removewildcard(field, id)
{
    var selected_values = jQuery('#'+field+'_value').val().split(',');
    var rem_values = new Array();
    for(var i=0;i<selected_values.length;i++)
    {
        if(jQuery('#'+field+'_'+id).attr('rel') != selected_values[i])
            rem_values.push(selected_values[i]);
    }

    jQuery('#'+field+'_value').val(rem_values.join(','));
    jQuery('#count_'+field).val(parseInt(jQuery('#count_'+field).val()) - 1);

    var parent = jQuery('#'+field+'_place');
    var child = jQuery('#'+field+'_'+id);
    child.remove();
}

function copy_multiple_color()
{
    jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
    jQuery('#loader').css('padding-top', (myHeight/2)+'px');
    jQuery('#loader').show();

    var select = jQuery('#colorsearchresults');
    if(select.options.length == 0)
        return;
    
    var selected_values = jQuery('#color_value').val().split(',');
    
    for (var i=0; i<select.options.length; i++){

        if(select.options[i].value == 0)
            continue;
        
        if(selected_values.indexOf(select.options[i].value) != -1)
            continue;
        
        selected_values.push(select.options[i].value);
        
        var str = '<label class="data_lable" id="color_'+select.options[i].value+'">'+select.options[i].text+'&nbsp;&nbsp;<span class="color_remove" onclick="removevalue(\'color\','+select.options[i].value+');"><i class="fa fa-remove"></i></span>'+'</label>';
        jQuery('#color_place').html(jQuery('#color_place').html() + str);
    }
    
    jQuery('#color_value').val(selected_values.join(','));

    jQuery('#loader').hide();
}

function remove_multiple_color()
{
    var select = jQuery("#colorsearchresults");
    if(select.options.length == 0)
        return;
    
    var ids = new Array();
    for (var i=0; i<select.options.length; i++){
        ids.push(select.options[i].value);
    }
    
    var parent = jQuery('#color_place');
    
    var selected_values = jQuery('#color_value').val().split(',');
    var rem_values = new Array();

    for(var j=0;j<selected_values.length;j++)
    {
        if(ids.indexOf(selected_values[j]) == -1)
        {
            rem_values.push(selected_values[j]);
        }
    }
    
    for(var k=0;k<ids.length;k++)
    {
        if(jQuery('#color_'+ids[k]).length)
        {
            var child = jQuery('#color_'+ids[k]);
            child.remove();
        }
    }

    jQuery('#color_value').val(rem_values.join(','));
}


function clear_all_color()
{
    var parent = jQuery('#color_place');
    var selected_values = jQuery('#color_value').val().split(',');
    var child;
    var j;

    for(j=0;j<selected_values.length;j++)
    {
        if(jQuery('#color_'+selected_values[j]).length)
        {
            child = jQuery('#color_'+selected_values[j]);
            child.remove();
        }
    }
    jQuery('#color_value').val('');

    parent = jQuery('#cwd_place');
    selected_values = jQuery('#cwd_value').val().split(',');
    var count = parseInt(jQuery('#count_cwd').val());

    for(j=0;j<count;j++)
    {
        if(jQuery('#cwd_'+j).length)
        {
            child = jQuery('#cwd_'+j);
            child.remove();
        }
    }
    jQuery('#cwd_value').val('');
    jQuery('#count_cwd').val(0);
}

function removevalue(field, id)
{
    var selected_values = jQuery('#'+field+'_value').val().split(',');
    var rem_values = new Array();
    for(var i=0;i<selected_values.length;i++)
    {
        if(id != selected_values[i])
            rem_values.push(selected_values[i]);
    }

    jQuery('#'+field+'_value').val(rem_values.join(','));

    var parent = jQuery('#'+field+'_place');
    var child = jQuery('#'+field+'_'+id);
    child.remove();
}

function select_all_filter(ele)
{
    if(jQuery(ele).is(':checked'))
    {
        jQuery('#breed_filter').prop('checked',1);
        jQuery('#gender_filter').prop('checked',1);
        jQuery('#newtrait_filter').prop('checked',1);
        jQuery('#hairlength_filter').prop('checked',1);
        jQuery('#category_filter').prop('checked',1);
        jQuery('#division_filter').prop('checked',1);
        jQuery('#color_filter').prop('checked',1);
        jQuery('#title_filter').prop('checked',1);
        jQuery('#manual_filter').prop('checked',1);
    }
    else
    {
        jQuery('#breed_filter').prop('checked',0);
        jQuery('#gender_filter').prop('checked',0);
        jQuery('#newtrait_filter').prop('checked',0);
        jQuery('#hairlength_filter').prop('checked',0);
        jQuery('#category_filter').prop('checked',0);
        jQuery('#division_filter').prop('checked',0);
        jQuery('#color_filter').prop('checked',0);
        jQuery('#title_filter').prop('checked',0);
        jQuery('#manual_filter').prop('checked',0);
    }
    
    toggle_field(jQuery('#breed_filter'),'breedname');
    toggle_field(jQuery('#gender_filter'),'gender');
    toggle_field(jQuery('#hairlength_filter'),'copy_cat_hairlength');
    toggle_field(jQuery('#category_filter'),'copy_cat_category');
    toggle_field(jQuery('#division_filter'),'copy_cat_division');
    toggle_field(jQuery('#color_filter'),'colorname');
    toggle_field(jQuery('#title_filter'),'title');
}

function save_congress_criteria()
{
    var ring_index = parseInt(jQuery('input[name=ring_index]').val());
    var ring_id = parseInt(jQuery('input[name=ring_id]').val());

    var breed_filter = parseInt(jQuery('input[name=breed_filter]:checked').val());
    var gender_filter = parseInt(jQuery('input[name=gender_filter]:checked').val());
    var newtrait_filter = parseInt(jQuery('input[name=newtrait_filter]:checked').val());
    var hairlength_filter = parseInt(jQuery('input[name=hairlength_filter]:checked').val());
    var category_filter = parseInt(jQuery('input[name=category_filter]:checked').val());
    var division_filter = parseInt(jQuery('input[name=division_filter]:checked').val());
    var color_filter = parseInt(jQuery('input[name=color_filter]:checked').val());
    var title_filter = parseInt(jQuery('input[name=title_filter]:checked').val());
    var manual_filter = parseInt(jQuery('input[name=manual_filter]:checked').val());

    var class_value = jQuery('input[name=class_value]').val();
    var breed_value = jQuery('input[name=breed_value]').val();
    var gender_value = jQuery('input[name=gender_value]').val();
    var hairlength_value = jQuery('input[name=hairlength_value]').val();
    var category_value = jQuery('input[name=category_value]').val();
    var division_value = jQuery('input[name=division_value]').val();
    var color_value = jQuery('input[name=color_value]').val();
    var title_value = jQuery('input[name=title_value]').val();
    
    var cwd_value = jQuery('input[name=cwd_value]').val();

    if(class_value == '')
    {
        jbox_alert(PLEASE_SELECT_CONGRESS_CRITERIA);
        return false;
    }

    var data = '';
    data += 'ring_index='+(isNaN(ring_index)?0:ring_index);
    data += '&ring_id='+(isNaN(ring_id)?0:ring_id);
    
    data += '&breed_filter='+(isNaN(breed_filter)?0:breed_filter);
    data += '&gender_filter='+(isNaN(gender_filter)?0:gender_filter);
    data += '&newtrait_filter='+(isNaN(newtrait_filter)?0:newtrait_filter);
    data += '&hairlength_filter='+(isNaN(hairlength_filter)?0:hairlength_filter);
    data += '&category_filter='+(isNaN(category_filter)?0:category_filter);
    data += '&division_filter='+(isNaN(division_filter)?0:division_filter);
    data += '&color_filter='+(isNaN(color_filter)?0:color_filter);
    data += '&title_filter='+(isNaN(title_filter)?0:title_filter);
    data += '&manual_filter='+(isNaN(manual_filter)?0:manual_filter);
    
    data += '&class_value='+class_value;
    data += '&breed_value='+breed_value;
    data += '&gender_value='+gender_value;
    data += '&hairlength_value='+hairlength_value;
    data += '&category_value='+category_value;
    data += '&division_value='+division_value;
    data += '&color_value='+color_value;
    data += '&title_value='+title_value;

    data += '&cwd_value='+cwd_value;

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.saveFilterCriteria',
        data: data,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        if(responseText === '1')
        {
            congress_filter_modal.close();
        }
        else
            jbox_alert(responseText);
            
    });
    return true;
}

function getThirdPartyUsers() {
	jQuery( "#user_name" ).autocomplete({
	  source: 'index.php?option=com_toes&task=entry.getUsers&tmpl=component',
	  select: function( event, ui ) {
	  	jQuery( "#user_id" ).val(ui.item.key);
	  	jQuery( "#user_name" ).val(ui.item.value);
	  }
	}); 
	
	jQuery( "#country_name" ).autocomplete({
	  source: 'index.php?option=com_toes&task=shows.getCountries&tmpl=component',
	  select: function( event, ui ) {
		jQuery( "#country" ).val(ui.item.key);
		jQuery( "#country_name" ).val(ui.item.value);

		jQuery('#state').val(0);
		jQuery('#state_name').val('');
		jQuery('#city').val(0);
		jQuery('#city_name').val('');

		if(ui.item.country_uses_states == 0) {
			 jQuery('.state_div').hide();
		}
	  }
	});    

	jQuery( "#state_name" ).autocomplete({
	  source: function( request, response ) {
		jQuery.ajax({
		  url: 'index.php?option=com_toes&task=shows.getStates&tmpl=component',
		  dataType: "json",
		  data: {
			term: request.term, 
			country_id: jQuery( "#country" ).val()
		  }
		}).done(function( data ) {
			response( data );
		});
	  },
	  select: function( event, ui ) {
		jQuery( "#state" ).val(ui.item.key);
		jQuery( "#state_name" ).val(ui.item.value);

		jQuery('#city').val(0);
		jQuery('#city_name').val('');
	  }
	}); 

	jQuery( "#city_name" ).autocomplete({
	  source: function( request, response ) {
		jQuery.ajax({
		  url: 'index.php?option=com_toes&task=shows.getCities&tmpl=component',
		  dataType: "json",
		  data: {
			term: request.term, 
			state_id: jQuery( "#state" ).val(),
			country_id: ((jQuery( "#state" ).val()>0)?0:jQuery( "#country" ).val())
		  }
		}).done(function( data ) {
			response( data );
		});
	  },
	  select: function( event, ui ) {
		jQuery( "#city" ).val(ui.item.key);
		jQuery( "#city_name" ).val(ui.item.value);
	  }
	}); 
}

function add_third_party_entry(id, parent_div)
{
	if(jQuery('.add-entry-div').length)
		jQuery('.add-entry-div').html('');
    
    if(jQuery('.add-placeholder-div').length)
        jQuery('.add-placeholder-div').html('');
    
    if(jQuery('.cat-details').length)
        jQuery('.cat-details').html('');

	jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');
    
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
        data: 'step=step0&clear_session=1&type=third_party&show_id='+id+'&parent_div='+parent_div,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#'+parent_div).html(responseText);
        jQuery('#'+parent_div).scrollIntoView();
		getThirdPartyUsers();
    });
}

function add_third_party_placeholder(id, parent_div)
{
	if(jQuery('.add-placeholder-div').length)
		jQuery('.add-placeholder-div').html('');
    
    if(jQuery('.add-entry-div').length)
        jQuery('.add-entry-div').html('');
    
    if(jQuery('.cat-details').length)
        jQuery('.cat-details').html('');

	jQuery('#'+parent_div).html('<img alt="loading..." src="'+site_root+'media/com_toes/images/loading.gif" />');
    
    jQuery.ajax({
        url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
        data: 'step=step0&clear_session=1&type=third_party&show_id='+id+'&parent_div='+parent_div,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        jQuery('#'+parent_div).html(responseText);
        jQuery('#'+parent_div).scrollIntoView();
		getThirdPartyUsers();
    });
}

function add_new_user()
{
    jQuery('#new-user').show();
    jQuery('#select-user').hide();
}

function cancel_new_user()
{
    jQuery('#new-user').hide();
    jQuery('#select-user').show();
}

if (!String.prototype.trim) {
    String.prototype.trim = function(){
        return this.replace(/^\s+|\s+$/g, '');
    };
}

function validEmail(e) {
    var filter = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
    return String(e).search (filter) != -1;
}

function validPhonenumber(e) {
    var filter = /^(?!.*-.*-.*-)(?=(?:\d{8,10}$)|(?:(?=.{9,11}$)[^-]*-[^-]*$)|(?:(?=.{10,12}$)[^-]*-[^-]*-[^-]*$)  )[\d-]+$/;
    return String(e).search (filter) != -1;
}

function save_new_user()
{
    var show_id = jQuery('#show_id').val();
    var parent_div = jQuery('#parent_div').val();
    var type = jQuery('#type').val();
    var source = jQuery('#source').val();
   
    var firstname = jQuery('input[name=firstname]').val().toString();
    var lastname = jQuery('input[name=lastname]').val().toString();
    var username = jQuery('input[name=username]').val().toString();
    var email = jQuery('input[name=email]').val().toString();
    var address1 = jQuery('input[name=address1]').val().toString();
    var address2 = jQuery('input[name=address2]').val().toString();
    var address3 = jQuery('input[name=address3]').val().toString();
    var city = jQuery('input[name=city]').val().toString();
    var zip = jQuery('input[name=zip]').val().toString();
    var state = jQuery('input[name=state]').val().toString();
    var country = jQuery('input[name=country]').val().toString();
    var phonenumber = jQuery('input[name=phonenumber]').val().toString();
    
    var still_required = '';
    if(firstname.trim() == "")
        still_required += '<br/>* '+FIRST_NAME;
    if(lastname.trim() == "")
        still_required += '<br/>* '+LAST_NAME;
    if(username.trim() == "")
        still_required += '<br/>* '+USERNAME;
    /*if(email.trim() == "")
        still_required += '<br/>* '+EMAIL;*/
    if(address1.trim() == "")
        still_required += '<br/>* '+ADDRESS1;
    if(city.trim() == "")
        still_required += '<br/>* '+CITY;
    if(zip.trim() == "")
        still_required += '<br/>* '+ZIP;
    /*if(state.trim() == "")
        still_required += '<br/>* '+STATE;*/
    if(country.trim() == "")
        still_required += '<br/>* '+COUNTRY;
    /*if(phonenumber.trim() == "")
        still_required += '<br/>* '+PHONENUMBER;*/

    if(still_required)
    {
        jbox_alert(STILL_REQUIRED+" <br/>"+still_required);
        return false;
    }
    
    if(email.trim() == "")
	{
	 	new jBox('Confirm',{
	        content: NO_EMAIL_ADDRESS_WARNING,
	        width: '400px',
	        cancelButton : NO_BUTTON,
	        confirmButton: YES_BUTTON,
	        confirm: function() {
	    	    var data = '';
	    	    data += 'firstname='+firstname;
	    	    data += '&lastname='+lastname;
	    	    data += '&username='+username;
	    	    data += '&email='+email;
	    	    data += '&address1='+address1;
	    	    data += '&address2='+address2;
	    	    data += '&address3='+address3;
	    	    data += '&city='+city;
	    	    data += '&zip='+zip;
	    	    data += '&state='+state;
	    	    data += '&country='+country;
	    	    data += '&phonenumber='+phonenumber;
	    	
	    	    jQuery.ajax({
	    	        url: 'index.php?option=com_toes&task=entry.saveUser',
	    	        data: data,
	    	        type: 'post',
    	        }).done(function(responseText){
					responseText = responseText.trim();
    	            if(isNaN(responseText))
    	            {
    	                jbox_alert(responseText);
    	            }
    	            else
    	            {
    	                if(source == 'entry')
    	                {
    	                    jQuery.ajax({
    	                        url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
    	                        data: 'show_id='+show_id+'&user_id='+responseText+'&step=step1'+'&parent_div='+parent_div+'&type='+type,
    	                        type: 'post',
	                        }).done(function(responseText){
								responseText = responseText.trim();
	                            jQuery('#'+parent_div).html(responseText);
	                            jQuery('#'+parent_div).scrollIntoView();
	                        });
    	                }
    	                else
    	                {
    	                    jQuery.ajax({
    	                        url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
    	                        data: 'show_id='+show_id+'&user_id='+responseText+'&step=step1'+'&parent_div='+parent_div+'&type='+type,
    	                        type: 'post',
	                        }).done(function(responseText){
								responseText = responseText.trim();
	                            jQuery('#'+parent_div).html(responseText);
	                            jQuery('#'+parent_div).scrollIntoView();
	                        });
    	                }
    	            }
    	        });
	        }
        }).open();    
	}
    else
    {
	    if(!validEmail(email))
	    {
	        jbox_alert(PLEASE_ENTER_VALID_EMAIL);
	        return false;
	    }
	    
	    /*if(!validPhonenumber(phonenumber))
	    {
	        jbox_alert(PLEASE_ENTER_VALID_PHONENUMBER)
	        return false;
	    }*/
	    
	    var data = '';
	    data += 'firstname='+firstname;
	    data += '&lastname='+lastname;
	    data += '&username='+username;
	    data += '&email='+email;
	    data += '&address1='+address1;
	    data += '&address2='+address2;
	    data += '&address3='+address3;
	    data += '&city='+city;
	    data += '&zip='+zip;
	    data += '&state='+state;
	    data += '&country='+country;
	    data += '&phonenumber='+phonenumber;
	
	    jQuery.ajax({
	        url: 'index.php?option=com_toes&task=entry.saveUser',
	        data: data,
	        type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            if(isNaN(responseText))
            {
                jbox_alert(responseText);
            }
            else
            {
                if(source == 'entry')
                {
                    jQuery.ajax({
                        url: 'index.php?option=com_toes&task=entry.step&tmpl=component',
                        data: 'show_id='+show_id+'&user_id='+responseText+'&step=step1'+'&parent_div='+parent_div+'&type='+type,
                        type: 'post',
                    }).done(function(responseText){
						responseText = responseText.trim();
                        jQuery('#'+parent_div).html(responseText);
                        jQuery('#'+parent_div).scrollIntoView();
                    });
                }
                else
                {
                    jQuery.ajax({
                        url: 'index.php?option=com_toes&task=placeholder.step&tmpl=component',
                        data: 'show_id='+show_id+'&user_id='+responseText+'&step=step1'+'&parent_div='+parent_div+'&type='+type,
                        type: 'post',
                    }).done(function(responseText){
						responseText = responseText.trim();
                        jQuery('#'+parent_div).html(responseText);
                        jQuery('#'+parent_div).scrollIntoView();
                    });
                }
            }
        });
    }
    return true;
}

function displayLink(ele)
{
    var rel = jQuery(ele).attr('rel');
	var link_content = '<div style="text-align:center;font-size: 18px;padding: 5px 0;">'+rel+'</div';
	new jBox('Modal',{
		content: link_content,
		width: '500px'
	}).open();
}

function check_AM_PM_for_entry(ele,show_day_id)
{
	if(jQuery(ele).is(':checked'))
	{
		if(!jQuery('#entry_participates_AM_'+show_day_id).is(':disabled'))
		jQuery('#entry_participates_AM_'+show_day_id).prop('checked',1);
		if(!jQuery('#entry_participates_PM_'+show_day_id).is(':disabled'))
		jQuery('#entry_participates_PM_'+show_day_id).prop('checked',1);
	}
	else
	{
		if(!jQuery('#entry_participates_AM_'+show_day_id).is(':disabled'))
		jQuery('#entry_participates_AM_'+show_day_id).prop('checked',0);
		if(!jQuery('#entry_participates_PM_'+show_day_id).is(':disabled'))
		jQuery('#entry_participates_PM_'+show_day_id).prop('checked',0);
	}
}

function check_show_day_for_entry(ele,timing,show_day_id)
{
	if(jQuery(ele).is(':checked'))
	{
		jQuery('#show_day_'+show_day_id).prop('checked',1);
	}
	else
	{
		if(!jQuery('#entry_participates_'+timing+'_'+show_day_id).is(':checked'))
		{
			jQuery('#show_day_'+show_day_id).prop('checked',0);
		}
	}
}


function check_AM_PM_for_placeholder(ele,show_day_id)
{
	if(jQuery(ele).is(':checked'))
	{
		if(!jQuery('#placeholder_participates_AM_'+show_day_id).is(':disabled'))
		jQuery('#placeholder_participates_AM_'+show_day_id).prop('checked',1);
		if(!jQuery('#placeholder_participates_PM_'+show_day_id).is(':disabled'))
		jQuery('#placeholder_participates_PM_'+show_day_id).prop('checked',1);
	}
	else
	{
		if(!jQuery('#placeholder_participates_AM_'+show_day_id).is(':disabled'))
		jQuery('#placeholder_participates_AM_'+show_day_id).prop('checked',0);
		if(!jQuery('#placeholder_participates_PM_'+show_day_id).is(':disabled'))
		jQuery('#placeholder_participates_PM_'+show_day_id).prop('checked',0);
	}
}

function check_show_day_for_placeholder(ele,timing,show_day_id)
{
	if(jQuery(ele).is(':checked'))
	{
		jQuery('#show_day_'+show_day_id).prop('checked',1);
	}
	else
	{
		if(!jQuery('#placeholder_participates_'+timing+'_'+show_day_id).is(':checked'))
		{
			jQuery('#show_day_'+show_day_id).prop('checked',0);
		}
	}
}

function save_reject_reason()
{
    var entry_id = jQuery('#reject_entry_id').val();
    var reason = jQuery('textarea[name=entry_refusal_reason_reason]').val().toString();

    if(reason.trim() == "")
    {
        jbox_alert(REFUSAL_REASON_REQUIRED);
        return false;
    }
    
    var data = '';
    data += 'entry_id='+entry_id;
	
	reason = Base64.encode(reason);
    data += "&reason="+reason;

    jQuery.ajax({
        url: 'index.php?option=com_toes&task=entry.reject_entry',
        data: data,
        type: 'post',
    }).done(function(responseText){
		responseText = responseText.trim();
        if(responseText !== '1')
        {
            jbox_alert(responseText);
        }
        else
        {
			jbox_notice(NOTIFICATION_SENT_TO_EXHIBITOR, 'green');
			setInterval(window.parent.location.reload(), 2000);
        }
    });
    return true;
}