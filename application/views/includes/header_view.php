<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
if(!isset($title))
{
   $title = "Welcome to admin panel";
}  
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?></title>


  <!-- loader part start here -->
<div id="loading">
    <center>
      <img id="loading-image" src="<?php echo ASSETS_URL."dist/img/loader.gif" ?>" alt="Loading..." />
    </center>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <style type="text/css">
    #loading {
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    display: block;
    opacity: 0.7;
    background-color: #fff;
    z-index: 99;
    text-align: center;
  }

  #loading-image {
    top: 50%;
    left: 50%;
    z-index: 100;
  }
  </style>

  <script type="text/javascript">
    $(document).ready(function() 
    {
  $('#loading').hide();
    });
  </script>
  <!-- loader part end here -->



   <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- Toastify CSS and JS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script type="text/javascript" src="<?=base_url("assets/js/toast-message.js")?>"></script>


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style type="text/css">
    .error_cls{
      border-color: #fd0000 !important;
    }
  </style>
</head>
<?php 
$toggle_status = $this->common_model->get_toggle_status();
?>
<body class="hold-transition skin-yellow <?php if($toggle_status == 'close') { ?> sidebar-collapse sidebar-mini <?php } else { ?> sidebar-mini <?php } ?>">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo base_url(''); ?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>A</b>CP</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Admin</b>Panel</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" onclick="return toggle_action();">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $admin_details['profile_image']; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo ucwords(strtolower($admin_details['name'])); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo $admin_details['profile_image']; ?>" class="img-circle" alt="User Image">

                <p>
                  <?php echo ucwords(strtolower($admin_details['name'])); ?> - Admin
                  <small><?php echo strtolower($admin_details['email']);  ?></small>
                </p>
              </li>
              
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo base_url('profile'); ?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo base_url('logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          
        </ul>
      </div>
    </nav>
  </header>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php
if($success_message = $this->session->flashdata('success_message'))
{
  ?>
  <script type="text/javascript">
    swal({
            title: "Success!",
            text: "<?php echo $success_message; ?>",
            icon: "success",
            button: "Ok",
          });
  </script>

  <?php
}
?>
<?php
if($error_message = $this->session->flashdata('error_message'))
{
  ?>
  <script type="text/javascript">
    swal({
            title: "Failed!",
            text: "<?php echo $error_message; ?>",
            icon: "error",
            button: "Ok",
          });
  </script>

  <?php
}
?>

<script type="text/javascript">
  function toggle_action()
  {
      var dataString = 'toggle=1';
      $.ajax({
      type: "POST",
      url: "<?=base_url('dashboard/toggle_action_update')?>",
      data: dataString,
      cache: false,
      success: function(html) {
       
      }
      });
  }
</script>
  