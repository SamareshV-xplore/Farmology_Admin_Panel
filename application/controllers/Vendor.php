<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		// $this->load->helper('url');
		$this->load->database();
		$this->load->library('session');
	}

	public function index()
	{
		$user = $this->session->userdata('userdata');
		if ($user == null) {
			$data['title'] = 'Login';
			$this->load->view("vendor/login", $data);
		}
		else{
			$this->dashboard();
		}
	}

	public function signup()
	{
		$data['pincodes'] = $this->db->select('pin_code')->from('FM_pin_code_lookup')->where('is_deleted', 'N')->get()->result();

		$data['title'] = 'Register';
		$this->load->view("vendor/signup", $data);
	}


	public function sendOtp()
    { 
    	$missingParam = array();

		if($this->input->post("contact")==null || !isset($_POST["contact"]))
		{
			$missingParam[] = "email_or_mobile";
		}
		else
		{
			$user_mobile_or_email = $this->input->post("contact");
		}

		if(count($missingParam)>0)
		{
			$response = array(
				"success" => false,
				"message" => $missingParam[0]." is not given",
				"userOtp" => (object)array()
			);
		}
		else
		{
			$vendor = $this->getUserByEmailOrMobile($user_mobile_or_email);
			if ($vendor == null) {
				$response = array(
					"status" => false,
					"message" => 'No account with this email or mobile number',
					"otpDetails" => (object)array()
				);
			}
			else
			{
				if (!filter_var($user_mobile_or_email, FILTER_VALIDATE_EMAIL))
				{
				  	$is_email = false;
				  	$phone = $user_mobile_or_email;
				}
				else
				{
					$is_email = true;
					$email = $user_mobile_or_email;
				}

				$random_otp_number = mt_rand(1000,9999);

				if($is_email)
				{
					$insertDataArr = array(
						"otp" => $random_otp_number,
						"email" => $email,
						"is_expired" => "N",
						"created_date" => date("Y-m-d H:i:s")
					);

					$this->db->insert("FM_email_otp_list", $insertDataArr);
					$id = $this->db->insert_id();
					$this->send_otp_to_email($email, $random_otp_number);
					$otp = $random_otp_number;
					$otp_source = "email";
					$message = "OTP has been sent";
					$userData = [
						'email_or_phone' => $email,
					];
				}
				else
				{
					$insertDataArr = array(
						"otp" => $random_otp_number,
						"phone" => $phone,
						"is_expired" => "N",
						"created_date" => date("Y-m-d H:i:s")
					);

					$this->db->insert("FM_phone_otp_list", $insertDataArr);
					$id = $this->db->insert_id();
					$this->send_otp_to_phone($phone, $random_otp_number);
					$otp = $random_otp_number;
					$otp_source = "phone";
					$message = "OTP has been sent";
					$userData = [
						'email_or_phone' => $phone,
					];
				}

				$response = array(
					"status" => true,
					"message" => $message,
					"otpDetails" => (object)array(
						"id" => strval($id),
						"otp" => $otp,
						"otpSource" => $otp_source,
					)
				);

				$userdata = [
					'id'	=> $vendor->hash_id,
					'name'	=> $vendor->name,
					'email' => $vendor->email,
					'phone'	=> $vendor->phone
				];

				$this->session->set_userdata('userdata', $userdata);
			}
		}
		echo json_encode($response);
    }

    public function getUserByEmailOrMobile($email_or_mobile)
    {
    	return $this->db->query("select * from FM_vendor where email = '$email_or_mobile' or phone = '$email_or_mobile'")->row();
    }

    // COMMON MOBILE SMS SENDING FUNCTION //
    function send_sms($phone_number, $otp_number)
    {
        // Account details
        $authkey = '335354AMUyfpp0uQ5f097111P1';

        // Template Details
        $template_id = '5facdebebfdb4a7b0a2c7d25';
     
     	// Mobile Number
        $numbers = $phone_number;

        // OTP Number
        $otp = $otp_number;

        $url = "https://api.msg91.com/api/v5/otp?authkey=".$authkey."&template_id=".$template_id."&mobile=".$numbers."&otp=".$otp;
		$curl = curl_init();
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));
		$response = curl_exec($curl);
		curl_close($curl);

        return $response;
    }

    function send_otp_to_phone($phone=null, $otp=null)
    { 
    	if($phone!=null && $otp!=null)
    	{
    		return $this->send_sms($phone, $otp);
    	}
    	else
    	{
    		return false;
    	}
    }

	// COMMON EMAIL SENDING FUNCTION //
	function email_send($send_to, $subject, $body)
    {
    	$this->load->library('email');
        $result = $this->email
            		   ->from(FROM_EMAIL, 'Farmology')
            		   ->to($send_to)
            		   ->subject($subject)
            		   ->message($body)
            		   ->send();
		
		return $result;
    }

    function send_otp_to_email($email=null, $otp=null)
  	{
	    if($email!=null && $otp!=null)
	    {
	      $subject = "Verify Login OTP - Farmology.com";
	      $body = "<p>Your Farmology Login OTP is <b>".$otp."</b>.<br>Do not share with anyone.</p>";
	      return $this->email_send($email, $subject, $body);
	    }
	    else
	    {
	      return false;
	    }
  	}

  	public function submitUser()
  	{
  		$name = $this->input->post('name');
  		$shopName = $this->input->post('shopName');
  		$mobile = $this->input->post('mobile');
  		$email = $this->input->post('email');
  		$address = $this->input->post('address');
  		$service_area = $this->input->post('serviceArea');

  		if ($this->getUserByEmailOrMobile($mobile) != null || $this->getUserByEmailOrMobile($email) != null) {
  			$res = [
  				'success'	=> true,
  				'message'	=> 'Email or Phone already associated with another account',
  				'isSubmitted' => false
  			];
  		}
  		else{
  			$data = [
	  			'hash_id'		=> $this->GUID(),
	  			'name'			=> $name,
	  			'shop_name'		=> $shopName,
	  			'address'		=> $address,
	  			'email'			=> $email,
	  			'phone'			=> $mobile,
	  			'service_area'	=> json_encode($service_area),
	  			'status'		=> 'A',
	  			'created_date'	=> date('Y-m-d')
	  		];

	  		$this->db->insert('FM_vendor', $data);

	  		if ($this->db->affected_rows() > 0) {
	  			$res = [
	  				'success'	=> true,
	  				'message'	=> 'Record inserted',
	  				'isSubmitted' => true
	  			];
	  		}
	  		else{
	  			$res = [
	  				'success'	=> true,
	  				'message'	=> 'Unknown Error',
	  				'isSubmitted' => false
	  			];
	  		}
  		}
  		echo json_encode($res);
  	}

  	public function GUID()
	{
		if (function_exists('com_create_guid') === true)
		{
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	public function dashboard()
	{
		$user = $this->session->userdata('userdata');
		if ($user == null) {
			$this->index();
			return;
		}
		else{
			$data['vendorName'] = $user['name'];
		}
		$data['title'] = 'Dashboard';
		$user_id = $user['id'];

		$data['total_products'] = $this->db->query("SELECT COUNT(*) as count FROM FM_product WHERE vendor_id = '$user_id' AND status = 'Y'")->row()->count;
		$data['total_orders'] = $this->db->query("SELECT COUNT(*) as count FROM FM_order_details INNER JOIN FM_order ON FM_order_details.order_id = FM_order.id INNER JOIN FM_product ON FM_order_details.product_id = FM_product.id WHERE FM_product.vendor_id = '$user_id' AND FM_product.status != 'C'")->row()->count;
		$total_customer = $this->db->query("SELECT COUNT(*) as count FROM FM_order_details INNER JOIN FM_order ON FM_order_details.order_id = FM_order.id INNER JOIN FM_product ON FM_order_details.product_id = FM_product.id WHERE FM_product.vendor_id = '$user_id' AND FM_product.status != 'C' GROUP BY FM_order.customer_id")->row();
		$data['current_orders'] = $this->db->query("SELECT COUNT(*) as count FROM FM_order_details INNER JOIN FM_order ON FM_order_details.order_id = FM_order.id INNER JOIN FM_product ON FM_order_details.product_id = FM_product.id WHERE FM_product.vendor_id = '$user_id' AND FM_product.status = 'P'")->row()->count;
		$data['total_customers'] = is_object($total_customer) ? $total_customer->count : 0;

		$this->load->view('vendor/dashboard', $data);
	}
	public function products()
	{
		$user = $this->session->userdata('userdata');
		if ($user == null) {
			$this->index();
			return;
		}
		else{
			$data['vendorName'] = $user['name'];
		}


		$data['product_list'] = $this->get_product_list($user['id']);

		$data['title'] = 'Products';
		$this->load->view('vendor/dashboard', $data);
	}

	public function addNewProduct()
	{
		$user = $this->session->userdata('userdata');
		if ($user == null) {
			$this->index();
			return;
		}
		else{
			$data['vendorName'] = $user['name'];
		}

		$parent_category = $this->get_category_list_by_parent_id(0);
        $filter_data = array("status" => 'all');
        $crop_list = $this->crop_list($filter_data);
        $state_list = $this->get_state_list();
        $data['main_parent'] = $parent_category;
        $data['crop_list'] = $crop_list;
        
        $list ='<option value="">Select state</option>';
        foreach($state_list as $k=> $state){
            $list.= '<option value="'.$state["id"].'">'.$state["state"].'</option>';
        }
        $data['state_list'] = $list;

		$data['title'] = 'Add New Product';
		$this->load->view('vendor/dashboard', $data);
	}

	function get_category_list_by_parent_id($parent_id = 0, $status = 'all')
    {
        $category_row = array();
        $this->db->select("*");
        $this->db->from("FM_product_category");
        $this->db->where("parent_id", $parent_id);
        $this->db->where("status !=", 'D');
        if($status != 'all')
        {
            $this->db->where("status", $status);
        }
        
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $parent_details = $this->get_category_short_details_by_id($rows->parent_id);
                $category_row[] = array("id" => $rows->id, "title" => $rows->title, "description" => $rows->description, "slug" => $rows->slug, "image" => FRONT_URL.$rows->image, "status" => $rows->status, "created_date" => $rows->created_date, "updated_date" => $rows->updated_date, "parent_details" => $parent_details, "is_featured" => $rows->is_featured);
            }
        }

        return $category_row;

    }
    function get_category_short_details_by_id($cate_id = 0)
    {
        $response = array("id" => "0", "title" => "Parent");

        $this->db->select("id, title");
        $this->db->from("FM_product_category");
        $this->db->where("id", $cate_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $response = array("id" => $row->id, "title" => $row->title);
        }

        return $response;
    }
    function crop_list($filter_data){
        $list = array();
        
        if(isset($filter_data['status']))
        {
            $filter_status =  $filter_data['status'];
        }
        else
        {
            $filter_status =  "all";
        }

        $this->db->select("*");
        $this->db->from("FM_crop");
        $this->db->where("status !=", 'D');
        if($filter_status != 'all'){
            $this->db->where("status", $filter_status);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $list[] = array("id" => $rows->id, "title" => $rows->title, "image" => FRONT_URL.$rows->image, "status" => $rows->status, "created_date" => $rows->created_date, "updated_date" => $rows->updated_date);
            }
        }
        return $list;

    }
    function get_state_list()
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_state_lookup");
        $this->db->where("is_deleted","Y");
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "state" => $row->state,
                    "created_date" => $row->created_date
                );
            }
        }
        return $list;
    }

    public function addProduct()
    {
    	try {
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

	            $user = $this->session->userdata('userdata');


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
	            $form_data['user_id'] = $user['id'];

	            
	            
	            $add_data = $this->add_product($form_data);
	            if($add_data['status'] == "Y")
	            {
	                


	                $this->session->set_flashdata('success_message', $add_data['message']);
	                redirect(base_url('vendors/products'));
	            }
	            else
	            {
	                $this->session->set_flashdata('error_message', $add_data['message']);
	                redirect(base_url('vendors/products'));
	            }
	        }
	        else
	        {
	            redirect(base_url('vendors/dashboard'));
	        }
    	} catch (Exception $e) {
    		echo $e;
    	}
    }

    function add_product($data)
    {
        $category_id = $data['category_id'];
        $crop_id = $data['crop_id'];
        $title = $data['title'];
        $slug = $data['slug'];
        $description = $data['description'];
        $short_description = $data['short_description'];
        $status = $data['status'];
        $image = $data['image'];
        $variation_title = $data['variation_title'];
        $price = $data['price'];
        $discount = $data['discount'];
        $state_id = $data['state_id'];
        $ai_title = $data['ai_title'];
        $ai_value = $data['ai_value'];
        $vendor = $data['user_id'];

        // check slug
        $slug_status = $this->check_slug_exist($slug, 0);

        if($slug_status == 'N')
        {
            $product_data = array("SKU" => "P".time(), "slug" => $slug, "title" => $title, "short_description" => $short_description, "description" => $description, "status" => $status, "created_date" => date("Y-m-d H:i:s"), "vendor_id" => $vendor);
            $this->db->insert("FM_product", $product_data);
            $product_id = $this->db->insert_id();

            if($product_id > 0)
            {

                // insert image
                $img_insert_data = array("product_id" => $product_id, "image" => $image, "created_date" => date("Y-m-d H:i:s"));
                $this->db->insert("FM_product_image", $img_insert_data);


                $crop_count = count($crop_id);
                for($cr=0;$cr < $crop_count; $cr++){
                    $var_crop_id = $crop_id[$cr];
                    $var_insert_crop_data = array("product_id" => $product_id, "crop_id" => $var_crop_id, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_crop_mapping", $var_insert_crop_data);
                }
                $category_count = count($category_id);
                for($cat=0;$cat < $category_count; $cat++){
                    $var_cat_id = $category_id[$cat];
                    $var_insert_cat_data = array("product_id" => $product_id, "category_id" => $var_cat_id, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_category_mapping", $var_insert_cat_data);
                }

                $variation_count = count($variation_title);
                for($i = 0; $i < $variation_count; $i++)
                {
                    $var_title = $variation_title[$i];
                    $var_price = $price[$i];
                    $var_discount = $discount[$i];
                    $var_state_id = $state_id[$i];

                    $var_insert_data = array("product_id" => $product_id, "title" => $var_title, "price" => $var_price, "discount" =>  $var_discount, "state_id" =>  $var_state_id, "created_date" => date("Y-m-d H:i:s"), "status" => "Y", "ord_by" => 0);
                    $this->db->insert("FM_product_variation", $var_insert_data);
                }

                $ai_count = count($ai_title);
                for($ai = 0; $ai < $ai_count; $ai++)
                {
                    $ai_title_str = $ai_title[$ai];
                    $ai_value_str = $ai_value[$ai];

                    $ai_data = array("product_id" => $product_id, "info_key" => $ai_title_str, "info_value" => $ai_value_str, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_product_additional_information", $ai_data);

                }

                $response = array("status" => "Y", "message" => "New product successfully created.", "product_id" => $product_id);
            }
            else
            {
                $response = array("status" => "N", "message" => "Internal server error.");
            }

        }
        else
        {
            $response = array("status" => "N", "message" => "Product creation failed! Product slug already exist.");
        }

        return $response;


    }

    public function orders()
    {
    	$user = $this->session->userdata('userdata');
		if ($user == null) {
			$this->index();
			return;
		}
		else{
			$page_data['vendorName'] = $user['name'];
		}
		$page_data['title'] = 'Orders';
		$page_data['filter_data'] = null;
        $page_data['export_flag'] = null;
        $order_list = $this->get_order_list($user['id']);
        $page_data['order_list'] = $order_list;

		$this->load->view('vendor/dashboard', $page_data);	
    }

    function get_order_list($vendor_id)
    {

        /*"filter" => true, "search-type" => $search_type, "order-status" => $order_status, 'custom-date' => $custom_date*/

        $order = array();

        $query = $this->db->query("SELECT FM_order.* FROM FM_order_details 
			INNER JOIN FM_product ON FM_product.id = FM_order_details.product_id 
			INNER JOIN FM_order ON FM_order_details.order_id = FM_order.id
			WHERE FM_product.vendor_id = '$vendor_id'");

        /*echo $this->db->last_query();
        exit;*/

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $order_row)
            {
                $customer_id = $order_row->customer_id;
                $address_details = $this->get_address_details_by_id($order_row->address_id);
                if($order_row->promo_code_id == NULL || $order_row->promo_code_id == 0)
                {
                    $promo_code_details = array();
                    
                }
                else
                {
                    $promo_code_details = $this->get_promo_code_details_by_id($order_row->promo_code_id);
                   
                }
                $time_slot_details = $this->get_delivery_time_slot_detail_by_id($order_row->delivery_time_slot);
                // get order product details

                $product_details = $this->get_product_details_order_id($order_row->id);

                $customer_details = $this->user_details_by_id($customer_id);



                $order[] = array("id" => $order_row->id, "order_no" => $order_row->order_no, "customer_details" => $customer_details,  "address_details" => $address_details, "total_price" => $order_row->total_price, "delivery_charge" => $order_row->delivery_charge, "discount" => $order_row->discount, "order_total" => $order_row->order_total, "promo_code_details" => $promo_code_details, "payment_method" => $order_row->payment_method, "transaction_id" => $order_row->transaction_id, "delivery_date" => $order_row->delivery_date, "time_slot_details" => $time_slot_details, "notes" => $order_row->notes, "invoice" => FRONT_URL.$order_row->invoice, "product_details" => $product_details, "status" => $order_row->status, "created_date" => $order_row->created_date);
            }
        }

        return $order;

    }

    function check_slug_exist($slug, $product_id = 0)
    {
        $this->db->select("id");
        $this->db->from("FM_product");
        $this->db->where("slug", $slug);
        $this->db->where("status !=", "D");
        if($product_id > 0)
        {
            $this->db->where("id !=", $product_id);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            // exist / not avilable
            $status = "Y";
        }
        else
        {
            // avilable
            $status = "N";
        }
        return $status;
    }

    function get_product_list($vendor_id = 0)
    {
    	$products = array();
    	$this->db->select("id");
    	$this->db->from("FM_product");
    	$this->db->where("status !=", "D");
    	$this->db->where('vendor_id', $vendor_id);
    	$this->db->order_by("id", "DESC");

    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		foreach($query->result() as $product_row)
    		{
    			$product_details = $this->get_product_details_by_id($product_row->id);
    			if(count($product_details) > 0)
    			{
    				$products[] = $product_details;
    			}

    		}
    	}

    	return $products;
    }

    function get_product_details_by_id($product_id = 0)
    {
    	$details = array();


    	$products = array();
    	$this->db->select("*");
    	$this->db->from("FM_product");
    	$this->db->where("status !=", "D");
    	$this->db->where("id", $product_id);

    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
	    		$product_row = $query->row();
                $cat_id = $this->get_selected_catId($product_row->id);
                $crop_id = $this->get_selected_cropId($product_row->id);
                foreach($cat_id as $k=> $val){
    			 $category_details[] = $this->get_category_short_details_by_id($val['category_id']);
                }
                foreach($crop_id as $key => $value){
                    $crop_details [] = $this->get_crop_short_details_by_id($value['crop_id']);
                }
    			$variation_list = $this->get_variation_list_by_product_id($product_row->id);

                $category_history = $this->get_parent_list_by_category_id($product_row->category_id);

    			$additional_information_list = $this->get_product_additional_information_list($product_row->id);

    			$image_list = $this->get_product_image_by_product_id($product_row->id);

    			$details = array("id" => $product_row->id, "name" => $product_row->title, "SKU" => $product_row->SKU, "image_list" => $image_list, "category_details" => $category_details, "crop_details" => $crop_details, "category_history" => $category_history, "slug" => $product_row->slug, "short_description" => $product_row->short_description, "description" => $product_row->description, "status" => $product_row->status, "created_date" => $product_row->created_date, "updated_date" => $product_row->updated_date, "variation_list" => $variation_list, "additional_information_list" => $additional_information_list, "ord_by" => $product_row->ord_by, "is_latest" => $product_row->is_latest);

    		
    	}

    	

    	return $details;
    }




    function get_variation_list_by_product_id($product_id = 0)
    {
    	$variation_list = array();

    	$this->db->select("*");
    	$this->db->from("FM_product_variation");
    	$this->db->where("product_id", $product_id);
    	$this->db->where("status !=", "D");
    	$this->db->order_by("ord_by", "ASC");
    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		foreach($query->result() as $var_row)
    		{
    			if($var_row->discount > 0)
    			{
    				$discount_amount = round($var_row->price * $var_row->discount / 100);   
    				$discount_amount = number_format($discount_amount, 2); 				
    			}
    			else
    			{
    				$discount_amount = number_format(0, 2);
    			}

    			$sale_price = $var_row->price - $discount_amount;
    			$sale_price = number_format($sale_price, 2); 

    			$variation_list[] = array("id" => $var_row->id, "title" => $var_row->title, "price" => $var_row->price, "state_id" => $var_row->state_id, "discount_percent" => $var_row->discount, "discount_amount" => $discount_amount, "sale_price" => $sale_price, "created_date" => $var_row->created_date, "updated_date" => $var_row->updated_date, "status" => $var_row->status, "order" => $var_row->ord_by);
    		}
    	}

    	return $variation_list;
    }

    function get_product_additional_information_list($product_id = 0)
    {
    	$additional_information = array();

    	$this->db->select("*");
    	$this->db->from("FM_product_additional_information");
    	$this->db->where("product_id", $product_id);

    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		foreach($query->result() as $row)
    		{
    			$additional_information[] = array("id" => $row->id, "info_key" => $row->info_key, "info_value" => $row->info_value);
    		}
    	}

    	return $additional_information;
    }

    function get_product_image_by_product_id($product_id = 0)
    {
    	$list = array();

    	$this->db->select("*");
    	$this->db->from("FM_product_image");
    	$this->db->where("product_id", $product_id);
    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		foreach($query->result() as $row)
    		{
    			$list[] = array("id" => $row->id, "image" => FRONT_URL.$row->image);
    		}
    	}

    	return $list;
    }
    function get_selected_catId($product_id = 0){
        $details = array();
        $this->db->select("category_id");
        $this->db->from("FM_category_mapping");
        $this->db->where("product_id", $product_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $details [] = array("category_id" => $rows->category_id);
            }
        }
        return $details;
    }

    function get_selected_cropId($product_id = 0){
        $details = array();
        $this->db->select("crop_id");
        $this->db->from("FM_crop_mapping");
        $this->db->where("product_id", $product_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $details [] = array("crop_id" => $rows->crop_id);
            }
        }
        return $details;
    }

    function get_crop_short_details_by_id($crop_id = 0)
    {
        $response = array("id" => "0", "title" => "Parent");

        $this->db->select("id, title");
        $this->db->from("FM_crop");
        $this->db->where("id", $crop_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $response = array("id" => $row->id, "title" => $row->title);
        }

        return $response;
    }

    function get_parent_list_by_category_id($cate_id = 0)
    {      

        $this->db->select("id, title, parent_id");
        $this->db->from("FM_product_category");
        $this->db->where("id", $cate_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {           
            $row = $query->row();   


            if($row->parent_id != 0)
            {
                $this->db->select("id, title, parent_id");
                $this->db->from("FM_product_category");
                $this->db->where("id", $row->parent_id);
                $query1 = $this->db->get();
                
                if($query1->num_rows() > 0)
                {
                    $row1 = $query1->row();
                    $response[] = array("id" => $row1->id, "title" => $row1->title);
                }
                else
                {
                    $response[] = array("id" => "0", "title" => "Parent");
                }

                $response[] = array("id" => $row->id, "title" => $row->title);

                
            }
            else
            {
                $response[] = array("id" => $row->id, "title" => $row->title);
                $response[] = array("id" => "0", "title" => "Parent");
                
            }
            
        }
        else
        {
            $response[] = array("id" => "0", "title" => "Parent");
            $response[] = array("id" => "0", "title" => "Parent");
        }

        return $response;
    }

    public function editProduct($id = 0)
    {
    	$id = $this->uri->segment(3);
    	$crop_id = $cat_id = [];
    	$user = $this->session->userdata('userdata');
		if ($user == null) {
			$this->index();
			return;
		}
		else{
			$data['vendorName'] = $user['name'];
		}
		$page_data['title'] = 'Edit Product';
		// get all parent_category
        $parent_category = $this->get_category_list_by_parent_id(0);
        $page_data['main_parent'] = $parent_category;

        $product_meta = $this->meta_data_model->get_product_meta_data_by_id($id);
        $page_data['product_meta'] = $product_meta;

        // product details
        $product_details = $this->get_product_details_by_id($id);
        $filter_data = array("status" => 'all');
        $crop_list = $this->crop_list($filter_data);
        $selected_category_id = $this->get_selected_catId($id);
        $state_list = $this->get_state_list();
        foreach($selected_category_id as $k => $val){
            $cat_id [] = $val['category_id'];
        }

        $selected_crop_id = $this->get_selected_cropId($id);
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
		$this->load->view('vendor/dashboard', $page_data);
    }
    function get_product_meta_data_by_id($product_id = 0)
    {
        $details = array("meta_title" => "", "meta_description" => "", "meta_keyword" => "");
        $this->db->select("*");
        $this->db->from("FM_product_meta_data");
        $this->db->where("product_id", $product_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $details = array("meta_title" => $row->meta_title, "meta_description" => $row->meta_description, "meta_keyword" => $row->meta_keyword);

        }
        return $details;
    }

    public function editSubmit()
    {
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

            
            
            
            $update_data = $this->update_product($form_data);
            if($update_data['status'] == "Y")
            {
                
                 $product_id = $id;
                // add meta data
                

                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('vendors/products'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('vendors/products'));
            }
        }
        else
        {
            redirect(base_url('vendors/products'));
        }
    }

    function update_product($data)
    {
        $id = $data['id'];
    	$category_id = $data['category_id'];
        $crop_id = $data['crop_id'];
    	$title = $data['title'];
    	$slug = $data['slug'];
    	$description = $data['description'];
    	$short_description = $data['short_description'];
    	$status = $data['status'];
    	$image = $data['image'];
        $variation_id = $data['variation_id'];
        $variation_type = $data['variation_type'];
    	$variation_title = $data['variation_title'];
    	$price = $data['price'];
    	$discount = $data['discount'];
        $state_id = $data['state_id'];
    	$ai_title = $data['ai_title'];
    	$ai_value = $data['ai_value'];

    	// check slug
    	$slug_status = $this->check_slug_exist($slug, $id);

    	if($slug_status == 'N')
    	{
    		$product_data = array("slug" => $slug, "title" => $title, "short_description" => $short_description, "description" => $description, "status" => $status, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
    		$this->db->update("FM_product", $product_data);
            $product_id = $id;
    		if($product_id > 0)
    		{
                if($image != '')
                {
                    // delete image
                    $this->db->where("product_id", $product_id);
                    $this->db->delete("FM_product_image");

                    // insert image
                    $img_insert_data = array("product_id" => $product_id, "image" => $image, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_product_image", $img_insert_data);
                }  			


                $this->db->where("product_id", $product_id);
                $this->db->delete("FM_crop_mapping");
                $this->db->where("product_id", $product_id);
                $this->db->delete("FM_category_mapping");

                $crop_count = count($crop_id);
                for($cr=0;$cr < $crop_count; $cr++){
                    $var_crop_id = $crop_id[$cr];
                    $var_insert_crop_data = array("product_id" => $product_id, "crop_id" => $var_crop_id, "created_date" => date("Y-m-d H:i:s"),"updated_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_crop_mapping", $var_insert_crop_data);
                }
                
                $category_count = count($category_id);
                for($cat=0;$cat < $category_count; $cat++){
                    $var_cat_id = $category_id[$cat];
                    $var_insert_cat_data = array("product_id" => $product_id, "category_id" => $var_cat_id, "created_date" => date("Y-m-d H:i:s"),"updated_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_category_mapping", $var_insert_cat_data);
                }
                



    			$variation_count = count($variation_title);

                $old_var = array();
	    		for($i = 0; $i < $variation_count; $i++)
	    		{
	    			$var_id = $variation_id[$i];
                    $var_type = $variation_type[$i];
                    $var_title = $variation_title[$i];
	    			$var_price = $price[$i];
	    			$var_discount = $discount[$i];
                    $var_state_id = $state_id[$i];

                    if($var_type == 'old')
                    {
                        
                        $var_update_data = array("title" => $var_title, "price" => $var_price, "discount" =>  $var_discount, "state_id" =>  $var_state_id, "updated_date" => date("Y-m-d H:i:s"), "status" => "Y", "ord_by" => 0);
                        $this->db->where("id", $var_id);
                        $this->db->update("FM_product_variation", $var_update_data);
                        $old_var[] = $var_id;
                    }
                    else if($var_type == 'new')
                    {
                       
                        $var_insert_data = array("product_id" => $product_id, "title" => $var_title, "price" => $var_price, "discount" =>  $var_discount, "state_id" =>  $var_state_id, "created_date" => date("Y-m-d H:i:s"), "status" => "Y", "ord_by" => 0);
                        $this->db->insert("FM_product_variation", $var_insert_data);
                        $var_insert_id = $this->db->insert_id();
                        $old_var[] = $var_insert_id;
                    }
                    else
                    {
                        
                        // do nothing
                    }

                    if(count($old_var) > 0)
                    {
                        $this->db->where("product_id", $product_id);
                        $this->db->where_not_in("id", $old_var);
                        $var_update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
                        $this->db->update("FM_product_variation", $var_update_data);
                    }
                    else
                    {
                        $this->db->where("product_id", $product_id);
                        $var_update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
                        $this->db->update("FM_product_variation", $var_update_data);
                    }

	    			
	    		}

                // delete ai
                $this->db->where("product_id", $product_id);
                $this->db->delete("FM_product_additional_information");

	    		$ai_count = count($ai_title);
	    		for($ai = 0; $ai < $ai_count; $ai++)
	    		{
	    			$ai_title_str = $ai_title[$ai];
	    			$ai_value_str = $ai_value[$ai];

	    			$ai_data = array("product_id" => $product_id, "info_key" => $ai_title_str, "info_value" => $ai_value_str, "created_date" => date("Y-m-d H:i:s"));
	    			$this->db->insert("FM_product_additional_information", $ai_data);
	    		}

	    		$response = array("status" => "Y", "message" => "Product successfully updated.");
    		}
    		else
    		{
    			$response = array("status" => "N", "message" => "Internal server error.");
    		}

    	}
    	else
    	{
    		$response = array("status" => "N", "message" => "Product update failed! Product slug already exist.");
    	}

    	return $response;


    }

    public function deleteProduct()
    {
    	$id = $this->uri->segment(3);
    	$delete_product = $this->delete_product_by_id($id);
    	redirect(base_url('vendors/products'));
    }

    function delete_product_by_id($id = 0)
    {
        $this->db->select("id");
        $this->db->from("FM_product");
        $this->db->where("id", $id);
        $this->db->where("status !=", "D");
        $check_query = $this->db->get();
        if($check_query->num_rows() > 0)
        {
            $update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_product", $update_data);
            
            $response = array("status" => "Y", "message" => "Product successfully deleted.");
        }
        else
        {
            $response = array("status" => "N", "message" => "Product already deleted or not found.");
        }
        $update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
        $this->db->where("product_id", $id);
        $this->db->update("FM_product_variation", $update_data);

        return $response;

    }

    function get_product_details_order_id($order_id = 0)
    {
        $order_details = array();
        $this->db->select("*");
        $this->db->from("FM_order_details");
        $this->db->where("order_id", $order_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $product_row)
            {
                $variation_details = $this->product_model->get_veriation_full_details_by_id($product_row->variation_id);
                $order_details[] = array("variation_details" => $variation_details, "unit_price" => $product_row->unit_price, "quantity" => $product_row->quantity, "total_price" => $product_row->total_price);
            }
        }

        return $order_details;
    }

    function get_address_details_by_id($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_customer_address");
        $this->db->where("id", $id);
        //$this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            { 
                $state_name = $this->common_model->get_state_name_by_id($row->state_id);
                $city_name  = $this->common_model->get_city_name_by_id($row->city_id);
                $details    = array(
                    "id" => $row->id,
                    "name" => $row->name,
                    "phone" => $row->phone,
                    "address_1" => $row->address_1,
                    "address_2" => $row->address_2,
                    "landmark" => $row->landmark,
                    "state_id" => $row->state_id,
                    "state_name" => $state_name,
                    "city_id" => $row->city_id,
                    "city_name" => $city_name,
                    "zip_code" => $row->zip_code
                );
            }
        }
        return $details;
    }

    function user_details_by_id($user_id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where("id", $user_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row     = $query->row();
            $details = array(
                "id" => $row->id,
                "first_name" => $row->first_name,
                "last_name" => $row->last_name,
                "full_name" => trim($row->first_name . " " . $row->last_name),
                "email" => $row->email, 
                "phone" => $row->phone,
                "profile_image" => FRONT_URL . $row->profile_image,
                "status" => $row->status,
                "registration_date" => $row->created_date
            );
        }
        return $details;
    }

    function get_delivery_time_slot_detail_by_id($id = 0)
    {
        $time_slot = array();
        $current_hour = date("H");

        $this->db->select("*");
        $this->db->from("FM_delivery_time_slot");
        $this->db->where("id", $id);
        $query = $this->db->get();       

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $start_time = $rows->start_time;
                $end_time = $rows->end_time;
                if($start_time == 12)
                {
                    $start_str = $start_time ." PM";
                }
                else if($start_time > 12)
                {
                    $start_str = $start_time - 12 ." PM";
                }
                else
                {
                    $start_str = $start_time ." AM";
                }

                if($end_time == 12)
                {
                    $end_str = $end_time ." PM";
                }
                else if($end_time > 12)
                {
                    $end_str = $end_time - 12 ." PM";
                }
                else
                {
                    $end_str = $end_time ." AM";
                }
                $time_slot = array("id" => $rows->id, "time_slot" => $start_str." - ".$end_str);
            }
        }

        return $time_slot;

    }

    function get_promo_code_details_by_id($id = 0)
    {
        $promo_details = array();

        $this->db->select("*");
        $this->db->from("FM_promo_code");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $promo_details = $query->row_array();
        }

        return $promo_details;

    }

    public function orderDetails()
    {
    	$id = $this->uri->segment(3);

    	$user = $this->session->userdata('userdata');
		if ($user == null) {
			$this->index();
			return;
		}
		else{
			$page_data['vendorName'] = $user['name'];
		}

		$order_no = $this->get_order_no_by_order_id($id);

        if($order_no == '')
        {
            redirect(base_url('vendors/orders'));
        }
        else{
            $order_details = $this->order_details_by_no($order_no);
        }        
        

        
        $page_data['order_details'] = $order_details;
		

		$page_data['title'] = 'Order Details';
		$this->load->view('vendor/dashboard', $page_data);
    }

    function get_order_no_by_order_id($id = 0)
    {
        $order_no = "";
        $this->db->select("order_no");
        $this->db->from("FM_order");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $order_no = $row->order_no;

        }
        return $order_no;
    }

    function order_details_by_no($order_no)
    {      

        $order = array();
       

        $this->db->select("*");
        $this->db->from("FM_order");       
        $this->db->where("order_no", $order_no);        
        $query = $this->db->get();


        if($query->num_rows() > 0)
        {
            foreach($query->result() as $order_row)
            {
                $customer_id = $order_row->customer_id;
                $address_details = $this->user_model->get_address_details_by_id($order_row->address_id);
                if($order_row->promo_code_id == NULL || $order_row->promo_code_id == 0)
                {
                    $promo_code_details = array();
                    
                }
                else
                {
                    $promo_code_details = $this->get_promo_code_details_by_id($order_row->promo_code_id);
                   
                }
                $time_slot_details = $this->get_delivery_time_slot_detail_by_id($order_row->delivery_time_slot);
                // get order product details

                $product_details = $this->get_product_details_order_id($order_row->id);

                $customer_details = $this->user_details_by_id($customer_id);



                $order = array("id" => $order_row->id, "order_no" => $order_row->order_no, "customer_details" => $customer_details,  "address_details" => $address_details, "total_price" => $order_row->total_price, "delivery_charge" => $order_row->delivery_charge, "discount" => $order_row->discount, "order_total" => $order_row->order_total, "promo_code_details" => $promo_code_details, "payment_method" => $order_row->payment_method, "transaction_id" => $order_row->transaction_id, "delivery_date" => $order_row->delivery_date, "time_slot_details" => $time_slot_details, "notes" => $order_row->notes, "invoice" => $order_row->invoice, "product_details" => $product_details, "status" => $order_row->status, "created_date" => $order_row->created_date);
            }
        }

        return $order;
    }

    public function profile()
    {
    	$user = $this->session->userdata('userdata');
		if ($user == null) {
			$this->index();
			return;
		}
		else{
			$page_data['vendorName'] = $user['name'];
		}
		$page_data['title'] = 'Profile';
		$page_data['pincodes'] = $this->db->select('pin_code')->from('FM_pin_code_lookup')->where('is_deleted', 'N')->get()->result();

		$page_data['userdata'] = $this->db->from('FM_vendor')->where('hash_id', $user['id'])->get()->row();

		if (!is_object($page_data['userdata'])) {
			$this->index();
			return;
		}

		$basepath_image_url = $this->db->select("content")
									   ->from("FM_preferences")
									   ->where("name","base_image_url")
									   ->get()->result()[0]->content;

		$page_data['userdata']->image = $basepath_image_url.$page_data['userdata']->image;
		$page_data['front_url'] = $basepath_image_url;

		$this->load->view('vendor/dashboard', $page_data);
    }

    public function updateVendor()
    {
    	$data = [];

    	$hash_id = $this->input->post('id');
    	$data['name'] = $this->input->post('name');
    	$data['address'] = $this->input->post('address');
    	$data['email'] = $this->input->post('email');
    	$data['phone'] = $this->input->post('phone');
    	$data['shop_name'] = $this->input->post('shop');
    	$service_area = $this->input->post('serviceArea');
    	$data['service_area'] = json_encode($service_area);
    	$bankingDetails = [];
    	$bankingDetails['account_no'] = $this->input->post('account-no');
    	$bankingDetails['ifsc_no'] = $this->input->post('ifsc_no');
    	$bankingDetails['account_holder_name'] = $this->input->post('account_holder_name');
    	$data['banking_details'] = json_encode($bankingDetails);

    	if(!empty($_FILES['profileimage']['name']))
        {
        	$upload_dir = 'media/uploads/vendor/profile_image/';
            $rand_name = time();
            $upload_file = $upload_dir.$rand_name.basename($_FILES['profileimage']['name']);
            $upload_file = str_replace(" ","-",$upload_file);
            $actual_path = 'uploads/vendor/profile_image/'.$rand_name.basename($_FILES['profileimage']['name']);
            $actual_path = str_replace(" ","-",$actual_path);
            if (move_uploaded_file($_FILES['profileimage']['tmp_name'], $upload_file))
            {
                $data['image'] = $actual_path;
            }
        }

        $data['updated_date'] = date('Y-m-d');

        $this->db->set($data);
        $this->db->where('hash_id', $hash_id);
        $this->db->update('FM_vendor');


        $this->profile();
    }

    public function logout()
    {
    	$this->session->unset_userdata('userdata');
    	$this->index();
		return;
    }
    
}