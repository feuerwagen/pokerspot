<?php /* Smarty version 2.6.22, created on 2010-08-24 15:40:35
         compiled from stats_bookings.html */ ?>
<h2>Belegungen im <?php echo $this->_tpl_vars['month']; ?>
 <?php echo $this->_tpl_vars['year']; ?>
</h2>
<h3>Gesamt: <?php echo $this->_tpl_vars['nights']; ?>
 Übernachtungen, <?php echo $this->_tpl_vars['rev']; ?>
 € Einnahmen</h3>
<table>
	<tr>
		<th>Gruppe</th>
		<th>von</th>
		<th>bis</th>
		<th>Nächte</th>
		<th>Personen</th>
		<th>Übernachtungen</th>
		<th>Tarif</th>
		<th>Betrag</th>
	</tr>
<?php $_from = $this->_tpl_vars['bookings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['b']):
?>
	<tr>
		<td>
			<?php if ($this->_tpl_vars['b']['booking']->organisation != ''): ?><strong><?php echo $this->_tpl_vars['b']['booking']->organisation; ?>
</strong><br><?php echo $this->_tpl_vars['b']['booking']->name; ?>

			<?php else: ?><strong><?php echo $this->_tpl_vars['b']['booking']->name; ?>
</strong><?php endif; ?>
		</td>
		<td><?php echo $this->_tpl_vars['b']['booking']->start->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['b']['booking']->end->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['b']['days']; ?>
</td>
		<td><?php echo $this->_tpl_vars['b']['booking']->guests; ?>
</td>
		<td><?php echo $this->_tpl_vars['b']['nights']; ?>
</td>
		<td><?php echo $this->_tpl_vars['b']['booking']->rate; ?>
<?php if ($this->_tpl_vars['b']['booking']->rate != 'pauschal'): ?> €<?php endif; ?></td>
		<td><?php echo $this->_tpl_vars['b']['rev']; ?>
 €</td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>