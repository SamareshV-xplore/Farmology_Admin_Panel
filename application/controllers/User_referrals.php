<?php defined("BASEPATH") OR exit("No direct script access allowed");

class User_referrals extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_referrals_model");
    }

    public function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function response($data, $status)
    {
        return $this->output->set_content_header("application/json")
                            ->set_header_status($status)
                            ->set_output(json_encode($data));
    }

    public function index()
    {
        if (!empty($_GET["code"]))
        {
            $referral_code = $_GET["code"];
            $data = [
                "hash_id" => $this->GUID(),
                "user_ip_address" => $this->input->ip_address(),
                "user_agent" => $this->input->user_agent(),
                "user_referral_code" => $_GET["code"] 
            ];
            
            $is_added = $this->user_referrals_model->add_user_referrals_data($data);
            if ($is_added)
            {
                // redirect(base_url("assets/new_farmology_apk/Farmology.apk"));
                $app_link = $this->get_farmology_app_link();
                if (!empty($app_link))
                {
                    redirect($app_link);
                }
                else
                {
                    echo "<b>Note:</b> Something Went Wrong! Please Try Again Later.";
                }
            }
            else
            {
                echo "<b>Note:</b> Something Went Wrong! Please Try Again Later.";
            }
        }
        else
        {
            echo "<b>Note:</b> This Referral Link is Invalid!";
        }
    }

    public function get_farmology_app_link()
    {
        $app_link_details = $this->db->get_where("FM_preferences", ["name" => "google_play_store_farmology_app_link"])->row();
        return (!empty($app_link_details->content)) ? $app_link_details->content : NULL;
    }

    public function download_farmology_apk()
    {
        $this->load->helper("download");
        $farmology_apk_file = "assets/nwe_farmology_apk/Farmology.apk";
        force_download($farmology_apk_file, NULL);
    }

}

?>