<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $this->siteTitle(); ?></title>
	<link rel="stylesheet" type="text/css" href="<?=$this->layout_url?>css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=$this->layout_url?>css/screen.css" />
	<?php echo $this->content('head'); ?>
</head>
<body>
	<?php echo $this->content('body'); ?>
	<script type="text/javascript" src="<?=$this->layout_url?>js/jquery.js"></script>
	<?php echo $this->content('footer'); ?>
</body>
</html>