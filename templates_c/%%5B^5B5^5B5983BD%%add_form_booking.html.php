<?php /* Smarty version 2.6.22, created on 2010-08-13 23:05:21
         compiled from add_form_booking.html */ ?>
<li><label for="bail">Kaution</label> <input type="Text" name="bail" value="<?php echo $this->_tpl_vars['renr']['value']; ?>
"<?php if ($this->_tpl_vars['renr']['paid'] === true): ?> readonly="readonly"<?php endif; ?>/> € (wird verbucht unter <strong>ReNr <?php echo $this->_tpl_vars['renr']['number']; ?>
</strong>)<input type="hidden" name="renr" value="<?php echo $this->_tpl_vars['renr']['number']; ?>
" /></li>