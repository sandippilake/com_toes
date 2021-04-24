<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Template styles list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_toes
 * @since		1.6
 */
class ToesControllerStates extends JControllerAdmin
{
	
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_toes&view=states');
	}
	
	public function edit()
	{
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
		$id = (int)$array[0];
		$this->setRedirect('index.php?option=com_toes&view=states&layout=edit&id='.$id);
	}
	
	public function add()
	{
		$this->setRedirect('index.php?option=com_toes&view=states&layout=edit');
	}
	
	public function save()
	{
		$app = JFactory::getApplication();
		$post = $app->input->post->get('jform', array(), 'array');
		//$model = parent::getModel($name = 'smtpaccount', $prefix = 'ToesModel', $config = array());
		$model = parent::getModel($name = 'states', $prefix = 'ToesModel', $config = array());
		$model->save($post);
		$this->setRedirect('index.php?option=com_toes&view=states','saved successfully');	
		//$app->redirect(JRoute::_('index.php?option=com_toes'));
	}
	
	public function delete()
	{	
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
		//$id = implode('\',\'',$array);
		//var_dump($id);die;
				
		$app = JFactory::getApplication();
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="DELETE FROM `#__toes_states_per_country` WHERE `id` IN (".implode(',',$array).")";
		//echo $query;die;
		$db->setQuery($query);
		$db->query();
			
		$this->setRedirect('index.php?option=com_toes&view=states','SMTP account deleted successfully');	
		
	}
}
