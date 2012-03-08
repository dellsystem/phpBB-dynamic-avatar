<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('overall_header.html'); ?>


<h1><?php echo ((isset($this->_rootref['L_ACP_DYNAMO_OVERVIEW'])) ? $this->_rootref['L_ACP_DYNAMO_OVERVIEW'] : ((isset($user->lang['ACP_DYNAMO_OVERVIEW'])) ? $user->lang['ACP_DYNAMO_OVERVIEW'] : '{ ACP_DYNAMO_OVERVIEW }')); ?></h1>

<p>Blah blah blah</p>

<?php $this->_tpl_include('overall_footer.html'); ?>