<style type="text/css">
    .required_cls
    {
        color: red;
    }
    .reset_btn{
        margin-top: 24px;
    }
    .high_label
    {
        font-size: 12px;
    }
    .selected_checkbox_td
    {
        width: 5%;
    }


    .container1 {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 22px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .container1 input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark1 {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border: solid 1px;
    }

    /* On mouse-over, add a grey background color */
    .container1:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container1 input:checked ~ .checkmark1 {
        background-color: #2196F3;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark1:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .container1 input:checked ~ .checkmark1:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .container1 .checkmark1:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Active Users List for Push Notification</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <form method="post" action="<?php echo base_url('push_notification/send_notification') ?>" id="push_notification_form_all">
                            <input type="hidden" name="all_users" value="all">
                            <input type="text" name="push_title" id="push_title_1" style="display: none;">
                            <textarea name="push_message" id="the_form_push_message_1" class="form-control" placeholder="Enter your message" rows="6" style="display: none;"></textarea>
                            <input type="text" name="redirect_id" id="redirect_id_1" style="display: none;">
                            <input type="text" name="type" id="type_1" style="display: none;">
                            <div class="form-group col-md-4 pull-left">
                                <button
                                    onclick="push_notification_form_type(this)"
                                    data-id="1"
                                    data-toggle="modal" data-target="#modal-message"
                                    type="button"
                                    class="btn btn-block btn-primary reset_btn">
                                    Click Here to Send Push Notification to All Users
                                </button>
                            </div>
                        </form>
                        <form method="post" action="<?php echo base_url('push_notification/send_notification') ?>" id="push_notification_form">
                            <input type="text" name="push_title" id="push_title_2" style="display: none;">
                            <textarea name="push_message" id="the_form_push_message_2" class="form-control" placeholder="Enter your message" rows="6" style="display: none;"></textarea>
                            <input type="text" name="redirect_id" id="redirect_id_2" style="display: none;">
                            <input type="text" name="type" id="type_2" style="display: none;">
                            <div class="form-group col-md-3 pull-right">
                                <button
                                    onclick="submit_push_notification(this)"
                                    data-id="2"
                                    type="button"
                                    class="btn btn-block btn-primary reset_btn">
                                    Click Here to Send Push Notification
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <!--<h3 class="box-title">Data Table With Full Features</h3>-->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="display: none;">Sl. No</th>
                                <th class="selected_checkbox_td">Select Users</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Device Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($users_list) > 0)
                            {
                                $slno = 1;
                                foreach($users_list as $list)
                                {

                                    ?>
                                    <tr>
                                        <td  style="display: none;"><?php echo $slno; ?></td>
                                        <td>
                                            <label class="container1">
                                                <input type="checkbox" value="<?php echo $list['id'] ?>" onclick="getCheckboxData(this)">
                                                <span class="checkmark1"></span>
                                            </label>
                                        </td>
                                        <td><?php echo $list['first_name'].' '.$list['last_name']; ?></td>
                                        <td><?php echo $list['email']; ?></td>
                                        <td><?php echo $list['phone']; ?></td>
                                        <td>
                                            <?php
                                            if($list['device_type'] == 'A')
                                            {
                                                echo "<label><b>Android<b></label>";
                                            }
                                            else
                                            {
                                                echo "<label><b>iOS<b></label>";
                                            }
                                            ?>
                                        </td>

                                    </tr>
                                    <?php
                                    $slno++;
                                }
                            }
                            ?>
                            </tbody>

                        </table>
                    </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<div class="modal fade" id="modal-message">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enter the push message</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">    
                    <div class="row">
                      <div class="col-md-4">
                            <div class="form-group">
                              <label for="type">Type</label>
                              <select name="type" id="type" onchange="return show_type_list(this.value);" class="form-control">
                                <option value="0">None</option>
                                <option value="1">Video</option>
                                <option value="2">Blog</option>    
                              </select>
                            </div>  
                      </div>
                      <div class="col-md-4 ml-auto video" style="display: none;">
                            <div class="form-group">
                              <label for="video">Video list</label>
                              <select name="video" id="video" class="form-control">
                                <option value="">Select one</option>
                                <?php
                                    if(count($video_list) > 0)
                                    {
                                      foreach($video_list as $parent_row)
                                      {
                                        ?>
                                        <option value="<?php echo $parent_row['id']; ?>"><?php echo $parent_row['title']; ?></option>
                                        <?php
                                      }
                                    }
                                ?>   
                              </select>
                            </div>
                      </div>
                      <div class="col-md-4 ml-auto blog" style="display: none;">
                          <div class="form-group">
                              <label for="blog">Blog List</label>
                              <select name="blog" id="blog" class="form-control">
                                <option value="">Select one</option>
                                <?php
                                    if(count($blog_list) > 0)
                                    {
                                      foreach($blog_list as $row)
                                      {
                                        ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?></option>
                                        <?php
                                      }
                                    }
                                ?>   
                              </select>
                            </div>
                      </div>
                    </div>
                </div>    

                <div class="clearfix"></div>

                <label for="last_name">Title<span class="required_cls">*</span></label>
                <input type="text" name="push_title" id="push_title"   class="form-control" maxlength="50">
                <label for="last_name">Message<span class="required_cls">*</span></label>
                <input type="hidden" name="form_type" id="form_type" value="">
                <textarea name="push_message" id="push_message" class="form-control" placeholder="Enter your message" rows="6" ></textarea>
                <span id="zip_err" style="color: red;"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="validate_push_message();">Send Push Notification</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<!-- /.content-wrapper -->
<script type="text/javascript">
    function submit_push_notification(data) {
        if ($("#example2 input:checkbox:checked").length > 0)
        {
            $("#form_type").val(data.getAttribute("data-id"));
            $('#modal-message').modal('show');
        }
        else
        {
            alert('Please select at least one user to send notification.');
        }
    }

    function show_type_list(val)
    {
        
        if(val == '1'){
            //alert(val);
            $('.video').show();
            $('.blog').hide();

        }else if(val == '2')
        {
            $('.blog').show();
            $('.video').hide();
        }else{
            $('.video').hide();
            $('.blog').hide();
        }
    }

    function push_notification_form_type(data) {
        $("#form_type").val(data.getAttribute("data-id"));
    }

    function getCheckboxData(data) {
        if($(data).prop('checked')){
            $('#push_notification_form').append('<input type="checkbox" name="user_id_for_push[]" value="'+data.value+'" id=the_form_value_'+data.value+' style="display: none;" checked />');
        }else{
            if($('#the_form_value_'+data.value).val()){
                $('#the_form_value_'+data.value).remove();
            }
        }
    }

    function validate_push_message()
    {
        $('.form-control').removeClass('error_cls');
        var focusStatus = "N";

        var push_title = document.getElementById("push_title").value.trim();
        var push_message = document.getElementById("push_message").value.trim();
        var form_type_data_value = document.getElementById("form_type").value.trim();
        var type = document.getElementById("type").value.trim();
        var video = document.getElementById("video").value.trim();
        var blog = document.getElementById("blog").value.trim();
        

        if(type == 1 && video.length == 0)
        {
           
            $('#video').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#video').focus();
                focusStatus = 'Y';
            }
                
        }

        if(type == 2 && blog.length == 0)
        {
           
            $('#blog').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#blog').focus();
                focusStatus = 'Y';
            }
                
        }

        if(push_title == '')
        {
            $('#push_title').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#push_title').focus();
                focusStatus = 'Y';
            }
        }

        if(push_message == '')
        {
            $('#push_message').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#push_message').focus();
                focusStatus = 'Y';
            }
        }

        if(focusStatus == "N")
        {
            if(form_type_data_value == 1){
                $('#the_form_push_message_'+form_type_data_value).val(push_message);
                $('#push_title_'+form_type_data_value).val(push_title);
                if(type == 1){
                    $('#redirect_id_'+form_type_data_value).val(video);
                }else if(type == 2)
                {
                    $('#redirect_id_'+form_type_data_value).val(blog);
                }
                $('#type_'+form_type_data_value).val(type);
                $("#push_notification_form_all").submit();
            }else if(form_type_data_value == 2){
                $('#the_form_push_message_'+form_type_data_value).val(push_message);
                $('#push_title_'+form_type_data_value).val(push_title);
                if(type == 1){
                    $('#redirect_id_'+form_type_data_value).val(video);
                }else if(type == 2)
                {
                    $('#redirect_id_'+form_type_data_value).val(blog);
                }
                $('#type_'+form_type_data_value).val(type);
                $("#push_notification_form").submit();
            }

        }
    }
</script>
