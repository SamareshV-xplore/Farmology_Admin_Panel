<?php
   defined('BASEPATH') OR exit('No direct script access allowed');
   ?>
   <script type="text/javascript">
     window.setInterval(function(){
        location.reload();
      }, 120000);
   </script>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1><b>Welcome to Farmology Admin Panel Dashboard</b></h1>
   </section>
   <!-- Main content -->
    <section class="content">
      <div class="row">
         <div class="content-header">
            <h2 class="page-header">Product Count</h2>
         </div>
         <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
               <div class="inner">
                  <h3><?=$product_count['total_product']?></h3>
                  <p>Total Product</p>
               </div>
               <div class="icon">
                  <i class="fa fa-product-hunt" aria-hidden="true"></i>
               </div>
               <a target="_blank" href="<?=base_url('product')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
         <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
               <div class="inner">
                  <h3><?=$product_count['active_product']?></h3>
                  <p>Active Product</p>
               </div>
               <div class="icon">
                  <i class="fa fa-product-hunt" aria-hidden="true"></i>
               </div>
               <a target="_blank" href="<?=base_url('product?filter=true&cate1=0&cate2=0&status=Y')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
         <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
               <div class="inner">
                  <h3><?=$product_count['inactive_product']?></h3>
                  <p>Inactive Product</p>
               </div>
               <div class="icon">
                  <i class="fa fa-product-hunt" aria-hidden="true"></i>
               </div>
               <a target="_blank" href="<?=base_url('product?filter=true&cate1=0&cate2=0&status=N')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="content-header">
            <h2 class="page-header">Order Count</h2>
         </div>
         <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
               <div class="inner">
                  <h3><?=$order_count['processing_order']?></h3>
                  <p>Processing Order</p>
               </div>
               <div class="icon">
                  <i class="ion ion-bag"></i>
               </div>
               <a target="_blank" href="<?=base_url('order')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
         <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
               <div class="inner">
                  <h3><?=$order_count['shipping_order']?></h3>
                  <p>Out For Delivery Order</p>
               </div>
               <div class="icon">
                <i class="ion ion-bag"></i>
               </div>
               <a target="_blank" href="<?=base_url('order')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
         <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
               <div class="inner">
                  <h3><?=$order_count['complete_order']?></h3>
                  <p>Completed Order</p>
               </div>
               <div class="icon">
                  <i class="ion ion-bag"></i>
               </div>
               <a target="_blank" href="<?=base_url('order')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
         <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
               <div class="inner">
                  <h3><?=$order_count['cancelled_order']?></h3>
                  <p>Cancelled Order</p>
               </div>
               <div class="icon">
                  <i class="ion ion-bag"></i>
               </div>
               <a target="_blank" href="<?=base_url('order')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
      </div>
   </section>
   <!-- /.content -->
</div>