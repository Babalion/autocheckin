<?php
/**
 * @author     Babalion https://github.com/Babalion
 * @copyright  © 2021 Babalion https://github.com/Babalion All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://github.com/Babalion/autocheckin
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
	private $element_types;

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
		$this->element_types = array(
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

		foreach ($this->element_types as $el_type)
		{
			// first index of $el_type[1]==1 if this element-type is toggled active in settings
			if ($el_type[1] != 0)
			{
				// select all checked out elements
				$query = 'SELECT ' . $el_type[2] . ',checked_out,checked_out_time FROM ' . $el_type[3] . ' WHERE checked_out > 0';
				$db->setQuery($query);
				$checked_out_elements = $db->loadObjectList();

				// now step over all elements and check in if max_co_time_s is reached
				foreach ($checked_out_elements as $a)
				{
					$time_checked_out = strtotime($time_now) - strtotime($a->checked_out_time);
					if ($time_checked_out >= $this->max_co_time_s)
					{
						$query = 'UPDATE  ' . $el_type[3] . ' SET checked_out = 0 WHERE ' . $el_type[2] . ' = ' . $a->{$el_type[2]};
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}

		return true;
	}

}
