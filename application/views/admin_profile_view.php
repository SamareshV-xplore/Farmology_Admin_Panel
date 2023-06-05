<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php
if(isset($_REQUEST['error-message']))
{
  ?>
  <script type="text/javascript">
    swal({
            title: "Opps!",
            text: "<?php echo $_REQUEST['error-message']; ?>",
            icon: "error",
            button: "Ok",
          });
  </script>

  <?php
}
?>
<?php
if(isset($_REQUEST['success-message']))
{
  ?>
  <script type="text/javascript">
    swal({
            title: "Success!",
            text: "<?php echo $_REQUEST['success-message']; ?>",
            icon: "success",
            button: "Ok",
          });
  </script>
  <?php
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        My Profile
        <small>Admin Panel</small>
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-6">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Profile Info</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('admin_profile/info_update_submit') ?>" id="info-form" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php echo $admin_details['name'] ?>">
                </div>
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo $admin_details['username'] ?>">
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $admin_details['email'] ?>">
                </div>
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" maxlength="10" value="<?php echo $admin_details['phone'] ?>">
                </div>
                
                <!--<div class="form-group">
                  <label for="profile_picture">Profile Image</label>
                  <input type="file" id="profile_picture" name="profile_picture">

                  <p class="help-block">Update your profile picture</p>
                </div>-->
                
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary" onclick="return info_update_submit();">Update Info</button>
              </div>
            </form>
          </div>

        </div>

        <div class="col-md-6">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Change Password</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" action="<?php echo base_url('admin_profile/password_update_submit') ?>" role="form" id="password-form">
              <div class="box-body">
                <div class="form-group">
                  <label for="password">New Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="New Password">
                </div>
                <div class="form-group">
                  <label for="confirm_password">Confirm New Password</label>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm New Password">
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary" onclick="return password_update_submit();">Update Password</button>
              </div>
            </form>
          </div>
          
        </div>
          <!-- /.box -->
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script type="text/javascript">

    // email check function start
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    // email check function end

    function password_update_submit()
    {
      $('.form-control').removeClass('error_cls');

      var password = document.getElementById("password").value.trim();
      var confirm_password = document.getElementById("confirm_password").value.trim();
      var focusStatus = "N";

      if(password == '' || password.length < 4)
      {
        $('#password').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#password').focus();
            focusStatus = 'Y';
        }     
      }

      if(confirm_password == '' || password != confirm_password)
      {
        $('#confirm_password').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#confirm_password').focus();
            focusStatus = 'Y';
        }     
      }

      if(focusStatus == 'N')
      {
        $("#password-form").submit();
      }

      return false;


    }

    function info_update_submit()
    {
      $('.form-control').removeClass('error_cls');

      var name = document.getElementById("name").value.trim();
      var username = document.getElementById("username").value.trim();
      var email = document.getElementById("email").value.trim();
      var email_check = validateEmail(email);
      var phone = document.getElementById("phone").value.trim();
      var focusStatus = "N";

      if(name == '')
      {
        $('#name').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#name').focus();
            focusStatus = 'Y';
        }     
      }

      if(username == '' || username.length < 4)
      {
        $('#username').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#username').focus();
            focusStatus = 'Y';
        }     
      }

      if(email == '' || email_check == false)
      {
        $('#email').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#email').focus();
            focusStatus = 'Y';
        }     
      }

      if(phone == '' || phone.length != 10 || isNaN(phone))
      {
        $('#phone').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#phone').focus();
            focusStatus = 'Y';
        }     
      }

      if(focusStatus == "N")
      {
        $("#info-form").submit();
      }

      return false;
    }
  </script>