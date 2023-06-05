<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_code_management extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model('promo_code_model');
    }

    //Promo Code List
    public function index()
    {
        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Promo Code List";
        $left_data['navigation'] = "promo";
        $left_data['sub_navigation'] = "promo-list";

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

        // get banner list
        $promo_code_list = $this->promo_code_model->promo_code_list($filter_data);
        $page_data['promo_code_list'] = $promo_code_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('promo_code/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //Promo Code Add Page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new Promo Code";
        $left_data['navigation'] = "promo";
        $left_data['sub_navigation'] = "promo-add";

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
        $this->load->view('promo_code/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //Promo Code Add Submit
    function add_submit()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        if($this->input->post('promo_code_form'))
        {
            $form_data = array();
            $form_data['promo_code'] = $this->input->post('promo_code');
            $form_data['title'] = $this->input->post('title');
            $form_data['description'] = $this->input->post('promo_description');
            $form_data['eligible_order_price'] = $this->input->post('eligible_order_price');
            $form_data['start_date'] = $this->input->post('start_date');
            $form_data['end_date'] = $this->input->post('end_date');
            $form_data['discount_limit'] = $this->input->post('discount_limit');
            $form_data['minimum_order_count'] = $this->input->post('minimum_order_count');
            $form_data['discount_type'] = $this->input->post('discount_type');
            $form_data['status'] = $this->input->post('status');
            $form_data['user_specific'] = $this->input->post('user_specific');
            $form_data['user_id'] = $this->input->post('user_id');
            $form_data['usage_count'] = $this->input->post('usage_count');
            $form_data['max_limit'] = $this->input->post('max_limit');

            $add_data = $this->promo_code_model->add_promo_code($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('promo-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('promo-add/'));
            }
        }
        else
        {
            redirect(base_url('promo-add/'));
        }

    }
    //Banner Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Promo Code";
        $left_data['navigation'] = "promo";
        $left_data['sub_navigation'] = "promo-edit";

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

        $promo_code_data = $this->promo_code_model->single_promo_details($id);

        if($promo_code_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Promo code details not found. Maybe promo code already deleted.');
            redirect(base_url('promo-list'));
        }
        else
        {
            /*echo '<pre>';
            print_r($promo_code_data["details"]);
            exit;*/
            $page_data["promo_code_details"] = $promo_code_data["details"];
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('promo_code/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //Banner Update
    function edit_submit()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        if($this->input->post('promo_code_id'))
        {
            $form_data = array();
            $promo_code_id = $this->input->post('promo_code_id');
            $form_data['promo_code_id'] = $this->input->post('promo_code_id');
            $form_data['promo_code'] = $this->input->post('promo_code');
            $form_data['title'] = $this->input->post('title');
            $form_data['description'] = $this->input->post('promo_description');
            $form_data['eligible_order_price'] = $this->input->post('eligible_order_price');
            $form_data['start_date'] = $this->input->post('start_date');
            $form_data['end_date'] = $this->input->post('end_date');
            $form_data['discount_limit'] = $this->input->post('discount_limit');
            $form_data['discount_type'] = $this->input->post('discount_type');
            $form_data['status'] = $this->input->post('status');
            $form_data['user_specific'] = $this->input->post('user_specific');
            $form_data['user_id'] = $this->input->post('user_id');
            $form_data['usage_count'] = $this->input->post('usage_count');
            $form_data['max_limit'] = $this->input->post('max_limit');

            $update_data = $this->promo_code_model->update_promo_code($form_data);
            if($update_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('promo-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('promo-edit/'.$promo_code_id));
            }
        }
        else
        {
            redirect(base_url(''));
        }
    }
    //Banner Delete
    function delete($id = 0)
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        $delete_banner = $this->promo_code_model->delete_promo_by_id($id);
        if($delete_banner['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_banner['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_banner['message']);
        }
        redirect(base_url('promo-list'));

    }

    function check_code(){
        $response = array('status' => false, 'message' => 'Something went wrong, please try again later.');
        $promo_code = $this->input->post('promo_code');
        if(!empty($promo_code)){
            $data = $this->promo_code_model->check_promo_code($promo_code);
            if(!empty($data) && $data['status'] == 'Y'){
                $response['status'] = false;
                $response['message'] = $data['message'];
            }else{
                $response['status'] = true;
                $response['message'] = $data['message'];
            }
        }
        echo json_encode($response);
    }
}
