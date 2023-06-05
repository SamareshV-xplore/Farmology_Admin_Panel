<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Crop_model extends CI_Model
{
    function crop_list($filter_data){
        $list = array();
        
        if(isset($filter_data['status']))
        {
            $filter_status =  $filter_data['status'];
        }
        else
        {
            $filter_status =  "all";
        }

        $this->db->select("*");
        $this->db->from("FM_crop");
        $this->db->where("status !=", 'D');
        if($filter_status != 'all'){
            $this->db->where("status", $filter_status);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $list[] = array("id" => $rows->id, "title" => $rows->title, "image" => FRONT_URL.$rows->image, "status" => $rows->status, "created_date" => $rows->created_date, "updated_date" => $rows->updated_date);
            }
        }
        return $list;

    }

    function add_crop($data)
    {
        $response = array("status" => "N", "message" => "Something was wrong");
        $title = $data['title'];
        $image = $data['image'];
        $status = $data['status'];

        
            $insert_data = array("title" => $title, "image" => $image, "status" => $status, "created_date" => date("Y-m-d H:i:s"));
            $this->db->insert("FM_crop", $insert_data);
            

            $response = array("status" => "Y", "message" => "New crop successfully created.");


        

        return $response;

    }

    function update_crop($data)
    {
        $response = array("status" => "N", "message" => "Something was wrong");
        $id = $data['id'];
        $title = $data['title'];
        $status = $data['status'];

        
            $update_data = array("title" => $title, "status" => $status, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_crop", $update_data);

            

            $response = array("status" => "Y", "message" => "Crop successfully updated.");


        

        return $response;

    }

    function delete_crop_by_id($id = 0)
    {
        $this->db->select("id");
        $this->db->from("FM_crop");
        $this->db->where("id", $id);
        $this->db->where("status !=", "D");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $main_up_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_crop", $main_up_data);


            $response = array("status" => "N", "message" => "Crop successfully deleted.");


        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid Try. Crop already deleted or not found.");
        }
    }

    function get_crop_details_id($id = 0)
    {
        $crop_row = array();
        $this->db->select("*");
        $this->db->from("FM_crop");
        $this->db->where("id", $id);
        $this->db->where("status !=", 'D');       
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
                $rows = $query->row();
                $crop_row = array(
                    "id" => $rows->id,
                    "title" => $rows->title,
                    "image" => FRONT_URL.$rows->image,
                    "status" => $rows->status,
                    "created_date" => $rows->created_date,
                    "updated_date" => $rows->updated_date
                );
            
        }

        return $crop_row;

    }

    function update_crop_image($data = array())
    {
        if(count($data) > 0)
        {
            $cate_id = $data['id'];
            $image = $data['image'];

            $update_data = array("image" => $image);
            $this->db->where("id", $cate_id);
            $this->db->update("FM_crop", $update_data);
            return true;

        }
        else
        {
            return false;
        }
        
    }

    
    
}

?>
