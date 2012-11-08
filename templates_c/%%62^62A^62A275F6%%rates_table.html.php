<?php /* Smarty version 2.6.22, created on 2010-08-14 01:14:41
         compiled from rates_table.html */ ?>
<div id="rates">
<table>
<thead>
    <tr>
        <th>Aktiv</th>
        <th>Betrag</th>
        <th>Farbe</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['rates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rate']):
?>
    <tr id="<?php echo $this->_tpl_vars['rate']['id']; ?>
">
        <td><?php if ($this->_tpl_vars['permissions']['activate'] == true): ?><a href="form/booking/rate_activate/<?php echo $this->_tpl_vars['rate']['id']; ?>
.html" class="toggle <?php if ($this->_tpl_vars['rate']['active'] == 1): ?>active<?php endif; ?>"><?php endif; ?><img src="images/buttons/button_<?php echo $this->_tpl_vars['rate']['active']; ?>
.png" /><?php if ($this->_tpl_vars['permissions']['activate'] == true): ?></a><?php endif; ?></td>
        <td><?php echo $this->_tpl_vars['rate']['value']; ?>
 €</td>
        <td><?php if ($this->_tpl_vars['rate']['color'] != ''): ?><div class="color" style="background-color:#<?php echo $this->_tpl_vars['rate']['color']; ?>
"></div><?php endif; ?></td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php if ($this->_tpl_vars['permissions']['update'] === true): ?>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#rates\').find(\'tr\').contextMenu({
		menu: \'rateMenu\'
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		openDialog($link);
	});
})
</script>
'; ?>

<?php endif; ?>
</div>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
	<?php if ($this->_tpl_vars['permissions']['create'] === true): ?>
<a class="dialog button form" href="admin/booking/rate_create/?width=500&height=320" title="Tarif hinzufügen">Tarif hinzufügen</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['update'] === true): ?>
<ul id="rateMenu" class="contextMenu">
    <li class="edit"><a href="admin/booking/rate_update/?width=500&height=320" class="form">Bearbeiten</a></li>
</ul>
	<?php endif; ?>
<?php endif; ?>