<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="custom_style.css">
    <title>Farmology</title>


    
    <style>

.container {
	width: 1140px;
	padding-right: 15px;
	padding-left: 15px;
	margin-right: auto;
	margin-left: auto;
}
.row {
	margin-right: -15px;
	margin-left: -15px;
}
* {
    box-sizing: border-box;
}

        body {
	background: #fff;
	color: #191919;
	font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-style: normal;
	font-size: 11px;
	line-height: 1.6;
	margin: 0;
	padding: 0;
}
sub, sup {
	font-size: 75%;
	position: relative;
	vertical-align: baseline;
}
sup {
	top: 10px;
	left: 10px;
}
sub {
	bottom: 10px;
	right: 10px
}
ul {
	margin: 0;
	padding: 0;
	list-style-type: none;
}
ul li {
	padding: 0;
	margin: 0;
}
h1, h2, h3, h4, h5, h6{
	padding: 0;
	margin:0;
	font-family: 'Poppins', sans-serif;
	font-style: normal;
}
h1 {
	font-size: 30px;
	line-height: 1.4;
	color: #191919;
    font-weight: bold;
}
h2 {
	font-size: 36px;
	line-height: 1.4;
	font-weight: bold;
    color: #191919;
}
h3 {
	font-size: 30px;
	line-height: 1.4;
	color: #191919;
    font-weight: 600;
}
h4 {
	font-size: 26px;
	line-height: 1.4;
	color: #191919;
    font-weight: 600;
}
h5 {
    font-size: 15px;
    font-weight: 600;
	line-height: 1.4;
	color: #191919;
    text-transform: uppercase;
}
p {
	padding: 0;
	margin:0 0 20px;
}
a {
	-webkit-transition: all 0.2s linear;
	-moz-transition: all 0.2s linear;
	-o-transition: all 0.2s linear;
	transition: all 0.2s linear;
	color: #454545;
	outline: none;
	text-decoration: none;
}
a:hover, a:focus {
	color: #454545;
	-webkit-transition: all 0.2s linear;
	-moz-transition: all 0.2s linear;
	-o-transition: all 0.2s linear;
	transition: all 0.2s linear;
	text-decoration: none !important;
	outline:none !important;
}

