<style type="text/css">
   .required_cls {
      color: red;
   }

   .reset_btn {
      margin-top: 24px;
   }

   .high_label {
      font-size: 12px;
   }
   
   .action_area_td{
      width: 13%;
   }

   .sl_margin {
      margin-right: 5px;
   }

   .btn:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    .modal-header {
        text-align: center;
        padding: 8px;
    }

    .modal-header h5 {
        font-size: 18px;
        font-weight: 600;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Order List</h1>
   </section>
   <?php
      // /print_r($filter_data);
      ?>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-body">
                  <form method="POST" action="<?=base_url('order')?>" id="filter_form">
                     <input type="hidden" name="filter" value="true">
                     <div class="form-group col-md-3">
                        <label for="search-type">Filter by </label>
                        <select name="search-type" id="search-type" class="form-control" onchange="return search_type_change();">
                           <option value="detault" <?php if($filter_data['search-type'] == 'detault') { ?> selected <?php } ?>>None - Last 30 Day's Order List</option>
                           <option value="manual-date" <?php if($filter_data['search-type'] == 'manual-date') { ?> selected <?php } ?> >Manual Date Renge</option>
                           <option value="today-delivery" <?php if($filter_data['search-type'] == 'today-delivery') { ?> selected <?php } ?> >Today's Delivery</option>
                        </select>
                     </div>
                     <div class="form-group col-md-3 manual-date-cls"  <?php if($filter_data['search-type'] != 'manual-date') { ?> style="display: none;" <?php } ?> >
                        <label for="official_email">Select Custom Date</label>
                        <div class="input-group">
                           <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                           </div>
                           <input type="text" class="form-control pull-right" id="reservation" name="custom-date" value="<?=$filter_data['custom-date']?>" >
                           <input type="hidden" id="hidden-custom-date" value="<?=$filter_data['custom-date']?>">
                        </div>
                     </div>
                     <div class="form-group col-md-3 manual-date-cls" <?php if($filter_data['search-type'] != 'manual-date') { ?> style="display: none;" <?php } ?> >
                        <label for="order_status" >Order Status</label>
                        <input type="hidden" id="hidden-order-status" value="<?=$filter_data['order-status']?>">
                        <select id="order-status" class="form-control ord_status" name="order-status">
                           <option value="all" <?php if($filter_data['order-status'] == 'all') { ?> selected <?php } ?> >All Status (Except Failed Order)</option>
                           <option value="NOP" <?php if($filter_data['order-status'] == 'NOP') { ?> selected <?php } ?> >Failed / Order In Process</option>
                           <option value="P" <?php if($filter_data['order-status'] == 'P') { ?> selected <?php } ?> >Processing Order</option>
                           <option value="S" <?php if($filter_data['order-status'] == 'S') { ?> selected <?php } ?> >Out for Delivery</option>
                           <option value="D" <?php if($filter_data['order-status'] == 'D') { ?> selected <?php } ?>>Completed Order</option>
                           <option value="C" <?php if($filter_data['order-status'] == 'C') { ?> selected <?php } ?>>Cancelled Order</option>
                        </select>
                     </div>
                     <div class="form-group col-md-1 manual-date-cls" <?php if($filter_data['search-type'] != 'manual-date') { ?> style="display: none;" <?php } ?>  >
                        <button type="button" class="btn btn-block btn-primary reset_btn " onclick="return submit_filter_form();">Filter</button>
                     </div>
                     <div class="form-group col-md-1" id="reset_btn_div" <?php if($filter_data['filter'] == false) { ?> style="display: none;" <?php } ?> >
                        <a href="<?=base_url('order')?>"><button type="button" class="btn btn-block btn-primary reset_btn ">Reset</button> </a>           
                     </div>
                     <div class="form-group col-md-1" <?php if($export_flag == "N" || count($order_list) == 0) { ?> style="display: none;" <?php } ?>>
                        <button type="button" class="btn btn-primary reset_btn " onclick="return export_order();"  >Export <i class="fa fa-file-excel-o"></i></button>
                     </div>
                     <div class="clearfix"></div>

                     <div class="col-md-12">
                      <p>Note: For export order data in Excel file, please use 'Manual Date Range' filter within 90 days limit.</p>
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
                           <th style="display: none;">No</th>
                           <th style="width:20%">Order Details</th>
                           <th style="width:20%">Shipping Details</th>
                           <th style="width:25%">Product Details</th>
                           <th style="width:15%">Price</th>
                           <th style="width:20%">Status</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $i = 1;
                        if(count($order_list) > 0) {
                        foreach($order_list as $order_row) { ?>
                        <tr>
                           <th style="display: none;"><?=$i?></th>
                           <td style="width:20%">
                              <ul class="list-group">
                                 <li class="list-group-item">ID: <b><?=$order_row['order_no']?></b></li>
                                 <li class="list-group-item"><?=date("dS M, Y h:i A", strtotime($order_row['created_date']))?></li>
                                 <a href="<?=base_url('order-details/'.$order_row['order_no'])?>">
                                   <a target="_blank" href="<?=base_url('user-edit/'.$order_row['customer_details']['id'])?>"><li class="list-group-item text-center"  style="background: #3d8cbc; color:white;">
                                    User: <?php echo $order_row['customer_details']['full_name'] ?>

                                   </li></a>
                                    <a target="_blank" href="<?=base_url('order/details/'.$order_row['id'])?>"><li class="list-group-item" style="background: #3d8cbc; color:white;">
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
                                      <?php }

                                      $district_and_city_text = ""; 
                                      if (!empty($order_row['address_details']['city_name'])) {
                                          $district_and_city_text .= strtoupper($order_row['address_details']['city_name']).", ";
                                      }
                                      if (!empty($order_row['address_details']['district_name'])) {
                                          $district_and_city_text .= strtoupper($order_row['address_details']['district_name']).",";
                                      }?>
                                         Landmark - <?=strtoupper($order_row['address_details']['landmark'])?>,<br>  <?=$district_and_city_text?> INDIA - <?=$order_row['address_details']['zip_code']?>
                                    </p>
                                 </li>
                                 <li id="<?=$order_row["order_no"]?>_driver_details_container" class="list-group-item">
                                 <?php if (!empty($order_row["delivery_driver_details"]->id)) { 
                                 $driver_details = $order_row["delivery_driver_details"]; ?>

                                    <div class="delivery_driver_details" style="margin-bottom:8px;">
                                       <div style="font-size:16px; font-weight:bold; white-space:nowrap;">Assigned Delivery Driver</div>
                                       <hr style="margin:0; padding:0; margin-bottom:8px;"/>
                                       <label style="margin-right:5px;">Name:</label><span><?=$driver_details->name?></span><br/>
                                       <label style="margin-right:5px;">Phone:</label><span><?=$driver_details->phone?></span>
                                    </div>

                                    <?php if (!empty($order_row["merchant_center_details"]->id)) {
                                    $merchant_center_id = $order_row["merchant_center_details"]->id;
                                    $center_details = $order_row["merchant_center_details"]; ?>

                                    <div class="merchant_center_details" style="margin-bottom:8px;">
                                       <div style="font-size:16px; font-weight:bold; white-space:nowrap;">Assigned Pickup Center</div>
                                       <hr style="margin:0; padding:0; margin-bottom:8px;"/>
                                       <label style="margin-right:5px;">Name:</label><span><?=$center_details->name?></span><br/>
                                       <label style="margin-right:5px;">Phone:</label><span><?=$center_details->phone?></span><br/>
                                       <label style="margin-right:5px;">Address:</label><span><?=$center_details->address?></span>
                                    </div>

                                    <?php } else { $merchant_center_id = ""; } ?>

                                    <button type="button" class="btn btn-primary" onclick="change_delivery_driver('<?=$order_row['order_no']?>', '<?=$driver_details->id?>', '<?=$merchant_center_id?>')" style="width:100%;">Change Delivery Driver</button>

                                 <?php } else { ?>

                                    <button type="button" class="btn btn-primary" onclick="assign_delivery_driver('<?=$order_row['order_no']?>')" style="width:100%;">Assign Delivery Driver</button>

                                 <?php } ?>
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
                                    <p>Subtotal<br><i class="fa fa-inr"></i><b><?=$order_row['total_price']?></b><br>
                                       Shipping(+)<br><i class="fa fa-inr"></i><b><?=$order_row['delivery_charge']?></b><br>
                                       Discount(-)<br><i class="fa fa-inr"></i><b><?=$order_row['discount']?></b>
                                       Total<br><i class="fa fa-inr"></i><b><?=$order_row['order_total']?></b><br>
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
                                 <a target="_blank" href="<?=$order_row['invoice']?>"><li class="list-group-item" style="background: #3d8cbc; color:white;">
                                       <center>View/Download Invoice</center>
                                       </b>
                                    </li>
                                  </a>
                              </ul>
                           </td>
                        </tr>
                        <?php $i++; }} else { ?>
                        <tr>
                           <td colspan="5">
                              <center>No Order Found.</center>
                           </td>
                        </tr>
                        <?php } ?>
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


