<style>

  .existing-image-preview {
    height:200px;
    object-fit:contain;
  }

</style>
<div class="content-wrapper">
    <section class="content-header">
      <h1>Edit Existing Delivery Driver</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Delivery Driver Details</h3>
            </div>
            <form id="delivery_driver_edit_form">
              <input type="hidden" name="driver_id" value="<?=(!empty($driver_details->driver_id)) ? $driver_details->driver_id : ""?>"/>
              <div class="box-body">
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required value="<?=(!empty($driver_details->name)) ? $driver_details->name : ''?>"/>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-control" required value="<?=(!empty($driver_details->phone)) ? $driver_details->phone : ''?>"/>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?=(!empty($driver_details->email)) ? $driver_details->email : ''?>"/>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/jpg, image/png"/>
                    <?php if (!empty($driver_details->profile_image)) { ?>
                      <label style="margin-top:8px;">Previously Uploaded Profile Image</label><br/>
                      <img src="<?=FRONT_URL.$driver_details->profile_image?>" alt="Previously Uploaded Profile Image" class="existing-image-preview"/>
                    <?php } ?>
                  </div>
                  <div class="form-group col-md-6">
                    <?php $required_status = (empty($driver_details->payment_qr_code_image)) ? "required" : ""; ?>
                    <label for="payment_qr_code_image">Payment QR Code Image</label>
                    <input type="file" name="payment_qr_code_image" id="payment_qr_code_image" class="form-control" accept="image/jpg, image/png" <?=$required_status?>/>
                    <?php if (!empty($driver_details->payment_qr_code_image)) { ?>
                      <label style="margin-top:8px;">Previously Uploaded QR Code Image</label><br/>
                      <img src="<?=FRONT_URL.$driver_details->payment_qr_code_image?>" alt="Previously Uploaded QR Code Image" class="existing-image-preview"/>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="state">State</label>
                    <select name="state_id" id="state" class="form-control" required onchange="get_districts_list()">
                      <?php if (!empty($states_list)) { ?>
                      <option value="">Choose Delivery Driver State</option>
                      <?php foreach ($states_list as $state_details) { 
                      $state_selection_status = ($state_details->id == $driver_details->state_id) ? "selected" : ""; ?>
                        <option value="<?=$state_details->id?>" <?=$state_selection_status?>><?=$state_details->state?></option>
                      <?php }} ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="district">District</label>
                    <select name="district_id" id="district" class="form-control" required>
                      <?php if (!empty($district_list)) { ?>
                      <option value="">Choose Delivery Driver District</option>
                      <?php foreach ($district_list as $district_details) { 
                      $district_selection_status = ($district_details->id == $driver_details->district_id) ? "selected" : ""; ?>
                        <option value="<?=$district_details->id?>" <?=$district_selection_status?>><?=$district_details->name?></option>
                      <?php }} else { ?>
                        <option value="">No District Found</option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <?php $selected_pincodes = (!empty($driver_details->available_pincodes)) ? explode(",", $driver_details->available_pincodes) : []; ?>
                    <label for="available_pincodes">Available Pincodes</label>
                    <select name="available_pincodes[]" id="available_pincodes" class="form-control" multiple>
                      <?php if (!empty($available_pincodes_list)) {
                      foreach ($available_pincodes_list as $pincode_details) { 
                        if (in_array($pincode_details->pin_code, $selected_pincodes)) {
                          echo "<option value='".$pincode_details->pin_code."' selected>".$pincode_details->pin_code."</option>";
                        } else {
                          echo "<option value='".$pincode_details->pin_code."'>".$pincode_details->pin_code."</option>";
                        } 
                      }} else { ?>
                        <option value="">No Pincode Found</option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                      <option value="A" <?=($driver_details->status == "A") ? "selected" : ""?>>Active</option>
                      <option value="I" <?=($driver_details->status == "I") ? "selected" : ""?>>Inactive</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="box-footer">
                <button id="submit_button" class="btn btn-primary pull-right">Save Driver Details</button>
                <a href="<?=base_url('delivery-drivers-list'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

<script>

  function get_districts_list()
  {
    var state = $("#state").val();
    $.ajax({
      url: "<?=base_url('get-districts-list-by-state/')?>"+state,
      type: "GET",
      error: function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function (response)
      {
        if (response.success == true)
        {
          render_districts_list(response.districts_list);
        }
        else if (response.success == false)
        {
          render_districts_list();
        }
        else
        {
          toast("Someting went wrong! Please try again later.", 1500);
          console.log(response);
        }
      }
    });
  }

  function render_districts_list(districts_list = null)
  {
    if (districts_list)
    {
      var district_input_options = "<option value=''>Choose Delivery Driver District</option>";
      districts_list.forEach((district_details, i) => {
        district_input_options += `<option value="${district_details.id}">${district_details.name}</option>`;
      });
    }
    else
    {
      var district_input_options = "<option value=''>No District Found</option>";
    }
    $("#district").html(district_input_options);
  }

  $("#delivery_driver_edit_form").submit(function(e){
    e.preventDefault();
    var form = document.getElementById("delivery_driver_edit_form");
    var formData = new FormData(form);

    $.ajax({
      url: "<?=base_url('edit-existing-delivery-driver')?>",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function()
      {
        $("#submit_button").attr("disabled", "disabled");
        $("#submit_button").text("Saving Driver Details...");
      },
      complete: function()
      {
        $("#submit_button").text("Save Driver Details");
        $("#submit_button").removeAttr("disabled");
      },
      error: function(a, b, c)
      {
        toast("Something went wrong! Please try again later.", 1200);
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function(response)
      {
        if (response.success == true)
        {
          toast(response.message, 1500);
          setTimeout(() => {
            location.href = response.redirect_to;
          }, 800);
        }
        else if (response.success == false)
        {
          toast(response.message, 1200);
          console.log(response.console_message);
        }
        else
        {
          toast("Something went wrong! Please try again later.", 1200);
          console.log(response);
        }
      }
    });
  });

</script>