.invoicesec {
    width: 80%;
    margin: auto;
    padding: 0 15px;
}
.logo img {
    width: 250px;
}
.invcheader {
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-transform: uppercase;
}
.invoicesec {
    font-weight: 600;
    color:#191919;
    text-transform: uppercase;
}
.billbysec h4 {
    /* border-bottom: 3px solid #000; */
    display: block;
    padding-bottom: 5px;
    font-size: 15px;
}
.invcdtls h5 {
    font-weight: 600;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-size: 15px;
}
.invcttldtls h5 span {
    font-weight: 500;
}
.billbysec {
    font-weight: 600;
    text-transform: uppercase;
}
.billbysec h5 {
   font-size: 15px;
   font-weight: 600;
}
.billbysec h5 span {
    font-size: 14px;
 }
 .customtable thead th {
    text-transform: uppercase;
    background-color: #e5f9ff;
    font-weight: 600;
 }

 .descwith {
    width: 200px;
 }
 .customtable table tbody tr td {
    height: 110px;
    overflow: auto;
 }
 .desccontnt, .Amntcnt {
    position: relative;
 }
 .desccontnt::before {
    content: "DELIVERY CHARGES";
    position: absolute;
    bottom: 10px;
    width: 100px;
    height: 20px;
    left: 10px;
    color: blue;
    font-size: 13px;
 }

 .Amntcnt::before {
    content: "Rs.";
    position: absolute;
    bottom: 10px;
    width: 100px;
    height: 20px;
    left: 10px;
    color: blue;
    font-size: 13px;
 }
 .tblrswdth {
    width:60px;
 }
 .inclttlwrd {
    height: 80px;
 }
 .paymtntimg {
    margin-top: 60px;
 }
 .paymntdtls {
    text-transform: uppercase;
    font-size: 12px!important;
    margin-top: 20px;
 }
 .paymntdtls li {
    margin-bottom: 10px;
 }
 .pmntwdth {
    width: 150px;
    display: inline-block;
 }
 .rounded {
	border-radius: .25rem !important;
}
.border {
	border: 1px solid #dee2e6 !important;
}

 @media (min-width: 576px) {

 .m-sm-0 {
    margin: 0 !important;
  }
  .mt-sm-0,
  .my-sm-0 {
    margin-top: 0 !important;
  }
  .mr-sm-0,
  .mx-sm-0 {
    margin-right: 0 !important;
  }
  .mb-sm-0,
  .my-sm-0 {
    margin-bottom: 0 !important;
  }
  .ml-sm-0,
  .mx-sm-0 {
    margin-left: 0 !important;
  }
  .m-sm-1 {
    margin: 0.25rem !important;
  }
  .mt-sm-1,
  .my-sm-1 {
    margin-top: 0.25rem !important;
  }
  .mr-sm-1,
  .mx-sm-1 {
    margin-right: 0.25rem !important;
  }
  .mb-sm-1,
  .my-sm-1 {
    margin-bottom: 0.25rem !important;
  }
  .ml-sm-1,
  .mx-sm-1 {
    margin-left: 0.25rem !important;
  }
  .m-sm-2 {
    margin: 0.5rem !important;
  }
  .mt-sm-2,
  .my-sm-2 {
    margin-top: 0.5rem !important;
  }
  .mr-sm-2,
  .mx-sm-2 {
    margin-right: 0.5rem !important;
  }
  .mb-sm-2,
  .my-sm-2 {
    margin-bottom: 0.5rem !important;
  }
  .ml-sm-2,
  .mx-sm-2 {
    margin-left: 0.5rem !important;
  }
  .m-sm-3 {
    margin: 1rem !important;
  }
  .mt-sm-3,
  .my-sm-3 {
    margin-top: 1rem !important;
  }
  .mr-sm-3,
  .mx-sm-3 {
    margin-right: 1rem !important;
  }
  .mb-sm-3,
  .my-sm-3 {
    margin-bottom: 1rem !important;
  }
  .ml-sm-3,
  .mx-sm-3 {
    margin-left: 1rem !important;
  }
  .m-sm-4 {
    margin: 1.5rem !important;
  }
  .mt-sm-4,
  .my-sm-4 {
    margin-top: 1.5rem !important;
  }
  .mr-sm-4,
  .mx-sm-4 {
    margin-right: 1.5rem !important;
  }
  .mb-sm-4,
  .my-sm-4 {
    margin-bottom: 1.5rem !important;
  }
  .ml-sm-4,
  .mx-sm-4 {
    margin-left: 1.5rem !important;
  }
  .m-sm-5 {
    margin: 3rem !important;
  }
  .mt-sm-5,
  .my-sm-5 {
    margin-top: 3rem !important;
  }
  .mr-sm-5,
  .mx-sm-5 {
    margin-right: 3rem !important;
  }
  .mb-sm-5,
  .my-sm-5 {
    margin-bottom: 3rem !important;
  }
  .ml-sm-5,
  .mx-sm-5 {
    margin-left: 3rem !important;
  }
  .p-sm-0 {
    padding: 0 !important;
  }
  .pt-sm-0,
  .py-sm-0 {
    padding-top: 0 !important;
  }
  .pr-sm-0,
  .px-sm-0 {
    padding-right: 0 !important;
  }
  .pb-sm-0,
  .py-sm-0 {
    padding-bottom: 0 !important;
  }
  .pl-sm-0,
  .px-sm-0 {
    padding-left: 0 !important;
  }
  .p-sm-1 {
    padding: 0.25rem !important;
  }
  .pt-sm-1,
  .py-sm-1 {
    padding-top: 0.25rem !important;
  }
  .pr-sm-1,
  .px-sm-1 {
    padding-right: 0.25rem !important;
  }
  .pb-sm-1,
  .py-sm-1 {
    padding-bottom: 0.25rem !important;
  }
  .pl-sm-1,
  .px-sm-1 {
    padding-left: 0.25rem !important;
  }
  .p-sm-2 {
    padding: 0.5rem !important;
  }
  .pt-sm-2,
  .py-sm-2 {
    padding-top: 0.5rem !important;
  }
  .pr-sm-2,
  .px-sm-2 {
    padding-right: 0.5rem !important;
  }
  .pb-sm-2,
  .py-sm-2 {
    padding-bottom: 0.5rem !important;
  }
  .pl-sm-2,
  .px-sm-2 {
    padding-left: 0.5rem !important;
  }
  .p-sm-3 {
    padding: 1rem !important;
  }
  .pt-sm-3,
  .py-sm-3 {
    padding-top: 1rem !important;
  }
  .pr-sm-3,
  .px-sm-3 {
    padding-right: 1rem !important;
  }
  .pb-sm-3,
  .py-sm-3 {
    padding-bottom: 1rem !important;
  }
  .pl-sm-3,
  .px-sm-3 {
    padding-left: 1rem !important;
  }
  .p-sm-4 {
    padding: 1.5rem !important;
  }
  .pt-sm-4,
  .py-sm-4 {
    padding-top: 1.5rem !important;
  }
  .pr-sm-4,
  .px-sm-4 {
    padding-right: 1.5rem !important;
  }
  .pb-sm-4,
  .py-sm-4 {
    padding-bottom: 1.5rem !important;
  }
  .pl-sm-4,
  .px-sm-4 {
    padding-left: 1.5rem !important;
  }
  .p-sm-5 {
    padding: 3rem !important;
  }
  .pt-sm-5,
  .py-sm-5 {
    padding-top: 3rem !important;
  }
  .pr-sm-5,
  .px-sm-5 {
    padding-right: 3rem !important;
  }
  .pb-sm-5,
  .py-sm-5 {
    padding-bottom: 3rem !important;
  }
  .pl-sm-5,
  .px-sm-5 {
    padding-left: 3rem !important;
  }
  .m-sm-auto {
    margin: auto !important;
  }
  .mt-sm-auto,
  .my-sm-auto {
    margin-top: auto !important;
  }
  .mr-sm-auto,
  .mx-sm-auto {
    margin-right: auto !important;
  }
  .mb-sm-auto,
  .my-sm-auto {
    margin-bottom: auto !important;
  }
  .ml-sm-auto,
  .mx-sm-auto {
    margin-left: auto !important;
  }
}

