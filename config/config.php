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
* Mobile types - these must be registered in your config file
*/
$GLOBALS['UA_TYPES'] = array(
	'mobile'	=> array(
		'maxImageWidth'		=> 300,
	),
	'tablet'	=> array(
		'maxImageWidth'		=> 800,
	),
);


/**
* Mobile HOOKs
*/
$GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('UATemplate', 'parseUATemplate');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('UATemplate', 'generateUAPage');
$GLOBALS['TL_HOOKS']['getImage'][] = array('UATemplate', 'getUAImage');
$GLOBALS['TL_HOOKS']['getContentElement'][] = array('UATemplate', 'showElementByUA');
?>
