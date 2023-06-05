 <?php 
  $time_after_one_hour = strtotime("+1 Hour", time());
  $date = date("Y-m-d", $time_after_one_hour);
  $time = date("H:i:s", $time_after_one_hour);
  $min_notification_send_datetime = $date."T".$time;
 ?>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
  .border-search {
      border: #bbb 1px solid;
      padding-right: 10px;
      width: 200px;
      display: contents;
  }

  .sttng_icon {
      font-size: 26px;
      margin-right: 20px;
  }

  .all_navbar_main {
      float: unset!important;
      display: inline-block;
      border: #bbb 1px solid;
      background: #fff;
      padding: 7px;
  }

  .sttng_icon_div {
    text-align: center;
    display: inline-block;
  }

  .inline_main_div {
    text-align: right;
    margin-top: 10px;
  }

  .search_boxex {
    border: none;
  }

  .notification_div {
      text-align: right;
      margin-top: 10px;
  }

  .BlockUIConfirm {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100vh;
    width: 100vw;
    z-index: 50;
  }

  .blockui-mask {
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: #333;
    opacity: 0.4;
  }

  .RowDialogBody {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    max-width: 450px;
    opacity: 1;
    background-color: white;
    border-radius: 4px;
  }

  .RowDialogBody > div:not(.confirm-body) {
    padding: 8px 10px;
  }

  .confirm-header {
    width: 100%;
    border-radius: 4px 4px 0 0;
    font-size: 13pt;
    font-weight: bold;
    margin: 0;
  }

  .row-dialog-hdr-success {
    /*border-top: 4px solid #5cb85c;*/
    border-bottom: 1px solid transparent;
  }

  .row-dialog-hdr-info {
    border-top: 4px solid #5bc0de;
    border-bottom: 1px solid transparent;
  }

  .confirm-body {
    /*border-top: 1px solid #ccc;*/
    padding:20px 10px;
    border-bottom: 1px solid #ccc;
  }

  .confirm-btn-panel {
    width: 100%;
  }
  .row-dialog-btn {
    cursor: pointer;
  }

  .btn-naked {
    background-color: transparent;
  }

  .all_input_shadow {
      border: none;
      box-shadow: 1px 1px 6px 0px #bbb;
  }

  .box_image {
      border: #bbb 1px solid;
      height: 300px;
      position: relative;
  }

  .main_picture_div {
      width: 50%;
      margin: 0 auto;
  }

  .picture_labl{
      position: absolute;
      top: 45%;
      left: 45%;
  }

  .fileUpload {
      position: relative;
      overflow: hidden;
      margin: 10px;
      background: #000;
      color: #fff;
  }

  .fileUpload:hover {
      color: #fff;
  }

  .fileUpload input.upload {
      position: absolute;
      top: 0;
      right: 0;
      margin: 0;
      padding: 0;
      font-size: 20px;
      cursor: pointer;
      opacity: 0;
      filter: alpha(opacity=0);
  }

  #uploadFile {
      font-size: 12px;
  }

  .border_search{
      border: #bbb 1px solid;
      background: #fff;
      padding-right: 10px;
  }

  .add_notif_btn {
      width: 100%;
      margin-top: 10px;
      margin-bottom: 10px;
      padding: 10px;
  }

  .input_text {
      padding: 24px 10px;
  }

  #remove_pop {
    float: right;
  }

  .search_main_divs {
    margin-bottom: 20px;
  }

  th {
    text-align: center;
  }

  td {
    text-align: center;
  }

  .cross_icons {
      margin-left: 10px;
      color: #fff;
      font-size: 10px;
      background: #f92626a8;
      padding: 5px 7px;
      border-radius: 20px;
  }

  .edit_icons {
      color: #bbb;
      font-size: 18px;
  }

  .modal_custom_title {
      display: -webkit-inline-box;
  }

  .header-with-button {
    display: flex;
    flex-flow: row;
    align-items: center;
    justify-content: space-between;
  }

  .image-in-row {
    width: auto;
    height: 50px;
    margin: 5px;
    border-radius: 3px;
  }

  .btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .btn-success:disabled {
    background-color: #5cb85c;
  }

  .btn-success:hover {
    border-color: #4cae4c;
  }
</style>

