<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
class Welcome extends REST_Controller {

	public function __construct()
    {
        parent:: __construct();
    }
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
	public function myApi_post($a,$b,$c){
	    $response = array("success" => TRUE, "message" => "message", "abc" => "$a $b $c");
        $this->response($response, REST_Controller::HTTP_OK);
	}

	public function checkVersion_get()
	{
		$version_details = $this->db->get('FM_preferences')->result();
		$version_number = intval($version_details[0]->content);
		$version_name = $version_details[1]->content;

		$response = array(
			"success" => TRUE,
			"message" => "version fetched successfully",
			"version" => array(
				"number" => $version_number,
				"name" => $version_name
			)
		);

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function homeBanners_get()
	{
		$banner_list = array();
		$sql = "SELECT id, title, subtitle, image as imageURL, actionType, actionData 
				FROM FM_banner 
				WHERE status='Y' and is_deleted='N'";
		
		$basepath_image_url = $this->db->select("content")->from("FM_preferences")->where("name","base_image_url")->get()->result()[0];
		$basepath_image_url = $basepath_image_url->content;

        $banner_data = $this->db->query($sql)->result();
        foreach($banner_data as $row)
        {
        	$banner_list[] = array(
        		"id" => $row->id,
        		"title" => $row->title,
        		"subtitle" => $row->subtitle,
        		"imageURL" => $basepath_image_url.$row->imageURL,
        		"actionType" => $row->actionType,
        		"actionData" => $row->actionData
        	);
        }

		$response = array(
			"success" => TRUE,
			"message" => "banner fetched successfully",
			"banners" => $banner_list
		);

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function homeVideo_post()
	{
		$condArr = array("id"=>1, "status"=>"Y");
		$video_row = $this->db->get_where("FM_video",$condArr)->row();
		$admin_user = $this->db->get_where("FM_admin_user",array("id"=>"1"))->row();

		$comment_count_sql = "SELECT COUNT(*) as count FROM FM_video_comments WHERE video_id=$video_row->id";
		$comments_count = $this->db->query($comment_count_sql)->result()[0]->count;

		$likes_count_sql = "SELECT COUNT(*) as count FROM FM_video_likes WHERE video_id=$video_row->id";
		$likes_count = $this->db->query($likes_count_sql)->result()[0]->count;

		$video_dislike_by_customer = 'false';
		if($this->input->post('customer_id')==null || !isset($_POST['customer_id']))
		{
			$video_like_by_customer = "false";
		}
		else
		{
			$customer_id = $this->input->post('customer_id');
			$detailsArr = array("video_id"=>$video_row->id, "customer_id"=>$customer_id, "is_deleted"=>"N");
			$result = $this->db->get_where("FM_video_likes",$detailsArr);
			if($result->num_row()>0)
			{
				$video_like_by_customer = "true";
			}
		}

		$video_details = array(
			"id" => $video_row->id,
			"videoTitle" => $video_row->title,
			"videoUploader" => $admin_user->name,
			"videoURL" => "https://www.youtube.com/embed/".$video_row->yt_video_id,
			"videoDescription" => $video_row->description,
			"videoUploadDate" => $this->getDifferenceBetweenDate($video_row->created_date)." days ago",
			"likes" => $likes_count,
			"isLiked" => $video_like_by_customer,
			"isDisliked" => $video_dislike_by_customer,
			"videoThumbnail" => "https://img.youtube.com/vi/".$video_row->yt_video_id."/0.jpg",
			"videoViews" => intval($video_row->videoViews),
			"tags" => $video_row->tags,
			"disLikes" => $video_row->disLikes
		);

		$response = array(
			"success" => TRUE,
			"message" => "video fetched successfully",
			"video" => $video_details
		);

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function homePageProducts_post()
	{
		$condArr = array("status"=>"Y");
		$sql = "SELECT * FROM FM_product WHERE status='Y' AND is_latest='yes' LIMIT 2";
		$products_details = $this->db->query($sql)->result();
		$products = array();

		for($i=0; $i<count($products_details); $i++)
		{
			$basepath_image_url = $this->db->select("content")->from("FM_preferences")->where("name","base_image_url")->get()->result()[0];
			$basepath_image_url = $basepath_image_url->content;

			$image_condArr = array("product_id"=>$products_details[$i]->id);
			$image_array = $this->db->get_where("FM_product_image",$image_condArr)->result();
			if(count($image_array)>0)
			{
				foreach($image_array as $image_details)
				{
					$image= array(
						"id" => $image_details->id,
						"image" => $basepath_image_url.$image_details->image
					);

					$image_list[] = $image;
				}
			}
			

			$var_condArr = array("product_id"=>$products_details[$i]->id,"status!="=>"D");
			$var_row = $this->db->get_where("FM_product_variation",$var_condArr)->result();

			if(count($var_row)>0)
			{
				$var_list = array();
				for($j=0; $j<count($var_row); $j++)
				{
		            $sale_price = $var_row[$j]->price;
		            $sale_price = number_format($sale_price, 2);
		            $limit = $this->getLimitOfProducts($var_row[$j]->id);

		            if($var_row[$j]->status=="Y")
		            {
		            	$var_status = TRUE;
		            }
		            else
		            {
		            	$var_status = FALSE;
		            }

					$var_arr = array(
						"id" => $var_row[$j]->id,
		                "title" => $var_row[$j]->title,
		                "price" => '',
		                "discount_percent" => 0,
		                "discount_amount" => 0,
		                "sale_price" => $sale_price,
		                "order" => "$j",
		                "status" => $var_status,
		                "wish_status" => "N",
		                "limit"		=> $limit,
					);

					$var_list[$j] = $var_arr;
				}
			}
			else
			{
				$var_list = array();
			}

            $default_sale_price = $var_row[0]->price;
            $default_sale_price = number_format($default_sale_price, 2);

			$details = array(
				"id" => $products_details[$i]->id,
				"name" => $products_details[$i]->slug,
				"SKU" => $products_details[$i]->SKU,
				"image_list" => $image_list,
				"variation_list" => $var_list,
				"title" => $products_details[$i]->title,
				"description" => $products_details[$i]->short_description,
				"variation_title" => $var_row[0]->title,
				"price" => '',
				"discount_percent" => 0,
				"discount_amount" => 0,
				"sale_price" => $default_sale_price,
				"order" => "$i",
				"status" => TRUE,
				"wish_status" => "N",
				"items_total" => "",
				"order_total" => "",
			);

			$products[$i] = $details;
			$image_list = [];
		}

		$response = array(
			"success" => TRUE,
			"message" => "products fetched successfully",
			"products" => $products
		);

		$this->response($response, REST_Controller::HTTP_OK);
	}

	function renderBlogList($blog_data)
	{
		$blog_list = array();
		if(count($blog_data)>0)
		{
			$blog_list = array();

			$basepath_image_url = $this->db->select("content")
										   ->from("FM_preferences")
										   ->where("name","base_image_url")
										   ->get()->result()[0];
			$basepath_image_url = $basepath_image_url->content;

			for($i=0; $i<count($blog_data); $i++)
			{
				$blogID = $blog_data[$i]->id;

				$comment_count_sql = "SELECT COUNT(*) as count FROM FM_blog_comments WHERE blog_id='$blogID'";
				$comments = $this->db->query($comment_count_sql)->result()[0]->count;

				$likes_count_sql = "SELECT COUNT(*) as count FROM FM_blog_likes WHERE blog_id='$blogID' and is_deleted='N'";
				$likes = $this->db->query($likes_count_sql)->result()[0]->count;

				$details = array(
					"id"=>$blog_data[$i]->id,
					"image"=>$basepath_image_url.$blog_data[$i]->image,
					"likes"=>$likes,
					"comments"=>$comments,
					"title"=>$blog_data[$i]->title,
					"description"=>$blog_data[$i]->blog_content
				);

				$blog_list[$i] = $details;
			}
		}
		return $blog_list;
	}

	public function homePageBlogs_post()
	{
		$SQL = "SELECT * FROM FM_blog WHERE is_deleted='N' ORDER BY id DESC LIMIT 2";
		$blog_data = $this->db->query($SQL)->result();
		if(count($blog_data)>0)
		{
			$blog_list = $this->renderBlogList($blog_data);
			$response = array(
				"success" => TRUE,
				"message" => "blogs fetched successfully",
				"blog_list" => $blog_list
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function getAllBlogs_post()
	{
		$SQL = "SELECT * FROM FM_blog WHERE is_deleted='N' ORDER BY id DESC";
		$blog_data = $this->db->query($SQL)->result();
		if(count($blog_data)>0)
		{
			$blog_list = $this->renderBlogList($blog_data);
			$response = array(
				"success" => TRUE,
				"message" => "blogs fetched successfully",
				"blog_list" => $blog_list
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function selectCropList_post()
	{
		if($this->input->post('user_id')==null || !isset($_POST['user_id']))
		{
			$response = array(
				"success" => FALSE,
				"message" => "Please send user_id to get crop list.",
				"crops" => array()
			);
		}
		else
		{	
			$user_id = $this->input->post('user_id');
			$crop_id_array = $this->db->select("crop_id")
									  ->from("FM_customer_crop_mapping")
									  ->where("customer_id",$user_id)
									  ->order_by("id","DESC")
									  ->get()->result();
			if(count($crop_id_array)>0)
			{
				foreach($crop_id_array as $crop_details)
				{
					$crop_id[] = $crop_details->crop_id;
				}
			}

			$basepath_image_url = $this->db->select("content")
										   ->from("FM_preferences")
										   ->where("name","base_image_url")
										   ->get()->result()[0]->content;

			$crop_list_array = $this->db->select("*")
										->from("FM_crop")
										->where("status","Y")
										->order_by("id","DESC")
										->get()->result();
			if(count($crop_list_array)>0)
			{
				foreach($crop_list_array as $details)
				{
					if(isset($crop_id))
					{
						$isSelected = (in_array($details->id, $crop_id)?TRUE:FALSE);
						if (in_array($details->id, $crop_id)) {
							$crop = array(
								"id" => $details->id,
								"image" => $basepath_image_url.$details->image,
								"isSelected" => $isSelected,
								"name" => $details->title
							);

							$crop_list[] = $crop;
						}
					}
					// else
					// {
					// 	$isSelected = False
					// 	;
					// }

					// $crop = array(
					// 	"id" => $details->id,
					// 	"image" => $basepath_image_url.$details->image,
					// 	"isSelected" => $isSelected,
					// 	"name" => $details->title
					// );

					// $crop_list[] = $crop;
				}
			}

			$response = array(
				"success" => TRUE,
				"message" => "crops fetched successfully",
				"crops" => $crop_list
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function productTabApi_post()
	{
		$condArr = array("status"=>"Y");
		$productTabs = $this->db->select("*")
					 ->from("FM_product_category")
					 ->where($condArr)
					 ->get()->result();

		if(count($productTabs)>0)
		{

			$productCategoryList = array();
			$productCategoryList[] = array('id' => 0, 'title' => 'All');

			foreach ($productTabs as $productTab) {
				$productCategory = array(
					"id" => $productTab->id,
					"title" => $productTab->title
				);

				$productCategoryList[] = $productCategory;
			}

			$response = array(
				"success" => true,
				"message" => "Product tabs fetched successfully",
				"productTabs" => $productCategoryList
			);
		}
		else
		{
			$response = array(
				"success" => false,
				"message" => "Failed to fetch product tabs",
				"productTabs" => array()
			);
		}
		
		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function userComments_post()
	{
		$missingParam = array();

		if($this->input->post('user_id')==null || !isset($_POST['user_id']))
		{
			// $missingParam[] = "User ID";
			$user_id = null;
		}
		else
		{
			$user_id = $this->input->post('user_id');
		}

		if($this->input->post('blog_id')==null || !isset($_POST['blog_id']))
		{
			$missingParam[] = "Blog ID";
		}
		else
		{
			$blog_id = $this->input->post('blog_id');
		}

		if($this->input->post('comment')==null || !isset($_POST['comment']))
		{
			$comment = null;
		}
		else
		{
			$comment = $this->input->post('comment');
		}
		
		if(count($missingParam)>0)
		{
			$missingString = implode(", ", $missingParam);
			$missingString = rtrim($missingString,", ");
			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"userPosts" => array()
			);
		}
		else
		{
			if($comment!=null)
			{
				$insertDataArr = array(
					"customer_id"=> $user_id,
		            "blog_id" => $blog_id,
		            "comments" => $comment,
		            "created_date" => date("Y-m-d h:m:s"),
		            "updated_date" => null,
		            "testing" => "Development_".date('F_Y')
				);

				$this->db->insert("FM_blog_comments",$insertDataArr);
			}

			$basepath_image_url = $this->db->select("content")
										   ->from("FM_preferences")
										   ->where("name","base_image_url")
										   ->get()->result()[0]->content;


			$comments_list = $this->db->get_where("FM_blog_comments",array("blog_id"=>$blog_id))->result();
			if(count($comments_list)>0)
			{
				for($i=0; $i<count($comments_list); $i++)
				{
					$customer_details = $this->db->get_where("FM_customer",array("id"=>$comments_list[$i]->customer_id))->result();
					foreach($customer_details as $customer)
					{
						$userImage = $basepath_image_url.$customer->profile_image;
						$userName = $customer->first_name.' '.$customer->last_name;
					}
					if($comments_list[$i]->updated_date!=null)
					{
						$date = $comments_list[$i]->updated_date;
					}
					else
					{
						$date = $comments_list[$i]->created_date;
					}

					$comment = array(
						"id" => $comments_list[$i]->id,
						"image" => $userImage,
						"personName" => $userName,
						"date" => $date,
						"comment" => $comments_list[$i]->comments
					);

					$userComments[] = $comment;
				}
			}
			
			$response = array(
				"success" => true,
				"message" => "Comments fetched successfully",
				"userComments" => $userComments
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function buyInputProducts_post()
	{
		$missingParam = array();

		if($this->input->post('product_tab_id')==null || !isset($_POST['product_tab_id']))
		{
			$product_tab_id = null;
		}
		else
		{
			$product_tab_id = $this->input->post('product_tab_id');
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ", $missingParam);
			$missingString = rtrim($missingString,", ");
			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"buy_input_products" => array()
			);
		}
		else
		{
			if($product_tab_id!=0)
			{
				$condArr = array("category_id"=>$product_tab_id);
				$productLists = $this->db->get_where('FM_category_mapping', $condArr)->result();
			}
			else{
				$productLists = '*';
			}
			
			$products_details = [];
			
			if ($productLists == '*') {
				$condArr = ['status' => 'Y'];
				$products_details = $this->db->get_where("FM_product",$condArr)->result();				
			}
			else{
				foreach ($productLists as $productList) {
					$condArr = ['id' => $productList->product_id, 'status' => 'Y'];
					$pd = $this->db->get_where("FM_product",$condArr)->row();
					$products_details[] = $pd;
				}
				
			}

			// print_r($products_details);

			$buy_input_products = array();

			if(count($products_details)>0)
			{
				foreach ($products_details as $productDetails) {
					if (isset($productDetails->id) && $productDetails->id != null) {
						$basepath_image_url = $this->db->select("content")->from("FM_preferences")->where("name","base_image_url")->get()->result()[0];
						$basepath_image_url = $basepath_image_url->content;

						$image_condArr = array("product_id"=>$productDetails->id);
						$image_array = $this->db->get_where("FM_product_image",$image_condArr)->result();
						if(count($image_array)>0)
						{
							foreach($image_array as $image_details)
							{
								$image= array(
									"id" => $image_details->id,
									"image" => $basepath_image_url.$image_details->image
								);

								$image_list[] = $image;
							}
						}
						

						$var_condArr = array("product_id"=>$productDetails->id,"status!="=>"D");
						$var_row = $this->db->get_where("FM_product_variation",$var_condArr)->result();

						if(count($var_row)>0)
						{
							$var_list = array();
							for($j=0; $j<count($var_row); $j++)
							{
					            $sale_price = $var_row[$j]->price;
					            $sale_price = number_format($sale_price, 2);
					            $limit = $this->getLimitOfProducts($var_row[$j]->id);

					            if($var_row[$j]->status=="Y")
					            {
					            	$var_status = TRUE;
					            }
					            else
					            {
					            	$var_status = FALSE;
					            }

								$var_arr = array(
									"id" => $var_row[$j]->id,
					                "title" => $var_row[$j]->title,
					                "price" => '',
					                "discount_percent" => 0,
					                "discount_amount" => 0,
					                "sale_price" => $sale_price,
					                "order" => "$j",
					                "status" => $var_status,
					                "wish_status" => "N",
					                "limit"		=> $limit
								);

								$var_list[$j] = $var_arr;
							}
						}
						else
						{
							$var_list = array();
						}

			            $default_sale_price = $var_row[0]->price;
			            $default_sale_price = number_format($default_sale_price, 2);

						$details = array(
							"id" => $productDetails->id,
							"SKU" => $productDetails->SKU,
							"category" => intval($productDetails->category_id),
							"description" => $productDetails->short_description,
							"discount_amount" => 0,
							"discount_percent" => 0,
							"image_list" => $image_list,
							"items_total" => "",
							"name" => $productDetails->slug,
							"order" => "0",
							"order_total" => "",
							"price" => '',
							"sale_price" => $default_sale_price,
							"status" => TRUE,
							"title" => $productDetails->title,
							"variation_list" => $var_list,
							"variation_title" => $var_row[0]->title,
							"wish_status" => "N",
						);

						$buy_input_products[] = $details;
						$image_list = [];
					}
				}

				$response = array(
					"success" => true,
					"message" => "Buy input products fetched successfully",
					"buy_input_products" => $buy_input_products
				);
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "Product Tab ID not found",
					"buy_input_products" => array()
				);
			}
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function quantityUnitApi_post()
	{
		$missingParam = array();

		if(count($missingParam)>0)
		{
			$missingString = implode(", ", $missingParam);
			$missingString = rtrim($missingString,", ");
			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"quantity_units" => array()
			);
		}
		else
		{
			$quantity_units = array(
				array("id"=>"1","unit"=>"kg"),
				array("id"=>"2","unit"=>"qt"),
				array("id"=>"3","unit"=>"ton")
			);

			$response = array(
				"success" => true,
				"message" => "Quantity units fetched successfully",
				"quantity_units" => $quantity_units
			);
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function check_isAvailable($date, $day)
	{
		$last_date_timestamp = strtotime($day." day", strtotime($date));
		$current_date = date("Y-m-d");
	  	$last_date = date("Y-m-d", $last_date_timestamp);
	  	if(strtotime($current_date)>strtotime($last_date))
	  	{
	    	$isAvailable = false;
	  	}
	  	else
	  	{
	    	$isAvailable = true;
	  	}

	  	return $isAvailable;
	}

	public function check_isSold($SPID)
	{
		return false;
	}

	public function sellProduceProductsApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null && !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("crop_id")==null && !isset($_POST["crop_id"]))
		{
			$missingParam[] = "crop_id";
		}
		else
		{
			$crop_id = $this->input->post("crop_id");
		}

		if($this->input->post("variety")==null && !isset($_POST["variety"]))
		{
			$missingParam[] = "variety";
		}
		else
		{
			$variety = $this->input->post("variety");
		}

		if($this->input->post("expectedQty")==null && !isset($_POST["expectedQty"]))
		{
			$missingParam[] = "expectedQty";
		}
		else
		{
			$expectedQty = $this->input->post("expectedQty");
		}

		if($this->input->post("qtyUnit")==null && !isset($_POST["qtyUnit"]))
		{
			$missingParam[] = "qtyUnit";
		}
		else
		{
			$qtyUnit = $this->input->post("qtyUnit");
		}

		if($this->input->post("expectedDate")==null && !isset($_POST["expectedDate"]))
		{
			$missingParam[] = "expectedDate";
		}
		else
		{
			$expectedDate = $this->input->post("expectedDate");
		}

		if($this->input->post("expectedDays")==null && !isset($_POST["expectedDays"]))
		{
			$missingParam[] = "expectedDays";
		}
		else
		{
			$expectedDays = $this->input->post("expectedDays");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ", $missingParam);
			$missingString = rtrim($missingString,", ");
			if(count($missingParam)>1)
			{
				$message = $missingString." are not given.";
			}
			else
			{
				$message = $missingString." is not given.";
			}

			$response = array(
				"success" => false,
				"message" => $message,
				"products" => array()
			);
		}
		else
		{
			$userComment = '';
            if(!empty($this->input->post('userComment'))){
                $userComment = $this->input->post('userComment');
            }

            $insertDataArr = array(
            	'customer_id' => $user_id, 
            	'crop_id' => $crop_id, 
            	'variety' => $variety, 
            	'qty' => $expectedQty, 
            	'qty_unit' => $qtyUnit, 
            	'price' => 0.00, 
            	'available_date' => $expectedDate, 
            	'available_in_days' => $expectedDays,
            	'note' => $userComment,
            	'status' => "A",
            	'created_date' => date("Y-m-d h:m:s"),
	            'updated_date' => null,
	            'testing' => "Development_".date('F_Y')
            );

            $this->db->insert('FM_sell_produce',$insertDataArr);
            $sellProduce_id =  $this->db->insert_id();

            if(!empty($_FILES['image']['name']))
            {
	            $filesCount = count($_FILES['image']['name']);

	            for ($i=0; $i <$filesCount; $i++) {
	                $upload_dir = FILE_UPLOAD_BASE_PATH.'/uploads/sellproduce/';
	                $rand_name = time()."-".$i;
	                $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name'][$i]);
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/sellproduce/'.$rand_name.basename($_FILES['image']['name'][$i]);
	                $actual_path = str_replace(" ","-",$actual_path);
	                if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $upload_file))
	                    {
	                        $image [] = $actual_path;
	                    }
	                    else
	                    {
	                        $image [] = "uploads/users/no-image.png";
	                    }

	            }
	            foreach($image as $v){
	                $image_data = array('sell_produce_id' => $sellProduce_id, 'image' => $v);
	                $this->db->insert('FM_sell_produce_image',$image_data);
	            }
            }

            $sellProduceData = $this->db->get_where("FM_sell_produce",array("status"=>"A"))->result();
            if(count($sellProduceData)>0)
            {
            	for($i=0; $i<count($sellProduceData); $i++)
            	{
            		$basepath_image_url = $this->db->select("content")
										   ->from("FM_preferences")
										   ->where("name","base_image_url")
										   ->get()->result()[0]->content;

					if($sellProduceData[$i]->testing=="Development_December_2021")
					{
						$imagePathPrefix = "https://testing.surobhiagro.in/api/v2/assets/";
					}
					else
					{
						$imagePathPrefix = $basepath_image_url;
					}
					
            		$imageData = $this->db->select("id,image")
            							   ->from("FM_sell_produce_image")
            							   ->where("sell_produce_id",$sellProduceData[$i]->id)
            							   ->get()->result();
            		if(count($imageData)>0)
            		{
            			for($j=0; $j<count($imageData); $j++)
            			{
            				$image = array(
            					"id" => $imageData[$j]->id,
            					"image" => $imagePathPrefix.$imageData[$j]->image
            				);

            				$image_list[] = $image;
            			}
            		}
            		else
            		{
            			$image_list = array();
            		}

          			$date = $sellProduceData[$i]->available_date;
	  				$day = $sellProduceData[$i]->available_in_days;
					$isAvailable = $this->check_isAvailable($date, $day);

					$SPID = $sellProduceData[$i]->id;
					$isSold = $this->check_isSold($SPID);

            		$product = array(
            			"id" => intval($sellProduceData[$i]->id),
            			"crop_id" => $sellProduceData[$i]->crop_id,
            			"variety" => $sellProduceData[$i]->variety,
            			"userComment" => $sellProduceData[$i]->note,
            			"expectedQty" => $sellProduceData[$i]->qty,
            			"qtyUnit" => $sellProduceData[$i]->qty_unit,
            			"expectedPrice" => $sellProduceData[$i]->price,
            			"expectedDate" => $sellProduceData[$i]->available_date,
            			"days" => $sellProduceData[$i]->available_in_days,
            			"images" => $image_list,
            			"isAvailable" => $isAvailable,
            			"isSold" => $isSold
            		);

            		$product_list[] = $product;
            	}

            	$response = array(
					"success" => true,
					"message" => "Your sell produce is added successfully",
					"products" => $product_list
				);
            }
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}



	// ============================= ||
	// Ask Community API Group Start ||
	function render_community_data_list($communityData)
	{
		if(count($communityData)>0)
		{
			for($i=0; $i<count($communityData); $i++)
			{
				$condArr = array("status"=>"Y", "id"=>$communityData[$i]['customer_id']);
				$userData = $this->db->get_where("FM_customer",$condArr)->result();
				
				if (count($userData) > 0) {
					$userData = $userData[0];
					$userName = $userData->first_name." ".$userData->last_name;

					$date = $communityData[$i]['created_date'];
					$date_timestamp = strtotime($date);
					$date_inText = date("F d",$date_timestamp);
					$time_inText = date("h:i A",$date_timestamp);
					$total_date = $date_inText." at ".$time_inText;

					$basepath_image_url = $this->db->select("content")
											   ->from("FM_preferences")
											   ->where("name","base_image_url")
											   ->get()->result()[0]->content;
					$userImageUrl = $basepath_image_url.$userData->profile_image;

					$condArr2 = array("community_id"=>$communityData[$i]['id']);
					$topicImageData = $this->db->get_where("FM_community_image",$condArr2)->result();
					if(count($topicImageData)>0)
					{
						$topicImageUrl = $basepath_image_url.$topicImageData[0]->image;
					}
					else
					{
						$topicImageUrl = "";
					}
					
					$condArr3 = array("status"=>"Y","id"=>$communityData[$i]['crop_id']);
					$topic_data = $this->db->get_where("FM_crop",$condArr3)->result();
					if(count($topic_data)>0)
					{
						$topicCategory = $topic_data[0]->title;
					}
					else
					{
						$topicCategory = "";
					}

					$condArr4 = array("community_id"=>$communityData[$i]['id']);
					$answer_list = $this->db->get_where("FM_community_comments",$condArr4)->result();
					$answerCount = count($answer_list);

					$getLikesConditionArr = [
						'topic_id'		=> $communityData[$i]['id'],
						'is_deleted'	=> 'N'
					];
					$likesCount = $this->db->from('FM_community_likes')->where($getLikesConditionArr)->get()->num_rows();

					$topic = array(
						"id" => intval($communityData[$i]['id']),
						"userName" => $userName,
						"date" => $total_date,
						"userImageUrl" => $userImageUrl,
						"topicHeading" => $communityData[$i]['quesstion'],
						"topicImageUrl" => $topicImageUrl,
						"topicDescription" => $communityData[$i]['problem_description'],
						"topicCategory" => $topicCategory,
						"likesCount" => $likesCount,
						"answerCount" => intval($answerCount),
						"hashTags" => ""
					);

					$community_data_list[] = $topic;
				}
			}

			return $community_data_list;
		}
	}

	function get_all_community_data()
	{
		$SQL = "SELECT * FROM FM_ask_community WHERE status='A' ORDER BY created_date DESC";
		$communityData = $this->db->query($SQL)->result();
		$communityData = json_decode(json_encode($communityData), true);
		$community_data_list = $this->render_community_data_list($communityData);
		return $community_data_list;
	}

	function get_users_and_others_community_data($user_id)
	{
		$SQL1 = "SELECT * FROM FM_ask_community WHERE status='A' AND customer_id='$user_id' ORDER BY created_date DESC";
		$user_community_data = $this->db->query($SQL1)->result();
		$user_community_data_array = json_decode(json_encode($user_community_data), true);

		$SQL2 = "SELECT * FROM FM_ask_community WHERE status='A' AND customer_id!='$user_id' ORDER BY created_date DESC";
		$others_community_data = $this->db->query($SQL2)->result();
		$others_community_data_array = json_decode(json_encode($others_community_data), true);

		$communityData = array_merge($user_community_data_array,$others_community_data_array);
		$community_data_list = $this->render_community_data_list($communityData);
		return $community_data_list;
	}

	public function communityTopicsApi_post()
	{
		if(isset($_POST["user_id"]))
		{
			$user_id = $_POST["user_id"];
			$community_data_list = $this->get_users_and_others_community_data($user_id);
			$response = array(
				"success" => true,
				"message" => "Community topics fetched successfully.",
				"listOfTopics" => $community_data_list
			);
		}
		else
		{
			$community_data_list = $this->get_all_community_data();
			$response = array(
				"success" => true,
				"message" => "Community topics fetched successfully.",
				"listOfTopics" => $community_data_list
			);
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}
	// Ask Community API Group End ||
	// =========================== ||



	public function communityTopicsDatesApi_post()
	{
		$communityData = $this->db->get_where("FM_ask_community",array("status"=>"A"))->result();
		if(count($communityData)>0)
		{
			for($i=0; $i<count($communityData); $i++)
			{
				$date = $communityData[$i]->created_date;
				$date_timestamp = strtotime($date);

				$date_inText = date("F d",$date_timestamp);
				$time_inText = date("h:i A",$date_timestamp);
				$total_date = $date_inText." at ".$time_inText;

				$date_obj = array(
					"id" => intval($communityData[$i]->id),
					"date" => $total_date
				);

				$date_list[] = $date_obj;
			}

			$response = array(
				"success" => true,
				"message" => "Community Topics Dates fetched successfully.",
				"listOfDates" => $date_list
			);
		}
		else
		{
			$response = array(
				"success" => false,
				"message" => "Failed to fetch Community Topic Dates.",
				"listOfDates" => array()
			);
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function communityTopicsCategoryApi_post()
	{
		$sql = "SELECT DISTINCT(crop_id) from FM_ask_community WHERE crop_id!=0 ORDER BY crop_id ASC";
		$communityData = $this->db->query($sql)->result();
		if(count($communityData)>0)
		{
			$basepath_image_url = $this->db->select("content")
										   ->from("FM_preferences")
										   ->where("name","base_image_url")
										   ->get()->result()[0]->content;

			for($i=0; $i<count($communityData); $i++)
			{
				$condArr = array("status"=>"Y","id"=>$communityData[$i]->crop_id);
				$category_data = $this->db->get_where("FM_crop",$condArr)->result();
				if(count($category_data)>0)
				{
					$category = array(
						"id" => intval($category_data[0]->id),
						"categoryName" => $category_data[0]->title,
						"image" => $basepath_image_url.$category_data[0]->image
					);

					$category_list[] = $category;
				}
			}

			$response = array(
				"success" => true,
				"message" => "Community Topics Categories fetched successfully.",
				"listOfCategory" => $category_list
			);
		}
		else
		{
			$response = array(
				"success" => false,
				"message" => "Failed to fetch Community Topic Categories.",
				"listOfCategory" => array()
			);
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function videoListApi_post()
	{	
		$missingParam = array();

		if($this->input->post("user_id")==null && !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ", $missingParam);
			$missingString = rtrim($missingString, ", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"listOfVideos" => array()
			);
		}
		else
		{
			$videoData = $this->db->select("*")
							  ->from("FM_video")
							  ->where("status","Y")
							  ->order_by("id","DESC")
							  ->get()->result();

			$adminData = $this->db->get_where('FM_admin_user',array("id" => '1'))->row();
			$videoURLPrefix = "https://www.youtube.com/embed/";

			if(count($videoData)>0)
			{
				for($i=0; $i<count($videoData); $i++)
				{
					$now = time();
			  		$your_date = strtotime(explode(" ", $videoData[$i]->created_date)[0]);
			  		$datediff = $now - $your_date;
			  		$dayCount = round($datediff / (60 * 60 * 24));
			  		if($dayCount>0)
			  		{
			  			$videoUploadDate = $dayCount." days ago";
			  		}
			  		else if($dayCount==0)
			  		{
			  			$videoUploadDate = "Today";
			  		}

					$condArr = array("is_deleted"=>"N", "video_id"=>$videoData[$i]->id);
					$likesCount = $this->db->select("COUNT(id) as likes_count")
							      		   ->from("FM_video_likes")
							        	   ->where($condArr)
							        	   ->get()->row()->likes_count;

					$condArr2 = array("customer_id"=>$user_id, "video_id"=>$videoData[$i]->id);
					$video_likes_data = $this->db->get_where("FM_video_likes", $condArr2)->result();
					if(count($video_likes_data)>0)
					{
						$sign = $video_likes_data[0]->sign;
						$is_deleted = $video_likes_data[0]->is_deleted;

						if($is_deleted=="N" && $sign=="")
						{
							$isLiked = true;
							$isDisliked = false;
						}
						else if($is_deleted=="Y" && $sign=="")
						{
							$isLiked = false;
							$isDisliked = false;
						}
						else if($is_deleted=="Y" && $sign=="negative")
						{
							$isLiked = false;
							$isDisliked = true;
						}
						else
						{
							$isLiked = false;
							$isDisliked = false;
						}
					}
					else
					{
						$isLiked = false;
						$isDisliked = false;
					}

					$video = array(
						"id" => intval($videoData[$i]->id),
						"videoURL" => $videoURLPrefix.$videoData[$i]->yt_video_id,
						"videoTitle" => $videoData[$i]->title,
						// "videoThumbnail" => $videoData[$i]->videoThumbnail,
						"videoThumbnail" => "https://img.youtube.com/vi/".$videoData[$i]->yt_video_id."/0.jpg",
						"videoUploader" => $adminData->name,
						"videoViews" => intval($videoData[$i]->videoViews),
						"videoUploadDate" => $videoUploadDate,
						"tags" => $videoData[$i]->tags,
						"videoDescription" => $videoData[$i]->description,
						"likes" => $likesCount,
						"disLikes" => $videoData[$i]->disLikes,
						"isLiked" => $isLiked,
						"isDisliked" => $isDisliked
					);

					$video_list[] = $video;
				}

				$response = array(
					"success" => true,
					"message" => "Video list get successfully.",
					"listOfVideos" => $video_list
				);
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "Failed to get Video List.",
					"listOfVideos" => array()
				);
			}
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function videoUserCommentsApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null && !isset($_POST["user_id"]))
		{
			$user_id = null;
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("comment")==null && !isset($_POST["comment"]))
		{
			$comment = null;
		}
		else
		{
			$comment = $this->input->post("comment");
		}

		if($this->input->post("video_id")==null && !isset($_POST["video_id"]))
		{
			if($comment!=null)
			{
				$missingParam[] = "video_id";
			}
			else
			{
				$video_id = null;
			}
		}
		else
		{
			$video_id = $this->input->post("video_id");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ", $missingParam);
			$missingString = rtrim($missingString,", ");
			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"listOfComments" => array()
			);
		}
		else
		{
			if($comment!=null && $video_id!=null)
			{
				$insertDataArr = array(
					"customer_id"=> $user_id,
		            "video_id" => $video_id,
		            "comments" => $comment,
		            "created_date" => date("Y-m-d h:m:s"),
		            "updated_date" => null,
		            "testing" => "Development_".date('F_Y')
				);

				$this->db->insert("FM_video_comments",$insertDataArr);
			}

			$commentsData = $this->db->select("*")
									  ->from("FM_video_comments")
									  ->order_by("id","ASC")
									  ->get()->result();

			if(count($commentsData)>0)
			{
				for($i=0; $i<count($commentsData); $i++)
				{
					$condArr = array("status"=>"Y", "id"=>$commentsData[$i]->customer_id);
					$personData = $this->db->get_where("FM_customer",$condArr)->result();
					if(count($personData)>0)
					{
						$personName = $personData[0]->first_name." ".$personData[0]->last_name;
						$personImage = $personData[0]->profile_image;
					}
					else
					{
						$personName = "";
						$personImage = "";
					}
					
					$date = $commentsData[$i]->created_date;
					$date_timestamp = strtotime($date);
					$commentDate = date("d F Y",$date_timestamp);

					$comment = array(
						"id" => intval($commentsData[$i]->id),
						"personName" => $personName,
						"personImage" => $personImage,
						"date" => $commentDate,
						"comment" => $commentsData[$i]->comments
					);

					if ($personName != null) {
						$comment_list[] = $comment;
					}
				}

				$response = array(
					"success" => true,
					"message" => "Video Comments fetched successfully.",
					"listOfComments" => $comment_list
				);
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "Failed to get Video Comments List.",
					"listOfComments" => array()
				);
			}
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	private function GUID()
	{
	    if (function_exists('com_create_guid') === true)
	    {
	        return trim(com_create_guid(), '{}');
	    }

	    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	public function cropDoctorAskApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null || !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("crop_id")==null || !isset($_POST["crop_id"]))
		{
			$missingParam[] = "crop_id";
		}
		else
		{
			$crop_id = $this->input->post("crop_id");
		}

		if($this->input->post("question")==null || !isset($_POST["question"]))
		{
			$missingParam[] = "question";
		}
		else
		{
			$question = $this->input->post("question");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"isSubmited" => "Failed"
			);
		}
		else
		{
			$unique_hash_id = $this->GUID();
			$insertDataArr = array(
				"hash_id" => $unique_hash_id,
				"customer_id" => $user_id,
				"crop_id" => $crop_id,
				"clone_id" => null,
				"title" => $question,
				"status" => "A",
				"is_clone" => "N",
				"created_date" => date("Y-m-d h:i:s"),
				"testing" => "Development_".date("F_Y")
			);

			$this->db->insert('FM_questions',$insertDataArr);
            $inserted_question_id =  $this->db->insert_id();

            if(!empty($_FILES['image']['name']))
            {
	            $filesCount = count($_FILES['image']['name']);

	            for ($i=0; $i<$filesCount; $i++) {
	                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/community/';
	                $rand_name = time()."-".$i;
	                $upload_file = $upload_dir.$rand_name.basename($_FILES['image']['name'][$i]);
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/community/'.$rand_name.basename($_FILES['image']['name'][$i]);
	                $actual_path = str_replace(" ","-",$actual_path);
	                if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $upload_file))
                    {
                        $image [] = $actual_path;
                    }
                    else
                    {
                        $image [] = "uploads/users/no-image.png";
                    }

	            }
	            foreach($image as $v){
	                $image_data = array(
	                	"question_id" => $inserted_question_id,
	                	"customer_id" => $user_id, 
	                	"image" => $v,
	                	"created_date" => date("Y-m-d h:i:s")
	                );
	                $this->db->insert('FM_question_image',$image_data);
	            }
            }

			$response = array(
				"success" => true,
				"message" => "Question submited successfully.",
				"isSubmited" => "Done"
			);
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	function render_crop_doctor_answers($answer_data)
	{
		if(count($answer_data)>0)
		{
			for($i=0; $i<count($answer_data); $i++)
			{
				$question_id = $answer_data[$i]['question_id'];

				$question_row = $this->db->select("*")
										 ->from("FM_questions")
										 ->where("id", $question_id)
										 ->get()->row();

				$question_image_row = $this->db->select("*")
											   ->from("FM_question_image")
											   ->where("question_id", $question_id)
											   ->get()->result();

				if(count($question_image_row)>0)
				{
					if(strpos($question_row->testing,"Development")!==false)
					{
						$image_URL_prefix = "https://testing.surobhiagro.in/api/v2/assets/";
					}
					else
					{
						$image_URL_prefix = $this->db->select("content")
												   ->from("FM_preferences")
												   ->where("name","base_image_url")
												   ->get()->result()[0]->content;
					}

					$question_image = $image_URL_prefix.$question_image_row[0]->image;
				}
				else
				{
					$question_image = "";
				}

				$image_URL_prefix = $this->db->select("content")
												   ->from("FM_preferences")
												   ->where("name","base_image_url")
												   ->get()->result()[0]->content;
				$listOfRecommendedProducts = [];
				$products = explode(',', $answer_data[$i]['recommended_products']);

				if (count($products) > 0) {
					foreach ($products as $product) {
						if ($product != null) {
							$product_id = explode('_', $product)[0];
							$product_name = $this->db->get_where('FM_product', ['id' => $product_id])->row()->title;
							$product_image = $this->db->get_where("FM_product_image", ['product_id' => $product_id])->row()->image;

							$temp = [
								'productIdWithVariationId'	=> $product,
								'productName'				=> $product_name
							];

							$listOfRecommendedProducts[] = $temp;
						}
					}
				}

				$answer = array(
					"id" => $answer_data[$i]['id'],
					"answer" => $answer_data[$i]['answer_text'],
					"question" => $question_row->title,
					"questionDate" => $this->formatIntuitiveDate($question_row->created_date),
					"image" => $question_image,
					"answerDate" => $this->formatIntuitiveDate($answer_data[$i]['created_date']),
					"listOfRecommendedProducts"	=> ($product != null) ? $listOfRecommendedProducts : []
				);

				$answers_list[] = $answer;
			}

			return $answers_list;
		}
	}

	function get_crop_doctor_all_answers()
	{
		$SQL = "SELECT * FROM FM_answers WHERE is_deleted='N' ORDER BY created_date DESC";
		$answer_data = $this->db->query($SQL)->result();
		$answer_data = json_decode(json_encode($answer_data), true);
		$list_of_answers = $this->render_crop_doctor_answers($answer_data);
		return $list_of_answers;
	}

	function get_crop_doctor_users_and_others_answers($user_id)
	{
		$listOfRecommendedProducts = $data1 = $data2 = [];
		$SQL1 = "SELECT * FROM `FM_answers` WHERE is_deleted='N' AND question_id IN (SELECT id FROM FM_questions WHERE customer_id='$user_id') ORDER BY created_date DESC";
		$crop_doctor_user_answers = $this->db->query($SQL1)->result();
		$crop_doctor_user_answers_array = json_decode(json_encode($crop_doctor_user_answers), true);

		$SQL2 = "SELECT * FROM `FM_answers` WHERE is_deleted='N' AND question_id IN (SELECT id FROM FM_questions WHERE customer_id!='$user_id') ORDER BY created_date DESC";
		$crop_doctor_others_answers = $this->db->query($SQL2)->result();

		$crop_doctor_others_answers_array = json_decode(json_encode($crop_doctor_others_answers), true);

		$answer_data = array_merge($crop_doctor_user_answers_array,$crop_doctor_others_answers_array);
		$list_of_answers = $this->render_crop_doctor_answers($answer_data);
		return $list_of_answers;
	}

	public function cropDoctorAnswerApi_post()
	{
		if(isset($_POST["user_id"]))
		{
			$list_of_answers = $this->get_crop_doctor_users_and_others_answers($_POST["user_id"]);
			$response = array(
				"success" => true,
				"message" => "Answer fetched successfully.",
				"listOfAnswers" => $list_of_answers
			);
		}
		else
		{
			$list_of_answers = $this->get_crop_doctor_all_answers();
			$response = array(
				"success" => true,
				"message" => "Answer fetched successfully.",
				"listOfAnswers" => $list_of_answers
			);
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	private function formatIntuitiveDate($date = '')
	{
		if (date('Y') == date('Y', strtotime($date))) {
			$shortDate = date('F d', strtotime($date));	
		}
		else{
			$shortDate = date('F d, Y', strtotime($date));
		}
		$time = date('h:i A', strtotime($date));
		return $shortDate.' at '.$time;
	}

	public function askCommunityDataApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null && !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("title")==null && !isset($_POST["title"]))
		{
			$missingParam[] = "title";
		}
		else
		{
			$title = $this->input->post("title");
		}

		if($this->input->post("topics")==null && !isset($_POST["topics"]))
		{
			$missingParam[] = "topics";
		}
		else
		{
			$topics = $this->input->post("topics");
		}

		if($this->input->post("category_id")==null && !isset($_POST["category_id"]))
		{
			$missingParam[] = "category_id";
		}
		else
		{
			$category_id = $this->input->post("category_id");
		}

		if($this->input->post("question")==null && !isset($_POST["question"]))
		{
			$missingParam[] = "question";
		}
		else
		{
			$question = $this->input->post("question");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"listOfCommunityData" => array()
			);
		}
		else
		{
			$insertDataArr = array(
				"customer_id" => $user_id,
				"crop_id" => $category_id,
				"quesstion" => $title,
				"problem_description" => $question,
				"topics" => $topics,
				"testing" => "Development_".date("F_Y")
			);

			$this->db->insert("FM_ask_community", $insertDataArr);
			$community_question_id = $this->db->insert_id();

			if(!empty($_FILES['image_list']['name']))
            {
	            $filesCount = count($_FILES['image_list']['name']);

	            $i = 0;

	            foreach ($_FILES['image_list']['name'] as $fileName) {
	            	$upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/community/';
	                $rand_name = time()."-".$i;
	                $upload_file = $upload_dir.$rand_name.basename($fileName);
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/community/'.$rand_name.basename($fileName);
	                $actual_path = str_replace(" ","-",$actual_path);
	                foreach ($_FILES['image_list']['tmp_name'] as $imageTmpName) {
	                	if (move_uploaded_file($imageTmpName, $upload_file))
	                    {
	                        $image [] = $actual_path;
	                    }
	                    else
	                    {
	                        $image [] = "uploads/users/no-image.png";
	                    }
	                }
                    $i++;
	            }
	            foreach($image as $v){
	                $image_data = array('community_id' => $community_question_id, 'image' => $v);
	                $this->db->insert('FM_community_image',$image_data);
	            }
            }

            $ask_community_data = $this->db->select("*")
            							   ->from("FM_ask_community")
            							   ->where("status","A")
            							   ->order_by("id","DESC")
            							   ->get()->result();

            if(count($ask_community_data)>0)
            {
            	for($i=0; $i<count($ask_community_data); $i++)
            	{
            		$data_id = $ask_community_data[$i]->id;

            		$basepath_image_url = $this->db->select("content")
										   ->from("FM_preferences")
										   ->where("name","base_image_url")
										   ->get()->result()[0]->content;
            		// $basepath_image_url = "https://testing.surobhiagro.in/api/v2/assets/";

            		$image_data = $this->db->get_where("FM_community_image",array("community_id"=>$data_id))->result();
            		if(count($image_data)>0)
            		{
            			for($k=0; $k<count($image_data); $k++)
            			{
            				$image = $basepath_image_url.$image_data[$k]->image;
            				$image_list[] = $image;
            			}
            		}
            		else
            		{
            			$image_list = array();
            		}

            		$topic_data = $ask_community_data[$i]->topics;

            		if($topic_data!="")
            		{
            			$topic_list = explode(",", $topic_data);
            		}
            		else
            		{
            			$topic_list = array();
            		}
            		

            		$answer_data = $this->db->get_where("FM_community_comments",array("community_id"=>$data_id))->result();
            		$answerCount = count($answer_data);

            		$data = array(
            			"id" => $data_id,
            			"image" => $image_list,
            			"title" => $ask_community_data[$i]->quesstion,
            			"topic" => $topic_list,
            			"likesCount" => 0,
            			"answerCount" => $answerCount,
            			"question" => $ask_community_data[$i]->problem_description,
            			"questionDate" => $ask_community_data[$i]->created_date,
            		);

            		$data_list[] = $data;
            	}
            }

            $response = array(
				"success" => true,
				"message" => "Your question is added Successfully into Community",
				"listOfCommunityData" => $data_list
			);
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function myStreamAnswerApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null && !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("question_id")==null && !isset($_POST["question_id"]))
		{
			$missingParam[] = "question_id";
		}
		else
		{
			$question_id = $this->input->post("question_id");
		}

		if($this->input->post("comment")==null && !isset($_POST["comment"]))
		{
			$missingParam[] = "comment";
		}
		else
		{
			$comment = $this->input->post("comment");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"communityData" => array()
			);
		}
		else
		{
			$condArr = array("status"=>"A", "id"=>$question_id);
			$question_data = $this->db->get_where("FM_ask_community",$condArr)->result();
			if(count($question_data)>0)
			{
				if(!empty($_FILES['image']['name']))
	            {
	                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/community/';
	                $rand_name = time();
	                $upload_file = $upload_dir.$rand_name.$_FILES['image']['name'];
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/community/'.$rand_name.$_FILES['image']['name'];
	                $actual_path = str_replace(" ","-",$actual_path);
	                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
                    {
                        $image = $actual_path;
                    }
                    else
                    {
                        $image = null;
                    }
	            }
	            else
	            {
	            	$image = null;
	            }

				$insertDataArr = array(
					"customer_id" => $user_id,
					"community_id" => $question_id,
					"comments" => $comment,
					"image" => $image,
					"testing" => "Development_".date("F_Y")
				);

				$this->db->insert("FM_community_comments", $insertDataArr);

				$condArr = array("status"=>"A", "id"=>$question_id);
				$ask_community_row = $this->db->select("*")
	            							   ->from("FM_ask_community")
	            							   ->where($condArr)
	            							   ->get()->row();

	            $data_id = $ask_community_row->id;

            	$basepath_image_url = $this->db->select("content")
								   ->from("FM_preferences")
								   ->where("name","base_image_url")
								   ->get()->result()[0]->content;
	            

	    		$image_data = $this->db->get_where("FM_community_image",array("community_id"=>$data_id))->result();
	    		if(count($image_data)>0)
	    		{
	    			for($k=0; $k<count($image_data); $k++)
	    			{
	    				$image = $basepath_image_url.$image_data[$k]->image;
	    				$image_list[] = $image;
	    			}
	    		}
	    		else
	    		{
	    			$image_list = array();
	    		}

	    		$answer_data = $this->db->get_where("FM_community_comments",array("community_id"=>$data_id))->result();
	    		if(count($answer_data)>0)
	    		{
	    			$answerCount = count($answer_data);
	    			for($j=0; $j<count($answer_data); $j++)
	    			{
	    				$answer_id = $answer_data[$j]->id;
	    				$customer_data = $this->db->get_where("FM_customer",array("id"=>$answer_id))->result();
	    				if(count($customer_data)>0)
	    				{
	    					$personName = $customer_data[0]->first_name." ".$customer_data[0]->last_name;
	    				}
	    				else
	    				{
	    					$personName = "";
	    				}

	    				if($answer_data[$j]->image!="")
	    				{
	    					$attachment = $basepath_image_url.$answer_data[$j]->image;
	    				}
	    				else
	    				{
	    					$attachment = "";
	    				}

	    				$answer = array(
	    					"id" => $answer_id,
	    					"personName" => $personName,
	    					"date" => intval(strtotime($answer_data[$j]->created_date) * 1000),
	    					"comment" => $answer_data[$j]->comments,
	    					"attachment" => $attachment
	    				);

	    				$answer_list[] = $answer;	
	    			}
	    		}
	    		else
	    		{
	    			$answerCount = 0;
	    			$answer_list = array();
	    		}

	    		$data = array(
	    			"id" => $data_id,
	    			"image" => $image_list,
	    			"title" => $ask_community_row->quesstion,
	    			"topic" => explode(",",$ask_community_row->topics),
	    			"likesCount" => 0,
	    			"answerCount" => $answerCount,
	    			"question" => $ask_community_row->problem_description,
	    			"questionDate" => $ask_community_row->created_date,
	    			"answer" => $answer_list
	    		);

				$response = array(
					"success" => true,
					"message" => "Community List Data fetched successfully",
					"communityData" => $data
				);
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "Given question_id is not exist",
					"communityData" => array()
				);
			}
		}

		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function myStreamDateApi_post()
	{
		$comments_data = $this->db->get("FM_community_comments")->result();
		if(count($comments_data)>0)
		{
			for($i=0; $i<count($comments_data); $i++)
			{
				$comment_date = array(
					"id" => $comments_data[$i]->id,
					"date" => $comments_data[$i]->created_date
				);

				$comments_date_list[] = $comment_date;
			}

			$response = array(
				"success" => true,
				"message" => "Comments Date List fetched successfully",
				"listOfDates" => $comments_date_list
			);
		}
		else
		{
			$response = array(
				"success" => false,
				"message" => "Failed to fetch Comments Date List",
				"listOfDates" => array()
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function myStreamOnLoadAnswerApi_post()
	{
		$missingParam = array();

		if($this->input->post("question_id")==null && !isset($_POST["question_id"]))
		{
			$missingParam[] = "question_id";
		}
		else
		{
			$question_id = $this->input->post("question_id");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"communityData" => array()
			);
		}
		else
		{
			$answer_data = $this->db->get_where("FM_community_comments",array("community_id"=>$question_id))->result();
    		if(count($answer_data)>0)
    		{
    			for($i=0; $i<count($answer_data); $i++)
    			{
    				$answer_id = $answer_data[$i]->id;
    				$customer_id = $answer_data[$i]->customer_id;
    				$customer_data = $this->db->get_where("FM_customer",array("id"=>$customer_id))->result();
    				if(count($customer_data)>0)
    				{
    					$personName = $customer_data[0]->first_name." ".$customer_data[0]->last_name;
    				}
    				else
    				{
    					$personName = "";
    				}

    				if($answer_data[$i]->testing=="")
    				{
    					$basepath_image_url = $this->db->select("content")
									   ->from("FM_preferences")
									   ->where("name","base_image_url")
									   ->get()->result()[0]->content;
    				}
    				else
    				{
    					$basepath_image_url = "https://testing.surobhiagro.in/api/v2/assets/";
    				}

    				if($answer_data[$i]->image!="")
    				{
    					$attachment = $basepath_image_url.$answer_data[$i]->image;
    				}
    				else
    				{
    					$attachment = "";
    				}

    				$answer = array(
    					"id" => $answer_id,
    					"personName" => $personName,
    					"date" => intval(strtotime($answer_data[$i]->created_date) * 1000),
    					"comment" => $answer_data[$i]->comments,
    					"attachment" => $attachment
    				);

    				$answer_list[] = $answer;
    			}

    			$response = array(
    				"success" => true,
    				"message" => "Answer list fetched successfully",
    				"answer" => $answer_list
    			);
    		}
    		else
    		{
    			$response = array(
    				"success" => false,
    				"message" => "Failed to fetch answer list",
    				"answer" => array()
    			);
    		}
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function videoLikeDislikeApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null || !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("video_id")==null || !isset($_POST["video_id"]))
		{
			$missingParam[] = "video_id";
		}
		else
		{
			$video_id = $this->input->post("video_id");
		}

		if($this->input->post("like_value")==null || !isset($_POST["like_value"]))
		{
			$missingParam[] = "like_value";
		}
		else
		{
			$like_value = $this->input->post("like_value");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"communityData" => array()
			);
			$status = false;
		}
		else
		{
			if($like_value=="1")
			{
				$condArr = array("customer_id"=>$user_id, "video_id"=>$video_id);
				$like_data = $this->db->get_where("FM_video_likes",$condArr)->result();
				if(count($like_data)>0)
				{
					$updateDataArr = array(
						"is_deleted" => "N",
						"sign" => "",
						"testing" => "Development_".date("F_Y")
					);
					$condArr2 = array("customer_id"=>$user_id, "video_id"=>$video_id);
					$this->db->set($updateDataArr);
					$this->db->where($condArr2);
					$this->db->update('FM_video_likes');
					$status = true;
				}
				else
				{
					$insertDataArr = array(
						"customer_id" => $user_id,
						"video_id" => $video_id,
						"testing" => "Development_".date("F_Y")
					);

					$this->db->insert("FM_video_likes",$insertDataArr);
					$status = true;
				}
			}
			else if($like_value=="0")
			{
				$condArr = array("customer_id"=>$user_id, "video_id"=>$video_id);
				$like_data = $this->db->get_where("FM_video_likes",$condArr)->result();
				if(count($like_data)>0)
				{
					$updateDataArr = array(
						"is_deleted" => "Y",
						"sign" => "",
						"testing" => "Development_".date("F_Y")
					);
					$condArr2 = array("customer_id"=>$user_id, "video_id"=>$video_id);
					$this->db->set($updateDataArr);
					$this->db->where($condArr2);
					$this->db->update('FM_video_likes');
					$status = true;
				}
				else
				{
					$insertDataArr = array(
						"is_deleted" => "Y",
						"sign" => "",
						"testing" => "Development_".date("F_Y")
					);

					$this->db->insert("FM_video_likes",$insertDataArr);
					$status = true;
				}
			}
			else if($like_value=="-1")
			{
				$condArr = array("customer_id"=>$user_id, "video_id"=>$video_id);
				$like_data = $this->db->get_where("FM_video_likes",$condArr)->result();
				if(count($like_data)>0)
				{
					$updateDataArr = array(
						"is_deleted" => "Y",
						"sign" => "negative",
						"testing" => "Development_".date("F_Y")
					);
					$condArr2 = array("customer_id"=>$user_id, "video_id"=>$video_id);
					$this->db->set($updateDataArr);
					$this->db->where($condArr2);
					$this->db->update('FM_video_likes');
					$status = true;
				}
				else
				{
					$insertDataArr = array(
						"customer_id" => $user_id,
						"video_id" => $video_id,
						"is_deleted" => "Y",
						"sign" => "nagative",
						"testing" => "Development_".date("F_Y")
					);

					$this->db->insert("FM_video_likes",$insertDataArr);
					$status = true;
				}
			}

			$success_response = array(
				"success" => true,
				"message" => "Add Like Details Successfully",
				"isUpdated" => true
			);
		}

		if($status)
		{
			$this->response($success_response,REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response($response,REST_Controller::HTTP_OK);
		}	
	}

    // COMMON MOBILE SMS SENDING FUNCTION //
    function send_sms($phone_number, $otp_number)
    {
        // Account details
        $authkey = '335354AMUyfpp0uQ5f097111P1';

        // Template Details
        $template_id = '5facdebebfdb4a7b0a2c7d25';
     
     	// Mobile Number
        $numbers = $phone_number;

        // OTP Number
        $otp = $otp_number;

        $url = "https://api.msg91.com/api/v5/otp?authkey=".$authkey."&template_id=".$template_id."&mobile=".$numbers."&otp=".$otp;
		$curl = curl_init();
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));
		$response = curl_exec($curl);
		curl_close($curl);

        return $response;
    }

    function send_otp_to_phone($phone=null, $otp=null)
    { 
    	if($phone!=null && $otp!=null)
    	{
    		return $this->send_sms($phone, $otp);
    	}
    	else
    	{
    		return false;
    	}
    }

	// COMMON EMAIL SENDING FUNCTION //
	function email_send($send_to, $subject, $body)
    {
        $result = $this->email
            		   ->from(FROM_EMAIL, 'Farmology')
            		   ->to($send_to)
            		   ->subject($subject)
            		   ->message($body)
            		   ->send();
		
		return $result;
    }

    function send_otp_to_email($email=null, $otp=null)
  	{
	    if($email!=null && $otp!=null)
	    {
	      $subject = "Verify Login OTP - Farmology.com";
	      $body = "<p>Your Farmology Login OTP is <b>".$otp."</b>.<br>Do not share with anyone.</p>";
	      return $this->email_send($email, $subject, $body);
	    }
	    else
	    {
	      return false;
	    }
  	}

	public function loginApi_post()
	{
		$email_sending_status = "";
		$missingParam = array();

		if($this->input->post("user_mobile_or_email")==null || !isset($_POST["user_mobile_or_email"]))
		{
			$missingParam[] = "user_mobile_or_email";
		}
		else
		{
			$user_mobile_or_email = $this->input->post("user_mobile_or_email");
		}

		if(count($missingParam)>0)
		{
			$response = array(
				"success" => false,
				"message" => $missingParam[0]." is not given",
				"userOtp" => (object)array()
			);
		}
		else
		{
			$user_mobile_or_email = filter_var($user_mobile_or_email, FILTER_SANITIZE_EMAIL);

			if (filter_var($user_mobile_or_email, FILTER_VALIDATE_EMAIL))
			{
			  	$is_email = true;
				$email = $user_mobile_or_email;
			}
			else
			{
				$is_email = false;
			  	$phone = $user_mobile_or_email;
			}

			$random_otp_number = mt_rand(1000,9999);

			if($is_email)
			{
				$insertDataArr = array(
					"otp" => $random_otp_number,
					"email" => $email,
					"is_expired" => "N",
					"created_date" => date("Y-m-d H:i:s")
				);

				$this->db->insert("FM_email_otp_list", $insertDataArr);
				$id = $this->db->insert_id();
				$email_sending_status = $this->send_otp_to_email($email, $random_otp_number);
				$otp = $random_otp_number;
				$otp_source = "email";
				$message = "OTP send to your email successfully";
			}
			else
			{
				$insertDataArr = array(
					"otp" => $random_otp_number,
					"phone" => $phone,
					"is_expired" => "N",
					"created_date" => date("Y-m-d H:i:s")
				);

				$this->db->insert("FM_phone_otp_list", $insertDataArr);
				$id = $this->db->insert_id();
				$this->send_otp_to_phone($phone, $random_otp_number);
				$otp = $random_otp_number;
				$otp_source = "phone";
				$message = "OTP send to your mobile number successfully";
			}

			$response = array(
				"success" => true,
				"message" => $message,
				"userOtp" => (object)array(
					"id" => strval($id),
					"otp" => $otp,
					"otp_source" => $otp_source,
					"email_sending_status" => $email_sending_status
				)
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function otpApi_post()
	{
		$missingParam = array();

		if($this->input->post("otp_id")==null || !isset($_POST["otp_id"]))
		{
			$missingParam[] = "otp_id";
		}
		else
		{
			$otp_id = $this->input->post("otp_id");
		}

		if($this->input->post("otp")==null || !isset($_POST["otp"]))
		{
			$missingParam[] = "otp";
		}
		else
		{
			$otp = $this->input->post("otp");
		}

		if($this->input->post("otp_source")==null || !isset($_POST["otp_source"]))
		{
			$missingParam[] = "otp_source";
		}
		else
		{
			$otp_source = $this->input->post("otp_source");
		}

		if($this->input->post("refferal_code")==null || !isset($_POST["refferal_code"]))
		{
			$refferal_code = null;
		}
		else
		{
			$refferal_code = $this->input->post("refferal_code");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"isMatched" => false
			);
		}
		else
		{
			if($otp_source=="email" || $otp_source=="phone")
			{
				$condArr = array("is_expired"=>"N", "id"=>$otp_id, "otp"=>$otp);
				$otp_source_table = "FM_".$otp_source."_otp_list";
				$user_otp_data = $this->db->get_where($otp_source_table, $condArr)->result();
				if(count($user_otp_data)>0)
				{
					$this->db->set("is_expired","Y");
					$this->db->where($condArr);
					$this->db->update($otp_source_table);

					$response = array(
						"success" => true,
						"message" => "User OTP verified successfully",
						"isMatched" => true
					);
				}
				else
				{
					$response = array(
						"success" => true,
						"message" => "Invalid OTP Number",
						"isMatched" => false
					);
				}
			}
			else
			{
				$response = array(
					"success" => true,
					"message" => "Invalid OTP Source",
					"isMatched" => false
				);
			}
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function customerInformationApi_post()
	{
		$missingParam = array();


		//check first name
		if($this->input->post("first_name")==null || !isset($_POST["first_name"]))
		{
			$missingParam[] = "first_name";
		}
		else
		{
			$first_name = $this->input->post("first_name");
		}

		//check last name
		if($this->input->post("last_name")==null || !isset($_POST["last_name"]))
		{
			$missingParam[] = "last_name";
		}
		else
		{
			$last_name = $this->input->post("last_name");
		}

		//check mobile or email
		if($this->input->post("mobile_number_or_email")==null || !isset($_POST["mobile_number_or_email"]))
		{
			$missingParam[] = "mobile_number_or_email";
		}
		else
		{
			$mobile_number_or_email = $this->input->post("mobile_number_or_email");
		}

		//check referral code
		if($this->input->post("referral_code")==null || !isset($_POST["referral_code"]))
		{
			// $missingParam[] = "refferal_code";
		}
		else
		{
			$referral_code = $this->input->post("referral_code");
		}


		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"userId" => intval("")
			);
		}
		else
		{
			if (!filter_var($mobile_number_or_email, FILTER_VALIDATE_EMAIL))
			{
			  	$phone = $mobile_number_or_email;
			}
			else
			{
				$email = $mobile_number_or_email;
			}

			if (isset($referral_code) && $referral_code != null && $referral_code != '') {
				$referred_by = $this->db->select('id')->from('FM_customer')->where('owned_referral_code', $referral_code)->get()->row()->id;
			}
			else{
				$referred_by = null;
			}

			$data = [
				'first_name'					=> $first_name,
				'last_name'						=> $last_name,
				'email'							=> (isset($email)) ? $email : null,
				'phone'							=> (isset($phone)) ? $phone : null,
				'referral_by'					=> (isset($referred_by)) ? $referred_by : null,
				'status'						=> 'Y',
				'created_date'					=> date('Y-m-d h:i:s'),
				'registered_with_referral_code' => (isset($referral_code)) ? $referral_code : null,
				'type'							=> 'user'
			];

			$this->db->insert('FM_customer', $data);
			$id = $this->db->insert_id();
			$update_arr = array('owned_referral_code'=> $this->getReferralCode($id));
			$this->db->set($update_arr);
			$this->db->where('id', $id);
			$this->db->update('FM_customer');

			$user_id = $id;
			if($referred_by != null)
			{
				

				$this->appendReward($user_id, 'referred', $referred_by);

				
			}
			$response = array(
				"success" => true,
				"message" => "User saved successfully",
				"userId" => intval($user_id) 
			);

		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	//generate referral code for each user

	public function getReferralCode($user_id)
	{
		$pass = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
		return $pass.$user_id;
	}

	public function communitySortingApi_post()
	{
		$values_list = array(
			array(
				"id" => "1",
				"sortValue" => "Sort by Date"
			),
			array(
				"id" => "2",
				"sortValue" => "Sort by Crop"
			)
		);

		$response = array(
			"success" => true,
			"message" => "Sorting values fetched successfully",
			"listOfValues" => $values_list
		);

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function selectStateApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null || !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("selected_state")==null || !isset($_POST["selected_state"]))
		{
			$missingParam[] = "selected_state";
		}
		else
		{
			$selected_state = $this->input->post("selected_state");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"isSubmited" => false
			);
		}
		else
		{
			$condArr = array("state"=>$selected_state);
			$selected_state_data = $this->db->get_where("FM_state_lookup",$condArr)->result();
			if(count($selected_state_data)>0)
			{
				$selected_state_id = $selected_state_data[0]->id;
				$condArr2 = array("status"=>"Y", "id"=>$user_id);
				$user_data = $this->db->get_where("FM_customer", $condArr2)->result();
				if(count($user_data)>0)
				{
					$this->db->set("state_id", $selected_state_id);
					$this->db->where("id", $user_id);
					$this->db->update("FM_customer");

					$response = array(
						"success" => true,
						"message" => "State updated for customer successfully",
						"isSubmitted" => true
					);
				}
				else
				{
					$response = array(
						"success" => false,
						"message" => "User ID not exist",
						"isSubmited" => false
					);
				}
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "Selected state does not found",
					"isSubmited" => false
				);
			}
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function changeLanguageApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null || !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("selected_language")==null || !isset($_POST["selected_language"]))
		{
			$missingParam[] = "selected_language";
		}
		else
		{
			$selected_language = $this->input->post("selected_language");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"isSubmited" => false
			);
		}
		else
		{
			$condArr = array("status"=>"Y", "id"=>$user_id);
			$user_data = $this->db->get_where("FM_customer", $condArr)->result();
			if(count($user_data)>0)
			{
				$get_selected_language = $this->db->from('FM_languages')->where('id', $selected_language)->get()->row();
				$selected_language = $get_selected_language->language_name;
				$this->db->set("language", substr($selected_language, 0, 1));
				$this->db->where("id", $user_id);
				$this->db->update("FM_customer");

				$response = array(
					"success" => true,
					"message" => "Language updated for customer successfully",
					"isSubmitted" => true,
					"selectedLanguage" => $selected_language
				);
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "User ID not exist",
					"isSubmited" => false,
					"selectedLanguage" => false
				);
			}
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function kycVerificationDocumentApi_post()
	{
		$verification_document_list = $this->db->select("id, document_name")
											   ->from("FM_kyc_verification_documents")
											   ->where("status","Y")
											   ->get()->result();

		if(count($verification_document_list)>0)
		{
			$response = array(
				"success" => true,
				"message" => "KYC Verification Documents List fetched successfully",
				"listOfDocuments" => $verification_document_list
			);
		}
		else
		{
			$response = array(
				"success" => false,
				"message" => "User KYC Verification Documents List not found",
				"listOfDocuments" => array()
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function kycVerificationApi_post()
	{
		$missingParam = array();

		if($this->input->post("user_id")==null || !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$user_id = $this->input->post("user_id");
		}

		if($this->input->post("selected_document")==null || !isset($_POST["selected_document"]))
		{
			$missingParam[] = "selected_document";
		}
		else
		{
			$selected_document = $this->input->post("selected_document");
		}

		if(empty($_FILES['document_image']['name']))
		{
			$missingParam[] = "document_image";
		}
		else
		{
			$upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/kycDocuments/';
            $rand_name = time()."-";
            $upload_file = $upload_dir.$rand_name.basename($_FILES['document_image']['name']);
            $upload_file = str_replace(" ","-",$upload_file);
            $actual_path = 'uploads/kycDocuments/'.$rand_name.basename($_FILES['document_image']['name']);
            $actual_path = str_replace(" ","-",$actual_path);
            if (move_uploaded_file($_FILES['document_image']['tmp_name'], $upload_file))
            {
                $document_image  = $actual_path;
            }
            else
            {
                $document_image = "No Image";
            }

			// $document_image = $this->input->post("document_image");
		}

		if($this->input->post("area_value")==null || !isset($_POST["area_value"]))
		{
			$missingParam[] = "area_value";
		}
		else
		{
			$area_value = $this->input->post("area_value");
		}

		if($this->input->post("area_unit")==null || !isset($_POST["area_unit"]))
		{
			$missingParam[] = "area_unit";
		}
		else
		{
			$area_unit = $this->input->post("area_unit");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"isSubmited" => false
			);
		}
		else
		{
			$condArr = array("status"=>"Y", "id"=>$user_id);
			$customer_data = $this->db->get_where("FM_customer",$condArr)->result();
			if(count($customer_data)>0)
			{
				$updateDataArr = array(
					"kyc_document_name" => $selected_document,
					"kyc_document_image" => $document_image,
					"area_value" => $area_value,
					"area_unit" => $area_unit
				);

				$this->db->set($updateDataArr);
				$this->db->where($condArr);
				$this->db->update("FM_customer");

				$response = array(
					"success" => true,
					"message" => "User KYC Verification Details Updated Successfully",
					"isSubmitted" => true
				);
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "User ID not exists",
					"isSubmitted" => false
				);
			}
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function helpAndFaqApi_post()
	{
		$condArr = array("status"=>"Y");
		$faq_data = $this->db->get_where("FM_faq", $condArr)->result();
		if(count($faq_data)>0)
		{
			$helpDescription = $faq_data[0]->answer;
			for($i=0; $i<count($faq_data); $i++)
			{
				$faq = array(
					"id" => $faq_data[$i]->id,
					"question" => $faq_data[$i]->question
				);

				$faq_list[] = $faq;
			}

			$helpAndFaqData = array(
				"id" => "1",
				"helpDescription" => $helpDescription,
				"listOfFaqs" => $faq_list
			);

			$response = array(
				"success" => true,
				"message" => "Help and FAQ list fetched successfully",
				"helpAndFaqData" => $helpAndFaqData
			);
		}
		else
		{
			$response = array(
				"success" => false,
				"message" => "Failed to get FAQ List",
				"helpAndFaqData" => array()
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	public function helpAndFaqAnswerApi_post()
	{
		$missingParam = array();

		if($this->input->post("question_id")==null || !isset($_POST["question_id"]))
		{
			$missingParam[] = "question_id";
		}
		else
		{
			$question_id = $this->input->post("question_id");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"faqAnswer" => false
			);
		}
		else
		{
			$condArr = array("status"=>"Y", "id"=>$question_id);
			$faq_answer_data = $this->db->select("id, answer")
										->from("FM_faq")
										->where($condArr)
										->get()->row();

			$response = array(
				"success" => true,
				"message" => "FAQ Answer fetched successfully",
				"faqAnswer" => $faq_answer_data
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}


/*
*
*Author : Rajdeep Adhikary
*
*/



	public function checkOutCustomerDetailsApi_post($value='')
	{

		$missingParam = array();
		if($this->input->post("user_id")==null || !isset($_POST["user_id"]))
		{
			$missingParam[] = "user_id";
		}
		else
		{
			$customer_id = $this->input->post("user_id");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"customerDetails" => false
			);
		}
		else
		{
			$result = $this->db->query("SELECT FM_customer.id,FM_customer.first_name, FM_customer.last_name, FM_customer_address.address_1, FM_customer_address.zip_code, FM_city_lookup.name FROM FM_customer LEFT JOIN FM_customer_address ON FM_customer.id = FM_customer_address.customer_id LEFT JOIN FM_city_lookup ON FM_customer_address.city_id = FM_city_lookup.id WHERE FM_customer.id = '$customer_id'")->row();

			if ($result != null) {
				$data = [
					'id' 					=> $result->id,
					'customerName' 			=> $result->first_name . ' ' . $result->last_name,
					// 'customerAddress' 		=> $result->address_1 . ' ' . $result->name . '-' . $result->zip_code ,
					'customerAddress' 		=> $result->address_1,
					'estimateDeliveryDate' 	=> date('d M, Y', strtotime("+2 days"))
				];
				$response = array(
					"success" => true,
					"message" => 'customerDetails fetch successfully',
					"customerDetails" => $data
				);
			}
			else{
				$response = array(
					"success" => true,
					"message" => 'No data found for this customer id',
					"customerDetails" => null
				);
			}
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	function checkoutOrderSummary_post()
    {
        $response_status = FALSE;
        $response_message = "Something was wrong."; 

        try{

            $missing_key = array();

            // check unique_id
            if($this->input->post('user_id') == null)
            {
                $missing_key[] = 'user_id';
            }    
            else
            {
                $unique_id = $this->input->post("user_id");
            }

            //check promo code
            if($this->input->post('promo_code') == null)
            {
                // $missing_key[] = 'user_id';
            }    
            else
            {
                $promo_code = $this->input->post("promo_code");
            }

            //check order id
            if($this->input->post('order_id') == null)
            {
                // $missing_key[] = 'order_id';
            }    
            else
            {
                $order_id = $this->input->post("order_id");
            }


            if(count($missing_key) == 0)
            {

            	if (!isset($promo_code)) {
            		$promo_code = null;
            	}
            	$cart_details = $this->getCartList($unique_id, $promo_code);
            	if (isset($order_id)) {
            		$order_id = substr($order_id, 1);
            		$cart_details = $this->getOrderDetails($order_id);
            	}

                $response_arr = array("status" => TRUE, "message" => "Successful.", "response" => $cart_details);
                $this->response($response_arr, REST_Controller::HTTP_OK);
            }            
            else
            {
                $implode_missing_key = implode(', ', $missing_key);
	            $response_message = $implode_missing_key." - key or value missing";

	            $response = array("status" => $response_status, "message" => $response_message);
	            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        }     

        catch (Exception $x)
        {
            $response = array("status" => $response_status, "message" => $response_message);
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

    }

    public function getCartList($user_id, $promo_code = '', $user_type = 'U')
    {
    	//check the cart and calculate current cart items
    	/*
    	*
		* After promocode sorted out this api will get changed and then the promocode will also be caculated. Now the promocode got a static value, 0.
		*
    	*/

    	$cart_row = array();

    	$cart_total = 0;
        $cart_count = 0;
        $promoDiscount = 0; // retrive promocode here.
        $refDiscount = 0;
        $shippingCharge = 0;
        // $gst = $this->db->select('content')->from('FM_preferences')->where('name', 'gst')->get()->row()->content;
        $totalPayable = $cart_total - ($promoDiscount + $refDiscount) + $shippingCharge;

        $this->db->select("*");
        $this->db->from("FM_cart");
        $this->db->where("unique_id", $user_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $quantity = 0;
            foreach($query->result() as $row)
            {
                $variation_id = $row->variation_id;
                $variation_details = $this->get_veriation_full_details_by_id($variation_id);
                if($variation_details['availability_status'] == "Y")
                {
                    $quantity++;
                    $product_total = $variation_details['variation_details']['price_details']['price'] * $row->quantity;
                    $sale_price[] = $product_total;
                   

                    $cart_row[] = array("id" => $row->id, "quantity" => $row->quantity, "details" => $variation_details, "product_total" => round($product_total));
                }
                else
                {
                    $delete_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
                    $this->db->where("id", $row->id);
                    $this->db->update("FM_cart", $delete_data);
                    
                    $sale_price[]  = 0;
                    $product_total = 0;
                    
                }

                

            }

            $cart_total = array_sum($sale_price);
            $cart_count = $quantity;
            $refDiscount =  $this->getRefDiscountAmount($user_id, $cart_total, $user_type);
            $shippingCharge = intval($this->get_delivery_charge_by_city_id($user_id));
            $totalPayable = $cart_total - ($promoDiscount + $refDiscount) + $shippingCharge;

            $response = [
            	"id" => $user_id,
            	"total" => round($cart_total), 
            	"promoDiscount" => $promoDiscount,
            	"refDiscount" => $refDiscount,
            	"shippingCharge" => $shippingCharge,
            	"totalPayable" => round($totalPayable),
            ];


        }
        else
        {
            // no cart

            $response = [
            	"id" => $user_id,
            	"total" => round($cart_total), 
            	"promoDiscount" => $promoDiscount,
            	"refDiscount" => $refDiscount,
            	"shippingCharge" => $shippingCharge,
            	"totalPayable" => round($totalPayable)
            ];
        }

        return $response;
    }

    function get_veriation_full_details_by_id($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_product_variation");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $product_name = $this->get_product_name_by_id($row->product_id);
            $product_image = $this->get_product_image_by_product_id($row->product_id);
            $product_status = $this->get_product_status_by_id($row->product_id);
            $product_details = array("id" => $row->product_id, "name" => $product_name, "image" => $product_image, "status" => $product_status);

            $price = $row->price;
            $discount = $row->discount;
            $discount_amount = $price * $discount / 100;
            $sale_price = $price - $discount_amount;

            $price_details = array("price" => round($price), "discount_percent" => round($discount), "discount_amount" => round($discount_amount), "sale_price" => round($sale_price));

            $variation_details = array("id" => $row->id, "title" => $row->title, "price_details" => $price_details, "status" => $row->status);

            if($product_status == "Y" && $row->status == "Y")
            {
                $availability_status = "Y";
            }
            else
            {
                $availability_status = "N";
            }
            
            $details = array("variation_details" => $variation_details, "product_details" => $product_details, "availability_status" => $availability_status);
            
        }

        return $details;
    }

    function get_product_status_by_id($product_id = 0)
    {
        $status = "D";
        $this->db->select("status");
        $this->db->from("FM_product");
        $this->db->where("id", $product_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $status = $row->status;
        }

        return $status;
    }

    function get_product_name_by_id($product_id = 0)
    {
        $name = "";
        $this->db->select("title");
        $this->db->from("FM_product");
        $this->db->where("id", $product_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $name = $row->title;
        }

        return $name;
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
                $list[] = array("id" => $row->id, "image" => STORE_URL.$row->image);
            }
        }

        return $list;
    }

    // public function checkoutPromoCodeApi_post()
    // {
    // 	$response_status = FALSE;
    //     $response_message = "Something was wrong."; 

    // 	$missing_key = array();

    //     // check unique_id
    //     if($this->input->post('user_id') == null)
    //     {
    //         $missing_key[] = 'user_id';
    //     }    
    //     else
    //     {
    //         $unique_id = $this->input->post("user_id");
    //     }
    //     if($this->input->post('promo_code') == null)
    //     {
    //         $missing_key[] = 'promo_code';
    //     }    
    //     else
    //     {
    //         $promo_code = $this->input->post("promo_code");
    //     }

    //     if(count($missing_key) == 0)
    //     {
    //         $cart_details = $this->getCartList($unique_id);

    //         $response_arr = array("status" => TRUE, "message" => "Data found.", "response" => $cart_details);
    //         $this->response($response_arr, REST_Controller::HTTP_OK);
    //     }            
    //     else
    //     {
    //         $implode_missing_key = implode(', ', $missing_key);
    //         $response_message = $implode_missing_key." - key or value missing";

    //         $response = array("status" => $response_status, "message" => $response_message);
    //         $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    //     }
    // }

    public function checkoutProductsApi_post()
    {
    	$response_status = FALSE;
        $response_message = "Something was wrong."; 

    	$missing_key = array();

        // check unique_id
        if($this->input->post('user_id') == null)
        {
            $missing_key[] = 'user_id';
        }    
        else
        {
            $unique_id = $this->input->post("user_id");
        }

        if(count($missing_key) == 0)
        {
            $cart_details = $this->getCartItemsList($unique_id);
            if (count($cart_details['cart']) > 0) {
				$response_arr = array("status" => TRUE, "message" => "Products found.", "listOfProducts" => $cart_details['cart']);            	
            }
            else{
            	$response_arr = array("status" => TRUE, "message" => "Products not found.", "listOfProducts" => $cart_details['cart']);
            }

            $this->response($response_arr, REST_Controller::HTTP_OK);
        }            
        else
        {
            $implode_missing_key = implode(', ', $missing_key);
            $response_message = $implode_missing_key." - key or value missing";

            $response = array("status" => $response_status, "message" => $response_message);
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function getCartItemsList($user_id)
    {
        $cart_row = array();

        $this->db->select("*");
        $this->db->from("FM_cart");
        $this->db->where("user_type", 'customer');
        $this->db->where("unique_id", $user_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $quantity = 0;
            foreach($query->result() as $row)
            {
                $variation_id = $row->variation_id;
                $variation_details = $this->get_veriation_full_details_by_id($variation_id);
                if($variation_details['availability_status'] == "Y")
                {
                    $quantity++;
                    $product_total = $variation_details['variation_details']['price_details']['price'] * $row->quantity;
                    $sale_price[] = $product_total;
                   

                    // $cart_row[] = array("id" => $row->id, "quantity" => $row->quantity, "details" => $variation_details, "product_total" => round($product_total));

                    $cart_row[] = array(
                    	"id" => $row->id, 
                    	"productName" => $variation_details['product_details']['name'],
                    	"productImage" => $variation_details['product_details']['image'][0]['image'],
                    	"productPrice" => $variation_details['variation_details']['price_details']['price'], 
                    	"productQuantity" => $row->quantity, 
                    	"totalPrice" => round($product_total));
                }
                else
                {
                    $delete_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
                    $this->db->where("id", $row->id);
                    $this->db->update("FM_cart", $delete_data);
                    
                    $sale_price[]  = 0;
                    $product_total = 0;
                    
                }   

                

            }

            $cart_total = array_sum($sale_price);
            $cart_count = $quantity;

            $response = [
            	'total' => $cart_total,
            	'cart' => $cart_row
            ];



        }
        else
        {
            // no cart

            $response = [
            	'total' => 0,
            	'cart' => $cart_row
            ];
        }

        return $response;
    }

    // public function paymentModeApi_post()
    // {
    // 	$response_status = FALSE;
    //     $response_message = "Something was wrong."; 

    // 	$missing_key = array();

    //     // check unique_id
    //     if($this->input->post('user_id') == null)
    //     {
    //         $missing_key[] = 'user_id';
    //     }    
    //     else
    //     {
    //         $user_id = $this->input->post("user_id");
    //     }
    //     if($this->input->post('mode') == null)
    //     {
    //         $missing_key[] = 'mode';
    //     }    
    //     else
    //     {
    //         $mode = $this->input->post("mode");
    //     }

    //     if(count($missing_key) == 0)
    //     {
    //     	// No implementation. Sending static response


    //         $response_arr = array("success" => TRUE, "message" => "Mode Saved", "isSubmitted" => true);
    //         $this->response($response_arr, REST_Controller::HTTP_OK);
    //     }            
    //     else
    //     {
    //         $implode_missing_key = implode(', ', $missing_key);
    //         $response_message = $implode_missing_key." - key or value missing";

    //         $response = array("success" => $response_status, "message" => $response_message);
    //         $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    //     }
    // }

    public function orderHistoryApi_post()
    {
    	$response_status = FALSE;
        $response_message = "Something was wrong."; 

    	$missing_key = array();

        // check unique_id
        if($this->input->post('user_id') == null)
        {
            $missing_key[] = 'user_id';
        }    
        else
        {
            $user_id = $this->input->post("user_id");
        }

        if(count($missing_key) == 0)
        {
        	// No implementation. Sending static response
        	$list_order_history = $this->getListOfOrderHistory($user_id);

        	if (count($list_order_history) > 0) {
        		$response_arr = array("success" => TRUE, "message" => "Order History Retrived Successfully", "listOfOrderHistory" => $list_order_history);	
        	}
        	else{
        		$response_arr = array("success" => TRUE, "message" => "No orders", "listOfOrderHistory" => $list_order_history);
        	}

            
            $this->response($response_arr, REST_Controller::HTTP_OK);
        }            
        else
        {
            $implode_missing_key = implode(', ', $missing_key);
            $response_message = $implode_missing_key." - not found";

            $response = array("success" => $response_status, "message" => $response_message);
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function getListOfOrderHistory($user_id='')
    {
    	$order_list = [];

    	$customerDetails = $this->db->query("SELECT FM_customer.id,FM_customer.first_name, FM_customer.last_name, FM_customer_address.address_1, FM_customer_address.zip_code, FM_city_lookup.name FROM FM_customer LEFT JOIN FM_customer_address ON FM_customer.id = FM_customer_address.customer_id LEFT JOIN FM_city_lookup ON FM_customer_address.city_id = FM_city_lookup.id WHERE FM_customer.id = '$user_id'")->row();

    	$order_history = $this->db->query("SELECT FM_order.id, (SELECT COUNT(FM_order_details.id) FROM FM_order_details WHERE FM_order_details.order_id = FM_order.id) as item, FM_order.order_total as totalAmount, FM_order.payment_method, FM_order.order_no, FM_order.delivery_date, FM_order.status, FM_order.created_date FROM FM_order WHERE FM_order.customer_id = '$user_id' AND FM_order.status != 'C'")->result();

    	foreach ($order_history as $order) {
    		$isDelivered = ($order->status == 'D') ? true : false;
    		$paymentMode = ($isDelivered) ? "PAID" : strtoupper($order->payment_method);
    		if ($order->status == 'P') {
    			$status = 'Processing';
    		}
    		elseif ($order->status == 'C') {
    			$status = 'Cancelled';
    		}
    		else{
    			$status = 'Delivered';
    		}

    		$order_list[] = [
    			'id' 					=> $order->id,
    			'customerName' 			=> $customerDetails->first_name . ' ' . $customerDetails->last_name,
    			'customerAddress' 		=> $customerDetails->address_1 . ' ' . $customerDetails->name . '-' . $customerDetails->zip_code,
    			'item'					=> '('.$order->item.')',
    			'totalAmount'			=> '₹'.$order->totalAmount. '/-',
    			'paymentMode'			=> $paymentMode,
    			'orderId'				=> '#'.$order->order_no,
    			'orderDate'				=> date('d M Y', strtotime($order->created_date)),
    			'deliveryDate'			=> date('d M Y', strtotime($order->delivery_date)),
    			'isDelivered'			=> $isDelivered,
    			'processingOrDelivered'	=> $status
    		];
    	}

    	return $order_list;
    }

    public function paymentGatewayDetails_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}   
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			//processes to do if everything is fine
			$amount = $this->getCartList($user_id, null)['totalPayable'];

			if ($amount == 0) {
				$amount = 1;
			}

			$prefill = $this->getUserContacts($user_id);
			$prefill->email = ($prefill->email == null) ? 'None' : $prefill->email;
			$prefill->contact = ($prefill->contact == null) ?'None' : $prefill->contact;
			$details = [
				'name'	=> 'Farmology',
				'description' => 'Farmology Order',
				'image' => 'https://s3.amazonaws.com/rzp-mobile/images/rzp.png',
				'currency' => 'INR',
				'amount' => floatval($amount * 100),
				'prefill' => $prefill
			];


		    $response_arr = array("success" => TRUE, "message" => "Cart details fetched Successfully", "data" => $details);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getUserContacts($user_id)
    {
    	return $this->db->select('email, phone as contact')->from('FM_customer')->where('id', $user_id)->where('status', 'Y')->get()->row();
    }

    public function getOrderDetails($order_id)
    {
    	$id = 0000;
    	$total = 0;
        $promoDiscount = 0; 
        $refDiscount = 0;
        $shippingCharge = 0;
        $gst = $this->db->select('content')->from('FM_preferences')->where('name', 'gst')->get()->row()->content;
        $totalPayable = 0;

        if ($order_id != null) {
        	$order_details = $this->db->query("SELECT FM_order.id, FM_order.total_price, FM_order.delivery_charge, FM_order.order_total FROM `FM_order` WHERE order_no = '$order_id'")->row();

        	if ($order_details != null) {
        		$id = $order_details->id;
	        	$shippingCharge = $order_details->delivery_charge;
	        	$total = $order_details->total_price;
	        	$totalPayable = $order_details->order_total;
        	}

        }

        $totalPayable = ($total + $shippingCharge) - ($promoDiscount + $refDiscount);

        $response = [
        	'id'	=> $id,
        	'total'	=> (float)$total - ($total * $gst/100),
        	"promoDiscount" => floatval($promoDiscount),
        	"refDiscount" => (float)$refDiscount,
        	"shippingCharge" => (float)$shippingCharge,
        	"gst" => (float)round(($total * $gst/100)),
        	"totalPayable" => (float)round($totalPayable),
        ];

        return $response;
    	
    }

    public function userProductHistoryApi_post()
    {
    	$response_status = FALSE;
        $response_message = "Something was wrong."; 

    	$missing_key = array();

        // check unique_id
        if($this->input->post('user_id') == null)
        {
            $missing_key[] = 'user_id';
        }    
        else
        {
            $unique_id = $this->input->post("user_id");
        }

        if(count($missing_key) == 0)
        {
            $cart_details = $this->getPreviousOrderedProducts($unique_id);
            if (count($cart_details) > 0) {
				$response_arr = array("success" => TRUE, "message" => "Products found.", "listOfProducts" => $cart_details);            	
            }
            else{
            	$response_arr = array("success" => TRUE, "message" => "No products found.", "listOfProducts" => $cart_details);
            }

            $this->response($response_arr, REST_Controller::HTTP_OK);
        }            
        else
        {
            $implode_missing_key = implode(', ', $missing_key);
            $response_message = $implode_missing_key." - not found";

            $response = array("status" => $response_status, "message" => $response_message);
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function getPreviousOrderedProducts($user_id)
    {
    	$cart_row = array();

        
        $query = $this->db->query("SELECT FM_order_details.product_id, FM_order_details.variation_id, FM_order_details.quantity FROM `FM_order` INNER JOIN FM_order_details ON FM_order.id = FM_order_details.order_id WHERE FM_order.customer_id = '$user_id'")->result();
        if(count($query) > 0)
        {
            $quantity = 0;
            foreach($query as $row)
            {
                $variation_id = $row->variation_id;
                $variation_details = $this->get_veriation_full_details_by_id($variation_id);
                if($variation_details['availability_status'] == "Y")
                {
                    $quantity++;
                    $product_total = $variation_details['variation_details']['price_details']['sale_price'] * $row->quantity;
                    $sale_price[] = $product_total;
                   

                    // $cart_row[] = array("id" => $row->id, "quantity" => $row->quantity, "details" => $variation_details, "product_total" => round($product_total));

                    $cart_row[] = array(
                    	"id" => $row->product_id, 
                    	"productName" => $variation_details['product_details']['name'],
                    	"productImage" => $variation_details['product_details']['image'][0]['image'],
                    	"productPriceWithQuantity" => $variation_details['variation_details']['price_details']['sale_price'] . ' x ' . $row->quantity, 
                    	"totalPrice" => '₹'.(float)round($product_total));
                }                

            }

            $cart_total = array_sum($sale_price);
            $cart_count = $quantity;

            $response = $cart_row;



        }
        else
        {
            // no cart

            $response = $cart_row;
        }

        return $response;
    }

    public function onProductFavoriteClickApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong.";

		$missing_key = array();
        // check variation_id
        if($this->input->post('variation_id') == null && $this->input->post('product_id') == null)
        {
            $missing_key[] = 'variation_id or Product_id';
        }    
        else
        {
            if ($this->input->post('variation_id') == null) {
            	$variation_id = $this->get_first_variation_of_product($this->input->post('product_id'));
            }
            else{
            	$variation_id = $this->input->post('variation_id');
            }
        }




        $user_type = 'customer';

        // check unique_id
        if($this->input->post('user_id') == null)
        {
            $missing_key[] = 'user_id';
        }    
        else
        {
            $user_id = $this->input->post("user_id");
        }

        if(count($missing_key) == 0)
        {
            $response = array("status" => "N", "message" => "Something is wrong");
            if($user_type == 'customer')
            {
                if($variation_id > 0)
                {
                    // process to wishlist add
                    $quantity = '1';
                    $wish_data = array("user_type" => $user_type, "unique_id" => $user_id, "variation_id" => $variation_id, "quantity" => $quantity);
                    $wish_process_data = $this->add_to_wish($wish_data);
                    
                    $response = $wish_process_data;
                }
            }

            if($response['status'] == "Y")
            {
                // $wish_details = $this->get_wish_list($user_type, $user_id);
                $response_arr = array("success" => TRUE, "message" => $response['message'], "isSubmitted" => true);
                $this->response($response_arr, REST_Controller::HTTP_OK);

            }
            else
            {
                $response_arr = array("status" => FALSE, "message" => $response['message']);
                $this->response($response_arr, REST_Controller::HTTP_OK);
            }
        }
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    function add_to_wish($data)
    {
        $user_type = $data['user_type'];
        $unique_id = $data['unique_id'];
        $variation_id = $data['variation_id'];
        $quantity = $data['quantity'];

        $variation_details = $this->get_veriation_full_details_by_id($variation_id);
        
        $product_name = (count($variation_details) > 0) ? $variation_details['product_details']['name'] : null; 

        // check variation exist or not 
        $this->db->select("id, quantity");
        $this->db->from("FM_wish");
        $this->db->where("variation_id", $variation_id);
        $this->db->where("user_type", $user_type);
        $this->db->where("unique_id", $unique_id);
        $this->db->where("is_deleted", "N");
        $check_var_query = $this->db->get();
        if($check_var_query->num_rows() > 0)
        {
            $found_var_row = $check_var_query->row();
            $quantity = $found_var_row->quantity;

            $this->db->where("id", $found_var_row->id);
            $update_data = array("quantity" => $quantity, "updated_date" => date("Y-m-d H:i:s"), "testing" => "Development_".date("F_Y"));
            $this->db->update("FM_wish", $update_data);

            

            $response = array("status" => "Y", "message" => "Product successfully added to wishlist.", "product_name" => $product_name);
        }
        else
        {
            $insert_data = array("variation_id" => $variation_id, "quantity" => $quantity, "user_type" => $user_type, "unique_id" => $unique_id, "created_date" => date("Y-m-d H:i:s"), "is_deleted" => "N", "testing" => "Development_".date("F_Y"));
            $this->db->insert("FM_wish", $insert_data);
            //echo $this->db->last_query(); exit;
            $response = array("status" => "Y", "message" => "Product successfully added to wishlist.", "product_name" => $product_name);
        }

        return $response;
    }

    function get_wish_list($user_type, $unique_id){
    	$wish_row = array();

        $this->db->select("*");
        $this->db->from("FM_wish");
        $this->db->where("user_type", $user_type);
        $this->db->where("unique_id", $unique_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $quantity = 0;
            foreach($query->result() as $row)
            {
                $variation_id = $row->variation_id;
                $variation_details = $this->get_veriation_full_details_by_unique_id($variation_id,$unique_id);
                if($variation_details['availability_status'] == "Y")
                {
                    $quantity++;
                    $product_total = $variation_details['variation_details']['price_details']['sale_price'] * $row->quantity;
                    $sale_price[] = $product_total;
                   

                    $wish_row[] = array("id" => $row->id, "quantity" => $row->quantity, "details" => $variation_details, "product_total" => round($product_total));
                }
                else
                {
                    $delete_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
                    $this->db->where("id", $row->id);
                    $this->db->update("FM_wish", $delete_data);
                    
                    $sale_price[]  = 0;
                    $product_total = 0;
                    
                }   

                

            }

            $wish_total = array_sum($sale_price);
            $wish_count = $quantity;

            foreach ($wish_row as $wrow) {
            	$variations[] = [
            		'id'					=> $wrow['details']['variation_details']['id'],
            		'title'					=> $wrow['details']['variation_details']['title'],
            		'price'					=> $wrow['details']['variation_details']['price_details']['price'],
            		'discount_percent'		=> $wrow['details']['variation_details']['price_details']['discount_percent'],
            		'discount_amount'		=> $wrow['details']['variation_details']['price_details']['discount_amount'],
            		'sale_price'			=> $wrow['details']['variation_details']['price_details']['price'],
            		'order'					=>'0',
            		'status'				=> ($wrow['details']['variation_details']['status'] == 'Y') ? true : false,
            		'wish_status'			=> $wrow['details']['variation_details']['wish_status'],
            		'limit'					=> $wrow['details']['variation_details']['limit'],
            	];
            }

            foreach ($wish_row as $wrow) {
            	
            	$wish_response[] = [
	            	'id'					=> $wrow['details']['product_details']['id'],
	            	'name'					=> $wrow['details']['product_details']['name'],
	            	'SKU'					=> $wrow['details']['product_details']['product_SKU'],
	            	'image_list'			=> $wrow['details']['product_details']['image'],
	            	'variation_list'		=> $variations,
	            	'title'					=> $wrow['details']['product_details']['name'],
	            	'description'			=> $wrow['details']['product_details']['product_description'],
	            	'variation_title'		=> $wrow['details']['variation_details']['title'],
	            	'price'					=> $wrow['details']['variation_details']['price_details']['price'],
	            	'discount_percent'		=> $wrow['details']['variation_details']['price_details']['discount_percent'],
	        		'discount_amount'		=> $wrow['details']['variation_details']['price_details']['discount_amount'],
	        		'sale_price'			=> $wrow['details']['variation_details']['price_details']['price'],
	        		'order'					=> '0',
	        		'status'				=> ($wrow['details']['variation_details']['status'] == 'Y') ? true : false,
	        		'wish_status'			=> $wrow['details']['variation_details']['wish_status'],
	        		'items_total'			=> $wish_count,
	        		'order_total'			=> $wish_total

	            ];

            }


            $response = $wish_response;



        }
        else
        {
            // no wish

            $response = $wish_row;
        }

        return $response;
    }

    function get_veriation_full_details_by_unique_id($id = 0,$unique_id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_product_variation");
        $this->db->where("id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $variation_id_array = $this->get_variationid_added_to_wishlist($row->id,$unique_id);
                if(count($variation_id_array) > 0){
                    foreach($variation_id_array as $k => $val){
                        $variation_id [] = $val['variation_id'];
                    }
                }else{
                    $variation_id = array();
                }
            $product_name = $this->get_product_name_by_id($row->product_id);
            $product = $this->get_product_by_id($row->product_id);
            $product_SKU = ($product != null) ? $product->SKU : null;
            $product_description = ($product != null) ? $product->description : null;
            $product_image = $this->get_product_image_by_product_id($row->product_id);
            $product_status = $this->get_product_status_by_id($row->product_id);
            $product_details = array("id" => $row->product_id, "name" => $product_name, "product_SKU"=> $product_SKU, "product_description" => $product_description , "image" => $product_image, "status" => $product_status);

            $price = $row->price;
            $discount = 0;
            // $discount_amount = $price * $discount / 100;
            // $sale_price = $price - $discount_amount;

            $discount_amount = 0;
            $sale_price = $price;
            $limit = $this->getLimitOfProducts($row->id);

            $price_details = array("price" => round($price), "discount_percent" => round($discount), "discount_amount" => round($discount_amount), "sale_price" => round($sale_price));

            $variation_details = array("id" => $row->id, "title" => $row->title, "price_details" => $price_details, "status" => $row->status, "wish_status" => (in_array($row->id, $variation_id)?'Y':'N'), "limit" => $limit);

            if($product_status == "Y" && $row->status == "Y")
            {
                $availability_status = "Y";
            }
            else
            {
                $availability_status = "N";
            }
            
            $details = array("variation_details" => $variation_details, "product_details" => $product_details, "availability_status" => $availability_status);
            
        }

        return $details;
    }

    function get_variationid_added_to_wishlist($variation_id,$customer_id){
        $list = array();
        $this->db->select("variation_id");
        $this->db->from("FM_wish");
        $this->db->where("variation_id", $variation_id);
        $this->db->where("unique_id", $customer_id);
        $this->db->where("is_deleted", 'N');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                
                $list[]     = array(
                    "variation_id" => $row->variation_id
                );
            }
        }
        return $list;
    }


    function get_veriation_full_details($id = 0)
    {
        $details = array();
        $this->db->select("*");
        $this->db->from("FM_product_variation");
        $this->db->where("product_id", $id);
        $this->db->where('status!=', 'D');
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
        	$result = $query->result();
            $row = $result[0];
            $variation_id = array();
            $product_name = $this->get_product_name_by_id($row->product_id);
            $product = $this->get_product_by_id($row->product_id);
            $product_SKU = ($product != null) ? $product->SKU : null;
            $product_description = ($product != null) ? $product->description : null;
            $product_image = $this->get_product_image_by_product_id($row->product_id);
            $product_status = $this->get_product_status_by_id($row->product_id);
            $product_details = array("id" => $row->product_id, "name" => $product_name, "product_SKU"=> $product_SKU, "product_description" => $product_description , "image" => $product_image, "status" => $product_status);

            $price = $row->price;
            $discount = 0;
            // $discount_amount = $price * $discount / 100;
            // $sale_price = $price - $discount_amount;

            $discount_amount = 0;
            $sale_price = $price;
            
            $price_details = array("price" => round($price), "discount_percent" => round($discount), "discount_amount" => round($discount_amount), "sale_price" => round($sale_price));

            foreach ($result as $rows) {
	            $limit = $this->getLimitOfProducts($rows->id);
            	$variation_details[] = array("id" => $rows->id, "title" => $rows->title, "price_details" => $price_details, "status" => $rows->status, "wish_status" => (in_array($rows->id, $variation_id)?'Y':'N'), "limit" => $limit);
            }

            if($product_status == "Y" && $row->status == "Y")
            {
                $availability_status = "Y";
            }
            else
            {
                $availability_status = "N";
            }
            
            $details = array("variation_details" => $variation_details, "product_details" => $product_details, "availability_status" => $availability_status);
            
        }

        return $details;
    }

    public function favouriteProductsListApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		$user_type = 'customer';

        // check unique_id
        if($this->input->post('user_id') == null)
        {
            $missing_key[] = 'user_id';
        }    
        else
        {
            $unique_id = $this->input->post("user_id");
        }

        if(count($missing_key) == 0)
        {
            $wish_details = $this->get_wish_list($user_type, $unique_id);
            if (count($wish_details) > 0) {
            	$response_arr = array("success" => TRUE, "message" => "Product found in wishlist", "listOfFavouriteProducts" => $wish_details);
            }
            else{
            	$response_arr = array("success" => TRUE, "message" => "No item in wishlist", "listOfFavouriteProducts" => $wish_details);
            }

            $this->response($response_arr, REST_Controller::HTTP_OK);
        }            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function get_product_by_id($product_id = 0)
    {
    	if($product_id > 0)
    		return $this->db->select('*')->from('FM_product')->where('id', $product_id)->get()->row();
    }

    public function get_first_variation_of_product($product_id)
    {
    	$first_variation = $this->db->select('id')->from('FM_product_variation')->where('product_id', $product_id)->where('status', 'Y')->get()->row()->id;

    	return ($first_variation != null) ? $first_variation : null;
    }

    public function getFcmToken_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check user_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		//check fcm_token
		if($this->input->post('token') == null)
		{
		    $missing_key[] = 'token';
		}    
		else
		{
		    $token = $this->input->post("token");
		}


		if(count($missing_key) == 0)
		{
			$data = [
				'fcm_token' => $token,
				'fcm_token_updated_timestamp' => date('Y-m-d H:i:s')
			];

			$this->db->where('id', $user_id);
			$this->db->update('FM_customer', $data);

			if ($this->db->affected_rows() > 0) {
				$response_arr = array("success" => TRUE, "message" => "FCM Token Updated", "isSubmitted" => true);	
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "FCM Token not Updated", "isSubmitted" => false);
			}

		    // $response_arr = array("success" => TRUE, "message" => "The message", "data or response(rename it)" => $abc);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getUserRefferalsApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{

			$user_referral = $this->db->select('owned_referral_code')->from('FM_customer')->where('id', $user_id)->get()->row()->owned_referral_code;
			$registered_users = $this->db->select('COUNT(id) as count')->from('FM_customer')->where('registered_with_referral_code', $user_referral)->where('status', 'Y')->get()->row()->count;
			
			$userReferral = [
				'id' => $user_id,
				'totalReferralsWithCurrentReferrals' => $registered_users
			];

		    $response_arr = array("success" => TRUE, "message" => "Fetched successfully", "userReferral" => $userReferral);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getRefferalCodeApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			$data = $this->db->select('id, owned_referral_code as code')->from('FM_customer')->where('id', $user_id)->get()->row();

		    $response_arr = array("success" => TRUE, "message" => "Referral Code Found", "referralCode" => $data);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getReferralImageApi_post()
    {
    	$image = 'https://safestorage.in/assets/new_design_css/referral_images/image-3.png';
    	$response_arr = array("success" => TRUE, "message" => "Referral Image", "referralImage" => $image);
    	$this->response($response_arr, REST_Controller::HTTP_OK);
    }

    public function getUserReferralOrderApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			$orders = $this->db->select('COUNT(*) as count')->from('FM_reward')->where('receiver_id', $user_id)->where('event', 'Order by descended user')->get()->row()->count;
			// $users  = $this->db->select('COUNT(*) as count')->from('FM_customer')->where('referral_by', $user_id)->get()->row()->count;

			$data = [
				'id' => $user_id,
				// 'totalOrdersWithCurrentOrder' => $orders.'/'.$users
				'totalOrdersWithCurrentOrder' => $orders
			];

		    $response_arr = array("success" => TRUE, "message" => "Data found", "referralOrder" => $data);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function checkAndChangeRewardStatus($user_id, $user_type = 'U')
    {
    	//get all Not Available refferal coupons of the user
    	$get_NA_ref_coupons = $this->db->get_where('FM_reward', ['receiver_id' => $user_id, 'status' => 'NA'])->result();

    	if (count($get_NA_ref_coupons) != 0) {
    		
			foreach ($get_NA_ref_coupons as $refCoupon) {
				$event = $this->reward_event_id_type_map($refCoupon->event, 'DEC');
				if ($event == 'ORDER') {
					
					$check_order = $this->db->from('FM_order')->where('order_no', $refCoupon->source_id)->where('status !=', 'C')->get()->row();

					if ($check_order != null) {
						$order_date = $check_order->created_date;
						$difference = $this->getDifferenceBetweenDate($order_date);

						if ($difference >= 1 && $user_type == 'U') {
							$this->makeRefCouponDiscountAvailable($refCoupon->id);
						}
						if ($difference >= 2 && $user_type == 'M') {
							$this->makeRefCouponDiscountAvailable($refCoupon->id);	
						}
					}

				}

				else{

					$this->makeRefCouponDiscountAvailable($refCoupon->id);

				}

			}    		

    	}
    }

    public function getRefDiscountAmount($user_id, $amount, $user_type = 'U')
    {
    	$discount_amount = 0;

    	$this->checkAndChangeRewardStatus($user_id, $user_type);

    	$getCoupon = $this->db->get_where('FM_reward', ['receiver_id' => $user_id, 'status' => 'A'])->result();


    	if (count($getCoupon) > 0) {
    		foreach ($getCoupon as $cp) {
    			$coupon = $this->db->get_where('FM_ref_code', ['code' => $cp->value])->row();
    			$discount = $coupon->discount;
	    		if ($coupon->discount_type == 'P') {
	    			$discount_amount += $amount*($discount/100);
	    		}
	    		else{
	    			$discount_amount += $discount;
	    		}
    		}
    	}
    	$maximum_discount_limit = $this->db->select('content')->from('FM_preferences')->where('name', 'maximum_discount_limit')->get()->row()->content;
    	$discount_amount = ($discount_amount > $maximum_discount_limit) ? $maximum_discount_limit : $discount_amount;
    	return round($discount_amount);
    }


    public function getRefDiscountPercentage($user_id, $user_type = 'U')
    {
    	$discount_percentage = 0;

    	$this->checkAndChangeRewardStatus($user_id, $user_type);

    	$getCoupon = $this->db->get_where('FM_reward', ['receiver_id' => $user_id, 'status' => 'A'])->result();


    	if (count($getCoupon) > 0) {
    		foreach ($getCoupon as $cp) {
    			$coupon = $this->db->get_where('FM_ref_code', ['code' => $cp->value])->row();
    			$discount = $coupon->discount;
	    		if ($coupon->discount_type == 'P') {
	    			// $discount_amount += $amount*($discount/100);
	    			$discount_percentage += $discount;
	    		}
	    		// else{
	    		// 	$discount_amount += $discount;
	    		// }
    		}
    	}
    	return $discount_percentage;
    }




    public function getReferrer($user_id)
    {
    	$referrer_id = $this->db->select('referral_by')->from('FM_customer')->where('id', $user_id)->get()->row()->referral_by;
    	return $referrer_id;
    }

    public function getRootReferrer($user_id)
    {
    	$user = $user_id;
		while ($user != null) {
			$referrer = $this->getReferrer($user);
			$user = $this->getReferrer($user);
			if ($user != null) {
				if ($this->db->get_where('FM_customer', ['id' => $user])->row()->type == 'M') {
					return $user;
				}
			}
		}

		return $referrer;
    }

    public function isFirstOrder($user_id)
    {
    	$orders = $this->db->query("SELECT * FROM `FM_order` WHERE customer_id = '$user_id' and (status = 'P' or status = 'D')")->result();
    	if (count($orders) > 1) {
    		return false;
    	}
    	else{
    		return true;
    	}
    }

    public function getRootReferrerType($user_id)
    {
    	$user = $user_id;
    	$rootReferrer = $this->getRootReferrer($user_id);
    	if ($rootReferrer != null) {
    		$referrerType = $this->db->select('type')->from('FM_customer')->where('id', $rootReferrer)->get()->row()->type;
    	}
    	else{
    		$referrerType = null;
    	}
		return $referrerType;
    }

    public function orderPlacedApi_post()
    {
    	$affected_rows = 0;
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");	
		}

		//check payment method
		if($this->input->post('payment_method') == null)
		{
		    $missing_key[] = 'payment_method';
		}    
		else
		{
		    $payment_method = $this->input->post("payment_method");
		}

		//check transaction_id
		if($this->input->post('transaction_id') == null)
		{
		    if (isset($payment_method) && $payment_method == 0) {
		    	$missing_key[] = 'transaction_id';
		    }
		}    
		else
		{
		    $transaction_id = $this->input->post("transaction_id");
		}

		if(count($missing_key) == 0)
		{
			
			$referrer = $this->getReferrer($user_id);
			$RootReferrer = $this->getRootReferrer($user_id);
			$RootReferrerType = $this->getRootReferrerType($user_id);

			$cart = $this->getCartList($user_id, null);
			$total_cart_price = $cart['total'];
			$minimum_order_amount = $this->db->select('content')->from('FM_preferences')->where('name', 'minimum_order_amount')->get()->row()->content;

			if ($total_cart_price >= $minimum_order_amount) {
				//prepare order data
				$order_data = [
					// 'order_no'			=> ORDER_PREFIX.date("dmy").$order_id,
					'customer_id' 		=> $user_id,
					'address_id'		=> $this->getAddressId($user_id),
					'total_price'		=> $cart['total'],
					'delivery_charge'	=> $cart['shippingCharge'],
					'discount'			=> 0,		// promo code
					'promo_code_id'		=> null,
					// 'referred_reward'	=> $this->getRefDiscountPercentage($user_id, 'user'),s
					'order_total'		=> $cart['totalPayable'],
					'created_date'		=> date('Y-m-d h:i:s'),
					'updated_date'		=> null,
					'status'			=> 'P',
					'payment_method'	=> ($payment_method == 0) ? 'online' : 'cod',
					'transaction_id'	=> (isset($transaction_id)) ? $transaction_id : '',
					'delivery_date'		=> date('Y-m-d', strtotime("+2 days")),
					'delivery_time_slot'=> '1',
					'notes'				=> '',
					'invoice'			=> '',
					'testing'			=> "Development_".date("F_Y")

				];


				//append order data

				$this->db->insert("FM_order", $order_data);
		        $order_id = $this->db->insert_id();
		        $order_no = ORDER_PREFIX.date("dmy").$order_id;
		        $order_update_data = array("order_no" => $order_no);
		        $this->db->where("id", $order_id);
		        $this->db->update("FM_order", $order_update_data);
				$affected_rows += $this->db->affected_rows();


				//if orderPlaced check referrer and all
				$res_msg = '';

				if ($affected_rows > 0) {

					$res_msg .= 'Order Placed';

					// check if first order
					if ($this->isFirstOrder($user_id)) {
						$res_msg .= '->First Order';


						// get the referrer
						$referrer = $this->getReferrer($user_id);
						if ($referrer != null) {
							$res_msg .= '->Has referrer';
							// if has referrer, add reward for referrer
							$this->appendReward($referrer, 'order', $order_no);
							$this->db->set('referred_reward', '10%');
							$this->db->where('order_no', $order_no);
							$this->db->update('FM_order');
						}
					}

					// make the reward coupon status 'USED'

					$all_rewards = $this->db->from('FM_reward')->where('receiver_id', $user_id)->where('status', 'A')->get()->result();

					if (count($all_rewards) > 0) {
						foreach ($all_rewards as $reward) {
							$res_msg .= '->reward status changed';
							$this->changeRewardStatus($reward->id, $order_no);
						}
					}

					if ($RootReferrerType == 'M') {
						$res_msg .= '->RootReferrer is merchant';
						$this->appendReward($RootReferrer, 'order', $order_no);
					}

					// append cart items into order_details
					$this->appendCartIntoOrder($user_id, $order_id);

					//clear the cart
					$this->clearCart($user_id);

					// $this->changeRewardStatus($user_id);

					$response_arr = array("success" => TRUE, "message" => 'Order Placed Successfully', "isSubmitted" => true);

				}	

				else{
					$response_arr = array("success" => TRUE, "message" => "Order not Placed", "isSubmitted" => false);
				}
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "Unable to place order below ".$minimum_order_amount." rupees.", "isSubmitted" => false);
			}

		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function clearCart($user_id)
    {
    	$count = 0;
        $this->db->select("*");
        $this->db->from("FM_cart");
        $this->db->where("unique_id", $user_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
            		$count++;
                
                    $delete_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
                    $this->db->where("id", $row->id);
                    $this->db->update("FM_cart", $delete_data);
            }

            $response = $count . " items removed";
        }
        else
        {
            $response = 'No items to delete';
        }

        return $response;
    }

    // public function appendRefOrder($user_id, $order_id)
    // {
    // 	$data = [
    // 		'user_id' => $user_id,
    // 		'order_id' => $order_id,
    // 		'status' => 'NA',
    // 		'refCode' => 'OFF10P',
    // 		'created_timestamp' => date("Y-m-d H:i:s"),
    // 	];

    // 	$this->db->insert('FM_ref_order', $data);

    // 	if ($this->db->affected_rows() > 0) {
    // 		return true;
    // 	}
    // 	return false;
    // }

    public function getAddressId($user_id)
    {
    	$address = $this->db->select('id')->from('FM_customer_address')->where(['customer_id' => $user_id])->get()->row();
    	if ($address != null) {
    		return $address->id;
    	}
    	else{
    		return -1;
    	}
    }

    public function getAllTopicsAnswers_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();	

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    // $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		//check answer
		if($this->input->post('answer') == null)
		{
		    // $missing_key[] = 'answer';
		}    
		else
		{
		    $answer = $this->input->post("answer");
		}

		//check community id
		if($this->input->post('topic_id') == null)
		{
		    $missing_key[] = 'topic_id';
		}    
		else
		{
		    $topic_id = $this->input->post("topic_id");
		}


		//check image
		if(empty($_FILES['image']['name']))
		{
			// $missingParam[] = "image";
		}
		else
		{
			$upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/answer_images/';
            $rand_name = time()."-";
            $upload_file = $upload_dir.$rand_name.basename($_FILES['answer_images']['name']);
            $upload_file = str_replace(" ","-",$upload_file);
            $actual_path = 'uploads/answer_images/'.$rand_name.basename($_FILES['image']['name']);
            $actual_path = str_replace(" ","-",$actual_path);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file))
            {
                $image  = $actual_path;
            }
            else
            {
                $image = "No Image";
            }

			// $document_image = $this->input->post("document_image");
		}

		if(count($missing_key) == 0)//fake checkup
		{

			if (isset($user_id) && isset($answer)) {
				$data = [
					'customer_id' => $user_id,
					'community_id' => $topic_id,
					'comments' => $answer,
					'image' => (isset($image)) ? $image : null,
					'created_date' => date('Y-m-d h:i:s'),
					'testing'	=> "Development_".date("F_Y")
				];

				$this->db->insert('FM_community_comments', $data);

				$topic_details = $this->db->select('*')->from('FM_ask_community')->where('id', $topic_id)->get()->row();
				$asked_by = $topic_details->customer_id;
				$question = $topic_details->quesstion;
				$answer_by_raw = $this->db->select('first_name, last_name')->from('FM_customer')->where('id', $user_id)->get()->row();
				$answer_by = $answer_by_raw->first_name.' '.$answer_by_raw->last_name;

				$notification_datetime = date('d M, Y - h:i a');
				$notification_body = "Your question '$question' just got answered by $answer_by \r\n $notification_datetime";

				$this->sendPushMessages($asked_by, $notification_body, 'Farmology');
			}
			
			// if getting question id, retrive list of answers
			if(isset($topic_id)){
				$answers = $this->db->query("SELECT FM_community_comments.id as id, concat(FM_customer.first_name, ' ', FM_customer.last_name) as username, FM_community_comments.created_date as answerDate, FM_community_comments.comments as answer, FM_community_comments.image as imageUrl FROM `FM_community_comments` INNER JOIN FM_customer ON FM_community_comments.customer_id = FM_customer.id WHERE FM_community_comments.community_id = '$topic_id'")->result();
			}

			$data = [];

			if (count($answers) > 0) {
				foreach ($answers as $answer) {
					$answer->answerDate = $this->getDifferenceBetweenDate($answer->answerDate)." Days Ago";
					$answer->imageUrl = ($answer->imageUrl != null) ? STORE_URL.$answer->imageUrl : null;
					// $data[] = $answer;
				}
			}

		    $response_arr = array("success" => TRUE, "message" => "List of answers", "listOfAnswers" => $answers);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getDifferenceBetweenDate($date)
    {
    	$now = date_create_from_format('Y-m-d', date('Y-m-d'));
    	 										// or your date as well
		$your_date = date_create_from_format('Y-m-d', date('Y-m-d',strtotime($date)));

		$datediff = (array)date_diff($now,$your_date);

		return $datediff['days'];
    }

    public function makeRefCouponDiscountAvailable($ref_coupon_id)
    {
    	$this->db->where('id', $ref_coupon_id);
    	$this->db->update('FM_reward', ['status' => 'A', 'updated_timestamp' => date('Y-m-d h:i:s')]);
    }

    public function reward_event_id_type_map($event, $type)
    {

    	$ev = strtoupper($event);
    	$value;

    	if ($type == 'ENC') {
    		switch ($ev) {
	    		case 'ORDER':
	    			$value = 'Order by descended user';
	    			break;
	    		case 'REFERRED':
	    			$value = 'Referred by user';
	    			break;
	    		default:
	    			break;
	    	}
    	}
    	else{
    		switch ($ev) {
	    		case 'ORDER BY DESCENDED USER':
	    			$value = 'ORDER';
	    			break;
	    		case 'REFERRED BY USER':
	    			$value = 'REFERRED';
	    			break;
	    		default:
	    			break;
	    	}
    	}

    	return $value;
    }

    public function appendReward($receiver, $event, $event_id)
    {
    	$rewardVal = (strtoupper($event) == 'ORDER') ? 'OFF4P' : 'OFF10P';

    	$rewardData = [
    		'hash_id' 			=> $this->GUID(),
    		'receiver_id'		=> $receiver,
    		'event'				=> $this->reward_event_id_type_map($event, 'ENC'),
    		'source_id'			=> $event_id,
    		'value'				=> $rewardVal,
    		'status'			=> 'NA',
    		'created_timestamp' => date('Y-m-d h:i:s'),
    	];

    	$this->db->insert('FM_reward', $rewardData);

    	if ($this->db->affected_rows() > 0) {
    		return true;
    	}
    	else{
    		return false;
    	}
    }

    public function finalCartItems_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		$cart = file_get_contents("php://input");

		// check unique_id
		if($cart == null)
		{
		    $missing_key[] = 'user_cart_information';
		}    
		else
		{
		    $user_cart_information = json_decode($cart);
		}

		

		if(count($missing_key) == 0)
		{
			if (isset($user_cart_information->user_id)) {
				$user_id = $user_cart_information->user_id;
				$this->clearCart($user_id);
				$cart_list = $user_cart_information->cart_list;
				$minimum_order_amount = $this->db->select('content')->from('FM_preferences')->where('name', 'minimum_order_amount')->get()->row()->content;

				
					
				foreach ($cart_list as $items) {

					// $items = json_decode($items);

					$data = [
						'user_type'	=> 'customer',
						'unique_id'	=> $user_id,
						'variation_id' => $items->details->variation_details->id,
						'quantity' => $items->quantity
					];

					$this->add_to_cart($data);
				}

				if ($this->checkCartBalance($user_id)) {
					$response_arr = array("success" => TRUE, "message" => "All items submitted successfully", "alert" => "allowed", "isSubmitted" => true);
				}

				else{
					$response_arr = array("success" => TRUE, "message" => "All items submitted successfully", "alert" => "Cart total amount is below $minimum_order_amount rupees", "isSubmitted" => false);
				}

				

			    
			    $this->response($response_arr, REST_Controller::HTTP_OK);
			}
			else{
				$response_arr = array("success" => FALSE, "message" => "Could not process the input data", "alert" => "", "isSubmitted" => false);
				$this->response($response_arr, REST_Controller::HTTP_OK);
			}
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function checkCartBalance($user_id = 0)
    {
    	$cart = $this->getCartList($user_id);
    	$minimum_order_amount = $this->db->select('content')->from('FM_preferences')->where('name', 'minimum_order_amount')->get()->row()->content;
    	if ($cart['total'] > $minimum_order_amount) {
    		return true;
    	}
    	return false;
    }

    public function getCartExtraChargesApi_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    // $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{

			$gst = $this->db->select('content')->from('FM_preferences')->where('name', 'gst')->get()->row()->content;

			$data = [
				'id'=> 1,
                'referralDiscount'=> floatval(0),
                'shippingCharge'=> 0,
                'gst'=> floatval($gst)
			];

			if (isset($user_id)) {

				$data['referralDiscount'] = floatval($this->getRefDiscountPercentage($user_id, ''));
				$data['shippingCharge'] = intval($this->get_delivery_charge_by_city_id($user_id));
			}


		    $response_arr = array("success" => TRUE, "message" => "Calculated Data", "cartExtraCharges" => $data);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    function get_delivery_charge_by_city_id($user_id = 0)
    {
    	//get city_id of user

    	$id = $this->db->select('city_id')->from('FM_customer_address')->where('customer_id', $user_id)->where('is_deleted', 'N')->get()->row()->city_id;

    	//get charge based on city id

        $charge = 0;
        $this->db->select("charge");
        $this->db->from("FM_city_lookup");
        $this->db->where("id", $id);
        $this->db->where("status", "Y");
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row  = $query->row();
            $charge = $row->charge;
        }
        return $charge;
    }


    function add_to_cart($data)
    {
    	$response = array();
        $user_type = $data['user_type'];
        $unique_id = $data['unique_id'];
        $variation_id = $data['variation_id'];
        $quantity = $data['quantity'];

        $variation_details = $this->get_veriation_full_details_by_id($variation_id);
        if ($variation_details != null) {
        	$product_name = $variation_details['product_details']['name']; 

	        // check variation exist or not 
	        $this->db->select("id, quantity");
	        $this->db->from("FM_cart");
	        $this->db->where("variation_id", $variation_id);
	        $this->db->where("user_type", $user_type);
	        $this->db->where("unique_id", $unique_id);
	        $this->db->where("is_deleted", "N");
	        $check_var_query = $this->db->get();
	        if($check_var_query->num_rows() > 0)
	        {
	            $found_var_row = $check_var_query->row();
	            $quantity =  $quantity;

	            $this->db->where("id", $found_var_row->id);
	            $update_data = array("quantity" => $quantity, "updated_date" => date("Y-m-d H:i:s"));
	            $this->db->update("FM_cart", $update_data);

	            

	            $response = array("status" => "Y", "message" => "Product successfully added to cart.", "product_name" => $product_name);
	        }
	        else
	        {
	            $insert_data = array("variation_id" => $variation_id, "quantity" => $quantity, "user_type" => $user_type, "unique_id" => $unique_id, "created_date" => date("Y-m-d H:i:s"), "is_deleted" => "N");
	            $this->db->insert("FM_cart", $insert_data);
	            //echo $this->db->last_query(); exit;
	            $response = array("status" => "Y", "message" => "Product successfully added to cart.", "product_name" => $product_name);
	        }
        }
        

        return $response;
    }

    public function changeRewardStatus($reward_id, $order_no)
    {
    	$this->db->set(['status' => 'U', 'updated_timestamp' => date('Y-m-d h:i:s'), 'redeemed_on_order' => $order_no]);
    	$this->db->where('id', $reward_id);
    	$this->db->update('FM_reward');

    	if ($this->db->affected_rows() > 0) {
    		return true;
    	}
    	return false;
    }

    public function appendCartIntoOrder($user_id, $order_id)
    {
    	$cart_row = array();

        $this->db->select("*");
        $this->db->from("FM_cart");
        $this->db->where("user_type", 'customer');
        $this->db->where("unique_id", $user_id);
        $this->db->where("is_deleted", "N");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $quantity = 0;
            foreach($query->result() as $row)
            {
                $variation_id = $row->variation_id;
                $variation_details = $this->get_veriation_full_details_by_id($variation_id);
                
                $cart_row = array(
                	"order_id" => $order_id, 
                	"product_id" => $variation_details['product_details']['id'],
                	"variation_id" => $variation_details['variation_details']['id'],
                	"quantity" => $row->quantity, 
                	"unit_price" => $variation_details['variation_details']['price_details']['price'], 
                	"total_price" => $variation_details['variation_details']['price_details']['price'] * $row->quantity,
                	'testing'	=> "Development_".date("F_Y"));

                $this->db->insert('FM_order_details', $cart_row);

            }

        }
            
    }

    public function checkoutUserAddressApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}


		// check address
		if($this->input->post('user_address') == null)
		{
		    // $missing_key[] = 'user_address';
		    
		}    
		else
		{
		    $user_address = $this->input->post("user_address");
		}

		if(count($missing_key) == 0)
		{
			
			$this->db->set('address_1', $user_address);
			$this->db->where('customer_id', $user_id);
			$this->db->update('FM_customer_address');

			$response_arr = array("success" => TRUE, "message" => "Address saved successfully", "isSubmitted" => true);

			// if ($this->db->affected_rows() > 0) {
			// 	$response_arr = array("success" => TRUE, "message" => "Address saved successfully", "isSubmitted" => true);		
			// }
			// else{
			// 	$response_arr = array("success" => TRUE, "message" => "Address not saved", "isSubmitted" => false);	
			// }
		    
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getAllCitiesApi_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			$state_id = $this->db->select('state_id')->from('FM_customer')->where('id', $user_id)->get()->row()->state_id;

			$listOfCities = $this->db->select('id, name as cityName')->from('FM_city_lookup')->where('state_id', $state_id)->get()->result();


		    $response_arr = array("success" => TRUE, "message" => "Successful", "listOfCities" => $listOfCities);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function checkUserHasAddress_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			$address = $this->db->from('FM_customer_address')->where('customer_id', $user_id)->where('is_deleted', 'N')->get()->result();

			if (count($address) > 0) {
				$response_arr = array("success" => TRUE, "message" => "User already has address", "hasAddress" => true);		
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "User has no address", "hasAddress" => false);		
			}

		    
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function setUserAddressApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check address
		if($this->input->post('address') == null)
		{
		    $missing_key[] = 'address';
		}    
		else
		{
		    $address = $this->input->post("address");
		}

		// check city_id
		if($this->input->post('city_id') == null)
		{
		    $missing_key[] = 'city_id';
		}    
		else
		{
		    $city_id = $this->input->post("city_id");
		}

		// check landmark
		if($this->input->post('landmark') == null)
		{
		    $missing_key[] = 'landmark';
		}    
		else
		{
		    $landmark = $this->input->post("landmark");
		}

		// check zip_code
		if($this->input->post('zip_code') == null)
		{
		    $missing_key[] = 'zip_code';
		}    
		else
		{
		    $zip_code = $this->input->post("zip_code");
		}

		if(count($missing_key) == 0)
		{

			$hasUserAddress = (count($this->db->query("SELECT * FROM `FM_customer_address` WHERE customer_id = '$user_id' and is_deleted = 'N'")->result()) > 0) ? true : false;

			if ($hasUserAddress) {
				$update_data = [
					'address_1'		=> $address,
					'city_id'		=> $city_id,
					'landmark'		=> $landmark,
					'zip_code'		=> $zip_code
				];

				$this->db->set($update_data);
				$this->db->where('customer_id', $user_id);
				$this->db->update('FM_customer_address');

				$response_arr = array("success" => TRUE, "message" => "Address Updated Successfully", "isSubmitted" => true);
			}

			else{
				$userData = $this->db->from('FM_customer')->where('id', $user_id)->get()->row();

				$addressData = [
					'customer_id' => $user_id,
					'name' 		=> $userData->first_name.' '.$userData->last_name,
					'phone'		=> $userData->phone,
					'address_1'	=> $address,
					'address_2'	=> null,
					'landmark'	=> $landmark,
					'state_id'	=> $userData->state_id,
					'city_id'	=> $city_id,
					'zip_code'	=> $zip_code,
					'is_deleted'=> 'N'
				];

				$this->db->insert('FM_customer_address', $addressData);

				if ($this->db->affected_rows() > 0) {
					$response_arr = array("success" => TRUE, "message" => "Address Saved Successfully", "isSubmitted" => true);
				}
				else{
					$response_arr = array("success" => TRUE, "message" => "Address not saved", "isSubmitted" => false);
				}
			}

		    	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function checkUserHasAccount_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_email_or_mobile') == null)
		{
		    $missing_key[] = 'user_email_or_mobile';
		}    
		else
		{
		    $user_email_or_mobile = $this->input->post("user_email_or_mobile");
		}

		if(count($missing_key) == 0)
		{
			$user = $this->db->query("SELECT id, first_name FROM `FM_customer` WHERE email = '$user_email_or_mobile' or phone = '$user_email_or_mobile'")->result();

			if (count($user) > 0) {
				$user = $user[0];
				$hasAccount = [
					'id'			=> '1',
					'user_id'		=> $user->id,
					'hasAccount'	=> true,
					'userName' 		=> $user->first_name
				];

				$msg = 'Account Found';
			}
			else{
				$hasAccount = [
					'id'			=> '1',
					'user_id'		=> null,
					'hasAccount'	=> false,
					'userName' 		=> null
				];
				$msg = 'No Account Found';
			}

		    $response_arr = array("success" => TRUE, "message" => $msg, "hasAccount" => $hasAccount);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getAllRewardsApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{

			//get the coupons that are redeemed
			$rewards = $this->db->from('FM_reward')->where('status', 'U')->where('event', 'Order by descended user')->where('receiver_id', $user_id)->get()->result();

			$user = $this->db->from('FM_customer')->where('id', $user_id)->get()->row();

			if (count($rewards) > 0) {
				foreach ($rewards as $reward) {
					$order = $this->db->from('FM_order')->where('order_no', $reward->source_id)->get()->row();
					$c = 1;

					$data[] = [

						'id' => $c++,
						'userName' => $user->first_name.' '.$user->last_name,
						'orderIdWith' => $reward->source_id,
						'orderStatus' => $order->status,
						'discountPercentOnNextOrder' => '4%'

					];
				}	
				$response_arr = array("success" => TRUE, "message" => "Reward Found", "listOfRewards" => $data);
			}
			else
		    	$response_arr = array("success" => TRUE, "message" => "Reward not found", "listOfRewards" => array());	

		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function merchantRegistration_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check profile_image
		if($this->input->post('profile_image') == null)
		{
		    $profile_image = null;
		}    
		else
		{
		    $profile_image = $this->input->post("profile_image");
		}

		// check first_name
		if($this->input->post('first_name') == null)
		{
		    $missing_key[] = 'first_name';
		}    
		else
		{
		    $first_name = $this->input->post("first_name");
		}


		// check last_name
		if($this->input->post('last_name') == null)
		{
		    $missing_key[] = 'last_name';
		}    
		else
		{
		    $last_name = $this->input->post("last_name");
		}


		// check mobile_number
		if($this->input->post('mobile_number') == null)
		{
		    $missing_key[] = 'mobile_number';
		}    
		else
		{
		    $mobile_number = $this->input->post("mobile_number");
		}


		// check email_address
		if($this->input->post('email_address') == null)
		{
		    $missing_key[] = 'email_address';
		}    
		else
		{
		    $email_address = $this->input->post("email_address");
		}


		// check address_One
		if($this->input->post('address_one') == null)
		{
		    $missing_key[] = 'address_one';
		}    
		else
		{
		    $address_one = $this->input->post("address_one");
		}



		// check address_two
		if($this->input->post('address_two') == null)
		{
		    // $missing_key[] = 'address_two';
		}    
		else
		{
		    $address_two = $this->input->post("address_two");
		}



		// check Pincode
		if($this->input->post('pincode') == null)
		{
		    $missing_key[] = 'pincode';
		}    
		else
		{
		    $pincode = $this->input->post("pincode");
		}



		// check city
		if($this->input->post('city') == null)
		{
		    $missing_key[] = 'city';
		}    
		else
		{
		    $city = $this->input->post("city");
		}


		// check State
		if($this->input->post('state') == null)
		{
		    $missing_key[] = 'state';
		}    
		else
		{
		    $state = $this->input->post("state");
		}

		if(count($missing_key) == 0)
		{
			if ($this->checkMerchantEmailAndPhone($email_address, $mobile_number)) {
				$registration_data = $this->registerMerchant($first_name, $last_name, $email_address, $mobile_number, $state, $profile_image);

				if ($registration_data != null) {
					
					$address_2 = (isset($address_two)) ? $address_two : null;
					$this->registerMerchantAddress($registration_data, $address_one, $address_2, $city, $pincode);

					$response_arr = array("status" => TRUE, "message" => "Registration Successful", "redirect_url" => "https://testing.surobhiagro.in/merchant_portal/dashboard");
					$userData = [
						'email_or_phone' => $email_address,
					];
					$this->load->library('session');
					$this->session->set_userdata($userData);
				}
				else{
					$response_arr = array("status" => FALSE, "message" => "Registration Not Successful", "redirect_url" => "");
				}
			}
			else{
				$response_arr = array("status" => FALSE, "message" => "Duplicate Email or Phone", "redirect_url" => "");
			}

		    	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function registerMerchant($first_name, $last_name, $email, $phone, $state_id, $profile_image)
    {
    	// $state_id = $this->db->select('id')->from('FM_state_lookup')->where('state', strtoupper($State))->get()->row()->id;

    	$data = [
    		'first_name'	=> $first_name,
    		'last_name'		=> $last_name,
    		'email'			=> $email,
    		'phone'			=> $phone,
    		'state_id'		=> $state_id,
    		'status'		=> 'Y',
    		'created_date'	=> date('Y-m-d h:i:s'),
    		'type'			=> 'M',
    		'merchant_profile_image' => $profile_image,
    		'testing'		=> "Development_".date("F_Y")
    	];

    	$this->db->insert('FM_customer', $data);

    	$id = $this->db->insert_id();

    	$owned_referral_code = $this->getReferralCode($id);

    	$this->db->set('owned_referral_code', $owned_referral_code);
    	$this->db->where('id', $id);
    	$this->db->update('FM_customer');
    	$data['id'] = $id;

    	return $data;
    }

    public function registerMerchantAddress($registration_data, $address_One, $address_two, $city_id, $Pincode)
    {
    	// $city_id = $this->db->select('id')->from('FM_city_lookup')->where('name', $city)->get()->row()->id;

    	$data = [
    		'customer_id'	=> $registration_data['id'],
    		'name'			=> $registration_data['first_name'].' '.$registration_data['last_name'],
    		'phone'			=> $registration_data['phone'],
    		'address_1'		=> $address_One,
    		'address_2'		=> $address_two,
    		'landmark'		=> '',
    		'state_id'		=> $registration_data['state_id'],
    		'city_id'		=> $city_id,
    		'zip_code'		=> $Pincode,
    		'is_deleted'	=> 'N',
    		'testing'	=> "Development_".date("F_Y")
    	];

    	$this->db->insert('FM_customer_address', $data);

    	if ($this->db->affected_rows() > 0) {
    		return true;
    	}
    	return false;
    }


    public function checkMerchantEmailAndPhone($email, $phone)
    {
    	$emailData = $this->db->from('FM_customer')->where('email', $email)->get()->result();
    	$phoneData = $this->db->from('FM_customer')->where('phone', $phone)->get()->result();

    	if (count($emailData) > 0 || count($phoneData) > 0) {
    		return false;
    		
    	}

    	return true;;
    }

    // sends otp to the given number or email

    public function merchantSendOtp_post()
    {
    	$missingParam = array();

		if($this->input->post("param")==null || !isset($_POST["param"]))
		{
			$missingParam[] = "email_or_mobile";
		}
		else
		{
			$user_mobile_or_email = $this->input->post("param");
		}

		if(count($missingParam)>0)
		{
			$response = array(
				"success" => false,
				"message" => $missingParam[0]." is not given",
				"userOtp" => (object)array()
			);
		}
		else
		{
			$merchant = $this->getUserByEmailOrPhone($user_mobile_or_email);
			if ($merchant == null) {
				$response = array(
					"status" => false,
					"message" => 'No account with this email or mobile number',
					"otpDetails" => (object)array()
				);
			}
			else
			{
				if (!filter_var($user_mobile_or_email, FILTER_VALIDATE_EMAIL))
				{
				  	$is_email = false;
				  	$phone = $user_mobile_or_email;
				}
				else
				{
					$is_email = true;
					$email = $user_mobile_or_email;
				}

				$random_otp_number = mt_rand(1000,9999);

				if($is_email)
				{
					$insertDataArr = array(
						"otp" => $random_otp_number,
						"email" => $email,
						"is_expired" => "N",
						"created_date" => date("Y-m-d H:i:s")
					);

					$this->db->insert("FM_email_otp_list", $insertDataArr);
					$id = $this->db->insert_id();
					$this->send_otp_to_email($email, $random_otp_number);
					$otp = $random_otp_number;
					$otp_source = "email";
					$message = "OTP has been sent";
					$userData = [
						'email_or_phone' => $email,
					];
				}
				else
				{
					$insertDataArr = array(
						"otp" => $random_otp_number,
						"phone" => $phone,
						"is_expired" => "N",
						"created_date" => date("Y-m-d H:i:s")
					);

					$this->db->insert("FM_phone_otp_list", $insertDataArr);
					$id = $this->db->insert_id();
					$this->send_otp_to_phone($phone, $random_otp_number);
					$otp = $random_otp_number;
					$otp_source = "phone";
					$message = "OTP has been sent";
					$userData = [
						'email_or_phone' => $phone,
					];
				}

				$response = array(
					"status" => true,
					"message" => $message,
					"otpDetails" => (object)array(
						"id" => strval($id),
						"otpSource" => $otp_source,
					)
				);
				$this->load->library('session');
				$this->session->set_userdata($userData);
			}
		}

		$this->response($response, REST_Controller::HTTP_OK);
    }
    
    // get user details by email or phone number

    public function getUserByEmailOrPhone($emailOrPhone)
    {
    	return $this->db->query("SELECT * FROM FM_customer WHERE email = '$emailOrPhone' OR phone = '$emailOrPhone'")->row();
    }

    // check the otp that was sent to use/ merchant

    public function merchantCheckOtp_post()
    {
    	$missingParam = array();

		if($this->input->post("otp_id")==null || !isset($_POST["otp_id"]))
		{
			$missingParam[] = "otp_id";
		}
		else
		{
			$otp_id = $this->input->post("otp_id");
		}

		if($this->input->post("otp")==null || !isset($_POST["otp"]))
		{
			$missingParam[] = "otp";
		}
		else
		{
			$otp = $this->input->post("otp");
		}

		if($this->input->post("otp_source")==null || !isset($_POST["otp_source"]))
		{
			$missingParam[] = "otp_source";
		}
		else
		{
			$otp_source = $this->input->post("otp_source");
		}

		if(count($missingParam)>0)
		{
			$missingString = implode(", ",$missingParam);
			$missingString = rtrim($missingString,", ");

			$response = array(
				"success" => false,
				"message" => $missingString." not given",
				"isMatched" => false
			);
		}
		else
		{
			if($otp_source=="email" || $otp_source=="phone")
			{
				$condArr = array("is_expired"=>"N", "id"=>$otp_id, "otp"=>$otp);
				$otp_source_table = "FM_".$otp_source."_otp_list";
				$user_otp_data = $this->db->get_where($otp_source_table, $condArr)->result();
				if(count($user_otp_data)>0)
				{
					$this->db->set("is_expired","Y");
					$this->db->where($condArr);
					$this->db->update($otp_source_table);

					$response = array(
						"status" => true,
						"message" => "User OTP verified successfully",
						"redirect_url" => "https://testing.surobhiagro.in/merchant_portal/dashboard"
					);
				}
				else
				{
					$response = array(
						"status" => false,
						"message" => "Invalid OTP Number",
						"redirect_url" => ''
					);
				}
			}
			else
			{
				$response = array(
					"status" => false,
					"message" => "Invalid OTP Source",
					"redirect_url" => ''
				);
			}
		}

		$this->response($response, REST_Controller::HTTP_OK);
    }

    // get the merchant details by phone number or email that was stored on the session

    public function getMerchantDetails_get()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		if(count($missing_key) == 0)
		{
			
			$this->load->library('session');
			if (!$this->session->has_userdata('email_or_phone') && $this->session->userdata('email_or_phone') == null) {
				$response_arr = array("status" => FALSE, "message" => "Saved data not found. Log in to continue", "redirect_url" => "https://testing.surobhiagro.in/merchant_portal/login");
			}
			else{
				$email_or_phone = $this->session->userdata('email_or_phone');

				$merchantDetails = $this->db->query("SELECT * FROM FM_customer INNER JOIN FM_customer_address ON FM_customer.id = FM_customer_address.customer_id WHERE FM_customer.email = '$email_or_phone' OR FM_customer.phone = '$email_or_phone'")->row();

				if ($merchantDetails != null) {
					$response_arr = array("status" => TRUE, "message" => "Merchant Details Found", "details" => $merchantDetails);
				}
				else{
					$response_arr = array("status" => FALSE, "message" => "Merchant Details not Found", "details" => array());	
				}
			}
			$this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("status" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    //get total number of referred user of a merchant

    public function totalMerchantReferrals_get()
    {
    	$this->load->library('session');
    	if (!$this->session->has_userdata('email_or_phone') && $this->session->userdata('email_or_phone') == null) {
			$response_arr = array("status" => FALSE, "message" => "Saved data not found. Log in to continue", "redirect_url" => "https://testing.surobhiagro.in/merchant_portal/login");
		}
		else{
			$email_or_phone = $this->session->userdata('email_or_phone');

			$id = $this->getUserByEmailOrPhone($email_or_phone)->id;

			$totalReferrals = $this->db->select('COUNT(*) as count')->from('FM_customer')->where('referral_by', $id)->get()->row()->count;

			$response_arr = array("status" => TRUE, "message" => "Total Referral", "number_of_referral" => $totalReferrals);
		}
		
		$this->response($response_arr, REST_Controller::HTTP_OK);
    }

    // get total number of orders made by referred users of merchant

    public function totalReferralOrders_get()
    {
    	$this->load->library('session');
    	if (!$this->session->has_userdata('email_or_phone') && $this->session->userdata('email_or_phone') == null) {
			$response_arr = array("status" => FALSE, "message" => "Saved data not found. Log in to continue", "redirect_url" => "https://testing.surobhiagro.in/merchant_portal/login");
		}
		else{
			$email_or_phone = $this->session->userdata('email_or_phone');

			$id = $this->getUserByEmailOrPhone($email_or_phone)->id;

			$referredUsers = $this->db->from('FM_customer')->where('referral_by', $id)->get()->result();

			$totalOrders = 0;

			foreach ($referredUsers as $refUser) {
				
				$totalOrders += $this->db->select('COUNT(id) as count')->from('FM_order')->where('customer_id', $refUser->id)->get()->row()->count;

			}

			$response_arr = array("status" => TRUE, "message" => "Total Referral Orders", "number_of_referrals_orders" => $totalOrders);
		}
		
		$this->response($response_arr, REST_Controller::HTTP_OK);
    }

    public function getAllStates_get()
    {
    	$states = $this->db->from('FM_state_lookup')->where('is_deleted', 'Y')->get()->result();

    	$modifiedStates = array();

    	foreach ($states as $state) {
    		$state->state = ucwords(strtolower($state->state));
    		$modifiedStates[] = $state;
    	}

    	$response_arr = array("status" => TRUE, "message" => "List of all states", "states" => $modifiedStates);
		$this->response($response_arr, REST_Controller::HTTP_OK);
    }

    public function getCitiesByStateId_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check state_id
		if($this->input->post('state_id') == null)
		{
		    $missing_key[] = 'state_id';
		}    
		else
		{
		    $state_id = $this->input->post("state_id");
		}

		if(count($missing_key) == 0)
		{
			
			$cities = $this->db->from('FM_city_lookup')->where('state_id', $state_id)->where('status', 'Y')->get()->result();

			if (count($cities) > 0) {
				$response_arr = array("status" => TRUE, "message" => "Cities Found", "cities" => $cities);	
			}	
			else{
				$response_arr = array("status" => FALSE, "message" => "Cities Not Found", "cities" => array());	
			}
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getDashboardReward_get() 
    {
    	$this->load->library('session');
		$email_or_phone = $this->session->userdata('email_or_phone');

		$id = $this->getUserByEmailOrPhone($email_or_phone)->id;

		$rewards = $this->getUserReferredOrders($id, 5);

		

		$response_arr = array("status" => TRUE, "message" => "Total Rewards", "orders" => $rewards);
		$this->response($response_arr, REST_Controller::HTTP_OK);
    }

    


    public function getAllOrdersOfDescendants($id)
    {
    	$referred_users = $this->getReferredUsers($id);
    	$orders = array();

    	if ($referred_users != false) {
    		
    		foreach ($referred_users as $refUser) {
	    		if($this->getOrders($refUser->id) != false)
	    			$orders = array_merge($orders, $this->getOrders($refUser->id));
	    	}

    	}

    	return $orders;
    }


    public function getUserById($id)
    {
    	return $this->db->get_where('FM_customer', ['id' => $id])->row();
    }



    public function getReferredUsers($user_id)
    {
    	$referredUsers = $this->db->from('FM_customer')->where('referral_by', $user_id)->get()->result();

    	if (count($referredUsers) > 0) {
    		return $referredUsers;
    	}
    	return false;
    }

    public function getOrders($user_id)
    {
    	$orders = $this->db->get_where('FM_order', ['customer_id' => $user_id, 'status != ' => 'C'])->result();
    	if (count($orders) > 0) {
    		return $orders;
    	}
    	return false;
    }

    public function getUserAllFarms_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			$farmList = $this->db->select('id, name as farmName, area as farmArea, crop_image as cropImage, next_report as nextReport')->from('FM_farm')->where('user_id', $user_id)->where('status', 'A')->order_by('id', 'DESC')->get()->result();

			if (count($farmList) > 0) {
				$data = [];
				foreach ($farmList as $fl) {
					$fl->farmArea = ' Acre : '.number_format($fl->farmArea, 2);
					$fl->cropImage = STORE_URL.$fl->cropImage;
					$data[] = $fl;
				}
				$response_arr = array("success" => TRUE, "message" => "Found farms for this user", "listOfFarms" => $data);
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "No farms found for this user", "listOfFarms" => $farmList);
			}

		    	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function addUserFarmApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check farmName
		if($this->input->post('farmName') == null)
		{
		    $missing_key[] = 'farmName';
		}    
		else
		{
		    $farmName = $this->input->post("farmName");
		}

		// check selectedCorp
		if($this->input->post('selectedCorp') == null)
		{
		    $missing_key[] = 'selectedCorp';
		}    
		else
		{
		    $selectedCorp = $this->input->post("selectedCorp");
		}

		// check sowIngDate
		if($this->input->post('sowIngDate') == null)
		{
		    $missing_key[] = 'sowIngDate';
		}    
		else
		{
		    $sowIngDate = $this->input->post("sowIngDate");
		}

		// check data
		if($this->input->post('data') == null)
		{
		    // $missing_key[] = 'data';
		    $data = null;
		}    
		else
		{
		    $data = $this->input->post("data");
		}

		if(count($missing_key) == 0)
		{
			$crop = $this->db->select('image')->from('FM_crop')->where('id', $selectedCorp)->get()->row();
			$farmData = [
				'hash_id'		=> $this->GUID(),
				'name'			=> $farmName,
				'crop_name'		=> $selectedCorp,
				'crop_image'	=> isset($crop->image) ? $crop->image : null,
				'area'			=> $this->getArea($data),
				'sowIngDate'	=> date('Y-m-d', strtotime($sowIngDate)),
				'next_report'	=> date('Y-m-d', strtotime('+7 days')),
				'created_timestamp'	=> date('Y-m-d h:i:s'),
				'status'		=> 'A',
				'user_id'		=> $user_id,
				'farm_boundaries' => json_encode($this->getFarmCoordinate($data))
			];

			$this->db->insert('FM_farm', $farmData);
			if ($this->db->affected_rows() > 0) {
				$response_arr = array("success" => TRUE, "message" => "Farm Added Successfully", "isAdded" => true);
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "Could not add Farm", "isAdded" => false);
			}

		    	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getArea($data)
    {
    	if ($data != null) {
    		$data = json_decode($data);
    		$res = $data->features[0]->properties->area;
    	}

    	return $res;
    }

    public function getDashboardUrl_get()
    {
    	$url = "www.google.com";
    	$response_arr = array("status" => TRUE, "message" => "Link URL", "url" => $url);
    	$this->response($response_arr, REST_Controller::HTTP_OK);
    }

    public function isSessionAvailable_get()
    {
    	$this->load->library('session');
    	if (!$this->session->has_userdata('email_or_phone') && $this->session->userdata('email_or_phone') == null && $this->session->userdata('email_or_phone') == null) {
			$response_arr = array("status" => FALSE);
		}
		else{
			$response_arr = array('status' => TRUE);
		}	
		$this->response($response_arr, REST_Controller::HTTP_OK);
    }

  //   public function getFarmBoundaries_post()
  //   {
  //   	$response_status = FALSE;
		// $response_message = "Something was wrong."; 

		// $missing_key = array();

		// // check data
		// if($this->input->post('data') == null)
		// {
		//     $missing_key[] = 'data';
		// }    
		// else
		// {
		//     $data = $this->input->post("data");
		// }

		// if(count($missing_key) == 0)
		// {
		// 	$this->load->helper('file');
			



		//     $response_arr = array("success" => TRUE, "message" => 'Got it', "data" => $res);	
		//     $this->response($response_arr, REST_Controller::HTTP_OK);
		// }            
		// else
		// {
		//     $implode_missing_key = implode(', ', $missing_key);
		//     $response_message = $implode_missing_key." - not found";

		//     $response = array("success" => $response_status, "message" => $response_message);
		//     $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		// }
  //   }


    // public function findStr($str='')
    // {
    // 	$revstr = 
    // }

    public function getFarmCoordinate($data)
    {
    	$res = null;

   //  	if ($data != null) {
    		
   //  		$revData = strrev($data);
			// $endPoint = strpos($revData, ']');

			// $end = intval('-'.$endPoint);

			// $writeData = substr($data, 20, $end);

			// $arr = json_decode($writeData);

			// if (count($arr) > 0) {
			// 	foreach ($arr as $ar) {
			// 		$res = $ar->geometry->coordinates;
			// 	}
			// }

			// return $res;

   //  	}

    	if ($data != null) {
    		$data = json_decode($data);
    		$res = $data->features[0]->geometry->coordinates;
    	}

    	return $res;
    }

    public function merchantReferralDetails_get()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		

		if(count($missing_key) == 0)
		{
			$this->load->library('session');
			$email_or_phone = $this->session->userdata('email_or_phone');

			$id = $this->getUserByEmailOrPhone($email_or_phone)->id;

			$referred_users = $this->db->query("SELECT concat(FM_customer.first_name, ' ', FM_customer.last_name) as name, FM_customer.created_date as installation_date, (SELECT COUNT(FM_order.id) FROM FM_order WHERE FM_order.customer_id = FM_customer.id) as number_of_orders, (SELECT SUM(FM_order.total_price) FROM FM_order WHERE FM_order.customer_id = FM_customer.id) as order_value, (SELECT FM_order.created_date FROM FM_order WHERE FM_order.customer_id = FM_customer.id ORDER BY FM_order.created_date DESC LIMIT 0,1) as last_order_date FROM FM_customer WHERE FM_customer.referral_by = '$id'")->result();

			$user = array();

			if (count($referred_users) > 0) {
				
				foreach ($referred_users as $refUser) {
					
					$refUser->installation_date = date('d S M Y', strtotime($refUser->installation_date));
					$refUser->last_order_date = date('d / m / Y', strtotime($refUser->last_order_date));

					$user[] = $refUser;


				}

			}
			

		    $response_arr = array("status" => TRUE, "message" => "Referred Users", "users" => $user);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getSoilInformationApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check farm_id
		if($this->input->post('farm_id') == null)
		{
		    $missing_key[] = 'farm_id';
		}    
		else
		{
		    $farm_id = $this->input->post("farm_id");
		}

		if(count($missing_key) == 0)
		{
			
			$farm = $this->db->get_where('FM_farm', ['id' => $farm_id])->row();

			$soilInformation = $this->soilAndCropReport($farm);
			// $soilInformation = null;

			// if ($soilInformation != null && is_object($soilInformation)) {
			// 	if (str_contains($soilInformation['soilMoisture']['moistureValue'], 'Whoa')) {
			// 		$soilInformation = null;
			// 		$response_arr = array("success" => TRUE, "message" => "Max Request Limit Reached. Try after sometimes", "soilInformation" => $soilInformation);
			// 	}
			// 	else{
			// 		if (!isset($soilInformation['soilMoisture']['moistureValue'])) {
			// 			$soilInformation['soilMoisture'] = null;
			// 			$response_arr = array("success" => TRUE, "message" => "Soil Information Not Found", "soilInformation" => $soilInformation);	
			// 		}
			// 		else{
			// 			$response_arr = array("success" => TRUE, "message" => "Soil Information Found", "soilInformation" => $soilInformation);
			// 		}
			// 	}
			// }
			// else
			// 	$response_arr = array("success" => TRUE, "message" => "Soil Information not Found", "soilInformation" => null);

			$err = [];

			$soilInformation['id'] = 1;

			if ($soilInformation['soilMoisture']['image'] == 'Error') {
				$err[] = 'Soil Moisture Report';
				$soilInformation['soilMoisture'] = new stdClass;
				$soilInformation['soilMoisture']->isAvailable = false;
			}
			else{
				$soilInformation['soilMoisture']['isAvailable'] = true;
				$soilInformation['soilMoisture']['unit'] = 'pH';
				$soilInformation['soilMoisture']['moistureRange'] = '0-1';
			}
			if (!is_object($soilInformation['cropHealthReport'])) {
				$err[] = 'Crop Health Report';
				$soilInformation['cropHealthReport'] = new stdClass;
				$soilInformation['cropHealthReport']->isAvailable = false;
			}
			else{
				$soilInformation['cropHealthReport']->isAvailable = true;
			}

			if (!is_object($soilInformation['cropGrowthReport'])) {
				$err[] = 'Crop Growth Report';
				$soilInformation['cropGrowthReport'] = new stdClass;
				$soilInformation['cropGrowthReport']->isAvailable = false;
			}
			else{
				$soilInformation['cropGrowthReport']->isAvailable = true;	
			}

			$missingReports = implode(', ', $err);
			$msg = $missingReports." - Not Found";

			$response_arr = array("success" => TRUE, "message" => ($msg != " - Not Found") ? $msg : 'soil Information Found', "soilInformation" => $soilInformation);
				
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function getListOfRecommendedProducts_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}


		// check farm_id
		if($this->input->post('farm_id') == null)
		{
		    $missing_key[] = 'farm_id';
		}    
		else
		{
		    $farm_id = $this->input->post("farm_id");
		}

		if(count($missing_key) == 0)
		{
			$data = $this->getRecommendedProducts($user_id);
			if(count($data)>0)
			{
				$response = array(
					"success" => true,
					"message" => "Recommended Products List fetched successfully.",
					"listOfRecommendedProducts" => $data
				);
			}
			else
			{
				$response = array(
					"success" => false,
					"message" => "No Recommended Products Found!",
					"listOfRecommendedProducts" => $data
				);
			}
			$this->response($response,REST_Controller::HTTP_OK); 
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getFirstVeriationIdByProductId($product_id)
    {
    	$SQL = "SELECT id FROM FM_product_variation WHERE status='Y' AND product_id='$product_id' ORDER BY created_date DESC";
    	$veriation_id = $this->db->query($SQL)->row()->id;
    	return $veriation_id;
    }

    public function getRecommendedProductsIdByUserId($user_id)
    {
    	$Product_Veriation_ID_List = array();
    	$SQL = "SELECT FMRP.product_id FROM FM_report FMR 
    			INNER JOIN FM_recommended_products FMRP ON FMRP.report_id=FMR.id 
    			WHERE FMR.status='C' AND FMRP.status='A' AND FMR.user_id='$user_id' 
    			ORDER BY FMR.update_date DESC";

    	$Product_ID_List = $this->db->query($SQL)->result();

    	if(count($Product_ID_List)>0)
    	{
    		foreach($Product_ID_List as $Product){
	    		$Product_Veriation_ID_List[] = $this->getFirstVeriationIdByProductId($Product->product_id);
	    	}
    	}
    	
    	return $Product_Veriation_ID_List;
    }

    public function getRecommendedProducts($user_id)
    {
    	$products = $this->getRecommendedProductsIdByUserId($user_id);
        if(count($products) > 0)
        {
            $quantity = 0;
            foreach($products as $row)
            {
                $variation_id = $row;
                $variation_details = $this->get_veriation_full_details_by_unique_id($variation_id);
                if($variation_details['availability_status'] == "Y")
                {
                    $quantity++;
                    $product_total = $variation_details['variation_details']['price_details']['price'];
                    $sale_price[] = $product_total;
                   

                    $wish_row[] = array("id" => $row, "quantity" => 1, "details" => $variation_details, "product_total" => round($product_total));
                }
                else
                {
                    $delete_data = array("is_deleted" => "Y", "updated_date" => date("Y-m-d H:i:s"));
                    $this->db->where("id", $row->id);
                    $this->db->update("FM_wish", $delete_data);
                    
                    $sale_price[]  = 0;
                    $product_total = 0;
                    
                }   

                

            }

            $wish_total = array_sum($sale_price);
            $wish_count = $quantity;

            foreach ($wish_row as $wrow) {
            	$variations[] = [
            		'id'					=> $wrow['details']['variation_details']['id'],
            		'title'					=> $wrow['details']['variation_details']['title'],
            		'price'					=> $wrow['details']['variation_details']['price_details']['price'],
            		'discount_percent'		=> $wrow['details']['variation_details']['price_details']['discount_percent'],
            		'discount_amount'		=> $wrow['details']['variation_details']['price_details']['discount_amount'],
            		'sale_price'			=> $wrow['details']['variation_details']['price_details']['price'],
            		'order'					=>'0',
            		'status'				=> ($wrow['details']['variation_details']['status'] == 'Y') ? true : false,
            		'wish_status'			=> $wrow['details']['variation_details']['wish_status'],
            	];
            }

            foreach ($wish_row as $wrow) {
            	
            	$wish_response[] = [
	            	'id'					=> $wrow['id'],
	            	'name'					=> $wrow['details']['product_details']['name'],
	            	'SKU'					=> $wrow['details']['product_details']['product_SKU'],
	            	'image_list'			=> $wrow['details']['product_details']['image'],
	            	'variation_list'		=> $variations,
	            	'title'					=> $wrow['details']['product_details']['name'],
	            	'description'			=> $wrow['details']['product_details']['product_description'],
	            	'variation_title'		=> $wrow['details']['variation_details']['title'],
	            	'price'					=> $wrow['details']['variation_details']['price_details']['price'],
	            	'discount_percent'		=> $wrow['details']['variation_details']['price_details']['discount_percent'],
	        		'discount_amount'		=> $wrow['details']['variation_details']['price_details']['discount_amount'],
	        		'sale_price'			=> $wrow['details']['variation_details']['price_details']['price'],
	        		'order'					=> '0',
	        		'status'				=> ($wrow['details']['variation_details']['status'] == 'Y') ? true : false,
	        		'wish_status'			=> $wrow['details']['variation_details']['wish_status'],
	        		'items_total'			=> $wish_count,
	        		'order_total'			=> $wish_total

	            ];

            }


            $response = $wish_response;
        }
        else
        {
            // no wish

            $response = $products;
        }

        return $response;
    }

    public function getCropGrowthInformation_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check farm_id
		if($this->input->post('farm_id') == null)
		{
		    $missing_key[] = 'farm_id';
		}    
		else
		{
		    $farm_id = $this->input->post("farm_id");
		}

		if(count($missing_key) == 0)
		{
			$cropGrowthReport = [
				'id'	=> '1',
				'reportImage'	=> "https://testing.surobhiagro.in/api/v2/assets/static/cropReportImage.png",
				'reportStatus'	=> 'Crop Growth is Excellent'
			];

		    $response_arr = array("success" => TRUE, "message" => "Crop Growth Report", "cropGrowthReport" => $cropGrowthReport);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getDownloadReportLink_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check farm_id
		if($this->input->post('farm_id') == null)
		{
		    $missing_key[] = 'farm_id';
		}    
		else
		{
		    $farm_id = $this->input->post("farm_id");
		}

		if(count($missing_key) == 0)
		{
			// $downloadReportLink = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';

			//check if already requested
			$checkRequested = $this->db->get_where('FM_report',['user_id' => $user_id, 'farm_id' => $farm_id])->result();

			if (count($checkRequested) > 0) {
				if ($checkRequested[0]->status == 'C') {
					$response_arr = array("success" => TRUE, "message" => "Report Found. Starting Download...", "reportDownloadLink" => STORE_URL.$checkRequested[0]->report_link);
				}
				else{
					$response_arr = array("success" => TRUE, "message" => "Your previously submitted report is still being generated. Have Patience", "reportDownloadLink" => '');
				}
			}
			else{
				$farm = $this->db->get_where('FM_farm', ['id' => $farm_id])->row();
				$crop = $this->db->select('title')->from('FM_crop')->where('id', $farm->crop_name)->get()->row()->title;
				$sowIngDate = $farm->sowing_date;

				if(strtolower($crop) == 'paddy' && $this->getDifferenceBetweenDate($sowIngDate) < 30){
					$response_arr = array("success" => TRUE, "message" => "Can't Submit Request. You can request for report after 30 days from planting date.", "reportDownloadLink" => '');	
				}
				else{
					$data = [
						'hash_id'	=> $this->GUID(),
						'user_id'	=> $user_id,
						'farm_id'	=> $farm_id,
						'request_date' => date('Y-m-d'),
						'status'	=> 'P',
					];

					$this->db->insert('FM_report', $data);
					if ($this->db->affected_rows() > 0) {
						$response_arr = array("success" => TRUE, "message" => "Request Submitted", "reportDownloadLink" => '');		
					}
					else{
						$response_arr = array("success" => TRUE, "message" => "Can't Submit Request. Try Again Later", "reportDownloadLink" => '');	
					}
				}
				
			}

		    // $response_arr = array("success" => TRUE, "message" => "Download Report", "reportDownloadLink" => $downloadReportLink);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function getMerchantsAllReward_get()
    {
    	$this->load->library('session');

		$email_or_phone = $this->session->userdata('email_or_phone');

		$id = $this->getUserByEmailOrPhone($email_or_phone)->id;

		if ($id == null) {
			$response_arr = array("status" => TRUE, "message" => "Id not found", "merchantRewards" => $rewards);
			$this->response($response_arr, REST_Controller::HTTP_OK);
		}

		$rewards = $this->getUserReferredOrders($id);

		if (count($rewards) < 1) {
			$response_arr = array("status" => TRUE, "message" => "No rewards found", "merchantRewards" => $rewards);
			$this->response($response_arr, REST_Controller::HTTP_OK);
		}

		$response_arr = array("status" => TRUE, "message" => "Merchant Rewards", "merchantRewards" => $rewards);
	    $this->response($response_arr, REST_Controller::HTTP_OK);
    }

    public function getUserReferredOrders($user_id, $limit = '*')
    {
    	// $allUsers = $this->db->select('id')->from('FM_customer')->where('status', 'Y')->get()->result();
    	$total_orders = array();

    	// foreach ($allUsers as $user) {
    	// 	if ($this->getRootReferrer($user->id) == $user_id) {
    	// 		$orders = $this->db->from('FM_order')->where('customer_id', $user->id)->get()->result();
    	// 		foreach ($orders as $order) {
    	// 			$order_date = $order->created_date;
					// $difference = $this->getDifferenceBetweenDate($order_date);
					// if ($difference >= 2) {
					// 	$userInfo = $this->db->select('first_name, last_name')->from('FM_customer')->where('id', $order->customer_id)->get()->row();
					// 	$orderStatus = ($order->status == 'D') ? 'Delivered' : 'Processing';
					// 	$data = [
					// 		'name'			=> $userInfo->first_name.' '.$userInfo->last_name,
					// 		'order_id'		=> $order->order_no,
					// 		'date'			=> date('dS M Y', strtotime($order->created_date)),
					// 		'order_status'	=> $orderStatus,
					// 		'discount_percentage'	=> 4
					// 	];
					// 	$total_orders[] = $data;
					// }
    	// 		}
    	// 	}
    	// }

    	//check and change reward status if needed
    	$this->checkAndChangeRewardStatus($user_id, 'M');

    	//get all available rewards of this merchant
    	$rewards = $this->db->get_where('FM_reward', ['receiver_id' => $user_id, 'status' => 'A'])->result();

    	//check if has reward
    	if (count($rewards) > 0) {
    		//itarate through rewards
    		foreach ($rewards as $reward) {
    			// check and if order was the of the reward
    			if (strtoupper($reward->event) == strtoupper("Order by descended user")) {
    				// get the order
    				$order = $this->db->get_where('FM_order', ['order_no' => $reward->source_id])->row();
    				//get the user information
    				$userInfo = $this->db->get_where('FM_customer', ['id'=> $order->customer_id])->row();
    				//get the order status
    				if($order->status == 'D')
    					$orderStatus = 'Delivered';
    				elseif($order->status == 'P')
    					$orderStatus = 'Processing';
    				elseif($order->status == 'C')
    					$orderStatus = 'Cancelled';

    				$totalReward = $this->getMerchantTotalReward($order->order_no);

    				// prepare response data
    				$data = [
						'name'			=> $userInfo->first_name.' '.$userInfo->last_name,
						'order_id'		=> $order->order_no,
						'date'			=> date('dS M Y', strtotime($order->created_date)),
						'order_status'	=> $orderStatus,
						'commision'		=> $totalReward
					];
					//store return data
					$total_orders[] = $data;
    			}
    		}
    	}

    	// check if a limit has been passed or not
    	if ($limit != '*') {
    		$total_orders = array_slice($total_orders, 0, $limit);
    	}
    	
    	// return response
    	return $total_orders;
    }

    public function getMerchantTotalReward($order_no)
    {
    	$orderId = $this->db->select('id')->from('FM_order')->where('order_no', $order_no)->get()->row()->id;
    	// echo $orderId;
    	$getList = $this->db->from('FM_order_details')->where('order_id',$orderId)->get()->result();
    	// $responseList = [];
    	$total = 0;
    	foreach ($getList as $list) {
    		$condition = ['id' => $list->variation_id, 'product_id' => $list->$product_id];
    		$temp = $this->db->select('merchant_commission')->from('FM_product_variation')->where($condition)->get()->row()->merchant_commission;
    		if ($temp != null) {
    			$total += $temp;
    		}
    		else{
    			$total += 0;	
    		}
    	
    	}

    	return $total;
    }

    public function logout_get()
    {
    	$this->load->library('session');

		$email_or_phone = $this->session->userdata('email_or_phone');

		$this->session->unset_userdata('email_or_phone');
    }

    public function updateRefCode_get()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		if(count($missing_key) == 0)
		{
			$allCustomers = $this->db->from('FM_customer')->get()->result();

			foreach ($allCustomers as $customer) {
				
				$ref_code = $this->getReferralCode($customer->id);
				$this->db->set('owned_referral_code', $ref_code);
				$this->db->where('id', $customer->id);
				$this->db->update('FM_customer');

			}

		    $response_arr = array("success" => TRUE, "message" => "Successful");	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function getRootReferrer_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			$rr = $this->getRootReferrer($user_id);
			$rrt = $this->getRootReferrerType($user_id);

		    $response_arr = array("success" => TRUE, "message" => "Data", "Root Referrer" => $rr, "Root Referrer Type" => $rrt);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getSupportDetails_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		if(count($missing_key) == 0)
		{
			$supportDetails = [
				'email'	=> 'company@surobhiagro.in',
				'contactNumber'	=> '+91 9073695511',
				'whatsAppNumber' => '+91 9073695511'
			];

		    $response_arr = array("success" => TRUE, "message" => "Support Details retrieved", "supportDetails" => $supportDetails);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function bookAFieldVisit_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		$visit_data = file_get_contents("php://input");

		// check unique_id
		if($visit_data == null)
		{
		    $missing_key[] = 'Data';
		}

		if(count($missing_key) == 0)
		{
			$data = json_decode($visit_data);

			if (isset($data->customer_name)) {
				// $user_info = $this->db->query("SELECT * FROM `FM_customer` WHERE email = '$data->email' or phone = '$data->mobile_number'")->row();
				// $user_id = ($user_info == null) ? null : $user_info->id;
				$insert_data = [
					'hash_id'		=> $this->GUID(),
					'user_id'		=> '',
					'full_name'		=> $data->customer_name,
					'phone'			=> $data->mobile_number,
					'address_1'		=> $data->street_address,
					'address_2'		=> $data->street_address1,
					'state'			=> $data->state,
					'pincode'		=> $data->pincode,
					'status'		=> 'A',
					'created_timestamp'	=> date('Y-m-d h:i:s'),
				];
				$this->db->insert('FM_field_visit_request', $insert_data);
				if ($this->db->affected_rows() > 0) {
					$response_arr = array("success" => TRUE, "message" => "Submitted Successfully", "isSubmitted" => true);
				}
				else{
					$response_arr = array("success" => TRUE, "message" => "Not submitted. Something went wrong", "isSubmitted" => false);
				}
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "Invalid visit data", "isSubmitted" => false);
			}
	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

  //   public function getWeatherForecast_post()
  //   {
  //   	$response_status = FALSE;
		// $response_message = "Something was wrong."; 

		// $missing_key = array();


		

		// if(count($missing_key) == 0)
		// {
			
		// 	$forecast[] = [
		// 		"id"	=> '1',
		// 		"weatherIcon"	=> 'https://testing.surobhiagro.in/api/v2/assets/static/sunny.png',
		// 		"temperature"	=> '18° C',
		// 		"weather"		=> "Clear Sky",
		// 		"updated_on"	=> date('h:i a d/m/Y')
		// 	];
		// 	$forecast[] = [
		// 		"id"	=> '2',
		// 		"weatherIcon"	=> 'https://testing.surobhiagro.in/api/v2/assets/static/sunny.png',
		// 		"temperature"	=> '17° C',
		// 		"weather"		=> "Clear Sky",
		// 		"updated_on"	=> date('d/m/Y', strtotime('+1 days'))
		// 	];
		// 	$forecast[] = [
		// 		"id"	=> '3',
		// 		"weatherIcon"	=> 'https://testing.surobhiagro.in/api/v2/assets/static/sunny.png',
		// 		"temperature"	=> '16° C',
		// 		"weather"		=> "Clear Sky",
		// 		"updated_on"	=> date('d/m/Y', strtotime('+2 days'))
		// 	];
		// 	$forecast[] = [
		// 		"id"	=> '4',
		// 		"weatherIcon"	=> 'https://testing.surobhiagro.in/api/v2/assets/static/sunny.png',
		// 		"temperature"	=> '15° C',
		// 		"weather"		=> "Clear Sky",
		// 		"updated_on"	=> date('d/m/Y', strtotime('+3 days'))
		// 	];
		// 	$forecast[] = [
		// 		"id"	=> '5',
		// 		"weatherIcon"	=> 'https://testing.surobhiagro.in/api/v2/assets/static/sunny.png',
		// 		"temperature"	=> '14° C',
		// 		"weather"		=> "Clear Sky",
		// 		"updated_on"	=> date('d/m/Y', strtotime('+4 days'))
		// 	];
		// 	$forecast[] = [
		// 		"id"	=> '6',
		// 		"weatherIcon"	=> 'https://testing.surobhiagro.in/api/v2/assets/static/sunny.png',
		// 		"temperature"	=> '13° C',
		// 		"weather"		=> "Clear Sky",
		// 		"updated_on"	=> date('d/m/Y', strtotime('+5 days'))
		// 	];
		// 	$forecast[] = [
		// 		"id"	=> '7',
		// 		"weatherIcon"	=> 'https://testing.surobhiagro.in/api/v2/assets/static/sunny.png',
		// 		"temperature"	=> '12° C',
		// 		"weather"		=> "Clear Sky",
		// 		"updated_on"	=> date('d/m/Y', strtotime('+6 days'))
		// 	];

		//     $response_arr = array("success" => TRUE, "message" => "Weather forecast", "forecasts" => $forecast);	
		//     $this->response($response_arr, REST_Controller::HTTP_OK);
		// }            
		// else
		// {
		//     $implode_missing_key = implode(', ', $missing_key);
		//     $response_message = $implode_missing_key." - not found";

		//     $response = array("success" => $response_status, "message" => $response_message);
		//     $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		// }
  //   }


    public function getWeatherForecast_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check lat
		if($this->input->post('lat') == null)
		{
		    $missing_key[] = 'latitude';
		}    
		else
		{
		    $lat = $this->input->post("lat");
		}

		// check lon
		if($this->input->post('lon') == null)
		{
		    $missing_key[] = 'longitude';
		}    
		else
		{
		    $lon = $this->input->post("lon");
		}

		if(count($missing_key) == 0)
		{
			$rawWeatherData = $this->get_seven_days_forecast($lat, $lon);
			$processedWeather = $this->process_weatherdata($rawWeatherData);



		    $response_arr = array("success" => TRUE, "message" => "Weather forecast", "forecasts" => $processedWeather);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function get_seven_days_forecast($lat, $lon)
    {
    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://data.kawa.space/missions_sync',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
		    "recipe_id": "weather_forecast_hourly",
		    "number_of_forecasts":148,
		    "custom_identifier": "",
		    "aoi": {
		        "type": "Feature",
		        "geometry": {
		            "type": "Point",
		            "coordinates": ['.
		         $lon.','.
		       $lat.'
		            ]
		        }
		    }
		}',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'x-api-key: kawa__dyDCYLAjauTywvp3VD3Dg'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
    }

    public function process_weatherdata($data)
    {
    	$data = json_decode($data)->data;
    	$responseData = [];

    	$dates = array_unique($data->validTimeLocal);

    	for ($i=0; $i < 148; $i++) { 
    		if ($i % 3 != 0) {
    			continue;
    		}
    		if ($i == 0 || strtotime(date('Y-m-d', strtotime($data->validTimeLocal[$i]))) != strtotime(date('Y-m-d', strtotime($data->validTimeLocal[$i-3])))) {
    			if (isset($response)) 
    				$responseData[] = $response;
    			$response['id'] = $i;
		    	$response['weatherIcon'] = $this->map_weather_with_icon($data->wxPhraseLong[$i]);
		    	$response['temparature'] = $data->temperature[$i].'°C';
		    	$response['weather'] = $data->wxPhraseLong[$i];
		    	$response['updated_on'] = date('d/m/Y', strtotime($data->validTimeLocal[$i]));
		    	// $response['time'] = date('h:i a', strtotime($data->validTimeLocal[$i]));
		    	$response['time'] = date('h:i a');
		    	$response['listOfHourlyForecast'] = [];
    		}
    		else{
    			$quarterData['id'] = $i;
		    	$quarterData['weatherIcon'] = $this->map_weather_with_icon($data->wxPhraseLong[$i]);
		    	$quarterData['temparature'] = $data->temperature[$i].'°C';
		    	$quarterData['weather'] = $data->wxPhraseLong[$i];
		    	$quarterData['updated_on'] = date('Y-m-d', strtotime($data->validTimeLocal[$i]));
		    	$quarterData['time'] = date('h:i a', strtotime($data->validTimeLocal[$i]));

		    	$response['listOfHourlyForecast'][] = $quarterData;
    		}
    	}

    	return $responseData;
    }

    public function map_weather_with_icon($weatherText)
    {
    	$weatherText = strtoupper($weatherText);
    	$image = STORE_URL;
    	if (str_contains($weatherText, 'SUN')) {
    		$image.= 'uploads/weatherIcons/sunny.png';
    	}
    	elseif (str_contains($weatherText, 'CLOUD')) {
    		$image.= 'uploads/weatherIcons/cloudy.png';
    	}
    	elseif (str_contains($weatherText, 'RAIN')) {
    		$image.= 'uploads/weatherIcons/rainy.png';
    	}
    	elseif (str_contains($weatherText, 'THUNDER') || str_contains($weatherText, 'STORM')) {
    		$image.= 'uploads/weatherIcons/thunderstorm.png';
    	}
    	elseif (str_contains($weatherText, 'CLEAR')) {
    		$image.= 'uploads/weatherIcons/sunny.png';
    	}
    	return $image;
    }

    public function blogDetailLike_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    // $missing_key[] = 'user_id';
		    $user_id = '';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check blog_id
		if($this->input->post('blog_id') == null)
		{
		    $missing_key[] = 'blog_id';
		}    
		else
		{
		    $blog_id = $this->input->post("blog_id");
		}

		// check like_value
		if($this->input->post('like_value') == null)
		{
		    $missing_key[] = 'like_value';
		}    
		else
		{
		    $like_value = $this->input->post("like_value");
		}

		if(count($missing_key) == 0)
		{
			if ($user_id != null && $user_id != "") {
				$condition_arr = ['customer_id' => $user_id, 'blog_id' => $blog_id];
				$query = $this->db->from('FM_blog_likes')->where($condition_arr)->get();
				$user_like = $query->num_rows();
				if ($user_like > 0) {
					$likeStatus = $query->row()->is_deleted;
					if ($likeStatus == 'Y' && $like_value == 1) {
						$update_arr = ['is_deleted' => 'N', 'updated_date' => date('Y-m-d h:i:s')];
						$this->db->where($condition_arr);
						$this->db->update('FM_blog_likes', $update_arr);
						$response_arr = array("success" => TRUE, "message" => "This blog is liked by user");
					}
					if ($likeStatus == 'Y' && $like_value == 0) {
						$response_arr = array("success" => TRUE, "message" => "This blog is disliked by user");
					}

					if ($likeStatus == 'N' && $like_value == 0) {
						$update_arr = ['is_deleted' => 'Y', 'updated_date' => date('Y-m-d h:i:s')];
						$this->db->where($condition_arr);
						$this->db->update('FM_blog_likes', $update_arr);
						$response_arr = array("success" => TRUE, "message" => "This blog is disliked by user");
					}
					if ($likeStatus == 'N' && $like_value == 1) {
						$response_arr = array("success" => TRUE, "message" => "This blog is liked by user");
					}
				}
				else{
					if ($like_value == 1) {
						$insertData = [
							'customer_id'	=> $user_id,
							'blog_id'		=> $blog_id,
							'created_date'	=> date('Y-m-d h:i:s'),
							'is_deleted'	=> 'N'
						];
						$this->db->insert('FM_blog_likes', $insertData);
						$response_arr = array("success" => TRUE, "message" => "This blog is liked by user");
					}
					else{
						$response_arr = array("success" => TRUE, "message" => "This blog is disliked by user");
					}
				}
				
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "Please login to like blogs");					
			}

		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function sendTopicLike_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    // $missing_key[] = 'user_id';
		    $user_id = '';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check topic_id
		if($this->input->post('topic_id') == null)
		{
		    $missing_key[] = 'topic_id';
		}    
		else
		{
		    $topic_id = $this->input->post("topic_id");
		}

		// check like_value
		if($this->input->post('like_value') == null)
		{
		    $missing_key[] = 'like_value';
		}    
		else
		{
		    $like_value = $this->input->post("like_value");
		}

		if(count($missing_key) == 0)
		{
			if ($user_id != null && $user_id != "") {
				$condition_arr = ['user_id' => $user_id, 'topic_id' => $topic_id];
				$query = $this->db->from('FM_community_likes')->where($condition_arr)->get();
				$user_like = $query->num_rows();
				if ($user_like > 0) {
					$likeStatus = $query->row()->is_deleted;
					if ($likeStatus == 'Y' && $like_value == 1) {
						$update_arr = ['is_deleted' => 'N', 'updated_date' => date('Y-m-d h:i:s')];
						$this->db->where($condition_arr);
						$this->db->update('FM_community_likes', $update_arr);
						$response_arr = array("success" => TRUE, "message" => "This topic is liked by user", 'isSubmitted' => true);
					}
					if ($likeStatus == 'Y' && $like_value == 0) {
						$response_arr = array("success" => TRUE, "message" => "This topic is disliked by user", 'isSubmitted' => true);
					}

					if ($likeStatus == 'N' && $like_value == 0) {
						$update_arr = ['is_deleted' => 'Y', 'updated_date' => date('Y-m-d h:i:s')];
						$this->db->where($condition_arr);
						$this->db->update('FM_community_likes', $update_arr);
						$response_arr = array("success" => TRUE, "message" => "This topic is disliked by user", 'isSubmitted' => true);
					}
					if ($likeStatus == 'N' && $like_value == 1) {
						$response_arr = array("success" => TRUE, "message" => "This topic is liked by user", 'isSubmitted' => true);
					}
				}
				else{
					if ($like_value == 1) {
						$insertData = [
							'user_id'	=> $user_id,
							'topic_id'		=> $topic_id,
							'created_date'	=> date('Y-m-d h:i:s'),
							'is_deleted'	=> 'N'
						];
						$this->db->insert('FM_community_likes', $insertData);
						$response_arr = array("success" => TRUE, "message" => "This topic is liked by user", 'isSubmitted' => true);
					}
					else{
						$response_arr = array("success" => TRUE, "message" => "This topic is disliked by user", 'isSubmitted' => true);
					}
				}
				
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "Please login to like topics", 'isSubmitted' => false);					
			}

		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getCropDoctorQuestionAnswerApi_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check crop_id
		if($this->input->post('crop_id') == null)
		{
		    $missing_key[] = 'crop_id';
		}    
		else
		{
		    $crop_id = $this->input->post("crop_id");
		}

		$imageList = array();

		if(count($missing_key) == 0)
		{
			$listOfQuestionsWithAnswers = array();
			$tellingArr = [
				'question'	=> 'Others',
				'answer'	=> '',
				'imageList' => []
			];
			$countQuestions = $this->db->select('COUNT(*) as count')->from('FM_questions')->where('crop_id', $crop_id)->where('status!=', 'D')->get()->row()->count;
			if ($countQuestions > 0) {
				$questions = $this->db->from('FM_questions')->where('crop_id', $crop_id)->where('status!=', 'D')->get()->result();
				foreach ($questions as $question) {
					$images = $this->db->get_where('FM_question_image', ['question_id' => $question->id])->result();
					foreach ($images as $image) {
						$imageList[] = [
							'id'	=> $image->id,
							'image'	=> STORE_URL.$image->image,
							'delete' => false
						];
					}
					$hasAnswer = $this->db->select('COUNT(*) as count')->from('FM_answers')->where('question_id', $question->id)->where('is_deleted!=', 'Y')->get()->row()->count;
					if ($hasAnswer > 0) {
						$answer = $this->db->from('FM_answers')->where('question_id', $question->id)->get()->row();
						$listOfQuestionsWithAnswers[] = [
							'question'	=> $question->title,
							'answer'	=> $answer->answer_text,
							'imageList'	=> $imageList
						];
					}
				}
				
				$listOfQuestionsWithAnswers[]= $tellingArr;
				$response_arr = array("success" => TRUE, "message" => "Question/Answers retrieved successfully", "listOfQuestionsWithAnswers" => $listOfQuestionsWithAnswers);
			}
			else{
				 $response_arr = array("success" => TRUE, "message" => "Question/Answers retrieved successfully", "listOfQuestionsWithAnswers" => array($tellingArr));
			}

		   	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function getWeatherApi_get()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		if(count($missing_key) == 0)
		{
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://data.kawa.space/missions_sync',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'{
			    "recipe_id": "weather_forecast_daily",
			    "number_of_forecasts":10,
			    "custom_identifier": "",
			    "aoi": {
			        "type": "Feature",
			        "geometry": {
			            "type": "Point",
			            "coordinates": [
			         80.507555,
			       			15.928844
			            ]
			        }
			    }
			}',
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json',
			    'x-api-key: kawa__dyDCYLAjauTywvp3VD3Dg'
			  ),
			));

			$response = curl_exec($curl);

		    $response_arr = array("success" => TRUE, "message" => "The message", "response" => $response);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function currentWeatherApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check lat
		if($this->input->post('lat') == null)
		{
		    $missing_key[] = 'latitude';
		}    
		else
		{
		    $lat = $this->input->post("lat");
		}


		// check lon
		if($this->input->post('lon') == null)
		{
		    $missing_key[] = 'longitude';
		}    
		else
		{
		    $lon = $this->input->post("lon");
		}

		if(count($missing_key) == 0)
		{
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=8f22c35eb0e4b82213b889a39d7a5104&units=metric",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
			    'x-api-key: 8f22c35eb0e4b82213b889a39d7a5104'
			  ),
			));

			$response = curl_exec($curl);
			$response = json_decode($response);
			$response->updateTimestamp = date('dd-MM-yyyy HH:mm:ss.sss');

		    $response_arr = array("success" => TRUE, "message" => "Weather retrived", "currentWeather" => $response);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getProductDetailsApi_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong.";

		$missing_key = array();

		// check unique_id
		if($this->input->post('product_id') == null)
		{
		    $missing_key[] = 'product_id';
		}    
		else
		{
		    $product_id = $this->input->post("product_id");
		}

		if(count($missing_key) == 0)
		{
			
			$details = $this->get_veriation_full_details($product_id);
			$variations = $details['variation_details'];
			$variation_arr = [];
			foreach ($variations as $variation) {
				$variation['price'] = '';
				$variation['discount_percent'] = 0;
				$variation['discount_amount'] = 0;
				$variation['sale_price'] = $variation['price_details']['price'];
				$variation_arr[] = $variation;
				unset($variation['price_details']);
				$limit = $this->getLimitOfProducts($variation['id']);
				$variation['limit'] = $limit;
			}

			$data = [
				'id'	=> $product_id,
				'name'	=> $details['product_details']['name'],
				'SKU'	=> $details['product_details']['product_SKU'],
				'image_list' => $details['product_details']['image'],
				'variation_list' => $variation_arr,
				'title'	=> $details['product_details']['name'],
				'description' => $details['product_details']['product_description'],
				'variation_title' => $details['variation_details'][0]['title'],
				'price' => '',
				'discount_percent' => $details['variation_details'][0]['price_details']['discount_percent'],
				'discount_amount' => $details['variation_details'][0]['price_details']['discount_amount'],
				'sale_price' => $details['variation_details'][0]['price_details']['price'],
				'Order'	=> '',
				'status' => $details['variation_details'][0]['status'],
				'wish_status' => $details['variation_details'][0]['wish_status'],
				'items_total' => $details['variation_details'][0]['price_details']['price'],
				'order_total'	=> 0,
			];
			$response_arr = array("success" => TRUE, "message" => "Product Found", "details" => $data);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    function getLimitOfProducts($variation_id){
    	$limit = 0;
    	if (in_array($variation_id, range(1,10))) {
    		$limit = 3;
    	}

    	if (in_array($variation_id, range(11,20))) {
    		$limit = 4;
    	}

    	if (in_array($variation_id, range(21,30))) {
    		$limit = -1;
    	}

    	if (in_array($variation_id, range(31,40))) {
    		$limit = 6;
    	}

    	if (in_array($variation_id, range(41,60))) {
    		$limit = 5;
    	}

    	if (in_array($variation_id, range(61,80))) {
    		$limit = 1;
    	}

    	if (in_array($variation_id, range(81,100))) {
    		$limit = 8;
    	}

    	if (in_array($variation_id, range(101,120))) {
    		$limit = 6;
    	}

    	if (in_array($variation_id, range(121,150))) {
    		$limit = 10;
    	}

    	if (in_array($variation_id, range(150,200))) {
    		$limit = 7;
    	}
    	return $limit;
    }

    public function chooseLanguageApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();
		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    // $missing_key[] = 'user_id';
		    $user_id = null;
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			$languages = $this->db->from('FM_languages')->get()->result();

			$data = [];

			if ($user_id == null) {
				foreach ($languages as $language) {
					$data[] = [
						'id'	=> $language->id,
						'language_name'	=> $language->language_name,
						'isSelected'	=> false
					];	
				}
			}
			else{
				$user = $this->db->from('FM_customer')->where('id', $user_id)->get()->row();
				foreach ($languages as $language) {
					$isSelected = false;
					if ($user->language == substr($language->language_name, 0,1)) {
						$isSelected = true;
					}	
					$data[] = [
						'id'	=> $language->id,
						'language_name'	=> $language->language_name,
						'isSelected'	=> $isSelected
					];
				}
			}

		    $response_arr = array("success" => TRUE, "message" => "Languages Found", "listOfLanguage" => $data);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function sendUserShippingAddressDetails_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		$data = file_get_contents("php://input");

		$userdata = json_decode($data);

		// check userdata
		if(!is_object($userdata))
		{
		    $missing_key[] = 'User Data';
		}    

		if(count($missing_key) == 0)
		{
			$user_id = $userdata->userId;
			$city_id = $this->get_city_id_by_pincode($userdata->userId);
			$state = $this->db->select('state_id')->from('FM_customer')->where('id', $user_id)->get()->row()->state_id;

			$data = [
				'name' => $userdata->customerName,
				'phone' => $userdata->phoneNumber,
				'zip_code'	=> $userdata->pinCode,
				'state_id'	=> $state,
				'address_1' => $userdata->houseAddress,
				'city_id'	=> $city_id,
				'landmark' => $userdata->areaLocation,
				'is_deleted' => 'N',
				'testing'	=> "Development_".date("F_Y")
			];

			$this->db->insert('FM_customer_address', $data);

		    $response_arr = array("success" => TRUE, "message" => "Address Saved Successfully", "isSubmitted" => true);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    function get_city_id_by_pincode($pincode = 0)
    {
        $city_id = 0;
        $this->db->select("city_id");
        $this->db->from("FM_pin_code_lookup");
        $this->db->where("pin_code", $pincode);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $row     = $query->row();
            $city_id = $row->city_id;
        }
        return $city_id;
    }

    public function getCartItemsDetails_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		$data = file_get_contents("php://input");

		$data = json_decode($data);

		// check userdata
		if(!is_array($data))
		{
		    $missing_key[] = 'Inputs';
		}    

		if(count($missing_key) == 0)
		{
			
			$items = $data;
			$c = 1;
			$output = [];
			foreach ($items as $item) {
				$variation = new stdClass;
				$tempArr = explode('_', $item->productVariation);
				$variation_id = $tempArr[1];

				$details = $this->get_veriation_full_details_by_unique_id($variation_id);
				$variation->id = $c;
				$variation->quantity = $item->quantity;
				$variation->details = $details;
				$output[] = $variation;
				$c++;
			}

		    $response_arr = array("success" => TRUE, "message" => "Cart Item Details retrieved", "cartItemDetails" => $output);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getAllStates_post($value='')
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		if(count($missing_key) == 0)
		{
			$states = $this->db->select('state')->from('FM_state_lookup')->where('is_deleted', 'Y')->get()->result();
			$stateArr = [];

			foreach ($states as $state) {
				$stateArr[] = ucwords(strtolower($state->state));
			}

		    $response_arr = array("success" => TRUE, "message" => "State List", "states" => $stateArr);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    function getAllProduceApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{

			$sell_produce = $this->db->get_where('FM_sell_produce', ['customer_id' => $user_id])->result();
			$products = [];

			foreach ($sell_produce as $sp) {
				$cropName = (is_object($this->db->from('FM_crop')->where('id', $sp->crop_id)->get()->row())) ? $this->db->from('FM_crop')->where('id', $sp->crop_id)->get()->row()->title : null;
				$lastAvailableDate = date('Y-m-d', strtotime("+$sp->available_in_days days"));
				if ($this->getDifferenceBetweenDate($lastAvailableDate) > 0) {
					$isAvailable = true;
				}
				else{
					$isAvailable = false;
				}
				$product = [
					'id'		=> $sp->id,
					'cropName'	=> $cropName,
					'variety'	=> $sp->variety,
					'userComment' => $sp->note,
					'expectedQty' => $sp->qty.' '.$sp->qty_unit,
					// 'quantityUnit' => ,
					'availableDays'=> $sp->available_in_days,
					'expectedDate' => $sp->available_date,
					'expectedPrice' => '₹'.$sp->price,
					'isAvailable' => $isAvailable,
					'isSold' => ($sp->status == 'S') ? true : false,
				];

				$products[] = $product;
			}

		    $response_arr = array("success" => TRUE, "message" => "Produces retrieved", "products" => $products);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getUserAllAddress_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			$addresses = $this->db->get_where('FM_customer_address', ['customer_id' => $user_id])->result();
			$listOfUserAddress = [];

			foreach ($addresses as $address) {
				$state = $this->db->get_where('FM_state_lookup', ['id' => $address->state_id])->row();
				$data = [
					'id'	=> $address->id,
					'address' => $address->address_1,
					'landmark'	=> $address->landmark,
					'phone'	=> $address->phone,
					'state'	=> (isset($state->state)) ? $state->state : null
				];

				$listOfUserAddress[] = $data;
			}

		    $response_arr = array("success" => TRUE, "message" => "User Addresses Found", "listOfUserAddress" => $listOfUserAddress);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getUserProfileDetailsApi_post()
    {
    	$response_status = FALSE;

		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			$userDetail = $this->db->get_where('FM_customer', ['id' => $user_id])->row();
			$userAddress = $this->db->get_where('FM_customer_address', ['customer_id' => $user_id])->row();
			$kycDocs = $this->db->get_where('FM_kyc_documents', ['user_id' => $user_id])->result();

			$landDoc = [];
			$voterDoc = [];
			$adhaarDoc = [];
			

			foreach ($kycDocs as $kd) {
				if ($kd->document_type == 'land document') {
					$landDoc[] = [
						'id'	=> $kd->id,
						'link'	=> STORE_URL.$kd->image
					];
				}
				if ($kd->document_type == 'voter card') {
					$voterDoc[] = [
						'id'	=> $kd->id,
						'link'	=> STORE_URL.$kd->image
					];
				}
				if ($kd->document_type == 'aadhar card') {
					$adhaarDoc[] = [
						'id'	=> $kd->id,
						'link'	=> STORE_URL.$kd->image
					];
				}
			}

			$lang = '';
			if ($userDetail->language == 'H') {
				$lang = 'Hindi';
			}
			if ($userDetail->language == 'B') {
				$lang = 'Bengali';
			}
			if ($userDetail->language == 'E') {
				$lang = 'English';
			}
			if ($userDetail->language == 'M') {
				$lang = 'Marathi';
			}


			$userProfileDetails = [
				'firstName'				=> $userDetail->first_name,
				'lastName'				=> $userDetail->last_name,
				'email'					=> $userDetail->email,
				'mobile'				=> $userDetail->phone,
				'selectedAppLanguage'	=> $lang,
				'address'				=> $userAddress->address_1,
				'listOfLandUnit'		=> [
					array('id'=>1, 'unit'=> 'Bigha'),
					array('id'=>2, 'unit'=> 'Acre'),
				],
				'kycDetails' 			=> [
					'listOfAadharCard'	=> $adhaarDoc,
					'listOfVoterCard'	=> $voterDoc,
					'listOfLandOwnershipValues'	=> $landDoc
				]
			];

		    $response_arr = array("success" => TRUE, "message" => "User profile data", "userProfileDetails" => $userProfileDetails);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    
  //   public function kawaTestApi()
  //   {
  //   	$response_status = FALSE;
		// $response_message = "Something was wrong."; 

		// $missing_key = array();

		// // check farm_id
		// if($this->input->post('farm_id') == null)
		// {
		//     $missing_key[] = 'farm_id';
		// }    
		// else
		// {
		//     $farm_id = $this->input->post("farm_id");
		// }

		// if(count($missing_key) == 0)
		// {
		// 	$farm = $this->db->select('farm_boundaries')->from('FM_farm')->where('id', $farm_id)->get()->row();
		// 	$soilMoisture = $this->getSoilMoisture($farm);
		// 	$kvi = $this->getKvi($farm);
		// 	$rvi = $this->getRvi($farm);


		//     $response_arr = array("success" => TRUE, "message" => "Data Found", "soilInformation" => array());	
		//     $this->response($response_arr, REST_Controller::HTTP_OK);
		// }            
		// else
		// {
		//     $implode_missing_key = implode(', ', $missing_key);
		//     $response_message = $implode_missing_key." - not found";

		//     $response = array("success" => $response_status, "message" => $response_message);
		//     $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		// }
  //   }


    public function soilAndCropReport($farm)
    {
    	if ($farm == null) {
    		return null;	
    	}

    	try{
    		$last_report = $this->db->query("select * from FM_last_report where updated_date > now() - INTERVAL 2 day and user_id = '$farm->user_id' AND farm_id = '$farm->id'")->row();
	    	// $this->db->get_where('FM_last_report', ['user_id' => $farm->user_id, 'farm_id' => $farm->farm_id])->row();



	    	if (is_object($last_report)) {
	    		$soilMoisture = $last_report->soil_moisture;
	    		$kvi = $last_report->kvi;
	    		$rvi = $last_report->rvi;
	    	}
	    	else{
	    		$soilMoisture = $this->getSoilMoisture($farm);
	    		$kvi = json_decode($this->getKvi($farm));
	    		$rvi = json_decode($this->getRvi($farm));

	    		// echo isset($soilMoisture->data);
	    		// echo $kvi;

	    		if (isset($soilMoisture->data) && isset($kvi->data) && isset($rvi->data)) {
	    			$lastReportData = [
		    			'hash_id'		=> $this->GUID(),
		    			'user_id'		=> $farm->user_id,
		    			'farm_id'		=> $farm->id,
		    			'soil_moisture' => json_encode($soilMoisture),
		    			'kvi'			=> json_encode($kvi),
		    			'rvi'			=> json_encode($rvi),
		    			'updated_date'	=> date('Y-m-d')
		    		];

		    		$this->db->insert('FM_last_report', $lastReportData);

		    		$reportRequest = $this->db->get_where('FM_report', ['user_id' => $farm->user_id, 'farm_id' => $farm->id, 'status' => 'P'])->row();

			    	if (is_object($reportRequest)) {
			    		$requestData = [
				    		'kvi'	=> json_encode($kvi),
				    		'rvi'	=> json_encode($rvi),
				    		'soil_moisture'	=> json_encode($soilMoisture),
				    		'update_date'	=> date('Y-m-d')
				    	];

				    	$this->db->set($requestData);
				    	$this->db->where('id', $reportRequest->id);
				    	$this->db->update('FM_report');
			    	}
	    		}
	    	}

	    	echo json_encode($soilMoisture);
	    	echo json_encode($kvi);
	    	echo json_encode($rvi);

	    	// $kvi = json_decode($this->getKvi($farm));
	    	// $rvi = json_decode($this->getRvi($farm));

	    	$soilMoistureInfo = (is_object($soilMoisture) ? $soilMoisture : json_decode($soilMoisture));
	    	$kviInfo = (is_object($kvi)) ? $kvi : json_decode($kvi);
	    	$rviInfo = (is_object($rvi)) ? $rvi : json_decode($rvi);

	    	// echo $kviInfo;

	    	$soilMoistureFinal = $kviFinal = $rviFinal = new stdClass;

	    	if(!isset($soilMoistureInfo->error))
	    		$soilMoistureFinal = $soilMoistureInfo->data[count($soilMoistureInfo->data)-1];
	    	else{
	    		$soilMoistureFinal->average_soil_moisture = $soilMoistureInfo->error;
	    		$soilMoistureFinal->image_url = 'Error';
	    	}
	    	if(!isset($kviInfo->error))
	    		$kviFinal = (is_object($kviInfo)) ? $kviInfo->data[count($kviInfo->data)-1] : $kviInfo;
	    	else
	    		$kviFinal->crop_growth_levels = $kviInfo->error;

	    	if(!isset($rviInfo->error))
	    		$rviFinal = (is_object($rviInfo) && $rviInfo->data != null) ? $rviInfo->data[count($rviInfo->data)-1] : $rviInfo;
	    	else
	    		$rviFinal->crop_health_levels = $rviInfo->error;
	    	

	    	

	    	$soilInformation = [
	    		'soilMoisture'	=>[
	    			'moistureValue'	=> (is_float($soilMoistureFinal->average_soil_moisture)) ? number_format($soilMoistureFinal->average_soil_moisture, 2) : $soilMoistureFinal->average_soil_moisture,
	    			'image'			=> $soilMoistureFinal->image_url,
	    		],
	    		'cropHealthReport'	=> (isset($kviFinal->crop_health_levels) && is_object($kviFinal->crop_health_levels)) ? $kviFinal->crop_health_levels : $kviFinal->crop_health_levels,
	    		'cropGrowthReport'	=> (isset($kviFinal->crop_growth_levels) && is_object($rviFinal->crop_growth_levels)) ? $rviFinal->crop_growth_levels : $rviFinal->crop_growth_levels
	    	];

	    	return $soilInformation;
    	}
    	catch(Exception $e){
    		$soilInformation = [
	    		'soilMoisture'	=>[
	    			'moistureValue'	=> "Something went wrong.. Try Again",
	    			'image'			=> 'Error',
	    		],
	    		'cropHealthReport'	=> "Something went wrong.. Try Again",
	    		'cropGrowthReport'	=> "Something went wrong.. Try Again"
	    	];
	    	return $soilInformation;
    	}

    	
    }

    public function getSoilMoisture($farm)
    {
    	$report = $this->db->get_where('FM_report', ['user_id' => $farm->user_id, 'farm_id' => $farm->id, 'status' => 'P'])->row();
    	if ($report == null) {
    		$start_date = date('Y-m-d', strtotime('-7 days'));
    		$end_date = date('Y-m-d');
    	}
    	else{
    		// $startTime = strtotime("+7 day", strtotime($report->request_date));
    		$start_date = date('Y-m-d', strtotime($farm->created_timestamp));
    		$end_date = $report->request_date;
    	}

    	$coordinates = $farm->farm_boundaries;

    	$data = json_decode('{"recipe_id":"soil_moisture","custom_identifier":"","start_date":"","end_date":"","aoi":{"type":"Feature","geometry":{"type":"Polygon","coordinates": '.$coordinates.'}}}');

    	$data->start_date = $start_date;
    	$data->end_date = $end_date;
    	// $data->aoi->geometry->coordinates = $coordinates;
    	$jsonData = json_encode($data);

    	// echo $jsonData;

    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://data.kawa.space/missions_sync',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$jsonData,	
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'x-api-key: kawa__dyDCYLAjauTywvp3VD3Dg'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$response = json_decode($response);

		if (isset($response->status) && $response->status == 'pending') {
			return $this->checkWithMissionStatus($response->missionID);

		}
		else{
			return $response;
		}
    }

    public function getKvi($farm)
    {
    	$report = $this->db->get_where('FM_report', ['user_id' => $farm->user_id, 'farm_id' => $farm->id, 'status' => 'P'])->row();
    	$crop = $this->db->get_where('FM_crop', ['id' => $farm->crop_name])->row();

    	if ($report == null) {
    		$start_date = date('Y-m-d', strtotime('-7 days'));
    		$end_date = date('Y-m-d');
    	}
    	else{
    		$start_date = date('Y-m-d', strtotime($farm->created_timestamp));
    		$end_date = $report->request_date;
    	}
    	$coordinates = $farm->farm_boundaries;

    	$data = json_decode('{"recipe_id":"kvi_threshold","custom_identifier":"","start_date":"","end_date":"","sowing_date":"","crop_type":"","aoi":{"type":"Feature","geometry":{"type":"Polygon","coordinates":'.$coordinates.'}}}');

    	$data->start_date = $start_date;
    	$data->end_date = $end_date;
    	// $data->aoi->geometry->coordinates = $coordinates;
    	$data->sowing_date = $farm->sowingDate;
    	$data->crop_type = ($crop->title == 'Paddy') ? 'rice' : strtolower($crop->title);

    	$jsonData = json_encode($data);

    	// echo $jsonData;

    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://data.kawa.space/missions_sync',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $jsonData,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'x-api-key: kawa__dyDCYLAjauTywvp3VD3Dg'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		if (isset($response->status) && $response->status == 'pending') {
			while ($response->status == 'pending') {
				$response = $this->checkWithMissionStatus($response->missionID);
			}
			return $response;
		}
		else{
			return $response;
		}
    }

    public function getRvi($farm)
    {
    	$report = $this->db->get_where('FM_report', ['user_id' => $farm->user_id, 'farm_id' => $farm->id, 'status' => 'P'])->row();
    	$crop = $this->db->get_where('FM_crop', ['id' => $farm->crop_name])->row();

    	if ($report == null) {
    		$start_date = date('Y-m-d', strtotime('-7 days'));
    		$end_date = date('Y-m-d');
    	}
    	else{
    		$start_date = date('Y-m-d', strtotime($farm->created_timestamp));
    		$end_date = $report->request_date;
    	}
    	$coordinates = $farm->farm_boundaries;

    	$data = json_decode('{"recipe_id":"rvi_threshold","custom_identifier":"","start_date":"","end_date":"","sowing_date":"","crop_type":"","aoi":{"type":"Feature","geometry":{"type":"Polygon","coordinates":'.$coordinates.'}}}');

    	$data->start_date = $start_date;
    	$data->end_date = $end_date;
    	// $data->aoi->geometry->coordinates = $coordinates;
    	$data->sowing_date = $farm->sowingDate;
    	$data->crop_type = ($crop->title == 'Paddy') ? 'rice' : strtolower($crop->title);

    	$jsonData = json_encode($data);


    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://data.kawa.space/missions_sync',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $jsonData,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'x-api-key: kawa__dyDCYLAjauTywvp3VD3Dg'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		if (isset($response->status) && $response->status == 'pending') {
			return $this->checkWithMissionStatus($response->missionID);

		}
		else{
			return $response;
		}
    }

    public function checkWithMissionStatus($mission_id)
    {
    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://data.kawa.space/status/'.$mission_id,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'x-api-key: kawa__dyDCYLAjauTywvp3VD3Dg'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
    }

    public function updateUserProfile_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}


		// check first name
		if($this->input->post('fname') == null)
		{
		    $missing_key[] = 'first name';
		}    
		else
		{
		    $fname = $this->input->post("fname");
		}


		// check lname
		if($this->input->post('lname') == null)
		{
		    $missing_key[] = 'last name';
		}    
		else
		{
		    $lname = $this->input->post("lname");
		}


		// check email
		if($this->input->post('email') == null)
		{
		 	$email = '';   
		}    
		else
		{
		    $email = $this->input->post("email");
		}


		// check mobile
		if($this->input->post('mobile') == null)
		{
		    $mobile = '';
		}    
		else
		{
		    $mobile = $this->input->post("mobile");
		}


		// check landArea
		if($this->input->post('landArea') == null)
		{
		    $missing_key[] = 'landArea';
		}    
		else
		{
		    $landArea = $this->input->post("landArea");
		}


		// check landUnit
		if($this->input->post('landUnit') == null)
		{
		    $missing_key[] = 'landUnit';
		}    
		else
		{
		    $landUnit = $this->input->post("landUnit");
		}


		if(count($missing_key) == 0)
		{
			if(!empty($_FILES['newly_added_voter']['name']))
            {
	            $filesCount = count($_FILES['newly_added_voter']['name']);

	            for ($i=0; $i<$filesCount; $i++) {
	                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/kycDocuments/';
	                $rand_name = time()."-".$i;
	                $upload_file = $upload_dir.$rand_name.basename($_FILES['newly_added_voter']['name'][$i]);
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/kycDocuments/'.$rand_name.basename($_FILES['newly_added_voter']['name'][$i]);
	                $actual_path = str_replace(" ","-",$actual_path);
	                if (move_uploaded_file($_FILES['newly_added_voter']['tmp_name'][$i], $upload_file))
                    {
                        $newly_added_voter [] = $actual_path;
                    }
                    else
                    {
                        $newly_added_voter = array();
                    }

	            }
	        }

	        if(!empty($_FILES['newly_added_aadhar']['name']))
            {
	            $filesCount = count($_FILES['newly_added_aadhar']['name']);

	            for ($i=0; $i<$filesCount; $i++) {
	                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/kycDocuments/';
	                $rand_name = time()."-".$i;
	                $upload_file = $upload_dir.$rand_name.basename($_FILES['newly_added_aadhar']['name'][$i]);
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/kycDocuments/'.$rand_name.basename($_FILES['newly_added_aadhar']['name'][$i]);
	                $actual_path = str_replace(" ","-",$actual_path);
	                if (move_uploaded_file($_FILES['newly_added_aadhar']['tmp_name'][$i], $upload_file))
                    {
                        $newly_added_aadhar [] = $actual_path;
                    }
                    else
                    {
                        $newly_added_aadhar = array();
                    }

	            }
	        }


	        if(!empty($_FILES['newly_added_landownership']['name']))
            {
	            $filesCount = count($_FILES['newly_added_landownership']['name']);

	            for ($i=0; $i<$filesCount; $i++) {
	                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/kycDocuments/';
	                $rand_name = time()."-".$i;
	                $upload_file = $upload_dir.$rand_name.basename($_FILES['newly_added_landownership']['name'][$i]);
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/kycDocuments/'.$rand_name.basename($_FILES['newly_added_landownership']['name'][$i]);
	                $actual_path = str_replace(" ","-",$actual_path);
	                if (move_uploaded_file($_FILES['newly_added_landownership']['tmp_name'][$i], $upload_file))
                    {
                        $newly_added_landownership [] = $actual_path;
                    }
                    else
                    {
                        $newly_added_landownership = array();
                    }

	            }
	        }

	        $customerDetails = [
	        	'first_name'	=> $fname,
	        	'last_name'		=> $lname,
	        	'email'			=> $email,
	        	'phone'			=> $mobile,
	        	'area_value'	=> $landArea,
	        	'area_unit'		=> $landUnit,
	        ];

	        $this->db->set($customerDetails)->where('id', $user_id)->update('FM_customer');

	        $main_kyc_doc = $this->db->select('kyc_document_name')->from('FM_customer')->where('id', $user_id)->get()->row()->kyc_document_name;

	        $deleted_voter = $this->input->post('deleted_voter');
	        if ($deleted_voter != null) {
	        	foreach ($deleted_voter as $dv) {
		        	$this->db->where('id', $dv)->delete('FM_kyc_documents');
		        }
	        }

	        $deleted_aadhar = $this->input->post('deleted_aadhar');
	        if ($deleted_aadhar != null) {
	        	foreach ($deleted_aadhar as $dv) {
		        	$this->db->where('id', $dv)->delete('FM_kyc_documents');
		        }
	        }

	        $deleted_landownership = $this->input->post('deleted_landownership');
	        if ($deleted_landownership != null) {
	        	foreach ($deleted_landownership as $dv) {
		        	$this->db->where('id', $dv)->delete('FM_kyc_documents');
		        }
	        }


	        if (isset($newly_added_voter) && count($newly_added_voter) > 0) {
	        	foreach ($newly_added_voter as $newVoter) {
		        	$data = [
		        		'hash_id'	=> $this->GUID(),
		        		'user_id'	=> $user_id,
		        		'document_type'	=> 'voter card',
		        		'image'			=> $newVoter,
		        	];

		        	$this->db->insert('FM_kyc_documents', $data);
		        }
	        }


	        if (isset($newly_added_aadhar) && count($newly_added_aadhar) > 0) {
	        	foreach ($newly_added_aadhar as $newAadhar) {
		        	$data = [
		        		'hash_id'	=> $this->GUID(),
		        		'user_id'	=> $user_id,
		        		'document_type'	=> 'aadhar card',
		        		'image'			=> $newAadhar,
		        	];
		        	$this->db->insert('FM_kyc_documents', $data);
		        }
	        }


	       	if (isset($newly_added_landownership) && count($newly_added_landownership) > 0) {
	       		foreach ($newly_added_landownership as $newLand) {
		        	$data = [
		        		'hash_id'	=> $this->GUID(),
		        		'user_id'	=> $user_id,
		        		'document_type'	=> 'land document',
		        		'image'			=> $newLand,
		        	];
		        	$this->db->insert('FM_kyc_documents', $data);
		        }
	       	}
			

		    $response_arr = array("success" => TRUE, "message" => "Updated Successfully", "isUpdated" => true);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function sendUserProduceData_post ()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}


		// check cropName
		if($this->input->post('cropName') == null)
		{
		    $missing_key[] = 'cropName';
		}    
		else
		{
		    $cropName = $this->input->post("cropName");
		}


		// check variety
		if($this->input->post('variety') == null)
		{
		    $missing_key[] = 'variety';
		}    
		else
		{
		    $variety = $this->input->post("variety");
		}


		// check userComment
		if($this->input->post('userComment') == null)
		{
		    $missing_key[] = 'userComment';
		}    
		else
		{
		    $userComment = $this->input->post("userComment");
		}


		// check expectedQty
		if($this->input->post('expectedQty') == null)
		{
		    $missing_key[] = 'expectedQty';
		}    
		else
		{
		    $expectedQty = $this->input->post("expectedQty");
		}


		// check quantityUnit
		if($this->input->post('quantityUnit') == null)
		{
		    $missing_key[] = 'quantityUnit';
		}    
		else
		{
		    $quantityUnit = $this->input->post("quantityUnit");
		}


		// check availableDays
		if($this->input->post('availableDays') == null)
		{
		    $missing_key[] = 'availableDays';
		}    
		else
		{
		    $availableDays = $this->input->post("availableDays");
		}

		// check expectedDate
		if($this->input->post('expectedDate') == null)
		{
		    $missing_key[] = 'expectedDate';
		}    
		else
		{
		    $expectedDate = $this->input->post("expectedDate");
		}

		// check expectedPrice
		if($this->input->post('expectedPrice') == null)
		{
		    $missing_key[] = 'expectedPrice';
		    // $expectedPrice = 0;
		}    
		else
		{
		    $expectedPrice = $this->input->post("expectedPrice");
		    $expectedPrice = substr($expectedPrice, 1, strlen($expectedPrice));
		    $expectedPrice = floatval($expectedPrice);
		}

		if(count($missing_key) == 0)
		{
			$data = [
				'customer_id'	=> $user_id,
				'crop_id'		=> $cropName,
				'variety'		=> $variety,
				'qty'			=> $expectedQty,
				'qty_unit'		=> $quantityUnit,
				'price'			=> $expectedPrice,
				'available_date'	=> date('Y-m-d', strtotime($expectedDate)),
				'available_in_days'	=> $availableDays,
				'note'				=> $userComment,
				'status'			=> 'A',
				'created_date'		=> date('Y-m-d'),
				'testing'			=> "Development_".date("F_Y")

			];

			$this->db->insert('FM_sell_produce', $data);
			$sp_id = $this->db->insert_id();

			if(!empty($_FILES['image_list']['name']))
            {
	            $filesCount = count($_FILES['image_list']['name']);

	            for ($i=0; $i<$filesCount; $i++) {
	                $upload_dir = FILE_UPLOAD_BASE_PATH.'uploads/sellproduce/';
	                $rand_name = time()."-".$i;
	                $upload_file = $upload_dir.$rand_name.basename($_FILES['image_list']['name'][$i]);
	                $upload_file = str_replace(" ","-",$upload_file);
	                $actual_path = 'uploads/sellproduce/'.$rand_name.basename($_FILES['image_list']['name'][$i]);
	                $actual_path = str_replace(" ","-",$actual_path);
	                if (move_uploaded_file($_FILES['image_list']['tmp_name'][$i], $upload_file))
                    {
                        $images [] = $actual_path;
                    }
                    else
                    {
                        $images = array();
                    }

	            }
	        }

	        if (isset($images) && !empty($images)) {
	        	foreach ($images as $image) {
	        		$imageData = [
	        			'sell_produce_id'	=> $sp_id,
	        			'image'				=> $image
	        		];
	        		$this->db->insert('FM_sell_produce_image', $imageData);
	        	}
	        }

		    $response_arr = array("success" => TRUE, "message" => "Sell Produce Submitted", "isSubmitted" => true);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function setProduceAvailability_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}


		// check selected_id
		if($this->input->post('selected_id') == null)
		{
		    $missing_key[] = 'selected produce';
		}    
		else
		{
		    $selected_id = $this->input->post("selected_id");
		}

		if(count($missing_key) == 0)
		{
			
			$product = $this->db->get_where('FM_sell_produce', ['id' => $selected_id])->row();
			$changedStatus = ($product->status == 'A') ? 'S' : 'A';

			$productData = [
				'status'	=> $changedStatus,
				'updated_date' => date('Y-m-d h:i:s')
			];

		    $this->db->set($productData)->where(['id' => $selected_id, 'customer_id' => $user_id])->update('FM_sell_produce');

		    if ($this->db->affected_rows() > 0) {
		    	$response_arr = array("success" => TRUE, "message" => "Status Changed Successfully", "isUpdated" => true);
		    }
		    else{
		    	$response_arr = array("success" => TRUE, "message" => "Status Change Failed", "isUpdated" => false);
		    }

		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getAllCrops_post()
    {
    	if($this->input->post('user_id')==null || !isset($_POST['user_id']))
		{
			$response = array(
				"success" => FALSE,
				"message" => "Please send user_id to get crop list.",
				"crops" => array()
			);
		}
		else
		{	
			$user_id = $this->input->post('user_id');
			$crop_id_array = $this->db->select("crop_id")
									  ->from("FM_customer_crop_mapping")
									  ->where("customer_id",$user_id)
									  ->order_by("id","DESC")
									  ->get()->result();
			if(count($crop_id_array)>0)
			{
				foreach($crop_id_array as $crop_details)
				{
					$crop_id[] = $crop_details->crop_id;
				}
			}

			$basepath_image_url = $this->db->select("content")
										   ->from("FM_preferences")
										   ->where("name","base_image_url")
										   ->get()->result()[0]->content;

			$crop_list_array = $this->db->select("*")
										->from("FM_crop")
										->where("status","Y")
										->order_by("id","DESC")
										->get()->result();
			if(count($crop_list_array)>0)
			{
				foreach($crop_list_array as $details)
				{
					if(isset($crop_id))
					{
						$isSelected = (in_array($details->id, $crop_id)?TRUE:FALSE);
						
					}
					else
					{
						$isSelected = False
						;
					}

					$crop = array(
						"id" => $details->id,
						"image" => $basepath_image_url.$details->image,
						"isSelected" => $isSelected,
						"name" => $details->title
					);

					$crop_list[] = $crop;
				}
			}

			$response = array(
				"success" => TRUE,
				"message" => "crops fetched successfully",
				"crops" => $crop_list
			);
		}

		$this->response($response, REST_Controller::HTTP_OK);
    }


    public function checkIsAccountCreated_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $response_arr = array("success" => TRUE, "message" => "User not regustered", "isCreated" => false);
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		if(count($missing_key) == 0)
		{
			
			$user = $this->db->get_where('FM_customer', ['id' => $user_id])->result();

			if (count($user) > 0) {
				$response_arr = array("success" => TRUE, "message" => "User is already regustered", "isCreated" => true);
			}
			else{
				$response_arr = array("success" => TRUE, "message" => "User not regustered", "isCreated" => false);
			}

		    // $response_arr = array("success" => TRUE, "message" => "The message", "data or response(rename it)" => $abc);	
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    
		    $this->response($response_arr, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function sendUserSelectedCrop_post($value='')
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		// check unique_id
		if($this->input->post('user_id') == null)
		{
		    $missing_key[] = 'user_id';
		}    
		else
		{
		    $user_id = $this->input->post("user_id");
		}

		// check selected_crop_ids
		if($this->input->post('selected_crop_ids') == null)
		{
		    $missing_key[] = 'Crops Id';
		}    
		else
		{
		    $selected_crop_ids = $this->input->post("selected_crop_ids");
		}

		if(count($missing_key) == 0)
		{
			foreach ($selected_crop_ids as $sc) {
				$data = [
					'customer_id'	=> $user_id,
					'crop_id'		=> $sc
				];

				$this->db->insert('FM_customer_crop_mapping', $data);
			}
			

		    $response_arr = array("success" => TRUE, "message" => "Submitted Successfully", "isSubmitted" => true);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function getAllProductsWithTabApi_post()
    {
    	$response_status = FALSE;
		$response_message = "Something was wrong."; 

		$missing_key = array();

		if(count($missing_key) == 0)
		{
			$condArr = array("status"=>"Y");
			$productTabs = $this->db->select("*")
						 ->from("FM_product_category")
						 ->where($condArr)
						 ->get()->result();

			if(count($productTabs)>0)
			{

				$productCategoryList = array();
				$productCategoryList[] = array('id' => "0", 'title' => 'All');

				foreach ($productTabs as $productTab) {
					$productCategory = array(
						"id" => $productTab->id,
						"title" => $productTab->title
					);

					$productCategoryList[] = $productCategory;
				}
			}

			// buy input below

			$products_details = [];
			$productLists = '*';
			
			if ($productLists == '*') {
				$condArr = ['status' => 'Y'];
				$products_details = $this->db->get_where("FM_product",$condArr)->result();				
			}
			else{
				foreach ($productLists as $productList) {
					$condArr = ['id' => $productList->product_id, 'status' => 'Y'];
					$pd = $this->db->get_where("FM_product",$condArr)->row();
					$products_details[] = $pd;
				}
				
			}

			// print_r($products_details);

			$buy_input_products = array();

			if(count($products_details)>0)
			{
				foreach ($products_details as $productDetails) {
					if (isset($productDetails->id) && $productDetails->id != null) {
						$basepath_image_url = $this->db->select("content")->from("FM_preferences")->where("name","base_image_url")->get()->result()[0];
						$basepath_image_url = $basepath_image_url->content;

						$image_condArr = array("product_id"=>$productDetails->id);
						$image_array = $this->db->get_where("FM_product_image",$image_condArr)->result();
						if(count($image_array)>0)
						{
							foreach($image_array as $image_details)
							{
								$image= array(
									"id" => $image_details->id,
									"image" => $basepath_image_url.$image_details->image
								);

								$image_list[] = $image;
							}
						}
						

						$var_condArr = array("product_id"=>$productDetails->id,"status!="=>"D");
						$var_row = $this->db->get_where("FM_product_variation",$var_condArr)->result();

						if(count($var_row)>0)
						{
							$var_list = array();
							for($j=0; $j<count($var_row); $j++)
							{
					            $sale_price = $var_row[$j]->price;
					            $sale_price = number_format($sale_price, 2);
					            $limit = $this->getLimitOfProducts($var_row[$j]->id);

					            if($var_row[$j]->status=="Y")
					            {
					            	$var_status = TRUE;
					            }
					            else
					            {
					            	$var_status = FALSE;
					            }

								$var_arr = array(
									"id" => $var_row[$j]->id,
					                "title" => $var_row[$j]->title,
					                "price" => '',
					                "discount_percent" => 0,
					                "discount_amount" => 0,
					                "sale_price" => $sale_price,
					                "order" => "$j",
					                "status" => $var_status,
					                "wish_status" => "N",
					                "limit"		=> $limit
								);

								$var_list[$j] = $var_arr;
							}
						}
						else
						{
							$var_list = array();
						}

			            $default_sale_price = $var_row[0]->price;
			            $default_sale_price = number_format($default_sale_price, 2);

						$details = array(
							"id" => $productDetails->id,
							"SKU" => $productDetails->SKU,
							"category" => intval($productDetails->category_id),
							"description" => $productDetails->short_description,
							"discount_amount" => 0,
							"discount_percent" => 0,
							"image_list" => $image_list,
							"items_total" => "",
							"name" => $productDetails->slug,
							"order" => "0",
							"order_total" => "",
							"price" => '',
							"sale_price" => $default_sale_price,
							"status" => TRUE,
							"title" => $productDetails->title,
							"variation_list" => $var_list,
							"variation_title" => $var_row[0]->title,
							"wish_status" => "N",
						);

						$buy_input_products[] = $details;
						$image_list = [];
					}
				}
			}

		    $response_arr = array("success" => TRUE, "message" => "Products with tabs fetched", "productTabs" => $productCategoryList, "buy_input_products" => $buy_input_products);
		    $this->response($response_arr, REST_Controller::HTTP_OK);
		}            
		else
		{
		    $implode_missing_key = implode(', ', $missing_key);
		    $response_message = $implode_missing_key." - not found";

		    $response = array("success" => $response_status, "message" => $response_message);
		    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
    }


    public function sendPushMessages($user_id, $body, $title) 
    {
    	if ($user_id == null) {
    		return;
    	}
    	$fcm = $this->db->select('fcm_token')->from('FM_customer')->where('id', $user_id)->get()->row()->fcm_token;
    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
		 "to" : "'.$fcm.'",
		 "notification" : {
		     "body" : "'.$body.'",
		     "title": "'.$title.'"
		 }
		}
		',
		  CURLOPT_HTTPHEADER => array(
		    'Authorization: key=AAAA3PV8wF0:APA91bEtzE-aMDPpt9p9xdacBSBxyRyVG7egubTipVOwLRdClf7FvQPzW1NeoVuKO5hE87yT9AnnRFAx9NIkEibEfe9_tMos0wUwB0Oa58I0CAwAk-PJkYwTGVeZdTizr9iu9Oqoo63P',
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		// echo $response;
    }
}


/*
$response_status = FALSE;
$response_message = "Something was wrong."; 

$missing_key = array();

// check unique_id
if($this->input->post('user_id') == null)
{
    $missing_key[] = 'user_id';
}    
else
{
    $user_id = $this->input->post("user_id");
}

if(count($missing_key) == 0)
{
	
	//processes to do if everything is fine

    $response_arr = array("success" => TRUE, "message" => "The message", "data or response(rename it)" => $abc);	
    $this->response($response_arr, REST_Controller::HTTP_OK);
}            
else
{
    $implode_missing_key = implode(', ', $missing_key);
    $response_message = $implode_missing_key." - not found";

    $response = array("success" => $response_status, "message" => $response_message);
    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
}

*/