<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Add New Category</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Category Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('category/add_submit') ?>" id="category-form" enctype="multipart/form-data">
              <input type="hidden" name="category_form" value="1">
              <div class="box-body">

                <div class="form-group col-md-6">
                  <label for="main_parent">Parent Category<span class="required_cls">*</span></label>
                  <select name="main_parent" id="main_parent" class="form-control" onchange="return get_child_category();">
                    <option value="0">Parent</option>
                    <?php
                    if(count($main_parent) > 0)
                    {
                      foreach($main_parent as $parent_row)
                      {
                        ?>
                        <option value="<?php echo $parent_row['id']; ?>"><?php echo $parent_row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>
                </div>

                <!--<div class="form-group col-md-6" id="sub_child_main_div" style="display:none;">
                  <label for="sub_parent">Child Category<span class="required_cls">*</span></label>
                  <div id="sub_child_sub_div">
                  <select name="sub_parent" id="sub_parent" class="form-control">
                    <option value="0">No Child</option>
                    <?php
                    if(count($main_parent) > 0)
                    {
                      foreach($main_parent as $parent_row)
                      {
                        ?>
                        <option value="<?php echo $parent_row['id']; ?>"><?php echo $parent_row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>
                </div>
                </div>-->

                <div class="clearfix"></div>
                

                <div class="form-group col-md-6">
                  <label for="cate_title">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="cate_title" name="cate_title" placeholder="Category Title" onblur="return check_slug();" >
                </div>

                <div class="form-group col-md-6">
                  <input type="hidden" id="slug_status" value="0">
                  <label for="cate_slug">Slug<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="cate_slug" name="cate_slug" placeholder="Category Slug" onblur="return check_custom_slug();" >
                </div>

                <div class="form-group col-md-12">
                  <label for="last_name" >Description</label>
                  <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group col-md-6">
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

                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="is_featured" value="Y"> <b>Set as Featured</b>
                      </label>
                    </div>
                  </div>

              </div>
              <!-- /.box-body -->
                <!--<div class="box">-->
                    <div class="box-header with-border">
                        <h3 class="box-title">SEO/Meta Data Details</h3>
                    </div>
                    <div class="box-body">

                        <div class="form-group col-md-6">
                            <label for="first_name">Meta Title</label>
                            <textarea name="meta_title" id="meta_title" class="form-control" placeholder="Enter Meta Title" rows="6"></textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="last_name" >Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" placeholder="Enter Meta description" rows="6"></textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="employee_id">Meta Keywords</label>
                            <textarea name="meta_keyword" id="meta_keyword" class="form-control" placeholder="Enter Meta Keywords" rows="6"></textarea>
                        </div>
                    </div>
                    <!-- /.box-body -->
                <!--</div>-->

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return add_category_submit();">Create Category</button>
                <a href="<?php echo base_url('category/list'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">
    // parent change function
    function get_child_category()
    {      
      var main_parent = document.getElementById("main_parent").value;
      if(main_parent > 0)
      {
        var dataString = 'parent_id=' + main_parent;

        $.ajax({
        type: "POST",
        url: "<?=base_url('category/ajax_get_category_list_by_parent_id')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          if(obj.status == 'Y')
          {            
            $( "#sub_child_sub_div" ).empty();
            $( "#sub_child_sub_div" ).append( obj.html );
            $( "#sub_child_main_div" ).show();
          }
          else
          {
            $( "#sub_child_sub_div" ).empty();
            $( "#sub_child_sub_div" ).append( '<select name="sub_parent" id="sub_parent" class="form-control"><option value="0">Select Child</option></select>' );
            $( "#sub_child_main_div" ).hide();
          }
          
        }
        });
        
      }
      else
      {            
            $( "#sub_child_sub_div" ).empty();
            $( "#sub_child_sub_div" ).append( '<select name="sub_parent" id="sub_parent" class="form-control"><option value="0">Child</option></select>' );
            $( "#sub_child_main_div" ).hide();
      }

      return false;
    }

    // check slug
    function check_slug()
    {
      var cate_title = document.getElementById("cate_title").value.trim();
      if(cate_title != '')
      {
        var dataString = 'cate_title=' + encodeURI(cate_title);

        $.ajax({
        type: "POST",
        url: "<?=base_url('category/ajax_get_category_slug')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          if(obj.status == 'Y')
          { 
            // avilable

            document.getElementById("slug_status").value = '1';    
            document.getElementById("cate_slug").value = obj.slug;  
            $('#cate_slug').removeClass('error_cls');
          }
          else
          {
            // not avilable
            document.getElementById("slug_status").value = '0';    
            document.getElementById("cate_slug").value = obj.slug;
            $('#cate_slug').addClass('error_cls');
          }
          
        }
        });
      }

      return false;
    }

    function check_custom_slug()
    {
      var cate_slug = document.getElementById("cate_slug").value.trim();
      if(cate_title != '')
      {
        var dataString = 'slug=' + encodeURI(cate_slug);

        $.ajax({
        type: "POST",
        url: "<?=base_url('category/check_custom_slug')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          if(obj.status == 'Y')
          { 
            // avilable

            document.getElementById("slug_status").value = '1';    
            document.getElementById("cate_slug").value = obj.slug;  
            $('#cate_slug').removeClass('error_cls');
          }
          else
          {
            // not avilable
            document.getElementById("slug_status").value = '0';    
            document.getElementById("cate_slug").value = obj.slug;
            $('#cate_slug').addClass('error_cls');
          }
          
        }
        });
      }

      return false;
    }
    

    function add_category_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var cate_title = document.getElementById("cate_title").value.trim();
      var cate_slug = document.getElementById("cate_slug").value.trim();
      var slug_status = document.getElementById("slug_status").value;
      var image = document.getElementById("image").value.trim();
      var status = document.getElementById("status").value;
      
      if(cate_title == '')
      {
        $('#cate_title').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#cate_title').focus();
            focusStatus = 'Y';
        }     
      }

      if(cate_slug == '' || slug_status == '0')
      {
        $('#cate_slug').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#cate_slug').focus();
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
        $("#category-form").submit();
      }

      return false;
    }
  </script>


