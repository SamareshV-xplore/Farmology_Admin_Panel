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
      <h1>Meta Data List</h1>
    </section>
    <!-- Main content -->
    <section class="content">

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
                  <th>Page Name</th>
                  <th>Meta Key words</th>
                  <th>Last Updated</th>
                  <th class="action_area_td">Action</th>
                </tr>
                </thead>
                <tbody> 
                <?php
                  if(count($meta_data_list) > 0)
                  {
                    $slno = 1;
                    foreach($meta_data_list as $list)
                    {
                      
                      ?>
                      <tr>
                        <td  style="display: none;"><?php echo $slno; ?></td>
                        <td><?php echo $list['page_name']; ?></td>
                        <td><?php echo $list['meta_keyword']; ?></td>
                          <td><?php echo date("d/m/y H:i", strtotime($list['updated_date'])); ?></td>
                        <td>
                          <a href="<?php echo base_url('meta-edit/'.$list['id']); ?>"><button type="button" class="btn bg-yellow btn-sm" title="Edit Details"><i class="fa fa-edit"></i>
                            </button></a>
                            <a href="<?php echo base_url('meta-delete/'.$list['id']); ?>" onclick="return confirm('Are you sure want to delete this banner?')"><button type="button" class="btn bg-red btn-sm" title="Delete"><i class="fa fa-trash"></i>
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
