<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_model extends CI_Model
{

	function check_slug_exist($slug, $product_id = 0)
    {
        $this->db->select("id");
        $this->db->from("FM_product");
        $this->db->where("slug", $slug);
        $this->db->where("status !=", "D");
        if($product_id > 0)
        {
            $this->db->where("id !=", $product_id);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            // exist / not avilable
            $status = "Y";
        }
        else
        {
            // avilable
            $status = "N";
        }
        return $status;
    }

    function add_product($data)
    {
        $category_id = $data['category_id'];
        $crop_id = $data['crop_id'];
        $title = $data['title'];
        $slug = $data['slug'];
        $description = $data['description'];
        $short_description = $data['short_description'];
        $status = $data['status'];
        $image = $data['image'];
        $variation_title = $data['variation_title'];
        $price = $data['price'];
        $discount = $data['discount'];
        $state_id = $data['state_id'];
        $ai_title = $data['ai_title'];
        $ai_value = $data['ai_value'];

        // check slug
        $slug_status = $this->check_slug_exist($slug, 0);

        if($slug_status == 'N')
        {
            $product_data = array("SKU" => "P".time(), "slug" => $slug, "title" => $title, "short_description" => $short_description, "description" => $description, "status" => $status, "created_date" => date("Y-m-d H:i:s"));
            $this->db->insert("FM_product", $product_data);
            $product_id = $this->db->insert_id();

            if($product_id > 0)
            {

                // insert image
                $img_insert_data = array("product_id" => $product_id, "image" => $image, "created_date" => date("Y-m-d H:i:s"));
                $this->db->insert("FM_product_image", $img_insert_data);


                $crop_count = count($crop_id);
                for($cr=0;$cr < $crop_count; $cr++){
                    $var_crop_id = $crop_id[$cr];
                    $var_insert_crop_data = array("product_id" => $product_id, "crop_id" => $var_crop_id, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_crop_mapping", $var_insert_crop_data);
                }
                $category_count = count($category_id);
                for($cat=0;$cat < $category_count; $cat++){
                    $var_cat_id = $category_id[$cat];
                    $var_insert_cat_data = array("product_id" => $product_id, "category_id" => $var_cat_id, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_category_mapping", $var_insert_cat_data);
                }

                $variation_count = count($variation_title);
                for($i = 0; $i < $variation_count; $i++)
                {
                    $var_title = $variation_title[$i];
                    $var_price = $price[$i];
                    $var_discount = $discount[$i];
                    $var_state_id = $state_id[$i];

                    $var_insert_data = array("product_id" => $product_id, "title" => $var_title, "price" => $var_price, "discount" =>  $var_discount, "state_id" =>  $var_state_id, "created_date" => date("Y-m-d H:i:s"), "status" => "Y", "ord_by" => 0);
                    $this->db->insert("FM_product_variation", $var_insert_data);
                }

                $ai_count = count($ai_title);
                for($ai = 0; $ai < $ai_count; $ai++)
                {
                    $ai_title_str = $ai_title[$ai];
                    $ai_value_str = $ai_value[$ai];

                    $ai_data = array("product_id" => $product_id, "info_key" => $ai_title_str, "info_value" => $ai_value_str, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_product_additional_information", $ai_data);

                }

                $response = array("status" => "Y", "message" => "New product successfully created.", "product_id" => $product_id);
            }
            else
            {
                $response = array("status" => "N", "message" => "Internal server error.");
            }

        }
        else
        {
            $response = array("status" => "N", "message" => "Product creation failed! Product slug already exist.");
        }

        return $response;


    }

    function update_product($data)
    {
        $id = $data['id'];
    	$category_id = $data['category_id'];
        $crop_id = $data['crop_id'];
    	$title = $data['title'];
    	$slug = $data['slug'];
    	$description = $data['description'];
    	$short_description = $data['short_description'];
    	$status = $data['status'];
    	$image = $data['image'];
        $variation_id = $data['variation_id'];
        $variation_type = $data['variation_type'];
    	$variation_title = $data['variation_title'];
    	$price = $data['price'];
    	$discount = $data['discount'];
        $state_id = $data['state_id'];
    	$ai_title = $data['ai_title'];
    	$ai_value = $data['ai_value'];

    	// check slug
    	$slug_status = $this->check_slug_exist($slug, $id);

    	if($slug_status == 'N')
    	{
    		$product_data = array("slug" => $slug, "title" => $title, "short_description" => $short_description, "description" => $description, "status" => $status, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
    		$this->db->update("FM_product", $product_data);
            $product_id = $id;
    		if($product_id > 0)
    		{
                if($image != '')
                {
                    // delete image
                    $this->db->where("product_id", $product_id);
                    $this->db->delete("FM_product_image");

                    // insert image
                    $img_insert_data = array("product_id" => $product_id, "image" => $image, "created_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_product_image", $img_insert_data);
                }  			


                $this->db->where("product_id", $product_id);
                $this->db->delete("FM_crop_mapping");
                $this->db->where("product_id", $product_id);
                $this->db->delete("FM_category_mapping");

                $crop_count = count($crop_id);
                for($cr=0;$cr < $crop_count; $cr++){
                    $var_crop_id = $crop_id[$cr];
                    $var_insert_crop_data = array("product_id" => $product_id, "crop_id" => $var_crop_id, "created_date" => date("Y-m-d H:i:s"),"updated_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_crop_mapping", $var_insert_crop_data);
                }
                
                $category_count = count($category_id);
                for($cat=0;$cat < $category_count; $cat++){
                    $var_cat_id = $category_id[$cat];
                    $var_insert_cat_data = array("product_id" => $product_id, "category_id" => $var_cat_id, "created_date" => date("Y-m-d H:i:s"),"updated_date" => date("Y-m-d H:i:s"));
                    $this->db->insert("FM_category_mapping", $var_insert_cat_data);
                }
                



    			$variation_count = count($variation_title);

                $old_var = array();
	    		for($i = 0; $i < $variation_count; $i++)
	    		{
	    			$var_id = $variation_id[$i];
                    $var_type = $variation_type[$i];
                    $var_title = $variation_title[$i];
	    			$var_price = $price[$i];
	    			$var_discount = $discount[$i];
                    $var_state_id = $state_id[$i];

                    if($var_type == 'old')
                    {
                        
                        $var_update_data = array("title" => $var_title, "price" => $var_price, "discount" =>  $var_discount, "state_id" =>  $var_state_id, "updated_date" => date("Y-m-d H:i:s"), "status" => "Y", "ord_by" => 0);
                        $this->db->where("id", $var_id);
                        $this->db->update("FM_product_variation", $var_update_data);
                        $old_var[] = $var_id;
                    }
                    else if($var_type == 'new')
                    {
                       
                        $var_insert_data = array("product_id" => $product_id, "title" => $var_title, "price" => $var_price, "discount" =>  $var_discount, "state_id" =>  $var_state_id, "created_date" => date("Y-m-d H:i:s"), "status" => "Y", "ord_by" => 0);
                        $this->db->insert("FM_product_variation", $var_insert_data);
                        $var_insert_id = $this->db->insert_id();
                        $old_var[] = $var_insert_id;
                    }
                    else
                    {
                        
                        // do nothing
                    }

                    if(count($old_var) > 0)
                    {
                        $this->db->where("product_id", $product_id);
                        $this->db->where_not_in("id", $old_var);
                        $var_update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
                        $this->db->update("FM_product_variation", $var_update_data);
                    }
                    else
                    {
                        $this->db->where("product_id", $product_id);
                        $var_update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
                        $this->db->update("FM_product_variation", $var_update_data);
                    }

	    			
	    		}

                // delete ai
                $this->db->where("product_id", $product_id);
                $this->db->delete("FM_product_additional_information");

	    		$ai_count = count($ai_title);
	    		for($ai = 0; $ai < $ai_count; $ai++)
	    		{
	    			$ai_title_str = $ai_title[$ai];
	    			$ai_value_str = $ai_value[$ai];

	    			$ai_data = array("product_id" => $product_id, "info_key" => $ai_title_str, "info_value" => $ai_value_str, "created_date" => date("Y-m-d H:i:s"));
	    			$this->db->insert("FM_product_additional_information", $ai_data);
	    		}

	    		$response = array("status" => "Y", "message" => "Product successfully updated.");
    		}
    		else
    		{
    			$response = array("status" => "N", "message" => "Internal server error.");
    		}

    	}
    	else
    	{
    		$response = array("status" => "N", "message" => "Product update failed! Product slug already exist.");
    	}

    	return $response;


    }


    function get_product_list($filter = array("status" => "all", "cate1" => 0, "cate3" => 0))
    {

        // get category in
        $cate_in =array();

        if($filter['cate1'] > 0 && $filter['cate2'] > 0)
        {
            $cate_in[] = $filter['cate1'];
            $cate_in[] = $filter['cate2'];
        }
        else if($filter['cate1'] > 0 && $filter['cate2'] == 0)
        {
            $cate_in[] = $filter['cate1'];
            $this->db->select("id");
            $this->db->from("FM_product_category");
            $this->db->where("parent_id", $filter['cate1']);
            $this->db->where("status !=", "D");            
            $get_ch = $this->db->get();
            if($get_ch->num_rows() > 0)
            {
                foreach($get_ch->result() as $ch_row)
                {
                    $cate_in[] = $ch_row->id;
                }
            }
        }



    	$products = array();
    	$this->db->select("id");
    	$this->db->from("FM_product");
    	$this->db->where("status !=", "D");
    	if($filter['status'] != 'all')
    	{
    		$this->db->where("status", $filter['status']);
    	}
        if(count($cate_in) > 0)
        {
            $this->db->where_in("category_id", $cate_in);
        }
    	$this->db->order_by("id", "DESC");

    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		foreach($query->result() as $product_row)
    		{
    			$product_details = $this->get_product_details_by_id($product_row->id);
    			if(count($product_details) > 0)
    			{
    				$products[] = $product_details;
    			}

    		}
    	}

    	return $products;
    }

    function get_product_details_by_id($product_id = 0)
    {
    	$details = array();


    	$products = array();
    	$this->db->select("*");
    	$this->db->from("FM_product");
    	$this->db->where("status !=", "D");
    	$this->db->where("id", $product_id);

    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
	    		$product_row = $query->row();
                $cat_id = $this->get_selected_catId($product_row->id);
                $crop_id = $this->get_selected_cropId($product_row->id);
                foreach($cat_id as $k=> $val){
    			 $category_details[] = $this->category_model->get_category_short_details_by_id($val['category_id']);
                }
                foreach($crop_id as $key => $value){
                    $crop_details [] = $this->get_crop_short_details_by_id($value['crop_id']);
                }
    			$variation_list = $this->get_variation_list_by_product_id($product_row->id);

                $category_history = $this->category_model->get_parent_list_by_category_id($product_row->category_id);

    			$additional_information_list = $this->get_product_additional_information_list($product_row->id);

    			$image_list = $this->get_product_image_by_product_id($product_row->id);

    			$details = array("id" => $product_row->id, "name" => $product_row->title, "SKU" => $product_row->SKU, "image_list" => $image_list, "category_details" => $category_details, "crop_details" => $crop_details, "category_history" => $category_history, "slug" => $product_row->slug, "short_description" => $product_row->short_description, "description" => $product_row->description, "status" => $product_row->status, "created_date" => $product_row->created_date, "updated_date" => $product_row->updated_date, "variation_list" => $variation_list, "additional_information_list" => $additional_information_list, "ord_by" => $product_row->ord_by, "is_latest" => $product_row->is_latest);

    		
    	}

    	

    	return $details;
    }




    function get_variation_list_by_product_id($product_id = 0)
    {
    	$variation_list = array();

    	$this->db->select("*");
    	$this->db->from("FM_product_variation");
    	$this->db->where("product_id", $product_id);
    	$this->db->where("status !=", "D");
    	$this->db->order_by("ord_by", "ASC");
    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		foreach($query->result() as $var_row)
    		{
    			if($var_row->discount > 0)
    			{
    				$discount_amount = round($var_row->price * $var_row->discount / 100);   
    				$discount_amount = number_format($discount_amount, 2, ".", ""); 				
    			}
    			else
    			{
    				$discount_amount = number_format(0, 2, ".", "");
    			}

    			$sale_price = $var_row->price - $discount_amount;
    			$sale_price = number_format($sale_price, 2, ".", ""); 

                $state = $this->get_state_name_by_id($var_row->state_id);

    			$variation_list[] = array("id" => $var_row->id, "title" => $var_row->title, "price" => $var_row->price, "state_id" => $var_row->state_id, "state" => $state, "is_available" => $var_row->is_available, "discount_percent" => $var_row->discount, "discount_amount" => $discount_amount, "sale_price" => $sale_price, "created_date" => $var_row->created_date, "updated_date" => $var_row->updated_date, "status" => $var_row->status, "order" => $var_row->ord_by);
    		}
    	}

    	return $variation_list;
    }

    function update_product_variation_on_condition($data, $condition)
    {
        return $this->db->set($data)->where($condition)->update("FM_product_variation");
    }

    function get_state_name_by_id($id)
    {
        $condition = ["is_available" => "Y", "id" => $id];
        $state_details = $this->db->get_where("FM_state_lookup", $condition)->row();
        return (!empty($state_details->state)) ? ucwords(strtolower($state_details->state)) : NULL;
    }

    function get_product_additional_information_list($product_id = 0)
    {
    	$additional_information = array();

    	$this->db->select("*");
    	$this->db->from("FM_product_additional_information");
    	$this->db->where("product_id", $product_id);

    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		foreach($query->result() as $row)
    		{
    			$additional_information[] = array("id" => $row->id, "info_key" => $row->info_key, "info_value" => $row->info_value);
    		}
    	}

    	return $additional_information;
    }

    function get_product_image_by_product_id($product_id = 0)
    {
    	$list = array();

    	$this->db->select("*");
    	$this->db->from("FM_product_image");
    	$this->db->where("product_id", $product_id);
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

    function delete_product_by_id($id = 0)
    {
        $this->db->select("id");
        $this->db->from("FM_product");
        $this->db->where("id", $id);
        $this->db->where("status !=", "D");
        $check_query = $this->db->get();
        if($check_query->num_rows() > 0)
        {
            $update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_product", $update_data);
            
            $response = array("status" => "Y", "message" => "Product successfully deleted.");
        }
        else
        {
            $response = array("status" => "N", "message" => "Product already deleted or not found.");
        }
        $update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
        $this->db->where("product_id", $id);
        $this->db->update("FM_product_variation", $update_data);

        return $response;

    }

    //////////////////------------------

    function get_product_name_by_id($product_id = 0)
    {
        $name = "";
        $this->db->select("title");
        $this->db->from("FM_product");
        $this->db->where("id", $product_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row  = $query->row();
            $name = $row->title;
        }
        return $name;
    }

    

    function get_product_status_by_id($product_id = 0)
    {
        $status = "D";
        $this->db->select("status");
        $this->db->from("FM_product");
        $this->db->where("id", $product_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row    = $query->row();
            $status = $row->status;
        }
        return $status;
    }

    function get_veriation_full_details_by_id($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_product_variation");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row               = $query->row();
            $product_name      = $this->get_product_name_by_id($row->product_id);
            $product_image     = $this->get_product_image_by_product_id($row->product_id);
            $product_status    = $this->get_product_status_by_id($row->product_id);
            $product_details   = array(
                "id"   => $row->product_id,
                "name" => $product_name,
                "image" => $product_image,
                "status" => $product_status
            );
            $price             = $row->price;
            $discount          = $row->discount;
            $discount_amount   = $price * $discount / 100;
            $sale_price        = round($price - $discount_amount);
            //$sale_price        = number_format($sale_price, 2);
            $price_details     = array(
                "price" => $price,
                "discount_percent" => $discount,
                "discount_amount" => $discount_amount,
                "sale_price" => $sale_price
            );
            $variation_details = array(
                "id" => $row->id,
                "title" => $row->title,
                "price_details" => $price_details,
                "status" => $row->status
            );
            if ($product_status == "Y" && $row->status == "Y")
            {
                $availability_status = "Y";
            }
            else
            {
                $availability_status = "N";
            }
            $details = array(
                "variation_details" => $variation_details,
                "product_details" => $product_details,
                "availability_status" => $availability_status
            );
        }
        return $details;
    }

    function update_product_order($product_id, $order_value)
    {
        $update_data = array("ord_by" => $order_value);
        $this->db->where("id", $product_id);
        $this->db->update("FM_product", $update_data);
        return true;
    }

    function get_selected_catId($product_id = 0){
        $details = array();
        $this->db->select("category_id");
        $this->db->from("FM_category_mapping");
        $this->db->where("product_id", $product_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $details [] = array("category_id" => $rows->category_id);
            }
        }
        return $details;
    }

    function get_selected_cropId($product_id = 0){
        $details = array();
        $this->db->select("crop_id");
        $this->db->from("FM_crop_mapping");
        $this->db->where("product_id", $product_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $details [] = array("crop_id" => $rows->crop_id);
            }
        }
        return $details;
    }

    function get_crop_short_details_by_id($crop_id = 0)
    {
        $response = array("id" => "0", "title" => "Parent");

        $this->db->select("id, title");
        $this->db->from("FM_crop");
        $this->db->where("id", $crop_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $response = array("id" => $row->id, "title" => $row->title);
        }

        return $response;
    }

    public function set_latest_products($current_latest_products)
    {
        $condArr = array("status"=>"Y", "is_latest"=>"Y");
        $products = $this->db->get_where("FM_product", $condArr)->result();
        if(count($products))
        {
            foreach($products as $product)
            {
                $condArr2 = array("status"=>"Y", "id"=>$product->id);
                $this->db->set("is_latest","N");
                $this->db->where($condArr2);
                $this->db->update("FM_product");
            }
        }

        if(count($current_latest_products))
        {
            for($i=0; $i<count($current_latest_products); $i++)
            {
                $condArr3 = array("status"=>"Y", "id"=>$current_latest_products[$i]);
                $this->db->set("is_latest","Y");
                $this->db->where($condArr3);
                $this->db->update("FM_product");
            }
        }

        if($this->db->affected_rows())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function toggle_latest($id='')
    {
        $SQL = "SELECT COUNT(*) AS count from FM_product WHERE status='Y'";
        $data = $this->db->query($SQL)->result();
        if(count($data))
        {
            $latest_count = $data[0]->count;
        }

        $temp_obj = $this->db->select('is_latest')->from('FM_product')->where('id', $id)->get()->row();
        $current_status = (is_object($temp_obj)) ? $temp_obj->is_latest : NULL;
        if($current_status==NULL || $current_status=='N')
        {
            if($latest_count<2)
            {
                $update_latest_permission = true;
                $update_field = array('is_latest' => 'Y', 'updated_date' => date('Y-m-d h:i:s'));
            }
            else
            {
                $update_latest_permission = false;
            }
            
        }
        else
        {
            $update_latest_permission = true;
            $update_field = array('is_latest' => 'N', 'updated_date' => date('Y-m-d h:i:s'));
        }

        if($update_latest_permission!=false)
        {
            $this->db->set($update_field);
            $this->db->where('id', $id);
            $this->db->update('FM_product');
            return $this->db->affected_rows();
        }
        else
        {
            return null;
        }
    }
    
}

?>