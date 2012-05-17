<?php
/**
*
* @package Ultimate Points Version Check
* @version $Id: ultimate_points_check_version.php 594 2009-11-18 09:34:38Z femu $
* @copyright (c) 2009 wuerzi & femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package mod_version_check
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class ultimate_points_check_version
{
	function version()
	{
		global $config;

		return array(
			'author'	=> 'femu',
			'title'		=> 'Ultimate Points',
			'tag'		=> 'ultimate_points',
			'version'	=> '1.0.6',
			'file'		=> array('die-muellers.org', 'updatecheck', 'ultimate_points.xml'),
		);
	}
}

?>