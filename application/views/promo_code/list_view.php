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
      <h1>Coupon Code List</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <form method="get" action="" id="filter_form">
                <input type="hidden" name="filter" value="true">
              
                <div class="form-group col-md-5">
                  <label for="official_email">Filter by Status </label>
                  <select name="status" id="status" class="form-control" onchange="return form_submit();">
                    <option value="all" <?php if($filter_data['status'] == 'all') { ?> selected <?php } ?>>All</option>
                    <option value="Y" <?php if($filter_data['status'] == 'Y') { ?> selected <?php } ?>>Active</option>
                    <option value="N" <?php if($filter_data['status'] == 'N') { ?> selected <?php } ?>>Inactive</option>
                  </select>
                </div>

                <div class="form-group col-md-2" <?php if($filter_data['status'] !="Y" && $filter_data['status'] !="N" ) { ?> style="display: none;" <?php } ?> >
                  <a href="<?php echo base_url('banner-list'); ?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
                </div>

              </form>


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
                  <th>Promo Code Name</th>
                  <th>Promo Code</th>
                  <th>Created Date</th>
                    <th>Status</th>
                  <th class="action_area_td">Action</th>
                </tr>
                </thead>
                <tbody> 
                <?php
                  if(count($promo_code_list) > 0)
                  {
                    $slno = 1;
                    foreach($promo_code_list as $list)
                    {
                      
                      ?>
                      <tr>
                        <td  style="display: none;"><?php echo $slno; ?></td>
                        <td><?php echo $list['title']; ?></td>
                        <td><?php echo $list['promo_code']; ?></td>
                        <td><?php echo date("d/m/y H:i", strtotime($list['created_date'])); ?></td>
                        <td>
                          <?php
                            if($list['status'] == 'Y')
                            {
                              echo "<label style='color:green'><b>Active<b></label>";
                            }
                            else
                            {
                              echo "<p style='color:red'>Inactive</p>";
                            }
                          ?>
                        </td>
                        <td>
                          <a href="<?php echo base_url('promo-edit/'.$list['id']); ?>"><button type="button" class="btn bg-yellow btn-sm" title="Edit Details"><i class="fa fa-edit"></i>
                            </button></a>
                            <a href="<?php echo base_url('promo-delete/'.$list['id']); ?>" onclick="return confirm('Are you sure want to delete this coupon?')"><button type="button" class="btn bg-red btn-sm" title="Delete"><i class="fa fa-trash"></i>
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
<script type="text/javascript">
    function form_submit()
    {
      $("#filter_form").submit();
    }
  </script>
