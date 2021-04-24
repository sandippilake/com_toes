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
class TOESControllerPlaceholder extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * 
     */
    public function getModel($name = 'placeholder', $prefix = 'ToesModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    public function save_placeholder() {
        $app   = JFactory::getApplication();
        $model = parent::getModel('placeholder', 'ToesModel', array('ignore_request' => true));

        if($model->save_placeholder())
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
        
    }
    
    public function cancel_edit_placeholder() {
        $session = JFactory::getSession();
        $session->clear('placeholder');
        return true;
    }
    
    public function step() {
		$app   = JFactory::getApplication();
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $step = $app->input->getVar('step');
        $dir = $app->input->getVar('dir','next');
        
        $clear_session = $app->input->getInt('clear_session',0);
        $session = JFactory::getSession();
		if($clear_session == 1)
			$session->clear('placeholder');
        
        if($session->has('placeholder'))
        {
            $placeholder = $session->get('placeholder');
            $edit = $app->input->getInt('edit');
            $placeholder_id = $app->input->getInt('placeholder_id');
            if($edit == 1 && @$placeholder->placeholder_id != $placeholder_id )
            {
                $placeholders = TOESHelper::getPlaceholderFullDetails($placeholder_id);

                $showdays = array();
				$placeholder_for_AM = array();
				$placeholder_for_PM = array();
                foreach($placeholders as $item)
                {
                    $showdays[] = $item->placeholder_day_showday;

					if($item->placeholder_participates_AM)
						$placeholder_for_AM[]=$item->placeholder_day_showday;

					if($item->placeholder_participates_PM)
						$placeholder_for_PM[]=$item->placeholder_day_showday;
                }

				$query = "SELECT * FROM `#__toes_summary` "
						. " WHERE summary_show = " . $db->quote($item->placeholder_show)
						. " AND summary_user = " . $db->quote($item->placeholder_exhibitor);
		
				$db->setQuery($query);
				$summary = $db->loadObject();
                
                $placeholder = new stdClass();
                $placeholder->placeholder_id = $placeholder_id;
                $placeholder->placeholder_show = $item->placeholder_show;
                $placeholder->placeholder_exhibitor = $item->placeholder_exhibitor;
                $placeholder->showdays = implode(',', $showdays);
				$placeholder->placeholder_for_AM = implode(',', $placeholder_for_AM);
				$placeholder->placeholder_for_PM = implode(',', $placeholder_for_PM);
                $placeholder->single_cages = $summary->summary_single_cages;
                $placeholder->double_cages = $summary->summary_double_cages;
                $placeholder->personal_cage = $summary->summary_personal_cages;
                $placeholder->grooming_space = $summary->summary_grooming_space;
                $placeholder->benching_request = $summary->summary_benching_request;
                $placeholder->summary_entry_clerk_note = $summary->summary_entry_clerk_note;
                $placeholder->summary_entry_clerk_private_note = $summary->summary_entry_clerk_private_note;
                $placeholder->remark = $summary->summary_remarks;
                $placeholder->edit = true;
            }
            else
            {
                $placeholder->edit = false;
            }
        }
        else
        {
            $edit = $app->input->getInt('edit');
            if($edit == 1)
            {
                $placeholder_id = $app->input->getInt('placeholder_id');
                $placeholders = TOESHelper::getPlaceholderFullDetails($placeholder_id);
                
                $showdays = array();
				$placeholder_for_AM = array();
				$placeholder_for_PM = array();
                foreach($placeholders as $item)
                {
                    $showdays[] = $item->placeholder_day_showday;
					
					if($item->placeholder_participates_AM)
						$placeholder_for_AM[]=$item->placeholder_day_showday;
					
					if($item->placeholder_participates_PM)
						$placeholder_for_PM[]=$item->placeholder_day_showday;
                }

				$query = "SELECT * FROM `#__toes_summary` "
						. " WHERE summary_show = " . $db->quote($item->placeholder_show)
						. " AND summary_user = " . $db->quote($item->placeholder_exhibitor);
		
				$db->setQuery($query);
				$summary = $db->loadObject();
                
                $placeholder = new stdClass();
                $placeholder->placeholder_id = $placeholder_id;
                $placeholder->placeholder_show = $item->placeholder_show;
                $placeholder->placeholder_exhibitor = $item->placeholder_exhibitor;
                $placeholder->showdays = implode(',', $showdays);
				$placeholder->placeholder_for_AM = implode(',', $placeholder_for_AM);
				$placeholder->placeholder_for_PM = implode(',', $placeholder_for_PM);
                $placeholder->single_cages = $summary->summary_single_cages;
                $placeholder->double_cages = $summary->summary_double_cages;
                $placeholder->personal_cage = $summary->summary_personal_cages;
                $placeholder->grooming_space = $summary->summary_grooming_space;
                $placeholder->benching_request = $summary->summary_benching_request;
                $placeholder->summary_entry_clerk_note = $summary->summary_entry_clerk_note;
                $placeholder->summary_entry_clerk_private_note = $summary->summary_entry_clerk_private_note;
                $placeholder->remark = $summary->summary_remarks;
                $placeholder->edit = true;
            }
            else
            {
                $placeholder = new stdClass();
                $placeholder->edit = false;
            }
        }

        switch ($step)
        {
            case 'init' :
                $placeholder = new stdClass();
                $session->set('placeholder', $placeholder);
                return;
                break;
            case 'step0' :
                if($dir != 'prev')
                {
                    $show_id = $app->input->getInt('show_id');
                    $type = $app->input->getVar('type');
                    $placeholder->placeholder_show = $show_id;
                    $placeholder->type = $type;
                }
                $layout = 'step0';
                break;
            case 'step1' :
                if($edit == false && $dir != 'prev')
                {
                    $show_id = $app->input->getInt('show_id');
                    $user_id = $app->input->getInt('user_id');
                    $type = $app->input->getVar('type');
                    $placeholder->placeholder_show = $show_id;
                    $placeholder->placeholder_exhibitor = $user_id;
                    $placeholder->type = $type;
                }
                $layout = 'step1';
                break;
            case 'step2' :
                if($edit == false && $dir != 'prev')
                {
	                $showdays = $app->input->getVar('showdays');
					$placeholder_for_AM = $app->input->getVar('placeholder_for_AM');
					$placeholder_for_PM = $app->input->getVar('placeholder_for_PM');
	                $placeholder->showdays = $showdays;
					$placeholder->placeholder_for_AM = $placeholder_for_AM;
					$placeholder->placeholder_for_PM = $placeholder_for_PM;
                }
                $layout = 'step2';
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
                
                $placeholder->single_cages = $single_cages;
                $placeholder->double_cages = $double_cages;
                $placeholder->personal_cage = $personal_cage;
                $placeholder->grooming_space = $grooming_space;
                $placeholder->benching_request = $benching_request;
                $placeholder->summary_entry_clerk_note = $summary_entry_clerk_note;
                $placeholder->summary_entry_clerk_private_note = $summary_entry_clerk_private_note;
                $placeholder->remark = $remark;
                
                $session->set('placeholder',$placeholder);
                $this->save_placeholder();
                return;
					
        }
        
        $session->set('placeholder',$placeholder);
        
        $view = $this->getView('placeholder', 'html');
        $view->setLayout($layout);
        $view->set('placeholder', $placeholder);

        $view->display();
		$app->close();
    }

    public function updateStatus()
    {
        $app    = JFactory::getApplication();
        $status = $app->input->getVar('status');
        $placeholder_day_id = $app->input->getInt('day_id');
        
        $model = $this->getModel();
        
        if($model->updateStatus($placeholder_day_id, $status))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
    }
	
	public function delete_placeholder() {
        $app = JFactory::getApplication();
        $model = $this->getModel();

        if($model->delete_placeholder())
            echo '1';
        else
            echo $model->getError();
            
        $app->close();
	}
}
