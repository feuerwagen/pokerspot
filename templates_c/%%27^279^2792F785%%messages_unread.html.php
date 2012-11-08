<?php /* Smarty version 2.6.22, created on 2010-09-04 01:30:10
         compiled from messages_unread.html */ ?>
<h2>Neue Nachrichten</h2>
<?php $_from = $this->_tpl_vars['messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
<div class="r_right">
	<ul class="m_info">
		<li><strong>Von: <?php echo $this->_tpl_vars['m']->sender->realname; ?>
</strong></li>
		<li>Am: <?php echo $this->_tpl_vars['m']->created['date']->returnDate('j.n.Y'); ?>
</li>
		<li>Um: <?php echo $this->_tpl_vars['m']->created['time']; ?>
</li>
	</ul>
	<div class="tip_arrow"><div></div></div>
	<div class="r_message ui-corner-all" id="<?php echo $this->_tpl_vars['m']->id; ?>
">
	<h3><a class="unread" href="form/message/mark/"><?php if ($this->_tpl_vars['m']->read === false): ?>●<?php else: ?>○<?php endif; ?></a><?php echo $this->_tpl_vars['m']->subject; ?>
</h3>
	<?php echo $this->_tpl_vars['m']->text; ?>

	</div>
</div>
<?php endforeach; endif; unset($_from); ?>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#bb_messages\').find(\'div.r_message\').contextMenu({
		menu: \'messageMenu\',
		onShow: function($el) {		
			if ($el.find(\'a.unread\').text() == \'●\')
				$(\'#messageMenu\').find(\'li.unread\').addClass(\'read\').removeClass(\'unread\').children(\'a\').text(\'Als Gelesen markieren\');
			else
				$(\'#messageMenu\').find(\'li.read\').addClass(\'unread\').removeClass(\'read\').children(\'a\').text(\'Als Ungelesen markieren\');
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

<ul id="messageMenu">
    <li class="read"><a href="form/message/mark/">Als Gelesen markieren</a></li>
	<li class="reply"><a href="admin/message/reply/?width=480&height=520" class="send" title="Nachricht beantworten">Beantworten</a></li>
</ul>