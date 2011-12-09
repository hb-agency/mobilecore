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

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace('{sitemap_legend:hide}', '{mobile_legend},mobileDns;{tablet_legend},tabletDns;{sitemap_legend:hide}', $GLOBALS['TL_DCA']['tl_page']['palettes']['root']);
	

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['mobileDns'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['mobileDns'],
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'url', 'trailingSlash'=>false, 'tl_class'=>'w50'),
	'save_callback' => array
	(
		array('tl_page_mobilecore', 'checkMobileUrl')
	)
);


$GLOBALS['TL_DCA']['tl_page']['fields']['tabletDns'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['tabletDns'],
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'url', 'trailingSlash'=>false, 'tl_class'=>'w50'),
	'save_callback' => array
	(
		array('tl_page_mobilecore', 'checkMobileUrl')
	)
);


class tl_page_mobilecore extends tl_page
{
	/**
	 * Check a mobile redirect URL
	 * @param mixed
	 * @return array
	 */
	public function checkMobileUrl($varValue)
	{
		return str_ireplace(array('http://', 'https://', 'ftp://'), '', $varValue);
	}
	
}


?>