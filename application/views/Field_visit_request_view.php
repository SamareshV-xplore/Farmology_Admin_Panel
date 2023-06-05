<!-- Styles for Refresh Animation Start -->
<style>
@-webkit-keyframes rotating /* Safari and Chrome */ {
  from {
    -webkit-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  to {
    -webkit-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes rotating {
  from {
    -ms-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -webkit-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  to {
    -ms-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -webkit-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
.rotating {
  -webkit-animation: rotating 0.6s linear infinite;
  -moz-animation: rotating 0.6s linear infinite;
  -ms-animation: rotating 0.6s linear infinite;
  -o-animation: rotating 0.6s linear infinite;
  animation: rotating 0.6s linear infinite;
}

</style>
<!-- Styles for Refresh Animation End -->

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
  .sl_margin
  {
    margin-right: 5px;
  }
  .recommended_product_div,.advice-div{
    width: 100%;
    padding: 5px;
    height: 100%;
  }
  .recommended_product_div{
    padding-right: 18px;
  }
  .advice-div{
    border-right: 1px solid #e1e1e1;
  }
  .button_holder{
    width: 100%;
    margin-top: 5px;
    text-align: right;
  }

  .product-parent{
    width: 100%;
    display: flex;
    flex-flow: row;
    margin-top: 20px;
  }

  .product-div{
    width: 50%;
    margin: 5px;
    padding: 5px;
    border-radius: 4px;
    display: flex;
    flex-flow: column;
    box-shadow: 0px 0px 9px 1px #898989;
  }

  .product-img{
    height: 200px;
    /*margin: auto;*/
  }

  select{
    cursor: pointer;
  }

  .icon{
    cursor: pointer;

  }

  .nameDiv{
    position: relative;
  }

  .deleteBtn{
    cursor: pointer; 
    height: 24px; 
    position: absolute;
    right: 0px;
    bottom: 0px;
  }

  .show_data_link {
    cursor: pointer;
    text-decoration: none;
  }

  #show_data_modal_bg_overlay {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(0, 0, 0, 0.2);
    display: flex;
    justify-content: center;
    z-index: 99999;
  }

  #show_data_modal {
    max-width: 90%;
    max-height: 95%;
    margin-top: 15px;
    margin-left: 5%;
    background: whitesmoke;
    overflow: auto;
    z-index: 100000;
  }

  #show_data_modal::-webkit-scrollbar {
    display: none;
  }

  .modal_header {
      position: fixed;
      top: 15px;
      left: 5%;
      width: 90%;
      height: 30px;
      background-color: #e8e8e8;
      color: #838383;
      font-size: 16px;
      font-weight: 400;
      border-bottom: 1px solid #b2b2b2;
      display: flex;
      flex-flow: row;
      justify-content: space-between;
      align-items: center;
  }

  .modal_header label {
    margin: 0 0 0 6px;
  }

  #modal_close_link {
    cursor: pointer;
    text-decoration: none;
    font-size: 26px;
    font-weight: 600;
    margin: 0 6px 0 0;
  }

  .modal_body {
    width: 100%;
    height: 95%;
    margin-top: 30px;
    overflow: auto;
  }

  .modal_body pre {
    margin: 0px;
    padding: 0px;
    border: none;
    border-radius: none;
  }

  .show {
    display: block !important;
  }

  .disable-scroll {
    overflow: hidden;
  }

  .inline_flex {
    display: flex;
    flex-flow: row;
    justify-content: start;
    align-items: center;
  }

  .select_product_control {
    width: 300px;
    margin-right: 10px;
  }

  #add_product_button {
    width: 60px;
    margin: 0;
  }

  #suggested_products_container {
    width: 100%;
    height: 300px;
    margin-top: 15px;
    display: flex;
    flex-wrap: wrap;
    justify-content: start;
    overflow-y: auto;
  }

  #suggested_products_container::-webkit-scrollbar-track {
    width: 8px;
    background: #f5f5f5;
  }

  #suggested_products_container::-webkit-scrollbar-thumb {
    width: 6px;
    background: #e8e8e8;
  }

  .products {
    width: 170px;
    max-height: 200px;
    margin: 0px 10px 10px 0px;
    background: #f2f2f2;
    border-radius: 5px;
    box-shadow: 0px 6px 8px -6px rgba(0, 0, 0, 0.6);
    display: flex;
    flex-flow: column;
    justify-items: center;
    align-content: space-evenly;
  }

  .product-image {
    width: 160px;
    height: 140px;
    margin: 5px;
    background: #fff;
    display: grid;
    place-items: center;
  }

  .product-details {
    width: 160px;
    margin: 0 5px 10px 5px;
    display: flex;
    flex-flow: row;
    justify-content: space-between;
    align-items: center;
  }

  .product-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-left: 6px;
  }

  .product-remove-button {
    margin: 0 6px 0 0;
    cursor: pointer;
  }

  .disabled_button {
    cursor: wait;
  }


