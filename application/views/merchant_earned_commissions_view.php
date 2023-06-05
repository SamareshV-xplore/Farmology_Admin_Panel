<div class="content-wrapper">
    <section class="content-header">
        <h1>Merchant Earned Commissions</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="merchant_earned_commissions_listing_table" class="table table-bordered table-striped">
                                <thead class="thead-light">
                                        <tr>
                                            <th>No. </th>
                                            <th>Merchant Name</th>
                                            <th>Merchant Earned Commissions (₹)</th>
                                        </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($merchant_earned_commissions_list)) {
                                $total_rows = count($merchant_earned_commissions_list);
                                foreach ($merchant_earned_commissions_list as $i => $details) { ?>
                                    <tr>
                                        <td><?=$total_rows-$i?></td>
                                        <td><?=$details->name?></td>
                                        <td><?=(!empty($details->earned_commissions)) ? "₹".$details->earned_commissions : "₹0.00"?></td>
                                    </tr>
                                <?php }}?>
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

    $("#merchant_earned_commissions_listing_table").DataTable({
        "language": {
            "emptyTable": "No merchants earned any commissions yet."
        },
        "order": [[0,"desc"]]
    });

});

</script>