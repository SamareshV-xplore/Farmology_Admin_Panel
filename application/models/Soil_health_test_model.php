<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Soil_health_test_model extends CI_Model {

    public function get_soil_health_test_request_by_hash_id ($hash_id)
    {
        $soil_health_test_requests = $this->db->get_where("FM_soil_health_test_requests", ["hash_id" => $hash_id])->row();
        return $soil_health_test_requests;
    }

    public function change_sample_received_status ($hash_id, $sample_received)
    {
		$current_date = date("Y-m-d H:i:s");
		if ($sample_received == 1)
		{
			$update_data = ["sample_received" => $sample_received, "sample_collection_date" => $current_date];
		}
        else
		{
			$update_data = ["sample_received" => $sample_received, "sample_collection_date" => null];
		}
        $this->db->set($update_data);
        $this->db->where(["hash_id" => $hash_id]);
        $this->db->update("FM_soil_health_test_requests");
    }

    public function get_all_soil_health_test_requests ()
	{   
        $requests_list = [];
        $results = $this->db->get("FM_soil_health_test_requests")->result();
		if (!empty($results))
        {
            foreach ($results as $row)
            {
                $request = $row;
                $payment_details = $this->getSoilHealthTestPaymentDetails($row->payment_id);
                if (!empty($payment_details->amount) && !empty($payment_details->status) && !empty($payment_details->date))
                {
                    $request->payment_status = ($payment_details->status == "C") ? true : false;
                    $request->payment_amount = ($payment_details->status == "C") ? "<b>".$payment_details->amount." Paid</b>" : $payment_details->amount." Due";
                    $request->payment_date = date("d/m/Y", strtotime($payment_details->date));
                }
                $request->status = $this->getSoilHealthStatus($row);
                $requests_list[] = $request;
            }
        }
        return $requests_list;
	}

	private function getSoilHealthStatus ($request)
	{
		$status = "";
		$payment_status = $this->checkSoilHealthPaymentStatus($request->payment_id);
		$report_status = $request->report_id;
		
		if (empty($request->receipt))
		{
			$status = "Receipt Not Uploaded";
		}
		elseif (!empty($request->receipt) && empty($request->sample_received))
		{
			$status = "Sample On The Way";
		}
		elseif (!empty($request->receipt) && !empty($request->sample_received) && empty($payment_status))
		{
			$status = "Sample Received";
		}
		elseif (!empty($request->receipt) && !empty($request->sample_received) && !empty($payment_status) && empty($report_status))
		{
			$status = "In Progress";
		}
		elseif (!empty($request->receipt) && !empty($request->sample_received) && !empty($payment_status) && !empty($report_status))
		{
			$status = "Completed";
		}

		return $status;
	}

    private function checkSoilHealthPaymentStatus ($id)
	{
		$status = false;
		$condition = ["hash_id" => $id, "status" => "C"];
		$completed_payment_details = $this->db->get_where("FM_soil_health_test_payments", $condition)->row();
		if (!empty($completed_payment_details))
		{
			$status = true;
		}
		return $status;
	}

    private function getSoilHealthTestPaymentDetails ($id)
	{
		$condition = ["hash_id" => $id];
		$payment_details = $this->db->get_where("FM_soil_health_test_payments", $condition)->row();
		return $payment_details;
	}

	public function get_soil_health_report_user_data ($request_id)
	{
		$user_data = $this->db->get_where("FM_soil_health_test_requests", ["hash_id" => $request_id])->row();
		return $user_data;
	}

	public function add_soil_health_test_report ($data)
	{
		$this->db->insert("FM_soil_health_test_reports", $data);
		return $this->db->affected_rows();
	}

	public function edit_soil_health_test_report ($id, $data)
	{
		$this->db->set($data)->where(["hash_id" => $id])->update("FM_soil_health_test_reports");
		return $this->db->affected_rows();
	}

	public function add_soil_health_test_recommended_product ($data)
	{
		$this->db->insert("FM_soil_health_test_recommended_products", $data);
		return $this->db->affected_rows();
	}

	public function generate_soil_health_test_report_pdf ($user_data, $report_data, $expert_advice, $recommended_products)
    {
        include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');
        $data = array();

		$data["user_data"] = $user_data;
        $data["report_data"] = $report_data;
        $data["expert_advice"] = $expert_advice;
        $data["recommended_products"] = $recommended_products;

        $HTML = $this->load->view("pdf-template/soil_health_report", $data, true);

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
        $random_name = time();
        $pdf_upload_url = 'uploads/soil_health_test_reports/SoilHealthReport-'.$random_name.'.pdf';
        $content = $PDF->Output(FILE_UPLOAD_BASE_PATH.$pdf_upload_url,'F');

        return $pdf_upload_url;
    }
}

?>