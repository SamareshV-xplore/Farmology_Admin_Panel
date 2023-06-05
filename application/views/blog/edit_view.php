<?php

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
      <h1>Edit Blog</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Blog Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('blog/edit_submit') ?>" id="blog-form" enctype="multipart/form-data">
              <input type="hidden" name="blog_id" id="blog_id" value="<?=$blog_details['id']?>">
              <input type="hidden" name="blog_form" value="1">
              <div class="box-body">

                <div class="form-group col-md-6">
                  <label for="main_parent">Blog Category<span class="required_cls">*</span></label>
                  
                    <select name="blog_category" id="blog_category" class="form-control">
                    <option value="">Select Blog Category</option>
                    <?php

                    if(count($blog_category) > 0)
                    {
                      foreach($blog_category as $parent_row)
                      {
                        ?>
                        <option value="<?php echo $parent_row['id']; ?>" <?php if($parent_row['id'] == $blog_details['category_id']) { ?> selected="selected" <?php } ?>><?php echo $parent_row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>

                    
                  
                </div>

                

                <div class="clearfix"></div>
                

                <div class="form-group col-md-6">
                  <label for="cate_title">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="blog_title" name="blog_title" placeholder="Blog Title" onblur="return check_slug();" value="<?=$blog_details['title']?>" >
                </div>

                <div class="form-group col-md-6">
                  <input type="hidden" id="slug_status" value="1">
                  <label for="cate_slug">Slug<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="blog_slug" name="blog_slug" placeholder="Blog Slug" onblur="return check_custom_slug();" value="<?=$blog_details['slug']?>" >
                </div>

                <div class="form-group col-md-12">
                  <label for="last_name" >Description<span class="required_cls">*</span></label>
                  <textarea name="description" id="description" class="form-control"><?=$blog_details['blog_content']?></textarea>
                </div>

                <div class="form-group col-md-4">
                  <label for="first_name">Image</label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                  <br>
                  <img style="height: 150px; width: 150px; object-fit: cover;" src="<?=$blog_details['image']?>" class="img-responsive">
                </div>    

                

                <div class="form-group col-md-4">
                  <label for="blood_group">Status</label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y" <?php if($blog_details['status'] == 'Y')
                    { ?> selected="selected" <?php } ?> >Active</option>
                    <option value="N" <?php if($blog_details['status'] == 'N')
                    { ?> selected="selected" <?php } ?> >Inactive</option>
                  </select>
                </div>

                


              </div>
                

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return update_blog_submit();">Update Blog</button>
                <a href="<?php echo base_url('blog'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
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
    // check slug
    function check_slug()
    {
      var blog_title = document.getElementById("blog_title").value.trim();
      var blog_id = document.getElementById("blog_id").value;
      
      if(blog_title != '')
      {
        var dataString = 'blog_title=' + encodeURI(blog_title) + '&blog_id=' + blog_id;

        $.ajax({
        type: "POST",
        url: "<?=base_url('blog/ajax_get_slug')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          if(obj.status == 'Y')
          { 
            // avilable

            document.getElementById("slug_status").value = '1';    
            document.getElementById("blog_slug").value = obj.slug;  
            $('#blog_slug').removeClass('error_cls');
          }
          else
          {
            // not avilable
            document.getElementById("slug_status").value = '0';    
            document.getElementById("blog_slug").value = obj.slug;
            $('#blog_slug').addClass('error_cls');
          }
          
        }
        });
      }

      return false;
    }

    function check_custom_slug()
    {
      var blog_slug = document.getElementById("blog_slug").value.trim();
      if(blog_slug != '')
      {
        var dataString = 'blog_id='+'<?=$blog_details['id']?>'+'&slug=' + encodeURI(blog_slug);

        $.ajax({
        type: "POST",
        url: "<?=base_url('blog/ajax_get_custom_slug')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          if(obj.status == 'Y')
          { 
            // avilable

            document.getElementById("slug_status").value = '1';    
            document.getElementById("blog_slug").value = obj.slug;  
            $('#blog_slug').removeClass('error_cls');
          }
          else
          {
            // not avilable
            document.getElementById("slug_status").value = '0';    
            document.getElementById("blog_slug").value = obj.slug;
            $('#blog_slug').addClass('error_cls');
          }
          
        }
        });
      }

      return false;
    }
    

    function update_blog_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var blog_title = document.getElementById("blog_title").value.trim();
      var blog_slug = document.getElementById("blog_slug").value.trim();
      var slug_status = document.getElementById("slug_status").value;
      
      var status = document.getElementById("status").value;

      var blog_category = $('#blog_category').val();
      var description = CKEDITOR.instances['description'].getData().replace(/<[^>]*>/gi, '').length;

      if(blog_category.length == 0){
        $('#blog_category').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#blog_category').focus();
            focusStatus = 'Y';
        }
      }
      
      if(blog_title == '')
      {
        $('#blog_title').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#blog_title').focus();
            focusStatus = 'Y';
        }     
      }

      if(blog_slug == '' || slug_status == '0')
      {
        $('#blog_slug').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#blog_slug').focus();
            focusStatus = 'Y';
        }     
      }

      if(description < 10)
      {
        $('#cke_description').addClass('error_cls');
        if(focusStatus == 'N')
        {
            focusStatus = 'Y';
        }     
      }
      else
      {
        $('#cke_description').removeClass('error_cls');
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