</style>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Field Visit Request</h1>
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
                    <option value="V" <?php if($filter_data['status'] == 'V') { ?> selected <?php } ?>>Visited</option>
                    <option value="P" <?php if($filter_data['status'] == 'P') { ?> selected <?php } ?>>Pending</option> 
                    <option value="C" <?php if($filter_data['status'] == 'C') { ?> selected <?php } ?>>Completed</option> 
                    <option value="D" <?php if($filter_data['status'] == 'D') { ?> selected <?php } ?>>Deleted</option> 
                    <!-- <option value="all">All</option>
                    <option value="new">Pending</option>
                    <option value="completed">Completed</option> -->
                  </select>
                </div>

                <div class="form-group col-md-2" <?php if($filter_data['status'] !="V" && $filter_data['status'] !="P" && $filter_data['status'] !="C" && $filter_data['status'] !="D") { ?> style="display: none;" <?php } ?> >
                  <a href="<?php echo base_url('blog'); ?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
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
                          <th style="width: 5%">#</th>
                          <th style="width: 15%">Customer Name</th>
                          <th style="width: 10%">Phone</th>
                          <th style="width: 15%">Address 1</th>
                          <th style="width: 15%">Address 2</th>
                          <th style="width: 10%">State</th>
                          <th style="width: 10%">Pincode</th>
                          <th style="width: 10%">Date</th>
                          <th style="width: 10%">Status</th>
                        
                      </tr>
                </thead>
                <tbody> 
                <?php if(count($request_list))
                {
                  
                  $rc = 0;
                  foreach($request_list as $req_row)
                  { ?>
                    <tr id="RPID<?=$req_row['id']?>">

                      <td style="width:5%"><?=$req_row['id']?></td>

                      <td style="width:15%"><?=$req_row['full_name']?></td>

                      <td style="width:10%"><?=$req_row['phone']?></td>

                      <td style="width:15%"><?=$req_row['address_1']?></td>

                      <td style="width: 15%"><?=$req_row['address_2']?></td>

                      <td style="width: 10%"><?=$req_row['state']?></td>

                      <td style="width: 10%"><?=$req_row['pincode']?></td>

                      <td style="width: 10%"><?=$req_row['req_date']?></td>

                      <td style="width: 10%">
                        <select id="status_<?=$req_row['id']?>" class="form-control ord_status" onchange="return update_status(<?=$req_row['id']?>);">

                            <option value="V" <?php if($req_row['status'] == 'V') { ?> selected <?php } ?>>Visited</option>
                            <option value="P" <?php if($req_row['status'] == 'P') { ?> selected <?php } ?>>Pending</option>
                            <option value="D" <?php if($req_row['status'] == 'D') { ?> selected <?php } ?>>Deleted</option>
                            <option value="C" <?php if($req_row['status'] == 'C') { ?> selected <?php } ?>>Cancelled</option>
                                  
                        </select>
                        <div class="text-center" style="display:none" id="status_loader_<?=$req_row['id']?>">
                          <img src="<?=base_url('assets/dist/img/loader-mini.gif')?>" style="width: 100px;">
                        </div>
                      </td>

                    </tr>
                  <?php 
                  $rc++;
                  }
                }?> 
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




<!-- show data popup modal start-->
<div id="show_data_modal_bg_overlay" style="display:none;">
  <div id="show_data_modal" style="display:none;">
    <div class="modal_header">
      <label></label>
      <a id="modal_close_link" onclick="showDataToggle()">&times;</a>
    </div>
    <div class="modal_body"></div>
  </div>
</div>





<script type="text/javascript">
  function form_submit()
  {
    $("#filter_form").submit();
  }

  var select = document.getElementById('select_product');

  function update_status(id)
  {
    var status_val = $('#status_' + id).val();
    $('#status_loader_'+id).show();
    $(".ord_status").prop('disabled', true);
    var dataString = 'id=' + id + '&status=' + status_val;
      $.ajax({
      type: "POST",
      url: "<?=base_url('Field_visit_request/update_request_status')?>",
      data: dataString,
      cache: false,
      success: function(html) {
        var obj = $.parseJSON(html);
        if(obj.status == "Y")
        {
          
          $('#status_loader_'+id).hide();
          $(".ord_status").prop('disabled', false);
          swal("Success!", "Order status successfully changed.", "success");
          
        }
        else
        {
          
          $('#status_loader_'+id).hide();
          $(".ord_status").prop('disabled', false);
          swal("Failed!", "Order status change failed!", "error");
          

        }
        
        
      }
      });
  }
</script>


