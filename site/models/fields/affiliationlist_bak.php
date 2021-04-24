<?php 
defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');
class JFormFieldAffiliationlist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'affiliationlist';


	protected function getOptions()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();		 
		//$user = JFactory::getUser();
		 
		 
		$db->setQuery("select DISTINCT(LOWER(recognized_registration_organization_affiliation)) as value, LOWER(recognized_registration_organization_affiliation) as text from
		`#__toes_recognized_registration_organization` where TRIM(recognized_registration_organization_affiliation) <> ''  order by value");
		 
		$options = $db->loadObjectList();

		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_AFFILIATION'))); 
		
		 
		
		return $options;
		

	}
}
?>
