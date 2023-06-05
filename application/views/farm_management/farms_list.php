<div class="content-wrapper">
<section class="content-header">
  <h1>List of Farms</h1>
</section>
<section class="content">
	<div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">	
                        <table id="farms_listing_table" class="table table-bordered table-striped">
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
                            <?php if (!empty($list_of_farms)) {
                            foreach ($list_of_farms as $i => $farm_details) {?>
                                <tr>
                                    <td><?=$i+1?></td>
                                    <td><?=$farm_details->name?></td>
                                    <td><?=$farm_details->username?></td>
                                    <td><?=$farm_details->crop?></td>
                                    <td><?=date("jS F Y", strtotime($farm_details->crop_sowing_date))?></td>
                                    <td><?=date("jS F Y", strtotime($farm_details->crop_season_end_date))?></td>
                                    <td><?=$farm_details->farmonaut_farm_id?></td>
                                    <td><?=(!empty($farm_details->farming_field_area)) ? $farm_details->farming_field_area : "---"?></td>
                                    <td>
                                        <?php if ($farm_details->status == "A") { ?>
                                            <span class="text-success" style="font-weight:700;">Active</span>
                                        <?php } else { ?>
                                            <span class="text-danger" style="font-weight:700;">Inactive</span>
                                        <?php } ?>
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

		$("#farms_listing_table").DataTable({
            "language": {
                "emptyTable": "No farms available"
            },
			"order": [[0, "DESC"]]
		})

	});

</script>