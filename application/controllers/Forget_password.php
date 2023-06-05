<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forget_password extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('forget_password_model');
        
    }
	
	public function index()
	{
		$page_data = array();
		// check login or not 
		if($this->common_model->user_login_check())
		{
			redirect(base_url('dashboard'));
		}
		else
		{
			// allow to access this page
		}		 

		$this->load->view('forget_password_view', $page_data);
	}

    public function forget_password_submit()
    {
        $username = $this->input->post('username');
        $check_data = $this->forget_password_model->check_username_for_forget_password($username);
        echo json_encode($check_data);
    }

    public function reset_password($unique_id)
    {
    	$page_data = array();
    	// check login or not 
		if($this->common_model->user_login_check())
		{
			redirect(base_url('dashboard'));
		}
		else
		{
			// allow to access this page
		}
		// check unique_id
		$unique_id_check_data = $this->forget_password_model->get_data_from_unique_id($unique_id);
		if($unique_id_check_data["status"] == "Y")
		{
			$page_data['unique_id'] = $unique_id;
			$this->load->view('reset_password_view', $page_data);
		}
		else
		{
			$page_data['heading'] = "404";
          	$page_data['message'] = $unique_id_check_data['message'];

			$this->load->view('errors/html/error_general', $page_data);

		}

    }

    public function reset_password_submit()
    {
    	$unique_id = $this->input->post('unique_id');
    	$password = $this->input->post('password');

    	$unique_id_check_data = $this->forget_password_model->get_data_from_unique_id($unique_id);
		if($unique_id_check_data["status"] == "Y")
		{
			$user_id = $unique_id_check_data["user_id"];
			$reset_password_data = $this->forget_password_model->change_password_for_reset($user_id, $password, $unique_id);
		}
		else
		{
			$reset_password_data = array("status" => "N", "message" => "Currently link has been expired. Please try again.");
		}

    	echo json_encode($reset_password_data);
    }
	
}
