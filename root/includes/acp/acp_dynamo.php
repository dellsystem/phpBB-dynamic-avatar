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
		global $phpbb_root_path, $phpEx, $auth, $user, $template, $config;
		
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
				
				if ($submit)
				{
					// Update the config values ... better way of doing this?
					add_log('admin', 'LOG_DYNAMO_SETTINGS');
					set_config('dynamo_enabled', request_var('dynamo_enabled', 0));
					set_config('dynamo_use_points', request_var('dynamo_use_points', 0));
					set_config('dynamo_change_base', request_var('dynamo_change_base', 0));
					set_config('dynamo_mandatory', request_var('dynamo_mandatory', 0));
					
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				
				$template_vars = array(
					'U_ACTION' 				=> $this->u_action,
					'DYNAMO_ENABLED'		=> $config['dynamo_enabled'],
					'DYNAMO_USE_POINTS'		=> $config['dynamo_use_points'],
					'DYNAMO_CHANGE_BASE'	=> $config['dynamo_change_base'],
					'DYNAMO_MANDATORY'		=> $config['dynamo_mandatory'],
				);
				
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
