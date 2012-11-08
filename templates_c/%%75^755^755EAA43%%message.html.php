<?php /* Smarty version 2.6.22, created on 2010-08-27 03:53:45
         compiled from message.html */ ?>
<div class="<?php if ($this->_tpl_vars['m']->sender->id != $this->_tpl_vars['user']->id): ?>r_left<?php else: ?>r_right<?php endif; ?>">
	<div class="tip_arrow"><div></div></div>
	<div class="r_message ui-corner-all" id="<?php echo $this->_tpl_vars['m']->id; ?>
">
	<h3><?php if ($this->_tpl_vars['m']->sender->id != $this->_tpl_vars['user']->id): ?><a class="unread" href="form/message/mark/"><?php if ($this->_tpl_vars['m']->read === false): ?>●<?php else: ?>○<?php endif; ?></a><?php endif; ?><?php echo $this->_tpl_vars['m']->subject; ?>
 <span class="date"><?php echo $this->_tpl_vars['m']->created['date']->returnDate('j.n.Y'); ?>
</span></h3>
	<?php echo $this->_tpl_vars['m']->text; ?>

	</div>
</div>
<?php $_from = $this->_tpl_vars['m']->replies; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['r']):
?>
<div class="<?php if ($this->_tpl_vars['r']->sender->id != $this->_tpl_vars['user']->id): ?>r_left<?php else: ?>r_right<?php endif; ?>">
	<div class="tip_arrow"><div></div></div>
	<div class="r_message ui-corner-all" id="<?php echo $this->_tpl_vars['r']->id; ?>
">
	<h3><?php if ($this->_tpl_vars['r']->sender->id != $this->_tpl_vars['user']->id): ?><a class="unread" href="form/message/mark/"><?php if ($this->_tpl_vars['r']->read === false): ?>●<?php else: ?>○<?php endif; ?></a><?php endif; ?><?php echo $this->_tpl_vars['r']->subject; ?>
 <span class="date"><?php echo $this->_tpl_vars['r']->created['date']->returnDate('j.n.Y'); ?>
</span></h3>
	<?php echo $this->_tpl_vars['r']->text; ?>

	</div>
</div>
<?php endforeach; endif; unset($_from); ?>
<?php echo '
<script>
$(document).ready(function() {
	path = \'message/show/\'+$(\'div.r_message\').first().attr(\'id\')+\'.html\';
	
	$(\'#main\').find(\'div.r_message\').contextMenu({
		menu: \'messageMenu\',
		onShow: function($el) {
			var enable = new Array(), disable = new Array(), remove = new Array();
			if ($el.parent(\'div\').hasClass(\'r_left\')) {
				remove.push(\'delete\');
				remove.push(\'b_edit\');
				enable.push(\'unread\');
				enable.push(\'read\');
				enable.push(\'reply\');
				
				if ($el.find(\'a.unread\').text() == \'●\')
					$(\'#messageMenu\').find(\'li.unread\').addClass(\'read\').removeClass(\'unread\').children(\'a\').text(\'Als Gelesen markieren\');
				else
					$(\'#messageMenu\').find(\'li.read\').addClass(\'unread\').removeClass(\'read\').children(\'a\').text(\'Als Ungelesen markieren\');
			} else {
				if ($(\'#u_message\').find(\'div.r_message\').last().attr(\'id\') == $el.attr(\'id\')) 
					enable.push(\'delete\');
				else
					disable.push(\'delete\');
				enable.push(\'b_edit\');
				remove.push(\'unread\');
				remove.push(\'read\');
				remove.push(\'reply\');
			}
			
			if (disable.length > 0)
				$(\'#messageMenu\').disableMenuItems(disable);
			if (remove.length > 0)
				$(\'#messageMenu\').removeMenuItems(remove);
			if (enable.length > 0)
				$(\'#messageMenu\').enableMenuItems(enable);
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
