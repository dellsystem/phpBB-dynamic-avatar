<?php
/** 
*
* @package Dynamo (Dynamic Avatar MOD for phpBB3)
* @version $Id: acp_dynamo.php ilostwaldo@gmail.com$
* @copyright (c) 2011 dellsystem (www.dellsystem.me)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class acp_dynamo_info
{
	function module()
	{
		global $user;

		return array(
			'filename'	=> 'acp_dynamo',
			'title'		=> 'ACP_DYNAMO',
			'version'	=> '0.0.1',
			'modes'		=> array(
				// Uses acl_a_forum for now because the proper auth field isn't working atm lol
				'overview'		=> array('title' => 'ACP_DYNAMO_OVERVIEW', 'auth' => 'acl_a_dynamo_overview', 'cat' => array('ACP_DYNAMO')),
				'settings'		=> array('title' => 'ACP_DYNAMO_SETTINGS', 'auth' => 'acl_a_dynamo_settings', 'cat' => array('ACP_DYNAMO')),
				'layers'		=> array('title' => 'ACP_DYNAMO_LAYERS', 'auth' => 'acl_a_dynamo_layers', 'cat' => array('ACP_DYNAMO')),
				'items'			=> array('title' => 'ACP_DYNAMO_ITEMS', 'auth' => 'acl_a_dynamo_items', 'cat' => array('ACP_DYNAMO')),
				'users'			=> array('title' => 'ACP_DYNAMO_USERS', 'auth' => 'acl_a_dynamo_users', 'cat' => array('ACP_DYNAMO')),
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
