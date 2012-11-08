<?php /* Smarty version 2.6.22, created on 2012-10-31 21:33:01
         compiled from mails_unread.html */ ?>
<h2>Neue Mails</h2>
<table id="mails_new">
<thead>
	<tr>
		<th>Belegung</th>
		<th>Absender</th>
		<th>Betreff</th>
		<th>Empfangen</th>
	</tr>
</thead>
<?php $_from = $this->_tpl_vars['mails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
<tbody>
	<tr id="m_<?php echo $this->_tpl_vars['m']->id; ?>
">
		<td><?php if (is_object ( $this->_tpl_vars['m']->booking )): ?><?php echo $this->_tpl_vars['m']->booking->getInfoString(); ?>
<?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['m']->name != ''): ?><?php echo $this->_tpl_vars['m']->name; ?>
 (<?php echo $this->_tpl_vars['m']->email; ?>
)<?php else: ?><?php echo $this->_tpl_vars['m']->email; ?>
<?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['m']->subject != '' || $this->_tpl_vars['m']->subject != 'Re:'): ?><a href="admin/mail/show/<?php echo $this->_tpl_vars['m']->id; ?>
.html"><?php echo $this->_tpl_vars['m']->subject; ?>
</a><?php endif; ?></td>
		<td><?php echo $this->_tpl_vars['m']->created['date']->returnDate('d.m.Y'); ?>
 <?php echo $this->_tpl_vars['m']->created['time']; ?>
</td>
	</tr>
</tbody>
<?php endforeach; endif; unset($_from); ?>
</table>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#mails_new\').tableHover({clickClass: \'click\'}).find(\'tbody tr\').tipTip({
		maxWidth:"600px", 
		defaultPosition:\'bottom\',
		position:\'bottom\',
		edgeOffset: -10,
		url: true,
		data: {call: \'ajax\'},
		enter: function(evt, opts) {
			opts.url = \'admin/mail/preview/\'+evt.attr(\'id\').substr(2)+\'.html\';
		}
	});
	$(\'#tiptip_loader\').html(\'ddddsssss\')
})
</script>
'; ?>