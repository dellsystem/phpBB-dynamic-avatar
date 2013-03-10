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

	function move_layer($layer_id, $desired_position = 0, $step = 0)
	{
		global $db;
		// First get this layer's current position
		// Also the name, in case we need to add it to the logs
		$sql = "SELECT dynamo_layer_position, dynamo_layer_name
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
			// If this is the case, it should be added to the logs
			$up_or_down = ($step > 0) ? 'up' : 'down';
			add_log('admin', 'LOG_DYNAMO_MOVE_LAYER', $row['dynamo_layer_name'], $up_or_down);
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

		include($phpbb_root_path . 'includes/functions_dynamo.' . $phpEx);
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
					'DYNAMO_POINTS_SUPPORT'	=> $config['dynamo_use_points'],
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

				$config_settings = array(
					// config_var_name		=> default value (for request_var)
					'dynamo_enabled' 		=> 0,
					'dynamo_use_points'		=> 0,
					'dynamo_change_base' 	=> 0,
					'dynamo_mandatory'		=> 0,
					'dynamo_width'			=> 0,
					'dynamo_height'			=> 0,
					'dynamo_image_fp'		=> '',
					'dynamo_avatar_fp'		=> '',
				);

				if ($submit)
				{
					foreach ($config_settings as $var => $default)
					{
						set_config($var, request_var($var, $default));
					}

					add_log('admin', 'LOG_DYNAMO_SETTINGS');

					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$template_vars = array(
					'POINTS_MOD_ENABLED'	=> $config['points_enable'],
					'L_TITLE'				=> $user->lang['SETTINGS'],
					'L_TITLE_EXPLAIN'		=> $user->lang['DYNAMO_SETTINGS_EXPLAIN'],
					'U_ACTION' 				=> $this->u_action,
				);

				// Add the values of the config settings to $template_vars
				foreach ($config_settings as $var => $default)
				{
					$template_vars[strtoupper($var)] = $config[$var];
				}

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

						$desired_name = utf8_normalize_nfc(request_var('dynamo_layer_name', '', true));
						$desired_desc = utf8_normalize_nfc(request_var('dynamo_layer_desc', '', true));
						$desired_position = request_var('dynamo_layer_position', 0);
						$desired_mandatory = request_var('dynamo_layer_mandatory', 0);
						$desired_default = 0; // Because it's a new layer, so it has no items
						$desired_price = request_var('default_price', 0);
						$actual_position = $max_position + 1;

						// Now add it to the database - the ID should be auto_incremented
						$insert_array = array(
							'dynamo_layer_name'			=> $desired_name,
							'dynamo_layer_desc'			=> $desired_desc,
							'dynamo_layer_default'		=> $desired_default,
							'dynamo_layer_mandatory'	=> $desired_mandatory,
							'dynamo_layer_position'		=> $actual_position,
							'default_price'				=> $desired_price,
						);
						$sql = "INSERT INTO " . DYNAMO_LAYERS_TABLE . " " . $db->sql_build_array('INSERT', $insert_array);
						$db->sql_query($sql);
						$layer_id = $db->sql_nextid();

						// Move all the other layers, and set this to the right position
						$this->move_layer($layer_id, $desired_position);

						add_log('admin', 'LOG_DYNAMO_ADD_LAYER', $desired_name);

						trigger_error($user->lang['ACP_DYNAMO_ADDED_LAYER'] . adm_back_link($this->u_action));
					}

					// Uses the same template file as edit, because that makes sense
					$this_template = 'acp_dynamo_layers_edit';

					// Create the dropdown menu for the position
					$sql = "SELECT dynamo_layer_name, dynamo_layer_position
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);

					$top_position = 0;

					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('layers_dropdown', array(
							'POSITION'		=> $row['dynamo_layer_position'],
							'POSITION_TEXT'	=> sprintf($user->lang['IMMEDIATELY_BELOW'], $row['dynamo_layer_name']),
						));

						// Used for the "at the very top" position
						// Add 1 at the end because this is a new layer
						$top_position = max($row['dynamo_layer_position'], $top_position);
					}

					$template_vars = array(
						'L_LAST_LAYER'		=> $l_last_layer,
						'TOP_POSITION'		=> $top_position + 1,
						'L_TITLE'			=> $user->lang['ADD_LAYER'],
						'L_TITLE_EXPLAIN'	=> $user->lang['ADD_LAYER_EXPLAIN'],
						'LAYER_NAME'		=> utf8_normalize_nfc(request_var('dynamo_layer_name', '', true)), // from the quick "add layer" form thing
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
						$desired_name = utf8_normalize_nfc(request_var('dynamo_layer_name', '', true));
						$desired_desc = utf8_normalize_nfc(request_var('dynamo_layer_desc', '', true));
						$desired_mandatory = request_var('dynamo_layer_mandatory', 0);
						$desired_default = request_var('dynamo_layer_default', 0);
						$desired_price = request_var('default_price', 0);

						// First update all non-position things
						$update_array = array(
							'dynamo_layer_name'			=> $desired_name,
							'dynamo_layer_desc'			=> $desired_desc,
							'dynamo_layer_mandatory'	=> $desired_mandatory,
							'dynamo_layer_default'		=> $desired_default,
							'default_price'				=> $desired_price,
						);
						$sql = "UPDATE " . DYNAMO_LAYERS_TABLE . "
								SET " . $db->sql_build_array('UPDATE', $update_array) . "
								WHERE dynamo_layer_id = $edit_get";
						$db->sql_query($sql);

						// Now move it
						$this->move_layer($edit_get, $desired_position);

						add_log('admin', 'LOG_DYNAMO_EDIT_LAYER', $desired_name);

						trigger_error($user->lang['ACP_DYNAMO_EDITED_LAYER'] . adm_back_link($this->u_action));
					}

					// If we need to edit a layer, the edit get var will be > 0 (will be the ID)
					$this_template = 'acp_dynamo_layers_edit';

					// Get the information for this layer from the db
					$sql = "SELECT l.dynamo_layer_id, l.dynamo_layer_name, l.dynamo_layer_desc, l.dynamo_layer_position, l.dynamo_layer_mandatory, l.dynamo_layer_default, l.default_price, i.dynamo_item_name
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

					$top_position = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('layers_dropdown', array(
							'POSITION'		=> $row['dynamo_layer_position'],
							'POSITION_TEXT'	=> sprintf($user->lang['IMMEDIATELY_BELOW'], $row['dynamo_layer_name']),
						));

						// Keep track of the position "at the very top" (don't add 1)
						$top_position = max($row['dynamo_layer_position'], $top_position);
					}

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
						'DEFAULT_PRICE'		=> $layer['default_price'],
						'CURRENT_ITEM'		=> $layer_default,
						'L_LAST_LAYER'		=> $l_last_layer,
						'L_TITLE'			=> sprintf($user->lang['EDITING_LAYER'], $layer_name),
						'L_TITLE_EXPLAIN'	=> $user->lang['EDITING_LAYER_EXPLAIN'],
						'LAYER_NAME'		=> $layer_name,
						'LAYER_DESC'		=> $layer['dynamo_layer_desc'],
						'LAYER_MANDATORY'	=> $layer['dynamo_layer_mandatory'],
						'LAYER_HAS_ITEMS'	=> $num_items > 0,
						'CURRENT_POSITION'	=> $layer['dynamo_layer_position'],
						'TOP_POSITION'		=> $top_position,
					);
				}
				else if ($delete_get > 0)
				{
					// Do the confirm box thing whatever before deleting
					if (confirm_box(true))
					{
						// If we need to delete a layer, the delete get var will be > 0 (will be the ID)
						// First figure out the name of the layer (for logging purposes)
						$sql = "SELECT dynamo_layer_name
								FROM " . DYNAMO_LAYERS_TABLE . "
								WHERE dynamo_layer_id = $delete_get";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);

						$sql = "DELETE FROM " . DYNAMO_LAYERS_TABLE . "
								WHERE dynamo_layer_id = $delete_get";
						$db->sql_query($sql);

						// Now set the associated items to an item ID of 0 (uncategorised)
						$sql = "UPDATE " . DYNAMO_ITEMS_TABLE . "
								SET dynamo_item_layer = 0
								WHERE dynamo_item_layer = $delete_get";
						$db->sql_query($sql);

						// Also rename the items to take the new layer into account
						// This is worse than usual ... try to clean it up eventually
						$prefix = $phpbb_root_path . $config['dynamo_image_fp'] . '/' . $delete_get;
						$glob_files = $prefix . '-*.png';
						$num_chars_to_strip = strlen($prefix);

						foreach (glob($glob_files) as $old_name) {
							$new_name = $phpbb_root_path . $config['dynamo_image_fp'] . '/' . '0' . substr($old_name, $num_chars_to_strip);
							rename($old_name, $new_name);
						}

						// Delete it from the users table
						$sql = "DELETE FROM " . DYNAMO_USERS_TABLE . "
								WHERE dynamo_user_layer = $delete_get";
						$db->sql_query($sql);
						// I miss MVC

						add_log('admin', 'LOG_DYNAMO_DELETE_LAYER', $row['dynamo_layer_name']);

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
					$sql = "SELECT l.dynamo_layer_id, l.dynamo_layer_name, l.dynamo_layer_desc, l.dynamo_layer_position, l.dynamo_layer_mandatory, l.dynamo_layer_default, l.default_price, i.dynamo_item_name
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
							'DEFAULT_PRICE'		=> $row['default_price'],
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
						$desired_name = utf8_normalize_nfc(request_var('dynamo_item_name', '', true));
						$desired_desc = utf8_normalize_nfc(request_var('dynamo_item_desc', '', true));
						$desired_layer = request_var('dynamo_item_layer', 0);
						$desired_price = request_var('item_price', 0);

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
							$file->realname = get_item_image_path('filename', $desired_layer, $item_id);

							// Move file and overwrite any existing image
							// $phpbb_root_path is prepended within the move_file function
							$file->move_file($config['dynamo_image_fp'], true);
						}

						$insert_array = array(
							'dynamo_item_id'	=> $item_id, // need this to avoid discrepancies lol
							'dynamo_item_name' 	=> $desired_name,
							'dynamo_item_desc'	=> $desired_desc,
							'dynamo_item_layer'	=> $desired_layer,
							'item_price'		=> $desired_price,
						);

						// Might as well not ignore the ID since we have it
						$sql = "INSERT INTO " . DYNAMO_ITEMS_TABLE . " " . $db->sql_build_array('INSERT', $insert_array);
						$db->sql_query($sql);

						// Figure out the layer name so it can be added to the logs
						if ($desired_layer > 0)
						{
							$sql = "SELECT dynamo_layer_name
									FROM " . DYNAMO_LAYERS_TABLE . "
									WHERE dynamo_layer_id = $desired_layer";
							$result = $db->sql_query($sql);
							$row = $db->sql_fetchrow($result);
							$layer_name = $row['dynamo_layer_name'];
						}
						else
						{
							$layer_name = $user->lang['UNCATEGORISED'];
						}

						add_log('admin', 'LOG_DYNAMO_ADD_ITEM', $desired_name, $layer_name);

						trigger_error($user->lang['ACP_DYNAMO_ADDED_ITEM'] . adm_back_link($this->u_action));
					}

					$this_template = 'acp_dynamo_items_edit';

					// Make the layer dropdown - get all the layers from the db
					// Also show the price
					$sql = "SELECT dynamo_layer_name, dynamo_layer_id, default_price
							FROM " . DYNAMO_LAYERS_TABLE . "
							ORDER BY dynamo_layer_position DESC";
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('layer_dropdown', array(
							'LAYER_ID'		=> $row['dynamo_layer_id'],
							'LAYER_NAME'	=> $row['dynamo_layer_name'],
							'DEFAULT_PRICE'	=> $row['default_price'],
						));
					}

					$template_vars = array(
						'IN_ADD'			=> true,
						'L_TITLE'			=> $user->lang['ADDING_ITEM'],
						'L_TITLE_EXPLAIN'	=> $user->lang['ADDING_ITEM_EXPLAIN'],
						'U_ACTION'			=> $this->u_action . '&amp;add=1',
						'ITEM_NAME'			=> utf8_normalize_nfc(request_var('dynamo_item_name', '', true)),
					);
				}
				else if ($edit_item_id > 0)
				{
					if ($submit)
					{
						$desired_name = utf8_normalize_nfc(request_var('dynamo_item_name', '', true));
						$desired_desc = utf8_normalize_nfc(request_var('dynamo_item_desc', '', true));
						$desired_layer = request_var('dynamo_item_layer', 0);
						$desired_price = request_var('item_price', 0);

						// Change the filename to reflect the new layer if necessary
						$sql = "SELECT dynamo_item_layer
								FROM " . DYNAMO_ITEMS_TABLE . "
								WHERE dynamo_item_id = $edit_item_id";
						$result = $db->sql_query($sql);
						$item = $db->sql_fetchrow($result);
						$old_layer = $item['dynamo_item_layer'];

						if ($old_layer != $desired_layer)
						{
							// First, rename the image file
							$old_file_name = get_item_image_path('entire', $old_layer, $edit_item_id);
							$new_file_name = get_item_image_path('entire', $desired_layer, $edit_item_id);

							if (!rename($old_file_name, $new_file_name))
							{
								trigger_error($user->lang['ACP_DYNAMO_CANNOT_MOVE'] . adm_back_link($this->u_action));
							}

							// Then, delete all traces of this item from the users table
							// Done to avoid confusion and overwriting other preferences etc
							$sql = "DELETE FROM " . DYNAMO_USERS_TABLE . "
									WHERE dynamo_user_item = $edit_item_id";
							$db->sql_query($sql);
						}

						$update_array = array(
							'item_price'		=> $desired_price,
							'dynamo_item_name' 	=> $desired_name,
							'dynamo_item_desc' 	=> $desired_desc,
							'dynamo_item_layer' => $desired_layer
						);

						$sql = "UPDATE " . DYNAMO_ITEMS_TABLE . "
								SET " . $db->sql_build_array('UPDATE', $update_array) . "
								WHERE dynamo_item_id = $edit_item_id";
						$db->sql_query($sql);

						add_log('admin', 'LOG_DYNAMO_EDIT_ITEM', $desired_name);

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
						'ITEM_PRICE'				=> $item['item_price'],
						'CURRENT_LAYER'		=> $item['dynamo_item_layer'],
						'L_TITLE'			=> sprintf($user->lang['EDITING_ITEM'], $item_name),
						'L_TITLE_EXPLAIN'	=> $user->lang['EDITING_ITEM_EXPLAIN'],
						'ITEM_IMAGE'		=> get_item_image_path('entire', $item['dynamo_item_layer'], $item['dynamo_item_id']),
						'ITEM_NAME'			=> $item_name,
						'ITEM_DESC'			=> $item['dynamo_item_desc'],
					);
				}
				else if ($delete_item_id > 0)
				{
				// Do the confirm box thing whatever before deleting
					if (confirm_box(true))
					{
						// Figure out the item name before deleting it (for logs)
						$sql = "SELECT dynamo_item_name, dynamo_item_layer
								FROM " . DYNAMO_ITEMS_TABLE . "
								WHERE dynamo_item_id = $delete_item_id";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);

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

						// Delete any traces of it from the users table as well
						$sql = "DELETE FROM " . DYNAMO_USERS_TABLE . "
								WHERE dynamo_user_item = $delete_item_id";
						$db->sql_query($sql);

						// Delete the image file
						$image_filepath = get_item_image_path('entire', $row['dynamo_item_layer'], $delete_item_id);
						unlink($image_filepath);

						add_log('admin', 'LOG_DYNAMO_DELETE_ITEM', $row['dynamo_item_name']);

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
						$item_image_url = get_item_image_path('entire', $item_layer, $item_id);

						$template->assign_block_vars('item', array(
							'ITEM_DESC'		=> $row['dynamo_item_desc'],
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
		$template_vars['POINTS_ENABLED'] = $config['dynamo_use_points'] && $config['points_enable'];
		$template->assign_vars($template_vars);
	}
}
?>