@media (min-width: 768px) {
  .m-md-0 {
    margin: 0 !important;
  }
  .mt-md-0,
  .my-md-0 {
    margin-top: 0 !important;
  }
  .mr-md-0,
  .mx-md-0 {
    margin-right: 0 !important;
  }
  .mb-md-0,
  .my-md-0 {
    margin-bottom: 0 !important;
  }
  .ml-md-0,
  .mx-md-0 {
    margin-left: 0 !important;
  }
  .m-md-1 {
    margin: 0.25rem !important;
  }
  .mt-md-1,
  .my-md-1 {
    margin-top: 0.25rem !important;
  }
  .mr-md-1,
  .mx-md-1 {
    margin-right: 0.25rem !important;
  }
  .mb-md-1,
  .my-md-1 {
    margin-bottom: 0.25rem !important;
  }
  .ml-md-1,
  .mx-md-1 {
    margin-left: 0.25rem !important;
  }
  .m-md-2 {
    margin: 0.5rem !important;
  }
  .mt-md-2,
  .my-md-2 {
    margin-top: 0.5rem !important;
  }
  .mr-md-2,
  .mx-md-2 {
    margin-right: 0.5rem !important;
  }
  .mb-md-2,
  .my-md-2 {
    margin-bottom: 0.5rem !important;
  }
  .ml-md-2,
  .mx-md-2 {
    margin-left: 0.5rem !important;
  }
  .m-md-3 {
    margin: 1rem !important;
  }
  .mt-md-3,
  .my-md-3 {
    margin-top: 1rem !important;
  }
  .mr-md-3,
  .mx-md-3 {
    margin-right: 1rem !important;
  }
  .mb-md-3,
  .my-md-3 {
    margin-bottom: 1rem !important;
  }
  .ml-md-3,
  .mx-md-3 {
    margin-left: 1rem !important;
  }
  .m-md-4 {
    margin: 1.5rem !important;
  }
  .mt-md-4,
  .my-md-4 {
    margin-top: 1.5rem !important;
  }
  .mr-md-4,
  .mx-md-4 {
    margin-right: 1.5rem !important;
  }
  .mb-md-4,
  .my-md-4 {
    margin-bottom: 1.5rem !important;
  }
  .ml-md-4,
  .mx-md-4 {
    margin-left: 1.5rem !important;
  }
  .m-md-5 {
    margin: 3rem !important;
  }
  .mt-md-5,
  .my-md-5 {
    margin-top: 3rem !important;
  }
  .mr-md-5,
  .mx-md-5 {
    margin-right: 3rem !important;
  }
  .mb-md-5,
  .my-md-5 {
    margin-bottom: 3rem !important;
  }
  .ml-md-5,
  .mx-md-5 {
    margin-left: 3rem !important;
  }
  .p-md-0 {
    padding: 0 !important;
  }
  .pt-md-0,
  .py-md-0 {
    padding-top: 0 !important;
  }
  .pr-md-0,
  .px-md-0 {
    padding-right: 0 !important;
  }
  .pb-md-0,
  .py-md-0 {
    padding-bottom: 0 !important;
  }
  .pl-md-0,
  .px-md-0 {
    padding-left: 0 !important;
  }
  .p-md-1 {
    padding: 0.25rem !important;
  }
  .pt-md-1,
  .py-md-1 {
    padding-top: 0.25rem !important;
  }
  .pr-md-1,
  .px-md-1 {
    padding-right: 0.25rem !important;
  }
  .pb-md-1,
  .py-md-1 {
    padding-bottom: 0.25rem !important;
  }
  .pl-md-1,
  .px-md-1 {
    padding-left: 0.25rem !important;
  }
  .p-md-2 {
    padding: 0.5rem !important;
  }
  .pt-md-2,
  .py-md-2 {
    padding-top: 0.5rem !important;
  }
  .pr-md-2,
  .px-md-2 {
    padding-right: 0.5rem !important;
  }
  .pb-md-2,
  .py-md-2 {
    padding-bottom: 0.5rem !important;
  }
  .pl-md-2,
  .px-md-2 {
    padding-left: 0.5rem !important;
  }
  .p-md-3 {
    padding: 1rem !important;
  }
  .pt-md-3,
  .py-md-3 {
    padding-top: 1rem !important;
  }
  .pr-md-3,
  .px-md-3 {
    padding-right: 1rem !important;
  }
  .pb-md-3,
  .py-md-3 {
    padding-bottom: 1rem !important;
  }
  .pl-md-3,
  .px-md-3 {
    padding-left: 1rem !important;
  }
  .p-md-4 {
    padding: 1.5rem !important;
  }
  .pt-md-4,
  .py-md-4 {
    padding-top: 1.5rem !important;
  }
  .pr-md-4,
  .px-md-4 {
    padding-right: 1.5rem !important;
  }
  .pb-md-4,
  .py-md-4 {
    padding-bottom: 1.5rem !important;
  }
  .pl-md-4,
  .px-md-4 {
    padding-left: 1.5rem !important;
  }
  .p-md-5 {
    padding: 3rem !important;
  }
  .pt-md-5,
  .py-md-5 {
    padding-top: 3rem !important;
  }
  .pr-md-5,
  .px-md-5 {
    padding-right: 3rem !important;
  }
  .pb-md-5,
  .py-md-5 {
    padding-bottom: 3rem !important;
  }
  .pl-md-5,
  .px-md-5 {
    padding-left: 3rem !important;
  }
  .m-md-auto {
    margin: auto !important;
  }
  .mt-md-auto,
  .my-md-auto {
    margin-top: auto !important;
  }
  .mr-md-auto,
  .mx-md-auto {
    margin-right: auto !important;
  }
  .mb-md-auto,
  .my-md-auto {
    margin-bottom: auto !important;
  }
  .ml-md-auto,
  .mx-md-auto {
    margin-left: auto !important;
  }
}


