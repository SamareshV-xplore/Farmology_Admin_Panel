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
      <h1>Rating & Review List</h1>
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
                    <option value="P" <?php if($filter_data['status'] == 'P') { ?> selected <?php } ?>>Panding</option>
                    <option value="A" <?php if($filter_data['status'] == 'A') { ?> selected <?php } ?>>Approved</option>
                    <option value="R" <?php if($filter_data['status'] == 'R') { ?> selected <?php } ?>>Rejected</option>
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
                  <th style="width: 20%">Product Name</th>
                  <th style="width: 15%">User Details</th>
                  <th style="width: 10%">Rate</th>
                  <th style="width: 30%">Review</th>
                  <th style="width: 10%">Post Date</th>
                  <th style="width: 15%">Status</th>
                  
                </tr>
                </thead>
                <tbody> 
                <?php
                  if(count($review_list) > 0)
                  {
                    $slno = 1;
                    foreach($review_list as $list)
                    {
                      
                      ?>
                      <tr>
                        <td  style="display: none;"><?php echo $slno; ?></td>
                        <td style="width: 20%">
                          <?php echo $list['product_details']['name']; ?>
                          </td>
                        <td style="width: 15%">
                          <?=$list['customer_details']['full_name']?><br>
                          <?=$list['customer_details']['phone']?>
                        </td>
                        <td style="width: 10%"><?=$list['rating']?> Star</td>
                        <td  style="width: 30%"><?php if($list['review_text'] == NULL || $list['review_text'] == '') { ?> <center><small class="label bg-blue">No Review</small></center> <?php } else { echo "<center>".$list['review_text']."</center>"; } ?></td>
                        <td style="width: 10%"><?=date("d/m/Y h:i A", strtotime($list['created_date']))?></td>

                        <td style="width: 15%" >
                          <select id="review_status_<?=$list['id']?>" class="form-control" onchange="return update_status(<?=$list['id']?>);">
                            <option <?php if($list['status'] == 'P') { ?> selected="selected" <?php } ?> value="P">Pending</option>
                            <option <?php if($list['status'] == 'A') { ?> selected="selected" <?php } ?> value="A">Approved</option>
                            <option <?php if($list['status'] == 'R') { ?> selected="selected" <?php } ?> value="R">Rejected</option>
                          </select>
                        </td>
                        
                      </tr>
                      <?php
                      $slno++;
                    }
                  }
                  else
                  {
                    ?>
                    <tr>
                      <td colspan="7"><center>No Review Found.</center></td>
                    </tr>
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
    function update_status(id)
    {
      if(id > 0)
      {

      var status_val = $('#review_status_' + id).val();

      var dataString = 'id=' + id + '&status=' + status_val;
        $.ajax({
        type: "POST",
        url: "<?=base_url('product_comment/update_comment_status')?>",
        data: dataString,
        cache: false,
        success: function(html) {
          swal("Success!", "Review status updated to!", "success");
        }
        });
  
      }
    }
  </script>