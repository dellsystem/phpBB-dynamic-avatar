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
 * @package acp
 */
class acp_dynamo
{
	function main($id, $mode)
	{
		global $phpbb_root_path, $phpEx, $auth, $user;
		
		$user->add_lang('mods/dynamo/acp');
		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;
		
		switch($mode)
		{
			case 'overview':
				$this->tpl_name = 'acp_dynamo_overview';
				$this->page_title = 'ACP_DYNAMO_OVERVIEW';
			break;
			case 'settings':
				$this->tpl_name = 'acp_dynamo_settings';
				$this->page_title = 'ACP_DYNAMO_SETTINGS';
			break;
			case 'layers':
				$this->tpl_name = 'acp_dynamo_layers';
				$this->page_title = 'ACP_DYNAMO_LAYERS';
			break;
			case 'items':
				$this->tpl_name = 'acp_dynamo_items';
				$this->page_title = 'ACP_DYNAMO_ITEMS';
			break;
			case 'users':
				$this->tpl_name = 'acp_dynamo_users';
				$this->page_title = 'ACP_DYNAMO_USERS';
			break;
		}
	}
}
?>
