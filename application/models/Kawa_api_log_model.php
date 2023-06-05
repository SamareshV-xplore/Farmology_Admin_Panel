<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kawa_api_log_model extends CI_Model
{
	public function get_all_logs ()
	{
		$all_logs_list = [];
		$all_logs_list = $this->db->select("*")->from("FM_kawa_api_log")->order_by("id","DESC")->get()->result();
		if (count($all_logs_list) > 0)
		{
			foreach ($all_logs_list as $log)
			{
				$log->username = $this->get_user_name_by_id($log->user_id);
			}
		}
		return $all_logs_list;
	}

	public function get_user_name_by_id ($id)
	{
		$customer_full_name = null;
		$customer = $this->db->get_where("FM_customer", ["id" => $id])->row();
		if (isset($customer))
		{
			$customer_full_name = $customer->first_name." ".$customer->last_name;
		}

		return $customer_full_name;
	}
}