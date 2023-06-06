<?php defined("BASEPATH") OR exit("No direct script access allowed.");

class Khata_management_model extends CI_Model {

    public function get_list_of_users_khata($limit = 100, $offset = 0) {
        $SQL = "SELECT C.id, CONCAT(C.first_name, ' ', C.last_name) AS name FROM FM_customer C WHERE C.status = 'Y' AND (C.type = 'U' OR C.type = 'user' OR C.type IS NULL) LIMIT $limit OFFSET $offset";
        $list_of_users = $this->db->query($SQL)->result();
        
        $list_of_users_khata = [];
        foreach ($list_of_users as $i => $user_details) {
            $user_khata_details = new stdClass();

            $crop_sales_SQL = "SELECT SUM(CS.sale_value) AS total_crop_sales FROM FM_customer_crop_sales CS WHERE CS.customer_id = '".$user_details->id."'";
            $crop_sales_details = $this->db->query($crop_sales_SQL)->row();
            $total_crop_sales = (!empty($crop_sales_details->total_crop_sales)) ? intval($crop_sales_details->total_crop_sales) : 0;

            $other_incomes_SQL = "SELECT SUM(OI.amount) AS total_other_incomes FROM FM_customer_other_incomes OI WHERE OI.customer_id = '".$user_details->id."'";
            $other_incomes_details = $this->db->query($other_incomes_SQL)->row();
            $total_other_incomes = (!empty($other_incomes_details->total_other_incomes)) ? intval($other_incomes_details->total_other_incomes) : 0;

            $expenses_SQL = "SELECT SUM(E.amount) AS total_expenses FROM FM_customer_expenses E WHERE E.customer_id = '".$user_details->id."'";
            $expenses_details = $this->db->query($expenses_SQL)->row();
            $total_expenses = (!empty($expenses_details->total_expenses)) ? intval($expenses_details->total_expenses) : 0;

            $total_incomes = $total_crop_sales + $total_other_incomes;
            $total_profits = intval($total_incomes - $total_expenses);

            $user_khata_details->id = $user_details->id;
            $user_khata_details->name = $user_details->name;
            $user_khata_details->total_incomes = $total_incomes;
            $user_khata_details->total_expenses = $total_expenses;
            $user_khata_details->total_profits = ($total_profits > 0) ? $total_profits : 0;

            $list_of_users_khata[] = $user_khata_details;
        }

        return $list_of_users_khata;
    }

}