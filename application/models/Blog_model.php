<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Blog_model extends CI_Model
{
    
    function blog_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_blog");
        if($filter_data['status'] == 'Y')
        {
            $this->db->where("status", "Y");
        }
        elseif($filter_data['status'] == 'N')
        {
            $this->db->where("status", "N");
        }          

        $this->db->where("is_deleted", "N");
        
        $this->db->order_by("id", "desc");
        //$this->db->order_by("category_id", "asc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                    $category_name = $this->get_blog_category_name_by_id($row->category_id);
                    $list[] = array("id" => $row->id,"title" => $row->title, "category_id" => $row->category_id, "category_name" => $category_name, "author_name" => $row->author_name, "image" => FRONT_URL.$row->image, "blog_content" => $row->blog_content, "home_page_show" => $row->home_page_show, "status" => $row->status, "created_date" => $row->created_date, "updated_date" => $row->updated_date);
                
                
            }
        }
        return $list;
    }  


    function blog_category_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_blog_category");
        if($filter_data['status'] == 'Y')
        {
            $this->db->where("status", "Y");
        }
        elseif($filter_data['status'] == 'N')
        {
            $this->db->where("status", "N");
        }          
        $this->db->where("is_deleted", "N");
        $this->db->order_by("id", "desc");
        //$this->db->order_by("category_id", "asc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $blog_count = $this->blog_count_by_category_id($row->id);
                $list[] = array("id" => $row->id,"title" => $row->title,  "status" => $row->status, "created_date" => $row->created_date, "blog_count" => $blog_count,  "updated_date" => $row->updated_date);
            }
        }
        return $list;
    } 


    function blog_count_by_category_id($category_id = 0)
    {
        $count = 0;

        $this->db->select("COUNT(id) as blog_count");
        $this->db->from("FM_blog");
        $this->db->where("is_deleted !=", "Y");
        $this->db->where("category_id", $category_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $count = $query->row()->blog_count;
        }

        return $count;
    }

    
    //Add blog
    function add_blog($data)
    {
        $title = ucwords(strtolower($data['title']));
        $author_name = ucwords(strtolower($data['author_name']));
        $slug = strtolower($data['slug']);
        $blog_content = $data['blog_content'];
        $status = $data['status'];
        $category = $data['category'];
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array("title" =>  $title, "category_id" => $category, "slug" => $slug, "author_name" => $author_name,"blog_content" => $blog_content, "status" => $status, "created_date" => $created_date);
        $this->db->insert("FM_blog", $insert_data);
        $id =  $this->db->insert_id();
        $response = array("status" => "Y", "message" => "New Blog Successfully Posted.", "id" => $id);

        return $response;

    }

    //Add category blog
    function add_category_blog($data)
    {
        $title = ucwords(strtolower($data['title']));
        $status = $data['status'];
        $created_date = date("Y-m-d H:i:s");
        $insert_data = array("title" =>  $title, "status" => $status, "created_date" => $created_date);
        $this->db->insert("FM_blog_category", $insert_data);
        $response = array("status" => "Y", "message" => "New Blog Category Successfully Creted.");

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
            $update_data = array("image" => $image, "updated_date" => date("Y-m-d H:i:s"));
        }

        $this->db->where("id", $id);
        $this->db->update("FM_blog", $update_data);
        return true;
        
    }

    function check_slug_exist($slug, $blog_id = 0)
    {
        $this->db->select("id");
        $this->db->from("FM_blog");
        $this->db->where("slug", $slug);
        $this->db->where("is_deleted !=", "Y");
        if($blog_id > 0)
        {
            $this->db->where("id !=", $blog_id);
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

    function single_blog_details($id)
    {
        $this->db->select("*");
        $this->db->from("FM_blog");
        $this->db->where("is_deleted", "N");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $category_name = $this->get_blog_category_name_by_id($row->category_id);
            
            $details = array("id" => $row->id, "title" => $row->title, "slug" => $row->slug, "category_id" => $row->category_id, "category_name" => $category_name, "author_name" => $row->author_name, "blog_content" => $row->blog_content, "image" => FRONT_URL.$row->image, "status" => $row->status, "created_date" => $row->created_date);

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);       

            

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found.");
        }
        return $response;

    }

    function single_cat_blog_details($id)
    {
        $this->db->select("*");
        $this->db->from("FM_blog_category");
        $this->db->where("is_deleted", "N");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            
            $details = array("id" => $row->id, "title" => $row->title, "status" => $row->status, "created_date" => $row->created_date);

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);       

            

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found.");
        }
        return $response;
    }
    

    function update_blog($data)
    {
        $id = $data['id'];
        $title = ucwords(strtolower($data['title']));
        $slug = strtolower($data['slug']);
        $author_name = ucwords(strtolower($data['author_name']));
        $blog_content = $data['blog_content'];
        $status = $data['status'];
        $category = $data['category'];
        $updated_date = date("Y-m-d H:i:s");

        // before update check ID
        $this->db->select("id");
        $this->db->from("FM_blog");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Blog details not found.");
        }
        else
        {
            $update_data = array("title" =>  $title, "category_id" => $category, "slug" => $slug, "author_name" => $author_name, "blog_content" => $blog_content, "status" => $status, "updated_date" => $updated_date);
            $this->db->where("id", $id);
            $this->db->update("FM_blog", $update_data);
            $response = array("status" => "Y", "message" => "Blog content successfully updated.");

        }
        return $response;
    }
    
    function update_category_blog($data)
    {

        $id = $data['id'];
        $title = ucwords(strtolower($data['title']));
        $status = $data['status'];
        $updated_date = date("Y-m-d H:i:s");

        // before update check ID
        $this->db->select("id");
        $this->db->from("FM_blog_category");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() == 0)
        {
            $response = array("status" => "N", "message" => "Invalid request. Blog details not found.");
        }
        else
        {
            $update_data = array("title" =>  $title, "status" => $status, "updated_date" => $updated_date);
            $this->db->where("id", $id);
            $this->db->update("FM_blog_category", $update_data);
            $response = array("status" => "Y", "message" => "Blog  category content successfully updated.");

        }
        return $response;
    }


    // blog delete
    function delete_blog_by_id($id)
    {
        $this->db->select("id");
        $this->db->from("FM_blog");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $update_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_blog", $update_data); 
                      
            $response = array("status" => "Y", "message" => "Blog successfully deleted.");

        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid try or blog already deleted.");
        }
        return $response;
    }

    // change homepage blog
    function change_homepage_blog_by_id ($id, $value)
    {
        $condition = ["is_deleted" => "N", "id" => $id];
        $data = ["home_page_show" => $value];
        
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update("FM_blog");

        if ($this->db->affected_rows() > 0)
        {
            $response = array("status" => "Y", "message" => "Homepage Blog changed successfully.");
        }
        else
        {
            $response = array("status" => "N", "message" => "Failed to change Homepage Blog!");
        }
    }

    //blog category delete
    function delete_blog_category_by_id($id)
    {

        $this->db->select("id");
        $this->db->from("FM_blog_category");
        $this->db->where("id", $id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $update_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_blog_category", $update_data); 
                      
            $response = array("status" => "Y", "message" => "Blog category successfully deleted.");

        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid try or blog category already deleted.");
        }
        return $response;
    }

    function get_blog_category()
    {
        $data = array();

        $this->db->select("*");
        $this->db->from("FM_blog_category");
        $this->db->where("status", "Y");
        $this->db->where("is_deleted", "N");
        $this->db->order_by("title", "ASC");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $data = $query->result_array();
        }

        return $data;
    }

    function get_blog_category_name_by_id($id = 0)
    {
        $name = "Unknown";

        $this->db->select("title");
        $this->db->from("FM_blog_category");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $name = $query->row()->title;
        }

        return $name;
    }
    
}

?>