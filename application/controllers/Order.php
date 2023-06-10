<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model("delivery_drivers_notifications_model");       
    }

	//Banner List
	public function index()
	{
        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Order List";
        $left_data['navigation'] = "order"; 
        $left_data['sub_navigation'] = "order-list"; 

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

        $export_flag = "N";
        
        if($this->input->post('filter'))
        {
            

            $search_type = $this->input->post('search-type');
            $custom_date = $this->input->post('custom-date');
            $order_status = $this->input->post('order-status');

            

            if($search_type == 'manual-date')
            {

                $date_range = $custom_date;
                $exp_date_range = explode(' - ', $date_range);

                $start_date = trim($exp_date_range[0]);
                $exp_start_date = explode('/', $start_date);
                $start_date_is = $exp_start_date[2]."-".$exp_start_date[0]."-".$exp_start_date[1];

                $end_date = trim($exp_date_range[1]);
                $exp_end_date = explode('/', $end_date);
                $end_date_is = $exp_end_date[2]."-".$exp_end_date[0]."-".$exp_end_date[1];

                // get days count for export                
                $datediff = strtotime($end_date_is." 23:59:59") - strtotime($start_date_is." 00:00:00");

                $total_days = round($datediff / (60 * 60 * 24));

                if($total_days < 91)
                {
                    $export_flag = "Y";
                }

                


                $filter_data = array("filter" => true, "search-type" => $search_type, "order-status" => $order_status, 'custom-date' => $custom_date);





            }
            else if($search_type == 'today-delivery')
            {
                $filter_data = array("filter" => true, "search-type" => $search_type, "order-status" => 'all', "custom-date" => "".date("m/d/Y")." - ".date("m/d/Y")."");
            }
            else
            {
                 $filter_data = array("filter" => false, "search-type" => 'default', "order-status" => 'all', "custom-date" => "".date("m/d/Y")." - ".date("m/d/Y")."");

            }
        }
        else
        {
            $filter_data = array("filter" => false, "search-type" => 'default', "order-status" => 'all', "custom-date" => "".date("m/d/Y")." - ".date("m/d/Y")."");
        }

        

        
        $page_data['filter_data'] = $filter_data;
        $page_data['export_flag'] = $export_flag;

        // get order list
        $order_no = "";
        $order_list = $this->order_model->get_order_list($filter_data, $order_no);
        $page_data['order_list'] = $order_list;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('order/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    function export_order()
    {

        if(isset($_REQUEST['date-range']) && isset($_REQUEST['status']))
        {
            $date_range = $_REQUEST['date-range'];
            $order_status = $_REQUEST['status'];
            $filter_data = array("filter" => true, "search-type" => 'manual-date', "order-status" => $order_status, 'custom-date' => $date_range);

            $order_no = "";
            $order_list = $this->order_model->get_order_list($filter_data, $order_no);

            /*echo "<pre>";
            print_r($order_list);
            echo "</pre>"; exit;*/


        require(APPPATH.'third_party/PHPExcel-1.8/Classes/PHPExcel.php');
        require(APPPATH.'third_party/PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php');
        $objPHPExcel = new PHPExcel;

        $objPHPExcel->getProperties()->setCreator("");
        $objPHPExcel->getProperties()->setLastModifiedBy("");
        $objPHPExcel->getProperties()->setTitle("");
        $objPHPExcel->getProperties()->setSubject("");
        $objPHPExcel->getProperties()->setDescription("");

        $objPHPExcel->setActiveSheetindex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ORDER DATE');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'ORDER ID');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'PRODUCT DETAILS');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'SHIPPING DETAILS');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'DELIVERY DATE');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'CUSTOMER DETAILS');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'PROMO CODE');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'SUBTOTAL');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'SHIPPING CHARGE');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'PROMO DISCOUNT');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'ORDER TOTAL');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'ORDER STATUS');

        $row_no = 2;

        if(count($order_list) > 0)
        {
            foreach($order_list as $report_row)
            {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$row_no, $report_row['created_date']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$row_no, $report_row['order_no']);
                
                //----------------------------
                $product_details = "";

                $product_count = count($report_row['product_details']);
                $no = 1;
                
                foreach($report_row['product_details'] as $order_product)
                {
                    $product_details.= $order_product['variation_details']['product_details']['name']." - ".$order_product['variation_details']['variation_details']['title']." x ".$order_product['quantity'];
                    if($no != $product_count)
                    {
                        $product_details.="\n";
                    }
                    $no++;
                }
                

                //--------------------------

                $objPHPExcel->getActiveSheet()->setCellValue('C'.$row_no, $product_details);
                
                //----------------------

                $shipping_details = "NAME: ".$report_row['address_details']['name']."\nPHONE: ".$report_row['address_details']['phone']."\nADDRESS 1: ".$report_row['address_details']['address_1']."\nADDRESS 2: ".$report_row['address_details']['address_2']."\nLANDMARK:".$report_row['address_details']['landmark']."\nCITY: ".$report_row['address_details']['city_name']."\nSTATE: ".$report_row['address_details']['state_name']."\nZIP CODE: ".$report_row['address_details']['zip_code'];


                //----------------------------

                $objPHPExcel->getActiveSheet()->setCellValue('D'.$row_no, $shipping_details);

                $delivery_date = $report_row['delivery_date']." (".$report_row['time_slot_details']['time_slot'].")";


                $objPHPExcel->getActiveSheet()->setCellValue('E'.$row_no, $delivery_date);
                $customer_details = "NAME: ".$report_row['customer_details']['full_name']."\n"."EMAIL: ".$report_row['customer_details']['email']."\nPHONE: ".$report_row['customer_details']['phone'];

                $objPHPExcel->getActiveSheet()->setCellValue('F'.$row_no, $customer_details);
                if(count($report_row['promo_code_details']) > 0)
                {
                    $promo_code = $report_row['promo_code_details']['promo_code'];
                }
                else
                {
                    $promo_code = "";
                }
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$row_no, $promo_code);
               
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$row_no, $report_row['total_price']);

                $objPHPExcel->getActiveSheet()->setCellValue('I'.$row_no, $report_row['delivery_charge']);

                $objPHPExcel->getActiveSheet()->setCellValue('J'.$row_no, $report_row['discount']);

                $objPHPExcel->getActiveSheet()->setCellValue('K'.$row_no, $report_row['order_total']);
                if($report_row['status'] == 'NOP')
                {
                    $order_status = "ORDER FAILED";
                }
                else if($report_row['status'] == 'P')
                {
                    $order_status = "PROCESSING";
                }
                else if($report_row['status'] == 'S')
                {
                    $order_status = "SHIPPING";
                }
                else if($report_row['status'] == 'D')
                {
                    $order_status = "COMPLETE";
                }
                else if($report_row['status'] == 'C')
                {
                    $order_status = "CANCELLED";
                }
                else
                {
                    $order_status = "UNKNOWN";
                }
                $objPHPExcel->getActiveSheet()->setCellValue('L'.$row_no, $order_status);
                
                $row_no++;
            }
        }       

        $exp_date_range = explode(' - ', $date_range);

        $start_date = trim($exp_date_range[0]);
        $exp_start_date = explode('/', $start_date);
        $start_date_is = $exp_start_date[2]."-".$exp_start_date[0]."-".$exp_start_date[1];

        $end_date = trim($exp_date_range[1]);
        $exp_end_date = explode('/', $end_date);
        $end_date_is = $exp_end_date[2]."-".$exp_end_date[0]."-".$exp_end_date[1];

        $filename = "ORDER-LIST[".$start_date_is." to ".$end_date_is."].xlsx";
        $objPHPExcel->getActiveSheet()->setTitle("Order List");

        header('Content-Type: application/vmd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age-0');
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //ob_end_clean();
        $writer->save('php://output');
        exit;

            
        }
        else
        {
            redirect(base_url('order'));
        }      

        

    }

    function gerenate_invoice()
    {
        $data = array();

        $html = $this->load->view('pdf-template/invoice', $data, true); 
        include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');

        $pdf = new \Mpdf\Mpdf();
        $pdf->AddPage();
        $pdf->WriteHTML($html);
        $pdf_url = 'uploads/invoice.pdf';
        $content = $pdf->Output(FILE_UPLOAD_BASE_PATH.$pdf_url,'F');
        /*$update_data = array("payslip" => $pdf_url);
        $this->db->where("id", $record_id);
        $this->db->update("HRMS_employee_salary_record", $update_data);*/
    }

    function update_order_status()
    {
        //$this->load->model('order_model');
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $order_no = $this->order_model->get_order_no_by_order_id($id);
        $update_status = $this->order_model->update_order_status($status, $order_no); 
        // call notification
        $this->notification_model->send_notification($order_no);
        //----------------------------------------------      
        $response = array("status" => "Y", "message" => "Successfully Updated.");
            echo json_encode($response);           
        

    }

    function update_order_details()
    {
        //$this->load->model('order_model');
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $payment_method = $this->input->post('payment_method');

        $order_no = $this->order_model->get_order_no_by_order_id($id);
        $update_status = $this->order_model->update_order_details($status, $payment_method, $order_no); 
        // call notification
        $this->notification_model->send_notification($order_no);
        //----------------------------------------------      
        $this->session->set_flashdata('success_message', "Order details successfully updated.");

        $response = array("status" => "Y", "message" => "Successfully Updated.");

            echo json_encode($response);           
        

    }

    function details($id = 0)
    {

        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Order Details";
        $left_data['navigation'] = "Order"; 
        $left_data['sub_navigation'] = "order-list"; 

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

        $order_no = $this->order_model->get_order_no_by_order_id($id);

        if($order_no == '')
        {
            redirect(base_url('order'));
        }
        else{
            $order_details = $this->order_model->order_details_by_no($order_no);
        }        
        

        
        $page_data['order_details'] = $order_details;
        
        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('order/details_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);

    }

    public function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }
    
    public function get_delivery_drivers_list_by_order_no($order_no)
    {
        if (!empty($order_no))
        {
            $delivery_drivers_list = $this->order_model->get_delivery_drivers_list_by_order_no($order_no);
            if (!empty($delivery_drivers_list))
            {
                $response = ["success" => true, "message" => "Delivery drivers list get successfully.", "delivery_drivers_list" => $delivery_drivers_list];
            }
            else
            {
                $response = ["success" => true, "message" => "No delivery drivers found!", "delivery_drivers_list" => []];
            }
        }
        else
        {
            $response = ["success" => false, "message" => "Please give us a order id to get available delivery drivers list."];
        }
        
        $this->response($response, 200);
    }

    public function get_merchant_centers_list_by_order_no($order_no)
    {
        if (!empty($order_no))
        {
            $merchant_centers_list = $this->order_model->get_merchant_centers_list_by_order_no($order_no);
            if (!empty($merchant_centers_list))
            {
                $response = ["success" => true, "message" => "Merchant centers list get successfully.", "merchant_centers_list" => $merchant_centers_list];
            }
            else
            {
                $response = ["success" => true, "message" => "No merchant centers found!", "merchant_centers_list" => []];
            }
        }
        else
        {
            $response = ["success" => false, "message" => "Please give us a order id to get available merchant centers list."];
        }
        
        $this->response($response, 200);
    }

    public function assign_delivery_driver()
    {
        $missing_keys = [];

        if (!empty($this->input->post("order_no")))
        {
            $order_no = $this->input->post("order_no");
        }
        else
        {
            $missing_keys[] = "order_no";
        }

        if (!empty($this->input->post("delivery_driver_id")))
        {
            $delivery_driver_id = $this->input->post("delivery_driver_id");
        }
        else
        {
            $missing_keys[] = "delivery_driver_id";
        }

        if (!empty($this->input->post("merchant_id")))
        {
            $merchant_id = $this->input->post("merchant_id");
            $merchant_address_id = $this->order_model->get_merchant_address_id_by_merchant_id($merchant_id);
        }
        else
        {
            $missing_keys[] = "merchant_id";
        }

        if (!empty($missing_keys))
        {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtrim($missing_string, ", ");
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => $missing_string." not given!"];
        }
        else
        {
            $condition = ["order_no" => $order_no];

            $delivery_address_coordinates = $this->get_delivery_address_coordinates_by_order_no($order_no);
            $pickup_address_coordinates = $this->get_pickup_address_coordinates_by_merchant_id($merchant_id);

            $data = ["delivery_driver_id" => $delivery_driver_id, "delivery_address_coordinates" => $delivery_address_coordinates, "merchant_id" => $merchant_id, "merchant_address_id" => $merchant_address_id, "pickup_address_coordinates" => $pickup_address_coordinates];

            $is_updated = $this->order_model->update_order_on_condition($data, $condition);

            $delivery_driver_details = $this->order_model->get_delivery_driver_details_by_id($delivery_driver_id);
            $merchant_center_details = $this->order_model->get_merchant_center_details_by_id($merchant_id);
            $delivery_driver_and_customer_details = $this->notification_model->send_delivery_driver_assigned_SMS_and_Notification($order_no);
            $this->delivery_drivers_notifications_model->send_notification_to_delivery_driver($order_no);

            if ($is_updated == true)
            {
                $response = ["success" => true, "message" => "Driver Assigned", "delivery_driver_details" => $delivery_driver_details, "merchant_center_details" => $merchant_center_details, "delivery_driver_and_customer_details" => $delivery_driver_and_customer_details];
            }
            else
            {
                $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "failed to update order details in database"];
            }
        }
        
        $this->response($response, 200);
    }

    public function get_delivery_address_coordinates_by_order_no($order_no)
    {
        $sql = "SELECT O.id AS order_id, O.order_no, C.id AS customer_id, CONCAT(C.first_name, ' ', C.last_name) AS customer_name, C.phone AS customer_phone, O.merchant_id, CA.address_1 AS address, SL.state, CA.zip_code, CA.landmark FROM FM_order O LEFT JOIN FM_customer C ON O.customer_id = C.id LEFT JOIN FM_customer_address CA ON O.address_id = CA.id LEFT JOIN FM_state_lookup SL ON CA.state_id = SL.id WHERE O.status NOT IN ('ONP','D','C') AND O.order_no = '$order_no'";
		$order_details = $this->db->query($sql)->row();
        
		if (!empty($order_details))
		{
			$delivery_address = "";
			$delivery_address .= (!empty($order_details->address)) ? $order_details->address : "";
			$delivery_address .= (!empty($order_details->state)) ? ", ".$order_details->state : "";
			$delivery_address .= (!empty($order_details->zip_code)) ? " ".$order_details->zip_code : "";
			$delivery_address .= (!empty($order_details->landmark)) ? " near ".$order_details->landmark : "";
			$delivery_address_coordinates = $this->get_coordinates_by_address($delivery_address);
		}
		
		return (!empty($delivery_address_coordinates)) ? json_encode($delivery_address_coordinates) : NULL;
    }

    public function get_pickup_address_coordinates_by_merchant_id($merchant_id)
    {   
        $sql = "SELECT C.id, CONCAT(C.first_name, ' ', C.last_name) AS name, C.phone, CA.address_1, (SELECT SL.state FROM `FM_state_lookup` SL WHERE SL.id = CA.state_id) AS state, (SELECT DL.name FROM `FM_district_lookup` DL WHERE DL.id = CA.district_id) AS district, (SELECT CL.name FROM `FM_city_lookup` CL WHERE CL.id = CA.city_id) AS city, CA.landmark, CA.zip_code FROM `FM_customer` C INNER JOIN `FM_customer_address` CA ON CA.customer_id = C.id WHERE C.status = 'Y' AND C.type = 'M' AND C.id = $merchant_id ORDER BY C.id DESC";
        $result = $this->db->query($sql)->row();

        if (!empty($result))
        {
            $merchant_center_address = (!empty($result->address_1)) ? $result->address_1 : "";
            $merchant_center_address .= (!empty($result->city)) ? ", ".$result->city : "";
            $merchant_center_address .= (!empty($result->district)) ? ", ".$result->district : "";
            $merchant_center_address .= (!empty($result->state)) ? ", ".$result->state : "";
            $merchant_center_address .= (!empty($result->zip_code)) ? " ".$result->zip_code : "";
            $merchant_center_address .= (!empty($result->landmark)) ? " near ".$result->landmark : "";
            $pickup_address_coordinates = $this->get_coordinates_by_address($merchant_center_address);
        }

        return (!empty($pickup_address_coordinates)) ? json_encode($pickup_address_coordinates) : NULL;
    }

    public function get_coordinates_by_address($address)
	{
		$API_KEY = "AIzaSyD5x9Edhus783sARyDVXSwTr26kIQjiqOo";
		if (!empty($address))
		{
			$url_encoded_address = urlencode($address);
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$url_encoded_address."&key=".$API_KEY;
			$curl_handler = curl_init($url);
			curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);
			$response = json_decode(curl_exec($curl_handler));
			curl_close($curl_handler);

			if (!empty($response->results[0]->geometry->location))
			{
				return $response->results[0]->geometry->location;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
}
