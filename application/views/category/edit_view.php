<?php
/*
echo "<pre>";
print_r($category_data);
echo "</pre>"; exit;
*/
?>
<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Edit Category</h1>
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
            <form method="post" role="form" action="<?php echo base_url('category/edit_submit') ?>" id="category-form" enctype="multipart/form-data">
              <input type="hidden" name="cate_id" id="cate_id" value="<?=$category_data['id']?>">
              <input type="hidden" name="category_form" value="1">
              <div class="box-body">

                <div class="form-group col-md-6">
                  <label for="main_parent">Parent Category<span class="required_cls">*</span></label>
                  <?php 
                  if($category_data['parent_details'][0]['id'] == 0) 
                  {
                  ?>
                  <input type="hidden" name="main_parent" id="main_parent">
                  <select class="form-control" onchange="return get_child_category();" disabled="disabled">
                    <option value="0">Parent</option>
                  </select>
                  <?php
                  }
                  else
                  {
                    ?>

                    <select name="main_parent" id="main_parent" class="form-control" onchange="return get_child_category();" >
                    <option value="0">Parent</option>
                    <?php

                    if(count($main_parent) > 0)
                    {
                      foreach($main_parent as $parent_row)
                      {
                        ?>
                        <option value="<?php echo $parent_row['id']; ?>" <?php if($parent_row['id'] == $category_data['parent_details'][0]['id']) { ?> selected="selected" <?php } ?>><?php echo $parent_row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>

                    <?php
                  } 
                  ?>
                  
                </div>

                <?php
                /*
                if($category_data['parent_details'][1]['id'] > 0) 
                {
                ?>

                <div class="form-group col-md-6" id="sub_child_main_div" <?php if($category_data['parent_details'][1]['id'] == 0) { ?> style="display: none;" <?php } ?>>
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
                </div>

                <?php 
                }
                */
                ?>

                <div class="clearfix"></div>
                

                <div class="form-group col-md-6">
                  <label for="cate_title">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="cate_title" name="cate_title" placeholder="Category Title" onblur="return check_slug();" value="<?=$category_data['title']?>" >
                </div>

                <div class="form-group col-md-6">
                  <input type="hidden" id="slug_status" value="1">
                  <label for="cate_slug">Slug<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="cate_slug" name="cate_slug" placeholder="Category Slug" onblur="return check_custom_slug();" value="<?=$category_data['slug']?>" >
                </div>

                <div class="form-group col-md-12">
                  <label for="last_name" >Description</label>
                  <textarea name="description" id="description" class="form-control"><?=$category_data['description']?></textarea>
                </div>

                <div class="form-group col-md-4">
                  <label for="first_name">Image</label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                  <br>
                  <img style="height: 150px; width: 150px; object-fit: cover;" src="<?=$category_data['image']?>" class="img-responsive">
                </div>    

                <div class="form-group col-md-4">
                  <label for="first_name">Icon</label>
                  <input type="file" class="form-control" id="icon" name="icon" placeholder="Image" accept="image/*">
                  <br>
                  <img style="height: 150px; width: 150px; object-fit: cover;" src="<?=$category_data['icon']?>" class="img-responsive">
                </div>

                <div class="form-group col-md-4">
                  <label for="blood_group">Status</label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y" <?php if($category_data['status'] == 'Y')
                    { ?> selected="selected" <?php } ?> >Active</option>
                    <option value="N" <?php if($category_data['status'] == 'N')
                    { ?> selected="selected" <?php } ?> >Inactive</option>
                  </select>
                </div>

                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="is_featured" value="Y" <?php if($category_data['is_featured'] == 'Y') { ?> checked="checked" <?php } ?> > <b>Set as Featured</b>
                      </label>
                    </div>
                  </div>


              </div>
                <!-- <div class="box-header with-border">
                    <h3 class="box-title">SEO/Meta Data Details</h3>
                </div>
                <div class="box-body">

                    <div class="form-group col-md-6">
                        <label for="first_name">Meta Title<span class="required_cls">*</span></label>
                        <textarea name="meta_title" id="meta_title" class="form-control" placeholder="Enter Meta Title" rows="6"> <?php echo $category_data['meta_details']['meta_title'] ?></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="last_name" >Description<span class="required_cls">*</span></label>
                        <textarea name="meta_description" id="meta_description" class="form-control" placeholder="Enter Meta description" rows="6"> <?php echo $category_data['meta_details']['meta_description'] ?></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="employee_id">Meta Keywords</label>
                        <textarea name="meta_keyword" id="meta_keyword" class="form-control" placeholder="Enter Meta Keywords" rows="6"><?php echo $category_data['meta_details']['meta_keyword'] ?></textarea>
                    </div>
                </div> -->
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return update_category_submit();">Update Category</button>
                <a href="<?php echo base_url('category'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">
    //get_child_category(<?=$category_data['parent_details'][0]['id']?>);
    // parent change function
    // check slug
    function check_slug()
    {
      var cate_title = document.getElementById("cate_title").value.trim();
      var cate_id = document.getElementById("cate_id").value;
      
      if(cate_title != '')
      {
        var dataString = 'cate_title=' + encodeURI(cate_title) + '&cate_id=' + cate_id;

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
      if(cate_slug != '')
      {
        var dataString = 'cate_id='+'<?=$category_data['id']?>'+'&slug=' + encodeURI(cate_slug);

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
    

    function update_category_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var cate_title = document.getElementById("cate_title").value.trim();
      var cate_slug = document.getElementById("cate_slug").value.trim();
      var slug_status = document.getElementById("slug_status").value;
      
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


