<?php /* Smarty version 2.6.22, created on 2012-11-01 03:55:26
         compiled from form_message.html */ ?>
<div id="messages"></div>
<form action="form/message/<?php echo $this->_tpl_vars['action']; ?>
/<?php echo $this->_tpl_vars['id']; ?>
.html" method="post" class="cmxform">
	<fieldset>
		<legend>Empfänger</legend>
		<ul>
			<li>
				<select name="user[]" size="3" multiple="multiple"<?php if ($this->_tpl_vars['id'] !== false): ?>disabled="disabled"<?php endif; ?>>
				<?php $_from = $this->_tpl_vars['user']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['u']):
?>
					<option value="<?php echo $this->_tpl_vars['u']->username; ?>
"<?php if (is_object ( $this->_tpl_vars['message']->receiver ) && $this->_tpl_vars['id'] === $this->_tpl_vars['message']->receiver->username): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['u']->realname; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
				</select>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>Nachricht</legend>
		<ul>
			<li><label for="subject">Betreff</label> <input type="Text" name="subject" value="<?php echo $this->_tpl_vars['message']->subject; ?>
" /></li>
			<li><label for="text">Text</label> <textarea name="text" id="editor"><?php echo $this->_tpl_vars['message']->text; ?>
</textarea></li>
		</ul>
	</fieldset>
</form>
<?php echo '
<script type="text/javascript">
$(document).ready(function() {
	$(\'#editor\').tinymce({
		// Location of TinyMCE script
		script_url : $(\'base\').attr(\'href\')+\'script/tinymce/tiny_mce_gzip.php\',

		// General options
		theme : "advanced",
		skin : \'wp_theme\',
		plugins : "safari,inlinepopups,paste",
		content_css : "/style/tinymce.css",

		// Theme options
		theme_advanced_buttons1 : "cut,copy,pastetext,|,bold,italic,strikethrough,|,bullist,numlist,|,link,unlink",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : false,

		paste_use_dialog : false,
		apply_source_formatting : true,
		forced_root_block : \'p\',
		force_br_newlines : false,
		force_p_newlines : true,
		invalid_elements: \'span\',	
		relative_urls : true,
		width : "100%"		
	});
	
	$(\'#dialog\').find(\'form\').bind(\'form-pre-serialize\', function(e) {
	    tinyMCE.triggerSave();
	});
});
</script>
'; ?>