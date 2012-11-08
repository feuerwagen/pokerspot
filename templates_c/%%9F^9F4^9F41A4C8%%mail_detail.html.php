<?php /* Smarty version 2.6.22, created on 2010-09-04 19:21:14
         compiled from mail_detail.html */ ?>
<?php if (is_object ( $this->_tpl_vars['m'] )): ?>
<div class="<?php if ($this->_tpl_vars['m']->type == 'in'): ?>r_left<?php else: ?>r_right<?php endif; ?>">
	<div class="tip_arrow"><div></div></div>
	<div class="r_message ui-corner-all<?php if ($this->_tpl_vars['m']->type == 'out'): ?> disabled<?php endif; ?>" id="<?php echo $this->_tpl_vars['m']->id; ?>
">
	<h3><?php if ($this->_tpl_vars['m']->type == 'in'): ?><a class="unread" href="form/mail/mark/"><?php if ($this->_tpl_vars['m']->read === false): ?>●<?php else: ?>○<?php endif; ?></a><?php endif; ?><?php if ($this->_tpl_vars['m']->subject != ''): ?><?php echo $this->_tpl_vars['m']->subject; ?>
<?php else: ?><em>kein Betreff</em><?php endif; ?> <span class="date"><?php echo $this->_tpl_vars['m']->created['date']->returnDate('j.n.Y'); ?>
</span></h3>
	<?php echo $this->_tpl_vars['m']->text; ?>

	</div>
</div>
<?php endif; ?>
<?php echo '
<script>
$(document).ready(function() {
	path = \'mail/\'+$(document).url().attr(\'file\')+\'/\'+$(\'div.r_message\').first().attr(\'id\')+\'.html\';
	
	$(\'#main\').find(\'div.r_message\').contextMenu({
		menu: \'messageMenu\',
		onShow: function($el) {
			if ($el.find(\'a.unread\').text() == \'●\')
				$(\'#messageMenu\').find(\'li.unread\').addClass(\'read\').removeClass(\'unread\').children(\'a\').text(\'Erledigt\');
			else
				$(\'#messageMenu\').find(\'li.read\').addClass(\'unread\').removeClass(\'read\').children(\'a\').text(\'Zu erledigen\');
		}
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		if ($link.attr(\'class\') == \'\') {
			o = ($link.parents(\'li\').hasClass(\'read\')) ? \'read\' : \'unread\';
			$.post($link.attr(\'href\'), {option: o, call: \'ajax\'}, function(data) {
				processJson(data);
				
				if (o == \'read\') 
					$el.find(\'a.unread\').html(\'○\');
				else
					$el.find(\'a.unread\').html(\'●\');
			}, \'json\');
		} else {
			openDialog($link);
		}
	});
	
	$(\'a.unread\').click(function() {
		$link = $(this);
		message = $link.parents(\'div.r_message\').attr(\'id\'); 
		if ($link.html() == \'●\') {
			$.post(this.href+message+\'.html\', {option: \'read\', call: \'ajax\'}, function(data) {
				processJson(data);
				$link.html(\'○\');
			}, \'json\');
		} else {
			$.post(this.href+message+\'.html\', {option: \'unread\', call: \'ajax\'}, function(data) {
				processJson(data);
				$link.html(\'●\');
			}, \'json\');
		}
		return false;
	});
});
</script>
'; ?>