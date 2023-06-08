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

}