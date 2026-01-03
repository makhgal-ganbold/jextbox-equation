<?php

/**
* @extension     JExtBOX Equation
* @publisher     JExtBOX - BOX of Joomla Extensions
* @publisherURL  www.jextbox.com
* @author        Makhgal Ganbold
* @authorUrl     www.galaa.net
* @copyright     Copyright (C) 2026 Makhgal Ganbold
* @license       GNU/GPL License - https://www.gnu.org/licenses/gpl.html
*/

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\Installer\JExtBOXEquation\Extension\JExtBOXEquation;

return new class implements ServiceProviderInterface {
	public function register(Container $container) {
		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$plugin = new JExtBOXEquation(
					(array) PluginHelper::getPlugin('installer', 'jextboxequation')
				);
				$plugin->setApplication(Factory::getApplication());
				return $plugin;
			}
		);
	}
};

?>
