<?php /* Smarty version 2.6.22, created on 2010-08-12 00:45:44
         compiled from submenu.html */ ?>
<?php if (is_array ( $this->_tpl_vars['subnav'] )): ?>
    <ul id="subnav">
    <?php $_from = $this->_tpl_vars['subnav']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>    
        <li<?php echo $this->_tpl_vars['item']['attr']; ?>
><?php if ($this->_tpl_vars['item']['link'] !== false): ?><a href="<?php echo $this->_tpl_vars['item']['link']; ?>
"><?php endif; ?><?php echo $this->_tpl_vars['item']['title']; ?>
<?php if ($this->_tpl_vars['item']['link'] !== false): ?></a><?php endif; ?></li>
    <?php endforeach; endif; unset($_from); ?>
    </ul>
<?php endif; ?>