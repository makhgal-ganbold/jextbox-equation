<?php

/**
* @extension     Installer - JExtBOX Equation
* @publisher     JExtBOX - BOX of Joomla Extensions
* @publisherURL  www.jextbox.com
* @author        Makhgal Ganbold
* @authorUrl     www.galaa.net
* @copyright     Copyright (C) 2017-2026 Makhgal Ganbold
* @license       GNU/GPL License - https://www.gnu.org/licenses/gpl.html
*/

namespace Joomla\Plugin\Installer\JExtBOXEquation\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Http\HttpFactory;
use Exception;

/**
 * JExtBOX Equation Installer Plugin
 * Handles update authorization check before package download.
 */
class JExtBOXEquation extends CMSPlugin
{
	/**
	 * Handle adding credentials to package download request
	 *
	 * @param   string  &$url      URL from which package is going to be downloaded
	 * @param   array   &$headers  Headers to be sent along the download request
	 *
	 * @return  boolean
	 * @throws  Exception
	 * * @since   4.0
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers): bool
	{

		// Check if the URL belongs to JExtBOX update server
		if (stripos($url, 'jextbox.com/download?extension=165&update=true') === false) {
			return true; // Return true as it's not our business
		}

		try {
			$http = HttpFactory::getHttp();

			// Set the authorization header as required by your original logic
			$requestHeaders = ['authorization' => 'true'];

			// Execute GET request with a 5-second timeout
			$response = $http->get('https://jextbox.com/download?extension=165&update=true', $requestHeaders, 5);

			// Check for connection errors (Joomla Http returns response object or throws exception)
			if (!$response || $response->code !== 200) {
				throw new Exception('An error has occurred when connected to the update server of the JExtBOX Equation extension.');
			}

			// Parse JSON response
			$data = json_decode($response->body);

			if (json_last_error() !== JSON_ERROR_NONE || !isset($data->authorized) || !isset($data->message)) {
				throw new Exception('The connection has failed for the extension JExtBOX Equation.');
			}

			// Final authorization logic
			if (!$data->authorized) {
				throw new Exception($data->message);
			}
		} catch (\Exception $e) {
			// Re-throw exception to stop the installation process with the error message
			throw new Exception($e->getMessage());
		}

		return true;

	}

}

?>
