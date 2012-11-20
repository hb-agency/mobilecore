<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Winans Creative 2010
 * @author     Blair Winans <blair@winanscreative.com>
 * @author     Adam Fisher <adam@winanscreative.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

class UATemplate extends UAController
{
  
	/**
	 * Required Hook for outputFrontendTemplate
	 * 
	 * Class:	FrontendTemplate
	 * Method:	parse
	 * Hook:	$GLOBALS['TL_HOOKS']['parseFrontendTemplate']
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function parseUATemplate($strBuffer, $strTemplate)
	{
		//Need to auto-adjust images that are too wide
		if(stripos($strTemplate, 'fe_') !== false && $this->blnMobile)
		{
			 // Include SimpleHtmlDom
			if (!function_exists('file_get_html'))
				require_once(TL_ROOT . '/system/modules/mobilecore/simple_html_dom.php');
				
    		$objDoc = str_get_html($strBuffer);
    		
    		$intMaxWidth = $GLOBALS['UA_TYPES'][$this->strAgent]['maxImageWidth'];
    		
		    foreach($objDoc->find('img') as $tag) 
		    {
		       $fileSrc = $tag->getAttribute('src');
		       $blnAddDomainPrefix = false;
		       $blnAddSubdomainPrefix = false;
		       $strDomain = '';
		       
		       //Check for subdomain settings
		       if(stripos($fileSrc,TL_FILES_URL)!==false)
		       {
		       		$blnAddSubdomainPrefix = true;
		       		$strURL = parse_url($fileSrc);
		       		$fileSrc = substr($strURL['path'],1);
		       }
		       //Check for external URL or one that we may not be able to resize
		       elseif( stripos($fileSrc,'http')!==false )
		       {
			       	$strURL = parse_url($fileSrc);
			       	//Check to see if it is on the same domain and file exists... If so we can resize it.
			       	if (file_exists(TL_ROOT . $strURL['path']))
					{
						$fileSrc = substr($strURL['path'],1);
						$strDomain = $strURL['scheme'] . '//:' . $strURL['host'];
					}
					//Otherwise we have an external image
					else
					{	
						//All we can do here with external images is reset and set the width attribute
						$tag->removeAttribute('width');
						$tag->removeAttribute('height');
						$tag->setAttribute('width', $intMaxWidth);
						continue;
					}
		       }
		       
		       $objFile = new File($fileSrc);
		       $blnResize = (($tag->hasAttribute('width') && $tag->getAttribute('width') > $intMaxWidth) || (!$tag->hasAttribute('width') && $objFile->width > $intMaxWidth)) ? true : false;
		       		       
		       if($blnResize)
		       {
		       	   	$src = $this->urlEncode($fileSrc);
		       	   	$width = $intMaxWidth;
		       	   	$height = '';
		       	   	$mode = 'proportional'; //@todo - Make configurable in settings
		       	   	$strCacheName = 'system/html/' . $objFile->filename . '-'.$this->strAgent.'-' . substr(md5('-w' . $width . '-h' . $height . '-' . $tag->getAttribute('src') . '-' . $mode . '-' . $objFile->mtime), 0, 8) . '.' . $objFile->extension;
		       	   
					// Return the path of the new image if it exists already
					if (file_exists(TL_ROOT . '/' . $strCacheName))
					{
						$newImage = $strCacheName;
					}
					else
					{
						$newImage = $this->getUAImage($src, $width, $height, $mode, $strCacheName, $objFile);
					}
					
					$newImageSrc = $blnAddSubdomainPrefix ? TL_FILES_URL. '/' .$newImage : ( $blnAddDomainPrefix ? $strDomain . '/' . $newImage : $newImage);
					
			       	$tag->setAttribute('src', $newImageSrc);
			       	$tag->removeAttribute('width');
			       	$tag->removeAttribute('height');
			   }
		    }
		    
		    $strBuffer = $objDoc->save();;
		    		    		
		}
		//HOOK for other mobile parsing - will have more options for this later
		if (isset($GLOBALS['TL_HOOKS']['outputUATemplate']) && is_array($GLOBALS['TL_HOOKS']['outputUATemplate']))
		{
			foreach ($GLOBALS['TL_HOOKS']['outputUATemplate'] as $callback)
			{
				$this->import($callback[0]);
				$strBuffer = $this->$callback[0]->$callback[1]($strBuffer, $strTemplate, $this);
			}
		}
			
	  	return $strBuffer;
	}
	
	 
	/**
	 * Required Hook for generatePage
	 * 
	 * Class:	PageRegular
	 * Method:	generate
	 * Hook:	$GLOBALS['TL_HOOKS']['generatePage']
	 *
	 * @access	public
	 * @param	object
	 * @param	object
	 * @param	object
	 * @return	void
	 */
	public function generateUAPage($objPage, $objLayout, $objPageRegular)
	{
	  	if(!$this->blnMobile)
	  		return;
	  		
	  	//Set up DB values based on UA
	  	$UAModules = $this->strAgent. 'modules';
	  	$UATemplate = $this->strAgent. 'template';
	  	$UADoctype = $this->strAgent. 'doctype';
	  	$UADns = $this->strAgent. 'Dns';
	  	
		list($strFormat, $strVariant) = explode('_', $objPage->{$UADoctype});
		$objPage->outputFormat = $strFormat;
		$objPage->outputVariant = $strVariant;
	  	
	  	//Determine whether to redirect to mobile subdomain
	  	$objRoot = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($objPage->rootId);
	  	
	  	if( $objRoot->numRows && strlen($objRoot->{$UADns}) )
	  	{
	  		$arrHost = explode('.', $this->Environment->host);
	  		if($arrHost[0] != $objRoot->{$UADns} && !$_SESSION['MC-OVERRIDE'][$this->strAgent])
	  		{
	  			unset($arrHost[0]);
	  			$strUrl = $this->Environment->ssl ? 'https://' : 'http://';
	  			$strUrl .= $objRoot->{$UADns};
	  			$strUrl .= count($arrHost) ? '.' . implode('.', $arrHost) : '';
	  			$strUrl .= $this->Environment->path . '/' . $this->Environment->request;
	  			$this->redirect($strUrl);
	  		}
	  	}
	  	
	  	//Switch to mobile template if one exists
		$objPage->template = strlen($objLayout->{$UATemplate}) ? $objLayout->{$UATemplate} : 'fe_'.$this->strAgent;
		
		// Initialize the template
		$this->createUATemplate($objPage, $objLayout, $objPageRegular);

		// Initialize modules and sections
		$arrCustomSections = array();
		$arrSections = array('header', 'left', 'right', 'main', 'footer');
		$arrModules = count(deserialize($objLayout->{$UAModules})) ? deserialize($objLayout->{$UAModules}) : deserialize($objLayout->modules);

		// Generate all modules
		foreach ($arrModules as $arrModule)
		{
			if (in_array($arrModule['col'], $arrSections))
			{
				// Filter active sections (see #3273)
				if ($arrModule['col'] == 'header' && !$objLayout->header)
				{
					continue;
				}
				if ($arrModule['col'] == 'left' && $objLayout->cols != '2cll' && $objLayout->cols != '3cl')
				{
					continue;
				}
				if ($arrModule['col'] == 'right' && $objLayout->cols != '2clr' && $objLayout->cols != '3cl')
				{
					continue;
				}
				if ($arrModule['col'] == 'footer' && !$objLayout->footer)
				{
					continue;
				}

				$objPageRegular->Template->$arrModule['col'] .= $this->getFrontendModule($arrModule['mod'], $arrModule['col']);
			}
			else
			{
				$arrCustomSections[$arrModule['col']] .= $this->getFrontendModule($arrModule['mod'], $arrModule['col']);
			}
		}
		
		$objPageRegular->Template->sections = $arrCustomSections;
	
	}
	


