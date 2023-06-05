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
        font-size: 20px;
        font-weight: 600;
    }

</style>

<div class="content-wrapper">
<section class="content-header">
    <h1>Help and FAQ Management</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Help and Support Details</h3>
                </div>
                <?php if (!empty($support_details)) {
                    $help_description = (!empty($support_details["help_description"])) ? $support_details["help_description"] : "";
                    $email = (!empty($support_details["email"])) ? $support_details["email"] : "";
                    $contact_number = (!empty($support_details["contact_number"])) ? $support_details["contact_number"] : "";
                    $whatsapp_number = (!empty($support_details["whatsapp_number"])) ? $support_details["whatsapp_number"] : "";
                }?>
                <form id="help_and_support_details_form">
                    <div class="box-body">
                        <div class="form-group col-md-12">
                            <label for="help_description">Help Description</label>
                            <textarea name="help_description" id="help_description" class="form-control" style="height:200px; resize:none;" required><?=$help_description?></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?=$email?>" required/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="contact_number">Contact Number</label>
                            <input type="tel" name="contact_number" id="contact_number" class="form-control" value="<?=$contact_number?>" required/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="whatsapp_number">Whatsapp Number</label>
                            <input type="tel" name="whatsapp_number" id="whatsapp_number" class="form-control" value="<?=$whatsapp_number?>" required/>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button id="help_and_support_details_form_submit_button" class="btn btn-primary" style="float:right;">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title" style="font-size:20px; font-weight:600;">List of FAQs</h3>
                    <button class="btn btn-success" style="float:right;" onclick="add_faq()">Add New FAQ</button>
                </div>
                <div class="box-body">
                    <table id="faq_listing_table" class="table table-responsive">
                        <thead>
                            <tr>
                                <th style="width:5%">No.</th>
                                <th style="width:30%">Question</th>
                                <th style="width:50%">Answer</th>
                                <th style="width:15%">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($list_of_FAQ)) {
                        $total_rows = count($list_of_FAQ);
                        foreach ($list_of_FAQ as $i => $faq_details) { ?>
                            <tr id="<?=$faq_details->hash_id?>">
                                <td style="width:5%"><?=$total_rows-$i?></td>
                                <td class="question" style="width:20%"><?=$faq_details->question?></td>
                                <td class="answer" style="width:60%"><?=$faq_details->answer?></td>
                                <td class="d-flex align-items-center" style="width:15%">
                                    <button type="button" class="btn btn-primary" style="margin-right:8px;" onclick="edit_faq('<?=$faq_details->hash_id?>')"><i class="fa fa-edit"></i></button>
                                    <button type="button" class="btn btn-danger" onclick="delete_faq('<?=$faq_details->hash_id?>')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php }} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<!-- =============== -->
<!-- FAQ Modal Start -->
<!-- =============== -->
<div id="faq_modal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="faq_modal_title" class="modal-title">Add FAQ</h5>
      </div>
      <div class="modal-body" style="padding:10px;">
      <form id="faq_modal_form">
         <input type="hidden" id="faq_modal_form_type" value="Add"/>
         <input type="hidden" name="hash_id" id="faq_id"/>
        <div class="form-group">
          <label for="question">Question</label>
          <input type="text" name="question" id="question" class="form-control" required/>
        </div>
        <div class="form-group">
          <label for="answer">Answer</label>
          <textarea name="answer" id="answer" class="form-control" style="height:200px; resize:none;" required></textarea>
        </div>
        <div style="display:flex; flex-flow:row; justify-content:end;">
          <button id="faq_modal_form_submit_button" class="btn btn-primary" style="margin-right:8px;">Add</button>
          <button type="button" class="btn btn-secondary" onclick="close_faq_modal()">Close</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
<!-- ============= -->
<!-- FAQ Modal End -->
<!-- ============= -->

<!-- =============================== -->
<!-- Delete Confirmation Modal Start -->
<!-- =============================== -->
<div id="delete_confirmation_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body" style="padding:10px;">
      <form id="delete_confirmation_modal_form">
        <input type="hidden" name="hash_id" id="deletable_faq_id"/>
        <h4 class="text-center">Are you sure you want to delete this FAQ?</h4>
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

    $("#faq_listing_table").dataTable({
        "language": {
            "emptyTable": "No Frequently Asked Question Added Yet."
        },
        "order": [[0, "desc"]]
    });

});

$("#help_and_support_details_form").submit(function(e){
    e.preventDefault();
    var form = document.getElementById("help_and_support_details_form");
    var formData = new FormData(form);
    var submit_button = $("#help_and_support_details_form_submit_button");

    $.ajax({
        url: "<?=base_url("update-help-and-support-details")?>",
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

function add_faq()
{
    $("#faq_modal_title").text("Add FAQ");
    $("#faq_modal_form_submit_button").text("Add");
    $("#faq_modal_form_type").val("Add");
    $("#faq_modal").modal("show");
}

$("#faq_modal_form").submit(function(e){
    e.preventDefault();
    var form = document.getElementById("faq_modal_form");
    var formData = new FormData(form);
    var submit_button = $("#faq_modal_form_submit_button");
    var plain_text = $("#faq_modal_form_type").val();
    var loading_text = plain_text+"ing...";

    $.ajax({
        url: "<?=base_url("add-faq")?>",
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
            if (response.success == true)
            {
                toast(response.message, 1200);
                close_faq_modal();
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

function close_faq_modal()
{
    $("#faq_modal").modal("hide");
    $("#faq_modal_form")[0].reset();
}

function edit_faq(faq_id)
{
    $("#faq_modal_title").text("Edit FAQ");
    $("#faq_modal_form_submit_button").text("Save");
    $("#faq_modal_form_type").val("Save");
    
    var row = $("#"+faq_id);
    var question = row.find(".question").text();
    var answer = row.find(".answer").text();

    $("#faq_id").val(faq_id);
    $("#question").val(question);
    $("#answer").val(answer);

    $("#faq_modal").modal("show");
}

function delete_faq(faq_id)
{
    $("#deletable_faq_id").val(faq_id);
    $("#delete_confirmation_modal").modal("show");
}

$("#delete_confirmation_modal_form").submit(function(e){
    e.preventDefault();
    var form = document.getElementById("delete_confirmation_modal_form");
    var formData = new FormData(form);
    var deletable_faq_id = $("#deletable_faq_id").val();

    $.ajax({
        url: "<?=base_url("delete-faq")?>",
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
                $("#"+deletable_faq_id).remove();
                toast(response.message, 1200);
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