<?php /* Smarty version 2.6.22, created on 2012-11-09 01:47:45
         compiled from tables.html */ ?>
<div id="sidebar">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'tables_list.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div id="main">
	<div id="fixed">
		<div id="messages"></div>
		<div id="pokertable">
			<div id="ptable">
				<div class="cards"><div>
					<div class="card f1"></div>
					<div class="card f2"></div>
					<div class="card f3"></div>
					<div class="card t"></div>
					<div class="card r"></div>
					<div class="pot">1000</div>
				</div></div>
				<div class="player" style="top:-10px; left:0px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="top:120px; left:-30px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="bottom:20px; left:0px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="top:-40px; left:345px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="top:-40px; left:170px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="top:-40px; right:170px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="top:-10px; right:0px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="top:120px; right:-30px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="bottom:20px; right:0px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
				<div class="player" style="bottom:0px; left:350px;">
					<div class="card f1"></div>
					<div class="card f2"></div>
					<h5>PlayerName</h5>
					<div class="pot">Bet: 30</div>
					<div class="stack">500</div>
				</div>
			</div>
			
				<div id="plog">
					<textarea readonly>Log</textarea>
					<button id="p_clear_log">Löschen</button><button id="p_save_log">Speichern</button>
				</div>
				<div id="pcontrols">
					<button id="p_fold" class="big">Fold</button>
					<button id="p_check_call" class="big">Check</button>
					<div class="pbet">
						<button id="p_bet_raise" class="big">Bet</button>
						<input type="number" id="p_bet_value" min="10" max="500" value="10" />
					</div>
					<div id="radio">
						<button id="p_bet_min" class="small">Min</button>
						<button id="p_bet_step1" class="small">X1</button>
						<button id="p_bet_step2" class="small">X2</button>
						<button id="p_bet_step3" class="small">X3</button>
						<button id="p_bet_max" class="small">Max</button>
				    </div>
				</div>
		</div>
	</div>
</div>
<ul id="messageMenu" style="display:none">
    <?php if ($this->_tpl_vars['permissions']['mark'] === true): ?><li class="read"><a href="form/mail/mark/">Erledigt</a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['new'] === true): ?><li class="m_reply"><a href="admin/mail/send/reply/?width=700&height=520" class="send" title="Nachricht beantworten">Beantworten</a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['assign'] === true): ?><li class="assign"><a href="admin/mail/assign/?width=450&height=200" class="form" title="Nachricht einer Belegung zuordnen">Einordnen</a></li><?php endif; ?>
</ul>
<ul id="hoverMenu">
    <?php if ($this->_tpl_vars['permissions']['mark'] === true): ?><li class="read"><a href="form/mail/mark/" title="Erledigt"></a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['new'] === true): ?><li class="m_reply"><a href="admin/mail/send/reply/?width=700&height=520" class="send" title="Nachricht beantworten"></a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['assign'] === true): ?><li class="assign"><a href="admin/mail/assign/?width=450&height=200" class="form" title="Nachricht einer Belegung zuordnen"></a></li><?php endif; ?>
</ul>
<?php echo '
<script>
$(document).ready(function() {		
	$(\'#sidebar\').find(\'li a\').live(\'click\', function() {
		$(\'#sidebar\').find(\'li.active\').removeClass(\'active\');
		$(this).parents(\'li\').addClass(\'active\');
		return false;
	});

	$(\'#radio\').buttonset();
});
</script>
'; ?>