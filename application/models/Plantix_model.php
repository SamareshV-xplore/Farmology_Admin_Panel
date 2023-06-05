<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Plantix_model extends CI_Model {

    public function get_all_plant_diagnosis_requests ()
    {
        return $this->db->get_where("FM_plant_diagnosis_requests", ["status" => "A"])->result();
    }
    
    public function get_all_plant_diagnosis_product_recommendations ()
    {
        $data_array = [];
        $result = $this->db->get_where("FM_plant_diagnosis_product_recommendations", ["status" => "A"])->result();
        foreach ($result as $row)
        {
            $data = $row;
            if (!empty($row->recommended_products))
            {
                $data->recommended_product_names = $this->get_recommended_product_names_by_ids($row->recommended_products);
            }
            $data_array[] = $data;
        }
        return $data_array;
    }

    public function get_recommended_product_names_by_ids ($ids)
    {
        $product_names = [];
        $list_of_ids = explode(",", $ids);
        foreach ($list_of_ids as $id)
        {
            $row = $this->db->get_where("FM_product", ["id" => $id])->row();
            if (!empty($row->title))
            {
                $product_names[] = $row->title;
            }
        }
        return implode(", ", $product_names);
    }

    public function edit_plant_diagnosis_product_recommendations ($hash_id, $recommended_products)
    {
        $condition = ["hash_id" => $hash_id];
        $update_data = ["recommended_products" => $recommended_products];
        $this->db->set($update_data)->where($condition)->update("FM_plant_diagnosis_product_recommendations");
        return $this->db->affected_rows();
    }

    public function get_products_by_product_ids ($product_ids)
    {
        $products = [];
        $ids_list = explode(",", $product_ids);
        foreach ($ids_list as $id)
        {
            $sql = "SELECT FM_product.id, FM_product.title, FM_product_image.image FROM FM_product INNER JOIN FM_product_image ON FM_product.id = FM_product_image.product_id WHERE FM_product.id = $id";
            $product = $this->db->query($sql)->row();
            if (is_object($product))
            {
                $product->image = FRONT_URL.$product->image;
            }
            $products[] = $product;
        }
        return $products;
    }

}

?>