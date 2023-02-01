<?php $this->setSiteTitle('Register'); ?>
<?php $this->start('head'); ?>
<?php $this->end(); ?>
<?php $this->start('body');
?>
<div class="auth-form">
<h1><?=SITE_TITLE;?> Register</h1>
      <form method="POST" action="<?=$this->module->action('Auth', 'register');?>">
        <?=$this->displayErrors;?>
        <?php
        if(isset($this->properties) && !empty($this->properties)) {
          $index = 0;
          foreach ($this->properties as $key => $value) {
            $type = 'text';
            if(isset($value['type'])) {
              $type = $value['type'];
            } else if(isset($value['valid_email']) && $value['valid_email']) {
              $type = 'email';
            }
            $field_value = isset($this->post[$key]) ? $this->post[$key] : '';
            ?>
            <div class="form-group">
              <div class="form-label-group">
                <label for="input<?=$key;?>"><?=$value['display'];?></label>
                <input type="<?=$type;?>" value="<?=$field_value;?>" name="<?=$key;?>" id="input<?=$key;?>" class="form-control" placeholder="<?=$value['display'];?>" <?=(isset($value['required']) && $value['required']) ? 'required="required"' : '';?> autocomplete="off" <?= ($index == 0) ? 'autofocus="autofocus"' : ''; ?>>
              </div>
            </div>
            <?php
          }
          $index++;
        } 
        ?>
        <button class="btn btn-primary btn-block" type="submit">Register</button>
      </form>
      <div class="text-center">
        <span>Already have an Account <a class="small mt-3" href="<?=$this->module->action('Auth', 'login');?>">Login</a></span>
      </div>
</div>
<?php $this->end(); ?>
<?php $this->start('footer'); ?>
<?php $this->end(); ?>