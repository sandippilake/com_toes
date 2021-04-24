<?php
/**
 * @service		Joomla.Administrator
 * @subservice	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Template style model.
 *
 * @service		Joomla.Administrator
 * @subservice	com_templates
 * @since		1.6
 */
 //Service service
 
class toesModeltoe extends JModelAdmin
{
	/**
	 * @var		string	The help screen base URL for the module.
	 * @since	1.6
	 */
	protected $helpURL;

	/**
	 * Item cache.
	 */
	private $_cache = array();

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_toes.toe', 'toe', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$app = JFactory::getApplication();
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_toes.edit.toe.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('toe.id') == 0) {
				$app = JFactory::getApplication();
				//$data->set('catid', $app->input->getInt('catid', $app->getUserState('com_content.services.filter.category_id')));
			}
		}

		return $data;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$pk = (int) $app->input->getInt('id');
		$this->setState('toe.id', $pk);

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_toes');
		$this->setState('params', $params);
	}

	/**
	 * Method to delete rows.
	 *
	 * @param	array	An array of item ids.
	 *
	 * @return	boolean	Returns true on success, false on failure.
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$pks	= (array) $pks;
		$user	= JFactory::getUser();
		$table	= $this->getTable();

		

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk)) {
				// Access checks.
				if (!$user->authorise('core.delete', 'com_toes')) {
					throw new Exception(JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
				}
				if (!$table->delete($pk)) {
					$this->setError($table->getError());
					return false;
				}
			}
			else {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to duplicate styles.
	 *
	 * @param	array	An array of primary key IDs.
	 *
	 * @return	boolean	True if successful.
	 * @throws	Exception
	 */
	public function duplicate(&$pks)
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$db		= $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.create', 'com_toes')) {
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true)) {
				// Reset the id to create a new record.
				$table->id = 0;

				// Alter the title.
				$m = null;
				//$table->service_name = $this->generateNewTitle(null, null, $table->service_name);

				if (!$table->check() || !$table->store()) {
					throw new Exception($table->getError());
				}
			}
			else {
				throw new Exception($table->getError());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the title.
	 *
	 * @param   integer  $category_id  The id of the category.
	 * @param   string   $alias        The alias.
	 * @param   string   $title        The title.
	 *
	 * @return	string  New title.
	 * @since	1.7.1
	 */
	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title
		$table = $this->getTable();
		while ($table->load(array('title'=>$title)))
		{
			$title = JString::increment($title);
		}

		return $title;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('toe.id');

		//if (!isset($this->_cache[$pk])) {
			$false	= false;

			// Get a row instance.
			$table = $this->getTable();
			
			//var_dump($table);die;

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError()) {
				$this->setError($table->getError());
				return $false;
			}

			// Convert to the JObject before adding other data.
			$properties = $table->getProperties(1);
			$this->_cache[$pk] = JArrayHelper::toObject($properties, 'JObject');

			return $this->_cache[$pk];
		
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'toe', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 */
	public function save($data)
	{
		// Detect disabled extension
		/*$extension = JTable::getInstance('Extension');
		if ($extension->load(array('enabled' => 0, 'type' => 'template', 'element' => $data['template'], 'client_id' => $data['client_id']))) {
			$this->setError(JText::_('COM_TEMPLATES_ERROR_SAVE_DISABLED_TEMPLATE'));
			return false;
		}
		*/
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState('toe.id');
		$isNew		= true;

		// Include the extension plugins for the save events.
		//JPluginHelper::importPlugin('extension');

		// Load the row if saving an existing record.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}
		
		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onExtensionBeforeSave event.
		//$result = $dispatcher->trigger('onExtensionBeforeSave', array('com_services.service', &$table, $isNew));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		$user = JFactory::getUser();
		
		// Clean the cache.
		$this->cleanCache();

		// Trigger the onExtensionAfterSave event.
		//$dispatcher->trigger('onExtensionAfterSave', array('com_services.service', &$table, $isNew));

		$this->setState('toe.id', $table->id);

		return true;
	
	}

	
	/**
	 * Custom clean cache method
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
}
