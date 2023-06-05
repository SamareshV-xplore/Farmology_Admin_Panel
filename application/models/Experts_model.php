<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Experts_model extends CI_Model
{
	public function get_report_request_list($filter_data)
	{
		$list = array();

		$additional_clause = '';

        if($filter_data['status'] == 'N')
        {
            $additional_clause = "AND FM_report.status = 'P'";
        }
        else if($filter_data['status'] == 'C')
        {
            $additional_clause = "AND FM_report.status = 'C'";
        }       

        $SQL1 = "SELECT FM_report.id, concat(FM_customer.first_name, ' ', FM_customer.last_name) as customer, FM_farm.name as farmName, FM_report.request_date, FM_report.status, FM_report.kvi, FM_report.rvi, FM_report.soil_moisture  FROM `FM_report` INNER JOIN `FM_customer` ON FM_customer.id = FM_report.user_id INNER JOIN FM_farm ON FM_farm.id = FM_report.farm_id WHERE FM_report.status != 'D' ".$additional_clause." ORDER BY FM_report.id DESC";

        $SQL2 = "SELECT 
                    FM_report.id, 
                    concat(FM_customer.first_name,' ',FM_customer.last_name) as customer,
                    FM_farm.id as farm_id, 
                    FM_farm.name as farmName,
                    FM_farm.sowingDate as sowing_date,
                    FM_crop.title as crop_type,
                    FM_report.request_date, 
                    FM_report.status, 
                    FM_report.kvi, 
                    FM_report.rvi, 
                    FM_report.soil_moisture  
                FROM `FM_report`  
                INNER JOIN `FM_customer` ON FM_customer.id = FM_report.user_id 
                INNER JOIN FM_farm ON FM_farm.id = FM_report.farm_id
                INNER JOIN FM_crop ON FM_farm.crop_name = FM_crop.id
                WHERE FM_report.status != 'D' ".$additional_clause." ORDER BY FM_report.id DESC";
        
        $query = $this->db->query($SQL2);

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {       
                    $crop_stage = $this->get_crop_stage_by_farm_id($row->farm_id);
                    
                    $list[] = array("id" => $row->id,"customer_name" => $row->customer, "farm_id" => $row->farm_id, "farm_name" => $row->farmName, "sowing_date" => $row->sowing_date, "crop_type" => $row->crop_type, "crop_stage" => $crop_stage, "kvi" => $row->kvi, "rvi" => $row->rvi, "soil_moisture" => $row->soil_moisture, "status" => $row->status, "req_date" => date('d-m-Y', strtotime($row->request_date)));
            }
        }
        return $list;
	}

    public function get_farm_report_requests($filter_data)
    {
		$more_conditions = "";

        if ($filter_data['status'] == 'N')
        {
            $more_conditions = "AND FM_report.status = 'P'";
        }
        else if ($filter_data['status'] == 'C')
        {
            $more_conditions = "AND FM_report.status = 'C'";
        }       

        $sql = "SELECT 
                    FM_report.id, 
                    concat(FM_customer.first_name,' ',FM_customer.last_name) as customer,
                    FM_new_farms.farm_id, 
                    FM_new_farms.name as farmName,
                    FM_new_farms.crop_sowing_date as sowing_date,
                    FM_crop.title as crop_type,
                    FM_report.request_date, 
                    FM_report.status, 
                    FM_report.ndvi, 
                    FM_report.savi, 
                    FM_report.ndwi  
                FROM `FM_report`  
                INNER JOIN `FM_customer` ON FM_customer.id = FM_report.user_id 
                INNER JOIN FM_new_farms ON FM_new_farms.farm_id = FM_report.farm_id
                INNER JOIN FM_crop ON FM_new_farms.crop_id = FM_crop.id
                WHERE FM_report.status != 'D' ".$more_conditions;
        
        $requests_list = $this->db->query($sql)->result();
        if (!empty($requests_list))
        {
            foreach ($requests_list as $row)
            {       
                $crop_stage = $this->get_crop_stage_by_farm_id($row->farm_id);
                
                $farm_report_requests[] = array("id" => $row->id, "customer_name" => $row->customer, "farm_id" => $row->farm_id, "farm_name" => $row->farmName, "sowing_date" => $row->sowing_date, "crop_type" => $row->crop_type, "crop_stage" => $crop_stage, "ndvi" => $row->ndvi, "savi" => $row->savi, "ndwi" => $row->ndwi, "status" => $row->status, "req_date" => date('d-m-Y', strtotime($row->request_date)));
            }
        }

        return (!empty($farm_report_requests)) ? $farm_report_requests : [];
    }

    public function refresh_report_data($id)
    {
        $SQL1 = "SELECT FM_report.id, concat(FM_customer.first_name, ' ', FM_customer.last_name) as customer, FM_farm.name as farm_name, FM_report.request_date, FM_report.status, FM_report.kvi, FM_report.rvi, FM_report.soil_moisture  FROM `FM_report` INNER JOIN FM_customer ON FM_customer.id = FM_report.user_id INNER JOIN FM_farm ON FM_farm.id = FM_report.farm_id WHERE FM_report.status != 'D' AND FM_report.id=$id";

        $SQL2 = "SELECT 
                    FM_report.id, 
                    concat(FM_customer.first_name,' ',FM_customer.last_name) as customer, 
                    FM_farm.id as farm_id, 
                    FM_farm.name as farm_name,
                    FM_farm.sowingDate as sowing_date,
                    FM_crop.title as crop_type,
                    FM_report.request_date, 
                    FM_report.status, 
                    FM_report.kvi, 
                    FM_report.rvi, 
                    FM_report.soil_moisture  
                FROM `FM_report`  
                INNER JOIN `FM_customer` ON FM_customer.id = FM_report.user_id 
                INNER JOIN FM_farm ON FM_farm.id = FM_report.farm_id
                INNER JOIN FM_crop ON FM_farm.crop_name = FM_crop.id
                WHERE FM_report.status != 'D' AND FM_report.id={$id}";

        $report = $this->db->query($SQL2)->row();

        if(is_object($report))
        {
            $report->crop_stage = $this->get_crop_stage_by_farm_id($report->farm_id);
            $report->request_date = date("d-m-Y", strtotime($report->request_date));
            return $report;
        } 
        else 
        {
            return null;
        }
    }

    public function getAllProducts()
    {
        return $this->db->get_where('FM_product', ['status' => 'Y'])->result();
    }

    public function get_product_by_product_id($id = '')
    {
        return $this->db->query("SELECT FM_product.id, FM_product.title, FM_product_image.image FROM FM_product INNER JOIN FM_product_image ON FM_product.id = FM_product_image.product_id WHERE FM_product.id = $id")->row();
    }

    public function get_product_by_id($id)
    {
        $product = $this->db->query("SELECT FM_product.id, FM_product.title, FM_product_image.image FROM FM_product INNER JOIN FM_product_image ON FM_product.id = FM_product_image.product_id WHERE FM_product.id = $id")->row();

        if(is_object($product))
        {
            $product->image = FRONT_URL.$product->image;
            return $product;
        }
        else
        {
            return null;
        }
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

    private function get_crop_stage_by_farm_id($farm_id)
    {
        $crop_stage = "Unknown";
        $farm_details_conditions = ["status" => "A", "farm_id" => $farm_id];
        $farm_details = $this->db->get_where("FM_new_farms", $farm_details_conditions)->row();
        if (!empty($farm_details->crop_id) && !empty($farm_details->crop_sowing_date)) {
            $crop_id = $farm_details->crop_id;
            $sowing_date = $farm_details->crop_sowing_date;
            $crop_health_stage_details = $this->get_crop_health_stage_details($crop_id, $sowing_date);
            if (!empty($crop_health_stage_details->stage_name)) {
                $crop_stage = ucwords(strtolower($crop_health_stage_details->stage_name));
            }
        }

        return $crop_stage;
    }

    private function get_crop_health_stage_details($crop_id, $sowing_date)
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

    public function get_crop_health_details_by_farm_id($id)
    {
        $crop_health_details = [];

        $condition = ["status" => "A", "farm_id" => $id];
        $farm = $this->db->select("*")
                             ->from("FM_new_farms")
                             ->where($condition)
                             ->get()->row();

        if (!empty($farm))
        {
            $crop_health_stage_details = $this->get_crop_health_stage_details($farm->crop_id, $farm->crop_sowing_date);

            if (!empty($crop_health_stage_details->hash_id)) {

                $crop_health_details["crop_health_stage_details"] = $crop_health_stage_details;
                $crop_stage_id = $crop_health_stage_details->hash_id;

                if (!empty($farm->coordinates)) {
                    $farm_coordinates = json_decode($farm->coordinates);
                    if (!empty($farm_coordinates[0]->lat) && !empty($farm_coordinates[0]->lng)) {
                        $lat = $farm_coordinates[0]->lat;
                        $lng = $farm_coordinates[0]->lng;

                        $farm_temperature_and_humidity = $this->get_farm_temperature_and_humidity($lat, $lng);
                        if ($farm_temperature_and_humidity["status"] == true) {

                            $farming_field_temperature = round($farm_temperature_and_humidity["temperature"]);
                            $farming_field_humidity = round($farm_temperature_and_humidity["humidity"]);
                            if(!empty($crop_stage_id) && !empty($farming_field_temperature) && !empty($farming_field_humidity)) {
                                $list_of_possible_diseases_and_pests = $this->get_list_of_possible_diseases_and_pests($crop_stage_id, $farming_field_temperature, $farming_field_humidity);
                                $crop_health_details["list_of_possible_diseases_and_pests"] = $list_of_possible_diseases_and_pests;
                            }
                        }
                    }
                }
            }
        }

        return $crop_health_details;
    }

    public function get_farm_temperature_and_humidity($lat, $lng)
    {
        $farm_temperature_and_humidity_result = ["status" => false];

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
                $farm_temperature_and_humidity_result = [
                    "status" => true,
                    "temperature" => $weather_forecast_data->values->temperature,
                    "humidity" => $weather_forecast_data->values->humidity
                ];
            }
        }

        return $farm_temperature_and_humidity_result;
    }

    public function get_list_of_possible_diseases_and_pests($crop_stage_id = NULL, $farming_field_temperature = NULL, $farming_field_humidity = NULL)
    {
        $response = ["pests" => [], "diseases" => []];

        if (isset($crop_stage_id) && isset($farming_field_temperature) && isset($farming_field_humidity)) {

            $SQL = "SELECT pest, disease FROM FM_pests_and_diseases_per_stage WHERE crop_stage_id = '$crop_stage_id' AND (minimum_temperature <= $farming_field_temperature AND maximum_temperature >= $farming_field_temperature) AND (minimum_humidity <= $farming_field_humidity AND maximum_humidity >= $farming_field_humidity)";
            $result = $this->db->query($SQL)->result();

            if (!empty($result)) {
                $pests = [];
                $diseases = [];
                foreach ($result as $i => $details) {
                    if (!empty($details->pest)) {
                        $pests[] = $details->pest;
                    }
                    if (!empty($details->disease)) {
                        $diseases[] = $details->disease;
                    }
                }

                $response["pests"] = $pests;
                $response["diseases"] = $diseases;
            }
        }

        return $response;
    }

    public function get_owner_name_by_farm_id($id)
    {
        $owner_name = "";
        $farm = $this->db->select("*")
                         ->from("FM_new_farms")
                         ->where("farm_id", $id)
                         ->get()->row();

        if (isset($farm))
        {
            $customer = $this->db->select("*")
                                 ->from("FM_customer")
                                 ->where("id", $farm->user_id)
                                 ->get()->row();

            if (isset($customer))
            {
                $owner_name = $customer->first_name." ".$customer->last_name;
            }
        }

        return $owner_name;
    }

    public function get_crop_name_by_farm_id($id)
    {
        $crop_name = null;
        $condArr = array("status"=>"A", "farm_id"=>$id);
        $crop_details = $this->db->select("*")
                             ->from("FM_new_farms")
                             ->where($condArr)
                             ->get()->row();

        if(is_object($crop_details))
        {
            $crop_name_id = $crop_details->crop_id;
            $condArr2 = array("status"=>"Y", "id"=>$crop_name_id);
            $crop_name_details = $this->db->select("*")
                                  ->from("FM_crop")
                                  ->where($condArr2)
                                  ->get()->row();

            if(is_object($crop_name_details))
            {
                $crop_name = $crop_name_details->title;
            }
        }

        return $crop_name;
    }

    public function get_sowing_date_by_farm_id($id)
    {   
        $sowing_date = null;
        $condArr = array("status"=>"A", "farm_id"=>$id);
        $farm_details = $this->db->select("*")
                                 ->from("FM_new_farms")
                                 ->where($condArr)
                                 ->get()->row();

        if(is_object($farm_details))
        {
            $sowing_date = date("d-m-Y", strtotime($farm_details->crop_sowing_date));
        }

        return $sowing_date;
    }

    public function add_recommended_product($report_id, $product_id)
    {
        $random_id = mt_rand(999, 999999999);
        $current_date = date("Y-m-d h:i:s");
        $SQL = "INSERT INTO FM_recommended_products (hash_id, report_id, product_id, recommended_on) VALUES ('$random_id', '$report_id', '$product_id', '$current_date')";
        $this->db->query($SQL);
    }

    public function get_report_by_id($id,$status)
    {
        $report = $this->db->query("SELECT id, kvi, rvi, soil_moisture, status FROM FM_report WHERE status='$status' AND id=$id")->row();

        if(is_object($report))
        {
            return $report;
        }
        else
        {
            return null;
        }
    }

    public function get_farm_id_from_report_id($report_id)
    {   
        $condArr = array("status"=>"P", "id"=>$report_id);
        $report_details = $this->db->get_where("FM_report", $condArr)->result();
        if(count($report_details))
        {
            $farm_id = $report_details[0]->farm_id;
        }
        else
        {
            $farm_id = $report_details;
        }

        return $farm_id;
    }

    public function generate_report_pdf($report_id, $report_data, $expert_advice, $recommended_products)
    {
        include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');
        $data = array();

        $farm_id = $this->get_farm_id_from_report_id($report_id);
        $crop_name = $this->get_crop_name_by_farm_id($farm_id);
        $sowing_date = $this->get_sowing_date_by_farm_id($farm_id);
        $owner_name = $this->get_owner_name_by_farm_id($farm_id);
        $crop_health_details = $this->get_crop_health_details_by_farm_id($farm_id);

        if ($crop_name!=null) {
            $data["crop_name"] = $crop_name;
        }

        if ($sowing_date!=null) {
            $data["sowing_date"] = $sowing_date;
        }

        if ($owner_name!="") {
            $data["owner_name"] = $owner_name;
        }

        if (!empty($crop_health_details["crop_health_stage_details"])) {
            $data["crop_health_stage_details"] = $crop_health_details["crop_health_stage_details"];
        }

        if (!empty($crop_health_details["list_of_possible_diseases_and_pests"])) {
            $data["list_of_possible_diseases_and_pests"] = $crop_health_details["list_of_possible_diseases_and_pests"];
        }

        $data["report_data"] = $report_data;
        $data["expert_advice"] = $expert_advice;
        $data["recommended_products"] = $recommended_products;

        $HTML = $this->load->view("pdf-template/farm-report-new", $data, true);

        $PDF = new \Mpdf\Mpdf(['default_font' => 'Calibri']);
        $PDF->SetDefaultBodyCSS("background", "#282828");
        $PDF->autoLangToFont = true;
        $PDF->autoScriptToLang = true;
        $PDF->AddPage();
        $PDF->WriteHTML($HTML);
        $random_name = time();
        $pdf_upload_url = 'uploads/report/FarmReport-'.$random_name.'.pdf';
        $content = $PDF->Output(FILE_UPLOAD_BASE_PATH.$pdf_upload_url,'F');

        return $pdf_upload_url;
    }

    public function delete_farm_and_reports ($farm_id)
    {
        $condition = ["id" => $farm_id];
        $this->db->where($condition);
        $this->db->delete("FM_farm");

        $condition2= ["farm_id" => $farm_id];
        $reports = $this->db->get_where("FM_report", $condition2)->result();
        foreach ($reports as $report)
        {
            $condition3 = ["id" => $report->id];
            $this->db->where($condition3);
            $this->db->delete("FM_report");
        }

        $last_reports = $this->db->get_where("FM_last_report", $condition2)->result();
        foreach ($last_reports as $last_report)
        {
            $condition4 = ["id" => $last_report->id];
            $this->db->where($condition4);
            $this->db->delete("FM_last_report");
        }

        return true;
    }

    public function get_farm_details_by_report_id($report_id)
    {
        $sql = "SELECT NF.* FROM FM_new_farms NF INNER JOIN FM_report R ON R.farm_id = NF.farm_id WHERE R.id = '$report_id' AND NF.status = 'A'";
        $result = $this->db->query($sql)->row();
        return $result;
    }
}