.col-sm-4 {
	width: 30%;
    float: left;
}
.col-sm-8 {
	width: 68%;
    float: left;
}
.col-md-5 {
	width: 36%;
    float: left;
}
.col-md-3 {
	flex: 0 0 25%;
	width: 22%;
    float: left;
}

.pb-3, .py-3 {
	padding-bottom: 1rem !important;
}
.pt-3, .py-3 {
	padding-top: 1rem !important;
}
.pb-4, .py-4 {
	padding-bottom: 1.5rem !important;
}
.pt-4, .py-4 {
	padding-top: 1.5rem !important;
}
/* .billbysec, .invcdtls {
  margin-top:40px;
} */

.table {
	width: 100%;
	max-width: 100%;
	margin-bottom: 1rem;
	background-color: transparent;
}
.table-bordered {
	border: 1px solid #dee2e6;
}
table {
	border-collapse: collapse;
}
*, ::after, ::before {
	box-sizing: border-box;
}
.table-bordered thead td, .table-bordered thead th {
	border-bottom-width: 2px;
}
.table thead th {
	vertical-align: bottom;
	border-bottom: 2px solid #dee2e6;
}
.table-bordered td, .table-bordered th {
	border: 1px solid #dee2e6;
}
.table td, .table th {
	padding: .75rem;
	vertical-align: top;
	border-top: 1px solid #dee2e6;
}
th {
	text-align: inherit;
}


