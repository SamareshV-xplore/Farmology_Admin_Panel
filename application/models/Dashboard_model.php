<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    function get_product_count()
    {
        $total_product_query = $this->db->query("SELECT count(id) as total_count FROM `FM_product` where status != 'D'");
        $total_product = $total_product_query->row()->total_count;

        $active_product_query = $this->db->query("SELECT count(id) as total_count FROM `FM_product` where status = 'Y'");
        $active_product = $active_product_query->row()->total_count;

        $inactive_product_query = $this->db->query("SELECT count(id) as total_count FROM `FM_product` where status = 'N'");
        $inactive_product = $inactive_product_query->row()->total_count;

        $response = array("total_product" => $total_product, "active_product" => $active_product, "inactive_product" => $inactive_product);

        return $response;


        
    }

    function get_order_count()
    {
        $processing_order_query = $this->db->query("SELECT count(id) as total_count FROM `FM_order` where status = 'P'");
        $processing_order = $processing_order_query->row()->total_count;

        $shipping_order_query = $this->db->query("SELECT count(id) as total_count FROM `FM_order` where status = 'S'");
        $shipping_order = $shipping_order_query->row()->total_count;

        $complete_order_query = $this->db->query("SELECT count(id) as total_count FROM `FM_order` where status = 'D'");
        $complete_order = $complete_order_query->row()->total_count;

        $cancelled_order_query = $this->db->query("SELECT count(id) as total_count FROM `FM_order` where status = 'C'");
        $cancelled_order = $cancelled_order_query->row()->total_count;

        $response = array("processing_order" => $processing_order, "shipping_order" => $shipping_order, "complete_order" => $complete_order, "cancelled_order" => $cancelled_order);

        return $response;
    }


}
