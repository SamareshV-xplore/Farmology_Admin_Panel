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
      <h1>Page Content List</h1>
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
                  <label for="official_email">Filter by Page </label>
                  <select name="page" id="page" class="form-control" onchange="return form_submit();">
                    <option value="all" <?php if($filter_data['page'] == 'all') { ?> selected <?php } ?>>All</option>
                    <?php
                    if(count($page_list) > 0)
                    {
                      foreach($page_list as $page_row)
                      {
                        ?>
                        <option value="<?php echo $page_row['id']; ?>" <?php if($page_row['id'] == $filter_data['page']) { ?> selected="selected" <?php } ?> ><?php echo $page_row['title']; ?></option>
                        <?php
                      }
                    }                    
                    ?>
                  </select>
                </div>

                <div class="form-group col-md-2" <?php if($filter_data['page'] == 'all' ) { ?> style="display: none;" <?php } ?> >
                  <a href="<?php echo base_url('page-content'); ?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
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
                  <th>Page</th>
                  <th>Section Title</th>
                  <th class="action_area_td">Action</th>
                </tr>
                </thead>
                <tbody> 
                <?php
                  if(count($page_content_list) > 0)
                  {
                    $slno = 1;
                    foreach($page_content_list as $list)
                    {
                      
                      ?>
                      <tr>
                        <td  style="display: none;"><?php echo $slno; ?></td>
                        <td><?php echo $list['page_details']['title']; ?></td>
                        <td><?php echo $list['title']; ?></td>
                        
                        <td>
                          <a href="<?php echo base_url('page-content-edit/'.$list['id']); ?>"><button type="button" class="btn bg-yellow btn-sm" title="Edit Details"><i class="fa fa-edit"></i>
                            </button></a>
                        </td>
                      </tr>
                      <?php
                      $slno++;
                    }
                  }
                  else
                    {
                      ?>
                      <tr><td colspan="3"><center>No Page Content Found.</center></td></tr>
                      <?php
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