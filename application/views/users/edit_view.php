<style type="text/css">
  .required_cls
  {
    color: red;
  }
  .kyc-images{
    height: 50px;
    margin: 5px;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Edit User</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">User Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form id="user-form" method="post" role="form" action="<?php echo base_url('users/edit_submit') ?>" enctype="multipart/form-data">
              <input type="hidden" id="user_id" name="user_id" value="<?=$user_details['id']?>">
              <div class="box-body">
               
                <div class="form-group col-md-6">
                  <label for="first_name">First name<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name"  value="<?php echo $user_details['first_name']; ?>">
                </div>
                  <div class="form-group col-md-6">
                      <label for="last_name">Last Name<span class="required_cls">*</span></label>
                      <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name"  value="<?php echo $user_details['last_name']; ?>">
                  </div>
                  <div class="form-group col-md-6">
                      <label for="email">Email<!-- <span class="required_cls">*</span> --></label>
                      <input type="text" class="form-control" id="email" name="email" placeholder="Email"  value="<?php echo $user_details['email']; ?>">
                      <span style="color: red;" id="email_err"></span>
                  </div>
                  <div class="form-group col-md-6">
                      <label for="phone">Contact Number<span class="required_cls">*</span></label>
                      <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone"  value="<?php echo $user_details['phone']; ?>">
                  </div>

                  <!-- User Address Inputs Start -->
                  <div class="form-group col-md-6">
                    <label for="address_1">Address 1</label>
                    <input type="text" class="form-control" id="address_1" name="address_1" value="<?=(isset($user_details['address']->address_1)) ? $user_details['address']->address_1 : '';?>">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="address_2">Address 2</label>
                    <input type="text" class="form-control" id="address_2" name="address_2" value="<?=(isset($user_details['address']->address_2)) ? $user_details['address']->address_2 : '';?>">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="landmark">Landmark</label>
                    <input type="text" class="form-control" id="landmark" name="landmark" value="<?=(isset($user_details['address']->landmark)) ? $user_details['address']->landmark : '';?>">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="state">State</label>
                    <select id="state" name="state" class="form-control">
                    <option value="">Select Your State</option> 
                    <?php foreach ($states as $state)
                          {
                            if (isset($user_details["address"]->state_id) && $state->value == $user_details["address"]->state_id)
                            {
                              echo "<option value='".$state->value."' selected>".$state->name."</option>";
                            }
                            else
                            {
                              echo "<option value='".$state->value."'>".$state->name."</option>";
                            }
                          }
                    ?>
                    </select>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="city">City</label>
                    <select id="city" name="city" class="form-control">
                    <option value="">Select Your City</option> 
                    <?php foreach ($cities as $city)
                          {
                            if (isset($user_details["address"]->city_id) && $city->value == $user_details["address"]->city_id)
                            {
                              echo "<option value='".$city->value."' selected>".$city->name."</option>";
                            }
                            else
                            {
                              echo "<option value='".$city->value."'>".$city->name."</option>";
                            }
                          }
                    ?>
                    </select>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="zipcode">Zipcode</label>
                    <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?=(isset($user_details['address']->zipcode)) ? $user_details['address']->zipcode : '';?>" minLength="4" maxLength="6">
                  </div>
                  <!-- User Address Input End -->

                  <!-- User Land Area Input Start -->
                  <div class="form-group col-md-6">
                    <label for="land_area_value">Land Area Value</label>
                    <input type="number" class="form-control" id="land_area_value" name="land_area_value" value="<?=(isset($user_details['area_value'])) ? $user_details['area_value'] : '';?>">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="land_area_unit">Land Area Unit</label>
                    <select id="land_area_unit" name="land_area_unit" class="form-control">
                    <option value="">Select Area Unit</option> 
                    <?php
                      if (isset($user_details["area_unit"]) && $user_details["area_unit"]=="Bigha")
                      {
                        echo "<option selected>Bigha</option>";
                      }
                      else
                      {
                        echo "<option>Bigha</option>";
                      }

                      if (isset($user_details["area_unit"]) && $user_details["area_unit"]=="Acre")
                      {
                        echo "<option selected>Acre</option>";
                      }
                      else
                      {
                        echo "<option>Acre</option>";
                      }
                    ?>
                    </select>
                  </div>
                  <!-- User Land Area Input End -->

                  <!-- User Language Input Start -->
                  <div class="form-group col-md-6">
                    <label for="language">Language</label>
                    <select id='language' name='language' class="form-control">
                    <option value="">Select Your Language</option> 
                    <?php foreach ($languages as $language)
                          {
                            if (isset($user_details["language"]) && $language->value == $user_details["language"])
                            {
                              echo "<option value='".$language->value."' selected>".$language->name."</option>";
                            }
                            else
                            {
                              echo "<option value='".$language->value."'>".$language->name."</option>";
                            }
                          }
                    ?>
                    </select>
                  </div>
                  <!-- User Language Input End -->

                  <!-- User Crop Selection Input Start -->
                  <div class="form-group col-md-6">
                    <label for="selected_crops">Selected Crops</label>
                    <select id="selected_crops" name="selected_crops[]" class="form-control" multiple> 
                    <?php foreach ($crops as $crop)
                        {
                          if (isset($user_details["selected_crop"]) && in_array($crop->value, $user_details["selected_crop"]))
                          {
                            echo "<option value='".$crop->value."' selected>".$crop->name."</option>";
                          }
                          else
                          {
                            echo "<option value='".$crop->value."'>".$crop->name."</option>";
                          }
                        }
                    ?>
                    </select>
                  </div>
                  <!-- User Crop Selection Input End -->

                <div class="form-group col-md-6">
                  <label for="first_name">Update Image</label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                </div>
                <div class="form-group col-md-6">
                    <?php
                        if(empty($user_details['profile_image'])){
                            $imgURL = ASSETS_URL.'dist/img/default-user.png';
                        }else{
                            $imgURL = FRONT_URL.$user_details['profile_image'];
                        }
                    ?>
                  <label for="first_name">Image</label><br>
                  <img src="<?php echo $imgURL; ?>" width="200px">
                </div>
                <div class="form-group col-md-6">
                  <label for="employee_id">Registration Date</label>
                  <input type="text" class="form-control" disabled id="link" name="link" placeholder="Link" maxlength="50" value="<?php echo $user_details['created_date']; ?>">
                </div>

                <div class="form-group col-md-6">
                  <label for="blood_group">Status<span class="required_cls">*</span></label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y"<?php if($user_details['status'] == 'Y'){ echo "selected"; } ?>>Active</option>
                    <option value="N"<?php if($user_details['status'] == 'N'){ echo "selected"; } ?>>Inactive</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label for="employee_id">Referred By</label>
                  <input type="text" class="form-control" id="referred_by" name="referred_by" placeholder="Referred By" maxlength="50" value="<?php echo $user_details['referred_by']; ?>">
                </div>

                <div class="form-group col-md-12">
                  <div class="container">
                    <?php 
                      if (count($user_details['kycDocs']) > 0) {
                        if (count($user_details['kycDocs']['voter_card']) > 0) {
                        ?>
                          <label>Voter Cards : </label>
                          <?php 
                            foreach ($user_details['kycDocs']['voter_card'] as $vc) {
                              ?>
                              <img src="<?= $vc['image'] ?>" alt="" class="kyc-images img-open"><br>
                              <?php 
                            }
                          ?>
                        <?php  
                        }

                        if (count($user_details['kycDocs']['aadhar_card']) > 0) {
                        ?>
                          <label>Aadhar Cards : </label>
                          <?php 
                            foreach ($user_details['kycDocs']['aadhar_card'] as $ac) {
                              ?>
                              <img src="<?= $ac['image'] ?>" alt="" class="kyc-images img-open"><br>
                              <?php 
                            }
                          ?>
                        <?php  
                        }

                        if (count($user_details['kycDocs']['land_document']) > 0) {
                        ?>
                          <label>Land Documents : </label>
                          <?php 
                            foreach ($user_details['kycDocs']['land_document'] as $ld) {
                              ?>
                              <img src="<?= $ld['image'] ?>" alt="" class="kyc-images img-open"><br>
                              <?php 
                            }
                          ?>
                        <?php  
                        }
                      }
                    ?>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return edit_user_submit();">Update User</button>

                <a href="<?php echo base_url('users-list'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>

  <?php 
    include APPPATH."views/modal_images.php";
  ?>
  
  <script type="text/javascript">
    // date check start
    function date_check(date_is)
    {
      return moment(date_is, 'DD/MM/YYYY',true).isValid();
    }
    // date check end

    // email check function start
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    //function checkExistingEmail(email) {
    //    var user_id = document.getElementById("user_id").value.trim();
    //    var dataString = 'email='+ email + '&user_id='+ user_id;
    //
    //    // AJAX Code To Submit Form.
    //    $.ajax({
    //        type: "POST",
    //        url: "<?php //echo base_url('users/check_email'); ?>//",
    //        data: dataString,
    //        cache: false,
    //        success: function(result){
    //            return result;
    //        }
    //
    //    });
    //}
    // email check function end

    function edit_user_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var first_name = document.getElementById("first_name").value.trim();
      var last_name = document.getElementById("last_name").value.trim();
      var email = document.getElementById("email").value.trim();
      if (email != "")
      {  
        var email_check = validateEmail(email);
      }
      var phone = document.getElementById("phone").value.trim();
      var status = document.getElementById("status").value;
      var referred_by = document.getElementById("referred_by").value;
      

      if(first_name == '')
      {
        $('#first_name').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#first_name').focus();
            focusStatus = 'Y';
        }     
      }
        if(last_name == '')
        {
            $('#last_name').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#last_name').focus();
                focusStatus = 'Y';
            }
        }

        // if(email == '' || email_check == false)
        // {
        //     $('#email').addClass('error_cls');
        //     if(focusStatus == 'N')
        //     {
        //         $('#email').focus();
        //         focusStatus = 'Y';
        //     }
        // }

        if(email != "" && email_check == false)
        {
            $('#email').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#email').focus();
                focusStatus = 'Y';
            }
        }

        if(phone == '')
        {
            $('#phone').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#phone').focus();
                focusStatus = 'Y';
            }
        }

      if(status == '')
      {
        $('#status').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#status').focus();
            focusStatus = 'Y';
        }     
      }

      if(focusStatus == "N")
      {
        // no validation error.. now submit the form
        $("#user-form").submit();
      }

      return false;
    }
  </script>

<script>
    
    $(document).ready(function(){

      // $("#user_edit_form").submit(function(e){
      //   e.preventDefault();
      //   var url = "<?=base_url('users/edit_submit')?>";
      //   var form = document.getElementById("user_edit_form");
      //   var postData = new FormData(form);
      //   $.ajax({
      //     url: url,
      //     type: "POST",
      //     data: postData,
      //     contentType: false,
      //     processData: false,
      //     error: function (a, b, c)
      //     {
      //       console.log(a);
      //       console.log(b);
      //       console.log(c);
      //     },
      //     success: function (res)
      //     {
      //       console.log(res);
      //     }
      //   })
      // });

    });

</script>

