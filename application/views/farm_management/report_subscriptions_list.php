<div class="content-wrapper">
<section class="content-header">
  <h1>Report Subscriptions List</h1>
</section>
<section class="content">
	<div class="row">
    <div class="col-xs-12">
    <div class="box">
    <div class="box-body">
	<div class="table-responsive">	
		<table id="reports_subscription_table" class="table table-bordered table-striped">
	       <thead>
              <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Owner</th>
                  <th>Free Report</th>
                  <th>Remaining Free Report</th>
                  <th>Paid Report</th>
                  <th>Paid Report Validity</th>
              </tr>
	        </thead>
	        <tbody>
	        	<?php 
	        		if (isset($reports_subscription_list))
	        		{
	        			$counter = 1;
                foreach ($reports_subscription_list as $farm)
                {
                    echo '<tr>';
                    echo '<td>'.$counter.'</td>';
                    echo '<td>'.$farm->name.'</td>';

                    if (!empty($farm->farm_owner_data))
                    {
                    	echo '<td>'.$farm->farm_owner_data->first_name.' '.$farm->farm_owner_data->last_name.'</td>';
                    }
                    else
                    {
                    	echo '<td style="color:#CCC;">Unkonwn</td>';
                    }

                    if ($farm->free_reports_available == "Y")
                    {
                    	echo '<td><b class="text-success">Available</b></td>';
                    }
                    else
                    {
                    	echo '<td><b class="text-danger">Not Available</b></td>';
                    }

                    if ($farm->free_reports_available == "Y")
                    {
                    	$remaing_free_report = round(4 - (int)$farm->free_reports_count);
                    	echo '<td>'.$remaing_free_report.'</td>';
                    }
                    else
                    {
                    	echo '<td><b class="text-danger">Not Available</b></td>';
                    }

                    if (!empty($farm->paid_subscription))
                    {
                    	$current_date = date("Y-m-d H:i:s");
                    	if (strtotime($farm->paid_subscription->valid_to) > strtotime($current_date))
	                    {
	                    	echo '<td><b class="text-success">Subscribed</b></td>';
	                    }
	                    else
	                    {
	                    	echo '<td><b class="text-danger">Subscription Expired</b></td>';
	                    }
                    }
                    else
                    {
                    	echo '<td><b class="text-danger">Not Subscribed</b></td>';
                    }

                    if (!empty($farm->paid_subscription))
                    {
                    	echo '<td>'.$farm->paid_subscription->valid_to.'</td>';
                    }
                    else
                    {
                    	echo '<td><b class="text-danger">Not Subscribed</b></td>';
                    }

                    echo '</tr>';
                    $counter++;
                }
	        		}
	        	?>  
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

		$("#reports_subscription_table").DataTable({
			"order": [[0, "DESC"]]
		})

	});

</script>