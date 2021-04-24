<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_toes
 * @since		1.6
 */
class ToesController extends JControllerLegacy
{
	/**
	 * @var		string	The extension for which the categories apply.
	 * @since	1.6
	 */

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$app = JFactory::getApplication();
		// Get the document object.
		$document = JFactory::getDocument();
        $document->addStyleSheet(JURI::base() . 'components/com_toes/assets/toes.css');

		// Set the default view name and format from the Request.
		$vName		= $app->input->getCmd('view', 'toes');
		$vFormat	= $document->getType();
		$lName		= $app->input->getCmd('layout', 'default');
		$id			= $app->input->getInt('id');
	
		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat)) 
		{
			$model = $this->getModel($vName, 'toesModel', array('name' => $vName));
			$view->setModel($model, true);
			$view->setLayout($lName);
			$view->assignRef('document', $document);
			$view->display();
		}

		return $this;
	}
}
