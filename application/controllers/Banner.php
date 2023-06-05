<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Banner extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('banner_model');        
    }

    public function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

	//Banner List
	public function index()
	{
        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Banner List";
        $left_data['navigation'] = "banner"; 
        $left_data['sub_navigation'] = "banner-list"; 

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
        $banner_list = $this->banner_model->banner_list($filter_data);
        $page_data['banner_list'] = $banner_list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('banner/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //Banner Add Page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new banner";
        $left_data['navigation'] = "banner"; 
        $left_data['sub_navigation'] = "banner-add"; 

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

        $page_data["app_redirections_list"] = $this->banner_model->get_list_of_app_redirections();
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('banner/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //Banner Add Submit
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

        if($this->input->post('banner_form'))
        {
            $form_data = array();


            // uploading image and adding uploaded image path in database
            if($_FILES['image']['name'] != '')
            {
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $image_size = $_FILES["image"]["size"];

                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/banner/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/banner/'.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);

                    if ($image_size <= 800000)
                    {
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
                        $image_compressed_and_uploaded = $this->compressAndUploadImage($_FILES['image']['tmp_name'], $upload_file, 75);
                        if ($image_compressed_and_uploaded)
                        {
                            $image = $actual_path;
                        }
                        else
                        {
                            $image = "assets/dist/img/default-user.png";
                        }
                    }
                }
                else
                {
                    $this->session->set_flashdata('error_message', 'Please Upload a valid image file. Ex:- (jpg,jpeg,png) format');
                    redirect(base_url('banner-add'));
                }    
            }
            else
            {
                $image = "assets/dist/img/default-user.png";
            }




            $form_data['title'] = $this->input->post('title');
            $form_data['description'] = $this->input->post('description');
            $form_data['link'] = $this->input->post('link');
            $form_data['status'] = $this->input->post('status');

            if (!empty($this->input->post("redirect_to")))
            {
                $form_data["redirect_to"] = $this->input->post("redirect_to");
            }

            $add_data = $this->banner_model->add_banner($form_data);
            if($add_data['status'] == "Y")
            {
                $id = $add_data["id"];

                
                // update image
                $update_type = "first";
                $this->banner_model->update_image($id, $image, $update_type);
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('banner-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('banner-add/'));
            }
        }
        else
        {
            redirect(base_url('banner-add/'));
        }

    }

    public function uploadCompressedImage()
    {
        if (!empty($_FILES["image"]["name"]))
        {
            $image_save_path = "uploads/testing_uploaded_images/".$_FILES["image"]["name"];
            $image_uploading_path = FILE_UPLOAD_BASE_PATH.$image_save_path;
            $this->compressAndUploadImage($_FILES["image"]["tmp_name"], $image_uploading_path, 75);
            $response = ["success" => true, "message" => "Image Compressed and Uploaded Successfully.", "uploadedImageURL" => FRONT_URL.$image_save_path];
        }
        else
        {
            $response = ["success" => false, "message" => "Image is not found!"];
        }

        $this->response($response, 200);
    }

    private function compressAndUploadImage($source, $destination, $quality)
    { 
        // Getting image information 
        $imgInfo = getimagesize($source); 
        $mime = $imgInfo['mime']; 
         
        // Creating a new image based on image type 
        switch($mime){ 
            case 'image/jpeg': 
                $image = imagecreatefromjpeg($source); 
                break; 
            case 'image/png': 
                $image = imagecreatefrompng($source); 
                break; 
            case 'image/gif': 
                $image = imagecreatefromgif($source); 
                break; 
            default: 
                $image = imagecreatefromjpeg($source); 
        } 
         
        // Saving image 
        return imagejpeg($image, $destination, $quality); 
    }

    //Banner Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Banner";
        $left_data['navigation'] = "banner"; 
        $left_data['sub_navigation'] = "banner-edit"; 

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

        $banner_data = $this->banner_model->single_banner_details($id);
       
        if($banner_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Banner details not found. Maybe banner already deleted.');
            redirect(base_url('banner-list'));
        }
        else
        {
            $page_data["banner_details"] = $banner_data["details"];
        }

        $page_data["app_redirections_list"] = $this->banner_model->get_list_of_app_redirections();


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('banner/edit_view', $page_data);
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

        if($this->input->post('banner_id'))
        {
            
            $form_data = array();
            $banner_id = $this->input->post('banner_id');
            $form_data['banner_id'] = $this->input->post('banner_id');
            $form_data['title'] = $this->input->post('title');
            $form_data['description'] = $this->input->post('description');
            $form_data['link'] = $this->input->post('link');
            $form_data['status'] = $this->input->post('status');

            if (!empty($this->input->post("redirect_to")))
            {
                $form_data["redirect_to"] = $this->input->post("redirect_to");
            }
            else
            {
                $form_data["redirect_to"] = NULL;
            }

            $update_data = $this->banner_model->update_banner($form_data);
            if($update_data['status'] == "Y")
            {
                $id = $form_data['banner_id'];
                // upload file start
                if($_FILES['image']['name'] != '')
                {
                    $banner_data = $this->banner_model->single_banner_details($id);
                    $banner_image = $banner_data['details']['image'];
                    $unlink_dir = FILE_UPLOAD_BASE_PATH.$banner_image;

                    $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                    if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                    {

                        $image_size = $_FILES["image"]["size"];

                        if(file_exists($unlink_dir)){
                            unlink($unlink_dir);
                        }

                        $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/banner/';
                        $rand_name = time()."-";
                        $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);    
                        $upload_file = str_replace(" ","-",$upload_file);                
                        $actual_path = 'uploads/banner/'.$rand_name.basename($_FILES['image']['name']);
                        $actual_path = str_replace(" ","-",$actual_path);

                        if ($image_size <= 800000)
                        {
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
                            $image_compressed_and_uploaded = $this->compressAndUploadImage($_FILES['image']['tmp_name'], $upload_file, 75);
                            if ($image_compressed_and_uploaded)
                            {
                                $image = $actual_path;
                            }
                            else
                            {
                                $image = "assets/dist/img/default-user.png";
                            }
                        }

                        // update image
                        $update_type = "next";
                        $this->banner_model->update_image($id, $image, $update_type);

                    }
                    else
                    {
                        $this->session->set_flashdata('error_message', 'Please Upload a valid image file. Ex:- (jpg,jpeg,png) format');
                        redirect(base_url('banner-edit/'.$banner_id));
                    }    
                }
                
                // upload file end                
                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('banner-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('banner-edit/'.$banner_id));
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

        $delete_banner = $this->banner_model->delete_banner_by_id($id);
        if($delete_banner['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_banner['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_banner['message']);
        }
        redirect(base_url('banner-list'));

    }
}

?>