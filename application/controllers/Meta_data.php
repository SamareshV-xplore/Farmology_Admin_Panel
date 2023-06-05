<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meta_data extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model('meta_data_model');
    }

    //Meta Data List
    public function index()
    {
        // meta data list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Meta Deta List";
        $left_data['navigation'] = "meta";
        $left_data['sub_navigation'] = "meta-list";

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



        // get meta data list
        $meta_data_list = $this->meta_data_model->meta_data_list();
        $page_data['meta_data_list'] = $meta_data_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('meta_data/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    // Meta Data Add Page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new Meta Data";
        $left_data['navigation'] = "meta";
        $left_data['sub_navigation'] = "meta-add";

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
        $this->load->view('meta_data/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //Meta Data Add Submit
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

        if($this->input->post('meta_data_form'))
        {
            $form_data = array();
            $form_data['page_name'] = $this->input->post('page_name');
            $form_data['meta_title'] = $this->input->post('meta_title');
            $form_data['meta_description'] = $this->input->post('meta_description');
            $form_data['meta_keyword'] = $this->input->post('meta_keyword');

            $add_data = $this->meta_data_model->add_meta_data($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('meta-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('meta-add/'));
            }
        }
        else
        {
            redirect(base_url('meta-add/'));
        }

    }
    //Meta Data Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Meta Data";
        $left_data['navigation'] = "meta";
        $left_data['sub_navigation'] = "meta-edit";

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

        $meta_data_data = $this->meta_data_model->single_meta_data_details($id);

        if($meta_data_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Meta Data details not found. Maybe meta data already deleted.');
            redirect(base_url('meta-list'));
        }
        else
        {
            $page_data["meta_data_details"] = $meta_data_data["details"];
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('meta_data/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //Meta data Update
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

        if($this->input->post('meta_data_id'))
        {

            $form_data = array();
            $meta_data_id = $this->input->post('meta_data_id');
            $form_data['meta_data_id'] = $this->input->post('meta_data_id');
            $form_data['page_name'] = $this->input->post('page_name');
            $form_data['meta_title'] = $this->input->post('meta_title');
            $form_data['meta_description'] = $this->input->post('meta_description');
            $form_data['meta_keyword'] = $this->input->post('meta_keyword');

            $update_data = $this->meta_data_model->update_meta_data($form_data);
            if($update_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('meta-list'));

            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('meta-edit/'.$meta_data_id));
            }

        }
        else
        {
            redirect(base_url(''));
        }
    }
    //Meta Data Delete
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

        $delete_meta_data = $this->meta_data_model->delete_meta_data_by_id($id);
        if($delete_meta_data['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_meta_data['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_meta_data['message']);
        }
        redirect(base_url('meta-list'));

    }
}
