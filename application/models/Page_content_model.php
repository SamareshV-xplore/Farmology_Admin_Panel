<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Page_content_model extends CI_Model
{

    function get_page_list()
    {
        $list = array();
        $this->db->select("*");
        $this->db->from("FM_page");
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $list[] = array("id" => $rows->id, "title" => $rows->page_title);
            }
        }

        return $list;
    }

    function page_details_by_id($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_page");
        $this->db->where("id", $id);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $details = array("id" => $rows->id, "title" => $rows->page_title);
            }
        }

        return $details;
    }

    function get_single_page_content_details($id = 0)
    {
        $details = array();

        $this->db->select("*");
        $this->db->from("FM_page_content");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $page_details = $this->page_details_by_id($row->page_id);
            $details = array("id" => $row->id, "page_details" => $page_details, "title" => $row->title, "image" => $row->image, "page_content" => $row->page_content, "updated_date" => $row->updated_date);
        }

        return $details;
    }

    function get_page_content_list($filter_data = array())
    {
        $list = array();

        $page_id = 0;

        if(count($filter_data) > 0)
        {
            if($filter_data['page'] > 0)
            {
                $page_id = $filter_data['page'];
            }
        }

        $this->db->select("*");
        $this->db->from("FM_page_content");
        if($page_id > 0)
        {
            $this->db->where("page_id", $page_id);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $page_details = $this->page_details_by_id($row->page_id);
                $list[] = array("id" => $row->id, "page_details" => $page_details, "title" => $row->title, "image" => $row->image, "page_content" => $row->page_content, "updated_date" => $row->updated_date);
            }
        }

        return $list;
    }

    function update_content($data)
    {
        $id = $data['id'];
        $title = $data['title'];
        $description = $data['description'];

        $this->db->select('id');
        $this->db->from('FM_page_content');
        $this->db->where('id', $id);
        $check_query = $this->db->get();

        if($check_query->num_rows() > 0)
        {
            $update_data = array("title" => $title, "page_content" => $description, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_page_content", $update_data);

            $response_data = array("status" => "Y", "message" => "Page content successfully updated.");
        }
        else
        {
            $response_data = array("status" => "N", "message" => "Invalid try. This page content does not exist.");
        }       

        return $response_data;
    }

    function update_image($id = 0, $image_path)
    {
        $update_data = array("image" => $image_path);
        $this->db->where("id", $id);
        $this->db->update("FM_page_content", $update_data);

        return true;
    }
    
}

?>