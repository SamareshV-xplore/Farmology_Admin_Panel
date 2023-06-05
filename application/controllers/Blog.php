<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 

class Blog extends CI_Controller {

	function __construct()
    {
        parent::__construct();   
        $this->load->model('blog_model');    
    }

	//blog list
	public function index()
	{

        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Blog List";
        $left_data['navigation'] = "blog"; 
        $left_data['sub_navigation'] = "blog-list"; 

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

        $filter_status = "all";
        $filter_category = "all";

        if(isset($_REQUEST['status']))
        {
            if($_REQUEST['status'] == 'Y' || $_REQUEST['status'] == 'N')
            {
                $filter_status = $_REQUEST['status'];
            }
            
        }       
        
        $filter_data = array("status" => $filter_status);
        $page_data['filter_data'] = $filter_data;

        // get blog list
        $blog_list = $this->blog_model->blog_list($filter_data);
        $page_data['blog_list'] = $blog_list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('blog/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //------------------------------------------------------- 

    //blog category list
    public function category()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Blog Category List";
        $left_data['navigation'] = "blogcat"; 
        $left_data['sub_navigation'] = "blog-cat-list"; 

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

        $filter_status = "all";
        $filter_category = "all";

        if(isset($_REQUEST['status']))
        {
            if($_REQUEST['status'] == 'Y' || $_REQUEST['status'] == 'N')
            {
                $filter_status = $_REQUEST['status'];
            }
            
        }       
        
        $filter_data = array("status" => $filter_status);
        $page_data['filter_data'] = $filter_data;

        // get blog category list
        $blog_cat_list = $this->blog_model->blog_category_list($filter_data);
        $page_data['blog_cat_list'] = $blog_cat_list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('blog/cat_list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //-------------------------------------------------------   
    //blog add page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add New Blog";
        $left_data['navigation'] = "blog"; 
        $left_data['sub_navigation'] = "blog-add"; 

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

        $blog_category = $this->blog_model->get_blog_category();
        $page_data['blog_category'] = $blog_category;


        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('blog/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //-------------------------------------------------------

    //blog category add page
    public function category_add()
    {

        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add New Category Blog";
        $left_data['navigation'] = "blogcat"; 
        $left_data['sub_navigation'] = "blog-cat-add"; 

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
        $this->load->view('blog/cat_add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //-------------------------------------------------------
    //blog add submit
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

        if($this->input->post('blog_form'))
        {
            $admin_user = $this->db->get_where('FM_admin_user',array("id" => '1'))->row();
            $form_data = array();

            // upload image and update image path in database
            if($_FILES['image']['name'] != '')
            {
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/blog/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = 'uploads/blog/'.$rand_name.basename($_FILES['image']['name']);
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
                    redirect(base_url('blog/add'));
                }    
            }
            else
            {
                $image = "uploads/default/no-image.png";
            }


            $form_data['title'] = $this->input->post('blog_title');
            $form_data['slug'] = $this->input->post('blog_slug');
            $form_data['author_name'] = $admin_user->name;
            $form_data['blog_content'] = $this->input->post('description');
            $form_data['status'] = $this->input->post('status');
            $form_data['category'] = $this->input->post('blog_category');

            $add_data = $this->blog_model->add_blog($form_data);
            if($add_data['status'] == "Y")
            {
                $id = $add_data["id"];
                
                // update image
                $update_type = "first";
                $this->blog_model->update_image($id, $image, $update_type);
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('blog'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('blog/add'));
            }
        }
        else
        {
            redirect(base_url('blog/add'));
        }

    }

    //blog category add submit

    function category_add_submit()
    {

        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }        

        if($this->input->post('blog_form'))
        {
            
            $form_data = array();
            $form_data['title'] = $this->input->post('title');
            $form_data['status'] = $this->input->post('status');

            $add_data = $this->blog_model->add_category_blog($form_data);
            if($add_data['status'] == "Y")
            {
                
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('blog/category'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('blog/category_add'));
            }
        }
        else
        {
            redirect(base_url('blog/category_add'));
        }
    }

    //blog edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Blog";
        $left_data['navigation'] = "blog"; 
        $left_data['sub_navigation'] = "blog-list"; 
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

        $blog_data = $this->blog_model->single_blog_details($id);
       
        if($blog_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Blog details not found or blog already deleted.');
            redirect(base_url('blog'));
        }
        else
        {
            $page_data["blog_details"] = $blog_data["details"];
        }

        
        $blog_category = $this->blog_model->get_blog_category();
        $page_data['blog_category'] = $blog_category;


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('blog/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //------------------------------------------------------

    //blog category edit page

    public function category_edit($id = 0)
    {

        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Blog";
        $left_data['navigation'] = "blog"; 
        $left_data['sub_navigation'] = "blog-list"; 
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

        $blog_cat_data = $this->blog_model->single_cat_blog_details($id);
       
        if($blog_cat_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Blog category details not found or blog already deleted.');
            redirect(base_url('blog/category'));
        }
        else
        {
            $page_data["blog_cat_details"] = $blog_cat_data["details"];
        }

        
        


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('blog/cat_edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //------------------------------------------------------
    //edit submit
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

        if($this->input->post('blog_id'))
        {
            $admin_user = $this->db->get_where('FM_admin_user',array("id" => '1'))->row();
            $form_data = array();
            $blog_id = $this->input->post('blog_id');
            $form_data['id'] = $blog_id;
            $form_data['title'] = $this->input->post('blog_title');
            $form_data['slug'] = $this->input->post('blog_slug');
            $form_data['author_name'] = $admin_user->name;
            $form_data['blog_content'] = $this->input->post('description');
            $form_data['status'] = $this->input->post('status');
            $form_data['category'] = $this->input->post('blog_category');

            $update_data = $this->blog_model->update_blog($form_data);
            if($update_data['status'] == "Y")
            {
                $id = $blog_id;
                // upload file start
                if($_FILES['image']['name'] != '')
                {
                    $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                   {
                        $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/blog/';
                        $rand_name = time()."-";
                        $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                        $actual_path = 'uploads/blog/'.$rand_name.basename($_FILES['image']['name']);
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                        {
                           $image = $actual_path;
                           // update image
                           $update_type = "next";
                           $this->blog_model->update_image($id, $image, $update_type);
                        }
                    }else{
                        $this->session->set_flashdata('error_message', 'Please Upload a valid image file. Ex:- (jpg,jpeg,png) format');
                        redirect(base_url('blog/edit/'.$blog_id));
                    }    
                    
                }
                
                // upload file end                

                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('blog'));

            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('blog/edit/'.$blog_id));
            }          

        }
        else
        {
            redirect(base_url(''));
        }
    }

    //edit category blog submit
    function category_edit_submit()
    {

        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }        

        if($this->input->post('blog_cate_id'))
        {
            
            $form_data = array();
            $blog_cate_id = $this->input->post('blog_cate_id');
            $form_data['id'] = $blog_cate_id;
            $form_data['title'] = $this->input->post('title');
            $form_data['status'] = $this->input->post('status');
            

            $update_data = $this->blog_model->update_category_blog($form_data);
            if($update_data['status'] == "Y")
            {
                

                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('blog/category'));

            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('blog/category_edit/'.$blog_cate_id));
            }          

        }
        else
        {
            redirect(base_url(''));
        }
    }
    // Delete
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

