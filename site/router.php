<?php

/**
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Build the route for the com_content component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function TOESBuildRoute(&$query) {
    $segments = array();

    // get a menu item based on Itemid or currently active
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    
    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if (empty($query['Itemid'])) {
        $menuItem = $menu->getActive();
        $menuItemGiven = false;
    } else {
        $menuItem = $menu->getItem($query['Itemid']);
        $menuItemGiven = true;
    }
    
    if (isset($query['view'])) {
        $view = $query['view'];
    } else {
        // we need to have a view in the query or it is an invalid URL
        return $segments;
    }

    if ($view == 'show' || $view == 'cat' || $view == 'entryclerk' || $view == 'users') {
        $segments[] = $view;
        unset($query['view']);

        if (isset($query['layout'])) {
            if ($menuItemGiven && isset($menuItem->query['layout'])) {
                if ($query['layout'] == $menuItem->query['layout']) {

                    unset($query['layout']);
                }
            } else {
                if ($query['layout'] == 'default') {
					if($view == 'users') {
						$segments[] = 'orgofficials';
					}
                    unset($query['layout']);
                }
                else
                {
                    $segments[] = $query['layout'];
                    unset($query['layout']);
                }
            }
        } else if($view == 'users') {
			$segments[] = 'orgofficials';
		}

        if(isset($query['id']))
        {
            $segments[] = $query['id'];
            unset($query['id']);
        }
    }
    else
    {
        if (isset($menuItem->query['view']) && $query['view'] == $menuItem->query['view']) {
            unset($query['view']);
        }
        else
        {
            $segments[] = $view;
            unset($query['view']);
        }

        if (isset($query['layout'])) {
            if ($menuItemGiven && isset($menuItem->query['layout'])) {
                if ($query['layout'] == $menuItem->query['layout']) {

                    unset($query['layout']);
                }
            } else {
                if ($query['layout'] == 'default') {
                    unset($query['layout']);
                }
            }
        }
    }

    // if the layout is specified and it is the same as the layout in the menu item, we
    // unset it so it doesn't go into the query string.

    return $segments;
}

/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 * @since	1.5
 */
function TOESParseRoute($segments) {
    $vars = array();
	error_reporting(0);
    //Get the active menu item.
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    $item = $menu->getActive();

    switch ($segments[0])
    {
        case 'cat':
            if(isset($segments[2]))
            {
                $vars['layout'] = $segments[1];
                $vars['id'] = $segments[2];
            }
            else
            {
                $vars['layout'] = $segments[1];
            }
            break;
        case 'show':
            if(isset($segments[2]))
            {
                $vars['layout'] = $segments[1];
                $vars['id'] = $segments[2];
            }
            else
            {
                if(is_numeric($segments[1]))
                    $vars['id'] = $segments[1];
                else
                    $vars['layout'] = $segments[1];
            }
            break;
        case 'entryclerk':
            if(isset($segments[2]))
            {
                $vars['layout'] = $segments[1];
                $vars['id'] = $segments[2];
            }
            else
            {
                if(isset($segments[1]))
                    $vars['id'] = $segments[1];
            }
            break;
        case 'users':
			if(isset($segments[1])) {
				
				if($segments[1] == 'orgofficials') {
					$vars['layout'] = 'default';
				} else {
					$vars['layout'] = $segments[1];
				}
			}
            break;
        default :
            break;
    }
    $vars['view'] = $segments[0];

    return $vars;
}
