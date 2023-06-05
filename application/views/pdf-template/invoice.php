<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>pdf-assets/style.css" media="all" />

  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img width="150px" height="auto" src="<?=FRONT_URL?>uploads/default/logo.jpg">
      </div>
      <h1>INVOICE</h1>
      <div id="company" class="clearfix">
        <div>Farmology Services Pvt. Ltd</div>
        <!-- <div>Noida Sector 51/12, India</div> -->
        <!--<div>(602) 519-0450</div>-->
        <div><a href="mailto:sales@farmology.com">sales@farmology.com</a></div>
      </div>
      <div id="project">
        <div><span>INVOICE NO:</span> <?php echo str_replace("FT","",$order_details['order_no']); ?></div>
        <div><span>ORDER ID:</span> <?php echo $order_details['order_no']; ?></div>
        <div><span>ORDER DATE:</span> <?=date("d/m/Y", strtotime($order_details['created_date']))?></div>
        <div><span>DELIVERY DATE:</span> <?=date("d/m/Y", strtotime($order_details['delivery_date']))?>(<?=$order_details['time_slot_details']['time_slot']?>)</div>
        
        <?php 
        if($order_details['payment_method'] == 'online')
        {
          $payment_method_text = "Online Payment.";
        }
        else
        {
          $payment_method_text = "Pay On Delivery(POD)<br>";
          ?>
          <div><span>PAYABLE AMOUNT:(INR)</span> <?=$order_details['order_total']?></div>
          <?php

        }

        ?>
        <div><span>PAYMENT METHOD:</span> <?=$payment_method_text?></div>
        <div><span>SHIPPING DETAILS:</span></div> 
        <div>Name: <?=strtoupper($order_details['address_details']['name'])?></div>
        <div>Phone: <?="+91-".$order_details['address_details']['phone']?></div>
        <div>Address: <br>
          <?=strtoupper($order_details['address_details']['address_1'])?>, <br>
          <?php if(!empty($order_details['address_details']['address_2'])){ ?><?=strtoupper($order_details['address_details']['address_2'])?>,<?php } ?> Near <?=strtoupper($order_details['address_details']['landmark'])?>, <br> 
          <?=strtoupper($order_details['address_details']['city_name'])?>, <?=strtoupper($order_details['address_details']['state_name'])?> - Zip Code: <?=strtoupper($order_details['address_details']['zip_code'])?>
        </div>
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <!--<th class="service">SERVICE</th>-->
            <th class="desc">DESCRIPTION</th>
            <th>PRICE</th>
            <th>QTY</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach($order_details['product_details'] as $product_row)
          {
          ?>
          <tr>
            <!--<td class="service">Design</td>-->
            <td class="desc"><?=$product_row['variation_details']['product_details']['name']?> - <?=$product_row['variation_details']['variation_details']['title']?></td>
            <td class="unit"><?=$product_row['unit_price']?></td>
            <td class="qty"><?=$product_row['quantity']?></td>
            <td class="total"><i class="fa fa-inr" aria-hidden="true"></i><?=$product_row['total_price']?></td>
          </tr>

          <?php
          } 
          ?>
          
          <tr>
            <td colspan="3">SUBTOTAL</td>
            <td class="total"><?=$order_details['total_price']?></td>
          </tr>
          <tr>
            <td colspan="3">SHIPPING CHARGE<small>(+)</small></td>
            <td class="total"><?=$order_details['delivery_charge']?>.00</td>
          </tr>
          <tr>
            <td colspan="3">PROMO DISCOUNT<small>(-)</small></td>
            <td class="total"><?=$order_details['discount']?></td>
          </tr>
          
          <tr>
            <td colspan="3" class="grand total">GRAND TOTAL(INR)</td>
            <td class="grand total"><?=$order_details['order_total']?></td>
          </tr>
        </tbody>
      </table>
      <!--<div id="notices">
        <div>NOTICE:</div>
        <div class="notice">Lorem Ipsum is simply dummy text of the printing and typesetting industry.
      </div>
    </div>-->
    </main>
    <footer>
      <center><b><h1 style="color:black; font-size: 35px">Thank You for buying at Farmology. We hope to see
you back very soon!</h1></b></center>
    </footer>
  </body>
</html>