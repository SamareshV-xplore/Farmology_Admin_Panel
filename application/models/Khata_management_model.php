<?php defined("BASEPATH") OR exit("No direct script access allowed.");

class Khata_management_model extends CI_Model {

    public function get_list_of_users_khata($limit = 100, $offset = 0) {
        $SQL = "SELECT C.id,
                       CONCAT(C.first_name, ' ', C.last_name) as name,
                    
                       (SELECT SUM(CS.sale_value) 
                        FROM FM_customer_crop_sales AS CS
                        WHERE CS.customer_id = C.id) as total_crop_sales,
                    
                       (SELECT SUM(OI.amount) 
                        FROM FM_customer_other_incomes AS OI
                        WHERE OI.customer_id = C.id) as total_other_incomes,

                       (SELECT SUM(E.amount)
                        FROM FM_customer_expenses AS E
                        WHERE E.customer_id = C.id) as total_expenses

                FROM FM_customer AS C
                WHERE C.status = 'Y' 
                    AND (C.type = 'U'
                         OR C.type = 'user'
                         OR C.type IS NULL)
                        
                    AND (((SELECT SUM(CS.sale_value) as total_crop_sales
                           FROM FM_customer_crop_sales AS CS
                           WHERE CS.customer_id = C.id) > 0)
                            
                    OR  ((SELECT SUM(OI.amount) as total_other_incomes
                          FROM FM_customer_other_incomes AS OI
                          WHERE OI.customer_id = C.id) > 0)
                        
                    OR ((SELECT SUM(E.amount) as total_expenses
                         FROM FM_customer_expenses AS E
                         WHERE E.customer_id = C.id) > 0))

                LIMIT $limit OFFSET $offset";

        $list_of_users = $this->db->query($SQL)->result();
        
        $list_of_users_khata = [];
        foreach ($list_of_users as $i => $user_details) {
            $user_khata_details = new stdClass();

            $total_crop_sales = (!empty($user_details->total_crop_sales)) ? $user_details->total_crop_sales : 0;
            $total_other_incomes = (!empty($user_details->total_other_incomes)) ? $user_details->total_other_incomes : 0;
            $total_expenses = (!empty($user_details->total_expenses)) ? $user_details->total_expenses : 0;

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

    public function get_user_khata_summary($user_id) {
        $user_khata_summary = new stdClass();
    }

}