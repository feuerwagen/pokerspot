<?php /* Smarty version 2.6.22, created on 2010-09-04 19:21:14
         compiled from mails.html */ ?>
<div id="sidebar">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'mail_list.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div id="main">
	<div id="fixed">
		<div id="messages"></div>
		<div id="u_mail">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'mail_detail.html', 'smarty_include_vars' => array('m' => $this->_tpl_vars['current'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>
</div>
<ul id="messageMenu" style="display:none">
    <?php if ($this->_tpl_vars['permissions']['mark'] === true): ?><li class="read"><a href="form/mail/mark/">Erledigt</a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['new'] === true): ?><li class="m_reply"><a href="admin/mail/send/reply/?width=700&height=520" class="send" title="Nachricht beantworten">Beantworten</a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['assign'] === true): ?><li class="assign"><a href="admin/mail/assign/?width=450&height=200" class="form" title="Nachricht einer Belegung zuordnen">Einordnen</a></li><?php endif; ?>
</ul>
<ul id="hoverMenu">
    <?php if ($this->_tpl_vars['permissions']['mark'] === true): ?><li class="read"><a href="form/mail/mark/" title="Erledigt"></a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['new'] === true): ?><li class="m_reply"><a href="admin/mail/send/reply/?width=700&height=520" class="send" title="Nachricht beantworten"></a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['assign'] === true): ?><li class="assign"><a href="admin/mail/assign/?width=450&height=200" class="form" title="Nachricht einer Belegung zuordnen"></a></li><?php endif; ?>
</ul>
<?php echo '
<script>
$(document).ready(function() {		
	$(\'#sidebar\').find(\'a\').live(\'click\', function() {
		$(\'#sidebar\').find(\'li.active\').removeClass(\'active\');
		$(this).parents(\'li\').addClass(\'active\');
		$(\'#u_mail\').load(this.href, {call: \'ajax\'});
		return false;
	});
});
</script>
'; ?>