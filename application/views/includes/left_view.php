<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
if(!isset($navigation))
{
  echo $navigation = "dashboard";
}
if(!isset($sub_navigation))
{
  echo $sub_navigation = "none";
}
?>
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo $admin_details['profile_image']; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo ucwords(strtolower($admin_details['name'])); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="<?php if($navigation == 'dashboard') { ?>active<?php } ?>">
          <a href="<?php echo base_url(''); ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>            
          </a>          
        </li>

         <li class="<?php if($navigation == 'site-setting') { ?>active<?php } ?>">
          <a href="<?php echo base_url('master-settings'); ?>">
            <i class="fa fa-cogs"></i> <span>Master Settings</span>            
          </a>          
        </li>

        <li class="<?=($navigation == "help_and_faq_management") ? "active" : ""?>">
          <a href="<?=base_url("help-and-faq-management")?>">
            <i class="fa fa-circle-o text-aqua"></i> <span>Help and FAQ Management</span>            
          </a>          
        </li>


        <li class="<?php if($navigation == 'content-list') { ?>active<?php } ?>">
          <a href="<?php echo base_url('page-content'); ?>">
            <i class="fa fa-circle-o text-aqua"></i> <span>Page Content Management</span>            
          </a>          
        </li>

         <li class="treeview <?php if($navigation == 'banner') { ?>active<?php } ?>">
          <a href="#">
            <i class="fa fa-circle-o text-aqua"></i> <span>Banner Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if($navigation == 'banner' && $sub_navigation == 'banner-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>banner-list"><i class="fa fa-circle-o"></i> Banner List</a></li>
            <li class="<?php if($navigation == 'banner' && $sub_navigation == 'banner-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>banner-add"><i class="fa fa-circle-o"></i> Create New Banner</a></li>
          </ul>
          
        </li>

        <li class="treeview <?php if($navigation == 'category') { ?>active<?php } ?>">
          <a href="#">
            <i class="fa fa-circle-o text-aqua"></i> <span>Category Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

        <ul class="treeview-menu">
            <li class="<?php if($navigation == 'category' && $sub_navigation == 'category-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>category"><i class="fa fa-circle-o"></i> Category List</a></li>
            <li class="<?php if($navigation == 'category' && $sub_navigation == 'category-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>category/add"><i class="fa fa-circle-o"></i> Create New Category</a></li>
          </ul>

        </li>

         <li class="treeview <?php if($navigation == 'product') { ?>active<?php } ?>">
          <a href="#">
            <i class="fa fa-circle-o text-aqua"></i> <span>Product Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

        <ul class="treeview-menu">
            <li class="<?php if($navigation == 'product' && $sub_navigation == 'product-list') { ?>active<?php } ?>"><a href="<?php echo base_url('product');?>"><i class="fa fa-circle-o"></i> Product List</a></li>
            <li class="<?php if($navigation == 'product' && $sub_navigation == 'product-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>product/add"><i class="fa fa-circle-o"></i> Create New Product</a></li>
          </ul>

        </li>

         <li class="treeview <?php if($navigation == 'crop') { ?>active<?php } ?>">
          <a href="#">
            <i class="fa fa-circle-o text-aqua"></i> <span>Crop Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

        <ul class="treeview-menu">
            <li class="<?php if($navigation == 'crop' && $sub_navigation == 'crop-list') { ?>active<?php } ?>"><a href="<?php echo base_url('crop');?>"><i class="fa fa-circle-o"></i> Crop List</a></li>
            <li class="<?php if($navigation == 'crop' && $sub_navigation == 'crop-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>crop/add"><i class="fa fa-circle-o"></i> Create New Crop</a></li>
          </ul>

        </li>

           <li class="<?php if($navigation == 'users') { ?>active<?php } ?>">
              <a href="<?php echo base_url('users-list'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>User Management</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'vendors') { ?>active<?php } ?>">
              <a href="<?php echo base_url('vendors-list'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Vendor Management</span>
              </a>
          </li>

          <li class="treeview <?php if($navigation == 'farm-management') { ?>active<?php } ?>">
            <a href="#">
              <i class="fa fa-circle-o text-aqua"></i> <span>Farm Management</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?=($navigation == 'farm-management' && $sub_navigation == 'farms-list') ? 'active' : ''?>">
                <a href="<?=base_url('farm-management/farms-list')?>"><i class="fa fa-circle-o"></i> Farms List</a>
              </li>
              <li class="<?=($navigation == 'farm-management' && $sub_navigation == 'report-subscriptions-list') ? 'active' : ''?>">
                <a href="<?=base_url('farm-management/report-subscriptions-list')?>"><i class="fa fa-circle-o"></i> Report Subscriptions List</a>
              </li>
              <li class="<?=($navigation == 'farm-management' && $sub_navigation == 'crop-health-reports') ? 'active' : ''?>">
                <a href="<?=base_url('farm-management/crop-health-reports')?>"><i class="fa fa-circle-o"></i> Crop Health Reports</a>
              </li>
            </ul>
          </li>

          <li class="treeview <?php if($navigation == 'khata-management') { ?>active<?php } ?>">
            <a href="#">
              <i class="fa fa-circle-o text-aqua"></i> <span>Khata Management</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?=($navigation == 'khata-management' && $sub_navigation == 'users-khata-list') ? 'active' : ''?>">
                <a href="<?=base_url('khata-management/users-khata-list')?>"><i class="fa fa-circle-o"></i> Users Khata List</a>
              </li>
            </ul>
          </li>

          <!-- <li class="treeview <?php if($navigation == 'promo') { ?>active<?php } ?>">
              <a href="#">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Promo Code Management</span>
                  <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu">
                  <li class="<?php if($navigation == 'promo' && $sub_navigation == 'promo-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>promo-list"><i class="fa fa-circle-o"></i> Promo Code List</a></li>
                  <li class="<?php if($navigation == 'promo' && $sub_navigation == 'promo-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>promo-add"><i class="fa fa-circle-o"></i> Create New Promo Code</a></li>
              </ul>

          </li> -->

          <li class="treeview <?php if($navigation == 'city') { ?>active<?php } ?>">
            <a href="#">
              <i class="fa fa-circle-o text-aqua"></i> <span>City Management</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?php if($navigation == 'city' && $sub_navigation == 'city-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>city-list"><i class="fa fa-circle-o"></i> City List</a></li>
              <li class="<?php if($navigation == 'city' && $sub_navigation == 'city-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>city-add"><i class="fa fa-circle-o"></i> Add New City</a></li>
            </ul>
          </li>

          <li class="<?php if($navigation == 'state_management') { ?>active<?php } ?>">
            <a href="<?=base_url('state-management')?>">
              <i class="fa fa-circle-o text-aqua"></i> <span>State Management</span>            
            </a>          
          </li>

          <li class="treeview <?php if($navigation == 'district') { ?>active<?php } ?>">
            <a href="#">
              <i class="fa fa-circle-o text-aqua"></i> <span>District Management</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?php if($navigation == 'district' && $sub_navigation == 'district-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>district-list"><i class="fa fa-circle-o"></i> District List</a></li>
              <li class="<?php if($navigation == 'district' && $sub_navigation == 'district-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>district-add"><i class="fa fa-circle-o"></i> Add New District</a></li>
            </ul>
          </li>

           <li class="treeview <?php if($navigation == 'order') { ?>active<?php } ?>">
              <a href="#">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Order Management</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
              </a>
              <ul class="treeview-menu">
                  <li class="<?php if($navigation == 'order' && $sub_navigation == 'orders-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>order"><i class="fa fa-circle-o"></i> Orders List</a></li>
              </ul>
          </li>

          <li class="treeview <?php if($navigation == 'delivery_drivers') { ?>active<?php } ?>">
            <a href="#">
              <i class="fa fa-circle-o text-aqua"></i> <span>Delivery Drivers Management</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?php if($navigation == 'delivery_drivers' && $sub_navigation == 'delivery_drivers_list') { ?>active<?php } ?>"><a href="<?=base_url('delivery-drivers-list')?>"><i class="fa fa-circle-o"></i> Delivery Drivers List</a></li>
              <li class="<?php if($navigation == 'delivery_drivers' && $sub_navigation == 'add_delivery_driver') { ?>active<?php } ?>"><a href="<?=base_url('add-delivery-driver')?>"><i class="fa fa-circle-o"></i> Add Delivery Driver</a></li>
            </ul>
          </li>

          <li class="<?=($navigation == 'delivery_date_management') ? "active" : ""?>"><a href="<?=base_url('delivery-date-management')?>"><i class="fa fa-circle-o"></i> Delivery Date Management</a></li>

          <li class="treeview <?php if($navigation == 'blog') { ?>active<?php } ?>">
              <a href="#">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Blog Management</span>
                  <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu">
                  <li class="<?php if($navigation == 'blog' && $sub_navigation == 'blog-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>blog"><i class="fa fa-circle-o"></i> Blog List</a></li>
                  <li class="<?php if($navigation == 'blog' && $sub_navigation == 'blog-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>blog/add"><i class="fa fa-circle-o"></i> Add New Blog</a></li>
              </ul>

          </li>

          <li class="treeview <?php if($navigation == 'blogcat') { ?>active<?php } ?>">
              <a href="#">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Blog  Category Management</span>
                  <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu">
                  <li class="<?php if($navigation == 'blogcat' && $sub_navigation == 'blog-cat-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>blog/category"><i class="fa fa-circle-o"></i> Blog Category List</a></li>
                  <li class="<?php if($navigation == 'blogcat' && $sub_navigation == 'blog-cat-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>blog/category_add"><i class="fa fa-circle-o"></i> Add New Blog Category</a></li>
              </ul>

          </li>

          <li class="treeview <?php if($navigation == 'video') { ?>active<?php } ?>">
          <a href="#">
            <i class="fa fa-circle-o text-aqua"></i> <span>Youtube Video Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if($navigation == 'video' && $sub_navigation == 'video-list') { ?>active<?php } ?>"><a href="<?php echo base_url('video');?>"><i class="fa fa-circle-o"></i> Video List</a></li>
            <li class="<?php if($navigation == 'video' && $sub_navigation == 'video-add') { ?>active<?php } ?>"><a href="<?php echo base_url('video/add');?>"><i class="fa fa-circle-o"></i> Upload New Video</a></li>
          </ul>
        </li>

        <li class="treeview <?php if($navigation == 'questions') { ?>active<?php } ?>">
            <a href="#">
                <i class="fa fa-circle-o text-aqua"></i> <span>Questions & Answers Management</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?php if($navigation == 'questions' && $sub_navigation == 'questions-list') { ?>active<?php } ?>"><a href="<?php echo base_url('questions-list');?>"><i class="fa fa-circle-o"></i> Questions List</a></li>
            <li class="<?php if($navigation == 'questions' && $sub_navigation == 'questions-add') { ?>active<?php } ?>"><a href="<?php echo base_url('questions/add');?>"><i class="fa fa-circle-o"></i>Add Question</a></li>
            </ul>
        </li>

          <li class="<?php if($navigation == 'sellproduces') { ?>active<?php } ?>">
          <a href="<?php echo base_url('sellproduces-list'); ?>">
            <i class="fa fa-circle-o text-aqua"></i> <span>Sell Produce List</span>            
          </a>          
        </li>
        <li class="<?php if($navigation == 'push') { ?>active<?php } ?>">
              <a href="<?php echo base_url('push-notification'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Push Notification Management</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'communities') { ?>active<?php } ?>">
              <a href="<?php echo base_url('communities-list'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Community List</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'referrals') { ?>active<?php } ?>">
              <a href="<?php echo base_url('referrals'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Referrals</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'field_req') { ?>active<?php } ?>">
              <a href="<?php echo base_url('field-visit-request'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Field Visit Request</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'merchants') { ?>active<?php } ?>">
              <a href="<?php echo base_url('merchants'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Merchants</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'merchants_commision') { ?>active<?php } ?>">
              <a href="<?php echo base_url('merchants/commission'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Merchants Commission</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'merchant_earned_commissions') { ?>active<?php } ?>">
              <a href="<?php echo base_url('merchant-earned-commissions'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Merchant Earned Commissions</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'code_generator') { ?>active<?php } ?>">
              <a href="<?php echo base_url('code_generator/'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Coupon Code Generator</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'kawa_api_logs') { ?>active<?php } ?>">
              <a href="<?php echo base_url('kawa_api_log/'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Kawa API Logs</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'schedule-push-notification') { ?>active<?php } ?>">
              <a href="<?php echo base_url('schedule-push-notification/'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Schedule Push Notification</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'soil-health-test') { ?>active<?php } ?>">
              <a href="<?php echo base_url('soil-health-test/'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Soil Health Test</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'plantix') { ?>active<?php } ?>">
              <a href="<?php echo base_url('plantix/'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Plantix</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'plantix-subscription-plans') { ?>active<?php } ?>">
              <a href="<?php echo base_url('plantix-subscription-plans/'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Plantix Subscription Plans</span>
              </a>
          </li>

          <li class="<?php if($navigation == 'service-coupons') { ?>active<?php } ?>">
              <a href="<?php echo base_url('service-coupons/'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Service Coupons</span>
              </a>
          </li>

          <!-- <li class="<?php if($navigation == 'comment') { ?>active<?php } ?>">
          <a href="<?php echo base_url('review-list'); ?>">
            <i class="fa fa-circle-o text-aqua"></i> <span>Review List</span>            
          </a>          
        </li> -->

          <!-- <li class="treeview <?php if($navigation == 'meta') { ?>active<?php } ?>">
              <a href="#">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Meta Data Management</span>
                  <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu">
                  <li class="<?php if($navigation == 'meta' && $sub_navigation == 'meta-list') { ?>active<?php } ?>"><a href="<?php echo base_url();?>meta-list"><i class="fa fa-circle-o"></i> Meta Data List</a></li>
                  <li class="<?php if($navigation == 'meta' && $sub_navigation == 'meta-add') { ?>active<?php } ?>"><a href="<?php echo base_url();?>meta-add"><i class="fa fa-circle-o"></i> Add New Meta Data</a></li>
              </ul>

          </li> -->

          <!-- <li class="<?php if($navigation == 'push') { ?>active<?php } ?>">
              <a href="<?php echo base_url('push-notification'); ?>">
                  <i class="fa fa-circle-o text-aqua"></i> <span>Push Notification Management</span>
              </a>
          </li> -->

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
