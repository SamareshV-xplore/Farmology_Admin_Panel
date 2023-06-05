<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Expert_advice extends CI_Controller 
{	
    function __construct()
    {
        parent::__construct();
        $this->load->model('Experts_model');
        $this->farmonaut_api_uid = "lnR1AGuLgmPWk5ZzEtFS3FlGRj42";
    }

    function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

	public function index()
	{
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Expert's Advice";
        $left_data['navigation'] = "experts";
        // $left_data['sub_navigation'] = "communitie-list";

        // check login or not
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


        $filter_status = "all";
        $filter_category = "all";

        if(isset($_REQUEST['status']))
        {
            if($_REQUEST['status'] == 'N' || $_REQUEST['status'] == 'C')
            {
                $filter_status = $_REQUEST['status'];
            }
            
        }       
        
        $filter_data = array("status" => $filter_status);
        $page_data['filter_data'] = $filter_data;
        $page_data['products'] = $this->Experts_model->getAllProducts();

        // get request list
        $req_list = $this->Experts_model->get_farm_report_requests($filter_data);
        $page_data['request_list'] = $req_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('expert_advice_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
	}

    public function getListOfPossiblePestAndDiseases($report_id)
    {
        if (!empty($report_id)) {
            $farm_details = $this->Experts_model->get_farm_details_by_report_id($report_id);
            if (!empty($farm_details)) {

                $farm_coordinates = json_decode($farm_details->coordinates);
                if (!empty($farm_coordinates[0]->lat) && !empty($farm_coordinates[0]->lng)) {
                    $lat = $farm_coordinates[0]->lat;
                    $lng = $farm_coordinates[0]->lng;
                    $farm_temperature_and_humidity = $this->get_farm_temperature_and_humidity($lat, $lng);
                    
                    if ($farm_temperature_and_humidity["status"] == true) {
                        $farm_temperature = $farm_temperature_and_humidity["temperature"];
                        $farm_humidity = $farm_temperature_and_humidity["humidity"];
                    }
                }

                if (!empty($farm_details->crop_id) && !empty($farm_details->crop_sowing_date)) {
                    $id = $farm_details->crop_id;
                    $date = $farm_details->crop_sowing_date;
                    $crop_health_stage_details = $this->get_crop_health_stage_details($id, $date);
                }

                if (!empty($farm_temperature) && !empty($farm_humidity) && !empty($crop_health_stage_details->hash_id))
                {
                    $crop_stage_id = $crop_health_stage_details->hash_id;
                    $list_of_possible_diseases_and_pests = $this->get_list_of_possible_diseases_and_pests($crop_stage_id, $farm_temperature, $farm_humidity);
                }

                $response = [
                    "success" => true, 
                    "message" => "Farm details get successfully.", 
                    "farm_details" => $farm_details,
                    "farm_temperature" => (!empty($farm_temperature)) ? round($farm_temperature) : NULL,
                    "farm_humidity" => (!empty($farm_humidity)) ? round($farm_humidity) : NULL,
                    "crop_health_stage_details" => (!empty($crop_health_stage_details)) ? $crop_health_stage_details : NULL,
                    "list_of_possible_diseases_and_pests" => (!empty($list_of_possible_diseases_and_pests)) ? $list_of_possible_diseases_and_pests : NULL
                ];
            }
            else {
                $response = ["success" => false, "message" => "No farm details found!"];
            }
        }
        else {
            $response = ["success" => false, "message" => "No Report ID is given!"];
        }

        $this->response($response, 200);
    }
    
    public function get_farm_temperature_and_humidity($lat, $lng)
    {
        $response = ["status" => false];

        $url = "https://api.tomorrow.io/v4/timelines?location=$lat,$lng&fields=temperature,humidity&timesteps=current&units=metric&timezone=auto&apikey=W7W35Qfx7G5SegM1rr83rYMVhE9B6PBW";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded_API_response = json_decode($response);
        if (!empty($decoded_API_response->data->timelines[0]->intervals))
        {
            $weather_forecast_data = $decoded_API_response->data->timelines[0]->intervals[0];
            if (!empty($weather_forecast_data->values->humidity) && !empty($weather_forecast_data->values->temperature)) {
                $response = [
                    "status" => true,
                    "temperature" => $weather_forecast_data->values->temperature,
                    "humidity" => $weather_forecast_data->values->humidity
                ];
            }
        }

        return $response;
    }

    private function get_date_diff_in_days($date)
    {
        $diff_in_days = -1;
        $current_timestamp = time();
        $destination_timestamp = strtotime($date);

        $diff_in_timestamp = $current_timestamp - $destination_timestamp;
        $diff_in_days = round($diff_in_timestamp / (60 * 60 * 24));

        return $diff_in_days;
    }

    public function get_crop_health_stage_details($crop_id, $sowing_date)
    {
        $crop_health_stage_details = null;
        $condition = ["status" => "A", "crop_id" => $crop_id];
        $crop_health_stages = $this->db->get_where("FM_crop_health_per_stage", $condition)->result();
        if (!empty($crop_health_stages))
        {
            $day_diff = $this->get_date_diff_in_days($sowing_date);
            foreach ($crop_health_stages as $crop_health_stage)
            {
                $start = $end = null;
                $limit = explode(",", $crop_health_stage->sowing_day_count);
                $start = $limit[0];
                if (!empty($limit[1]))
                {
                    $end = $limit[1];
                }

                if (!empty($end))
                {
                    if ($start <= $day_diff && $end >= $day_diff)
                    {
                        $crop_health_stage_details = $crop_health_stage;
                        break;
                    }
                }
                else
                {
                    if ($start <= $day_diff)
                    {
                        $crop_health_stage_details = $crop_health_stage;
                        break;
                    }
                }
            }   
        }

        return $crop_health_stage_details;
    }

    public function get_list_of_possible_diseases_and_pests($crop_stage_id = NULL, $farm_field_temperature = NULL, $farm_field_humidity = NULL)
    {
        $response = ["pests" => [], "diseases" => []];

        if (isset($crop_stage_id) && isset($farm_field_temperature) && isset($farm_field_humidity)) {
            $SQL = "SELECT pest, disease FROM FM_pests_and_diseases_per_stage WHERE crop_stage_id = '{$crop_stage_id}' AND (minimum_temperature < {$farm_field_temperature} AND maximum_temperature > {$farm_field_temperature}) AND (minimum_humidity < {$farm_field_humidity} AND maximum_humidity > {$farm_field_humidity})";
            $result = $this->db->query($SQL);

            if (!empty($result)) {
                $pests = [];
                $diseases = [];
                foreach ($result as $i => $details) {
                    if (!empty($details->pest)) {
                        $pests[] = $details->pest;
                    }
                    if (!empty($details->disease)) {
                        $diseases[] = $deatils->disease;
                    }
                }

                $response["pests"] = $pests;
                $response["diseases"] = $diseases;
            }
        }

        return $response;
    }

    public function getWeatherForecast()
	{
        $url = "https://api.tomorrow.io/v4/timelines?location=22.5726,88.3639&fields=temperature,humidity&timesteps=current&units=metric&timezone=auto&apikey=W7W35Qfx7G5SegM1rr83rYMVhE9B6PBW";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded_response = json_decode($response);
        $this->response($decoded_response, 200);
	}

    public function getProductByProductId()
    {
        $product_id = $this->input->post('id');
        $product = $this->Experts_model->get_product_by_product_id($product_id);
        if (is_object($product)) {
            $product->image = FRONT_URL.$product->image;
            echo json_encode(array('success' => true, 'message' => 'Product Found', 'product' => $product));
        }
        else{
            echo json_encode(array('success' => true, 'message' => 'Product Not Found'));
        }

    }

    public function setRecommendedProducts()
    {
        $req_id = $this->input->post('id');
        $reportText = $this->input->post('report');
        $products = $this->input->post('products');
        $res = array('success' => true, 'message' => 'OK');
        
        
        echo json_encode($res);
    }

    function get_recommended_products_list($product_id_list)
    {
        $products_list = array();
        foreach($product_id_list as $product_id)
        {
            $products_list[] = $this->Experts_model->get_product_by_id($product_id);
        }
        return $products_list;
    }

    function stdObjectToArray($object)
    {
        return json_decode(json_encode($object), true);
    }

    function jsonStringToArray($json_string)
    {
        return json_decode(json_decode($json_string), true);
    }

    function jsonObjectToArray($json_object)
    {
        return json_decode($json_object, true);
    }

    function lastIndex($array)
    {
        return intval(count($array)-1);
    }

    function get_report_data($report_id)
    {
        $report_data_array = [];

        $report_details = $this->db->get_where("FM_report", ["id" => $report_id])->row();
        $ndvi = (!empty($report_details->ndvi)) ? $report_details->ndvi : NULL;
        $savi = (!empty($report_details->savi)) ? $report_details->savi : NULL;
        $ndwi = (!empty($report_details->ndwi)) ? $report_details->ndwi : NULL;
        $farm_id = (!empty($report_details->farm_id)) ? $report_details->farm_id : NULL;

        if (!empty($ndvi) && !empty($farm_id))
        {
            $report_images_details = $this->get_report_images($farm_id, "ndvi");
            if (!empty($report_images_details["reportImages"]))
            {
                $report_images = $report_images_details["reportImages"];
                $report_data = $this->render_report_images_data($report_images);
                $report_data["value"] = $ndvi;
                $report_data_array["ndvi"] = $report_data;
            }
        }

        if (!empty($savi) && !empty($farm_id))
        {
            $report_images_details = $this->get_report_images($farm_id, "savi");
            if (!empty($report_images_details["reportImages"]))
            {
                $report_images = $report_images_details["reportImages"];
                $report_data = $this->render_report_images_data($report_images);
                $report_data["value"] = $savi;
                $report_data_array["savi"] = $report_data;
            }
        }

        if (!empty($ndwi) && !empty($farm_id))
        {
            $report_images_details = $this->get_report_images($farm_id, "ndwi");
            if (!empty($report_images_details["reportImages"]))
            {
                $report_images = $report_images_details["reportImages"];
                $report_data = $this->render_report_images_data($report_images);
                $report_data["value"] = $ndwi;
                $report_data_array["ndwi"] = $report_data;
            }
        }

        return $report_data_array;
    }

    private function render_report_images_data($report_images)
    {
        $report_images_data = [];
        foreach ($report_images as $images_details)
        {
            $key = $images_details["label"];
            $value = $images_details["image"];
            $report_images_data[$key] = $value;
        }
        return $report_images_data;
    }
    
    function get_report_data_new($report_id)
    {
        $report_data_array = array();
        $condArr = array("status"=>"P", "id"=>$report_id);
        $report_data = $this->db->get_where("FM_report", $condArr)->result();
        if(count($report_data))
        {
            $kvi = json_decode($report_data[0]->kvi, true);
            $rvi = json_decode($report_data[0]->rvi, true);
            $sm = json_decode($report_data[0]->soil_moisture, true);

            // $report_data_array["kvi"] = $kvi;
            // $report_data_array["rvi"] = $rvi;
            // $report_data_array["sm"] = $sm;

            if(is_array($kvi))
            {
                if(!empty($kvi["data"]))
                {
                    $kvi_data = $kvi["data"];
                    $kvi_data_last_index = $this->lastIndex($kvi_data);
                    $report_data_array["kvi"] = $kvi_data[$kvi_data_last_index];
                }
            }

            if(is_array($rvi))
            {
                if(!empty($rvi["data"]))
                {
                    $rvi_data = $rvi["data"];
                    $rvi_data_last_index = $this->lastIndex($rvi_data);
                    $report_data_array["rvi"] = $rvi_data[$rvi_data_last_index];
                }
            }

            if(is_array($sm))
            {
                if(!empty($sm["data"]))
                {
                    $sm_data = $sm["data"];
                    $sm_data_last_index = $this->lastIndex($sm_data);
                    $report_data_array["sm"] = $sm_data[$sm_data_last_index];
                }
            }
        }
        
        return $report_data_array;
    }

    function add_recommended_products($report_id, $product_id_list)
    {
        // Delete Previously Recommended Products for this User Farm
        // =========================================================
        $user_id = $this->db->get_where("FM_report", ["id" => $report_id])->row()->user_id;
        $farm_id = $this->db->get_where("FM_report", ["id" => $report_id])->row()->farm_id;
        $condition = ["user_id" => $user_id, "farm_id" => $farm_id];
        $previous_report_id_list = $this->db->get_where("FM_report", $condition)->result();
        foreach ($previous_report_id_list as $report_data)
        {
            $this->db->set(["status" => "D"]);
            $this->db->where(["report_id" => $report_data->id, "status" => "A"]);
            $this->db->update("FM_recommended_products");
        }

        foreach($product_id_list as $product_id){
            $this->Experts_model->add_recommended_product($report_id, $product_id);
        }
    }

    public function generate_report()
    {
        $missing_key = array();

        if(isset($_POST["report_id"]))
        {
            $report_id = $_POST["report_id"];
            $report_data = $this->get_report_data($report_id);
        }
        else
        {
            $missing_key[] = "report_id";
        }

        if(isset($_POST["expert_advice"]))
        {
            $expert_advice = $_POST["expert_advice"];

        }
        else
        {
            $missing_key[] = "expert_advice";
        }

        if(isset($_POST["suggested_products"]))
        {
            $product_id_list = $_POST["suggested_products"];
            $recommended_products = $this->get_recommended_products_list($product_id_list);
        }
        else
        {
            $missing_key[] = "suggested_products";
        }

        if(count($missing_key)>0)
        {
            $missing_string = implode(", ", $missing_key);
            $missing_string = rtrim($missing_string, ", ");
            $response = array(
                "success" => false,
                "message" => $missing_string." not given!"
            );
        }
        else
        {
            $current_date = date("Y-m-d h:i:s");
            $expert_advice = str_replace("'", "''", $expert_advice);
            $pdf_upload_url = $this->Experts_model->generate_report_pdf($report_id, $report_data, $expert_advice, $recommended_products);
            $SQL = "UPDATE FM_report SET expert_advice='$expert_advice', update_date='$current_date' WHERE id=$report_id";
            $this->db->query($SQL);
            $this->add_recommended_products($report_id, $product_id_list);

            $PDF_report_data = array(
                "id" => $report_id,
                "url" => $pdf_upload_url
            );

            $response = array(
                "success" => true,
                "message" => "Farm Report Generated Successfully.",
                "report_data" => $PDF_report_data
            );
        }

        $this->response($response, 200);
    }

    public function preview_generated_report()
    {
        $page_data = [];

        if(isset($_POST["report_id"]))
        {
            $page_data["report_id"] = $_POST["report_id"];
        }

        if(isset($_POST["report_url"]))
        {
            $page_data["report_url"] = $_POST["report_url"];
        }

        $this->load->view('preview_farm_report', $page_data);
    }

    public function process_generated_report()
    {
        if(isset($_POST["report_id"]))
        {
            $report_id = $_POST["report_id"];
        }

        if(isset($_POST["report_url"]))
        {
            $report_url = $_POST["report_url"];
        }

        if(isset($_POST["status"]))
        {
            $save_report = $_POST["status"];
        }

        if($save_report=="TRUE")
        {
            $update_data = array(
                "report_link"=>$report_url, 
                "status"=>"C", 
                "update_date"=>date("Y-m-d h:i:s")
            );
            $this->db->set($update_data);
            $this->db->where("id", $report_id);
            $this->db->update("FM_report");
        }
        else
        {
            unlink(FILE_UPLOAD_BASE_PATH.$report_url);
        }

        header("Location: ".base_url("farm-management/crop-health-reports"));
        die();
    }

    function refresh_soil_information_data($user_id, $farm_id)
    {
        $curl = curl_init();
        $post_data = array("user_id"=>$user_id, "farm_id"=>$farm_id);

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://testing.surobhiagro.in/api/v2/app/getSoilInformationApi',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $post_data,
          CURLOPT_HTTPHEADER => array(
            'X-API-KEY: FARMOLOGY@123'
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function refresh_report_data($id)
    {
        $api_call_response = null;
        $condArr = array("status"=>"P", "id"=>$id);
        $report_data = $this->db->get_where("FM_report", $condArr)->result();
        if(count($report_data))
        {
            $user_id = $report_data[0]->user_id;
            $farm_id = $report_data[0]->farm_id;
            $api_call_response = $this->refresh_soil_information_data($user_id, $farm_id);
            if($api_call_response!=null)
            {
                $report_data = $this->Experts_model->refresh_report_data($id);
                if($report_data!=null)
                {
                    $response = array(
                        "success" => true,
                        "message" => "Report Data Refreshed.",
                        "report_data" => $report_data
                    );
                }
                else
                {
                    $respone = array(
                        "success" => false,
                        "message" => "Failed to Refresh Report Data!"
                    );
                }
            }
        }

        print_r(json_encode($response));
    }

    public function get_crop_name_by_farm_id($farm_id)
    {
        $crop_name = $this->Experts_model->get_crop_name_by_farm_id($farm_id);
        if($crop_name!=null)
        {
            echo $crop_name;
        }
        else
        {
            echo "No Crop Name Found!";
        }
    }

    public function get_sowing_date_by_farm_id($farm_id)
    {
        $sowing_date = $this->Experts_model->get_sowing_date_by_farm_id($farm_id);
        if($sowing_date!=null)
        {
            echo $sowing_date;
        }
        else
        {
            echo "No Sowing Date Found!";
        }
    }

    public function delete_farm_and_reports ($farm_id)
    {
        $is_deleted = $this->Experts_model->delete_farm_and_reports($farm_id);
        if($is_deleted)
        {
            $this->session->set_flashdata('success_message', "Farm Deleted Successfully.");
        }
        else
        {
            $this->session->set_flashdata('error_message', "Failed to Delete Farm!");
        }
        redirect(base_url('expert'));
    }

    public function test_pdf_structure($template_name)
    {
        include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');

        $data = array();

        // $crop_name = $this->Experts_model->get_crop_name_by_farm_id("58");
        // if($crop_name!=null)
        // {
        //     $data["crop_name"] = $crop_name;
        // }

        // $sowing_date = $this->Experts_model->get_sowing_date_by_farm_id("58");
        // if($sowing_date!=null)
        // {
        //     $data["sowing_date"] = $sowing_date;
        // }

        // $report_data = $this->get_report_data_new("7");
        // if(count($report_data))
        // {
        //     $data["report_data"] = $report_data;
        // }

        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";

        $HTML = $this->load->view("pdf-template/$template_name", $data, true);
        $PDF = new \Mpdf\Mpdf(['default_font' => 'Calibri']);
        $PDF->SetDefaultBodyCSS("background", "#282828");
        $PDF->SetDisplayMode("fullpage");
        $PDF->AddPage();
        $PDF->WriteHTML($HTML);

        $PDF->Output();
    }

    public function getFarmReportImages()
    {
        $missing_keys = [];

        if (!empty($this->input->post("farm_id")))
        {
            $farm_id = $this->input->post("farm_id");
        }
        else
        {
            $missing_keys[] = "farm_id";
        }

        if (!empty($this->input->post("image_type")))
        {
            $image_type = $this->input->post("image_type");
        }
        else
        {
            $missing_keys[] = "image_type";
        }

        if (!empty($missing_keys))
        {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtrim($missing_string, ", ");
            $response = ["success" => false, "message" => $missing_string." not given!"];
        }
        else
        {
            $response = $this->get_report_images($farm_id, $image_type);
        }

        $this->response($response, 200);
    }

    private function get_report_images($farm_id, $image_type)
    {
        $farm_details = $this->db->get_where("FM_new_farms", ["status" => "A", "farm_id" => $farm_id])->row();
        if (!empty($farm_details->farmonaut_farm_id))
        {
            $farmonaut_farm_id = $farm_details->farmonaut_farm_id;
            $sensed_days = $this->getSensedDays($farmonaut_farm_id);
            if (!empty($sensed_days))
            {
                $report_images_list = [];
                $report_types_list = ["Field Image", "Field Index Area Image"];
                $recent_sensed_day = $sensed_days[0];
                foreach ($report_types_list as $i => $report_type)
                {
                    $report_image = $this->getReportImage($report_type, $farmonaut_farm_id, $recent_sensed_day, $image_type);
                    if (!empty($report_image))
                    {
                        $report_details = [
                            "label" => $report_type,
                            "image" => $report_image
                        ];

                        $report_images_list[] = $report_details;
                    }
                }

                if (!empty($report_images_list))
                {
                    $report_analysis_scale_image = $this->get_report_analysis_scale_image($image_type);
                    if (!empty($report_analysis_scale_image))
                    {
                        $report_images_list[] = [
                            "label" => "Analysis Scale",
                            "image" => $report_analysis_scale_image
                        ];
                    }

                    $response = ["success" => true, "message" => "Farmonaut report images get successfully.", "reportImages" => $report_images_list];
                }
                else
                {
                    $response = ["success" => false, "message" => "Failed to get farmonaut report images!"];
                }
            }
            else
            {
                $response = ["success" => false, "message" => "Farm is not sensed by monitering satellite yet!"];
            }
        }
        else
        {
            $response = ["success" => false, "message" => "Farmonaut Farm ID is not found!"];
        }

        return $response;
    }

    private function get_report_analysis_scale_image($image_type)
    {
        $image_details = $this->db->get_where("FM_preferences", ["name" => $image_type."_analysis_scale"])->row();
        return (!empty($image_details->content)) ? FRONT_URL.$image_details->content : NULL;
    }

    public function getFarmReport_get($farm_id)
	{
		$farm_details = $this->db->get_where("FM_new_farms", ["status" => "A", "farm_id" => $farm_id])->row();
		if (!empty($farm_details->farmonaut_farm_id))
		{
			$farmonaut_farm_id = $farm_details->farmonaut_farm_id;
			$sensed_days = $this->getSensedDays($farmonaut_farm_id);
			if (!empty($sensed_days))
			{
				$total_farm_reports = [];
				$image_types_list = ["ndvi", "ndmi"];
				$report_types_list = ["Field Image", "Field Index Area Image"];
				$recent_sensed_day = $sensed_days[0];
				
				foreach ($image_types_list as $image_type)
				{
					$farm_reports = [];
					foreach ($report_types_list as $report_type)
					{
						$report_image = $this->getReportImage($report_type, $farmonaut_farm_id, $recent_sensed_day, $image_type);
						if (!empty($report_image))
						{
							$report_details = [
								"title" => $report_type,
								"date" => $this->convert_sensed_day_into_date($recent_sensed_day),
								"image" => $report_image
							];
							
							$farm_reports[] = $report_details;
						}
					}

					if (!empty($farm_reports))
					{
						$total_farm_reports[$image_type] = $farm_reports;
					}
				}

				if (!empty($total_farm_reports))
				{
					$response = ["success" => true, "message" => "Farm reports get successfully.", "farmReports" => $total_farm_reports];
				}
				else
				{
					$response = ["success" => false, "message" => "No Farm Reports are available yet!", "farmReports" => []];
				}
			}
			else
			{
				$response = ["success" => true, "message" => "Farm is not sensed by Monitoring Satellite yet."];
			}
		}
		else
		{
			$response = ["success" => false, "message" => "Farm is not found!"];
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	private function getSensedDays($farmonaut_farm_id)
	{
		$url = "https://us-central1-farmbase-b2f7e.cloudfunctions.net/getSensedDays";
		$postFields = [
			"UID" => $this->farmonaut_api_uid,
			"FieldID" => $farmonaut_farm_id
		];

		$response = $this->call_API_using_cURL($url, $postFields);
		if ($response->status)
		{
			foreach ($response->data as $key => $value)
			{
				$sensed_days[] = $key;
			}
		}

		return (!empty($sensed_days)) ? array_reverse($sensed_days) : [];
	}

	private function getReportImage($report_type, $field_id, $sensed_day, $image_type = "ndvi")
	{
		$previous_report = $this->db->query("SELECT * FROM FM_new_farm_report_images WHERE farm_id = $field_id AND image_type = '$image_type'")->row();
		$recent_sensed_date = $this->convert_sensed_day_into_date($sensed_day);

		if ($report_type == "Field Image")
		{
			$url = "https://us-central1-farmbase-b2f7e.cloudfunctions.net/getFieldImage";
			$image_saving_path = "uploads/new_farm_reports_images/".$field_id."-".$image_type."-field-image.png";
			$previous_report_image = (!empty($previous_report->field_image)) ? $previous_report->field_image : NULL;
			$previous_report_date = (!empty($previous_report->field_image_date)) ? $previous_report->field_image_date : NULL;
		}
		elseif ($report_type == "Field Index Area Image")
		{
			$url = "https://us-central1-farmbase-b2f7e.cloudfunctions.net/getFieldIndexAreaImage";
			$image_saving_path = "uploads/new_farm_reports_images/".$field_id."-".$image_type."-field-index-area-image.png";
			$previous_report_image = (!empty($previous_report->field_index_area_image)) ? $previous_report->field_index_area_image : NULL;
			$previous_report_date = (!empty($previous_report->field_index_area_image_date)) ? $previous_report->field_index_area_image_date : NULL;
		}

		if (!empty($previous_report_image) && (!empty($previous_report_date) && $previous_report_date == $recent_sensed_date))
		{
			$field_image = FRONT_URL.$previous_report_image;
		}
		else
		{
			$postFields = [
				"UID" => $this->farmonaut_api_uid,
				"FieldID" => $field_id,
				"SensedDay" => $sensed_day,
				"ImageType" => $image_type,
				"ColorMap" => "1"
			];

			$response = $this->call_API_using_cURL($url, $postFields);
			if ($response->status && !empty($response->data->url))
			{
				$image_uploading_path = FILE_UPLOAD_BASE_PATH.$image_saving_path;
				$is_uploaded = $this->upload_image_from_url($response->data->url, $image_uploading_path);
				if ($is_uploaded)
				{
					$field_image = FRONT_URL.$image_saving_path;
				}
			}
		}

		if (!empty($is_uploaded) && !empty($previous_report) && (!empty($previous_report_date) && $previous_report_date != $recent_sensed_date))
		{
			$condition = ["farm_id" => $field_id, "image_type" => $image_type];
			if ($report_type == "Field Image")
			{
				$data = ["field_image" => $image_saving_path, "field_image_date" => $recent_sensed_date];
			}
			elseif ($report_type == "Field Index Area Image")
			{
				$data = ["field_index_area_image" => $image_saving_path, "field_index_area_image_date" => $recent_sensed_date];
			}
			$this->db->set($data)->where($condition)->update("FM_new_farm_report_images");
		}
		elseif (!empty($is_uploaded) && !empty($previous_report) && empty($previous_report_date))
		{
			$condition = ["farm_id" => $field_id, "image_type" => $image_type];
			if ($report_type == "Field Image")
			{
				$data = ["field_image" => $image_saving_path, "field_image_date" => $recent_sensed_date];
			}
			elseif ($report_type == "Field Index Area Image")
			{
				$data = ["field_index_area_image" => $image_saving_path, "field_index_area_image_date" => $recent_sensed_date];
			}
			$this->db->set($data)->where($condition)->update("FM_new_farm_report_images");
		}
		elseif (!empty($is_uploaded) && empty($previous_report))
		{
			$data = ["farm_id" => $field_id, "image_type" => $image_type];
			if ($report_type == "Field Image")
			{
				$data["field_image"] = $image_saving_path;
				$data["field_image_date"] = $recent_sensed_date;
			}
			elseif ($report_type == "Field Index Area Image")
			{
				$data["field_index_area_image"] = $image_saving_path;
				$data["field_index_area_image_date"] = $recent_sensed_date;
			}
			$this->db->insert("FM_new_farm_report_images", $data);
		}

		return (!empty($field_image)) ? $field_image : NULL;
	}

	private function call_API_using_cURL($url, $postFields)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$API_response = curl_exec($ch);
        if (curl_errno($ch))
        {
            $API_call_error = curl_error($ch);
        }
		curl_close($ch);
		$decoded_API_response = json_decode($API_response);

		$response = new stdClass;
		if (!empty($decoded_API_response))
		{
			$response->status = true;
			$response->data = $decoded_API_response;
		}
		else
		{
			$response->status = false;
			$response->error = $API_call_error;
		}

		return $response;
	}

    private function upload_image_from_url($url, $image_uploading_destination)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$raw_image = curl_exec($ch);
		curl_close($ch);

		if (file_exists($image_uploading_destination))
		{
			unlink($image_uploading_destination);
		}

		$image_file = fopen($image_uploading_destination, 'x');
		fwrite($image_file, $raw_image);
		fclose($image_file);

		return (file_exists($image_uploading_destination)) ? true : false;
	}

	private function convert_sensed_day_into_date($sensed_day)
	{
		$year = substr($sensed_day, 0, 4);
		$month = substr($sensed_day, 4, 2);
		$day = substr($sensed_day, 6);
		return $year."-".$month."-".$day;
	}
}