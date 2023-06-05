<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Banner_model extends CI_Model
{
    // Get banner list
    function banner_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_banner");
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
            // no status check 
        }

        $this->db->where("is_deleted", "N");
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array("id" => $row->id,"title" => $row->title, "description" => $row->description, "image" => $row->image, "link" => $row->link, "status" => $row->status, "created_date" => $row->created_date);
            }
        }
        return $list;
    }

    // Add banner data
    function add_banner($data)
    {
        $title = $data['title'];
        $description = $data['description'];
        $link = $data['link'];
        $status = $data['status'];
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array("title" =>  $title, "description" => $description, "link" => $link, "redirect_to" => $redirect_to, "status" => $status, "created_date" => $created_date);

        if (!empty($data["redirect_to"]))
        {
            $insert_data["redirect_to"] = $data["redirect_to"];
        }

        $this->db->insert("FM_banner", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New banner created", "id" => $id);

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
        $this->db->update("FM_banner", $update_data);
        return true;
        
    }

    // Update banner data
    function update_banner($data)
    {
        $id = $data['banner_id'];
        $title = $data['title'];
        $description = $data['description'];
        $link = $data['link'];
        $redirect_to = $data["redirect_to"];
        $status = $data['status'];
        $modified_date = date("Y-m-d H:i:s");

        // before update banner check banner ID
        $this->db->select("id");
        $this->db->from("FM_banner");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Maybe banner already deleted.");
        }
        else
        {
            $update_data = array("title" =>  $title, "description" => $description, "link" => $link, "redirect_to" => $redirect_to, "status" => $status, "modified_date" => $modified_date);
            $this->db->where("id", $id);
            $this->db->update("FM_banner", $update_data);
            $response = array("status" => "Y", "message" => "Banner Details updated.");

        }
        return $response;
    }

    // Get single banner details
    function single_banner_details($id)
    {
        $this->db->select("*");
        $this->db->from("FM_banner");
        $this->db->where("is_deleted", "N");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $details = array("id" => $row->id, "title" => $row->title, "description" => $row->description, "image" => $row->image, "link" => $row->link, "status" => $row->status, "created_date" => $row->created_date);

            if (!empty($row->redirect_to))
            {
                $details["redirect_to"] = $row->redirect_to;
            }

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found. Maybe banner is already deleted.");
        }
        return $response;

    }

    // Banner Delete
    function delete_banner_by_id($id)
    {
        $this->db->select("id");
        $this->db->from("FM_banner");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $update_data = array("is_deleted" => "Y", "modified_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_banner", $update_data); 
                      
            $response = array("status" => "Y", "message" => "Banner successfully deleted.");

        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid banner ID or banner already deleted.");
        }
        return $response;
    }

    // Get app redirections list
    function get_list_of_app_redirections()
    {
        return $this->db->get_where("FM_app_redirections", ["status" => "A"])->result();
    }
}

?>
