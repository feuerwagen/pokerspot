<?php /* Smarty version 2.6.22, created on 2010-08-26 18:47:49
         compiled from message_list.html */ ?>
<div>
<ul>
<?php $_from = $this->_tpl_vars['messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['m']):
?>
	<li class="s_message<?php if ($this->_tpl_vars['read'][$this->_tpl_vars['id']] === true): ?> read<?php endif; ?><?php if ($this->_tpl_vars['m'] === $this->_tpl_vars['current']): ?> active<?php endif; ?><?php if (! is_array ( $this->_tpl_vars['m']->replies ) && $this->_tpl_vars['m']->sender === $this->_tpl_vars['user']): ?> single<?php endif; ?>" id="s_<?php echo $this->_tpl_vars['id']; ?>
">
		<?php if ($this->_tpl_vars['read'][$this->_tpl_vars['id']] === false): ?><span class="unread">&bull;</span><?php endif; ?>
		<h5><a href="admin/message/show/<?php echo $this->_tpl_vars['id']; ?>
.html"><?php if ($this->_tpl_vars['m']->subject != ''): ?><?php echo $this->_tpl_vars['m']->subject; ?>
<?php else: ?><em>kein Betreff</em><?php endif; ?></a></h5>
		<?php if ($this->_tpl_vars['m']->sender != $this->_tpl_vars['user']): ?>&larr; <?php echo $this->_tpl_vars['m']->sender->realname; ?>
<?php else: ?>&rarr; <?php echo $this->_tpl_vars['m']->receiver->realname; ?>
<?php endif; ?>  <span class="date"><?php echo $this->_tpl_vars['m']->created['date']->returnDate('j.n.Y'); ?>
</span>
	</li>
<?php endforeach; endif; unset($_from); ?>
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
});
</script>
'; ?>

</div>