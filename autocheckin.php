<?php
/**
 * @version        $Id: autocheckin.php 2011-07-30 eXtro-media.de $
 * @copyright      Copyright (C) 2011 eXtro-media.de All rights reserved.
 * @license        GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

class PlgSystemAutocheckin extends CMSPlugin
{
	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 * @since  Joomla! 3.1
	 */
	protected $autoloadLanguage = true;


	private $app;
	private $max_co_time_s;
	private $active_element_types;

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->app           = Factory::getApplication();
		$this->max_co_time_s = $this->params->get('max_co_time') * 20;

		// read in element types
		$checkin_articles   = $this->params->get('checkin_articles');
		$checkin_categories = $this->params->get('checkin_categories');
		$checkin_menus      = $this->params->get('checkin_menus');
		$checkin_modules    = $this->params->get('checkin_modules');
		$checkin_plugins    = $this->params->get('checkin_plugins');

		// load the element-types
		// third entry is the database-table-name of this elements
		$this->active_element_types = array(
			array('articles', $checkin_articles, 'id', '#__content'),
			array('categories', $checkin_categories, 'id', '#__categories'),
			array('menus', $checkin_menus, 'id', '#__menu'),
			array('modules', $checkin_modules, 'id', '#__modules'),
			array('plugins', $checkin_plugins, 'extension_id', '#__extensions')
		);

	}

	public function onAfterRender(): bool
	{
		// TODO is this needed?
		if ($this->app->getName() == 'site')
		{
			return true;
		}

		$time_now = Factory::getDate('now');
		$db       = Factory::getDBO();

		foreach ($this->active_element_types as $a_el_type)
		{
			// first index of $a_el_type[1]==1 if this element-type is toggled active in settings
			if ($a_el_type[1] != 0)
			{
				// select all checked out elements
				$query = 'SELECT ' . $a_el_type[2] . ',checked_out,checked_out_time FROM ' . $a_el_type[3] . ' WHERE checked_out > 0';
				$db->setQuery($query);
				$checked_out_elements = $db->loadObjectList();

				// now step over all elements and check in if max_co_time_s is reached
				foreach ($checked_out_elements as $a)
				{
					$time_checked_out = strtotime($time_now) - strtotime($a->checked_out_time);
					if ($time_checked_out >= $this->max_co_time_s)
					{
						$query = 'UPDATE  ' . $a_el_type[3] . ' SET checked_out = 0 WHERE ' . $a_el_type[2] . ' = ' . $a->{$a_el_type[2]};
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}

		return true;
	}

}
