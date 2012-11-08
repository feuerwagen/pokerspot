<?php /* Smarty version 2.6.22, created on 2010-09-14 17:22:21
         compiled from table_canceled.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load' && $this->_tpl_vars['page'] === true): ?>
<div id="booking_canceled">
<?php endif; ?>
<table id="b_canceled">
<thead>
	<tr>
		<th>Gruppe</th>
		<th>Anreise</th>
		<th>Abreise</th>
		<th>Personen</th>
		<th>Storniert am</th>
		<th>Storniert von</th>
	</tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['bookings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['b']):
?>
	<tr id="<?php echo $this->_tpl_vars['b']->id; ?>
"<?php if ($this->_tpl_vars['b']->mark === true): ?> class="marked"<?php endif; ?>>
		<td>
			<?php if ($this->_tpl_vars['b']->organisation != '' && strtolower ( $this->_tpl_vars['b']->organisation ) != 'privat'): ?>
			<strong><?php echo $this->_tpl_vars['b']->organisation; ?>
</strong><br>
			<?php if ($this->_tpl_vars['b']->email != ''): ?><a href="admin/mail/list/<?php echo $this->_tpl_vars['b']->id; ?>
.html"><?php endif; ?><?php echo $this->_tpl_vars['b']->name; ?>
<?php if ($this->_tpl_vars['b']->email != ''): ?></a><?php endif; ?>
			<?php else: ?>
			<strong><?php if ($this->_tpl_vars['b']->email != ''): ?><a href="admin/mail/list/<?php echo $this->_tpl_vars['b']->id; ?>
.html"><?php endif; ?><?php echo $this->_tpl_vars['b']->name; ?>
<?php if ($this->_tpl_vars['b']->email != ''): ?></a><?php endif; ?></strong>
			<?php endif; ?>
		</td>
		<td><?php echo $this->_tpl_vars['b']->start->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['b']->end->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['b']->guests; ?>
</td>
		<td><?php echo $this->_tpl_vars['b']->canceled->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['b']->user->realname; ?>
</td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php echo '
<script>
$(document).ready(function(){
	// show tooltips for houses
	$(\'.h img\').tipTip();
	
	$(\'#b_canceled\').tablesorter({
		headers: { 
			1: {sorter: \'shortDate\'},
			2: {sorter: \'shortDate\'},
			4: {sorter: \'shortDate\'},
		}
	}).tableHover({clickClass: \'click\'});
'; ?>

<?php if ($this->_tpl_vars['permissions']['restore'] == true): ?>
<?php echo '
	// menu
	$(\'#b_canceled\').find(\'tr\').contextMenu({
		menu: \'canceledMenu\'
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		openDialog($link);
	});
'; ?>

<?php endif; ?>
<?php echo '
});
</script>
'; ?>

<?php if ($this->_tpl_vars['call'] != 'load' && $this->_tpl_vars['page'] === true): ?>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<?php if ($this->_tpl_vars['permissions']['restore'] == true): ?>
<ul id="canceledMenu">
	<li class="restore"><a href="admin/booking/restore/?width=350&height=150" class="confirm">Stornierung aufheben</a></li>
</ul>
<?php endif; ?>
<?php endif; ?>