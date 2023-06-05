<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Zip_management extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('zip_model');
    }

    //Promo Code List
    public function index($city_id = 0)
    {
        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Zip Code List";
        $left_data['navigation'] = "city";
        $left_data['sub_navigation'] = "city-list";

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

        if(empty($city_id) || $city_id == 0)
        {
            redirect(base_url('city-list'));
        }

        // get zip list
        $city_list = $this->zip_model->zip_list($city_id);
        $city = $this->zip_model->city_by_id($city_id);
        $page_data['zip_list'] = $city_list;
        $page_data['city'] = $city;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('zip/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //Zip Add
    function add_zip()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        if($this->input->post('city_id') && $this->input->post('city_id') !== null)
        {
            $city_id = $this->input->post('city_id');
            $form_data = array();
            $form_data['city_id'] = $this->input->post('city_id');
            $form_data['pin_code'] = $this->input->post('pin_code');

            $add_data = $this->zip_model->add_zip($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('zip-list/'.$city_id));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('zip-list/'.$city_id));
            }
        }
        else
        {
            redirect(base_url('city-list/'));
        }

    }

    //Zip Update
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

        if($this->input->post('zip_id'))
        {
            $form_data = array();
            $zip_id = $this->input->post('zip_id');
            $form_data['zip_id'] = $zip_id;
            $form_data['zip_code'] = $this->input->post('zip_code');

            $update_data = $this->zip_model->update_zip_code($form_data);
            if($update_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect($_SERVER['HTTP_REFERER']);
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else
        {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    //Zip Delete
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

        $delete_zip = $this->zip_model->delete_zip_by_id($id);
        if($delete_zip['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_zip['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_zip['message']);
        }
        redirect($_SERVER['HTTP_REFERER']);

    }
}
