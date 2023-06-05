<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login_model extends CI_Model
{
    function user_login($username, $password)
    {
        $response = array("status" => "N", "message" => "You have entered an invalid username or password.");
        $password = md5($password);

        $query = $this->db->query("select * from FM_admin_user where (email = '".$username."' or username = '".$username."') and is_deleted = 'N'");
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            if($row->status == 'N')
            {
                $response = array("status" => "N", "message" => "You are currently inactive by admin. Please contact site admin.");
            }
            else if($row->password == $password)
            {
                $response = array("status" => "Y", "message" => "Login Success.");
                $this->session->set_userdata('admin_user_id', $row->id);
                $this->session->set_userdata('admin_user_type', $row->user_type);
            }
            else
            {
                $response = array("status" => "N", "message" => "You have entered an invalid password.");
            }
        }
        else
        {
            $response = array("status" => "N", "message" => "You have entered an invalid username or email.");
        }
        return $response;
    }
    
}

?>