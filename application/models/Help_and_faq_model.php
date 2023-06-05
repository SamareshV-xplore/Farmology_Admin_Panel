<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Help_and_faq_model extends CI_Model {

    public function get_support_details()
    {
        $support_details = [];
        $result = $this->db->get("FM_support_details")->result();
        if (!empty($result))
        {
            foreach ($result as $row)
            {
                $support_details[$row->name] = $row->value;
            }
        }
        return $support_details;
    }

    public function update_support_details_on_condition($data, $condition)
    {
        $this->db->set($data)->where($condition)->update("FM_support_details");
    }

    public function add_FAQ($data)
    {
        return $this->db->insert("FM_FAQ", $data);
    }

    public function get_list_of_FAQ()
    {
        return $this->db->select("*")->from("FM_FAQ")->order_by("id", "DESC")->get()->result();
    }

    public function update_FAQ_on_condition($data, $condition)
    {
        return $this->db->set($data)->where($condition)->update("FM_FAQ");
    }

    public function delete_FAQ_on_condition($condition)
    {
        $this->db->where($condition)->delete("FM_FAQ");
    }

}

?>