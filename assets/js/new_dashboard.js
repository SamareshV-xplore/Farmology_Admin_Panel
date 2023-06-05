$.ajax({
	url: "https://admin.surobhiagro.in/get_dashboard_order_value_list",
	type: "GET",
	error: function (a, b, c) {
		console.log(a);
		console.log(b);
		console.log(c);
	},
	success: function (data) {
		var parsed_data = JSON.parse(data);
		renderChartData(parsed_data);
	}
});

function renderChartData(data) {
	var months = [];
	var amounts = [];

	for (var i=0; i<data.length; i++) {
		months.push(data[i].month);
		amounts.push(data[i].amount);
	}

	new Chart("myChart", {
		type: "bar",
		data: {
			labels: months,
			datasets: [{
				backgroundColor: "#00A78F",
				data: amounts
			}]
		},
		options: {
			legend: {display: false},
			title: {
			display: true,
			text: "Previous 12 Month Orders Value"
			}
		}
	});
}