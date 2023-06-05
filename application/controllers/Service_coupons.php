<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Service_coupons extends CI_Controller {

    public function __construct ()
    {
        parent::__construct();
        $this->load->model("service_coupons_model");
    }

    private function response ($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_output(json_encode($data))
                            ->set_status_header($status);
    }

    private function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function index ()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Service Coupons";
        $left_data['navigation'] = "service-coupons";

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

        $page_data["service_coupons_list"] = $this->service_coupons_model->get_service_coupons_list();

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('service_coupons_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function add_service_coupon()
    {
        $data = $_POST;

        if (empty($data["maximum_usage_limit"]))
        {
            $data["maximum_usage_limit"] = NULL;
        }

        $data["hash_id"] = $this->GUID();
        $is_added = $this->service_coupons_model->add_service_coupon($data);
        if ($is_added)
        {
            $response = ["success" => true, "message" => "New service coupon created successfully."];
        }
        else
        {
            $response = ["success" => false, "message" => "Failed to create new service coupon!"];
        }
        $this->response($response, 200);
    }

    public function delete_service_coupon()
    {
        if (!empty($_POST["hash_id"]))
        {
            $this->service_coupons_model->delete_service_coupon($_POST["hash_id"]);
            $response = ["success" => true, "message" => "service coupon deleted successfully."];
        }
        else
        {
            $response = ["success" => false, "message" => "service coupon id is not given!"];
        }
        $this->response($response, 200);
    }
}

?>