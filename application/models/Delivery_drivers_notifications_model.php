<?php defined("BASEPATH") OR exit("No direct script access allowed");

require APPPATH.'libraries/php-jwt-main/src/JWT.php';
require APPPATH.'libraries/php-jwt-main/src/Key.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Delivery_drivers_notifications_model extends CI_model {

    public function send_notification_to_delivery_driver($order_no)
    {
        $sql = "SELECT DD.driver_id, DD.name AS delivery_driver_name, DD.phone AS delivery_driver_mobile_number, CONCAT(C.first_name, ' ', C.last_name) AS customer_name, C.phone AS customer_mobile_number, CDD.device_token AS customer_device_token, O.address_id, O.delivery_date, O.delivery_time_slot FROM FM_order O INNER JOIN FM_delivery_drivers DD ON DD.driver_id = O.delivery_driver_id COLLATE utf8_unicode_ci INNER JOIN FM_customer C ON C.id = O.customer_id INNER JOIN FM_customer_device_details CDD ON CDD.customer_id = O.customer_id WHERE O.order_no = '$order_no' AND O.status NOT IN ('NOP','D','C')";
        $details = $this->db->query($sql)->row();

        if (!empty($details))
        {
            $order_number = $order_no;
            $driver_id = $details->driver_id;
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

            $token = $this->get_delivery_driver_fcm_token_by_id($driver_id);
            if (!empty($token))
            {
                $title = "New Order Delivery Assigned";
                $body = "A new order delivery is assigned to you.";

                if (!empty($order_number))
                {
                    $body .= "Order number is #".$order_number;                    
                }

                if (!empty($delivery_address))
                {
                    $body .= ", delivery address is ".$delivery_address;
                }

                if (!empty($customer_name))
                {
                    $body .= ", customer name is ".$customer_name;
                }

                if (!empty($customer_mobile_number))
                {
                    $body .= "and his/her mobile number is ".$customer_mobile_number;
                }

                $body .= ".";
                $this->send_notification($title, $body, $token);
            }
        }
    }

    public function get_delivery_driver_fcm_token_by_id($id)
    {
        $delivery_driver_details = $this->db->get_where("FM_delivery_drivers", ["status" => "A", "driver_id" => $id])->row();
        return (!empty($delivery_driver_details->fcm_token)) ? $delivery_driver_details->fcm_token : NULL;
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

    public function send_notification($title, $body, $token)
	{
        $ch = curl_init();
        $project_id = "farmology-30da6";
        $access_token = $this->returnFireBaseTkn();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.$project_id.'/messages:send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $postFields = "{\n\"message\":
                            {\n   
                                \"notification\":{\n
                                    \"title\":\"". $title ."\",\n     
                                    \"body\":\"". $body ."\",\n
                                },\n  
                                \"data\":{\n                                                       
                                    \"title\":\"". $title ."\",\n     
                                    \"body\":\"". $body ."\"\n
                                },\n  
                                \"token\":\"". $token ."\"\n
                            }
                        }";
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $headers = [];
        $headers[] = 'Authorization: Bearer '. $access_token;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);
    }

	public function returnFireBaseTkn()
	{
        $jsonInfo = json_decode(file_get_contents(APPPATH.'third_party/farmology-30da6-3d5a9dbcc86b.json'), true);
        $now_seconds = time();
        $privateKey = $jsonInfo['private_key'];
        $payload = [
            'iss' => $jsonInfo['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $jsonInfo['token_uri'],
            'exp' => $now_seconds + (60 * 60),
            'iat' => $now_seconds
        ];
        
        $jwt = JWT::encode($payload, $privateKey, 'RS256');
        
        $ch = curl_init();
        $post = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ];
        $ch = curl_init($jsonInfo['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        $jsonObj = json_decode($response, true);
        
        return $jsonObj['access_token'];
    }

}

?>