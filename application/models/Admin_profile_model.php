<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_profile_model extends CI_Model
{
    function update_profile_data($data)
    {
        $error_status = "N";
        $name = ucwords(strtolower($data['name']));
        $email = strtolower($data['email']);
        $username = $data['username'];
        $phone = $data['phone'];
        $user_id = $this->session->userdata('admin_user_id');

        // check email 
        $this->db->select("id");
        $this->db->from("FM_admin_user");
        $this->db->where("id !=", $user_id);
        $this->db->where("email", $email);
        $this->db->where("is_deleted", "N");
        $email_check = $this->db->get();
        if($email_check->num_rows() > 0)
        {
            $response = array("status" => "N", "message" => "This email already exist with another user.");
            $error_status = "Y";
        }

        // check username 
        $this->db->select("id");
        $this->db->from("FM_admin_user");
        $this->db->where("id !=", $user_id);
        $this->db->where("username", $username);
        $this->db->where("is_deleted", "N");
        $username_check = $this->db->get();
        if($username_check->num_rows() > 0 && $error_status == "N")
        {
            $response = array("status" => "N", "message" => "This username already exist with another user.");
            $error_status = "Y";
        }

        if($error_status == "N")
        {
            // update data
            $update_data = array("name" => $name, "username" => $username, "email" => $email, "phone" => $phone, "updated_at" => date("Y-m-d H:i:s"));
            $this->db->where("id", $user_id);
            $this->db->update("FM_admin_user", $update_data);
            $response = array("status" => "Y", "message" => "Profile info successfully updated.");
        }
        return $response;

    }

    function update_password_data($password)
    {
        $user_id = $this->session->userdata('admin_user_id');
        $update_data = array("password" => md5($password), "updated_at" => date("Y-m-d H:i:s"));
        $this->db->where("id", $user_id);
        $this->db->update("FM_admin_user", $update_data);

        $response = array("status" => "Y", "message" => "Profile password successfully changed.");
        return $response;
    }
}

?>