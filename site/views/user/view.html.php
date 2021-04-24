<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * View to edit a template style.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESViewUser extends JViewLegacy {

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        
        $official = $app->input->getVar('official');
              
        switch ($official)
        {
            case 'organization':
                if($user->authorise('toes.manage_org_officials','com_toes')) { break; }
            case 'club':
                if($user->authorise('toes.manage_club_officials','com_toes')) { break; }
            case 'show':
                if($user->authorise('toes.manage_show_officials','com_toes')) { break; }
            default:
                JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
                $app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
        }
        
        $layout = $app->input->getVar('layout');
        if ($layout == 'organization') {
			$this->organization_detail = $this->get('organization_detail');
		} else if ($layout == 'club') {
			$this->club_detail = $this->get('club_detail');
		} else if ($layout == 'show') {
			$this->show_detail = $this->get('show_detail');
		}
		
		if ($official == 'organization') {
	        $this->regions = $this->get('Regions');
		} else {
	        $this->clubs = $this->get('Clubs');
        }

        $this->official_rolls = $this->get('Official_rolls');
        $this->officials = $this->get('Officials');

        parent::display($tpl);
    }

}
