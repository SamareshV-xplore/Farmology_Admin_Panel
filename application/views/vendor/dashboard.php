<?php include "head_links.php"; ?>
<?php include "style.php"; ?>
<div class="container-fluid mx-0 px-0">
	<div class="row">
		<div class="col-12">
			<div class="header_nav">
				<label class="dashboard-welcome-text">Welcome <?= isset($vendorName) ? $vendorName : "Vendor" ?></label>
				<div class="dropdown show float-right">
				  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <div class="log-out-butt">
						<?= isset($vendorName) ? substr($vendorName, 0,1) : "?" ?>
					</div>
				  </a>

				  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
				    <a class="dropdown-item" href="<?= base_url('vendors/logout') ?>">Log Out</a>
				  </div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 50px;">
		<div class="col-2">
			<!-- The sidebar -->
			<div class="sidebar">
			  <a href="<?= base_url('vendors/dashboard') ?>" id="dashboard_nav">Dashboard</a>
			  <a href="<?= base_url('vendors/products') ?>" id="product_nav">Product</a>
			  <a href="<?= base_url('vendors/orders') ?>" id="orders_nav">Orders</a>
			  <a href="<?= base_url('vendors/profile') ?>" id="profile_nav">Profile</a>
			</div>
		</div>
		<div class="col-10">
			<div id="dashboard-content">
				<div class="header">
					<label class="header-label"><?= $title ?></label>
					<hr>
				</div>
				<div class="main-container">
					<?php
						if (strtoupper($title) == 'DASHBOARD') {
							include "dashboard_content.php";
						}
						if (strtoupper($title) == 'PRODUCTS') {
							include "products.php";
						}
						if (strtoupper($title) == 'ORDERS') {
							include "orders.php";
						}
						if (strtoupper($title) == 'PROFILE') {
							include "profile.php";
						}
						if (strtoupper($title) == 'ADD NEW PRODUCT') {
							include "add_product.php";
						}
						if (strtoupper($title) == 'EDIT PRODUCT') {
							include "edit_product.php";
						}
						if (strtoupper($title) == 'ORDERS') {
							include "orders.php";
						}
						if (strtoupper($title) == 'ORDER DETAILS') {
							include "order_detail.php";
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		var title = "<?= $title ?>";
		if (title.toUpperCase() == 'DASHBOARD') {
			gel('dashboard_nav').className = 'active';
		}
		if (title.toUpperCase() == 'PRODUCTS') {
			gel('product_nav').className = 'active';
		}
		if (title.toUpperCase() == 'ORDERS') {
			gel('orders_nav').className = 'active';
		}
		if (title.toUpperCase() == 'PROFILE') {
			gel('profile_nav').className = 'active';
		}
		if (title.toUpperCase() == 'ADD NEW PRODUCT') {
			gel('product_nav').className = 'active';
		}
		if (title.toUpperCase() == 'EDIT PRODUCT') {
			gel('product_nav').className = 'active';
		}
		if (title.toUpperCase() == 'ORDERS') {
			gel('orders_nav').className = 'active';
		}
		if (title.toUpperCase() == 'ORDER DETAILS') {
			gel('orders_nav').className = 'active';
		}
	})
</script>