<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Delivery_date_management extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("delivery_date_model");
    }

    public function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

    public function index()
    {
        $header_data['title'] = "Delivery Date Management";
        $left_data['navigation'] = "delivery_date_management";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;
            $page_data["delivery_dates_list"] = $this->delivery_date_model->get_list_of_delivery_dates();
            $page_data["list_of_districts"] = $this->delivery_date_model->get_list_of_districts();

            $this->load->view('includes/header_view', $header_data);
            $this->load->view('includes/left_view', $left_data);
            $this->load->view('delivery_date_management_view', $page_data);
            $this->load->view('includes/footer_view');
        }
        else
        {
            redirect(base_url(''));
        }
    }

    public function add_delivery_date()
    {
        $missing_keys = [];

        if (!empty($this->input->post("hash_id")))
        {
            $hash_id = $this->input->post("hash_id");
        }

        if (!empty($this->input->post("district")))
        {
            $district = ucwords($this->input->post("district"));
        }
        else
        {
            $missing_keys[] = "district";
        }

        if (!empty($this->input->post("no_of_days_for_delivery")))
        {
            $no_of_days_for_delivery = $this->input->post("no_of_days_for_delivery");
        }
        else
        {
            $missing_keys[] = "no_of_days_for_delivery";
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
                $update_data = [
                    "district" => $district, 
                    "no_of_days_for_delivery" => $no_of_days_for_delivery, 
                    "created_date" => date("Y-m-d H:i:s"),
                    "status" => "A"
                ];
                $is_updated = $this->delivery_date_model->update_delivery_date_on_condition($update_data, $condition);
            }
            else
            {
                $condition = ["district" => $district];
                $prev_delivery_date_details = $this->delivery_date_model->get_delivery_date_details_on_condition($condition);
                if (!empty($prev_delivery_date_details->hash_id))
                {
                    $update_condition = ["hash_id" => $prev_delivery_date_details->hash_id];
                    $update_data = [
                        "district" => $district,
                        "no_of_days_for_delivery" => $no_of_days_for_delivery,
                        "created_date" => date("Y-m-d H:i:s"),
                        "status" => "A"
                    ];
                    $is_updated = $this->delivery_date_model->update_delivery_date_on_condition($update_data, $update_condition);
                }
                else
                {
                    $insert_data = [
                        "hash_id" => $this->GUID(),
                        "district" => $district,
                        "no_of_days_for_delivery" => $no_of_days_for_delivery,
                        "created_date" => date("Y-m-d H:i:s"),
                        "status" => "A"
                    ];
                    $is_added = $this->delivery_date_model->add_delivery_date($insert_data);
                }
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
                $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "Unable to add or update delivery date details in database!"];
            }
        }

        $this->response($response, 200);
    }

    public function delete_delivery_date()
    {
        if (!empty($this->input->post("hash_id")))
        {
            $condition = ["hash_id" => $this->input->post("hash_id")];
            $this->delivery_date_model->delete_delivery_date_on_condition($condition);
            $response = ["success" => true, "message" => "Deleted Successfully"];
        }
        else
        {
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "Deletable delivery date id is not given!"];
        }

        $this->response($response, 200);
    }

}

?>