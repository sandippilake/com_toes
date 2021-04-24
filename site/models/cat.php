<?php

/**
 * @service	Joomla
 * @subservice	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 *
 * @service	Joomla
 * @subservice	com_toes
 */
//Service service

class TOESModelCat extends JModelLegacy {

    /**
     * Item cache.
     */
    private $_cache = array();

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState() {
		$app = JFactory::getApplication();
        // Load the User state.
        $pk = (int) $app->input->getInt('id');
        $this->setState('cat.id', $pk);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_toes');
        $this->setState('params', $params);
        
        parent::populateState();
    }

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function getItem($pk = null) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('cat.id');

		if(!isset($this->_cache[$pk])) {
	        $query->select('*');
	        $query->from('#__toes_cat as c');
	        $query->join('LEFT', '#__toes_cat_registration_number AS cr ON c.cat_id=cr.cat_registration_number_cat');
	        $query->where('c.cat_id=' . (int) $pk);
	
	        $db->setQuery($query);
	        $return = $db->loadObject();
	
	        $this->_cache[$pk] = $return;
        }

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
    public function getTable($type = 'Cat', $prefix = 'ToesTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getCompetitiveregion() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('competitive_region_id AS value, concat(competitive_region_name,\' (\',competitive_region_abbreviation,\')\') AS text, competitive_region_abbreviation');
        $query->from('#__toes_competitive_region');
        $query->order('competitive_region_id ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_COMPETITIVE_REGION')));
        return $options;
    }

    public function getHairlength() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('cat_hair_length_id AS value, concat(cat_hair_length,\' (\',cat_hair_length_abbreviation,\')\') AS text');
        $query->from('#__toes_cat_hair_length');
        $query->order('cat_hair_length_id ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_HAIRLENGTH')));
        return $options;
    }

    public function getCategory() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('category_id AS value, category AS text');
        $query->from('#__toes_category');
        $query->where('category_organization = 1');
        //$query->order('category ASC');
        $query->order('category_id ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_CATEGORY')));
        return $options;
    }

    public function getDivision() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('division_id AS value, division_name AS text');
        $query->from('#__toes_division');
        $query->where('division_organization = 1');
        //$query->order('division_name ASC');
        $query->order('division_id ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_DIVISION')));
        return $options;
    }

    public function getColor() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('color_id AS value, color_name AS text');
        $query->from('#__toes_color');
        $query->where('color_organization = 1');
        //$query->order('color_name ASC');
        $query->order('color_id ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_COLOR')));
        return $options;
    }

    public function getbreed() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);


        $query->select('breed_id AS value, concat(breed_name,\' (\',breed_abbreviation,\')\') AS text');
        $query->from('#__toes_breed');
        $query->where('breed_organization = 1');
        $query->order('breed_name ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_BREED')));
        return $options;
    }
	 
    public function getdocument_types_list() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);


        $query->select('allowed_registration_document_id AS value, allowed_registration_document_type AS text');
        $query->from('#__toes_allowed_registration_document_type');
        $query->order('allowed_registration_document_id ASC');

        // Get the options.
        $db->setQuery($query);

        return $db->loadObjectList();
        
    }
     

    public function getorganization() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);


        $query->select('recognized_registration_organization_id AS value, concat(recognized_registration_organization_name,\' (\',recognized_registration_organization_abbreviation,\')\') AS text');
        $query->from('#__toes_recognized_registration_organization');
        $query->order('recognized_registration_organization_name ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_REGISTRATION_ORGANISATION')));
        return $options;
    }

    public function getgender() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('gender_id AS value, concat(gender_name,\' (\',gender_short_name,\')\') AS text');
        $query->from('#__toes_cat_gender');
        $query->order('gender_id ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_GENDER')));
        return $options;
    }

    public function gettitle() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('cat_title_id AS value, concat(cat_title,\' (\',cat_title_abbreviation,\')\') AS text, cat_title_abbreviation');
        $query->from('#__toes_cat_title');
        $query->where('cat_title_organization = 1');
        $query->order('cat_title_id ASC');

        // Get the options.
        $db->setQuery($query);

        $titles = $db->loadObjectList();

        $cat = $this->getItem();
        
        if($cat)
        {
            $isHHP = false;
            $query = "SELECT `breed_id` FROM `#__toes_breed` WHERE breed_group = 'Household Pet'";
            $db->setQuery($query);
            $breed_id = $db->loadResult();

            if ($cat->cat_breed == $breed_id)
                $isHHP = true;
            else
                $isHHP = false;
        
            $HHP_titles = array('', 'Master', 'GRM', 'DGM', 'TGM', 'QGM', 'SGM');
            $notHHP_titles = array('', 'CH', 'GRC', 'DGC', 'TGC', 'QGC', 'SGC');

            $options = array();
            foreach($titles as $item)
            {
                if($isHHP)
                {
                    if(in_array($item->cat_title_abbreviation, $HHP_titles))
                    {
                        $options[] = $item;
                    }
                }
                else if($cat->cat_gender == 1 || $cat->cat_gender == 2)
                {
                    if(in_array($item->cat_title_abbreviation, $notHHP_titles))
                    {
                        $options[] = $item;
                    }
                }
                else
                    $options[] = $item;
            }
        }
        else {
            $options = $titles;
        }
        
        //array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_TITLE')));
        return $options;
    }

    public function getprefix() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('cat_prefix_id AS value, concat(cat_prefix,\' (\',cat_prefix_abbreviation,\')\') AS text');
        $query->from('#__toes_cat_prefix');
        $query->where('cat_prefix_organization = 1');
        $query->order('cat_prefix_id ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        //array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_PREFIX')));
        return $options;
    }

    public function getsuffix() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('cat_suffix_id AS value, concat(cat_suffix,\' (\',cat_suffix_abbreviation,\')\') AS text, cat_suffix_abbreviation');
        $query->from('#__toes_cat_suffix');
        $query->where('cat_suffix_organization = 1');
        $query->order('cat_suffix_id ASC');

        // Get the options.
        $db->setQuery($query);

        $suffixes = $db->loadObjectList();

        $cat = $this->getItem();
        
        if($cat)
        {
            $options = array();
            foreach($suffixes as $item)
            {
                if( (($cat->cat_gender == 1 || $cat->cat_gender == 3) && $item->cat_suffix_abbreviation != 'OD')
                    || (($cat->cat_gender == 2 || $cat->cat_gender == 4) && $item->cat_suffix_abbreviation != 'OS'))
                $options[] = $item;
            }
        }
        else
        {
            $options = $suffixes;
        }
        
        //array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_SUFFIX')));
        return $options;
    }

    public function checkuseravailable($username) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__users');
        $query->where('username = ' . $db->quote($username));
        $db->setQuery($query);
        $user_details = $db->loadObject();

        if ($user_details->id)
            return 'ok';
        else
            return '1';
    }

    /**
     * Method to save the form data.
     *
     * @param	array	The form data.
     * @return	boolean	True on success.
     */
    public function save($data) {
		var_dump($data);
		echo '<br/>';
		var_dump($_FILES);
		//die;
		
        // Initialise variables;
        $app = JFactory::getApplication();
		$session = JFactory::getSession();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $breed = (int)$data['breed'];
         
        
        if (!isset($data['new_trait']))
            $data['new_trait'] = 0;

         
        $data['owner'] = $data['username_owner'];
        $data['breeder'] = $data['username_breeder'];
		
		 
		
        if (isset($data['username_agent']) && count($data['username_agent'])) {
            for ($ua = 0; $ua < count($data['username_agent']); $ua++)
                $data['other_relation'][] = 'Agent';
        } else {
            $data['username_agent'][0] = '';
            $data['other_relation'][] = 'Agent';
        }

        if (isset($data['username_lessee']) && count($data['username_lessee'])) {
            for ($ua = 0; $ua < count($data['username_lessee']); $ua++)
                $data['other_relation'][] = 'Lessee';
        } else {
            $data['username_lessee'][0] = '';
            $data['other_relation'][] = 'Lessee';
        }

        $data['other'] = array_merge($data['username_agent'], $data['username_lessee']);

        if ($data['id']) {
            //where cat_id is available in case edited form save
            $query = "SELECT cat_registration_number_cat FROM #__toes_cat_registration_number WHERE cat_registration_number = " . $db->quote($data['sire_r']);
            $db->setQuery($query);
            $sire_cat_id = $db->loadResult();
               
            $query = "SELECT cat_cat_connection_type_id 
                    FROM #__toes_cat_cat_connection_type 
                    WHERE cat_to_cat_connection_type = 'Sire' ";
            $db->setQuery($query);
            $sire_field_id = $db->loadResult();

            $query = "DELETE FROM `#__toes_cat_relates_to_cat` 
                    WHERE `of_cat_2` = '" . $data['id'] . "'
                    AND `cat_cat_connection_type` = '" . $sire_field_id . "'";
            $db->setQuery($query);
            $db->query();

            if ($sire_cat_id) {

                $query = "INSERT INTO `#__toes_cat_relates_to_cat` (`cat_1_is`, `of_cat_2`, `cat_cat_connection_type`) 
				VALUES ('" . $sire_cat_id . "', '" . $data['id'] . "', '" . $sire_field_id . "')";
                $db->setQuery($query);
                $db->query();
            }

            $query = "SELECT cat_registration_number_cat FROM #__toes_cat_registration_number WHERE cat_registration_number = " . $db->quote($data['dam_r']);
            $db->setQuery($query);
            $dam_cat_id = $db->loadResult();

            $query = "SELECT cat_cat_connection_type_id 
                    FROM #__toes_cat_cat_connection_type 
                    WHERE cat_to_cat_connection_type = 'Dam' ";
            $db->setQuery($query);
            $dam_field_id = $db->loadResult();

            $query = "DELETE FROM `#__toes_cat_relates_to_cat` 
                    WHERE `of_cat_2` = '" . $data['id'] . "'
                    AND `cat_cat_connection_type` = '" . $dam_field_id . "'";
            $db->setQuery($query);
            $db->query();

            if ($dam_cat_id) {
                $query = "INSERT INTO `#__toes_cat_relates_to_cat` (`cat_1_is`, `of_cat_2`, `cat_cat_connection_type`) 
				VALUES ('" . $dam_cat_id . "', '" . $data['id'] . "', '" . $dam_field_id . "')";
                $db->setQuery($query);
                $db->query();
            }

            $query = "UPDATE #__toes_cat SET cat_breed = " . $db->quote($data['breed']) . " 
                    , cat_hair_length= " . $db->quote($data['cat_hair_length']) . "
                    , cat_category= " . $db->quote($data['category']) . "
                    , cat_division= " . $db->quote($data['division']) . "
                    , cat_color= " . $db->quote($data['color']) . "
                    , cat_date_of_birth= " . $db->quote($data['date_of_birth']) . "
                    , cat_gender= " . $db->quote($data['gender']) . "
                    , cat_prefix= " . $db->quote($data['prefix']) . "
                    , cat_title= " . $db->quote($data['title']) . "
                    , cat_name= " . $db->quote($data['name']) . "
                    , cat_suffix= " . $db->quote($data['suffix']) . "
                    , cat_competitive_region= " . $db->quote($data['cat_competitive_region']) . "
                    , cat_sire= " . $db->quote($data['sire']) . "
                    , cat_dam= " . $db->quote($data['dam']) . "
                    , cat_new_trait= " . $db->quote($data['new_trait']) . "
                    , cat_owner= " . $db->quote($data['cat_owner']) . "
                    , cat_breeder= " . $db->quote($data['cat_breeder']) . "
                    , cat_lessee= " . $db->quote($data['cat_lessee']) . "
                    , cat_id_chip_number= " . $db->quote($data['cat_id_chip_number']) . "
                    WHERE cat_id= " . $db->quote($data['id']) . "  ";

            $db->setQuery($query);
            $db->query();
            
            $cat_id = $data['id'];
			
			
			// sandy hack comment following condition on 17 oct 2019 ref #BRD24CNDREM
			//if($breed != 24){ 
			if(isset($data['has_tica_reg_number'])) {
            if(strtolower($data['registration_number']) == "pending" || !$data['has_tica_reg_number'] )
            {
                $query = "DELETE FROM `#__toes_cat_registration_number` 
                        WHERE `cat_registration_number_cat` = " . $db->quote($data['id']);
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $query = "DELETE FROM `#__toes_cat_registration_number` WHERE `cat_registration_number_cat` = " . $db->quote($data['id']);
                $db->setQuery($query);
                $db->query();
                
               echo  $query = "INSERT INTO `#__toes_cat_registration_number` 
                        SET `cat_registration_number` = " . $db->quote($data['registration_number']) . " ,
                        `cat_registration_number_organization` = 1 , 
                        `cat_registration_number_cat` = " . $db->quote($data['id']) . "  ";
                $db->setQuery($query);
                $db->query();
            }
            }
            //}

            $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE cat_user_connection_type  = 'Owner'";
            $db->setQuery($query);
            $owner_field_id = $db->loadResult();

            $query = "DELETE FROM `#__toes_cat_relates_to_user` WHERE `of_cat` = " . $db->quote($data['id']) . " AND `cat_user_connection_type` = " . $db->quote($owner_field_id) . "  ";
            $db->setQuery($query);
            $db->query();

            $owner = $data['owner'];
            if (count($owner)) {
                $query = "SELECT id FROM #__users WHERE username IN ('" . implode('\',\'', $owner) . "')";
                $db->setQuery($query);
                $owner_ids = $db->loadObjectList();

                foreach ($owner_ids as $owner_id) {
                    $query = "INSERT INTO `#__toes_cat_relates_to_user` (`of_cat`, `person_is`, `cat_user_connection_type`) VALUES ('" . $data['id'] . "', '" . $owner_id->id . "', '" . $owner_field_id . "')";
                    $db->setQuery($query);
                    $db->query();
                }
            }

            $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE cat_user_connection_type  = 'Breeder'";
            $db->setQuery($query);
            $breeder_field_id = $db->loadResult();

            $query = "DELETE FROM `#__toes_cat_relates_to_user` WHERE `of_cat` = " . $db->quote($data['id']) . " AND `cat_user_connection_type` = " . $db->quote($breeder_field_id) . "  ";
            $db->setQuery($query);
            $db->query();

            $breeder = $data['breeder'];
            if (count($breeder)) {
                $query = "SELECT id FROM #__users WHERE username IN ('" . implode('\',\'', $breeder) . "')";
                $db->setQuery($query);
                $breeder_ids = $db->loadObjectList();

                foreach ($breeder_ids as $breeder_id) {
                    $query = "INSERT INTO `#__toes_cat_relates_to_user` (`of_cat`, `person_is`, `cat_user_connection_type`) VALUES ('" . $data['id'] . "', '" . $breeder_id->id . "', '" . $breeder_field_id . "')";
                    $db->setQuery($query);
                    $db->query();
                }
            }

            $other = $data['other'];
            $other_relation = $data['other_relation'];
            for ($o = 0; $o < count($other); $o++) {
                if ($other_relation[$o]) {
                    $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE LOWER(cat_user_connection_type)  = LOWER(" . $db->quote($other_relation[$o]) . ")";
                    $db->setQuery($query);
                    $other_field_id = $db->loadResult();

                    if (!$other_field_id) {
                        $query = "INSERT INTO `#__toes_cat_user_connection_type` (`cat_user_connection_type`) VALUES (" . $db->quote($other_relation[$o]) . ")";
                        $db->setQuery($query);
                        $db->query();

                        $other_field_id = $db->insertid();
                    }

                    $query = "DELETE FROM `#__toes_cat_relates_to_user` WHERE `of_cat` = " . $db->quote($data['id']) . " AND `cat_user_connection_type` = " . $db->quote($other_field_id) . "  ";
                    $db->setQuery($query);
                    $db->query();

                    if ($other[$o]) {
                        $query = "SELECT id FROM #__users WHERE username = " . $db->quote($other[$o]) . " ";
                        $db->setQuery($query);
                        $other_user_id = $db->loadResult();

                        if ($other_user_id) {
                            $query = "INSERT INTO `#__toes_cat_relates_to_user` (`of_cat`, `person_is`, `cat_user_connection_type`) VALUES ('" . $data['id'] . "', '" . $other_user_id . "', '" . $other_field_id . "')";
                            $db->setQuery($query);
                            $db->query();
                        }
                    }
                }
            }
        } else {
            //in case new cat is saved

            $query = "INSERT INTO #__toes_cat ( cat_breed, 
                    cat_hair_length, 
                    cat_category, 
                    cat_division, 
                    cat_color, 
                    cat_date_of_birth, 
                    cat_gender, 
                    cat_prefix, 
                    cat_title, 
                    cat_name, 
                    cat_suffix, 
                    cat_competitive_region, 
                    cat_sire, 
                    cat_dam, 
                    cat_new_trait,
                    cat_owner, 
                    cat_breeder, 
                    cat_lessee, 
                    cat_id_chip_number
                    ) 
                    VALUES (" . $db->quote($data['breed']) . ", 
                    " . $db->quote($data['cat_hair_length']) . ", 
                    " . $db->quote($data['category']) . ", 
                    " . $db->quote($data['division']) . ", 
                    " . $db->quote($data['color']) . ", 
                    " . $db->quote($data['date_of_birth']) . ", 
                    " . $db->quote($data['gender']) . ", 
                    " . $db->quote($data['prefix']) . ", 
                    " . $db->quote($data['title']) . ", 
                    " . $db->quote($data['name']) . ", 
                    " . $db->quote($data['suffix']) . ", 
                    " . $db->quote($data['cat_competitive_region']) . ", 
                    " . $db->quote($data['sire']) . ", 
                    " . $db->quote($data['dam']) . ", 
                    " . $data['new_trait'] . ", 
                    " . $db->quote($data['cat_owner']) . ", 
                    " . $db->quote($data['cat_breeder']) . ", 
                    " . $db->quote($data['cat_lessee']) . ", 
                    " . $db->quote($data['cat_id_chip_number']) . ") ";
            $db->setQuery($query);
            if(!$db->query())
            {
                $this->setError($db->getErrorMsg());
                return false;
            }

            $cat_id = $db->insertid();
            // 24 is HHP
            
            // sandy commented this condition now
           // if($breed != 24){
			
			if(isset($data['has_tica_reg_number'])) {
					
            if($data['registration_number'] != 'PENDING' && $data['registration_number'])
            {
                $query = "INSERT INTO #__toes_cat_registration_number 
                        (cat_registration_number, cat_registration_number_organization, cat_registration_number_cat ) 
                        VALUES(" . $db->quote($data['registration_number']) . ",1," . $db->quote($cat_id) . ")";
                $db->setQuery($query);
                $db->query();
            }
            
            }
            
            //}
			
            //sire & dam
            $query = "SELECT cat_registration_number_cat 
                    FROM #__toes_cat_registration_number 
                    WHERE cat_registration_number = " . $db->quote($data['sire_r']);
            $db->setQuery($query);
            $sire_cat_id = $db->loadResult();

            if ($sire_cat_id) {
                $query = "SELECT cat_cat_connection_type_id 
                        FROM #__toes_cat_cat_connection_type 
                        WHERE cat_to_cat_connection_type = 'Sire' ";
                $db->setQuery($query);
                $sire_field_id = $db->loadResult();

                $query = "INSERT INTO `#__toes_cat_relates_to_cat` 
                        (`cat_1_is`, `of_cat_2`, `cat_cat_connection_type`) 
                        VALUES ('" . $sire_cat_id . "', '" . $cat_id . "', '" . $sire_field_id . "')";
                $db->setQuery($query);
                $db->query();
            }

            $query = "SELECT cat_registration_number_cat 
                    FROM #__toes_cat_registration_number 
                    WHERE cat_registration_number = " . $db->quote($data['dam_r']);
            $db->setQuery($query);
            $dam_cat_id = $db->loadResult();

            if ($dam_cat_id) {
                $query = "SELECT cat_cat_connection_type_id 
                        FROM #__toes_cat_cat_connection_type 
                        WHERE cat_to_cat_connection_type = 'Dam' ";
                $db->setQuery($query);
                $dam_field_id = $db->loadResult();

                $query = "INSERT INTO `#__toes_cat_relates_to_cat` 
                        (`cat_1_is`, `of_cat_2`, `cat_cat_connection_type`) 
                        VALUES ('" . $dam_cat_id . "', '" . $cat_id . "', '" . $dam_field_id . "')";
                $db->setQuery($query);
                $db->query();
            }

            $query = "UPDATE #__toes_cat 
                    SET cat_sire= " . $db->quote($data['sire']) . "
                    , cat_dam= " . $db->quote($data['dam']) . "
                    WHERE cat_id= " . $db->quote($cat_id) . "  ";
            $db->setQuery($query);
            $db->query();

            //sire & dam complete
            $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE cat_user_connection_type  = 'Owner'";
            $db->setQuery($query);
            $owner_field_id = $db->loadResult();

            $owner = $data['owner'];
            if (count($owner)) {
                $query = "SELECT id FROM #__users WHERE username IN ('" . implode('\',\'', $owner) . "')";
                $db->setQuery($query);
                $owner_ids = $db->loadObjectList();

                foreach ($owner_ids as $owner_id) {
                    $query = "INSERT INTO `#__toes_cat_relates_to_user` (`of_cat`, `person_is`, `cat_user_connection_type`) VALUES ('" . $cat_id . "', '" . $owner_id->id . "', '" . $owner_field_id . "')";
                    $db->setQuery($query);
                    if(!$db->query())
                    {
                        $this->setError($db->getErrorMsg());
                        return false;
                    }
                }
            }

            $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE cat_user_connection_type  = 'Breeder'";
            $db->setQuery($query);
            $breeder_field_id = $db->loadResult();

            $breeder = $data['breeder'];
            if (count($breeder)) {
                $query = "SELECT id FROM #__users WHERE username IN ('" . implode('\',\'', $breeder) . "')";
                $db->setQuery($query);
                $breeder_ids = $db->loadObjectList();

                foreach ($breeder_ids as $breeder_id) {
                    $query = "INSERT INTO `#__toes_cat_relates_to_user` (`of_cat`, `person_is`, `cat_user_connection_type`) VALUES ('" . $cat_id . "', '" . $breeder_id->id . "', '" . $breeder_field_id . "')";
                    $db->setQuery($query);
                    if(!$db->query())
                    {
                        $this->setError($db->getErrorMsg());
                        return false;
                    }
                }
            }

            $other = $data['other'];
            $other_relation = $data['other_relation'];
            for ($o = 0; $o < count($other); $o++) {
                if ($other[$o] && $other_relation[$o]) {
                    $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE LOWER(cat_user_connection_type)  = LOWER(" . $db->quote($other_relation[$o]) . ")";
                    $db->setQuery($query);
                    $other_field_id = $db->loadResult();

                    if (!$other_field_id) {
                        $query = "INSERT INTO `#__toes_cat_user_connection_type` (`cat_user_connection_type`) VALUES (" . $db->quote($other_relation[$o]) . ")";
                        $db->setQuery($query);
                        if(!$db->query())
                        {
                            $this->setError($db->getErrorMsg());
                            return false;
                        }

                        $other_field_id = $db->insertid();
                    }

                    $query = "SELECT id FROM #__users WHERE username = " . $db->quote($other[$o]) . " ";
                    $db->setQuery($query);
                    $other_user_id = $db->loadResult();

                    if ($other_user_id) {
                        $query = "INSERT INTO `#__toes_cat_relates_to_user` (`of_cat`, `person_is`, `cat_user_connection_type`) VALUES ('" . $cat_id . "', '" . $other_user_id . "', '" . $other_field_id . "')";
                        $db->setQuery($query);
                        if(!$db->query())
                        {
                            $this->setError($db->getErrorMsg());
                            return false;
                        }
                    }
                }
            }
        }

		 
		$file_names = array();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$files = $app->input->files->get('cat_images');
		
		if (!file_exists(TOES_MEDIA_PATH . DS . 'cats' . DS . $cat_id))
			JFolder::create(TOES_MEDIA_PATH . DS . 'cats' . DS . $cat_id, 0777);
		else
			chmod(TOES_MEDIA_PATH . DS . 'cats' . DS . $cat_id, 0777);

		if($files){
			foreach ($files as $key => $file) {
				if($file['size']) {
					$file_names[] = $file['name'];
	
					$filepath = TOES_MEDIA_PATH.DS.'cats'.DS.$cat_id.DS.$file['name'];
					JFile::copy($file['tmp_name'], $filepath);
				}
			}
			
			//create cat image table
			if($file_names) {
				foreach ($file_names as $file_name) {
					$query = "INSERT INTO `#__toes_cat_images` (`cat_id`, `file_name`) VALUES ('" . $cat_id . "', '" . $file_name . "')";
			        $db->setQuery($query);
			        $db->query();
				}
			}
		}
		 
		
		 
		// documents and organizations
		if($breed != 24){
		if(isset($data['has_tica_reg_number']) && $data['registration_number'] && $data['registration_number'] != 'PENDING' ) {
		$db->setQuery('delete from `#__toes_cat_document` where `cat_document_cat_id` ='.$cat_id)->execute();	
			
		}else{
		
		$folder = floor($cat_id/1000); 
		$document_type_ids = $data['document_type_ids'];
		
		var_dump($document_type_ids);
		if($document_type_ids){
			 
		$document_type_ids_array = explode(',',trim($document_type_ids));
		if(count($document_type_ids_array)){
			
			
			$db->setQuery('delete from `#__toes_cat_document` where `cat_document_cat_id` ='.$cat_id."
			AND `cat_document_registration_document_type_id` NOT IN (".implode(',',$document_type_ids_array).")")->execute();
			/*
			$params = JComponentHelper::getParams('com_toes');
			$double_document_id = $params->get('double_document_id');
			*/ 
			
			foreach($document_type_ids_array as $dtid){
				$db->setQuery("select * from `#__toes_allowed_registration_document_type`
				where `allowed_registration_document_id` =".$dtid);
				$doctyperecord = $db->loadObject();
				
				$organization_id = $data['organization_'.$dtid];				
				$db->setQuery("select * from `#__toes_recognized_registration_organization`
				where `recognized_registration_organization_id` =".$organization_id);
				$organizationrecord = $db->loadObject();
			 
				
				$docfile   = $app->input->files->get('document_'.$dtid);
				 
				
				if (!file_exists(TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder))
					JFolder::create(TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder, 0777);
				else
					chmod(TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder, 0777);
				//var_dump($docfile );
				 
				
				if(count($docfile)){
				 
							 
							echo $name = $docfile['name'] ;
							echo '<br/>';
							$nameparts = explode('.',$name);
							echo $ext = end($nameparts);
							echo '<br/>';
							
							echo $tmppath = TOES_MEDIA_PATH . DS .$cat_id.'_'.$name;
							 
							if($docfile['tmp_name'] && move_uploaded_file($docfile['tmp_name'], $tmppath)){								
							$db->setQuery('delete from `#__toes_cat_document` where `cat_document_cat_id` ='.$cat_id."
							AND `cat_document_registration_document_type_id` =".$dtid)->execute();
		
							$db->setQuery("INSERT INTO `#__toes_cat_document`(`cat_document_registration_document_type_id`,`cat_document_registration_document_organization_id`,`cat_document_cat_id`)
							VALUES(".$dtid.",".$organization_id.",".$cat_id.") ")->execute();	
							$document_id = $db->insertid();
							$filename = $cat_id.'_'.$document_id.'_'.$dtid.'_'.str_replace(array('.',',',' '),array('_','_','_'),$organizationrecord->recognized_registration_organization_abbreviation).'.'.$ext ;
			
							$filepath = TOES_MEDIA_PATH . DS . 'registration_documents' . DS . $folder.DS.$filename;
							
							if(rename($tmppath , $filepath)){
							$filepathdb = 'media'.DS.'com_toes'.DS.'registration_documents'.DS.$folder.DS.$filename;
							$db->setQuery("UPDATE `#__toes_cat_document` SET `cat_document_file_name` =".$db->Quote($filepathdb)."
							where `cat_document_id` =".$document_id)->execute();	
								
								
							}						
								
							}else{
							 
							}
					 
					
					 
				}	
				 
				 
				
			 
				
			}		
		}				
		}else{
		 
		$db->setQuery('delete from `#__toes_cat_document` where `cat_document_cat_id` ='.$cat_id)->execute();
			
		}
		
		}
		
		}
		//	
		
            
        if(!$data['id'] && $data['show_id'])
        {
            $session = JFactory::getSession();
            $session->set('add_show',$data['show_id']); 
            $app->redirect(JRoute::_('index.php?option=com_toes&view=shows'));
        }

		//die;
        // Clean the cache.
        $this->cleanCache();
        return true;
    }
	function getDocuments(){
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id');
		$db = JFactory::getDBO();
		$db->setQuery("select d.*,dt.allowed_registration_document_type,
		dt.allowed_registration_document_name_language_constant,dt.allowed_registration_document_title_language_constant
		from `#__toes_cat_document` as d
		JOIN `#__toes_allowed_registration_document_type` as dt ON d.cat_document_registration_document_type_id = dt.allowed_registration_document_id
		JOIN `#__toes_recognized_registration_organization` as o ON d.cat_document_registration_document_organization_id = o.recognized_registration_organization_id
        where d.`cat_document_cat_id` =".$id);
		return $db->loadObjectList();
	}
    /**
     * Custom clean cache method
     *
     * @since	1.6
     */
    protected function cleanCache($group = null, $client_id = 0) {
        parent::cleanCache('com_taskmanager');
        parent::cleanCache('_system');
    }

    public function getRegNumberFormats() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('rnf_id, regformat, type');
        $query->from('#__toes_regnumber_formats');
        $query->order('rnf_id ASC');

        $db->setQuery($query);
        $formats = $db->loadObjectList();

        return $formats;
    }
    function getDocumenttype(){
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id');
		$db = JFactory::getDbo();
		$db->setQuery("select * from `#__toes_allowed_registration_document_type` where `allowed_registration_document_id` =".$id);
		return $db->loadObject();
	}
    function getDocument_types(){
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id');
		$db = JFactory::getDbo();
		$db->setQuery("select * from `#__toes_allowed_registration_document_type` order by allowed_registration_document_id");
		$typelist =  $db->loadObjectList();

		$options[] = JHTML::_('select.option','',JText::_('COM_TOES_SELECT_DOCUMENT_TYPE'));
		foreach($typelist as $r) :
			$options[] = JHTML::_('select.option',$r->allowed_registration_document_id,JText::_($r->allowed_registration_document_name_language_constant));
		endforeach;
		 
		return JHTML::_('select.genericlist',$options,'document_type[]','class="dtype required" style="width:500px!important"','value','text',null);
		
		
	}
	function getDocument_type_labels(){
		$db = JFactory::getDbo();
		$db->setQuery("select allowed_registration_document_id as value,allowed_registration_document_title_language_constant
		as text from `#__toes_allowed_registration_document_type` order by allowed_registration_document_id");
		$typelist =  $db->loadObjectList();
		if(count($typelist)){
		foreach($typelist as $t)
		$t->text = JText::_($t->text); 
		}
		return $typelist;
		
	}
}
