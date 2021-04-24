<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Template styles list controller class.
 *
 * @package	Joomla.Administrator
 * @subpackage	com_toes
 * @since	1.6
 */
class ToesControllerClubs extends JControllerAdmin {

    public function cancel() {
        //parent::cancel();
        $this->setRedirect('index.php?option=com_toes&view=clubs');
    }

    public function edit() {
		$app = JFactory::getApplication();
        $array = $app->input->getVar('cid', 0, '', 'array');
        $id = (int) $array[0];
        $this->setRedirect('index.php?option=com_toes&view=clubs&layout=edit&id=' . $id);
    }

    public function add() {
        $this->setRedirect('index.php?option=com_toes&view=clubs&layout=edit');
    }

    public function save() {
		$app = JFactory::getApplication();
        $post = $app->input->post->getArray();
        
        $model = parent::getModel($name = 'clubs', 'ToesModel', array());
        $model->save($post);
        $this->setRedirect('index.php?option=com_toes&view=clubs', JText::_('COM_TOES_SAVE_SUCCESSFUL'));
    }

    public function delete() {
 		$app = JFactory::getApplication();
        $array = $app->input->getVar('cid', 0, '', 'array');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query = "DELETE FROM `#__toes_club` WHERE club_id IN (" . implode(',', $array) . ")";
        $db->setQuery($query);
        $db->query();

        $this->setRedirect('index.php?option=com_toes&view=clubs', JText::_('COM_TOES_DELETE_SUCESSFUL'));
    }

}
