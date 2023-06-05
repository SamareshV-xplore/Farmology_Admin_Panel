<?php defined("BASEPATH") OR exit("No direct script access allowed");

    class Service_coupons_model extends CI_Model {

        public function add_service_coupon($data)
        {
            $this->db->insert("FM_service_coupons", $data);
            return $this->db->affected_rows();
        }
        
        public function get_service_coupons_list()
        {
            $sql = "SELECT * FROM FM_service_coupons WHERE status != 'D'";
            return $this->db->query($sql)->result();
        }

        public function delete_service_coupon($hash_id)
        {
            $this->db->set(["status" => "D"]);
            $this->db->where(["hash_id" => $hash_id]);
            $this->db->update("FM_service_coupons");
        }

    }

?>