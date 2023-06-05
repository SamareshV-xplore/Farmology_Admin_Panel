<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
        $this->load->helper('date');
    }
    //Get banner list
    function orders_list($filter_data, $start_date, $end_date)
    {
        //echo date('Y-m-d H:i:s');exit;
        $list = array();

        $this->db->select("
        FM_order.id, FM_order.order_no, FM_order.customer_id, FM_order.address_id, FM_order.total_price,FM_order.discount,
        FM_order.promo_code_id,FM_order.order_total,FM_order.created_date,FM_order.updated_date,FM_order.status,
        FM_order.payment_method,FM_order.transaction_id,FM_order.delivery_charge,FM_order.delivery_date,FM_order.delivery_time_slot,
        FM_customer.first_name,FM_customer.last_name,FM_customer.phone,
        FM_customer_address.address_1,
        FM_delivery_time_slot.start_time as delivery_start_time,FM_delivery_time_slot.end_time as delivery_end_time
        ");
        $this->db->from("FM_order");
        switch ($filter_data['status']) {
            case 'P':
                $this->db->where("FM_order.status", "P");
                break;
            case 'S':
                $this->db->where("FM_order.status", "S");
                break;
            case 'D':
                $this->db->where("FM_order.status", "D");
                break;
            case 'ONP':
                $this->db->where("FM_order.status", "ONP");
                break;
            case 'C':
                $this->db->where("FM_order.status", "C");
                break;
            default:
            $this->db->where("FM_order.status !=", "ONP");

        }
        
        if(!empty($start_date) && !empty($end_date)){
            $this->db->where('FM_order.created_date BETWEEN "'. date('Y-m-d H:i:s', strtotime($start_date.' 00:00:01')). '" and "'. date('Y-m-d H:i:s', strtotime($end_date.' 23:59:59')).'"');
        }elseif (!empty($start_date)){
            $this->db->where('FM_order.created_date BETWEEN "'. date('Y-m-d H:i:s', strtotime($start_date.' 00:00:01')). '" and "'. date('Y-m-d H:i:s', strtotime(date("Y/m/d H:i:s"))).'"');
        }elseif (!empty($end_date)){
            $this->db->where('FM_order.created_date BETWEEN "'. date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:01')). '" and "'. date('Y-m-d H:i:s', strtotime($end_date.' 23:59:59')).'"');
        }
        $this->db->join('FM_customer', 'FM_order.customer_id = FM_customer.id');
        $this->db->join('FM_delivery_time_slot', 'FM_order.delivery_time_slot = FM_delivery_time_slot.id');
        $this->db->join('FM_customer_address', 'FM_customer_address.customer_id = FM_customer.id');
        $this->db->join('FM_city_lookup', 'FM_customer_address.city_id = FM_city_lookup.id');
        $this->db->order_by("FM_order.id", "desc");
        $this->db->limit(2000);

        $query = $this->db->get();
        /*print_r($this->db->last_query());
        exit;*/

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {/*
                echo '<pre>';
                print_r($row);
                exit;*/
                $this->db->select("
                FM_order_details.order_id,FM_order_details.product_id,FM_order_details.variation_id,FM_order_details.quantity,FM_order_details.unit_price,FM_order_details.total_price,
                FM_product.id,FM_product.title,
                FM_product_variation.title as variation_title,FM_product_variation.price as product_price
                ");
                $this->db->from("FM_order_details");
                $this->db->where('order_id', $row->id);
                $this->db->join('FM_product', 'FM_order_details.product_id = FM_product.id');
                $this->db->join('FM_product_variation', 'FM_order_details.variation_id = FM_product_variation.id');
                $query1 = $this->db->get()->result_array();
                $list[] = array(
                    "id" => $row->id,
                    "order_no" => $row->order_no,
                    "total_price" => $row->total_price,
                    "discount" => $row->discount,
                    "payment_method" => $row->payment_method,
                    "transaction_id" => $row->transaction_id,
                    "order_total" => $row->order_total,
                    "created_date" => $row->created_date,
                    "updated_date" => $row->updated_date,
                    "status" => $row->status,
                    "customer_name" => $row->first_name.' '.$row->last_name,
                    "phone" => $row->phone,
                    "address" => $row->address_1,
                    "delivery_charge" => $row->delivery_charge,
                    "delivery_date" => $row->delivery_date,
                    "delivery_start_time" => $row->delivery_start_time,
                    "delivery_end_time" => $row->delivery_end_time,
                    "products" => $query1,
                );
            }
        }
        
        return $list;
    }

    function order_by_id($id=0){
        $details = array();
        $this->db->select("
        FM_order.id, FM_order.order_no, FM_order.customer_id, FM_order.address_id, FM_order.total_price,FM_order.discount,
        FM_order.promo_code_id,FM_order.order_total,FM_order.created_date,FM_order.updated_date,FM_order.status,
        FM_order.payment_method,FM_order.transaction_id,FM_order.delivery_charge,FM_order.delivery_date,FM_order.delivery_time_slot,
        FM_customer.first_name,FM_customer.last_name,FM_customer.email,FM_customer.phone,
        FM_customer_address.address_1,FM_customer_address.address_2,FM_customer_address.landmark,FM_customer_address.state_id,FM_customer_address.city_id,zip_code,
        FM_city_lookup.name as city_name,FM_state_lookup.state as state_name
        ");
        $this->db->from("FM_order");
        $this->db->where("FM_order.id", $id);
        $this->db->join('FM_customer', 'FM_order.customer_id = FM_customer.id');
        $this->db->join('FM_customer_address', 'FM_customer_address.customer_id = FM_customer.id');
        $this->db->join('FM_city_lookup', 'FM_customer_address.city_id = FM_city_lookup.id');
        $this->db->join('FM_state_lookup', 'FM_customer_address.state_id = FM_state_lookup.id');
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            /*foreach($query->result() as $row)
            {*/
                $row = $query->row();
                $this->db->select("
                FM_order_details.order_id,FM_order_details.product_id,FM_order_details.variation_id,FM_order_details.quantity,FM_order_details.unit_price,FM_order_details.total_price,
                FM_product.id as product_id,FM_product.title,FM_product.SKU as sku,
                FM_product_image.image as product_image,
                FM_product_variation.title as variation_title,FM_product_variation.price as product_price
                ");
                $this->db->from("FM_order_details");
                $this->db->where('order_id', $row->id);
                $this->db->join('FM_product', 'FM_order_details.product_id = FM_product.id');
                $this->db->join('FM_product_image', 'FM_product_image.product_id = FM_product.id');
                $this->db->join('FM_product_variation', 'FM_order_details.variation_id = FM_product_variation.id');
                $query1 = $this->db->get()->result_array();





                $details = array(
                    "id" => $row->id,
                    "order_no" => $row->order_no,
                    "total_price" => $row->total_price,
                    "discount" => $row->discount,
                    "order_total" => $row->order_total,
                    "created_date" => $row->created_date,
                    "updated_date" => $row->updated_date,
                    "status" => $row->status,
                    "phone" => $row->phone,
                    "customer_name" => $row->first_name.' '.$row->last_name,
                    "email" => $row->email,
                    "address1" => $row->address_1,
                    "address2" => $row->address_2,
                    "landmark" => $row->landmark,
                    "zip_code" => $row->zip_code,
                    "city_name" => $row->city_name,
                    "delivery_charge" => $row->delivery_charge,
                    "delivery_date" => $row->delivery_date,
                    "delivery_time_slot" => $row->delivery_time_slot,
                    "state_name" => $row->state_name,
                    "payment_method" => $row->payment_method,
                    "transaction_id" => $row->transaction_id,
                    "products" => $query1,
                );
            //}
            $response = array(
                "status" => "Y",
                "message" => "Details found",
                "details" => $details,
                "order_no" => $row->order_no
            );
        }
        else{
            $response = array("status" => "N", "message" => "No details found. Something went wrong.");
        }
        return $response;
    }

    function order_by_order_number($order_number){
        $details = array();
        $this->db->select("
        FM_order.id, FM_order.order_no, FM_order.customer_id, FM_order.address_id, FM_order.total_price,FM_order.discount,
        FM_order.promo_code_id,FM_order.order_total,FM_order.created_date,FM_order.updated_date,FM_order.status,
        FM_order.payment_method,FM_order.transaction_id,FM_order.delivery_charge,FM_order.delivery_date,FM_order.delivery_time_slot,
        FM_customer.first_name,FM_customer.last_name,FM_customer.email,FM_customer.phone,
        FM_customer_address.address_1,FM_customer_address.address_2,FM_customer_address.landmark,FM_customer_address.state_id,FM_customer_address.city_id,zip_code,
        FM_city_lookup.name as city_name,FM_state_lookup.state as state_name
        ");
        $this->db->from("FM_order");
        $this->db->where("FM_order.order_no", $order_number);
        $this->db->join('FM_customer', 'FM_order.customer_id = FM_customer.id');
        $this->db->join('FM_customer_address', 'FM_customer_address.customer_id = FM_customer.id');
        $this->db->join('FM_city_lookup', 'FM_customer_address.city_id = FM_city_lookup.id');
        $this->db->join('FM_state_lookup', 'FM_customer_address.state_id = FM_state_lookup.id');
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            /*foreach($query->result() as $row)
            {*/
            $row = $query->row();
            $this->db->select("
                FM_order_details.order_id,FM_order_details.product_id,FM_order_details.variation_id,FM_order_details.quantity,FM_order_details.unit_price,FM_order_details.total_price,
                FM_product.id as product_id,FM_product.title,FM_product.SKU as sku,
                FM_product_image.image as product_image,
                FM_product_variation.title as variation_title,FM_product_variation.price as product_price
                ");
            $this->db->from("FM_order_details");
            $this->db->where('order_id', $row->id);
            $this->db->join('FM_product', 'FM_order_details.product_id = FM_product.id');
            $this->db->join('FM_product_image', 'FM_product_image.product_id = FM_product.id');
            $this->db->join('FM_product_variation', 'FM_order_details.variation_id = FM_product_variation.id');
            $query1 = $this->db->get()->result_array();

            $details = array(
                "id" => $row->id,
                "order_no" => $row->order_no,
                "total_price" => $row->total_price,
                "discount" => $row->discount,
                "order_total" => $row->order_total,
                "created_date" => $row->created_date,
                "updated_date" => $row->updated_date,
                "status" => $row->status,
                "phone" => $row->phone,
                "customer_name" => $row->first_name.' '.$row->last_name,
                "email" => $row->email,
                "address1" => $row->address_1,
                "address2" => $row->address_2,
                "landmark" => $row->landmark,
                "zip_code" => $row->zip_code,
                "city_name" => $row->city_name,
                "delivery_charge" => $row->delivery_charge,
                "delivery_date" => $row->delivery_date,
                "delivery_time_slot" => $row->delivery_time_slot,
                "state_name" => $row->state_name,
                "payment_method" => $row->payment_method,
                "transaction_id" => $row->transaction_id,
                "products" => $query1,
            );
            //}
            $response = array(
                "status" => "Y",
                "message" => "Details found",
                "details" => $details,
                "order_no" => $row->order_no
            );
        }
        else{
            $response = array("status" => "N", "message" => "No details found. Something went wrong.");
        }
        return $response;
    }

    /**
     * Update order details
     * @param $data
     * @return array
     */
    function update_order($data)
    {
        $id = $data['order_id'];
        $status = $data['status'];
        $updated_date = date("Y-m-d H:i:s");

        // before update banner check banner ID
        $this->db->select("id");
        $this->db->from("FM_order");
        $this->db->where("id", $id);
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Something went wrong. Please try again.");
        }
        else
        {
            $update_data = array("status" => $status, "updated_date" => $updated_date);
            $this->db->where("id", $id);
            $this->db->update("FM_order", $update_data);
            $response = array("status" => "Y", "message" => "Order status updated.");

        }
        return $response;
    }

    function check_invoice(){
        $this->db->select_max('id');
        $result= $this->db->get('FM_invoice')->row_array();
        return $result['id'];
    }

    function find_all_orders($form_data){
        if(!empty($form_data['start_date']) && !empty($form_data['end_date'])){
            $details = array();
            $this->db->select("
        FM_order.id, FM_order.order_no, FM_order.customer_id, FM_order.address_id, FM_order.total_price,FM_order.discount,
        FM_order.promo_code_id,FM_order.order_total,FM_order.created_date,FM_order.updated_date,FM_order.status,
        FM_order.payment_method,FM_order.transaction_id,FM_order.delivery_charge,FM_order.delivery_date,FM_order.delivery_time_slot,
        FM_customer.first_name,FM_customer.last_name,FM_customer.email,FM_customer.phone,
        FM_customer_address.address_1,FM_customer_address.address_2,FM_customer_address.landmark,FM_customer_address.state_id,FM_customer_address.city_id,zip_code,
        FM_city_lookup.name as city_name,FM_state_lookup.state as state_name
        ");
            $this->db->from("FM_order");
            $this->db->where('FM_order.created_date BETWEEN "'. date('Y-m-d H:i:s', strtotime($form_data['start_date'].' 00:00:01')). '" and "'. date('Y-m-d H:i:s', strtotime($form_data['end_date'].' 23:59:59')).'"');
            $this->db->join('FM_customer', 'FM_order.customer_id = FM_customer.id');
            $this->db->join('FM_customer_address', 'FM_customer_address.customer_id = FM_customer.id');
            $this->db->join('FM_city_lookup', 'FM_customer_address.city_id = FM_city_lookup.id');
            $this->db->join('FM_state_lookup', 'FM_customer_address.state_id = FM_state_lookup.id');
            $query = $this->db->get();

            if($query->num_rows() > 0)
            {
                foreach($query->result() as $row)
                {
                    $this->db->select("
                FM_order_details.order_id,FM_order_details.product_id,FM_order_details.variation_id,FM_order_details.unit_price,
                FM_product.id,FM_product.title,FM_product.SKU as sku,
                FM_product_image.image as product_image,
                FM_product_variation.title as quantity,FM_product_variation.price as product_price,
                FM_product_variation.discount as product_discount
                ");
                    $this->db->from("FM_order_details");
                    $this->db->join('FM_order', 'FM_order_details.order_id = '.$row->id);
                    $this->db->join('FM_product', 'FM_order_details.product_id = FM_product.id');
                    $this->db->join('FM_product_image', 'FM_product_image.product_id = FM_product.id');
                    $this->db->join('FM_product_variation', 'FM_order_details.variation_id = FM_product_variation.id');
                    $query1 = $this->db->get()->result_array();
                    $details[] = array(
                        "id" => $row->id,
                        "order_no" => $row->order_no,
                        "total_price" => $row->total_price,
                        "discount" => $row->discount,
                        "order_total" => $row->order_total,
                        "created_date" => $row->created_date,
                        "updated_date" => $row->updated_date,
                        "status" => $row->status,
                        "phone" => $row->phone,
                        "customer_name" => $row->first_name.' '.$row->last_name,
                        "email" => $row->email,
                        "address1" => $row->address_1,
                        "address2" => $row->address_2,
                        "landmark" => $row->landmark,
                        "zip_code" => $row->zip_code,
                        "city_name" => $row->city_name,
                        "delivery_charge" => $row->delivery_charge,
                        "delivery_date" => $row->delivery_date,
                        "delivery_time_slot" => $row->delivery_time_slot,
                        "state_name" => $row->state_name,
                        "payment_method" => $row->payment_method,
                        "transaction_id" => $row->transaction_id,
                        "products" => $query1,
                    );
                }
                $response = array(
                    "status" => "Y",
                    "message" => "Details found",
                    "total_order_count" => $query->num_rows(),
                    "details" => $details
                );
            }
            else{
                $response = array("status" => "N", "message" => "No details found. Something went wrong.");
            }
        }else{
            $response = array("status" => "N", "message" => "No details found. Something went wrong.");
        }

        return $response;
    }

    function get_user_details($order_id){
        $this->db->select("customer_id");
        $this->db->from("FM_order");
        $this->db->where("id", $order_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();

            $this->db->select("*");
            $this->db->from("FM_customer");
            $this->db->where("id", $row->customer_id);
            $query1 = $this->db->get();
            if($query1->num_rows() > 0){

                $row1 = $query1->row();
                $details = array(
                    'id' => $row1->id,
                    "first_name" =>  $row1->first_name,
                    "email" => $row1->email
                );
                $response = array("status" => "Y", "message" => "Details found", "details" => $details);
                return $response;
            }

            $response = array("status" => "N", "message" => "No details found.");

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found.");
        }
        return $response;
    }

    function get_order_no_by_order_id($id = 0)
    {
        $order_no = "";
        $this->db->select("order_no");
        $this->db->from("FM_order");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $order_no = $row->order_no;

        }
        return $order_no;
    }

    function update_order_status($order_no = "", $status)
    {

        if($status == "ONP" || $status == "P" || $status == "C" || $status == "D" || $status == "S")
        {
            $update_data = array("status" => $status, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("order_no", $order_no);
            $this->db->update("FM_order", $update_data);
        }

        return "success";
        
    }
}
