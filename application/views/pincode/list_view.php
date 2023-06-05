<style>

  .btn:focus {
    outline: none !important;
    box-shadow: none !important;
  }

  .modal-header {
    text-align: center;
    padding: 8px;
  }

  .modal-header h5 {
    font-size: 18px;
    font-weight: 600;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Pincodes List</h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
              <div class="form-group col-md-6">
                <?php $district_id = (!empty($district_details->id)) ? $district_details->id : "";
                $district_name = (!empty($district_details->name)) ? $district_details->name : ""; ?>
                  <label for="official_email">List of Pincodes for <h2><?=$district_name?></h2></label>
              </div>
              <div class="form-group col-md-6">
                  <button type="button" class="btn btn-primary pull-right" onclick="add_district_pincode()">Add New Pincode</button>
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="table-responsive">
            <table id="pincodes_listing_table" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>No.</th>
                <th>Pincode</th>
                <th>Created Date</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
              <?php if (!empty($pincodes_list)) {
              $total_rows = count($pincodes_list);
              foreach ($pincodes_list as $i => $pincode_details) {
              $pincode_id = $pincode_details->id; ?>
                <tr id="<?=$pincode_id?>">
                  <td><?=$total_rows-$i?></td>
                  <td><?=$pincode_details->pin_code?></td>
                  <td><?=date("jS F Y", strtotime($pincode_details->created_date))?></td>
                  <td>
                    <button class="btn bg-yellow btn-sm" style="margin-right:5px;" onclick="edit_district_pincode('<?=$pincode_details->pin_code?>')"><i class="fa fa-edit"></i></button>
                    <button class="btn bg-red btn-sm" onclick="delete_district_pincode('<?=$pincode_details->pin_code?>')"><i class="fa fa-trash"></i></button>
                  </td>
                </tr>
              <?php }} ?>
              </tbody>
            </table>
          </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- ============================ -->
<!-- Pincode Add/Edit Modal Start -->
<!-- ============================ -->
<div id="pincode_modal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="pincode_modal_title" class="modal-title">Add Pincode</h5>
      </div>
      <div class="modal-body" style="padding:10px;">
      <form id="pincode_modal_form">
        <input type="hidden" name="district_id" id="district_id" value="<?=$district_id?>"/>
        <div class="form-group">
          <label>District</label>
          <input type="text" class="form-control plain-text" value="<?=$district_name?>" readonly/>
        </div>
        <div class="form-group">
          <label for="pincode">Pincode</label>
          <input type="text" name="pincode" id="pincode" class="form-control" required/>
        </div>
        <div style="display:flex; flex-flow:row; justify-content:end;">
          <button id="pincode_modal_submit_button" class="btn btn-primary" style="margin-right:8px;">Add</button>
          <button type="button" class="btn btn-secondary" onclick="close_pincode_modal()">Close</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
<!-- ========================== -->
<!-- Pincode Add/Edit Modal End -->
<!-- ========================== -->


<!-- =============================== -->
<!-- Delete Confirmation Modal Start -->
<!-- =============================== -->
<div id="delete_confirmation_modal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body" style="padding:10px;">
      <form id="delete_confirmation_modal_form">
        <input type="hidden" name="district_id" id="district_id"/>
        <input type="hidden" name="pincode" id="deletable_pincode"/>
        <h4 class="text-center">Are you sure you want to delete this pincode?</h4>
        <div style="display:flex; flex-flow:row; justify-content:center;">
          <button class="btn btn-primary" style="margin-right:8px;">Yes</button>
          <button type="button" class="btn btn-secondary" onclick="close_delete_confirmation_modal()">No</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
<!-- ============================= -->
<!-- Delete Confirmation Modal End -->
<!-- ============================= -->


<script>

  $(document).ready(function(){

    $("#pincodes_listing_table").dataTable({
      "language": {
        "emptyTable": "No pincodes available"
      },
      "order": [[0,"desc"]]
    });

  });

  function add_district_pincode()
  {
    $("#pincode_modal_form")[0].reset();
    $("#pincode_modal_title").text("Add Pincode");
    $("#pincode_modal_submit_button").text("Add");
    $("#pincode_modal").modal("show");
  }

  $("#pincode_modal_form").submit(function(e){
    e.preventDefault();
    var form = document.getElementById("pincode_modal_form");
    var formData = new FormData(form);

    $.ajax({
      url: "<?=base_url('add-district-pincode')?>",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function()
      {
        $("#pincode_modal_submit_button").attr("disabled", "disabled");
      },
      complete: function()
      {
        $("#pincode_modal_submit_button").removeAttr("disabled");
      },
      error: function(a, b, c)
      {
        toast("Something went wrong! Please try again later.");
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function(response)
      {
        if (response.success == true)
        {
          toast(response.message, 1200);
          close_pincode_modal();
          setTimeout(() => {
            location.reload();
          }, 600);
        }
        else if (response.success == false)
        {
          toast(response.message, 1500);
        }
        else
        {
          toast("Something went wrong! Please try again later.");
          console.log(response);
        }
      }
    });
  });

  function close_pincode_modal()
  {
    $("#pincode_modal").modal("hide");
  }

  function edit_district_pincode(pincode)
  {
    $("#pincode_modal_form")[0].reset();
    $("#pincode_modal_title").text("Edit Pincode");
    $("#pincode_modal_submit_button").text("Save");
    $("#pincode").val(pincode);
    $("#pincode_modal").modal("show");
  }

  function delete_district_pincode(pincode)
  {
    $("#delete_confirmation_modal_form")[0].reset();
    $("#deletable_pincode").val(pincode);
    $("#delete_confirmation_modal").modal("show");
  }
  
  $("#delete_confirmation_modal_form").submit(function(e){
    e.preventDefault();
    var form = document.getElementById("delete_confirmation_modal_form");
    var formData = new FormData(form);

    $.ajax({
      url: "<?=base_url('delete-district-pincode')?>",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      error: function(a, b, c)
      {
        toast("Something went wrong! Please try again later.");
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function(response)
      {
        if (response.success == true)
        {
          toast(response.message, 1200);
          close_delete_confirmation_modal();
          setTimeout(() => {
            location.reload();
          }, 600);
        }
        else if (response.success == false)
        {
          toast(response.message, 1500);
        }
        else
        {
          toast("Something went wrong! Please try again later.");
          console.log(response);
        }
      }
    });
  });

  function close_delete_confirmation_modal()
  {
    $("#delete_confirmation_modal").modal("hide");
  }

</script>