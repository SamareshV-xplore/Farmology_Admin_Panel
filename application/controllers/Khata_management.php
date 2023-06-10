<?php defined("BASEPATH") OR exit("No direct script acccess allowed.");

class Khata_management extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("khata_management_model");
    }

    private function response($data, $status) {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

    public function users_khata_list_view() {
        if ($this->common_model->user_login_check()) {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data["admin_details"] = $admin_details;
            $left_data["admin_details"] = $admin_details;

            $header_data["title"] = "Khata Management | Farmology Admin Panel";
            $left_data["navigation"] = "khata-management";
            $left_data["sub_navigation"] = "users-khata-list";
            $page_data["list_of_users_khata"] = $this->khata_management_model->get_list_of_users_khata();
            
            $this->load->view("includes/header_view", $header_data);
            $this->load->view("includes/left_view", $left_data);
            $this->load->view("khata_management/users_khata_list", $page_data);
            $this->load->view("includes/footer_view");
        }
        else {
            redirect(base_url());
        }
    }

    public function user_khata_details_view($user_id) {
        if ($this->common_model->user_login_check()) {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data["admin_details"] = $admin_details;
            $left_data["admin_details"] = $admin_details;

            $header_data["title"] = "Khata Management | Farmology Admin Panel";
            $left_data["navigation"] = "khata-management";
            $left_data["sub_navigation"] = "users-khata-list";
            $page_data["user_id"] = $user_id;
            $page_data["user_khata_summary"] = $this->khata_management_model->get_user_khata_summary($user_id);
            
            $this->load->view("includes/header_view", $header_data);
            $this->load->view("includes/left_view", $left_data);
            $this->load->view("khata_management/user_khata_details", $page_data);
            $this->load->view("includes/footer_view");
        }
        else {
            redirect(base_url());
        }
    }

    public function get_list_of_crop_sales($user_id) {
        $list_of_crop_sales = [];
        $crop_sales_data = $this->khata_management_model->get_list_of_crop_sales($user_id);
        foreach ($crop_sales_data as $i => $details) {
            $crop_sale_details = $details;
            $crop_sale_details->sale_value = "₹ ".number_format($crop_sale_details->sale_value, 0);
            $crop_sale_details->date = date("d/m/Y", strtotime($crop_sale_details->date));
            $list_of_crop_sales[] = $crop_sale_details;
        }
        $response = ["success" => true, "message" => "List of crop sales get successfully.", "data" => $list_of_crop_sales];
        $this->response($response, 200);
    }

    public function get_list_of_other_incomes($user_id) {
        $list_of_other_incomes = [];
        $other_incomes_data = $this->khata_management_model->get_list_of_other_incomes($user_id);
        foreach ($other_incomes_data as $i => $details) {
            $other_income_details = $details;
            $other_income_details->amount = "₹ ".number_format($other_income_details->amount, 0);
            $other_income_details->date = date("d/m/Y", strtotime($other_income_details->date));
            $list_of_other_incomes[] = $other_income_details;
        }
        $response = ["success" => true, "message" => "List of other incomes get successfully.", "data" => $list_of_other_incomes];
        $this->response($response, 200);
    }

    public function get_list_of_product_expenses($user_id) {
        $list_of_product_expenses = [];
        $product_expenses_data = $this->khata_management_model->get_list_of_product_expenses($user_id);
        foreach ($product_expenses_data as $i => $details) {
            $product_expenses_details = $details;
            $product_expenses_details->amount = "₹ ".number_format($product_expenses_details->amount, 0);
            $product_expenses_details->date = date("d/m/Y", strtotime($product_expenses_details->date));
            $list_of_product_expenses[] = $product_expenses_details;
        }
        $response = ["success" => true, "message" => "List of product expenses get successfully.", "data" => $list_of_product_expenses];
        $this->response($response, 200);
    }
    
    public function get_list_of_farming_expenses($user_id) {
        $list_of_farming_expenses = [];
        $farming_expenses_data = $this->khata_management_model->get_list_of_farming_expenses($user_id);
        foreach ($farming_expenses_data as $i => $details) {
            $farming_expenses_details = $details;
            $farming_expenses_details->amount = "₹ ".number_format($farming_expenses_details->amount, 0);
            $farming_expenses_details->date = date("d/m/Y", strtotime($farming_expenses_details->date));
            $list_of_farming_expenses[] = $farming_expenses_details;
        }
        $response = ["success" => true, "message" => "List of farming expenses get successfully.", "data" => $list_of_farming_expenses];
        $this->response($response, 200);
    }
    
    public function get_list_of_other_expenses($user_id) {
        $list_of_other_expenses = [];
        $other_expenses_data = $this->khata_management_model->get_list_of_other_expenses($user_id);
        foreach ($other_expenses_data as $i => $details) {
            $other_expenses_details = $details;
            $other_expenses_details->amount = "₹ ".number_format($other_expenses_details->amount, 0);
            $other_expenses_details->date = date("d/m/Y", strtotime($other_expenses_details->date));
            $list_of_other_expenses[] = $other_expenses_details;
        }
        $response = ["success" => true, "message" => "List of other expenses get successfully.", "data" => $list_of_other_expenses];
        $this->response($response, 200);
    }

}