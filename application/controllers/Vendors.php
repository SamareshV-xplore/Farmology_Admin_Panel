<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendors extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('vendors_model');
    }

    public function index()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Vendor Management";
        $left_data['navigation'] = "vendors";

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

        $vendors_list = $this->vendors_model->vendors_list();
        $page_data['vendors_list'] = $vendors_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('vendors/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
}
