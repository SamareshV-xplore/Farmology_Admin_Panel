<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Code_generator extends CI_Controller {
	
    function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
        $this->load->model("code_generator_model");
	}

	public function index()
	{
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Code Generator";
        $left_data['navigation'] = "code_generator";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;
        }
        else
        {
            redirect(base_url(''));
        }

        $page_data['promo_codes'] = $this->code_generator_model->get_coupon_code_list();

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('code_generator', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
	}

    public function add_new_coupon ()
    {
        $response = ["success" => false, "message" => "Unable to Generate New Coupon"];
        $coupon_code = $this->generate_coupon_code();
        $coupon_details = [
            "promo_code" => $coupon_code,
            "title" => $_POST["title"],
            "start_date" => $_POST["applicable_from"],
            "end_date" => $_POST["applicable_till"],
            "discount_limit" => $_POST["discount"],
            "discount_type" => $_POST["discount_type"],
            "status" => $_POST["status"],
            "user_specific" => $_POST["specific_user"]
        ];

        if (isset($_POST["promo_code_desc"]) && $_POST["promo_code_desc"]!="")
        {
            $coupon_details["description"] = $_POST["promo_code_desc"];
        }

        if (isset($_POST["min_order_price"]) && $_POST["min_order_price"]!="")
        {
            $coupon_details["eligible_order_price"] = intval($_POST["min_order_price"]);
        }

        if (isset($_POST["max_discount_limit"]) && $_POST["max_discount_limit"]!="")
        {
            $coupon_details["max_limit"] = floatval($_POST["max_discount_limit"]);
        }

        if (isset($_POST["users_id_list"]))
        {
            $selected_user_id = explode(',', $_POST["users_id_list"]);
            $coupon_details["user_id"] = $selected_user_id[0];
        }

        $result = $this->code_generator_model->add_new_coupon_code($coupon_details);
        if ($result > 0)
        {
            $response["success"] = true;
            $response["message"] = "New Coupon Generated Successfully.";
        }

        $response["coupon_list"] = $this->code_generator_model->get_coupon_code_list();
        echo json_encode($response);
    }

    public function generate_coupon_code ()
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $coupon_code = "";
        for ($i=0; $i<10; $i++)
        {
            $coupon_code .= $chars[mt_rand(0, strlen($chars)-1)];
        }

        return $coupon_code;
    }

    public function get_coupon_details_by_coupon_code ($coupon_code)
    {
        $coupon_details = $this->code_generator_model->get_coupon_details($coupon_code);
        if ($coupon_details->user_id != 0 || $coupon_details->user_id != null)
        {

        }
        echo json_encode($coupon_details);
    }

    public function edit_coupon_details ()
    {
        $response = ["success" => false, "message" => "Unable to Edit Coupon Details!"];
        $coupon_code = $_POST["coupon_code"];
        $coupon_details = [
            "promo_code" => $coupon_code,
            "title" => $_POST["title"],
            "start_date" => $_POST["applicable_from"],
            "end_date" => $_POST["applicable_till"],
            "discount_limit" => $_POST["discount"],
            "discount_type" => $_POST["discount_type"],
            "status" => $_POST["status"],
            "user_specific" => $_POST["specific_user"],
            "updated_date" => date("Y-m-d h:i:s")
        ];

        if (isset($_POST["promo_code_desc"]) && $_POST["promo_code_desc"]!="")
        {
            $coupon_details["description"] = $_POST["promo_code_desc"];
        }

        if (isset($_POST["min_order_price"]) && $_POST["min_order_price"]!="")
        {
            $coupon_details["eligible_order_price"] = intval($_POST["min_order_price"]);
        }

        if (isset($_POST["max_discount_limit"]) && $_POST["max_discount_limit"]!="")
        {
            $coupon_details["max_limit"] = floatval($_POST["max_discount_limit"]);
        }

        if (isset($_POST["users_id_list"]))
        {
            $selected_user_id = explode(',', $_POST["users_id_list"]);
            $coupon_details["user_id"] = $selected_user_id[0];
        }

        $result = $this->code_generator_model->edit_existing_coupon_details($coupon_code, $coupon_details);
        if ($result > 0)
        {
            $response["success"] = true;
            $response["message"] = "Coupon Details Edited Successfully.";
        }

        $response["coupon_list"] = $this->code_generator_model->get_coupon_code_list();
        echo json_encode($response);
    }

    public function get_customers_list ()
    {
        $customers_list = [];
        $customers_list_data = $this->code_generator_model->get_customers_list(50);
        if (count($customers_list_data) > 0)
        {
            foreach ($customers_list_data as $customer_data)
            {
                $data["id"] = $customer_data->id;
                $data["name"] = $customer_data->first_name." ".$customer_data->last_name;
                $data["email"] = $customer_data->email;
                $data["phone"] = $customer_data->phone;
                $customers_list[] = $data;
            }
        }

        echo json_encode($customers_list);
    }

    public function get_customer_name ($id)
    {
        $customer = $this->code_generator_model->get_customer_details_by_id($id);
        $customer_name = $customer->first_name." ".$customer->last_name;
        echo $customer_name;
    }

    public function search_from_customers_list ($search_value)
    {
        $customers_list = [];
        $customers_list_data = $this->code_generator_model->search_customers($search_value);
        if (count($customers_list_data) > 0)
        {
            foreach ($customers_list_data as $customer_data)
            {
                $data["id"] = $customer_data->id;
                $data["name"] = $customer_data->first_name." ".$customer_data->last_name;
                $data["email"] = $customer_data->email;
                $data["phone"] = $customer_data->phone;
                $customers_list[] = $data;
            }
        }

        if (count($customers_list) > 0)
        {
            echo json_encode($customers_list);
        }
        else
        {
            echo "";
        }
    }
}