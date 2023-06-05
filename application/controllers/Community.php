<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Community extends CI_Controller {
	function __construct()
    {
        parent::__construct();
        
    }

    //Answers List
    public function index()
    {
        // users list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Community List";
        $left_data['navigation'] = "communities";
        $left_data['sub_navigation'] = "communitie-list";

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


        // get users list
        $community_list = $this->common_model->get_community_list();
        $page_data['community_list'] = $community_list;
        /*echo'<pre>';
        print_r($community_list);
        die();*/

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('community_list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

     public function details($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Community details";
        $left_data['navigation'] = "Community details";
        $left_data['sub_navigation'] = "Community details";

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

        $community_details = $this->common_model->get_community_details_by_id($id);
        
        $page_data["community_details"] = $community_details;
        /*echo'<pre>';
        print_r($community_details);
        die();   */    

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('community_details_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    function delete($id = 0)
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        } 

        $delete_community = $this->common_model->delete_community_by_id($id);
        if($delete_community['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_community['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_community['message']);
        }
        redirect(base_url('communities-list'));

    }
    function comments_delete($id = 0,$community_id)
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        } 

        $delete_community_comments = $this->common_model->delete_community_comments_by_id($id);
        if($delete_community_comments['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_community_comments['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_community_comments['message']);
        }
        redirect(base_url('communities-details/'.$community_id));
    }

    public function add_comment()
    {
        $image = "";
        if(!empty($_FILES['images']['name']))
        {
            $filesCount = count($_FILES['images']['name']);

            for ($i=0; $i<$filesCount; $i++) {
                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/answer_images/';
                $rand_name = time()."-".$i;
                $upload_file = $upload_dir.$rand_name.basename($_FILES['images']['name'][$i]);
                $upload_file = str_replace(" ","-",$upload_file);
                $actual_path = 'uploads/answer_images/'.$rand_name.basename($_FILES['images']['name'][$i]);
                $actual_path = str_replace(" ","-",$actual_path);
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $upload_file))
                {
                    $image  = $actual_path;
                }
            }
        }

        $community_id = $this->input->post('id');
        $answer = $this->input->post('comment');

        $data = [
            'customer_id' => 0,
            'community_id' => $community_id,
            'comments' => $answer,
            'image' => $image,
            'created_date' => date('Y-m-d h:i:s'),
            'testing'   => "Development_".date("F_Y")
        ];

        $this->db->insert('FM_community_comments', $data);
        $this->send_push_notification($community_id, $answer);

        echo json_encode(array('success' => true, 'message' => 'Success', 'isSubmitted' => true));
    }

    private function send_push_notification ($community_id, $answer)
    {
        $question_details = $this->get_asked_question($community_id);
        if (isset($question_details))
        {
            $customer_id = $question_details->customer_id;
            $question = $question_details->problem_description;

            $title = "Your question answer.";
            $body = "Dear customer your answer for the question #".$question." is  #".$answer.". Thank you!";
            $action = "community_question";

            $user_app_version = "";
            $user_app_version = $this->get_user_app_version($customer_id);
            $this->sendPushMessages($customer_id, $body, $title, $action, $community_id);
        }
    }

    private function get_asked_question($community_id)
    {   
        $question_details = null;
        $condition = ["status" => "A", "id" => $community_id];
        $asked_question_data = $this->db->get_where("FM_ask_community", $condition)->row();
        if (isset($asked_question_data))
        {
            $question_details = $asked_question_data;
        }

        return $question_details;
    }

    private function sendPushMessages($user_id, $body, $title, $action = "", $redirection_id = "") 
    {
        if ($user_id == null) {
            return;
        }
        $fcm = $this->db->select('device_token')->from('FM_customer_device_details')->where('customer_id', $user_id)->get()->row()->device_token;
        $curl = curl_init();

        // $json = '{"to":"'.$fcm.'", "priority": "high", "data":{ "title": "'.$title.'", "body": "'.$body.'", "action":"'.$action.'" } }';

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
