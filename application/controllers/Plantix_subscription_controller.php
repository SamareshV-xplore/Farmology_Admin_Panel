<?php defined("BASEPATH") or exit("No direct script access allowed");

class Plantix_subscription_controller extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model("plantix_subscription_model");
    }

    private function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
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

        $header_data['title'] = "Plantix Subscription Plans";
        $left_data['navigation'] = "plantix_subscription_plans";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;
            $page_data['paid_subscription_plans'] = $this->plantix_subscription_model->get_paid_subscription_plans_list();
        }
        else
        {
            redirect(base_url(''));
        }

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('plantix_subscription_plans', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function edit_existing_plan()
    {
        $missing_keys = $data = [];

        if (!empty($this->input->post("plan_id")))
        {
            $plan_id = $this->input->post("plan_id");
        }
        else
        {
            $missing_keys[] = "plan_id";
        }

        if (!empty($this->input->post("name")))
        {
            $data["name"] = $this->input->post("name");
        }
        else
        {
            $missing_keys[] = "name";
        }

        if (!empty($this->input->post("plan_description")))
        {
            $data["description"] = $this->input->post("plan_description");
        }
        else
        {
            $missing_keys[] = "plan_description";
        }

        if (!empty($this->input->post("original_price")))
        {
            $data["original_price"] = $this->input->post("original_price");
        }
        else
        {
            $missing_keys[] = "original_price";
        }

        if (!empty($this->input->post("discounted_price")))
        {
            $data["discounted_price"] = $this->input->post("discounted_price");
        }

        if (!empty($missing_keys))
        {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtrim($missing_string, ", ");
            $response = ["success" => false, "message" => $missing_string." not given!"];
        }
        else
        {
            $is_updated = $this->plantix_subscription_model->update_paid_subscription_plan($plan_id, $data);
            if ($is_updated)
            {
                $response = ["success" => true, "message" => "Saved Successfully"];
            }
            else
            {
                $response = ["success" => false, "message" => "Failed to edit subscription plan"];
            }
        }

        $this->response($response, 200);
    }
}