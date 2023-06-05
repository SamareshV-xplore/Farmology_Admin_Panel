<?php defined("BASEPATH") OR exit("No direct script access allowed");

class New_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_date = date("Y-m-d");
    }

    private function format_currency($amount)
    {
        $formatter = new NumberFormatter('en_IN',  NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, 'INR');
    }

    public function get_total_farmers_count() {
        $total_farmers_count = 0;
        $sql = "SELECT COUNT(*) AS count FROM FM_customer WHERE status = 'Y'";
        $total_farmers = $this->db->query($sql)->row();
        if (!empty($total_farmers->count)) {
            $total_farmers_count = $total_farmers->count;
        }
        return $total_farmers_count;
    }

    public function get_total_farmers_count_for_today() {
        $total_farmers_count_for_today = 0;
        $sql = "SELECT COUNT(*) AS count FROM FM_customer WHERE status = 'Y' AND created_date LIKE '%".$this->current_date."%'";
        $total_farmers = $this->db->query($sql)->row();
        if (!empty($total_farmers->count)) {
            $total_farmers_count_for_today = $total_farmers->count;
        }
        return $total_farmers_count_for_today;
    }

    public function get_total_orders_count() {
        $total_orders_count = 0;
        $sql = "SELECT COUNT(*) AS count FROM FM_order WHERE status NOT LIKE '%ONP%' AND status NOT LIKE '%C%'";
        $total_orders = $this->db->query($sql)->row();
        if (!empty($total_orders->count)) {
            $total_orders_count = $total_orders->count;
        }
        return $total_orders_count;
    }

    public function get_total_orders_count_for_today() {
        $total_orders_count_for_today = 0;
        $sql = "SELECT COUNT(*) AS count FROM FM_order WHERE status NOT LIKE '%ONP%' AND status NOT LIKE '%C%' AND created_date LIKE '%".$this->current_date."%'";
        $total_orders = $this->db->query($sql)->row();
        if (!empty($total_orders->count)) {
            $total_orders_count_for_today = $total_orders->count;
        }
        return $total_orders_count_for_today;
    }

    public function get_total_orders_value() {
        $total_orders_value = 0;
        $sql = "SELECT SUM(order_total) AS value FROM FM_order WHERE status NOT LIKE '%ONP%' AND status NOT LIKE '%C%'";
        $total_orders = $this->db->query($sql)->row();
        if (!empty($total_orders->value)) {
            $total_orders_value = $this->format_currency($total_orders->value);
        }
        return $total_orders_value;
    }

    public function get_total_orders_value_for_today() {
        $total_orders_value_for_today = 0;
        $sql = "SELECT SUM(order_total) AS value FROM FM_order WHERE status NOT LIKE '%ONP%' AND status NOT LIKE '%C%' AND created_date LIKE '%".$this->current_date."%'";
        $total_orders = $this->db->query($sql)->row();
        if (!empty($total_orders->value)) {
            $total_orders_value_for_today = $this->format_currency($total_orders->value);
        }
        return $total_orders_value_for_today;
    }

    public function get_total_soil_test_request_count() {
        $total_soil_test_request = 0;
        $sql = "SELECT COUNT(*) AS total FROM FM_soil_health_test_requests WHERE status = 'A'";
        $soil_test_request = $this->db->query($sql)->row();
        if (!empty($soil_test_request->total)) {
            $total_soil_test_request = $soil_test_request->total;
        }
        return $total_soil_test_request;
    }

    public function get_total_soil_test_request_count_for_today() {
        $total_soil_test_request_for_today = 0;
        $sql = "SELECT COUNT(*) AS total FROM FM_soil_health_test_requests WHERE status = 'A' AND created_date LIKE '%".$this->current_date."%'";
        $soil_test_request = $this->db->query($sql)->row();
        if (!empty($soil_test_request->total)) {
            $total_soil_test_request_for_today = $soil_test_request->total;
        }
        return $total_soil_test_request_for_today;
    }

    public function get_soil_health_request_list() {
        $sql = "SELECT name, crop, created_date AS date FROM FM_soil_health_test_requests WHERE status = 'A' ORDER BY created_date DESC LIMIT 5";
        $soil_health_request_list = $this->db->query($sql)->result();
        return $soil_health_request_list;
    }

    public function get_new_customer_list() {
        $sql = "SELECT CA.name, CA.zip_code, CA.phone FROM FM_customer C INNER JOIN FM_customer_address CA ON CA.customer_id = C.id WHERE C.status = 'Y' ORDER BY C.created_date DESC LIMIT 5";
        $new_customer_list = $this->db->query($sql)->result();
        return $new_customer_list;
    }

    public function get_new_marchent_list() {
        $sql = "SELECT CA.name, CA.zip_code, CA.phone FROM FM_customer C INNER JOIN FM_customer_address CA ON CA.customer_id = C.id WHERE C.status = 'Y' AND type = 'M' ORDER BY C.created_date DESC LIMIT 5";
        $new_marchent_list = $this->db->query($sql)->result();
        return $new_marchent_list;
    }
    
    public function get_new_question_list() {
        $new_question_list = [];
        $sql = "SELECT id, title AS question FROM FM_questions WHERE status = 'A' ORDER BY created_date DESC LIMIT 5";
        $questions = $this->db->query($sql)->result();
        foreach ($questions as $question) {
            $question_data = [
                "question" => $question->question,
                "answered" => $this->check_question_answered($question->id)
            ];
            $new_question_list[] = $question_data;
        }
        return $new_question_list;
    }

    private function check_question_answered($id) {
        $question_answered = false;
        $condition = ["question_id" => $id, "is_deleted" => "N"];
        $answer_data = $this->db->get_where("FM_answers", $condition)->row();
        if (!empty($answer_data)) {
            $question_answered = true;
        }
        return $question_answered;
    }

    public function get_crop_health_request_list() {
        $sql = "SELECT CA.name, CR.title AS crop, R.request_date AS date FROM FM_report R INNER JOIN FM_new_farms F ON F.farm_id = R.farm_id INNER JOIN FM_crop CR ON CR.id = F.crop_id INNER JOIN FM_customer C ON C.id = R.user_id INNER JOIN FM_customer_address CA ON CA.customer_id = C.id WHERE R.status != 'D' ORDER BY R.request_date DESC LIMIT 5";
        $crop_health_request_list = $this->db->query($sql)->result();
        return $crop_health_request_list;
    }

    public function get_12_month_order_value_list() {
        $now = time();
        $order_value_list = [];
        for ($i=0; $i<12; $i++) {
            $order_value_data = new stdClass;
            
            if ($i == 0) {
                $time = $now;
            } else {
                $time = strtotime("- ".$i." months", $now);
            }

            $order_value_data->month = date("M Y", $time);
            $order_value_data->amount = $this->get_order_value_by_time($time);
            $order_value_list[] = $order_value_data;
        }
        return array_reverse($order_value_list);
    }

    private function get_order_value_by_time($time) {
        $date = date("Y-m-d", $time);
        $sql = "SELECT SUM(order_total) AS value FROM FM_order WHERE status = 'D' AND YEAR(created_date) = YEAR('".$date."') AND MONTH(created_date) = MONTH('".$date."')";
        $total_orders = $this->db->query($sql)->row();
        return (!empty($total_orders->value)) ? $total_orders->value : 0;
    }

}

?>