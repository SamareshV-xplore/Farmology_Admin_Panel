<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class District_model extends CI_Model {
    //Get City list
    function city_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_district_lookup");
        if($filter_data['status'] == 'Y')
        {
            $this->db->where("status", "Y");
        }
        elseif($filter_data['status'] == 'N')
        {
            $this->db->where("status", "N");
        }
        else
        {
            $this->db->where("status !=", "D");
        }
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $state_details = $this->state_by_id($row->state_id);
                $list[] = array(
                    "id" => $row->id,
                    "state_details" => $state_details,
                    "name" => $row->name,
                    "charge" => $row->charge,
                    "status" => $row->status,
                    "created_date" => $row->created_date
                );
            }
        }
        return $list;
    }

    function state_by_id($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_state_lookup");
        $this->db->where("id", $id);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $details = array("id" => $rows->id, "state" => $rows->state);
            }
        }

        return $details;
    }

    //Get City list
    function state_list()
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_state_lookup");
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "state" => $row->state,
                    "created_date" => $row->created_date
                );
            }
        }
        return $list;
    }


    function get_state_list()
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_state_lookup");
        $this->db->where("is_deleted","Y");
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "state" => $row->state,
                    "created_date" => $row->created_date
                );
            }
        }
        return $list;
    }

    //Add city data
    function add_district($data)
    {
        $state_id = $data['state_id'];
        $name = $data['name'];
        $charge = $data['charge'];
        $status = $data['status'];
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array(
            "state_id" =>  $state_id,
            "name" => $name,
            "charge" => $charge,
            "status" => $status,
            "created_date" => $created_date
        );
        $this->db->insert("FM_district_lookup", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New District added", "id" => $id);

        return $response;

    }

    function single_district_details($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_district_lookup");
        $this->db->where("id", $id);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $details = array(
                "id" => $row->id,
                "state_id" => $row->state_id,
                "district_name" => $row->name,
                "charge" => $row->charge,
                "image" => $row->image,
                "created_date" => $row->created_date,
                "status" => $row->status,
            );

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found. Maybe district is already deleted.");
        }
        return $response;
    }

    // Update image data
    function update_image($id, $image, $update_type)
    {
        if($update_type == 'first')
        {
            $update_data = array("image" => $image);
        }
        else
        {
            $update_data = array("image" => $image, "created_date" => date("Y-m-d H:i:s"));
        }

        $this->db->where("id", $id);
        $this->db->update("FM_district_lookup", $update_data);
        return true;

    }

    function update_district($data)
    {
        $id = $data['id'];
        $name = $data['district'];
        $charge = $data['charge'];
        $state_id = $data['state_id'];
        $status = $data['status'];

        // before update banner check banner ID
        $this->db->select("id");
        $this->db->from("FM_district_lookup");
        $this->db->where("id", $id);
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Maybe district already deleted.");
        }
        else
        {
            $update_data = array(
                "name" =>  $name,
                "charge" =>  $charge,
                "state_id" => $state_id,
                "status" => $status
            );
            $this->db->where("id", $id);
            $this->db->update("FM_district_lookup", $update_data);
            $response = array("status" => "Y", "message" => "District Details updated.");

        }
        return $response;
    }

    // Banner Delete
    function delete_city_by_id($id)
    {
        $this->db->select("id");
        $this->db->from("FM_district_lookup");
        $this->db->where("id", $id);
        $this->db->where("status !=", "D");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $update_data = array("status" => "D");
            $this->db->where("id", $id);
            $this->db->update("FM_district_lookup", $update_data);

            $response = array("status" => "Y", "message" => "District successfully deleted.");

        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid district ID or district already deleted.");
        }
        return $response;
    }
}
