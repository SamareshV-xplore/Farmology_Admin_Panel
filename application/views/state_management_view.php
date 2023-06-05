<style>

    input[type=checkbox] {
        width: 23px;
        height: 23px;
    }

</style>
<div class="content-wrapper">
    <section class="content-header"><h1>States List</h1></section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="states_listing_table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Available</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($list_of_states)) {
                                foreach ($list_of_states as $i => $state_details) { ?>
                                    <tr id="<?=$state_details->id?>">
                                        <td><?=$i+1?></td>
                                        <td><?=$state_details->state?></td>
                                        <td>
                                            <?php $checked_status = (!empty($state_details->is_available) && $state_details->is_available == "Y") ? "checked" : ""; ?>
                                            <input type="checkbox" onchange="change_state_availability(this, '<?=$state_details->id?>')" <?=$checked_status?>/>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger" onclick="delete_state('<?=$state_details->id?>')">Delete</button>
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

<!-- Delete Confirmation Modal Start -->
<div id="delete_confirmation_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete_confirmation_modal" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content" style="padding:10px;">
        <form id="delete_confirmation_modal_form">
            <input type="hidden" name="state_id" id="deletable_state_id"/>
            <h3 class="text-center" style="margin-top:0;">Are you sure you want to delete this state?</h3>
            <div class="text-center">
                <button id="delete_confirmation_submit_button" class="btn btn-primary" style="margin-right:10px;">Yes</button>
                <button type="button" class="btn btn-secondary" onclick="close_delete_confirmation_modal()">No</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- Delete Confirmation Modal End -->

<!-- Toastify CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script type="text/javascript" src="<?=base_url("assets/js/toast-message.js")?>"></script>

<script>
    
$(document).ready(function(){
    $("#states_listing_table").dataTable({
        "language": {
            "emptyTable": "No States Available!"
        },
        "order": [[0,"desc"]]
    });    
});

function change_state_availability(checkbox, state_id)
{
    if (checkbox.checked == true) {
        var state_availability_status = "Y";
    } else {
        var state_availability_status = "N";
    }
    var postData = {"state_id": state_id, "state_availability_status": state_availability_status};

    $.ajax({
        url: "<?=base_url('change-state-availability')?>",
        type: "POST",
        data: postData,
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
            }
            else if (response.success == false)
            {
                toast(response.message, 1200);
                console.log(response.console_message);
            }
            else
            {
                toast("Something went wrong! Please try again later.", 1500);
                console.log(response);
            }
        }
    });
}

function delete_state(state_id)
{
    $("#deletable_state_id").val(state_id);
    $("#delete_confirmation_modal").modal("show");
}

$("#delete_confirmation_modal_form").submit(function(e)
{
    e.preventDefault();
    var form = document.getElementById("delete_confirmation_modal_form");
    var formData = new FormData(form);
    var state_id = $("#deletable_state_id").val();

    $.ajax({
        url: "<?=base_url('delete-state')?>",
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
                $("#"+state_id).remove();
                toast(response.message, 1500);
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