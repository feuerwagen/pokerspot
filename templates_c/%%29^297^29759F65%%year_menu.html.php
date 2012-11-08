<?php /* Smarty version 2.6.22, created on 2010-08-24 22:14:01
         compiled from year_menu.html */ ?>
<ul id="yearMenu" class="noIcons">
<?php $_from = $this->_tpl_vars['years']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['year']):
?>
	<li class="<?php echo $this->_tpl_vars['year']; ?>
"><a href="form/stat/set/<?php echo $this->_tpl_vars['year']; ?>
.html"><?php echo $this->_tpl_vars['year']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
</ul>	
<?php echo '
<script>
$(document).ready(function(){
	// show select menu for years (in submenu)
	$(".select").hoverMenu({
		menu: "yearMenu",
		position: "bottom",
		positionType: "fixed",
		animation: "slide",
		showArrow: false,
		onShow: function($el) {
			$(\'#yearMenu\').enableMenuItems();
			$(\'#yearMenu\').disableMenuItems([$el.find(\'a\').html()]);
		}
	}, function($link, $el) {
		var o = ($el.attr(\'id\') == \'selStart\') ? \'start\' : \'end\';
		$.post($link.attr(\'href\'), {option: o, call: \'ajax\'}, function(data) {
			processJson(data);
			$el.find(\'a\').html($link.html());
		}, \'json\');
	});
});
</script>
'; ?>