<?php

	$main_logo = base_url("assets/pdf-assets/logo.png");
	$new_logo = base_url("assets/pdf-assets/new_logo_small.png");
	$color_grade = base_url("assets/pdf-assets/color_grade.jpg");

	if(isset($report_data))
	{
		if(isset($report_data["kvi"]))
		{
			$kvi = $report_data["kvi"];
			$kvi_image = $kvi["image_url"];
			$crop_health_levels = $kvi["crop_health_levels"];

			if($crop_health_levels["Excellent"]==100)
			{
				$crop_health_result = "<span color='#0ced07'>
									   	&nbsp; EXCELLENT
									   </span>";
			}
			else if($crop_health_levels["Good"]==100)
			{
				$crop_health_result = "<span color='#e8d904'>
									   	&nbsp; GOOD
									   </span>";
			}
			else if($crop_health_levels["Poor"]==100)
			{
				$crop_health_result = "<span color='#db0e02'>
									   	&nbsp; BAD
									   </span>";
			}
		}

		if(isset($report_data["rvi"]))
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

		$soil_moisture_result = "0.56 <span color='#e8d904'>(GOOD)</span>";
		if(isset($report_data["sm"]))
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
			margin: 2px 5px 2px 5px;
			height: 240px;
		}

		.image_container {
			width: 75%;
			height: 240px;
			background-color: #e8e8e8;
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

		.expert_advice_header {
			font-size: 14px;
			font-weight: bold;
			color: #aef231;
			margin: 5px 3px;
			text-align: left;
		}

		.expert_advice_body {
			height: 130px;
			background-color: whitesmoke;
			margin: 0px 3px 3px 3px;
			border-radius: 0px 0px 3px 3px;
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
		</div>

		<div class="info_container">
			<div class="row">
				<div class="col">
					<div class="info_title">CROP TYPE:</div>
					<div class="info_text">
						<?php 
						if(isset($crop_name))
						{
							echo $crop_name; 
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
					<div class="info_text">GROWING STAGE</div>
				</div>
			</div>
		</div>

		<div class="info_detailed_container">
			<div class="info_heading">CROP HEALTH STATUS:</div>
			<div class="info_image_container">
			<?php 
			if(isset($kvi_image))
			{
				echo "<div class='image_container'>
						  <img height='240px' src='{$kvi_image}'/>
					  </div>";

				echo "<div class='color_grade_container'>
						  <div height='30px' class='color_grade_heading'>CROP HEALTH BY COLOR GRADE</div>
						  <img height='210px' src='{$color_grade}'/>
					  </div>";
			}
			?>
			</div>
			<div class="info_details">
				<div>YOUR CROP HEALTH STATUS =><?=$crop_health_result?></div>
			</div>
		</div>

		<div class="info_detailed_container">
			<div class="info_heading">CROP GROWTH STATUS:</div>
			<div class="info_image_container">
			<?php 
			if(isset($rvi_image))
			{
				echo "<div class='image_container'>
						  <img height='240px' src='{$rvi_image}'/>
					  </div>";

				echo "<div class='color_grade_container'>
						  <div height='30px' class='color_grade_heading'>CROP GROWTH BY COLOR GRADE</div>
						  <img height='210px' src='{$color_grade}'/>
					  </div>";
			}
			?>
			</div>
			<div class="info_details">
				<div>YOUR CROP GROWTH STATUS =><?=$crop_growth_result?></div>
			</div>
		</div>

		<div class="info_container">
			<div class="row">
				<div class="col">
					<div class="info_title" style="color:#02bce6;">SOIL MOISTURE:</div>
					<div class="info_text"><?=$soil_moisture_result?></div>
				</div>
				<div class="info_text" style="color:#02bce6;">[ NORMAL RANGE- 0.00 TO 1.00 ]</div>
			</div>
		</div>

		<div class="advice_section">
			<div class="expert_advice_container">
				<div class="expert_advice_header">EXPERTS ADVICE:</div>
				<div class="expert_advice_body">
					In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before the final copy is available.
				</div>
			</div>
			<div class="recommended_products_container">
				<div class="expert_advice_header">PRODUCT RECOMMENDATION:</div>
				<div class="expert_advice_body">
					<ul>
					  <li>Coffee</li>
					  <li>Tea</li>
					  <li>Milk</li>
					  <li>Coffee</li>
					  <li>Tea</li>
					  <li>Milk</li>
					  <li>Coffee</li>
					  <li>Tea</li>
					  <li>Milk</li>
					</ul>
				</div>
			</div>
		</div>

	</main>
</body>
</html>
