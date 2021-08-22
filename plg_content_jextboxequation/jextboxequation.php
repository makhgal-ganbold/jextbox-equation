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

	private $first_time_executing = array
	(
		'mathjax' => true,
		'latex2html5' => true
	);

	function onContentPrepare($context, &$article, &$params, $limitstart=0)
	{

		if (strpos($article->text, '$$') === false && strpos($article->text, '\[') === false && strpos($article->text, '$') === false && strpos($article->text, '\(') === false && strpos($article->text, '\begin{eq') === false && strpos($article->text, '\begin{pspicture}') === false)
		{
			return false;
		}
		if ($this->first_time_executing['mathjax'])
		{
			$this->first_time_executing['mathjax'] = false;
			// Document
			$doc = JFactory::getDocument();
			// MathJax configuration
			$doc->addScriptDeclaration('MathJax.Hub.Config({ TeX: { equationNumbers: {autoNumber: "AMS"} }, showMathMenu: false, messageStyle: "none" });', 'text/x-mathjax-config');
			// MathJax
			$doc->addScript('http'.(JFactory::getApplication()->isSSLConnection() ? 's' : '').'://cdnjs.cloudflare.com/ajax/libs/mathjax/'.$this->params->get('mathjax-version', '2.7.7').'/MathJax.js?config=TeX-MML-AM_CHTML');
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
				$article->text = str_replace($pspicture, '<script type="tex/latex">'.$replace.'</script>', $article->text);
			}
			if ($this->first_time_executing['latex2html5'])
			{
				$this->first_time_executing['latex2html5'] = false;
				// CSS for LaTeX2HTML5
				$doc->addStyleSheet('plugins/content/jextboxequation/latex2html5/latex2html5.min.css');
				// LaTeX2HTML5 javascript library
				$doc->addScript('plugins/content/jextboxequation/latex2html5/latex2html5.min.js');
				// adding launcher of LaTeX2HTML5
				$article->text .= '<script type="text/javascript">$("body").latex();</script>';
			}
		}
		return true;

	}

}

?>
