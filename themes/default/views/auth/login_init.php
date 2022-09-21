<!-- Initial version of Login page -->

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>CitySmart</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="theme-color" content="#ffffff">
  <meta name="MobileOptimized" content="320">
  <meta name="HandheldFriendly" content="true">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, target-densitydpi=device-dpi">
  <meta name="author" content="http://psdhtml.me">
  <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>/themes/default/assets/styles/screen.css">
  <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>/themes/default/assets/styles/style.css">
  <link rel="stylesheet" media="print" href="<?php echo base_url(); ?>/themes/default/assets/styles/print.css">
  <meta property="og:title" content="">
  <meta property="og:type" content="website">
  <meta property="og:description" content=".">
  <meta property="og:site_name" content="">
  <meta property="og:url" content="">
  <meta property="og:image" content="">
  <script type="application/ld+json">
            {
                "@context": "http://schema.org/",
                "@type": "Organization",
                "url": "",
                "name": "CitySmart",
                "legalName": "CitySmart",
                "description": "",
                "logo": "",
                "image": "",
                "author": "psdHTML.me",
                "contactPoint": {
                    "@type": "ContactPoint",
                    "contactType": "Customer service",
                    "telephone": ""
                },
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "",
                    "addressLocality": "",
                    "addressRegion": "",
                    "postalCode": "",
                    "addressCountry": ""
                }
            }
        
  </script>
</head>

<body class="login">

<a class="hiddenanchor" id="signup"></a>
<a class="hiddenanchor" id="signin"></a>

<div class="form login_form">
  <section class="login_content">
      <?php echo form_open("auth/login"); ?>
    <h1 class="login-heading">Login</h1>
      <?php if ($message != null) { ?>
        <div class="alert">
          <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            <?php echo $message; ?>
        </div>

      <?php } ?>
    <div>
        <?php echo form_input($identity); ?>
    </div>
    <div>
        <?php echo form_input($password); ?>
    </div>

    <div>
      <p>
          <?php //echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
          <?php //echo lang('login_remember_label', 'remember');?>
      </p>
    </div>
    <div>
      <p><?php echo form_submit('submit', lang('login_submit_btn'), 'class="btn btn-default submit"'); ?></p>
      <p><a href="forgot_password" class="reset_pass"><?php echo lang('login_forgot_password'); ?></a></p>
    </div>

    <div class="clearfix"></div>

    <div class="separator">
      <!--<p class="change_link">New to site?
        <a href="#signup" class="to_register"> Create Account </a>
      </p> -->

      <div class="clearfix"></div>
      <br/>

      <div>
        <!--<h1><i class="fa fa-paw"></i> Gentelella Alela!</h1>-->
        <p>© 2022 All Rights Reserved.</p>
      </div>
    </div>
      <?php echo form_close(); ?>
  </section>
</div>

<script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script defer src="javascript/jquery.js"><\/script>');</script>
<script defer src="<?php echo base_url(); ?>/themes/default/assets/javascript/scripts.js"></script>
<script defer src="<?php echo base_url(); ?>  /themes/default/assets/javascript/custom.js"></script>
<script defer src="<?php echo base_url(); ?>/themes/default/assets/javascript/mobile.js"></script>
</body>
</html>
