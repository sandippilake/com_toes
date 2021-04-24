<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template style controller class.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESControllerUser extends JControllerForm {

    /**
     * @var		string	The prefix to use with controller messages.
     */
    public function save($key = NULL, $urlVar = NULL) {
		$app = JFactory::getApplication();
        $model = parent::getModel('user', 'ToesModel', array('ignore_request' => true));
        $post = $app->input->post->getArray();

        $return = $model->save($post);

        if ($return)
            $this->setRedirect(JRoute::_('index.php?option=com_toes&view=users'), JText::_('COM_TOES_OFFICIAL_ADDED_SUCCESS'));
        else
            $this->setRedirect(JRoute::_('index.php?option=com_toes&view=users'), JText::_('COM_TOES_OFFICIAL_ADDED_UNSUCCESS'));
    }
    
    public function getUsers() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT user.id AS `key`, CONCAT(cb.firstname,' ',cb.lastname,' - ',user.username) AS `value` 
                FROM #__users as user
                LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                WHERE CONCAT(LOWER(cb.firstname),' ',LOWER(cb.lastname),' - ',LOWER(user.username)) LIKE  " . $like;

            $db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
                echo json_encode($users);
            }
        }
        $app->close();
    }

    public function changeshows() {
        $app = JFactory::getApplication();
        $club = $app->input->getVar('club');
        $show_id = $app->input->getVar('show_id','');
//cntry.name LEFT JOIN #__toes_country as cntry ON cntry.id = ta.address_country
//state.name LEFT JOIN #__toes_states_per_country as state ON state.id = ta.address_state
// LEFT JOIN #__toes_cities_per_state as city ON city.id = ta.address_city
        if ($club) 
        {
            $db = JFactory::getDBO();
            $query = "SELECT a.show_id as `value`, CONCAT(ta.address_city,' ',IF(ta.address_state != NULL,ta.address_state,''),' ',ta.address_country,' : ',DATE_FORMAT(a.show_start_date,'%d %b %y'),' - ',DATE_FORMAT(a.show_end_date,'%d %b %y') ) as text FROM #__toes_show as a
                    LEFT JOIN #__toes_club_organizes_show as b ON b.show = a.show_id   
                    LEFT JOIN #__toes_venue as tv ON a.show_venue = tv.venue_id
                    LEFT JOIN #__toes_address as ta ON tv.venue_address = ta.address_id
	                
	                
	               
                    WHERE b.club = " . $club;

            $db->setQuery($query);
            $user_rolls = $db->loadObjectList();

            $user_rolllist = array();
            $user_rolllist[] = JHTML::_('select.option', '', 'Select Show');
            $user_rolllist = array_merge($user_rolllist, $user_rolls);

            echo JHTML::_('select.genericlist', $user_rolllist, 'official', '', 'value', 'text', $show_id);
        }
        else
        {
			$user_rolllist[] = JHTML::_('select.option', '', 'Select Show');
			echo JHTML::_('select.genericlist', $user_rolllist, 'official', '', 'value', 'text', '');
		}
							
        $app->close();
    }
    function response($response_code,$response_desc){
	 $app = JFactory::getApplication();	 
	 $response['response_code'] = $response_code;
	 $response['response_desc'] = $response_desc;
	 
	 $json_response = json_encode($response);
	 echo $json_response;
	 $app->close();		
	}

    public function set_tds_guid(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$data_str = file_get_contents('php://input');
		$data = json_decode($data_str , true);
		//var_dump($data);
		
		$key = trim($data['key']);
		
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		
		if(!$key){
			http_response_code(401);
			$this->response('401','Key missing');
			$app->close();
		}
		$params = JComponentHelper::getParams('com_toes');
		$key_for_tds_to_access_tica = $params->get('key_for_tds_to_access_tica');
		if($key != $key_for_tds_to_access_tica ){
			http_response_code(401);
			$this->response('401','Incorrect key');
			$app->close();
		}
		
		$tica_guid = trim($data['tica_guid']);
		if(!$tica_guid){
			http_response_code(400);
			$this->response('400','Tica GUID missing');
			$app->close();			
		}
		$db->setQuery("select * from `#__comprofiler` where `cb_tica_guid` =".$db->Quote($tica_guid)); 
		$exists = $db->loadObject();
		if(!$exists){
			http_response_code(400);
			$this->response('400','Tica GUID incorrect');
			$app->close();					
		}
		$tds_guid = trim($data['tds_guid']);
		if(!$tds_guid){
			http_response_code(402);
			$this->response('402','TDS GUID missing');
			$app->close();			
		}
		
		
		
		preg_match('/[0-9A-F]{8}-(?:[0-9A-F]{4}-){3}[0-9A-F]{12}/i', $tds_guid,$matches);
		if($matches[0] == $tds_guid ){
			if(!$exists->cb_tds_guid){
			$db->setQuery("UPDATE `#__comprofiler` SET `cb_tds_guid` =".$db->Quote($tds_guid)." where `id` =".$exists->id)->execute();
			http_response_code(202);
			$this->response('202','TDS GUID updated');
			$app->close();		
			}else{
			// send email to webmaster@tica.org	
			http_response_code(200);
			$this->response('200','TDS GUID already set');
			$app->close();		
				
			}			
			
		}else{			
			http_response_code(403);
			$this->response('403','TDS GUID not in proper format');
			$app->close();				
		}
		
		 
		
		
		
	}

}
