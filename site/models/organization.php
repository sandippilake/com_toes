<?php

defined('_JEXEC') or die;

//jimport('joomla.application.component.modelform');

class ToesModelOrganization  extends JModelAdmin
{
	public function getTable($type = 'Organization', $prefix = 'ToesTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	public function getForm($data = array(), $loadData = true){
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_toes.organization', 'organization', array('control' => 'jform', 'load_data' => $loadData));
		$form->addFormPath(JPATH_COMPONENT . '/models/forms');
		$form->addFieldPath(JPATH_COMPONENT . '/models/fields');       
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	protected function loadFormData()	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_toes.edit.organization.data', array());
		if (empty($data)) {
			$app	= JFactory::getApplication();
			$id = $app->input->getInt('id');
			$row = JTable::getInstance('Organization','ToesTable');
			$row->load($id);
			//var_dump($row);
			
			//var_dump($data);
			//$data = $this->getItem();
			$data = $row;
		}
		return $data;
	}
	
	
	 
}
