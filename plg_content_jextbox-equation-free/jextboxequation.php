<?php

/**
* @extension     JExtBOX Equation
* @author        Galaa
* @authorUrl     www.galaa.mn
* @authorEmail   contact@galaa.mn
* @publisher     JExtBOX - BOX of Joomla Extensions
* @publisherURL  www.jextbox.com
* @copyright     Copyright (C) 2013 Galaa
* @license       This extension in released under the GNU/GPL License - http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );

class plgContentJExtBOXEquation extends JPlugin
{

	private $first_time_executing = array(
		'mathjax' => true,
		'latex2html5' => true
	);

	function onContentPrepare($context, &$article, &$params, $limitstart=0){

		if(JString::strpos($article->text, '$$') === false && JString::strpos($article->text, '\[') === false && JString::strpos($article->text, '$') === false && JString::strpos($article->text, '\(') === false && JString::strpos($article->text, '\begin{eq') === false && JString::strpos($article->text, '\begin{pspicture}') === false){
			return false;
		}
		if($this->first_time_executing['mathjax']){
			$this->first_time_executing['mathjax'] = false;
			// Document
			$doc = JFactory::getDocument();
			// MathJax configuration
			$doc->addScriptDeclaration('MathJax.Hub.Config({ TeX: { equationNumbers: {autoNumber: "AMS"} }, showMathMenu: false, messageStyle: "none" });', 'text/x-mathjax-config');
			// MathJax
//			if(JFactory::getApplication()->isSSLConnection()){ // works in Joomla 3.2
			if(!empty($_SERVER['HTTPS'])){  // for Joomla 2.5
				$doc->addScript('https://c328740.ssl.cf1.rackcdn.com/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML');
			}else{
				$doc->addScript('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML');
			}
		}
		// converting, on the fly
		if($this->params->get('convertonthefly', 1)){
			// $$
			$article->text = preg_replace('/\$\$([^\$]+)\$\$/', '\[$1\]', $article->text);
			// $
			$article->text = preg_replace('/\$([^\$]+)\$/', '\($1\)', $article->text);
		}
		// LaTeX2HTML5 (pspicture) -- MathJax must be loaded
		if(strpos($article->text, '\begin{pspicture}') !== false){ // checking for existing pspicture
			// preparing all PSPictures for LaTeX2HTML5
			preg_match_all('/.begin.pspicture.[\s\S]*?.end.pspicture./i', $article->text, $pspictures);
			foreach($pspictures[0] as $pspicture){
				$replace = preg_replace('/<br ?\/?>/i', "\r\n", $pspicture);
				$replace = str_replace('&gt;', '>', $replace);
				$article->text = str_replace($pspicture, '<script type="tex/latex">'.$replace.'</script>', $article->text);
			}
			if($this->first_time_executing['latex2html5']){
				$this->first_time_executing['latex2html5'] = false;
				// jQuery
				switch($this->params->get('jquery', 0)){
					case 2:
						$doc->addScript('//ajax.googleapis.com/ajax/libs/jquery/'.$this->params->get('jqueryversion', '1.11.1').'/jquery.min.js');
						break;
					case 1:
						$doc->addScript($this->params->get('localjquery', '//ajax.googleapis.com/ajax/libs/jquery/'.$this->params->get('jqueryversion', '1.11.1').'/jquery.min.js'));
						break;
					default:
						break;
				}
				// CSS for LaTeX2HTML5
				$doc->addStyleSheet('plugins/content/jextboxequation/latex2html5/latex2html5.min.css');
				// LaTeX2HTML5 javascript library
				$doc->addScript('plugins/content/jextboxequation/latex2html5/latex2html5.min.js');
				// jQuery.noConflict
				if($this->params->get('noconflict', 0) === 1){
					$doc->addScriptDeclaration('jQuery.noConflict()');
					$script = 'jQuery("body").latex();';
				}else{
					$script = '$("body").latex();';
				}
				// adding launcher of LaTeX2HTML5
				$article->text .= '<script type="text/javascript">'.$script.'</script>';
			}
		}
		return true;

	}

}

?>
