<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_profile extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('admin_profile_model');        
    }

	public function index()
	{
		$header_data = array();
		$page_data = array();
		$left_data = array();
		$footer_data = array();

		$header_data['title'] = "My Profile";
		$left_data['navigation'] = "profile"; 
		$left_data['sub_navigation'] = "none"; 

		// check login or not 
		if($this->common_model->user_login_check())
		{
			// allow and get admin details
			$admin_details = $this->common_model->get_admin_user_details();
			$header_data['admin_details'] = $admin_details;
			$page_data['admin_details'] = $admin_details;
			$left_data['admin_details'] = $admin_details;
		}
		else
		{
			redirect(base_url(''));
		}

		$this->load->view('includes/header_view', $header_data);
		$this->load->view('includes/left_view', $left_data);
		$this->load->view('admin_profile_view', $page_data);
		$this->load->view('includes/footer_view', $footer_data);
	}

	function info_update_submit()
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}
		$form_data = array();
		$form_data['name'] = $this->input->post('name');
		$form_data['username'] = $this->input->post('username');
		$form_data['email'] = $this->input->post('email');
		$form_data['phone'] = $this->input->post('phone');

		$update_data = $this->admin_profile_model->update_profile_data($form_data);
		if($update_data['status'] == "N")
		{
			redirect(base_url('profile?error-message='.$update_data["message"]));
		}

		if($update_data['status'] == "Y")
		{
			redirect(base_url('profile?success-message='.$update_data["message"]));			
		}		
		
	}

	function password_update_submit()
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}

		$password = $this->input->post('password');
		$update_data = $this->admin_profile_model->update_password_data($password);

		if($update_data['status'] == "N")
		{
			redirect(base_url('profile?error-message='.$update_data["message"]));
		}

		if($update_data['status'] == "Y")
		{
			redirect(base_url('profile?success-message='.$update_data["message"]));			
		}

	}
}
