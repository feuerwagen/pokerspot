<?php /* Smarty version 2.6.22, created on 2012-11-08 05:09:41
         compiled from %21.html */ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <base href="http://localhost/~elias/poker/" />
        <link rel="shortcut icon" href="favicon.ico" >
		<link rel="stylesheet" href="style/reset.css" media="screen" />
		<link rel="stylesheet" href="style/jquery-ui.css" media="screen" />
		<link rel="stylesheet" href="style/jquery.contextmenu.css" media="screen" />
		<link rel="stylesheet" href="style/jquery.tiptip.css" media="screen" />
		<link rel="stylesheet" href="style/screen.css" media="screen" />
		<link rel="stylesheet" href="style/forms.css" media="screen" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
        <script src="script/jquery-ui.min.js"></script>
		<script src="script/jquery.form.js"></script>
		<script src="script/jquery.tablesorter.js"></script>
		<script src="script/jquery.tablehover.js"></script>
		<script src="script/jquery.urltoolbox.js"></script>
		<script src="script/jquery.contextmenu.js"></script>
		<script src="script/jquery.hoverintent.js"></script>
		<script src="script/jquery.tiptip.js"></script>
		<script src="script/effect.fade.js"></script>
		<script src="script/backend.js"></script>
		<script>var path='<?php echo $this->_tpl_vars['path']; ?>
'</script>
        <title><?php echo $this->_tpl_vars['title']; ?>
</title>
    </head>
    <body>
		<div id="top">
			<div id="head">
			    <h1>PokerSpot</h1>
			<?php if (is_array ( $this->_tpl_vars['nav'] )): ?>
			    <ul id="nav">
			    <?php $_from = $this->_tpl_vars['nav']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>    
			        <li id="nav_<?php echo $this->_tpl_vars['item']['name']; ?>
"<?php echo $this->_tpl_vars['item']['attr']; ?>
><a href="<?php echo $this->_tpl_vars['item']['link']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</a></li>
			    <?php endforeach; endif; unset($_from); ?>
			    </ul>
			<?php endif; ?>
			<?php if (is_object ( $this->_tpl_vars['user'] )): ?>
			    <div id="user"><a href="admin/user/self/<?php echo $this->_tpl_vars['user']->username; ?>
?width=500&height=500" class="dialog form" title="Benutzer bearbeiten"><?php echo $this->_tpl_vars['user']->realname; ?>
</a> <a href="admin/user/logout" id="logout"></a></div>
			<?php endif; ?>
			</div>
			<div id="bar">
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
			</div>
		</div>
		<?php if (is_array ( $this->_tpl_vars['buttons'] )): ?>
		    <ul id="buttons">
		    <?php $_from = $this->_tpl_vars['buttons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>    
		        <li<?php echo $this->_tpl_vars['item']['attr']; ?>
><a href="<?php echo $this->_tpl_vars['item']['link']; ?>
" class="dialog <?php echo $this->_tpl_vars['item']['dialog']; ?>
" title="<?php echo $this->_tpl_vars['item']['title']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</a></li>
		    <?php endforeach; endif; unset($_from); ?>
		    </ul>
		<?php endif; ?>
        <div id="content">
		<?php if (is_array ( $this->_tpl_vars['messages'] )): ?>
			<?php $_from = $this->_tpl_vars['messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['type'] => $this->_tpl_vars['message']):
?>
			<div class="<?php echo $this->_tpl_vars['type']; ?>
">
				<?php $_from = $this->_tpl_vars['message']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
				<p><?php echo $this->_tpl_vars['m']; ?>
</p>
				<?php endforeach; endif; unset($_from); ?>
			</div>
			<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
            <?php echo $this->_tpl_vars['content']; ?>

        </div>
		<div id="dialog"></div>
    </body>
</html>