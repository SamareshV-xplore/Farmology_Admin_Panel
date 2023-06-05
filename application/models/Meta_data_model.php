<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meta_data_model extends CI_Model {
    //Get banner list
    function meta_data_list()
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_page_meta_data");
        /*if($filter_data['status'] == 'Y')
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
        }*/

        $this->db->where("is_deleted", "N");
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "page_name" => $row->page_name,
                    "meta_keyword" => $row->meta_keyword,
                    "updated_date" => $row->updated_date,
                );
            }
        }
        return $list;
    }
    //Add banner data
    function add_meta_data($data)
    {
        $page_name = $data['page_name'];
        $meta_title = $data['meta_title'];
        $meta_description = $data['meta_description'];
        $meta_keyword = $data['meta_keyword'];
        $updated_date = date("Y-m-d H:i:s");
        $insert_data = array(
            "page_name" =>  $page_name,
            "meta_title" => $meta_title,
            "meta_description" => $meta_description,
            "meta_keyword" => $meta_keyword,
            "is_deleted" => 'N',
            "updated_date" => $updated_date
        );
        $this->db->insert("FM_page_meta_data", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New Meta data created", "id" => $id);

        return $response;

    }

    function update_meta_data($data)
    {
        $id = $data['meta_data_id'];
        $page_name = $data['page_name'];
        $meta_title = $data['meta_title'];
        $meta_description = $data['meta_description'];
        $meta_keyword = $data['meta_keyword'];
        $updated_date = date("Y-m-d H:i:s");

        // before update banner check banner ID
        $this->db->select("id");
        $this->db->from("FM_page_meta_data");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Maybe meta data already deleted.");
        }
        else
        {
            $update_data = array(
                "page_name" =>  $page_name,
                "meta_title" => $meta_title,
                "meta_description" => $meta_description,
                "meta_keyword" => $meta_keyword,
                "updated_date" => $updated_date
            );
            $this->db->where("id", $id);
            $this->db->update("FM_page_meta_data", $update_data);
            $response = array("status" => "Y", "message" => "Meta data Details updated.");

        }
        return $response;
    }
    // Get single banner details
    function single_meta_data_details($id)
    {
        $this->db->select("*");
        $this->db->from("FM_page_meta_data");
        $this->db->where("is_deleted", "N");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $details = array(
                'id' => $row->id,
                "page_name" =>  $row->page_name,
                "meta_title" => $row->meta_title,
                "meta_description" => $row->meta_description,
                "meta_keyword" => $row->meta_keyword,
            );

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found. Maybe meta data is already deleted.");
        }
        return $response;

    }
    // Banner Delete
    function delete_meta_data_by_id($id)
    {
        $this->db->select("id");
        $this->db->from("FM_page_meta_data");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $update_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_page_meta_data", $update_data);

            $response = array("status" => "Y", "message" => "Meta data successfully deleted.");

        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid meta data ID or meta data already deleted.");
        }
        return $response;
    }

    function update_meta_data_for_product($data = array())
    {
        if(count($data) > 0)
        {
            $product_id = $data['product_id'];
            $meta_title = $data['meta_title'];
            $meta_description = $data['meta_description'];
            $meta_keyword = $data['meta_keyword'];

            // check status
            $this->db->select("id");
            $this->db->from("FM_product_meta_data");
            $this->db->where("product_id", $product_id);
            $this->db->where("is_deleted", "N");
            $check_query = $this->db->get();
            if($check_query->num_rows() > 0)
            {
                $row = $check_query->row();
                // update data
                $update_data = array("meta_title" => $meta_title, "meta_description" => $meta_description, "meta_keyword" => $meta_keyword, "updated_date" => date("Y-m-d H:i:s"));
                $this->db->where("id", $row->id);
                $this->db->update("FM_product_meta_data", $update_data);
                $response = array("status" => "Y", "message" => "Data update.");

            }
            else
            {
                $insert_data = array("meta_title" => $meta_title, "meta_description" => $meta_description, "meta_keyword" => $meta_keyword, "updated_date" => date("Y-m-d H:i:s"), "product_id" => $product_id, "is_deleted" => "N");
                $this->db->insert("FM_product_meta_data", $insert_data);
                $response = array("status" => "Y", "message" => "Data created.");

            }
        }
        else
        {
            $response = array("status" => "N", "message" => "No data found.");
        }
        return $response;
    }

    function get_product_meta_data_by_id($product_id = 0)
    {
        $details = array("meta_title" => "", "meta_description" => "", "meta_keyword" => "");
        $this->db->select("*");
        $this->db->from("FM_product_meta_data");
        $this->db->where("product_id", $product_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $details = array("meta_title" => $row->meta_title, "meta_description" => $row->meta_description, "meta_keyword" => $row->meta_keyword);

        }
        return $details;
    }
}
