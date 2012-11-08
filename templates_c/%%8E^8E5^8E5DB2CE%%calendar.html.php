<?php /* Smarty version 2.6.22, created on 2010-09-03 21:18:53
         compiled from calendar.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<div id="bookings">
<?php endif; ?>
<table>
	<thead>
		<tr>
			<th></th>
			<th></th>
			<?php echo $this->_tpl_vars['cols']['thead']; ?>

			<th colspan="<?php echo $this->_tpl_vars['maxcols']; ?>
"><?php echo $this->_tpl_vars['month']; ?>
</th>
		</tr>
	</thead>
	<tbody>
<?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['day'] => $this->_tpl_vars['row']):
?>
		<tr<?php if ($this->_tpl_vars['mark'][$this->_tpl_vars['day']] === true): ?> class="weekend"<?php endif; ?>>
			<td><?php echo $this->_tpl_vars['names'][$this->_tpl_vars['day']]; ?>
</td>
			<td><?php echo $this->_tpl_vars['day']; ?>
</td>
			<?php echo $this->_tpl_vars['cols']['tbody'][$this->_tpl_vars['day']]; ?>

	<?php $_from = $this->_tpl_vars['row']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['b']):
?>
			<td class="booking<?php if ($this->_tpl_vars['b']->mark == true): ?> mark<?php endif; ?><?php if ($this->_tpl_vars['b']->type == 'programm'): ?> event<?php endif; ?><?php if ($this->_tpl_vars['b']->class != ''): ?><?php echo $this->_tpl_vars['b']->class; ?>
<?php endif; ?>" id="<?php echo $this->_tpl_vars['id']; ?>
" rowspan="<?php echo $this->_tpl_vars['b']->duration; ?>
">
		<?php if ($this->_tpl_vars['b']->outerStart === true || $this->_tpl_vars['b']->outerEnd === true): ?>
				<p class="sign">
					<?php if ($this->_tpl_vars['b']->outerStart === true): ?>Seit dem <?php echo $this->_tpl_vars['b']->start->returnDate('j.n.Y'); ?>
<?php endif; ?>		
					<?php if ($this->_tpl_vars['b']->outerEnd === true): ?>Bis zum <?php echo $this->_tpl_vars['b']->end->returnDate('j.n.Y'); ?>
<?php endif; ?>	
				</p>
		<?php endif; ?>
				<div class="houses"><?php $_from = $this->_tpl_vars['b']->houses; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['house']):
?><img src="images/houses/<?php echo $this->_tpl_vars['house']; ?>
.png" title="<?php echo $this->_tpl_vars['houses'][$this->_tpl_vars['house']]; ?>
" /><?php endforeach; endif; unset($_from); ?></div>
				<div class="info">
					<?php if ($this->_tpl_vars['b']->organisation != ''): ?>
					<h3><?php echo $this->_tpl_vars['b']->organisation; ?>
</h3>
					<?php if ($this->_tpl_vars['b']->email != ''): ?><a href="mailto:<?php echo $this->_tpl_vars['b']->email; ?>
"><?php endif; ?><?php echo $this->_tpl_vars['b']->name; ?>
<?php if ($this->_tpl_vars['b']->email != ''): ?></a><?php endif; ?>
					<?php else: ?>
					<h3><?php if ($this->_tpl_vars['b']->email != ''): ?><a href="mailto:<?php echo $this->_tpl_vars['b']->email; ?>
"><?php endif; ?><?php echo $this->_tpl_vars['b']->name; ?>
<?php if ($this->_tpl_vars['b']->email != ''): ?></a><?php endif; ?></h3>
					<?php endif; ?>
					<ul>
						<?php if ($this->_tpl_vars['b']->phone != ''): ?><li>Telefon: <?php echo $this->_tpl_vars['b']->phone; ?>
</li><?php endif; ?>
						<?php if ($this->_tpl_vars['b']->rate != ''): ?><li>Tarif: <?php echo $this->_tpl_vars['b']->rate; ?>
<?php if ($this->_tpl_vars['b']->rate != 'pauschal'): ?> €<?php else: ?><?php if ($this->_tpl_vars['b']->price > 0): ?> (<?php echo $this->_tpl_vars['b']->price; ?>
 €)<?php endif; ?><?php endif; ?></li><?php endif; ?>
						<?php if ($this->_tpl_vars['b']->guests > 0): ?><li>Personen: <?php echo $this->_tpl_vars['b']->guests; ?>
</li><?php endif; ?>
						<?php if (is_array ( $this->_tpl_vars['b']->visits )): ?><li class="visits">Gäste:<br/>
							<?php $_from = $this->_tpl_vars['b']->visits; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
								<span title="<?php echo $this->_tpl_vars['v']['comment']; ?>
" id="v<?php echo $this->_tpl_vars['v']['id']; ?>
"><?php if ($this->_tpl_vars['v']['email'] != ''): ?><a href="mailto:<?php echo $this->_tpl_vars['v']['email']; ?>
"><?php endif; ?><?php echo $this->_tpl_vars['v']['name']; ?>
<?php if ($this->_tpl_vars['v']['email'] != ''): ?></a><?php endif; ?> (<?php if ($this->_tpl_vars['v']['organisation'] != ''): ?><?php echo $this->_tpl_vars['v']['organisation']; ?>
, <?php endif; ?><?php echo $this->_tpl_vars['v']['guests']; ?>
 P.)</span><br/>
							<?php endforeach; endif; unset($_from); ?>
						</li><?php endif; ?>
					</ul>
					<?php echo $this->_tpl_vars['b']->additional; ?>

					<?php if ($this->_tpl_vars['b']->comment != ''): ?><p><?php echo $this->_tpl_vars['b']->comment; ?>
</p><?php endif; ?>
				</div>
				<p class="sign"><?php echo $this->_tpl_vars['b']->user->realname; ?>
 am <?php echo $this->_tpl_vars['b']->created['date']->returnDate('j.n.Y'); ?>
</p>
			</td>
	<?php endforeach; endif; unset($_from); ?>
	<?php unset($this->_sections['f']);
$this->_sections['f']['name'] = 'f';
$this->_sections['f']['loop'] = is_array($_loop=$this->_tpl_vars['fill'][$this->_tpl_vars['day']]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['f']['show'] = true;
$this->_sections['f']['max'] = $this->_sections['f']['loop'];
$this->_sections['f']['step'] = 1;
$this->_sections['f']['start'] = $this->_sections['f']['step'] > 0 ? 0 : $this->_sections['f']['loop']-1;
if ($this->_sections['f']['show']) {
    $this->_sections['f']['total'] = $this->_sections['f']['loop'];
    if ($this->_sections['f']['total'] == 0)
        $this->_sections['f']['show'] = false;
} else
    $this->_sections['f']['total'] = 0;
if ($this->_sections['f']['show']):

            for ($this->_sections['f']['index'] = $this->_sections['f']['start'], $this->_sections['f']['iteration'] = 1;
                 $this->_sections['f']['iteration'] <= $this->_sections['f']['total'];
                 $this->_sections['f']['index'] += $this->_sections['f']['step'], $this->_sections['f']['iteration']++):
$this->_sections['f']['rownum'] = $this->_sections['f']['iteration'];
$this->_sections['f']['index_prev'] = $this->_sections['f']['index'] - $this->_sections['f']['step'];
$this->_sections['f']['index_next'] = $this->_sections['f']['index'] + $this->_sections['f']['step'];
$this->_sections['f']['first']      = ($this->_sections['f']['iteration'] == 1);
$this->_sections['f']['last']       = ($this->_sections['f']['iteration'] == $this->_sections['f']['total']);
?>
			<td></td>
	<?php endfor; endif; ?>
		</tr>
<?php endforeach; endif; unset($_from); ?>
	</tbody>
</table>
<?php echo '
<script>
$(document).ready(function(){
	// show tooltips for houses
	$(\'.houses img\').tipTip();
	$(\'.visits span\').tipTip();
	
	// delete button for event visits
	$(\'#bookings\').find(\'.visits span\').contextMenu({
		menu: \'visitMenu\'
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		openDialog($link);
	});
	
	// show edit buttons on td hover
	$(\'#bookings\').find(\'td.booking\').hoverMenu({
		menu: \'bookingMenu\',
		onShow: function($el) {
			var enable = new Array(), disable = new Array(), remove = new Array();
			var $menu = $(\'#bookingMenu\');
			if ($el.hasClass(\'event\')) {
				enable.push(\'guests\');
				remove.push(\'evaluate\');
			} else {
				remove.push(\'guests\');
				enable.push(\'evaluate\');
			}	
'; ?>

		<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['e']):
?>
			<?php echo $this->_tpl_vars['e']['script']; ?>

		<?php endforeach; endif; unset($_from); ?>
<?php echo '
			if (disable.length > 0)
				$(\'#bookingMenu\').disableMenuItems(disable);
			if (remove.length > 0)
				$(\'#bookingMenu\').removeMenuItems(remove);
			if (enable.length > 0)
				$(\'#bookingMenu\').enableMenuItems(enable);
		}
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		if ($link.attr(\'class\') == \'\')
			return true;
		else
			openDialog($link);
	});
});
</script>
'; ?>

<?php if ($this->_tpl_vars['call'] != 'load'): ?>
</div>
<?php echo '
<script>
$(document).ready(function(){
	// show select menu for years (in submenu)
	$(\'#selYear\').hoverMenu({
		menu: \'yearMenu\',
		position: \'bottom\',
		positionType: \'fixed\',
		animation: \'slide\',
		showArrow: false,
		onShow: function($el) {
			$(\'#yearMenu\').enableMenuItems();
			$(\'#yearMenu\').disableMenuItems([$el.find(\'a\').html()]);
		}
	}, function($link, $el) {
		$.post($link.attr(\'href\'), {call: \'ajax\'}, function(data) {
			processJson($.parseJSON(data));
			$el.find(\'a\').html($link.html());
		});
	});
});
</script>
'; ?>

<ul id="bookingMenu">
	<?php if ($this->_tpl_vars['permissions']['update'] == true): ?><li class="edit"><a href="admin/booking/change/?width=800&height=600" class="form" title="Belegung bearbeiten">Bearbeiten</a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['guest'] == true): ?><li class="guests"><a href="admin/booking/vadd/?width=500&height=550" class="form" title="Gäste eintragen">Gäste&nbsp;eintragen</a></li><?php endif; ?>
<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['e']):
?>
	<li class="<?php echo $this->_tpl_vars['e']['class']; ?>
<?php if ($this->_tpl_vars['k'] == 0): ?> separator<?php endif; ?>"><a href="<?php echo $this->_tpl_vars['e']['link']; ?>
"<?php if ($this->_tpl_vars['e']['type'] != 'site'): ?> class="form"<?php endif; ?>><?php echo $this->_tpl_vars['e']['title']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
<!-- <li class="evaluate"><a href="#" class="form" title="Gruppe bewerten">Gruppe&nbsp;bewerten</a></li> -->
	<?php if ($this->_tpl_vars['permissions']['delete'] == true): ?><li class="delete separator"><a href="admin/booking/delete/?width=350&height=150" class="delete" title="Belegung stornieren">Stornieren</a></li><?php endif; ?>
</ul>
<ul id="yearMenu" class="noIcons">
<?php $_from = $this->_tpl_vars['years']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['year']):
?>
	<li class="<?php echo $this->_tpl_vars['year']; ?>
"><a href="form/booking/set/<?php echo $this->_tpl_vars['year']; ?>
.html"><?php echo $this->_tpl_vars['year']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
</ul>
<?php if ($this->_tpl_vars['permissions']['vdelete'] == true): ?>
<ul id="visitMenu">
	<li class="delete"><a href="admin/booking/vdelete/?width=350&height=150" class="delete" title="Anmeldung löschen">Löschen</a></li>
</ul>
<?php endif; ?>
<?php endif; ?>