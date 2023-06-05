<style>
    .variation_count_id
    {
        width: 20%;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Invoice
            <small><?php echo $invoice; ?></small>
        </h1>
    </section>

    <div class="pad margin no-print">
        <div class="callout callout-info" style="margin-bottom: 0!important;">
            <h4><i class="fa fa-info"></i> Note:</h4>
            This page has been enhanced for printing. Click the print button at the bottom of the invoice to proceed.
        </div>
    </div>

    <!-- Main content -->
    <section class="invoice" id="print_invoice">
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <img src="<?= FRONT_URL.'/uploads/default/logo.jpg'?>" width="100px" height="50px" class="img-responsive">
                    <small class="pull-right"><?php echo date('d/m/Y') ?></small>
                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                From
                <address>
                    <strong>Flesh Kart</strong><br>
                    Kolkata, India<br>
                    Phone: (824) 045-4838<br>
                    Email: info@fleshkart.com
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                To
                <address>
                    <strong><?php echo $order_details['customer_name']; ?></strong><br>
                    <?php echo $order_details['address1'].','.$order_details['address2']; ?><br>
                    <?php echo $order_details['city_name'].','.$order_details['state_name'].' '.$order_details['zip_code']; ?><br>
                    Phone: <?php echo $order_details['phone']; ?><br>
                    Email: <?php echo $order_details['email']; ?>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Invoice <?php echo $invoice; ?></b><br>
                <br>
                <b>Order ID:</b> <?php echo $order_details['order_no']; ?><br>
                <b>Payment Due:</b> <?php echo ($order_details['payment_method'] == 'cod') ? $order_details['order_total'] : 'Paid Online'; ?><br>
                <!--<b>Account:</b> 968-34567-->
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
                        <th>SKU #</th>
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
                                    <td><?php echo $list['title']; ?></td>
                                    <td><?php echo $list['sku']; ?></td>
                                    <td><?php echo $list['variation_title']. ' <b>x</b> ' .$list['quantity'] ?></td>
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
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">
                <p class="lead">Payment Methods:</p>
                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                    <?php echo ($order_details['payment_method'] == 'cod') ? 'Cash On Delivery' : 'Online Payment'; ?><br>
                </p>
            </div>
            <!-- /.col -->
            <div class="col-xs-6">
                <p class="lead">Bill Details</p>

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:50%">Subtotal:</th>
                            <td>₹<?php echo $order_details['total_price']; ?></td>
                        </tr>
                        <!--<tr>
                            <th>Tax (9.3%)</th>
                            <td>$10.34</td>
                        </tr>-->
                        <tr>
                            <th>Discount:</th>
                            <td>₹<?php echo $order_details['discount'] ?></td>
                        </tr>
                        <tr>
                            <th>Shipping Charges:</th>
                            <td>₹<?php echo $order_details['delivery_charge'].'.00' ?></td>
                        </tr>
                        <tr>
                            <th>Total:</th>
                            <td>₹<?php echo $order_details['order_total']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-xs-12">
                <button class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
                <a href="<?php echo base_url('orders-list'); ?>" class="btn btn-success pull-right"><i class="fa fa-arrow-left"></i> Back</a>
                <a href="<?php echo base_url('download-invoice/'.$order_details['id']); ?>" class="btn btn-primary pull-right" style="margin-right: 5px;">
                    <i class="fa fa-download"></i> Generate PDF
                </a>
            </div>
        </div>
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
</div>

<script>
    /*function printBill() {
        window.print();
    }*/
</script>
