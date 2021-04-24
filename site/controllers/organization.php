<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class ToesControllerOrganization  extends JControllerForm
{
	public function __construct(){
		$this->view_list = 'organizations';
		
		parent::__construct();
	}
	public function save($key = NULL, $urlVar = NULL){
		
		$app	= JFactory::getApplication();
		$data = $app->input->get('jform',array(),'array'); 
		 
		
		if($this->getModel()->save($data)){
		$msg = 'Organization saved successfully';
		}else
		$msg = 'Organization could not be saved';
		
		
		 
		 
		 
		$app->redirect("index.php?option=com_toes&view=organizations",$msg);
		
		 
		
		 
	}
	public function remove(){
	$app	= JFactory::getApplication();
	$db = JFactory::getDBO(); 	
	$user = JFactory::getUser();
	if(!$user->authorise('core.admin') || !$user->authorise('core.admin') )
	$app->redirect('index.php',JText::_('NOT_AUTH'));	
	$id = $app->input->getInt('id');
	$db->setQuery("DELETE from `#__toes_recognized_registration_organization` where 
	`recognized_registration_organization_id` =".$id)->execute();
	$app->redirect('index.php?option=com_toes&view=oranizations',JText::_('COM_TOES'));	
	}
}
