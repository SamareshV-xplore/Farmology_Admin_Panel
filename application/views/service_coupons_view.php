<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="<?=base_url('assets/css/farmology_new_pages.css')?>"/>
<style>

    .header-with-button {
        margin: 0;
        padding: 0;
        display: flex;
        flex-flow: row;
        align-items: center;
        justify-content: space-between;
    }

    .settings_button {
        width: 40px;
        height: 40px;
        margin: 0;
        padding: 0;
        font-size: 20px;
        border-radius: 50%;
        color: black;
        background-color: white;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        display: grid;
        place-items: center;
    }

    .btn:focus {
        outline: none !important;
    }

    a {
        text-decoration: none !important;
        color: black;
    }

</style>

<!-- Delete Coupon Modal Start -->
<div class="modal fade" id="delete_coupon_modal" tabindex="-1" role="dialog" aria-labelledby="delete_coupon_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <form id="delete_coupon_modal_form">
                    <input type="hidden" name="hash_id" id="coupon_hash_id"/>
                    <div style="font-size:18px;margin-bottom:12px;">
                        Are you sure you want to delete this coupon?
                    </div>
                    <div>
                        <button class="btn btn-primary" style="margin-right:8px;">Yes</button>
                        <button type="button" class="btn btn-secondary" onclick="close_delete_coupon_modal()">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Coupon Modal End -->

