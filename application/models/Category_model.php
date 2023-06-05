<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Category_model extends CI_Model
{
    function category_list_tree($filter_data)
    {
        $list = array();
        if(isset($filter_data['status']))
        {
            $filter_status =  $filter_data['status'];
        }
        else
        {
            $filter_status =  "all";
        }

        // get 1 st lavel
        $lavel_1 = $this->get_category_list_by_parent_id(0, $filter_status);
        if(count($lavel_1) > 0)
        {
            foreach($lavel_1 as $level_1_row)
            {
                $level_2_list = array();
                // get 2nd level
                $lavel_2 = $this->get_category_list_by_parent_id($level_1_row['id'], $filter_status);


                if(count($lavel_2) > 0)
                {
                    foreach($lavel_2 as $lavel_2_row)
                    {
                        $level_3_list = array();
                        $lavel_3 = $this->get_category_list_by_parent_id($lavel_2_row['id'], $filter_status);

                        //-----------------

                        if(count($lavel_3) > 0)
                        {
                            foreach($lavel_3 as $lavel_3_row)
                            {      
                                $product_count = $this->common_model->product_count_by_category_id($lavel_3_row['id']);                          

                                $level_3_list[] = array("id" => $lavel_3_row['id'], "title" => $lavel_3_row['title'], "description" => $lavel_3_row['description'], "slug" => $lavel_3_row['slug'], "image" => $lavel_3_row['image'], "status" => $lavel_3_row['status'], "created_date" => $lavel_3_row['created_date'], "updated_date" => $lavel_3_row['updated_date'], "product_count" => $product_count);
                            }
                        }

                        $product_count = $this->common_model->product_count_by_category_id($lavel_2_row['id']);

                        //-------------------    
                        $level_2_list[] = array("id" => $lavel_2_row['id'], "title" => $lavel_2_row['title'], "description" => $lavel_2_row['description'], "slug" => $lavel_2_row['slug'], "image" => $lavel_2_row['image'], "status" => $lavel_2_row['status'], "created_date" => $lavel_2_row['created_date'], "updated_date" => $lavel_2_row['updated_date'], "child" => $level_3_list, "product_count" => $product_count);
                    }
                }
                $product_count = $this->common_model->product_count_by_category_id($level_1_row['id']);
                $list[] = array("id" => $level_1_row['id'], "title" => $level_1_row['title'], "description" => $level_1_row['description'], "slug" => $level_1_row['slug'], "image" => $level_1_row['image'], "status" => $level_1_row['status'], "created_date" => $level_1_row['created_date'], "updated_date" => $level_1_row['updated_date'], "child" => $level_2_list, "product_count" => $product_count);
            }
        }

        return $list;
    }

    function get_category_short_details_by_id($cate_id = 0)
    {
        $response = array("id" => "0", "title" => "Parent");

        $this->db->select("id, title");
        $this->db->from("FM_product_category");
        $this->db->where("id", $cate_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $response = array("id" => $row->id, "title" => $row->title);
        }

        return $response;
    }

    function get_parent_list_by_category_id($cate_id = 0)
    {      

        $this->db->select("id, title, parent_id");
        $this->db->from("FM_product_category");
        $this->db->where("id", $cate_id);

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {           
            $row = $query->row();   


            if($row->parent_id != 0)
            {
                $this->db->select("id, title, parent_id");
                $this->db->from("FM_product_category");
                $this->db->where("id", $row->parent_id);
                $query1 = $this->db->get();
                
                if($query1->num_rows() > 0)
                {
                    $row1 = $query1->row();
                    $response[] = array("id" => $row1->id, "title" => $row1->title);
                }
                else
                {
                    $response[] = array("id" => "0", "title" => "Parent");
                }

                $response[] = array("id" => $row->id, "title" => $row->title);

                
            }
            else
            {
                $response[] = array("id" => $row->id, "title" => $row->title);
                $response[] = array("id" => "0", "title" => "Parent");
                
            }
            
        }
        else
        {
            $response[] = array("id" => "0", "title" => "Parent");
            $response[] = array("id" => "0", "title" => "Parent");
        }

        return $response;
    }

    function category_list($filter_data)
    {
        $list = array();
        if(isset($filter_data['status']))
        {
            $filter_status =  $filter_data['status'];
        }
        else
        {
            $filter_status =  "all";
        }

        // get 1 st lavel
        $lavel_1 = $this->get_category_list_by_parent_id(0, $filter_status);
        if(count($lavel_1) > 0)
        {
            foreach($lavel_1 as $level_1_row)
            {

                $product_count = $this->common_model->get_product_count_by_category_id($level_1_row['id']);
                $child_count = $this->common_model->child_category_count_by_category_id($level_1_row['id']);

                $list[] = array("id" => $level_1_row['id'], "title" => $level_1_row['title'], "description" => $level_1_row['description'],  "slug" => $level_1_row['slug'], "image" => $level_1_row['image'], "status" => $level_1_row['status'], "created_date" => $level_1_row['created_date'], "updated_date" => $level_1_row['updated_date'], "parent_details" => $level_1_row['parent_details'], "is_featured" => $level_1_row["is_featured"], "product_count" => $product_count, "child_count" => $child_count);
                
                // get 2nd level
                $lavel_2 = $this->get_category_list_by_parent_id($level_1_row['id'], $filter_status);


                if(count($lavel_2) > 0)
                {
                    foreach($lavel_2 as $lavel_2_row)
                    {
                        $product_count = $this->common_model->product_count_by_category_id($lavel_2_row['id']);
                        $child_count = $this->common_model->child_category_count_by_category_id($lavel_2_row['id']);
                        $list[] = array("id" => $lavel_2_row['id'], "title" => $lavel_2_row['title'], "description" => $lavel_2_row['description'], "slug" => $lavel_2_row['slug'], "image" => $lavel_2_row['image'], "status" => $lavel_2_row['status'], "created_date" => $lavel_2_row['created_date'], "updated_date" => $lavel_2_row['updated_date'], "parent_details" => $lavel_2_row['parent_details'], "is_featured" => $lavel_2_row["is_featured"], "product_count" => $product_count, "child_count" => $child_count);
                        
                        $lavel_3 = $this->get_category_list_by_parent_id($lavel_2_row['id'], $filter_status);

                        //-----------------

                        if(count($lavel_3) > 0)
                        {
                            foreach($lavel_3 as $lavel_3_row)
                            {                                
                                $product_count = $this->common_model->product_count_by_category_id($lavel_3_row['id']);
                                 $child_count = $this->common_model->child_category_count_by_category_id($lavel_3_row['id']);
                                $list[] = array("id" => $lavel_3_row['id'], "title" => $lavel_3_row['title'], "description" => $lavel_3_row['description'], "slug" => $lavel_3_row['slug'], "image" => $lavel_3_row['image'], "status" => $lavel_3_row['status'], "created_date" => $lavel_3_row['created_date'], "updated_date" => $lavel_3_row['updated_date'], "parent_details" => $lavel_3_row['parent_details'], "is_featured" => $lavel_3_row["is_featured"], "product_count" => $product_count, "child_count" => $child_count);
                            }
                        }
 
                        
                    }
                }

                
            }
        }

        

        return $list;
    }

    function get_category_list_by_parent_id($parent_id = 0, $status = 'all')
    {
        $category_row = array();
        $this->db->select("*");
        $this->db->from("FM_product_category");
        $this->db->where("parent_id", $parent_id);
        $this->db->where("status !=", 'D');
        if($status != 'all')
        {
            $this->db->where("status", $status);
        }
        
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $rows)
            {
                $parent_details = $this->get_category_short_details_by_id($rows->parent_id);
                $category_row[] = array("id" => $rows->id, "title" => $rows->title, "description" => $rows->description, "slug" => $rows->slug, "image" => FRONT_URL.$rows->image, "status" => $rows->status, "created_date" => $rows->created_date, "updated_date" => $rows->updated_date, "parent_details" => $parent_details, "is_featured" => $rows->is_featured);
            }
        }

        return $category_row;

    }

    function check_slug_exist($slug, $cate_id = 0)
    {
        $this->db->select("id");
        $this->db->from("FM_product_category");
        $this->db->where("slug", $slug);
        $this->db->where("status !=", "D");
        if($cate_id > 0)
        {
            $this->db->where("id !=", $cate_id);
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

    function add_category($data)
    {
        $response = array("status" => "N", "message" => "Something was wrong");
        $title = $data['title'];
        $slug = $data['slug'];
        $description = $data['description'];
        $parent_id = $data['parent_id'];
        $image = $data['image'];
        $icon = $data['icon'];
        $status = $data['status'];
        $is_featured = $data['is_featured'];
        if($is_featured != 'Y')
        {
            $is_featured = "N";
        }

        // check slug before insert
        $this->db->select("id");
        $this->db->from("FM_product_category");
        $this->db->where("slug", $slug);
        $this->db->where("status !=", "D");
        $check_query = $this->db->get();
        if($check_query->num_rows() > 0)
        {
            $response = array("status" => "N", "message" => "This slug already exist with another category.");
        }
        else
        {
            $insert_data = array("title" => $title, "description" => $description, "parent_id" => $parent_id, "image" => $image, "icon" => $icon, "slug" => $slug, "status" => $status, "is_featured" => $is_featured, "created_date" => date("Y-m-d H:i:s"));
            $this->db->insert("FM_product_category", $insert_data);
            $id =  $this->db->insert_id();
            $this->add_new_meta_data($id,$data['meta_title'],$data['meta_description'],$data['meta_keyword']);

            $response = array("status" => "Y", "message" => "New category successfully created.");


        }

        return $response;

    }

    function update_category($data)
    {
        $response = array("status" => "N", "message" => "Something was wrong");
        $id = $data['id'];
        $title = $data['title'];
        $slug = $data['slug'];
        $description = $data['description'];
        $parent_id = $data['parent_id'];
        $status = $data['status'];
        $is_featured = $data['is_featured'];
        if($is_featured != 'Y')
        {
            $is_featured = "N";
        }

        // check slug before insert
        $this->db->select("id");
        $this->db->from("FM_product_category");
        $this->db->where("slug", $slug);
        $this->db->where("id !=", $id);
        $this->db->where("status !=", "D");
        $check_query = $this->db->get();
        if($check_query->num_rows() > 0)
        {
            $response = array("status" => "N", "message" => "This slug already exist with another category.");
        }
        else
        {
            $update_data = array("title" => $title, "description" => $description, "parent_id" => $parent_id, "slug" => $slug, "status" => $status, "is_featured" => $is_featured, "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_product_category", $update_data);

            if($status == 'N')
            {
                // 2nd level
                $this->db->select("id, parent_id");
                $this->db->from("FM_product_category");
                $this->db->where("parent_id", $id);
                $up_query1 = $this->db->get();
                if($up_query1->num_rows() > 0)
                {
                    foreach($up_query1->result() as $up_row1)
                    {
                        $lv_1_update_data = array("status" => "N", "updated_date" => date("Y-m-d H:i:s"));
                        $this->db->where("id", $up_row1->id);
                        $this->db->update("FM_product_category", $lv_1_update_data);

                        // check second level
                        $this->db->select("id");
                        $this->db->from("FM_product_category");
                        $this->db->where("parent_id", $up_row1->id);
                        $up_query2 = $this->db->get();
                        if($up_query2->num_rows() > 0)
                        {
                            foreach($up_query2->result() as $up_row2)
                            {
                                $lv_2_update_data = array("status" => "N", "updated_date" => date("Y-m-d H:i:s"));
                                $this->db->where("id", $up_row2->id);
                                $this->db->update("FM_product_category", $lv_2_update_data);

                                // check second level

                            }
                        }

                    }
                }

            }

            $this->update_meta_data_details($id,$data['meta_title'],$data['meta_description'],$data['meta_keyword']);

            $response = array("status" => "Y", "message" => "Category successfully updated.");


        }

        return $response;

    }

    function delete_category_by_id($id = 0)
    {
        $this->db->select("id, parent_id");
        $this->db->from("FM_product_category");
        $this->db->where("id", $id);
        $this->db->where("status !=", "D");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $main_up_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_product_category", $main_up_data);

            // 2nd level
            $this->db->select("id, parent_id");
            $this->db->from("FM_product_category");
            $this->db->where("parent_id", $id);
            $up_query1 = $this->db->get();
            if($up_query1->num_rows() > 0)
            {
                foreach($up_query1->result() as $up_row1)
                {
                    $lv_1_update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
                    $this->db->where("id", $up_row1->id);
                    $this->db->update("FM_product_category", $lv_1_update_data);

                    // check second level
                    $this->db->select("id");
                    $this->db->from("FM_product_category");
                    $this->db->where("parent_id", $up_row1->id);
                    $up_query2 = $this->db->get();
                    if($up_query2->num_rows() > 0)
                    {
                        foreach($up_query2->result() as $up_row2)
                        {
                            $lv_2_update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
                            $this->db->where("id", $up_row2->id);
                            $this->db->update("FM_product_category", $lv_2_update_data);

                            // check second level

                        }
                    }

                }
            }

            $response = array("status" => "N", "message" => "Category successfully deleted.");


        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid Try. Category already deleted or not found.");
        }
    }

    function get_category_details_id($id = 0)
    {
        $category_row = array();
        $this->db->select("*");
        $this->db->from("FM_product_category");
        $this->db->where("id", $id);
        $this->db->where("status !=", 'D');       
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
                $rows = $query->row();
                $parent_details = $this->get_parent_list_by_category_id($rows->parent_id);
                $meta_details = $this->get_meta_data_by_id($rows->id);
                $category_row = array(
                    "id" => $rows->id,
                    "title" => $rows->title,
                    "description" => $rows->description,
                    "slug" => $rows->slug,
                    "image" => FRONT_URL.$rows->image,
                    "icon" => FRONT_URL.$rows->icon,
                    "status" => $rows->status,
                    "created_date" => $rows->created_date,
                    "updated_date" => $rows->updated_date,
                    "parent_details" => $parent_details,
                    "meta_details" => $meta_details,
                    "is_featured" => $rows->is_featured
                );
            
        }

        return $category_row;

    }

    function update_category_image($data = array())
    {
        if(count($data) > 0)
        {
            $cate_id = $data['id'];
            $image = $data['image'];

            $update_data = array("image" => $image);
            $this->db->where("id", $cate_id);
            $this->db->update("FM_product_category", $update_data);
            return true;

        }
        else
        {
            return false;
        }
        
    }

    function update_category_icon($data = array())
    {
        if(count($data) > 0)
        {
            $cate_id = $data['id'];
            $icon = $data['icon'];

            $update_data = array("icon" => $icon);
            $this->db->where("id", $cate_id);
            $this->db->update("FM_product_category", $update_data);
            return true;

        }
        else
        {
            return false;
        }
        
    }

    function add_new_meta_data($category_id,$meta_title, $meta_description, $meta_keyword){
        $insert_data = array();
        $insert_data['category_id'] = $category_id;
        $insert_data['meta_title'] = !empty($meta_title) ? $meta_title : null;
        $insert_data['meta_description'] = !empty($meta_description) ? $meta_title : null;
        $insert_data['meta_keyword'] = !empty($meta_keyword) ? $meta_title : null;
        $insert_data['updated_date'] = date("Y-m-d H:i:s");

        $this->db->insert("FM_category_meta_data", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New meta data created", "id" => $id);

        return $response;
    }

    function update_meta_data_details($category_id, $meta_title, $meta_description, $meta_keyword){
        $update_data = array();
        $insert_data['category_id'] = $category_id;
        $update_data['meta_title'] = !empty($meta_title) ? $meta_title : null;
        $update_data['meta_description'] = !empty($meta_description) ? $meta_description : null;
        $update_data['meta_keyword'] = !empty($meta_keyword) ? $meta_keyword : null;
        $update_data['updated_date'] = date("Y-m-d H:i:s");


        $this->db->where("category_id", $category_id);
        $this->db->update("FM_category_meta_data", $update_data);
        $response = array("status" => "Y", "message" => "Meta data Details updated.");
        return $response;
    }

    function get_meta_data_by_id($category_id){
        $response = array(
            'id' => null,
            'category_id' => $category_id,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keyword' => null,
        );
        $this->db->select("*");
        $this->db->from("FM_category_meta_data");
        $this->db->where("category_id", $category_id);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $rows = $query->row();
            $response = array(
                "id" => $rows->id,
                "category_id" => $rows->category_id,
                "meta_title" => $rows->meta_title,
                "meta_description" => $rows->meta_description,
                "meta_keyword" => $rows->meta_keyword,
            );
        }
        return $response;
    }
    
}

?>
