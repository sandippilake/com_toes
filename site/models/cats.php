<?php

/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Categories Component Categories Model
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESModelCats extends JModelList {

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'user_id',
                'category_id',
                'name',
                'description',
                'start_date',
                'start_time',
                'end_date',
                'end_time',
                'due_date',
                'due_time',
                'priority',
                'recurring',
                'everywhat',
                'everynumber',
                'status'
            );
        }

        parent::__construct($config);
    }
}
