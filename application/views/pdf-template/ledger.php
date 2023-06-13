<?php 

$main_logo = "https://admin.surobhiagro.in/assets/pdf-assets/logo.png";
$new_logo = "https://admin.surobhiagro.in/assets/pdf-assets/new_logo_small.png";

if (empty($customer_name)) {
    $customer_name = "<span style='color:#CCC;'>unknown</span>";
}

if (!empty($date_range)) {
    $date_range = $date_range["start"]." - ".$date_range["end"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Details PDF</title>
    <style>
        body {
			margin: 0;
			padding: 0;
			font-family: Calibri;
		}
        .table-with-borders {
            border-collapse: collapse;
            text-align: center;
            font-size: 12px;
        }
        .crop-details {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <header>
		<table>
			<tr>
				<td width="15%" align="center">
					<img height="100px" src="<?=$new_logo?>"/><br/>
                    <h2>Ledger Details</h2>
				</td>
			</tr>
            <tr>
                <td width="20%" align="center">
                    <p>
                        <b>Customer Name: </b> <?=$customer_name?> <br/>
                        <b>Date Range: </b> <?=$date_range?>
                    </p>
                </td>
            </tr>
		</table>
	</header>
    <main>

        <?php if (!empty($crop_sales)) { ?>
            <div id="crop_sales_container" style="margin-bottom:20px;">
                <h3 style="margin-bottom:0px;">Crop Sales</h3>
                <hr style="margin-top:3px;"><br/>
                <table width="100%" border="1" class="table-with-borders">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Crop</th>
                            <th>Date</th>
                            <th>Total Produce</th>
                            <th>Sale Value</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crop_sales as $i => $crop_sale_details) { ?>
                            <tr>
                                <td><?=$i+1?></td>
                                <td width="15%" align="left">
                                    <div class="crop-details">
                                        <img height="20px" src="<?=$crop_sale_details->crop_image?>" style="margin:0px 3px;">
                                        <span style="padding-bottom:5px;"><?=$crop_sale_details->crop_name?></span>
                                    </div>
                                </td>
                                <td><?=date("d/m/Y", strtotime($crop_sale_details->date))?></td>
                                <td><?=$crop_sale_details->total_produce?></td>
                                <td>₹ <?=number_format($crop_sale_details->sale_value, 0)?></td>
                                <td width="35%">
                                    <?php if (!empty($crop_sale_details->reference)) {
                                        echo $crop_sale_details->reference;
                                    }
                                    else {
                                        echo '<div style="font-weight:500; color:#CCC;">N/A</div>';
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <?php if (!empty($other_incomes)) { ?>
            <div id="other_incomes_container" style="margin-bottom:20px;">
                <h3 style="margin-bottom:0px;">Other Incomes</h3>
                <hr style="margin-top:3px;"><br/>
                <table width="100%" border="1" class="table-with-borders">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Income Type</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($other_incomes as $i => $other_income_details) { ?>
                            <tr>
                                <td width="30px"><?=$i+1?></td>
                                <td><?=$other_income_details->income_type?></td>
                                <td><?=date("d/m/Y", strtotime($other_income_details->date))?></td>
                                <td>₹ <?=number_format($other_income_details->amount, 0)?></td>
                                <td width="35%">
                                    <?php if (!empty($other_income_details->reference)) {
                                        echo $other_income_details->reference;
                                    }
                                    else {
                                        echo '<div style="font-weight:500; color:#CCC;">N/A</div>';
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <?php if (!empty($product_related_expenses)) { ?>
            <div id="product_related_expenses_container" style="margin-bottom:20px;">
                <h3 style="margin-bottom:0px;">Product Related Expenses</h3>
                <hr style="margin-top:3px;"><br/>
                <table width="100%" border="1" class="table-with-borders">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Expense Category</th>
                            <th>Product Type</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($product_related_expenses as $i => $details) { ?>
                            <tr>
                                <td width="30px"><?=$i+1?></td>
                                <td><?=$details->category_name?></td>
                                <td><?=$details->product_type?></td>
                                <td><?=date("d/m/Y", strtotime($details->date))?></td>
                                <td>₹ <?=number_format($details->amount, 0)?></td>
                                <td width="35%">
                                    <?php if (!empty($product_related_expenses->reference)) {
                                        echo $product_related_expenses->reference;
                                    }
                                    else {
                                        echo '<div style="font-weight:500; color:#CCC;">N/A</div>';
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <?php if (!empty($farming_related_expenses)) { ?>
            <div id="farming_related_expenses_container" style="margin-bottom:20px;">
                <h3 style="margin-bottom:0px;">Farming Related Expenses</h3>
                <hr style="margin-top:3px;"><br/>
                <table width="100%" border="1" class="table-with-borders">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Expense Category</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($farming_related_expenses as $i => $details) { ?>
                            <tr>
                                <td width="30px"><?=$i+1?></td>
                                <td><?=$details->category_name?></td>
                                <td><?=date("d/m/Y", strtotime($details->date))?></td>
                                <td>₹ <?=number_format($details->amount, 0)?></td>
                                <td width="35%">
                                    <?php if (!empty($farming_related_expenses->reference)) {
                                        echo $farming_related_expenses->reference;
                                    }
                                    else {
                                        echo '<div style="font-weight:500; color:#CCC;">N/A</div>';
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <?php if (!empty($other_expenses)) { ?>
            <div id="other_expenses_container" style="margin-bottom:20px;">
                <h3 style="margin-bottom:0px;">Other Expenses</h3>
                <hr style="margin-top:3px;"><br/>
                <table width="100%" border="1" class="table-with-borders">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Expense Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($other_expenses as $i => $details) { ?>
                            <tr>
                                <td width="30px"><?=$i+1?></td>
                                <td><?=$details->expense_name?></td>
                                <td><?=date("d/m/Y", strtotime($details->date))?></td>
                                <td>₹ <?=number_format($details->amount, 0)?></td>
                                <td width="35%">
                                    <?php if (!empty($other_expenses->reference)) {
                                        echo $other_expenses->reference;
                                    }
                                    else {
                                        echo '<div style="font-weight:500; color:#CCC;">N/A</div>';
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </main>
</body>
</html>