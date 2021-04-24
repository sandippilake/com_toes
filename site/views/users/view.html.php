<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the Category package.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESViewUsers extends JViewLegacy {

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        if(!$user->authorise('toes.user_mgt','com_toes'))
        {
            JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
            $app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
        }
        
	  	$layout = $app->input->get('layout');
		if($layout == 'showofficials') 
		{
			if ($user->authorise('toes.manage_show_officials','com_toes')) {
				$this->ShowOfficial = $this->get('ShowOfficials');
				$this->pagination = $this->get('Pagination');
				$this->showrolllist = $this->get('showrolllist');
				$this->show_statuslist = $this->get('show_statuslist');
			} else {
				JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
				$app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
			}
		} 
		elseif($layout == 'clubofficials') 
		{
			if ($user->authorise('toes.manage_club_officials','com_toes')) {
				$this->ClubOfficial = $this->get('ClubOfficials');
			} elseif ($user->authorise('toes.manage_show_officials','com_toes')) {
				$app->redirect(JRoute::_('index.php?option=com_toes&view=users&layout=showofficials'));
			} else {
				JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
				$app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
			}
		} 
		else 
		{
			if($user->authorise('toes.manage_org_officials','com_toes')) {
	        	$this->OrgOfficial = $this->get('OrgOfficial');
			} elseif ($user->authorise('toes.manage_club_officials','com_toes')) {
				$app->redirect(JRoute::_('index.php?option=com_toes&view=users&layout=clubofficials'));
			} elseif ($user->authorise('toes.manage_show_officials','com_toes')) {
				$app->redirect(JRoute::_('index.php?option=com_toes&view=users&layout=showofficials'));
			} else {
				JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
				$app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
			}
		}
		
		parent::display($tpl);
	  
		if($app->input->get('tmpl') == 'component') {
			$app->close();
		}
    }
}

