<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kawa_api_log extends CI_Controller 
{	
    function __construct()
    {
        parent::__construct();
        $this->load->model('Kawa_api_log_model');
    }

    public function index()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Kawa API Logs";
        $left_data['navigation'] = "kawa_api_logs";

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

        // Getting All KAWA API Logs
        $page_data["api_logs_list"] = $this->Kawa_api_log_model->get_all_logs();

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('kawa_api_log_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

}