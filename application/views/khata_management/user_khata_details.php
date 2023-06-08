<style>

    .khata-summary-table {
        width: 100%;
        margin-bottom: 20px;
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
                                    <th>Name</th>
                                    <th>Owner</th>
                                    <th>Crop</th>
                                    <th>Sowing Date</th>
                                    <th>Season End Date</th>
                                    <th>Farmonaut Farm ID</th>
                                    <th>Farming Field Area (SQM)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                </tr>
                                <tr>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                </tr>
                                <tr>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                </tr>
                                <tr>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                    <td>###########</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script>

    $(document).ready(function(){
        $("#crop_sales_listing_table").DataTable({
            "language": {
                "emptyTable": "No Crop Sales Available"
            },
            "order": [[0, "DESC"]]
        });
    });


</script>