        $delete_blog = $this->blog_model->delete_blog_by_id($id);
        if($delete_blog['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_blog['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_blog['message']);
        }
        redirect(base_url('blog'));

    }

    // Change Homepage Blog
    public function change_homepage_blog ()
    {
        if($this->common_model->user_login_check())
        {   
            $missing_key = [];

            if (isset($_POST["id"]))
            {
                $id = $_POST["id"];
            }
            else
            {
                $missing_key[] = "id";
            }

            if (isset($_POST["status"]))
            {
                $status = $_POST['status'];
            }
            else
            {
                $missing_key[] = "status";
            }

            if (count($missing_key) == 0)
            {
                $homepage_blog_change = $this->blog_model->change_homepage_blog_by_id($id, $status);
                if($homepage_blog_change['status'] == "Y")
                {
                    $this->session->set_flashdata('success_message', $homepage_blog_change['message']);
                    redirect(base_url('blog'));
                }
                else
                {
                    $this->session->set_flashdata('error_message', $homepage_blog_change['message']);
                    redirect(base_url('blog'));
                }
            }
            else
            {
                $missing_string = implode(", ", $missing_key);
                $missing_string = rtrim($missing_string, ", ");
                $this->session->set_flashdata('error_message', $missing_string." - not given!");
                redirect(base_url('blog'));
            }
        }
        else
        {
            redirect(base_url(''));
        }
    }

    //Delete blog category
    function category_delete($id = 0)
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        } 

        $delete_blog = $this->blog_model->delete_blog_category_by_id($id);
        if($delete_blog['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_blog['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_blog['message']);
        }
        redirect(base_url('blog/category'));
    }

    function ajax_get_slug()
    {
        $response = array("status" => "N", "slug" => "");
        if($this->input->post('blog_title'))
        {
            $title = urldecode($this->input->post('blog_title'));
            $slug = $this->common_model->slugify($title); 
            if($this->input->post('blog_id'))
            {
                $blog_id = $this->input->post('blog_id');
            }
            else
            {
                $blog_id = 0;
            }
            $slug_status = $this->blog_model->check_slug_exist($slug, $blog_id);
            if($slug_status == 'N')
            {
                $response = array("status" => "Y", "slug" => $slug);
            }
            else
            {
                $response = array("status" => "N", "slug" => $slug);
            }

        }
        echo json_encode($response);
    }

    function ajax_get_custom_slug()
    {
        $response = array("status" => "N", "slug" => "");
        if($this->input->post('blog_slug'))
        {
            $slug = urldecode($this->input->post('blog_slug'));
            $slug = $this->common_model->slugify($slug); 
            if($this->input->post('blog_id'))
            {
                $blog_id = $this->input->post('blog_id');
            }
            else
            {
                $blog_id = 0;
            }
            $slug_status = $this->blog_model->check_slug_exist($slug, $blog_id);
            if($slug_status == 'N')
            {
                $response = array("status" => "Y", "slug" => $slug);
            }
            else
            {
                $response = array("status" => "N", "slug" => $slug);
            }

        }
        echo json_encode($response);
    }

	
}
