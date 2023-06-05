
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Download | Invoice</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>dist/css/AdminLTE.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body>
<div class="wrapper">
    <!-- Main content -->
    <section class="invoice">
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
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
