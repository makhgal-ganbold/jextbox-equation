<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * @package     Installer - JExtBOX Equation
 * @publisher   JExtBOX.com - BOX of Joomla Extensions (www.jextbox.com)
 * @author      Galaa
 * @copyright   Copyright (C) 2017-2023 Galaa
 * @authorUrl   www.galaa.net
 * @license     GNU/GPL License - https://www.gnu.org/licenses/gpl.html
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgInstallerJExtBOXEquationInstallerScript
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
			->where($db->quoteName('folder') . ' = ' . $db->quote('installer'))
		;
		$db->setQuery($query);
		$db->execute();

	}

}

?>
