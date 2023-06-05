<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once (APPPATH."controllers/Push_notification.php");

class Send_schedule_push_notification extends Push_notification {

    public function __construct ()
    {
        parent::__construct();
    }
    
    public function index ()
    {
        $notifications_list = $this->get_notifications_list();
        foreach ($notifications_list as $notification)
        {
            $data = [];
            
            // All User Schedule Push Notification Code
            // ========================================
            $all_user_tokens = $this->get_all_user_device_token_by_state($notification["target_state"]);
            $data["subject"] = $notification["title"];
            $data["body"] = $notification["description"];
            $data["image"] = FRONT_URL.$notification["image"];
            $data["action"] = $notification["redirect_to"];
            $data["redirection_id"] = "2";
            $data["new_user_tokens"] = $all_user_tokens["new_user"];
            $data["old_user_tokens"] = $all_user_tokens["old_user"];


            // Testing User Schedule Push Notification Code
            // ============================================
            // $data["subject"] = $notification["title"];
            // $data["body"] = $notification["description"];
            // $data["image"] = FRONT_URL.$notification["image"];
            // $data["action"] = $notification["redirect_to"];
            // $data["redirection_id"] = "2";
            // $data["new_user_tokens"][0] = $this->get_testing_users_device_tokens();
            
            $this->send_schedule_push_notification($data);
        }
        $this->mark_notifications_list_completed();
    }

    public function get_notifications_list ()
    {
        $current_date = date("Y-m-d");
        $current_hour = date("H");
        $last_hour = date("H", strtotime("-1 hour", time()));
        $second_last_hour =  date("H", strtotime("-2 hour", time()));
        $third_last_hour =  date("H", strtotime("-3 hour", time()));
        $coverage_hours = $current_hour.",".$last_hour.",".$second_last_hour.",".$third_last_hour;
        $SQL = "SELECT * FROM FM_schedule_push_notifications WHERE status = 'P' AND DATE(send_date) = '".$current_date."' AND HOUR(send_date) IN (".$current_hour.")";
        $notifications_list = $this->db->query($SQL)->result_array();
        return $notifications_list;
    }

    public function mark_notifications_list_completed ()
    {
        $current_date = date("Y-m-d");
        $current_hour = date("H");
        $last_hour = date("H", strtotime("-1 hour", time()));
        $second_last_hour =  date("H", strtotime("-2 hour", time()));
        $third_last_hour =  date("H", strtotime("-3 hour", time()));
        $coverage_hours = $current_hour.",".$last_hour.",".$second_last_hour.",".$third_last_hour;
        $SQL = "SELECT * FROM FM_schedule_push_notifications WHERE status = 'P' AND DATE(send_date) = '".$current_date."' AND HOUR(send_date) IN (".$current_hour.")";
        $notifications_list = $this->db->query($SQL)->result_array();
        foreach ($notifications_list as $notification)
        {
            $this->db->set(["status" => "C"]);
            $this->db->where(["hash_id" => $notification["hash_id"]]);
            $this->db->update("FM_schedule_push_notifications");
        }
    }

    private function get_all_user_device_token_by_state ($state)
    {
        $new_user_details = $this->get_new_user_device_tokens_by_state($state);
        $old_user_details = $this->get_old_user_device_tokens_by_state($state);

        $new_user_tokens = $this->render_user_token_array($new_user_details);
        $old_user_tokens = $this->render_user_token_array($old_user_details);

        return ["new_user" => $new_user_tokens, "old_user" => $old_user_tokens];
    }

    private function render_user_token_array ($user_details)
    {
        $user_token_array = [];
        $total_users = count($user_details);
        $total_iteration = intval($total_users / 1000);
        $total_iteration += ($total_users % 1000 > 0) ? 1 : 0;

        for ($i=0; $i<$total_iteration; $i++)
        {
            $start = (($i == 0) ? $i : $i*1000);
            $end = $start + 1000;
            while(count($user_details) > 0)
            {
                $user_token_array[] = array_splice($user_details, $start, $end);
            }
        }
        return $user_token_array;
    }

    private function get_new_user_device_tokens_by_state ($state)
    {
        $list = array();

        if (!empty($state))
        {
            $sql = "SELECT FMCDD.device_token FROM FM_customer FMC INNER JOIN FM_customer_device_details FMCDD ON FMC.id = FMCDD.customer_id WHERE FMC.status = 'Y' AND FMC.state_id = ".$state." AND (FMCDD.app_version IS NOT NULL AND FMCDD.app_version >= 2) ORDER BY FMC.id DESC";
        }
        else
        {
            $sql = "SELECT FMCDD.device_token FROM FM_customer FMC INNER JOIN FM_customer_device_details FMCDD ON FMC.id = FMCDD.customer_id WHERE FMC.status = 'Y' AND (FMCDD.app_version IS NOT NULL AND FMCDD.app_version >= 2) ORDER BY FMC.id DESC";
        }
        
        $query = $this->db->query($sql);
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = $row->device_token;
            }
        }
        return $list;
    }

    private function get_old_user_device_tokens_by_state ($state)
    {
        $list = array();

        if (!empty($state))
        {
            $sql = "SELECT FMCDD.device_token FROM FM_customer FMC INNER JOIN FM_customer_device_details FMCDD ON FMC.id = FMCDD.customer_id WHERE FMC.status = 'Y' AND FMC.state_id = ".$state." AND (FMCDD.app_version IS NULL OR FMCDD.app_version = 0 OR FMCDD.app_version = '') ORDER BY FMC.id DESC";
        }
        else
        {
            $sql = "SELECT FMCDD.device_token FROM FM_customer FMC INNER JOIN FM_customer_device_details FMCDD ON FMC.id = FMCDD.customer_id WHERE FMC.status = 'Y' AND (FMCDD.app_version IS NULL OR FMCDD.app_version = 0 OR FMCDD.app_version = '') ORDER BY FMC.id DESC";
        }
        
        $query = $this->db->query($sql);
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = $row->device_token;
            }
        }
        return $list;
    }

    private function get_testing_users_device_tokens ()
    {
        $list  = [];
        $testing_users_ids = "3159,3517,3599";
        $sql = "SELECT FMCDD.device_token FROM FM_customer FMC INNER JOIN FM_customer_device_details FMCDD ON FMC.id = FMCDD.customer_id WHERE FMC.status = 'Y' AND FMC.id IN (".$testing_users_ids.") AND (FMCDD.app_version IS NOT NULL AND FMCDD.app_version >= 2) ORDER BY FMC.id DESC";
        $query = $this->db->query($sql);
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $list[] = $row->device_token;
            }
        }
        return $list;
    }

}

?>