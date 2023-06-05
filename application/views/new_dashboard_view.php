<?php if (!empty($new_dashboard_model)) {

    $total_farmers = $new_dashboard_model->get_total_farmers_count();
    $total_farmers_for_today = $new_dashboard_model->get_total_farmers_count_for_today();

    $total_orders = $new_dashboard_model->get_total_orders_count();
    $total_orders_for_today = $new_dashboard_model->get_total_orders_count_for_today();

    $total_orders_value = $new_dashboard_model->get_total_orders_value();
    $total_orders_value_for_today = $new_dashboard_model->get_total_orders_value_for_today();
    
    $total_soil_test_request = $new_dashboard_model->get_total_soil_test_request_count();
    $total_soil_test_request_for_today = $new_dashboard_model->get_total_soil_test_request_count_for_today();

    $soil_health_request_list = $new_dashboard_model->get_soil_health_request_list();
    $new_customer_list = $new_dashboard_model->get_new_customer_list();
    $new_marchent_list = $new_dashboard_model->get_new_marchent_list();
    $new_question_list = $new_dashboard_model->get_new_question_list();
    $crop_health_request_list = $new_dashboard_model->get_crop_health_request_list();

}?>

<link rel="stylesheet" type="text/css" href="<?=base_url("assets/css/new_dashboard.css")?>">
<style>
    a, a:hover, a:focus, a:active {
        text-decoration: none;
        color: inherit;
    }
    a {
        color: inherit;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <a href="https://admin.surobhiagro.in/users-list">
                <div class="col-md-3">
                    <div class="box_main_1">
                        <div class="total_farmer_txt">Total Farmer</div>
                        <div class="total_farmer_txt_amt"><?=$total_farmers?></div>
                        <div class="total_farmer_txt">Today Total - <?=$total_farmers_for_today?></div>
                    </div>
                </div>
            </a>
            
            <a href="https://admin.surobhiagro.in/order">
                <div class="col-md-3">
                    <div class="box_main_2">
                        <div class="total_farmer_txt">Total Orders</div>
                        <div class="total_farmer_txt_amt"><?=$total_orders?></div>
                        <div class="total_farmer_txt">Today Total - <?=$total_orders_for_today?></div>
                    </div>
                </div>
            </a>

            <a href="https://admin.surobhiagro.in/order">
                <div class="col-md-3">
                    <div class="box_main_3">
                        <div class="total_farmer_txt">Total Orders Value</div>
                        <div class="total_farmer_txt_amt"><?=$total_orders_value?></div>
                        <div class="total_farmer_txt">Today Total - <?=$total_orders_value_for_today?></div>
                    </div>
                </div>
            </a>

            <a href="https://admin.surobhiagro.in/soil-health-test/">
                <div class="col-md-3">
                    <div class="box_main_4">
                        <div class="total_farmer_txt">Total Soil Test Request</div>
                        <div class="total_farmer_txt_amt"><?=$total_soil_test_request?></div>
                        <div class="total_farmer_txt">Today Total - <?=$total_soil_test_request_for_today?></div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="row">
            <div class="col-md-9">
                <canvas id="myChart" class="barchatsec" width="809" height="404" style="display: block; width: 809px; height: 404px;"></canvas>
            </div>

            <a href="https://admin.surobhiagro.in/soil-health-test/">
                <div class="col-md-3">
                    <div class="custom_card">
                        <div class="card_header">
                            Soil Health Request
                        </div>
                        <div class="card_body">
                            <table border="0" width="100%">
                                <thead>
                                    <tr class="tbl_tr">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Crop</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($soil_health_request_list)) {
                                    foreach ($soil_health_request_list as $i => $request) { ?>
                                        <tr class="tbl_tr">
                                            <td><?=$i+1?></td>
                                            <td class="pd_class"><?=ucwords(strtolower($request->name))?></td>
                                            <td class="pd_class"><?=ucwords(strtolower($request->crop))?></td>
                                            <td class="pd_class"><?=date("d/m/Y", strtotime($request->date))?></td>
                                        </tr>
                                    <?php }} else { ?>
                                        <tr class="text-center">
                                            <td colspan="4">No soil health request available</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="row">
            <a href="https://admin.surobhiagro.in/users-list">
                <div class="col-md-3">
                    <div class="custom_card">
                        <div class="card_header">
                            New Customer
                        </div>
                        <div class="card_body">
                            <table border="0" width="100%">
                                <thead>
                                    <tr class="tbl_tr">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Zipcode</th>
                                        <th scope="col">Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($new_customer_list)) {
                                    foreach ($new_customer_list as $i => $customer) { ?>
                                        <tr class="tbl_tr">
                                            <td><?=$i+1?></td>
                                            <td class="pd_class"><?=ucwords(strtolower($customer->name))?></td>
                                            <td class="pd_class"><?=$customer->zip_code?></td>
                                            <td class="pd_class"><?=(!empty($customer->phone)) ? $customer->phone : "<span class='text-muted'>N/A</span>"?></td>
                                        </tr>
                                    <?php }} else { ?>
                                        <tr class="text-center">
                                            <td colspan="4">No new customer available</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </a>
            
            <a href="https://admin.surobhiagro.in/users-list">
                <div class="col-md-3">
                    <div class="custom_card">
                        <div class="card_header">
                            New Marchent
                        </div>
                        <div class="card_body">
                            <table border="0" width="100%">
                                <thead>
                                    <tr class="tbl_tr">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Zipcode</th>
                                        <th scope="col">Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($new_marchent_list)) {
                                    foreach ($new_marchent_list as $i => $marchent) { ?>
                                        <tr class="tbl_tr">
                                            <td><?=$i+1?></td>
                                            <td class="pd_class"><?=ucwords(strtolower($marchent->name))?></td>
                                            <td class="pd_class"><?=$marchent->zip_code?></td>
                                            <td class="pd_class"><?=(!empty($marchent->phone)) ? $marchent->phone : "<span class='text-muted'>N/A</span>"?></td>
                                        </tr>
                                    <?php }} else { ?>
                                        <tr class="text-center">
                                            <td colspan="4">No new marchent available</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </a>

            <a href="https://admin.surobhiagro.in/questions-list">
                <div class="col-md-3">
                    <div class="custom_card">
                        <div class="card_header">
                            New Question
                        </div>
                        <div class="card_body">
                            <table border="0" width="100%">
                                <thead>
                                    <tr class="tbl_tr">
                                        <th scope="col">#</th>
                                        <th scope="col">Qestions</th>
                                        <th scope="col">Answered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($new_question_list)) {
                                    foreach ($new_question_list as $i => $question) { ?>
                                        <tr class="tbl_tr">
                                            <td><?=$i+1?></td>
                                            <td class="pd_class"><?=ucfirst(strtolower($question["question"]))?></td>
                                            <td class="pd_class"><?=(!empty($question["answered"])) ? "Yes" : "No"?></td>
                                        </tr>
                                    <?php }} else { ?>
                                        <tr class="text-center">
                                            <td colspan="4">No new question available</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </a>

            <a href="https://admin.surobhiagro.in/expert">
                <div class="col-md-3">
                    <div class="custom_card">
                        <div class="card_header">
                            Crop Health Request
                        </div>
                        <div class="card_body">
                            <table border="0" width="100%">
                                <thead>
                                    <tr class="tbl_tr">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Crop</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($crop_health_request_list)) {
                                    foreach ($crop_health_request_list as $i => $request) { ?>
                                        <tr class="tbl_tr">
                                            <td><?=$i+1?></td>
                                            <td class="pd_class"><?=ucwords(strtolower($request->name))?></td>
                                            <td class="pd_class"><?=ucwords(strtolower($request->crop))?></td>
                                            <td class="pd_class"><?=date("d/m/Y", strtotime($request->date))?></td>
                                        </tr>
                                    <?php }} else { ?>
                                        <tr class="text-center">
                                            <td colspan="4">No crop health request available</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="<?=base_url("assets/js/new_dashboard.js")?>"></script>