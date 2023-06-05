<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_settings_model extends CI_Model {

    function get_order_settings()
    {
        $this->db->select("*");
        $this->db->from("FM_master_order_settings");
        $query = $this->db->get();
        $row = $query->row();
        //print_r($row); exit;
            $response = array("minimum_order_value" => $row->minimum_order_value, "cod_availability" => $row->cod_availability, "online_availability" => $row->online_availability, "max_day_order_limit" => $row->max_day_order_limit, "promo_code_apply_text" => $row->promo_code_apply_text, "updated_date" => $row->updated_date);
       

        return $response;
        
    }

    function add_new_delivery_time_slot($data)
    {
        $start_time = $data['start_time'];
        $end_time = $data['end_time'];

        // check
        $query = $this->db->query("SELECT id FROM FM_delivery_time_slot where start_time <= '".$start_time."' and end_time >= '".$start_time."' and is_deleted = 'N'");
        if($query->num_rows() > 0)
        {
            // throw error
             $this->session->set_flashdata('error_message', "Already exist time slot between ".$start_time. " - ". $end_time);
        }
        else
        {
            $insert_data = array("start_time" => $start_time, "end_time" => $end_time, "is_deleted" => "N");
            $this->db->insert("FM_delivery_time_slot", $insert_data);
            $this->session->set_flashdata('success_message', "New Delivery Time Slot Successfully Added.");
        }

        return  true;

    }

    function save_referral_data($data)
    {
        $referral_form = $data['referral_from'];
        $referral_to = $data['referral_to'];
        $max_discount_amount = $data['min_order_amount'];
        $discount_limit = $data['discount_limit'];


        $insert_data = array("referral_from" => $referral_form, "referral_to" => $referral_to, "min_order_amount" => $max_discount_amount, "discount_limit" => $discount_limit);
            $this->db->insert("FM_referral_settings", $insert_data);
            $this->session->set_flashdata('success_message', "New Referral settings data Successfully Added.");
        return  true;    
    }

    function update_referral_data($data)
    {
        $id = $data['id'];
        $referral_form = $data['referral_from'];
        $referral_to = $data['referral_to'];
        $max_discount_amount = $data['min_order_amount'];
        $discount_limit = $data['discount_limit'];


        $update_data = array("referral_from" => $referral_form, "referral_to" => $referral_to, "min_order_amount" => $max_discount_amount, "discount_limit" => $discount_limit);
            $this->db->where("id", $id);
            $this->db->update("FM_referral_settings", $update_data);
            $this->session->set_flashdata('success_message', "Referral settings data Successfully Updated.");
        return  true;    
    }

    function get_delivery_time_slot()
    {
         $time_slot = array();

        $this->db->select("*");
        $this->db->from("FM_delivery_time_slot");
        $this->db->where("is_deleted", "N");
        $this->db->order_by("start_time", "ASC");
        $query = $this->db->get();


        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $start_time = $rows->start_time;
                $end_time = $rows->end_time;
                if($start_time == 12)
                {
                    $start_str = $start_time ." PM";
                }
                else if($start_time > 12)
                {
                    $start_str = $start_time - 12 ." PM";
                }
                else
                {
                    $start_str = $start_time ." AM";
                }

                if($end_time == 12)
                {
                    $end_str = $end_time ." PM";
                }
                else if($end_time > 12)
                {
                    $end_str = $end_time - 12 ." PM";
                }
                else
                {
                    $end_str = $end_time ." AM";
                }
                $time_slot[] = array("id" => $rows->id, "time_slot" => $start_str." - ".$end_str);
            }
        }
        
        return $time_slot;

    }

    function get_order_block_list()
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_order_block");
        $this->db->order_by("block_date", "DESC");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
           $list = $query->result_array(); 
        }

        return $list;
    }

    function get_referral_settings()
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_referral_settings");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
           $list = $query->result_array(); 
        }

        return $list;
    }

    function delete_block_date_by_id($id = 0)
    {
        $this->db->where("id", $id);
        $this->db->delete("FM_order_block");
        return true;
    }

    function delete_time_slot_by_id($id = 0)
    {
        $update_data = array("is_deleted" =>  "Y");
    
        $this->db->where("is_deleted", "N");
        $this->db->where("id", $id);

        $this->db->update("FM_delivery_time_slot", $update_data);
       
        if($this->db->affected_rows() > 0)
        {
            $this->session->set_flashdata('success_message', "Delivery time slot successfully deleted.");
        }
        else
        {
            $this->session->set_flashdata('error_message', "Delivery time slot not found or already deleted.");
        }
    }

    function new_order_date_block($date_arr)
    {
        if(count($date_arr) > 0)
        {   
            foreach($date_arr as $date_row)
            {
                // check 
                $this->db->select('id');
                $this->db->from("FM_order_block");
                $this->db->where("block_date", $date_row);
                $ck_query = $this->db->get();
                if($ck_query->num_rows() > 0)
                {
                    $ck_row = $ck_query->row();
                    $update_data = array("created_date" => date("Y-m-d H:i:s"));
                    $this->db->where("id", $ck_row->id);
                    $this->db->update("FM_order_block", $update_data);
                }
                else
                {
                    $inser_data = array("block_date" => $date_row, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_order_block", $inser_data);


                }
            }
            
        }
        $this->session->set_flashdata('success_message', "Order successfully blocked for new date range.");
        return true;
    }

    function update_order_data($data)
    {
        $minimum_order_value = $data['minimum_order_value'];
        $max_day_order_limit = $data['max_day_order_limit'];
        $cod_availability = $data['cod_availability'];
        $online_availability = $data['online_availability'];
        $promo_code_apply_text = $data['promo_code_apply_text'];

        $update_data = array("minimum_order_value" => $minimum_order_value, "max_day_order_limit" => $max_day_order_limit, "cod_availability" => $cod_availability, "online_availability" => $online_availability, "promo_code_apply_text" => $promo_code_apply_text, "updated_date" => date("Y-m-d H:i:s"));
        $this->db->update("FM_master_order_settings", $update_data);

        $this->session->set_flashdata('success_message', "Order settings successfully updated.");
                redirect(base_url('master-settings'));
        return true;
    }

    function get_latest_app_version ()
    {
        $latest_app_version = null;
        $condition = ["name" => "latest_version"];
        $app_version = $this->db->get_where("FM_preferences", $condition)->row();
        if (isset($app_version))
        {
            $latest_app_version = $app_version->content;
        }

        return $latest_app_version;
    }

    function update_latest_app_version ($version)
    {
        $condition = ["name" => "latest_version"];
        $data = ["content" => number_format(intval($version), 1)];

        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update("FM_preferences");

        return $this->db->affected_rows();
    }

    function get_subscription_amount ()
    {
        $report_subscription_amount = null;
        $condition = ["name" => "report_subscription_amount"];
        $subscription_amount = $this->db->get_where("FM_preferences", $condition)->row();
        if (isset($subscription_amount))
        {
            $report_subscription_amount = $subscription_amount->content;
        }

        return $report_subscription_amount;
    }

    function update_subscription_amount ($amount)
    {
        $condition = ["name" => "report_subscription_amount"];
        $data = ["content" => $amount];

        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update("FM_preferences");

        return $this->db->affected_rows();
    }
}
