<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
    }

    //Users List
    public function index()
    {
        // users list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "User Management";
        $left_data['navigation'] = "users";
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

        // get users list
        $users_list = $this->users_model->users_list($filter_data);
        $page_data['users_list'] = $users_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('users/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //User Add Page
    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Add new User";
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


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('banner/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //User Add Submit
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
            $form_data['title'] = $this->input->post('title');
            $form_data['description'] = $this->input->post('description');
            $form_data['link'] = $this->input->post('link');
            $form_data['status'] = $this->input->post('status');

            $add_data = $this->users_model->add_banner($form_data);
            if($add_data['status'] == "Y")
            {
                $id = $add_data["id"];

                // upload image and update image path in database
                if($_FILES['image']['name'] != '')
                {
                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/banner/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/banner/'.$rand_name.basename($_FILES['image']['name']);
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
                $this->users_model->update_image($id, $image, $update_type);
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

    //User Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit User";
        $left_data['navigation'] = "users";
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

        $user_data = $this->users_model->single_user_details($id);
        $page_data["states"] = $this->get_list_of_states();
        $page_data["cities"] = $this->get_list_of_cities();
        $page_data["languages"] = $this->get_list_of_languages();
        $page_data["crops"] = $this->get_list_of_crops();

        if($user_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'User details not found. Maybe user already deleted.');
            redirect(base_url('users-list'));
        }
        else
        {
            $page_data["user_details"] = $user_data["details"];
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('users/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //User Update
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

        if($this->input->post('user_id'))
        {
            $form_data = array();
            $user_id = $this->input->post('user_id');
            $form_data['user_id'] = $this->input->post('user_id');
            $form_data['first_name'] = $this->input->post('first_name');
            $form_data['last_name'] = $this->input->post('last_name');
            if (isset($_POST['email']) && $this->input->post('email') != "")
            {
                $form_data['email'] = $this->input->post('email');
            }
            $form_data['phone'] = $this->input->post('phone');
            $form_data['state_id'] = $this->input->post('state');
            $form_data['area_value'] = $this->input->post('land_area_value');
            $form_data['area_unit'] = $this->input->post('land_area_unit');
            $form_data['language'] = $this->input->post('language');
            $form_data['status'] = $this->input->post('status');
            $form_data['referral_by'] = $this->input->post('referred_by');
            $update_data = $this->users_model->update_user($form_data);

            $address_data['customer_id'] = $form_data['user_id'];
            $address_data['name'] = $form_data['first_name']." ".$form_data['last_name'];
            $address_data['phone'] = $form_data['phone'];
            $address_data['address_1'] = $this->input->post('address_1');
            $address_data['address_2'] = $this->input->post('address_2');
            $address_data['landmark'] = $this->input->post('landmark');
            $address_data['state_id'] = $this->input->post('state');
            $address_data['city_id'] = $this->input->post('city');
            $address_data['zip_code'] = $this->input->post('zipcode');
            $is_address_updated = $this->users_model->update_user_address($address_data);

            $selected_crops_data['customer_id'] = $form_data['user_id'];
            $selected_crops_data['selected_crops'] = $this->input->post("selected_crops");
            $is_crops_updated = $this->users_model->update_user_selected_crops($selected_crops_data);
            
            if($update_data['status'] == "Y")
            {
                $id = $form_data['user_id'];
                // upload file start
                if($_FILES['image']['name'] != '')
                {
                    $user_data = $this->users_model->single_user_details($id);
                    $user_image = $user_data['details']['profile_image'];
                    $unlink_dir = FILE_UPLOAD_BASE_PATH.$user_image;
                    if(file_exists($unlink_dir)){
                        unlink($unlink_dir);
                    }

                    $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/users/';
                    $rand_name = time()."-";
                    $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name']);
                    $upload_file = str_replace(" ","-",$upload_file);
                    $actual_path = 'uploads/users/'.$rand_name.basename($_FILES['image']['name']);
                    $actual_path = str_replace(" ","-",$actual_path);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                    {
                        $image = $actual_path;
                        // update image
                        $update_type = "next";
                        $this->users_model->update_image($id, $image, $update_type);
                    }

                }

                // upload file end

                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('users-list'));

            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('user-edit/'.$user_id));
            }

        }
        else
        {
            redirect(base_url(''));
        }
    }

    //User Delete
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

        $delete_user = $this->users_model->delete_user_by_id($id);
        if($delete_user['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_user['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_user['message']);
        }
        redirect(base_url('users-list'));

    }

    // Check if user email already exists
    public function check_email(){
        $email = strtolower($this->input->post('email'));
        $id = strtolower($this->input->post('user_id'));
        if(!empty($email)){
            $status = $this->users_model->check_user_email($email, $id);
            return $status;
        }else{
            return false;
        }
    }

    // Check if user phone number already exists
    public function check_mobile(){
        $response = array('status' => false, 'data' => 'Mobile number is required.');
        $mobile = $this->input->post('mobile');
        if(!empty($mobile)){
            $data = $this->users_model->check_user_mobile($mobile);
            if(!empty($data) && $data['status'] == 'Y'){
                $response['status'] = true;
                $response['data'] = $data['details'];
            }else{
                $response['status'] = false;
                $response['data'] = $data['message'];
            }
        }
        echo json_encode($response);
    }

    function get_list_of_states ()
    {
        $states = [];
        $list = $this->users_model->get_list_of_states();
        for ($i=0; $i<count($list); $i++)
        {
            $state = new stdClass;
            $state->value = $list[$i]->id;
            $state->name = ucwords(strtolower($list[$i]->state));
            $states[$i] = $state;
        }

        return $states;
    }

    function get_list_of_cities ()
    {
        $cities = [];
        $list = $this->users_model->get_list_of_cities();
        for ($i=0; $i<count($list); $i++)
        {
            $city = new stdClass;
            $city->value = $list[$i]->id;
            $city->name = ucwords(strtolower($list[$i]->name));
            $cities[$i] = $city;
        }

        return $cities;
    }

    function get_list_of_languages ()
    {   
        $languages = [];
        $list = $this->users_model->get_list_of_languages();
        for ($i=0; $i<count($list); $i++)
        {
            $lang = new stdClass;
            $lang->value = $list[$i]->language_name[0];
            $lang->name = $list[$i]->language_name;
            $languages[$i] = $lang;
        }

        return $languages;
    }

    function get_list_of_crops ()
    {
        $crops = [];
        $list = $this->users_model->get_list_of_crops();
        for ($i=0; $i<count($list); $i++)
        {
            $crop = new stdClass;
            $crop->value = $list[$i]->id;
            $crop->name = $list[$i]->title;
            $crops[$i] = $crop;
        }

        return $crops;
    }

    function download_users_contact_details_in_csv()
    {
        $users_contacts_data_array = $this->users_model->get_users_contacts();
        $this->export_in_csv_file_from_array($users_contacts_data_array);
    }

    public function export_in_csv_file_from_array($data_array)
    {
        $column_headers = ["NAME","PHONE","EMAIL"];
        $filename = "users-contact-details_".date("Y-m-d").".csv";

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        $handle = fopen('php://output', 'w');
        fputcsv($handle, $column_headers);
        foreach ($data_array as $data) {
            fputcsv($handle, $data);
        }
        fclose($handle);
        exit;
    }

}
