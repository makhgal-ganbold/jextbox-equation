<?php

/**
* @extension     JExtBOX Equation
* @publisher     JExtBOX - BOX of Joomla Extensions
* @publisherURL  www.jextbox.com
* @author        Makhgal Ganbold
* @authorUrl     www.galaa.net
* @copyright     Copyright (C) 2013-2026 Makhgal Ganbold
* @license       GNU/GPL License - https://www.gnu.org/licenses/gpl.html
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

class PlgContentJExtBOXEquationInstallerScript
{

	public function update($parent): void
	{

		$this->install($parent);

	}

	public function install($parent): void
	{

		try {
			// Get the container
			$container = Factory::getContainer();
			// Check for the database object safely
			if ($container->has('DatabaseInterface')) {
				$db = $container->get('DatabaseInterface');
			} else {
				// Fallback for some J5 environments
				$db = $container->get(\Joomla\Database\DatabaseInterface::class);
			}
			$query = $db->getQuery(true);
			$query
				->update($db->quoteName('#__extensions'))
				->set($db->quoteName('enabled') . ' = 1')
				->where($db->quoteName('element') . ' = ' . $db->quote('jextboxequation'))
				->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
				->where($db->quoteName('folder') . ' = ' . $db->quote('content'));
			$db->setQuery($query);
			$db->execute();
		} catch (\Exception $e) {
			Log::add('Could not enable JExtBOX Equation content plugin: ' . $e->getMessage(), Log::WARNING, 'jerror');
		}

	}

}

?>
