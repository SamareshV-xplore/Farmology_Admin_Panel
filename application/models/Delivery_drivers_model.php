<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Delivery_drivers_model extends CI_Model {

    public function add_delivery_driver($data)
    {
        return $this->db->insert("FM_delivery_drivers", $data);
    }

    public function get_available_states_list()
    {
        return $this->db->select("id, state")->from("FM_state_lookup")->where(["is_available" => "Y"])->get()->result();
    }

    public function get_districts_list_by_state_id($state_id = NULL)
    {
        if (!empty($state_id)) {
            $condition = ["status" => "Y", "state_id" => $state_id];
        } else {
            $condition = ["status" => "Y"];
        }
        return $this->db->select("id, name")->from("FM_district_lookup")->where($condition)->get()->result();
    }

    public function get_pincodes_list_by_district_id($district_id)
    {
        return $this->db->select("pin_code")->from("FM_pin_code_lookup")->where(["is_deleted" => "N", "district_id" => $district_id])->get()->result();
    }

    public function get_delivery_drivers_list_by_status($status = NULL)
    {
        if (!empty($status))
        {
            $sql = "SELECT DD.driver_id, DD.name, DD.phone, DD.email, DD.profile_image, SL.state, DL.name AS district, DD.status, DD.available_pincodes FROM FM_delivery_drivers DD INNER JOIN FM_state_lookup SL ON SL.id = DD.state_id INNER JOIN FM_district_lookup DL ON DL.id = DD.district_id WHERE DD.status = '$status' ORDER BY DD.id DESC";
        }
        else
        {
            $sql = "SELECT DD.driver_id, DD.name, DD.phone, DD.email, DD.profile_image, SL.state, DL.name AS district, DD.status, DD.available_pincodes FROM FM_delivery_drivers DD INNER JOIN FM_state_lookup SL ON SL.id = DD.state_id INNER JOIN FM_district_lookup DL ON DL.id = DD.district_id ORDER BY DD.id DESC";
        }
        return $this->db->query($sql)->result();
    }

    public function get_delivery_driver_details_by_id($driver_id)
    {
        return $this->db->get_where("FM_delivery_drivers", ["driver_id" => $driver_id])->row();
    }

    public function update_delivery_driver_on_condition($condition, $data)
    {
        $this->db->set($data)->where($condition)->update("FM_delivery_drivers");
    }

    public function delete_delivery_driver($driver_id)
    {
        $this->db->where("driver_id", $driver_id)->delete("FM_delivery_drivers");
    }

}