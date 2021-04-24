<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

use PayPal\Api\Address;
use PayPal\Api\BillingInfo;
use PayPal\Api\Cost;
use PayPal\Api\Currency;
use PayPal\Api\Invoice;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\PaymentTerm;

/**
 * Template styles list controller class.
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESControllerShow extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * 
     */
    public function getModel($name = 'show', $prefix = 'ToesModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }
    
    public function displayLink(){
        $app = JFactory::getApplication();
        $show_id = $app->input->getInt('show_id');
        $url = JURI::getInstance();
        
        echo '<div style="text-align:center;font-size: 18px;padding: 5px 0;">';
        echo $url->getScheme().'://'.$url->getHost().JRoute::_('index.php?option=com_toes&view=shows',false).'#show'.$show_id;
        echo '</div>';
        
        $app->close();
    }

    public function copy() {
        $model = $this->getModel();
        $id = $model->copy();
        if ($id)
            $this->setRedirect(JRoute::_('index.php?option=com_toes&view=show&layout=edit&id='.$id), JText::_('COM_TOES_SHOW_ADDED_SUCCESS'));
        else
            $this->setRedirect(JRoute::_('index.php?option=com_toes&view=shows'), $model->getError());
    }
	//conflict show
	public function checkradiusresultshow()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$post = $app->input->post->getArray();
		
		
		$db = JFactory::getDBO();
		$p = $app->input->getString('data');
		parse_str(urldecode($p),$value);
		
		//$radius = 7000;
		$address = $value['venue_name'];
		
		$params = JComponentHelper::getParams('com_toes');
		$radius = $params->get('show_miles');
		
	
		if($radius > 0 ){
			if($radius && $address && $value['lat']!== '0.00000000' && $value['lng']!== '0.00000000' && $data['lat']!== '' && $data['lng']!== ''){
				$query = $db->getQuery(true);
				$query .='select s.show_id,v.venue_id,a.address_latitude,a.address_longitude';
				$query .=' ,(ACOS( SIN(RADIANS('.$value['lat'].')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$value['lat'].')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$value['lng'].')) ) * 3963.1676) AS distance'; 
				$query .=' FROM `#__toes_show` as s ';
				$query .=' LEFT join `#__toes_venue` as v ON s.`show_venue` = v.`venue_id`';
				$query .=' LEFT join `#__toes_address` as a ON v.`venue_address` = a.`address_id`';
				$query .=' WHERE ((( s.`show_end_date` BETWEEN '.$db->quote($value['show_start_date']).' AND ' .$db->quote($value['show_end_date']). ' ) OR
							( s.`show_start_date` BETWEEN '.$db->quote($value['show_start_date']).' AND ' .$db->quote($value['show_end_date']). ' )) AND 
							( s.show_status = 2  OR s.show_status = 1 OR s.show_status = 4 OR s.show_status = 7) AND
							(((ACOS( SIN(RADIANS('.$value['lat'].')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$value['lat'].')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$value['lng'].')) ) * 3963.1676) <= '.$radius.') OR (a.address_latitude = '.$value['lat'].' AND a.address_longitude = '.$value['lng'].')) AND s.show_id !='.$db->quote($value['id']).')';
				/*
				$query .='select s.show_id,v.venue_id';
				
				$query .= ' ,(3959 * acos( cos( radians('.$value['lat'].'))* cos(radians(address_latitude))*cos(radians(address_longitude)-radians('.$value['lng'].'))+sin(radians('.$value['lng'].') )*sin(radians(address_latitude))))
							AS distance ' ;
				$query	.=' FROM `#__toes_show` as s ';
				$query .='LEFT join `#__toes_venue` as v ON s.`show_venue` = v.`venue_id`';
				$query .='LEFT join `#__toes_address` as a ON v.`venue_address` = a.`address_id`';
				$query .=' WHERE  ( ( s.`show_end_date` BETWEEN '.$db->quote($value['show_start_date']).' AND ' .$db->quote($value['show_end_date']). ' )  
							AND ( s.`show_start_date` BETWEEN '.$db->quote($value['show_start_date']).' AND ' .$db->quote($value['show_end_date']). ' )
							AND ( s.show_status = 2  OR s.show_status = 1 OR s.show_status = 4 OR s.show_status = 7) AND s.show_id !='.$db->quote($value['id']).' AND (a.address_latitude <> \'0.00000000\' AND a.address_longitude <> \'0.00000000\'))';
				$query .= ' HAVING distance <= '.$radius;//.' OR distance IS NULL';
				*/ 
				//echo $query;
				$db->setQuery($query);
				$radiusresult = $db->loadObjectList();
				
			}	
		}
		
		if($radiusresult)
		{
			echo '2';
			exit();
		}	
		else
		{
			echo '1';
			exit();
		}
		
	}
	//conflict show  -copy
	public function checkradiusresultshowcopy()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$copy_show_id = $app->input->getInt('copy_show_id');
		$start_date = $app->input->getString('start_date');
		$query = "SELECT s.*,v.venue_name,a.address_latitude,a.address_longitude 
		FROM `#__toes_show` as s JOIN `#__toes_venue` as v ON s.show_venue = v.venue_id
		JOIN `#__toes_address` as a ON a.address_id = v.venue_address
		WHERE  s.`show_id` = " . $copy_show_id;
		 
		$db->setQuery($query);
		$copy_show = $db->loadObject();
		$club = TOESHelper::getClub($copy_show_id);
		if (!TOESHelper::isAdmin() && !TOESHelper::is_clubowner($user->id, $club->club_id)) {
			$this->setError(JText::_('COM_TOES_NOAUTH'));
			return false;
			echo -1;
			exit();
		}
		
		// calculate end date
		$db->setQuery("select count(*) from `#__toes_show_day` where `show_day_show` =".$copy_show_id);
		$show_days = $db->loadResult();		 	
		$end_date = date('Y-m-d',strtotime(date('Y-m-d',strtotime($start_date." +".($show_days -1)." days"))));	  
		$params = JComponentHelper::getParams('com_toes');
		$radius = $params->get('show_miles');
		
	
		if($radius > 0 ){
			if($radius && $copy_show->venue_name && $copy_show->address_latitude!== '0.00000000' && $copy_show->address_longitude!== '0.00000000' && $copy_show->address_latitude!== '' && $copy_show->address_longitude!== ''){
				$query = $db->getQuery(true);
				$query .='select s.show_id,v.venue_id,a.address_latitude,a.address_longitude';
				$query .=' ,(ACOS( SIN(RADIANS('.$copy_show->address_latitude.')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$copy_show->address_latitude.')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$copy_show->address_longitude.')) ) * 3963.1676) AS distance'; 
				$query .=' FROM `#__toes_show` as s ';
				$query .=' LEFT join `#__toes_venue` as v ON s.`show_venue` = v.`venue_id`';
				$query .=' LEFT join `#__toes_address` as a ON v.`venue_address` = a.`address_id`';
				$query .=' WHERE ((( s.`show_end_date` BETWEEN '.$db->quote($start_date).' AND ' .$db->quote($end_date). ' ) OR
							( s.`show_start_date` BETWEEN '.$db->quote($start_date).' AND ' .$db->quote($end_date). ' )) AND 
							( s.show_status = 2  OR s.show_status = 1 OR s.show_status = 4 OR s.show_status = 7) AND
							(((ACOS( SIN(RADIANS('.$copy_show->address_latitude.')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$copy_show->address_latitude.')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$copy_show->address_longitude.')) ) * 3963.1676) <= '.$radius.') OR (a.address_latitude = '.$copy_show->address_latitude.' AND a.address_longitude = '.$copy_show->address_longitude.')))';
				 
				//echo $query;
				$db->setQuery($query);
				$radiusresult = $db->loadObjectList();
				
			}	
		}
		
		if($radiusresult)
		{
			echo '2';
			exit();
		}	
		else
		{
			echo '1';
			exit();
		}
		
	}
	
    public function save() {
		$app = JFactory::getApplication();
        $model = $this->getModel();
        $post = $app->input->post->getArray();
		
		$request_show_license = $app->input->get('request_show_license');
		
		$return_on_page = $post['return_on_page'];

        $return = $model->save($post);
		
        if ($return)
		{
			if($request_show_license) {
				if($this->sendShowLicenseApplication()) {
					$app->enqueueMessage(JText::_('COM_TOES_SHOW_LICENSE_APPLICATION_SENT'));
				} else {
					$app->enqueueMessage(JText::_('COM_TOES_SHOW_LICENSE_APPLICATION_SENDING_ERROR'),'warning');
				}
			}
			
			$app->enqueueMessage(JText::_('COM_TOES_SHOW_ADDED_SUCCESS'));
			
			if($return_on_page) {
				$this->setRedirect(JRoute::_('index.php?option=com_toes&view=show&layout=edit&id='.$return));
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_toes&view=shows'));
			}
		} else {
			$app->enqueueMessage(JText::_('COM_TOES_SHOW_ADDED_UNSUCCESS'),'error');
            $this->setRedirect(JRoute::_('index.php?option=com_toes&view=shows'));
		}
    }
    
    public function getRingDayDropdown(){
        $app = JFactory::getApplication();
        $org_start_date = $app->input->getVar('org_start_date');
        $show_day_date = $app->input->getVar('show_day_date');
        $start_date = $app->input->getVar('start_date');
        $end_date = $app->input->getVar('end_date');

		$orgDiff= new stdClass();
        //$orgStart = new DateTime($org_start_date);
        //$showDayDate = new DateTime($show_day_date);
        //$orgDiff = $showDayDate->diff($orgStart);
        $diff = abs(strtotime($show_day_date) - strtotime($org_start_date));
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $orgDiff->days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		
        if($orgDiff->days < 0 || $orgDiff->days > 3)
        {
            //$orgDiff = $showDayDate->diff(new DateTime($start_date));
            $diff = abs(strtotime($show_day_date) - strtotime($start_date));
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $orgDiff->days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        }

        $cnt = $app->input->getInt('cnt');
        
        $show_format = $app->input->getVar('show_format');
        $hidden = '';
        if($show_format == 'Continuous')
            $hidden = 'style="display:none;"';
        
        echo '<select name="ring_show_day[]" id="ring_show_day_'.$cnt.'" '.$hidden.'>';
        echo '<option value="">'.JText::_('COM_TOES_SELECT').'</option>';
        if(strtotime($start_date) && strtotime($end_date))
        {
            for($i= strtotime($start_date); $i <= strtotime($end_date); $i+=86400)
            {
                $selected = ($org_start_date != '0000-00-00' && $i == strtotime("+".$orgDiff->days." days",  strtotime($start_date))) ? 'selected="selected"' : '' ;
                echo '<option '.$selected.' value="'.date('Y-m-d',$i).'">'.date('l',$i).'</option>';
            }
        }
        echo '</select>';
        $app->close();
    }

    public function getUsers() {
        $app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);
			
              $query = "SELECT CONCAT(b.firstname,' ',b.lastname,' (',a.username,')') AS `key`, CONCAT(b.firstname,' ',b.lastname,' - ',a.username) AS value
                    FROM #__users as a 
                    LEFT JOIN #__comprofiler as b ON a.id = b.user_id
                    WHERE (concat(LOWER(b.firstname),' ',LOWER(b.lastname),' ',LOWER(a.username)) LIKE ".$like." ) 
                    ORDER BY b.lastname";       
                    
            $db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
                echo json_encode($users);
            }
        }
        $app->close();
    }

    public function getRingClerks() {
        $app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);
			
              $query = "SELECT a.id AS `key`, CONCAT(b.firstname,' ',b.lastname,' - ',a.username) AS value
                    FROM #__users as a 
                    LEFT JOIN #__comprofiler as b ON a.id = b.user_id
                    WHERE (concat(LOWER(b.firstname),' ',LOWER(b.lastname),' ',LOWER(a.username)) LIKE ".$like." ) 
                    ORDER BY b.lastname";       
                    
            $db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
                echo json_encode($users);
            }
        }
        $app->close();
    }
    
    public function getJudges() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT j.judge_id AS `key`, CONCAT(cb.firstname,' ',cb.lastname,' - ',jl.judge_level) AS value
                    FROM #__toes_judge AS j
                    LEFT JOIN #__toes_judge_level AS jl ON jl.judge_level_id = j.judge_level
                    LEFT JOIN #__toes_judge_status AS js ON js.judge_status_id = j.judge_status
                    LEFT JOIN #__users as u ON u.id = j.user
                    LEFT JOIN #__comprofiler as cb ON u.id = cb.user_id
                    WHERE js.judge_status = 'Active'
                    AND (LOWER(cb.firstname) LIKE  " . $like." OR LOWER(cb.lastname) LIKE  " . $like." )
                    ORDER BY cb.lastname ASC 
                    ";
            //echo $query;
            $db->setQuery($query);
            $judges = $db->loadObjectList();
            if (count($judges)) {
                echo json_encode($judges);
            }
        }
        
        $app->close();
    }
    public function getJudges_new() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT j.id AS `key`, CONCAT(cb.firstname,' ',cb.lastname,' - ',jl.judge_level) AS value
                    FROM #__jdg_judges AS j
                    LEFT JOIN #__jdg_judge_level AS jl ON jl.judge_level_id = j.judgelevel
                    LEFT JOIN #__jdg_judge_status AS js ON js.judge_status_id = j.judgestatus
                    LEFT JOIN #__users as u ON u.id = j.user_id
                    LEFT JOIN #__comprofiler as cb ON u.id = cb.user_id
                    WHERE js.judge_status = 'Active'
                    AND (LOWER(cb.firstname) LIKE  " . $like." OR LOWER(cb.lastname) LIKE  " . $like." )
                    ORDER BY cb.lastname ASC 
                    ";
            //echo $query;
            $db->setQuery($query);
            $judges = $db->loadObjectList();
            if (count($judges)) {
                echo json_encode($judges);
            }
        }
        
        $app->close();
    }

    public function getvenues() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT venue_id AS `key`, venue_name AS value FROM #__toes_venue WHERE LOWER(venue_name) LIKE  " . $like;
            $db->setQuery($query);
            $venues = $db->loadObjectList();
            if (count($venues)) {
                echo json_encode($venues);
            }
        }
        $app->close();
    }

    public function set_venuedata() {
    	$app = JFactory::getApplication();
        $venue = $app->input->getVar('venue');

        if ($venue) {
            $db = JFactory::getDBO();
/*  				LEFT JOIN #__toes_country as cntry ON cntry.name = b.address_country 
                    LEFT JOIN #__toes_states_per_country as state ON state.name = b.address_state 
                    LEFT JOIN #__toes_cities_per_state as city ON city.name = b.address_city 
                    cntry.name AS address_country_name, state.name AS address_state_name, city.name as address_city_name */
            $query = "SELECT a.*, b.*,b.address_country as address_country_name, b.address_state as address_state_name,
					b.address_city as address_city_name ,b.address_latitude as lat ,b.address_longitude as lng
                    FROM #__toes_venue as a 
                    LEFT JOIN #__toes_address as b ON a.venue_address = b.address_id 
                    WHERE LOWER(venue_name) = " . $db->Quote(strtolower($venue));
            
            $db->setQuery($query);
            $v = $db->loadObject();
            if ($v) {
                echo "{$v->address_line_1}|{$v->address_line_2}|{$v->address_line_3}|{$v->address_zip_code}|{$v->address_city_name}|{$v->address_city}|{$v->address_state_name}|{$v->address_state}|{$v->address_country_name}|{$v->address_country}|{$v->lat}|{$v->lng}";
            }
            else
                echo '';
        }
        
        $app->close();
    }
    
    public function subscribe(){
        $app = JFactory::getApplication();
        
        $model = $this->getModel();
        
        $show_id = $app->input->getInt('show_id');
        $user_id = $app->input->getInt('user_id');
        
        if($model->subscribe($show_id,$user_id))
        {
            echo '1';
        }
        else
        {
            echo 'error';
        }
        
        $app->close();
    } 
    
    public function unsubscribe(){
        $app = JFactory::getApplication();
        
        $model = $this->getModel();
        
        $show_id = $app->input->getInt('show_id');
        $user_id = $app->input->getInt('user_id');
        
        if($model->unsubscribe($show_id,$user_id))
        {
            echo '1';
        }
        else
        {
            echo 'error';
        }
        
        $app->close();
    } 
    
    public function sendShowLicenseApplication(){
    	$app = JFactory::getApplication();
    	$db = JFactory::getDbo();
    	
    	$model = $this->getModel();
    	
    	$show_id = $app->input->getInt('id');
    	$rsl_first_show = $app->input->getInt('rsl_first_show');
    	$rsl_ab_rings = $app->input->getInt('rsl_ab_rings');
    	$rsl_sp_rings = $app->input->getInt('rsl_sp_rings');
    	$rsl_congress_rings = $app->input->getInt('rsl_congress_rings');
    	$rsl_total_fees = $app->input->getInt('rsl_total_fee');
    	$rsl_license_fees = $app->input->getInt('rsl_license_fee');
    	$rsl_anual_award_fees = $app->input->getInt('rsl_anual_award_fee');
    	
    	$rsl_include_show_supplies = $app->input->getInt('rsl_include_show_supplies');
    	//$rsl_insurance_info = base64_decode($app->input->getVar('rsl_insurance_info'));
		$rsl_insurance_info = $app->input->getVar('rsl_insurance_info');
    	$rsl_ship_name = $app->input->getVar('rsl_ship_name');
    	$rsl_ship_address = $app->input->getVar('rsl_ship_address');
    	$rsl_ship_city = $app->input->getVar('rsl_ship_city');
    	$rsl_ship_zip = $app->input->getVar('rsl_ship_zip');
    	$rsl_ship_state = $app->input->getVar('rsl_ship_state');
    	$rsl_ship_country = $app->input->getVar('rsl_ship_country');
    	
    	$show    = TOESHelper::getShowDetails($show_id);
    	$showMangers = TOESHelper::getShowManagers($show_id);
    	$clubOfficials = TOESHelper::getClubOfficials($show->club_id);
    	
		$mailTemplate = TOESMailHelper::getTemplate('show_license_application');

		if($mailTemplate) {
			$subject = $mailTemplate->mail_subject;
			$body = $mailTemplate->mail_body;
		} else {
			$subject = JText::_('COM_TOES_LICENSE_APPLICATION_EMAIL_SUBJECT');
			$body = JText::_('COM_TOES_LICENSE_APPLICATION_EMAIL_BODY');
		}

		$subject = str_replace('[club]', $show->club_name, $subject);
    	
    	$body = str_replace('[club]', $show->club_name, $body);
    	$body = str_replace('[show_format]', $show->show_format, $body);
    	$body = str_replace('[show_location]', $show->Show_location, $body);
    	$body = str_replace('[regionname]', $show->competitive_region_abbreviation, $body);
    	
    	if($rsl_first_show)
    		$body = str_replace('[first_show]', JText::_('COM_TOES_LICENSE_APPLICATION_FIRST_SHOW'), $body);
    	else 
    		$body = str_replace('[first_show]', '', $body);

    	if($rsl_include_show_supplies)
    	{
    		$body = str_replace('[NOT]', '', $body);
    		$shipping_address = $rsl_ship_name.'<br/>';
    		$shipping_address .= $rsl_ship_address.'<br/>';
    		$shipping_address .= $rsl_ship_city.'<br/>';
    		$shipping_address .= $rsl_ship_zip.'<br/>';
    		if($rsl_ship_state)
    			$shipping_address .= $rsl_ship_state.'<br/>';
    		$shipping_address .= $rsl_ship_country.'<br/>';
	    	
	    	$shipping_info = JText::_('COM_TOES_LICENSE_APPLICATION_SHIPPING_INFO');
	    	$shipping_info = str_replace('[shipping_address]', $shipping_address, $shipping_info);
	    	
	    	$body = str_replace('[shipping_info]', $shipping_info, $body);
    	}
    	else 
    	{
    		$body = str_replace('[NOT]', 'not', $body);
    		$body = str_replace('[shipping_info]', '', $body);
    	}
    	
    	$start_date = date('d', strtotime($show->show_start_date));
    	$start_date_month = date('M', strtotime($show->show_start_date));
    	$start_date_year = date('Y', strtotime($show->show_start_date));
    	
    	$end_date = date('d', strtotime($show->show_end_date));
    	$end_date_month = date('M', strtotime($show->show_end_date));
    	$end_date_year = date('Y', strtotime($show->show_end_date));
    	
    	$show_date =  $start_date_month.' '.$start_date;
    	
    	if ($end_date_year != $start_date_year){
    		$show_date .= ' '.$start_date_year;
    	}
    	
    	if ($end_date_month != $start_date_month){
    		if(date('t', strtotime($show->show_start_date)) != $start_date)
    			$show_date .= ' - '.date('t', strtotime($show->show_start_date));
    		if($end_date == '01')
    			$show_date .= ', ' .$end_date_month.' '.$end_date;
    		else
    			$show_date .= ', ' .$end_date_month.' 01 - '.$end_date;
    	} else {
    		if($start_date != $end_date)
    			$show_date .= ' - ' . $start_date_month.' '.$end_date;
    	}
    	
    	$show_date .= ' '.$end_date_year;
    	
    	$body = str_replace('[Startdate] - [Enddate]', $show_date, $body);
    	 
    	$showManagerInfo = '';
    	foreach($showMangers as $manager) {
    		$showManager = '';
    		$showManager .= $manager->show_manager_name.'<br/>';
    		$showManager .= '<a href="mailto:'.$manager->show_manager_email.'">'.$manager->show_manager_email.'</a><br/>';
    		$showManagerInfo .= $showManager;
    	}
    	$body = str_replace('[show_manager_contact]', $showManagerInfo, $body);
    	
    	$body = str_replace('[insurance_info]', $rsl_insurance_info, $body);
    	$body = str_replace('[ab_rings]', $rsl_ab_rings, $body);
    	$body = str_replace('[sp_rings]', $rsl_sp_rings, $body);
    	$body = str_replace('[congress_rings]', $rsl_congress_rings, $body);
    	
    	$body = str_replace('[total_fee]', $rsl_total_fees, $body);
    	$body = str_replace('[license_fee]', $rsl_license_fees, $body);
    	$body = str_replace('[anual_award_fee]', $rsl_anual_award_fees, $body);
    	
    	$judges = $model->getJudges();
    	$congress_judges = $model->getCongressJudges();
    	$showdays = $model->getShowDays();
    		
    	$this->judges = array();
    	$this->congress_judges = array();
    	$this->ring_timings = array();
    	
    	foreach($judges as $judge)
    	{
    		if(!$judge->ring_timing)
    			$ring_timing = 0;
    		else
    			$ring_timing = $judge->ring_timing;
    		$this->judges[$judge->show_day_date][$ring_timing][] = $judge;
    	
    		if(!(isset($this->ring_timings[$judge->show_day_date]) && in_array($ring_timing, $this->ring_timings[$judge->show_day_date])))
    			$this->ring_timings[$judge->show_day_date][] = $ring_timing;
    	}
    	
    	foreach($congress_judges as $judge)
    	{
    		if(!$judge->ring_timing)
    			$ring_timing = 0;
    		else
    			$ring_timing = $judge->ring_timing;
    		$this->congress_judges[$judge->show_day_date][$ring_timing][] = $judge;
    	
    		if(!(isset($this->ring_timings[$judge->show_day_date]) && in_array($ring_timing, $this->ring_timings[$judge->show_day_date])))
    			$this->ring_timings[$judge->show_day_date][] = $ring_timing;
    	}
    	foreach($showdays as $showday)
    	{
    		if(isset($this->ring_timings[$showday->show_day_date]))
    			ksort($this->ring_timings[$showday->show_day_date]);
    	}
    	
    	ob_start();
		if ($this->judges || $this->congress_judges) {
			if ($show->show_format == 'Continuous') {
				$i = 0;
				foreach ( $this->judges as $show_day => $ring_timings ) {
					foreach ( $ring_timings as $ring_timing => $judges ) {
						foreach ( $judges as $judge ) {
							if ($i != 0)
								echo ', ';
							switch ($judge->ring_format) {
								case 'Allbreed' :
									$ring_format = 'AB';
									break;
								case 'Specialty' :
									$ring_format = 'SP';
									break;
							}
							
							echo $judge->name . '(' . $ring_format . ')';
							$i ++;
						}
					}
				}
				echo '<br/>';
				foreach ( $this->congress_judges as $show_day => $ring_timings ) {
					foreach ( $ring_timings as $ring_timing => $judges ) {
						if (isset ( $this->congress_judges [$show_day] [$ring_timing] ) && count ( $this->congress_judges [$show_day] [$ring_timing] ) > 0) {
							echo JText::_ ( 'COM_TOES_ENTRY_CONGRESS' ) . ': ';
							$i = 0;
							foreach ( $this->congress_judges [$show_day] [$ring_timing] as $judge ) {
								if ($i != 0)
									echo ', ';
								echo $judge->name . '(' . $judge->ring_name . ')';
								$i ++;
							}
						}
					}
				}
			} else {
				foreach ( $showdays as $showday ) {
					$show_day = $showday->show_day_date;
					if (isset ( $this->ring_timings [$show_day] ) && count ( $this->ring_timings [$show_day] ) > 0) {
						echo date ( 'l', strtotime ( $show_day ) ) . '<br/>';
						foreach ( $this->ring_timings [$show_day] as $ring_timing ) {
							echo "&nbsp;&nbsp;&nbsp;&nbsp;";
							switch ($ring_timing) {
								case '1' :
									echo 'AM: ';
									break;
								case '2' :
									echo 'PM: ';
									break;
							}
							if (isset ( $this->judges [$show_day] [$ring_timing] ) && count ( $this->judges [$show_day] [$ring_timing] ) > 0) {
								$i = 0;
								foreach ( $this->judges [$show_day] [$ring_timing] as $judge ) {
									if ($i != 0)
										echo ', ';
									switch ($judge->ring_format) {
										case 'Allbreed' :
											$ring_format = 'AB';
											break;
										case 'Specialty' :
											$ring_format = 'SP';
											break;
									}
									
									echo $judge->name . '(' . $ring_format . ')';
									$i ++;
								}
								echo '<br/>';
							} else {
								if ($ring_timing)
									echo '<br/>';
							}
							if (isset ( $this->congress_judges [$show_day] [$ring_timing] ) && count ( $this->congress_judges [$show_day] [$ring_timing] ) > 0) {
								if ($ring_timing)
									echo "&nbsp;&nbsp;&nbsp;&nbsp;";
								echo "&nbsp;&nbsp;&nbsp;&nbsp;" . JText::_ ( 'COM_TOES_ENTRY_CONGRESS' ) . ': ';
								$i = 0;
								foreach ( $this->congress_judges [$show_day] [$ring_timing] as $judge ) {
									if ($i != 0)
										echo ', ';
									echo $judge->name . '(' . $judge->ring_name . ')';
									$i ++;
								}
								echo '<br/>';
							}
						}
					}
				}
			}
		}
		
        $rings = ob_get_contents();
    	ob_end_clean();
    	
    	$body = str_replace('[rings]', $rings, $body);
    	 
    	$params		= JComponentHelper::getParams('com_toes');
    	
    	$recipient  = $params->get('tica_eo_acc_dept_email');
    	 
    	$cc = array();
        foreach($clubOfficials as $co) {
    		$cc[] = $co->co_email;
		}
    	
    	if($show->show_use_club_show_manager_address)
    		$cc[] = $show->show_use_club_show_manager_address;
    	else
    	{
    	    foreach($showMangers as $manager) {
    	    	$cc[] = $manager->show_manager_email;
    		}
    	}
		
    	if(TOESMailHelper::sendMail('show_license_application', $subject, $body, $recipient, JText::_('COM_TOES_TICA_EO'), $cc))
    	{	
			$query = $db->getQuery(true);
			
			$query->update('#__toes_show');
			$query->set('show_licensed = 1');
			$query->where('show_id = '.$show_id);
			
			$db->setQuery($query);
			$db->execute();
    		return true;
    	}
    	else
    	{
			return false;
    		//echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');
    	}
    	//$app->close();
    }
	
	public function sendInvoice(){

		$app    = JFactory::getApplication();

        if(!TOESHelper::isAdmin())
        {
            JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
            $app->close();
        }

		jimport('paypal.bootstrap');

		$show_id = $app->input->getInt('show_id',0);
		$club_id = $app->input->getInt('club_id',0);

		$club = TOESHelper::getClub($show_id);
		$show = TOESHelper::getShowDetails($show_id);
		
		if($club->club_id != $club_id ) {
			echo 'Club does not match';
			$app->close();
		}

		$params = JComponentHelper::getParams('com_toes');
		
		if($params->get('paypal_sandbox') == '1') {
			$clientId = $params->get('paypal_sandbox_client_id');
			$clientSecret = $params->get('paypal_sandbox_secret');

			$merchant_email = $params->get('paypal_sandbox_merchant_email');
			$customer_email = $params->get('paypal_sandbox_customer_email');
		} else {
			$clientId = $params->get('paypal_client_id');
			$clientSecret = $params->get('paypal_secret');

			$merchant_email = $params->get('paypal_merchant_email');
			$customer_email = $club->club_invoice_paypal;
		}

		if(!$customer_email) {
			echo JText::_('COM_TOES_CLUB_DOESNT_HAVE_PAYPAL');
			$app->close();
		}
		
		$show_entries = TOESHelper::getShowEntriesByShowday($show_id);
		$max_entries = max($show_entries);
		
		$invoice_price = 0;
		if($max_entries > 125){
			$invoice_price = 65;
		} else if($max_entries > 90){
			$invoice_price = 55;
		} else {
			$invoice_price = 45;
		}

		$invoice = new Invoice();

		$invoice_name = JText::_('COM_TOES_PAYPAL_INVOICE_TITLE');
		$invoice_description = JText::_('COM_TOES_PAYPAL_INVOICE_DESCRIPTION');
		
		$invoice_currency = $params->get('paypal_currency','EUR');
		
		$invoice_description = str_replace('[club]', $show->club_name, $invoice_description);
		$invoice_description = str_replace('[show_dates]', $show->show_dates, $invoice_description);
		$invoice_description = str_replace('[show_location]', $show->Show_location, $invoice_description);
		
		$invoice
			->setNumber($show_id)
			->setMerchantInfo(new MerchantInfo())
			->setBillingInfo(array(new BillingInfo()))
			->setNote($invoice_description)
			->setPaymentTerm(new PaymentTerm());

		$invoice->getMerchantInfo()
			->setEmail($merchant_email);

		$billing = $invoice->getBillingInfo();
		$billing[0]
			->setEmail($customer_email);

		$items = array();
		$items[0] = new InvoiceItem();
		$items[0]
			->setName($invoice_name)
			->setQuantity(1)
			->setUnitPrice(new Currency());

		$items[0]->getUnitPrice()
			->setCurrency($invoice_currency)
			->setValue($invoice_price);

		$invoice->setItems($items);

		$invoice->getPaymentTerm()
			->setTermType("NET_10");

		$apiContext = getApiContext($clientId,$clientSecret);
		
		try {
			// ### Create Invoice
			$invoice->create($apiContext);
		} catch (Exception $ex) {
			$data = json_decode($ex->getData());
			if(isset($data->name)) {
				echo 'Create Invoice Error : '.$data->name;
			} else {
				echo 'Create Invoice Error : '.$ex->getMessage();
			}
			$app->close();
		}

		try {
			// ### Send Invoice
			$invoice->send($apiContext);
		} catch (Exception $ex) {
			$data = json_decode($ex->getData());
			if(isset($data->name)) {
				echo 'Send Invoice Error : '.$data->name;
			} else {
				echo 'Send Invoice Error : '.$ex->getMessage();
			}
			$app->close();
		}
		
		$show_invoice = Invoice::get($invoice->getId(), $apiContext);

		$db = JFactory::getDBO();
		$query = "INSERT INTO `#__toes_paypal_invoice_detail` (`show_id`, `billing_email`, `invoice_id`, `invoice_status`, `created_date`) 
					VALUES (".$db->Quote($show_id).", ".$db->Quote($customer_email).", 
					".$db->Quote($show_invoice->getId()).", ".$db->Quote($show_invoice->getStatus()).", ".$db->Quote($show_invoice->getMetadata()->getCreatedDate()).")";
					
		$db->setQuery($query);
		if($db->execute()) {
			echo '1';
		} else {
			echo 'Error : '.$db->getErrorMessage();
		}
		$app->close();
	}
	
	 public function approveshow()
    {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->getInt('id');
		$hash = $app->input->get('hash');
		$reason = $app->input->get('data');
		
		$query = $db->getQuery(true);
		$query->select('`hash`');
		$query->from('#__toes_show_club_approval');
		$query->where('show_id='.$db->quote($id).'AND `hash`='.$db->quote($hash));
		$db->setQuery($query);
		$url = $db->loadResult();
		
		$reject = '0';
		$approval = '1';
		$query1 = $db->getQuery(true);
		$query1 = "update `#__toes_show_club_approval` set `reason`=".$db->quote($reason).",
				`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
				where show_id=".$db->quote($id)." AND `hash`=".$db->quote($url);

		$db->setQuery($query1);
		$db->query();
		echo 1;
		exit();
	}
	
	public function disapproveshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		$id = $app->input->getInt('id');
		$hash = $app->input->get('hash');
		$reason = $app->input->get('data');
	
		$query = $db->getQuery(true);
		$query->select('current_show_id,datetime');
		$query->from('#__toes_show_club_approval');
		$query->where('show_id='.$db->quote($id).'AND `hash`='.$db->quote($hash));
		$db->setQuery($query);
		$clubdetails = $db->loadObject();
		
		$query1 = $db->getQuery(true);
		$query1->select('show_id');
		$query1->from('#__toes_show_club_approval');
		$query1->where('current_show_id='.$db->quote($clubdetails->current_show_id).' AND datetime='.$db->quote($clubdetails->datetime));
		$db->setQuery($query1);
		$conflictshow = $db->loadObjectList();
	
		foreach($conflictshow as $c)
		{	
			$query1 = $db->getQuery(true);
			$reject = '1';
			$approval = '0';
			$query1 = "update `#__toes_show_club_approval` set `reason`=".$db->quote($reason).",
					`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
					where show_id=".$db->quote($id)." AND `current_show_id`=".$db->quote($clubdetails->current_show_id).' AND datetime='.$db->quote($clubdetails->datetime);
		
			$db->setQuery($query1);
			$db->query();
			$status = 'Rejected';
			
			if($status == 'Rejected')
			{
				$query = $db->getQuery(true);

				$query->update('#__toes_show');
				$query->set('`show_status` = (SELECT `show_status_id` FROM `#__toes_show_status` WHERE `show_status` = ' . $db->quote($status) . ')');
				$query->where('`show_id` = ' . $clubdetails->current_show_id);
				$db->setQuery($query);
				$db->query();
			}
		}	
	
		echo 1;
		exit();
	}
	
	public function rdapproveshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->getInt('id');
		$hash = $app->input->get('hash');
		$reason = $app->input->get('data');
		
		$reject = '0';
		$approval = '1';
		$query1 = $db->getQuery(true);
		$query1 = "update `#__toes_show_reginal_director_approval` set `reason`=".$db->quote($reason).",
				`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
				where current_show_id=".$db->quote($id)." AND `hash`=".$db->quote($hash);

		$db->setQuery($query1);
		$db->query();
		$status = 'Approved';
		if($status == 'Approved')
		{
			$query = $db->getQuery(true);

			$query->update('#__toes_show');
			$query->set('`show_status` = (SELECT `show_status_id` FROM `#__toes_show_status` WHERE `show_status` = ' . $db->quote($status) . ')');
			$query->where('`show_id` = ' . $id);
			$db->setQuery($query);
			$db->query();
		}
		echo 1;
		exit();	
	}
	
	public function rddisapproveshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		$id = $app->input->getInt('id');
		$hash = $app->input->get('hash');
		$reason = $app->input->get('data');
		$query1 = $db->getQuery(true);
		$reject = '1';
		$approval = '0';
		$query1 = "update `#__toes_show_reginal_director_approval` set `reason`=".$db->quote($reason).",
				`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
				where show_id=".$db->quote($id)."AND `hash`=".$db->quote($hash);
	
		$db->setQuery($query1);
		$db->query();
		$status = 'Rejected';
		if($status == 'Rejected')
		{
			$query = $db->getQuery(true);

			$query->update('#__toes_show');
			$query->set('`show_status` = (SELECT `show_status_id` FROM `#__toes_show_status` WHERE `show_status` = ' . $db->quote($status) . ')');
			$query->where('`show_id` = ' . $id);
			$db->setQuery($query);
			$db->query();
		}
		echo 1;
		exit();
	}
	
	public function conflicted_rdapproveshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->getInt('id');
		$hash = $app->input->get('hash');
		$reason = $app->input->get('data');
		
		
		$query = $db->getQuery(true);
		$query->select('`hash`');
		$query->from('#__toes_show_reginal_director_approval');
		$query->where('show_id='.$db->quote($id).'AND `hash`='.$db->quote($hash));
		$db->setQuery($query);
		$url = $db->loadResult();
		
		$reject = '0';
		$approval = '1';
		$query1 = $db->getQuery(true);
		$query1 = "update `#__toes_show_reginal_director_approval` set `reason`=".$db->quote($reason).",
				`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
				where show_id=".$db->quote($id)." AND `hash`=".$db->quote($hash);

		$db->setQuery($query1);
		$db->query();
		echo 1;
		exit();	
	}
	
	public function confliced_rddisapproveshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->getInt('id');
		$hash = $app->input->get('hash');
		$reason = $app->input->get('data');
		$query = $db->getQuery(true);
		$query->select('new_conflicting_show_id,datetime');
		$query->from('#__toes_show_regional_director_approval');
		$query->where('existing_show_id='.$db->quote($id).'AND `hash`='.$db->quote($hash));
		$db->setQuery($query);
		$clubdetails = $db->loadObject();
		$query1 = $db->getQuery(true);
		$reject = '1';
		$approval = '0';
		$query1 = "update `#__toes_show_reginal_director_approval` set `reason`=".$db->quote($reason).",
				`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
				where show_id=".$db->quote($id)."AND `hash`=".$db->quote($hash);
		$db->setQuery($query1);
		$db->query();
		$status = 'Rejected';
		if($status == 'Rejected')
		{
			$query = $db->getQuery(true);

			$query->update('#__toes_show');
			$query->set('`show_status` = (SELECT `show_status_id` FROM `#__toes_show_status` WHERE `show_status` = ' . $db->quote($status) . ')');
			$query->where('`show_id` = ' . $clubdetails->current_show_id);
			$db->setQuery($query);
			$db->query();
		}
		echo 1;
		exit();
	}
}
