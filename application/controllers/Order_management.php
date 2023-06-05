<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_management extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('push_model');
    }

    //Banner List
    public function index()
    {
        // banner list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();
        $start_date = array('start_date' => '');
        $end_date = array('end_date' => '');

        $header_data['title'] = "Orders List";
        $left_data['navigation'] = "orders";
        $left_data['sub_navigation'] = "orders-list";

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

        if(isset($_REQUEST['filter']))
        {
            $filter_data = array("status" => $_REQUEST['status']);
        }
        else
        {
            $filter_data = array("status" => 'all');
        }

        if(isset($_REQUEST['start_date']))
        {
            $start_date['start_date'] = $_REQUEST['start_date'];
        }

        if(isset($_REQUEST['end_date']))
        {
            $end_date['end_date'] = $_REQUEST['end_date'];
        }

        $page_data['filter_data'] = $filter_data;
        $page_data['start_date'] = $start_date;
        $page_data['end_date'] = $end_date;
        //print_r($start_date);exit;

        // get banner list
        $orders_list = $this->orders_model->orders_list($filter_data, $start_date['start_date'], $end_date['end_date']);
        $page_data['orders_list'] = $orders_list;
        /*echo '<pre>';
        print_r($orders_list);exit;*/

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('orders/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    //Order Edit page
    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Order Details";
        $left_data['navigation'] = "orders";
        $left_data['sub_navigation'] = "orders-edit";

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

        $order_data = $this->orders_model->order_by_id($id);

        if($order_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Order details not found. Something went wrong.');
            redirect(base_url('orders-list'));
        }
        else
        {
            $page_data["order_details"] = $order_data["details"];
        }
        /*echo '<pre>';
        print_r($order_data);
        exit;*/

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('orders/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    // Order Update
    function order_update()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        if($this->input->post('order_id'))
        {

            $form_data = array();
            $order_id = $this->input->post('order_id');
            $order_no = $this->input->post('order_no');
            $form_data['order_id'] = $this->input->post('order_id');
            $form_data['status'] = $this->input->post('status');

            $attachment_file = FILE_UPLOAD_BASE_PATH.'uploads/orders/invoice_'.$order_no.'.pdf';

            $update_data = $this->orders_model->update_order($form_data);
            if($update_data['status'] == "Y")
            {
                if($this->input->post('status') == "D"){
                    if(!file_exists($attachment_file)){
                        $this->save_email_invoice($this->input->post('order_id'));
                    }
                    $user_data = $this->orders_model->get_user_details($order_id);
                    if($user_data['status'] == "Y"){
                        $email_subject = 'Flesh Kart-Order Delivered';
                        $email_body = "<p> Hello, ".$user_data['details']['first_name']."</p>";
                        $email_body.= "<p> Your order has been delivered, </p>";
                        $email_body.= "<p> Please find the attachment for billing details. </p>";
                        $this->common_model->email_send($user_data['details']['email'], $email_subject, $email_body, $attachment_file);
                        $user_id = array($user_data['details']['id']);
                        $message = 'Your order has been delivered';
                        $push_details = $this->push_model->find_device_by_id($user_id);
                        $this->common_model->manage_notification($user_id, $message);
                    }
                }
                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect(base_url('orders-list'));
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect(base_url('orders-edit/'.$order_id));
            }

        }
        else
        {
            redirect(base_url(''));
        }
    }

    

    function save_email_invoice($id){
        $order_data = $this->orders_model->order_by_id($id);

        if($order_data["status"] == "Y")
        {
            //$invoice_id = $this->orders_model->check_invoice();
            $invoice_id = $id;
            $invoice_length = strlen((string)$invoice_id);
            $invoice = '';
            //echo $invoice_count;exit;
            switch ($invoice_length) {
                case 0:
                    $invoice = '#00001';
                    break;
                case 1:
                    $invoice = '#0000'.($invoice_id);
                    break;
                case 2:
                    $invoice = '#000'.($invoice_id);
                    break;
                case 3:
                    $invoice = '#00'.($invoice_id);
                    break;
                case 4:
                    $invoice = '#0'.($invoice_id);
                    break;
                default:
                    $invoice = '#'.($invoice_id);
            }
            $order_data = $this->orders_model->order_by_id($id);

            $page_data["order_details"] = $order_data["details"];
            $page_data["invoice"] = $invoice;

            $html = $this->load->view('orders/invoice_print', $page_data, true);

            //$html = $this->output->get_output();

            include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');

            $pdf = new \Mpdf\Mpdf();
            $pdf->AddPage();
            $pdf->WriteHTML($html);
            $payslip_url = FILE_UPLOAD_BASE_PATH.'uploads/orders/invoice_'.$order_data['order_no'].'.pdf';
            $content = $pdf->Output($payslip_url,'F');
            return true;
        }
    }

    function view_invoice($id = 0){
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "View Invoice";
        $left_data['navigation'] = "orders";
        $left_data['sub_navigation'] = "orders-edit";

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

        /*$invoice_id = $this->orders_model->check_invoice();
        $invoice_length = strlen((string)$invoice_id);*/
        //echo $invoice_count;exit;

        $order_data = $this->orders_model->order_by_id($id);

        if($order_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Order details not found. Something went wrong.');
            redirect(base_url('orders-list'));
        }
        else
        {
            $invoice = '';
            $invoice_id = $order_data['details']['id'];
            $invoice_length = strlen((string)$invoice_id);
            switch ($invoice_length) {
                case 0:
                    $invoice = '#00001';
                    break;
                case 1:
                    $invoice = '#0000'.($invoice_id);
                    break;
                case 2:
                    $invoice = '#000'.($invoice_id);
                    break;
                case 3:
                    $invoice = '#00'.($invoice_id);
                    break;
                case 4:
                    $invoice = '#0'.($invoice_id);
                    break;
                default:
                    $invoice = '#'.($invoice_id);
            }
            $page_data["order_details"] = $order_data["details"];
            $page_data["invoice"] = $invoice;
        }
        /*echo '<pre>';
        print_r($order_data);
        exit;*/

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('orders/invoice_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function download_invoice($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "View Invoice";
        $left_data['navigation'] = "orders";
        $left_data['sub_navigation'] = "orders-edit";

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

        $invoice_id = $this->orders_model->check_invoice();
        $invoice_length = strlen((string)$invoice_id);
        $invoice = '';
        //echo $invoice_count;exit;
        switch ($invoice_length) {
            case 0:
                $invoice = '#00001';
                break;
            case 1:
                $invoice = '#0000'.($invoice_id + 1);
                break;
            case 2:
                $invoice = '#000'.($invoice_id + 1);
                break;
            case 3:
                $invoice = '#00'.($invoice_id + 1);
                break;
            case 4:
                $invoice = '#0'.($invoice_id + 1);
                break;
            default:
                $invoice = '#'.($invoice_id + 1);
        }
        $order_data = $this->orders_model->order_by_id($id);

        if($order_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Order details not found. Something went wrong.');
            redirect(base_url('orders-list'));
        }
        else
        {
            $page_data["order_details"] = $order_data["details"];
            $page_data["invoice"] = $invoice;
        }

        $html = $this->load->view('orders/invoice_print', $page_data, true);

        //$html = $this->output->get_output();

        include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');

        $pdf = new \Mpdf\Mpdf();
        $pdf->AddPage();

        /*$stylesheet1 = file_get_contents(ASSETS_URL.'bower_components/bootstrap/dist/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents(ASSETS_URL.'bower_components/font-awesome/css/font-awesome.min.css');
        $stylesheet3 = file_get_contents(ASSETS_URL.'dist/css/AdminLTE.min.css');

        $pdf->WriteHTML($stylesheet1, \Mpdf\HTMLParserMode::HEADER_CSS);
        $pdf->WriteHTML($stylesheet2, \Mpdf\HTMLParserMode::HEADER_CSS);
        $pdf->WriteHTML($stylesheet3, \Mpdf\HTMLParserMode::HEADER_CSS);*/

        $pdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);


        $payslip_url = FILE_UPLOAD_BASE_PATH.'uploads/orders/invoice_'.$order_data['order_no'].'.pdf';
        $content = $pdf->Output($payslip_url,'F');
        $content = $pdf->Output($payslip_url,'D');
        redirect(base_url('orders-invoice/'.$id));
    }

    function invoice_by_order_number($order_number) {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "View Invoice";
        $left_data['navigation'] = "orders";
        $left_data['sub_navigation'] = "orders-edit";

        if(empty($order_number)){
            exit;
        }

        $order_data = $this->orders_model->order_by_order_number($order_number);
        /*echo '<pre>';
        print_r($order_data);exit;*/
        if($order_data['status'] == 'Y'){
            $invoice_id = $order_data['details']['id'];
            $invoice_length = strlen((string)$invoice_id);
            $invoice = '';
            //echo $invoice_count;exit;
            switch ($invoice_length) {
                case 0:
                    $invoice = '#00001';
                    break;
                case 1:
                    $invoice = '#0000'.($invoice_id + 1);
                    break;
                case 2:
                    $invoice = '#000'.($invoice_id + 1);
                    break;
                case 3:
                    $invoice = '#00'.($invoice_id + 1);
                    break;
                case 4:
                    $invoice = '#0'.($invoice_id + 1);
                    break;
                default:
                    $invoice = '#'.($invoice_id + 1);
            }


            if($order_data["status"] == "N")
            {
                $this->session->set_flashdata('error_message', 'Order details not found. Something went wrong.');
                redirect(base_url('orders-list'));
            }
            else
            {
                $page_data["order_details"] = $order_data["details"];
                $page_data["invoice"] = $invoice;
            }

            $html = $this->load->view('orders/invoice_print', $page_data, true);

            //$html = $this->output->get_output();

            include_once (APPPATH.'third_party/mpdf/vendor/autoload.php');

            $pdf = new \Mpdf\Mpdf();
            $pdf->AddPage();

            /*$stylesheet1 = file_get_contents(ASSETS_URL.'bower_components/bootstrap/dist/css/bootstrap.min.css');
            $stylesheet2 = file_get_contents(ASSETS_URL.'bower_components/font-awesome/css/font-awesome.min.css');
            $stylesheet3 = file_get_contents(ASSETS_URL.'dist/css/AdminLTE.min.css');*/

            /*$pdf->WriteHTML($stylesheet1, \Mpdf\HTMLParserMode::HEADER_CSS);
            $pdf->WriteHTML($stylesheet2, \Mpdf\HTMLParserMode::HEADER_CSS);
            $pdf->WriteHTML($stylesheet3, \Mpdf\HTMLParserMode::HEADER_CSS);*/

            $pdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);


            $payslip_url = FILE_UPLOAD_BASE_PATH.'uploads/orders/invoice_'.$order_data['order_no'].'.pdf';
            $content = $pdf->Output($payslip_url,'F');
            $content = $pdf->Output($payslip_url,'D');
            exit;
        }else{
            exit;
        }
    }

    function export_order(){
        // check login or not
        if($this->common_model->user_login_check())
        {
            if($this->input->post('export_data') && $this->input->post('export_data') == 'y') {
                $form_data = array();
                $form_data['start_date'] = $this->input->post('order_start_date');
                $form_data['end_date'] = $this->input->post('order_end_date');

                //$order_data = $this->orders_model->find_all_orders($form_data);
                /*echo '<pre>';
                print_r($order_data);
                exit;*/

                // create file name
                $fileName = 'data-'.time().'.xlsx';
                //fopen($fileName, "w");
                // load excel library
                $this->load->library('excel');
                $listInfo = $this->orders_model->find_all_orders($form_data);
                /*echo '<pre>';
                print_r($listInfo['details']);exit;*/
                $objPHPExcel = new PHPExcel();

                $objPHPExcel->setActiveSheetIndex(0);
                // set Header
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Order Date');
                $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
                $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Order No');
                $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Transaction ID');
                $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Total Price');
                $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Discount');
                $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Total Orders');
                $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Delivery Charge');
                $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Payment Method');
                $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Products List');
                $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Customer Name');
                $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Phone');
                $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Email');
                $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Address');
                $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Zip Code');
                $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'City Name');
                $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'State Name');
                $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Status');
                $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Last Updated');
                // set Row
                $rowCount = 2;
                foreach ($listInfo['details'] as $list) {
                    $all_products = array();
                    foreach ($list['products'] as $product){
                        $all_products[] = 'SKU: '.$product['sku'].', Product Name: '.$product['title'].', Quantity: '.$product['quantity'].', Product Price: '.$product['product_price'].', Product Discount: '.$product['product_discount'];
                    }
                    $product_output = implode(" ", $all_products);
                    /*echo '<pre>';
                    print_r($list);exit;*/
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $list['created_date']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $list['id']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $list['order_no']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $list['transaction_id']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $list['total_price']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $list['discount']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $list['order_total']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $list['delivery_charge']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $list['payment_method']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $product_output);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $list['customer_name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $list['phone']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $list['email']);
                    $objPHPExcel->getActiveSheet()->SetCellValue(
                        'N' . $rowCount,
                        $list['address1'].','.$list['address2'].','.$list['landmark']
                    );
                    $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $list['zip_code']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $list['city_name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $list['state_name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $list['status']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $list['updated_date']);
                    $rowCount++;
                }




                PHPExcel_Calculation::getInstance($objPHPExcel)->clearCalculationCache();
                PHPExcel_Calculation::getInstance($objPHPExcel)->disableCalculationCache();
                PHPExcel_Calculation::getInstance($objPHPExcel)->setCalculationCacheEnabled(false);
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save(FILE_UPLOAD_BASE_PATH.'uploads/orders/'.$fileName);
                // download file
                //header("Content-Type: application/vnd.ms-excel");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=file.xls");
                header("Cache-Control: cache, must-revalidate");
                header("Pragma: public");
                header("Expires: 0");

                redirect(FRONT_URL.'uploads/orders/'.$fileName);

            }else{
                redirect(base_url('orders-list'));
            }
        }
        else
        {
            redirect(base_url(''));
        }
    }

    function test()
    {
        $postData = array(
           'order_no' => 'FT1104208'
           );

          // Setup cURL
          $ch = curl_init('http://localhost/fleshkart_new/ajax/send_notification_by_order_no/1');
          curl_setopt_array($ch, array(
          CURLOPT_POST => TRUE,
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'
         ),
          CURLOPT_POSTFIELDS => json_encode($postData)
        ));

         // Send the request

           $response = curl_exec($ch);
           echo "<pre>";
           print_r($response);
           echo "</pre>";
    }
}
