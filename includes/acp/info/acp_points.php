<?php
/** 
*
* @package acp
* @version $Id: acp_points.php 447 2009-07-19 12:42:31Z femu $
* @copyright (c) 2009 wuerzi & femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/
class acp_points_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_points',
			'title'		=> 'ACP_POINTS',
			'version'	=> '1.0.4',
			'modes'		=> array(
				'points'		=> array('title' => 'ACP_POINTS_INDEX_TITLE', 'auth' => 'acl_a_points', 'cat' => array('ACP_POINTS')),
				'forumpoints'	=> array('title' => 'ACP_POINTS_FORUM_TITLE', 'auth' => 'acl_a_points', 'cat' => array('ACP_POINTS')),
                'bank'			=> array('title' => 'ACP_POINTS_BANK_TITLE', 'auth' => 'acl_a_points', 'cat' => array('ACP_POINTS')),
                'lottery'		=> array('title' => 'ACP_POINTS_LOTTERY_TITLE', 'auth' => 'acl_a_points', 'cat' => array('ACP_POINTS')),
				'robbery'		=> array('title' => 'ACP_POINTS_ROBBERY_TITLE', 'auth' => 'acl_a_points', 'cat' => array('ACP_POINTS')),
				'userguide'		=> array('title' => 'ACP_POINTS_USERGUIDE_TITLE', 'auth' => 'acl_a_points', 'cat' => array('ACP_POINTS')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>