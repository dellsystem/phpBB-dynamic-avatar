<?php if (!defined('IN_PHPBB')) exit; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo (isset($this->_rootref['S_CONTENT_DIRECTION'])) ? $this->_rootref['S_CONTENT_DIRECTION'] : ''; ?>" lang="<?php echo (isset($this->_rootref['S_USER_LANG'])) ? $this->_rootref['S_USER_LANG'] : ''; ?>" xml:lang="<?php echo (isset($this->_rootref['S_USER_LANG'])) ? $this->_rootref['S_USER_LANG'] : ''; ?>">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo (isset($this->_rootref['S_CONTENT_ENCODING'])) ? $this->_rootref['S_CONTENT_ENCODING'] : ''; ?>" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Language" content="<?php echo (isset($this->_rootref['S_USER_LANG'])) ? $this->_rootref['S_USER_LANG'] : ''; ?>" />
<meta http-equiv="imagetoolbar" content="no" />
<?php if ($this->_rootref['META']) {  echo (isset($this->_rootref['META'])) ? $this->_rootref['META'] : ''; } ?>

<title><?php echo (isset($this->_rootref['PAGE_TITLE'])) ? $this->_rootref['PAGE_TITLE'] : ''; ?></title>

<link href="<?php echo (isset($this->_rootref['UMIL_ROOT_PATH'])) ? $this->_rootref['UMIL_ROOT_PATH'] : ''; ?>style/style.css" rel="stylesheet" type="text/css" media="screen" />

<?php $this->_tpl_include('parse.css'); ?>


<script type="text/javascript">
// <![CDATA[

/**
* Find a member
*/
function find_username(url)
{
	popup(url, 760, 570, '_usersearch');
	return false;
}

/**
* Window popup
*/
function popup(url, width, height, name)
{
	if (!name)
	{
		name = '_popup';
	}

	window.open(url.replace(/&amp;/g, '&'), name, 'height=' + height + ',resizable=yes,scrollbars=yes, width=' + width);
	return false;
}

/**
* Set display of page element
* s[-1,0,1] = hide,toggle display,show
*/
function dE(n, s, type)
{
	if (!type)
	{
		type = 'block';
	}

	var e = document.getElementById(n);
	if (!s)
	{
		s = (e.style.display == '' || e.style.display == 'block') ? -1 : 1;
	}
	e.style.display = (s == 1) ? type : 'none';
}

// ]]>
</script>

</head>

<body class="<?php echo (isset($this->_rootref['S_CONTENT_DIRECTION'])) ? $this->_rootref['S_CONTENT_DIRECTION'] : ''; ?>">
<div id="wrap">
	<div id="page-header">
		<h1><?php echo ((isset($this->_rootref['L_INSTALL_PANEL'])) ? $this->_rootref['L_INSTALL_PANEL'] : ((isset($user->lang['INSTALL_PANEL'])) ? $user->lang['INSTALL_PANEL'] : '{ INSTALL_PANEL }')); ?></h1>
		<p><a href="<?php echo (isset($this->_rootref['U_ADM_INDEX'])) ? $this->_rootref['U_ADM_INDEX'] : ''; ?>"><?php echo ((isset($this->_rootref['L_ADMIN_INDEX'])) ? $this->_rootref['L_ADMIN_INDEX'] : ((isset($user->lang['ADMIN_INDEX'])) ? $user->lang['ADMIN_INDEX'] : '{ ADMIN_INDEX }')); ?></a> &bull; <a href="<?php echo (isset($this->_rootref['U_INDEX'])) ? $this->_rootref['U_INDEX'] : ''; ?>"><?php echo ((isset($this->_rootref['L_FORUM_INDEX'])) ? $this->_rootref['L_FORUM_INDEX'] : ((isset($user->lang['FORUM_INDEX'])) ? $user->lang['FORUM_INDEX'] : '{ FORUM_INDEX }')); ?></a></p>
		<p id="skip"><a href="#acp"><?php echo ((isset($this->_rootref['L_SKIP'])) ? $this->_rootref['L_SKIP'] : ((isset($user->lang['SKIP'])) ? $user->lang['SKIP'] : '{ SKIP }')); ?></a></p>
		<?php if ($this->_rootref['S_LANG_SELECT']) {  ?>

		<form method="post" action="">
			<fieldset class="nobg">
				<label for="language"><?php echo ((isset($this->_rootref['L_SELECT_LANG'])) ? $this->_rootref['L_SELECT_LANG'] : ((isset($user->lang['SELECT_LANG'])) ? $user->lang['SELECT_LANG'] : '{ SELECT_LANG }')); ?>:</label>
				<?php echo (isset($this->_rootref['S_LANG_SELECT'])) ? $this->_rootref['S_LANG_SELECT'] : ''; ?>

				<input class="button1" type="submit" id="change_lang" name="change_lang" value="<?php echo ((isset($this->_rootref['L_CHANGE'])) ? $this->_rootref['L_CHANGE'] : ((isset($user->lang['CHANGE'])) ? $user->lang['CHANGE'] : '{ CHANGE }')); ?>" />
			</fieldset>
		</form>
		<?php } ?>

	</div>

	<div id="page-body">
		<div id="acp">
		<div class="panel">
			<span class="corners-top"><span></span></span>
				<div id="content">
					<div id="menu">
						<ul>
						<?php $_l_block_count = (isset($this->_tpldata['l_block'])) ? sizeof($this->_tpldata['l_block']) : 0;if ($_l_block_count) {for ($_l_block_i = 0; $_l_block_i < $_l_block_count; ++$_l_block_i){$_l_block_val = &$this->_tpldata['l_block'][$_l_block_i]; ?>

							<li<?php if ($_l_block_val['S_SELECTED']) {  ?> id="activemenu"<?php } ?>><?php if ($_l_block_val['U_TITLE']) {  ?><a href="<?php echo $_l_block_val['U_TITLE']; ?>"><?php } ?><span<?php if ($_l_block_val['S_COMPLETE']) {  ?> class="completed"<?php } ?>><?php echo $_l_block_val['L_TITLE']; ?></span><?php if ($_l_block_val['U_TITLE']) {  ?></a><?php } ?></li>
						<?php }} ?>

						</ul>
					</div>

					<div id="main" class="install-body">

		 <?php if ($this->_rootref['S_BOARD_DISABLED']) {  ?>

		<div class="rules">
			<div class="inner"><span class="corners-top"><span></span></span>
				<strong><?php echo ((isset($this->_rootref['L_INFORMATION'])) ? $this->_rootref['L_INFORMATION'] : ((isset($user->lang['INFORMATION'])) ? $user->lang['INFORMATION'] : '{ INFORMATION }')); ?>:</strong> <?php echo ((isset($this->_rootref['L_BOARD_DISABLED'])) ? $this->_rootref['L_BOARD_DISABLED'] : ((isset($user->lang['BOARD_DISABLED'])) ? $user->lang['BOARD_DISABLED'] : '{ BOARD_DISABLED }')); ?>

			<span class="corners-bottom"><span></span></span></div>
		</div>
		<?php } ?>