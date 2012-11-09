<?php /* Smarty version 2.6.22, created on 2012-11-08 19:25:21
         compiled from tables_list.html */ ?>
<div>
<h3>Tische</h3>
<ul>
<?php $_from = $this->_tpl_vars['tables']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['t']):
?>
	<li class="<?php if ($this->_tpl_vars['t'] === $this->_tpl_vars['current']): ?>active<?php endif; ?>" id="ptable<?php echo $this->_tpl_vars['id']; ?>
" data-idtable="<?php echo $this->_tpl_vars['id']; ?>
">
		<?php if ($this->_tpl_vars['t']->active === true): ?><span class="unread">&bull;</span><?php endif; ?>
		<h5><a href="#"><?php echo $this->_tpl_vars['t']->title; ?>
</a></h5>
		Plätze: <?php echo $this->_tpl_vars['t']->seats; ?>
 (<?php echo $this->_tpl_vars['t']->seats; ?>
 frei) – Blinds: <?php echo $this->_tpl_vars['t']->blinds['small']; ?>
/<?php echo $this->_tpl_vars['t']->blinds['big']; ?>

	</li>
<?php endforeach; endif; unset($_from); ?>
</ul>
</div>
<?php echo '
<script>
$(document).ready(function() {	
	// load first table into main window
	//$(\'#pokertable\').poker($(\'#sidebar\').find(\'li.active\').data(\'idtable\'));
});
</script>
'; ?>