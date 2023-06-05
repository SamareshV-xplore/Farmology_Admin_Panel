<style type="text/css">
  .page-header{
    padding-left: 10px;
  }
  .sub-head{
    font-weight: 600;
  }
</style>
<section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h5 class="page-header"> ORDER NO: <?=$order_details['order_no']?><hr style="background-color: #3c93ff;">
          </h5>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          <span class="sub-head">Delivery Address Details:</span>
          <address>
         <p><b>Name: </b><?=$order_details['address_details']['name']?><br><b>Phone:</b> <?=$order_details['address_details']['phone']?><br><b>Address:</b><br>
                  <?=$order_details['address_details']['address_1']?>,
                  <?php if(!empty($order_details['address_details']['address_2'])){ ?>
                   <?=$order_details['address_details']['address_2']?>, 
                   <?php } ?>
                   Landmark - <?=$order_details['address_details']['landmark']?> <br><?=$order_details['address_details']['city_name']?>, <?=$order_details['address_details']['state_name']?> - <?=$order_details['address_details']['zip_code']?>, India
               </p>
             </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
         <span class="sub-head">Account Holder:</span>
          <address>
            <strong>Name:</strong> <?=$order_details['customer_details']['full_name']?><br>
            <strong>Email:</strong> <?=$order_details['customer_details']['email']?><br>
            <strong>Phone:</strong> <?=$order_details['customer_details']['phone']?><br>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <b>Order NO:</b><?=$order_details['order_no']?>
          <br>
          <b>Order Date:</b><?=date("dS M, Y h:i A", strtotime($order_details['created_date']))?>
          <br>
          <b>Order Total:</b><i class="fa fa-inr"></i> <?=$order_details['order_total']?>
          <br>
           <b>Payment method:</b>
                           <?php
                           if($order_details['payment_method'] == 'online')
                           {
                              ?>
                              Online Payment Done.<br>
                              <b>Transaction ID:</b> <?=$order_details['transaction_id']?>
                              <?php
                           }
                           else
                           {
                              ?>
                              Pay On Delivery (POD)
                              <?php
                           }
                           if(count($order_details['promo_code_details']) > 0)
                           {
                            ?>
                            <br><b>Promo code: </b><?=$order_details['promo_code_details']['promo_code']?>

                            <?php
                           }  


                           ?>
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              
              <th>Product</th>
              <th>Variation</th>
              <th>Unit Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
              <?php
              foreach($order_details['product_details'] as $product_details)
              {
                ?>

                <tr>              
                <td><?=$product_details['variation_details']['product_details']['name']?></td>
                <td><?=$product_details['variation_details']['variation_details']['title']?></td>
                <td><?=$product_details['unit_price']?></td>
                
                <td><?=$product_details['quantity']?></td>
                <td><?=$product_details['total_price']?></td>
              </tr>

                <?php
              }

              ?>

            
            
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-sm-6">
          <div class="form-group col-md-6">
                  <label for="page">Payment Method</label>
                  <select class="form-control" name="payment_method" id="payment_method">
                      <option value="cod" <?php if($order_details['payment_method'] == 'cod') { ?> selected="selected" <?php } ?> >Pay On Delivery (POD)</option>
                      <option value="online" <?php if($order_details['payment_method'] == 'online') { ?> selected="selected" <?php } ?> >Online Payment</option>                                            
                  </select>
          </div>

          <div class="form-group col-md-6">
                  <label for="page">Order Status</label>
                  <select class="form-control" name="ord_status" id="ord_status">
                    <option value="NOP" <?php if($order_details['status'] == 'NOP') { ?> selected="selected" <?php } ?>>Order In Process / Failed</option>
                    <option value="P" <?php if($order_details['status'] == 'P') { ?> selected="selected" <?php } ?> >Processing</option>
                    <option value="S" <?php if($order_details['status'] == 'S') { ?> selected="selected" <?php } ?> >Shipped</option>
                    <option value="D" <?php if($order_details['status'] == 'D') { ?> selected="selected" <?php } ?>  >Delivered</option>
                    <option value="C" <?php if($order_details['status'] == 'C') { ?> selected="selected" <?php } ?> >Cancelled</option>
                                            
                  </select>
          </div>
          <div class="form-group col-md-12">
                 <button type="button" class="btn btn-primary float-right" onclick="return update_order_status(<?=$order_details['id']?>);" id="update_btn">Update</button>
                 <br>
                 <div class="text-center" style="display:none" id="status_loader">
                                <img src="<?=base_url('assets/dist/img/loader-mini.gif')?>" style="width: 100px;">
                              </div>
          </div>

        </div>
        <!-- /.col -->
        <div class="col-sm-6">
          <p class="lead">Delivery Date & Time: <?=date("dS M, Y", strtotime($order_details['delivery_date']))?> (<?=$order_details['time_slot_details']['time_slot']?>)</p>

          <div class="table-responsive">
            <table class="table">
              <tr>
                <th style="width:50%">Subtotal</th>
                <td class="pull-right"><i class="fa fa-inr"></i><?=$order_details['total_price']?></td>
              </tr>
              <tr>
                <th>Shipping Charge<small>(+)</small></th>
                <td class="pull-right"><i class="fa fa-inr"></i><?=$order_details['delivery_charge']?>.00</td>
              </tr>
              <tr>
                <th>Promo Discount<small>(-)</small></th>
                <td class="pull-right"><i class="fa fa-inr"></i><?=$order_details['discount']?></td>
              </tr>
              <tr>
                <th>Order Total:</th>
                <td class="pull-right"><i class="fa fa-inr"></i><?=$order_details['order_total']?></td>
              </tr>
            </table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12" style="display:none;">
          <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
          <button type="button" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment
          </button>
          <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px;">
            <i class="fa fa-download"></i> Generate PDF
          </button>
        </div>
      </div>
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
  </div>

  <script type="text/javascript">
    function update_order_status(id)
    {
      var status_val = $('#ord_status').val();
      var payment_method = $('#payment_method').val();
      $('#status_loader').show();
      $(".update_btn").prop('disabled', true);
      var dataString = 'id=' + id + '&status=' + status_val + '&payment_method=' + payment_method;
      $.ajax({
      type: "POST",
      url: "<?=base_url('order/update_order_details')?>",
      data: dataString,
      cache: false,
      success: function(html) {
        location.reload();
      }
      });

    }
  </script>
