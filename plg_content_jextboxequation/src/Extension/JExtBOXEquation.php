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

namespace JExtBOX\Plugin\Content\JExtBOXEquation\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

final class JExtBOXEquation extends CMSPlugin implements SubscriberInterface
{

	public static function getSubscribedEvents(): array
	{

		return [
			'onContentPrepare' => 'onContentPrepare',
		];

	}

	public function onContentPrepare(Event $event): void
	{

		if ($this->getApplication()->isClient('api') || $event->getContext() === 'com_finder.indexer') {
			return;
		}

		$article = $event->getItem();
		if (!isset($article->text)) {
			return;
		}

		$math_patterns = ['$$', '\[', '$', '\(', '\begin{eq', '\begin{pspicture}'];
		$found = false;
		foreach ($math_patterns as $math_pattern) {
			if (str_contains($article->text, $math_pattern)) {
				$found = true;
				break;
			}
		}
		if (!$found) {
			return;
		}

		$doc = Factory::getDocument();
		$app = Factory::getApplication();
		$wa  = $doc->getWebAssetManager();

		$registry = $wa->getRegistry();
		if (!$registry->exists('style', 'latex2js.css')) {
			$registry->addExtensionRegistryFile('plg_content_jextboxequation');
		}

		$wa->useStyle('mathjax4.custom.css')->useScript('mathjax4')->useScript('mathjax4.init');

		$article->text = preg_replace('/\$\$([^\$]+)\$\$/', '\[$1\]', $article->text);
		$article->text = preg_replace('/\$([^\$]+)\$/', '\($1\)', $article->text);

		if (str_contains($article->text, '\begin{pspicture}')) {
			preg_match_all('/.begin.pspicture.[\s\S]*?.end.pspicture./i', $article->text, $pspictures);
			foreach ($pspictures[0] as $pspicture) {
				$replace = preg_replace('/<br ?\/?>/i', "\r\n", $pspicture);
				$replace = str_replace('&gt;', '>', $replace);
				$article->text = str_replace($pspicture, '<script type="text/latex">' . $replace . '</script>', $article->text);
			}
			$wa->useStyle('latex2js.css')->useScript('latex2html5.bundle')->useScript('latex2html5.init');
		}

	}

}

?>
