<style>

    .khata-summary-table {
        width: 100%;
        margin-bottom: 25px;
    }
    
    .khata-summary-table td {
        padding-right: 5px;
    }

    .khata-summary-table h4 {
        margin: 0px 0px 5px 0px;
        font-weight:600;
    }

    .khata-summary-table h5 {
        margin: 0px;
    }

    .income-summary-card, .expenses-summary-card {
        height: 65px;
        padding: 10px;
        color: #fff;
        border-radius: 5px;
    }

    .income-summary-card {
        border: 2px solid #28a745;
        background: rgba(40, 167, 69, 0.8);
    }

    .expenses-summary-card {
        border: 2px solid #dc3545;
        background: rgba(220, 53, 69, 0.8);
    }

    .table-header {
        margin: 0px;
        font-size: 20px;
        font-weight: 600;
    }

    .crop-details-container {
        display: flex;
        align-items: center;
    }

    .crop-image {
        width: 20px;
        height: 20px;
        margin: 0px 5px 0px 0px;
    }

    .crop-name {
        font-weight: 500;
    }

    .not-available {
        display: grid;
        place-items: center;
        font-weight: 600;
        color: #b3b3b3;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

</style>
<div class="content-wrapper">
<section class="content-header">
  <h1><?=(!empty($user_khata_summary->user_name)) ? $user_khata_summary->user_name."'s" : "User"?> Khata Details</h1>
</section>
<section class="content">

    <?php if (!empty($user_khata_summary->total_crop_sales)
              || !empty($user_khata_summary->total_other_incomes)
              || !empty($user_khata_summary->total_product_expenses)
              || !empty($user_khata_summary->total_farming_expenses)
              || !empty($user_khata_summary->total_other_expenses)) { ?>
        <table class="khata-summary-table">
            <tbody>
                <tr>
                    <?php if (!empty($user_khata_summary->total_crop_sales)) { ?>
                        <td style="width:20%;">
                            <div class="income-summary-card">
                                <h4>Total Crop Sales</h4>
                                <h5>₹ <?=number_format($user_khata_summary->total_crop_sales, 0)?></h5>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if (!empty($user_khata_summary->total_other_incomes)) { ?>
                        <td style="width:20%;">
                            <div class="income-summary-card">
                                <h4>Total Other Incomes</h4>
                                <h5>₹ <?=number_format($user_khata_summary->total_other_incomes, 0)?></h5>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if (!empty($user_khata_summary->total_product_expenses)) { ?>
                        <td style="width:20%;">
                            <div class="expenses-summary-card">
                                <h4>Total Product Expenses</h4>
                                <h5>₹ <?=number_format($user_khata_summary->total_product_expenses, 0)?></h5>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if (!empty($user_khata_summary->total_farming_expenses)) { ?>
                        <td style="width:20%;">
                            <div class="expenses-summary-card">
                                <h4>Total Farming Expenses</h4>
                                <h5>₹ <?=number_format($user_khata_summary->total_farming_expenses, 0)?></h5>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if (!empty($user_khata_summary->total_other_expenses)) { ?>
                        <td style="width:20%;">
                            <div class="expenses-summary-card">
                                <h4>Total Other Expenses</h4>
                                <h5>₹ <?=number_format($user_khata_summary->total_other_expenses, 0)?></h5>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
    <?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <h3 class="table-header box-header with-border">
                    List of Crop Sales
                </h3>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="crop_sales_listing_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Crop</th>
                                    <th>Total Produce</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success">
                <h3 class="table-header box-header with-border">
                    List of Other Incomes
                </h3>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="other_incomes_listing_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Income Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-danger">
                <h3 class="table-header box-header with-border">
                    List of Product Expenses
                </h3>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="product_expenses_listing_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category Name</th>
                                    <th>Product Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-danger">
                <h3 class="table-header box-header with-border">
                    List of Farming Expenses
                </h3>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="farming_expenses_listing_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category Name</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-danger">
                <h3 class="table-header box-header with-border">
                    List of Other Expenses
                </h3>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="other_expenses_listing_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Expense Name</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script>

    var user_id = `<?=$user_id?>`;
    var crop_sales_listing_table = null;
    var other_incomes_listing_table = null;
    var product_expenses_listing_table = null;
    var farming_expenses_listing_table = null;
    var other_expenses_listing_table = null;

    $(document).ready(function(){
        crop_sales_listing_table = $("#crop_sales_listing_table").DataTable({
            "language": {
                "emptyTable": "No Crop Sales Available"
            }
        });
        get_list_of_crop_sales();

        other_incomes_listing_table = $("#other_incomes_listing_table").DataTable({
            "language": {
                "emptyTable": "No Other Incomes Available"
            }
        });
        get_list_of_other_incomes();

        product_expenses_listing_table = $("#product_expenses_listing_table").DataTable({
            "language": {
                "emptyTable": "No Product Expenses Available"
            }
        });
        get_list_of_product_expenses();

        farming_expenses_listing_table = $("#farming_expenses_listing_table").DataTable({
            "language": {
                "emptyTable": "No Farming Expenses Available"
            }
        });
        get_list_of_farming_expenses();

        other_expenses_listing_table = $("#other_expenses_listing_table").DataTable({
            "language": {
                "emptyTable": "No Other Expenses Available"
            }
        });
        get_list_of_other_expenses();

    });

    function get_list_of_crop_sales() {
        $.ajax({
            url: "<?=base_url('get-list-of-crop-sales/')?>"+user_id,
            type: "GET",
            error: function(a, b, c) {
                toast("Something went wrong! Failed to get list of crop sales.", 3000);
                console.log(a);
                console.log(b);
                console.log(c);
            },
            success: function(response) {
                if (response.success == true) {
                    render_list_of_crop_sales(response.data);
                }
                else if (response.success == false) {
                    toast(response.message, 3000);
                }
                else {
                    toast("Something went wrong! Failed to get list of crop sales.", 3000);
                    console.log(response);
                }
            }
        });
    }

    function render_list_of_crop_sales(data) {
        data.forEach((details, i) => {
            let crop = `<div class="crop-details-container">
                <img src="${details.crop_image}" alt="Crop Image ${i+1}" class="crop-image">
                <span class="crop-name">${details.crop_name}</span>
            </div>`;
            let total_produce = details.total_produce;
            let sale_value = details.sale_value;
            let date = details.date;
            let reference = (details.hasOwnProperty("reference") && details.reference != null) ? details.reference : "<span class='not-available'>N/A</span>";

            crop_sales_listing_table.row.add([i+1, crop, total_produce, sale_value, date, reference]).draw(false);
        });
    }

    function get_list_of_other_incomes() {
        $.ajax({
            url: "<?=base_url('get-list-of-other-incomes')?>/"+user_id,
            type: "GET",
            error: function(a, b, c) {
                toast("Something went wrong! Failed to get list of other incomes.", 3000);
                console.log(a);
                console.log(b);
                console.log(c);
            },
            success: function(response) {
                if (response.success == true) {
                    render_list_of_other_incomes(response.data);
                }
                else if (response.success == false) {
                    toast(response.message, 3000);
                }
                else {
                    toast("Something went wrong! Failed to get list of other incomes.", 3000);
                    console.log(response);
                }
            }
        });
    }

    function render_list_of_other_incomes(data) {
        data.forEach((details, i) => {
            let reference = (details.hasOwnProperty("reference") && details.reference != null) ? details.reference : "<span class='not-available'>N/A</span>";
            other_incomes_listing_table.row.add([i+1, details.income_type, details.amount, details.date, reference]).draw(false);
        });
    }

    function get_list_of_product_expenses() {
        $.ajax({
            url: "<?=base_url('get-list-of-product-expenses')?>/"+user_id,
            type: "GET",
            error: function(a, b, c) {
                toast("Something went wrong! Failed to get list of product expenses.", 3000);
                console.log(a);
                console.log(b);
                console.log(c);
            },
            success: function(response) {
                if (response.success == true) {
                    render_list_of_product_expenses(response.data);
                }
                else if (response.success == false) {
                    toast(response.message, 3000);
                }
                else {
                    toast("Something went wrong! Failed to get list of product expenses.", 3000);
                    console.log(response);
                }
            }
        });
    }

    function render_list_of_product_expenses(data) {
        data.forEach((details, i) => {
            let reference = (details.hasOwnProperty("reference") && details.reference != null) ? details.reference : "<span class='not-available'>N/A</span>";
            product_expenses_listing_table.row.add([i+1, details.category_name, details.product_type, details.amount, details.date, reference]).draw(false);
        });
    }
    
    function get_list_of_farming_expenses() {
        $.ajax({
            url: "<?=base_url('get-list-of-farming-expenses')?>/"+user_id,
            type: "GET",
            error: function(a, b, c) {
                toast("Something went wrong! Failed to get list of farming expenses.", 3000);
                console.log(a);
                console.log(b);
                console.log(c);
            },
            success: function(response) {
                if (response.success == true) {
                    render_list_of_farming_expenses(response.data);
                }
                else if (response.success == false) {
                    toast(response.message, 3000);
                }
                else {
                    toast("Something went wrong! Failed to get list of farming expenses.", 3000);
                    console.log(response);
                }
            }
        });
    }

    function render_list_of_farming_expenses(data) {
        data.forEach((details, i) => {
            let reference = (details.hasOwnProperty("reference") && details.reference != null) ? details.reference : "<span class='not-available'>N/A</span>";
            farming_expenses_listing_table.row.add([i+1, details.category_name, details.amount, details.date, reference]).draw(false);
        });
    }
    
    function get_list_of_other_expenses() {
        $.ajax({
            url: "<?=base_url('get-list-of-other-expenses')?>/"+user_id,
            type: "GET",
            error: function(a, b, c) {
                toast("Something went wrong! Failed to get list of other expenses.", 3000);
                console.log(a);
                console.log(b);
                console.log(c);
            },
            success: function(response) {
                if (response.success == true) {
                    render_list_of_other_expenses(response.data);
                }
                else if (response.success == false) {
                    toast(response.message, 3000);
                }
                else {
                    toast("Something went wrong! Failed to get list of other expenses.", 3000);
                    console.log(response);
                }
            }
        });
    }

    function render_list_of_other_expenses(data) {
        data.forEach((details, i) => {
            let reference = (details.hasOwnProperty("reference") && details.reference != null) ? details.reference : "<span class='not-available'>N/A</span>";
            other_expenses_listing_table.row.add([i+1, details.expense_name, details.amount, details.date, reference]).draw(false);
        });
    }

</script>