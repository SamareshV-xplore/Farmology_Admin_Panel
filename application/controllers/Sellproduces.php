<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sellproduces extends CI_Controller {
	function __construct()
    {
        parent::__construct();
    }

    //Answers List
    public function index()
    {
        // users list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Sellproduce Management";
        $left_data['navigation'] = "sellproduces";
        $left_data['sub_navigation'] = "sellproduces-list";

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

        if(isset($_REQUEST['filter']))
        {
            $filter_data = array("status" => $_REQUEST['status']);
        }
        else
        {
            $filter_data = array("status" => 'all');
        }

        $page_data['filter_data'] = $filter_data;

        // get users list
        $sellproduces_list = $this->common_model->sellproduces_list($filter_data);
        $page_data['sellproduces_list'] = $sellproduces_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('sellproduces/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function details($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Sell Produce Details";
        $left_data['navigation'] = "sellproduces";
        $left_data['sub_navigation'] = "sellproduces-details";

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

        $sellproduce_data = $this->common_model->get_produce_details_by_id($id);

        if($sellproduce_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Sellproduce details not found. Maybe Sellproduce already deleted.');
            redirect(base_url('sellproduces-list'));
        }
        else
        {
            $page_data["sellproduce_details"] = $sellproduce_data["details"];
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('sellproduces/detail_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function delete ($id = 0)
    {
        $condition = ["id" => "$id"];
        $this->db->where($condition);
        $this->db->delete("FM_sell_produce");

        header("Location: ".base_url("sellproduces-list"));
        die();
    }
}    