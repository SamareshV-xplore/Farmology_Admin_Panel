<?php
	
	if(!isset($crop_name))
	{$crop_name = "N/A";}

	if(!isset($sowing_date))
	{$sowing_date = "N/A";}

	if(!isset($owner_name))
	{$owner_name = "N/A";}

	if(!empty($crop_health_stage_details))
	{$crop_stage = $crop_health_stage_details->stage_name;}

	if(!isset($expert_advice))
	{
		$expert_advice = "N/A";
	}

	if(!isset($recommended_products))
	{
		$recommended_products = "N/A";	
	}
	else
	{
		$recommended_products_list = "<ul>";
		foreach($recommended_products as $recommended_product)
		{
			$recommended_products_list .= "<li id='{$recommended_product->id}'>
												<img width='30px' height='30px' src='{$recommended_product->image}'/>&nbsp; {$recommended_product->title}
											</li>";
		}
		$recommended_products_list .= "</ul>";

		$recommended_products = $recommended_products_list;
	}

	$no_image_url = "https://media.istockphoto.com/vectors/thumbnail-image-vector-graphic-vector-id1147544807?k=20&m=1147544807&s=612x612&w=0&h=pBhz1dkwsCMq37Udtp9sfxbjaMl27JUapoyYpQm0anc=";
	$kvi_image = $no_image_url;
	$rvi_image = $no_image_url;

	$main_logo = base_url("assets/pdf-assets/logo.png");
	$new_logo = base_url("assets/pdf-assets/new_logo_small.png");
	$color_grade = base_url("assets/pdf-assets/color_grade.jpg");

	$crop_health_result = "<span>&nbsp; N/A</span>";
	$crop_growth_result = "<span>&nbsp; N/A</span>";
	$plant_moisture_result = "0.00 <span>(N/A)</span>";

	$common_scale = [
		["min" => 0, "max" => 30, "value" => "BAD", "color" => "#db0e02"],
		["min" => 30, "max" => 60, "value" => "MEDIUM", "color" => "#e8d904"],
		["min" => 60, "max" => 100, "value" => "GOOD", "color" => "#0ced07"]
	];

	$ndwi_scale = [
		["min" => 0, "max" => 20, "value" => "BAD", "color" => "#db0e02"],
		["min" => 20, "max" => 40, "value" => "MEDIUM", "color" => "#e8d904"],
		["min" => 40, "max" => 100, "value" => "GOOD", "color" => "#0ced07"]
	];

	if(!empty($report_data))
	{
		if (!empty($report_data["ndvi"]))
		{
			$ndvi_value = (!empty($report_data["ndvi"])) ? $report_data["ndvi"]["value"] : NULL;
			foreach ($common_scale as $key => $value)
			{
				if ($value["min"] < $ndvi_value && $value["max"] >= $ndvi_value)
				{
					$crop_health_result = "<span color='".$value['color']."'>&nbsp; ".$value['value']." (".$ndvi_value." out of 100)</span>";
				}
			}
		}

		if (!empty($report_data["savi"]))
		{
			$savi_value = (!empty($report_data["savi"])) ? $report_data["savi"]["value"] : NULL;
			foreach ($common_scale as $key => $value)
			{
				if ($value["min"] < $savi_value && $value["max"] >= $savi_value)
				{
					$crop_growth_result = "<span color='".$value['color']."'>&nbsp; ".$value['value']." (".$savi_value." out of 100)</span>";
				}
			}
		}

		if (!empty($report_data["ndwi"]))
		{
			$ndwi_value = (!empty($report_data["ndwi"])) ? $report_data["ndwi"]["value"] : NULL;
			foreach ($ndwi_scale as $key => $value)
			{
				if ($value["min"] < $ndwi_value && $value["max"] >= $ndwi_value)
				{
					$plant_moisture_result = "<span color='".$value['color']."'>&nbsp; ".$value['value']." (".$ndwi_value." out of 100)</span>";
				}
			}
		}

		if(!empty($report_data["kvi"]))
		{
			$kvi = $report_data["kvi"];
			$kvi_image = $kvi["image_url"];
			$crop_health_levels = $kvi["crop_health_levels"];

			$crop_health_levels_max = 0;
			$crop_health_result = "<span color='#db0e02'>&nbsp; BAD</span>";

			if ($crop_health_levels_max < $crop_health_levels["Excellent"])
			{
				$crop_health_levels_max = $crop_health_levels["Excellent"];
				$crop_health_result = "<span color='#0ced07'>&nbsp; EXCELLENT</span>";
			}

			if ($crop_health_levels_max < $crop_health_levels["Good"])
			{
				$crop_health_levels_max = $crop_health_levels["Good"];
				$crop_health_result = "<span color='#e8d904'>&nbsp; GOOD</span>";
			}

			if ($crop_health_levels_max < $crop_health_levels["Poor"])
			{
				$crop_health_levels_max = $crop_health_levels["Poor"];
				$crop_health_result = "<span color='#db0e02'>&nbsp; BAD</span>";
			}
		}

		if(!empty($report_data["rvi"]))
		{
			$rvi = $report_data["rvi"];
			$rvi_image = $rvi["image_url"];
			$crop_growth_levels = $rvi["crop_growth_levels"];

			if($crop_growth_levels["Excellent"]==100)
			{
				$crop_growth_result = "<span color='#0ced07'>
									   	&nbsp; EXCELLENT
									   </span>";;
			}
			else if($crop_growth_levels["Good"]==100)
			{
				$crop_growth_result = "<span color='#e8d904'>
									   	&nbsp; GOOD
									   </span>";
			}
			else if($crop_growth_levels["Poor"]==100)
			{
				$crop_growth_result = "<span color='#db0e02'>
									   	&nbsp; BAD
									   </span>";
			}
		}

		if(!empty($report_data["sm"]))
		{
			$sm = $report_data["sm"];
			$sm_image = $sm["image_url"];

			if(isset($sm["average_soil_moisture"]))
			{
				$soil_moisture = number_format($sm["average_soil_moisture"], 2);
				if($soil_moisture<=0.30)
				{
					$soil_moisture_result = "{$soil_moisture} <span color='#db0e02'>(BAD)</span>";
				}
				else if($soil_moisture<=0.60)
				{
					$soil_moisture_result = "{$soil_moisture} <span color='#e8d904'>(GOOD)</span>";
				}
				else
				{
					$soil_moisture_result = "{$soil_moisture} <span color='#0ced07'>(EXCELLENT)</span>";
				}
			}
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<style>

		body {
			margin: 0;
			padding: 0;
			font-family: Calibri;
			background-color: #282828;
			color: #333;
		}

		#expert_advice_div {
			font-family: siyamrupali;
		}

		header {
			margin: 0;
			padding: 0;
			width: 100%;
			height: 8%;
		}

		.pdf_title {
			margin: 0;
			padding: 0;
			width: 100%;
		}

		.title_text {
			width: 52%;
			margin: auto;
			margin-top: -2px;
			padding: 0px 5px 3px 5px;
			color: whitesmoke;
			background-color: #282828;
			font-size: 30px;
			border-radius: 0 0 10px 10px;
		}

		.owner_name {
			color: whitesmoke;
		}

		main {
			margin: 0;
			padding: 0;
			width: 100%;
			height: 85%;
			background-color: #515151;
			text-align: center;
		}

		.info_container {
			width: 92%;
			margin: 5px auto;
			padding: 5px 15px;
			border-radius: 10px;
			color: whitesmoke;
			background-color: #282828;
		}

		.info_detailed_container {
			width: 96%;
			margin: 5px auto;
			border-radius: 10px;
			color: whitesmoke;
			background-color: #282828;
		}

		.info_heading {
			margin: 5px 15px 0px 15px;
			font-size: 20px;
			font-weight: bold;
			color: #aef231;
			text-align: left;
		}

		.info_image_container {
			margin: 15px 5px;
			height: 260px;
			align-items: center;
		}

		.image_container {
			width: 32%;
			height: 240px;
			background: transparent;
			padding: 0 3px;
			float: left;
		}

		.color_grade_container {
			width: 22%;
			height: 240px;
			float: right;
			margin-right: 5px;
		}

		.color_grade_heading {
			font-size: 15px;
			font-weight: bold;
			color: whitesmoke;
			text-align: center;
		}

		.info_details {
			margin: 0px 15px 5px 15px;
			font-size: 20px;
			font-weight: bold;
			color: whitesmoke;
			text-align: left;
		}

		.info_image {
			width: 50%;
			background-color: #f5f5f5;
			border: 2px solid ccc;
			height: 180px;
		}

		.row {
			width: 100%;
		}

		.col {
			width: 50%;
			float: left;
			display: inline-flex;
		}

		.info_title, .info_text {
			margin: 0;
			padding: 0;
			font-size: 18px;
			font-weight: bold;
			width: 50%;
			float: left;
			text-align: left;
			color: whitesmoke;
		}

		.info_title {
			color: #aef231;
		}

		.advice_section {
			width: 100%;
		}

		.expert_advice_container {
			width: 60%;
			height: 150px;
			margin: 5px 3px 5px 10px;
			background-color: #282828;
			border-radius: 5px;
			float: left;
		}

		.possible_pests_and_diseases_listing_container {
			width: 97%;
			height: 150px;
			margin: 5px 10px 5px 10px;
			background-color: #282828;
			border-radius: 5px;
		}

		.advice_header {
			font-size: 14px;
			font-weight: bold;
			color: #aef231;
			margin: 5px 3px;
			text-align: left;
		}

		.advice_body {
			height: 130px;
			background-color: whitesmoke;
			margin: 0px 3px 3px 3px;
			border-radius: 0px 0px 3px 3px;
			text-align: left;
		}

		.advice_body {
			padding: 3px 5px;
		}

		.advice_body ul {
			list-style: none;
			margin: 0;
			padding: 0;
		}



		.recommended_products_container {
			width: 36%;
			height: 150px;
			margin: 0px 10px 5px 3px;
			background-color: #282828;
			border-radius: 5px;
			float: right;
		}

		.color_box {
			width: 30px;
			height: 30px;
			border-radius: 2px;
		}

		.pr-2 {
			padding-right: 8px;
		}

		.text-left {
			text-align: left;
		}
	</style>
</head>
<body>

	<header>
		<table>
			<tr>
				<td width="20%">
					<img height="100px" src="<?=$new_logo?>"/>
				</td>
				<td width="60%" colspan="2" align="center">
					<img height="140px" src="<?=$main_logo?>"/>
				</td>
				<td width="20%" align="right">
					<img height="100px" src="<?=$new_logo?>"/>
				</td>
			</tr>
		</table>
	</header>
	<main>

		<div class="pdf_title" align="center">
			<h2 class="title_text">CROP HEALTH REPORT</h2>
			<h3 class="owner_name">USERNAME : <?=strtoupper($owner_name)?></h3>
		</div>

		<div class="info_container">
			<div class="row">
				<div class="col">
					<div class="info_title">CROP TYPE:</div>
					<div class="info_text">
						<?php 
						if(isset($crop_name))
						{
							echo strtoupper($crop_name); 
						}
						else
						{
							echo "PADDY";
						}?>
					</div>
				</div>
				<div class="col">
					<div class="info_title">REPORT DATE:</div>
					<div class="info_text"><?=date("d-m-Y")?></div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="info_title">SOWING DATE:</div>
					<div class="info_text">
						<?php 
						if(isset($sowing_date))
						{
							echo $sowing_date;
						}
						else
						{
							echo "22<sup>th</sup> FEB 2022";
						}?>
					</div>
				</div>
				<div class="col">
					<div class="info_title">CROP STAGE:</div>
					<div class="info_text">
						<?php 
						if(isset($crop_stage))
						{
							echo strtoupper($crop_stage);
						}
						else
						{
							echo "GROWING STAGE";
						}?>
					</div>
				</div>
			</div>
		</div>

		<?php if (!empty($report_data["ndvi"])) { ?>
			<div class="info_detailed_container">
				<div class="info_heading">CROP HEALTH STATUS:</div>
				<div class="info_image_container">
					<?php if (isset($report_data["ndvi"]["Field Image"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["ndvi"]["Field Image"]}'/>
							</div>";
					} ?>

					<?php if (!empty($report_data["ndvi"]["Field Index Area Image"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["ndvi"]["Field Index Area Image"]}'/>
							</div>";
					} ?>

					<?php if (!empty($report_data["ndvi"]["Analysis Scale"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["ndvi"]["Analysis Scale"]}'/>
							</div>";
					} ?>
				</div>
				<div class="info_details">
					<div>YOUR CROP HEALTH STATUS =><?=$crop_health_result?></div>
				</div>
			</div>
		<?php } ?>

		<?php if (!empty($report_data["savi"])) { ?>
			<div class="info_detailed_container">
				<div class="info_heading">CROP GROWTH STATUS:</div>
				<div class="info_image_container">
					<?php if(isset($report_data["savi"]["Field Image"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["savi"]["Field Image"]}'/>
							</div>";
					} ?>

					<?php if (!empty($report_data["savi"]["Field Index Area Image"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["savi"]["Field Index Area Image"]}'/>
							</div>";
					} ?>

					<?php if (!empty($report_data["savi"]["Analysis Scale"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["savi"]["Analysis Scale"]}'/>
							</div>";
					} ?>
				</div>
				<div class="info_details">
					<div>YOUR CROP GROWTH STATUS =><?=$crop_growth_result?></div>
				</div>
			</div>
		<?php } ?>
			
		<?php if (!empty($report_data["ndwi"])) { ?>
			<div class="info_detailed_container">
				<div class="info_heading">PLANT MOISTURE STATUS:</div>
				<div class="info_image_container">
					<?php if (isset($report_data["ndwi"]["Field Image"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["ndwi"]["Field Image"]}'/>
							</div>";
					} ?>

					<?php if (!empty($report_data["ndwi"]["Field Inedx Area Image"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["ndwi"]["Field Index Area Image"]}'/>
							</div>";
					} ?>

					<?php if (!empty($report_data["ndwi"]["Analysis Scale"])) {
						echo "<div class='image_container'>
								<img height='240px' src='{$report_data["ndwi"]["Analysis Scale"]}'/>
							</div>";
					} ?>
				</div>
				<div class="info_details">
					<div>YOUR PLANT MOISTURE STATUS =><?=$plant_moisture_result?></div>
				</div>
			</div>
		<?php } ?>

		<div class="advice_section">
			<div class="expert_advice_container">
				<div class="advice_header">EXPERTS ADVICE:</div>
				<div id="expert_advice_div" class="advice_body"><?=$expert_advice?></div>
			</div>
			<div class="recommended_products_container">
				<div class="advice_header">PRODUCT RECOMMENDATION:</div>
				<div class="advice_body"><?=$recommended_products?></div>
			</div>

			<?php if (!empty($list_of_possible_diseases_and_pests["diseases"]) || !empty($list_of_possible_diseases_and_pests["pests"])) {?>
			<div class="possible_pests_and_diseases_listing_container">
				<div class="advice_header">LIST OF POSSIBLE CROP DISEASES AND PESTS:</div>
				<div class="advice_body">
					<?php if (!empty($list_of_possible_diseases_and_pests["diseases"])) {
					$diseases = $list_of_possible_diseases_and_pests["diseases"];?>
					<table style="margin-top: 5px;">
						<thead><tr><th align="left">Crop Diseases</th></tr></thead>
						<tbody>
							<?php foreach ($diseases as $i => $disease_name) {?>
								<tr><td><?=round($i+1).". ".$disease_name?></td></tr>
							<?php } ?>
						</tbody>
					</table>
					<?php } ?>
					
					<?php if (!empty($list_of_possible_diseases_and_pests["pests"])) {
					$pests = $list_of_possible_diseases_and_pests["pests"];?>
					<table style="margin-top: 10px;">
						<thead><tr><th align="left">Pest Attacks</th></tr></thead>
						<tbody>
							<?php foreach ($pests as $i => $pest_name) {?>
								<tr><td><?=round($i +1).". ".$pest_name?></td></tr>
							<?php } ?>
						</tbody>
					</table>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

		</div>
	</main>
</body>
</html>
