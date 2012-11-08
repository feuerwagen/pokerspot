<?php /* Smarty version 2.6.22, created on 2010-09-04 00:55:10
         compiled from form_eval.html */ ?>
<div id="messages"></div>
<form action="form/evaluation/create/<?php echo $this->_tpl_vars['id']; ?>
.html" method="post" class="cmxform">
	<fieldset>
		<legend>Gruppe bewerten</legend>
		<ul>
			<li><label for="subject">Bewertung</label> <div id="stars" style="display:inline-block">
			        <select name="rate">
			            <option value="1">Übel</option>
			            <option value="2">Naja</option>
			            <option value="3">Geht so</option>
			            <option value="4">Gut</option>
			            <option value="5">Perfekt</option>
			        </select>
			    </div> <span id="cap"></span></li>
			<li><label for="comment">Kommentar</label> <textarea name="comment"></textarea></li>
		</ul>
	</fieldset>
</form>
<?php echo '
<script type="text/javascript">
$(document).ready(function() {
	$("#stars").stars({
		inputType: "select",
		captionEl: $("#cap"),
		cancelShow: false,
	});
});
</script>
'; ?>