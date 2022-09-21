<!doctype html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo base_url(); ?>themes/default/assets/css/bootstrap.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>themes/default/assets/css/custom.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>themes/default/assets/css/signin.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <title>Simpli-city - Log In</title>
</head>

<body class="text-center">
<?php echo form_open("auth/login", 'class="form-signin"'); ?>
<img class = "logo" src="<?php echo base_url(); ?>themes/default/assets/images/SimpliCitySvg.svg" alt="">
<h1 class="h3 mb-3 font-weight-normal"></h1>
<?php if ($message != null) {
    ?>
  <div class="alert alert-info alert-dismissible fade show text-left" role="alert">
      <?php echo $message; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php } ?>
<label for="inputEmail" class="sr-only">Email address</label>
<?php echo form_input($identity); ?>
<label for="inputPassword" class="sr-only">Password</label>
<?php echo form_input($password); ?>
<div class="custom-control custom-checkbox mt-3 mb-3">
    <?php echo form_checkbox('remember', '1', true, 'id="remember" class="custom-control-input"'); ?>
  <label class="custom-control-label active" for="remember">Remember Me</label>
</div>

<?php echo form_submit('submit', lang('login_submit_btn'), 'class="btn btn-lg btn-primary btn-block submit"'); ?>
</p>
<p class="mt-5 mb-3 text-muted">
  © 2022 All Rights Reserved.
</p>
<?php echo form_close(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
</body>

</html>
