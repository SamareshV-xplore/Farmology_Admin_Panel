<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_code_model extends CI_Model {
    //Get banner list
    function promo_code_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_promo_code");
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
                $list[] = array(
                    "id" => $row->id,
                    "promo_code" => $row->promo_code,
                    "title" => $row->title,
                    "discount_type" => $row->discount_type,
                    "status" => $row->status,
                    "created_date" => $row->created_date
                );
            }
        }
        return $list;
    }

    //Add promo code data
    function add_promo_code($data)
    {/*
        echo '<pre>';
        print_r($data);exit;*/
        if($data['discount_type'] == "P")
        {
            $max_limit = $data['max_limit'];
        }
        else
        {
            $max_limit = "0.00";
        }
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array(
            "promo_code" =>  $data['promo_code'],
            "title" =>  $data['title'],
            "max_limit" => $max_limit,
            "description" =>  $data['description'],
            "eligible_order_price" =>  $data['eligible_order_price'],
            "start_date" =>  $data['start_date'],
            "end_date" =>  $data['end_date'],
            "discount_limit" =>  $data['discount_limit'],
            "discount_type" =>  $data['discount_type'],
            "user_id" =>  $data['user_id'],
            "user_specific" => $data['user_specific'],
            "status" =>  $data['status'],
            "usage_count" =>  $data['usage_count'],
            "created_date" => $created_date
        );
        if($data['start_date'] == '0000-00-00' || empty($data['start_date'])){
            $insert_data['start_date'] = null;
        }
        if($data['end_date'] == '0000-00-00' || empty($data['end_date'])){
            $insert_data['end_date'] = null;
        }
        if($data['user_specific'] == 'N'){
            $insert_data['user_id'] = null;
        }
        $this->db->insert("FM_promo_code", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New promo code created", "id" => $id);

        return $response;

    }

    // Get single promo code details
    function single_promo_details($id)
    {
        $this->db->select("*");
        $this->db->from("FM_promo_code");
        $this->db->where("status !=", "D");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();

            $details = array(
                "id" => $row->id,
                "promo_code" => $row->promo_code,
                "title" => $row->title,
                "description" => $row->description,
                "eligible_order_price" => $row->eligible_order_price,
                "start_date" => ($row->start_date == '0000-00-00')? '' : $row->start_date,
                "end_date" => ($row->end_date == '0000-00-00')? '' : $row->end_date,
                "discount_limit" => $row->discount_limit,
                "discount_type" => $row->discount_type,
                "max_limit" => $row->max_limit,
                "status" => $row->status,
                "user_id" =>  $row->user_id,
                "user_specific" =>  $row->user_specific,
                "mobile_number" =>  null,
                "usage_count" =>  $row->usage_count,
                "created_date" => $row->created_date
            );
            if($row->user_specific == 'Y' && !empty($row->user_id)){
                $this->db->select("phone");
                $this->db->from("FM_customer");
                $this->db->where("id",  $row->user_id);
                $query1 = $this->db->get();
                /*$result = $query1->result();*/
                //print_r($query1);exit;

                if($query1->num_rows() > 0){
                    $row1 = $query1->row();
                    $details['mobile_number'] = $row1->phone;
                }
            }

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);
        }
        else
        {
            $response = array("status" => "N", "message" => "No details found. Maybe coupon is already deleted.");
        }
        return $response;

    }

    /**
     * @param $data
     * @return array
     * update promo code details
     */
    function update_promo_code($data)
    {/*
        echo '<pre>';
        print_r($data);exit;*/
        $id = $data['promo_code_id'];
        // before update banner check promo code ID
        $this->db->select("id");
        $this->db->from("FM_promo_code");
        $this->db->where("id", $id);
        $this->db->where("status !=", "D");
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Maybe promo code already deleted.");
        }
        else
        {
            if($data['discount_type'] == "P")
            {
                $max_limit = $data['max_limit'];
            }
            else
            {
                $max_limit = "0.00";
            }
            $update_data = array(
                "promo_code" =>  $data['promo_code'],
                "title" =>  $data['title'],
                "description" =>  $data['description'],
                "eligible_order_price" =>  $data['eligible_order_price'],
                "start_date" =>  $data['start_date'],
                "end_date" =>  $data['end_date'],
                "discount_limit" =>  $data['discount_limit'],
                "discount_type" =>  $data['discount_type'],
                "status" =>  $data['status'],
                "user_id" =>  $data['user_id'],
                "user_specific" =>  $data['user_specific'],
                "max_limit" => $max_limit,
                "usage_count" =>  $data['usage_count'],
                "updated_date" => date("Y-m-d H:i:s")
            );
            if($data['start_date'] == '0000-00-00' || empty($data['start_date'])){
                $update_data['start_date'] = null;
            }
            if($data['end_date'] == '0000-00-00' || empty($data['end_date'])){
                $update_data['end_date'] = null;
            }
            if($data['user_specific'] == 'N'){
                $update_data['user_id'] = null;
            }
            $this->db->where("id", $id);
            $this->db->update("FM_promo_code", $update_data);
            $response = array("status" => "Y", "message" => "Promo Code Details updated.");

        }
        return $response;
    }

    // Promo code Delete
    function delete_promo_by_id($id)
    {
        $response = array("status" => "N", "message" => "This promo code already deleted or not found.");

        $this->db->where("id", $id);
        $this->db->delete("FM_promo_code");
        if($this->db->affected_rows() > 0)
        {
            $response =  array("status" => "Y", "message" => "Promo code successfully deleted.");
        }

        return $response;

    }

    function check_promo_code($promo_code){
        $this->db->select("*");
        $this->db->from("FM_promo_code");
        $this->db->where('status !=', 'D');
        $this->db->where('promo_code like binary', $promo_code);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $response = array("status" => "Y", "message" => "A promo code is already exists with this name.");
        }
        else
        {
            $response = array("status" => "N", "message" => "This name can be used to create a new one.");
        }
        return $response;
    }

    function get_promo_code_details_by_id($id = 0)
    {
        $promo_details = array();

        $this->db->select("*");
        $this->db->from("FM_promo_code");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $promo_details = $query->row_array();
        }

        return $promo_details;

    }
}
