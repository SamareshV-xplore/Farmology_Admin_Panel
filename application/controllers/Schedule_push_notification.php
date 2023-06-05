<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule_push_notification extends CI_Controller 
{	
    function __construct()
    {
        parent::__construct();
        $this->load->model('schedule_push_notification_model');
    }

    private function response ($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_output(json_encode($data))
                            ->set_status_header($status);
    }

    private function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

	public function index()
	{
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Schedule Push Notification";
        $left_data['navigation'] = "schedule_push_notification";

        if($this->common_model->user_login_check())
        {
            // allow and get admin details
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;
            $page_data["notifications_list"] = $this->schedule_push_notification_model->get();
        }
        else
        {
            redirect(base_url(''));
        }

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('schedule_push_notification', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
	}

    public function add()
    {
        if (!empty($_POST) && !empty($_FILES["notification_image"]["name"]))
        {
            $image = $this->upload_image($_FILES["notification_image"]);
            $target_state = (!empty($_POST["notification_target_state"])) ? $_POST["notification_target_state"] : "";
            $data = [
                "hash_id" => $this->GUID(),
                "title" => $_POST["notification_title"],
                "description" => $_POST["notification_desc"],
                "image" => $image,
                "redirect_to" => $_POST["notification_redirect_to"],
                "target_state" => $target_state,
                "send_date" => $_POST["notification_send_date"]
            ];

            $is_added = $this->schedule_push_notification_model->add($data);

            if ($is_added)
            {
                $response = [
                    "success" => true,
                    "message" => "Push Notification Scheduled Successfully."
                ];
            }
            else
            {
                $response = [
                    "success" => false,
                    "message" => "Failed to Schedule Push Notification!"
                ];
            }
        }
        else
        {
            $response = [
                "success" => false,
                "message" => "Something Went Wrong!"
            ];
        }

        header("Content-type: application/json;");
        echo json_encode($response);
    }

    private function upload_image($image)
    {
        $image_url = "";
        $name_arr = explode(".", $image["name"]);
        $extension = end($name_arr);
        $name = $this->GUID()."-".time();
        $file_upload_path = FCPATH."media/uploads/schedule_push_notification_images/".$name.".".$extension;
        $file_save_path = "uploads/schedule_push_notification_images/".$name.".".$extension;
        if (move_uploaded_file($image["tmp_name"], $file_upload_path))
        {
            $image_url = $file_save_path;
        }

        return $image_url;
    }
    
    public function get()
    {
        $notifications_list = $this->schedule_push_notification_model->get();
        if (!empty($notifications_list))
        {
            $response = [
                "success" => true,
                "message" => "Pending Scheduled Push Notifications get successfully.",
                "list" => $notifications_list
            ];
        }
        else
        {
            $response = [
                "success" => false,
                "message" => "Failed to get Pending Scheduled Push Notifications!"
            ];
        }
        $this->response($response, 200);
    }

    public function get_app_redirection_options()
    {
        $options = $this->schedule_push_notification_model->get_app_redirection_options();
        if (!empty($options))
        {
            $response = [
                "success" => true,
                "message" => "available app redirection options get successfully.",
                "options" => $options
            ];
        }
        else
        {
            $response = [
                "success" => false,
                "message" => "failed to fetch available app redirection options!"  
            ];
        }
        
        $this->response($response, 200);
    }

    public function get_target_state_options()
    {
        $options = $this->schedule_push_notification_model->get_available_states();
        if (!empty($options))
        {
            $response = [
                "success" => true,
                "message" => "available target states options get successfully.",
                "options" => $options
            ];
        }
        else
        {
            $response = [
                "success" => false,
                "message" => "failed to get available target states options!"
            ];
        }
        $this->response($response, 200);
    }

    public function edit()
    {
        if (!empty($_POST["hash_id"])) {
            $data = [
                "title" => $_POST["notification_title"],
                "description" => $_POST["notification_desc"],
                "redirect_to" => $_POST["notification_redirect_to"],
                "target_state" => $_POST["notification_target_state"],
                "send_date" => $_POST["notification_send_date"],
                "status" => "P"
            ];

            if (!empty($_FILES["notification_image"]["name"])) {
                $image = $_FILES["notification_image"];
                $data["image"] = $this->upload_image($image);
            }

            $is_edited = $this->schedule_push_notification_model->edit($_POST["hash_id"], $data);
            if ($is_edited) {
                $response = ["success" => true, "message" => "scheduled push notification edited successfully."];
            } else {
                $response = ["success" => false, "message" => "failed to edit schedule push notification!"];
            }
        } else {
            $response = ["success" => false, "message" => "scheduled notification id is not given!"];
        }

        $this->response($response, 200);
    }

    public function delete()
    {
        $response = ["success" => false, "message" => "someting went wrong!"];
        if (!empty($_POST["hash_id"]))
        {   
            $is_deleted = $this->schedule_push_notification_model->delete($_POST["hash_id"]);
            if ($is_deleted)
            {
                $response = ["success" => true, "message" => "scheduled notification deleted successfully."];
            }
        }
        $this->response($response, 200);
    }
}

?>