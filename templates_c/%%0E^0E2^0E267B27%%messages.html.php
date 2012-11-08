<?php /* Smarty version 2.6.22, created on 2010-09-04 19:21:09
         compiled from messages.html */ ?>
<div id="sidebar">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'message_list.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div id="main">
	<div id="fixed">
		<div id="messages"></div>
		<div id="u_message">
			<h3>Erste (ungelesene) Nachricht</h3>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'message.html', 'smarty_include_vars' => array('m' => $this->_tpl_vars['current'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>
</div>
<ul id="messageMenu">
    <li class="read"><a href="form/message/mark/">Als Gelesen markieren</a></li>
	<li class="reply"><a href="admin/message/reply/?width=480&height=520" class="send" title="Nachricht beantworten">Beantworten</a></li>
	<li class="b_edit"><a href="admin/message/update/?width=480&height=520" class="form" title="Nachricht bearbeiten">Bearbeiten</a></li>
    <li class="delete"><a href="admin/message/delete/?width=350&height=150" class="delete" title="Nachricht löschen">Löschen</a></li>
</ul>
<ul id="hoverMenu">
    <li class="read"><a href="form/message/mark/" title="Als Gelesen markieren"></a></li>
	<li class="reply"><a href="admin/message/reply/?width=480&height=520" class="send" title="Nachricht beantworten"></a></li>
    <li class="delete"><a href="admin/message/delete/?width=350&height=150" class="delete" title="Nachricht löschen"></a></li>
</ul>
<?php echo '
<script>
$(document).ready(function() {	
	$(\'#sidebar\').find(\'li.s_message\').hoverButtons({
		menu: \'hoverMenu\',
		offset: \'-2 5\',
		onShow: function($el) {
			var enable = new Array(), disable = new Array(), remove = new Array();
			
			if ($el.hasClass(\'single\')) {
				enable.push(\'delete\');
				remove.push(\'reply\');
				remove.push(\'unread\');
				remove.push(\'read\');
			} else {
				remove.push(\'delete\');
				enable.push(\'reply\');
				enable.push(\'unread\');
				enable.push(\'read\');
			}
			
			if ($el.find(\'span.unread\').length > 0)
				$(\'#hoverMenu\').find(\'li.unread\').addClass(\'read\').removeClass(\'unread\').children(\'a\').attr(\'title\', \'Als Gelesen markieren\');
			else
				$(\'#hoverMenu\').find(\'li.read\').addClass(\'unread\').removeClass(\'read\').children(\'a\').attr(\'title\', \'Als Ungelesen markieren\');
			
			if (disable.length > 0)
				$(\'#hoverMenu\').disableMenuItems(disable);
			if (remove.length > 0)
				$(\'#hoverMenu\').removeMenuItems(remove);
			if (enable.length > 0)
				$(\'#hoverMenu\').enableMenuItems(enable);
			$(\'#hoverMenu\').find(\'a\').tipTip();
		}
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\').substr(2)+\'.html\');
		if ($link.attr(\'class\') == \'\') {
			o = ($link.parents(\'li\').hasClass(\'read\')) ? \'read\' : \'unread\';
			$.post($link.attr(\'href\'), {option: o, messages: \'all\', call: \'ajax\'}, function(data) {
				processJson(data);
				if (o == \'read\')
					$el.addClass(\'read\').find(\'span.unread\').remove();
				else
					$el.prepend(\'<span class="unread">&bull;</span>\').removeClass(\'read\');
			}, \'json\');
		} else {
			openDialog($link);
		}
	});
	
	$(\'#sidebar\').find(\'a\').live(\'click\', function() {
		$(\'#sidebar\').find(\'li.active\').removeClass(\'active\');
		$(this).parents(\'li\').addClass(\'active\');
		$(\'#u_message\').load(this.href, {call: \'ajax\'});
		return false;
	});
});
</script>
'; ?>