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
        
        $user_incomes_SQL = "SELECT CONCAT(C.first_name, ' ', C.last_name) as name,
                                    (SELECT SUM(CS.sale_value)
                                     FROM FM_customer_crop_sales AS CS
                                     WHERE CS.customer_id = C.id) as total_crop_sales,
                                    (SELECT SUM(OI.amount)
                                     FROM FM_customer_other_incomes AS OI
                                     WHERE OI.customer_id = C.id) as total_other_incomes
                             FROM FM_customer AS C 
                             WHERE C.status = 'Y' 
                                   AND C.id = '".$user_id."'";
        $user_income_details = $this->db->query($user_incomes_SQL)->row();
        if (!empty($user_income_details->name)) {
            $user_khata_summary->user_name = $user_income_details->name;
        }        
        if (!empty($user_income_details->total_crop_sales)) {
            $user_khata_summary->total_crop_sales = $user_income_details->total_crop_sales;
        }
        if (!empty($user_income_details->total_other_incomes)) {
            $user_khata_summary->total_other_incomes = $user_income_details->total_other_incomes;
        }

        $user_expenses_SQL_template = "SELECT SUM(E.amount) as total 
                                       FROM FM_customer_expenses AS E 
                                       WHERE E.expense_type = '%s'
                                             AND E.customer_id = %d";

        $user_product_expenses_SQL = sprintf($user_expenses_SQL_template, "product_related_expenses", $user_id);                               
        $user_product_expenses = $this->db->query($user_product_expenses_SQL)->row();
        if (!empty($user_product_expenses->total)) {
            $user_khata_summary->total_product_expenses = $user_product_expenses->total;
        }

        $user_farming_expenses_SQL = sprintf($user_expenses_SQL_template, "farming_related_expenses", $user_id);
        $user_farming_expenses = $this->db->query($user_farming_expenses_SQL)->row();
        if (!empty($user_farming_expenses->total)) {
            $user_khata_summary->total_farming_expenses = $user_farming_expenses->total;
        }

        $user_other_expenses_SQL = sprintf($user_expenses_SQL_template, "other_expenses", $user_id);
        $user_other_expenses = $this->db->query($user_other_expenses_SQL)->row();
        if (!empty($user_other_expenses->total)) {
            $user_khata_summary->total_other_expenses = $user_other_expenses->total;
        }
        
        return $user_khata_summary;
    }

    public function get_list_of_crop_sales($user_id) {
        $SQL = "SELECT CS.crop_sale_id as id,
                       CONCAT('".FRONT_URL."', '', C.image) as crop_image,
                       C.title as crop_name,
                       CS.total_produce,
                       CS.sale_value,
                       CS.date,
                       CS.reference
                FROM FM_customer_crop_sales AS CS
                INNER JOIN FM_crop AS C 
                           ON C.id = CS.crop_id
                WHERE CS.customer_id = ".$user_id."
                ORDER BY CS.date DESC";
        $list_of_crop_sales = $this->db->query($SQL)->result();
        return $list_of_crop_sales;
    }

    public function get_list_of_other_incomes($user_id) {
        $SQL = "SELECT OI.other_income_id as id,
                       OI.income_type,
                       OI.amount,
                       OI.date,
                       OI.reference
                FROM FM_customer_other_incomes AS OI
                WHERE OI.customer_id = ".$user_id."
                ORDER BY OI.date DESC";
        $list_of_other_incomes = $this->db->query($SQL)->result();
        return $list_of_other_incomes;
    }

    
    public function get_list_of_product_expenses($user_id) {
        $SQL = "SELECT E.expense_id as id,
                       EC.category_name,
                       E.product_type,
                       E.amount,
                       E.date,
                       E.reference
                FROM FM_customer_expenses AS E
                INNER JOIN FM_expenses_categories AS EC
                           ON EC.category_id = E.expense_category_id
                WHERE E.customer_id = ".$user_id."
                      AND E.expense_type = 'product_related_expenses'
                ORDER BY E.date DESC";
        $list_of_product_expenses = $this->db->query($SQL)->result();
        return $list_of_product_expenses;
    }

    public function get_list_of_farming_expenses($user_id) {
        $SQL = "SELECT E.expense_id as id,
                       EC.category_name,
                       E.amount,
                       E.date,
                       E.reference
                FROM FM_customer_expenses AS E
                INNER JOIN FM_expenses_categories AS EC
                           ON EC.category_id = E.expense_category_id
                WHERE E.customer_id = ".$user_id."
                      AND E.expense_type = 'farming_related_expenses'
                ORDER BY E.date DESC";
        $list_of_farming_expenses = $this->db->query($SQL)->result();
        return $list_of_farming_expenses;
    }
    
    public function get_list_of_other_expenses($user_id) {
        $SQL = "SELECT E.expense_id as id,
                       E.expense_name,
                       E.amount,
                       E.date,
                       E.reference
                FROM FM_customer_expenses AS E
                WHERE E.customer_id = ".$user_id."
                      AND E.expense_type = 'other_expenses'
                ORDER BY E.date DESC";
        $list_of_other_expenses = $this->db->query($SQL)->result();
        return $list_of_other_expenses;
    }

}