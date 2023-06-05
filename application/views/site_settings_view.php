<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Master Settings 
        <small>Admin Panel</small>
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Order Settings</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('site_settings/update_order_info') ?>" id="order-info-form" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group col-lg-6">
                  <label for="minimum_order_value">Minimum Order Value</label>
                  <input type="text" class="form-control" id="minimum_order_value" name="minimum_order_value" placeholder="Name" value="<?php echo $order_settings['minimum_order_value']; ?>">
                </div>
                <div class="form-group col-lg-6">
                  <label for="max_day_order_limit">Order Taken Max Day Limit</label>
                  <input type="text" class="form-control" id="max_day_order_limit" name="max_day_order_limit" placeholder="Maximum day limit for get order." value="<?php echo $order_settings['max_day_order_limit']; ?>">
                </div>
                <div class="form-group col-lg-6">
                  <label for="online_availability">Online Payment Availability</label>
                  <select class="form-control" name="online_availability" id="online_availability">
                    <option value="N" <?php if($order_settings['online_availability'] == "N") { ?> selected="selected" <?php } ?> >Inactive</option>
                    <option value="Y" <?php if($order_settings['online_availability'] == "Y") { ?> selected="selected" <?php } ?> >Active</option>
                  </select>
                </div>

                <div class="form-group col-lg-6">
                  <label for="cod_availability">Pay On Delivery Availability</label>
                  <select class="form-control" name="cod_availability" id="cod_availability">
                    <option value="N" <?php if($order_settings['cod_availability'] == "N") { ?> selected="selected" <?php } ?> >Inactive</option>
                    <option value="Y" <?php if($order_settings['cod_availability'] == "Y") { ?> selected="selected" <?php } ?> >Active</option>
                  </select>
                </div> 

                <div class="form-group col-lg-12">
                  <label for="cod_availability">Promo Code text for Checkout Page</label>

                  <input type="text" name="promo_code_apply_text" class="form-control" value="<?=$order_settings['promo_code_apply_text']?>">
                  
                </div>
              <div class="form-group col-lg-12">
                <center><span style="color: #f39c12"><b>Last updated on <?=date("d/m/Y H:i", strtotime($order_settings['updated_date']))?></b></span></center>
              </div>
                
                
                
              </div>

              
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary" onclick="return update_order_info();">Update Info</button>
              </div>
            </form>
          </div>

        </div>
  <?php if(count($referral_settings) == 0) { ?>
        <div class="col-md-12">
         
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Referrals Settings</h3>
            </div>
            
            <form method="post" action="<?php echo base_url('site_settings/new_referral_block') ?>" role="form" id="referral-form">
              <div class="box-body">
                <div class="form-group col-lg-6">
                  <label for="referral_from">Discount Percentage (Referral From) in (%)</label>
                     <input type="text" class="form-control pull-right" id="referral_from" name="referral_from" value="" placeholder="Referral From get in (%)" >
                </div>   
                <div class="form-group col-lg-6">
                  <label for="referral_to">Discount Percentage (Referral To) in (%)</label>
                     <input type="text" class="form-control pull-right" id="referral_to" name="referral_to" value="" placeholder="Referral To get in (%)">
                </div> 
                <div class="form-group col-lg-6">
                  <label for="max_limit">Minimum Order Amount</label>
                  <input type="text" class="form-control" id="max_limit" name="max_limit" placeholder="Max Discount Amount" value="">
                </div> 
                <div class="form-group col-lg-6">
                  <label for="max_limit">Maximum Discount Amount</label>
                  <input type="text" class="form-control" id="discount_limit" name="discount_limit" placeholder="Discount Limit" value="">
                </div>           
              </div>
              

              <div class="box-footer">
                <button type="submit" class="btn btn-primary" onclick="return referral_info();">Update info</button>
              </div>
            </form>
          </div>
          
        </div>
  <?php } else {?>
    <div class="col-md-12">
         
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Referrals Settings Edit</h3>
            </div>
            
            <form method="post" action="<?php echo base_url('site_settings/update_referral_block') ?>" role="form" id="update-referral-form">
              <input type="hidden" name="referral_id" value="<?=$referral_settings[0]['id']?>">
              <div class="box-body">
                <div class="form-group col-lg-6">
                  <label for="referral_from">Discount Percentage (Referral From) in (%)</label>
                     <input type="text" class="form-control pull-right" id="referral_from" name="referral_from" value="<?=$referral_settings[0]['referral_from']?>" placeholder="Referral From get in (%)" >
                </div>   
                <div class="form-group col-lg-6">
                  <label for="referral_to">Discount Percentage (Referral To) in (%)</label>
                     <input type="text" class="form-control pull-right" id="referral_to" name="referral_to" value="<?=$referral_settings[0]['referral_to']?>" placeholder="Referral To get in (%)">
                </div> 
                <div class="form-group col-lg-6">
                  <label for="max_limit">Minimum Order Amount</label>
                  <input type="text" class="form-control" id="max_limit" name="max_limit" placeholder="Max Discount Amount" value="<?=$referral_settings[0]['min_order_amount']?>">
                </div> 
                <div class="form-group col-lg-6">
                  <label for="max_limit">Maximum Discount Amount</label>
                  <input type="text" class="form-control" id="discount_limit" name="discount_limit" placeholder="Discount Limit" value="<?=$referral_settings[0]['discount_limit']?>">
                </div>           
              </div>
              

              <div class="box-footer">
                <button type="submit" class="btn btn-primary" onclick="return update_referral_info();">Update info</button>
              </div>
            </form>
          </div>
          
        </div>
  <?php } ?>      

        <div class="col-md-6"> 
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add New Order Time Slot</h3>
            </div>
            
            <form method="post" action="<?php echo base_url('site_settings/new_time_slot') ?>" role="form" id="time-slot-form">
              <div class="box-body">
                <div class="form-group col-md-6">
                  <label for="password">Start Time <small>(24 hours format)</small></label>
                  
                           <input type="number" class="form-control pull-right" id="slot_start_time" name="slot_start_time" >
                        
                </div>
                <div class="form-group col-md-6">
                  <label for="password">End Time <small>(24 hours format)</small></label>
                  
                           <input type="number" class="form-control pull-right" id="slot_end_time" name="slot_end_time" >
                        
                </div>                
              </div>
              

              <div class="box-footer">
                <button type="button" class="btn btn-primary" onclick="return submit_new_delivery_slot();">Add New</button>
              </div>
              <hr>

              <div class="box-header with-border">
              <h3 class="box-title">Delivery Time Slot List</h3>
            </div>
              <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="display: none;">no</th>
                  <th>Time Slot</th>
                  <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
                  <?php
                  if(count($delivery_slot) > 0)
                  {
                    $bl = 0;
                    foreach($delivery_slot as $time_slot_row)
                    {
                      ?>

                      <tr>
                        <td style="display: none;"><?=$bl?></td>
                      <td><?=$time_slot_row['time_slot']?></td>
                      
                      <td class="pull-right">
                        <a href="<?=base_url('site_settings/delete_time_slot/'.$time_slot_row["id"])?>" onclick="return confirm('Are you sure want to delete this Time Slot?')">
                          <button type="button" class="btn bg-red btn-sm" title="Unblock"><i class="fa fa-trash"></i>
                            </button>
                          </a>
                      </td>
                      
                    </tr>

                    <?php
                    }
                    
                  }
                  else
                  {
                    ?>
                    <td colspan="3"><center>No Record Found.</center></td>
                    <?php
                  }
                  ?>
                
                
                
              </table>

              </div>
            </form>
          </div>
        </div>

        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Crop Health Report Subscription Amount</h3>
            </div>
            <div class="box-body">
              <form id="change_subscription_amount_form" method="post" action="<?php echo base_url('site_settings/change_subscription_amount') ?>">
                <div class="form-group">
                  <label for="latest_app_version">Change subscription amount</label>
                  <input type="text" minlength="2" maxlength="5" id="subscription_amount" name="subscription_amount" class="form-control" value="<?=(isset($subscription_amount)) ? $subscription_amount : "";?>">
                </div>
                <div class="form-group">
                  <button class="btn btn-primary">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Latest App Version</h3>
            </div>
            <div class="box-body">
              <form id="change_latest_app_version_form" method="post" action="<?php echo base_url('site_settings/change_latest_app_version') ?>">
                <div class="form-group">
                  <label for="latest_app_version">Change latest app version</label>
                  <input type="text" minlength="2" maxlength="5" id="latest_app_version" name="latest_app_version" class="form-control" value="<?=(isset($latest_app_version)) ? $latest_app_version : "";?>">
                </div>
                <div class="form-group">
                  <button class="btn btn-primary">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>


        <!-- =================================================== -->
        <!-- Delivery Driver App Version Details Container Start -->
        <!-- =================================================== -->
        <?php if (!empty($delivery_driver_app_version_details)) {
          $version_details = $delivery_driver_app_version_details;
          $latest_app_version = (!empty($version_details->latest_app_version)) ? $version_details->latest_app_version : "";
          $release_date = (!empty($version_details->release_date)) ? $version_details->release_date : "";
          $latest_app_download_link = (!empty($version_details->latest_app_download_link)) ? $version_details->latest_app_download_link : "";
          $release_note = (!empty($version_details->release_note)) ? $version_details->release_note : "";
        }?>

        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Delivery Driver App Version Details</h3>
            </div>
            <form id="delivery_driver_app_version_details_form">
              <div class="box-body" style="padding:0;">
                <div class="col-md-4" style="padding:10px 15px;">
                  <label for="latest_app_version">Latest App Version</label>
                  <input type="number" name="latest_app_version" id="latest_app_version" class="form-control" required value="<?=$latest_app_version?>"/>
                </div>
                <div class="col-md-4" style="padding:10px 15px;">
                  <label for="release_date">Release Date</label>
                  <input type="datetime-local" name="release_date" id="release_date" class="form-control" required/>
                  <?php if (!empty($release_date)) { 
                  $date = date("Y-m-d", strtotime($release_date));
                  $time = date("H:i:s", strtotime($release_date)); 
                  $datetime = $date."T".$time; ?>
                    <script>document.getElementById("release_date").value = "<?=$datetime?>";</script>  
                  <?php } ?>
                </div>
                <div class="col-md-4" style="padding:10px 15px;">
                  <label for="latest_app_download_link">Latest App Download Link</label>
                  <input type="url" name="latest_app_download_link" id="latest_app_download_link" class="form-control" required value="<?=$latest_app_download_link?>"/>
                </div>
                <div class="col-md-12" style="padding:10px 15px; padding-top:0;">
                  <label for="release_note">Release Note</label>
                  <textarea name="release_note" id="release_note" class="form-control" style="height:200px; resize:none;"><?=$release_note?></textarea>
                </div>
              </div>
              <div class="box-footer">
                <button id="delivery_driver_app_version_details_form_submit_button" class="btn btn-primary" style="float:right;">Save</button>
              </div>
            </form>
          </div>
        </div>
        <!-- ================================================= -->
        <!-- Delivery Driver App Version Details Container End -->
        <!-- ================================================= -->

        <!-- /.box -->
      </div>
    </section>
    <!-- /.content -->
  </div>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script type="text/javascript">

    function submit_new_delivery_slot()
    {
       $('.form-control').removeClass('error_cls');
       var start_time = document.getElementById("slot_start_time").value.trim();
        var end_time = document.getElementById("slot_end_time").value.trim();
        

      var focusStatus = "N";

      if(start_time < 0 || start_time == 0)
      {
        $('#slot_start_time').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#slot_start_time').focus();
            focusStatus = 'Y';
        }     
      }

      if(end_time < 0 || end_time == 0)
      {
        $('#slot_end_time').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#slot_end_time').focus();
            focusStatus = 'Y';
        }     
      }

      if(focusStatus == 'N')
      {
        $("#time-slot-form").submit();
      }

      return false;
    }

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

    function update_order_info()
    {
      $('.form-control').removeClass('error_cls');

      var minimum_order_value = document.getElementById("minimum_order_value").value.trim();
      var max_day_order_limit = document.getElementById("max_day_order_limit").value.trim();     
      
      
      var focusStatus = "N";

      if(minimum_order_value == '' || isNaN(minimum_order_value))
      {
        $('#minimum_order_value').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#minimum_order_value').focus();
            focusStatus = 'Y';
        }     
      }

       if(max_day_order_limit == '' || isNaN(max_day_order_limit))
      {
        $('#max_day_order_limit').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#max_day_order_limit').focus();
            focusStatus = 'Y';
        }     
      }

      
      if(focusStatus == "N")
      {
        $("#order-info-form").submit();
      }

      return false;
    }
    function referral_info()
    {

      $('.form-control').removeClass('error_cls');

      var referral_from = document.getElementById("referral_from").value.trim();
      var referral_to = document.getElementById("referral_to").value.trim(); 
      var max_limit =  document.getElementById("max_limit").value.trim();  
      var discount_limit = document.getElementById("discount_limit").value.trim();

      
      
      var focusStatus = "N";

      if(referral_from == '' || isNaN(referral_from))
      {
        $('#referral_from').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#referral_from').focus();
            focusStatus = 'Y';
        }     
      }

       if(referral_to == '' || isNaN(referral_to))
      {
        $('#referral_to').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#referral_to').focus();
            focusStatus = 'Y';
        }     
      }
      if(max_limit == '' || isNaN(max_limit))
      {
        $('#max_limit').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#max_limit').focus();
            focusStatus = 'Y';
        }     
      }
      if(discount_limit == '' || isNaN(discount_limit))
      {
        $('#discount_limit').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#discount_limit').focus();
            focusStatus = 'Y';
        }     
      }

      
      if(focusStatus == "N")
      {
        $("#referral-form").submit();
      }

      return false;
    }
    fubction update_referral_info()
    {


      $('.form-control').removeClass('error_cls');

      var referral_from = document.getElementById("referral_from").value.trim();
      var referral_to = document.getElementById("referral_to").value.trim(); 
      var max_limit =  document.getElementById("max_limit").value.trim();  
      var discount_limit = document.getElementById("discount_limit").value.trim();

      
      
      var focusStatus = "N";

      if(referral_from == '' || isNaN(referral_from))
      {
        $('#referral_from').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#referral_from').focus();
            focusStatus = 'Y';
        }     
      }

       if(referral_to == '' || isNaN(referral_to))
      {
        $('#referral_to').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#referral_to').focus();
            focusStatus = 'Y';
        }     
      }
      if(max_limit == '' || isNaN(max_limit))
      {
        $('#max_limit').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#max_limit').focus();
            focusStatus = 'Y';
        }     
      }
      if(discount_limit == '' || isNaN(discount_limit))
      {
        $('#discount_limit').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#discount_limit').focus();
            focusStatus = 'Y';
        }     
      }

      
      if(focusStatus == "N")
      {
        $("#update-referral-form").submit();
      }

      return false;
    }
  </script>

  <!-- ================================================ -->
  <!-- Delivery Driver App Version Details Script Start -->
  <!-- ================================================ -->
  <script>

    $("#delivery_driver_app_version_details_form").submit(function(e){
      e.preventDefault();
      var form = document.getElementById("delivery_driver_app_version_details_form");
      var formData = new FormData(form);
      var submit_button = $("#delivery_driver_app_version_details_form_submit_button");

      $.ajax({
        url: "<?=base_url("update-delivery-driver-app-version-details")?>",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function()
        {
          submit_button.attr("disabled", "disabled");
          submit_button.text("Saving...");
        },
        complete: function()
        {
          submit_button.text("Save");
          submit_button.removeAttr("disabled");
        },
        error: function(a, b, c)
        {
          toast("Something went wrong! Please try again later.", 1500);
          console.log(a);
          console.log(b);
          console.log(c);
        },
        success: function(response)
        {
          if (response.success == true)
          {
            toast(response.message, 1200);
          }
          else if (response.success == false)
          {
            toast(response.message, 1500);
            console.log(response.message);
          }
          else
          {
            toast("Something went wrong! Please try again later.", 1500);
            console.log(response);
          }
        }
      });
    });

  </script>
  <!-- ============================================== -->
  <!-- Delivery Driver App Version Details Script End -->
  <!-- ============================================== -->