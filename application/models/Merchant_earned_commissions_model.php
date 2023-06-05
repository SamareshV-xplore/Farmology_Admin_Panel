<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Merchant_earned_commissions_model extends CI_Model {

    public function get_merchant_earned_commissions_list()
    {
        $sql = "SELECT *, CONCAT(first_name, ' ', last_name) AS name FROM FM_customer WHERE type = 'M' AND status = 'Y' ORDER BY id DESC";

        $result = $this->db->query($sql)->result();
        if (!empty($result)) {
        foreach ($result as $row) {
            $row->earned_commissions = number_format($this->get_earned_commissions($row->id), 2);
        }}

        return $result;
    }

    public function get_earned_commissions($merchant_id)
    {	
        $total_amount = 0.00;
        $SQL = "SELECT * FROM FM_reward WHERE receiver_id='$merchant_id' AND status='NA'";
        $reward_list = $this->db->query($SQL)->result();
        foreach($reward_list as $reward)
        {
            if (substr($reward->source_id, 0, 2) == "FM")
            {
                $total_amount += $this->get_reward_order_price($reward->source_id, $reward->value);
            }
        }
        return $total_amount;
    }

    public function get_reward_discount($code)
    {
        $SQL = "SELECT * FROM FM_ref_code WHERE status='Y' AND code='$code'";
        $ref_code_details = $this->db->query($SQL)->row();
        return $ref_code_details->discount;
    }

    public function get_reward_order_price($order_no, $code)
    {
        $total_price = 0.00;
        $SQL = "SELECT * FROM FM_order WHERE order_no='$order_no'";
        $order_details = $this->db->query($SQL)->row();
        
        if(is_object($order_details))
        {
            $total_price = floatval($order_details->total_price);
        }

        if($total_price==0.00)
        {
            $order_price = 0.00;
        }
        else
        {
            $discount = $this->get_reward_discount($code);
            $order_price = floatval($total_price*($discount/100));
        }
        return $order_price;
    }

}

?>