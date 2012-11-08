<?php /* Smarty version 2.6.22, created on 2010-09-05 21:13:25
         compiled from add_booking_calendar.html */ ?>
<ul>
	<li>ReNr:<?php $_from = $this->_tpl_vars['acc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['r']):
?> <a href="accounting/list/<?php echo $this->_tpl_vars['r']->created->Y; ?>
.html"><?php echo $this->_tpl_vars['r']->id; ?>
</a><?php endforeach; endif; unset($_from); ?></li>
	<?php $_from = $this->_tpl_vars['acc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['r']):
?>
	<li><?php if ($this->_tpl_vars['r']->type == 'Anmeldung'): ?>Kaution<?php else: ?>Rechnung<?php endif; ?>: <?php echo $this->_tpl_vars['r']->value; ?>
 € <img src="images/buttons/button_<?php if ($this->_tpl_vars['r']->paid !== false): ?>1<?php else: ?>0<?php endif; ?>.png" /></li>
	<?php endforeach; endif; unset($_from); ?>
</ul>