<!-- =========================== -->
<!-- Delivery Driver Modal Start -->
<!-- =========================== -->
<div id="delivery_driver_modal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="delivery_driver_modal_title" class="modal-title">Assign Delivery Driver</h5>
      </div>
      <div class="modal-body" style="padding:10px;">
      <form id="delivery_driver_modal_form">
         <input type="hidden" id="delivery_driver_modal_form_type" value="assign"/>
        <div class="form-group">
          <label for="order_no">Order ID</label>
          <input type="text" name="order_no" id="order_no" class="form-control plain-text" readonly required/>
        </div>
        <div class="form-group">
          <label for="delivery_driver_id">Delivery Driver</label>
          <select name="delivery_driver_id" id="delivery_driver_id" class="form-control" onchange="toggle_assign_button()" required></select>
        </div>
        <div class="form-group">
          <label for="merchant_id">Pickup Center</label>
          <select name="merchant_id" id="merchant_id" class="form-control" onchange="toggle_assign_button()" required>
          </select>
        </div>
        <div style="display:flex; flex-flow:row; justify-content:end;">
          <button id="delivery_driver_modal_submit_button" class="btn btn-primary" style="margin-right:8px;" disabled="disabled">Assign</button>
          <button type="button" class="btn btn-secondary" onclick="close_delivery_driver_modal()">Close</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
