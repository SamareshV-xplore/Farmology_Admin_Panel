<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Common_model extends CI_Model
{
    function email_send_with_attachment($send_to, $subject, $body, $attachment)
    {
        $this->load->library('email');
        $result = $this->email
            ->from(FROM_EMAIL, 'Farmology')
            ->to($send_to)
            ->subject($subject)
            ->message($body)
            ->attach($attachment)
            ->send();
            return $result;
    }



    function createDateRangeArray($start, $end) {

    $range = array();

    if (is_string($start) === true) $start = strtotime($start." 00:00:00");
    if (is_string($end) === true ) $end = strtotime($end." 23:59:59");

    if ($start > $end) return $this->createDateRangeArray($end, $start);

    do {
    $range[] = date('Y-m-d', $start);
    $start = strtotime("+ 1 day", $start);
    }
    while($start < $end);

    return $range;
    } 

     function email_send($send_to, $subject, $body, $cc = "")
    {
        $this->load->library('email');
        $result = $this->email
            ->from(FROM_EMAIL, 'Farmology')
            ->to($send_to)
            ->cc($cc)
            ->subject($subject)
            ->message($body)
            ->send();
            return $result;
    }
    
    function user_login_check()
    {
        $return_status = false;
        $user_id = $this->session->userdata('admin_user_id');
        if($user_id > 0)
        {
            $return_status = true;
        }
        return $return_status;
    }    

    function get_admin_user_details()
    {
        $response = array();
        $user_id = $this->session->userdata('admin_user_id');

        $this->db->select("*");
        $this->db->from("FM_admin_user");
        $this->db->where("id", $user_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $response = array("id" => $row->id, "name" => $row->name, "username" => $row->username, "email" => $row->email, "phone" => $row->phone, "profile_image" => base_url($row->profile_image), "created_at" => $row->created_at, "updated_at" => $row->updated_at, "user_type" => $row->user_type, "status" => $row->status);
        }
        return $response;

    }

    public function slugify($text)
    {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }

    function get_toggle_status()
    {      
        
        $toggle_action = $this->input->cookie('toggle_action', TRUE);
        if($toggle_action == '1')
        {
            $reurn_str = "close";
        }
        else
        {
            $reurn_str = "open";
        }
    

        return $reurn_str;
    }


    public function manage_notification($device_data, $message){

        $android_devices = array();
        $ios_devices = array();

        foreach ($device_data as $details){
            if($details['device_type'] == 'A'){
                $android_devices[] = $details['device_token'];
            }elseif($details['device_type'] == 'I'){
                $ios_devices[] = $details['device_token'];
            }
        }
        if(count($android_devices) > 0){
            $result = $this->send_android_notification($android_devices, array('message' => $message));
        }

        if(count($ios_devices) > 0){
            $result = $this->send_android_notification($android_devices, array('message' => $message));
        }

        return $result;
    }

    function send_android_notification($registration_ids, $message) {
        /*$fields = array(
            'registration_ids' => array($registration_ids),
            'data'=> $message,
        );
        $headers = array(
            'Authorization: key=AAAAF0IBgPU:APA91bGSUhJCOPRbuat6Sg3O-KrRIwOnkQYBH1jNgmVJ5heDsjymCrFAmLMAZfFGRO1zbssgMTk_UtPj96JVdaiJx0Lav99mK-pCEAaqhX5iRtGM6NUKGzLLhf7Bcd-ZXS-cFKp09b-q7rQOrHzsII0x7vkCwJbXrg', // FIREBASE_API_KEY_FOR_ANDROID_NOTIFICATION
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

        // Disabling SSL Certificate support temporarly
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Execute post
        $result = curl_exec($ch );
        if($result === false){
            die('Curl failed:' .curl_errno($ch));
        }

        // Close connection
        curl_close( $ch );
        return $result;*/
        return true;
    }

    function send_ios_notification($registration_ids, $message) {
        return true;
    }



    function get_city_id()
    {
        $city = $this->input->cookie('city', TRUE);
        if ($city > 0)
        {
            $reurn_str = $city;
        }
        else
        {
            $reurn_str = 0;
        }
        return $reurn_str;
    }
    function get_city_list()
    {
        $list = array();
        $this->db->select("*");
        $this->db->from("FM_city_lookup");
        $this->db->where("status", "Y");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "name" => $row->name,
                    "image" => base_url($row->image),
                    "created_date" => $row->created_date
                );
            }
        }
        return $list;
    }

    function get_city_name_by_id($id = 0)
    {
        $name = NULL;
        $this->db->select("name");
        $this->db->from("FM_city_lookup");
        $this->db->where("id", $id);
        $this->db->where("status", "Y");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row  = $query->row();
            $name = $row->name;
        }
        return $name;
    }

    function get_district_name_by_id($id = NULL)
    {
        if (!empty($id)) {
            $district_details = $this->db->query("SELECT name FROM FM_district_lookup WHERE id = $id")->row();
        }
        return (!empty($district_details->name)) ? $district_details->name : NULL;
    }

    function get_delivery_charge_by_city_id($id = 0)
    {
        $charge = 0;
        $this->db->select("charge");
        $this->db->from("FM_city_lookup");
        $this->db->where("id", $id);
        $this->db->where("status", "Y");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row  = $query->row();
            $charge = $row->charge;
        }
        return $charge;
    }
    function category_list_tree($featured = "N")
    {
        $list          = array();
        $filter_status = "Y";
        // get 1 st lavel
        $lavel_1       = $this->get_category_list_by_parent_id(0, $filter_status, $featured);
        if (count($lavel_1) > 0)
        {
            foreach ($lavel_1 as $level_1_row)
            {
                $level_2_list = array();
                // get 2nd level
                $lavel_2      = $this->get_category_list_by_parent_id($level_1_row['id'], $filter_status);
                if (count($lavel_2) > 0)
                {
                    foreach ($lavel_2 as $lavel_2_row)
                    {
                        $level_3_list = array();
                        $lavel_3      = $this->get_category_list_by_parent_id($lavel_2_row['id'], $filter_status);
                        //-----------------
                        if (count($lavel_3) > 0)
                        {
                            foreach ($lavel_3 as $lavel_3_row)
                            {
                                $level_3_list[] = array(
                                    "id" => $lavel_3_row['id'],
                                    "title" => $lavel_3_row['title'],
                                    "description" => $lavel_3_row['description'],
                                    "slug" => $lavel_3_row['slug'],
                                    "image" => $lavel_3_row['image'],
                                    "status" => $lavel_3_row['status'],
                                    "created_date" => $lavel_3_row['created_date'],
                                    "updated_date" => $lavel_3_row['updated_date']
                                );
                            }
                        }
                        //-------------------    
                        $level_2_list[] = array(
                            "id" => $lavel_2_row['id'],
                            "title" => $lavel_2_row['title'],
                            "description" => $lavel_2_row['description'],
                            "slug" => $lavel_2_row['slug'],
                            "image" => $lavel_2_row['image'],
                            "status" => $lavel_2_row['status'],
                            "created_date" => $lavel_2_row['created_date'],
                            "updated_date" => $lavel_2_row['updated_date'],
                            "child" => $level_3_list
                        );
                    }
                }
                $list[] = array(
                    "id" => $level_1_row['id'],
                    "title" => $level_1_row['title'],
                    "description" => $level_1_row['description'],
                    "slug" => $level_1_row['slug'],
                    "image" => $level_1_row['image'],
                    "status" => $level_1_row['status'],
                    "created_date" => $level_1_row['created_date'],
                    "updated_date" => $level_1_row['updated_date'],
                    "child" => $level_2_list
                );
            }
        }
        return $list;
    }
    function get_category_list_by_parent_id($parent_id = 0, $status = 'all', $featured = "N")
    {
        $category_row = array();
        $this->db->select("*");
        $this->db->from("FM_product_category");
        $this->db->where("parent_id", $parent_id);
        $this->db->where("status !=", 'D');
        if ($featured == 'Y')
        {
            $this->db->where("is_featured", "Y");
        }
        if ($status != 'all')
        {
            $this->db->where("status", $status);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $rows)
            {
                $parent_details = $this->get_category_short_details_by_id($rows->parent_id);
                $category_row[] = array(
                    "id" => $rows->id,
                    "title" => $rows->title,
                    "description" => $rows->description,
                    "slug" => $rows->slug,
                    "image" => base_url('') . $rows->image,
                    "status" => $rows->status,
                    "created_date" => $rows->created_date,
                    "updated_date" => $rows->updated_date,
                    "parent_details" => $parent_details,
                    "is_featured" => $rows->is_featured
                );
            }
        }
        return $category_row;
    }
    function get_category_short_details_by_id($cate_id = 0)
    {
        $response = array(
            "id" => "0",
            "title" => "Parent"
        );
        $this->db->select("id, title");
        $this->db->from("FM_product_category");
        $this->db->where("id", $cate_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row      = $query->row();
            $response = array(
                "id" => $row->id,
                "title" => $row->title
            );
        }
        return $response;
    }
    function get_category_id_by_slug($slug)
    {
        $id = 0;
        $this->db->select('id');
        $this->db->from("FM_product_category");
        $this->db->where("status", "Y");
        $this->db->where("slug", $slug);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
            $id  = $row->id;
        }
        return $id;
    }
    function get_product_id_by_slug($slug)
    {
        $id = 0;
        $this->db->select('id');
        $this->db->from("FM_product");
        $this->db->where("status", "Y");
        $this->db->where("slug", $slug);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
            $id  = $row->id;
        }
        return $id;
    }
    function get_state_name_by_id($state_id = 0)
    {
        $state_name = "";
        $this->db->select("state");
        $this->db->from("FM_state_lookup");
        $this->db->where("id", $state_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row        = $query->row();
            $state_name = $row->state;
        }
        return $state_name;
    }
    function get_city_id_by_pincode($pincode = 0)
    {
        $city_id = 0;
        $this->db->select("city_id");
        $this->db->from("FM_pin_code_lookup");
        $this->db->where("pin_code", $pincode);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row     = $query->row();
            $city_id = $row->city_id;
        }
        return $city_id;
    }
    function get_state_id_by_city_id($city_id = 0)
    {
        $state_id = 0;
        $this->db->select("state_id");
        $this->db->from("FM_city_lookup");
        $this->db->where("id", $city_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row      = $query->row();
            $state_id = $row->state_id;
        }
        return $state_id;
    }
    function check_zip_code_availability($zip_code = 0)
    {
        $response = array(
            "status" => "N",
            "message" => "We don't delivered this Zip Code."
        );
        $this->db->select("id");
        $this->db->from("FM_pin_code_lookup");
        $this->db->where("pin_code", $zip_code);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $response = array(
                "status" => "Y",
                "message" => "Delivery available."
            );
        }
        return $response;
    }
    function get_category_details_by_id($cate_id = 0)
    {
        $response = array();
        $this->db->select("*");
        $this->db->from("FM_product_category");
        $this->db->where("id", $cate_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row      = $query->row();
            $response = array(
                "id" => $row->id,
                "title" => $row->title,
                "description" => $row->description,
                "slug" => $row->slug,
                "image" => base_url($row->image),
                "parent_id" => $row->parent_id,
                "is_featured" => $row->is_featured
            );
        }
        return $response;
    }
    function get_page_content_by_id($id = 0)
    {
        $page_content = array();
        $this->db->select("*");
        $this->db->from("FM_page_content");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
            if (trim($row->image) == '')
            {
                $image = "";
            }
            else
            {
                $image = base_url($row->image);
            }
            $page_content = array(
                "id" => $row->id,
                "title" => $row->title,
                "image" => $image,
                "content" => $row->page_content
            );
        }
        return $page_content;
    }
    function get_page_meta_data($id = 0)
    {
        $meta_data = array(
            "meta_title" => "",
            "meta_description" => "",
            "meta_keyword" => ""
        );
        $this->db->select("*");
        $this->db->from("FM_page_meta_data");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row       = $query->row();
            $meta_data = array(
                "meta_title" => $row->meta_title,
                "meta_description" => $row->meta_description,
                "meta_keyword" => $row->meta_keyword
            );
        }
        return $meta_data;
    }
    function get_product_meta_data($product_id = 0)
    {
        $meta_data = array(
            "meta_title" => "",
            "meta_description" => "",
            "meta_keyword" => ""
        );
        $this->db->select("*");
        $this->db->from("FM_product_meta_data");
        $this->db->where("product_id", $product_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row       = $query->row();
            $meta_data = array(
                "meta_title" => $row->meta_title,
                "meta_description" => $row->meta_description,
                "meta_keyword" => $row->meta_keyword
            );
        }
        return $meta_data;
    }
    function get_category_meta_data($category_id = 0)
    {
        $meta_data = array(
            "meta_title" => "",
            "meta_description" => "",
            "meta_keyword" => ""
        );
        $this->db->select("*");
        $this->db->from("FM_category_meta_data");
        $this->db->where("category_id", $category_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row       = $query->row();
            $meta_data = array(
                "meta_title" => $row->meta_title,
                "meta_description" => $row->meta_description,
                "meta_keyword" => $row->meta_keyword
            );
        }
        return $meta_data;
    }
    function get_minimum_order_value()
    {
        $this->db->select("minimum_order_value");
        $this->db->from("FM_master_order_settings");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $minimum_order_value = $row->minimum_order_value;
        }
        else
        {
            $minimum_order_value = 0;
        }

        return $minimum_order_value;        

    }

    function get_payment_availability()
    {
        $this->db->select("cod_availability, online_availability");
        $this->db->from("FM_master_order_settings");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $cod_availability = $row->cod_availability;
            $online_availability = $row->online_availability;
        }
        else
        {
            $cod_availability = "N";
            $online_availability = "N";
        }

        return $response = array("cod_availability" => $cod_availability, "online_availability" => $online_availability);

    }

    function get_delivery_time_slot($date)
    {
         $time_slot = array();

        // check order block for this date
        $this->db->select("id");
        $this->db->from("FM_order_block");
        $this->db->where("block_date", $date);
        $block_query = $this->db->get();

        if($block_query->num_rows() > 0)
        {
            // return block
        }
        else
        {


       
        if(date("Y-m-d") == $date)
        {
            $current_hour = date("H");

            $this->db->select("*");
            $this->db->from("FM_delivery_time_slot");
            $this->db->where("start_time >", $current_hour);
            $this->db->where("is_deleted", "N");
            $query = $this->db->get();

        }
        else
        {
            $this->db->select("*");
            $this->db->from("FM_delivery_time_slot");
            $this->db->where("is_deleted", "N");
            $query = $this->db->get();

        }

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $start_time = $rows->start_time;
                $end_time = $rows->end_time;
                if($start_time == 12)
                {
                    $start_str = $start_time ." PM";
                }
                else if($start_time > 12)
                {
                    $start_str = $start_time - 12 ." PM";
                }
                else
                {
                    $start_str = $start_time ." AM";
                }

                if($end_time == 12)
                {
                    $end_str = $end_time ." PM";
                }
                else if($end_time > 12)
                {
                    $end_str = $end_time - 12 ." PM";
                }
                else
                {
                    $end_str = $end_time ." AM";
                }
                $time_slot[] = array("id" => $rows->id, "time_slot" => $start_str." - ".$end_str);
            }
        }
        }
        return $time_slot;

    }

    function get_delivery_time_slot_detail_by_id($id = 0)
    {
        $time_slot = array();
        $current_hour = date("H");

        $this->db->select("*");
        $this->db->from("FM_delivery_time_slot");
        $this->db->where("id", $id);
        $query = $this->db->get();       

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $start_time = $rows->start_time;
                $end_time = $rows->end_time;
                if($start_time == 12)
                {
                    $start_str = $start_time ." PM";
                }
                else if($start_time > 12)
                {
                    $start_str = $start_time - 12 ." PM";
                }
                else
                {
                    $start_str = $start_time ." AM";
                }

                if($end_time == 12)
                {
                    $end_str = $end_time ." PM";
                }
                else if($end_time > 12)
                {
                    $end_str = $end_time - 12 ." PM";
                }
                else
                {
                    $end_str = $end_time ." AM";
                }
                $time_slot = array("id" => $rows->id, "time_slot" => $start_str." - ".$end_str);
            }
        }

        return $time_slot;

    }

    function product_count_by_category_id($category_id = 0)
    {
        $count = 0;

        $this->db->select("COUNT(id) as product_count");
        $this->db->from("FM_product");
        $this->db->where("status !=", "D");
        $this->db->where("category_id", $category_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $count = $query->row()->product_count;
        }

        return $count;
    }


    function get_product_count_by_category_id($category_id){
        $count = 0;

        $this->db->distinct();
        $this->db->select("COUNT(product_id) as product_count");
        $this->db->from("FM_category_mapping");
        $this->db->join('FM_product','FM_product.id = FM_category_mapping.product_id', 'left');
        $this->db->where("FM_category_mapping.category_id", $category_id);
        $this->db->where("FM_product.status !=", "D");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $count = $query->row()->product_count;
        }
        return $count;
    }

    function child_category_count_by_category_id($category_id = 0)
    {
        $count = 0;

        $this->db->select("COUNT(id) as child_count");
        $this->db->from("FM_product_category");
        $this->db->where("status !=", "D");
        $this->db->where("parent_id", $category_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $count = $query->row()->child_count;
        }

        return $count;
    }

    function sellproduces_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_sell_produce");
        /*if($filter_data['status'] == 'A')
        {
            $this->db->where("status", "A");
        }
        elseif($filter_data['status'] == 'S')
        {
            $this->db->where("status", "S");
        }
        else
        {
            // no status check
        }*/
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $user_details = $this->db->get_where('FM_customer',array('id' =>$row->customer_id,'status' => 'Y'))->row();
                $crop_details = $this->db->get_where('FM_crop',array('id' =>$row->crop_id,'status' => 'Y'))->row();

                $list[] = array(
                    "id" => $row->id,
                    "customer_name" => (!empty($user_details)?$user_details->first_name.' '.$user_details->last_name:''),
                    "crop_name" => (!empty($crop_details)?$crop_details->title:''),
                    "variety" => $row->variety,
                    "qty" => $row->qty.' '.$row->qty_unit,
                    "price" => $row->price.' Rs. (per qty)',
                    "available_date" => $row->available_date,
                    "status" => $row->status,
                    "created_date" => $row->created_date,
                    "updated_date" => $row->updated_date
                );
            }
        }
        return $list;
    }

    function get_produce_image_list_by_id($sell_produce_id)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_sell_produce_image");
        $this->db->where("sell_produce_id", $sell_produce_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array("id" => $row->id, "image" => FRONT_URL.$row->image);
            }
        }

        return $list;
    }

    function get_produce_details_by_id($sell_produce_id)
    {
        $details = array();

        $this->db->select("*");
        $this->db->from("FM_sell_produce");
        $this->db->where("id", $sell_produce_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $produce_row = $query->row();


            $user_details = $this->db->get_where('FM_customer',array('id' =>$produce_row->customer_id,'status' => 'Y'))->row();
            $crop_details = $this->db->get_where('FM_crop',array('id' =>$produce_row->crop_id,'status' => 'Y'))->row();

            $images = $this->get_produce_image_list_by_id($produce_row->id);

                $details  = array('id'=> $produce_row->id,"customer_name" => (!empty($user_details)?$user_details->first_name.' '.$user_details->last_name:''), "crop_name" => (!empty($crop_details)?$crop_details->title:''), 'variety' => $produce_row->variety, 'images' => $images, 'qty' => $produce_row->qty, 'qty_unit' => $produce_row->qty_unit , 'price' => $produce_row->price, 'available_date' => $produce_row->available_date, 'available_in_days' => $produce_row->available_in_days, 'note' => $produce_row->note, 'created_date' => $produce_row->created_date,'status' => $produce_row->status);
                $response = array("status" => "Y", "message" => "Details found", "details" => $details);
        }else{
            $response = array("status" => "N", "message" => "No details found. Maybe user is already deleted.");
        }
        return $response;
    }

    function get_community_list()
    {
        $list = $image_list = array();
        $this->db->select("*");
        $this->db->from("FM_ask_community");
        $this->db->order_by("id", "DESC");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $community_row)
            {
                $image_list  = $this->get_cummunity_image_by_community_id($community_row->id);
                
                $user_details =  $this->user_details_by_id($community_row->customer_id);
                $comments = $this->comments_count_by_community_id($community_row->id);
                $list [] = array('id'=> $community_row->id, 'quesstion' => $community_row->quesstion, 'problem_description' => $community_row->problem_description, 'image' => $image_list, 'user_details' => $user_details, 'comments_count' => $comments,'status' => $community_row->status);
            }

        }
        return $list;
    }

    function get_community_details_by_id($community_id)
    {
        $details = $image_list = array();
        $this->db->select("*");
        $this->db->from("FM_ask_community");
        $this->db->where("id", $community_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $community_row = $query->row();
            $image_list  = $this->get_cummunity_image_by_community_id($community_row->id);
            $user_details =  $this->user_details_by_id($community_row->customer_id);
            $location = $this->get_state_name_by_id($user_details['state_id']);
            $comments_list = $this->get_community_comments_list($community_row->id);
            $details = array('id'=>$community_row->id, 'quesstion' => $community_row->quesstion, 'problem_description' => $community_row->problem_description, 'user_name' => $user_details['first_name'].' '.$user_details['last_name'], 'user_id' => $user_details['id'], 'location' => $location, 'image' => $image_list, 'comments_list' => $comments_list);

        }
       return $details;
    }

    function get_community_comments_list($community_id = 0)
    {
        $list = array();

        $this->db->select('commu.*,cus.first_name,cus.last_name');
        $this->db->from('FM_customer AS cus');
        $this->db->join('FM_community_comments commu','cus.id = commu.customer_id', 'left');
        $this->db->where('cus.status', 'Y');
        $this->db->where('commu.community_id', $community_id);
        $this->db->order_by("id", "DESC");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $var_row)
            {
                $list [] = array('name' => $var_row->first_name.' '.$var_row->last_name, 'comments' => $var_row->comments, 'comment_time' => $var_row->created_date, "comments_id" => $var_row->id, 'image' => (!empty($var_row->image))?FRONT_URL.$var_row->image:$var_row->image);
            }
        }
        return $list;
    }

    function get_cummunity_image_by_community_id($community_id = 0)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_community_image");
        $this->db->where("community_id", $community_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array("id" => $row->id, "image" => FRONT_URL.$row->image);
            }
        }

        return $list;
    }

    function user_details_by_id($user_id = 0)
    {
        $details = array();

        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where("id", $user_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
          $row = $query->row();
          $details = array("id" => $row->id, "first_name" => $row->first_name, "last_name" => $row->last_name, "full_name" => trim($row->first_name." ".$row->last_name), "email" => $row->email, "phone" => $row->phone,"language" => $row->language, "profile_image" => FRONT_URL.$row->profile_image, "status" => $row->status, "registration_date" => $row->created_date,'state_id' => $row->state_id);
        }

        return $details;
    }

    function comments_count_by_community_id($community_id = 0)
    {
        $count = 0;

        $this->db->select("COUNT(id) as comments_count");
        $this->db->from("FM_community_comments");
        $this->db->where("community_id", $community_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $count = $query->row()->comments_count;
        }

        return $count;
    }

    function delete_community_by_id($id = 0)
    {
        $this->db->where("id", $id);
        $this->db->delete("FM_ask_community");
        $response = array("status" => "Y", "message" => "Community successfully deleted.");
        return $response;
    }

    function delete_community_comments_by_id($id = 0)
    {
        $this->db->where("id", $id);
        $this->db->delete("FM_community_comments");
        $response = array("status" => "Y", "message" => "Community Comments successfully deleted.");
        return $response;
    }

    public function get_merchant_commssion_all()
    {
        return $this->db->query("SELECT FM_product_variation.id, FM_product.title as product_name, FM_product_variation.title as variation_name, IFNULL(FM_product_variation.merchant_commission,0) as commission, FM_state_lookup.state FROM `FM_product_variation` INNER JOIN FM_product ON FM_product_variation.product_id = FM_product.id INNER JOIN FM_state_lookup ON FM_product_variation.state_id = FM_state_lookup.id WHERE FM_product_variation.status = 'Y'")->result();
    }

}

?>
