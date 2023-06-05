<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class District_management extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model('district_model');
    }

    //Promo Code List
    public function index()
    {
        // City list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "District List";
        $left_data['navigation'] = "district";
        $left_data['sub_navigation'] = "district-list";

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

        // get city list
        $city_list = $this->district_model->city_list($filter_data);
        $page_data['city_list'] = $city_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('district/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //Promo Code Add Page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new District";
        $left_data['navigation'] = "district";
        $left_data['sub_navigation'] = "district-add";

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

        // get state list
        $state_list = $this->district_model->state_list();
        $page_data['state_list'] = $state_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('district/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //City Add Submit
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

        if($this->input->post('district_form'))
        {
            $form_data = array();
            $form_data['state_id'] = $this->input->post('state');
            $form_data['name'] = $this->input->post('district');
            $form_data['charge'] = $this->input->post('charge');
            $form_data['status'] = $this->input->post('status');

            $add_data = $this->district_model->add_district($form_data);
            if($add_data['status'] == "Y")
            {
                $id = $add_data["id"];

                // upload image and update image path in database
                if($_FILES['image']['name'] != '')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/district/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/district/'.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                    {
                        $image = $actual_path;
                    }
                    else
                    {
                        $image = "assets/dist/img/default-user.png";
                    }
                }
                else
                {
                    $image = "assets/dist/img/default-user.png";
                }
                // update image
                $update_type = "first";
                $this->district_model->update_image($id, $image, $update_type);
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('district-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('district-add/'));
            }
        }
        else
        {
            redirect(base_url('district-add/'));
        }

    }

    //City Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit District";
        $left_data['navigation'] = "district";
        $left_data['sub_navigation'] = "district-list";

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

        $district_data = $this->district_model->single_district_details($id);

        if($district_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'District details not found. Maybe district already deleted.');
            redirect(base_url('promo-list'));
        }
        else
        {
            // get state list
            $state_list = $this->district_model->state_list();
            $page_data['state_list'] = $state_list;
            $page_data["district_details"] = $district_data["details"];
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('district/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //City Update
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

        if($this->input->post('district_id'))
        {
            $form_data = array();
            $district_id = $this->input->post('district_id');
            $form_data['id'] = $this->input->post('district_id');
            $form_data['district'] = $this->input->post('district');
            $form_data['charge'] = $this->input->post('charge');
            $form_data['state_id'] = $this->input->post('state_id');
            $form_data['status'] = $this->input->post('status');

            $update_data = $this->district_model->update_district($form_data);
            if($update_data['status'] == "Y")
            {
                $id = $form_data['id'];
                // upload file start
                if($_FILES['image']['name'] != '')
                {
                    $city_data = $this->district_model->single_district_details($id);
                    $city_image = $city_data['details']['image'];
                    $unlink_dir = FILE_UPLOAD_BASE_PATH.$city_image;
                    if(file_exists($unlink_dir)){
                        unlink($unlink_dir);
                    }

                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/district/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/district/'.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                    {
                        $image = $actual_path;
                        // update image
                        $update_type = "next";
                        $this->district_model->update_image($id, $image, $update_type);
                    }

                }

                // upload file end

                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('district-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('district-edit/'.$district_id));
            }
        }
        else
        {
            redirect(base_url(''));
        }
    }
    //City Delete
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

        $delete_city = $this->district_model->delete_city_by_id($id);
        if($delete_city['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_city['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_city['message']);
        }
        redirect(base_url('district-list'));

    }
}
