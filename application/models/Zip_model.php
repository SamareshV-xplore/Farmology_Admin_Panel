<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Zip_model extends CI_Model {

    //Get Zip list
    function zip_list($city_id)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_pin_code_lookup");
        $this->db->where("city_id", $city_id);
        $this->db->where("is_deleted", "N");
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "city_id" => $row->city_id,
                    "pin_code" => $row->pin_code,
                    "created_date" => $row->created_date
                );
            }
        }
        return $list;
    }

    function city_by_id($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_city_lookup");
        $this->db->where("id", $id);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $details = array("id" => $rows->id, "city_name" => $rows->name);
            }
        }

        return $details;
    }

    //Add zip data
    function add_zip($data)
    {
        $city_id = $data['city_id'];
        $pin_code = $data['pin_code'];
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array(
            "city_id" =>  $city_id,
            "pin_code" => $pin_code,
            "created_date" => $created_date
        );
        $this->db->insert("FM_pin_code_lookup", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New Zip added", "id" => $id);

        return $response;

    }

    /**
     * @param $data
     * @return array
     * update zip code details
     */
    function update_zip_code($data)
    {
        $id = $data['zip_id'];
        // before update zip check Zip ID
        $this->db->select("id");
        $this->db->from("FM_pin_code_lookup");
        $this->db->where("is_deleted", "N");
        $this->db->where("id", $id);
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Maybe zip code already deleted.");
        }
        else
        {
            $update_data = array(
                "pin_code" =>  $data['zip_code'],
            );
            $this->db->where("id", $id);
            $this->db->update("FM_pin_code_lookup", $update_data);
            $response = array("status" => "Y", "message" => "Zip Code Details updated.");

        }
        return $response;
    }

    // Zip Delete
    function delete_zip_by_id($id)
    {
        $this->db->select("id");
        $this->db->from("FM_pin_code_lookup");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $update_data = array("is_deleted" => "Y");
            $this->db->where("id", $id);
            $this->db->update("FM_pin_code_lookup", $update_data);

            $response = array("status" => "Y", "message" => "Zip code successfully deleted.");
        }
        else
        {
            $response = array("status" => "N", "message" => "Zip code already deleted.");
        }
        return $response;
    }
}
