<?php 
	
	function render_show_data_link ($title, $body)
	{
		$show_data_link = "<a style='cursor:pointer' onclick='show_data(this)'>Show Data</a>
							<div class='data_title' style='display:none'>".$title."</div>
							<div class='data_body' style='display:none'>".$body."</div>";

		return $show_data_link;
	}

?>

<style>

	.modal-fullscreen {
	  	padding: 0 !important;
	 }

	.modal-dialog {
		width: 100%;
		max-width: none;
		height: 100%;
		min-height: 100%;
		margin: 0;
	}
  
  	.modal-content {
  		height: 100%;
    	min-height: 100%;
    	border: 0;
    	border-radius: 0;
  	}

  	.modal-header {
  		display: flex;
  		flex-flow: row;
  		align-items: center;
  		justify-content: start;
  		margin: 0;
  		padding: 0;
  	}
  
  	.modal-body {
    	overflow-y: auto;
  	}

  	#data_modal_title {
  		margin: 10px 15px;
  		font-size: 28px;
  		font-weight: bold;
  	}

  	#data_modal_body {
  		margin: 10px;
  		font-size: 20px;
  		overflow-x: auto;
  		overflow-y: auto;
  	}

  	.close {
  		width: 35px;
  		height: 35px;
  		margin-right: 15px;
  		margin-left: auto;
  		font-size: 36px;
  		font-weight: bold;
  	}
</style>

<div class="content-wrapper">
<section class="content-header">
  <h1>Kawa API Logs</h1>
</section>
<section class="content">
	<div class="row">
    <div class="col-xs-12">
    <div class="box">
    <div class="box-body">
	<div class="table-responsive">	
		<table id="api_log_table" class="table table-bordered table-striped">
	       <thead>
              <tr>
                  <th style="width:5%">#</th>
                  <th style="width:25%">Username</th>
                  <th style="width:10%">Request</th>
                  <th style="width:10%">Response</th>
                  <th style="width:25%">Request Date & Time</th>
                  <th style="width:25%">Response Date & Time</th>
              </tr>
	        </thead>
	        <tbody>
	        	<?php 
	        	if (isset($api_logs_list))
	        	{
	        		foreach ($api_logs_list as $log)
	        		{
	        			$row = "<tr><td style='width:5%'>".$log->id."</td>";

	        			if ($log->username != "" && $log->username != null)
	        			{
	        				$row .= "<td style='width:10%'>".$log->username."</td>";
	        			}
	        			else
	        			{
	        				$row .= "<td style='width:25%'><p style='color:#938f8f;''>Unknown</p></td>";
	        			}

	        			
	        			$row .= "<td style='width:10%'>".render_show_data_link('Request Data', $log->request)."</td>";

	        			if ($log->response != "" && $log->response != null)
	        			{
	        				$row .= "<td style='width:10%'>".render_show_data_link('Response Data', $log->response)."</td>";
	        			}
	        			else
	        			{
	        				$row .= "<td style='width:10%'><p style='color:#938f8f;''>Not Found</p></td>";
	        			}
	        			
	        			$row .= "<td style='width:25%'>".$log->request_timestamp."</td>";

	        			if ($log->response_timestamp != "" && $log->response_timestamp != null)
	        			{
	        				$row .= "<td style='width:25%'>".$log->response_timestamp."</td>";
	        			}
	        			else
	        			{
	        				$row .= "<td style='width:25%'><p style='color:#938f8f;''>Not Found</p></td>";
	        			}

	        			print($row);
	        		}
	        	}
	        	else
	        	{
	        		print("<tr align='center'>No API Logs Found!</tr>");
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

<!-- Modal Fullscreen xs -->
<div class="modal modal-fullscreen" id="data_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mr-auto" id="data_modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body" id="data_modal_body"></div>
    </div>
  </div>
</div>

<script>

	$(document).ready(function () {
		$("#api_log_table").DataTable({
			"order": [[0, 'desc']]
		});
	});
	

	function show_data(show_data_link)
	{
		$("#data_modal_title").html($(show_data_link).siblings(".data_title").html());
		$("#data_modal_body").html($(show_data_link).siblings(".data_body").html());
		$("#data_modal").modal("show");
	}

</script>