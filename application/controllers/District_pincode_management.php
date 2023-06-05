<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class District_pincode_management extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model("district_pincode_model");
    }

    public function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

    public function index($district_id = NULL)
    {
        $header_data["title"] = "District Pincode List";
        $left_data["navigation"] = "district";
        $left_data["sub_navigation"] = "district-list";

        if($this->common_model->user_login_check())
        {
            if (!empty($district_id))
            {
                $admin_details = $this->common_model->get_admin_user_details();
                $header_data['admin_details'] = $admin_details;
                $left_data['admin_details'] = $admin_details;

                $pincodes_list = $this->district_pincode_model->get_pincodes_list_by_district_id($district_id);
                $district_details = $this->district_pincode_model->get_district_details_by_id($district_id);
                $page_data["pincodes_list"] = $pincodes_list;
                $page_data["district_details"] = $district_details;

                $this->load->view('includes/header_view', $header_data);
                $this->load->view('includes/left_view', $left_data);
                $this->load->view('pincode/list_view', $page_data);
                $this->load->view('includes/footer_view');
            }
            else
            {
                redirect(base_url("district-list"));
            }
        }
        else
        {
            redirect(base_url());
        }
    }

    //Zip Add
    function add_zip()
    {
        if($this->common_model->user_login_check())
        {
            // allow to access
        }
        else
        {
            redirect(base_url(''));
        }

        if($this->input->post('city_id') && $this->input->post('city_id') !== null)
        {
            $city_id = $this->input->post('city_id');
            $form_data = array();
            $form_data['city_id'] = $this->input->post('city_id');
            $form_data['pin_code'] = $this->input->post('pin_code');

            $add_data = $this->zip_model->add_zip($form_data);
            if($add_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $add_data['message']);
                redirect(base_url('zip-list/'.$city_id));
            }
            else
            {
                $this->session->set_flashdata('error_message', $add_data['message']);
                redirect(base_url('zip-list/'.$city_id));
            }
        }
        else
        {
            redirect(base_url('city-list/'));
        }

    }

    public function add_pincode()
    {
        $missing_keys = [];

        if (!empty($this->input->post("district_id")))
        {
            $district_id = $this->input->post("district_id");
        }
        else
        {
            $missing_keys[] = "district_id";
        }

        if (!empty($this->input->post("pincode")))
        {
            $pincode = $this->input->post("pincode");
        }
        else
        {
            $missing_keys[] = "pincode";
        }

        if (!empty($missing_keys))
        {
            $missing_string = implode(", ", $missing_keys);
            $missing_string = rtimr($missing_string, ", ");
            $response = ["success" => false, "message" => "Something went wrong! Please try again later.", "console_message" => $missing_string." not given!"];
        }
        else
        {
            $is_added = $is_updated = 0;
            $pincode_details = $this->district_pincode_model->get_pincode_details_on_condition($pincode);
            if (!empty($pincode_details))
            {
                $is_updated = $this->district_pincode_model->update_pincode_on_condition($district_id, $pincode);
            }
            else
            {
                $data = [
                    "district_id" => $district_id,
                    "pin_code" => $pincode,
                    "created_date" => date("Y-m-d H:i:s"),
                    "is_deleted" => "N"
                ];
                $is_added = $this->district_pincode_model->add_pincode($data);
            }

            if ($is_updated)
            {
                $response = ["success" => true, "message" => "Saved Successfully"];
            }
            elseif ($is_added)
            {
                $response = ["success" => true, "message" => "Added Successfully"];
            }
            else
            {
                $response = ["success" => false, "message" => "Something went wrong! Please try again later."];
            }
        }

        $this->response($response, 200);
    }

    //Zip Update
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

        if($this->input->post('zip_id'))
        {
            $form_data = array();
            $zip_id = $this->input->post('zip_id');
            $form_data['zip_id'] = $zip_id;
            $form_data['zip_code'] = $this->input->post('zip_code');

            $update_data = $this->zip_model->update_zip_code($form_data);
            if($update_data['status'] == "Y")
            {
                $this->session->set_flashdata('success_message', $update_data['message']);
                redirect($_SERVER['HTTP_REFERER']);
            }
            else
            {
                $this->session->set_flashdata('error_message', $update_data['message']);
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else
        {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    //Zip Delete
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

        $delete_zip = $this->zip_model->delete_zip_by_id($id);
        if($delete_zip['status'] == "Y")
        {
            $this->session->set_flashdata('success_message', $delete_zip['message']);
        }
        else
        {
            $this->session->set_flashdata('error_message', $delete_zip['message']);
        }
        redirect($_SERVER['HTTP_REFERER']);

    }
    
    public function delete_pincode()
    {
        if (!empty($this->input->post("pincode")))
        {
            $pincode = $this->input->post("pincode");
            $condition = ["pin_code" => $pincode];
            $this->district_pincode_model->delete_pincode_on_condition($condition);
            $response = ["success" => true, "message" => "Deleted Successfully"];
        }
        else
        {
            $response = ["success" => false, "message" => "District ID is not given!"];
        }

        $this->response($response, 200);
    }
}
