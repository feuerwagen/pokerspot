<?php /* Smarty version 2.6.22, created on 2010-08-02 23:44:37
         compiled from backend/login.html */ ?>
<form method="post" action="form/user/login" id="login">
    <h3>Anmeldung</h3>
    Benutzername:<br/>
    <input type="text" name="username" value="<?php echo $this->_tpl_vars['username']; ?>
" /><br/><br/>
    Passwort:<br/>
    <input type="password" name="password" value="<?php echo $this->_tpl_vars['password']; ?>
" /><br/><br/>
    <input type="hidden" name="path" value="<?php echo $this->_tpl_vars['path']; ?>
" />
    <input type="submit" value="Anmelden" />
</form>