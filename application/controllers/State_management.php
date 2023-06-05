<?php defined('BASEPATH') OR exit('No direct script access allowed');

class State_management extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('state_model');
    }

    public function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

    public function index()
    {
        $data['title'] = "State Management";
        $data['navigation'] = "state_management";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $data['admin_details'] = $admin_details;
            $data['list_of_states'] = $this->state_model->get_list_of_states();

            $this->load->view('includes/header_view', $data);
            $this->load->view('includes/left_view', $data);
            $this->load->view('state_management_view', $data);
            $this->load->view('includes/footer_view', $data);
        }
        else
        {
            redirect(base_url());
        }
    }

    public function change_state_availability()
    {
        $missing_keys = [];

        if (!empty($this->input->post("state_id")))
        {
            $state_id = $this->input->post("state_id");
        }
        else
        {
            $missing_keys[] = "state_id";
        }

        if (!empty($this->input->post("state_availability_status")))
        {
            $state_availability_status = $this->input->post("state_availability_status");
        }
        else
        {
            $missing_keys[] = "state_availability_status";
        }

        if (!empty($missing_keys))
        {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtrim($missing_string, ", ");
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => $missing_string." not given!"];
        }
        else
        {
            $condition = ["id" => $state_id];
            $data = ["is_available" => $state_availability_status];
            $is_updated = $this->state_model->update_state_on_condition($data, $condition);
            if ($is_updated)
            {
                $response = ["success" => true, "message" => "Availability Status Changed"];
            }
            else
            {
                $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "Failed to change state availability status!"];
            }
        }

        $this->response($response, 200);
    }

    public function delete_state()
    {
        if (!empty($this->input->post("state_id")))
        {
            $state_id = $this->input->post("state_id");
            $this->state_model->delete_state($state_id);
            $response = ["success" => true, "message" => "Deleted Successfully"];
        }
        else
        {
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "State ID is not given!"];
        }
        
        $this->response($response, 200);
    }
}