<?php defined("BASEPATH") OR exit("No direct script access allowed");

class User_referrals_model extends CI_Model {

    public function add_user_referrals_data($data)
    {
        return $this->db->insert("FM_user_referrals", $data);
    }

}

?>