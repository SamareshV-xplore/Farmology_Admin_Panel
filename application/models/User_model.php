<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_model extends CI_Model
{
    function send_otp_for_email($email = "")
    {
        $otp = rand(1111, 9999);
        if ($email != '')
        {
            // new entry
            $insert_data = array(
                "email" => $email,
                "otp" => $otp,
                "is_expired" => "N",
                "created_date" => date("Y-m-d H:i:s")
            );
            $this->db->insert("FM_email_otp_list", $insert_data);
            // send email
            $subject = "Please verify email - Fleshkart.com";
            
            $email_data = array("otp" => $otp);

            $email_body = $this->load->view('email-template/sign_in_sign_up_view', $email_data, true);
            $this->common_model->email_send($email, $subject, $email_body);
            return true;
        }
        else
        {
            return false;
        }
    }
    function varify_email_otp($email = "", $otp = "")
    {
        $response = array(
            "status" => "N",
            "message" => "Invalid OTP. PLease enter valid OTP."
        );
        if ($email != "")
        {
            $this->db->select("id");
            $this->db->from("FM_email_otp_list");
            $this->db->where("email", $email);
            $this->db->where("otp", $otp);
            $this->db->where("is_expired", "N");
            $query = $this->db->get();
            if ($query->num_rows() > 0)
            {
                $this->db->select("id");
                $this->db->from("FM_email_otp_list");
                $this->db->where("email", $email);
                $this->db->where("is_expired", "N");
                $ch_query = $this->db->get();
                if ($ch_query->num_rows() > 0)
                {
                    $update_data = array(
                        "is_expired" => "Y"
                    );
                    $this->db->where("email", $email);
                    $this->db->where("is_expired", "N");
                    $this->db->update("FM_email_otp_list", $update_data);
                }
                $response = array(
                    "status" => "Y",
                    "message" => "Email successfully varified by OTP."
                );
            }
        }
        return $response;
    }
    function user_details_by_id($user_id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where("id", $user_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row     = $query->row();
            $details = array(
                "id" => $row->id,
                "first_name" => $row->first_name,
                "last_name" => $row->last_name,
                "full_name" => trim($row->first_name . " " . $row->last_name),
                "email" => $row->email, 
                "phone" => $row->phone,
                "profile_image" => FRONT_URL . $row->profile_image,
                "status" => $row->status,
                "registration_date" => $row->created_date
            );
        }
        return $details;
    }
    function common_login_register($email = "", $phone = "")
    {
        $response = array(
            "status" => "N",
            "message" => "Something is wrong!"
        );
        if ($phone != "")
        {
            $this->db->select("id");
            $this->db->from("FM_customer");
            $this->db->where("phone", $phone);
            $this->db->where("status", "Y");
            $query = $this->db->get();
            if ($query->num_rows() > 0)
            {
                $row          = $query->row();
                $user_details = $this->user_details_by_id($row->id);
                $response     = array(
                    "status" => "Y",
                    "message" => "Successfully login by phone number.",
                    "user_details" => $user_details
                );
            }
            else
            {
                // register by phone
                $insert_data = array(
                    "phone" => $phone,
                    "status" => "Y",
                    "created_date" => date("Y-m-d H:i:s")
                );
                $this->db->insert("FM_customer", $insert_data);
                $user_id      = $this->db->insert_id();
                $user_details = $this->user_details_by_id($user_id);
                $response     = array(
                    "status" => "Y",
                    "message" => "Successfully registred by phone number.",
                    "user_details" => $user_details
                );
            }
        }
        else if ($email != "")
        {
            $this->db->select("id");
            $this->db->from("FM_customer");
            $this->db->where("email", $email);
            $this->db->where("status", "Y");
            $query = $this->db->get();
            if ($query->num_rows() > 0)
            {
                $row          = $query->row();
                $user_details = $this->user_details_by_id($row->id);
                $response     = array(
                    "status" => "Y",
                    "message" => "Successfully login by email.",
                    "user_details" => $user_details
                );
            }
            else
            {
                // register by email
                $insert_data = array(
                    "email" => $email,
                    "status" => "Y",
                    "created_date" => date("Y-m-d H:i:s")
                );
                $this->db->insert("FM_customer", $insert_data);
                $user_id      = $this->db->insert_id();
                $user_details = $this->user_details_by_id($user_id);
                $response     = array(
                    "status" => "Y",
                    "message" => "Successfully registred by email.",
                    "user_details" => $user_details
                );
            }
        }
        else
        {
            // do nothing..
        }
        return $response;
    }
    function update_user_device_info($data = array())
    {
        if (count($data) > 0)
        {
            $user_id      = $data['user_id'];
            $device_type  = $data["device_type"];
            $device_token = $data["device_token"];
            // check data
            $this->db->select("id");
            $this->db->from("FM_customer_device_details");
            $this->db->where("customer_id", $user_id);
            $query = $this->db->get();
            if ($query->num_rows() > 0)
            {
                $row         = $query->row();
                // update new details
                $update_data = array(
                    "device_type" => $device_type,
                    "device_token" => $device_token,
                    "updated_date" => date("Y-m-d H:i:s")
                );
                $this->db->where("id", $row->id);
                $this->db->update("FM_customer_device_details", $update_data);
            }
            else
            {
                // create new details
                $insert_data = array(
                    "customer_id" => $user_id,
                    "device_type" => $device_type,
                    "device_token" => $device_token,
                    "updated_date" => date("Y-m-d H:i:s")
                );
                $this->db->insert("FM_customer_device_details", $insert_data);
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    function check_email_exist_check($user_id = 0, $email = "")
    {
        $this->db->select("email");
        $this->db->from("FM_customer");
        $this->db->where("email", $email);
        if ($user_id > 0)
        {
            $this->db->where("id !=", $user_id);
        }
        $this->db->where("status !=", "D");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $response = array(
                "status" => "N",
                "message" => "Email already exist with another account."
            );
        }
        else
        {
            $response = array(
                "status" => "Y",
                "message" => "Email available."
            );
        }
        return $response;
    }
    function check_phone_exist_check($user_id = 0, $phone = "0")
    {
        $this->db->select("phone");
        $this->db->from("FM_customer");
        $this->db->where("phone", $phone);
        if ($user_id > 0)
        {
            $this->db->where("id !=", $user_id);
        }
        $this->db->where("status !=", "D");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $response = array(
                "status" => "N",
                "message" => "Phone number already exist with another account."
            );
        }
        else
        {
            $response = array(
                "status" => "Y",
                "message" => "Phone number available."
            );
        }
        return $response;
    }
    function update_email($user_id = 0, $email = "")
    {
        $update_data = array(
            "email" => $email,
            "updated_date" => date("Y-m-d H:i:s")
        );
        $this->db->where("id", $user_id);
        $this->db->update("FM_customer", $update_data);
        return true;
    }
    function update_phone($user_id = 0, $phone = "0")
    {
        $update_data = array(
            "phone" => $phone,
            "updated_date" => date("Y-m-d H:i:s")
        );
        $this->db->where("id", $user_id);
        $this->db->update("FM_customer", $update_data);
        return true;
    }
    function update_name($data)
    {
        $user_id     = $data['user_id'];
        $first_name  = ucfirst(strtolower($data['first_name']));
        $last_name   = ucfirst(strtolower($data['last_name']));
        $update_data = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "updated_date" => date("Y-m-d H:i:s")
        );
        $this->db->where("id", $user_id);
        $this->db->update("FM_customer", $update_data);
        return true;
    }
    function address_list($customer_id = 0)
    {
        $list = array();
        $this->db->select("*");
        $this->db->from("FM_customer_address");
        $this->db->where("customer_id", $customer_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $state_name = $this->common_model->get_state_name_by_id($row->state_id);
                $city_name  = $this->common_model->get_city_name_by_id($row->city_id);
                $list[]     = array(
                    "id" => $row->id,
                    "name" => $row->name,
                    "phone" => $row->phone,
                    "address_1" => $row->address_1,
                    "address_2" => $row->address_2,
                    "landmark" => $row->landmark,
                    "state_id" => $row->state_id,
                    "state_name" => $state_name,
                    "city_id" => $row->city_id,
                    "city_name" => $city_name,
                    "zip_code" => $row->zip_code
                );
            }
        }
        return $list;
    }
    function get_zip_code_by_address_id($address_id = 0)
    {   
        $zip_code = "";
        $this->db->select("zip_code");
        $this->db->from("FM_customer_address");
        $this->db->where("id", $address_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $zip_code = $query->row()->zip_code;
        }
        return $zip_code;


    }

    function get_address_details_by_id($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_customer_address");
        $this->db->where("id", $id);
        //$this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            { 
                $state_name = $this->common_model->get_state_name_by_id($row->state_id);
                $city_name  = $this->common_model->get_city_name_by_id($row->city_id);
                $district_name = $this->common_model->get_district_name_by_id($row->district_id);
                $details    = array(
                    "id" => $row->id,
                    "name" => $row->name,
                    "phone" => $row->phone,
                    "address_1" => $row->address_1,
                    "address_2" => $row->address_2,
                    "landmark" => $row->landmark,
                    "state_id" => $row->state_id,
                    "state_name" => $state_name,
                    "district_id" => $row->district_id,
                    "district_name" => $district_name,
                    "city_id" => $row->city_id,
                    "city_name" => $city_name,
                    "zip_code" => $row->zip_code
                );
            }
        }
        return $details;
    }
    function add_new_address($data)
    {
        $customer_id = $data['customer_id'];
        $name        = ucfirst(strtolower($data['name']));
        $phone       = $data['phone'];
        $address_1   = $data['address_1'];
        $address_2   = $data['address_2'];
        $landmark    = $data['landmark'];
        $zip_code    = $data['zip_code'];
        $city_id     = $this->common_model->get_city_id_by_pincode($zip_code);
        $state_id    = $this->common_model->get_state_id_by_city_id($city_id);
        $insert_data = array(
            "customer_id" => $customer_id,
            "name" => $name,
            "phone" => $phone,
            "address_1" => $address_1,
            "address_2" => $address_2,
            "landmark" => $landmark,
            "state_id" => $state_id,
            "city_id" => $city_id,
            "zip_code" => $zip_code
        );
        $this->db->insert("FM_customer_address", $insert_data);
        return $address_id = $this->db->insert_id();
    }
    function delete_address($id, $customer_id)
    {
        $update_data = array(
            "is_deleted" => "Y"
        );
        $this->db->where("customer_id", $customer_id);
        $this->db->where("id", $id);
        $this->db->update("FM_customer_address", $update_data);
        return true;
    }
}