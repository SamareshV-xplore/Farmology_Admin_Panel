<style type="text/css">
  .required_cls
  {
    color: red;
  }
  .variation_count_id
  {
      width: 20%;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Order Details</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">User Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('order_management/order_update') ?>" id="order-form" enctype="multipart/form-data">
              <input type="hidden" name="order_id" value="<?=$order_details['id']?>">
              <div class="box-body">
               
                <div class="form-group col-md-6">
                  <label for="first_name">Name</label>
                  <input type="text" class="form-control" id="title" name="title" placeholder="Title"  value="<?php echo $order_details['customer_name']; ?>" disabled>
                </div>
                <div class="form-group col-md-6">
                  <label for="first_name">Email</label>
                  <input type="text" class="form-control" id="image" name="image" placeholder="Order Date" value="<?php echo $order_details['email']; ?>" disabled>
                </div>
                  <div class="form-group col-md-6">
                      <label for="employee_id">Mobile</label>
                      <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="255" value="<?php echo $order_details['phone']; ?>" disabled>
                  </div>
                  <div class="form-group col-md-3">
                      <label for="employee_id">State</label>
                      <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="255" value="<?php echo $order_details['state_name']; ?>" disabled>
                  </div>
                  <div class="form-group col-md-3">
                      <label for="employee_id">City</label>
                      <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="255" value="<?php echo $order_details['city_name']; ?>" disabled>
                  </div>
              </div>
              <div class="box">
                  <div class="box-header with-border">
                      <h3 class="box-title">Product List</h3>
                  </div>
                  <div class="box-body">

                      <table id="example2" class="table table-bordered table-striped">
                          <thead>
                          <tr>
                              <th style="display: none;">Sl. No</th>
                              <th>Product Image</th>
                              <th>Product Name</th>
                              <th class="variation_count_id">Quantity</th>
                              <th>Unit Price</th>
                              <th>Product Total Price</th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php
                          if(count($order_details['products']) > 0)
                          {
                              $slno = 1;
                              foreach($order_details['products'] as $list)
                              {

                                  ?>
                                  <tr>
                                      <td  style="display: none;"><?php echo $slno; ?></td>
                                      <td><img src="<?=FRONT_URL?><?php echo $list['product_image']; ?>" width="100px" height="100px"></td>
                                      <td><?php echo $list['title']; ?></td>
                                      <td><?php echo $list['variation_title']. ' <b>x</b> ' .$list['quantity']; ?></td>
                                      <td><?php echo $list['unit_price']; ?></td>
                                      <td><?php echo $list['total_price']; ?></td>
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
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Order Details</h3>
                    </div>
                    <div class="box-body">

                        <div class="form-group col-md-6">
                            <label for="first_name">Order Number</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Title"  value="<?php echo $order_details['order_no']; ?>" disabled>
                            <input type="hidden" class="form-control" name="order_no" value="<?php echo $order_details['order_no']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="first_name">Order Date</label>
                            <input type="text" class="form-control" id="image" name="image" placeholder="Order Date" value="<?php echo $order_details['created_date']; ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="employee_id">Total Amount</label>
                            <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="100" value="<?php echo $order_details['total_price']; ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="employee_id">Delivery Charge</label>
                            <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="100" value="<?php echo $order_details['delivery_charge'].".00"; ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="employee_id">Discount Amount(-)</label>
                            <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="100" value="<?php echo $order_details['discount']; ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="employee_id">Grand Total</label>
                            <input type="text" class="form-control" id="link" name="link" placeholder="Link" maxlength="100" value="<?php echo $order_details['order_total']; ?>" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="employee_id">Delivery Address</label>
                            <textarea name="address" id="address" class="form-control" disabled>
                                <?php
                                    echo $order_details['address1'].', '.
                                        $order_details['address2'].', '.
                                        $order_details['landmark'].', '.
                                        $order_details['city_name'].', '.
                                        $order_details['state_name'].', Pin: '.
                                        $order_details['zip_code'];
                                ?>
                            </textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="blood_group">Status<span class="required_cls">*</span></label>
                            <select class="form-control" name="status" id="status">
                                <option value="P" <?php if($order_details['status'] == 'P') { ?> selected <?php } ?>>Processing</option>
                                <option value="S" <?php if($order_details['status'] == 'S') { ?> selected <?php } ?>>Shipped</option>
                                <option value="D" <?php if($order_details['status'] == 'D') { ?> selected <?php } ?>>Delivered</option>
                                <option value="C" <?php if($order_details['status'] == 'C') { ?> selected <?php } ?>>Cancelled</option>
                                <option value="ONP" <?php if($order_details['status'] == 'ONP') { ?> selected <?php } ?>>Order Not Placed</option>
                            </select>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn btn-primary pull-right" onclick="return edit_order_submit();">Update Order Status</button>

                        <a href="<?php echo base_url('orders-list'); ?>"><button type="button" class="btn btn-primary pull-left">Cancel</button></a>
                    </div>

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

    function edit_order_submit()
    {
      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";

      var status = document.getElementById("status").value;

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
        $("#order-form").submit();
      }

      return false;
    }
  </script>


