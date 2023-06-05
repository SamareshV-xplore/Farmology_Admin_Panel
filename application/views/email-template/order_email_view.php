<?php
/*echo "<pre>";
print_r($order_details);
echo "</pre>";*/
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css" rel="stylesheet" media="all">
    /* Base ------------------------------ */
    
    @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");
    body {
      width: 100% !important;
      height: 100%;
      margin: 0;
      -webkit-text-size-adjust: none;
    }
<title>Surobhi Agro Industries Pvt. Ltd.</title>

<style>

.email_table {
  color: #333;
  font-family: sans-serif;
  font-size: 15px;
  font-weight: 300;
  text-align: center;
  border-collapse: separate;
  border-spacing: 0;
  width: 99%;
  margin: 6px auto;
  box-shadow:none;
}
table {
  color: #333;
  font-family: sans-serif;
  font-size: 15px;
  font-weight: 300;
  text-align: center;
  border-collapse: separate;
  border-spacing: 0;
  width: 99%;
  margin: 50px auto;
  box-shadow: 0 4px 8px 0 rgba(0,0,0,.16);
}

th {font-weight: bold; padding:10px; border-bottom:2px solid #000;}

tbody td {border-bottom: 1px solid #ddd; padding:10px;}



.email_main_div{width:700px; margin:auto; background-color:#EEEEEE; min-height:500px; border:2px groove #999999;}
strong{font-weight:bold;}
.item_table{text-align:left;}
</style>
</head>

<body>
<!--<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-67816487-1', 'auto');
  ga('send', 'pageview');

</script>-->

<div class="email_main_div">
<table width="100%">
	<tr>
		<td  width="800px" style="text-align:center; padding:10px;">
			<center><img src="<?php echo FRONT_URL.'uploads/default/logo.jpg'; ?>" width="250" height="100px" /></center>
		</td>
		
	</tr>
</table>
<?php
  if($status == "P")
  {
    ?>
    <table  width="100%" class="email_table">
  <tr>
    <td  width="800px" style="text-align:center; padding:10px; background: #256525; color: white;">
      <h1>Your order is successfully Placed.</h1>      
    </td>    
  </tr>
</table>

    <?php
  }
  else if($status == "D")
  {
    ?>
    <table  width="100%" class="email_table">
    <tr>
      <td width="800px" style="text-align:center; padding:10px; background: #256525; color: white;">
        <h1>Your order is completed.</h1>      
      </td>    
    </tr>
  </table>

    <?php
  }
  else if($status == "C")
  {
    ?>
    <table  width="100%" class="email_table">
    <tr>
      <td width="800px;" style="text-align:center; padding:10px; background: #b92b24; color: white;">
        <h1>Your order has been cancelled.</h1>      
      </td>    
    </tr>
  </table>

    <?php
  }
?>

<?php
  if($order_details['payment_method'] == "online")
  {
    $payment_method_text = "Online Payment";
  }
  else
  {
    $payment_method_text = "Pay On Delivery (POD)";
  }
?>
<table  width="100%" style="margin-top:14px; border:1px solid #ccc;">
	<tr>
		<td width="50%" style="text-align:left; padding:10px; border: solid 2px #ccc;">
		<b>Order No: </b><?=$order_details['order_no']?><br>
    <b>Order Date: </b><?=date("dS M, Y h:i A", strtotime($order_details['created_date']))?><br>

    <b>Delivery Date: </b><?=date("dS M, Y", strtotime($order_details['delivery_date']))?> (<?=$order_details['time_slot_details']['time_slot']?>)<br>
    <b>Total: </b><?=$order_details['order_total']?> (INR)<br>
    <b>Payment Method: </b><?=$payment_method_text?>

		</td>
		<td  width="50%" style="text-align:left; padding:10px; border: solid 2px #ccc;">
		<strong>Billing & Shipping Address :</strong><br />
		<b>Name:</b> <?=$order_details['address_details']['name']?></br>
               <b>Phone:</b> <?=$order_details['address_details']['phone']?><br>
                  <b>Address:</b> <?=$order_details['address_details']['address_1']?>, <?=$order_details['address_details']['address_2']?>, near <?=$order_details['address_details']['landmark']?>, <br><?=$order_details['address_details']['city_name']?>, <?=$order_details['address_details']['state_name']?> - <?=$order_details['address_details']['zip_code']?>, India
		</td>
	</tr>
</table>

  <center><h2>Order Details</h2></center>


<table width="100%" style="border: 1px  solid #ccc ">
  <thead>
    <tr style="background: #cccccc4f;">
      <th width="50%" style="border:1px solid #ccc;">Product</th>
      <th  width="50%" style="text-align:right; border:1px solid #ccc;">Subtotal</th>
	  
    </tr>
  </thead>
  <tbody>
    <?php
       foreach($order_details['product_details'] as $products)
       {
          ?>
    <tr>
      <td  width="50%" style="border:1px solid #ccc;">
        <img src="<?=$products['variation_details']['product_details']['image'][0]['image']?>" width="50">
                              <?=$products['variation_details']['product_details']['name']?> – <?=$products['variation_details']['variation_details']['title']?> <strong>x <?=$products['quantity']?></strong>

      </td>
      <td  width="50%" style="text-align:right; border:1px solid #ccc;"><?=$products['total_price']?></td>      
    </tr>
    <?php
      }
      ?>
      <tr>
        <td style="border:1px solid #ccc;"  width="50%">
          <b>Subtotal </b>
        </td>
        <td  width="50%" style="text-align:right; border:1px solid #ccc;"><?=$order_details['total_price']?></td> 
      </tr>

      <tr>
        <td  width="50%" style="border:1px solid #ccc;">
          <b>Shipping Charge </b>(+)
        </td>
        <td  width="50%" style="text-align:right; border:1px solid #ccc;"><?=$order_details['delivery_charge']?>.00</td> 
      </tr>

      <tr>
        <td  width="50%" style="border:1px solid #ccc;">
          <b>Promo Discount </b>(-)
        </td>
        <td  width="50%" style="text-align:right; border:1px solid #ccc;"><?=$order_details['discount']?></td> 
      </tr>

      <tr>
        <td  width="50%" style="border:1px solid #ccc;">
          <b>Order Total </b>
        </td>
        <td  width="50%" style="text-align:right; border:1px solid #ccc;">(INR)<b> <?=$order_details['order_total']?></b></td> 
      </tr>
    
	
  </tbody>
</table>
<div style="width:100%; padding:1%; margin-top:10px; font-size:15px; text-align:center;">
Surobhi Agro Industries Pvt. Ltd.
</div>
</div>
</body>
</html>
