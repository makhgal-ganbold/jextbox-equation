<?php

/**
* @extension     JExtBOX Equation
* @publisher     JExtBOX - BOX of Joomla Extensions
* @publisherURL  www.jextbox.com
* @author        Galaa
* @authorUrl     www.galaa.net
* @copyright     Copyright (C) 2013-2023 Galaa
* @license       GNU/GPL License - https://www.gnu.org/licenses/gpl.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

class PlgContentJExtBOXEquationInstallerScript
{

	public function update($parent)
	{

		$this->install($parent);

	}

	public function install($parent)
	{

		// Enable plugin
		$db	= Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->update('#__extensions')
			->set($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('element') . ' = ' . $db->quote('jextboxequation'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('content'))
		;
		$db->setQuery($query);
		$db->execute();

	}

}

?>
