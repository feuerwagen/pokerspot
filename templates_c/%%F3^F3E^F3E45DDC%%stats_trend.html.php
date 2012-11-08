<?php /* Smarty version 2.6.22, created on 2010-08-24 22:49:35
         compiled from stats_trend.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<div id="s_trend">
<?php endif; ?>
<h2>Übernachtungen <?php echo $this->_tpl_vars['start']; ?>
 – <?php echo $this->_tpl_vars['end']; ?>
:</h2>
<table class="stat">
	<tr>
		<th>Jahr</th>
		<th>Einnahmen</th>
		<th>Übernachtungen</th>
	</tr>
<?php $_from = $this->_tpl_vars['rev']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['year'] => $this->_tpl_vars['r']):
?>
	<tr>
		<td><?php echo $this->_tpl_vars['year']; ?>
</td>
		<td><?php if ($this->_tpl_vars['r']['total'] == $this->_tpl_vars['rev_e'][$this->_tpl_vars['year']]['total']): ?><?php echo $this->_tpl_vars['r']['total']; ?>
 €<?php else: ?><?php if ($this->_tpl_vars['r']['total'] == 0): ?><em><?php echo $this->_tpl_vars['rev_e'][$this->_tpl_vars['year']]['total']; ?>
 €</em><?php else: ?><?php echo $this->_tpl_vars['r']['total']; ?>
 € <em>(<?php echo $this->_tpl_vars['rev_e'][$this->_tpl_vars['year']]['total']; ?>
 €)</em><?php endif; ?><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['nights'][$this->_tpl_vars['year']]['total'] == $this->_tpl_vars['nights_e'][$this->_tpl_vars['year']]['total']): ?><?php echo $this->_tpl_vars['nights'][$this->_tpl_vars['year']]['total']; ?>
<?php else: ?><?php if ($this->_tpl_vars['nights'][$this->_tpl_vars['year']]['total'] == 0): ?><em><?php echo $this->_tpl_vars['nights_e'][$this->_tpl_vars['year']]['total']; ?>
</em><?php else: ?><?php echo $this->_tpl_vars['nights'][$this->_tpl_vars['year']]['total']; ?>
 <em>(<?php echo $this->_tpl_vars['nights_e'][$this->_tpl_vars['year']]['total']; ?>
)</em><?php endif; ?><?php endif; ?></td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<div class="chart">
	<img src="modules/stat/charts/einnahmen_jahre_<?php echo $this->_tpl_vars['start']; ?>
_<?php echo $this->_tpl_vars['end']; ?>
.png" /><br/><img src="modules/stat/charts/uebernachtungen_jahre_<?php echo $this->_tpl_vars['start']; ?>
_<?php echo $this->_tpl_vars['end']; ?>
.png" />
</div>
<h2>Aufteilung der Belegungen</h2>
<?php if ($this->_tpl_vars['nodata'] === false): ?>
<table class="stat">
	<tr>
		<th>Jahr</th>
	<?php $_from = $this->_tpl_vars['rates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rate']):
?>
		<th><?php echo $this->_tpl_vars['rate']; ?>
<?php if ($this->_tpl_vars['rate'] != 'pauschal'): ?> €<?php endif; ?></th>
	<?php endforeach; endif; unset($_from); ?>
	</tr>
<?php $_from = $this->_tpl_vars['rev']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['year'] => $this->_tpl_vars['r']):
?>
	<tr>
		<td><?php echo $this->_tpl_vars['year']; ?>
</td>
	<?php $_from = $this->_tpl_vars['rates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rate']):
?>
		<td><?php if ($this->_tpl_vars['rev_r'][$this->_tpl_vars['year']] == ''): ?>??<?php else: ?><?php echo $this->_tpl_vars['rev_r'][$this->_tpl_vars['year']][$this->_tpl_vars['rate']]; ?>
 %<?php endif; ?></td>
	<?php endforeach; endif; unset($_from); ?>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<div class="chart">
	<img src="modules/stat/charts/tarife_jahre_<?php echo $this->_tpl_vars['start']; ?>
_<?php echo $this->_tpl_vars['end']; ?>
.png" />
</div>
<?php else: ?>
<div class="warning">Keine Daten für den gewählten Zeitraum vorhanden!</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
</div>
<?php endif; ?>