<!-- Bootstrap Modal Start  -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div id="modal_header" class="modal-header">
        <h4 id="modal_title" class="modal-title modal_custom_title" id="exampleModalLabel">Schedule Notification Push</h4>
        <button type="button" class="close" onclick="reset_and_close_modal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modal_body" class="modal-body">
        <div id="alert_container"></div>
        <form id="modal_form" onsubmit="add_notification(event)">

          <div class="form-group">
            <input type="hidden" id="hash_id" name="hash_id"/>
          </div>

          <div class="form-group">
            <input type="text" id="title" name="notification_title" class="form-control" placeholder="Notification Title" required>
          </div>

          <div class="form-group">
            <textarea id="desc" name="notification_desc" rows="6" class="form-control" placeholder="Notification Description" style="resize:none;" required></textarea>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <select id="redirect_to" name="notification_redirect_to" class="form-control" required>
                  <option value="">Notification Redirect To</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <select id="target_state" name="notification_target_state" class="form-control">
                  <option value="">Notification Target State</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <button type="button" class="btn btn-primary mr-auto mb-1">
                  <label for="image" style="margin:0;">Upload Image</label>
                  <input type="file" accept="image/png, image/jpeg" id="image" name="notification_image" onchange="checkFileValidation()" style="display:none;"/>
                </button>
                <div id="image_alert_container" class="text-muted">Less then 50KB (JPG/PNG Supported)</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="datetime-local" id="send_date" name="notification_send_date" min="<?=$time_after_one_hour?>" class="form-control" required>
                <div class="text-muted mt-1">Can choose future date time only</div>
              </div>
            </div>
          </div>
        </form>
        <div class="form-group">
          <button form="modal_form" id="modal_form_submit_button" class="btn btn-success form-control" disabled="disabled">Add Notification</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap Modal End -->

