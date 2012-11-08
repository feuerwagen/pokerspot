<?php /* Smarty version 2.6.22, created on 2010-08-24 22:46:08
         compiled from stats_forecast.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<div id="s_forecast">
<?php endif; ?>
<h2>Prognose für <?php echo $this->_tpl_vars['year']; ?>
 (Verlauf)</h2>
<?php if ($this->_tpl_vars['nodata'] === false): ?>
<table class="stat">
	<tr>
		<th>Datum</th>
		<th>Einnahmen</th>
		<th>Übernachtungen</th>
	</tr>
<?php $_from = $this->_tpl_vars['stat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['s']):
?>
	<tr>
		<td><?php echo $this->_tpl_vars['s']['date']; ?>
</td>
		<td><?php echo $this->_tpl_vars['s']['rev']; ?>
 €</td>
		<td><?php echo $this->_tpl_vars['s']['nights']; ?>
</td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<div class="chart">
	<img src="modules/stat/charts/p_einnahmen_<?php echo $this->_tpl_vars['year']; ?>
.png" /><br/><img src="modules/stat/charts/p_uebernachtungen_<?php echo $this->_tpl_vars['year']; ?>
.png" />
</div>
<?php else: ?>
<div class="warning">Keine Daten für dieses Jahr vorhanden!</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
</div>
<?php endif; ?>