	/**
	 * Create a new mobile page template
	 *
	 * @access	protected
	 * @param	object
	 * @param	object
	 * @param	object
	 * @return	void
	 */
	protected function createUATemplate(Database_Result $objPage, Database_Result $objLayout, $objPageRegular)
	{	  	
		$objPageRegular->Template = new FrontendTemplate($objPage->template);
		
		// Generate the DTD
		if ($objPage->outputFormat == 'xhtml')
		{
			if ($objPage->outputVariant == 'strict')
			{
				$objPageRegular->Template->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			}
			else
			{
				$objPageRegular->Template->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
			}
		}
		
		// Meta robots tag
		$objPageRegular->Template->robots = ($objPage->robots != '') ? $objPage->robots : 'index,follow';

		// Include basic style sheets
		$objPageRegular->Template->framework = '<link rel="stylesheet"' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . ' href="system/contao.css" media="screen"' . (($objPage->outputFormat == 'xhtml') ? " />\n" : ">\n");

		// MooTools scripts
		if ($objLayout->mooSource == 'moo_googleapis')
		{
			$protocol = $this->Environment->ssl ? 'https://' : 'http://';

			$objPageRegular->Template->mooScripts  = '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . $protocol . 'ajax.googleapis.com/ajax/libs/mootools/' . MOOTOOLS . '/mootools-yui-compressed.js"></script>' . "\n";
			$objPageRegular->Template->mooScripts .= '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . TL_PLUGINS_URL . 'plugins/mootools/' . MOOTOOLS . '/mootools-more.js"></script>' . "\n";
		}
		else
		{
			$objCombiner = new Combiner();

			$objCombiner->add('plugins/mootools/' . MOOTOOLS . '/mootools-core.js', MOOTOOLS_CORE);
			$objCombiner->add('plugins/mootools/' . MOOTOOLS . '/mootools-more.js', MOOTOOLS_MORE);

			$objPageRegular->Template->mooScripts = '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . $objCombiner->getCombinedFile() . '"></script>' . "\n";
		}

		// Initialize sections
		$objPageRegular->Template->header = '';
		$objPageRegular->Template->left = '';
		$objPageRegular->Template->main = '';
		$objPageRegular->Template->right = '';
		$objPageRegular->Template->footer = '';

		// Initialize custom layout sections
		$objPageRegular->Template->sections = array();
		$objPageRegular->Template->sPosition = $objLayout->sPosition;

		// Default settings
		$objPageRegular->Template->layout = $objLayout;
		$objPageRegular->Template->language = $GLOBALS['TL_LANGUAGE'];
		$objPageRegular->Template->charset = $GLOBALS['TL_CONFIG']['characterSet'];
		$objPageRegular->Template->base = $this->Environment->base;
		$objPageRegular->Template->disableCron = $GLOBALS['TL_CONFIG']['disableCron'];
		
		//Set up DB values based on UA
		$UAStylesheet = $this->strAgent. 'stylesheet';
	  	$UAModules = $this->strAgent. 'modules';
	  	$UATemplate = $this->strAgent. 'template';
	  	$UAHead = $this->strAgent. 'head';
	  	$UAMootools = $this->strAgent. 'mootools';
	  	$UAScript = $this->strAgent. 'script';
	  			
		// Override with Mobile
		$objLayout->stylesheet = count($objLayout->{$UAStylesheet}) ? $objLayout->{$UAStylesheet} : $objLayout->stylesheet;
		$objLayout->modules = count($objLayout->{$UAModules}) ? $objLayout->{$UAModules} : $objLayout->modules;
  		$objLayout->template = strlen($objLayout->{$UATemplate}) ? $objLayout->{$UATemplate} : $objLayout->template;
  		$objLayout->head = $objLayout->{$UAHead};
  		$objLayout->mootools = $objLayout->{$UAMootools};
  		$objLayout->script = $objLayout->{$UAScript};
		
	}
  
}

?>