.customtable thead th {
    text-transform: uppercase;
    background-color: #e5f9ff;
    font-weight: 600;
 }
 
 .text-center {
	text-align: center !important;
}
.p-3 {
	padding: 1rem !important;
}
.mt-4, .my-4 {
	margin-top: 1.5rem !important;
}








    </style>

  </head>
  <body>
  <?php
    // function for converting the amount in words
    function AmountInWords(float $amount)
    {
       $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
       // Check if there is any number after decimal
       $amt_hundred = null;
       $count_length = strlen($num);
       $x = 0;
       $string = array();
       $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
         3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
         7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
         10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
         13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
         16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
         19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
         40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
         70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
        $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $x < $count_length ) {
          $get_divider = ($x == 2) ? 10 : 100;
          $amount = floor($num % $get_divider);
          $num = floor($num / $get_divider);
          $x += $get_divider == 10 ? 1 : 2;
          if ($amount) {
           $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
           $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
           $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
           '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
           '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
            }
       else $string[] = null;
       }
       $implode_to_Rupees = implode('', array_reverse($string));
       $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
       " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
       return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
    }
  ?>
    <header class="bg-white">
       <div class="container">
          <div class="d-flex align-items-center justify-content-between">
            <div class="logo" style="width: 60%; float: left; "><img width="250px" height="auto" src="<?=FRONT_URL?>uploads/default/logo.jpg">
            </div>
            <div  style="width: 40%; float: left; text-align:right; padding-top:20px">
            <h1>Tax Invoice</h1>
            </div>
            <div class="floatClear"></div>
          </div>
       </div> 
    </header>

    <section class="py-4 invcttldtls">
        <div class="container">
            <div class="row">
                <div class="col-sm-4 billbysec">
                    <h4 class="mb-3">Billed By -</h4>
                    <div>SUROBHI AGRO INDUSTRIES PVT. LTD. <br>
                        Kasa Industrial Park <br>
                        Duilya, Jalardhar, Andul Road <br>
                        Howrah, West Bengal - 711302 <br>
                        Contact - 9073695510 <br>
                        </div>

                        <div class="mt-4"><h5>GSTIN : <span class="ml-2">19ABBCS3198L1ZQ</span></h5></div>
                </div>
                <div class="col-sm-4 billbysec">
                    <h4> Billed To -</h4>
                    <div><?=strtoupper($order_details['address_details']['name'])?></div>
                    <div><?=strtoupper($order_details['address_details']['address_1'])?>, <br>
                      <?php if(!empty($order_details['address_details']['address_2'])){ ?><?=strtoupper($order_details['address_details']['address_2'])?>,<?php } ?> Near <?=strtoupper($order_details['address_details']['landmark'])?>, <br> 
                      <?=strtoupper($order_details['address_details']['city_name'])?>, <?=strtoupper($order_details['address_details']['state_name'])?> - <?=strtoupper($order_details['address_details']['zip_code'])?>
                    </div>
                    <div>CONTACT - <?=$order_details['address_details']['phone']?></div>
                </div>
                <div class="col-sm-4">
                    <div class="invcdtls">
                        <h5>
                          Invoice No. 
                          <?php if (!empty($order_details["order_no"])) {
                            echo str_replace("FM","",$order_details["order_no"]);
                          }?>
                        </h5>
                        <h5>Invoice Date: <?=date("d/m/Y")?></h5>
                        <h5>
                          Order Date:
                          <?php if (!empty($order_details["created_date"])) {
                            echo date("d/m/Y", strtotime($order_details["created_date"]));
                          }?>
                        </h5>
                    </div>
                    
                    <div class="mt-5 billbysec pt-lg-4">
                        <h5>Country of Supply: <span class="ml-2">India</span> </h5>
                        <h5>
                          Place of Supply: 
                          <span class="ml-2">
                            <?=strtoupper($order_details['address_details']['state_name'])?>
                          </span>
                        </h5>
                    </div>
                  
                </div>
    <div class="floatClear"></div>
            </div>
        </div>
    </section>

    <section class="py-4 customtable">
        <div class="container">
            <table class="table table-bordered">
                <thead>
                  <tr>
                    <th  bgcolor="e5f9ff"  class="descwith" scope="col">Item Description</th>
                    <th  bgcolor="e5f9ff"  scope="col">Quantity</th>
                    <th  bgcolor="e5f9ff"  scope="col">Rate</th>
                    <th  bgcolor="e5f9ff"  scope="col">Discount</th>
                    <th  bgcolor="e5f9ff"  scope="col">GST</th>
                    <th  bgcolor="e5f9ff"  class="totalamnt" scope="col">Total Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($order_details["product_details"] as $product_row) { ?>
                    <tr>
                      <td class="desccontnt">
                        <?=$product_row['variation_details']['product_details']['name']?> - <?=$product_row['variation_details']['variation_details']['title']?>
                      </td>
                      <td><?=$product_row['quantity']?></td>
                      <td><?=$product_row["unit_price"];?></td>
                      <td>
                        <?php if (!empty($product_row["variation_details"]["variation_details"]["price_details"]["discount_percent"])) {
                            $discount = $product_row["variation_details"]["variation_details"]["price_details"]["discount_percent"];
                            $discount_amount = $product_row["variation_details"]["variation_details"]["price_details"]["discount_amount"];
                          }
                          echo intval($discount)."%";
                        ?>
                      </td>
                      <td>0.00</td>
                      <td class="Amntcnt">
                        <?=number_format(($product_row["unit_price"] - $discount_amount) * $product_row["quantity"], 2)?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
        </div>
    </section>



    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-sm-8">
                    <h5 class="mb-3">Terms & Conditions :</h5>
                    <div class="mb-2">1. </div>
                    <div class="mb-2">2. </div>
                </div>
                <div class="col-sm-4 customtbl2">
                    <table class="table table-bordered">
                        <tbody>
                          <tr>
                            <td style="width:100px;">SUB TOTAL </td>
                            <td>Rs. <span><?=$order_details["total_price"]?></span> </td>
                          
                          </tr>
                          <tr>
                            <td class="tblrswdth">TOTAL GST (+)</td>
                            <td class="tblrswdth">Rs. <span>0.00</span> </td>
                          </tr>
                          <tr>
                            <td class="tblrswdth">SHIPPING CHARGE (+)</td>
                            <td class="tblrswdth">Rs. <span><?=$order_details['delivery_charge']?>.00</span> </td>
                          </tr>
                          <tr>
                            <td class="tblrswdth">PROMO DISCOUNT (-)</td>
                            <td class="tblrswdth">Rs. <span><?=$order_details['discount']?></span> </td>
                          </tr>
                          <tr>
                            <td bgcolor="16a9e0" style="color:#fff; background-color:#16a9e0; width:100px;">TOTAL AMOUNT  </td>
                            <td bgcolor="16a9e0" style="color:#fff; background-color:#16a9e0;" class="tblrswdth">Rs. <span><?=$order_details['order_total']?></span> </td>
                          </tr>
                          <tr>
                            <td class="inclttlwrd" colspan="2">Invoice Total in Words:
                                <div class="mt-2"><?=AmountInWords(floatval($order_details['order_total']))?></div>
                            </td>
                          </tr>
                        
                        </tbody>
                      </table>
                </div>

                <div class="floatClear"></div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
           <div class="row">
               <div class="col-md-5">
                   <h5 class="mb-3">Bank and Payment Details </h5>
                   <ul class="paymntdtls">
                    <li><span class="pmntwdth">Account Name</span><span class="mr-2">:</span> SUROBHI AGRO INDUSTRIES PVT LTD
                    </li>
                    <li><span class="pmntwdth">Account Number</span><span class="mr-2">:</span> 919020044043217</li>
                    <li><span class="pmntwdth">IFSC</span><span class="mr-2">:</span> UTIB0000721</li>
                    <li><span class="pmntwdth">Account Type</span><span class="mr-2">:</span> CURRENT</li>
                    <li><span class="pmntwdth">Bank Name</span><span class="mr-2">:</span> AXIS BANK                    </li>
                   </ul>
               </div>
               <div class="col-md-3">
                   <img src="<?=ASSETS_URL?>pdf-assets/scan.png" alt="" width="120px">
               </div>
               <div class="col-md-4">
                <div class="mb-3">
                  PAYMENT TYPE :  
                  <?php if (!empty($order_details["payment_method"]) && $order_details["payment_method"] == "online") {
                    echo "Online Payment";
                  } elseif (!empty($order_details["payment_method"]) && $order_details["payment_method"] != "online") {
                    echo "Pay On Delivery (POD)";
                  } else {
                    echo "Unknown";
                  }?>
                </div>
                <div class="mb-3">PAYMENT STATUS : Success</div>
               <div class="paymtntimg"> <img src="<?=ASSETS_URL?>pdf-assets/payment.png" alt="" width="230px"></div>
               </div>

               <div class="floatClear"></div>
           </div>

           <div class="border rounded text-center p-3 mt-5">
            For any enquiries, email us on <a href="mailto:sales@farmologyindia.com">sales@farmologyindia.com</a>  or Call us on <a href="tel:+91 9073695511">+91 9073695511</a> 
           </div>

           <div class="mt-4 text-center">
            <h5>Thank You for buying at Farmology. We hope to see you back very soon !</h5>
           </div>
        </div>
    </section>




    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>