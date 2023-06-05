<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Add New Meta Data</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Meta Data Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('meta_data/add_submit') ?>" id="meta-form" enctype="multipart/form-data">
              <input type="hidden" name="meta_data_form" value="1">
              <div class="box-body">
                

                <div class="form-group col-md-6">
                  <label for="first_name">Page Name<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="page_name" name="page_name" placeholder="Page Name" >
                </div>
                  <div class="form-group col-md-6">
                      <label for="first_name">Meta Title<span class="required_cls">*</span></label>
                      <textarea name="meta_title" id="meta_title" class="form-control" placeholder="Enter Meta Title" rows="6"></textarea>
                  </div>

                <div class="form-group col-md-6">
                  <label for="last_name" >Description<span class="required_cls">*</span></label>
                  <textarea name="meta_description" id="meta_description" class="form-control" placeholder="Enter Meta description" rows="6"></textarea>
                </div>

                <div class="form-group col-md-6">
                  <label for="employee_id">Meta Keywords<span class="required_cls">*</span></label>
                    <textarea name="meta_keyword" id="meta_keyword" class="form-control" placeholder="Enter Meta Keywords" rows="6"></textarea>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return add_meta_data_submit();">Create Meta Data</button>
                <a href="<?php echo base_url('meta-list'); ?>"><button type="button" class="btn btn-primary pull-left">Cancel</button></a>
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

    function add_meta_data_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var page_name = document.getElementById("page_name").value.trim();
      var meta_title = document.getElementById("meta_title").value.trim();
      var meta_description = document.getElementById("meta_description").value.trim();
      var meta_keyword = document.getElementById("meta_keyword").value.trim();

      if(page_name == '')
      {
        $('#page_name').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#page_name').focus();
            focusStatus = 'Y';
        }     
      }

        if(meta_title == '')
        {
            $('#meta_title').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#meta_title').focus();
                focusStatus = 'Y';
            }
        }

      if(meta_description == '')
      {
        $('#meta_description').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#meta_description').focus();
            focusStatus = 'Y';
        }     
      }

      if(meta_keyword == '')
      {
        $('#meta_keyword').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#meta_keyword').focus();
            focusStatus = 'Y';
        }     
      }

      if(focusStatus == "N")
      {
        // no validation error.. now submit the form
        $("#meta-form").submit();
      }

      return false;
    }
  </script>


