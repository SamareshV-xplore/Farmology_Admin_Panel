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
    <section class="content-header"><h1>Edit Merchant</h1></section>

    <!-- Content Body -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">Merchant Details</h3></div>
                        <form id="merchant_form" method="post" role="form" action="<?=base_url('merchants/edit_submit')?>" enctype="multipart/form-data">
                            <input type="hidden" id="user_id" name="user_id" value="<?=$user_details['id']?>">
                            <div class="box-body">

                                <!-- ========================================== -->
                                <!-- Merchant Personal Information Inputs Start -->
                                <!-- ========================================== -->
                                <div class="form-group col-md-6">
                                    <label for="first_name">First name<span class="required_cls">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name"  value="<?=$user_details['first_name']?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="last_name">Last Name<span class="required_cls">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name"  value="<?=$user_details['last_name']?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Email"  value="<?=$user_details['email']?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="phone">Contact Number<span class="required_cls">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone"  value="<?=$user_details['phone']?>">
                                </div>
                                <!-- ======================================== -->
                                <!-- Merchant Personal Information Inputs End -->
                                <!-- ======================================== -->


                                <!-- ===================================== -->
                                <!-- Merchant Banking Details Inputs Start -->
                                <!-- ===================================== -->
                                <div class="form-group col-md-6">
                                    <label for="bank_name">Bank Name</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?=(isset($user_details['bank_name'])) ? $user_details['bank_name'] : ''?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="acct_holder_name">Account Holder Name</label>
                                    <input type="text" class="form-control" id="acct_holder_name" name="holder_name" value="<?=(isset($user_details['holder_name'])) ? $user_details['holder_name'] : ''?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="acct_number">Account Number</label>
                                    <input type="text" class="form-control" id="acct_number" name="bank_account_no" value="<?=(isset($user_details['bank_account_no'])) ? $user_details['bank_account_no'] : ''?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="ifsc_code">IFSC Code</label>
                                    <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?=(isset($user_details['ifsc_code'])) ? $user_details['ifsc_code'] : ''?>">
                                </div>
                                <!-- =================================== -->
                                <!-- Merchant Banking Details Inputs End -->
                                <!-- =================================== -->


                                <!-- ===================================== -->
                                <!-- Merchant Address Details Inputs Start -->
                                <!-- ===================================== -->
                                <div class="form-group col-md-6">
                                    <label for="address_1">Address 1</label>
                                    <input type="text" class="form-control" id="address_1" name="address_1" value="<?=(isset($user_details['address']->address_1)) ? $user_details['address']->address_1 : '';?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="address_2">Address 2</label>
                                    <input type="text" class="form-control" id="address_2" name="address_2" value="<?=(isset($user_details['address']->address_2)) ? $user_details['address']->address_2 : '';?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="state">State</label>
                                    <select id="state" name="state" class="form-control">
                                        <option value="">Select Your State</option> 
                                        <?php foreach ($states as $state) {
                                            if (isset($user_details["address"]->state_id) && $state->value == $user_details["address"]->state_id)
                                            {
                                                echo "<option value='".$state->value."' selected>".$state->name."</option>";
                                            }
                                            else
                                            {
                                                echo "<option value='".$state->value."'>".$state->name."</option>";
                                            }
                                        }?>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="city">City</label>
                                    <select id="city" name="city" class="form-control">
                                        <option value="">Select Your City</option> 
                                        <?php foreach ($cities as $city){
                                            if (isset($user_details["address"]->city_id) && $city->value == $user_details["address"]->city_id)
                                            {
                                                echo "<option value='".$city->value."' selected>".$city->name."</option>";
                                            }
                                            else
                                            {
                                                echo "<option value='".$city->value."'>".$city->name."</option>";
                                            }
                                        }?>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="zipcode">Zipcode</label>
                                    <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?=(isset($user_details['address']->zipcode)) ? $user_details['address']->zipcode : '';?>" minLength="4" maxLength="6">
                                </div>
                                <!-- =================================== -->
                                <!-- Merchant Address Details Inputs End -->
                                <!-- =================================== -->


                                <!-- =================================== -->
                                <!-- Other Merchant Details Inputs Start -->
                                <!-- =================================== -->
                                <div class="form-group col-md-6">
                                    <label for="employee_id">Registration Date</label>
                                    <input type="text" class="form-control" disabled id="link" name="link" placeholder="Link" maxlength="50" value="<?=$user_details['created_date']?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="blood_group">Status<span class="required_cls">*</span></label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="Y" <?=($user_details['status'] == 'Y') ? "selected" : ""?>>Active</option>
                                        <option value="N" <?=($user_details['status'] == 'N') ? "selected" : ""?>>Inactive</option>
                                    </select>
                                </div>
                                <!-- ================================= -->
                                <!-- Other Merchant Details Inputs End -->
                                <!-- ================================= -->
                            </div>
                            <div class="box-footer">
                                <button type="button" class="btn btn-primary pull-right" onclick="edit_merchant_submit()">Update</button>
                                <a href="<?=base_url('merchants')?>">
                                    <button type="button" class="btn btn-default pull-left">Cancel</button>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include APPPATH."views/modal_images.php";?>

<script type="text/javascript">

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

    // date check start
    function date_check(date_is)
    {
      return moment(date_is, 'DD/MM/YYYY',true).isValid();
    }

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

    function edit_merchant_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";
      var first_name = document.getElementById("first_name").value.trim();
      var last_name = document.getElementById("last_name").value.trim();
      var email = document.getElementById("email").value.trim();
      var phone = document.getElementById("phone").value.trim();
      var status = document.getElementById("status").value;

      if (email != "")
      {  
        var email_check = validateEmail(email);
      }
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
        $("#merchant_form").submit();
      }

      return false;
    }
</script>