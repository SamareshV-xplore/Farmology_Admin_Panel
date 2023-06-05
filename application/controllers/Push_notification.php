<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push_notification extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('push_model');
        $this->load->model('video_model');
        $this->load->model('blog_model');
        $this->load->model('schedule_push_notification_model');
    }

    function index(){
        // users list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Push Notification";
        $left_data['navigation'] = "push";
        $left_data['sub_navigation'] = "push-notification";

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

        $filter_data = array("status" => 'all');

        // get users list
        $users_list = $this->push_model->users_list();
        $page_data['users_list'] = $users_list;
        $video_list = $this->video_model->get_video_list($filter_data);
        $page_data['video_list'] = $video_list;
        $app_redirection_list = $this->schedule_push_notification_model->get_app_redirection_options();
        $page_data['app_redirection_list'] = $app_redirection_list;
        $blog_list = $this->blog_model->blog_list($filter_data);
        $page_data['blog_list'] = $blog_list;

        /*echo '<pre>';
        print_r($video_list);
        echo '<pre>';

        echo '<pre>';
        print_r($blog_list);
        die();*/

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('push/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function test($data)
    {
        return $data;
    }

    function send_notification(){
        //print_r($this->input->post());exit;
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

        /*echo'<pre>';
        print_r($header_data['admin_details']);
        echo'<pre>';
        echo'<pre>';
        print_r($admin_details);
        die();*/

        $message = $this->input->post('push_message');
        $title = $this->input->post('push_title');
        $type  = $this->input->post('type');
        $subject = $title;
        $body = $message;

        if($type == '0')
        {
            $redirection_id = '';
            $notification_type = '';
            $action = "farmology_home";
        }
        else if($type == '1')
        {
            $redirection_id = $this->input->post('redirect_id');
            $notification_type = 'Video';
            $action = "video_answer";
        }
        else
        {
            $redirection_id = $this->input->post('redirect_id');
            $notification_type = 'Blog';
            $action = "blog_answer";
        }

        /*echo'<pre>'; 
        print_r($redirection_id);
         echo'<pre>'; 
        print_r($notification_type);
        die();*/
        if($this->input->post('all_users') == 'all')
        {
            $device_tokens = $this->get_all_user_device_token();
            $new_user_tokens = $device_tokens["new_user"];
            $old_user_tokens = $device_tokens["old_user"];

            if (!empty($new_user_tokens) || !empty($old_user_tokens))
            {
                foreach ($new_user_tokens as $tokens)
                {
                    $data = ["device_token" => $tokens, "subject" => $subject, "body" => $body, "action" => $action, "redirection_id" => $redirection_id];
                    $this->send_new_push_android($data);
                }

                foreach ($old_user_tokens as $tokens)
                {
                    $data = ["device_token" => $tokens, "subject" => $title, "message" => $message, "notification_type" => $notification_type, "redirection_id" => $redirection_id];
                    $this->notification_model->send_push_android($data);
                }

                $this->session->set_flashdata('success_message', 'Push notification successfully sent to all users.');
                redirect(base_url('push-notification'));
            }
            else
            {
                $this->session->set_flashdata('error_message', 'Failed! No user found.');
                redirect(base_url('push-notification'));
            }
            
            // $get_device_data = $this->push_model->find_all_devices();
            // if($get_device_data['status'] == 'Y')
            // {
            //     $customer_id = array();
            //     foreach($get_device_data['details'] as $customer_row)
            //     {
            //          $customer_id [] = $customer_row['id'];
            //         //echo "==".$customer_row['id']."<br>";
            //     }
            //     if(count($customer_id) > 0){

            //         foreach ($customer_id as $id)
            //         {
            //             $user_app_version = "";
            //             $user_app_version = $this->get_user_app_version($id);
            //             if ($user_app_version != "" && $user_app_version >= 2)
            //             {
            //                 $this->sendPushMessages($id, $body, $subject, $action, $redirection_id);
                            // $app_latest_version_data = $this->db->get_where("FM_preferences", ["name" => "latest_version"])->row();
                            // if (isset($app_latest_version_data))
                            // {
                            //     $latest_version = $app_latest_version_data->content;

                            //     if ($user_app_version == $latest_version)
                            //     {        
                            //         $this->sendPushMessages($id, $body, $subject, $action, $redirection_id);
                            //     }
                            // }
                //         }
                //         else
                //         {
                //             $push_data = array("customer_id" => $id, "subject" => $title, "message" => $message, "notification_type" => $notification_type,"redirection_id" => $redirection_id);
                //             $this->notification_model->send_push_notification($push_data);
                //         }
                //     }
                // }
                /*$push_data = array("customer_id" => $customer_row['id'], "subject" => $title, "message" => $message, "notification_type" => $notification_type,"redirection_id" => $redirection_id);
                    
                $this->notification_model->send_push_notification($push_data);*/

            //     $this->session->set_flashdata('success_message', 'Push notification successfully sent to all users.');
                
            //     redirect(base_url('push-notification'));
                 
            // }
            // else
            // {
            //     $this->session->set_flashdata('error_message', 'Failed! No user found.');
            //     redirect(base_url('push-notification'));
            // }

        }
        else if($this->input->post('user_id_for_push'))
        {
            $data = $this->input->post('user_id_for_push');
            foreach($data as $customer_row)
            {
                $customer_id = $customer_row;

                $user_app_version = "";
                $user_app_version = $this->get_user_app_version($customer_id);
                if ($user_app_version != "" && $user_app_version >= 2)
                {
                    $this->sendPushMessages($customer_id, $body, $subject, $action, $redirection_id);
                }
                else
                {
                    $push_data = array("customer_id" => $customer_row, "subject" => $title, "message" => $message, "notification_type" => $notification_type,"redirection_id" => $redirection_id);
                    $this->notification_model->send_push_notification($push_data);
                } 
            }

            $this->session->set_flashdata('success_message', 'Push notification successfully sent to selected users.');
            redirect(base_url('push-notification'));
                
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Failed! Something is wrong..');
            redirect(base_url('push-notification'));
        } 
    }
    
    function send_schedule_push_notification ($data)
    {
        $title = $data["subject"];
        $subject = $data["subject"];

        $body = $data["body"];
        $message = $data["body"];

        $image = $data["image"];

        $action = $data["action"];
        $notification_type = $data["action"];

        $redirection_id = $data["redirection_id"];

        if (!empty($data["new_user_tokens"]))
        {
            foreach ($data["new_user_tokens"] as $tokens)
            {
                $data = ["device_token" => $tokens, "subject" => $subject, "body" => $body, "image" => $image, "action" => $action, "redirection_id" => $redirection_id];
                $this->send_new_push_android($data);
            }
        }
        
        if (!empty($data["old_user_tokens"]))
        {
            foreach ($data["old_user_tokens"] as $tokens)
            {
                $data = ["device_token" => $tokens, "subject" => $title, "message" => $message, "notification_type" => $notification_type, "redirection_id" => $redirection_id];
                $this->notification_model->send_push_android($data);
            }
        }
    }

    public function get_all_user_device_token ()
    {
        $new_user_details = $this->push_model->get_new_user_device_tokens();
        $old_user_details = $this->push_model->get_old_user_device_tokens();

        $new_user_tokens = $this->render_user_token_array($new_user_details);
        $old_user_tokens = $this->render_user_token_array($old_user_details);

        return ["new_user" => $new_user_tokens, "old_user" => $old_user_tokens];
    }

    private function render_user_token_array ($user_details)
    {
        $user_token_array = [];
        $total_users = count($user_details);
        $total_iteration = intval($total_users / 1000);
        $total_iteration += ($total_users % 1000 > 0) ? 1 : 0;

        for ($i=0; $i<$total_iteration; $i++)
        {
            $start = (($i == 0) ? $i : $i*1000);
            $end = $start + 1000;
            while(count($user_details) > 0)
            {
                $user_token_array[] = array_splice($user_details, $start, $end);
            }
        }
        return $user_token_array;
    }

    public function manage_notification($device_data, $message){
        $android_devices = array();
        $ios_devices = array();

        foreach ($device_data as $details){
            if($details['device_type'] == 'A'){
                $android_devices[] = $details['device_token'];
            }elseif($details['device_type'] == 'I'){
                $ios_devices[] = $details['device_token'];
            }
        }
        if(count($android_devices) > 0){
            $result = $this->send_android_notification($android_devices, array('message' => $message));
        }

        if(count($ios_devices) > 0){
            $result = $this->send_android_notification($android_devices, array('message' => $message));
        }

        return $result;
    }

    function send_android_notification($registration_ids, $message) {
        /*$fields = array(
            'registration_ids' => array($registration_ids),
            'data'=> $message,
        );
        $headers = array(
            'Authorization: key=AAAAF0IBgPU:APA91bGSUhJCOPRbuat6Sg3O-KrRIwOnkQYBH1jNgmVJ5heDsjymCrFAmLMAZfFGRO1zbssgMTk_UtPj96JVdaiJx0Lav99mK-pCEAaqhX5iRtGM6NUKGzLLhf7Bcd-ZXS-cFKp09b-q7rQOrHzsII0x7vkCwJbXrg', // FIREBASE_API_KEY_FOR_ANDROID_NOTIFICATION
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

        // Disabling SSL Certificate support temporarly
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Execute post
        $result = curl_exec($ch );
        if($result === false){
            die('Curl failed:' .curl_errno($ch));
        }

        // Close connection
        curl_close( $ch );
        return $result;*/
        return true;
    }

    function send_ios_notification($registration_ids, $message) {
        return true;
    }

    private function get_user_app_version ($user_id)
    {
        $app_version = "";
        $user_device_details = $this->db->get_where("FM_customer_device_details", ['customer_id' => $user_id])->row();
        if (isset($user_device_details))
        {
            $app_version = $user_device_details->app_version;
        }

        return $app_version;
    }

    public function sendPushMessages($user_id, $body, $title, $action = "", $redirection_id = "") 
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

    private function send_new_push_android ($data)
    {   
        $curl = curl_init();
        $image = (!empty($data["image"])) ? $data["image"] : "";
        if ($data["action"]=="farmology_home")
        {
            if (is_array($data["device_token"]))
            {
                $json = '{
                    "registration_ids": '.json_encode($data["device_token"]).',
                    "notification": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["body"].'",
                        "image": "'.$image.'"
                    },
                    "data": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["body"].'",
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
                        "body": "'.$data["body"].'",
                        "image": "'.$image.'"
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
            if (is_array($data["device_token"]))
            {
                $json = '{
                    "registration_ids": '.json_encode($data["device_token"]).',
                    "notification": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["body"].'",
                        "image": "'.$image.'"
                    },
                    "data": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["body"].'",
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
                        "body": "'.$data["body"].'",
                        "image": "'.$image.'"
                    },
                    "data": {
                        "title": "'.$data["subject"].'",
                        "body": "'.$data["body"].'",
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

    private function save_log ($request)
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

    private function update_log ($log_id, $response)
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

}
