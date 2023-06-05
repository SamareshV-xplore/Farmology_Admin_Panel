<style type="text/css">
  .required_cls
  {
    color: red;
  }
  .reset_btn{
    margin-top: 24px;
  }
  .high_label
  {
    font-size: 12px;
  }
  .action_area_td
  {
    width: 13%;
  }
</style>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Zip List</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
                <div class="form-group col-md-6">
                    <label for="official_email">List of Zip Codes for <h2><?php echo $city['city_name']; ?></h2> </label>
                </div>
                <div class="form-group col-md-6">
                    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-default">Add New Zip Code</button>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <!--<h3 class="box-title">Data Table With Full Features</h3>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="display: none;">Sl. No</th>
                  <th>Zip Code</th>
                  <th>Created Date</th>
                  <th class="action_area_td">Action</th>
                </tr>
                </thead>
                <tbody> 
                <?php
                  if(count($zip_list) > 0)
                  {
                    $slno = 1;
                    foreach($zip_list as $list)
                    {
                      
                      ?>
                      <tr>
                        <td  style="display: none;"><?php echo $slno; ?></td>
                        <td><?php echo $list['pin_code']; ?></td>
                        <td><?php echo date("d/m/y H:i", strtotime($list['created_date']));; ?></td>
                        <td>
                            <button type="button" class="btn bg-yellow btn-sm" data-toggle="modal" data-target="#modal-edit" title="Edit Details" data-zipid="<?php echo $list['id']; ?>" data-zipcode="<?php echo $list['pin_code']; ?>" onclick="getZipData(this);"><i class="fa fa-edit"></i></button>
                            <a href="<?php echo base_url('zip-delete/'.$list['id']); ?>" onclick="return confirm('Are you sure want to delete this zip code?')"><button type="button" class="btn bg-red btn-sm" title="Delete"><i class="fa fa-trash"></i>
                            </button></a>
                        </td>
                      </tr>
                      <?php
                      $slno++;
                    }
                  }
                ?>
                </tbody>
                
              </table>
            </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<div class="modal fade" id="modal-default">
    <form method="post" role="form" action="<?php echo base_url('zip_management/add_zip') ?>" id="zip-form">
        <input type="hidden" name="city_id" value="<?php echo $city['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Zip Code</h4>
                </div>
                <div class="modal-body">
                    <label>Zip Code <span class="required_cls">*</span></label>
                    <input type="text" id="pin_code" name="pin_code" class="form-control"">
                    <span id="pin_err" style="color: red;"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="return validate_new_add();">Add</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal-edit">
    <form method="post" role="form" action="<?php echo base_url('zip_management/edit_submit') ?>" id="pin-form">
        <input type="hidden" name="zip_id" id="zip_id" value="">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Zip Code</h4>
                </div>
                <div class="modal-body">
                    <label>Zip Code <span class="required_cls">*</span></label>
                    <input type="text" id="zip_code" value="" name="zip_code" class="form-control"">
                    <span id="zip_err" style="color: red;"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="return validate_edit_zip();">Update Zip Code</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>

<script type="text/javascript">
    function validate_new_add()
    {
        $('.form-control').removeClass('error_cls');
        var focusStatus = "N";

        var pin_code = document.getElementById("pin_code").value.trim();

        if(pin_code == '')
        {
            $('#pin_code').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#pin_code').focus();
                focusStatus = 'Y';
            }
        }else if((pin_code.length != 6) || !(/^\d+$/.test(pin_code))){
            $('#pin_code').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#pin_code').focus();
                focusStatus = 'Y';
                $("#pin_err").text('Please provide a valid 6 digit pincode(ex:700001).');
            }
        }

        if(focusStatus == "N")
        {
            $("#zip-form").submit();
        }
    }

    function validate_edit_zip()
    {
        $('.form-control').removeClass('error_cls');
        var focusStatus = "N";

        var zip_code = document.getElementById("zip_code").value.trim();

        if(zip_code == 0)
        {
            $('#zip_code').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#zip_code').focus();
                focusStatus = 'Y';
            }
        }else if((zip_code.length != 6) || !(/^\d+$/.test(zip_code))){
            $('#zip_code').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#zip_code').focus();
                focusStatus = 'Y';
                $("#zip_err").text('Please provide a valid 6 digit pincode(ex:700001).');
            }
        }

        if(focusStatus == "N")
        {
            $("#pin-form").submit();
        }
    }

    function getZipData(data) {
        $("#zip_id").val(data.getAttribute("data-zipid"));
        $("#zip_code").val(data.getAttribute("data-zipcode"));
    }
</script>
