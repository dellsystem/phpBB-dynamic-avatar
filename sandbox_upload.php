<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = './root/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// For figuring out why the upload file function won't work

page_header('Testing upload shit', false);

$template->set_filenames(array(
	'body' => 'sandbox_upload_body.html')
);

if (isset($_POST['submit'])) {
	include_once($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
	// Must be in gif or png for transparency? figure this out later
	// Need config settings for avatar width and height later
	$upload = new fileupload('AVATAR_', array('gif', 'png'), false, 100, 100, 120, 120);

	// Maybe edit functions_uploaded.php or something ... or maybe not
	if (!empty($_FILES['uploadfile']['name']))
	{
		$file = $upload->form_upload('uploadfile');
		$file->realname = 'lol.png';
		// Make sure file is an image ... later
		echo $file->realname;
	
		// Make a config option to set this later
		$destination = 'images/dynamo';
		// Move file and overwrite any existing image
		echo $file->is_image();
		echo $file->is_uploaded();
		if (!$file->move_file($destination, true)) {
			echo "errors";
		}
	}
}

page_footer();

?>
