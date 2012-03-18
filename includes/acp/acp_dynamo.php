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

	// Get the path for an item's image from its layer ID and item ID
	// Returns a path relative to the board root (e.g. `image/dynamo/
	// Depends on $config['dynamo_image_fp'];
	// Modes: entire path, just the image filename, just the dirs
	function get_item_image_path($mode = 'entire', $layer_id = 0, $item_id = 0)
	{
		global $config, $phpbb_root_path;
		$filename = $layer_id . '-' . $item_id . '.png';
		$dirs = $phpbb_root_path . $config['dynamo_image_fp'];

		switch ($mode)
		{
			case 'entire':
				return $dirs . '/' . $filename;
			case 'filename':
				return $filename;
			case 'dirs':
				return $dirs;
		}
	}

	function move_layer($layer_id, $desired_position = 0, $step = 0)
	{
		global $db;
		// First get this layer's current position
		$sql = "SELECT dynamo_layer_position
				FROM " . DYNAMO_LAYERS_TABLE . "
				WHERE dynamo_layer_id = $layer_id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$old_position = $row['dynamo_layer_position'];

		// If $desired_position is 0, use the "step"
		// For moving up/down by 1 when you don't know the actual position
		if ($desired_position == 0)
		{
			$desired_position = $old_position + $step;
		}

		// Move all the ones between
		if ($desired_position != $old_position)
		{
			$lower_bound = min($desired_position - 1, $old_position);
			$upper_bound = max($desired_position + 1, $old_position);
			$op = ($desired_position > $old_position) ? '-' : '+';
			$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
					SET dynamo_layer_position = dynamo_layer_position $op 1
					WHERE dynamo_layer_position > $lower_bound
					AND dynamo_layer_position < $upper_bound";
			$db->sql_query($sql);

			// Now update the layer itself
			$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
					SET dynamo_layer_position = $desired_position
					WHERE dynamo_layer_id = $layer_id";
			$db->sql_query($sql);
		}
	}

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

				// Figure out the number of users
				$sql = "SELECT COUNT(DISTINCT dynamo_user_id) as num_users
						FROM " . DYNAMO_USERS_TABLE;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$num_users = $row['num_users'];

				// Figure out the number of items
				$sql = "SELECT COUNT(dynamo_item_id) as num_items
						FROM " . DYNAMO_ITEMS_TABLE;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$num_items = $row['num_items'];

				// Figure out the number of layers
				$sql = "SELECT COUNT(dynamo_layer_id) as num_layers
						FROM " . DYNAMO_LAYERS_TABLE;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$num_layers = $row['num_layers'];

				$template_vars = array(
					'DYNAMO_MOD_ENABLED'	=> $config['dynamo_enabled'], // does this even do anything?
					'DYNAMO_NUM_USERS'		=> $num_users,
					'DYNAMO_NUM_ITEMS'		=> $num_items,
					'DYNAMO_NUM_LAYERS'		=> $num_layers,
					'L_TITLE'				=> $user->lang['DYNAMO_OVERVIEW'],
					'L_TITLE_EXPLAIN'		=> $user->lang['DYNAMO_OVERVIEW_EXPLAIN'],
					'DYNAMO_WIDTH'			=> $config['dynamo_width'],
					'DYNAMO_HEIGHT'			=> $config['dynamo_height'],
					'DYNAMO_IMAGE_FP'		=> $config['dynamo_image_fp'],
					'DYNAMO_AVATAR_FP'		=> $config['dynamo_avatar_fp'],
				);
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
					set_config('dynamo_width', request_var('dynamo_width', 0));
					set_config('dynamo_height', request_var('dynamo_height', 0));
					set_config('dynamo_image_fp', request_var('dynamo_image_fp', ''));
					set_config('dynamo_avatar_fp', request_var('dynamo_avatar_fp', ''));

					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$template_vars = array(
					'L_TITLE'				=> $user->lang['SETTINGS'],
					'L_TITLE_EXPLAIN'		=> $user->lang['DYNAMO_SETTINGS_EXPLAIN'],
					'U_ACTION' 				=> $this->u_action,
					'DYNAMO_ENABLED'		=> $config['dynamo_enabled'],
					'DYNAMO_USE_POINTS'		=> $config['dynamo_use_points'],
					'DYNAMO_CHANGE_BASE'	=> $config['dynamo_change_base'],
					'DYNAMO_MANDATORY'		=> $config['dynamo_mandatory'],
					'DYNAMO_WIDTH'			=> $config['dynamo_width'],
					'DYNAMO_HEIGHT'			=> $config['dynamo_height'],
					'DYNAMO_IMAGE_FP'		=> $config['dynamo_image_fp'],
					'DYNAMO_AVATAR_FP'		=> $config['dynamo_avatar_fp'],
				);

			break;
			case 'layers':
				$this_title = 'ACP_DYNAMO_LAYERS';
				// the get variables for the various modes
				$add_get = request_var('add', 0);
				$edit_get = request_var('edit', 0);
				$delete_get = request_var('delete', 0);
				$move_down = request_var('move_down', 0);
				$move_up = request_var('move_up', 0);

				// If we need to add a layer, the add get var will be 1
				if ($add_get == 1)
				{
					// If the form was submitted, process the shit and stop
					if ($submit)
					{
						// Get the maximum position first
						$sql = "SELECT MAX(dynamo_layer_position) as max_position
								FROM " . DYNAMO_LAYERS_TABLE;
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$max_position = $row['max_position'];
								
						$desired_name = request_var('dynamo_layer_name', '');
						$desired_desc = request_var('dynamo_layer_desc', '');
						$desired_position = request_var('dynamo_layer_position', 0);
						$desired_mandatory = request_var('dynamo_layer_mandatory', 0);
						$desired_default = 0; // Because it's a new layer, so it has no items
						$actual_position = $max_position + 1;

						// Now add it to the database - the ID should be auto_incremented
						$insert_array = array(
							'dynamo_layer_name'			=> $desired_name,
							'dynamo_layer_desc'			=> $desired_desc,
							'dynamo_layer_default'		=> $desired_default,
							'dynamo_layer_mandatory'	=> $desired_mandatory,
							'dynamo_layer_position'		=> $actual_position,
						);
						$sql = "INSERT INTO " . DYNAMO_LAYERS_TABLE . " " . $db->sql_build_array('INSERT', $insert_array);
						$db->sql_query($sql);
						$layer_id = $db->sql_nextid();
						
						// Move all the other layers, and set this to the right position
						// If it's 0, it means "below the current 1", so the pos should be 1
						$this->move_layer($layer_id, $desired_position + 1);

						trigger_error($user->lang['ACP_DYNAMO_ADDED_LAYER'] . adm_back_link($this->u_action));
					}

					// Uses the same template file as edit, because that makes sense
					$this_template = 'acp_dynamo_layers_edit';

					// Create the dropdown menu for the position
					$sql = "SELECT dynamo_layer_name, dynamo_layer_position
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);

					$previous_layer = '';

					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('layers_dropdown', array(
							'POSITION'		=> $row['dynamo_layer_position'],
							'PREVIOUS'		=> $previous_layer,
							'POSITION_TEXT'	=> ($previous_layer == '') ? $user->lang['LAYER_AT_TOP'] : sprintf($user->lang['IMMEDIATELY_BELOW'], $previous_layer),
						));

						$previous_layer = $row['dynamo_layer_name'];
						$last_position = $row['dynamo_layer_position'] - 1;
					}

					// Add this to the end, always, so there is at least one option
					$l_last_layer = $user->lang['LAYER_AT_BOTTOM'];
					$l_last_layer .= ($previous_layer == '') ? '' : ' - ' . sprintf($user->lang['IMMEDIATELY_BELOW'], $previous_layer);

					$template_vars = array(
						'L_LAST_LAYER'		=> $l_last_layer,
						'LAST_POSITION'		=> $last_position, // used for the dropdown
						'L_TITLE'			=> $user->lang['ADD_LAYER'],
						'L_TITLE_EXPLAIN'	=> $user->lang['ADD_LAYER_EXPLAIN'],
						'LAYER_NAME'		=> request_var('dynamo_layer_name', ''), // from the quick "add layer" form thing
						'IN_CREATE'			=> true,
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
						$desired_default = request_var('dynamo_layer_default', 0);

						// First update all non-position things
						$update_array = array(
							'dynamo_layer_name'			=> $desired_name,
							'dynamo_layer_desc'			=> $desired_desc,
							'dynamo_layer_mandatory'	=> $desired_mandatory,
							'dynamo_layer_default'		=> $desired_default,
						);
						$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
								SET " . $db->sql_build_array('UPDATE', $update_array) . "
								WHERE dynamo_layer_id = $edit_get";
						$db->sql_query($sql);

						// Now move it
						$this->move_layer($edit_get, $desired_position + 1);

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
					$layer_mandatory = $layer['dynamo_layer_mandatory'];
					$layer_default = $layer['dynamo_layer_default'];

					// Now get the information for all the layers, for the position dropdown menu
					$sql = "SELECT dynamo_layer_id, dynamo_layer_name, dynamo_layer_position
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);

					$previous_layer = '';
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('layers_dropdown', array(
							'POSITION'		=> $row['dynamo_layer_position'],
							'PREVIOUS'		=> $previous_layer,
							'POSITION_TEXT'	=> ($previous_layer == '') ? $user->lang['LAYER_AT_TOP'] : sprintf($user->lang['IMMEDIATELY_BELOW'], $previous_layer),
						));

						$previous_layer = $row['dynamo_layer_name'];
						$last_position = $row['dynamo_layer_position'] - 1;
					}

					$l_last_layer = $user->lang['LAYER_AT_BOTTOM'];
					$l_last_layer .= ($previous_layer == '') ? '' : ' - ' . sprintf($user->lang['IMMEDIATELY_BELOW'], $previous_layer);

					// Now get the items associated with this layer
					// Three db queries isn't fun, try to optimise this later
					$sql = "SELECT dynamo_item_id, dynamo_item_name
							FROM " . DYNAMO_ITEMS_TABLE . "
							WHERE dynamo_item_layer = $edit_get
							ORDER BY dynamo_item_id ASC";
					$result = $db->sql_query($sql);

					// If the layer is NOT mandatory, let there be an option to choose no default item
					// Self note: layers that have no item don't show up anyway so this is okay
					// Moved to template file
					$num_items = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$num_items++;
						$template->assign_block_vars('items_dropdown', array(
							'ITEM_ID'		=> $row['dynamo_item_id'],
							'ITEM_NAME'		=> $row['dynamo_item_name'],
						));
					}

					$template_vars = array(
						'CURRENT_ITEM'		=> $layer_default,
						'L_LAST_LAYER'		=> $l_last_layer,
						'L_TITLE'			=> sprintf($user->lang['EDITING_LAYER'], $layer_name),
						'L_TITLE_EXPLAIN'	=> $user->lang['EDITING_LAYER_EXPLAIN'],
						'LAYER_NAME'		=> $layer_name,
						'LAYER_DESC'		=> $layer['dynamo_layer_desc'],
						'LAYER_MANDATORY'	=> $layer['dynamo_layer_mandatory'],
						'LAYER_HAS_ITEMS'	=> $num_items > 0,
						'CURRENT_POSITION'	=> $layer['dynamo_layer_position'],
						'LAST_POSITION'		=> $last_position,
					);
				}
				else if ($delete_get > 0)
				{
					// Do the confirm box thing whatever before deleting
					if (confirm_box(true))
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
						 
						trigger_error($user->lang['ACP_DYNAMO_DELETED_LAYER'] . adm_back_link($this->u_action));
					}
					else
					{
						$s_hidden_fields = build_hidden_fields(array(
							'submit'    => true,
							)
						);

						confirm_box(false, $user->lang['ACP_DYNAMO_DELETE_LAYER'], $s_hidden_fields);
					}
				}
				else
				{
					// Else, we just need to show all the layers
					$this_template = 'acp_dynamo_layers';
					$this_title = 'ACP_DYNAMO_LAYERS';

					// Check if we need to move something up or down
					if ($layer_id = max($move_down, $move_up))
					{
						$step = ($move_down > 0) ? -1 : 1;
						$this->move_layer($layer_id, 0, $step);
					}

					// Left join so that even if there is no default_item we still get results lol
					$sql = "SELECT l.dynamo_layer_id, l.dynamo_layer_name, l.dynamo_layer_desc, l.dynamo_layer_position, l.dynamo_layer_mandatory, l.dynamo_layer_default, i.dynamo_item_name
							FROM " . DYNAMO_LAYERS_TABLE . " l
							LEFT JOIN " . DYNAMO_ITEMS_TABLE . " i
							ON l.dynamo_layer_default = i.dynamo_item_id
							ORDER BY l.dynamo_layer_position DESC";
					$result = $db->sql_query($sql);
					
					// For disabling move up/move down icons
					$min_position = 0;
					$max_position = 0;

					while ($row = $db->sql_fetchrow($result))
					{
						$position = $row['dynamo_layer_position'];
						$max_position = ($position > $max_position) ? $position : $max_position;
						$min_position = ($position < $min_position || $min_position == 0) ? $position : $min_position;
						// Get all the layers from the database
						$layer_id = $row['dynamo_layer_id'];
						$template->assign_block_vars('layers', array(
							'LAYER_ID' 			=> $layer_id,
							'LAYER_NAME'		=> $row['dynamo_layer_name'],
							'LAYER_DESC'		=> $row['dynamo_layer_desc'],
							'LAYER_POSITION'	=> $position,
							'LAYER_MANDATORY'	=> ($row['dynamo_layer_mandatory']) ? 'Yes' : 'No',
							'DEFAULT_ITEM'		=> ($row['dynamo_layer_default'] == 0) ? 'None' : '<strong>' . $row['dynamo_item_name'] . '</strong>',
							'U_EDIT'			=> $this->u_action . '&amp;edit=' . $layer_id,
							'U_DELETE'			=> $this->u_action . '&amp;delete=' . $layer_id,
							'U_MOVE_DOWN'		=> $this->u_action . '&amp;move_down=' . $layer_id,
							'U_MOVE_UP'			=> $this->u_action . '&amp;move_up=' . $layer_id,
							)
						);
					}

					$template_vars = array(
						'MAX_POSITION'		=> $max_position,
						'MIN_POSITION'		=> $min_position,
						'L_TITLE'			=> $user->lang['DYNAMO_LAYERS'],
						'L_TITLE_EXPLAIN'	=> $user->lang['DYNAMO_LAYERS_EXPLAIN'],
						'U_ADD_ACTION' 		=> $this->u_action . '&amp;add=1', // Form for adding a new layer
					);
				}
			break;
			case 'items':
				$this_title = 'ACP_DYNAMO_ITEMS';

				$add_item = request_var('add', 0);
				$edit_item_id = request_var('edit', 0);
				$delete_item_id = request_var('delete', 0);

				// If add_item is 1, then, add a new item
				if ($add_item)
				{
					// If the form was submitted, create this item
					if ($submit)
					{
						// The necessary post vars for this new item
						$desired_name = request_var('dynamo_item_name', '');
						$desired_desc = request_var('dynamo_item_desc', '');
						$desired_layer = request_var('dynamo_item_layer', 0);

						// First get the next item ID from the database
						$sql = "SELECT dynamo_item_id
								FROM " . DYNAMO_ITEMS_TABLE . "
								ORDER BY dynamo_item_id DESC
								LIMIT 1";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);

						$item_id = $row['dynamo_item_id'] + 1;

						// Now upload the file - include functions_upload.php
						// Init upload class
						include_once($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
						// Must be in gif or png for transparency? figure this out later
						// Need config settings for avatar width and height later
						$upload = new fileupload('AVATAR_', array('gif', 'png'), false, 100, 100, 120, 120);

						// Maybe edit functions_uploaded.php or something ... or maybe not
						if (!empty($_FILES['uploadfile']['name']))
						{
							$file = $upload->form_upload('uploadfile');
							$file->realname = $this->get_item_image_filepath('filename', $desired_layer, $item_id);

							// Move file and overwrite any existing image
							$file->move_file($this->get_item_image_filepath('dirs'), true);
						}

						$insert_array = array(
							'dynamo_item_id'	=> $item_id, // need this to avoid discrepancies lol
							'dynamo_item_name' 	=> $desired_name,
							'dynamo_item_desc'	=> $desired_desc,
							'dynamo_item_layer'	=> $desired_layer
						);

						// Might as well not ignore the ID since we have it
						$sql = "INSERT INTO " . DYNAMO_ITEMS_TABLE . " " . $db->sql_build_array('INSERT', $insert_array);
						$db->sql_query($sql);

						trigger_error($user->lang['ACP_DYNAMO_ADDED_ITEM'] . adm_back_link($this->u_action));
					}
					$this_template = 'acp_dynamo_items_edit';

					// Make the layer dropdown - get all the layers from the db
					$sql = "SELECT dynamo_layer_name, dynamo_layer_id
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('layer_dropdown', array(
							'LAYER_ID'		=> $row['dynamo_layer_id'],
							'LAYER_NAME'	=> $row['dynamo_layer_name'],
						));
					}

					$template_vars = array(
						'L_TITLE'			=> $user->lang['ADDING_ITEM'],
						'L_TITLE_EXPLAIN'	=> $user->lang['ADDING_ITEM_EXPLAIN'],
						'U_ACTION'			=> $this->u_action . '&amp;add=1',
						'ITEM_NAME'			=> request_var('dynamo_item_name', ''),
					);
				}
				else if ($edit_item_id > 0)
				{
					if ($submit)
					{
						$desired_name = request_var('dynamo_item_name', '');
						$desired_desc = request_var('dynamo_item_desc', '');
						$desired_layer = request_var('dynamo_item_layer', 0);

						// Change the filename to reflect the new layer if necessary
						$sql = "SELECT dynamo_item_layer
								FROM " . DYNAMO_ITEMS_TABLE . "
								WHERE dynamo_item_id = $edit_item_id";
						$result = $db->sql_query($sql);
						$item = $db->sql_fetchrow($result);
						$old_layer = $item['dynamo_item_layer'];

						if ($old_layer != $desired_layer)
						{
							$old_file_name = $this->get_item_image_path('entire', $old_layer, $edit_item_id);
							$new_file_name = $this->get_item_image_path('entire', $desired_layer, $edit_item_id);
							if (!rename($old_file_name, $new_file_name))
							{
								trigger_error("Can't move the image file attached to the item. Please file a bug report." . adm_back_link($this->u_action));
							}
						}

						$update_array = array(
							'dynamo_item_name' 	=> $desired_name,
							'dynamo_item_desc' 	=> $desired_desc,
							'dynamo_item_layer' => $desired_layer
						);

						$sql = "UPDATE " . DYNAMO_ITEMS_TABLE . "
								SET " . $db->sql_build_array('UPDATE', $update_array) . "
								WHERE dynamo_item_id = $edit_item_id";
						$db->sql_query($sql);

						trigger_error($user->lang['ACP_DYNAMO_EDITED_ITEM'] . adm_back_link($this->u_action));
					}
					// Editing the item
					$this_title = 'ACP_DYNAMO_ITEMS_EDIT';
					$this_template = 'acp_dynamo_items_edit';

					// Get the info related to this item
					$sql = "SELECT *
							FROM " . DYNAMO_ITEMS_TABLE . "
							WHERE dynamo_item_id = $edit_item_id";
					$result = $db->sql_query($sql);
					$item = $db->sql_fetchrow($result);

					$item_name = $item['dynamo_item_name'];

					// Make the layer dropdown - get all the layers from the db
					// Make this some sort of helper function later
					$sql = "SELECT dynamo_layer_name, dynamo_layer_id
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('layer_dropdown', array(
							'LAYER_ID'		=> $row['dynamo_layer_id'],
							'LAYER_NAME'	=> $row['dynamo_layer_name'],
						));
					}

					$template_vars = array(
						'CURRENT_LAYER'		=> $item['dynamo_item_layer'],
						'L_TITLE'			=> sprintf($user->lang['EDITING_ITEM'], $item_name),
						'L_TITLE_EXPLAIN'	=> $user->lang['EDITING_ITEM_EXPLAIN'],
						'ITEM_IMAGE'		=> $this->get_item_image_path('entire', $item['dynamo_item_layer'], $item['dynamo_item_id']),
						'ITEM_NAME'			=> $item_name,
						'ITEM_DESC'			=> $item['dynamo_item_desc'],
					);
				}
				else if ($delete_item_id > 0)
				{
				// Do the confirm box thing whatever before deleting
					if (confirm_box(true))
					{
						// Delete the item
						$sql = "DELETE FROM " . DYNAMO_ITEMS_TABLE . "
								WHERE dynamo_item_id = $delete_item_id";
						$db->sql_query($sql);

						// Now see if the layer has it as a default item ... if so, set the default item to 0
						$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
								SET dynamo_layer_default = 0
								WHERE dynamo_layer_default = $delete_item_id";
						// This way, we don't need to do a select query first
						$db->sql_query($sql);

						trigger_error($user->lang['ACP_DYNAMO_DELETED_ITEM'] . adm_back_link($this->u_action));	
					}
					else
					{
						$s_hidden_fields = build_hidden_fields(array(
							'submit'    => true,
							)
						);

						confirm_box(false, $user->lang['ACP_DYNAMO_DELETE_ITEM'], $s_hidden_fields);
					}
				}
				else
				{
					// Just show all the items
					$this_template = 'acp_dynamo_items';

					// Select all the items from the database, left join to get their layer name if applicable
					// Layers start indexing at 1 so a layer of 0 == uncategorised
					$sql = "SELECT i.dynamo_item_id, i.dynamo_item_name, i.dynamo_item_layer, i.dynamo_item_desc, l.dynamo_layer_name, l.dynamo_layer_position
							FROM " . DYNAMO_ITEMS_TABLE . " i
							LEFT JOIN " . DYNAMO_LAYERS_TABLE . " l
							ON i.dynamo_item_layer = l.dynamo_layer_id
							ORDER BY l.dynamo_layer_position DESC";
					$result = $db->sql_query($sql);

					$previous_layer = '';
					$num_layers = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$item_layer = $row['dynamo_item_layer'];
						$new_layer = ($previous_layer != $item_layer);
						$num_layers++;

						// For determining if we need a new row or not
						$num_in_layer = ($new_layer) ? 1 : $num_in_layer + 1;

						$item_id = $row['dynamo_item_id'];

						// Figure out the item's image URL
						$item_image_url = $this->get_item_image_path('entire', $item_layer, $item_id);

						$template->assign_block_vars('item', array(
							'NEW_LAYER'		=> $new_layer,
							'NUM_IN_LAYER'	=> $num_in_layer,
							'ITEM_NAME'		=> $row['dynamo_item_name'],
							'ITEM_IMAGE'	=> $item_image_url,
							'FIRST_LAYER'	=> ($num_layers == 1) ? true : false,
							'U_EDIT'		=> $this->u_action . '&amp;edit=' . $item_id,
							'U_DELETE'		=> $this->u_action . '&amp;delete=' . $item_id,
							'LAYER_NAME'	=> ($item_layer) ? $row['dynamo_layer_name'] : 'Uncategorised')
						);
						$previous_layer = $item_layer;
					}

					$template_vars = array(
						'L_TITLE'				=> $user->lang['DYNAMO_ITEMS'],
						'L_TITLE_EXPLAIN'		=> $user->lang['DYNAMO_ITEMS_EXPLAIN'],
						'U_ADD_ACTION' 			=> $this->u_action . '&amp;add=1', // Form for adding a new item
					);
				}
			break;
			case 'users':
				$this_template = 'acp_dynamo_users';
				$this_title = 'ACP_DYNAMO_USERS';

				// Get all the users who have dynamic avatars. There is probably a better way to do this.
				$sql = "SELECT DISTINCT dynamo_user_id, username, user_avatar
						FROM " . DYNAMO_USERS_TABLE . "
						LEFT JOIN " . USERS_TABLE . "
						ON dynamo_user_id = user_id";
				$result = $db->sql_query($sql);

				$i = 1;
				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('users', array(
						'USERNAME'		=> $row['username'],
						'AVATAR_URL'	=> $row['user_avatar'],
						'INDEX'			=> $i,
					));
					$i++;
				}

				$template_vars = array(
					'L_TITLE'				=> $user->lang['DYNAMO_USERS'],
					'L_TITLE_EXPLAIN'		=> $user->lang['DYNAMO_USERS_EXPLAIN']
				);
			break;
		}
		$this->tpl_name = $this_template;
		$this->page_title = $this_title;
		$template->assign_vars($template_vars);
	}
}
?>
