<?php $this->start('head'); ?>
<style type="text/css">
	.content {
		margin: 0px auto;
		width: 800px;
		margin-top: 50px;
		background: #eee;
		padding: 10px;		
	}
	.content h1 {
		text-align: center;
	}
</style>
<?php $this->end(); ?>
<?php $this->start('body'); ?>
<div class="content">
	<h1>Welcome to JZ-Code</h1>
	<h3>Hello From App\Controllers\WelcomeController</h3>
	<p>You are visiting Index View</p>
</div>
<?php $this->end(); ?>