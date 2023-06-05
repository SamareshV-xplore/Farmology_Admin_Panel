<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	function test_email()
	{
		echo "hi";
		$data = $this->common_model->email_send('koushik.techpro@gmail.com', 'Dummy', "<p>Hi</p>", "");
		print_r($data);
	}
	function test_sms(){
		
		//https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=9062144661&authkey=335354AMUyfpp0uQ5f097111P1&message=Hello!%20This%20is%20a%20test%20message		
		/*$order_no ='FM2012548936';
		$total = '500';
		$text = "You have received a new order with id #".$order_no." and order value Rs #".$total."";
		//$text = "You have received a new order";
		$phone = '9062144661';
		$text = urlencode($text);

		$url = "https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$phone."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text;*/
		//https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".ADMIN_PHONE."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text
		//https://api.msg91.com/api/sendhttp.php?country=91&sender=farmol&route=4&mobiles=".$phone."&authkey=335354AMUyfpp0uQ5f097111P1&message=".$text
		/*$ch  = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data_response = curl_exec($ch);
		curl_close($ch);*/
		//"{\"flow_id\":\"6099082073c31a75216d0a53\",\"mobiles\":\".'$phone_no'.\",\"order\":\".'$order_no'.\"}"


		$order_no ='FM2444455';
		$phone_no = '919062144661';

		$json = array('flow_id' => '6099082073c31a75216d0a53','mobiles' => $phone_no, 'order' => $order_no);
		$json = json_encode($json);
        $ch = curl_init();

        curl_setopt_array($ch, array(
          CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $json,
          CURLOPT_HTTPHEADER => array(
            "authkey: 335354AMUyfpp0uQ5f097111P1",
            "content-type: application/JSON"
          ),
        ));

        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);
                if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
		
	}
	function test_code()
	{
		$response = array('status' => false, 'message' => 'Something went wrong, please try again later.');
        $referral_from_id = '1';
        $referral_to_id = '2';
        $generate_otp_1 = $this->test1();
        $unique_id_1 = $generate_otp_1.$referral_from_id;
        $data = $this->promo_code_model->check_promo_code($unique_id_1);
        $referral_settings = $this->site_settings_model->get_referral_settings();
        $current_date = date('Y-m-d H:i:s');
        $endDate =date('Y-m-d',strtotime('+30 day'));
        //echo $endDate;die();
        if(count($referral_settings) > 0){
	        if(!empty($data) && $data['status'] == 'Y'){
	            $response['status'] = false;
	            $response['message'] = $data['message'];
	        }else{
	            $form_data = array();
	        	$form_data['promo_code'] = $unique_id_1;
	        	$form_data['title'] = 'Referral From code';
	        	$form_data['description'] = 'Get Referral From '.$referral_settings[0]['referral_from'].'% Off on Orders above Rs '.$referral_settings[0]['min_order_amount'];
	        	$form_data['eligible_order_price'] = $referral_settings[0]['min_order_amount'];
	        	$form_data['start_date'] = $current_date;
	        	$form_data['end_date'] = $endDate;
	        	$form_data['discount_limit'] = $referral_settings[0]['referral_from'];
	        	$form_data['discount_type'] = 'P';
	        	$form_data['max_limit'] = $referral_settings[0]['discount_limit'];
	        	$form_data['status'] = 'Y';
	        	$form_data['user_specific'] = 'Y';
	        	$form_data['user_id']  = '1';
	        	$form_data['usage_count'] = '1';
	        	$add_data = $this->promo_code_model->add_promo_code($form_data);
	        	if($add_data['status'] == "Y")
            	{
            		$generate_otp_2 = $this->test1();
            		$unique_id_2 = $generate_otp_2.$referral_to_id;
            		$check = $this->promo_code_model->check_promo_code($unique_id_2);
            		if(!empty($check) && $check['status'] == 'Y'){
	            		$response['status'] = false;
	            		$response['message'] = $check['message'];
	        		}else{
	        			$form_data_1 = array();
			        	$form_data_1['promo_code'] = $unique_id_2;
			        	$form_data_1['title'] = 'Referral To code';
			        	$form_data_1['description'] = 'Get Referral To '.$referral_settings[0]['referral_to'].'% Off on Orders above Rs '.$referral_settings[0]['min_order_amount'];
			        	$form_data_1['eligible_order_price'] = $referral_settings[0]['min_order_amount'];
			        	$form_data_1['start_date'] = $current_date;
			        	$form_data_1['end_date'] = $endDate;
			        	$form_data_1['discount_limit'] = $referral_settings[0]['referral_to'];
			        	$form_data_1['discount_type'] = 'P';
			        	$form_data_1['max_limit'] = $referral_settings[0]['discount_limit'];
			        	$form_data_1['status'] = 'Y';
			        	$form_data_1['user_specific'] = 'Y';
			        	$form_data_1['user_id']  = '2';
			        	$form_data_1['usage_count'] = '1';
			        	$add_data_1 = $this->promo_code_model->add_promo_code($form_data_1);
	        		}
            	}

	            $response['status'] = true;
	            $response['message'] = $data['message'];
	        }
	    }else{
	    	$response['status'] = false;
	        $response['message'] = 'Please fill up referral settings';
	    }    
    echo json_encode($response);
	}

	function generate_random_no()
	{
	    $generate_otp = "";
	    $random_number = "123456789987654321ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $generate_otp  = substr(str_shuffle($random_number), 0, 6);
	    return $generate_otp;
	}

	
	function test_code_generate()
	{
			$users = $this->db->get_where('FM_customer',array('status' => 'Y'))->result();
			foreach($users as $k => $v){
				$random_no = $this->generate_random_no();
				$owned_referral_code = $random_no.$v->id;
				$update_data = array("owned_referral_code" => $owned_referral_code, "updated_date" => date("Y-m-d H:i:s"));
        $this->db->where("id", $v->id);
        $this->db->update("FM_customer", $update_data);
			}
			echo'<pre>';
			print_r($users);
			die();
	}
}
