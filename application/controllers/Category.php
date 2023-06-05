<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');        
    }

	//Banner List
	public function index()
	{
        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Category List";
        $left_data['navigation'] = "category"; 
        $left_data['sub_navigation'] = "category-list"; 

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

        // get category list
        $category_list = $this->category_model->category_list($filter_data);
        $page_data['category_list'] = $category_list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('category/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //category add page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new category";
        $left_data['navigation'] = "category"; 
        $left_data['sub_navigation'] = "category-add"; 

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

        // get all parent_category
        $parent_category = $this->category_model->get_category_list_by_parent_id(0);
        $page_data['main_parent'] = $parent_category;

        
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('category/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //category add submit
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

        if($this->input->post('category_form'))
        {
            $form_data = array();
            /*$main_parent = $this->input->post('main_parent');
            $sub_parent = $this->input->post('sub_parent');
            if($main_parent > 0)
            {
                if($sub_parent > 0)
                {
                    $parent_id = $sub_parent;
                }
                else
                {
                    $parent_id = $main_parent;
                }
            }
            else
            {
                $parent_id = 0;
            }*/

            $parent_id =  $main_parent = '0';

            if($_FILES['image']['name'] != '')
            {
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/category/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/category/'.$rand_name.basename($_FILES['image']['name']);
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
                    redirect(base_url('category/add'));
                }    
            }
            else
            {
                $image = "uploads/default/no-image.png";
            }

            if($_FILES['icon']['name'] != '')
            {
                $extension = pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/icon/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['icon']['name']);
                    $actual_path = 'uploads/icon/'.$rand_name.basename($_FILES['icon']['name']);
                    if (move_uploaded_file($_FILES['icon']['tmp_name'], $upload_file))
                    {
                       $icon = $actual_path;
                    }
                    else
                    {
                        $icon = "uploads/default/no-image.png";
                    }
                }else{
                    $this->session->set_flashdata('error_message', 'Please Upload a valid icon image file. Ex:- (jpg,jpeg,png) format');
                    redirect(base_url('category/add'));
                }    
            }
            else
            {
                $icon = "uploads/default/no-image.png";
            }



            $form_data['parent_id'] = $parent_id;
            $form_data['title'] = $this->input->post('cate_title');
            $form_data['slug'] = $this->input->post('cate_slug');
            $form_data['description'] = $this->input->post('description');
            $form_data['image'] = $image;
            $form_data['icon'] = $icon;
            $form_data['status'] = $this->input->post('status');
            $form_data['is_featured'] = $this->input->post('is_featured');

            $form_data['meta_title'] = $this->input->post('meta_title');
            $form_data['meta_description'] = $this->input->post('meta_description');
            $form_data['meta_keyword'] = $this->input->post('meta_keyword');
            

            $add_data = $this->category_model->add_category($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('category'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('category'));
            }
        }
        else
        {
            redirect(base_url('category'));
        }

    }
    //category Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Category";
        $left_data['navigation'] = "category"; 
        $left_data['sub_navigation'] = "category-list"; 

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

        // get all parent_category
        $parent_category = $this->category_model->get_category_list_by_parent_id(0);
        $page_data['main_parent'] = $parent_category;

        $category_data = $this->category_model->get_category_details_id($id);
       
        if(count($category_data) == 0)
        {
            $this->session->set_flashdata('error_message', 'Invalid URL. Category not found.');
            redirect(base_url('category'));
        }
        else
        {
            $page_data["category_data"] = $category_data;
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('category/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    //category Update
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

        if($this->input->post('category_form'))
        {


            $form_data = array();
            $cate_id = $this->input->post('cate_id');
            $form_data['id'] = $cate_id;
            $main_parent = $this->input->post('main_parent');
            
            /*if($this->input->post('sub_parent'))
            {
               $sub_parent = $this->input->post('sub_parent'); 
            }
            else
            {
                $sub_parent = 0;
            }
            
            if($main_parent > 0)
            {
                if($sub_parent > 0)
                {
                    $parent_id = $sub_parent;
                }
                else
                {
                    $parent_id = $main_parent;
                }
            }
            else
            {
                $parent_id = 0;
            }*/

            $parent_id = $main_parent;

            if($_FILES['image']['name'] != '')
            {
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/category/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/category/'.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                    {
                        $image = $actual_path;
                        $update_data = array("id" => $cate_id, "image" => $image);
                        $this->category_model->update_category_image($update_data);
                       
                    }
                    else
                    {
                        // do nothing
                    }
                }else{
                    $this->session->set_flashdata('error_message', 'Please Upload a valid image file. Ex:- (jpg,jpeg,png) format');
                    redirect(base_url('category/edit/'.$cate_id));
                }    
            }

            if($_FILES['icon']['name'] != '')
            {
                $extension = pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION);

                if($extension=='jpg' || $extension=='jpeg' || $extension=='png')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/icon/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['icon']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/icon/'.$rand_name.basename($_FILES['icon']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);
                    if (move_uploaded_file($_FILES['icon']['tmp_name'], $upload_file))
                    {
                        $icon = $actual_path;
                        $update_data = array("id" => $cate_id, "icon" => $icon);
                        $this->category_model->update_category_icon($update_data);
                       
                    }
                    else
                    {
                        // do nothing
                    }
                }else{
                    $this->session->set_flashdata('error_message', 'Please Upload a valid icon image file. Ex:- (jpg,jpeg,png) format');
                    redirect(base_url('category/edit/'.$cate_id));
                }    
            }
            



            $form_data['parent_id'] = $parent_id;
            $form_data['title'] = $this->input->post('cate_title');
            $form_data['slug'] = $this->input->post('cate_slug');
            $form_data['description'] = $this->input->post('description');
            $form_data['status'] = $this->input->post('status');
            $form_data['is_featured'] = $this->input->post('is_featured');

            $form_data['meta_title'] = $this->input->post('meta_title');
            $form_data['meta_description'] = $this->input->post('meta_description');
            $form_data['meta_keyword'] = $this->input->post('meta_keyword');

            $add_data = $this->category_model->update_category($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('category'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('category'));
            }
        }
        else
        {
            redirect(base_url('category'));
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

        $delete_banner = $this->category_model->delete_category_by_id($id);
        if($delete_banner['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_banner['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_banner['message']);
        }
        redirect(base_url('category'));

    }

    function ajax_get_category_list_by_parent_id()
    {
        $response = array("status" => "N", "message" => "Something was wrong");

        if($this->input->post('parent_id'))
        {
            $parent_id = $this->input->post('parent_id');
            $category_rows = $this->category_model->get_category_list_by_parent_id($parent_id);

            if(count($category_rows) > 0)
            {
                $html = '<select name="sub_parent" id="sub_parent" class="form-control"><option value="0">Select Child</option>';
                foreach($category_rows as $category_row)
                {
                    $html.= '<option value="'.$category_row["id"].'">'.$category_row["title"].'</option>';
                }
                $html.= '</select>';
                $response = array("status" => "Y", "message" => "List Found.", "html" => $html);
            }
            else
            {
                $html = '<select name="sub_parent" id="sub_parent" class="form-control"><option value="0">Select Child</option></select>';
                $response = array("status" => "Y", "message" => "List Found.", "html" => $html);
            }

        }
        echo json_encode($response);
    }

    function ajax_get_category_slug()
    {
        $response = array("status" => "N", "slug" => "");
        if($this->input->post('cate_id'))
        {
            $cate_id = $this->input->post('cate_id');
        }
        else
        {
            $cate_id = 0;
        }
        if($this->input->post('cate_title'))
        {
            $cate_title = urldecode($this->input->post('cate_title'));
            $slug = $this->common_model->slugify($cate_title);
            $slug_status = $this->category_model->check_slug_exist($slug, $cate_id);
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

    function check_custom_slug()
    {
        $response = array("status" => "N", "slug" => "");
        if($this->input->post('slug'))
        {
            if($this->input->post('cate_id'))
            {
                $cate_id = $this->input->post('cate_id');
            }
            else
            {
                $cate_id = 0;
            }

            $slug = urldecode($this->input->post('slug'));
            $slug_status = $this->category_model->check_slug_exist($slug, $cate_id);
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
