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
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('{expert_legend:hide}', '{mobile_legend:hide},mobilestylesheet,mobilemodules,mobiletemplate,mobilehead,mobilemootools,mobilescript;{tablet_legend:hide},tabletstylesheet,tabletmodules,tablettemplate,tablethead,tabletmootools,tabletscript;{expert_legend:hide}', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']); 


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_layout']['fields']['mobilestylesheet'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mobilestylesheet'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout', 'getStyleSheets'),
	'eval'                    => array('multiple'=>true)
);
		
$GLOBALS['TL_DCA']['tl_layout']['fields']['mobilemodules'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mobilemodules'],
	'exclude'                 => true,
	'inputType'               => 'moduleWizard'
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['mobiletemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mobiletemplate'],
	'exclude'                 => true,
	'default'				  => 'fe_mobile',
	'filter'                  => true,
	'search'                  => true,
	'sorting'                 => true,
	'flag'                    => 11,
	'inputType'               => 'select',
	'options_callback'        => array('tl_layout', 'getPageTemplates'),
	'eval'                    => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['mobilehead'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mobilehead'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'textarea',
	'eval'                    => array('style'=>'height:60px;', 'preserveTags'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['mobilemootools'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mobilemootools'],
	'exclude'                 => true,
	'filter'                  => true,
	'search'                  => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout', 'getMooToolsTemplates'),
	'eval'                    => array('multiple'=>true)
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['mobilescript'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mobilescript'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'textarea',
	'eval'                    => array('style'=>'height:120px;', 'preserveTags'=>true)
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['tabletstylesheet'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['tabletstylesheet'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout', 'getStyleSheets'),
	'eval'                    => array('multiple'=>true)
);
		
$GLOBALS['TL_DCA']['tl_layout']['fields']['tabletmodules'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['tabletmodules'],
	'exclude'                 => true,
	'inputType'               => 'moduleWizard'
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['tablettemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['tablettemplate'],
	'exclude'                 => true,
	'default'				  => 'fe_mobile',
	'filter'                  => true,
	'search'                  => true,
	'sorting'                 => true,
	'flag'                    => 11,
	'inputType'               => 'select',
	'options_callback'        => array('tl_layout', 'getPageTemplates'),
	'eval'                    => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['tablethead'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['tablethead'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'textarea',
	'eval'                    => array('style'=>'height:60px;', 'preserveTags'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['tabletmootools'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['tabletmootools'],
	'exclude'                 => true,
	'filter'                  => true,
	'search'                  => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout', 'getMooToolsTemplates'),
	'eval'                    => array('multiple'=>true)
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['tabletscript'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['tabletscript'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'textarea',
	'eval'                    => array('style'=>'height:120px;', 'preserveTags'=>true)
);
		


?>