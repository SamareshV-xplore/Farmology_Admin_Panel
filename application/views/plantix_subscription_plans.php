<div class="content-wrapper">
  
    <section class="content-header">
        <h3><span>Plantix Subscription Plans</span></h3>
    </section>
    
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="paid_subscription_plans_table" class="table table-striped">
                                <thead class="thead-light">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Duration</th>
                                    <th>Original Price</th>
                                    <th>Discounted Price</th>
                                    <th>Options</th>
                                </thead>
                                <tbody>
                                    <?php if (!empty($paid_subscription_plans)) {
                                    foreach ($paid_subscription_plans as $i => $plan_details) { ?>
                                        <tr id="<?=$plan_details->plan_id?>">
                                            <td><?=$i+1?></td>
                                            <td>
                                                <?=$plan_details->name?>
                                                <input type="hidden" class="name" value="<?=$plan_details->name?>"/>
                                            </td>
                                            <td>
                                                <?=$plan_details->description?>
                                                <input type="hidden" class="description" value="<?=$plan_details->description?>"/>
                                            </td>
                                            <td>
                                                <?=$plan_details->duration?> Month(s)
                                                <input type="hidden" class="duration" value="<?=$plan_details->duration?>"/>
                                            </td>
                                            <td>
                                                ₹<?=number_format($plan_details->original_price, 0)?>
                                                <input type="hidden" class="original_price" value="<?=$plan_details->original_price?>"/>
                                            </td>
                                            <td>
                                                <?php if (!empty($plan_details->discounted_price)) {
                                                    $discounted_price = $plan_details->discounted_price;
                                                    echo "₹".number_format($plan_details->discounted_price, 0);
                                                } else {
                                                    $discounted_price = NULL;
                                                    echo "<span class='text-muted font-weight-bold'>N/A</span>";
                                                }?>
                                                <input type="hidden" class="discounted_price" value="<?=$discounted_price?>"/>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary mr-2" onclick="edit_subscription_plan('<?=$plan_details->plan_id?>')">Edit</button>
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


<!-- ============================= -->
<!-- Subscription Plan Modal Start -->
<!-- ============================= -->

<div id="subscription_plan_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="subscription_plan_modal_title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="subscription_plan_modal_title" style="display: -webkit-inline-box;">Edit Plan Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="subscription_plan_modal_form">
            <input type="hidden" name="plan_id" id="plan_id"/>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="plan_description">Description</label>
                <textarea name="plan_description" id="plan_description" class="form-control" rows="6" style="resize:none;"></textarea>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="duration">Duration</label>
                        <input type="number" min="1" name="duration" id="duration" class="form-control"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="original_price">Original Price</label>
                        <input type="number" name="original_price" id="original_price" class="form-control"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="discounted_price">Discounted Price</label>
                        <input type="number" name="discounted_price" id="discounted_price" class="form-control"/>
                    </div>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="close_subscription_plan_modal()">Close</button>
        <button type="submit" form="subscription_plan_modal_form" id="subscription_plan_submit_button" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- ============================= -->
<!-- Subscription Plan Modal Start -->
<!-- ============================= -->

<script>

    $(document).ready(function(){

        $("#paid_subscription_plans_table").dataTable({
            "language": {
                "emptyTable": "No paid subscription plan available!"
            },
            "order": [[0,"desc"]]
        })

    });

    function edit_subscription_plan(plan_id)
    {
        $("#subscription_plan_modal_form")[0].reset();

        var name = $("#"+plan_id).find(".name").val();
        var description = $("#"+plan_id).find(".description").val();
        var duration = $("#"+plan_id).find(".duration").val();
        var original_price = $("#"+plan_id).find(".original_price").val();
        var discounted_price = $("#"+plan_id).find(".discounted_price").val();

        $("#plan_id").val(plan_id);
        $("#name").val(name);
        $("#plan_description").val(description);
        $("#duration").val(duration);
        $("#original_price").val(original_price);
        $("#discounted_price").val(discounted_price);

        $("#subscription_plan_modal").modal("show");
    }

    $("#subscription_plan_modal_form").submit(function(e){
        e.preventDefault();
        var form = document.getElementById("subscription_plan_modal_form");
        var formData = new FormData(form);
        var formDataObject = {};

        $.ajax({
            url: "<?=base_url('edit-subscription-plan')?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function()
            {
                $("#subscription_plan_submit_button").attr("disabled", "disabled");
                $("#subscription_plan_submit_button").text("Saving...");
            },
            complete: function()
            {
                $("#subscription_plan_submit_button").text("Save");
                $("#subscription_plan_submit_button").removeAttr("disabled");
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
                    toast(response.message, 1500);
                    location.reload();
                }
                else if (response.message == false)
                {
                    toast("Something went wrong! Please try again later.", 1500);
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

    function close_subscription_plan_modal()
    {
        $("#subscription_plan_modal").modal("hide");
    }

</script>