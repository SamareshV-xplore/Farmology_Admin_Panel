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
   .list-group-item {
      position: relative;
      display: block;
      padding: 10px 15px;
      margin-bottom: -1px;
      background-color: #fff;
      border: 1px solid #ddd;
    }
    .list-group-item:first-child {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }
    .list-group-item:last-child {
        margin-bottom: 0;
        border-bottom-right-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <img src onerror='deleteDuplicateTable()'>
   <?php
      // /print_r($filter_data);
      ?>
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
                  <table id="example2" class="table table-bordered table-striped" style="width: 99%;">
                     <thead>
                        <tr>
                           <th style="display: none;">No</th>
                           <th style="width:20%">Order Details</th>
                           <th style="width:20%">Shipping Details</th>
                           <th style="width:25%">Product Details</th>
                           <th style="width:15%">Price</th>
                           <th style="width:20%">Status</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           
                           $i = 1;
                           if(count($order_list) > 0)
                           {
                             foreach($order_list as $order_row)
                             {
                             ?>
                        <tr>
                           <th style="display: none;"><?=$i?></th>
                           <td style="width:20%">
                              <ul class="list-group">
                                 <li class="list-group-item">ID: <b><?=$order_row['order_no']?></b></li>
                                 <li class="list-group-item"><?=date("dS M, Y h:i A", strtotime($order_row['created_date']))?></li>
                                 <a href="<?=base_url('order-details/'.$order_row['order_no'])?>">
                                   <!-- <a target="_blank" href="<?=base_url('user-edit/'.$order_row['customer_details']['id'])?>"><li class="list-group-item text-center"  style="background: #3c93ff; color:white;">
                                    User: <?php echo $order_row['customer_details']['full_name'] ?>

                                   </li></a> -->
                                    <a target="_blank" href="<?=base_url('vendors/order_details/'.$order_row['id'])?>"><li class="list-group-item" style="background: #3c93ff; color:white;">
                                       <center>View Details</center>
                                       </b>
                                    </li>
                                 </a>
                              </ul>
                           </td>
                           <td style="width:20%">
                              <ul class="list-group">
                                 <li class="list-group-item">
                                    <p>
                                       <?=strtoupper($order_row['address_details']['name'])?>                              
                                       <br>
                                       <?=strtoupper($order_row['address_details']['phone'])?>
                                       <br>
                                       <?=strtoupper($order_row['address_details']['address_1'])?>,
                                       <?php if(!empty($order_row['address_details']['address_2'])){ ?>
                                        <?=strtoupper($order_row['address_details']['address_2'])?>,
                                      <?php } ?>
                                         Landmark - <?=strtoupper($order_row['address_details']['landmark'])?>,<br>  <?=strtoupper($order_row['address_details']['city_name'])?>, <?=strtoupper($order_row['address_details']['state_name'])?>, INDIA - <?=$order_row['address_details']['zip_code']?>
                                    </p>
                                 </li>
                              </ul>
                           </td>
                           <td style="width:25%">
                              <ul class="list-group">
                                 <?php
                                    if(count($order_row['product_details']) > 0)
                                    {
                                      foreach($order_row['product_details'] as $product_row)
                                      {
                                      ?>
                                 <li class="list-group-item">
                                    <p><?=$product_row['variation_details']['product_details']['name']?> <br> <?=$product_row['variation_details']['variation_details']['title']?>&nbsp;<i class="fa fa-times"></i>&nbsp;<?=$product_row['quantity']?>  
                                    </p>
                                 </li>
                                 <?php
                                      }
                                    }
                                    ?>
                              </ul>
                           </td>
                           <td style="width:15%">
                              <ul class="list-group">
                                 <li class="list-group-item">
                                    <p>Subtotal<br>₹<b><?=$order_row['total_price']?></b><br>
                                       Shipping(+)<br>₹<b><?=$order_row['delivery_charge']?></b><br>
                                       Discount(-)<br>₹<b><?=$order_row['discount']?></b><br>
                                       Total<br>₹<b><?=$order_row['order_total']?></b><br>
                                       <?php 
                                       if($order_row['payment_method'] == 'online')
                                       {
                                        echo "<b>Online Payment</b>";
                                       }
                                       else
                                       {
                                        echo "<b>Pay On Delivery (POD)</b>";
                                       }
                                       ?>
                                    </p>
                                 </li>
                              </ul>
                           </td>
                           <td style="width:20%">
                              
                              <ul class="list-group">
                                 <li class="list-group-item"><b>
                                   
                                   <select id="status_<?=$order_row['id']?>" class="form-control ord_status" onchange="return update_status(<?=$order_row['id']?>);">
                                  <option value="NOP" <?php if($order_row['status'] == 'NOP') { ?> selected <?php } ?>>Failed / Order In Process</option>
                                  <option value="P" <?php if($order_row['status'] == 'P') { ?> selected <?php } ?>>Processing</option>
                                  <option value="S" <?php if($order_row['status'] == 'S') { ?> selected <?php } ?>>Shipped</option>
                                  <option value="D" <?php if($order_row['status'] == 'D') { ?> selected <?php } ?>>Delivered</option>
                                  <option value="C" <?php if($order_row['status'] == 'C') { ?> selected <?php } ?>>Cancelled</option>
                                  
                              </select>
                              <div class="text-center" style="display:none" id="status_loader_<?=$order_row['id']?>">
                                <img src="<?=base_url('assets/dist/img/loader-mini.gif')?>" style="width: 100px;">
                              </div>

                                 </b>
                                 </li>
                                 <li class="list-group-item">Delivery Date & Time: <b><br><?=date("dS M, Y", strtotime($order_row['delivery_date']))?> (<?=$order_row['time_slot_details']['time_slot']?>)
                                    </b>
                                 </li>
                                 <a target="_blank" href="<?=$order_row['invoice']?>"><li class="list-group-item" style="background: #3c93ff; color:white;">
                                       <center>View/Download Invoice</center>
                                       </b>
                                    </li>
                                  </a>
                              </ul>
                              
                           </td>
                        </tr>
                        <?php
                           $i++;
                           }
                           }
                           else
                           {
                           ?>
                        <tr>
                           <td colspan="5">
                              <center>No Order Found.</center>
                           </td>
                        </tr>
                        <?php
                           }
                           ?>
                     </tbody>
                  </table>
                </a>
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
  $(document).ready(function(){
    
  });

  // function export_order()
  //  {    
  //     var date_range = document.getElementById('hidden-custom-date').value;
  //     var order_status = document.getElementById('hidden-order-status').value;
      
  //       swal({
  //           title: "Success",
  //           text: "Excel file successfully created.",
  //           icon: "success",
  //           button: "Ok",
  //         });
  //       window.location.href = "<?php echo base_url('order/export_order?'); ?>date-range=" + date_range + "&status=" + order_status;    
      
  //  }

   //-----------------------------------------------
  function update_status(id)
  {
    var status_val = $('#status_' + id).val();
    $('#status_loader_'+id).show();
    $(".ord_status").prop('disabled', true);
    var dataString = 'id=' + id + '&status=' + status_val;
      $.ajax({
      type: "POST",
      url: "<?=base_url('order/update_order_status')?>",
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
   // function search_type_change()
   // {
   //   $(".manual-date-cls").hide();
   //   $("#reset_btn_div").show();
     
   
   //   var search_type = $("#search-type").val();
   //   if(search_type == 'manual-date')
   //   {
   //     $(".manual-date-cls").show();
   //     $("#reset_btn_div").show();
   //   }
   //   else if(search_type == 'today-delivery')
   //   {
   //     $("#filter_form").submit();
   //   }
   //   else
   //   {
   //     window.location.href = "<?=base_url('order')?>";
   //   }
   
   //   //alert(search_type);
   // }
   
   // function submit_filter_form()
   // {
   //   $("#filter_form").submit();

   // }
   function deleteDuplicateTable () {
      if (document.getElementsByTagName('table').length > 1) {
        document.getElementsByTagName('table')[1].remove();
      }
      else{
        $('#example2').DataTable( {
          // paging: false
        });
      }
   }
</script>