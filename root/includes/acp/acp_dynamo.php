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
	var $u_action;
	
	function main($id, $mode)
	{
		global $phpbb_root_path, $phpEx, $auth, $user, $template;
		
		$user->add_lang('mods/dynamo/acp');
		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;
		
		switch($mode)
		{
			case 'overview':
				$this_template = 'acp_dynamo_overview';
				$this_title = 'ACP_DYNAMO_OVERVIEW';
				$template_vars = array();
			break;
			case 'settings':
				$this_template = 'acp_dynamo_settings';
				$this_title = 'ACP_DYNAMO_SETTINGS';
				$template_vars = array();
				
			break;
			case 'layers':
				$this_template = 'acp_dynamo_layers';
				$this_title = 'ACP_DYNAMO_LAYERS';
				$template_vars = array();
			break;
			case 'items':
				$this_template = 'acp_dynamo_items';
				$this_title = 'ACP_DYNAMO_ITEMS';
				$template_vars = array();
			break;
			case 'users':
				$this_template = 'acp_dynamo_users';
				$this_title = 'ACP_DYNAMO_USERS';
				$template_vars = array();
			break;
		}
		$this->tpl_name = $this_template;
		$this->page_title = $this_title;
		$template->assign_vars($template_vars);
	}
}
?>
