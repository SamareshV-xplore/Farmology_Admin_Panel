<?php include "head_links.php"; ?>
<?php include "style.php"; ?>

<div id="container">
	<center><label class="welcome-text">WELCOME</label></center>
	<form>
		<div class="form-group">
			<input type="text" id="emailOrMobile" class="form-control shadow-none inputbox" placeholder="Enter Email or Phone Number">
			<button class="btn btn-secondary" id="otpButton" style="float: right; margin: 5px;" onclick="sendOtpClick(event)">Send OTP</button>
			<input type="text" id="otp" class="form-control shadow-none inputbox" disabled="true" placeholder="Enter OTP">
			<button class="btn form-control login-btn shadow-none" disabled="true" id="loginBtn" onclick="checkOTP(event)">Login</button>
			<a href="<?= base_url("vendors/register") ?>" class="login-link"><label style="cursor: pointer;">Click Here to Register</label></a>
		</div>
	</form>
</div>

<script type="text/javascript">
	var otp = null;
	function sendOtpClick (e) {
		e.preventDefault();
		var contact = gel('emailOrMobile').value;
		$.ajax({
			url: '<?= base_url('vendors/sendOtp') ?>',
			type: 'post',
			data: {contact : contact},
			success: function (data) {
				var response = JSON.parse(data);
				if (response.otpDetails != null) {
					otp = response.otpDetails.otp;
					unclockOtpField();
				}
			}
		});
	}

	function unclockOtpField () {
		$('#otp').prop('disabled', false);
		$('#loginBtn').prop('disabled', false);
		$('#emailOrMobile').prop('disabled', true);
		$('#otpButton').prop('disabled', true);
	}

	function checkOTP(e) {
		e.preventDefault();
		var givenOtp = gel('otp').value;
		if (otp == givenOtp) {
			location.href = "<?= base_url('vendors/dashboard') ?>";
		}
		else{
			// alert('otp : ' + otp + 'given : ' + givenOtp);
			alert("Given OTP doesn't match with the one sent to you. Refresh the page and try again");
		}
	}
</script>