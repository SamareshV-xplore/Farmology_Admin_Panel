<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_comment extends CI_Controller {

	function __construct()
    {
        parent::__construct();   
        $this->load->model('product_comment_model');    
    }

	//Comment List
	public function index()
	{
        // content list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Product Comment List";
        $left_data['navigation'] = "comment"; 
        $left_data['sub_navigation'] = "content-list"; 

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

        if(isset($_REQUEST['status']))
        {
            $filter_data = array("status" => $_REQUEST['status']);
        }
        else
        {
            $filter_data = array("status" => 'all');
        }

        $page_data['filter_data'] = $filter_data;

        $review_list = $this->product_comment_model->get_comment_list($filter_data);
        /*echo "<pre>";
        print_r($review_list);
        echo "</pre>"; exit;*/
        $page_data['review_list'] = $review_list;

                
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('product_comment/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    
    function update_comment_status()
    {
        // check login or not 
        if($this->common_model->user_login_check())
        {
            // allow
        }
        else
        {
            redirect(base_url(''));
        }

        if($this->input->post('id') == null || $this->input->post('status') == null)
        {
            echo "some thing is wrong";
        }
        else
        {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $form_data = array("id" => $id, "status" => $status);
            $this->db->where("id", $id);
            $this->product_comment_model->update_review_status($form_data);
            echo "success";
        }

    }

    
   
	
}
