<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Edit District</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">District Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('district_management/edit_submit')?>" id="district-form" enctype="multipart/form-data">
              <input type="hidden" name="district_id" value="<?=$district_details['id']?>">
              <div class="box-body">
               
                <div class="form-group col-md-6">
                  <label for="first_name">District<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="district" name="district" placeholder="District Name"  value="<?php echo $district_details['district_name']; ?>">
                </div>

                  <div class="form-group col-md-6">
                      <label for="blood_group">State<span class="required_cls">*</span></label>
                      <select class="form-control" name="state_id" id="state">
                          <?php
                            foreach ($state_list as $state){
                          ?>
                          <option value="<?php echo $state['id'] ?>"<?php if($district_details['state_id'] == $state['id']){ echo "selected"; } ?>><?php echo $state['state']; ?></option>
                          <?php } ?>
                      </select>
                  </div>

                <div class="form-group col-md-6">
                  <label for="first_name">Update Image</label>
                  <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*">
                </div>
                <div class="form-group col-md-6">
                  <label for="first_name">Image</label><br>
                  <img src="<?=FRONT_URL?><?php echo $district_details['image']; ?>" width="200px">
                </div>

                  <div class="form-group col-md-6">
                      <label for="first_name">Delivery Charge</label>
                      <input type="text" class="form-control" id="charge" name="charge" placeholder="Enter Amount" value="<?php echo $district_details['charge']; ?>">
                  </div>

                <div class="form-group col-md-6">
                  <label for="blood_group">Status<span class="required_cls">*</span></label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y"<?php if($district_details['status'] == 'Y'){ echo "selected"; } ?>>Active</option>
                    <option value="N"<?php if($district_details['status'] == 'N'){ echo "selected"; } ?>>Inactive</option>
                  </select>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return edit_district_submit();">Update District Details</button>

                <a href="<?php echo base_url('district-list'); ?>"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
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

    function edit_district_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      
      var district = document.getElementById("district").value.trim();
      var state = document.getElementById("state").value;
      var status = document.getElementById("status").value;
      

      if(district == '')
      {
        $('#district').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#district').focus();
            focusStatus = 'Y';
        }     
      }

      if(state == '')
      {
        $('#state').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#state').focus();
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
        $("#district-form").submit();
      }

      return false;
    }
  </script>


