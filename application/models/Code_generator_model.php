<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Code_generator_model extends CI_Model
{

	public function get_coupon_code_list ()
	{
		return $this->db->from('FM_promo_code')->order_by('created_date', 'DESC')->get()->result();
	}

	public function add_new_coupon_code ($coupon_details)
	{
		$this->db->insert("FM_promo_code", $coupon_details);
		return $this->db->affected_rows();
	}

	public function get_coupon_details ($coupon_code)
	{
		$condition = ["status" => "Y", "promo_code" => "$coupon_code"];
		$coupon_details = $this->db->get_where("FM_promo_code", $condition)->row();
		return $coupon_details;
	}

	public function edit_existing_coupon_details ($coupon_code, $coupon_details)
	{
		$condition = ["status" => "Y", "promo_code" => "$coupon_code"];
		$this->db->set($coupon_details);
		$this->db->where($condition);
		$this->db->update("FM_promo_code");
		return $this->db->affected_rows();
	}

	public function get_customers_list ($limit)
	{
		$this->db->from("FM_customer");
		$this->db->where(["status" => "Y"]);
		$this->db->order_by("id","DESC");
		$this->db->limit($limit);
		return $this->db->get()->result();
	}

	public function get_customer_details_by_id ($id)
	{
		$condition = ["status" => "Y", "id" => $id];
		$customer_details = $this->db->get_where("FM_customer", $condition)->row();
		return $customer_details;
	}

	public function search_customers ($search_value)
	{
		$this->db->select("*")->from("FM_customer")->where(["status" => "Y"]);
		$this->db->like("first_name", "$search_value");
		$this->db->or_like("last_name", "$search_value");
		$this->db->or_like("email", "$search_value");
		$this->db->or_like("phone", "$search_value");
		return $this->db->get()->result();
	}

}