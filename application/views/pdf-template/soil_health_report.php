<?php

	$report_no = (!empty($user_data->id)) ? $user_data->id : "";
	$name = (!empty($user_data->name)) ? ucwords(strtolower($user_data->name)) : "";
	$mobile_no = (!empty($user_data->mobile_number)) ? $user_data->mobile_number : "";
	$farm_name = (!empty($user_data->farm_name)) ? ucwords(strtolower($user_data->farm_name)) : "";
	$land_size = (!empty($user_data->land_size)) ? $user_data->land_size : "";
	$land_unit = (!empty($user_data->land_unit)) ? $user_data->land_unit : "";

	$report_generate_date = date("jS F Y", time());
	$SC_date = (!empty($user_data->sample_collection_date)) ? $user_data->sample_collection_date : "";
	$sample_collection_date = date("jS F Y", strtotime($SC_date));
	$crop = (!empty($user_data->crop)) ? ucwords(strtolower($user_data->crop)) : "";
	$pincode = (!empty($user_data->pincode)) ? ucwords(strtolower($user_data->pincode)) : "";
	$district = (!empty($user_data->district)) ? ucwords(strtolower($user_data->district)) : "";
?>

<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous"> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<style>
	@font-face {
		font-family: 'eurostile';
	}

	.report_no_divs {
		border: #436479 2px solid;
		background: #bac34c;
		padding: 4px 6px;
		border-radius: 5px;
		color: #34586e;
		font-weight: 600;
	}

	.hdrbtm h3 {
		color: #34586e;
		font-weight: 600;
		letter-spacing: 2px;
		font-family: 'eurostile';
		font-size: 36px;
	}

	header.header {
		background: #ebeed9;
		padding: 10px 0px;
	}

	.test_details_sec label{
		font-weight: 600;
		text-transform: uppercase;
		font-size: 11px;
	}

	.bigha_cnt_divs label{
		font-weight: 600;
		text-transform: uppercase;
		font-size: 11px;
	}

	.pincode_divs label{
		font-weight: 600;
		text-transform: uppercase;
		font-size: 11px;
	}

	.report_generate_date_divs label{
		font-weight: 600;
		text-transform: uppercase;
		font-size: 11px;
	}

	.test_details_divs {
		background: #c5d4eb;
		padding: 10px;
		border: 2px #6791b7 solid;
		border-radius: 12px;
		margin-bottom: 20px;
	}

	.test_details_divs input[type=text] {
		background: #fff;
		border: 1px #000 solid;
		height: 25px;
	}

	.test_details_divs input[type=date] {
		background: #fff;
		border: 1px #000 solid;
		height: 25px;
	}

	.test_details_sec {
		background: #426078;
	}

	.soil_information_table {
		width: 100%;
		text-align: center;
	}

	table.soil_information_table p {
		background: #bac34c;
		/*margin: 4px;*/
		margin-bottom: 0;
		padding: 10px;
		border-radius: 10px 10px 0px 0px;
		text-transform: uppercase;
		font-size: 14px;
	}

	.soil_information_table11  {
		background: #6491bb;
		font-size: 16px;
		height: 35px;
		/* border: 2px solid #426078;
         border-collapse: collapse; */
	}

	.bordercstmtbl {
		border: 2px solid #426078;
		border-collapse: collapse;
		padding: 0 8px;
	}

	.green_txt {
		color: #bac34c;
		font-weight: 600;
		text-align: right;
		text-transform: uppercase;
	}

	.product_rec {
		background: #ebeed9 !important;
	}

	.actinable_inside1 {
		width: 100%;
		background: #bac34c;
		padding: 10px;
		color: #426078;
		font-size: 16px;
		margin-top: 0;
	}

	.actinable_inside {
		border: #6491bb 4px solid;
		height: 250px;
		background: #fff;
		border-radius: 8px;
		overflow: hidden;
	}

	.this_txt_captute_divs {
		border: #fff 4px solid;
		padding: 10px;
		border-radius: 8px;
	}

	.custom_support_divs1  {
		width: 100%;
		background: #bac34c;
		padding: 10px;
		color: #426078;
		font-size: 11px;
		margin-top: 0;
	}

	.custom_support_divs {
		background: #6491bb;
		border-radius: 8px 8px 0px 0px;
		overflow: hidden;
	}

	.logo_images_top {
		height: 80px !important;
	}

	label {
		margin-bottom: 0 !important;
	}

	/* @media screen and (max-width: 767px){
		.logo_images_top {
			height: 80px !important;
		} 

		.hdrbtm h3 {
			font-size: 10px !important;
			font-weight: 700 !important;
		}

		.report_no_divs {
			font-size: 10px !important;
		}

		table.soil_information_table h6 {
			font-size: 10px;
		}

		.green_txt {
			font-size: 10px !important;
		}

		table.soil_information_table p {
			font-size: 10px !important;
			height: 50px !important;
		}

		.test_details_sec label {
			font-size: 11px !important;
		}

		.actinable_inside {
			min-height: 200px;
			margin-bottom: 15px !important;
		}

		.test_details_sec table {
			display: block;
			overflow: auto;
			width: 100%;
		}
	}

	@media only screen and (min-width: 768px) and (max-width: 991px){
		table.soil_information_table p {
			font-size: 14px !important;
		}

		.this_txt_captute_divs h6 {
			font-size: 11px !important;
		}

		.helpline {
			font-size: 12px !important;
		}

		.hdrbtm h3 {
			font-weight: 700 !important;
			font-size: 22px !important;
		}
	} */

	/* .hdrtop {
		display: flex;
		align-items: center;
		justify-content: space-between;
	} */

	.w-60 {
		width: 65%;
		float: left;
	}

	.w-40 {
		width: 35%;
		float: left;
	}

	.floatLeft {
		float: left;
		width: 30%;
		text-align:right;
	}

	.floatRight {
		float: right;
		width: 62%;
		padding-right: 20px;
	}

	.floatclear {
	clear: both;
	}

	.w-100 {
		width:60%
	}

	.hdtrtxt {
		color: #34586e;
		font-weight: 800 !important;
		font-size: 22px;
		letter-spacing: 1px;
	}

	.mr-3 {
		margin-right:15px
	}

	.pb-5, .py-5 {
		padding-bottom: 2rem !important;
	}

	.pt-5, .py-5 {
		padding-top: 2rem !important;
	}

	.pl-5, .px-5 {
		padding-left: 2rem !important;
	}

	.pr-5, .px-5 {
		padding-right: 2rem !important;
	}

	.p-5 {
		padding: 2rem !important;
	}

	.p-2{
		padding: 1rem !important;
	}

	.pr-5, .px-3 {
		padding-right: 1.5rem !important;
	}

	.colL-6 {
		width: 49%!important;
		float: left!important;
		box-sizing: border-box!important;
	}

	.colL-8 {
		width: 76%!important;
		float: left!important;
		box-sizing: border-box!important;
	}

	.colR-6 {
		width: 49%!important;
		float: right!important;
		box-sizing: border-box!important;
	}

	.colR-4 {
		width: 23%!important;
		float: right!important;
		box-sizing: border-box!important;
	}

	.frmlblwidth1 {
		width: 25%!important;
		float: left!important;
		padding-top: 5px!important;
	}

	.frmlblwidth2 {
		float: right!important;
		width: 65%!important;
	}

	.frmlblwidth3 {
		width: 40%!important;
		float: left!important;
		padding-top: 5px!important;
	}

	.frmlblwidth4 {
		float: right!important;
		width: 45%!important;
	}

	.mt-2, .my-2 {
		margin-top: .5rem !important;
	}

	.frmlblwidth20 {
		width: 18%!important;
		float: left!important;
		padding-top: 5px!important;
	}

	.frmlblwidth21 {
		width: 23%!important;
		float: left!important;
		padding-top: 5px!important;
	}

	.frmlblwidth30 {
		float: left!important;
		width: 36%!important;
		margin-right: 2%!important;
	}

	.frmlblwidth25 {
		float: left!important;
		width: 18%!important;
	}

	.frmlblwidth2525 {
		float: right!important;
		width: 18%!important;
	}

	.frmlblwidth30 {
		float: left!important;
		width: 22%!important;
		margin-right: 1%!important;
	}

	.frmlblwidth3030 {
		padding-top: 5px!important;
	}

	.frmlblwidth31 {
		float: right!important;
		width: 22%!important;
	}

	.col-8 {
		float: left!important;
		width: 68%!important;
		padding:0 5px!important;
	}

	.col-4 {
		float: right!important;
		width: 28%!important;
		padding:0 5px!important;
	}

	.rhtboxlft {
		float: left!important;
		width: 67%!important;
	}

	.rhtboxrht {
		float: right!important;
		width: 28%!important;
	}

	.text-white {
		color:#fff!important;
	}

	b, strong {
		font-weight: bolder;
	}

	.mt-3, .my-3 {
		margin-top: 1rem !important;
	}

	.pl-2, .px-2 {
		padding-left: .5rem !important;
	}

	.pr-2, .px-2 {
		padding-right: .5rem !important;
	}

	.p-1 {
		padding: .25rem !important;
	}

	.brtprdus {
		border-top-left-radius: 12px !important;
		border-top-right-radius: 12px !important;
		font-size: 14px !important;
	}

	body {
		font-size:14px
	}
