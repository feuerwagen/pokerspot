<?php /* Smarty version 2.6.22, created on 2010-09-07 15:26:15
         compiled from info.html */ ?>
<h2>Systemkonfiguration</h2>
<h3>Chispa</h3>
<table>
<thead>
    <tr>
        <th>Variable</th>
        <th>Wert</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td>Chispa Version</td>
        <td><?php echo $this->_tpl_vars['chispa']['version']; ?>
</td>
    </tr>
    <tr>
        <td>Server-Pfad</td>
        <td><?php echo $this->_tpl_vars['chispa']['path']; ?>
</td>
    </tr>
    <tr>
        <td>HTML-Pfad</td>
        <td><?php echo $this->_tpl_vars['chispa']['htmlpath']; ?>
</td>
    </tr>
</tbody>
</table>
<h3>Installiertes System</h3>
<table>
<thead>
    <tr>
        <th>Variable</th>
        <th>Wert</th>
    </tr>
</thead>
<tbody>
	<tr>
        <td>Name</td>
        <td><?php echo $this->_tpl_vars['site']['title']; ?>
</td>
    </tr>
	<tr>
        <td>Version</td>
        <td><?php echo $this->_tpl_vars['site']['version']; ?>
</td>
    </tr>
	<tr>
        <td>Frontend</td>
        <td><?php if ($this->_tpl_vars['site']['has_frontend'] === true): ?>Ja<?php else: ?>Nein<?php endif; ?></td>
    </tr>
    <tr>
        <td>Anzahl Benutzer</td>
        <td><?php echo $this->_tpl_vars['site']['users']; ?>
</td>
    </tr>
    <tr>
        <td>Anzahl der Module (davon aktiv)</td>
        <td><?php echo $this->_tpl_vars['site']['modules']['all']; ?>
 (<?php echo $this->_tpl_vars['site']['modules']['active']; ?>
)</td>
    </tr>
</tbody>
</table>
<h3>Server</h3>
<table>
<thead>
    <tr>
        <th>Variable</th>
        <th>Wert</th>
    </tr>
</thead>
<tbody>
    <tr>
		<td>Server-Betriebssystem</td>
        <td><?php echo $this->_tpl_vars['server']['type']; ?>
</td>
    </tr>
    <tr>
        <td>Datenbank</td>
        <td>MySQL <?php echo $this->_tpl_vars['server']['db']; ?>
</td>
    </tr>
    <tr>
        <td>PHP-Version</td>
        <td><?php echo $this->_tpl_vars['server']['php']['version']; ?>
</td>
    </tr>
    <tr>
        <td>safe_mode</td>
        <td><?php if ($this->_tpl_vars['server']['php']['safe_mode'] == 1): ?><span class="false">An<?php else: ?><span class="true">Aus<?php endif; ?></span></td>
    </tr>
    <tr>
        <td>magic_quotes_gpc</td>
        <td><?php if ($this->_tpl_vars['server']['php']['magic_quotes_gpc'] == 1): ?>An<?php else: ?>Aus<?php endif; ?></td>
    </tr>
    <tr>
        <td>magic_quotes_runtime</td>
        <td><?php if ($this->_tpl_vars['server']['php']['magic_quotes_runtime'] == 1): ?>An<?php else: ?>Aus<?php endif; ?></td>
    </tr>
    <tr>
        <td>gpc_order</td>
        <td><?php if ($this->_tpl_vars['server']['php']['gpc_order'] == 1): ?>An<?php else: ?>Aus<?php endif; ?></td>
    </tr>
    <tr>
        <td>memory_limit</td>
        <td><?php echo $this->_tpl_vars['server']['php']['memory_limit']; ?>
</td>
    </tr>
    <tr>
        <td>max_execution_time</td>
        <td><?php echo $this->_tpl_vars['server']['php']['max_execution_time']; ?>
 Sekunden</td>
    </tr>
    <tr>
        <td>Deaktivierte Funktionen</td>
        <td><?php if (strlen ( $this->_tpl_vars['server']['php']['disabled_functions'] ) > 0): ?><span class="false"><?php echo $this->_tpl_vars['server']['php']['disabled_functions']; ?>
<?php else: ?><span class="true">Keine<?php endif; ?></span></td>
    </tr>   
    <tr>
        <td>sql.safe_mode</td>
        <td><?php if ($this->_tpl_vars['server']['php']['sql_safe_mode'] == 1): ?><span class="false">An<?php else: ?><span class="true">Aus<?php endif; ?></span></td>
    </tr>
    <tr>
        <td>GD-Bibliothek</td>
        <td>
			<table>
			<thead>
		        <tr>
		            <th>Einstellung</th>
		            <th>Wert</th>
		        </tr>
			</thead>
			<tbody>
			<?php $_from = $this->_tpl_vars['server']['gd']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['name'] => $this->_tpl_vars['value']):
?>
				<tr>
		            <td><?php echo $this->_tpl_vars['name']; ?>
</td>
		            <td><?php echo $this->_tpl_vars['value'][0]; ?>
</td>
		        </tr>
			<?php endforeach; endif; unset($_from); ?>
			</tbody>
			</table>
		</td>
    </tr>
    <tr>
        <td>include_path</td>
        <td><?php echo $this->_tpl_vars['server']['php']['include_path']; ?>
</td>
    </tr>
</tbody>
</table>

<h2>Errorlog</h2>
<textarea class="system" id="errorlog" readonly="readonly" style="height:<?php if ($this->_tpl_vars['errorlog'] == 'Errorlog ist leer!' || $this->_tpl_vars['errorlog'] === false): ?>1.3em<?php else: ?>20em<?php endif; ?>"><?php if ($this->_tpl_vars['errorlog'] === false): ?>Kein Errorlog gefunden!<?php else: ?><?php echo $this->_tpl_vars['errorlog']; ?>
<?php endif; ?></textarea>
<br/><a href="form/system/cleanlog/error.html" id="cleanlog">Errorlog leeren</a>

<h2>Changelog</h2>
<textarea class="system" readonly="readonly" style="height:<?php if ($this->_tpl_vars['changelog'] === false): ?>1.3em<?php else: ?>20em<?php endif; ?>"><?php if ($this->_tpl_vars['changelog'] === false): ?>Kein Changelog gefunden!<?php else: ?><?php echo $this->_tpl_vars['changelog']; ?>
<?php endif; ?></textarea>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#cleanlog\').button().click(function() {
		$.post(this.href, {}, function() {
			$(\'#errorlog\').empty().append(\'Errorlog wurde geleert!\');
		});
		return false;
	});
});
</script>
'; ?>