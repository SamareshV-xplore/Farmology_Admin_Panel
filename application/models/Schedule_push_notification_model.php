<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule_push_notification_model extends CI_Model
{
	public function add($data)
	{
		$this->db->insert("FM_schedule_push_notifications", $data);
		return $this->db->affected_rows();
	}

	public function get()
	{
		$notifications_list = $this->db->select("*")->from("FM_schedule_push_notifications")->where("status !=", "D")->get()->result();
		if (!empty($notifications_list))
		{
			foreach ($notifications_list as $notification)
			{
				$notification->image = FRONT_URL.$notification->image;
				$notification->redirection_name = $this->get_redireciton_name_by_value($notification->redirect_to);
				$notification->state_name = $this->get_state_name_by_id($notification->target_state);
			}
		}
		return $notifications_list;
	}

	private function get_redireciton_name_by_value($value)
	{
		$name = "";
		$row = $this->db->get_where("FM_app_redirections", ["value" => $value])->row();
		if (!empty($row))
		{
			$name = $row->name;
		}
		return $name;
	}

	private function get_state_name_by_id($id)
	{
		$name = "";
		$row = $this->db->get_where("FM_state_lookup", ["id" => $id])->row();
		if (!empty($row))
		{
			$name = ucwords(strtolower($row->state));
		}
		return $name;
	}

	public function get_app_redirection_options()
	{
		$options = $this->db->select("*")->from("FM_app_redirections")->where("status","A")->get()->result();
		return $options;
	}
	
	public function get_available_states()
	{
		$states = $this->db->select("*")->from("FM_state_lookup")->where("is_deleted","Y")->get()->result();
		if (!empty($states))
		{
			foreach ($states as $state)
			{
				$state->state = ucwords(strtolower($state->state));
			}
		}
		return $states;
	}

	public function edit($hash_id, $data)
	{
		$condition = ["hash_id" => $hash_id];
		if (!empty($data["image"]))
		{
			$prev_checking_row = $this->db->get_where("FM_schedule_push_notifications", $condition)->row();
			if (!empty($prev_checking_row->image))
			{
				$image = $prev_checking_row->image;
				$this->delete_file($image);
			}
		}
		$this->db->set($data)->where($condition)->update("FM_schedule_push_notifications");
		return $this->db->affected_rows();
	}

	public function delete($hash_id)
	{
		$condition = ["hash_id" => $hash_id];
		$prev_checking_row = $this->db->get_where("FM_schedule_push_notifications", $condition)->row();
		if (!empty($prev_checking_row))
		{
			$this->delete_file($prev_checking_row->image);
		}
		$this->db->delete("FM_schedule_push_notifications", $condition);
		return $this->db->affected_rows();
	}

	private function delete_file($file)
	{
		$file_path = FCPATH."media/".$file;
		if (file_exists($file_path))
		{
			unlink($file_path);
		}
	}
}

?>