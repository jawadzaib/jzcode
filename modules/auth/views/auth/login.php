<?php $this->setSiteTitle('Login'); ?>
<?php $this->start('head'); ?>
<?php $this->end(); ?>
<?php $this->start('body');
?>
<div class="auth-form">
<h1><?=SITE_TITLE;?> Login</h1>
<form method="POST" action="<?=$this->module->action('Auth', 'login');?>">
<?=$this->displayErrors;?>
<div class="form-group">
  <div class="form-label-group">
    <label for="inputUsername">Username/Email</label>
    <input type="text" name="username" id="inputUsername" class="form-control" placeholder="Username" required="required" autocomplete="off" autofocus="autofocus">
  </div>
</div>
<div class="form-group">
  <div class="form-label-group">
    <label for="inputPassword">Password</label>
    <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="required">
  </div>
</div>
<div class="form-group">
  <div class="checkbox">
    <label>
      <input type="checkbox" name="remember_me" value="remember-me">
      Remember Password
    </label>
  </div>
</div>
<button class="btn btn-primary btn-block" type="submit">Login</button>
</form>
<div class="text-center">
<a class="" href="<?=$this->module->action('Auth', 'register');?>">Register an Account</a>
<a class="" href="forgot-password.html">Forgot Password?</a>
</div>
</div>
<?php $this->end(); ?>
<?php $this->start('footer'); ?>
<?php $this->end(); ?>