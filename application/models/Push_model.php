<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push_model extends CI_Model {

    //Get users list
    function users_list()
    {
        $list = array();

        $this->db->select("
        FM_customer.id,FM_customer.first_name,FM_customer.last_name,FM_customer.email,FM_customer.phone,FM_customer.status,
        FM_customer_device_details.device_type,FM_customer_device_details.device_token,FM_customer_device_details.app_version
        ");
        $this->db->from("FM_customer");
        $this->db->where("status", "Y");
        $this->db->join('FM_customer_device_details', 'FM_customer.id = FM_customer_device_details.customer_id');
        $this->db->order_by("FM_customer.id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "first_name" => $row->first_name,
                    "last_name" => $row->last_name,
                    "email" => $row->email,
                    "phone" => $row->phone,
                    "status" => $row->status,
                    "device_type" => $row->device_type,
                    "device_token" => $row->device_token,
                    "app_version" => $row->app_version
                );
            }
        }
        return $list;
    }

    public function get_new_user_device_tokens ()
    {
        $list = array();
        $sql = "SELECT FMCDD.device_token FROM FM_customer FMC INNER JOIN FM_customer_device_details FMCDD ON FMC.id = FMCDD.customer_id WHERE FMC.status = 'Y' AND (FMCDD.app_version IS NOT NULL AND FMCDD.app_version >= 2) ORDER BY FMC.id DESC";
        $query = $this->db->query($sql);
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = $row->device_token;
            }
        }
        return $list;
    }

    public function get_old_user_device_tokens ()
    {
        $list = array();
        $sql = "SELECT FMCDD.device_token FROM FM_customer FMC INNER JOIN FM_customer_device_details FMCDD ON FMC.id = FMCDD.customer_id WHERE FMC.status = 'Y' AND (FMCDD.app_version IS NULL OR FMCDD.app_version = 0 OR FMCDD.app_version = '') ORDER BY FMC.id DESC";
        $query = $this->db->query($sql);
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = $row->device_token;
            }
        }
        return $list;
    }
    

    function find_device_by_id($ids){
        $this->db->select("device_type,device_token");
        $this->db->from("FM_customer_device_details");
        $this->db->where_in('customer_id', $ids);
        $query = $this->db->get();

        if($query->num_rows() > 0) {
            $details = $query->result_array();
            $response = array("status" => "Y", "message" => "Details found", "details" => $details);
        }else{
            $response = array("status" => "N", "message" => "No details found.");
        }
        return $response;
    }

    function find_all_devices(){
        $this->db->select("id");
        $this->db->from("FM_customer");
        $this->db->where("status", "Y");
        $query = $this->db->get();

        if($query->num_rows() > 0) {
            $details = $query->result_array();
            $response = array("status" => "Y", "message" => "Details found", "details" => $details);
        }else{
            $response = array("status" => "N", "message" => "No details found.");
        }
        return $response;
    }

}
