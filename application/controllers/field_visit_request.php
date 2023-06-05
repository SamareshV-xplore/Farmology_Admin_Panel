<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Field_visit_request extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		// $this->load->helper('url');
		$this->load->database();
		$this->load->library('session');
	}
	public function index()
	{
		$header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Field Visit Request";
        $left_data['navigation'] = "field_req";
        // $left_data['sub_navigation'] = "communitie-list";

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

        $filter_status = "all";
        $filter_category = "all";

        if(isset($_REQUEST['status']))
        {
            if($_REQUEST['status'] == 'V' || $_REQUEST['status'] == 'P' || $_REQUEST['status'] == 'C' || $_REQUEST['status'] == 'D')
            {
                $filter_status = $_REQUEST['status'];
            }
            
        }       
        
        $filter_data = array("status" => $filter_status);
        $page_data['filter_data'] = $filter_data;

        $this->load->model('Field_request_model');

        // get request list
        echo $filter_data;
        $req_list = $this->Field_request_model->get_field_visit_request_list($filter_data);
        $page_data['request_list'] = $req_list;


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('Field_visit_request_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
	}

	public function update_request_status()
	{
		$this->db->set(['status' => $this->input->post('status')]);
		$this->db->where('id', $this->input->post('id'));
		$this->db->update('FM_field_visit_request');
		echo json_encode(array('status' => 'Y'));
	}
}