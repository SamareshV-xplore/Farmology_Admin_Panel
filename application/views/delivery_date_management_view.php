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
  
  .sl_margin {
      margin-right: 5px;
   }

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
  <section class="content-header" style="display:flex; flex-flow:row; justify-content:space-between;">
    <h1>Delivery Date Management</h1>
    <button class="btn btn-success" onclick="add_delivery_date()">Add Delivery Date for New District</button>
  </section>
  <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <div class="table-responsive">
              <table id="delivery_date_listing_table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>District</th>
                    <th>No. of Days for Delivery</th>
                    <th class="action_area_td">Options</th>
                  </tr>
                </thead>
                <tbody style="font-size:15px;">
                <?php if (!empty($delivery_dates_list)) {
                $total_rows = count($delivery_dates_list);
                foreach ($delivery_dates_list as $i => $delivery_date_details) { 
                $delivery_date_id = $delivery_date_details->hash_id;
                $district = $delivery_date_details->district;
                $no_of_days = $delivery_date_details->no_of_days_for_delivery; 
                $current_date = date("jS F Y");
                $delivery_date = date("jS F Y", strtotime("+".$no_of_days." days", time()));?>

                  <tr id="<?=$delivery_date_id?>">
                    <td style="font-weight:600;"><?=$total_rows-$i?></td>
                    <td class="district" style="font-weight:600;"><?=$district?></td>
                    <td>
                      <span class="no_of_days" style="font-weight:600;"><?=$no_of_days." Day(s)"?></span><br/>
                      <span><i>*Note: </i>orders placed in <?=$current_date?> will be delivered on <?=$delivery_date?>.</span>
                    </td>
                    <td>
                      <button type="button" class="btn btn-primary" style="margin-right:8px;" onclick="edit_delivery_date('<?=$delivery_date_id?>')"><i class="fa fa-edit"></i></button>
                      <button type="button" class="btn btn-danger" onclick="delete_delivery_date('<?=$delivery_date_id?>')"><i class="fa fa-trash"></i></button>
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


<!-- ========================= -->
<!-- Delivery Date Modal Start -->
<!-- ========================= -->
<div id="delivery_date_modal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="delivery_date_modal_title" class="modal-title">Add Delivery Date</h5>
      </div>
      <div class="modal-body" style="padding:10px;">
      <form id="delivery_date_modal_form">
         <input type="hidden" id="delivery_date_modal_form_type" value="assign"/>
         <input type="hidden" name="hash_id" id="delivery_date_hash_id"/>
         <div class="form-group">
          <label for="district">District</label>
          <input type="text" name="district" id="district" class="form-control" list="list_of_districts" required/>
          <?php if (!empty($list_of_districts)) { ?>
            <datalist id="list_of_districts">
            <?php foreach ($list_of_districts as $district_details) { ?>
              <option value="<?=$district_details->name?>"><?=$district_details->name?></option>
            <?php } ?>
            </datalist>
          <?php } ?>
        </div>
        <div class="form-group">
          <label for="no_of_days_for_delivery">No. of Days for Delivery</label>
          <input type="number" name="no_of_days_for_delivery" id="no_of_days_for_delivery" class="form-control" required/>
        </div>
        <div style="display:flex; flex-flow:row; justify-content:end;">
          <button id="delivery_date_modal_submit_button" class="btn btn-primary" style="margin-right:8px;">Add</button>
          <button type="button" class="btn btn-secondary" onclick="close_delivery_date_modal()">Close</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
<!-- ======================= -->
<!-- Delivery Date Modal End -->
<!-- ======================= -->

<!-- =============================== -->
<!-- Delete Confirmation Modal Start -->
<!-- =============================== -->
<div id="delete_confirmation_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body" style="padding:10px;">
      <form id="delete_confirmation_modal_form">
        <input type="hidden" name="hash_id" id="deletable_delivery_date_id"/>
        <h4 class="text-center">Are you sure you want to delete this delivery date?</h4>
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

<script type="text/javascript">

    $(document).ready(function(){

      $("#delivery_date_listing_table").dataTable({
        "language": {
          "emptyTable": "No delivery date added yet."
        },
        "order": [[0,"desc"]]
      });

    });

    function add_delivery_date()
    {
        $("#delivery_date_modal_title").text("Add Delivery Date");
        $("#delivery_date_modal_submit_button").text("Add");
        $("#delivery_date_modal_form_type").val("Add");
        $("#delivery_date_modal").modal("show");
    }

    $("#delivery_date_modal_form").submit(function(e){
        e.preventDefault();
        var form = document.getElementById("delivery_date_modal_form");
        var formData = new FormData(form);
        var submit_button = $("#delivery_date_modal_submit_button");
        var plain_text = $("#delivery_date_modal_form_type").val();
        var loading_text = plain_text+"ing...";

        $.ajax({
            url: "<?=base_url("add-delivery-date")?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function()
            {
                submit_button.attr("disabled", "disabled");
                submit_button.text(loading_text);
            },
            complete: function()
            {
                submit_button.text(plain_text);
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
              // console.log(response);
                if (response.success == true)
                {
                    toast(response.message, 1200);
                    close_delivery_date_modal();
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                }
                else if (response.success == false)
                {
                    toast(response.message, 1500);
                    console.log(response.console_message);
                }
                else
                {
                    toast("Something went wrong! Please try again later.", 1500);
                    console.log(response);
                }
            }
        });
    });

    function close_delivery_date_modal()
    {
        $("#delivery_date_modal_form")[0].reset();
        $("#delivery_date_modal").modal("hide");
    }

    function edit_delivery_date(delivery_date_id)
    {
        $("#delivery_date_modal_title").text("Edit Delivery Date");
        $("#delivery_date_modal_submit_button").text("Save");
        $("#delivery_date_modal_form_type").val("Save");

        var row = $("#"+delivery_date_id);
        var district = row.find(".district").text();
        var no_of_days = row.find(".no_of_days").text();
        no_of_days = no_of_days.split(" ")[0];

        $("#delivery_date_hash_id").val(delivery_date_id);
        $("#district").val(district);
        $("#no_of_days_for_delivery").val(no_of_days);
        $("#delivery_date_modal").modal("show");
    }

    function delete_delivery_date(delivery_date_id)
    {
      $("#deletable_delivery_date_id").val(delivery_date_id);
      $("#delete_confirmation_modal").modal("show");
    }

    $("#delete_confirmation_modal_form").submit(function(e){
      e.preventDefault();
      var form = document.getElementById("delete_confirmation_modal_form");
      var formData = new FormData(form);

      $.ajax({
        url: "<?=base_url("delete-delivery-date")?>",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
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
            var delivery_date_id = $("#deletable_delivery_date_id").val();
            $("#"+delivery_date_id).remove();
            close_delete_confirmation_modal();           
          }
          else if (response.success == false)
          {
            toast(response.message, 1500);
            console.log(response.console_message);
          }
          else
          {
            toast("Something went wrong! Please try again later.", 1500);
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