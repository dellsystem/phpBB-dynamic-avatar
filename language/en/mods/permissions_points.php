<?php
/** 
*
* permissions_points [English]
*
* version $Id: permissions_points.php 406 2009-06-29 16:06:16Z femu $
* copyright (c) 2009 wuerzi & femu
* license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// Adding new category
$lang['permission_cat']['points'] = 'Points';

// Adding the permissions
$lang = array_merge($lang, array(
	'acl_u_use_points'		=> array('lang' => 'Can use Ultimate Points', 'cat' => 'points'),
	'acl_u_use_robbery'		=> array('lang' => 'Can use Robbery Module', 'cat' => 'points'),
	'acl_u_use_bank'		=> array('lang' => 'Can use Bank Module', 'cat' => 'points'),
	'acl_u_use_logs'		=> array('lang' => 'Can use Log Module', 'cat' => 'points'),
	'acl_u_use_lottery'		=> array('lang' => 'Can use Lottery Module', 'cat' => 'points'),
	'acl_u_use_transfer'	=> array('lang' => 'Can use Transfer Module', 'cat' => 'points'),
	'acl_m_chg_points'		=> array('lang' => 'Can change users points', 'cat' => 'points'),
	'acl_m_chg_bank'		=> array('lang' => 'Can change users Bank points', 'cat' => 'points'),
	'acl_a_points'			=> array('lang' => 'Can administrate Ultimate Points', 'cat' => 'points'),
));

?>