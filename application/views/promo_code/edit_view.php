<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Edit Promo Code</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Promo Code Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('promo_code_management/edit_submit') ?>" id="promo-form">
              <input type="hidden" name="promo_code_id" value="<?=$promo_code_details['id']?>">
              <div class="box-body">

                <div class="form-group col-md-6">
                      <label for="first_name">Promo Code<span class="required_cls">*</span></label>
                      <input type="text" class="form-control" id="promo_code" name="promo_code" placeholder="Promo Code" value="<?php echo $promo_code_details['promo_code']; ?>"  onblur="checkCouponCode()">
                    <input type="hidden" class="form-control" id="duplicate_code" name="duplicate_code" value="n">
                    <input type="hidden" class="form-control" id="current_code" name="current_code" value="<?php echo $promo_code_details['promo_code']; ?>">
                    <span id="promo_code_err" style="color: red;"></span>
                    <span id="promo_code_succ" style="color: green;"></span>
                  </div>
                <div class="form-group col-md-6">
                  <label for="first_name">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="title" name="title" placeholder="Title"  value="<?php echo $promo_code_details['title']; ?>">
                </div>
                  <div class="clearfix"></div>

                  <div class="form-group col-md-6">
                      <label for="last_name" >Description</label>
                      <input type="text" class="form-control" id="promo_description" name="promo_description" placeholder="Description" value="<?php echo $promo_code_details['description']; ?>">
                  </div>
                  <div class="form-group col-md-6">
                      <label for="first_name">Eligible Order Total Price</label>
                      <input type="text" class="form-control" id="eligible_order_price" name="eligible_order_price" placeholder="Eligible Order Price" value="<?php echo $promo_code_details['eligible_order_price']; ?>">
                      <span style="color: red;" id="total_price_err"></span>
                  </div>
                  <div class="clearfix"></div>

                  <div class="form-group col-md-6">
                      <label>Start Date</label>
                      <div class="input-group date">
                          <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start Date" value="<?php echo $promo_code_details['start_date']; ?>">
                      </div>
                      <span style="color: red;" id="start_date_err" class="clearfix"></span>
                  </div>
                  <div class="form-group col-md-6">
                      <label>End Date</label>
                      <div class="input-group date">
                          <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date" value="<?php echo $promo_code_details['end_date']; ?>">
                      </div>
                      <span style="color: red;" id="end_date_err" class="clearfix"></span>
                  </div>
                  <div class="clearfix"></div>

                  <div class="form-group col-md-6">
                      <label for="first_name">Max Number of Usage</label>
                      <input type="text" class="form-control" id="usage_count" name="usage_count" placeholder="Max Number of Usage" value="<?php echo $promo_code_details['usage_count']; ?>">
                      <span style="color: red;" id="usage_count_err"></span>
                  </div>
                  <div class="form-group col-md-6">
                      <label for="blood_group">Status<span class="required_cls">*</span></label>
                      <select class="form-control" name="status" id="status">
                          <option value="Y"<?php if($promo_code_details['status'] == 'Y'){ echo "selected"; } ?>>Active</option>
                          <option value="N"<?php if($promo_code_details['status'] == 'N'){ echo "selected"; } ?>>Inactive</option>
                      </select>
                  </div>
                  <div class="clearfix"></div>

                <div class="form-group col-md-6">
                      <label for="first_name">Couopn Type<span class="required_cls">*</span></label>
                      <select class="form-control" name="discount_type" id="discount_type">
                          <option value="P" <?php if($promo_code_details['discount_type'] == 'P'){ echo "selected"; } ?>>Percentage</option>
                          <option value="FL" <?php if($promo_code_details['discount_type'] == 'FL'){ echo "selected"; } ?>>Flat</option>
                          <option value="FR" <?php if($promo_code_details['discount_type'] == 'FR'){ echo "selected"; } ?>>Free Delivery</option>
                      </select>
                  </div>
                  <div class="form-group col-md-6" id="check_discount">
                      <label for="first_name">Discount Limit<span class="required_cls">*</span></label>
                      <input type="text" class="form-control" id="discount_limit" name="discount_limit" placeholder="Discount Limit" value="<?php echo $promo_code_details['discount_limit']; ?>">
                      <span style="color: red;" id="discount_limit_err" class="clearfix"></span>
                  </div>
                  <div class="form-group col-md-6" id="max_discount_div" <?php if($promo_code_details['discount_type'] != 'P'){ ?> style="display: none" <?php } ?> >
                      <label for="first_name">Max Discount Amount<span class="required_cls">*</span></label>
                      <input type="text" class="form-control" id="max_limit" name="max_limit" placeholder="Max Discount Amount" value="<?php echo $promo_code_details['max_limit']; ?>" >
                  </div>
                  <div class="clearfix"></div>
                  <div class="form-group col-md-6">
                      <label for="blood_group">Assign Coupon to specific User</label>
                      <select class="form-control" name="user_specific" id="user_specific">
                          <option value="N" <?php if($promo_code_details['user_specific'] == 'N'){ echo "selected"; } ?>>No</option>
                          <option value="Y" <?php if($promo_code_details['user_specific'] == 'Y'){ echo "selected"; } ?>>Yes</option>
                      </select>
                  </div>
                  <div class="col-md-6" id="mobile_number" <?php if($promo_code_details['user_specific'] == 'N') { ?> style="display: none;" <?php } ?>>
                      <label for="first_name">User Mobile Number<span class="required_cls">*</span></label>
                      <div class="form-inline form-group">
                          <img src="<?php echo ASSETS_URL ?>dist/img/search-loader.gif" title="Search for mobile" style="display: none;" id="loader_img">
                          <input type="text" class="form-control" id="mobile" name="mobile" placeholder="10 Digit Mobile Number" value="<?php echo $promo_code_details['mobile_number']; ?>">
                          <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo $promo_code_details['user_id']; ?>">
                          <button type="button" class="btn btn-warning" onclick="check_user_mobile()"><i class="fa fa-search" aria-hidden="true"></i> Check Availability</button>
                      </div>
                      <span id="mobile_err" style="color: red"></span>
                      <span id="mobile_succ" style="color: green"></span>
                  </div>
                  <div class="clearfix"></div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return edit_promo_code_submit();">Update Promo Code</button>

                <a href="<?php echo base_url('promo-list'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">

      window.onload = function(e){
          var discount_type = document.getElementById("discount_type").value.trim();
          if(discount_type == 'FR'){
              document.getElementById("discount_limit").value = '';
              document.getElementById("discount_limit_err").innerHTML = '';
              document.getElementById("check_discount").style.display = "none";
          }else{
              document.getElementById("check_discount").style.display = "block";
          }
      }
          // date check start
      function date_check(date_is)
      {
          return moment(date_is, 'YYYY-MM-DD',true).isValid();
      }
      // date check end

      function checkNumber(value){
          return /^[0-9]+$/.test(value);
      }

    function edit_promo_code_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

        

        var promo_code = document.getElementById("promo_code").value.trim();
        var title = document.getElementById("title").value.trim();
        var promo_description = document.getElementById("promo_description").value.trim();

        var eligible_order_price = document.getElementById("eligible_order_price").value.trim();
        var check_eligible_order_price = checkNumber(eligible_order_price);

        var start_date = document.getElementById("start_date").value.trim();
        var check_start_date = date_check(start_date);
        var end_date = document.getElementById("end_date").value.trim();

        var check_end_date = date_check(end_date);
        var discount_limit = document.getElementById("discount_limit").value.trim();
        var check_discount_limit = checkNumber(discount_limit);
        var usage_count = document.getElementById("usage_count").value.trim();
        var check_usage_count = checkNumber(usage_count);
        var discount_type = document.getElementById("discount_type").value.trim();
        var status = document.getElementById("status").value;
        var user_specific = document.getElementById("user_specific").value;
        var user_id = document.getElementById("user_id").value;
        var duplicate_code = document.getElementById("duplicate_code").value;
        var current_code = document.getElementById("current_code").value;
        var max_limit = document.getElementById("max_limit").value;
        var check_max_limit = checkNumber(max_limit);

        if(promo_code == '' )
        {
            $('#promo_code').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#promo_code').focus();
                focusStatus = 'Y';
            }
        }else if(duplicate_code == 'y'){
            $('#promo_code').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $("#promo_code_err").text('Please provide a unique promo code.');
                $('#promo_code').focus();
                focusStatus = 'Y';
            }
        }

        if(title == '')
        {
            $('#title').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#title').focus();
                focusStatus = 'Y';
            }
        }

        if(eligible_order_price != '' && check_eligible_order_price == false )
        {
            $("#total_price_err").text('Please provide valid price. Only numbers accepted.');
            $('#eligible_order_price').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#eligible_order_price').focus();
                focusStatus = 'Y';

            }
        }else {
            $("#total_price_err").text('');
        }

        if(start_date == '' && check_start_date == false)
        {
            $("#start_date_err").text('Please provide valid date followed by eg: YYYY/MM/DD.');
            $('#start_date').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#start_date').focus();
                focusStatus = 'Y';
            }
        }

        if(end_date == '' && check_end_date == false)
        {
            $("#end_date_err").text('Please provide valid date followed by eg: YYYY/MM/DD.');
            $('#end_date').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#end_date').focus();
                focusStatus = 'Y';
            }
        }

        if(discount_limit == '' || check_discount_limit == false)
        {
          if(discount_type != 'FR')
          {
            $("#discount_limit_err").text('Please provide valid price. Only numbers accepted.');
            $('#discount_limit').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#discount_limit').focus();
                focusStatus = 'Y';
            }
          }

          
            
        }

        if(discount_type == 'P'){
            if(max_limit == '' || check_max_limit == false)
            {
                $('#max_limit').addClass('error_cls');
                if(focusStatus == 'N')
                {
                    $('#max_limit').focus();
                    focusStatus = 'Y';
                }
            }
        }

        if(usage_count != '' && check_usage_count == false)
        {
            $("#usage_count_err").text('Please provide valid order count. Only numbers accepted.');
            $('#usage_count').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#usage_count').focus();
                focusStatus = 'Y';
            }
        }

        if(discount_type == '')
        {
            $('#discount_type').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#discount_type').focus();
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

        if(user_specific == 'Y' && user_id == ''){
            $("#mobile_err").text('Please provide a valid 10 digit mobile number and validate it.');
            $('#mobile').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#mobile').focus();
                focusStatus = 'Y';
            }
        }

      if(focusStatus == "N")
      {
        // no validation error.. now submit the form
        $("#promo-form").submit();
      }

      return false;
    }

      $("#discount_type").on('change', function () {
        var discount_type = document.getElementById("discount_type").value.trim();
        if(discount_type == 'FR'){
            $("#discount_limit").val('');
            $("#discount_limit_err").text('');
            $('#check_discount').hide();
            $('#max_discount_div').hide();
        }
        else if(discount_type == 'P')
        {
          $('#max_discount_div').show();
          $('#check_discount').show();
        }
        else{
            $('#check_discount').show();
            $('#max_discount_div').hide();
        }

    });

      $("#user_specific").on('change', function () {
          var user_specific = document.getElementById("user_specific").value.trim();
          if(user_specific == 'N'){
              $("#mobile_number").hide();
              $("#mobile").val('');
              $("#mobile_err").text('');
          }else {
              $("#mobile").val('');
              $("#mobile_succ").text('');
              $("#mobile_err").text('');
          }
      });

      //Ajax Call
      function check_user_mobile()
      {
          var mobile = document.getElementById("mobile").value.trim();
          if(mobile != '' && (mobile.length == 10) && (/^\d+$/.test(mobile)))
          {
              $("#mobile_succ").text('');
              $("#mobile_err").text('');
              $('#mobile').removeClass('error_cls');
              $("#loader_img").show();
              var dataString = 'mobile=' + encodeURI(mobile);
              $.ajax({
                  type: "POST",
                  url: "<?=base_url('users/check_mobile')?>",
                  data: dataString,
                  cache: false,
                  success: function(data) {
                      var obj = $.parseJSON(data);
                      if(obj.status)
                      {
                          // avilable
                          $("#loader_img").hide();
                          $("#mobile_err").text('');
                          document.getElementById("user_id").value = obj.data.id;
                          $('#mobile').removeClass('error_cls');
                          $("#mobile_succ").text("User available");
                      }
                      else
                      {
                          // not avilable
                          $("#loader_img").hide();
                          $("#mobile_succ").text('');
                          document.getElementById("user_id").value = '';
                          $('#mobile').addClass('error_cls');
                          $("#mobile_err").text(obj.data);
                      }

                  },
                  fail: function (data) {
                      $("#mobile_succ").text('');
                      $("#loader_img").hide();
                      $("#mobile_err").text('Something went wrong, please try again later.');
                      $('#mobile').addClass('error_cls');
                  }
              });
          }else{
              $("#mobile_succ").text('');
              $("#loader_img").hide();
              $("#mobile_err").text('Please enter a valid 10 digit mobile number.');
              $('#mobile').addClass('error_cls');
              $('#mobile').focus();
          }
      }

      function checkCouponCode() {
          var promo_code = document.getElementById("promo_code").value.trim();
          var current_code = document.getElementById("current_code").value;

          if((promo_code != '') && (promo_code !== current_code))
          {
              var dataString = 'promo_code=' + encodeURI(promo_code);
              $.ajax({
                  type: "POST",
                  url: "<?=base_url('promo_code_management/check_code')?>",
                  data: dataString,
                  cache: false,
                  success: function(data) {
                      var obj = $.parseJSON(data);
                      if(obj.status)
                      {
                          // avilable
                          $("#promo_code_err").text('');
                          $('#promo_code').removeClass('error_cls');
                          $("#promo_code_succ").text(obj.message);
                          $("#duplicate_code").val('n');
                      }
                      else
                      {
                          // not avilable
                          $("#promo_code_succ").text('');
                          $('#promo_code').addClass('error_cls');
                          $("#promo_code_err").text(obj.message);
                          $("#duplicate_code").val('y');
                      }

                  },
                  fail: function (data) {
                      $("#promo_code_succ").text('');
                      $('#promo_code').addClass('error_cls');
                      $("#promo_code_err").text('Something went wrong, please try again later.');
                  }
              });
          }else if(promo_code !== current_code){
              $("#promo_code_succ").text('');
              $('#promo_code').addClass('error_cls');
              $("#promo_code_err").text('Please provide a unique promo code.');
          }else if(promo_code === current_code){
              $("#promo_code_succ").text('');
              $('#promo_code').removeClass('error_cls');
          }

      }
  </script>


