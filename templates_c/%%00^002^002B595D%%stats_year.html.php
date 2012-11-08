<?php /* Smarty version 2.6.22, created on 2010-08-24 22:41:29
         compiled from stats_year.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<div id="s_year">
<?php endif; ?>
<h2><?php echo $this->_tpl_vars['year']; ?>
: <?php echo $this->_tpl_vars['rev']['total']; ?>
 € bei <?php echo $this->_tpl_vars['nights']['total']; ?>
 Übernachtungen</h2>
<?php if ($this->_tpl_vars['noforecast'] === false): ?>
<table class="stat">
<thead>
	<tr>
		<th>Monat</th>
		<th>Einnahmen</th>
		<th>Übernachtungen</th>
	</tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['months']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m'] => $this->_tpl_vars['name']):
?>
	<tr>
		<td><a href="admin/stat/list/month/<?php echo $this->_tpl_vars['m']; ?>
.html?width=900&height=650" class="dialog"><?php echo $this->_tpl_vars['name']; ?>
</a></td>
		<td><?php if ($this->_tpl_vars['rev'][$this->_tpl_vars['m']]['total'] == $this->_tpl_vars['rev_e'][$this->_tpl_vars['m']]['total']): ?><?php echo $this->_tpl_vars['rev'][$this->_tpl_vars['m']]['total']; ?>
 €<?php else: ?><?php if ($this->_tpl_vars['rev'][$this->_tpl_vars['m']]['total'] == 0): ?><em><?php echo $this->_tpl_vars['rev_e'][$this->_tpl_vars['m']]['total']; ?>
 €</em><?php else: ?><?php echo $this->_tpl_vars['rev'][$this->_tpl_vars['m']]['total']; ?>
 € <em>(<?php echo $this->_tpl_vars['rev_e'][$this->_tpl_vars['m']]['total']; ?>
 €)</em><?php endif; ?><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['nights'][$this->_tpl_vars['m']] == $this->_tpl_vars['nights_e'][$this->_tpl_vars['m']]): ?><?php echo $this->_tpl_vars['nights'][$this->_tpl_vars['m']]; ?>
<?php else: ?><?php if ($this->_tpl_vars['nights'][$this->_tpl_vars['m']] == 0): ?><em><?php echo $this->_tpl_vars['nights_e'][$this->_tpl_vars['m']]; ?>
</em><?php else: ?><?php echo $this->_tpl_vars['nights'][$this->_tpl_vars['m']]; ?>
 <em>(<?php echo $this->_tpl_vars['nights_e'][$this->_tpl_vars['m']]; ?>
)</em><?php endif; ?><?php endif; ?></td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<div class="chart">
	<img src="modules/stat/charts/einnahmen_<?php echo $this->_tpl_vars['year']; ?>
.png" /><br/><img src="modules/stat/charts/uebernachtungen_<?php echo $this->_tpl_vars['year']; ?>
.png" />
</div>
<?php else: ?>
<div class="warning">Keine Daten für dieses Jahr vorhanden!</div>
<?php endif; ?>
<h2>Aufteilung der Belegungen</h2>
<?php if ($this->_tpl_vars['nodata'] === false): ?>
<table class="stat">
<thead>
	<tr>
		<th>Monat</th>
	<?php $_from = $this->_tpl_vars['rates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rate']):
?>
		<th><?php echo $this->_tpl_vars['rate']; ?>
<?php if ($this->_tpl_vars['rate'] != 'pauschal'): ?> €<?php endif; ?></th>
	<?php endforeach; endif; unset($_from); ?>
	</tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['months']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m'] => $this->_tpl_vars['name']):
?>
	<tr>
		<td><?php echo $this->_tpl_vars['name']; ?>
</td>
	<?php $_from = $this->_tpl_vars['rates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rate']):
?>
		<td><?php if ($this->_tpl_vars['rev'][$this->_tpl_vars['m']]['total'] == 0): ?>??<?php else: ?><?php echo $this->_tpl_vars['rev_r'][$this->_tpl_vars['m']][$this->_tpl_vars['rate']]; ?>
 %<?php endif; ?></td>
	<?php endforeach; endif; unset($_from); ?>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<div class="chart">
	<img src="modules/stat/charts/tarife_<?php echo $this->_tpl_vars['year']; ?>
.png" />
</div>
<?php else: ?>
<div class="warning">Keine Daten für dieses Jahr vorhanden!</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
</div>
<?php endif; ?>