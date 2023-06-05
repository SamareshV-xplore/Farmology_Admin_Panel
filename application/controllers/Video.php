<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Video extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('video_model');        
    }

	//video list
	public function index()
	{
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Video List";
        $left_data['navigation'] = "video"; 
        $left_data['sub_navigation'] = "video-list"; 

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
        $video_list = $this->video_model->get_video_list($filter_data);
        $page_data['video_list'] = $video_list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('video/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //Video Add Page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Upload New Video";
        $left_data['navigation'] = "video"; 
        $left_data['sub_navigation'] = "video-add"; 

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
        $this->load->view('video/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //cideo upload submit
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

        if($this->input->post('title'))
        {
            $form_data = array();
            $title = $this->input->post('title');
			$yt_video_id = $this->input->post('yt_video_id');
			$description = $this->input->post('description');
			$status = $this->input->post('status');
			
			// process to update database
            $form_data = array("title" => $title, "description" =>$description, "yt_video_id" => $yt_video_id, "status" => $status);
            $add_data = $this->video_model->add_video($form_data);
			$id = $add_data["id"];
			

            $this->session->set_flashdata('success_message', 'New video successfully added.');
            redirect(base_url('video'));
                
           

        }
        else
        {
            redirect(base_url('video/add'));
        }

    }
    

    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Video";
        $left_data['navigation'] = "video"; 
        $left_data['sub_navigation'] = "video-edit"; 

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

        $video_details = $this->video_model->get_single_video_details($id);
       
        if(count($video_details) == 0)
        {
            $this->session->set_flashdata('error_message', 'Invalid try or video  does not exist.');
            redirect(base_url('video'));
        }
        else
        {
            $page_data["video_details"] = $video_details;
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('video/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    
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

        if($this->input->post('video_form'))
        {
            
            $form_data = array();
            $id = $this->input->post('video_id');
			$title = $this->input->post('title');
			$description = $this->input->post('description');
            $yt_video_id = $this->input->post('yt_video_id');
            $status = $this->input->post('status');

            $form_data = array('id' => $id, "description" => $description, "title" => $title, "yt_video_id" => $yt_video_id, "status" => $status);
			$this->video_model->update_video_data($form_data);
			
			


            $this->session->set_flashdata('success_message', 'Video successfully updated.');
            redirect(base_url('video'));        

        }
        else
        {
            redirect(base_url('video'));
        }

    }



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

        $delete_video = $this->video_model->video_delete($id);
        if($delete_video['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_video['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_video['message']);
        }
        redirect(base_url('video'));

    }


    function update_video_status()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        $video_id = $this->input->post('id');
        $status_value = $this->input->post('status_value');

        $this->video_model->update_video_status($video_id, $status_value);

        echo "success";
    }

	
}
