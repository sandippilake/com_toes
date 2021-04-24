<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template styles list controller class.
 * @package	Joomla
 * @subpackage	com_toes
 */
require_once('media/com_toes/class.fileuploader.php');

class TOESControllerEntry extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * 
     */
    public function getModel($name = 'entry', $prefix = 'ToesModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }
    
    function saveFilterCriteria()
    {
        $app    = JFactory::getApplication();
        $model  = $this->getModel();

        if($model->saveFilterCriteria())
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
    }
    
    function matchCriteriaforsameCongress()
    {
        $app    = JFactory::getApplication();
        $db     = JFactory::getDbo();
        
        $validate = true;
        
        $temp_rings = explode(',', $app->input->getVar('rings'));
        
        $congress_rings = array();
        for($i=0;$i<count($temp_rings);$i+=3)
        {
            $ring['index'] = $temp_rings[$i];
            $ring['ring_id'] = $temp_rings[$i+1];
            $ring['ring_name'] = $temp_rings[$i+2];
            
            $congress_rings[strtolower($temp_rings[$i+2])][] = $ring;
        }

        foreach($congress_rings as $rings)
        {
            if(count($rings) > 2)
            {
                for($j=1;$j<count($rings);$j++)
                {
                    if(!$this->compareCriteria($rings[0],$rings[$j]))
                    {
                        $validate = false;
                        break;
                    }
                }
            }
            elseif(count($rings) == 2)
            {
                if(!$this->compareCriteria($rings[0],$rings[1]))
                {
                    $validate = false;
                    break;
                }
            }
            
            if($validate == false)
                break;
        }
        
        if($validate)
        {
            echo 1;
        }
        else
        {
            echo "Error";
        }
        
        $app->close();
    }
    
    function compareCriteria($ring1, $ring2)
    {
        $ring1_filters = '';
        $ring2_filters = '';
        
        $session = JFactory::getSession();
        $filters = '';
        
        if($session->has('congress_filters'))
        {
            $str = $session->get('congress_filters');
            $filters = unserialize($str);
        }
        
        if($ring1['ring_id'])
            $ring1_filters = TOESHelper::getCongressFilters ($ring1['ring_id']);
        else {
            foreach ($filters as $item)
            {
                if($item->ring_index == $ring1['index'])
                    $ring1_filters = $item;
            }
        }
        if($ring2['ring_id'])
            $ring2_filters = TOESHelper::getCongressFilters ($ring2['ring_id']);
        else {
            foreach ($filters as $item)
            {
                if($item->ring_index == $ring2['index'])
                    $ring2_filters = $item;
            }
        }
        
        if($ring1_filters && $ring2_filters)
        {
            if($ring1_filters->breed_filter != $ring2_filters->breed_filter)
                return false;
            
            if($ring1_filters->gender_filter != $ring2_filters->gender_filter)
                return false;
            
            if($ring1_filters->newtrait_filter != $ring2_filters->newtrait_filter)
                return false;
            
            if($ring1_filters->hairlength_filter != $ring2_filters->hairlength_filter)
                return false;
            
            if($ring1_filters->category_filter != $ring2_filters->category_filter)
                return false;
            
            if($ring1_filters->division_filter != $ring2_filters->division_filter)
                return false;
            
            if($ring1_filters->color_filter != $ring2_filters->color_filter)
                return false;
            
            if($ring1_filters->title_filter != $ring2_filters->title_filter)
                return false;
            
            if($ring1_filters->manual_filter != $ring2_filters->manual_filter)
                return false;

            $ring1_class_value = explode(',', $ring1_filters->class_value);
            $ring2_class_value = explode(',', $ring2_filters->class_value);
            if(array_diff($ring1_class_value, $ring2_class_value))
                return false;

            $ring1_breed_value = explode(',', $ring1_filters->breed_value);
            $ring2_breed_value = explode(',', $ring2_filters->breed_value);
            if(array_diff($ring1_breed_value, $ring2_breed_value))
                return false;

            $ring1_gender_value = explode(',', $ring1_filters->gender_value);
            $ring2_gender_value = explode(',', $ring2_filters->gender_value);
            if(array_diff($ring1_gender_value, $ring2_gender_value))
                return false;

            $ring1_hairlength_value = explode(',', $ring1_filters->hairlength_value);
            $ring2_hairlength_value = explode(',', $ring2_filters->hairlength_value);
            if(array_diff($ring1_hairlength_value, $ring2_hairlength_value))
                return false;

            $ring1_category_value = explode(',', $ring1_filters->category_value);
            $ring2_category_value = explode(',', $ring2_filters->category_value);
            if(array_diff($ring1_category_value, $ring2_category_value))
                return false;

            $ring1_division_value = explode(',', $ring1_filters->division_value);
            $ring2_division_value = explode(',', $ring2_filters->division_value);
            if(array_diff($ring1_division_value, $ring2_division_value))
                return false;

            $ring1_color_value = explode(',', $ring1_filters->color_value);
            $ring2_color_value = explode(',', $ring2_filters->color_value);
            if(array_diff($ring1_color_value, $ring2_color_value))
                return false;

            $ring1_title_value = explode(',', $ring1_filters->title_value);
            $ring2_title_value = explode(',', $ring2_filters->title_value);
            if(array_diff($ring1_title_value, $ring2_title_value))
                return false;

            $ring1_cwd_value = explode(',', strtolower($ring1_filters->cwd_value));
            $ring2_cwd_value = explode(',', strtolower($ring2_filters->cwd_value));
            if(array_diff($ring1_cwd_value, $ring2_cwd_value))
                return false;
            
            return true;
        }
        else
            return false;
    }
    
    function validateCriteria()
    {
        $app    = JFactory::getApplication();
        $db     = JFactory::getDbo();
        
        $validate = false;
        
        $ring_index = $app->input->getInt('index');
        $ring_id = $app->input->getInt('ring_id');

        if($ring_id)
        {
            $query = $db->getQuery(true);
            $query->select('congress_id');
            $query->from('#__toes_congress');
            $query->where('congress_id='.$ring_id );    
            $db->setQuery($query);
            $isCongressFilterAvilable = $db->loadResult();
            if($isCongressFilterAvilable)
            {
                $query = $db->getQuery(true);
                $query->select('congress_competitive_class_competitive_class');
                $query->from('#__toes_congress_competitive_class');
                $query->where('congress_competitive_class_congress='.$ring_id );    
                $db->setQuery($query);
                if($db->loadResult())
                    $validate = true;
            }
        }
        
        if(!$validate && $ring_index)
        {
            $session = JFactory::getSession();
            
            if($session->has('congress_filters'))
            {
                $str = $session->get('congress_filters');
                $filters = unserialize($str);
                
                foreach ($filters as $item)
                {
                    if($item->ring_index == $ring_index)
                    {
                        if($item->class_value)
                            $validate = true;
                    }
                }
            }
        }
        
        if($validate)
        {
            echo 1;
        }
        else
        {
            echo "Error";
        }
        
        $app->close();
    }

    function saveEntryDetails()
    {
        $app    = JFactory::getApplication();
        $model  = $this->getModel();
        $show_id = $app->input->getInt('show_id');
        $cat_id = $app->input->getInt('cat_id');

        if($model->saveEntryDetails())
        {
            $isApplicable = TOESHelper::isApplicableForCongress($show_id, $cat_id);
            if($isApplicable)
            {
                echo 'Congress: '.$isApplicable;
            }
            else
                echo '1';
        }
        else
        {
            echo 'Error: '.$model->getError();
        }
        $app->close();
    }

    public function save_entry() {
        $model = parent::getModel('entry', 'ToesModel', array('ignore_request' => true));
        $app    = JFactory::getApplication();

        if($model->save_entry())
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
    }
    
    public function save_summary() {
		$app    = JFactory::getApplication();
        $model = parent::getModel('entry', 'ToesModel', array('ignore_request' => true));
        if($model->save_summary())
		{
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
		$app->close();
    }
    
    public function save_fees() {
		$app    = JFactory::getApplication();
        $model = parent::getModel('entry', 'ToesModel', array('ignore_request' => true));
        if($model->save_fees())
		{
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
		$app->close();
    }
    
    public function cancel_edit_entry() {
        $app = JFactory::getApplication();
        $session = JFactory::getSession();
        $session->clear('entry');
        $app->close();
    }
    public function imageupload(){
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
		$user=JFactory::getUser();
		$session = JFactory::getSession();
		if (!file_exists('media/com_toes/registration_documents')) 
		{
			mkdir('media/com_toes/registration_documents', 0777, true);
		}
        // initialize FileUploader
        // $doc_no = $app->input->getInt('doc_no');
        $dtid = $app->input->getInt('doc_no');
        $cat_id = $app->input->getInt('cat_id');
        $org_id = $app->input->getInt('org_id');
		$FileUploader = new FileUploader('document_'.$dtid, array(
			'uploadDir' => 'media/com_toes/registration_documents/'
		));
		// call to upload the files
        $data = $FileUploader->upload();

        if($data){
            $folder = floor($cat_id/1000); 
			
            $db->setQuery("select * from `#__toes_recognized_registration_organization`
            where `recognized_registration_organization_id` =".$org_id);
            $organizationrecord = $db->loadObject();
            
            if (!file_exists(TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder))
                JFolder::create(TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder, 0777);
            else
                chmod(TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder, 0777);

            $name = $data['files'][0]['name'];
            $nameparts = explode('.',$name);
            $ext = end($nameparts);
            
            $tmppath = 'media/com_toes/registration_documents/'.$data['files'][0]['name'];
            // $tmppath = TOES_MEDIA_PATH . DS . 'registration_documents' . DS .$name;
            
            $db->setQuery('delete from `#__toes_cat_document` where `cat_document_cat_id` ='.$cat_id."
            AND `cat_document_registration_document_type_id` =".$dtid)->execute();

            $db->setQuery("INSERT INTO `#__toes_cat_document`(`cat_document_registration_document_type_id`,`cat_document_registration_document_organization_id`,`cat_document_cat_id`,cat_document_file_name)
            VALUES(".$dtid.",".$org_id.",".$cat_id.",".$db->Quote($tmppath).") ")->execute();	
            $document_id = $db->insertid();
            $filename = $cat_id.'_'.$document_id.'_'.$dtid.'_'.str_replace(array('.',',',' '),array('_','_','_'),$organizationrecord->recognized_registration_organization_abbreviation).'.'.$ext ;

            $filepath = TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder.DS.$filename;
            
            if(rename($tmppath , $filepath)){
                $filepathdb = 'media'.DS.'com_toes'.DS.'registration_documents'.DS.$folder.DS.$filename;
                $db->setQuery("UPDATE `#__toes_cat_document` SET `cat_document_file_name` =".$db->Quote($filepathdb)."
                where `cat_document_id` =".$document_id)->execute();	
            }
        }
		echo json_encode($data);
		exit;
    }
    public function step() {
		$app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $step = $app->input->getVar('step');
        $dir = $app->input->getVar('dir','next');
        $clear_session = $app->input->getInt('clear_session',0);
        $session = JFactory::getSession();
		if($clear_session == 1) {
			$session->clear('entry');
		}

        if($session->has('entry'))
        {
            $entry = $session->get('entry');
            $edit = $app->input->getInt('edit');
            //echo $edit?1:0;return;
            $cat_id = $app->input->getInt('cat_id',0);
            $show_id = $app->input->getInt('show_id',0);
            if($edit == 1) {
                if(($cat_id && @$entry->cat_id != $cat_id ) || ($show_id && $show_id != @$entry->show_id))
                {
                    $entries = TOESHelper::getEntryFullDetails($cat_id, $show_id);

                    $showdays = array();
					$entry_for_AM = array();
					$entry_for_PM = array();
                    $congress = array();
                    foreach($entries as $item)
                    {
                        $showdays[] = $item->show_day;
					
						if($item->entry_participates_AM)
							$entry_for_AM[]=$item->show_day;

						if($item->entry_participates_PM)
							$entry_for_PM[]=$item->show_day;

                        $query = "SELECT ring_name 
                                FROM #__toes_ring AS r 
                                LEFT JOIN #__toes_entry_participates_in_congress AS c ON c.congress_id = r.ring_id
                                WHERE c.entry_id = {$item->entry_id}";
                        $db->setQuery($query);
                        $result = $db->loadColumn();
                        if($result)
                            $congress = array_merge($congress, $result);
                    }

                    $entry = new stdClass();
                    $entry->entry_id = $item->entry_id;
                    $entry->show_id = $show_id;
                    $entry->user_id = $item->summary_user;
                    $entry->cat_id = $cat_id;
                    $entry->showdays = implode(',', $showdays);
					$entry->entry_for_AM = implode(',', $entry_for_AM);
					$entry->entry_for_PM = implode(',', $entry_for_PM);
                    $entry->exh_only = $item->exhibition_only;
                    $entry->for_sale = $item->for_sale;
                    $entry->agent_name = $item->copy_cat_agent_name;
                    $entry->congress = implode(',', $congress);
                    $entry->single_cages = $item->summary_single_cages;
                    $entry->double_cages = $item->summary_double_cages;
                    $entry->personal_cage = $item->summary_personal_cages;
                    $entry->grooming_space = $item->summary_grooming_space;
                    $entry->benching_request = $item->summary_benching_request;
                    $entry->remark = $item->summary_remarks;
                    $entry->placeholder_id = 0;
                }
                $entry->edit = true;
            }
            else
            {
                if(($show_id && $show_id != $entry->show_id))
                {
                    $entry->show_id = $show_id;
                }
                $entry->edit = false;
                $placeholder_id = $app->input->getInt('placeholder_id');
                if($placeholder_id)
                    $entry->placeholder_id = $placeholder_id;
                elseif(!@$entry->placeholder_id)
                    $entry->placeholder_id = 0;
            }
        }
        else
        {
            $edit = $app->input->getInt('edit');
            if($edit == 1)
            {
                $cat_id = $app->input->getInt('cat_id');
                $show_id = $app->input->getInt('show_id');
                $entries = TOESHelper::getEntryFullDetails($cat_id, $show_id);
                
                $showdays = array();
				$entry_for_AM = array();
				$entry_for_PM = array();
                $congress = array();
                foreach($entries as $item)
                {
                    $showdays[] = $item->show_day;
					
					if($item->entry_participates_AM)
						$entry_for_AM[]=$item->show_day;
					
					if($item->entry_participates_PM)
						$entry_for_PM[]=$item->show_day;
                    
                    $query = "SELECT ring_name 
                            FROM #__toes_ring AS r 
                            LEFT JOIN #__toes_entry_participates_in_congress AS c ON c.congress_id = r.ring_id
                            WHERE c.entry_id = {$item->entry_id}";
                    $db->setQuery($query);
                    $result = $db->loadColumn();
                    if($result)
                        $congress = array_merge($congress, $result);
                }
                
                $entry = new stdClass();
                $entry->entry_id = $item->entry_id;
                $entry->show_id = $show_id;
                $entry->user_id = $item->summary_user;
                $entry->cat_id = $cat_id;
                $entry->showdays = implode(',', $showdays);
				$entry->entry_for_AM = implode(',', $entry_for_AM);
				$entry->entry_for_PM = implode(',', $entry_for_PM);
                $entry->exh_only = $item->exhibition_only;
                $entry->for_sale = $item->for_sale;
                $entry->agent_name = $item->copy_cat_agent_name;
                $entry->congress = implode(',', $congress);
                $entry->single_cages = $item->summary_single_cages;
                $entry->double_cages = $item->summary_double_cages;
                $entry->personal_cage = $item->summary_personal_cages;
                $entry->grooming_space = $item->summary_grooming_space;
                $entry->benching_request = $item->summary_benching_request;
                $entry->remark = $item->summary_remarks;
                $entry->placeholder_id = 0;
                $entry->edit = true;
            }
            else
            {
                $entry = new stdClass();
                $entry->edit = false;

                $placeholder_id = $app->input->getInt('placeholder_id');
                if($placeholder_id)
                    $entry->placeholder_id = $placeholder_id;
            }
        }
        
        switch ($step)
        {
            case 'init' :
                $entry = new stdClass();
                $session->set('entry', $entry);
                return;
                break;
            case 'step0' :
                if($dir != 'prev')
                {
                    $show_id = $app->input->getInt('show_id');
                    $entry->show_id = $show_id;
                    $type = $app->input->getVar('type');
                    $entry->type = $type;

                }
                $layout = 'step0';
                break;
            case 'step1' :
                if($dir != 'prev')
                {
                    $type = $app->input->getVar('type');
                    if($type != 'new')
                    {
                        if(isset($entry->type) && $entry->type == 'third_party')
                            $app->input->set('type', $entry->type);
                    }
                    else
                    {
                        $entry->type = $type;
                        $user_id = $app->input->getInt('user_id');
                        if($user_id)
                            $entry->user_id = $user_id;
                        else
                            $entry->user_id = $user->id;
                    }
                    
                    if(!isset($entry->show_id) || !$entry->show_id)
                    {
                        $show_id = $app->input->getInt('show_id');
                        $entry->show_id = $show_id;
                    }
                    $user_id = $app->input->getInt('user_id');
                    if($user_id)
                        $entry->user_id = $user_id;
                    else
                    {
                        if(!isset($entry->user_id) || !$entry->user_id)
                        {
                            $entry->user_id = $user->id;
                        }
                    }
                }
                $layout = 'step1';
                break;
            /*case 'step_1_5':
                $cat_id = $app->input->getInt('cat_id');
                $db->setQuery('select * from `#__toes_cat_registration_number` where cat_registration_number_cat ='.$cat_id);
                $exists = $db->loadObject();
                if($exists){
                    $layout = 'step2';
                }else{
                    $layout = 'step1_5';
                }
                break;*/
            case 'step1_5':
                $show = TOESHelper::getShowDetails($entry->show_id);
                if($dir != 'prev')
                {
                    $type = $app->input->getVar('type');
                    $entry->type = $type;
                    $cat_id = $app->input->getInt('cat_id');
                    $entry->cat_id = $cat_id;
					$entry_details = '';
                    
                    if($show->show_format == 'Continuous' || $entry->placeholder_id)
                    {
                        if($entry->placeholder_id)
                        {
							$showdays = array();
							$entry_for_AM = array();
							$entry_for_PM = array();
							$entry_details = TOESHelper::getEntryFullDetails($entry->cat_id, $entry->show_id);
							if($entry_details)
							{
								$entry->edit = 1;
								$congress = array();
								foreach($entry_details as $item)
								{
									$showdays[] = $item->show_day;

									if($item->entry_participates_AM)
										$entry_for_AM[]=$item->show_day;

									if($item->entry_participates_PM)
										$entry_for_PM[]=$item->show_day;

									$query = "SELECT ring_name 
											FROM #__toes_ring AS r 
											LEFT JOIN #__toes_entry_participates_in_congress AS c ON c.congress_id = r.ring_id
											WHERE c.entry_id = {$item->entry_id}";
									$db->setQuery($query);
									$result = $db->loadColumn();
									if($result)
										$congress = array_merge($congress, $result);
								}

								$entry->entry_id = $item->entry_id;
								$entry->exh_only = $item->exhibition_only;
								$entry->for_sale = $item->for_sale;
								$entry->agent_name = $item->copy_cat_agent_name;
								$entry->congress = implode(',', $congress);
								$entry->single_cages = $item->summary_single_cages;
								$entry->double_cages = $item->summary_double_cages;
								$entry->personal_cage = $item->summary_personal_cages;
								$entry->grooming_space = $item->summary_grooming_space;
								$entry->benching_request = $item->summary_benching_request;
								$entry->remark = $item->summary_remarks;						
							}

                            $placeholder = TOESHelper::getPlaceholderFullDetails($entry->placeholder_id);
                            foreach($placeholder as $day)
                            {
                                if($day->entry_status != 'Waiting List' || $day->entry_status != 'Waiting List & Confirmed' || $day->entry_status != 'Waiting List & Paid')
								{
                                    $showdays[] = $day->placeholder_day_showday;
					
									if($day->placeholder_participates_AM)
										$entry_for_AM[]=$day->placeholder_day_showday;

									if($day->placeholder_participates_PM)
										$entry_for_PM[]=$day->placeholder_day_showday;
								}
                            }
							
							$entry->showdays = implode(',', $showdays);
							$entry->entry_for_AM = implode(',', $entry_for_AM);
							$entry->entry_for_PM = implode(',', $entry_for_PM);
                        }
                        else
                        {
                            $query = "SELECT `show_day_id` FROM `#__toes_show_day` WHERE `show_day_show` = ".$entry->show_id;
                            $db->setQuery($query);
                            $show_day_id = $db->loadResult();
                            $entry->showdays = $show_day_id;
							
							$app->input->set('step','step3');
							$layout = 'step3';
							break;
                        }
                    }
                }
                else
                {
                    if($show->show_format == 'Continuous')
                    {
						$layout = 'step1';
						$app->input->set('step','step1');
						break;
                    }
                }
                // check if breed == HHP
                
                $HHP_breed_abbreviation = 'HHP';
                $db->setQuery("select `breed_id` from `#__toes_breed` where `breed_abbreviation` = ".$db->Quote($HHP_breed_abbreviation));
                $HHP_breed_id = $db->loadResult();
                $db->setQuery("select `cat_breed` from `#__toes_cat` where `cat_id` =".$cat_id );
                $cat_breed = $db->loadResult();
                
                if($cat_breed == $HHP_breed_id){					
				$layout = 'step2';	
				}else{             
                
                $db->setQuery('select * from `#__toes_cat_registration_number` where cat_registration_number_cat ='.$cat_id);
                $exists = $db->loadObject();
                if($exists){
                    $layout = 'step2';
                    $app->input->set('step','step2');
                }else{
                    $db->setQuery('select SUM(dt.allowed_registration_document_weight) from `#__toes_cat_document` as ct
                    LEFT JOIN `#__toes_allowed_registration_document_type` as dt ON dt.allowed_registration_document_id = ct.cat_document_registration_document_type_id
                    where ct.cat_document_cat_id ='.$cat_id);
                    $weight = $db->loadResult();
                    if($weight >= 2){
                        $layout = 'step2';
                        $app->input->set('step','step2');
                    }else{
                        $layout = 'step1_5';
                    }
                }
                }
                 
                
                //$layout = 'step1_5';
                break;
            case 'step2':
                // $show = TOESHelper::getShowDetails($entry->show_id);
                if($dir != 'prev')
                {
                    //  echo "<pre>";
                    // var_dump($app->input->getString('registration_number'));
                    // die;
                    // $docs = $app->input->getVar('docs');
                    // $entry->documents = $docs;
                    if($app->input->getString('registration_number') && strtolower($app->input->getString('registration_number')) != 'pending'){
                    // update cat here only
                    //$db->setQuery("UPDATE `#__toes_cat` SET `` ");
                    
                    $entry->copy_cat_registration_number = $app->input->getString('registration_number');
                    $entry->registration_number = $app->input->getString('registration_number');
					}
                }

                $layout = 'step2';
                break;
            case 'step3':
                if($dir != 'prev')
                {
                    $showdays = $app->input->getVar('showdays');
					$entry_for_AM = $app->input->getVar('entry_for_AM');
					$entry_for_PM = $app->input->getVar('entry_for_PM');
                    $entry->showdays = $showdays;
					$entry->entry_for_AM = $entry_for_AM;
					$entry->entry_for_PM = $entry_for_PM;
                }
                $layout = 'step3';
                break;
            case 'step4':
                if($dir != 'prev')
                {
                    $exh_only = $app->input->getVar('exh_only');
                    $for_sale = $app->input->getVar('for_sale');
                    $agent_name = $app->input->getVar('agent_name');
                    $entry->exh_only = $exh_only;
                    $entry->for_sale = $for_sale;
                    $entry->agent_name = $agent_name;
                    
                }
                $layout = 'step4';
                break;
            case 'step5':
                if($dir != 'prev')
                {
                    $congress = $app->input->getVar('congress');
                    $participate_in_congress = $app->input->getVar('participate_in_congress');
                    $entry->congress = $congress;
                    $entry->participated_in_congress = $participate_in_congress;
                }
                $layout = 'step5';
                break;
            case 'final':
                $single_cages = $app->input->getVar('single_cages');
                $double_cages = $app->input->getVar('double_cages');
                $personal_cage = $app->input->getVar('personal_cage');
                $grooming_space = $app->input->getVar('grooming_space');
                $benching_request = base64_decode($app->input->getVar('benching_request'));
                $summary_entry_clerk_note = base64_decode($app->input->getVar('summary_entry_clerk_note'));
                $summary_entry_clerk_private_note = base64_decode($app->input->getVar('summary_entry_clerk_private_note'));
                $remark = base64_decode($app->input->getVar('remark'));
                
                $entry->single_cages = $single_cages;
                $entry->double_cages = $double_cages;
                $entry->personal_cage = $personal_cage;
                $entry->grooming_space = $grooming_space;
                $entry->benching_request = $benching_request;
                $entry->summary_entry_clerk_note = $summary_entry_clerk_note;
                $entry->summary_entry_clerk_private_note = $summary_entry_clerk_private_note;
                $entry->remark = $remark;
                
                $session->set('entry',$entry);
                $this->save_entry();
                $app->close();
        }
        
        $session->set('entry',$entry);
        
        $view = $this->getView('entry', 'html');
        $view->setLayout($layout);
        $view->set('entry', $entry);

        $view->display();
		$app->close();
    }

    public function changecatlist() {
    	$app = JFactory::getApplication();
        $breed = $app->input->getInt('breed');
        $category_id = $app->input->getInt('category');
        $db = JFactory::getDbo();

        $query = "SELECT DISTINCT `c`.`category_id` as value, `c`.`category` as text
                FROM `#__toes_breed_category_division_color` AS `bcdc`
                LEFT JOIN `#__toes_category` AS `c` ON `c`.`category_id` = `bcdc`.`category`
                WHERE (`bcdc`.`organization` = 1)";

        if ($breed)
            $query .=" AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";

        //$query.= " ORDER BY c.`category` ASC";
        $query.= " ORDER BY c.`category_id` ASC";

        $db->setQuery($query);
        $categories = $db->loadObjectList();

        $catlist = array();
        $catlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_CATEGORY'));
        $catlist = array_merge($catlist, $categories);

        $exhibitor = $app->input->getInt('exhibitor');
        if($exhibitor)
            $readonly = 'disabled';
        else
            $readonly = '';

        echo JHTML::_('select.genericlist', $catlist, 'copy_cat_category', 'class="inputbox required" aria-required="true" required="required" aria-invalid="false" onchange="changeDivision();" '.$readonly, 'value', 'text', $category_id);
        $app->close();
    }

    public function changedivisionlist() {
    	$app = JFactory::getApplication();
        $breed = $app->input->getInt('breed');
        $category = $app->input->getVar('category');
        $division_id = $app->input->getInt('division');
        $db = JFactory::getDbo();

        $query = "SELECT DISTINCT `d`.`division_id` as value, `d`.`division_name` AS `text`
                FROM `#__toes_breed_category_division_color` AS `bcdc`
                LEFT JOIN `#__toes_division` AS `d` ON `d`.`division_id` = `bcdc`.`division`
                WHERE (`bcdc`.`organization` = 1)";

        if ($breed)
            $query .= " AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";
        if ($category)
            $query .= " AND (`bcdc`.`category` = " . $db->quote($category) . " ) ";

        //$query.= " ORDER BY d.`division_name` ASC";
        $query.= " ORDER BY d.`division_id` ASC";

        $db->setQuery($query);
        $divisions = $db->loadObjectList();

        $divisionlist = array();
        $divisionlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_DIVISION'));
        $divisionlist = array_merge($divisionlist, $divisions);

        $exhibitor = $app->input->getInt('exhibitor');
        if($exhibitor)
            $readonly = 'disabled';
        else
            $readonly = '';
        
        echo JHTML::_('select.genericlist', $divisionlist, 'copy_cat_division', 'class="inputbox required" aria-required="true" required="required" aria-invalid="false" onchange="changeColor();" '.$readonly, 'value', 'text', $division_id);
        $app->close();
    }

    public function changecolorlist() {
        $app = JFactory::getApplication();
        $breed = $app->input->getInt('breed');
        $category = $app->input->getVar('category');
        $division = $app->input->getVar('division');
        $color_id = $app->input->getInt('color');
        $db = JFactory::getDbo();

        $query = "SELECT DISTINCT `c`.`color_id` as value, `c`.`color_name` AS `text`
                FROM `#__toes_breed_category_division_color` AS `bcdc`
                LEFT JOIN `#__toes_color` AS `c` ON `c`.`color_id` = `bcdc`.`color`
                WHERE (`bcdc`.`organization` = 1) ";

        if ($breed)
            $query .= " AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";
        if ($category)
            $query .= " AND (`bcdc`.`category` = " . $db->quote($category) . " ) ";
        if ($division)
            $query .= " AND (`bcdc`.`division` = " . $db->quote($division) . " ) ";

        //$query.= " ORDER BY c.`color_name` ASC";
        $query.= " ORDER BY c.`color_id` ASC";

        $db->setQuery($query);
        $colors = $db->loadObjectList();

        $colorlist = array();
        $colorlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_COLOR'));
        $colorlist = array_merge($colorlist, $colors);

        $exhibitor = $app->input->getInt('exhibitor');
        if($exhibitor)
            $readonly = 'disabled';
        else
            $readonly = '';
        
        echo JHTML::_('select.genericlist', $colorlist, 'copy_cat_color', 'class="inputbox required" aria-required="true" required="required" aria-invalid="false" '.$readonly, 'value', 'text', $color_id);
        $app->close();
    }

    public function changehairlength() {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $breed = $app->input->getVar('breed');
        $hairlength = $app->input->getInt('hairlength');

        $query = $db->getQuery(true);
        $query->select('b.breed_hair_length');
        $query->from('#__toes_breed AS b');
        $query->where("b.breed_id = " . $db->quote($breed));
        
        $db->setQuery($query);
        $hairlength_id = $db->loadResult();

        $query = $db->getQuery(true);
        $query->select('h.cat_hair_length_id AS value, concat(h.cat_hair_length,\' (\',h.cat_hair_length_abbreviation,\')\') AS text');
        $query->from('#__toes_cat_hair_length AS h');
        $query->order('h.cat_hair_length_id ASC');

        if($hairlength_id){
            $query->where("h.cat_hair_length_id = " . $hairlength_id);
        }

        $db->setQuery($query);
        $options = $db->loadObjectList();

        if (!$hairlength_id)
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_HAIRLENGTH')));
	
        if(!$hairlength_id)
            $hairlength_id = $hairlength;
		
        echo JHTML::_('select.genericlist', $options, 'copy_cat_hair_length', 'class="inputbox required" aria-required="true" required="required" aria-invalid="false"', 'value', 'text', $hairlength_id);
        $app->close();
    }    

    public function getBreeds() {
        $app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);
			
            $query = $db->getQuery(true);
            $query->select('breed_id AS `key`, concat(breed_name,\' (\',breed_abbreviation,\')\') AS value');
            $query->from('#__toes_breed');
            $query->where('breed_organization = 1');
            $query->where('breed_name LIKE '.$like);
            $query->order('breed_name ASC');       
                    
            $db->setQuery($query);
            //echo 
            $breeds = $db->loadObjectList();
            if (count($breeds)) {
                echo json_encode($breeds);
            }
        }
        $app->close();
    }

    public function getColors() {
		$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);
			
            $category = $app->input->getVar('category');
            $division = $app->input->getVar('division');

            $query = "SELECT DISTINCT `c`.`color_id` as `key`, `c`.`color_name` AS `value`
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    LEFT JOIN `#__toes_color` AS `c` ON `c`.`color_id` = `bcdc`.`color`
                    WHERE (`bcdc`.`organization` = 1) AND (`c`.`color_name` LIKE ".$like.") ";

            if ($category)
                $query .= " AND (`bcdc`.`category` = " . $db->quote($category) . " ) ";
            if ($division)
                $query .= " AND (`bcdc`.`division` = " . $db->quote($division) . " ) ";

            //$query.= " ORDER BY c.`color_name` ASC";
            $query.= " ORDER BY c.`color_id` ASC";

            $db->setQuery($query);
            $colors = $db->loadObjectList();
            if (count($colors)) {
                echo json_encode($colors);
            }
        }
        $app->close();
    }    
    
    public function searchColors()
    {
        $app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT DISTINCT `c`.`color_id` AS `key`, `c`.`color_name` AS `value`
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    LEFT JOIN `#__toes_color` AS `c` ON `c`.`color_id` = `bcdc`.`color`
                    WHERE (`bcdc`.`organization` = 1) AND (`c`.`color_name` LIKE ".$like.") ";

            //$query.= " ORDER BY c.`color_name` ASC";
            $query.= " ORDER BY c.`color_id` ASC";

            $db->setQuery($query);
            $colors = $db->loadObjectList();
            if (count($colors)) {
                echo json_encode($colors);
            }
        }
        $app->close();
    }
    
    public function getclassName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select('show_class');
        $query->from('#__toes_show_class');
        $query->where('show_class_id='.$id);
        $query->order('show_class_id ASC');

        $db->setQuery($query);
        echo 'class;'.$db->loadResult().';'.$id;
        $app->close();
    }

    public function gethairlengthName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select('concat(cat_hair_length,\' (\',cat_hair_length_abbreviation,\')\') AS text');
        $query->from('#__toes_cat_hair_length');
        $query->where('cat_hair_length_id='.$id);
        $query->order('cat_hair_length_id ASC');

        $db->setQuery($query);
        echo 'hairlength;'.$db->loadResult().';'.$id;
        $app->close();
    }

    public function getcategoryName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select('category AS text');
        $query->from('#__toes_category');
        $query->where('category_organization = 1');
        $query->where('category_id='.$id);
        $query->order('category_id ASC');

        $db->setQuery($query);
        echo 'category;'.$db->loadResult().';'.$id;
        $app->close();
    }

    public function getdivisionName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select('division_name AS text');
        $query->from('#__toes_division');
        $query->where('division_organization = 1');
        $query->where('division_id='.$id);
        $query->order('division_id ASC');

        $db->setQuery($query);
        echo 'division;'.$db->loadResult().';'.$id;
        $app->close();
    }

    public function getcolorName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select('color_name AS text');
        $query->from('#__toes_color');
        $query->where('color_organization = 1');
        $query->where('color_id='.$id);
        $query->order('color_id ASC');

        $db->setQuery($query);
        echo 'color;'.$db->loadResult().';'.$id;
        $app->close();
    }

    public function getbreedName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select('concat(breed_name,\' (\',breed_abbreviation,\')\') AS text');
        $query->from('#__toes_breed');
        $query->where('breed_organization = 1');
        $query->where('breed_id='.$id);
        $query->order('breed_name ASC');

        $db->setQuery($query);
        echo 'breed;'.$db->loadResult().';'.$id;
        $app->close();
    }

    public function getgenderName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select('concat(gender_name,\' (\',gender_short_name,\')\') AS text');
        $query->from('#__toes_cat_gender');
        $query->where('gender_id='.$id);
        $query->order('gender_id ASC');

        $db->setQuery($query);
        echo 'gender;'.$db->loadResult().';'.$id;
        $app->close();
    }

    public function gettitleName() {
        $app = JFactory::getApplication();
            
        $db = JFactory::getDbo();
        $id = $app->input->getInt('id');

        $query = $db->getQuery(true);
        $query->select("concat(cat_title,' (',cat_title_abbreviation,')') AS text");
        $query->from('#__toes_cat_title');
        $query->where('cat_title_organization = 1');
        $query->where('cat_title_id='.$id);
        $query->order('cat_title_id ASC');

        $db->setQuery($query);
        echo 'title;'.$db->loadResult().';'.$id;
        $app->close();
    }
    
    public function saveUser()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel();

        $user_id = $model->saveUser();
        if($user_id)
            echo $user_id;
        else
            echo $model->getError();
            
        $app->close();
    }
	
	public function reject_entry() {
        $app = JFactory::getApplication();
        $model = $this->getModel();

        if($model->reject_entry())
            echo '1';
        else
            echo $model->getError();
            
        $app->close();
	}

	public function delete_entry() {
        $app = JFactory::getApplication();
        $model = $this->getModel();

        if($model->delete_entry())
            echo '1';
        else
            echo $model->getError();

        $app->close();
	}

    public function getUsers() {
        $app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);
			
              $query = "SELECT a.id AS `key`, CONCAT(b.firstname,' ',b.lastname,' - ',a.email) AS value
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
    
    public function approve_documents(){
		$app = JFactory::getApplication();
		$entry_id = $app->input->getInt('entry_id');
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE `#__toes_entry` SET `status` = 2 where `entry_id` =".$entry_id)->execute();
		echo '1';
		$app->close();
		
	}
    public function reject_documents(){
		$app = JFactory::getApplication();
		$entry_id = $app->input->getInt('entry_id');
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE `#__toes_entry` SET `status` = 3 where `entry_id` =".$entry_id)->execute();
		echo '1';
		$app->close();
		
	}
	public function checkregistration_number(){
		$app = JFactory::getApplication();
		$registration_number =  $app->input->getString('registration_number');
		$cat_id =  $app->input->getInt('cat_id');
		$db = JFactory::getDBO();
		
		//cat_registration_number_cat
		
		$db->setQuery("select * from `#__toes_cat_registration_number` where `cat_registration_number` =".$db->Quote($registration_number));
		$record = $db->loadObject();
		//var_dump($record);
		if(($record && $record->cat_registration_number_cat == $cat_id) || !$record ) 
		echo '1';
		else
		echo '-1';	
		$app->close(); 
		
		
		
	}
	public function removedocument(){
		$app = JFactory::getApplication();		 
		$cat_document_id =  $app->input->getInt('cat_document_id');
		$db = JFactory::getDBO();
		$query = "DELETE FROM `#__toes_cat_document` where `cat_document_id` =".$cat_document_id;
		if($db->setQuery($query)->execute())
		echo '1';
		else
		echo '-1';
		$app->close();
		
		
	}
}
