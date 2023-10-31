<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * @package     Installer - JExtBOX Equation
 * @publisher   JExtBOX.com - BOX of Joomla Extensions
 * @author      Galaa
 * @copyright   Copyright (C) 2017-2023 Galaa
 * @authorUrl   www.galaa.net
 * @license     GNU/GPL License - https://www.gnu.org/licenses/gpl.html
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgInstallerJExtBOXEquation extends Joomla\CMS\Plugin\CMSPlugin
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
			return false;
		$ch = curl_init('https://jextbox.com/download?extension=165&update=true');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: true'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
		$response = curl_exec($ch);
		curl_close($ch);
		if ($response === false)
			throw new Exception('An error has occurred when connected to the update server of the extension JExtBOX Equation.');
		$response = json_decode($response);
		if (json_last_error() != JSON_ERROR_NONE || !isset($response->authorized) || !isset($response->message))
			throw new Exception('The connection has failed for the extension JExtBOX Equation.');
		if (!$response->authorized)
			throw new Exception($response->message);
		return true;

	}

}

?>
