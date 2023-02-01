<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $this->siteTitle(); ?></title>

    <!-- Bootstrap core CSS-->
    <link href="<?=$this->layout_url;?>vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="<?=$this->layout_url;?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="<?=$this->layout_url;?>css/sb-admin.css" rel="stylesheet">
    <?php echo $this->content('head'); ?>

  </head>

  <body class="bg-dark">

    <div class="container">
      <?php echo $this->content('body'); ?>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?=$this->layout_url;?>vendor/jquery/jquery.min.js"></script>
    <script src="<?=$this->layout_url;?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="<?=$this->layout_url;?>vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="<?=$this->layout_url;?>js/sb-admin.min.js"></script>
    <?php echo $this->content('footer'); ?>
  </body>

</html>
