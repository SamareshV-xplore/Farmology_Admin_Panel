<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Preview Generated Report</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>

	<!-- Popper JS -->
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container-fluid" style="width:100vw; height:100vh;">
	<?php 

		if(isset($report_pdf))
		{
			$report_pdf_url = FRONT_URL.$report_pdf;
			echo "<iframe width='100%' height='90%' src='$report_pdf_url'></iframe>";
		}

	?>
	<form id="preview_report_form">
		<input type="hidden" name="report_id" value="<?=$report_id?>"/>
		<input type="hidden" name="report_pdf" value="<?=$report_pdf?>"/>
        <input type="hidden" name="request_id" value="<?=$request_id?>"/>
		<input type="hidden" id="status" name="status"/>
		<button type="button" class="btn btn-success" onclick="form_submit('TRUE')">Save</button>
		<button type="button" class="btn btn-danger" onclick="form_submit('FALSE')">Close</button>
	</form>
</div>
<script>
	function form_submit(status)
	{
		var form = document.getElementById("preview_report_form");
		form.method = "POST";
		form.action = "<?=base_url('process_soil_health_report')?>";

		var status_input = document.getElementById("status");
		status_input.value = status;

		form.submit();
	}
</script>
</body>
</html>