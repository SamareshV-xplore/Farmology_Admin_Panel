<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Product_comment_model extends CI_Model
{
    function get_comment_list($filter)
    {
        $list = array();
        $this->db->select("*");
        $this->db->from("FM_rating_review");
        if($filter['status'] == 'P' || $filter['status'] == 'A' || $filter['status'] == 'R')
        {
            $this->db->where("status", $filter['status']);
        }
        //$this->db->where("review_text !=", NULL);
        $this->db->order_by("id", "DESC");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $product_id = $row->product_id;
                $customer_id = $row->customer_id;

                $product_name = $this->product_model->get_product_name_by_id($product_id);
                $product_image     = $this->product_model->get_product_image_by_product_id($product_id);
                $product_details = array("id" => $product_id, "name" => $product_name, "image" => $product_image);
                $customer_details = $this->user_model->user_details_by_id($customer_id);

                $list[] = array("id" => $row->id, "product_details" => $product_details, "customer_details" => $customer_details, "rating" => $row->rating, "review_text" => $row->review_text, "status" => $row->status, "created_date" => $row->created_date, "updated_date" => $row->updated_date);

            }
        }

        return $list;
    }

    function update_review_status($data)
    {
        $id = $data['id'];
        $status = $data['status'];

        $update_data = array("status" => $status, "updated_date" => date("Y-m-d H:i:s"));
        $this->db->where("id", $id);
        $this->db->update("FM_rating_review", $update_data);
        return true;
    }
}