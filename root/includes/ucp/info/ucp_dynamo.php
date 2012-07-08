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
class ucp_dynamo_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_dynamo',
			'title'		=> 'UCP_DYNAMO',
			'version'	=> '0.9.0',
			'modes'		=> array(
				//Gotta fix the below
				'edit'	=> array('title' => 'Edit avatar', 'auth' => 'acl_u_dynamo', 'cat' => array('UCP_PROFILE')))
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
