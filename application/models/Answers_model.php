<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Answers_model extends CI_Model
{
	//Get users list
    function questions_list($filter_data)
    {
        $list = array();

        $this->db->select("*");
        $this->db->from("FM_questions");
        $this->db->where("status !=", "D");
        $this->db->where("status !=", "NP");
        $this->db->where("is_clone !=", "Y");
        if($filter_data['status'] == 'A')
        {
            $this->db->where("status", "A");
        }
        elseif($filter_data['status'] == 'P')
        {
            $this->db->where("status", "P");
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
            	$answer = $this->db->get_where('FM_answers',array('question_id' =>$row->id,'is_deleted' => 'N'))->row();
            	$user_details = $this->db->get_where('FM_customer',array('id' =>$row->customer_id,'status' => 'Y'))->row();
                $crop_details = $this->db->get_where('FM_crop',array('id' =>$row->crop_id,'status' => 'Y'))->row();
                $list[] = array(
                    "id" => $row->id,
                    "customer_name" => (!empty($user_details)?$user_details->first_name.' '.$user_details->last_name:'Admin'),
                    "question" => $row->title,
                    "crop_name" => (!empty($crop_details)?$crop_details->title:''),
                    "answer" => (!empty($answer)?$answer->answer_text:''),
                    "status" => $row->status,
                    "created_date" => $row->created_date,
                    "updated_date" => $row->updated_date,
                );
            }
        }
        return $list;
    }

    // Get single question details
    function single_question_details($id)
    {
        $this->db->select("*");
        $this->db->from("FM_questions");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $answer = $this->db->get_where('FM_answers',array('question_id' =>$row->id,'is_deleted' => 'N'))->row();
            if(isset($row->customer_id)){

                $image_data = $this->db->get_where('FM_question_image',array('question_id' =>$row->id))->row();
                if(count((array)$image_data)>0)
                {
                    $image = FRONT_URL.$image_data->image;
                }
                else
                {
                    $image = '';
                }
                
            }else{
                $image = '';
            }

            $details = array(
                "id" => $row->id,
                "question" => $row->title,
                "customer_id" => $row->customer_id,
                "image" => $image,
                "answer" => (!empty($answer)?$answer->answer_text:''),
                "status" => $row->status,
                "crop_id" => $row->crop_id,
                "created_date" => $row->created_date,
                "updated_date" => $row->updated_date,
            );

            $response = array("status" => "Y", "message" => "Details found", "details" => $details);

        }
        else
        {
            $response = array("status" => "N", "message" => "No details found. Maybe user is already deleted.");
        }
        return $response;

    }

    function delete_question_by_id($id)
    {
        $this->db->select("id");
        $this->db->from("FM_questions");
        $this->db->where("id", $id);
        $this->db->where("status !=", "D");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $update_data = array("status" => "D", "updated_date" => date("Y-m-d H:i:s"));
            $answer_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
            $this->db->where("id", $id);
            $this->db->update("FM_questions", $update_data);

            $this->db->where("clone_id", $id);
            $this->db->update("FM_questions", $update_data);

            $this->db->where("question_id", $id);
            $this->db->update("FM_answers", $answer_data);

            $response = array("status" => "Y", "message" => "Question successfully deleted.");

        }
        else
        {
            $response = array("status" => "N", "message" => "Invalid Question ID or Question already deleted.");
        }
        return $response;
    }

    function add_question($data)
    {
        $response = array("status" => "N", "message" => "Something was wrong");
        $customer_id = $data['customer_id'];
        $title = $data['title'];
        $crop_id = $data['crop_id'];
        $status = $data['status'];

        
            $insert_data = array("customer_id" => $customer_id, "title" => $title, "crop_id" => $crop_id, "status" => $status, "created_date" => date("Y-m-d H:i:s"));
            $this->db->insert("FM_questions", $insert_data);
            $id = $this->db->insert_id();

            $response = array("status" => "Y", "message" => "New question successfully created.",'question_id' => $id);


        

        return $response;

    }
}