</style>
</head>
<body>
<header class="header">  
	<section class="hdrbtm"> 
		<div class="container">
			<div>
				<div class="w-60">
					<a class="logo" rel="home">
						<img src="<?=base_url('assets/pdf-assets/soil_health_test/logo.png')?>" alt="" class="logo_images_top">
					</a>
				</div>	

				<div class="w-40">
					<div class="d-flex flex-row align-items-center"> 
						<div class="floatLeft"> 
							<img src="<?=base_url('assets/pdf-assets/soil_health_test/soil.png')?>" alt="" class="logo_images_top w-100">
						</div>	
						<div class="floatRight" style="margin-top:10px;">
							<div class="hdtrtxt">Soil Test Report</div>
							<div class="report_no_divs">Report No. <span><?=$report_no?></span></div>
						</div>
						<div class="floatclear"></div>
					</div>
				</div>

				<div class="floatclear"></div>
			</div>
		</div>
	</section>
</header>

<section class="p-2 test_details_sec">
	<div class="container mb-0">
		<div class="row">
			<div class="colL-6">
				<div class="test_details_divs">
					<div class="">
						<label class="frmlblwidth1">Name<span class="ml-auto">:</span></label>
						<input type="text" name="name" class="name form-control frmlblwidth2"  value="<?=$name?>">
						<div class="floatclear"></div>
					</div>

					<div class="mt-2">
						<label class="frmlblwidth1">Mobile No<span class="ml-auto">:</span></label>
						<input type="text" name="mobile_no" class="mobile_no form-control frmlblwidth2" value="<?=$mobile_no?>">
						<div class="floatclear"></div>
					</div>
				</div>

				<div class="test_details_divs">
					<div class="">
						<label class="frmlblwidth1">Farm Name<span class="ml-auto">:</span></label>
						<input type="text" name="farm_name" class="farm_name form-control frmlblwidth2" value="<?=$farm_name?>">
						<div class="floatclear"></div>
					</div>

					<div class="mt-2 bigha_cnt_divs">
						<div>
							<label class="frmlblwidth20">Land Size<span class="ml-auto">:</span></label>
							<input type="text" name="farm_name" class="farm_name form-control frmlblwidth30" value="<?=$land_size?>">
							&nbsp; &nbsp;
							<input type="checkbox" name="land_unit_bigha" class="form-control" <?=(strtolower($land_unit) == "bigha") ? "checked='checked'" : ""?>>
							<label>Bigha</label>
							&nbsp;
							<input type="checkbox" name="land_unit_acre" class="form-control" <?=(strtolower($land_unit) == "acre") ? "checked='checked'" : ""?>>
							<label>Acre</label>
						</div>
						<div class="floatclear"></div>
					</div>
				</div>
			</div>

	       	<div class="colR-6">
		      	<div class="test_details_divs report_generate_date_divs">
		        	<div class="">
		        		<label class="frmlblwidth3">Report Generate Date<span class="ml-auto">:</span></label>
		        		<input type="text" name="report_generate_date" class="report_generate_date form-control frmlblwidth4" value="<?=$report_generate_date?>">
						<div class="floatclear"></div>
		        	</div>

		        	<div class=" mt-2">
		        		<label class="frmlblwidth3">Sample Collection Date<span class="ml-auto">:</span></label>
		        		<input type="text" name="sample_connection_date" class="sample_connection_date form-control frmlblwidth4" value="<?=$sample_collection_date?>">
						<div class="floatclear"></div>
		        	</div>
		        </div>

		        <div class="test_details_divs">
		        	<div class="">
		        		<label class="frmlblwidth30 frmlblwidth3030">Corp(s)<span class="ml-auto">:</span></label>
		        		<input type="text" name="corps1" class="name form-control ml-1 frmlblwidth30" value="<?=$crop?>">
		        		<input type="text" name="corps2" class="name form-control ml-1 frmlblwidth30">
		        		<input type="text" name="corps3" class="name form-control ml-1 frmlblwidth31">
						<div class="floatclear"></div>
		        	</div>

		        	<div class=" mt-2 pincode_divs">
		        		<label class="frmlblwidth21">Pin Code<span class="ml-auto">:</span></label>
		        		<input type="text" name="pincode" class="pincode form-control frmlblwidth30" value="<?=$pincode?>">
		        		<label class="pl-2 frmlblwidth21">District<span class="ml-auto">:</span></label>
		        		<input type="text" name="district" class="district form-control frmlblwidth30" value="<?=$district?>">
		        		<div class="floatclear"></div>
		        	</div>
		        </div>
	       </div>

		   <div class="floatclear"></div>
	    </div>

		<div class="row">
			<table class="soil_information_table bordercstmtbl">
				<thead>
					<tr>
						<th class="p-2 bordercstmtbl text-center" bgcolor="#bac34c" colspan="2" style="font-size:22px!important;">Soil Test Information</th>
						<th class="p-2 bordercstmtbl text-center" bgcolor="#bac34c" style="font-size:22px!important;">Ideal Range</th>
						<th class="p-2 bordercstmtbl text-center" bgcolor="#bac34c" style="font-size:22px!important;">Rating</th>
						<th class="p-2 bordercstmtbl text-center" bgcolor="#bac34c" style="font-size:22px!important;">Product Recommendation</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($report_data)) { 
					foreach ($report_data as $i => $row) { ?>
						<tr class="soil_information_table11">
							<td class="p-1 bordercstmtbl text-left">
								<h5 class="text-white">
									<?=(!empty($row["name"])) ? strtoupper($row["name"]) : "-"?>
								</h5>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white"><?=(!empty($row["value"])) ? strtoupper($row["value"]) : "-"?></span>
								<span class="green_txt"><?=(!empty($row["unit"])) ? strtoupper($row["unit"]) : "-"?></span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white"><?=(!empty($row["ideal_value"])) ? strtoupper($row["ideal_value"]) : "-"?></span>
								<span class="green_txt"><?=(!empty($row["unit"])) ? strtoupper($row["unit"]) : "-"?></span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<?=(!empty($row["rating"])) ? strtoupper($row["rating"]) : "-"?>
							</td>
							<td class="p-1 bordercstmtbl text-right product_rec">
								<?php if (!empty($recommended_products[$i]->title) && !empty($recommended_products[$i]->image)) { ?>
									<img style="max-width:25px; max-height:22px;" src="<?=$recommended_products[$i]->image?>"/>
									&nbsp; &nbsp;
									<span><?=$recommended_products[$i]->title?></span>
								<?php } ?>
							</td>
						</tr>
					<?php }} else { ?>
						<tr class="soil_information_table11">
							<td class="p-1 bordercstmtbl text-left">
								<h5 class="text-white">
									Nitrogen
								</h5>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">100</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">300</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								33.5
							</td>
							<td class="p-1 bordercstmtbl text-right product_rec">
								SPARK
							</td>
						</tr>
						<tr class="soil_information_table11">
							<td class="p-1 bordercstmtbl text-left">
								<h5 class="text-white">
									Nitrogen
								</h5>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">100</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">300</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								33.5
							</td>
							<td class="p-1 bordercstmtbl text-right product_rec">
								SPARK
							</td>
						</tr>
						<tr class="soil_information_table11">
							<td class="p-1 bordercstmtbl text-left">
								<h5 class="text-white">
									Nitrogen
								</h5>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">100</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">300</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								33.5
							</td>
							<td class="p-1 bordercstmtbl text-right product_rec">
								SPARK
							</td>
						</tr>
						<tr class="soil_information_table11">
							<td class="p-1 bordercstmtbl text-left">
								<h5 class="text-white">
									Nitrogen
								</h5>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">100</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">300</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								33.5
							</td>
							<td class="p-1 bordercstmtbl text-right product_rec">
								SPARK
							</td>
						</tr>
						<tr class="soil_information_table11">
							<td class="p-1 bordercstmtbl text-left">
								<h5 class="text-white">
									Nitrogen
								</h5>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">100</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">300</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								33.5
							</td>
							<td class="p-1 bordercstmtbl text-right product_rec">
								SPARK
							</td>
						</tr>
						<tr class="soil_information_table11">
							<td class="p-1 bordercstmtbl text-left">
								<h5 class="text-white">
									Nitrogen
								</h5>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">100</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								<span class="text-white">300</span>
								<span class="green_txt">Kg/Acre</span>
							</td>
							<td class="p-1 bordercstmtbl text-right">
								33.5
							</td>
							<td class="p-1 bordercstmtbl text-right product_rec">
								SPARK
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

	    <div class="row mt-3 mb-0">
	    	<div class="colL-8" style="height:220px;overflow-y:hidden;">
	    		<div class="actinable_inside" style="height:210px;overflow-y:hidden;">
	    			<h5 class="actinable_inside1">ACTIONABLE INSIGHT :</h5>
					<div class="py-1 px-2">
						<?=!empty($expert_advice) ? $expert_advice : "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like)."?>
					</div>
	    		</div>
	        </div>

	        <div class="colR-4" style="height:200px;overflow-y:hidden;">
	        	<div class="this_txt_captute_divs" style="max-height:100px;overflow-y:hidden;">
	        		<div class="">
	        		<div class="rhtboxlft"><h6 class="text-white">THIS SOIL TEST REPORT HAS BEEN PREPARED BY SOIL TESTING MACHINE BUILT BY KANPUR IIT</h6></div>	
	        		<div class="rhtboxrht"><img src="<?=base_url('assets/pdf-assets/soil_health_test/Capture.PNG')?>" width="80px"></div>	
						<div class="floatclear"></div>
	        		</div>
	        	</div>

	        	<div class="custom_support_divs mt-2" style="max-height:100px;overflow-y:hidden;">
	        		<h6 class="custom_support_divs1">CUSTOM SUPPORT</h6>
	        		<div class="helpline text-white px-2"><strong>HELPLINE.</strong> +7894561230</div>
	        		<div class="helpline text-white px-2"><strong>WHATSAPP.</strong> +7894561230</div>
	        		<div class="helpline text-white px-2 pb-2"><strong>EMAIL.</strong> info@farmologyindia.com</div>
	        	</div>
	        </div>
	    </div>
	</div>
</section>
</body>
</html>