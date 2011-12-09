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
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

abstract class UAController extends Controller
{
	
	/**
	* Boolean mobile
	*/
	protected $blnMobile = false;
	
	/**
	* User agent string
	*/
	protected $strAgent = 'regular';
	
	
	/**
	* Load UA or Browser object
	*/
	protected function __construct()
	{
		parent::__construct();
		
		$this->import('Database');
		
		$ua = $this->Environment->agent;
		if($ua->mobile)
		{
			$this->blnMobile = true;
			
			if(stripos($this->Environment->httpUserAgent,'iPad') !== false)
			{
				$this->strAgent = 'tablet';
			}
			else	
			{
				$this->strAgent = 'mobile';
			}
		}
		
		//HOOK to set another user agent string
		if (isset($GLOBALS['TL_HOOKS']['setUAType']) && is_array($GLOBALS['TL_HOOKS']['setUAType']))
		{
			foreach ($GLOBALS['TL_HOOKS']['setUAType'] as $callback)
			{
				$this->import($callback[0]);
				$this->strAgent = $this->$callback[0]->$callback[1]($this->strAgent, $this);
			}
		}
		
	}
	
	
	public function showElementByUA($objElement, $strBuffer)
	{
		$strOnly = $this->strAgent . 'hide';
		
		if( TL_MODE=='FE' && $objElement->$strOnly)
		{
			$strBuffer = '';
		}
		
		return $strBuffer;
	}



