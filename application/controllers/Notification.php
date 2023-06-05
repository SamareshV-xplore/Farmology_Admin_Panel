<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {  

    function index()
    {
        reditect(FRONT_URL);
    }

    function send_notification($order_no = "")
    {   

        $response = $this->notification_model->send_notification($order_no);
        echo json_encode($response);
        
    }

    function send_push_notification()
    {
    	$data = array();
    	
    	$data = $this->input->post();
    	/*echo '<pre>';
    	print_r($data);die();*/
    	$response = $this->notification_model->send_push_android_notification($data);
        echo json_encode($response);
    }

    function send_push_notification_android()
    {
        $data = array();
        
        $data = $this->input->post();
        /*echo '<pre>';
        print_r($data);die();*/
        $response = $this->notification_model->send_push_android_notification_referral($data);
        echo json_encode($response);
    }

    public function sendPush()
    {
        $data = array();
        
        $data = $this->input->post();
        /*echo '<pre>';
        print_r($data);die();*/
        $response = $this->notification_model->send_push_message($data);
        echo json_encode($response);
    }
}
