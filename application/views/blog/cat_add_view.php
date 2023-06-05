<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Add New Category Blog</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Blog Category Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('blog/category_add_submit') ?>" id="blog-form">
              <input type="hidden" name="blog_form" value="1">
              <div class="box-body">

                <div class="form-group col-md-6">
                  <label for="cate_title">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="blog_title" name="title" placeholder="Blog Category Title">
                </div>

                

                <!-- <div class="clearfix"></div> -->

                <div class="form-group col-md-6">
                  <label for="blood_group">Status</label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y">Active</option>
                    <option value="N">Inactive</option>
                  </select>
                </div>


              </div>
              

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return add_blog_category_submit();">Create Blog Category</button>
                <a href="<?php echo base_url('blog/category'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">

    function add_blog_category_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var blog_title = document.getElementById("blog_title").value.trim();
      var status = document.getElementById("status").value;


      
      if(blog_title == '')
      {
        $('#blog_title').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#blog_title').focus();
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
        $("#blog-form").submit();
      }

      return false;
    }
  </script>


