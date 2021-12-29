<?php
/**
 * @version        $Id: autocheckin.php 2011-07-30 eXtro-media.de $
 * @copyright      Copyright (C) 2011 eXtro-media.de All rights reserved.
 * @license        GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Version;

class PlgSystemAutocheckin extends CMSPlugin
{
	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;


	public function onAfterRender(): bool
	{
		$this->app = Factory::getApplication();
		if ($this->app->getName() == 'site')
		{
			return true;
		}
		$zeit   = Factory::getDate('now');
		$maxco  = $this->params->get('maxco', 24);
		$maxsec = $maxco * 3600;

		$db = Factory::getDBO();

		/**

		// Artikel prüfen und einchecken
		$query = 'SELECT id,checked_out,checked_out_time FROM #__content WHERE checked_out > 0';
		$db->setQuery($query);
		$erg = $db->loadObjectList();
		$anz = count($erg);
		for ($x = 0; $x < $anz; ++$x)
		{
			$unter = strtotime($zeit) - strtotime($erg[$x]->checked_out_time);
			if ($unter >= $maxsec)
			{
				$query = 'UPDATE #__content SET checked_out = 0 WHERE id = ' . $erg[$x]->id;
				$db->setQuery($query);
				$erg1 = $db->query();
			}
		}


		// Module prüfen und einchecken
		$query = 'SELECT id,checked_out,checked_out_time FROM #__modules WHERE checked_out > 0';
		$db->setQuery($query);
		$erg = $db->loadObjectList();
		$anz = count($erg);
		for ($x = 0; $x < $anz; ++$x)
		{
			$unter = strtotime($zeit) - strtotime($erg[$x]->checked_out_time);
			if ($unter >= $maxsec)
			{
				$query = 'UPDATE #__modules SET checked_out = 0 WHERE id = ' . $erg[$x]->id;
				$db->setQuery($query);
				$erg1 = $db->getQuery($query);
			}
		}
		// Plugins prüfen und einchecken, fehler korrigiert
		$query = 'SELECT extension_id,checked_out,checked_out_time,type FROM #__extensions WHERE type = \'plugin\' AND checked_out > 0';
		$db->setQuery($query);
		$erg = $db->loadObjectList();
		$anz = count($erg);
		for ($x = 0; $x < $anz; ++$x)
		{
			$unter = strtotime($zeit) - strtotime($erg[$x]->checked_out_time);
			if ($unter >= $maxsec)
			{
				$query = 'UPDATE #__extensions SET checked_out = 0 WHERE extension_id = ' . $erg[$x]->extension_id;
				$db->setQuery($query);
				$erg1 = $db->getQuery($query);
			}
		}
		// Menüs prüfen und einchecken
		$query = 'SELECT id,checked_out,checked_out_time,type FROM #__menu WHERE checked_out > 0';
		$db->setQuery($query);
		$erg = $db->loadObjectList();
		$anz = count($erg);
		for ($x = 0; $x < $anz; ++$x)
		{
			$unter = strtotime($zeit) - strtotime($erg[$x]->checked_out_time);
			if ($unter >= $maxsec)
			{
				$query = 'UPDATE #__menu SET checked_out = 0 WHERE id = ' . $erg[$x]->id;
				$db->setQuery($query);
				$erg1 = $db->query();
			}
		}
		 */

		return true;
	}

}
