<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

    //Get users list
    function users_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_customer");
        if($filter_data['status'] == 'Y')
        {
            $this->db->where("status", "Y");
        }
        elseif($filter_data['status'] == 'N')
        {
            $this->db->where("status", "N");
        }
        else
        {
            // no status check
        }
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "first_name" => $row->first_name,
                    "last_name" => $row->last_name,
                    "email" => $row->email,
                    "phone" => $row->phone,
                    "profile_image" => $row->profile_image,
                    "status" => $row->status,
                    "created_date" => $row->created_date,
                    "updated_date" => $row->updated_date,
                );
            }
        }
        return $list;
    }

    // get users contacts list
    function get_users_contacts()
    {
        $SQL = "SELECT TRIM(CONCAT(first_name, ' ', last_name)) AS name, phone, email FROM `FM_customer` WHERE (first_name !='' || last_name !='') AND phone != ''";
        return $this->db->query($SQL)->result_array();
    }

    //Add banner data
    function add_banner($data)
    {
        $title = $data['title'];
        $description = $data['description'];
        $link = $data['link'];
        $status = $data['status'];
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array("title" =>  $title, "description" => $description, "link" => $link, "status" => $status, "created_date" => $created_date);
        $this->db->insert("FM_customer", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New banner created", "id" => $id);

        return $response;

    }
    // Update image data
    function update_image($id, $image, $update_type)
    {
        if($update_type == 'first')
        {
            $update_data = array("image" => $image);
        }
        else
        {
            $update_data = array("profile_image" => $image, "updated_date" => date("Y-m-d H:i:s"));
        }

        $this->db->where("id", $id);
        $this->db->update("FM_customer", $update_data);
        return true;

    }

    function update_user($data)
    {
        $id = $data['user_id'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];

        if (isset($data["email"]))
        {
            $email = $data['email'];
        }

        if (isset($data["state_id"]))
        {
            $state_id = $data["state_id"];
        }
        else
        {
            $state_id = "";
        }

        if (isset($data["area_value"]))
        {
            $area_value = $data["area_value"];
        }
        else
        {
            $area_value = "";
        }

        if (isset($data["area_unit"]))
        {
            $area_unit = $data["area_unit"];
        }
        else
        {
            $area_unit = "";
        }

        if (isset($data["language"]))
        {
            $language = $data["language"];
        }
        else
        {
            $language = "";
        }
        
        $phone = $data['phone'];
        $status = $data['status'];
        $modified_date = date("Y-m-d H:i:s");
        $referral_by = $this->getIdFromReferralCode($data['referral_by']);

        // before update banner check banner ID
        $this->db->select("id");
        $this->db->from("FM_customer");
        $this->db->where("id", $id);
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Maybe user already deleted.");
        }
        else
        {
            if (isset($data["email"]))
            {
                $emailEesponse = $this->check_user_email($data['email'], $data['user_id']);
            }
            else
            {
                $emailEesponse = "N";
            }
            
            $phoneEesponse = $this->check_user_contact($data['phone'], $data['user_id']);

            if($emailEesponse == 'N'){
                if($phoneEesponse == "N"){
                    if (isset($data["email"]))
                    {
                        $update_data = array("first_name" =>  $first_name, "last_name" => $last_name, "email" => $email, "phone" => $phone, "state_id" => $state_id, "status" => $status, "referral_by" => $referral_by, "language" => $language, "registered_with_referral_code" => $data['referral_by'], "updated_date" => $modified_date, "area_value" => $area_value, "area_unit" => $area_unit);
                        
                    }
                    else
                    {
                        $update_data = array("first_name" =>  $first_name, "last_name" => $last_name, "phone" => $phone, "state_id" => $state_id, "status" => $status, "referral_by" => $referral_by, "language" => $language, "registered_with_referral_code" => $data['referral_by'], "updated_date" => $modified_date, "area_value" => $area_value, "area_unit" => $area_unit);
                    }
                    $this->db->where("id", $id);
                    $this->db->update("FM_customer", $update_data);
                    $response = array("status" => "Y", "message" => "User Details updated.");
                }else{
                    $response = array("status" => "N", "message" => "This contact number is already exists with another user.");
                }
            }else{
                $response = array("status" => "N", "message" => "Email already exists.");
            }
        }
        return $response;
    }

    function update_merchant($id, $data)
    {
        $this->db->select("id");
        $this->db->from("FM_customer");
        $this->db->where("id", $id);
        $emp_check_query = $this->db->get();
        if($emp_check_query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Maybe user already deleted.");
        }
        else
        {
            if (isset($data["email"]))
            {
                $emailEesponse = $this->check_user_email($data['email'], $id);
            }
            else
            {
                $emailEesponse = "N";
            }
            
            $phoneEesponse = $this->check_user_contact($data['phone'], $id);

            if($emailEesponse == 'N'){
                if($phoneEesponse == "N"){
                    $this->db->set($data);
                    $this->db->where("id", $id);
                    $this->db->update("FM_customer");
                    $response = array("status" => "Y", "message" => "Merchant Details Updated Successfully.");
                }else{
                    $response = array("status" => "N", "message" => "Phone Number already exists!");
                }
            }else{
                $response = array("status" => "N", "message" => "Email already exists!");
            }
        }
        return $response;
    }

    function update_user_address ($data)
    {
        if (isset($data["customer_id"]))
        {
            $condition = ["customer_id" => $data["customer_id"]];
            $prev_customer_address = $this->db->get_where("FM_customer_address", $condition)->row();
            if (isset($prev_customer_address))
            {
                unset($data["customer_id"]);
                $this->db->set($data);
                $this->db->where($condition);
                $this->db->update("FM_customer_address");
            }
            else
            {
                $this->db->insert("FM_customer_address", $data);
            }
        }

        return $this->db->affected_rows();
    }

    function update_user_selected_crops ($data)
    {
        if (isset($data["customer_id"]))
        {   
            $customer_id = $data["customer_id"];
            $selected_crops = $data["selected_crops"];

            $this->db->where("customer_id", $customer_id);
            $this->db->delete("FM_customer_crop_mapping");

            for ($i=0; $i<count($selected_crops); $i++)
            {
                $data = ["customer_id" => $customer_id, "crop_id" => $selected_crops[$i]];
                $is_exist = $this->db->get_where("FM_customer_crop_mapping", $data)->row();
                if (!isset($is_exist))
                {
                    $this->db->insert("FM_customer_crop_mapping", $data);
                }
            }
        }

        return $this->db->affected_rows();
    }

    public function getIdFromReferralCode($referral_code)
    {
        return $this->db->select('id')->from('FM_customer')->where('owned_referral_code', $referral_code)->get()->row()->id;
    }
    
    // Get single user details
    function single_user_details($id)
    {
        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $details = array(
                "id"            => $row->id,
                "first_name"    => $row->first_name,
                "last_name"     => $row->last_name,
                "email"         => $row->email,
                "phone"         => $row->phone,
                "bank_name"     => $row->bank_name,
                "holder_name"   => $row->holder_name,
                "bank_account_no" => $row->bank_account_no,
                "ifsc_code"     => $row->ifsc_code,
                "address"       => $this->get_address_by_id($id),
                "area_value"    => $row->area_value,
                "area_unit"     => $row->area_unit,
                "language"      => $row->language,
                "selected_crop" => $this->get_selected_crops_by_id($id),
                "profile_image" => $row->profile_image,
                "status"        => $row->status,
                "created_date"  => $row->created_date,
                "updated_date"  => $row->updated_date,
                "referred_by"   => ($row->referral_by != NULL) ? $this->getReferralCodeByUserId($row->referral_by) : '',
                "kycDocs"       => $this->getKycDocsOfUsers($id)
            );

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found. Maybe user is already deleted.");
        }
        return $response;
    }

    // Get user address by id
    function get_address_by_id ($id)
    {   
        $address = new stdClass;
        $condition = ["is_deleted" => "N", "customer_id" => $id];
        $user_address_data = $this->db->get_where("FM_customer_address", $condition)->row();
        if (isset($user_address_data))
        {
            $state_details = $this->get_state_by_id($user_address_data->state_id);
            $city_details = $this->get_city_by_id($user_address_data->city_id);
            if (isset($state_details) && isset($city_details))
            {
                $state = ucwords(strtolower($state_details->state));
                $city = ucwords(strtolower($city_details->name));
            }
            else
            {
                $state = "";
                $city = "";
            }
            
            $address->address_1 = $user_address_data->address_1;
            $address->address_2 = $user_address_data->address_2;
            $address->landmark = $user_address_data->landmark;
            $address->state_id = $user_address_data->state_id;
            $address->state = $state;
            $address->city_id = $user_address_data->city_id;
            $address->city = $city;
            $address->zipcode = $user_address_data->zip_code;
        }

        return $address;
    }

    // Get user selected crops by id
    function get_selected_crops_by_id ($id)
    {
        $crops = [];
        $condition = ["customer_id" => $id];
        $selected_crops = $this->db->get_where("FM_customer_crop_mapping", $condition)->result();
        if (isset($selected_crops) && !empty($selected_crops))
        {
            foreach ($selected_crops as $selected_crop)
            {
                $crops[] = $selected_crop->crop_id;
            }
        }

        return $crops;
    }

    // Get state by id
    function get_state_by_id ($id)
    {
        $condition = ["id" => $id];
        $state_data = $this->db->get_where("FM_state_lookup", $condition)->row();
        return $state_data;
    }

    // Get city by id
    function get_city_by_id ($id)
    {
        $condition = ["status" => "Y", "id" => $id];
        $city_data = $this->db->get_where("FM_city_lookup", $condition)->row();
        return $city_data;
    }

    public function getReferralCodeByUserId($referral_by='')
    {
        $data = $this->db->select('owned_referral_code')->from('FM_customer')->where('id', $referral_by)->get()->row();
        if (is_object($data)) {
            return $data->owned_referral_code;
        }
        else{
            return '';
        }
    }

    // Get single user details
    function check_user_email($email, $id)
    {
        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where('id !=', $id);
        $this->db->where('status !=', 'D');
        $this->db->where("email", $email);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $response = "E";
        }
        else
        {
            $response = "N";
        }
        return $response;
    }

    // Get single user details
    function check_user_contact($contact, $id)
    {
        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where('id !=', $id);
        $this->db->where('status !=', 'D');
        $this->db->where("phone", $contact);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $response = "P";
        }
        else
        {
            $response = "N";
        }
        return $response;
    }

    // Get single user details
    function check_user_mobile($mobile_number)
    {
        $this->db->select("*");
        $this->db->from("FM_customer");
        $this->db->where('status !=', 'D');
        $this->db->where("phone", $mobile_number);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $details = array(
                "id" => $row->id,
                "phone" => $row->phone
            );
            $response = array("status" => "Y", "message" => "User available", "details" => $details);
        }
        else
        {
            $response = array("status" => "N", "message" => "User not found.");
        }
        return $response;
    }

    // User Delete
    function delete_user_by_id ($id)
    {
        $this->db->select("id");
        $this->db->from("FM_customer");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $this->db->where("id", $id);
            $this->db->delete("FM_customer");
            $response = array("status" => "Y", "message" => "User successfully deleted.");
        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid User ID or user already deleted.");
        }
        return $response;
    }

    public function merchant_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_customer");
        if($filter_data['status'] == 'Y')
        {
            $this->db->where("status", "Y");
        }
        elseif($filter_data['status'] == 'N')
        {
            $this->db->where("status", "N");
        }
        else
        {
            // no status check
        }
        $this->db->where('type', 'M');
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = array(
                    "id" => $row->id,
                    "first_name" => $row->first_name,
                    "last_name" => $row->last_name,
                    "email" => $row->email,
                    "phone" => $row->phone,
                    "profile_image" => $row->profile_image,
                    "status" => $row->status,
                    "created_date" => $row->created_date,
                    "updated_date" => $row->updated_date,
                    "owned_referral_code" => $row->owned_referral_code
                );
            }
        }
        return $list;
    }

    public function getKycDocsOfUsers($user_id)
    {
        $ret = [];
        $voter_card = [];
        $aadhar_card = [];
        $land_document = [];
        $data = $this->db->from('FM_kyc_documents')->where('user_id', $user_id)->get()->result();
        foreach ($data as $d) {
            if ($d->document_type == 'voter card') {
                $voter_card[] = [
                    'type' => $d->document_type,
                    'image' => FRONT_URL.$d->image
                ];   
            }
            if ($d->document_type == 'aadhar card') {
                $aadhar_card[] = [
                    'type' => $d->document_type,
                    'image' => FRONT_URL.$d->image
                ];   
            }
            if ($d->document_type == 'land document') {
                $land_document[] = [
                    'type' => $d->document_type,
                    'image' => FRONT_URL.$d->image
                ];   
            }
        }
        $ret['voter_card'] = $voter_card;
        $ret['aadhar_card'] = $aadhar_card;
        $ret['land_document'] = $land_document;
        return $ret;
    }

    function get_list_of_states ()
    {   
        $condition = ["is_deleted" => "Y"];
        $states = $this->db->get_where("FM_state_lookup", $condition)->result();
        return $states;
    }

    function get_list_of_cities ()
    {   
        $condition = ["status" => "Y"];
        $states = $this->db->get_where("FM_city_lookup", $condition)->result();
        return $states;
    }

    function get_list_of_languages ()
    {   
        $condition = ["status" => "A"];
        $languages = $this->db->get_where("FM_languages", $condition)->result();
        return $languages;
    }

    function get_list_of_crops ()
    {
        $condition = ["status" => "Y"];
        $crops = $this->db->get_where("FM_crop", $condition)->result();
        return $crops;
    }

}