<!-- ========================= -->
<!-- Delivery Driver Modal End -->
<!-- ========================= -->

<!-- =================================== -->
<!-- Assign Delivery Driver Script Start -->
<!-- =================================== -->
<script>

    function assign_delivery_driver(order_id)
    {
        $("#delivery_driver_modal_form")[0].reset();
        $("#order_no").val(order_id);
        get_delivery_drivers_list(order_id);
        get_merchant_centers_list(order_id);
        $("#delivery_driver_modal_title").text("Assign Delivery Driver");
        $("#delivery_driver_modal_submit_button").text("Assign");
        $("#delivery_driver_modal_form_type").val("assign");
        $("#delivery_driver_modal").modal("show");
    }

    $("#delivery_driver_modal_form").submit(function(e){
      e.preventDefault();
      var form = document.getElementById("delivery_driver_modal_form");
      var formData = new FormData(form);
      var order_no = $("#order_no").val();

      var form_type = $("#delivery_driver_modal_form_type").val();
      if (form_type == "change")
      {
         var plain_text = "Change";
         var loading_text = "Changing...";
      }
      else
      {
         var plain_text = "Assign";
         var loading_text = "Assigning...";
      }

      $.ajax({
         url: "<?=base_url("assign-delivery-driver")?>",
         type: "POST",
         data: formData,
         contentType: false,
         processData: false,
         beforeSend: function()
         {
            $("#delivery_driver_modal_submit_button").attr("disabled", "disabled");
            $("#delivery_driver_modal_submit_button").text(loading_text);
         },
         complete: function()
         {
            $("#delivery_driver_modal_submit_button").text(plain_text);
            $("#delivery_driver_modal_submit_button").removeAttr("disabled");
         },
         error: function(a, b, c)
         {
            toast("Something went wrong! Please try again later.");
            console.log(a);
            console.log(b);
            console.log(c);
         },
         success: function(response)
         {
            if (response.success == true)
            {
               toast(response.message, 1200);
               render_delivery_driver_details(order_no, response.delivery_driver_details, response.merchant_center_details);
               close_delivery_driver_modal();
               if (response.hasOwnProperty("delivery_driver_and_customer_details"))
               {
                  console.log("Delivery Driver and Customer Details: ");
                  console.log(response.delivery_driver_and_customer_details);
               }
            }
            else if (response.success == false)
            {
               toast(response.message, 1500);
               console.log(response.console_message);
            }
            else
            {
               toast("Something went wrong! Please try again later.");
               console.log(response);
            }
         }
      });
    });

    function close_delivery_driver_modal()
    {
        $("#delivery_driver_modal").modal("hide");
    }

    function get_delivery_drivers_list(order_id, delivery_driver_id = null)
    {
      $("#delivery_driver_id").html("<option value=''>Loading...</option>");
      $.ajax({
         url: "<?=base_url('get-delivery-drivers-list/')?>"+order_id,
         type: "GET",
         error: function(a, b, c)
         {
            console.log(a);
            console.log(b);
            console.log(c);
         },
         success: function(response)
         {
            if (response.success == true)
            {
               render_delivery_drivers_list(response.delivery_drivers_list, delivery_driver_id);
            }
            else if (response.success == false)
            {
               toast(response.message, 1500);
            }
            else
            {
               toast("Something went wrong! Please try again later.", 1500);
               console.log(response);
            }
         }
      });
    }
    
    function render_delivery_drivers_list(delivery_drivers_list, delivery_driver_id = null)
    {
      if (Object.keys(delivery_drivers_list).length > 0)
      {
         $("#delivery_driver_id").html("<option value=''>Choose a driver for delivery</option>");
         delivery_drivers_list.forEach((delivery_driver_details, index) => {
            if (delivery_driver_id == delivery_driver_details.id) {
               var option = `<option value="${delivery_driver_details.id}" selected>${delivery_driver_details.name} | ${delivery_driver_details.phone}</option>`;
               $("#delivery_driver_modal_submit_button").removeAttr("disabled");
            } else {
               var option = `<option value="${delivery_driver_details.id}">${delivery_driver_details.name} | ${delivery_driver_details.phone}</option>`;
            }
            $("#delivery_driver_id").append(option);
         });
      }
      else
      {
         $("#delivery_driver_id").html("<option value=''>No delivery drivers found</option>");
      }
    }

    function toggle_assign_button()
    {
      var selected_delivery_driver_id = $("#delivery_driver_id").val();
      var selected_merchant_id = $("#merchant_id").val();
      if (selected_delivery_driver_id != "" && selected_merchant_id != "")
      {
         $("#delivery_driver_modal_submit_button").removeAttr("disabled");
      }
      else
      {
         $("#delivery_driver_modal_submit_button").attr("disabled", "disabled");
      }
    }

    function change_delivery_driver(order_id, delivery_driver_id, merchant_center_id)
    {
      $("#delivery_driver_modal_form")[0].reset();
      $("#order_no").val(order_id);
      get_delivery_drivers_list(order_id, delivery_driver_id);
      get_merchant_centers_list(order_id, merchant_center_id);
      $("#delivery_driver_modal_title").text("Change Delivery Driver");
      $("#delivery_driver_modal_submit_button").text("Change");
      $("#delivery_driver_modal_form_type").val("change");
      $("#delivery_driver_modal").modal("show");
    }

    function render_delivery_driver_details(order_id, driver_details, center_details)
    {
      var driver_details_html = `<div class="delivery_driver_details" style="margin-bottom:8px;">
                                    <div style="font-size:16px; font-weight:bold; white-space:nowrap;">Assigned Delivery Driver</div>
                                    <hr style="margin:0; padding:0; margin-bottom:8px;"/>
                                    <label style="margin-right:5px;">Name:</label><span>${driver_details.name}</span><br/>
                                    <label style="margin-right:5px;">Phone:</label><span>${driver_details.phone}</span>
                                 </div>`;

      var merchant_center_id = "";
      if (Object.keys(center_details).length > 0) 
      {
         if (center_details.id)
         {
            merchant_center_id = center_details.id;
         }

         driver_details_html += `<div class="merchant_center_details" style="margin-bottom:8px;">
                                    <div style="font-size:16px; font-weight:bold; white-space:nowrap;">Assigned Pickup Center</div>
                                    <hr style="margin:0; padding:0; margin-bottom:8px;"/>
                                    <label style="margin-right:5px;">Name:</label><span>${center_details.name}</span><br/>
                                    <label style="margin-right:5px;">Phone:</label><span>${center_details.phone}</span><br/>
                                    <label style="margin-right:5px;">Address:</label><span>${center_details.address}</span>
                                 </div>`;
      }

      driver_details_html += `<button type="button" class="btn btn-primary" onclick="change_delivery_driver('${order_id}', '${driver_details.id}','${merchant_center_id}')" style="width:100%;">Change Delivery Driver</button>`;

      $("#"+order_id+"_driver_details_container").html(driver_details_html);
    }

    function get_merchant_centers_list(order_id, merchant_id = null)
    {
      $("#merchant_id").html("<option value=''>Loading...</option>");
      $.ajax({
         url: "<?=base_url('get-merchant-centers-list/')?>"+order_id,
         type: "GET",
         error: function(a, b, c)
         {
            console.log(a);
            console.log(b);
            console.log(c);
         },
         success: function(response)
         {
            if (response.success == true)
            {
               render_merchant_centers_list(response.merchant_centers_list, merchant_id);
            }
            else if (response.success == false)
            {
               toast(response.message, 1500);
            }
            else
            {
               toast("Something went wrong! Please try again later.", 1500);
               console.log(response);
            }
         }
      });
    }

    function render_merchant_centers_list(merchant_centers_list, merchant_id = null)
    {
      if (Object.keys(merchant_centers_list).length > 0)
      {
         $("#merchant_id").html("<option value=''>Choose a merchant center for pickup</option>");
         merchant_centers_list.forEach((merchant_center_details, index) => {
            if (merchant_id == merchant_center_details.id) {
               var option = `<option value="${merchant_center_details.id}" selected>${merchant_center_details.name} | ${merchant_center_details.phone}</option>`;
               $("#delivery_driver_modal_submit_button").removeAttr("disabled");
            } else {
               var option = `<option value="${merchant_center_details.id}">${merchant_center_details.name} | ${merchant_center_details.phone}</option>`;
            }
            $("#merchant_id").append(option);
         });
      }
      else
      {
         $("#merchant_id").html("<option value=''>No merchant centers found</option>");
      }
    }
</script>
<!-- ================================= -->
<!-- Assign Delivery Driver Script End -->
<!-- ================================= -->


<script type="text/javascript">

  function export_order()
   {    
      var date_range = document.getElementById('hidden-custom-date').value;
      var order_status = document.getElementById('hidden-order-status').value;
      
        swal({
            title: "Success",
            text: "Excel file successfully created.",
            icon: "success",
            button: "Ok",
          });
        window.location.href = "<?php echo base_url('order/export_order?'); ?>date-range=" + date_range + "&status=" + order_status;    
      
   }

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

        console.log("Update Order Status API Response: ");
        console.log(obj);

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
   function search_type_change()
   {
     $(".manual-date-cls").hide();
     $("#reset_btn_div").show();
     
   
     var search_type = $("#search-type").val();
     if(search_type == 'manual-date')
     {
       $(".manual-date-cls").show();
       $("#reset_btn_div").show();
     }
     else if(search_type == 'today-delivery')
     {
       $("#filter_form").submit();
     }
     else
     {
       window.location.href = "<?=base_url('order')?>";
     }
   
     //alert(search_type);
   }
   
   function submit_filter_form()
   {
     $("#filter_form").submit();

   }
</script>