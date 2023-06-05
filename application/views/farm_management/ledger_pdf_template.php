<?php 

$main_logo = base_url("assets/pdf-assets/logo.png");
$new_logo = base_url("assets/pdf-assets/new_logo_small.png");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Details PDF</title>
    <style>
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
                        <b>Customer Name: </b> Samaresh Adak <br/>
                        <b>Date Range: </b> 01/05/2023 - 10/05/2023
                    </p>
                </td>
            </tr>
		</table>
	</header>
    <main>
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
                    <tr>
                        <td>1</td>
                        <td width="15%" align="left">
                            <div class="crop-details">
                                <img height="20px" src="<?=FRONT_URL."uploads/crop/1627459359-rice-plant-vector-design.png"?>" style="margin:0px 3px;">
                                <span style="padding-bottom:5px;">Rice</span>
                            </div>
                        </td>
                        <td>01/05/2023</td>
                        <td>1 Ton</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td width="15%" align="left">
                            <div class="crop-details">
                                <img height="20px" src="<?=FRONT_URL."uploads/crop/1627459359-rice-plant-vector-design.png"?>" style="margin:0px 3px;">
                                <span style="padding-bottom:5px;">Rice</span>
                            </div>
                        </td>
                        <td>01/05/2023</td>
                        <td>1 Ton</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td width="15%" align="left">
                            <div class="crop-details">
                                <img height="20px" src="<?=FRONT_URL."uploads/crop/1627459359-rice-plant-vector-design.png"?>" style="margin:0px 3px;">
                                <span style="padding-bottom:5px;">Rice</span>
                            </div>
                        </td>
                        <td>01/05/2023</td>
                        <td>1 Ton</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td width="15%" align="left">
                            <div class="crop-details">
                                <img height="20px" src="<?=FRONT_URL."uploads/crop/1627459359-rice-plant-vector-design.png"?>" style="margin:0px 3px;">
                                <span style="padding-bottom:5px;">Rice</span>
                            </div>
                        </td>
                        <td>01/05/2023</td>
                        <td>1 Ton</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td width="15%" align="left">
                            <div class="crop-details">
                                <img height="20px" src="<?=FRONT_URL."uploads/crop/1627459359-rice-plant-vector-design.png"?>" style="margin:0px 3px;">
                                <span style="padding-bottom:5px;">Rice</span>
                            </div>
                        </td>
                        <td>01/05/2023</td>
                        <td>1 Ton</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Income Type 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Income Type 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Income Type 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Income Type 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Income Type 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            N.P.K Fertilizers
                        </td>
                        <td>Product Type 01</td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            N.P.K Fertilizers
                        </td>
                        <td>Product Type 01</td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            N.P.K Fertilizers
                        </td>
                        <td>Product Type 01</td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            N.P.K Fertilizers
                        </td>
                        <td>Product Type 01</td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            N.P.K Fertilizers
                        </td>
                        <td>Product Type 01</td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Labour Expense
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Labour Expense
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Labour Expense
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Labour Expense
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Labour Expense
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Expense 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Expense 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Expense 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Expense 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30px">1</td>
                        <td>
                            Expense 01
                        </td>
                        <td>01/05/2023</td>
                        <td>₹ 15,000</td>
                        <td width="35%">
                            <div style="font-weight:500; color:#CCC;">N/A</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>