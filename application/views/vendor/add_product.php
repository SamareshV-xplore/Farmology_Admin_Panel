<style type="text/css">
  .required_cls
  {
    color: red;
  }
  .option_div
  {
    /* border: 2px solid #e2d6d6; */
  }
  .box{
    margin-bottom: 30px;
  }
  .box.box-solid{
    border: 1px solid #d2d6de;
  }
  .px-0{
    padding-left: 0;
    padding-right: 0;
  }
  .box-header>.fa {
    margin-right: 0;
    cursor: pointer;
  }
  .pt-2{
    padding-top: 1em;
  }
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Product Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start --> 
            <form method="post" role="form" action="<?php echo base_url('vendors/add_product') ?>" id="product-form" enctype="multipart/form-data">
              <input type="hidden" name="product_form" value="1">
              <div class="box-body">

                <div class="form-group col-md-6">
                  <label for="cate1">Chose Crop<span class="required_cls">*</span></label>
                  <select name="crop[]" id="crop" class="form-control" multiple="">
                    <!-- <option value="0">Select Crop</option> -->
                    <?php
                    if(count($crop_list) > 0)
                    {
                      foreach($crop_list as $row)
                      {
                        ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>
                </div>



                <div class="form-group col-md-6">
                  <label for="cate1">Category<span class="required_cls">*</span></label>
                  <select name="cate[]" id="cate1" class="form-control" multiple="">
                    
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


                <div class="clearfix"></div>
                

                <div class="form-group col-md-6">
                  <label for="name">Name<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Product Name" onblur="return check_slug();" >
                </div>

                <div class="form-group col-md-6">
                  <input type="hidden" id="slug_status" value="0">
                  <label for="slug">Slug<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="slug" name="slug" placeholder="Category Slug" onblur="return check_custom_slug();" >
                </div>

                <div class="form-group col-md-6">
                  <label for="image">Image<span class="required_cls">*</span></label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                </div>                

                <div class="form-group col-md-6">
                  <label for="status">Status</label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y">Active</option>
                    <option value="N">Inactive</option>
                  </select>
                </div>

                <div class="form-group col-md-12">
                  <label for="short_description" >Short Description<span class="required_cls">*</span></label>
                  <textarea name="short_description" id="short_description" class="form-control"></textarea>
                </div>

                <div class="form-group col-md-12">
                  <label for="description" >Description<span class="required_cls">*</span></label>
                  <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group col-md-12 px-0" id="ai_div">

                  <div class="col-md-6">
                    <label class="pt-2">Additional Information</label>
                  </div>

                  <div class="form-group col-md-6">
                    <button class="btn btn-primary pull-right" onclick="return new_ai();" type="button">Add Information</button>
                  </div>

                  

                </div>

                

                <div class="form-group col-md-12 px-0" id="variation_div">
                  <div class="col-md-6">
                    <label class="pt-2">Product Variation<span class="required_cls">*</span></label>
                  </div>
                  <div class="form-group col-md-6">
                    <button class="btn btn-primary pull-right" onclick="return new_veriation();" type="button">Add New Variation</button>
                  </div>
                  <br>
              
              

              </div>

              



              </div>

              
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return form_submit();">Create Product</button>
                <a href="<?php echo base_url('product'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">

    new_veriation();

    CKEDITOR.replace( 'short_description' );
    CKEDITOR.replace( 'description' );

    function remove_ai(u_id)
    {
      $( "#div_"+u_id ).remove();
    }

    function remove_option(u_id)
    {
      $( "#div_"+u_id ).remove();
    }

    function new_ai()
    {
      var u_id = Math.round(new Date().getTime()/1000);

      var html_str = '<div class="col-md-12 option_div" id="div_'+u_id+'"><input type="hidden" class="ai_u_id" name="ai_u_id[]" value="'+u_id+'"><div class="box box-solid with-border"><div class="box-header with-border"><div class = "right"><i class="fa fa-times-circle pull-right" style="color:red" onclick="return remove_option('+u_id+');" ></i></div></div><div class="box-body"><div class="col-md-6 form-group"><label>Title<span class="required_cls">*</span></label><input type="text" name="ai_title[]" class="form-control" id="ai_title_'+u_id+'"></div><div class="col-md-6 form-group"><label>Value<span class="required_cls">*</span></label><input type="text" name="ai_value[]" class="form-control" id="ai_value_'+u_id+'"></div></div></div>';

      $( "#ai_div" ).append( html_str );




    }

    function new_veriation()
    {
      var u_id = Math.round(new Date().getTime()/1000);

      var list = '<?php echo $state_list; ?>';
      var state_list = '<select class="form-control" name="state_id[]" id="state_id_'+u_id+'">'+list+'</select>';

      var html_str = '<div class="col-md-6 option_div" id="div_'+u_id+'"><input type="hidden" class="option_u_id" name="option_u_id[]" value="'+u_id+'"><div class="box box-solid with-border"><div class="box-header with-border"><div class = "right"><i class="fa fa-times-circle pull-right" style="color:red" onclick="return remove_option('+u_id+');" ></i></div></div<div class="box-body"><div class="col-md-12 form-group"><label>Variation Title<span class="required_cls">*</span></label><input type="text" name="variation_title[]" class="form-control" id="variation_title_'+u_id+'"></div><div class="col-md-6 form-group"><label>Price<span class="required_cls">*</span></label><input type="number" name="price[]" class="form-control" id="price_'+u_id+'"></div><div class="col-md-6 form-group"><label>Discount(%)</label><input type="number" name="discount[]" id="discount_'+u_id+'" class="form-control"></div><div class="col-md-6 form-group"><label>State List<span class="required_cls">*</span></label>'+state_list+'</div></div></div></div>';

      $( "#variation_div" ).append( html_str );




    }
    

    // check slug
    function check_slug()
    {
      
      var name = document.getElementById("name").value.trim();
      if(name != '')
      {
        var dataString = 'name=' + encodeURI(name);

        $.ajax({
        type: "POST",
        url: "<?=base_url('product/ajax_get_product_slug')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          
          if(obj.status == 'Y')
          { 
            // avilable
            document.getElementById("slug_status").value = '1';    
            document.getElementById("slug").value = obj.slug;  
            $('#slug').removeClass('error_cls');
          }
          else
          {
            // not avilable
            document.getElementById("slug_status").value = '0';    
            document.getElementById("slug").value = obj.slug;
            $('#slug').addClass('error_cls');
          }
          
        }
        });
      }

      return false;
    }

    function check_custom_slug()
    {
      var slug = document.getElementById("slug").value.trim();
      if(slug != '')
      {
        var dataString = 'slug=' + encodeURI(slug);
        $.ajax({
        type: "POST",
        url: "<?=base_url('product/check_custom_slug')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          if(obj.status == 'Y')
          { 
            // avilable
            document.getElementById("slug_status").value = '1';    
            document.getElementById("slug").value = obj.slug;  
            $('#slug').removeClass('error_cls');
          }
          else
          {
            // not avilable
            document.getElementById("slug_status").value = '0';    
            document.getElementById("slug").value = obj.slug;
            $('#slug').addClass('error_cls');
          }
          
        }
        });
      }

      return false;
    }
    

    function form_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      var cate1 = $('#cate1 > option:selected');
      var crop = $('#crop > option:selected');
      //var cate3 = document.getElementById("cate3").value;

      
      var name = document.getElementById("name").value.trim();
      var slug = document.getElementById("slug").value.trim();
      var image = document.getElementById("image").value;
      var short_description = CKEDITOR.instances['short_description'].getData().replace(/<[^>]*>/gi, '').length;
      var description = CKEDITOR.instances['description'].getData().replace(/<[^>]*>/gi, '').length;

      
         
      

      if(crop.length == 0){
        $('#crop').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#crop').focus();
            focusStatus = 'Y';
        }
      }

      if(cate1.length == '0')
      {
        $('#cate1').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#cate1').focus();
            focusStatus = 'Y';
        }     
      }

      if(name == '')
      {
        $('#name').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#name').focus();
            focusStatus = 'Y';
        }     
      }

      if(slug == '' || slug_status == '0')
      {
        $('#slug').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#slug').focus();
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

    
      if(short_description < 10 )
      {
        
        $('#short_description').addClass('error_cls');
        if(focusStatus == 'N')
        {
            focusStatus = 'Y';
        }     
      }
      else
      {
        
        $('#short_description').removeClass('error_cls');
      }

      if(description < 10)
      {
        $('#description').addClass('error_cls');
        if(focusStatus == 'N')
        {
            focusStatus = 'Y';
        }     
      }
      else
      {
        $('#description').removeClass('error_cls');
      }

      var ai_count = $('.ai_u_id').length;

      if(ai_count > 0)
      {

        $("input[name='ai_u_id[]']")
              .map(function(){
                var ai_u_id = $(this).val();
                var ai_title = document.getElementById("ai_title_"+ai_u_id).value;
                var ai_value = document.getElementById("ai_value_"+ai_u_id).value;
                
                if(ai_title == '')
                {
                  $('#ai_title_'+ai_u_id).addClass('error_cls');
                  if(focusStatus == 'N')
                  {
                      $('#ai_title_'+ai_u_id).focus();
                      focusStatus = 'Y';
                  }  
                }

                if(ai_value == '')
                {
                  $('#ai_value_'+ai_u_id).addClass('error_cls');
                  if(focusStatus == 'N')
                  {
                      $('#ai_value_'+ai_u_id).focus();
                      focusStatus = 'Y';
                  }  
                }


              }).get();
      }


      var variation_count = $('.option_u_id').length;
      
      if(variation_count == 0)
      {
        
        focusStatus = 'Y';
        new_veriation();
        swal({
              title: "Required Variation",
              text: "You must add at least one variation for create new product.",
              icon: "error",
            });
        variation_count++;
      }

      if(variation_count > 0)
      {
        $("input[name='option_u_id[]']")
              .map(function(){
                var option_u_id = $(this).val();
                var op_title = document.getElementById("variation_title_"+option_u_id).value;
                var op_price = document.getElementById("price_"+option_u_id).value;
                var op_discount = document.getElementById("discount_"+option_u_id).value;
                var op_state = $('#state_id_'+option_u_id).val();
                
                if(op_state.length == 0){
                  $('#state_id_'+option_u_id).addClass('error_cls');
                  if(focusStatus == 'N')
                    {
                      $('#state_id_'+option_u_id).focus();
                      focusStatus = 'Y';
                    }
                }
                if(op_title == '')
                {
                  $('#variation_title_'+option_u_id).addClass('error_cls');
                  if(focusStatus == 'N')
                  {
                      $('#variation_title_'+option_u_id).focus();
                      focusStatus = 'Y';
                  }  
                }

                if(op_price < 0 || op_price == 0 || op_price == '')
                {
                  $('#price_'+option_u_id).addClass('error_cls');
                  if(focusStatus == 'N')
                  {
                      $('#price_'+option_u_id).focus();
                      focusStatus = 'Y';
                  }  
                }

                if(op_discount < 0 || op_discount > 99)
                {
                  $('#discount_'+option_u_id).addClass('error_cls');
                  if(focusStatus == 'N')
                  {

                      $('#discount_'+option_u_id).focus();
                      focusStatus = 'Y';
                  }  
                }                
                

              }).get();
      }
              
        
          



      if(focusStatus == "N")
      {
        // no validation error.. now submit the form
        $("#product-form").submit();
        //alert('all right');
      }

      return false;
    }
  </script>