<div class="content-wrapper">
  <section class="content-header header-with-button">
    <h3>Scheduled Push Notifications</h3>
    <button type="button" class="btn btn-success" onclick="show_add_notification_modal()">Add Notification</button>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="table-responsive">
              <table id="notification_listing_table" class="table table-striped vertical-align-middle display">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                        <th scope="col">Image</th>
                        <th scope="col">Redirect</th>
                        <th scope="col" width="100">Target State</th>
                        <th scope="col">Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if (!empty($notifications_list)) {
                  foreach ($notifications_list as $i => $notification) { ?>
                    <tr id="<?=$notification->hash_id?>">
                      <td><?=$i+1?></td>
                      <td class="text-left"><?=$notification->title?></td>
                      <td class="text-left"><?=$notification->description?></td>
                      <td><img src="<?=$notification->image?>" class="image-in-row"/></td>
                      <td>
                        <span><?=$notification->redirection_name?></span>
                        <span style="display:none;"><?=$notification->redirect_to?></span>
                      </td>
                      <td>
                        <span><?=(!empty($notification->state_name)) ? $notification->state_name : "All"?></span>
                        <span style="display:none;"><?=$notification->target_state?></span>        
                      </td>
                      <td><?=$notification->send_date?></td>
                      <td>
                      <?php if ($notification->status == "P") {
                        echo "Pending";
                      } elseif ($notification->status == "C") {
                        echo "Complete";
                      } else {
                        echo "Unknown";
                      }?>
                      </td>
                      <td>
                        <a href="javascript:show_edit_notification_modal('<?=$notification->hash_id?>')"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="javascript:show_delete_notification_modal('<?=$notification->hash_id?>')"><i class="fa-solid fa-xmark cross_icons"></i></a>
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

<!-- Bootstrap Small Modal Start -->
<div id="delete_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div id="delete_modal_header" class="modal-header">
        <h4 id="delete_modal_title" class="modal-title modal_custom_title" id="deleteModalTitle">Delete Confirmation</h4>
        <button type="button" class="close ml-auto" onclick="close_delete_modal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="delete_modal_body" class="modal-body">
        <form id="delete_modal_form" onsubmit="delete_notification(event)">
          <h4 class="m-0 p-0">Are you sure you want to delete this notification?</h4>
          <input type="hidden" id="deleteable_hash_id" name="hash_id"/>
        </form>
      </div>
      <div class="modal-footer">
        <button form="delete_modal_form" class="btn btn-success">Yes</button>  
        <button type="button" class="btn btn-danger" onclick="close_delete_modal()">No</button>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap Small Modal End -->

<script>

  var min_datetime_local = "<?=$min_notification_send_datetime?>";
  var send_datetime_input = document.getElementById("send_date");
  send_datetime_input.min = min_datetime_local;
  send_datetime_input.value = min_datetime_local;
  var notification_data_table = {};

  function GUID ()
  {  
    function s4 ()
    {  
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);  
    }  
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();  
  }

  function dateFormat(inputDate, format)
  {
      //parse the input date
      const date = new Date(inputDate);

      //extract the parts of the date
      const day = date.getDate();
      const month = date.getMonth() + 1;
      const year = date.getFullYear();    

      //replace the month
      format = format.replace("MM", month.toString().padStart(2,"0"));        

      //replace the year
      if (format.indexOf("yyyy") > -1) {
          format = format.replace("yyyy", year.toString());
      } else if (format.indexOf("yy") > -1) {
          format = format.replace("yy", year.toString().substr(2,2));
      }

      //replace the day
      format = format.replace("dd", day.toString().padStart(2,"0"));

      return format;
  }

  function checkFileValidation ()
  {
    const fi = document.getElementById('image');
    // Check if any file is selected.
    if (fi.files.length > 0) {
        for (const i = 0; i <= fi.files.length - 1; i++) {

            const fsize = fi.files.item(i).size;
            const file = Math.round((fsize / 1024));
            // The size of the file.
            if (file >= 50) {
                FileValidationAlert("text-danger","File size is too big! please select a file less than 50KB.");
                changeSubmitButtonState("disabled");
            } else {
                FileValidationAlert("text-info","File size is "+file+"KB.");
                changeSubmitButtonState("enabled");
            }
        }
    }
  }

  function FileValidationAlert (type, message)
  {
    $("#image_alert_container").attr("class", type);
    $("#image_alert_container").text(message);
  }
  
  function changeSubmitButtonState (state)
  {
    if (state == "disabled")
    {
      $("#modal_form_submit_button").attr("disabled","disabled");
    }
    else if (state == "enabled")
    {
      $("#modal_form_submit_button").removeAttr("disabled");
    }
  }

  function reset_and_close_modal ()
  {
    $("#modal_form")[0].reset();
    send_datetime_input.value = min_datetime_local;
    $("#hash_id").val();
    $("#modal_form").attr("onsubmit", "add_notification(event)");
    $("#redirect_to option:selected").removeAttr("selected");
    $("#target_state option:selected").removeAttr("selected");
    $("#modal_form_submit_button").removeClass("btn-primary");
    $("#modal_form_submit_button").addClass("btn-success");
    $("#modal_form_submit_button").attr("disabled","disabled");
    FileValidationAlert("text-muted","Less then 50KB (JPG/PNG Supported)");
    $("#modal_form_submit_button").text("Add Notification");
    $("#modal").modal("hide");
  }

  function show_add_notification_modal ()
  {
    $("#modal").modal("show");
    $("#modal_form_submit_button").attr("disabled","disabled");
  }

  function add_notification (e)
  {
    e.preventDefault();
    var form = document.getElementById("modal_form");
    var postData = new FormData(form);
    $.ajax({
      url: "<?=base_url('schedule_push_notification/add')?>",
      type: "POST",
      data: postData,
      contentType: false,
      processData: false,
      error: function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function (data)
      {
        if (data.success)
        {
          // get_notifications_list();
          
          $("#modal_form")[0].reset();
          send_datetime_input.value = min_datetime_local;
          show_modal_alert("success", data.message);
          setTimeout(() => {
            reset_and_close_modal();
          }, 2000);

          location.reload();

          // setTimeout(() => {
          //   $("#modal_alert").fadeOut();
          // }, 5000);
        }
        else
        {
          show_modal_alert("failed", data.message);
          setTimeout(() => {
            $("#modal_alert").fadeOut();
          }, 5000);
        }
      }
    })
  }

  function show_edit_notification_modal (row_id)
  {
    var row = $("#"+row_id);
    var cols = row.children();
    console.log(cols);
    $("#hash_id").val(row_id);
    $("#title").val(cols[1].outerText);
    $("#desc").val(cols[2].outerText);
    var redirect_to_value = cols[4].childNodes[3].outerText;
    var target_state_value = cols[5].childNodes[3].outerText;
    $(`#redirect_to option[value="${redirect_to_value}"]`).attr("selected","selected");
    $(`#target_state option[value="${target_state_value}"]`).attr("selected","selected");
    $("#modal_form").attr("onsubmit", "edit_notification(event)");
    $("#modal_form_submit_button").removeClass("btn-success");
    $("#modal_form_submit_button").addClass("btn-primary");
    $("#modal_form_submit_button").removeAttr("disabled");
    $("#modal_form_submit_button").text("Edit Notification");

    $("#modal").modal("show");
  }
  
  function edit_notification (e)
  {
    e.preventDefault();
    var form = document.getElementById("modal_form");
    var postData = new FormData(form);
    $.ajax({
      url: "<?=base_url('schedule_push_notification/edit')?>",
      type: "POST",
      data: postData,
      contentType: false,
      processData: false,
      error: function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function (data)
      {
        if (data.success)
        {
          // get_notifications_list();

          $("#modal_form")[0].reset();
          send_datetime_input.value = min_datetime_local;
          show_modal_alert("success", data.message);
          setTimeout(() => {
            reset_and_close_modal();
          }, 2000);

          location.reload();

          // setTimeout(() => {
          //   $("#modal_alert").fadeOut();
          // }, 5000);
        }
        else
        {
          show_modal_alert("failed", data.message);
          setTimeout(() => {
            $("#modal_alert").fadeOut();
          }, 5000);
        }
      }
    })
  }

  function show_delete_notification_modal (row_id)
  {
    $("#deleteable_hash_id").val(row_id);
    $("#delete_modal").modal("show");
  }

  function delete_notification (e)
  {
    e.preventDefault();
    var hash_id = document.getElementById("deleteable_hash_id").value;
    var postData = {"hash_id": hash_id};
    $.ajax({
      url: "<?=base_url('schedule_push_notification/delete')?>",
      type: "POST",
      data: postData,
      error: function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function (data)
      {
        if (data.success)
        {
          close_delete_modal();
          get_notifications_list();
        }
      }
    })
  }

  function close_delete_modal ()
  {
    $("#deleteable_hash_id").val("");
    $("#delete_modal").modal("hide");
  }

  function render_notifications_list (notifications_list)
  {
    var list = "";
    notifications_list.forEach(function(notification, i){
      
      if (notification.state_name) {
        var state_name = notification.state_name;
      }
      else
      {
        var state_name = "All";
      }

      var row = `<tr id="${notification.hash_id}"><td class="text-left">${i+1}</td>`;
      row += `<td class="text-left">${notification.title}</td>`;
      row += `<td class="text-left">${notification.description}</td>`;
      row += `<td><img src="${notification.image}" class="image-in-row"/></td>`;
      row += `<td>
                <span>${notification.redirection_name}</span>
                <span style="display:none;">${notification.redirect_to}</span>
              </td>`;
      row += `<td>
                <span>${state_name}</span>
                <span style="display:none;">${notification.target_state}</span>        
              </td>`;
      row += `<td>${notification.send_date}</td>`;
      if (notification.status == "P")
      {
        row += `<td>Pending</td>`;
      }
      else if (notification.status == "C")
      {
        row += `<td>Complete</td>`;
      }
      row += `<td>
                <a href="javascript:show_edit_notification_modal('${notification.hash_id}')"><i class="fa-solid fa-pen-to-square"></i></a>
                <a href="javascript:show_delete_notification_modal('${notification.hash_id}')"><i class="fa-solid fa-xmark cross_icons"></i></a>
              </td>`;
      list += row;
    });
    $("#notification_listing_table tbody").html(list);
  }

  function get_notifications_list ()
  {
    $.get("<?=base_url('schedule_push_notification/get')?>", function(data){
      if (data.success) {
        if (data.list)
        {
          render_notifications_list(data.list);
        }
        else
        {
          location.reload();
        }
      }
    });
  }

  function render_redirect_to_options (options)
  {
    var html = `<option value="">Notification Redirect To</option>`;
    options.forEach(function(option){
      html += `<option value="${option.value}">${option.name}</option>`;
    });
    $("#redirect_to").html(html);
  }

  function get_redirect_to_options ()
  {
    $.get("<?=base_url('schedule_push_notification/get_app_redirection_options')?>", function(data){
      if (data.success)
      {
        render_redirect_to_options(data.options);
      }
    });
  }

  get_redirect_to_options();

  function render_target_state_options (options)
  {
    var html = `<option value="">Notification Target State</option>
                <option value="">All</option>`;
    options.forEach(function(option){
      html += `<option value="${option.id}">${option.state}</option>`;
    });
    $("#target_state").html(html);
  }

  function get_target_state_options ()
  {
    $.get("<?=base_url('schedule_push_notification/get_target_state_options')?>", function(data){
      if (data.success)
      {
        render_target_state_options(data.options);
      }
    });
  }

  get_target_state_options();

  function show_modal_alert (type, message)
  {
    var alert = `<div id="modal_alert" class="alert alert-info alert-dismissible">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  ${message}
                </div>`;

    if (type == "success")
    {
      alert = `<div id="modal_alert" class="alert alert-success alert-dismissible">
                <a class="close" data-dismiss="alert" aria-label="close">&times;</a>
                ${message}
              </div>`;
    }
    else if (type == "failed")
    {
      alert = `<div id="modal_alert" class="alert alert-danger alert-dismissible">
                <a class="close" data-dismiss="alert" aria-label="close">&times;</a>
                ${message}
              </div>`;
    }

    $("#alert_container").html(alert);
  }

  $(document).ready(function(){

    $('#notification_listing_table').dataTable({
      "language": {
        "emptyTable": "No scheduled push notification is available"
      },
      "order": [[0,'desc']],
    });

  });
</script>