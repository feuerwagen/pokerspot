<?php /* Smarty version 2.6.22, created on 2010-08-31 17:40:45
         compiled from booking_info.html */ ?>
<div class="booking<?php if ($this->_tpl_vars['b']->mark == true): ?> mark<?php endif; ?><?php if ($this->_tpl_vars['b']->type == 'programm'): ?> event<?php endif; ?>" id="<?php echo $this->_tpl_vars['id']; ?>
">
	<div class="houses"><?php $_from = $this->_tpl_vars['b']->houses; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['house']):
?><img src="images/houses/<?php echo $this->_tpl_vars['house']; ?>
.png" title="<?php echo $this->_tpl_vars['houses'][$this->_tpl_vars['house']]; ?>
" /><?php endforeach; endif; unset($_from); ?></div>
	<div class="info">
		<?php if ($this->_tpl_vars['b']->organisation != ''): ?>
		<h3><?php echo $this->_tpl_vars['b']->organisation; ?>
</h3>
		<?php echo $this->_tpl_vars['b']->name; ?>

		<?php else: ?>
		<h3><?php echo $this->_tpl_vars['b']->name; ?>
</h3>
		<?php endif; ?>
		<ul>
			<li><?php echo $this->_tpl_vars['b']->start->returnDate('j.n.Y'); ?>
 – <?php echo $this->_tpl_vars['b']->end->returnDate('j.n.Y'); ?>
</li>
			<?php if ($this->_tpl_vars['b']->phone != ''): ?><li>Telefon: <?php echo $this->_tpl_vars['b']->phone; ?>
</li><?php endif; ?>
			<?php if ($this->_tpl_vars['b']->rate != ''): ?><li>Tarif: <?php echo $this->_tpl_vars['b']->rate; ?>
<?php if ($this->_tpl_vars['b']->rate != 'pauschal'): ?> €<?php else: ?><?php if ($this->_tpl_vars['b']->price > 0): ?> (<?php echo $this->_tpl_vars['b']->price; ?>
 €)<?php endif; ?><?php endif; ?></li><?php endif; ?>
			<?php if ($this->_tpl_vars['b']->guests > 0): ?><li>Personen: <?php echo $this->_tpl_vars['b']->guests; ?>
</li><?php endif; ?>
		</ul>
		<?php echo $this->_tpl_vars['b']->additional; ?>

		<?php if ($this->_tpl_vars['b']->comment != ''): ?><p><?php echo $this->_tpl_vars['b']->comment; ?>
</p><?php endif; ?>
	</div>
	<p class="sign"><?php echo $this->_tpl_vars['b']->user->realname; ?>
 am <?php echo $this->_tpl_vars['b']->created['date']->returnDate('j.n.Y'); ?>
</p>
</div>