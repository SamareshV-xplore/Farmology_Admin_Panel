<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Video_model extends CI_Model
{

    function add_video($data)
    {
        $title = $data['title'];
		$yt_video_id = $data['yt_video_id'];
		$description = $data['description'];
        $status = $data['status'];
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array("title" =>  $title, "description" => $description, "yt_video_id" => $yt_video_id, "status" => $status, "created_date" => $created_date);
        $this->db->insert("FM_video", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New video successfully added.", "id" => $id);

        return $response;

    }

    function get_video_list($filter_data = array())
    {
        $list = array();

        if(count($filter_data) > 0)
        {
            $status = $filter_data["status"];
        }
        else
        {
            $status = "";
        }

        $this->db->select("*");
        $this->db->from("FM_video");
        if($status == 'N' || $status == 'Y')
        {
            $this->db->where("status", $status);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $list = $query->result_array();
        }

        return $list;
    }

    function get_single_video_details($id = 0)
    {
        $details = array();

        $this->db->select("*");
        $this->db->from("FM_video");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $details = $query->row_array();
        }
        return $details;
    } 


    function update_video_data($data)
    {
        $id = $data['id'];
		$title = $data['title'];
		$description = $data['description'];
        $yt_video_id = $data['yt_video_id'];
        $status = $data['status'];

        $update_data = array("title" => $title, "description" => $description, "yt_video_id" => $yt_video_id, "status" => $status, "updated_date" => date("Y-m-d H:i:s"));
        $this->db->where("id", $id);
        $this->db->update("FM_video", $update_data);
        return true;
    }

    function update_video_status($video_id, $status_value)
    {
        $update_data = array("home_page_show" => $status_value);
        $this->db->where("id", $video_id);
        $this->db->update("FM_video", $update_data);
        return true;
    }

    function video_delete($id = 0)
    {
        $response =  array("status" => "N", "message" => "Video already deleted or not exist.");

        $this->db->where("id", $id);
        $this->db->delete("FM_video");
        if($this->db->affected_rows() > 0)
        {
            $response =  array("status" => "Y", "message" => "Video successfully deleted.");
        }

        return $response;
	}
	
	// Update image data
    function update_image($id, $image)
    {
        
		$update_data = array("image" => $image, "updated_date" => date("Y-m-d H:i:s"));
        $this->db->where("id", $id);
        $this->db->update("FM_video", $update_data);
        return true;

    }
    
    
    
}

?>
