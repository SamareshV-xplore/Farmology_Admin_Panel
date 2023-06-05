<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Help_and_faq_management extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("help_and_faq_model");
    }

    public function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

    public function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
    
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function index()
	{
		$header_data["title"] = "Help and FAQ Management";
		$left_data["navigation"] = "help_and_faq_management";
 
		if ($this->common_model->user_login_check())
		{
			$admin_details = $this->common_model->get_admin_user_details();
			$header_data['admin_details'] = $admin_details;
			$left_data['admin_details'] = $admin_details;
            $page_data["support_details"] = $this->help_and_faq_model->get_support_details();
            $page_data["list_of_FAQ"] = $this->help_and_faq_model->get_list_of_FAQ();

            $this->load->view('includes/header_view', $header_data);
            $this->load->view('includes/left_view', $left_data);
            $this->load->view('help_and_faq_management_view', $page_data);
            $this->load->view('includes/footer_view');
		}
		else
		{
			redirect(base_url(''));
		}
	}

    public function update_help_and_support_details()
    {
        if (!empty($_POST))
        {
            foreach ($_POST as $name => $value)
            {
                $data = ["value" => $value];
                $condition = ["name" => $name];
                $this->help_and_faq_model->update_support_details_on_condition($data, $condition);
            }

            $response = ["success" => true, "message" => "Saved Successfully"];
        }
        else
        {
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "No post data given!"];
        }

        $this->response($response, 200);
    }

    public function add_FAQ()
    {
        $missing_keys = [];
        
        if (!empty($this->input->post("hash_id")))
        {
            $hash_id = $this->input->post("hash_id");
        }

        if (!empty($this->input->post("question")))
        {
            $question = $this->input->post("question");
        }
        else
        {
            $missing_keys[] = "question";
        }

        if (!empty($this->input->post("answer")))
        {
            $answer = $this->input->post("answer");
        }
        else
        {
            $missing_keys[] = "answer";
        }

        if (!empty($missing_keys))
        {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtrim($missing_string, ", ");
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => $missing_string." not given!"];
        }
        else
        {
            $is_added = $is_updated = 0;
            if (!empty($hash_id))
            {
                $condition = ["hash_id" => $hash_id];
                $update_data = ["question" => $question, "answer" => $answer];
                $is_updated = $this->help_and_faq_model->update_FAQ_on_condition($update_data, $condition);
            }
            else
            {
                $data = ["hash_id" => $this->GUID(), "question" => $question, "answer" => $answer];
                $is_added = $this->help_and_faq_model->add_FAQ($data);
            }

            if ($is_added)
            {
                $response = ["success" => true, "message" => "Added Successfully"];
            }
            elseif ($is_updated)
            {
                $response = ["success" => true, "message" => "Saved Successfully"];
            }
            else
            {
                $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "Failed to add or update FAQ details in database!"];
            }
        }

        $this->response($response, 200);
    }

    public function delete_FAQ()
    {
        if (!empty($this->input->post("hash_id")))
        {
            $condition = ["hash_id" => $this->input->post("hash_id")];
            $this->help_and_faq_model->delete_FAQ_on_condition($condition);
            $response = ["success" => true, "message" => "Deleted Successfully"];
        }
        else
        {
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "Deletable FAQ id is not given!"];
        }

        $this->response($response, 200);
    }

}

?>