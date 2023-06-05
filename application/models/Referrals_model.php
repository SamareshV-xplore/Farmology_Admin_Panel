<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Referrals_model extends CI_Model
	{
		public function get_data($LIMIT)
		{
			$info_array = array();
	        $info_array["data"] = $this->get_referrals_data_list($LIMIT);
	        $info_array["countAll"] = $this->count_all_data();
	        $info_array["countFiltered"] = $this->count_filtered_data($LIMIT);
	        return $info_array;
		}

		public function count_all_data()
		{
			$SQL = "SELECT COUNT(*) AS count FROM FM_customer WHERE status='Y' AND (type IS NULL OR type='user')";
			$data = $this->db->query($SQL)->row();
			if(is_object($data)){ return $data->count; } else { return 0; }
		}

		public function count_filtered_data($LIMIT)
		{
			$SQL = "SELECT COUNT(*) AS count FROM FM_customer WHERE status='Y' AND (type IS NULL OR type='user') LIMIT $LIMIT";
			$data = $this->db->query($SQL)->row();
			if(is_object($data)){ return $data->count; } else { return 0; }
		}

	    /*
	     * Fetch members data from the database
	     * @param $_POST filter data based on the posted parameters
	     */
	    // public function getRows($postData){
	    //     $this->_get_datatables_query($postData);
	    //     if($postData['length'] != -1){
	    //         $this->db->limit($postData['length'], $postData['start']);
	    //     }
	    //     $query = $this->db->get();
	    //     return $query->result();
	    // }
	    
	    /*
	     * Count all records
	     */
	    // public function countAll(){
	    //     $this->db->from($this->table);
	    //     return $this->db->count_all_results();
	    // }
	    
	    /*
	     * Count records based on the filter params
	     * @param $_POST filter data based on the posted parameters
	     */
	    // public function countFiltered($postData){
	    //     $this->_get_datatables_query($postData);
	    //     $query = $this->db->get();
	    //     return $query->num_rows();
	    // }
	    
	    /*
	     * Perform the SQL queries needed for an server-side processing requested
	     * @param $_POST filter data based on the posted parameters
	     */
	    // private function _get_datatables_query($postData)
	    // {
	    //     $this->db->from($this->table);
	 
	    //     $i = 0;
	    //     // loop searchable columns 
	    //     foreach($this->column_search as $item){
	    //         // if datatable send POST for search
	    //         if($postData['search']['value']){
	    //             // first loop
	    //             if($i===0){
	    //                 // open bracket
	    //                 $this->db->group_start();
	    //                 $this->db->like($item, $postData['search']['value']);
	    //             }else{
	    //                 $this->db->or_like($item, $postData['search']['value']);
	    //             }
	                
	    //             // last loop
	    //             if(count($this->column_search) - 1 == $i){
	    //                 // close bracket
	    //                 $this->db->group_end();
	    //             }
	    //         }
	    //         $i++;
	    //     }
	         
	    //     if(isset($postData['order'])){
	    //         $this->db->order_by($this->column_order[$postData['order']['0']['column']], $postData['order']['0']['dir']);
	    //     }else if(isset($this->order)){
	    //         $order = $this->order;
	    //         $this->db->order_by(key($order), $order[key($order)]);
	    //     }
	    // }

		public function get_all_customer_id($LIMIT)
		{
			$SQL = "SELECT * FROM FM_customer WHERE status='Y' AND (type IS NULL OR type='user') ORDER BY id DESC LIMIT $LIMIT";
			$customers = $this->db->query($SQL)->result();
			return $customers;
		}

		public function get_referrals_data_list($LIMIT)
		{
			$data = array();
			$customers = $this->get_all_customer_id($LIMIT);
			foreach($customers as $customer)
			{
				$data["id"] = $customer->id;
				$data["name"] = strval($customer->first_name." ".$customer->last_name);
				$data["mobile"] = $customer->phone;
				$data["no_of_refer"] = $this->get_number_of_refer($customer->id);
				$data["pincode"] = $this->get_customer_zipcode($customer->id);
				$data["order_value"] = $this->get_order_value($customer->id);
				$data["last_order"] = $this->get_last_order($customer->id);
				$data["refer_discount"] = $this->get_refer_discount($customer->id);
				$data["amount"] = $this->get_amount($customer->id);
				$data["action"] = "";
				$data_list[] = $data;
			}
			return $data_list;
		}

		public function get_number_of_refer($customer_id)
		{
			$SQL = "SELECT COUNT(*) AS count FROM FM_customer WHERE (type IS NULL OR type='user') AND referral_by='$customer_id'";
			$number_of_refer = $this->db->query($SQL)->row()->count;
			return $number_of_refer;
		}

		public function get_customer_zipcode($customer_id)
		{
			$SQL = "SELECT * FROM FM_customer_address WHERE is_deleted='N' AND customer_id='$customer_id'";
			$customer_address = $this->db->query($SQL)->row();
			if(count((array)$customer_address)>0)
			{
				return $customer_address->zip_code;
			}
			else
			{
				return "";
			}
		}

		public function get_order_value($customer_id)
		{	
			$order_value = floatval("");
			$SQL = "SELECT * FROM FM_reward WHERE event='Order by descended user' AND receiver_id='$customer_id'";
			$reward_list = $this->db->query($SQL)->result();
			foreach($reward_list as $reward)
			{
				$order_value += $this->get_order_total_by_no($reward->source_id);
			}
			return $order_value;
		}

		public function get_order_total_by_no($order_no)
		{
			$SQL = "SELECT * FROM FM_order WHERE order_no='$order_no'";
			$order_data = $this->db->query($SQL)->row();
			if(count((array)$order_data)>0)
			{
				return floatval($order_data->order_total);
			}
			else
			{
				return floatval("");
			}
		}

		public function date_compare($date, $timestamp)
		{
			if($timestamp<strtotime($date))
			{
				$timestamp = strtotime($date);
			}

			return $timestamp;
		}

		public function get_last_order($customer_id)
		{
			$date_list = array();
			$last_order_date = "";
			$max_date_timestamp = 0;
			$SQL = "SELECT * FROM FM_reward WHERE event='Order by descended user' AND receiver_id='$customer_id'";
			$reward_list = $this->db->query($SQL)->result();
			foreach($reward_list as $reward)
			{
				$date_list[] = $this->get_last_order_date_by_no($reward->source_id);
			}

			for($i=0; $i<count($date_list); $i++)
			{
				$max_date_timestamp = $this->date_compare($date_list[$i],$max_date_timestamp);
			}

			if($max_date_timestamp==0)
			{
				return "";
			}
			else
			{
				return date("d/m/Y",$max_date_timestamp);
			}
		}

		public function get_last_order_date_by_no($order_no)
		{
			$SQL = "SELECT * FROM FM_order WHERE order_no='$order_no' ORDER BY created_date DESC LIMIT 1";
			$order_data = $this->db->query($SQL)->row();
			if(count((array)$order_data)>0)
			{
				return $order_data->created_date;
			}
			else
			{
				return "";
			}
		}

		public function get_refer_discount($customer_id)
		{
			$SQL = "SELECT COUNT(*) AS count FROM FM_reward WHERE receiver_id='$customer_id'";
			$total_reward = $this->db->query($SQL)->row();

			$SQL2 = "SELECT COUNT(*) AS count FROM FM_reward WHERE receiver_id='$customer_id' AND status='U'";
			$used_reward = $this->db->query($SQL2)->row();

			return $used_reward->count."(".$total_reward->count.")";
		}

		public function get_amount($customer_id)
		{	
			$total_amount = 0.00;
			$SQL = "SELECT * FROM FM_reward WHERE receiver_id='$customer_id' AND status='U'";
			$reward_list = $this->db->query($SQL)->result();
			foreach($reward_list as $reward)
			{
				$total_amount += $this->get_reward_order_price($reward->redeemed_on_order, $reward->value);
			}
			return $total_amount;
		}

		public function get_reward_discount($code)
		{
			$SQL = "SELECT * FROM FM_ref_code WHERE status='Y' AND code='$code'";
			$ref_code_details = $this->db->query($SQL)->row();
			return $ref_code_details->discount;
		}

		public function get_reward_order_price($order_no, $code)
		{
			$total_price = 0.00;
			$SQL = "SELECT * FROM FM_order WHERE order_no='$order_no'";
			$order_details = $this->db->query($SQL)->row();
			
			if(is_object($order_details))
			{
				$total_price = floatval($order_details->total_price);
			}
	
			if($total_price==0.00)
			{
				$order_price = 0.00;
			}
			else
			{
				$discount = $this->get_reward_discount($code);
				$order_price = floatval($total_price*($discount/100));
			}
			return $order_price;
		}
	}
?>