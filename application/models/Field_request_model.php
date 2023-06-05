<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Field_request_model extends CI_Model
{
	public function get_field_visit_request_list($filter_data)
	{
		$all_data = [];
		
		if ($filter_data['status'] == 'all') {
			$lists = $this->db->from('FM_field_visit_request')->get()->result();
		}
		else{
			$lists = $this->db->from('FM_field_visit_request')->where('status', $filter_data['status'])->get()->result();
		}

		foreach ($lists as $list) {
			$data['id'] = $list->id;
			$data['full_name'] = $list->full_name;
			$data['phone'] = $list->phone;
			$data['address_1'] = $list->address_1;
			$data['address_2'] = $list->address_2;
			$data['state'] = $list->state;
			$data['pincode'] = $list->pincode;
			$data['req_date'] = date('Y-m-d', strtotime($list->created_timestamp));
			$data['status'] = $list->status;

			$all_data[] = $data;
		}

		return $all_data;
	}
}