	public function getUAImage($image, $width, $height, $mode, $strCacheName, $objFile)
	{
		if(!$this->blnMobile)
			return null;
				
		// Return the path to the original image if the GDlib cannot handle it
		if (!extension_loaded('gd') || !$objFile->isGdImage || $objFile->width > 3000 || $objFile->height > 3000 || (!$width && !$height) || $width > 1200 || $height > 1200)
		{
			return $image;
		}

		$intPositionX = 0;
		$intPositionY = 0;
		
		//Custom for mobile
		//if width is more than MaxWidth we will need to resize proportionately
		
		$intMaxWidth = $GLOBALS['UA_TYPES'][$this->strAgent]['maxImageWidth'];
				
		$width = !strlen($width) ? $objFile->width : $width;
		$blnMobileResize = (TL_MODE=='FE' && $objFile->width > $intMaxWidth && ($width > $intMaxWidth || !strlen($width))) ? true : false;
		$mode = $blnMobileResize ? 'proportional' : $mode;
		$intWidth = $blnMobileResize ? $intMaxWidth : $width; //setting the width to be no wider than maxwidth
		$intHeight = $height;

		// Mode-specific changes
		if ($intWidth && $intHeight)
		{
			switch ($mode)
			{
				case 'proportional':
					if ($objFile->width >= $objFile->height)
					{
						unset($height, $intHeight);
					}
					else
					{
						unset($width, $intWidth);
					}
					break;

				case 'box':
					if (ceil($objFile->height * $width / $objFile->width) <= $intHeight)
					{
						unset($height, $intHeight);
					}
					else
					{
						unset($width, $intWidth);
					}
					break;
			}
		}

		// Resize width and height and crop the image if necessary
		if ($intWidth && $intHeight)
		{
			if (($intWidth * $objFile->height) != ($intHeight * $objFile->width))
			{
				$intWidth = ceil($objFile->width * $intHeight / $objFile->height);
				$intPositionX = -intval(($intWidth - $width) / 2);

				if ($intWidth < $width)
				{
					$intWidth = $width;
					$intHeight = ceil($objFile->height * $intWidth / $objFile->width);
					$intPositionX = 0;
					$intPositionY = -intval(($intHeight - $height) / 2);
				}
			}

			$strNewImage = imagecreatetruecolor($width, $height);
		}

		// Calculate the height if only the width is given
		elseif ($intWidth)
		{
			$intHeight = ceil($objFile->height * $intWidth / $objFile->width);
			$strNewImage = imagecreatetruecolor($intWidth, $intHeight);
		}

		// Calculate the width if only the height is given
		elseif ($intHeight)
		{
			$intWidth = ceil($objFile->width * $intHeight / $objFile->height);
			$strNewImage = imagecreatetruecolor($intWidth, $intHeight);
		}

		$arrGdinfo = gd_info();
		$strGdVersion = preg_replace('/[^0-9\.]+/', '', $arrGdinfo['GD Version']);

		switch ($objFile->extension)
		{
			case 'gif':
				if ($arrGdinfo['GIF Read Support'])
				{
					$strSourceImage = imagecreatefromgif(TL_ROOT . '/' . $image);
					$intTranspIndex = imagecolortransparent($strSourceImage);

					// Handle transparency
					if ($intTranspIndex >= 0 && $intTranspIndex < imagecolorstotal($strSourceImage))
					{
						$arrColor = imagecolorsforindex($strSourceImage, $intTranspIndex);
						$intTranspIndex = imagecolorallocate($strNewImage, $arrColor['red'], $arrColor['green'], $arrColor['blue']);
						imagefill($strNewImage, 0, 0, $intTranspIndex);
						imagecolortransparent($strNewImage, $intTranspIndex);
					}
				}
				break;

			case 'jpg':
			case 'jpeg':
				if ($arrGdinfo['JPG Support'] || $arrGdinfo['JPEG Support'])
				{
					$strSourceImage = imagecreatefromjpeg(TL_ROOT . '/' . $image);
				}
				break;

			case 'png':
				if ($arrGdinfo['PNG Support'])
				{
					$strSourceImage = imagecreatefrompng(TL_ROOT . '/' . $image);

					// Handle transparency (GDlib >= 2.0 required)
					if (version_compare($strGdVersion, '2.0', '>='))
					{
						imagealphablending($strNewImage, false);
						$intTranspIndex = imagecolorallocatealpha($strNewImage, 0, 0, 0, 127);
						imagefill($strNewImage, 0, 0, $intTranspIndex);
						imagesavealpha($strNewImage, true);
					}
				}
				break;
		}

		// The new image could not be created
		if (!$strSourceImage)
		{
			$this->log('Image "' . $image . '" could not be processed', 'Controller getImage()', TL_ERROR);
			return null;
		}

		imagecopyresampled($strNewImage, $strSourceImage, $intPositionX, $intPositionY, 0, 0, $intWidth, $intHeight, $objFile->width, $objFile->height);

		// Fallback to PNG if GIF ist not supported
		if ($objFile->extension == 'gif' && !$arrGdinfo['GIF Create Support'])
		{
			$objFile->extension = 'png';
		}

		// Create the new image
		switch ($objFile->extension)
		{
			case 'gif':
				imagegif($strNewImage, TL_ROOT . '/' . $strCacheName);
				break;

			case 'jpg':
			case 'jpeg':
				imagejpeg($strNewImage, TL_ROOT . '/' . $strCacheName, (!$GLOBALS['TL_CONFIG']['jpgQuality'] ? 80 : $GLOBALS['TL_CONFIG']['jpgQuality']));
				break;

			case 'png':
				imagepng($strNewImage, TL_ROOT . '/' . $strCacheName);
				break;
		}

		// Destroy the temporary images
		imagedestroy($strSourceImage);
		imagedestroy($strNewImage);

		// Resize the original image
		if ($target)
		{
			$this->import('Files');
			$this->Files->rename($strCacheName, $target);

			return $target;
		}

		// Set the file permissions when the Safe Mode Hack is used
		if ($GLOBALS['TL_CONFIG']['useFTP'])
		{
			$this->import('Files');
			$this->Files->chmod($strCacheName, 0644);
		}

		// Return the path to new image
		return $strCacheName;
	
	}
	

	/**
	* Abstract function - required by all subclasses
	* This will come in handy down the line (*wink*)
	*/
	abstract public function parseUATemplate($strBuffer, $strTemplate);
  
}

?>