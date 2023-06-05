<div class="content-wrapper">
    <section class="content-header">
      <h1>Add New Delivery Driver</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Delivery Driver Details</h3>
            </div>
            <form id="delivery_driver_form">
              <div class="box-body">
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required/>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-control" required/>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"/>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/jpg, image/png"/>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="payment_qr_code_image">Payment QR Code Image</label>
                    <input type="file" name="payment_qr_code_image" id="payment_qr_code_image" class="form-control" accept="image/jpg, image/png" required/>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="state">State</label>
                    <select name="state_id" id="state" class="form-control" required onchange="get_districts_list()">
                      <?php if (!empty($states_list)) { ?>
                      <option value="">Choose Delivery Driver State</option>
                      <?php foreach ($states_list as $state_details) { ?>
                        <option value="<?=$state_details->id?>"><?=$state_details->state?></option>
                      <?php }} ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="district">District</label>
                    <select name="district_id" id="district" class="form-control" onchange="get_available_pincodes_list()" required>
                      <?php if (!empty($district_list)) { ?>
                      <option value="">Choose Delivery Driver District</option>
                      <?php foreach ($district_list as $district_details) { ?>
                        <option value="<?=$district_details->id?>"><?=$district_details->name?></option>
                      <?php }} else { ?>
                        <option value="">No District Found</option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="available_pincodes">Available Pincodes</label>
                    <select name="available_pincodes[]" id="available_pincodes" class="form-control" multiple>
                      <option value="">No Pincode Found</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                      <option value="A">Active</option>
                      <option value="I">Inactive</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="box-footer">
                <button id="submit_button" class="btn btn-primary pull-right">Add Delivery Driver</button>
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

  function get_available_pincodes_list()
  {
    var state = $("#district").val();
    $.ajax({
      url: "<?=base_url('get-available-pincodes-list-by-district/')?>"+state,
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
          render_available_pincodes_list(response.available_pincodes_list);
        }
        else if (response.success == false)
        {
          render_available_pincodes_list();
        }
        else
        {
          toast("Someting went wrong! Please try again later.", 1500);
          console.log(response);
        }
      }
    });
  }

  function render_available_pincodes_list(available_pincodes_list = null)
  {
    if (available_pincodes_list)
    {
      var pincode_input_options = "<option value=''>Choose Driver Available Pincodes</option>";
      available_pincodes_list.forEach((pincode_details, i) => {
        pincode_input_options += `<option value="${pincode_details.pin_code}">${pincode_details.pin_code}</option>`;
      });
    }
    else
    {
      var pincode_input_options = "<option value=''>No Pincode Found</option>";
    }
    $("#available_pincodes").html(pincode_input_options);
  }

  $("#delivery_driver_form").submit(function(e){
    e.preventDefault();
    var form = document.getElementById("delivery_driver_form");
    var formData = new FormData(form);

    $.ajax({
      url: "<?=base_url('add-new-delivery-driver')?>",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function()
      {
        $("#submit_button").attr("disabled", "disabled");
        $("#submit_button").text("Adding Delivery Driver...");
      },
      complete: function()
      {
        $("#submit_button").text("Add Delivery Driver");
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
          $("#delivery_driver_form")[0].reset();
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