<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Panel | Forget Password</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>plugins/iCheck/square/blue.css">
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <style type="text/css">
  	.error_cls{
  		border-color: #fd0000 !important;
  	}
    .login-page
    {
      background-color: #4bf58fe0 !important;
    }
  </style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <center>
        <img src="<?=FRONT_URL.'uploads/default/logo.jpg'?>" class="img-responsive">
    </center>
    <a href="#"><b>Admin</b>Panel</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Please enter your email / username to reset your password</p>

    
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Username / Email" name="username" id="username" >
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      
      <div class="row">        
        <!-- /.col -->
        <div class="col-xs-12" id="loader" style="display: none;">
          <center>
            <img style="height: 40px; width: auto;" src="<?php echo ASSETS_URL."dist/img/loader.gif" ?>">
          </center>
        </div>
        <div class="col-xs-12">
          <button type="button" id="submit_btn" class="btn btn-primary btn-block btn-flat" onclick="return forget_password_submit();">Submit</button>
        </div>
        <!-- /.col -->
      </div>
    
    
    <!-- /.social-auth-links -->
    <br>
    <a href="<?php echo base_url(''); ?>">Login</a><br>

  </div>

  <br>

  <div class="login-logo">
    <center><p style="font-size: 1.3rem; color: #3c3a3a; font-weight: 400;">Copyright © <?php echo date('Y') ?>. All rights reserved.</p>
    </center>
</div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- Bootstrap 3.3.7 -->
<script src="<?php echo ASSETS_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo ASSETS_URL; ?>plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>

<script type="text/javascript">
	function forget_password_submit() {

		$('.form-control').removeClass('error_cls');

		var username = document.getElementById("username").value;
		var focusStatus = "N";		

		if(username == '')
		{
			$('#username').addClass('error_cls');
			if(focusStatus == 'N')
			{
			    $('#username').focus();
			    focusStatus = 'Y';
			}			
		}		

		if(focusStatus == 'N')
		{
      document.getElementById("submit_btn").disabled = true;
      $("#loader").show();
			// submit this form
			var dataString = 'username=' + username;

			$.ajax({
			type: "POST",
			url: "<?=base_url('forget_password/forget_password_submit')?>",
			data: dataString,
			cache: false,
			success: function(html) {
        var obj = $.parseJSON(html);
        if(obj.status == 'Y')
        {
          document.getElementById("submit_btn").disabled = false;
          $("#loader").hide();
          document.getElementById("username").value = '';
          swal({
            title: "Success!",
            text: obj.message,
            icon: "success",
            button: "Ok",
          });
        }
        else if(obj.status == 'N')
        {
          document.getElementById("submit_btn").disabled = false;
          $("#loader").hide();
          swal({
            title: "Opps!",
            text: obj.message,
            icon: "error",
            button: "Ok",
          });

        }
			}
			});
		}
		return false;
		}
</script>
</body>
</html>
