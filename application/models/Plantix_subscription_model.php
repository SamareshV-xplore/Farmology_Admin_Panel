<?php defined("BASEPATH") or exit("No direct script access allowed");

class Plantix_subscription_model extends CI_Model {

    public function get_paid_subscription_plans_list()
    {
        $result = $this->db->get_where("FM_preferences", ["name" => "plantix_free_subscription_plan_id"])->row();
        $free_subscription_plan_id = (!empty($result->content)) ? $result->content : NULL;
        return $this->db->get_where("plantix_subscription_plans", ["plan_id !=" => $free_subscription_plan_id])->result();
    }

    public function update_paid_subscription_plan($id, $data)
    {
        return $this->db->set($data)->where(["plan_id" => $id])->update("plantix_subscription_plans");
    }

}