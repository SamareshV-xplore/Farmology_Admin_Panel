<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Add New Banner</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Banner Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('banner/add_submit') ?>" id="banner-form" enctype="multipart/form-data">
              <input type="hidden" name="banner_form" value="1">
              <div class="box-body">
                

                <div class="form-group col-md-12">
                  <label for="first_name">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="title" name="title" placeholder="Title" >
                </div>

                <div class="form-group col-md-12">
                  <label for="last_name" >Description<span class="required_cls">*</span></label>
                  <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group col-md-6">
                  <label for="first_name">Image<span class="required_cls">*</span></label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                </div>

                <div class="form-group col-md-6">
                  <label for="employee_id">Hyperlink URL</label>
                  <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="50">
                </div>

                <div class="form-group col-md-6">
                  <label for="redirect_to">Redirect to</label>
                  <select class="form-control" name="redirect_to" id="redirect_to">
                  <?php if (!empty($app_redirections_list)) { ?>
                  <option value="">Please choose an app redirection option</option>
                  <?php foreach ($app_redirections_list as $i => $redirection_details) { ?>
                    <option value="<?=$redirection_details->value?>"><?=$redirection_details->name?></option>
                  <?php }} else { ?>
                    <option value="">No app redirection options found</option>
                  <?php } ?>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label for="blood_group">Status<span class="required_cls">*</span></label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y">Active</option>
                    <option value="N">Inactive</option>
                  </select>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return add_banner_submit();">Create Banner</button>
                <a href="<?php echo base_url('banner-list'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">
    // date check start
    function date_check(date_is)
    {
      return moment(date_is, 'DD/MM/YYYY',true).isValid();
    }
    // date check end

    function add_banner_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var title = document.getElementById("title").value.trim();
      var description = CKEDITOR.instances.description.getData();
      var image = document.getElementById("image").value.trim();
      var link = document.getElementById("link").value;
      var status = document.getElementById("status").value;
      
      if(title == '')
      {
        $('#title').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#title').focus();
            focusStatus = 'Y';
        }     
      }

      if(description == '')
      {
        $('#cke_description').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#description').focus();
            focusStatus = 'Y';
        }     
      }
      else
      {
        $('#cke_description').removeClass('error_cls');
      }

      if(image == '')
      {
        $('#image').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#image').focus();
            focusStatus = 'Y';
        }     
      }

      if(status == '')
      {
        $('#status').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#status').focus();
            focusStatus = 'Y';
        }     
      }

      if(focusStatus == "N")
      {
        // no validation error.. now submit the form
        $("#banner-form").submit();
      }

      return false;
    }
  </script>


