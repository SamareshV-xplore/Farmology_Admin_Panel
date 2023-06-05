<style type="text/css">
  
  .required_cls {
    color: red;
  }

  .reset_btn {
    margin-top: 24px;
  }

  .high_label {
    font-size: 12px;
  }

  .action_area_td {
    width: 13%;
  }

</style>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; flex-flow:row; justify-content:space-between;">
    <h1>Delivery Drivers List</h1>
    <a href="<?=base_url("add-delivery-driver")?>"><button class="btn btn-success">Add New Delivery Driver</button></a>
  </section>
  <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <form method="get" action="" id="filter_form">
                <input type="hidden" name="filter" value="true">
              
                <div class="form-group col-md-5">
                  <label for="official_email">Filter by Status </label>
                  <select name="status" id="status" class="form-control" onchange="return form_submit();">
                    <option value="all" <?php if($filter_data['status'] == 'all') { ?> selected <?php } ?>>All</option>
                    <option value="A" <?php if($filter_data['status'] == 'A') { ?> selected <?php } ?>>Active</option>
                    <option value="I" <?php if($filter_data['status'] == 'I') { ?> selected <?php } ?>>Inactive</option>
                  </select>
                </div>

                <div class="form-group col-md-2" 
                <?php if ($filter_data['status'] != "A" && $filter_data['status'] != "I" ) { ?> 
                  style="display: none;" 
                <?php } ?>>
                  <a href="<?=('delivery-drivers-list')?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <div class="table-responsive">
              <table id="delivery_drivers_listing_table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Profile Image</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>State</th>
                    <th>District</th>
                    <th>Available Pincodes</th>
                    <th>Status</th>
                    <th class="action_area_td">Options</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($delivery_drivers_list)) {
                  $total_rows = count($delivery_drivers_list);
                  foreach ($delivery_drivers_list as $i => $driver_details) { ?>
                    <tr id="<?=$driver_details->driver_id?>">
                      <td><?=$total_rows-$i?></td>
                      <td class="text-center"><img src="<?=(!empty($driver_details->profile_image)) ? FRONT_URL.$driver_details->profile_image : FRONT_URL."/uploads/delivery_driver_profile_images/user.png"?>" alt="Profile Image" class="rounded" style="width:40px; height:40px;"/></td>
                      <td><?=$driver_details->name?></td>
                      <td><?=$driver_details->phone?></td>
                      <td><?=(!empty($driver_details->email)) ? $driver_details->email : "<span style='color:#CCC; font-weight:600;'>N/A</span>"?></td>
                      <td><?=$driver_details->state?></td>
                      <td><?=$driver_details->district?></td>
                      <td><?php if (!empty($driver_details->available_pincodes)) { 
                        echo $driver_details->available_pincodes;
                      } else {
                        echo "<span style='color:#CCC; font-weight:600;'>N/A</span>";
                      }?></td>
                      <td><?=($driver_details->status == "A") ? "<span class='text-success' style='font-weight:600;'>Active</span>" : "<span class='text-danger' style='font-weight:600;'>Inactive</span>"?></td>
                      <td>
                        <a href="<?=base_url("edit-delivery-driver/$driver_details->driver_id")?>"><button type="button" class="btn btn-primary mr-2">Edit</button></a>
                        <button type="button" class="btn btn-danger" onclick="delete_delivery_driver('<?=$driver_details->driver_id?>')">Delete</button>
                      </td>
                    </tr>
                  <?php }}?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Delete Confirmation Modal Start -->
<div id="delete_confirmation_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body" style="padding:10px;">
      <form id="delete_confirmation_modal_form">
        <input type="hidden" name="driver_id" id="deletable_driver_id"/>
        <h4 class="text-center">Are you sure you want to delete this delivery driver?</h4>
        <div style="display:flex; flex-flow:row; justify-content:center;">
          <button class="btn btn-primary" style="margin-right:8px;">Yes</button>
          <button type="button" class="btn btn-secondary" onclick="close_delete_confirmation_modal()">No</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
<!-- Delete Confirmation Modal End -->

<script type="text/javascript">
    
    function form_submit() {
      $("#filter_form").submit();
    }

    $(document).ready(function(){

      $("#delivery_drivers_listing_table").dataTable({
        "language": {
          "emptyTable": "No delivery drivers added yet."
        }
      });

    });

    function delete_delivery_driver(driver_id)
    {
      $("#deletable_driver_id").val(driver_id);
      $("#delete_confirmation_modal").modal("show");
    }

    $("#delete_confirmation_modal_form").submit(function(e){
      e.preventDefault();
      var form = document.getElementById("delete_confirmation_modal_form");
      var formData = new FormData(form);

      $.ajax({
        url: "<?=base_url("delete-delivery-driver")?>",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
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
            var driver_id = $("#deletable_driver_id").val();
            $("#"+driver_id).remove();
            close_delete_confirmation_modal();           
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

    function close_delete_confirmation_modal()
    {
      $("#delete_confirmation_modal").modal("hide");
      $("#delete_confirmation_modal_form")[0].reset();
    }

</script>