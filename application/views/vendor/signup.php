<?php include "head_links.php"; ?>
<?php include "style.php"; ?>

<div id="container" style="margin: 25px auto !important;">
	<center><label class="welcome-text">WELCOME</label></center>
	<form>
		<div class="form-group">
			<input type="text" id="name" class="form-control shadow-none inputbox" placeholder="Enter Name">
			<input type="text" id="shopName" class="form-control shadow-none inputbox" placeholder="Enter Shop Name">
			<input type="tel" id="mobile" class="form-control shadow-none inputbox" placeholder="Enter Mobile Number">
			<input type="email" id="email" class="form-control shadow-none inputbox" placeholder="Enter Email ID">
			<input type="text" id="address" class="form-control shadow-none inputbox" placeholder="Enter Address">
			<!-- <input type="text" id="serviceArea" class="form-control shadow-none inputbox" placeholder="Enter Service Area"> -->
			<label style="margin-top: 30px;">Select Service Area</label>
			<select name="serviceArea" id="serviceArea" multiple class="form-control shadow-none" title="Press ctrl key and click to select multiple">
		    <?php foreach ($pincodes as $pincode) {
		    ?>
		    	<option value="<?= $pincode->pin_code ?>"><?= $pincode->pin_code ?></option>
		    <?php 
		    }
		    ?>
		  	</select>
			<button class="btn form-control login-btn shadow-none" onclick="registerUser(event)">Sign Up</button>
			<a href="<?= base_url("vendors") ?>" class="login-link"><label style="cursor: pointer;">Click Here to Login</label></a>
		</div>
	</form>
</div>

<script type="text/javascript">
	function registerUser (e) {
		e.preventDefault();
		var data = gatherData();

		$.ajax({
				url: 'submitUser',
				type: 'post',
				data: data,
				success: function (data) {
					 var response = JSON.parse(data);
					 if (response.isSubmitted == true) {
					 	location.href = "<?= base_url('vendors') ?>";
					 }
					 else{
					 	alert(response.message);
					 }
				}
			});
	}

	function gatherData() {
		var ob = {};
		ob.name = gel('name').value;
		ob.shopName = gel('shopName').value;
		ob.mobile = gel('mobile').value;
		ob.email = gel('email').value;
		ob.address = gel('address').value;
		ob.serviceArea = $('#serviceArea').val();

		return ob;
	}
</script>