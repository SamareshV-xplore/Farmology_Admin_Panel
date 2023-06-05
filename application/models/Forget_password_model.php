<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Forget_password_model extends CI_Model
{
    function check_username_for_forget_password($username)
    {
        $response = array("status" => "N", "message" => "You have entered an invalid username");

        $query = $this->db->query("select * from FM_admin_user where (email = '".$username."' or username = '".$username."') and is_deleted = 'N'");
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            if($row->status == 'N')
            {
                $response = array("status" => "N", "message" => "You are currently inactive by admin. Please contact site admin.");
            }
            else
            {

                $rand_number = time().rand('1111', '9999');
                $email_to = $row->email;
                $email_subject = "Reset Password";
                $email_body = "<p>Hello, ".$row->name."</p>";
                $email_body.= "<p>We got a request to reset your password. Click here for <a href='".base_url('reset-password/'.$rand_number)."'>Reset your password</a>.</p>";
                $email_body.= "<p>If you ignore this message, your password won't be changed and it will be automatically expire after 24 hours.</p>";
                $this->common_model->email_send($email_to, $email_subject, $email_body);
                $this->create_entry_for_forget_password(array("user_id" => $row->id, "unique_id" => $rand_number));

                $response = array("status" => "Y", "message" => "We have sent you an mail with reset password link to your registred email.");
            }
        }
        else
        {
            $response = array("status" => "N", "message" => "You have entered an invalid username or email.");
        }
        return $response;
    }

    function create_entry_for_forget_password($data)
    {
        $user_id = $data['user_id'];
        $unique_id = $data['unique_id'];
        $expire_on = date('Y-m-d H:i:s', strtotime('+1 day', strtotime(date("Y-m-d H:i:s"))));

        $insert_data = array("user_id" => $user_id, "unique_id" => $unique_id, "expire_on" => $expire_on);
        $this->db->insert("FM_reset_password", $insert_data);

        return true;
    }

    function change_password_for_reset($user_id, $password, $unique_id)
    {        
        // update password
        $update_data = array("password" => md5($password), "updated_at" => date("Y-m-d H:i:s"));
        $this->db->where("id", $user_id);
        $this->db->update("FM_admin_user", $update_data);

        //expire reset unique_id
        $update_unique_destroy_data = array("expiry_status" => "Y");
        $this->db->where("unique_id", $unique_id);
        $this->db->where("user_id", $user_id);
        $this->db->update("FM_reset_password", $update_unique_destroy_data);

        $response = array("status" => "Y", "message" => "Your password successfully change. Please login with your new password.");
        return $response;        
    }

    function get_data_from_unique_id($unique_id)
    {
        $response = array("status" => "N", "message" => "Invalid URL or this link has been expired.");
        
        $query = $this->db->query("select user_id from FM_reset_password where unique_id = '".$unique_id."' and expiry_status = 'N' and expire_on > '".date("Y-m-d H:i:s")."'");
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $response = array("status" => "Y", "message" => "Data found.", "user_id" => $row->user_id);

        }
        return $response;
    }
    
}

?>