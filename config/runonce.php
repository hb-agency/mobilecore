<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Winans Creative 2010
 * @author     Blair Winans <blair@winanscreative.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class MobileRunonce extends Controller
{

  /**
   * Initialize the object
   */
  public function __construct()
  {
    parent::__construct();

    $this->import((TL_MODE=='BE' ? 'BackendUser' : 'FrontendUser'), 'User');
    $this->import('Database');
  }


  /**
   * Run the controller
   */
  public function run()
  {
	// tl_content.mobileonly has been renamed to tl_content.mobilehide
    if ($this->Database->fieldExists('mobileonly', 'tl_content'))
    {
      $this->Database->query("ALTER TABLE tl_content CHANGE COLUMN mobileonly mobilehide char(1) NOT NULL default ''");
    }
    
    // tl_content.regularonly has been renamed to tl_content.regularhide
    if ($this->Database->fieldExists('mobileonly', 'tl_content'))
    {
      $this->Database->query("ALTER TABLE tl_content CHANGE COLUMN regularonly regularhide char(1) NOT NULL default ''");
    }
    
    // now we reverse the values since we are hiding instead of displaying
    $objContent = $this->Database->execute("SELECT * FROM tl_content WHERE mobilehide=1 OR regularhide=1");
   	while($objContent->next())
   	{
   		if($objContent->mobilehide)
   		{
   			$this->Database->query("UPDATE tl_content SET regularhide=1,mobilehide=0 WHERE id={$objContent->id}");
   		}
   		else
   		{
   			$this->Database->query("UPDATE tl_content SET regularhide=0,mobilehide=1 WHERE id={$objContent->id}");
   		}
   	}
   
  }
  
}


/**
 * Instantiate controller
 */
$objIsotope = new MobileRunonce();
$objIsotope->run();