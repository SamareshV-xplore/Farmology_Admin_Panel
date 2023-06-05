<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Plantix extends CI_Controller {

    public function __construct ()
    {
        parent::__construct();
        $this->load->model("plantix_model");
        $this->load->model('Experts_model');
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
        $header_data['title'] = "Plantix Requests";
        $left_data['navigation'] = "plantix";
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
        $page_data["plant_diagnosis_requests"] = $this->plantix_model->get_all_plant_diagnosis_requests();
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('plantix_requests', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function product_recommendation ()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();
        $header_data['title'] = "Plantix Product Recommendation";
        $left_data['navigation'] = "plantix";
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
        $page_data['products'] = $this->Experts_model->getAllProducts();
        $page_data["plant_diagnosis_product_recommendations"] = $this->plantix_model->get_all_plant_diagnosis_product_recommendations();
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('plantix_product_recommendations', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function edit_product_recommendation ()
    {
        if (!empty($_POST["hash_id"]) && !empty($_POST["recommended_products"]))
        {
            $hash_id = $_POST["hash_id"];
            $recommended_products_array = json_decode($_POST["recommended_products"], TRUE);
            $recommended_products = implode(",", $recommended_products_array);
            $isAdded = $this->plantix_model->edit_plant_diagnosis_product_recommendations($hash_id, $recommended_products);
            if ($isAdded)
            {
                $response = ["success" => true, "message" => "product recommendation added successfully."];
            }
            else
            {
                $response = ["success" => false, "message" => "failed to add product recommendation!"];
            }
        }
        else
        {
            $response = ["success" => false, "message" => "recommendation id or recommended products is not given!"];
        }
        $this->response($response, 200);
    }

    public function get_previously_suggested_products ()
    {
        if (!empty($_POST["product_ids"]))
        {
            $products = $this->plantix_model->get_products_by_product_ids($_POST["product_ids"]);
            if (!empty($products))
            {
                $response = ["success" => true, "message" => "previously suggested products fetched successfully.", "products" => $products];
            }
            else
            {
                $response = ["success" => false, "message" => "failed to fetch previously suggested products!"];
            }
        }
        else
        {
            $response = ["success" => flase, "message" => "product ids is not given!"];
        }
        $this->response($response, 200);
    }

}

?>