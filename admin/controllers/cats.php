<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Template styles list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class ToesControllercats extends JControllerAdmin
{
	
	public function cancel()
	{
		//parent::cancel();
		$this->setRedirect('index.php?option=com_toes&view=cats');
	}
	
	public function edit()
	{
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
		$id = (int)$array[0];
		$this->setRedirect('index.php?option=com_toes&view=cats&layout=edit&id='.$id);
	}
	
	public function add()
	{
		$this->setRedirect('index.php?option=com_toes&view=cats&layout=edit');
	}
	
	public function save()
	{
		$app = JFactory::getApplication();
		$post = $app->input->post->getArray();
		$model = parent::getModel($name = 'cats', $prefix = 'ToesModel', $config = array());
		$model->save($post);
		$this->setRedirect('index.php?option=com_toes&view=cats','saved successfully');	
	}
	
	public function delete()
	{	
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
						
		$app = JFactory::getApplication();
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="DELETE FROM `#__toes_cat` WHERE cat_id IN (".implode(',',$array).")";
		//echo $query;die;
		$db->setQuery($query);
		$db->query();
			
		$this->setRedirect('index.php?option=com_toes&view=cats','cat deleted successfully');	
		
	}
	
}
