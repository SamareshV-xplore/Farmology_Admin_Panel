<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Edit Page Content</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Page Content Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('page_content/edit_submit') ?>" id="page-content-form" enctype="multipart/form-data">
              
              <div class="box-body">

                <div class="form-group col-md-6">
                  <label for="page">Page</label>
                  <select class="form-control" name="page" id="page" disabled="disabled" >
                    <?php
                    if(count($page_list) > 0)
                    {
                      foreach($page_list as $page_row)
                      {
                        ?>
                        <option value="<?php echo $page_row['id']; ?>" <?php if($page_content_details['page_details']['id'] == $page_row['id']) { ?> selected="selected" <?php } ?> ><?php echo $page_row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                    
                  </select>
                </div>
                <?php
                    if(count($page_list) > 0)
                    {
                      foreach($page_list as $page_row)
                      {
                         if($page_content_details['page_details']['id'] == $page_row['id']) {
                        ?>
                        <input type="hidden" name="page_id" value="<?php echo $page_row['id']; ?>">
                        <?php
                         }
                      }
                    }
                    ?>


                <input type="hidden" name="page_content_id" value="<?php echo $page_content_details['id']; ?>">
               
                <div class="form-group col-md-6">
                  <label for="first_name">Section Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="title" name="title" placeholder="Title"  value="<?php echo $page_content_details['title']; ?>">
                </div>

                <div class="form-group col-md-12">
                  <label for="last_name" >Description<span class="required_cls">*</span></label>
                  <textarea name="description" id="description" class="form-control"><?php echo $page_content_details['page_content']; ?></textarea>
                </div>               

                <div class="form-group col-md-6">
                  <label for="first_name">Image (If any)</label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                </div>

                <?php 
                if($page_content_details['image'] != '')
                {
                  ?>
                  <div class="form-group col-md-6">
                    <label for="">Image</label><br>
                    <img src="<?=FRONT_URL?><?php echo $page_content_details['image']; ?>" width="200px">
                  </div>
                  <?php
                }
                ?>              
                
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return form_submit_function();">Update</button>

                <a href="<?php echo base_url('page-content'); ?>"><button type="button" class="btn btn-primary pull-left">Cancel</button></a>
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

    function form_submit_function()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      var title = document.getElementById("title").value.trim();
      var description = CKEDITOR.instances['description'].getData();
      
      /*var description = document.getElementById("description").value.trim();*/
      

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

      if(focusStatus == "N")
      {
        // no validation error.. now submit the form
        $("#page-content-form").submit();
      }

      return false;
    }
  </script>


