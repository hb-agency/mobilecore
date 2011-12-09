-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************


-- 
-- Table `tl_layout`
-- 

CREATE TABLE `tl_layout` (
  `mobilestylesheet` blob NULL,
  `mobilemodules` blob NULL,
  `mobiletemplate` varchar(64) NOT NULL default '',
  `mobilehead` text NULL,
  `mobilemootools` text NULL,
  `mobilescript` text NULL,
  `tabletstylesheet` blob NULL,
  `tabletmodules` blob NULL,
  `tablettemplate` varchar(64) NOT NULL default '',
  `tablethead` text NULL,
  `tabletmootools` text NULL,
  `tabletscript` text NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_content`
-- 

CREATE TABLE `tl_content` (
  `tablethide` char(1) NOT NULL default '',
  `mobilehide` char(1) NOT NULL default '',
  `regularhide` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `mobileDns` varchar(255) NOT NULL default '',
  `tabletDns` varchar(255) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
