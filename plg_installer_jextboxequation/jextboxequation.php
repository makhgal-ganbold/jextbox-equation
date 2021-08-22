<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * @package     Installer - JExtBOX Equation
 * @author      Galaa
 * @publisher   JExtBOX.com - BOX of Joomla Extensions (www.jextbox.com)
 * @copyright   Copyright (C) 2017-2021 Galaa
 * @authorUrl   www.galaa.mn
 * @license     This extension in released under the GNU/GPL License - http://www.gnu.org/copyleft/gpl.html
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport( 'joomla.plugin.plugin' );

class plgInstallerJExtBOXEquation extends JPlugin
{

	/**
	 * Handle adding credentials to package download request
	 *
	 * @param   string  $url        url from which package is going to be downloaded
	 * @param   array   $headers    headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true if credentials have been added to request or not our business, false otherwise (credentials not set by user)
	 *
	 * @since   2.5
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{

		if (stripos($url, 'jextbox.com/download?extension=165&update=true') === false)
		{
			return true;
		}
		$plugin = JPluginHelper::getPlugin('content', 'jextboxequation');
		$params = new JRegistry($plugin->params);
		$paymentid = $params->get('paymentid', '');
		if (empty($paymentid))
		{
			JFactory::getApplication()->enqueueMessage('Payment ID is not entered for the extension JExtBOX Equation.', 'error');
			return false;
		}
		$headers['payment-id'] = $paymentid;
		return true;

	}

}

?>
