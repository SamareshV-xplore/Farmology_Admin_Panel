<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');        
    }

	public function index()
	{
		$header_data = array();
		$page_data = array();
		$left_data = array();
		$footer_data = array();

		$header_data['title'] = "Welcome to dashboard";
		$left_data['navigation'] = "dashboard"; 
		$left_data['sub_navigation'] = "none"; 

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

		$page_data['product_count'] = $this->dashboard_model->get_product_count();

		$page_data['order_count'] = $this->dashboard_model->get_order_count();

		$this->load->view('includes/header_view', $header_data);
		$this->load->view('includes/left_view', $left_data);
		$this->load->view('dashboard_view', $page_data);
		$this->load->view('includes/footer_view', $footer_data);
	}

	function toggle_action_update()
	{
		$reurn_str = "";
		if($this->input->post('toggle'))
		{
			$toggle_action = $this->input->cookie('toggle_action', TRUE);
			if($toggle_action == '1')
			{
				delete_cookie("toggle_action");
				$reurn_str = "removed";
			}
			else
			{
				$name   = 'toggle_action';
	            $value  = '1';
	            $expire = time()+2592000;
	            $path  = '/';
	            $secure = TRUE;
	            setcookie($name,$value,$expire,$path); 
	            $reurn_str = "added";
			}
		}

		echo $reurn_str;
	}

	
}
