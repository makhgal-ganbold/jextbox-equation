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

namespace Joomla\Plugin\Content\JExtBOXEquation\Extension;

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

		if (is_null($app->input->get('jextbox_mathjax'))) {
			$app->input->set('jextbox_mathjax', 'loaded');
			$wa->addInlineStyle('
				mjx-container:focus, mjx-container * {
					outline: none !important; 
					-webkit-tap-highlight-color: transparent;
				}
				mjx-container {
					cursor: default;
					-webkit-touch-callout: none;
				}
			');
			$wa->addInlineScript('
				window.MathJax = {
					tex: {
						tags: "ams",
						autoload: {
							color: [],
							colorv2: ["color"]
						},
						packages: {"[+]": ["noerrors"]}
					},
					options: {
						enableTabOrder: false,
						enableMenu: false,
						renderActions: {
							addMenu: []
						},
						ignoreHtmlClass: "tex2jax_ignore",
						processHtmlClass: "tex2jax_process"
					},
					loader: {
						load: ["input/asciimath", "[tex]/noerrors"]
					},
					startup: {
						pageReady() {
							return MathJax.startup.defaultPageReady().then(function () {
								document.querySelectorAll("mjx-container").forEach(function(node) {
									node.setAttribute("tabindex", "-1");
									node.style.outline = "none";
									node.style.pointerEvents = "none";
								});
							});
						}
					}
				};
			');
			$wa->registerAndUseScript(
				'plg_jextbox_mathjax',
				'https://cdn.jsdelivr.net/npm/mathjax@4/tex-mml-chtml.js',
				[],
				['defer' => true]
			);
		}

		$article->text = preg_replace('/\$\$([^\$]+)\$\$/', '\[$1\]', $article->text);
		$article->text = preg_replace('/\$([^\$]+)\$/', '\($1\)', $article->text);

		if (str_contains($article->text, '\begin{pspicture}')) {
			preg_match_all('/.begin.pspicture.[\s\S]*?.end.pspicture./i', $article->text, $pspictures);
			foreach ($pspictures[0] as $pspicture) {
				$replace = preg_replace('/<br ?\/?>/i', "\r\n", $pspicture);
				$replace = str_replace('&gt;', '>', $replace);
				$article->text = str_replace($pspicture, '<script type="text/latex">' . $replace . '</script>', $article->text);
			}
			if (is_null($app->input->get('jextbox_latex2html5'))) {
				$app->input->set('jextbox_latex2html5', 'loaded');
				$wa = $doc->getWebAssetManager();
				$pluginBaseUrl = Uri::root(true) . '/plugins/content/jextboxequation/latex2html5/';
				$wa->registerAndUseStyle(
					'plg_content_jextbox_latex2js',
					$pluginBaseUrl . 'latex2js.css'
				);
				$wa->registerAndUseScript(
					'plg_content_jextbox_latex2html5',
					$pluginBaseUrl . 'latex2html5.bundle.js',
					[],
					['defer' => true]
				);
				$wa->addInlineScript('document.addEventListener("DOMContentLoaded", function() { if(typeof LaTeX2HTML5 !== "undefined") LaTeX2HTML5.init(); });');
			}
		}

	}

}

?>
