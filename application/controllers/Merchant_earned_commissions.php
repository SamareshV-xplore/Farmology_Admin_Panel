<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Merchant_earned_commissions extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("merchant_earned_commissions_model");
    }

    public function index()
    {
        $header_data['title'] = "Merchant Earned Commissions";
        $left_data['navigation'] = "merchant_earned_commissions";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;
            $page_data["merchant_earned_commissions_list"] = $this->merchant_earned_commissions_model->get_merchant_earned_commissions_list();

            $this->load->view('includes/header_view', $header_data);
            $this->load->view('includes/left_view', $left_data);
            $this->load->view('merchant_earned_commissions_view', $page_data);
            $this->load->view('includes/footer_view');
        }
        else
        {
            redirect(base_url(''));
        }
    }

}

?>