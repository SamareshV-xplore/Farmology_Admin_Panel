<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Add New Crop</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Crop Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('crop/add_submit') ?>" id="crop-form" enctype="multipart/form-data">
              <input type="hidden" name="crop_form" value="1">
              <div class="box-body">

                

                <div class="clearfix"></div>
                

                <div class="form-group col-md-6">
                  <label for="cate_title">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="crop_title" name="crop_title" placeholder="Crop Title" >
                </div>

                

                <div class="form-group col-md-4">
                  <label for="first_name">Image<span class="required_cls">*</span></label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                </div>   

                

                <div class="form-group col-md-6">
                  <label for="blood_group">Status</label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y">Active</option>
                    <option value="N">Inactive</option>
                  </select>
                </div>

                

              </div>
              <!-- /.box-body -->
                

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return add_crop_submit();">Create Crop</button>
                <a href="<?php echo base_url('crop'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">

    function add_crop_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var crop_title = document.getElementById("crop_title").value.trim();
      
      var image = document.getElementById("image").value.trim();
      
      var status = document.getElementById("status").value;
      
      if(crop_title == '')
      {
        $('#crop_title').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#crop_title').focus();
            focusStatus = 'Y';
        }     
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
        $("#crop-form").submit();
      }

      return false;
    }
  </script>


