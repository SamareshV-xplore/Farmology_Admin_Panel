<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchants extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
        $this->load->model("users_model");
	}

	public function index()
	{
		 // users list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Merchants";
        $left_data['navigation'] = "merchants";

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
        $this->load->model('users_model');
        $users_list = $this->users_model->merchant_list($filter_data);
        $page_data['users_list'] = $this->add_no_of_refers_field($users_list);

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('merchants/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
	}

    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Merchant";
        $left_data['navigation'] = "merchants";

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
            $this->session->set_flashdata('error_message', 'Merchant details not found. Maybe Merchant already deleted.');
            redirect(base_url('merchants'));
        }
        else
        {
            $page_data["user_details"] = $user_data["details"];
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('merchants/edit_view', $page_data);
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

        if($this->input->post('user_id'))
        {
            $form_data = array();
            $id = $this->input->post('user_id');
            $form_data['first_name'] = $this->input->post('first_name');
            $form_data['last_name'] = $this->input->post('last_name');

            if (isset($_POST['email']) && $this->input->post('email') != "") {
                $form_data['email'] = $this->input->post('email');
            }

            if (isset($_POST['bank_name']) && $this->input->post('bank_name') != "") {
                $form_data['bank_name'] = $this->input->post('bank_name');
            }

            if (isset($_POST['holder_name']) && $this->input->post('holder_name') != "") {
                $form_data['holder_name'] = $this->input->post('holder_name');
            }

            if (isset($_POST['bank_account_no']) && $this->input->post('bank_account_no') != "") {
                $form_data['bank_account_no'] = $this->input->post('bank_account_no');
            }

            if (isset($_POST['ifsc_code']) && $this->input->post('ifsc_code') != ""){
                $form_data['ifsc_code'] = $this->input->post('ifsc_code');
            }

            $form_data['phone'] = $this->input->post('phone');
            $form_data['state_id'] = $this->input->post('state');
            $form_data['status'] = $this->input->post('status');
        
            $update_data = $this->users_model->update_merchant($id, $form_data);

            $address_data['customer_id'] = $id;
            $address_data['name'] = $form_data['first_name']." ".$form_data['last_name'];
            $address_data['phone'] = $form_data['phone'];
            $address_data['address_1'] = $this->input->post('address_1');
            $address_data['address_2'] = $this->input->post('address_2');
            $address_data['state_id'] = $this->input->post('state');
            $address_data['city_id'] = $this->input->post('city');
            $address_data['zip_code'] = $this->input->post('zipcode');
            $is_address_updated = $this->users_model->update_user_address($address_data);

            $selected_crops_data['customer_id'] = $id;
            $selected_crops_data['selected_crops'] = $this->input->post("selected_crops");
            $is_crops_updated = $this->users_model->update_user_selected_crops($selected_crops_data);
            
            if($update_data['status'] == "Y")
            {
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
                redirect(base_url('merchants'));

            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('merchant-edit/'.$id));
            }

        }
        else
        {
            redirect(base_url(''));
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

    private function add_no_of_refers_field ($users_list)
    {   
        $updated_users_list = [];
        foreach ($users_list as $user)
        {   
            $merchant_id = $user["id"];
            $no_of_refers = $this->get_number_of_refer($merchant_id);
            $user["no_of_refers"] = $no_of_refers;
            $updated_users_list[] = $user;
        }

        return $updated_users_list;
    }

    public function get_number_of_refer($customer_id)
    {
        $SQL = "SELECT COUNT(*) AS count FROM FM_customer WHERE (type IS NULL OR type='user') AND referral_by='$customer_id'";
        $number_of_refer = $this->db->query($SQL)->row()->count;
        return $number_of_refer;
    }

    public function commission()
    {
         // users list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Merchant Commission";
        $left_data['navigation'] = "merchants_commision";

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
        $commissions = $this->common_model->get_merchant_commssion_all();
        $page_data['commissions'] = $commissions;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('merchants_commission_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function updateCommission()
    {
        $id = $this->input->post('id');
        $commission = $this->input->post('commission');

        $data = [
            'updated_date'  => date('Y-m-d h:i:s'),
            'merchant_commission' => $commission
        ];

        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('FM_product_variation');

        if ($this->db->affected_rows() > 0) {
            $response = array('success' => true, 'message' => 'Success', 'isUpdated' => true);
        }
        else{
            $response = array('success' => true, 'message' => 'failed', 'isUpdated' => false);
        }

        echo json_encode($response);
    }
}