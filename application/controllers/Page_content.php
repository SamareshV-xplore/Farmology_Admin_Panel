<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_content extends CI_Controller {

	function __construct()
    {
        parent::__construct();   
        $this->load->model('page_content_model');    
    }

	//content List
	public function index()
	{
        // content list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Page Content List";
        $left_data['navigation'] = "content-list"; 
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

        if(isset($_REQUEST['page']))
        {
            $filter_data = array("page" => $_REQUEST['page']);
        }
        else
        {
            $filter_data = array("page" => 'all');
        }

        $page_data['filter_data'] = $filter_data;

        $page_list = $this->page_content_model->get_page_list();
        $page_data['page_list'] = $page_list;

        $page_content_list = $this->page_content_model->get_page_content_list($filter_data);
        $page_data['page_content_list'] = $page_content_list;        
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('page_content/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    
    
    //content edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Content";
        $left_data['navigation'] = "content-list"; 
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

        $page_list = $this->page_content_model->get_page_list();
        $page_data['page_list'] = $page_list;
        $page_content_details = $this->page_content_model->get_single_page_content_details($id);

        $page_data['page_content_details'] = $page_content_details;

        if(count($page_content_details) == 0)
        {
            $this->session->set_flashdata('error_message', 'Invalid try or page content does not exist');
            redirect(base_url('page-content'));
        }
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('page_content/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //page content update
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

        if($this->input->post('page_content_id'))
        {
            $page_content_id = $this->input->post('page_content_id');

            $form_data = array();            
            $form_data['id'] = $this->input->post('page_content_id');
            $form_data['title'] = $this->input->post('title');
            if($this->input->post('page_id') == '2'){
                $text=str_ireplace('<p>','',$this->input->post('description'));
                $text=str_ireplace('</p>','',$text);
                $form_data['description'] = $text;
            }else{
                $form_data['description'] = $this->input->post('description');
            }
            
            

            $update_data = $this->page_content_model->update_content($form_data);

            if($update_data['status'] == 'Y')
            {

                // if image found to upload
                if($_FILES['image']['name'] != '')
                {
                    $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                    {
                        $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/cms/';
                        $rand_name = time()."-";
                        $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                        $upload_file = str_replace(" ","-",$upload_file);
                        $actual_path = 'uploads/cms/'.$rand_name.basename($_FILES['image']['name']);
                        $actual_path = str_replace(" ","-",$actual_path);
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                        {
                           $image = $actual_path;                       
                           $this->page_content_model->update_image($form_data['id'], $image);
                        }
                    }else{
                        $this->session->set_flashdata('error_message', 'Please Upload a valid image file. Ex:- (jpg,jpeg,png) format');
                        redirect(base_url('page-content-edit/'.$page_content_id));
                    }    
                }


                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('page-content'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('page-content'));
            }            

        }
        else
        {
            redirect(base_url(''));
        }
    }

    
   
	
}
