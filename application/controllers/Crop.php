<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crop extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('crop_model');        
    }

	//crop List
	public function index()
	{
        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Crop List";
        $left_data['navigation'] = "crop"; 
        $left_data['sub_navigation'] = "crop-list"; 

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

        // get crop list
        $crop_list = $this->crop_model->crop_list($filter_data);
        $page_data['crop_list'] = $crop_list;

        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('crop/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //crop add page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new crop";
        $left_data['navigation'] = "crop"; 
        $left_data['sub_navigation'] = "crop-add"; 

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
        $this->load->view('crop/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //crop add submit
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

        if($this->input->post('crop_form'))
        {
            $form_data = array();
            

            if($_FILES['image']['name'] != '')
            {
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/crop/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/crop/'.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                    {
                       $image = $actual_path;
                    }
                    else
                    {
                        $image = "uploads/default/no-image.png";
                    }
                }else{
                    $this->session->set_flashdata('error_message', 'Please Upload a valid image file. Ex:- (jpg,jpeg,png) format');
                    redirect(base_url('crop/add'));
                }    
            }
            else
            {
                $image = "uploads/default/no-image.png";
            }

            



            $form_data['title'] = $this->input->post('crop_title');
            $form_data['image'] = $image;
            $form_data['status'] = $this->input->post('status');


            $add_data = $this->crop_model->add_crop($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('crop'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('crop'));
            }
        }
        else
        {
            redirect(base_url('crop'));
        }

    }
    //crop Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Crop";
        $left_data['navigation'] = "crop"; 
        $left_data['sub_navigation'] = "crop-list"; 

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


        $crop_data = $this->crop_model->get_crop_details_id($id);
       
        if(count($crop_data) == 0)
        {
            $this->session->set_flashdata('error_message', 'Invalid URL. Crop not found.');
            redirect(base_url('crop'));
        }
        else
        {
            $page_data["crop_data"] = $crop_data;
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('crop/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //crop Update
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

        if($this->input->post('crop_form'))
        {


            $form_data = array();
            $crop_id = $this->input->post('crop_id');
            $form_data['id'] = $crop_id;
            

            if($_FILES['image']['name'] != '')
            {
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/crop/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/crop/'.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                    {
                        $image = $actual_path;
                        $update_data = array("id" => $crop_id, "image" => $image);
                        $this->crop_model->update_crop_image($update_data);
                       
                    }
                    else
                    {
                        // do nothing
                    }
                }else{
                    $this->session->set_flashdata('error_message', 'Please Upload a valid image file. Ex:- (jpg,jpeg,png) format');
                    redirect(base_url('crop/edit/'.$crop_id));
                }    
            }

            
            $form_data['title'] = $this->input->post('crop_title');
            $form_data['status'] = $this->input->post('status');
            

            $add_data = $this->crop_model->update_crop($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('crop'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('crop'));
            }
        }
        else
        {
            redirect(base_url('crop'));
        }

    
    }
    //crop Delete
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

        $delete_banner = $this->crop_model->delete_crop_by_id($id);
        if($delete_banner['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_banner['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_banner['message']);
        }
        redirect(base_url('crop'));

    }

    

	
}
