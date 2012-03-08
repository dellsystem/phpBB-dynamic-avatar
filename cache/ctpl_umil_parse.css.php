<?php if (!defined('IN_PHPBB')) exit; ?><style type="text/css">
#page-header {
	background: url("<?php if ($this->_rootref['LOGO_IMG']) {  echo (isset($this->_rootref['LOGO_IMG'])) ? $this->_rootref['LOGO_IMG'] : ''; } else { echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/phpbb_logo.gif<?php } ?>") top left no-repeat;
}

.rtl #page-header {
	background: url("<?php if ($this->_rootref['LOGO_IMG']) {  echo (isset($this->_rootref['LOGO_IMG'])) ? $this->_rootref['LOGO_IMG'] : ''; } else { echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/phpbb_logo.gif<?php } ?>") top right no-repeat;
}

#tabs a {
	background:url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/bg_tabs1.gif") no-repeat 0% -34px;
}

#tabs a span {
	background: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/bg_tabs2.gif") no-repeat 100% -34px;
}

.panel {
	background: #F3F3F3 url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/innerbox_bg.gif") repeat-x top;
}

span.corners-top {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_left.gif");
}

span.corners-top span {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_right.gif");
}

span.corners-bottom {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_left.gif");
}

span.corners-bottom span {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_right.gif");
}

/* WinIE tweaks \*/
* html span.corners-top, * html span.corners-bottom { background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_left.gif"); }
* html span.corners-top span, * html span.corners-bottom span { background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_right.gif"); }
/* End tweaks */

#toggle-handle {
	background-image: url(<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/toggle.gif);
}

.rtl #toggle-handle {
	background-image: url(<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/toggle.gif);
}

#menu li#activemenu a:hover span, #menu li#activemenu span {
	background: #FFFFFF url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/arrow_right.gif") 1% 50% no-repeat;
}

.rtl #menu li#activemenu a:hover span, .rtl #menu li#activemenu span {
	background: #FFFFFF url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/arrow_left.gif") 99% 50% no-repeat;
}

	background: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/arrow_down.gif") 1% 50% no-repeat;
}

.rtl #menu li span.completed {
	background: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/arrow_down.gif") 99% 50% no-repeat;
}

th {
	background: #70AED3 url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/gradient2b.gif") bottom left repeat-x;
}

a.button1, input.button1, input.button3,
a.button2, input.button2 {
	background: #EFEFEF url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/bg_button.gif") repeat-x top;
}

a.button1:hover, input.button1:hover,
a.button2:hover, input.button2:hover {
	background: #EFEFEF url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/bg_button.gif") repeat bottom;
}

.permissions-category a {
	background: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/bg_tabs_alt1.gif") no-repeat 0% -35px;
}

.rtl .permissions-category a {
	float: right;
}

.permissions-category a span.tabbg {
	background: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/bg_tabs_alt2.gif") no-repeat 100% -35px;
}

.permissions-panel span.corners-top {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_left2.gif");
}

.permissions-panel span.corners-top span {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_right2.gif");
}

.permissions-panel span.corners-bottom {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_left2.gif");
}

.permissions-panel span.corners-bottom span {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_right2.gif");
}

.permissions-panel span.corners-top {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_left2.gif");
}

.permissions-panel span.corners-top span {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_right2.gif");
}

.permissions-panel span.corners-bottom {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_left2.gif");
}

.permissions-panel span.corners-bottom span {
	background-image: url("<?php echo (isset($this->_rootref['ROOT_PATH'])) ? $this->_rootref['ROOT_PATH'] : ''; ?>adm/images/corners_right2.gif");
}
</style>