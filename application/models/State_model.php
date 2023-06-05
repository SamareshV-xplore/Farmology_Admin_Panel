<?php defined('BASEPATH') OR exit('No direct script access allowed');

class State_model extends CI_Model {
    
    public function get_list_of_states()
    {
        return $this->db->get("FM_state_lookup")->result();
    }

    public function update_state_on_condition($data, $condition)
    {
        $this->db->set($data);
        $this->db->where($condition);
        return $this->db->update("FM_state_lookup");
    }

    public function delete_state($state_id)
    {
        $this->db->where("id", $state_id);
        $this->db->delete("FM_state_lookup");
    }

}