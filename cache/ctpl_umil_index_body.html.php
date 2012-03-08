<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('overall_header.html'); if ($this->_rootref['S_CONFIRM']) {  ?>

<form id="confirm" method="post" action="<?php echo (isset($this->_rootref['S_CONFIRM_ACTION'])) ? $this->_rootref['S_CONFIRM_ACTION'] : ''; ?>">

<fieldset>
	<h1><?php echo (isset($this->_rootref['MESSAGE_TITLE'])) ? $this->_rootref['MESSAGE_TITLE'] : ''; ?></h1>
	<p><?php echo (isset($this->_rootref['MESSAGE_TEXT'])) ? $this->_rootref['MESSAGE_TEXT'] : ''; ?></p>

	<?php echo (isset($this->_rootref['S_HIDDEN_FIELDS'])) ? $this->_rootref['S_HIDDEN_FIELDS'] : ''; ?>


	<div style="text-align: center;">
		<input type="submit" name="confirm" value="<?php echo ((isset($this->_rootref['L_YES'])) ? $this->_rootref['L_YES'] : ((isset($user->lang['YES'])) ? $user->lang['YES'] : '{ YES }')); ?>" class="button2" />&nbsp;
		<input type="submit" name="cancel" value="<?php echo ((isset($this->_rootref['L_NO'])) ? $this->_rootref['L_NO'] : ((isset($user->lang['NO'])) ? $user->lang['NO'] : '{ NO }')); ?>" class="button2" />
	</div>

</fieldset>

</form>
<?php } if (sizeof($this->_tpldata['options'])) {  ?>

	<h1><?php echo ((isset($this->_rootref['L_TITLE'])) ? $this->_rootref['L_TITLE'] : ((isset($user->lang['TITLE'])) ? $user->lang['TITLE'] : '{ TITLE }')); ?></h1>

	<p><?php echo ((isset($this->_rootref['L_TITLE_EXPLAIN'])) ? $this->_rootref['L_TITLE_EXPLAIN'] : ((isset($user->lang['TITLE_EXPLAIN'])) ? $user->lang['TITLE_EXPLAIN'] : '{ TITLE_EXPLAIN }')); ?></p>

	<?php if ($this->_rootref['S_ERROR']) {  ?>

		<div class="errorbox">
			<h3><?php echo ((isset($this->_rootref['L_WARNING'])) ? $this->_rootref['L_WARNING'] : ((isset($user->lang['WARNING'])) ? $user->lang['WARNING'] : '{ WARNING }')); ?></h3>
			<p><?php echo (isset($this->_rootref['ERROR_MSG'])) ? $this->_rootref['ERROR_MSG'] : ''; ?></p>
		</div>
	<?php } ?>


	<form id="umil" method="post" action="<?php echo (isset($this->_rootref['U_ACTION'])) ? $this->_rootref['U_ACTION'] : ''; ?>" name="umil">

		<?php $_options_count = (isset($this->_tpldata['options'])) ? sizeof($this->_tpldata['options']) : 0;if ($_options_count) {for ($_options_i = 0; $_options_i < $_options_count; ++$_options_i){$_options_val = &$this->_tpldata['options'][$_options_i]; if ($_options_val['S_LEGEND']) {  if (! $_options_val['S_FIRST_ROW']) {  ?>

					</fieldset>
				<?php } ?>

				<fieldset>
					<legend><?php echo $_options_val['LEGEND']; ?></legend>

			<?php } else { ?>

				<dl>
					<dt><label for="<?php echo $_options_val['KEY']; ?>"><?php echo $_options_val['TITLE']; ?>:</label><?php if ($_options_val['S_EXPLAIN']) {  ?><br /><span><?php echo $_options_val['TITLE_EXPLAIN']; ?></span><?php } ?></dt>
					<dd><?php echo $_options_val['CONTENT']; ?></dd>
					<?php if ($_options_val['S_FIND_USER']) {  ?><dd>[ <a href="<?php echo $_options_val['U_FIND_USER']; ?>" onclick="find_username(this.href); return false;"><?php echo ((isset($this->_rootref['L_FIND_USERNAME'])) ? $this->_rootref['L_FIND_USERNAME'] : ((isset($user->lang['FIND_USERNAME'])) ? $user->lang['FIND_USERNAME'] : '{ FIND_USERNAME }')); ?></a> ]</dd><?php } ?>

				</dl>

			<?php } }} ?>


		<p class="submit-buttons">
			<input class="button1" type="submit" id="submit" name="submit" value="<?php echo ((isset($this->_rootref['L_SUBMIT'])) ? $this->_rootref['L_SUBMIT'] : ((isset($user->lang['SUBMIT'])) ? $user->lang['SUBMIT'] : '{ SUBMIT }')); ?>" />&nbsp;
			<input class="button2" type="reset" id="reset" name="reset" value="<?php echo ((isset($this->_rootref['L_RESET'])) ? $this->_rootref['L_RESET'] : ((isset($user->lang['RESET'])) ? $user->lang['RESET'] : '{ RESET }')); ?>" />
		</p>
		<?php echo (isset($this->_rootref['S_HIDDEN_FIELDS'])) ? $this->_rootref['S_HIDDEN_FIELDS'] : ''; ?>

		<?php echo (isset($this->_rootref['S_FORM_TOKEN'])) ? $this->_rootref['S_FORM_TOKEN'] : ''; ?>

	</fieldset>
	</form>
<?php } if ($this->_rootref['S_RESULTS']) {  ?>

	<h1><?php echo ((isset($this->_rootref['L_TITLE'])) ? $this->_rootref['L_TITLE'] : ((isset($user->lang['TITLE'])) ? $user->lang['TITLE'] : '{ TITLE }')); ?> - <font style="color: <?php if ($this->_rootref['S_SUCCESS']) {  ?>green<?php } else { ?>red<?php } ?>;"><?php echo ((isset($this->_rootref['L_RESULTS'])) ? $this->_rootref['L_RESULTS'] : ((isset($user->lang['RESULTS'])) ? $user->lang['RESULTS'] : '{ RESULTS }')); ?></font></h1>

	<br />

	<p><?php echo ((isset($this->_rootref['L_DATABASE_TYPE'])) ? $this->_rootref['L_DATABASE_TYPE'] : ((isset($user->lang['DATABASE_TYPE'])) ? $user->lang['DATABASE_TYPE'] : '{ DATABASE_TYPE }')); ?> :: <strong><?php echo (isset($this->_rootref['SQL_LAYER'])) ? $this->_rootref['SQL_LAYER'] : ''; ?></strong></p>

	<?php if (! $this->_rootref['S_SUCCESS']) {  ?>

		<div class="errorbox">
			<h3><?php echo ((isset($this->_rootref['L_WARNING'])) ? $this->_rootref['L_WARNING'] : ((isset($user->lang['WARNING'])) ? $user->lang['WARNING'] : '{ WARNING }')); ?></h3>
			<p><?php echo ((isset($this->_rootref['L_ERROR_NOTICE'])) ? $this->_rootref['L_ERROR_NOTICE'] : ((isset($user->lang['ERROR_NOTICE'])) ? $user->lang['ERROR_NOTICE'] : '{ ERROR_NOTICE }')); ?></p>
		</div>
	<?php } if ($this->_rootref['S_PERMISSIONS']) {  ?>

		<div class="errorbox">
			<h3><?php echo ((isset($this->_rootref['L_WARNING'])) ? $this->_rootref['L_WARNING'] : ((isset($user->lang['WARNING'])) ? $user->lang['WARNING'] : '{ WARNING }')); ?></h3>
			<p><?php echo ((isset($this->_rootref['L_PERMISSIONS_WARNING'])) ? $this->_rootref['L_PERMISSIONS_WARNING'] : ((isset($user->lang['PERMISSIONS_WARNING'])) ? $user->lang['PERMISSIONS_WARNING'] : '{ PERMISSIONS_WARNING }')); ?></p>
		</div>
	<?php } if (sizeof($this->_tpldata['results'])) {  ?>

	<fieldset>
		<legend></legend>
		<?php $_results_count = (isset($this->_tpldata['results'])) ? sizeof($this->_tpldata['results']) : 0;if ($_results_count) {for ($_results_i = 0; $_results_i < $_results_count; ++$_results_i){$_results_val = &$this->_tpldata['results'][$_results_i]; ?>

				<p><?php echo $_results_val['COMMAND']; ?></p>
				<div style="color: <?php if ($_results_val['S_SUCCESS']) {  ?>green<?php } else { ?>red<?php } ?>;"><?php echo $_results_val['RESULT']; ?></div>
				<?php if (! $_results_val['S_LAST_ROW']) {  ?><hr /><?php } }} ?>

	</fieldset>
	<?php } } $this->_tpl_include('overall_footer.html'); ?>