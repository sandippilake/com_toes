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
 * @subpackage	com_templates
 * @since		1.6
 */
class ToesControllerCategories extends JControllerAdmin
{
	
	public function edit()
	{
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
		$id = (int)$array[0];
		$this->setRedirect('index.php?option=com_toes&view=categories&layout=edit&id='.$id);
	}
	
	public function add()
	{
		$this->setRedirect('index.php?option=com_toes&view=categories&layout=edit');
	}
	
	public function save()
	{
		$app = JFactory::getApplication();
		$post = $app->input->post->getArray();
		//$model = parent::getModel($name = 'category', $prefix = 'ToesModel', $config = array());
		$model = parent::getModel($name = 'categories', $prefix = 'ToesModel', $config = array());
		$model->save($post);
		$this->setRedirect('index.php?option=com_toes&view=categories','saved successfully');	
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

		$query="DELETE FROM `#__toes_category` WHERE category_id IN (".implode(',',$array).")";
		//echo $query;die;
		$db->setQuery($query);
		$db->query();
			
		$this->setRedirect('index.php?option=com_toes&view=categories','category deleted successfully');	
		
	}
	
}
