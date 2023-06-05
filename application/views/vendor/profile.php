<center>
	<form method="post" action="<?= base_url('vendors/update_vendor') ?>" enctype='multipart/form-data'>
			<div class="profile-container">
				<h5>
					Personal Details
					<hr class="profile-lines">
				</h5>
				<?php if ($userdata->image != $front_url) {
					$src = $userdata->image;
				} 
				else{
					$src = "https://admin.surobhiagro.in/media/uploads/users/no-image.png";
				}
				?>
				<img src="<?= $src ?>" class="profile-image" id="img_preview" onclick="imgPreviewClick()">
				<br>
				<label class="hide-input-file">
					<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
					<input type="file" name="profileimage" id="input_file" accept="image/*" onchange="imgChange(event)" hidden>
				</label>
				<div class="form-group">
					<input type="hidden" name="id" value="<?= $userdata->hash_id ?>">
					<label class="profile-form-labels">
						Name
					</label>
					<input type="text" name="name" class="form-control shadow-none profile-inputbox" required value="<?= $userdata->name ?>">
				</div>

				<div class="form-group">
					<label class="profile-form-labels">
						Address
					</label>
					<input type="text" name="address" class="form-control shadow-none profile-inputbox" required value="<?= $userdata->address ?>">
				</div>

				<div class="form-group">
					<label class="profile-form-labels">
						Email
					</label>
					<input type="email" name="email" class="form-control shadow-none profile-inputbox" required value="<?= $userdata->email ?>">
				</div>

				<div class="form-group">
					<label class="profile-form-labels">
						Phone
					</label>
					<input type="tel" name="phone" class="form-control shadow-none profile-inputbox" pattern="(6|7|8|9)\d{9}" required value="<?= $userdata->phone ?>">
				</div>

				<h5>
					Business Details
					<hr class="profile-lines">
				</h5>

				<div class="form-group">
					<label class="profile-form-labels">
						Shop Name
					</label>
					<input type="text" name="shop" class="form-control shadow-none profile-inputbox" required value="<?= $userdata->shop_name ?>">
				</div>

				<div class="form-group">
					<label class="profile-form-labels">
						Service Area
					</label>
					<select name="serviceArea[]" id="serviceArea" multiple class="form-control shadow-none" title="Press ctrl key and click to select multiple">
				    <?php 
				    	$serviceArea = json_decode($userdata->service_area);
				    	foreach ($pincodes as $pincode) {
				    	$selected = (in_array(strval($pincode->pin_code), $serviceArea) == true) ? 'selected' : '';
				    ?>
				    	<option value="<?= $pincode->pin_code ?>" <?= $selected ?>><?= $pincode->pin_code ?></option>
				    <?php 
				    }
				    ?>
				  	</select>

				</div>

				<?php
					$banking = json_decode($userdata->banking_details);

				?>

				<h5>
					Banking Details
					<hr class="profile-lines">
				</h5>
				<div class="form-group">
					<label class="profile-form-labels">
						Account Number
					</label>
					<input type="text" name="account-no" class="form-control shadow-none profile-inputbox" required value="<?= is_object($banking) ? $banking->account_no : '' ?>">
				</div>
				<div class="form-group">
					<label class="profile-form-labels">
						IFSC Code
					</label>
					<input type="text" name="ifsc_no" class="form-control shadow-none profile-inputbox" required value="<?= is_object($banking) ? $banking->ifsc_no : '' ?>">
				</div>
				<div class="form-group">
					<label class="profile-form-labels">
						Account Holder's Name
					</label>
					<input type="text" name="account_holder_name" class="form-control shadow-none profile-inputbox" required value="<?= is_object($banking) ? $banking->account_holder_name : '' ?>">
				</div>

				<div class="form-group">
					<input type="submit" class="profile-update-button" value="Update">
				</div>
			</div>
	</form>
</center>


<script type="text/javascript">
	function imgPreviewClick() {
		gel('input_file').click();
	}
	function readURL(input) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $('#img_preview').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$("#input_file").change(function(){
	    readURL(this);
	});

</script>