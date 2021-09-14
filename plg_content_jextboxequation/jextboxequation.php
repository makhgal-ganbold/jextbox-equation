<?php

/**
* @extension     JExtBOX Equation
* @author        Galaa
* @authorUrl     www.galaa.mn
* @publisher     JExtBOX - BOX of Joomla Extensions
* @publisherURL  www.jextbox.com
* @copyright     Copyright (C) 2013-2021 Galaa
* @license       This extension in released under the GNU/GPL License - http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );

class plgContentJExtBOXEquation extends JPlugin
{

	function onContentPrepare($context, &$article, &$params, $limitstart=0)
	{

		if (strpos($article->text, '$$') === false && strpos($article->text, '\[') === false && strpos($article->text, '$') === false && strpos($article->text, '\(') === false && strpos($article->text, '\begin{eq') === false && strpos($article->text, '\begin{pspicture}') === false)
			return false;
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		if (is_null($app->input->get('jextbox_mathjax')))
		{
			$app->input->set('jextbox_mathjax', 'loaded');
			// MathJax configuration
			$doc->addScriptDeclaration('MathJax.Hub.Config({ TeX: { equationNumbers: {autoNumber: "AMS"} }, showMathMenu: false, messageStyle: "none" });', 'text/x-mathjax-config');
			// MathJax
			$version = $this->params->get('mathjax-version', '2.7.7');
			if (!preg_match('/^2\.[0-9]+\.[0-9]+$/', $version))
			{
				$version = '2.7.7';
			}
			$doc->addScript('//cdnjs.cloudflare.com/ajax/libs/mathjax/'.$version.'/MathJax.js?config=TeX-MML-AM_CHTML');
		}
		// converting line equation $$
		$article->text = preg_replace('/\$\$([^\$]+)\$\$/', '\[$1\]', $article->text);
		// converting inline equation $
		$article->text = preg_replace('/\$([^\$]+)\$/', '\($1\)', $article->text);
		// LaTeX2HTML5 (pspicture) -- MathJax must be loaded
		if (strpos($article->text, '\begin{pspicture}') !== false) // checking for existing pspicture
		{
			// preparing all PSPictures for LaTeX2HTML5
			preg_match_all('/.begin.pspicture.[\s\S]*?.end.pspicture./i', $article->text, $pspictures);
			foreach ($pspictures[0] as $pspicture)
			{
				$replace = preg_replace('/<br ?\/?>/i', "\r\n", $pspicture);
				$replace = str_replace('&gt;', '>', $replace);
				$article->text = str_replace($pspicture, '<script type="text/latex">'.$replace.'</script>', $article->text);
			}
			if (is_null($app->input->get('jextbox_latex2html5')))
			{
				$app->input->set('jextbox_latex2html5', 'loaded');
				// CSS for LaTeX2HTML5
				$doc->addStyleSheet('plugins/content/jextboxequation/latex2html5/latex2js.css');
				// LaTeX2HTML5 javascript library
				$doc->addScript('plugins/content/jextboxequation/latex2html5/latex2html5.bundle.js');
				// adding launcher of LaTeX2HTML5
				$doc->addScriptDeclaration('document.addEventListener("DOMContentLoaded",function(){LaTeX2HTML5.init();});');
			}
		}
		return true;

	}

}

?>
