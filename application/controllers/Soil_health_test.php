<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once (APPPATH."controllers/Push_notification.php");

class Soil_health_test extends Push_notification {

    public function __construct ()
    {
        parent::__construct();
        $this->load->model("soil_health_test_model");
        $this->load->model("notification_model");
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

        $header_data['title'] = "Soil Health Test Requests";
        $left_data['navigation'] = "soil-health-test";

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
        $page_data["soil_health_test_requests"] = $this->soil_health_test_model->get_all_soil_health_test_requests();

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('soil_health_test_requests', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }
    
    public function change_sample_received_status ()
    {
        if (isset($_POST["request_id"]) && isset($_POST["sample_received"]))
        {
            $hash_id = $_POST["request_id"];
            $sample_received = $_POST["sample_received"];
            $this->soil_health_test_model->change_sample_received_status($hash_id, $sample_received);
            $isUpdated = $this->db->affected_rows();
            if ($isUpdated)
            {
                if ($sample_received)
                {
                    $notification_sending_status = $this->send_sample_received_notification($hash_id);
                }
                $response = ["success" => true, "message" => "sample received status updated successfully."];
            }
            else
            {
                $response = ["success" => false, "message" => "failed to update sample received status!"];
            }
        }
        else
        {
            $response = ["success" => false, "message" => "request_id or sample_received parameter is not given!"];
        }
        
        $this->response($response, 200);
    }

    private function send_sample_received_notification ($request_id)
    {
        $request_details = $this->soil_health_test_model->get_soil_health_test_request_by_hash_id($request_id);
        $user_id = $request_details->user_id;
        $subject = "Soil Sample Received";
        $message = "We have successfully received the soil samples for the soil health test request #".$request_details->hash_id.".";
        $action = "farmology_home";
        $redirection_id = "2";
        $this->sendPushMessages($user_id, $message, $subject, $action, $redirection_id);
    }

    public function generate_soil_health_report ()
    {
        $report = [];
        $request_id = (!empty($_POST["request_id"])) ? $_POST["request_id"] : "";
        $soil_health_report_data = [];
        $recommended_products_data = [];

        if (!empty($_POST["nitrogen_value"]) && !empty($_POST["potassium_value"]) && !empty($_POST["phosphorus_value"]) && !empty($_POST["organic_carbon_value"]) && !empty($_POST["cation_exchange_value"]) && !empty($_POST["clay_content_value"]))
        {
            $report_id = $this->GUID();
            $soil_health_report_data["hash_id"] = $report_id;
            $report[] = [
                "name" => "nitrogen",
                "unit" => $_POST["nitrogen_unit"], 
                "value" => $_POST["nitrogen_value"],
                "ideal_value" => $_POST["nitrogen_ideal_value"],
                "rating" => $_POST["nitrogen_rating"], 
                "range" => $_POST["nitrogen_range"]
            ];

            $report[] = [
                "name" => "potassium",
                "unit" => $_POST["potassium_unit"], 
                "value" => $_POST["potassium_value"],
                "ideal_value" => $_POST["potassium_ideal_value"],
                "rating" => $_POST["potassium_rating"], 
                "range" => $_POST["potassium_range"]
            ];

            $report[] = [
                "name" => "phosphorus",
                "unit" => $_POST["phosphorus_unit"], 
                "value" => $_POST["phosphorus_value"],
                "ideal_value" => $_POST["phosphorus_ideal_value"],
                "rating" => $_POST["phosphorus_rating"],
                "range" => $_POST["phosphorus_range"]
            ];

            $report[] = [
                "name" => "organic carbon",
                "unit" => $_POST["organic_carbon_unit"], 
                "value" => $_POST["organic_carbon_value"],
                "ideal_value" => $_POST["organic_carbon_ideal_value"],
                "rating" => $_POST["organic_carbon_rating"],
                "range" => $_POST["organic_carbon_range"]
            ];

            $report[] = [
                "name" => "cation exchange",
                "unit" => $_POST["cation_exchange_unit"], 
                "value" => $_POST["cation_exchange_value"],
                "ideal_value" => $_POST["cation_exchange_ideal_value"],
                "rating" => $_POST["cation_exchange_rating"],
                "range" => $_POST["cation_exchange_range"]
            ];

            $report[] = [
                "name" => "clay content",
                "unit" => $_POST["clay_content_unit"], 
                "value" => $_POST["clay_content_value"],
                "ideal_value" => $_POST["clay_content_ideal_value"],
                "rating" => $_POST["clay_content_rating"],
                "range" => $_POST["clay_content_range"]
            ];
            $soil_health_report_data["report"] = json_encode($report);
            $soil_health_report_data["expert_advice"] = (!empty($_POST["expert_advice"])) ? $_POST["expert_advice"] : "";

            $is_report_added = $this->soil_health_test_model->add_soil_health_test_report($soil_health_report_data);
        }

        if (isset($_POST["suggested_products"]) && !empty($report_id))
        {
            $recommended_product_ids = json_decode($_POST["suggested_products"], TRUE);
            $recommended_products_data["hash_id"] = $this->GUID();
            $recommended_products_data["report_id"] = $report_id;
            $recommended_products_data["recommended_products"] = implode(",", $recommended_product_ids);

            $is_recommended_product_added = $this->soil_health_test_model->add_soil_health_test_recommended_product($recommended_products_data);

            $user_data = $this->soil_health_test_model->get_soil_health_report_user_data($request_id);
            $report_data = $report;
            $expert_advice = (!empty($_POST["expert_advice"])) ? $_POST["expert_advice"] : "";;
            $product_id_list = json_decode($_POST["suggested_products"], TRUE);
            $recommended_products = $this->get_recommended_products_list($product_id_list);
            $report_pdf = $this->soil_health_test_model->generate_soil_health_test_report_pdf($user_data, $report_data, $expert_advice, $recommended_products);

            $data = ["report_id" => $report_id, "report_pdf" => $report_pdf, "request_id" => $request_id];
        }

        if (!empty($is_report_added) && !empty($is_recommended_product_added))
        {
            $response = ["success" => true, "message" => "soil health test report and recommended products added successfully.", "data" => $data];
        }
        else
        {
            $response = ["success" => false, "message" => "failed to add soil health test report and recommended products!"];
        }
        
        $this->response($response, 200);
    }

    private function get_recommended_products_list($product_id_list)
    {
        $products_list = array();
        foreach($product_id_list as $product_id)
        {
            $products_list[] = $this->Experts_model->get_product_by_id($product_id);
        }
        return $products_list;
    }

    public function preview_generated_soil_health_report()
    {
        $page_data = [];

        if(isset($_POST["report_id"]))
        {
            $page_data["report_id"] = $_POST["report_id"];
        }

        if(isset($_POST["report_pdf"]))
        {
            $page_data["report_pdf"] = $_POST["report_pdf"];
        }

        if(isset($_POST["request_id"]))
        {
            $page_data["request_id"] = $_POST["request_id"];
        }

        $this->load->view('preview_soil_health_report', $page_data);
    }

    public function process_soil_health_report()
    {
        if(isset($_POST["report_id"]))
        {
            $report_id = $_POST["report_id"];
        }

        if(isset($_POST["report_pdf"]))
        {
            $report_pdf = $_POST["report_pdf"];
        }

        if(isset($_POST["request_id"]))
        {
            $request_id = $_POST["request_id"];
        }

        if(isset($_POST["status"]))
        {
            $save_report = $_POST["status"];
        }

        if($save_report=="TRUE")
        {
            $update_data = ["report_pdf" => $report_pdf];
            $is_updated = $this->soil_health_test_model->edit_soil_health_test_report($report_id, $update_data);

            $update_data2 = ["report_id" => $report_id];
            $this->db->set($update_data2)->where(["hash_id" => $request_id])->update("FM_soil_health_test_requests");
        }
        else
        {
            $this->db->where(["hash_id" => $report_id])->delete("FM_soil_health_test_reports");
            $this->db->where(["report_id" => $report_id])->delete("FM_soil_health_test_recommended_products");
            unlink(FILE_UPLOAD_BASE_PATH.$report_pdf);
        }

        header("Location: ".base_url("soil-health-test"));
        die();
    }

    public function test_pdf_structure ()
    {
        include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');
        $HTML = $this->load->view("pdf-template/soil_health_report", [], true);
        $PDF = new \Mpdf\Mpdf([
			'default_font' => 'eurostile',
			'mode' => 'utf-8',
			'format' => 'A4-L',
			'margin_left' => 0,
			'margin_right' => 0,
			'margin_top' => 0,
			'margin_bottom' => 0,
			'margin_header' => 0,
			'margin_footer' => 0
		]);
        $PDF->autoLangToFont = true;
        $PDF->autoScriptToLang = true;
		$PDF->SetDefaultBodyCSS("background", "#426078");
        $PDF->AddPage();
        $PDF->WriteHTML($HTML);

        $PDF->Output();
    }

    public function test_pdf_structure_in_html ()
    {
        $this->load->view("pdf-template/soil_health_report");
    }
}

?>