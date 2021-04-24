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
class ToesControllerMailtmpls extends JControllerAdmin
{
	
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_toes&view=mailtmpls');
	}
	
	public function edit()
	{
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
		$id = (int)$array[0];
		$this->setRedirect('index.php?option=com_toes&view=mailtmpls&layout=edit&id='.$id);
	}
	
	public function add()
	{
		$this->setRedirect('index.php?option=com_toes&view=mailtmpls&layout=edit');
	}
	
	public function save()
	{
		$app = JFactory::getApplication();
		$post = $app->input->post->get('jform', array(), 'array');
		//$model = parent::getModel($name = 'mailtmpl', $prefix = 'ToesModel', $config = array());
		$model = parent::getModel($name = 'mailtmpls', $prefix = 'ToesModel', $config = array());
		$model->save($post);
		$this->setRedirect('index.php?option=com_toes&view=mailtmpls','saved successfully');	
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

		$query="DELETE FROM `#__toes_mail_templates` WHERE `tmpl_id` IN (".implode(',',$array).")";
		//echo $query;die;
		$db->setQuery($query);
		$db->query();
			
		$this->setRedirect('index.php?option=com_toes&view=mailtmpls','Mail Template deleted successfully');	
		
	}
	
}