<!-- Add Coupon Modal Start -->
<div class="modal fade" id="add_coupon_modal" tabindex="-1" role="dialog" aria-labelledby="add_coupon_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title header-with-button" id="add_coupon_modal_title">
                    <span>Create New Service Coupon</span>
                    <button type="button" class="close" onclick="close_add_coupon_modal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <form id="add_coupon_modal_form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon Title</label>
                                <input type="text" name="coupon_title" class="form-control" required/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon Code</label>
                                <div class="row">
                                    <div class="col-md-8" style="padding:0;">
                                        <input type="text" id="coupon_code_input" name="coupon_code" class="form-control" required/>
                                    </div>
                                    <div class="col-md-4" style="padding:0 0 0 2px;">
                                        <button type="button" id="generate_coupon_code_button" class="btn btn-success" onclick="generateRandomCouponCode()">Generate</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Coupon Type</label>
                        <select name="coupon_type" class="form-control" required>
                            <option value="">Select Coupon Type</option>
                            <option value="B">Crop Health & Soil Health</option>
                            <option value="CH">Crop Health</option>
                            <option value="SH">Soil Health</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon Discount</label>
                                <input type="text" name="coupon_discount" class="form-control" required/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Discount Type</label>
                                <select name="discount_type" class="form-control" required>
                                    <option value="">Select Discount Type</option>
                                    <option value="F">Flat</option>
                                    <option value="P">Percentage</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Multiple Time Usage Allowed</label><br/>
                                <input type="radio" name="multiple_time_usage_allowed" value="Y" onclick="toggle_maximum_usage_limit_input()"/> Yes &nbsp;
                                <input type="radio" name="multiple_time_usage_allowed" value="N" onclick="toggle_maximum_usage_limit_input()" checked/> No
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="maximum_usage_limit">Maximum Usage Limit</label>
                                <select name="maximum_usage_limit" id="maximum_usage_limit" class="form-control" disabled="disabled">
                                    <option value="">Unlimited</option>
                                    <?php for($i=1000; $i<=10000; $i += 1000) { ?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Coupon Time Limited</label> &nbsp; &nbsp;
                        <input type="radio" name="coupon_time_limit" value="Y" onclick="toggleTimeLimitSection()"/>Yes &nbsp;
                        <input type="radio" name="coupon_time_limit" value="N" onclick="toggleTimeLimitSection()" checked/>No
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Available From</label>
                                <input type="datetime-local" name="available_from" class="form-control" disabled="disabled"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Available To</label>
                                <input type="datetime-local" name="available_to" class="form-control" disabled="disabled"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="add_coupon_modal_form" id="create_coupon_button" class="btn btn-primary">Create</button>
                <button type="button" class="btn btn-secondary" onclick="close_add_coupon_modal()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Add Coupon Modal End -->

<div class="content-wrapper">
  <section class="content-header">
    <h3 class="header-with-button">
        <span>Service Coupons</span>
        <button class="btn btn-primary" onclick="add_coupon()">Create New Coupon</button>
    </h3>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">

            <div class="table-responsive">
              <table id="service_coupons_listing_table" class="table table-striped vertical-align-middle display">
                <thead>
                    <tr>
                        <th style="text-align:left;">#</th>
                        <th style="text-align:left;">Coupon Title</th>
                        <th>Coupon Code</th>
                        <th>Coupon Type</th>
                        <th>Coupon Discount</th>
                        <th>Coupon Time Limit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($service_coupons_list)) {
                    foreach ($service_coupons_list as $i => $service_coupon) { ?>
                        <tr>
                        <td style="text-align:left;"><?=$i+1?></th>
                        <td style="text-align:left;"><?=$service_coupon->coupon_title?></td>
                        <td><?=$service_coupon->coupon_code?></td>
                        <td>
                            <?php if ($service_coupon->coupon_type == "B") {
                                echo "Crop Health and Soil Health";
                            } elseif ($service_coupon->coupon_type == "CH") {
                                echo "Crop Health";
                            } elseif ($service_coupon->coupon_type == "SH") {
                                echo "Soil Health";
                            }?>
                        </td>
                        <td>
                            <?php if ($service_coupon->discount_type == "P") {
                                echo $service_coupon->coupon_discount."% OFF";
                            } elseif ($service_coupon->discount_type == "F") {
                                echo "FLAT ₹".$service_coupon->coupon_discount." OFF"; 
                            }?>
                        </td>
                        <td>
                            <?php if ($service_coupon->coupon_time_limit == "Y") {
                                $start_date = date("jS F Y", strtotime($service_coupon->available_from));
                                $end_date = date("jS F Y", strtotime($service_coupon->available_to));
                                echo "FROM ".$start_date." TO ".$end_date;
                            } elseif ($service_coupon->coupon_time_limit == "N") {
                                echo "<span class='text-muted'>NONE</span>";
                            }?>
                        </td>
                        <td>
                            <?php if ($service_coupon->status != "A") {
                                echo "<span class='text-danger'>INVALID</span>";
                            } elseif ($service_coupon->coupon_time_limit == "Y") {
                                $current_date = time();
                                $available_to = strtotime($service_coupon->available_to);
                                if ($current_date <= $available_to) {
                                    echo "<span class='text-success'>VALID</span>";
                                } elseif ($current_date > $available_to) {
                                    echo "<span class='text-danger'>INVALID</span>";
                                }
                            } elseif ($service_coupon->coupon_time_limit == "N") {
                                echo "<span class='text-success'>VALID</span>";
                            }?>
                        </td>
                        <td>
                            <a href="javascript:delete_coupon('<?=$service_coupon->hash_id?>')">
                                <i class="fa-solid fa-xmark cross_icons"></i>
                            </a>
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
<script>
  
  $(document).ready(function(){

    $("#service_coupons_listing_table").DataTable({
      "language": {
        "emptyTable": "No service coupon is available",
      },
      "order" : [[0,'desc']]
    });

    $("#add_coupon_modal_form").on("submit", function(e){
        e.preventDefault();
        var form = document.getElementById("add_coupon_modal_form");
        var formData = new FormData(form);

        $.ajax({
            url: "<?=base_url("add-service-coupon")?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function()
            {
                $("#create_coupon_button").attr("disabled", "disabled");
                $("#create_coupon_button").text("Creating...");
            },
            complete: function()
            {
                $("#create_coupon_button").text("Create");
                $("#create_coupon_button").removeAttr("disabled");
            },
            error: function(a, b, c)
            {
                console.log(a);
                console.log(b);
                console.log(c);
            },
            success: function(data)
            {
                if (data.success)
                {
                    close_add_coupon_modal();
                    location.reload();
                }
                else
                {
                    console.log(data.message);
                }
            }
        });
    });

  });

  function add_coupon()
  {
    $("#add_coupon_modal").modal("show");
  }

  function close_add_coupon_modal()
  {
    $("#add_coupon_modal").modal("hide");
  }
  
  function generateRandomCouponCode()
  {
    var keys = "abcdefghijklmnopqrstubwsyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    var code='';
    for(i=0; i<6; i++)
    {
        code += keys.charAt(Math.floor(Math.random()*keys.length));
    }
    $("#coupon_code_input").val(code);
  }

  function toggleTimeLimitSection()
  {
    var available_from_input = $("#add_coupon_modal_form input[name='available_from']");
    var available_to_input = $("#add_coupon_modal_form input[name='available_to']");
    var coupon_time_limit = $("#add_coupon_modal_form input[name='coupon_time_limit']:checked").val();
    if (coupon_time_limit == "Y")
    {
        available_from_input.removeAttr("disabled");
        available_to_input.removeAttr("disabled");

        available_from_input.attr("required","required");
        available_to_input.attr("required","required");
    }
    else
    {
        available_from_input.removeAttr("required");
        available_to_input.removeAttr("required");

        available_from_input.attr("disabled","disabled");
        available_to_input.attr("disabled","disabled");
    }
  }

  function delete_coupon(coupon_id)
  {
    $("#coupon_hash_id").val(coupon_id);
    $("#delete_coupon_modal").modal("show");
  }

  function close_delete_coupon_modal()
  {
    $("#delete_coupon_modal").modal("hide");
    $("#delete_coupon_modal_form")[0].reset();
  }

  $("#delete_coupon_modal_form").on("submit", function(e){
    e.preventDefault();
    var form = document.getElementById("delete_coupon_modal_form");
    var formData = new FormData(form);

    $.ajax({
        url: "<?=base_url("delete-service-coupon")?>",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        error: function(a, b, c)
        {
            console.log(a);
            console.log(b);
            console.log(c);
        },
        success: function(data)
        {
            if (data.success)
            {
                location.reload();
            }
            else
            {
                console.log(data.message);
            }
        }
    });
  });

  function toggle_maximum_usage_limit_input()
  {
    var multiple_time_usage_allowed = $("input[name='multiple_time_usage_allowed']:checked").val();
    if (multiple_time_usage_allowed == "Y")
    {
        $("#maximum_usage_limit").removeAttr("disabled", "disabled");
    }
    else
    {
        $("#maximum_usage_limit").attr("disabled", "disabled");
    }
  }
  
</script>