<?php /* Smarty version 2.6.22, created on 2010-09-14 17:32:49
         compiled from mail_list.html */ ?>
<div>
<div id="mbinfo">
<?php echo $this->_tpl_vars['booking']; ?>

</div>
<?php if ($this->_tpl_vars['new'] !== false && $this->_tpl_vars['new']->email != ''): ?>
<a href="admin/mail/send/?width=700&height=520" class="dialog send" id="<?php echo $this->_tpl_vars['new']->id; ?>
">Neue Mail schreiben</a>
<?php endif; ?>
<ul>
<?php $_from = $this->_tpl_vars['messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['m']):
?>
	<li class="s_message<?php if ($this->_tpl_vars['m']->read !== false || $this->_tpl_vars['m']->type == 'out'): ?> read<?php endif; ?><?php if ($this->_tpl_vars['m'] === $this->_tpl_vars['current']): ?> active<?php endif; ?><?php if ($this->_tpl_vars['m']->type == 'out'): ?> disabled<?php endif; ?>" id="s_<?php echo $this->_tpl_vars['id']; ?>
">
		<?php if ($this->_tpl_vars['m']->read === false && $this->_tpl_vars['m']->type == 'in'): ?><span class="unread">&bull;</span><?php endif; ?>
		<h5><a href="admin/mail/show/<?php echo $this->_tpl_vars['id']; ?>
.html"<?php if ($this->_tpl_vars['new'] === false): ?> class="bload"<?php endif; ?>><?php if ($this->_tpl_vars['m']->subject != ''): ?><?php echo $this->_tpl_vars['m']->subject; ?>
<?php else: ?><em>kein Betreff</em><?php endif; ?></a></h5>
		<?php if ($this->_tpl_vars['m']->type == 'in'): ?>&larr;<?php else: ?>&rarr;<?php endif; ?><?php if ($this->_tpl_vars['m']->email != ''): ?> <?php if ($this->_tpl_vars['m']->name != ''): ?><?php echo $this->_tpl_vars['m']->name; ?>
<?php else: ?><?php echo $this->_tpl_vars['m']->email; ?>
<?php endif; ?><?php endif; ?> <span class="date"><?php echo $this->_tpl_vars['m']->created['date']->returnDate('j.n.Y'); ?>
</span><br/>
		<?php if ($this->_tpl_vars['m']->type == 'out'): ?><em>Verschickt von <?php echo $this->_tpl_vars['m']->user->realname; ?>
</em>
		<?php else: ?><?php if ($this->_tpl_vars['m']->read !== false): ?><em>Erledigt von <?php echo $this->_tpl_vars['m']->user->realname; ?>
 am <?php echo $this->_tpl_vars['m']->read['date']->returnDate('j.n.Y'); ?>
</em><?php endif; ?><?php endif; ?>
	</li>
<?php endforeach; endif; unset($_from); ?>
</ul>
<?php echo '
<script>
$(document).ready(function() {	
	$(\'.houses img\').tipTip();
	
	// add booking info if mail selected
	$(\'a.bload\').click(function() {
		$link = $(this);
		if (!$link.parents(\'li\').hasClass(\'active\')) {
			$(\'#mbinfo\').fadeOut(function() {
				$(this).load(\'admin/mail/info/\'+$link.url().attr(\'file\'), {call: \'ajax\'}, function() {
					$(this).fadeIn();
				});
			});
		}
	});
	
	// button for new mail
	$(\'#sidebar\').find(\'a.send\').button({
		icons: {
			primary: \'icon-mail\'
		}
	}).click(function() {
		$(this).url().attr(\'file\', $(this).attr(\'id\')+\'.html\');
		openDialog($(this));
		return false;
	});

	$(\'#sidebar\').find(\'li.s_message\').hoverButtons({
		menu: \'hoverMenu\',
		offset: \'-2 5\',
		onShow: function($el) {
			if ($el.find(\'span.unread\').length > 0)
				$(\'#hoverMenu\').find(\'li.unread\').addClass(\'read\').removeClass(\'unread\').children(\'a\').attr(\'title\', \'Erledigt\');
			else
				$(\'#hoverMenu\').find(\'li.read\').addClass(\'unread\').removeClass(\'read\').children(\'a\').attr(\'title\', \'Zu erledigen\');

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