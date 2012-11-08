<?php /* Smarty version 2.6.22, created on 2010-09-01 23:29:42
         compiled from form_mail.html */ ?>
<div id="messages"></div>
<form action="form/mail/send/" method="post" class="cmxform">
	<fieldset>
		<legend>Empfänger</legend>
		<ul>
			<li><?php echo $this->_tpl_vars['receiver']; ?>
 <input type="hidden" name="idreply" value="<?php echo $this->_tpl_vars['idreply']; ?>
" /><input type="hidden" name="idbooking" value="<?php echo $this->_tpl_vars['idbooking']; ?>
" /></li>
		</ul>
	</fieldset>
	<?php if ($this->_tpl_vars['idbooking'] != ''): ?>
	<fieldset>
		<legend>Vorlagen</legend>
		<ul id="templates">
			<li><a href="admin/mail/template/night/<?php echo $this->_tpl_vars['idbooking']; ?>
.html">Bestätigung für Anmeldung (Übernachtung)</a></li>
			<li><a href="admin/mail/template/event/<?php echo $this->_tpl_vars['idbooking']; ?>
.html">Bestätigung für Anmeldung (Tagesveranstaltung)</a></li>
		</ul>
	</fieldset>
	<?php endif; ?>
	<fieldset>
		<legend>Nachricht</legend>
		<ul>
			<li><label for="subject">Betreff</label> <input type="Text" id="subject" name="subject" value="<?php echo $this->_tpl_vars['subject']; ?>
" /></li>
			<li><label for="text">Text</label> <textarea name="text" id="editor" style="height:200px"><?php echo $this->_tpl_vars['text']; ?>
</textarea></li>
		</ul>
	</fieldset>
</form>
<?php echo '
<script type="text/javascript">
$(document).ready(function() {
	// get mail templates
	$(\'#templates\').find(\'a\').click(function() {
		$(\'#subject\').val(\'Anmeldung auf Burg Balduinstein\');
		$(\'#editor\').load(this.href, {call: \'ajax\'});
		return false;
	});
	
	$(\'#editor\').tinymce({
		// Location of TinyMCE script
		script_url : $(\'base\').attr(\'href\')+\'script/tinymce/tiny_mce_gzip.php\',

		// General options
		theme : "advanced",
		skin : \'wp_theme\',
		language : "de",
		plugins : "safari,inlinepopups,paste",
		//content_css : "css/content.css",

		// Theme options
		theme_advanced_buttons1 : "cut,copy,pastetext,|,bold,italic,strikethrough,|,bullist,numlist,|,link,unlink",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : false,
		theme_advanced_statusbar_location : "bottom",

		paste_use_dialog : false,
		apply_source_formatting : false,
		forced_root_block : \'p\',
		force_br_newlines : false,
		force_p_newlines : true,	
		relative_urls : true,
		entity_encoding : "raw",
		width : "100%"		
	});
	
	$(\'#dialog\').find(\'form\').bind(\'form-pre-serialize\', function(e) {
	    tinyMCE.triggerSave();
	});
});
</script>
'; ?>