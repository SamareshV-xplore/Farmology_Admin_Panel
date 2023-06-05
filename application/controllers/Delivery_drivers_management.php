<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Delivery_drivers_management extends CI_Controller {
 
    public function __construct()
    {
        parent::__construct();
        $this->load->model("delivery_drivers_model");
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
        $header_data['title'] = "Delivery Drivers List";
        $left_data['navigation'] = "delivery_drivers";
        $left_data['sub_navigation'] = "delivery_drivers_list";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;

            if (isset($_REQUEST['filter'])) {
                $filter_data = array("status" => $_REQUEST['status']);
                $status = ($_REQUEST['status'] != "all") ? $_REQUEST['status'] : NULL;
            } else {
                $filter_data = array("status" => 'all');
                $status = NULL;
            }
            $page_data['filter_data'] = $filter_data;
            $page_data['delivery_drivers_list'] = $this->delivery_drivers_model->get_delivery_drivers_list_by_status($status);

            $this->load->view('includes/header_view', $header_data);
            $this->load->view('includes/left_view', $left_data);
            $this->load->view('delivery_drivers/list_view', $page_data);
            $this->load->view('includes/footer_view');
        }
        else
        {
            redirect(base_url(''));
        }
    }

    public function add_delivery_driver()
    {
        $header_data['title'] = "Add Delivery Driver";
        $left_data['navigation'] = "delivery_drivers";
        $left_data['sub_navigation'] = "add_delivery_driver";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;

            $page_data["states_list"] = $this->delivery_drivers_model->get_available_states_list();
            $page_data["district_list"] = $this->delivery_drivers_model->get_districts_list_by_state_id();

            $this->load->view('includes/header_view', $header_data);
            $this->load->view('includes/left_view', $left_data);
            $this->load->view('delivery_drivers/add_view', $page_data);
            $this->load->view('includes/footer_view');
        }
        else
        {
            redirect(base_url(''));
        }
    }

    public function get_districts_list_by_state_id($state_id)
    {
        $districts_list = $this->delivery_drivers_model->get_districts_list_by_state_id($state_id);
        if (!empty($districts_list))
        {
            $response = ["success" => true, "message" => "Districts list get successfully.", "districts_list" => $districts_list];
        }
        else
        {
            $response = ["success" => false, "message" => "Failed to get districts list!"];
        }
        
        $this->response($response, 200);
    }

    public function get_available_pincodes_list_by_district_id($district_id)
    {
        $available_pincodes_list = $this->delivery_drivers_model->get_pincodes_list_by_district_id($district_id);
        if (!empty($available_pincodes_list))
        {
            $response = ["success" => true, "message" => "Pincodes list get successfully.", "available_pincodes_list" => $available_pincodes_list];
        }
        else
        {
            $response = ["success" => false, "message" => "Failed to get pincodes list!"];
        }
        
        $this->response($response, 200);
    }

    public function upload_image($image, $image_upload_directory)
    {
        $uploaded_image_path = NULL;
        if (!empty($image) && !empty($image_upload_directory))
        {
            $file_name = $this->GUID();
            $file_extension = ".".pathinfo($image["name"], PATHINFO_EXTENSION);
            
            $file_save_path = $image_upload_directory.$file_name.$file_extension;
            $file_upload_path = FILE_UPLOAD_BASE_PATH.$file_save_path;

            if (move_uploaded_file($image["tmp_name"], $file_upload_path))
            {
                $uploaded_image_path = $file_save_path;
            }
        }
        return $uploaded_image_path;
    }

    public function delete_image($image_path)
    {
        if (!empty($image_path))
        {
            unlink(FCPATH."media/".$image_path);
        }
    }

    public function add_new_delivery_driver()
    {
        $missing_keys = [];
        $data = [];

        if (!empty($this->input->post("name")))
        {
            $data["name"] = $this->input->post("name");
        }
        else
        {
            $missing_keys[] = "name";
        }

        if (!empty($this->input->post("phone")))
        {
            $data["phone"] = $this->input->post("phone");
        }
        else
        {
            $missing_keys[] = "phone";
        }

        if (!empty($this->input->post("email")))
        {
            $data["email"] = $this->input->post("email");
        }

        if (empty($_FILES["payment_qr_code_image"]["name"]))
        {
            $missing_keys[] = "payment_qr_code_image";
        }

        if (!empty($this->input->post("state_id")))
        {
            $data["state_id"] = $this->input->post("state_id");
        }
        else
        {
            $missing_keys[] = "state_id";
        }

        if (!empty($this->input->post("district_id")))
        {
            $data["district_id"] = $this->input->post("district_id");
        }
        else
        {
            $missing_keys[] = "district_id";
        }

        if (!empty($this->input->post("available_pincodes")))
        {
            $data["available_pincodes"] = implode(",", $this->input->post("available_pincodes"));
            $data["available_pincodes"] = rtrim($data["available_pincodes"], ",");
        }

        if (!empty($missing_keys))
        {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtrim($missing_string, ", ");
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => $missing_string." not given!"];
        }
        else
        {
            $data["driver_id"] = $this->GUID();
            $data["payment_qr_code_image"] = $this->upload_image($_FILES["payment_qr_code_image"], "uploads/delivery_driver_payment_qr_code_images/");
            $data["created_date"] = date("Y-m-d H:i:s");
            

            if (!empty($_FILES["profile_image"]["name"]))
            {
                $data["profile_image"] = $this->upload_image($_FILES["profile_image"], "uploads/delivery_driver_profile_images/");
            }

            $is_added = $this->delivery_drivers_model->add_delivery_driver($data);
            if ($is_added)
            {
                $response = ["success" => true, "message" => "Delivery Driver Added Successfully", "redirect_to" => base_url("delivery-drivers-list")];
            }
            else
            {
                $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "failed to add delivery driver details in database"];
            }
        }

        $this->response($response, 200);
    }

    public function edit_delivery_driver($driver_id)
    {
        $header_data['title'] = "Edit Delivery Driver";
        $left_data['navigation'] = "delivery_drivers";
        $left_data['sub_navigation'] = "delivery_drivers_list";

        if($this->common_model->user_login_check())
        {
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;

            $page_data["driver_details"] = $this->delivery_drivers_model->get_delivery_driver_details_by_id($driver_id);
            $page_data["states_list"] = $this->delivery_drivers_model->get_available_states_list();
            if (!empty($page_data["driver_details"]->state_id))
            {
                $page_data["district_list"] = $this->delivery_drivers_model->get_districts_list_by_state_id($page_data["driver_details"]->state_id);
            }
            else
            {
                $page_data["district_list"] = $this->delivery_drivers_model->get_districts_list_by_state_id();
            }

            if (!empty($page_data["driver_details"]->district_id))
            {
                $page_data["available_pincodes_list"] = $this->delivery_drivers_model->get_pincodes_list_by_district_id($page_data["driver_details"]->district_id);
            }

            $this->load->view('includes/header_view', $header_data);
            $this->load->view('includes/left_view', $left_data);
            $this->load->view('delivery_drivers/edit_view', $page_data);
            $this->load->view('includes/footer_view');
        }
        else
        {
            redirect(base_url(''));
        }
    }

    public function edit_existing_delivery_driver()
    {
        if (!empty($this->input->post("driver_id")))
        {
            $driver_id = $this->input->post("driver_id");
            $previous_driver_details = $this->delivery_drivers_model->get_delivery_driver_details_by_id($driver_id);

            $_POST["available_pincodes"] = implode(",", $_POST["available_pincodes"]);
            $_POST["available_pincodes"] = rtrim($_POST["available_pincodes"], ",");
            unset($_POST["driver_id"]);
            $update_data = $_POST;

            if (!empty($_FILES["profile_image"]["name"]) && !empty($previous_driver_details->profile_image))
            {
                $this->delete_image($previous_driver_details->profile_image);
                $update_data["profile_image"] = $this->upload_image($_FILES["profile_image"], "uploads/delivery_driver_profile_images/");
            }
            elseif (!empty($_FILES["profile_image"]["name"]) && empty($previous_driver_details->profile_image))
            {
                $update_data["profile_image"] = $this->upload_image($_FILES["profile_image"], "uploads/delivery_driver_profile_images/");
            }

            if (!empty($_FILES["payment_qr_code_image"]["name"]) && !empty($previous_driver_details->payment_qr_code_image))
            {
                $this->delete_image($previous_driver_details->payment_qr_code_image);
                $update_data["payment_qr_code_image"] = $this->upload_image($_FILES["payment_qr_code_image"], "uploads/delivery_driver_payment_qr_code_images/");
            }

            $condition = ["driver_id" => $driver_id];
            $this->delivery_drivers_model->update_delivery_driver_on_condition($condition, $update_data);
            $response = ["success" => true, "message" => "Saved Successfully", "redirect_to" => base_url("delivery-drivers-list")];
        }
        else
        {
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => "Driver Id is not given!"];
        }

        $this->response($response, 200);
    }

    public function delete_delivery_driver()
    {
        if (!empty($this->input->post("driver_id")))
        {
            $driver_id = $this->input->post("driver_id");
            $previous_driver_details = $this->delivery_drivers_model->get_delivery_driver_details_by_id($driver_id);

            if (!empty($previous_driver_details->profile_image))
            {
                $this->delete_image($previous_driver_details->profile_image);
            }

            if (!empty($previous_driver_details->payment_qr_code_image))
            {
                $this->delete_image($previous_driver_details->payment_qr_code_image);
            }

            $this->delivery_drivers_model->delete_delivery_driver($driver_id);
            $response = ["success" => true, "message" => "Deleted Successfully"];
        }
        else
        {
            $response = ["success" => false, "message" => "Someting went wrong! Please try again later.", "console_message" => "Driver Id is not given!"];
        }

        $this->response($response, 200);
    }

}