<div class="content-wrapper">
<section class="content-header">
  <h1>List of Users Khata</h1>
</section>
<section class="content">
	<div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">	
                        <table id="users_khata_listing_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Total Incomes</th>
                                <th>Total Expenses</th>
                                <th>Total Profit</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($list_of_users_khata)) {
                            foreach ($list_of_users_khata as $i => $khata_details) {?>
                                <tr>
                                    <td><?=$i+1?></td>
                                    <td><?=$khata_details->name?></td>
                                    <td>₹ <?=number_format($khata_details->total_incomes, 0)?></td>
                                    <td>₹ <?=number_format($khata_details->total_expenses, 0)?></td>
                                    <td>₹ <?=number_format($khata_details->total_profits, 0)?></td>
                                    <td>
                                        <a href="<?=base_url('khata-management/user-khata-details/'.$khata_details->id)?>" target="_blank">Show Khata Details</a>
                                    </td>
                                </tr>
                            <?php }} ?>
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

		$("#users_khata_listing_table").DataTable({
            "language": {
                "emptyTable": "No users khata available."
            },
			"order": [[0, "DESC"]]
		})

	});

</script>