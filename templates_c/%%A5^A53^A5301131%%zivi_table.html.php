<?php /* Smarty version 2.6.22, created on 2010-08-22 16:53:42
         compiled from zivi_table.html */ ?>
<div id="ziviDays">
<h3>Verfügbare Arbeits- und Urlaubstage</h3>
<table title="Die Zahlen in der Zeile „Urlaub” geben an, wie viele Tage Resturlaub der Zivi noch zur Verfügung hat. In den Zeilen „Ausgleichstage” und „Wochenenddienst” stehen jeweils die Anzahl der Tage, die dem Zivi noch zustehen bzw. zu leisten sind, um einen Ausgleich zu erreichen.">
<thead>
    <tr>
        <th></th>
		<th>Zivi</th>
        <th>Betreuer</th>
        <th>Übereinstimmend</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['days']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['type'] => $this->_tpl_vars['c']):
?>
    <tr>
        <td><?php echo $this->_tpl_vars['names'][$this->_tpl_vars['type']]; ?>
</td>
		<td><?php echo $this->_tpl_vars['c']['zivi']; ?>
</td>
		<td><?php echo $this->_tpl_vars['c']['admin']; ?>
</td>
		<td><?php echo $this->_tpl_vars['c']['confirmed']; ?>
</td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<p>Zählung ab dem <?php echo $this->_tpl_vars['reset']->returnDate('j.n.Y'); ?>
</p>
</div>
<?php echo '
<script>
$(document).ready(function() {	
	// tooltip
	$(\'#ziviDays\').find(\'table\').tipTip({maxWidth: "400px"});
	
	// reset button
'; ?>

	<?php if ($this->_tpl_vars['permission'] === true): ?>
	$('div.ui-dialog-buttonpane').append('<a class="dialog button confirm" href="admin/zivi/reset/?width=350&height=150" title="Rechner zurücksetzen">Rechner zurücksetzen</a>').find('a.button').button();
	<?php endif; ?>
<?php echo '
});
</script>
'; ?>