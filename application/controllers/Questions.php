<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Questions extends CI_Controller {
	function __construct()
    {
        parent::__construct();
        $this->load->model('answers_model');
        $this->load->model('crop_model');
        $this->load->model('Experts_model');
    }

    private function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    //Answers List
    public function index()
    {
        // users list
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Question Management";
        $left_data['navigation'] = "questions";
        $left_data['sub_navigation'] = "questions-list";

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

        $page_data['filter_data'] = $filter_data;

        // get users list
        $questions_list = $this->answers_model->questions_list($filter_data);
        $page_data['questions_list'] = $questions_list;

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('questions/list_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function get_recommended_product($product_id)
    {
        if(isset($product_id))
        {
            $product = $this->Experts_model->get_product_by_product_id($product_id);
            if(is_object($product)){
                $product->image = FRONT_URL.$product->image;
                $response = array(
                    "success" => true,
                    "message" => "Recommended Product fetched successfully.",
                    "product" => $product
                );
            }
            else
            {
                $response = array(
                    "success" => false,
                    "message" => "Recommended Product Not Found!"
                );
            }

            print_r(json_encode($response));
        }
        else
        {
            echo "ERROR: product_id is not given!";
        }
    }

    function render_recommended_products_data($recommended_products)
    {
        $product_data_list = array();
        $product_id_list = explode(",", $recommended_products);
        foreach($product_id_list as $product_id){
            $SQL = "SELECT * FROM FM_product_variation WHERE status='Y' AND product_id='$product_id'";
            $variation = $this->db->query($SQL)->row();
            $product_data = $product_id."_".$variation->id;
            $product_data_list[] = $product_data;
        }

        $product_data_string = implode(",", $product_data_list);
        $product_data_string = rtrim($product_data_string,",_");
        return $product_data_string;
    }

    public function add()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Question Management";
        $left_data['navigation'] = "questions";
        $left_data['sub_navigation'] = "questions-add";

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

        // get crops list
        $crop_list = $this->crop_model->crop_list($filter_data);
        $page_data['crop_list'] = $crop_list;
        $page_data['products'] = $this->Experts_model->getAllProducts();

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('questions/add_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data); 
    }

    function add_submit()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url('questions-list'));
        }

        if($this->input->post('question_form'))
        {   
            $customer_id = 0;
            $crop_id = $this->input->post('crop_id');
            $question = $this->input->post('question');
            $answer = $this->input->post('answer_text');
            $recommended_products = $this->input->post('suggested_products');
            $question_images = $_FILES["question_images"];

            if (!empty($recommended_products) && isset($recommended_products))
            {
                $recommended_products = $this->render_recommended_products_data($recommended_products);
            }
            $status = $this->input->post('status');
            $form_data = array('customer_id' => $customer_id, 'crop_id'=> $crop_id, 'title' => $question, 'status' => $status);
            $question_data = $this->answers_model->add_question($form_data);
            $question_id = $question_data['question_id'];
            $answer_data_insert = array(
                'answer_text' => $answer, 
                'recommended_products' => $recommended_products,
                'created_date' => date("Y-m-d H:i:s"), 
                'question_id' => $question_id
            );
            $this->db->insert("FM_answers", $answer_data_insert);
            $result = $this->upload_question_images($question_id, $customer_id, $question_images);

            $this->session->set_flashdata('success_message', 'Question Data added successfully.');
            redirect(base_url('questions-list'));

        }else{
            redirect(base_url('questions-list'));
        }
    }

    private function upload_question_images ($question_id, $customer_id, $images)
    {
        $uploaded_images_path = [];
        if (!empty($images["name"]))
        {
            for ($i=0; $i<count($images["name"]); $i++)
            {
                $name = $this->GUID()."-".time();
                $extension = end(explode(".", $images["name"][$i]));
                $file_upload_path = FCPATH."media/uploads/community/".$name.".".$extension;
                $file_save_path = "uploads/community/".$name.".".$extension;
                if (move_uploaded_file($images["tmp_name"][$i], $file_upload_path))
                {
                    $uploaded_images_path[] = $file_save_path;
                }
            }
        }

        foreach ($uploaded_images_path as $image)
        {
            $data = [
                "question_id" => $question_id,
                "customer_id" => $customer_id,
                "image" => $image,
                "created_date" => date("Y-m-d H:i:s")
            ];
            $this->db->insert("FM_question_image", $data);
        }
    }

    public function edit($id = 0)
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Edit Question";
        $left_data['navigation'] = "questions";
        $left_data['sub_navigation'] = "question-edit";

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

        $question_data = $this->answers_model->single_question_details($id);
        // get crops list
        $filter_data = array("status" => 'all');
        $crop_list = $this->crop_model->crop_list($filter_data);
        $page_data['crop_list'] = $crop_list;

        if($question_data["status"] == "N")
        {
            $this->session->set_flashdata('error_message', 'Question details not found. Maybe Question already deleted.');
            redirect(base_url('questions-list'));
        }
        else
        {
            $page_data["question_details"] = $question_data["details"];
            $page_data['products'] = $this->Experts_model->getAllProducts();
        }


        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('questions/edit_view', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    function edit_submit()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        if($this->input->post('question_id'))
        {
            $question_id = $this->input->post('question_id');
            $status = $this->input->post('status');
            $quesstion = $this->input->post('question');
            $answer = $this->input->post('answer_text');
            $recommended_products = $this->input->post('suggested_products');
            if (!empty($recommended_products) && isset($recommended_products))
            {
                $recommended_products = $this->render_recommended_products_data($recommended_products);
            }
            $modified_date = date("Y-m-d H:i:s");

            $customer_id = $this->input->post('customer_id');

            $crop_id = $this->input->post('crop_id');

            $user_details = $this->db->get_where('FM_customer',array('id' =>$customer_id,'status' => 'Y'))->row();

            $check_answer = $this->db->get_where('FM_answers',array('question_id'=> $question_id, 'is_deleted' => 'N'))->row();

            $form_data = array('title' => $quesstion, 'status' => $status, 'updated_date' => $modified_date);
            $form_data1 = array('title' => $quesstion, 'crop_id'=> $crop_id, 'status' => $status, 'updated_date' => $modified_date);
            
            $answer_data = array(
                'answer_text' => $answer, 
                'recommended_products' => $recommended_products, 
                'updated_date' => $modified_date
            );

            $answer_data_insert = array(
                'answer_text' => $answer, 
                'recommended_products' => $recommended_products, 
                'created_date' => $modified_date, 
                'question_id' => $question_id
            );

            $this->db->where("id", $question_id);
            $this->db->update("FM_questions", $form_data1);

            $this->db->where("clone_id", $question_id);
            $this->db->update("FM_questions", $form_data);
            if(!empty($check_answer)){
                $this->db->where("question_id", $question_id);
                $this->db->update("FM_answers", $answer_data);
            }else{
                $this->db->insert("FM_answers", $answer_data_insert);

            }    


            $phone_no = $user_details->phone;
            if(!empty($customer_id))
            {
                $text = "Dear customer your answer for the question #".$quesstion." is  #".$answer.". Thank you!";
                $notification_subject = "Your question answer.";
                $notification_body = $text;
                $notification_action = "crop_doctor_question";

                $user_app_version = "";
                $user_app_version = $this->get_user_app_version($customer_id);
                if ($user_app_version != "" && $user_app_version >= 2)
                {
                    $this->sendPushMessages($customer_id, $notification_body, $notification_subject, $notification_action);
                }
                else
                {
                    $insert_data = array("customer_id" => $customer_id, "phone_no" =>$phone_no, "notification_subject" => $notification_subject, "notification_text" => $notification_body, "notification_type" => 'Enquiry', "redirection_id" => $customer_id, "quesstion" => $quesstion, "answer" => $answer, "created_date" => date("Y-m-d H:i:s"));

                    $data = http_build_query($insert_data);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, base_url("notification/send_push_notification"));
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $output = curl_exec($ch);
                    curl_close($ch);
                }
            }

            $this->session->set_flashdata('success_message', 'Question Details updated.');
            redirect(base_url('questions-list'));

        }
        else
        {
            redirect(base_url(''));
        }
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

        $delete_question = $this->answers_model->delete_question_by_id($id);
        if($delete_question['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_question['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_question['message']);
        }
        redirect(base_url('questions-list'));

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

    public function sendPushMessages($user_id, $body, $title, $action = "") 
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

}