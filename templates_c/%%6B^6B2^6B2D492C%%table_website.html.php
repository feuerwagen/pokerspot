<?php /* Smarty version 2.6.22, created on 2012-10-31 21:33:00
         compiled from table_website.html */ ?>
<h3>Anmeldungen von der Website</h3>
<table id="bookings_website">
<thead>
	<tr>
		<th>Gruppe</th>
		<th>Anreise</th>
		<th>Abreise</th>
		<th>Personen</th>
		<th>Häuser</th>
		<th>Eintrag vom</th>
	</tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['bookings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['b']):
?>
	<tr id="<?php echo $this->_tpl_vars['b']->id; ?>
" class="website<?php if ($this->_tpl_vars['b']->class != ''): ?><?php echo $this->_tpl_vars['b']->class; ?>
<?php endif; ?>">
		<td>
			<?php if ($this->_tpl_vars['b']->organisation != ''): ?>
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
 <input type="hidden" class="month" value="<?php echo $this->_tpl_vars['b']->start->n; ?>
" /><input type="hidden" class="year" value="<?php echo $this->_tpl_vars['b']->start->Y; ?>
" /></td>
		<td><?php echo $this->_tpl_vars['b']->end->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['b']->guests; ?>
</td>
		<td class="h"><?php $_from = $this->_tpl_vars['b']->houses; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['house']):
?><img src="images/houses/<?php echo $this->_tpl_vars['house']; ?>
.png" title="<?php echo $this->_tpl_vars['houses'][$this->_tpl_vars['house']]; ?>
" /> <?php endforeach; endif; unset($_from); ?></td>
		<td><?php echo $this->_tpl_vars['b']->created['date']->returnDate('d.m.Y'); ?>
</td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#bookings_website\').tablesorter({
		headers: { 
			1: {sorter: \'shortDate\'},
			2: {sorter: \'shortDate\'},
			4: {sorter: false},
			5: {sorter: \'shortDate\'}
		}
	}).tableHover({clickClass: \'click\'});
	
	// menu
	$(\'#bookings_website\').find(\'tr\').contextMenu({
		menu: \'websiteMenu\',
		onShow: function($el) {
			var enable = new Array(), disable = new Array(), remove = new Array();
			var $menu = $(\'#websiteMenu\');
			remove.push(\'evaluate\');
			remove.push(\'guests\');
'; ?>

		<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['e']):
?>
			<?php echo $this->_tpl_vars['e']['script']; ?>

		<?php endforeach; endif; unset($_from); ?>
<?php echo '
			if (disable.length > 0)
				$(\'#websiteMenu\').disableMenuItems(disable);
			if (remove.length > 0)
				$(\'#websiteMenu\').removeMenuItems(remove);
			if (enable.length > 0)
				$(\'#websiteMenu\').enableMenuItems(enable);
		}
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		if ($link.hasClass(\'form\') || $link.hasClass(\'delete\') || $link.hasClass(\'confirm\')) {
			openDialog($link);
		} else {
			if ($link.parent(\'li\').hasClass(\'view\')) {
				$l = $link.url();
				$l.attr(\'file\', $el.find(\'input.month\').val()+\'.html\');
				$l.param(\'year\', $el.find(\'input.year\').val());
				$l.param(\'preview\', $el.attr(\'id\'));
				$l.attr(\'hash\', $el.attr(\'id\'));
			}
			return true;
		}
	});
})
</script>
'; ?>

<ul id="websiteMenu">
	<?php if ($this->_tpl_vars['permissions']['update'] == true): ?><li class="b_edit"><a href="admin/booking/change/?width=800&height=600" class="form">Bearbeiten</a></li><?php endif; ?>
	<li class="view separator"><a href="admin/booking/list/">Im Kalender anzeigen</a></li>
	<?php if ($this->_tpl_vars['permissions']['transfer'] == true): ?><li class="transfer"><a href="admin/booking/transfer/?width=350&height=150" class="confirm">In den Kalender übernehmen</a></li><?php endif; ?>
<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['e']):
?>
	<li class="<?php echo $this->_tpl_vars['e']['class']; ?>
"><a href="<?php echo $this->_tpl_vars['e']['link']; ?>
"<?php if ($this->_tpl_vars['e']['type'] != 'site'): ?> class="form"<?php endif; ?>><?php echo $this->_tpl_vars['e']['title']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
	<?php if ($this->_tpl_vars['permissions']['delete'] == true): ?><li class="b_delete separator"><a href="admin/booking/delete/?width=350&height=150" class="delete">Stornieren</a></li><?php endif; ?>
</ul>