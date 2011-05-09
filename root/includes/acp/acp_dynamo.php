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
		global $phpbb_root_path, $db, $phpEx, $auth, $user, $template, $config;
		
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
				// the get variables for the various modes
				$add_get = request_var('add', 0);
				$edit_get = request_var('edit', 0);
				$delete_get = request_var('delete', 0);
				
				// If we need to add a layer, the add get var will be 1
				if ($add_get == 1)
				{
					// If the form was submitted, process the shit and stop
					if ($submit)
					{
						$desired_name = request_var('dynamo_layer_name', '');
						$desired_desc = request_var('dynamo_layer_desc', '');
						$desired_position = request_var('dynamo_layer_position', 0);
						$desired_mandatory = request_var('dynamo_layer_mandatory', 0);
						$desired_default = 0; // Because it's a new layer, so it has no items
						
						// Handle the positions ... make this a different function later?
						// The desired position will be one less than the actual position it should get
						// So if $desired_position is 0, set it to 1 ... confusing, might change it later
						
						// First, update all the positions > $desired_position
						$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
								SET dynamo_layer_position = dynamo_layer_position + 1
								WHERE dynamo_layer_position > $desired_position";
						$db->sql_query($sql);
						
						$actual_position = $desired_position + 1;
						
						// Now add it to the database - the ID should be auto_incremented
						$sql = "INSERT INTO " . DYNAMO_LAYERS_TABLE . " (dynamo_layer_name, dynamo_layer_desc, dynamo_layer_default, dynamo_layer_mandatory, dynamo_layer_position)
								VALUES ('$desired_name', '$desired_desc', $desired_default, $desired_mandatory, $actual_position)";
						$db->sql_query($sql);
							
						trigger_error($user->lang['ACP_DYNAMO_ADDED_LAYER'] . adm_back_link($this->u_action));
					}
					
					// Uses the same template file as edit, because that makes sense
					$this_template = 'acp_dynamo_layers_edit';
					
					// Create the dropdown menu for the position
					$sql = "SELECT dynamo_layer_name, dynamo_layer_position
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);
					
					$position_dropdown = '<select name="dynamo_layer_position">';
					while ($row = $db->sql_fetchrow($result))
					{
						$position_dropdown .= '<option value="' . $row['dynamo_layer_position'] . '">';
					
						$position_dropdown .= ($previous_layer == '') ? 'At the very top' : 'Immediately below ' . $previous_layer;
						
						$position_dropdown .= '</option>';
						$previous_layer = $row['dynamo_layer_name'];
						$last_position = $row['dynamo_layer_position'] - 1; // keeps updating
					}
					
					// Add this to the end, always, so there is at least one option
					$position_dropdown .= '<option value="' . $last_position . '">At the very bottom';
					$position_dropdown .= ($previous_layer == '') ? '' : ' - immediately below ' . $previous_layer;
					$position_dropdown .= '</option></select>';
					
					$template_vars = array(
						'LAYER_ADD_EDIT'	=> 'Add a new layer', // the page title basically, change this later
						'MODE_DESCRIPTION'	=> 'Add a layer',
						'LAYER_NAME'		=> request_var('dynamo_layer_name', ''), // from the quick "add layer" form thing
						'POSITION_DROPDOWN'	=> $position_dropdown,
						'DEFAULT_DROPDOWN'	=> 'You can add items after creating the layer',
					);
				}
				else if ($edit_get > 0)
				{
					// If the form was submitted, process the shit and stop
					if ($submit)
					{
						// Get the GET vars that we'll be updating the layer with
						$desired_position = request_var('dynamo_layer_position', 0);
						$desired_name = request_var('dynamo_layer_name', '');
						$desired_desc = request_var('dynamo_layer_desc', '');
						$desired_mandatory = request_var('dynamo_layer_mandatory', 0);
						$desired_default = request_var('dynamo_default_item', 0);
						
						// First get the old position from the db
						$sql = "SELECT dynamo_layer_position
								FROM " . DYNAMO_LAYERS_TABLE . "
								WHERE dynamo_layer_id = $edit_get";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$old_position = $row['dynamo_layer_position'];
						
						// If the position was changed, then edit the other layers' positions
						// It's not any sort of key or index so this is safe
						if ($desired_position > $old_position)
						{
							// If the new position is greater, move the ones in between up
							$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
									SET dynamo_layer_position = dynamo_layer_position - 1
									WHERE dynamo_layer_position > $old_position
									AND dynamo_layer_position <= $desired_position";
							$db->sql_query($sql);
						}
						else if ($desired_position < $old_position)
						{
							// Old position is greater, so moving up, move in-betweens down
							$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
									SET dynamo_layer_position = dynamo_layer_position + 1
									WHERE dynamo_layer_position >= $desired_position
									AND dynamo_layer_position < $old_position";
							$db->sql_query($sql);
							// reuse this code for the adding one
						}
						
						// Ugh so many db queries
						$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
								SET dynamo_layer_name = '$desired_name',
									dynamo_layer_desc = '$desired_desc',
									dynamo_layer_mandatory = $desired_mandatory,
									dynamo_layer_default = $desired_default,
									dynamo_layer_position = $desired_position
								WHERE dynamo_layer_id = $edit_get";
						$db->sql_query($sql);
						
						trigger_error($user->lang['ACP_DYNAMO_EDITED_LAYER'] . adm_back_link($this->u_action));
					}
					
					// If we need to edit a layer, the edit get var will be > 0 (will be the ID)
					$this_template = 'acp_dynamo_layers_edit';
					
					// Get the information for this layer from the db
					$sql = "SELECT l.dynamo_layer_id, l.dynamo_layer_name, l.dynamo_layer_desc, l.dynamo_layer_position, l.dynamo_layer_mandatory, l.dynamo_layer_default, i.dynamo_item_name
							FROM " . DYNAMO_LAYERS_TABLE . " l
							LEFT JOIN " . DYNAMO_ITEMS_TABLE . " i
							ON l.dynamo_layer_default = i.dynamo_item_id
							WHERE dynamo_layer_id = $edit_get";
					$result = $db->sql_query($sql);
					$layer = $db->sql_fetchrow($result);
					$layer_name = $layer['dynamo_layer_name'];
					
					// Now get the information for all the layers, for the position dropdown menu
					$sql = "SELECT dynamo_layer_id, dynamo_layer_name, dynamo_layer_position
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);
					
					$position_dropdown = '<select name="dynamo_layer_position">';
					$previous_layer = '';
					while ($row = $db->sql_fetchrow($result))
					{
						$position_layer_id = $row['dynamo_layer_id'];
						
						$position_dropdown .= '<option value="' . $row['dynamo_layer_position'] . ' "';
						// If it's this layer, indicate that, and make it selected
						$position_dropdown .= ($position_layer_id == $edit_get) ? ' selected="selected">Keep it where it is - ' : '>';
						
						$position_dropdown .= ($previous_layer == '') ? 'At the very top' : 'Immediately below ' . $previous_layer;
						
						$position_dropdown .= '</option>';
						$previous_layer = $row['dynamo_layer_name'];
					}
					$position_dropdown .= '</select>';
					
					// Now get the items associated with this layer
					// Three db queries isn't fun, try to optimise this later
					$sql = "SELECT dynamo_item_id, dynamo_item_name
							FROM " . DYNAMO_ITEMS_TABLE . "
							WHERE dynamo_item_layer = $edit_get
							ORDER BY dynamo_item_id ASC";
					$result = $db->sql_query($sql);
					
					// Make the dropdown for the default item selection
					$default_dropdown = '<select name="dynamo_layer_default">';
					$num_items = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$num_items++;
						$default_dropdown .= '<option value="' . $row['dynamo_item_id'] . '">' . $row['dynamo_item_name'] . '</option>';
					}
					$default_dropdown .= '</select>';
					
					$template_vars = array(
						// the page title basically, change this later (language constants etc)
						'LAYER_ADD_EDIT'	=> 'Editing the layer ' . $layer_name,
						'LAYER_NAME'		=> $layer_name,
						'LAYER_DESC'		=> $layer['dynamo_layer_desc'],
						'MODE_DESCRIPTION'	=> 'Edit a layer',
						'POSITION_DROPDOWN'	=>	$position_dropdown,
						'LAYER_MANDATORY'	=> $layer['dynamo_layer_mandatory'],
						// Move this shit to the template file someday
						'DEFAULT_DROPDOWN'	=> ($num_items > 0) ? $default_dropdown : 'No items for this layer',
					);
				}
				else if ($delete_get > 0)
				{
					// If we need to delete a layer, the delete get var will be > 0 (will be the ID)
					$sql = "DELETE FROM " . DYNAMO_LAYERS_TABLE . "
							WHERE dynamo_layer_id = $delete_get";
					$db->sql_query($sql);
					
					// Now set the associated items to an item ID of 0 (uncategorised)
					$sql = "UPDATE " . DYNAMO_ITEMS_TABLE . "
							SET dynamo_item_layer = 0
							WHERE dynamo_item_layer = $delete_get";
					$db->sql_query($sql);
					
					// Need a confirm box in the future
					 
					trigger_error($user->lang['ACP_DYNAMO_DELETED_LAYER'] . adm_back_link($this->u_action));
				}
				else
				{
					// Else, we just need to show all the layers
					$this_template = 'acp_dynamo_layers';
					$this_title = 'ACP_DYNAMO_LAYERS';
				
					// Left join so that even if there is no default_item we still get results lol
					$sql = "SELECT l.dynamo_layer_id, l.dynamo_layer_name, l.dynamo_layer_desc, l.dynamo_layer_position, l.dynamo_layer_mandatory, l.dynamo_layer_default, i.dynamo_item_name
							FROM " . DYNAMO_LAYERS_TABLE . " l
							LEFT JOIN " . DYNAMO_ITEMS_TABLE . " i
							ON l.dynamo_layer_default = i.dynamo_item_id
							ORDER BY l.dynamo_layer_position DESC";
					$result = $db->sql_query($sql);
				
					while ($row = $db->sql_fetchrow($result))
					{
						// Get all the layers from the database
						$layer_id = $row['dynamo_layer_id'];
						$template->assign_block_vars('layers', array(
							'LAYER_ID' 			=> $layer_id,
							'LAYER_NAME'		=> $row['dynamo_layer_name'],
							'LAYER_DESC'		=> $row['dynamo_layer_desc'],
							'LAYER_POSITION'	=> $row['dynamo_layer_position'],
							'LAYER_MANDATORY'	=> ($row['dynamo_layer_mandatory']) ? 'Yes' : 'No',
							'DEFAULT_ITEM'		=> ($row['dynamo_layer_default'] == 0) ? 'None' : '<strong>' . $row['dynamo_item_name'] . '</strong>',
							'U_EDIT'			=> $this->u_action . '&amp;edit=' . $layer_id,
							'U_DELETE'			=> $this->u_action . '&amp;delete=' . $layer_id,
							)
						);
					}
					
					// Regular template variables (just one lol)
					$template_vars = array(
							'U_ADD_ACTION' 		=> $this->u_action . '&amp;add=1', // Form for adding a new layer
					);
				}
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
