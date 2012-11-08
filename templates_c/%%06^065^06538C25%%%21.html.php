<?php /* Smarty version 2.6.22, created on 2010-08-03 16:57:44
         compiled from backend/%21.html */ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <base href="http://localhost/~elias/chispa/" />
        <link rel="shortcut icon" href="favicon.ico" >
        <link rel="stylesheet" href="style/screen.css" type="text/css" media="screen" />
		<script>
			var path = '<?php echo $this->_tpl_vars['path']; ?>
';
		</script>
        <script type="text/javascript" src="script/jquery.js"></script>
        <script type="text/javascript" src="script/backend.js"></script>
        <title><?php echo $this->_tpl_vars['title']; ?>
</title>
    </head>
    <body>
		<div id="top">
			<div id="head">
			    <h1>Balduinstein Belegungskalender</h1>
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
			    <div id="user"><a href="admin/user/list"><?php echo $this->_tpl_vars['user']->realname; ?>
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
><a href="<?php echo $this->_tpl_vars['item']['link']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</a></li>
			    <?php endforeach; endif; unset($_from); ?>
			    </ul>
			<?php endif; ?>
			</div>
		</div>
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
    </body>
</html>