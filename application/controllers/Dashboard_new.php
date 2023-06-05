<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Dashboard_new extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('new_dashboard_model');        
    }

	public function index()
	{
		$header_data['title'] = "New Dashboard | Farmology";
		$left_data['navigation'] = "dashboard";
        $page_data["new_dashboard_model"] = $this->new_dashboard_model;
        $footer_data = [];

		// check login or not 
		if($this->common_model->user_login_check())
		{
			// allow and get admin details
			$admin_details = $this->common_model->get_admin_user_details();
			$header_data['admin_details'] = $admin_details;
			$left_data['admin_details'] = $admin_details;
		}
		else
		{
			redirect(base_url(''));
		}

		$this->load->view('includes/header_view', $header_data);
		$this->load->view('includes/left_view', $left_data);
		$this->load->view('new_dashboard_view', $page_data);
		$this->load->view('includes/footer_view', $footer_data);
	}

	public function get_12_month_order_value_list()
	{
		echo json_encode($this->new_dashboard_model->get_12_month_order_value_list());
	}

	public function show_past_12_month_orders_value()
	{
		$data = $this->new_dashboard_model->get_12_month_order_value_list();
		echo "<pre>";
		foreach ($data as $i => $row)
		{
			echo $row->month.": ₹".number_format($row->amount, 0)."<br/>";
		}
		echo "</pre>";
	}

}
