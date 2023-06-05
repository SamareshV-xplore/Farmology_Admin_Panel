<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Delivery_date_model extends CI_Model
{

    public function add_delivery_date($data)
    {
        return $this->db->insert("FM_delivery_dates", $data);
    }

    public function get_list_of_delivery_dates()
    {
        return $this->db->select("*")->from("FM_delivery_dates")->order_by("id", "DESC")->get()->result();
    }

    public function get_delivery_date_details_on_condition($condition)
    {
        return $this->db->get_where("FM_delivery_dates", $condition)->row();
    }

    public function update_delivery_date_on_condition($data, $condition)
    {
        return $this->db->set($data)->where($condition)->update("FM_delivery_dates");
    }

    public function delete_delivery_date_on_condition($condition)
    {
        $this->db->where($condition)->delete("FM_delivery_dates");
    }

    public function get_list_of_districts()
    {
        return $this->db->query("SELECT id, name FROM FM_district_lookup WHERE status = 'Y'")->result();
    }

}

?>