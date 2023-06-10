<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {

    function get_order_list($filter_data, $order_no = "")
    {

        /*"filter" => true, "search-type" => $search_type, "order-status" => $order_status, 'custom-date' => $custom_date*/

        $order = array();
        $today_date = date("Y-m-d");
        $till_date = date('Y-m-d', strtotime('-30 days'));

        $this->db->select("*");
        $this->db->from("FM_order");
        
        
        if($order_no != '')
        {
            $this->db->where("order_no", $order_no);
        }

        $filter_flag = "N";
        if($filter_data['filter'] == true)
        {
            // check_status 
            if($filter_data['filter'] == true)
            {
                $filter_flag = "Y";

                // check type
                if($filter_data['search-type'] == "manual-date")
                {
                    $date_range = $filter_data['custom-date'];
                    $exp_date_range = explode(' - ', $date_range);

                    $start_date = trim($exp_date_range[0]);
                    $exp_start_date = explode('/', $start_date);
                    $start_date_is = $exp_start_date[2]."-".$exp_start_date[0]."-".$exp_start_date[1];

                    $end_date = trim($exp_date_range[1]);
                    $exp_end_date = explode('/', $end_date);
                    $end_date_is = $exp_end_date[2]."-".$exp_end_date[0]."-".$exp_end_date[1];

                    if($start_date_is == $end_date_is)
                    {
                        $this->db->where("created_date LIKE ", "%".$start_date_is."%");
                    }
                    else
                    {
                        $this->db->where("created_date BETWEEN '".$start_date_is." 00:00:00' AND '".$end_date_is." 23:59:59'");
                    }

                    if($filter_data['order-status'] != 'all')
                    {
                        $this->db->where("status", $filter_data['order-status']);
                    }
                    else
                    {
                        //-------------
                        $this->db->where("status !=", "NOP");
                    }

                    
                }
                else if($filter_data['search-type'] == "today-delivery")
                {

                    $this->db->where("delivery_date LIKE ", "%".$today_date."%");
                    $this->db->where("status !=", "NOP");
                }
                else
                {
                    $this->db->where("status !=", "NOP");
                    $this->db->where("created_date BETWEEN '".$till_date." 00:00:00' AND '".$today_date." 23:59:59'");

                }

            }
            else
            {
                //-------------

                $this->db->where("status !=", "NOP");
                $this->db->where("created_date BETWEEN '".$till_date." 00:00:00' AND '".$today_date." 23:59:59'");
            }

        }
        else
        {
            //-------------
            
            $this->db->where("status !=", "NOP");
            $this->db->where("created_date BETWEEN '".$till_date." 00:00:00' AND '".$today_date." 23:59:59'");
        }


        $this->db->order_by("id", "DESC");
        $query = $this->db->get();

        /*echo $this->db->last_query();
        exit;*/

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $order_row)
            {
                $customer_id = $order_row->customer_id;
                $address_details = $this->user_model->get_address_details_by_id($order_row->address_id);

                if (!empty($order_row->delivery_driver_id))
                {
                    $delivery_driver_details = $this->get_delivery_driver_details_by_id($order_row->delivery_driver_id);
                }
                else
                {
                    $delivery_driver_details = new stdClass;
                }

                if (!empty($order_row->merchant_id))
                {
                    $merchant_center_details = $this->get_merchant_center_details_by_id($order_row->merchant_id);
                }
                else
                {
                    $merchant_center_details = new stdClass;
                }

                if($order_row->promo_code_id == NULL || $order_row->promo_code_id == 0)
                {
                    $promo_code_details = array();
                    
                }
                else
                {
                    $promo_code_details = $this->promo_code_model->get_promo_code_details_by_id($order_row->promo_code_id);
                   
                }
                $time_slot_details = $this->common_model->get_delivery_time_slot_detail_by_id($order_row->delivery_time_slot);
                // get order product details

                $product_details = $this->get_product_details_order_id($order_row->id);

                $customer_details = $this->user_model->user_details_by_id($customer_id);



                $order[] = array("id" => $order_row->id, "order_no" => $order_row->order_no, "customer_details" => $customer_details,  "address_details" => $address_details, "delivery_driver_details" => $delivery_driver_details, "merchant_center_details" => $merchant_center_details, "total_price" => $order_row->total_price, "delivery_charge" => $order_row->delivery_charge, "discount" => $order_row->discount, "order_total" => $order_row->order_total, "promo_code_details" => $promo_code_details, "payment_method" => $order_row->payment_method, "transaction_id" => $order_row->transaction_id, "delivery_date" => $order_row->delivery_date, "time_slot_details" => $time_slot_details, "notes" => $order_row->notes, "invoice" => FRONT_URL.$order_row->invoice, "product_details" => $product_details, "status" => $order_row->status, "created_date" => $order_row->created_date);
            }
        }

        return $order;

    }

    function order_details_by_no($order_no)
    {      

        $order = array();
       

        $this->db->select("*");
        $this->db->from("FM_order");       
        $this->db->where("order_no", $order_no);        
        $query = $this->db->get();


        if($query->num_rows() > 0)
        {
            foreach($query->result() as $order_row)
            {
                $customer_id = $order_row->customer_id;
                $address_details = $this->user_model->get_address_details_by_id($order_row->address_id);
                if($order_row->promo_code_id == NULL || $order_row->promo_code_id == 0)
                {
                    $promo_code_details = array();
                    
                }
                else
                {
                    $promo_code_details = $this->promo_code_model->get_promo_code_details_by_id($order_row->promo_code_id);
                   
                }
                $time_slot_details = $this->common_model->get_delivery_time_slot_detail_by_id($order_row->delivery_time_slot);
                // get order product details

                $product_details = $this->get_product_details_order_id($order_row->id);

                $customer_details = $this->user_model->user_details_by_id($customer_id);



                $order = array("id" => $order_row->id, "order_no" => $order_row->order_no, "customer_details" => $customer_details,  "address_details" => $address_details, "total_price" => $order_row->total_price, "delivery_charge" => $order_row->delivery_charge, "discount" => $order_row->discount, "order_total" => $order_row->order_total, "promo_code_details" => $promo_code_details, "payment_method" => $order_row->payment_method, "transaction_id" => $order_row->transaction_id, "delivery_date" => $order_row->delivery_date, "time_slot_details" => $time_slot_details, "notes" => $order_row->notes, "invoice" => $order_row->invoice, "product_details" => $product_details, "status" => $order_row->status, "created_date" => $order_row->created_date);
            }
        }

        return $order;

    }
 
    function get_product_details_order_id($order_id = 0)
    {
        $order_details = array();
        $this->db->select("*");
        $this->db->from("FM_order_details");
        $this->db->where("order_id", $order_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $product_row)
            {
                $variation_details = $this->product_model->get_veriation_full_details_by_id($product_row->variation_id);
                $order_details[] = array("variation_details" => $variation_details, "unit_price" => $product_row->unit_price, "quantity" => $product_row->quantity, "total_price" => $product_row->total_price);
            }
        }

        return $order_details;
    }

    function get_order_no_by_id($id = 0)
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

    function update_order_status($status, $order_no = "")
    {

        if($status == "NOP" || $status == "P" || $status == "C" || $status == "D" || $status == "S")
        {
            $update_data = array("status" => $status, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("order_no", $order_no);
            $this->db->update("FM_order", $update_data);
        }

        return "success";
        
    }

    function update_order_details($status, $payment_method, $order_no = "")
    {

        if($status == "NOP" || $status == "P" || $status == "C" || $status == "D" || $status == "S")
        {
            $update_data = array("status" => $status, "payment_method" => $payment_method, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("order_no", $order_no);
            $this->db->update("FM_order", $update_data);
        }

        return "success";
        
    }

    function get_delivery_drivers_list_by_order_no($order_no)
    {
        $order_address_sql = "SELECT CA.state_id, CA.district_id, CA.zip_code FROM FM_customer_address CA INNER JOIN FM_order O ON CA.id = O.address_id WHERE O.order_no = '$order_no'";
        $order_address_result = $this->db->query($order_address_sql)->row();

        if (!empty($order_address_result->zip_code))
        {
            $sql = "SELECT driver_id AS id, name, phone FROM FM_delivery_drivers WHERE status = 'A' AND available_pincodes LIKE '%$order_address_result->zip_code%'";
            $delivery_drivers_list = $this->db->query($sql)->result();
        }
        else
        {
            $delivery_drivers_list = [];
        }

        return $delivery_drivers_list;
    }

    function get_merchant_centers_list_by_order_no($order_no)
    {
        $order_address_sql = "SELECT CA.state_id, CA.district_id, CA.zip_code FROM FM_customer_address CA INNER JOIN FM_order O ON CA.id = O.address_id WHERE O.order_no = '$order_no'";
        $order_address_result = $this->db->query($order_address_sql)->row();

        if (!empty($order_address_result->zip_code))
        {
            $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, phone FROM FM_customer WHERE status = 'Y' AND type = 'M' ORDER BY id DESC";
            $merchant_centers_list = $this->db->query($sql)->result();
        }
        else
        {
            $merchant_centers_list = [];
        }

        return $merchant_centers_list;
    }

    function update_order_on_condition($data, $condition)
    {
        $result = $this->db->set($data)->where($condition)->update("FM_order");
        return ($result > 0) ? true : false;
    }

    function get_delivery_driver_details_by_id($id)
    {
        return $this->db->select("driver_id AS id, name, phone")->from("FM_delivery_drivers")->where(["status" => "A", "driver_id" => $id])->get()->row();
    }

    function get_merchant_address_id_by_merchant_id($merchant_id)
    {
        $sql = "SELECT id FROM FM_customer_address WHERE customer_id = $merchant_id AND is_deleted = 'N' ORDER BY id DESC LIMIT 1";
        $merchant_address_details = $this->db->query($sql)->row();
        return (!empty($merchant_address_details->id)) ? $merchant_address_details->id : NULL;
    }

    function get_merchant_center_details_by_id($merchant_id)
    {
        $merchant_center_details = new stdClass;
        
        $sql = "SELECT C.id, CONCAT(C.first_name, ' ', C.last_name) AS name, C.phone, CA.address_1, (SELECT SL.state FROM `FM_state_lookup` SL WHERE SL.id = CA.state_id) AS state, (SELECT DL.name FROM `FM_district_lookup` DL WHERE DL.id = CA.district_id) AS district, (SELECT CL.name FROM `FM_city_lookup` CL WHERE CL.id = CA.city_id) AS city, CA.landmark, CA.zip_code FROM `FM_customer` C INNER JOIN `FM_customer_address` CA ON CA.customer_id = C.id WHERE C.status = 'Y' AND C.type = 'M' AND C.id = $merchant_id ORDER BY C.id DESC";
        $result = $this->db->query($sql)->row();

        if (!empty($result))
        {
            $merchant_center_details->id = (!empty($result->id)) ? $result->id : NULL;
            $merchant_center_details->name = (!empty($result->name)) ? $result->name : NULL;
            $merchant_center_details->phone = (!empty($result->phone)) ? $result->phone : NULL;

            $merchant_center_address = (!empty($result->address_1)) ? $result->address_1 : "";
            $merchant_center_address .= (!empty($result->city)) ? ", ".$result->city : "";
            $merchant_center_address .= (!empty($result->district)) ? ", ".$result->district : "";
            $merchant_center_address .= (!empty($result->state)) ? ", ".$result->state : "";
            $merchant_center_address .= (!empty($result->zip_code)) ? " PIN CODE:".$result->zip_code : "";
            $merchant_center_address .= (!empty($result->landmark)) ? " LANDMARK:".$result->landmark : "";

            $merchant_center_details->address = $merchant_center_address;
        }

        return $merchant_center_details;
    }
    
}

?>