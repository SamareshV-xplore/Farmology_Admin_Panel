<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');  
        $this->load->model('category_model'); 
        $this->load->model('crop_model');   
        $this->load->model('city_model');   
    }

    function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

	//product List
	public function index()
	{
        // product list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Product List";
        $left_data['navigation'] = "product"; 
        $left_data['sub_navigation'] = "product-list"; 

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
            $filter_data = array("status" => $_REQUEST['status'], "cate1" => 0, "cate2" => 0);
        }
        else
        {
            $filter_data = array("status" => 'all', "cate1" => 0, "cate2" => 0);
        }


        $page_data['filter_data'] = $filter_data;

        $parent_category = $this->category_model->get_category_list_by_parent_id(0);
        $page_data['main_parent'] = $parent_category;

        // get product list
        $product_list = $this->product_model->get_product_list($filter_data);
       
        $page_data['product_list'] = $product_list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('product/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    // product add page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new product";
        $left_data['navigation'] = "product"; 
        $left_data['sub_navigation'] = "product-add"; 

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
        $filter_data = array("status" => 'all');
        $crop_list = $this->crop_model->crop_list($filter_data);
        $state_list = $this->city_model->get_state_list();
        $page_data['main_parent'] = $parent_category;
        $page_data['crop_list'] = $crop_list;
        
        $list ='<option value="">Select state</option>';
        foreach($state_list as $k=> $state){
            $list.= '<option value="'.$state["id"].'">'.$state["state"].'</option>';
        }
        $page_data['state_list'] = $list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('product/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    
    //product add submit
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

        if($this->input->post('product_form'))
        {            
            $form_data = array();
            
            $category_id = $this->input->post('cate');
            
            $crop_id = $this->input->post('crop');
            //$category_id = $this->input->post('cate3');
            $name = $this->input->post('name');
            $slug = $this->input->post('slug');
            $description = $this->input->post('description');
            $short_description = $this->input->post('short_description');
            $status = $this->input->post('status');

            $variation_title = $this->input->post('variation_title');
            $price = $this->input->post('price');
            $discount = $this->input->post('discount'); 
            $state_id = $this->input->post('state_id');

            $meta_title = $this->input->post("meta_title");
            $meta_description = $this->input->post("meta_description");
            $meta_keyword = $this->input->post("meta_keyword");


            if($this->input->post('ai_title') != null && $this->input->post('ai_value') != null)
            {
                $ai_title = $this->input->post('ai_title');
                $ai_value = $this->input->post('ai_value');
            }        
            else
            {
                $ai_title = array();
                $ai_value  = array();
            }

            if($_FILES['image']['name'] != '')
            {
                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/product/';
                $rand_name = time()."-";
                $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                $upload_file = str_replace(" ","-",$upload_file);
                $actual_path = 'uploads/product/'.$rand_name.basename($_FILES['image']['name']);
                $actual_path = str_replace(" ","-",$actual_path);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                {
                   $image = $actual_path;
                }
                else
                {
                    $image = "uploads/default/no-image.png";
                }
            }
            else
            {
                $image = "uploads/default/no-image.png";
            }


            $form_data['crop_id']  = $crop_id;
            $form_data['category_id'] = $category_id;
            $form_data['image'] = $image;
            $form_data['title'] = $name;
            $form_data['slug'] = $slug;
            $form_data['description'] = $description;
            $form_data['short_description'] = $short_description;
            $form_data['status'] = $status;
            $form_data['variation_title'] = $variation_title;
            $form_data['price'] = $price;
            $form_data['discount'] = $discount;
            $form_data['state_id'] = $state_id;
            $form_data['ai_title'] = $ai_title;
            $form_data['ai_value'] = $ai_value;
            
            
            $add_data = $this->product_model->add_product($form_data);
            if($add_data['status'] == "Y")
            {
                


                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('product'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('product'));
            }
        }
        else
        {
            redirect(base_url('product'));
        }

    }
    //category Edit page
    public function edit($id = 0)
    {        
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit product";
        $left_data['navigation'] = "product"; 
        $left_data['sub_navigation'] = "product-list"; 

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

        $product_meta = $this->meta_data_model->get_product_meta_data_by_id($id);
        $page_data['product_meta'] = $product_meta;

        // product details
        $product_details = $this->product_model->get_product_details_by_id($id);
        $filter_data = array("status" => 'all');
        $crop_list = $this->crop_model->crop_list($filter_data);
        $selected_category_id = $this->product_model->get_selected_catId($id);
        $state_list = $this->city_model->get_state_list();
        foreach($selected_category_id as $k => $val){
            $cat_id [] = $val['category_id'];
        }

        $selected_crop_id = $this->product_model->get_selected_cropId($id);
        foreach($selected_crop_id as $k => $value){
            $crop_id [] = $value['crop_id'];
        }

        
        $page_data['crop_list'] = $crop_list;
        $page_data['selected_cropid'] = $crop_id;
        $page_data['selected_cateid'] = $cat_id;
        $page_data['product_details'] = $product_details;

        $list ='<option value="">Select state</option>';
        foreach($state_list as $k=> $state){
            $list.= '<option value="'.$state["id"].'">'.$state["state"].'</option>';
        }
        $page_data['state_list'] = $list;
        $page_data['state_actual_list'] = $state_list;


        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('product/edit_view', $page_data);
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

        if($this->input->post('product_form'))
        {            
            $form_data = array();
            
            $category_id = $this->input->post('cate');
            $crop_id = $this->input->post('crop');
            //$category_id = $this->input->post('cate3');
            $id = $this->input->post('product_id');
            $name = $this->input->post('name');
            $slug = $this->input->post('slug');
            $description = $this->input->post('description');
            $short_description = $this->input->post('short_description');
            $status = $this->input->post('status');

            $variation_id = $this->input->post('option_u_id');
            $variation_title = $this->input->post('variation_title');
            $price = $this->input->post('price');
            $discount = $this->input->post('discount');
            $state_id = $this->input->post('state_id');
            $variation_type = $this->input->post('option_type');

            


           if($this->input->post('ai_title') != null && $this->input->post('ai_value') != null)
            {
                $ai_type = $this->input->post('ai_type');
                $ai_title = $this->input->post('ai_title');
                $ai_value = $this->input->post('ai_value');
            }        
            else
            {
                $ai_type = array();
                $ai_title = array();
                $ai_value  = array();
            }

            $image = "";

            if($_FILES['image']['name'] != '')
            {
                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/product/';
                $rand_name = time()."-";
                $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                $upload_file = str_replace(" ","-",$upload_file);
                $actual_path = 'uploads/product/'.$rand_name.basename($_FILES['image']['name']);
                $actual_path = str_replace(" ","-",$actual_path);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                {
                   $image = $actual_path;
                }
                
            }
            


            $form_data['id'] = $id;
            $form_data['crop_id']  = $crop_id;
            $form_data['category_id'] = $category_id;
            $form_data['image'] = $image;
            $form_data['title'] = $name;
            $form_data['slug'] = $slug;
            $form_data['description'] = $description;
            $form_data['short_description'] = $short_description;
            $form_data['status'] = $status;
            $form_data['variation_id'] = $variation_id;
            $form_data['variation_type'] = $variation_type;
            $form_data['variation_title'] = $variation_title;
            $form_data['price'] = $price;
            $form_data['discount'] = $discount;
            $form_data['state_id'] = $state_id;
            $form_data['ai_title'] = $ai_title;
            $form_data['ai_value'] = $ai_value;
            $form_data['ai_type'] = $ai_type;

            
            
            
            $update_data = $this->product_model->update_product($form_data);
            if($update_data['status'] == "Y")
            {
                
                 $product_id = $id;
                // add meta data
                

                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('product'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('product'));
            }
        }
        else
        {
            redirect(base_url('product'));
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

        $delete_product = $this->product_model->delete_product_by_id($id);
        if($delete_product['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_product['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_product['message']);
        }
        redirect(base_url('product'));

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
                $html = '<option value="0">Select Child Category</option>';
                foreach($category_rows as $category_row)
                {
                    $html.= '<option value="'.$category_row["id"].'">'.$category_row["title"].'</option>';
                }
                $response = array("status" => "Y", "message" => "List Found.", "html" => $html);
            }
            else
            {
                $html = '<option value="0">Select Child Category</option>';
                $response = array("status" => "Y", "message" => "List Found.", "html" => $html);
            }

        }
        echo json_encode($response);
    }

    function ajax_get_product_slug()
    {
        $response = array("status" => "N", "slug" => "");
        if($this->input->post('name'))
        {
            $name = urldecode($this->input->post('name'));
            $slug = $this->common_model->slugify($name); 
            if($this->input->post('product_id'))
            {
                $product_id = $this->input->post('product_id');
            }
            else
            {
                $product_id = 0;
            }
            $slug_status = $this->product_model->check_slug_exist($slug, $product_id);
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
            if($this->input->post('slug'))
            {
                $product_id = $this->input->post('product_id');
            }
            else
            {
                $product_id = 0;
            }

            $slug = urldecode($this->input->post('slug'));
            $slug_status = $this->product_model->check_slug_exist($slug, $product_id);
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

    function update_product_order()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        $product_id = $this->input->post('id');
        $order_value = $this->input->post('order_value');

        $this->product_model->update_product_order($product_id, $order_value);

        echo "success";
    }

    public function toggle_latest_status()
    {
        $product_id = $this->input->post('product_id');
        $result = $this->product_model->toggle_latest($product_id);
        if($result!=null)
        {
            if($result > 0)
            {
                $response = array(
                    "success" => true,
                    "message" => "Latest Product Updated."
                );
            }
            else
            {
                $response = array(
                    "success" => false,
                    "message" => "Failed to Update Latest Product!"
                );
            }
        }
        else
        {
            $response = array(
                "success" => false,
                "message" => "Only 2 products can be latest! uncheck one of them and try again."
            );
        }

        print_r(json_encode($response));
    }

    public function get_latest_products()
    {
        $condArr = array("status"=>"Y", "is_latest"=>"Y");
        $latest_products = $this->db->get_where("FM_product", $condArr)->result();
        if(count($latest_products))
        {
            $response = array(
                "success" => true,
                "message" => "Latest products list fetched successfully.",
                "latest_products" => $latest_products
            );
        }
        else
        {
            $response = array(
                "success" => false,
                "message" => "Failed to fetch latest products list!"
            );
        }

        print_r(json_encode($response));
    }

    public function set_latest_products()
    {
        $is_updated = false;
        if(isset($_POST["data"]))
        {
            $data = $_POST["data"];
            $is_updated = $this->product_model->set_latest_products($data);
        }

        if($is_updated)
        {
            $response = array(
                "success" => true,
                "message" => "Latest products set successfully."
            );  
        }
        else
        {
            $response = array(
                "success" => false,
                "message" => "Failed to set latest products data!"
            );
        }

        print_r(json_encode($response));
    }

	public function getLatestProductCount()
    {
        echo $this->db->select('count(*) as count')->from('FM_product')->where('is_latest', 'yes')->get()->row()->count;
    }

    public function change_product_variation_availability()
    {
        $missing_keys = $condition = $data = [];

        if (!empty($this->input->post("product_id"))) {
            $condition["product_id"] = $this->input->post("product_id");
        }
        else {
            $missing_keys[] = "product_id";
        }

        if (!empty($this->input->post("variation_id"))) {
            $condition["id"] = $this->input->post("variation_id");
        }
        else {
            $missing_keys[] = "variation_id";
        }

        if (!empty($this->input->post("state_id"))) {
            $condition["state_id"] = $this->input->post("state_id");
        }
        else {
            $missing_keys[] = "state_id";
        }

        if (!empty($this->input->post("availability_status"))) {
            $data["is_available"] = $this->input->post("availability_status");
        }
        else {
            $missing_keys[] = "availability_status";
        }

        if (!empty($missing_keys)) {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtrim($missing_string, ", ");
            $response = ["success" => false, "message" => $missing_string." not given!"];
        }
        else {
            $is_updated = $this->product_model->update_product_variation_on_condition($data, $condition);
            if ($is_updated) {
                $response = ["success" => true, "message" => "Availability Changed"];
            }
            else {
                $response = ["succcess" => false, "message" => "Failed to change Product Variation Availability!"];
            }
        }

        $this->response($response, 200);
    }
}
