<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model
{

    function generate_invoice($order_no = "")
    {
        $order_details = $this->order_model->order_details_by_no($order_no);

        if(count($order_details) > 0)
        {
            $data = array("order_details" => $order_details);
            $html = $this->load->view('pdf-template/invoice', $data, true); 
            include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');

            $pdf = new \Mpdf\Mpdf();
            $pdf->AddPage();
            $pdf->WriteHTML($html);
            $pdf_url = 'uploads/invoice/Invoice-'.$order_no.'.pdf';
            $content = $pdf->Output(FILE_UPLOAD_BASE_PATH.$pdf_url,'F');

            $update_data = array("invoice" => $pdf_url);
            $this->db->where("order_no", $order_no);
            $this->db->update("FM_order", $update_data);


            $response = array("status" => "Y", "message" => "Invoice successfully generated.");
        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid Try.");
        }

        return $response;
    }

    function generate_invoice_new($order_no = "")
    {
        $order_details = $this->order_model->order_details_by_no($order_no);

        if(count($order_details) > 0)
        {
            $data = array("order_details" => $order_details);

            // Old Invoice PDF Structure
            // $html = $this->load->view('pdf-template/invoice', $data, true); 

			// New Invoice PDF Structure
			$html = $this->load->view('pdf-template/new_invoice_format', $data, true);

            include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');
            $pdf = new \Mpdf\Mpdf();
            $pdf->AddPage();
            $pdf->WriteHTML($html);
            $pdf_url = 'uploads/invoice/Invoice-'.$order_no.'.pdf';
            $content = $pdf->Output(FILE_UPLOAD_BASE_PATH.$pdf_url,'F');

            $update_data = array("invoice" => $pdf_url);
            $this->db->where("order_no", $order_no);
            $this->db->update("FM_order", $update_data);


            $response = array("status" => "Y", "message" => "Invoice successfully generated.");
        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid Try.");
        }

        return $response;
    }
}
?>