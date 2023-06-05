<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Add New City</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">City Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('city_management/add_submit') ?>" id="city-form" enctype="multipart/form-data">
              <input type="hidden" name="city_form" value="1">
              <div class="box-body">
                

                <div class="form-group col-md-6">
                  <label for="first_name">Select State<span class="required_cls">*</span></label>
                    <select class="form-control" name="state" id="state">
                        <?php
                            if(count($state_list) > 0){
                                foreach($state_list as $list){
                        ?>
                                    <option value="<?php echo $list['id'] ?>"><?php echo $list['state'] ?></option>
                        <?php
                                }
                            }else{
                        ?>
                                <option>No State Found</option>
                        <?php
                            }
                        ?>
                    </select>
                </div>

                  <div class="form-group col-md-6">
                      <label for="first_name">City Name<span class="required_cls">*</span></label>
                      <input type="text" class="form-control" id="city" name="city" placeholder="City Name" >
                  </div>

                <div class="form-group col-md-4">
                  <label for="first_name">Image<span class="required_cls">*</span></label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                </div>

                  <div class="form-group col-md-4">
                      <label for="first_name">Delivery Charge</label>
                      <input type="text" class="form-control" id="charge" name="charge" placeholder="Enter Amount" >
                  </div>

                <div class="form-group col-md-4">
                  <label for="blood_group">Status<span class="required_cls">*</span></label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y">Active</option>
                    <option value="N">Inactive</option>
                  </select>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return add_city_submit();">Add City</button>
                <a href="<?php echo base_url('city-list'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
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

    function add_city_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var state = document.getElementById("state").value;
      var city = document.getElementById("city").value.trim();
      var image = document.getElementById("image").value.trim();
      var status = document.getElementById("status").value;
      
      if(state == '')
      {
        $('#state').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#state').focus();
            focusStatus = 'Y';
        }     
      }

        if(city == '')
        {
            $('#city').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#city').focus();
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
        $("#city-form").submit();
      }

      return false;
    }
  </script>


