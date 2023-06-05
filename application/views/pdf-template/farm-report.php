<?php

if(isset($report_data) && count($report_data)>0){
	
	if(isset($report_data["KVI"]))
	{
		$KVI = $report_data["KVI"];
	}

	if(isset($report_data["RVI"]))
	{
		$RVI = $report_data["RVI"];
	}
	 
	if(isset($report_data["SM"]))
	{
		$SM = $report_data["SM"];
	}
}

if(isset($recommended_products)){ 
	$RP = json_decode(json_encode($recommended_products), true); 
}

if(isset($SM)){
	$soil_moisture = number_format($SM["average_soil_moisture"], 2);
	if($soil_moisture<=0.30)
	{
		$soil_moisture_value = "<span color='#ff4d4d'>$soil_moisture</span>";
	}
	else if($soil_moisture<=0.60)
	{
		$soil_moisture_value = "<span color='#ff6633'>$soil_moisture</span>";
	}
	else
	{
		$soil_moisture_value = "<span color='#47d16a'>$soil_moisture</span>";
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
			font-family: Calibri;
			color: #333;
		}

		h2 {
			color: #333;
		}

		hr {
			height: 2px;
			color: #bdbdbd;
			margin: 8px auto;
		}

		table {
			width: 100%;
			height: auto;
		}

		table tr {
			width: 100%;
			padding: 20px 10px;
			color: #707070;
			font-size: 16px;
			font-weight: 500;
		}

		table tr td {
			margin: 5px;
			padding: 3px;
			/*background-color: #f5f5f5;*/
		}
	</style>
</head>
<body>
	<header>
		<table>
			<tr>
				<td width="25%">
					<b style="font-size:16px"><?php if(isset($pdf_name)){print $pdf_name;}?></b>
				</td>
				<td width="50%" colspan="2" align="center">
					<img height="120px" src="<?=FRONT_URL?>uploads/default/new_logo_medium.png"/>
				</td>
				<td width="25%" align="right">
					<b style="font-size:16px"><?php if(isset($pdf_date)){print "Date: ".$pdf_date;}?></b>
				</td>
			</tr>
		</table>
	</header>
	<main>

		<?php if(isset($SM)){ ?>
			<table>
				<tr>
					<td colspan="2">
						<h2>Soil Moisture</h2>
					</td>
					<td width="10%" align="right" style="font-size:20px">
						<b><?=$soil_moisture_value;?></b>
					</td>
					<td width="20%" align="right" style="color:#b2b2b2; font-size:20px;"><b>0 - 1</b></td>
				</tr>
			</table>
			<br/><br/>
		<?php } ?>

		<?php if(isset($RVI)){ ?>
			<table>
				<tr>
					<td colspan="4"><h2>Crop Health</h2><hr/></td>
				</tr>
				<tr width="60%">
					<td align="center">
						<?='<img height="250px" src="'.$RVI["image_url"].'">';?>
					</td>
					<td style="font-size:22px; padding-left:20px;">
						<?php if(isset($RVI["crop_health_levels"])){
							$CHL =  $RVI["crop_health_levels"];
							$levels = '<div>
											<div>Excellent - '.$CHL["Excellent"].'</div>
											<div>Good - '.$CHL["Good"].'</div>
											<div>Poor - '.$CHL["Poor"].'</div>
									   </div>';
							print $levels;
						}?>
					</td>
				</tr>
			</table>
			<br/><br/>
		<?php } ?>

		<?php if(isset($KVI)){ ?>
			<table>
				<tr>
					<td colspan="4"><h2>Crop Growth</h2><hr/></td>
				</tr>
				<tr width="60%">
					<td align="center">
						<?='<img height="250px" src="'.$KVI["image_url"].'">';?>
					</td>
					<td style="font-size:22px; padding-left:20px;">
						<?php if(isset($KVI["crop_growth_levels"])){
							$CHL =  $KVI["crop_growth_levels"];
							$levels = '<div>
											<div>Excellent - '.$CHL["Excellent"].'</div>
											<div>Good - '.$CHL["Good"].'</div>
											<div>Poor - '.$CHL["Poor"].'</div>
									   </div>';
							print $levels;
						}?>
					</td>
				</tr>
			</table>
			<br/><br/>
		<?php } ?>

		<?php if(isset($expert_advice)){ ?>
			<table>
				<tr>
					<td colspan="4">
						<h2>Expert Advice</h2><hr/>
						<p><?=$expert_advice;?></p>
					</td>
				</tr>
			</table>
			<br/>
		<?php } ?>

		<?php if(isset($RP)){ 
			print '<table width="80%" align="left" cellspacing="3px" cellpadding="2px" style="padding-left:20px;">';
					foreach($RP as $product)
					{
						$productHTML = '<tr>
											<td colspan="1" width="80px" align="center" style="border:2px solid #ccc;background-color:#f2f2f2;">
												<img height="65px" src="'.$product["image"].'">
											</td>
											<td colspan="3" align="left" style="font-size:16px;padding-left:15px;">
												<span>'.$product["title"].'</span>
											</td>
											<td></td>
											<td></td>
										</tr>';

						print $productHTML;
					}
			print '</table>';
		} ?>

	</main>
</body>
</html>
