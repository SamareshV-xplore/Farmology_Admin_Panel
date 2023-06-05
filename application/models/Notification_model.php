<?php
class Notification_model extends CI_Model
{
    function send_notification($order_no = "")
    {
        // get order_details
        $this->db->select("status, customer_id, invoice");
        $this->db->from("FM_order");
        $this->db->where("order_no", $order_no);
        $query = $this->db->get();
        //$this->db->last_query(); 
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $order_status = $row->status;
            $customer_id = $row->customer_id;

            $user_details = $this->common_model->user_details_by_id($customer_id);
            $customer_name = $user_details['full_name'];
            

            //generate invoice
            $this->invoice_model->generate_invoice_new($order_no);

            // get order details
            $order_details = $this->order_model->order_details_by_no($order_no);          
            /*echo '<pre>';
            print_r($order_details['order_total']);
            die();*/

            if($order_status == "NOP")
            {
                // push notification start----------------------

                $notification_subject = "Failed to place order.";
                $notification_body = "You have failed to place order.";
                // store notification
                $store_notification_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_body,"notification_type" => 'Order', "redirection_id" => $order_no);
                $store_notification = $this->store_notification($store_notification_data);
                // send push notification 
                $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_body,"notification_type" => 'Order',"redirection_id" => $order_no);
                $this->send_push_notification($push_data);

                // push notification end----------------------

                // send email to admin    
                $email_data = array("order_details" => $order_details, "status" => $order_status);
                $email_body = $this->load->view('email-template/admin_failed_order_email_view', $email_data, true);
                $customer_email = $order_details['customer_details']['email'];
                $email_subject = "Order Failed! Order ID: ".$order_no;
                $email_cc = ADMIN_CC_EMAIL;
                //$email_cc = "";
                $send_email = $this->common_model->email_send(ADMIN_EMAIL, $email_subject, $email_body, $email_cc);  
                //$send_email = $this->common_model->email_send('koushik.techpro@gmail.com', $email_subject, $email_body, $email_cc); 


