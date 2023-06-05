<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Referrals extends CI_Controller {

		function __construct()
		{
			parent::__construct();
			$this->load->model("Referrals_model");
		}

		public function index()
		{	
			$header_data = array();
	        $page_data = array();
	        $left_data = array();
	        $footer_data = array();

	        $header_data['title'] = "Referrals";
	        $left_data['navigation'] = "referrals";

	        // check login or not
	        if($this->common_model->user_login_check())
	        {
	            // allow and get admin details
	            $admin_details = $this->common_model->get_admin_user_details();
	            $header_data['admin_details'] = $admin_details;
	            $left_data['admin_details'] = $admin_details;
	        }
	        else
	        {
	            redirect(base_url(''));
	        }

	        // $postData = array("length"=>500);
	        // $page_data["referrals_list"] = $this->Referrals_model->get_referrals_data_list($postData);      
	        $page_data["record_limit_list"] = $this->get_record_limit_list();

	        $this->load->view('includes/header_view', $header_data);
	        $this->load->view('includes/left_view', $left_data);
	        $this->load->view('referrals_view', $page_data);
	        $this->load->view('includes/footer_view', $footer_data);
		}

		function get_record_limit_list()
		{
			$limit_list = array();
			$maxCount = $this->Referrals_model->count_all_data();
			for($i=100; $i<$maxCount; $i=$i*2)
			{
				$limit_list[] = $i;
			}
			$limit_list[] = $maxCount;
			return $limit_list;
		}

		public function get_referrals_data()
		{
			$LIMIT = 100;
			if(isset($_POST["filter_data"]))
			{
				$filter_data = $_POST["filter_data"];
				$limit_data = explode("&", $filter_data)[0];
				$record_limit = explode("=",$limit_data)[1];
				$LIMIT = intval($record_limit);
			}

			$data_array = array();
			$info_array = $this->Referrals_model->get_data($LIMIT);
			$output = array(
				"data" => $info_array["data"],
				"recordsTotal" => $info_array["countAll"],
				"recordsFiltered" => $info_array["countFiltered"]
			);

			echo json_encode($output);
		}

		// public function getLists()
		// {
	 //        $data = $row = array();
	        
	 //        // Fetch member's records
	 //        $memData = $this->member->getRows($_POST);
	        
	 //        $i = $_POST['start'];
	 //        foreach($memData as $member){
	 //            $i++;
	 //            $created = date( 'jS M Y', strtotime($member->created));
	 //            $status = ($member->status == 1)?'Active':'Inactive';
	 //            $data[] = array($i, $member->first_name, $member->last_name, $member->email, $member->gender, $member->country, $created, $status);
	 //        }
	        
	 //        $output = array(
	 //            "draw" => $_POST['draw'],
	 //            "recordsTotal" => $this->member->countAll(),
	 //            "recordsFiltered" => $this->member->countFiltered($_POST),
	 //            "data" => $data,
	 //        );
	        
	 //        // Output to JSON format
	 //        echo json_encode($output);
	 //    }
	}
?>