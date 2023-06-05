<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_settings extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('admin_profile_model');  
        $this->load->model('site_settings_model');      
    }

	public function response($data, $status)
	{
		return $this->output->set_content_type("application/json")
							->set_status_header($status)
							->set_output(json_encode($data));
	}

	public function index()
	{
		$header_data = array();
		$page_data = array();
		$left_data = array();
		$footer_data = array();

		$header_data['title'] = "Master Settings";
		$left_data['navigation'] = "site-settings"; 
		$left_data['sub_navigation'] = "none"; 

		// check login or not 
		if($this->common_model->user_login_check())
		{
			// allow and get admin details
			$admin_details = $this->common_model->get_admin_user_details();
			$header_data['admin_details'] = $admin_details;
			$page_data['admin_details'] = $admin_details;
			$left_data['admin_details'] = $admin_details;

			// get site settings
			$order_settings = $this->site_settings_model->get_order_settings();
			if(count($order_settings) == 0)
			{
				$this->session->set_flashdata('error_message', "Settings not found in database.");
				redirect(base_url('dashboard'));
			}
			else
			{
				$page_data['order_settings'] = $order_settings;
			}
		}
		else
		{
			redirect(base_url(''));
		}

		$order_block_date_list = $this->site_settings_model->get_order_block_list();
		$page_data['order_block_date_list'] = $order_block_date_list;

		// get delivery time slot
		$delivery_slot = $this->site_settings_model->get_delivery_time_slot();
		$page_data['delivery_slot'] = $delivery_slot;

		$referral_settings = $this->site_settings_model->get_referral_settings();
		$page_data['referral_settings'] = $referral_settings;

		$page_data["latest_app_version"] = $this->site_settings_model->get_latest_app_version();
		$page_data["subscription_amount"] = $this->site_settings_model->get_subscription_amount();

		$delivery_driver_app_version_details = $this->db->get_where("FM_preferences", ["name" => "delivery_driver_app_version_details"])->row();
		if (!empty($delivery_driver_app_version_details->content))
		{
			$page_data["delivery_driver_app_version_details"] = json_decode($delivery_driver_app_version_details->content);
		}

		/*echo "<pre>";
		print_r($referral_settings);
		echo "</pre>";*/


		$this->load->view('includes/header_view', $header_data);
		$this->load->view('includes/left_view', $left_data);
		$this->load->view('site_settings_view', $page_data);
		$this->load->view('includes/footer_view', $footer_data);
	}

	public function update_delivery_driver_app_version_details()
	{
		$missing_keys = [];
		$data = [];

		if (!empty($this->input->post("latest_app_version")))
		{
			$data["latest_app_version"] = $this->input->post("latest_app_version");
		}
		else
		{
			$missing_keys[] = "latest_app_version";
		}

		if (!empty($this->input->post("release_date")))
		{
			$data["release_date"] = $this->input->post("release_date");
		}
		else
		{
			$missing_keys[] = "release_date";
		}

		if (!empty($this->input->post("latest_app_download_link")))
		{
			$data["latest_app_download_link"] = $this->input->post("latest_app_download_link");
		}
		else
		{
			$missing_keys[] = "latest_app_download_link";
		}

		if (!empty($this->input->post("release_note")))
		{
			$data["release_note"] = $this->input->post("release_note");
		}
		else
		{
			$missing_keys[] = "release_note";
		}

		if (!empty($missing_keys))
		{
			$missing_string = implode(", ", $missing_keys);
			$missing_string = rtrim($missing_string, ", ");
			$response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => $missing_string." not given!"];
		}
		else
		{
			$this->db->set("content", json_encode($data))->where("name", "delivery_driver_app_version_details")->update("FM_preferences");
			$response = ["success" => true, "message" => "Saved Successfully"];
		}

		$this->response($response, 200);
	}

	function update_order_info()
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}
		$form_data = array();
		$form_data['minimum_order_value'] = $this->input->post('minimum_order_value');
		$form_data['max_day_order_limit'] = $this->input->post('max_day_order_limit');
		$form_data['cod_availability'] = $this->input->post('cod_availability');
		$form_data['online_availability'] = $this->input->post('online_availability');
		$form_data['promo_code_apply_text'] = $this->input->post('promo_code_apply_text');

		$update_data = $this->site_settings_model->update_order_data($form_data);
		redirect(base_url('site_settings'));
		

			
		
	}

	function new_time_slot()
	{

		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}
		$form_data = array();
		$form_data['start_time'] = $this->input->post('slot_start_time');
		$form_data['end_time'] = $this->input->post('slot_end_time');
		$this->site_settings_model->add_new_delivery_time_slot($form_data);
		redirect(base_url('master-settings'));

		// process and add


	}

	function new_date_block()
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}

		$date_range = $this->input->post('custom-date');
        $exp_date_range = explode(' - ', $date_range);

        $start_date = trim($exp_date_range[0]);
        $exp_start_date = explode('/', $start_date);
        $start_date_is = $exp_start_date[2]."-".$exp_start_date[0]."-".$exp_start_date[1];

        $end_date = trim($exp_date_range[1]);
        $exp_end_date = explode('/', $end_date);
        $end_date_is = $exp_end_date[2]."-".$exp_end_date[0]."-".$exp_end_date[1];

        $all_date = $this->common_model->createDateRangeArray($start_date_is, $end_date_is);
        if(count($all_date) > 0)
        {
        	// add new order block
        	$new_order_block = $this->site_settings_model->new_order_date_block($all_date);
        }
        else
        {
        	$this->session->set_flashdata('error_message', "Invalid try or something is wrong!");
        }

        redirect(base_url('master-settings'));

	}

	function new_referral_block()
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}
		$form_data = array();
		$form_data['referral_from'] = $this->input->post('referral_from');
		$form_data['referral_to'] = $this->input->post('referral_to');
		$form_data['min_order_amount'] = $this->input->post('max_limit');
		$form_data['discount_limit'] = $this->input->post('discount_limit');

		$update_data = $this->site_settings_model->save_referral_data($form_data);
		redirect(base_url('master-settings'));
	}
	function update_referral_block()
	{
		$form_data = array();
		$form_data['id'] = $this->input->post('referral_id');
		$form_data['referral_from'] = $this->input->post('referral_from');
		$form_data['referral_to'] = $this->input->post('referral_to');
		$form_data['min_order_amount'] = $this->input->post('max_limit');
		$form_data['discount_limit'] = $this->input->post('discount_limit');
		/*echo'<pre>';
		print_r($form_data);
		die();*/
		$update_data = $this->site_settings_model->update_referral_data($form_data);
		redirect(base_url('master-settings'));
	}

	function delete_block_date($id = 0)
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}
		if($id > 0)
		{
			$this->site_settings_model->delete_block_date_by_id($id);
			$this->session->set_flashdata('success_message', "Date successfully Unblocked for order.");
		}

		
		redirect(base_url('master-settings'));
	}

	function delete_time_slot($id = 0)
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}
		if($id > 0)
		{
			$this->site_settings_model->delete_time_slot_by_id($id);
			
		}
		redirect(base_url('master-settings'));

	}

	/*function password_update_submit()
	{
		if($this->common_model->user_login_check())
		{
			// allow 
		}
		else
		{
			redirect(base_url(''));
		}

		$password = $this->input->post('password');
		$update_data = $this->admin_profile_model->update_password_data($password);

		if($update_data['status'] == "N")
		{
			redirect(base_url('profile?error-message='.$update_data["message"]));
		}

		if($update_data['status'] == "Y")
		{
			redirect(base_url('profile?success-message='.$update_data["message"]));			
		}

	}*/
	function test(){

        /*$generate_otp = "";
        $random_number = "123456789987654321";
        $generate_otp  = substr(str_shuffle($random_number), 0, 4);
        echo $generate_otp;*/
        /*$response = array('status' => false, 'message' => 'Something went wrong, please try again later.');
        $referral_from_id = '1';
        $referral_to_id = '2';
        $generate_otp_1 = $this->test1();
        $unique_id_1 = $generate_otp_1.$referral_from_id;
        $data = $this->promo_code_model->check_promo_code($unique_id_1);
        $referral_settings = $this->site_settings_model->get_referral_settings();
        $current_date = date('Y-m-d H:i:s');
        $endDate =date('Y-m-d',strtotime('+30 day'));
        //echo $endDate;die();
        if(count($referral_settings) > 0){
	        if(!empty($data) && $data['status'] == 'Y'){
	            $response['status'] = false;
	            $response['message'] = $data['message'];
	        }else{
	            $form_data = array();
	        	$form_data['promo_code'] = $unique_id_1;
	        	$form_data['title'] = 'Referral From code';
	        	$form_data['description'] = 'Get Referral From '.$referral_settings[0]['referral_from'].'% Off on Orders above Rs '.$referral_settings[0]['min_order_amount'];
	        	$form_data['eligible_order_price'] = $referral_settings[0]['min_order_amount'];
	        	$form_data['start_date'] = $current_date;
	        	$form_data['end_date'] = $endDate;
	        	$form_data['discount_limit'] = $referral_settings[0]['referral_from'];
	        	$form_data['discount_type'] = 'P';
	        	$form_data['max_limit'] = $referral_settings[0]['discount_limit'];
	        	$form_data['status'] = 'Y';
	        	$form_data['user_specific'] = 'Y';
	        	$form_data['user_id']  = '1';
	        	$form_data['usage_count'] = '1';
	        	$add_data = $this->promo_code_model->add_promo_code($form_data);
	        	if($add_data['status'] == "Y")
            	{
            		$generate_otp_2 = $this->test1();
            		$unique_id_2 = $generate_otp_2.$referral_to_id;
            		$check = $this->promo_code_model->check_promo_code($unique_id_2);
            		if(!empty($check) && $check['status'] == 'Y'){
	            		$response['status'] = false;
	            		$response['message'] = $check['message'];
	        		}else{
	        			$form_data_1 = array();
			        	$form_data_1['promo_code'] = $unique_id_2;
			        	$form_data_1['title'] = 'Referral To code';
			        	$form_data_1['description'] = 'Get Referral To '.$referral_settings[0]['referral_to'].'% Off on Orders above Rs '.$referral_settings[0]['min_order_amount'];
			        	$form_data_1['eligible_order_price'] = $referral_settings[0]['min_order_amount'];
			        	$form_data_1['start_date'] = $current_date;
			        	$form_data_1['end_date'] = $endDate;
			        	$form_data_1['discount_limit'] = $referral_settings[0]['referral_to'];
			        	$form_data_1['discount_type'] = 'P';
			        	$form_data_1['max_limit'] = $referral_settings[0]['discount_limit'];
			        	$form_data_1['status'] = 'Y';
			        	$form_data_1['user_specific'] = 'Y';
			        	$form_data_1['user_id']  = '2';
			        	$form_data_1['usage_count'] = '1';
			        	$add_data_1 = $this->promo_code_model->add_promo_code($form_data_1);
	        		}
            	}

	            $response['status'] = true;
	            $response['message'] = $data['message'];
	        }
	    }else{
	    	$response['status'] = false;
	        $response['message'] = 'Please fill up referral settings';
	    }    
    echo json_encode($response);*/
    	$generate_otp = "";
        $random_number = "123456789987654321ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $generate_otp  = substr(str_shuffle($random_number), 0, 6);
        return $generate_otp;
    
        
    
	}
	function test1()
	{
		$generate_otp = "";
        $random_number = "123456789987654321ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $generate_otp  = substr(str_shuffle($random_number), 0, 6);
        //return $generate_otp;
        $user_lists = $this->db->get_where('FM_customer',array('status'=>'Y'))->result_array();
        /*echo'<pre>';
        print_r($user_lists);
        die();*/
        foreach($user_lists as $k=>$val){
        	$owned_referral_code = $this->test().$val['id'];
        	//echo $owned_referral_code;
        	$this->db->where('id',$val['id']);
        	$this->db->update('FM_customer',array('owned_referral_code'=>$owned_referral_code));
        }
	}

	public function change_latest_app_version ()
	{
		if (isset($_POST["latest_app_version"]) && $_POST["latest_app_version"]!=" ")
		{
			$version = $_POST["latest_app_version"];
			$is_updated = $this->site_settings_model->update_latest_app_version($version);
			if ($is_updated)
			{
				$this->session->set_flashdata('success_message', "Latest App Version Changed Successfully.");
			}
			else
			{	
				$this->session->set_flashdata('error_message', "Failed to Change Latest App Version!");
			}
		}
		else
		{
			$this->session->set_flashdata('error_message', "Latest App Version is Not Given!.");
		}

		redirect(base_url("master-settings"));
	}

	public function change_subscription_amount ()
	{
		if (isset($_POST["subscription_amount"]) && $_POST["subscription_amount"]!=" ")
		{
			$amount = $_POST["subscription_amount"];
			$is_updated = $this->site_settings_model->update_subscription_amount($amount);
			if ($is_updated)
			{
				$this->session->set_flashdata('success_message', "Subscription Amount Changed Successfully.");
			}
			else
			{	
				$this->session->set_flashdata('error_message', "Failed to Change Subscription Amount!.");
			}
		}
		else
		{
			$this->session->set_flashdata('error_message', "Subscription Amount is Not Given!.");
		}

		redirect(base_url("master-settings"));
	}
}