                 $response = array("status" => "Y", "message" => "Notification sent for NOP");
            }
            else if($order_status == "P")
            {
                // push notification start----------------------
               
                $notification_subject = "Order successfully placed.";
                $notification_body = "Order successfully placed. Order ID:".$order_no;
                 // store notification
                $store_notification_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_body ,"notification_type" => 'Order', "redirection_id" => $order_no);
                $store_notification = $this->store_notification($store_notification_data);
                // send push notification 
                $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_body,"notification_type" => 'Order',"redirection_id" => $order_no);
                $this->send_push_notification($push_data);

                // push notification end----------------------

                if($order_details['customer_details']['email'] != '')
                {
                    // email notification start --------------------
                    // send email to user    
                    $email_data = array("order_details" => $order_details, "status" => $order_status);
                    $email_body = $this->load->view('email-template/order_email_view', $email_data, true);
                    $customer_email = $order_details['customer_details']['email'];
                    $email_subject = "Order Placed: Your Order ".$order_no." is successfully placed!";
                    $send_email = $this->common_model->email_send($customer_email, $email_subject, $email_body);

                                        


                // email notification end -------------------

                }   

                // send sms to admin start ---------
                /*$phone_no = $order_details['customer_details']['phone'];                
                $text = "You have received a new order with id #".$order_no." and order value Rs #".$order_details['order_total']."";
                $text = urlencode($text);
                $url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".ADMIN_PHONE."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text;
                
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data_response = curl_exec($ch);
                curl_close($ch);*/

                $phone_no = '91'.ADMIN_PHONE;
                $json = array('flow_id' => '609e313ccb3731648072ddc3','mobiles' => $phone_no, 'orderno' => $order_no, 'orderamount' => $order_details['order_total'], 'user' => $customer_name);
                $json = json_encode($json);
                $ch = curl_init();

                curl_setopt_array($ch, array(
                  CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $json,
                  CURLOPT_HTTPHEADER => array(
                    "authkey: 335354AMUyfpp0uQ5f097111P1",
                    "content-type: application/JSON"
                  ),
                ));

                $response = curl_exec($ch);
                $err = curl_error($ch);

                curl_close($ch);


                // send sms to admin start ---------

                // send email to admin    
                $email_data = array("order_details" => $order_details, "status" => $order_status);
                $email_body = $this->load->view('email-template/admin_order_email_view', $email_data, true);
                $customer_email = $order_details['customer_details']['email'];
                $email_subject = "New Order Placed: Order ID: ".$order_no;
                $email_cc = ADMIN_CC_EMAIL;
                //$email_cc = "";
                $send_email = $this->common_model->email_send(ADMIN_EMAIL, $email_subject, $email_body, $email_cc);  
                //$send_email = $this->common_model->email_send('koushik.techpro@gmail.com', $email_subject, $email_body, $email_cc); 
                
                // send sms start ---------
                if (!empty($order_details["customer_details"]["phone"])) {
                    $phone_no = $order_details['customer_details']['phone'];
                    $text = "Farmology.com: we have received your order #".$order_no." with value Rs #".$order_details['order_total'].". Expected delivery date & time; ".$order_details['delivery_date'].", ".$order_details['time_slot_details']['time_slot'].". Thank you!";
                    $text = urlencode($text);
                    $url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$phone_no."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text;
                    
                    $ch  = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data_response = curl_exec($ch);
                    curl_close($ch);
                }
                /*$data_response = json_decode($data_response);
                $data_response = json_decode(json_encode($data_response), true);*/


                // send sms end ---------

                

                $response = array("status" => "Y", "message" => "Notification sent for P");

            }
            else if($order_status == "S")
            {
                // push notification start----------------------

                $notification_subject = "Order out for delivery";
                $notification_body = "Order: '".$order_no."' out for delivery.";
                 // store notification
                $store_notification_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_body , "notification_type" => 'Order', "redirection_id" => $order_no);
                $store_notification = $this->store_notification($store_notification_data);
                // send push notification 
                $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_body,"notification_type" => 'Order',"redirection_id" => $order_no);
                $this->send_push_notification($push_data);

                // push notification end----------------------

                //send sms start ---------
                /*$phone_no = $order_details['customer_details']['phone'];
                $text = "Hi, your order #".$order_no." has been out for delivery.";
                $text = urlencode($text);
                $url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$phone_no."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text;
                
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data_response = curl_exec($ch);
                curl_close($ch);*/

                if (!empty($order_details["customer_details"]["phone"])) {
                    $phone_no = '91'.$order_details['customer_details']['phone'];
                    $json = array('flow_id' => '609925ca5444c91fc107fa22','mobiles' => $phone_no, 'orderno' => $order_no, 'orderamount' => $order_details['order_total']);
                    $json = json_encode($json);
                    $ch = curl_init();
    
                    curl_setopt_array($ch, array(
                      CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $json,
                      CURLOPT_HTTPHEADER => array(
                        "authkey: 335354AMUyfpp0uQ5f097111P1",
                        "content-type: application/JSON"
                      ),
                    ));
    
                    $response = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);
                }

                // send sms end ---------

                $response = array("status" => "Y", "message" => "Notification sent for S");

            }
            else if($order_status == "D")
            {
                // push notification start----------------------

                $notification_subject = "Order is complete.";
                $notification_body = "Order: '".$order_no."' successfully deliverd.";
                 // store notification
                $store_notification_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_body , "notification_type" => 'Order', "redirection_id" => $order_no);
                $store_notification = $this->store_notification($store_notification_data);
                // send push notification 
                $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_body,"notification_type" => 'Order',"redirection_id" => $order_no);
                $this->send_push_notification($push_data);

                // push notification end----------------------

               if($order_details['customer_details']['email'] != '')
                {
                    // email notification start --------------------
                    $attachment = FILE_UPLOAD_BASE_PATH.$order_details['invoice'];
                    $email_data = array("order_details" => $order_details, "status" => $order_status);
                    $email_body = $this->load->view('email-template/order_email_view', $email_data, true);
                    $customer_email = $order_details['customer_details']['email'];
                    $email_subject = "Order Complete: You Order ".$order_no." is successfully completed!";
                    $send_email = $this->common_model->email_send_with_attachment($customer_email, $email_subject, $email_body, $attachment);
                    
                // email notification end -------------------

                }   


                // send sms start ---------
                /*$phone_no = $order_details['customer_details']['phone'];
                $text = "Hi, your order #".$order_no." with Farmology.com has been delivered and completed successfully.";
                $text = urlencode($text);
                $url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$phone_no."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text;
                
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data_response = curl_exec($ch);
                curl_close($ch);*/

                if (!empty($order_details["customer_details"]["phone"])) {
                    $phone_no = '91'.$order_details['customer_details']['phone'];
                    $json = array('flow_id' => '6099264ee9152427dc275a43','mobiles' => $phone_no, 'orderno' => $order_no);
                    $json = json_encode($json);
                    $ch = curl_init();

                    curl_setopt_array($ch, array(
                    CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $json,
                    CURLOPT_HTTPHEADER => array(
                        "authkey: 335354AMUyfpp0uQ5f097111P1",
                        "content-type: application/JSON"
                    ),
                    ));

                    $response = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);
                }

                /*$data_response = json_decode($data_response);
                $data_response = json_decode(json_encode($data_response), true);*/


                // send sms end ---------

                $response = array("status" => "Y", "message" => "Notification sent for D");

            }
            else if($order_status == "C")
            {
                // push notification start----------------------

                $notification_subject = "Order is cancelled.";
                $notification_body = "Order: '".$order_no."' is cancelled.";
                 // store notification
                $store_notification_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_body , "notification_type" => 'Order', "redirection_id" => $order_no);
                $store_notification = $this->store_notification($store_notification_data);
                // send push notification 
                $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_body,"notification_type" => 'Order',"redirection_id" => $order_no);
                $this->send_push_notification($push_data);

                // push notification end----------------------

                if($order_details['customer_details']['email'] != '')
                {
                    // email notification start --------------------

                    $email_data = array("order_details" => $order_details, "status" => $order_status);
                    $email_body = $this->load->view('email-template/order_email_view', $email_data, true);
                    $customer_email = $order_details['customer_details']['email'];
                    $email_subject = "Order Cancelled: You Order ".$order_no." is successfully cancelled!";
                    $send_email = $this->common_model->email_send($customer_email, $email_subject, $email_body);                   


                // email notification end -------------------

                }   

                // send sms start ---------
                /*$phone_no = $order_details['customer_details']['phone'];
                $text = "Hi, your order #".$order_no." has been cancelled due to un-avoidable conditions. We apologize for the inconvenience caused.";
                $text = urlencode($text);
                $url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$phone_no."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text;
                
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data_response = curl_exec($ch);
                curl_close($ch);*/
                
                if (!empty($order_details["customer_details"]["phone"])) {
                    $phone_no = '91'.$order_details['customer_details']['phone'];
                    $json = array('flow_id' => '6099082073c31a75216d0a53','mobiles' => $phone_no, 'order' => $order_no);
                    $json = json_encode($json);
                    $ch = curl_init();

                    curl_setopt_array($ch, array(
                    CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $json,
                    CURLOPT_HTTPHEADER => array(
                        "authkey: 335354AMUyfpp0uQ5f097111P1",
                        "content-type: application/JSON"
                    ),
                    ));

                    $response = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);
                }

                
                /*$data_response = json_decode($data_response);
                $data_response = json_decode(json_encode($data_response), true);*/

                $response = array("status" => "Y", "message" => "Notification sent for C");

            }
            else
            {
                $response = array("status" => "N", "message" => "Invalid order status");
            }
        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid order id");
        }

        return $response;
    }

    public function send_delivery_driver_assigned_SMS_and_Notification($order_no)
    {
        $customer_sms = $delivery_driver_sms = "";

        $sql = "SELECT DD.name AS delivery_driver_name, DD.phone AS delivery_driver_mobile_number, CONCAT(C.first_name, ' ', C.last_name) AS customer_name, C.phone AS customer_mobile_number, CDD.device_token AS customer_device_token, O.address_id, O.delivery_date, O.delivery_time_slot FROM FM_order O INNER JOIN FM_delivery_drivers DD ON DD.driver_id = O.delivery_driver_id COLLATE utf8_unicode_ci INNER JOIN FM_customer C ON C.id = O.customer_id INNER JOIN FM_customer_device_details CDD ON CDD.customer_id = O.customer_id WHERE O.order_no = '$order_no' AND O.status NOT IN ('NOP','D','C')";
        $details = $this->db->query($sql)->row();

        if (!empty($details))
        {
            $order_number = $order_no;
            $delivery_driver_name = $details->delivery_driver_name;
            $delivery_driver_mobile_number = $details->delivery_driver_mobile_number;

            $customer_name = $details->customer_name;
            $customer_mobile_number = $details->customer_mobile_number;

            if (!empty($details->address_id))
            {
                $delivery_address = $this->get_delivery_address_details($details->address_id);
            }
            else
            {
                $delivery_address = "";
            }

            if (!empty($details->delivery_date))
            {
                $delivery_date = date("jS F Y", strtotime($details->delivery_date));
            }
            else
            {
                $delivery_date = "";
            }

            if (!empty($details->delivery_time_slot))
            {
                $delivery_time = $this->get_delivery_time($details->delivery_time_slot);
            }
            else
            {
                $delivery_time = "";
            }

            $customer_sms = "Your order #".$order_number." will be delivered by ".$delivery_driver_name." on ".$delivery_date.$delivery_time.". You can reach him/her by calling on ".$delivery_driver_mobile_number.".";
        }

        $customer_SMS_sending_response = $this->send_sms($customer_mobile_number, $customer_sms);

        return ["customer_SMS" => $customer_sms, "customer_SMS_sending_response" => $customer_SMS_sending_response];
    }

    // COMMON MOBILE SMS SENDING FUNCTION //
    function send_sms($mobile_number, $message)
    {              
        $message = urlencode($message);
        $url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$mobile_number."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$message;
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function get_delivery_address_details($address_id)
    {
        $delivery_address = "";

        $delivery_address_details = $this->db->get_where("FM_customer_address", ["id" => $address_id])->row();
        if (!empty($delivery_address_details))
        {
            $delivery_address = $delivery_address_details->address_1;
            if (!empty($delivery_address_details->zip_code))
            {
                $delivery_address .= " PIN CODE: ".$delivery_address_details->zip_code;
            }
            if (!empty($delivery_address_details->landmark))
            {
                $delivery_address .= " near ".$delivery_address_details->landmark;
            }
        }
        
        return $delivery_address;
    }

    public function get_delivery_time($timeslot_id)
    {
        $delivery_time = "";

        $delivery_timeslot_details = $this->db->get_where("FM_delivery_time_slot", ["id" => $timeslot_id])->row();
        if (!empty($delivery_timeslot_details))
        {
            if (!empty($delivery_timeslot_details->start_time))
            {
                $start_time = $this->convert_time_number_into_text($delivery_timeslot_details->start_time);
            }
            
            if (!empty($delivery_timeslot_details->end_time))
            {
                $end_time = $this->convert_time_number_into_text($delivery_timeslot_details->end_time);
            }
        }

        if (!empty($start_time) && !empty($end_time))
        {
            $delivery_time = " between ".$start_time." to ".$end_time;
        }
        
        return $delivery_time;
    }

    public function convert_time_number_into_text($time)
    {
        $text = "";

        if ($time > 12)
        {
            $text = intval($time) - 12;
            $text = $text." PM";
        }
        elseif ($time == 12)
        {
            $text = $time." PM";
        }
        elseif ($time < 12)
        {
            $text =  $time." AM";
        }

        return $text;
    }

    function send_push_android_notification($data)
    {
        $customer_id = $data['customer_id'];
        $notification_subject = $data['notification_subject'];
        $notification_text = $data['notification_text'];
       // if(!empty($data['notification_type']) && !empty($data['redirection_id'])){
            $notification_type = $data['notification_type'];
            $redirection_id = $data['redirection_id'];
        /*}else{
            $notification_type = '';
            $redirection_id = '';
        }*/    

        $insert_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_text, "notification_type" => $notification_type, "redirection_id" => $redirection_id, "created_date" => date("Y-m-d H:i:s"));

        $this->db->insert("FM_customer_notification", $insert_data);

        $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_text, "notification_type" => $notification_type,"redirection_id" => $redirection_id);
        $this->send_push_notification($push_data);
        $text = urlencode($notification_text);

        if($notification_type == 'Community'){
            $current_user_name = $data['current_user_name'];
            $quesstion = $data['quesstion'];
            $phone_no = '91'.$data['phone_no'];
            $json = array('flow_id' => '6099275a8c327c4d1e20f9b2','mobiles' => $phone_no, 'quesstion' => $quesstion, 'user' => $current_user_name);
            $json = json_encode($json);
        }else if($notification_type == 'Enquiry' && $notification_subject == 'Your question answer.'){
            $answer = $data['answer'];
            $quesstion = $data['quesstion'];
            $phone_no = '91'.$data['phone_no'];
            $json = array('flow_id' => '609e27c4132aa1045e128406','mobiles' => $phone_no, 'quesstion' => $quesstion, 'answer' => $answer);
            $json = json_encode($json);
        }else if($notification_type == "Enquiry" && $notification_subject == "Your question's answer given by admin."){
            $phone_no = '91'.$data['phone_no'];
            $json = array('flow_id' => '609e28183b37e3275a506f93','mobiles' => $phone_no);
            $json = json_encode($json);
        }else if($notification_type == "Sell Produce"){
            $phone_no = '91'.$data['phone_no'];
            $json = array('flow_id' => '609e2722b611613ed455e8d1','mobiles' => $phone_no);
            $json = json_encode($json);
        }    
            $ch = curl_init();

            curl_setopt_array($ch, array(
              CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => $json,
              CURLOPT_HTTPHEADER => array(
                "authkey: 335354AMUyfpp0uQ5f097111P1",
                "content-type: application/JSON"
              ),
            ));

            $response = curl_exec($ch);
            $err = curl_error($ch);

            curl_close($ch);
        /*}else{
            $phone_no = $data['phone_no'];
            $url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$phone_no."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text;
                    
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data_response = curl_exec($ch);
            curl_close($ch);
        }*/    

        $response = array("status" => "Y", "message" => "Notification sent for P");
        return $response;
    }

    function send_push_android_notification_referral($data)
    {

        $customer_id = $data['customer_id'];
        $notification_subject = $data['notification_subject'];
        $notification_text = $data['notification_text'];

            $notification_type = '';
            $redirection_id = '';
        

        $insert_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_text, "created_date" => date("Y-m-d H:i:s"));

        $this->db->insert("FM_customer_notification", $insert_data);

        $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_text, "notification_type" => $notification_type,"redirection_id" => $redirection_id);
        $this->send_push_notification($push_data);

        $response = array("status" => "Y", "message" => "Notification sent for P");
        return $response;
    }

    function store_notification($data)
    {
        $customer_id = $data['customer_id'];
        $notification_subject = $data['notification_subject'];
        $notification_text = $data['notification_text'];
        $notification_type = $data['notification_type'];
        $redirection_id = $data['redirection_id'];

        $insert_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_text, "notification_type" => $notification_type, "redirection_id" => $redirection_id, "created_date" => date("Y-m-d H:i:s"));

        $this->db->insert("FM_customer_notification", $insert_data);

        return true;
    }

    function send_push_notification($data)
    {
        $customer_id = $data['customer_id'];
        $subject = $data['subject'];
        $message = $data['message'];
        $notification_type = $data['notification_type'];
        $redirection_id = $data['redirection_id'];

        // find device type
        $this->db->select("*");
        $this->db->from("FM_customer_device_details");
        $this->db->where("customer_id", $customer_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $device_token = $row->device_token;
            if($row->device_type == "A")
            {
                // android
                $push_data = array("device_token" => $device_token, "subject" => $subject, "message" => $message ,"notification_type" => $notification_type, "redirection_id" => $redirection_id);
                if (!empty($row->app_version) && $row->app_version >= 2) {
                    $this->send_push_android_for_new_users($push_data);
                }
                else {
                    $this->send_push_android($push_data);
                }
                return true;
            }
            else if($row->device_type == "I")
            {
                // ios
                $push_data = array("device_token" => $device_token, "subject" => $subject, "message" => $message);
                $this->send_push_IOS($push_data);
                return true;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    function send_push_notification_arr($data)
    {
        $android_token_arr = array();
        $ios_token_arr = array();
        $customer_id = $data['customer_id'];
        $subject = $data['subject'];
        $message = $data['message'];
        $notification_type = $data['notification_type'];
        $redirection_id = $data['redirection_id'];

        // find device type
        $this->db->select("*");
        $this->db->from("FM_customer_device_details");

        $customer_ids_chunk = array_chunk($customer_id,25);
        foreach($customer_ids_chunk as $customer_ids)
        {
            $this->db->or_where_in('customer_id', $customer_ids);
        }        
        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        if($query->num_rows() > 0)
        {
            $android_token = array();
            $ios_token = array();
            $android_token_count = 0;
            $ios_token_count = 0;
            foreach($query->result() as $row)
            {
                $device_token = $row->device_token;
                if($row->device_type == "A")
                {
                    if($android_token_count == multipush_limit){
                        $android_token_count = 1;
                        $android_token_arr[] = $android_token; 
                        $android_token = array();
                        $android_token[] = $device_token;
                        
                    }else{
                        $android_token[] = $device_token;
                        $android_token_count ++;

                    }
                    
                    // android                

                }
                else if($row->device_type == "I")
                {
                    $ios_token[] = $device_token;
                    // ios                
                    
                }
            }
            if($android_token_count != 1){
                $android_token_arr[] = $android_token;  
            }
            
            
        }
        /*echo '<pre>';
        print_r($android_token_arr);
        die();*/

        if(count($android_token_arr) > 0)
        {
            $push_data = array("device_token" => $android_token_arr, "subject" => $subject, "message" => $message,"notification_type" => $notification_type, "redirection_id" => $redirection_id);
            $this->send_push_android_multiple($push_data);
            
                        
        } 

        /*if(count($ios_token) > 0)
        {
            $push_data = array("device_token" => $ios_token, "subject" => $subject, "message" => $message);
            $this->send_push_IOS_multiple($push_data);
            
        }*/

        return true;
    }

    function send_push_android_multiple($data)
    {
        // send for previous version app

        foreach ($data['device_token'] as $device_token_arr) {
            $device_token = $device_token_arr;
            $subject = $data['subject'];
            $message = $data['message'];
            $notification_type = $data['notification_type'];
            $redirection_id = $data['redirection_id'];

            $type = 0;
            $url = "https://fcm.googleapis.com/fcm/send";
            $title = $subject;
            $body = $message;
            $token =  $device_token;

             

            $serverKey = 'AAAA3PV8wF0:APA91bEtzE-aMDPpt9p9xdacBSBxyRyVG7egubTipVOwLRdClf7FvQPzW1NeoVuKO5hE87yT9AnnRFAx9NIkEibEfe9_tMos0wUwB0Oa58I0CAwAk-PJkYwTGVeZdTizr9iu9Oqoo63P';
            

            //$data_arr = array("title" => $title , "body" => $body, "icon" => base_url("assets/dist/img/app_icon.png"), "type" => $type);

            $data_arr = array("title" => $title , "body" => $body, "icon" => base_url("assets/dist/img/app_icon.png"), "type" => $type, "notification_type" => $notification_type, "redirection_id" => $redirection_id);
            //$notification = array('click_action' => '.DashboardActivity', 'title' => $title , 'body' => $body, 'sound' => 'default', 'badge' => '1', 'data' => 'default', 'icon' => base_url('assets/app_icon.png'), 'type' => $type);
            $notification = array('click_action' => 'OPEN_ACTIVITY_1', 'title' => $title , 'body' => $body, 'sound' => 'default', 'badge' => '1', 'data' => 'default', 'icon' => base_url('assets/dist/img/app_icon.png'), 'type' => $type,  'notification_type' => $notification_type, 'redirection_id' => $redirection_id);
            /*$arrayToSend = array('registration_ids' => array($token), 'notification' => $notification, 'priority'=>'high', 
                'data' => $data_arr, 'content_available' => true);*/
            $arrayToSend = array('registration_ids' => $token, 'notification' => $notification, 'priority'=>'high', 
                'data' => $data_arr, 'content_available' => true);

            $json = json_encode($arrayToSend, true);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key='. $serverKey;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //Send the request
            $response = curl_exec($ch);
            //print_r($response); 
            /*echo '<br>';
             if ($response === FALSE) {
             die('FCM Send Error: ' . curl_error($ch));
             }*/
            curl_close($ch);
        }
            
            //exit;
             
        return true;

    }

    function save_log ($request)
    {
        $data = [
            "request" => $request,
            "created_timestamp" => date("Y-m-d H:i:s")
        ];

        $this->db->insert("FM_push_notification_log", $data);
        if ($this->db->affected_rows() > 0)
        {
            return $this->db->insert_id();
        }
    }

    function update_log ($log_id, $response)
    {
        $condition = ["id" => $log_id];
        $data = [
            "response" => $response,
            "updated_timestamp" => date("Y-m-d H:i:s")
        ];
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update("FM_push_notification_log");
    }

    public function send_push_android($data)
    {
        $device_token = $data['device_token'];
        $subject = $data['subject'];
        $message = $data['message'];
        $notification_type = $data['notification_type'];
        $redirection_id = $data['redirection_id'];

        $type = 1;//0 //.MainActivity
        $url = "https://fcm.googleapis.com/fcm/send";
        $title = $subject;
        $body = $message;
        $token = $device_token;
        $serverKey = 'AAAA3PV8wF0:APA91bEtzE-aMDPpt9p9xdacBSBxyRyVG7egubTipVOwLRdClf7FvQPzW1NeoVuKO5hE87yT9AnnRFAx9NIkEibEfe9_tMos0wUwB0Oa58I0CAwAk-PJkYwTGVeZdTizr9iu9Oqoo63P';
        
        
        

        $data_arr = array("title" => $title , "body" => $body, "icon" => base_url("assets/dist/img/app_icon.png"), "type" => $type, "notification_type" => $notification_type, "redirection_id" => $redirection_id, "action" => "farmology_home");
        /*if($notification_type == ''){
            $click_action = 'OPEN_ACTIVITY_1';
        }else if($notification_type == 'Video'){
            $click_action = 'OPEN_ACTIVITY_6';
        }else if($notification_type == 'Blog'){
            $click_action = 'OPEN_ACTIVITY_5';
        }else if($notification_type == 'Sell Produce'){
            $click_action = 'OPEN_ACTIVITY_4';
        }else if($notification_type == 'Community'){
            $click_action = 'OPEN_ACTIVITY_2';
        }else if($notification_type == 'Order'){
            $click_action = 'OPEN_ACTIVITY_3';
        }else if($notification_type == 'Enquiry'){
            $click_action = 'OPEN_ACTIVITY_1';
        }*/
        $click_action = 'OPEN_ACTIVITY_1';
        //'OPEN_ACTIVITY_1'
        $notification = array('click_action' => $click_action, 'title' => $title , 'body' => $body, 'sound' => 'default', 'badge' => '1', 'data' => 'default', 'icon' => base_url('assets/dist/img/app_icon.png'), 'type' => $type,  'notification_type' => $notification_type, 'redirection_id' => $redirection_id);
        if (is_array($token))
        {
            $arrayToSend = array('registration_ids' => $token, 'notification' => $notification, 'priority'=>'high', 'data' => $data_arr, 'content_available' => true);
        }
        else
        {
            $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority'=>'high', 'data' => $data_arr, 'content_available' => true);
        }
        

        $json = json_encode($arrayToSend, true);
        $log_id = $this->save_log($json);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //Send the request
        $response = curl_exec($ch);

        if (isset($log_id))
        {
            $this->update_log($log_id, $response);
        }
        // if ($response === FALSE) {
        // die('FCM Send Error: ' . curl_error($ch));
        // }
        curl_close($ch);
        return true;

    }

    public function send_push_android_for_new_users($data)
    {   
        $curl = curl_init();
        if (!empty($data["action"]) && $data["action"]=="farmology_home")
        {
            if (is_array($data["device_token"]))
            {
                $json = '{
                    "registration_ids": '.json_encode($data["device_token"]).',
                    "notification": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["message"].'"
                    },
                    "data": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["message"].'",
                        "action": "'.$data["action"].'",
                        "isAllowed": "true"
                    }
                }';
            }
            else
            {
                $json = '{
                    "to": "'.$data["device_token"].'",
                    "notification": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["body"].'"
                    },
                    "data": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["body"].'",
                        "action": "'.$data["action"].'",
                        "isAllowed": "true"
                    }
                }';
            }
        }
        else
        {   
            if (empty($data["action"])) {
                $data["action"] = "farmology_home";
            }

            if (is_array($data["device_token"]))
            {
                $json = '{
                    "registration_ids": '.json_encode($data["device_token"]).',
                    "notification": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["message"].'"
                    },
                    "data": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["message"].'",
                        "redirection_id": "'.$data["redirection_id"].'",
                        "action": "'.$data["action"].'",
                        "isAllowed": "true"
                    }
                }';
            }
            else
            {
                $json = '{
                    "to": "'.$data["device_token"].'",
                    "notification": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["message"].'"
                    },
                    "data": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["message"].'",
                        "redirection_id": "'.$data["redirection_id"].'",
                        "action": "'.$data["action"].'",
                        "isAllowed": "true"
                    }
                }';
            }
        }

        $log_id = $this->save_log($json);
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $json,
          CURLOPT_HTTPHEADER => array(
            'Authorization: key=AAAA3PV8wF0:APA91bEtzE-aMDPpt9p9xdacBSBxyRyVG7egubTipVOwLRdClf7FvQPzW1NeoVuKO5hE87yT9AnnRFAx9NIkEibEfe9_tMos0wUwB0Oa58I0CAwAk-PJkYwTGVeZdTizr9iu9Oqoo63P',
            'Content-Type: application/json'
          ),
        ));
        $response = curl_exec($curl);
        $response = json_decode($response);
        if (isset($response->results[0]))
        {
            $results_object = $response->results[0];
            $results_object->action = $data["action"];
            $response->results[0] = $results_object;
        }
        $response = json_encode($response);
        
        if (isset($log_id))
        {
            $this->update_log($log_id, $response);
        }

        curl_close($curl);
    }

    function send_push_IOS($data)
    {
        $device_token = $data['device_token'];
        $subject = $data['subject'];
        $message = $data['message'];

        $device_token = $data['device_token'];
        $subject = $data['subject'];
        $message = $data['message'];

        $type = 0;
        $url = "https://fcm.googleapis.com/fcm/send";
        $title = $subject;
        $body = $message;
        $token = $device_token;
        $serverKey = 'AAAAlUZPkNg:APA91bGkAu2d35zXQIv5UaNqHOT9Gfc8vd53GdcIHOqtdqZ8WLkMq6h9JjN5O-JWa6TQPAbMvzvYwsyNWVT8_iK-JJTVM5WekIFV-Tioo4DDbBoSCGomjVX6IHmAk8TQaapiydvc2hp4';
        

        $data_arr = array("title" => $title , "body" => $body, "icon" => base_url("assets/dist/img/app_icon.png"), "type" => $type, "action" => "farmology_home");
        $notification = array('click_action' => '.MainActivity', 'title' => $title , 'body' => $body, 'sound' => 'default', 'badge' => '1', 'data' => 'default', 'icon' => base_url('assets/img/app_icon.png'), 'type' => $type);
        $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority'=>'high', 
            'data' => $data_arr, 'content_available' => true);

        $json = json_encode($arrayToSend, true);
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //Send the request
        $response = curl_exec($ch);
        // if ($response === FALSE) {
        // die('FCM Send Error: ' . curl_error($ch));
        // }
        curl_close($ch);
        return true;

    }

    public function send_push_message()
    {
        $customer_id = $data['customer_id'];
        $notification_subject = $data['notification_subject'];
        $notification_text = $data['notification_text'];
       // if(!empty($data['notification_type']) && !empty($data['redirection_id'])){
            $notification_type = $data['notification_type'];
            $redirection_id = $data['redirection_id'];
        /*}else{
            $notification_type = '';
            $redirection_id = '';
        }*/    

        $insert_data = array("customer_id" => $customer_id, "notification_subject" => $notification_subject, "notification_text" => $notification_text, "notification_type" => $notification_type, "redirection_id" => $redirection_id, "created_date" => date("Y-m-d H:i:s"));

        $this->db->insert("FM_customer_notification", $insert_data);

        $push_data = array("customer_id" => $customer_id, "subject" => $notification_subject, "message" => $notification_text, "notification_type" => $notification_type,"redirection_id" => $redirection_id);
        $this->sendPushMessage($push_data);
        

        $response = array("status" => "Y", "message" => "Notification sent for P");
        return $response;
    }

    public function sendPushMessage($data)
    {
        $customer_id = $data['customer_id'];
        $subject = $data['subject'];
        $message = $data['message'];
        $notification_type = $data['notification_type'];
        $redirection_id = $data['redirection_id'];

        // find device type
        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where("customer_id", $customer_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $device_token = $row->fcm_token;
            if($device_token != '')
            {
                // android
                $push_data = array("device_token" => "f1uNFQS2RuGhdXG2qomrHC:APA91bGHBMt_pxmE5W7fPq5TVV6sYVGNbDEgApx2fqKrXQpAaGZ5XMRJtOD2BaX1zpBazD9NlXh7RJPt2Va3Oj5LXDY4RCHcwYXC--1nkmZnfQ9-Fmms8j4xr3FXukgqo4Rh63tXPlH3", "subject" => $subject, "message" => $message ,"notification_type" => $notification_type, "redirection_id" => $redirection_id);
                $this->send_push_android($push_data);
                return true;

            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }
    
    public function sendPushMessagesForNewUsers($user_id, $body, $title, $action = "", $redirection_id = "") 
    {
        if ($user_id == null) {
            return;
        }
        $fcm = $this->db->select('device_token')->from('FM_customer_device_details')->where('customer_id', $user_id)->get()->row()->device_token;
        $curl = curl_init();

        // $json = '{"to":"'.$fcm.'", "priority": "high", "data":{ "title": "'.$title.'", "body": "'.$body.'", "action":"'.$action.'" } }';

        if ($action=="farmology_home")
        {
            $json = '{
                "to": "'.$fcm.'",
                "notification": {
                    "title": "'.$title.'",
                    "body": "'.$body.'"
                },
                "data": {
                    "title": "'.$title.'",
                    "body": "'.$body.'",
                    "action": "'.$action.'",
                    "isAllowed": "true"
                }
            }';
        }
        else
        {
            $json = '{
                "to": "'.$fcm.'",
                "notification": {
                    "title": "'.$title.'",
                    "body": "'.$body.'"
                },
                "data": {
                    "title": "'.$title.'",
                    "body": "'.$body.'",
                    "redirection_id": "'.$redirection_id.'",
                    "action": "'.$action.'",
                    "isAllowed": "true"
                }
            }';
        }

        $log_id = $this->save_log($json);

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $json,
          CURLOPT_HTTPHEADER => array(
            'Authorization: key=AAAA3PV8wF0:APA91bEtzE-aMDPpt9p9xdacBSBxyRyVG7egubTipVOwLRdClf7FvQPzW1NeoVuKO5hE87yT9AnnRFAx9NIkEibEfe9_tMos0wUwB0Oa58I0CAwAk-PJkYwTGVeZdTizr9iu9Oqoo63P',
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);
        if (isset($response->results[0]))
        {
            $results_object = $response->results[0];
            $results_object->action = $action;
            $response->results[0] = $results_object;
        }
        $response = json_encode($response);
        
        if (isset($log_id))
        {
            $this->update_log($log_id, $response);
        }

        curl_close($curl);
    }

}