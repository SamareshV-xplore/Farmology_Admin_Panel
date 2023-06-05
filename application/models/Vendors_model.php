<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendors_model extends CI_Model {

    function vendors_list()
    {
        $list = $this->db->get("FM_vendor")->result();
        return $list;
    }
}

?>