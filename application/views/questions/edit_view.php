<script> var can_submit_form = false; </script>
<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<style>

  .inline_flex {
    display: flex;
    flex-flow: row;
    justify-content: start;
    align-items: center;
  }

  .select_product_control {
    width: 300px;
    margin-right: 10px;
  }

  #add_product_button {
    width: 60px;
    margin: 0;
  }

  #suggested_products_container {
    width: 100%;
    height: 300px;
    margin-top: 15px;
    display: flex;
    flex-wrap: wrap;
    justify-content: start;
    overflow-y: auto;
  }

  #suggested_products_container::-webkit-scrollbar-track {
    width: 8px;
    background: #f5f5f5;
  }

  #suggested_products_container::-webkit-scrollbar-thumb {
    width: 6px;
    background: #e8e8e8;
  }

  .products {
    width: 170px;
    max-height: 200px;
    margin: 0px 10px 10px 0px;
    background: #f2f2f2;
    border-radius: 5px;
    box-shadow: 0px 6px 8px -6px rgba(0, 0, 0, 0.6);
    display: flex;
    flex-flow: column;
    justify-items: center;
    align-content: space-evenly;
  }

  .product-image {
    width: 160px;
    height: 140px;
    margin: 5px;
    background: #fff;
    display: grid;
    place-items: center;
  }

  .product-details {
    width: 160px;
    margin: 0 5px 10px 5px;
    display: flex;
    flex-flow: row;
    justify-content: space-between;
    align-items: center;
  }

  .product-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-left: 6px;
  }

  .product-remove-button {
    margin: 0 6px 0 0;
    cursor: pointer;
  }

  .disabled_button {
    cursor: wait;
  }

</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Edit Question & Answer</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Question Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form id="question-form" method="POST" action="<?=base_url('questions/edit_submit')?>" enctype="multipart/form-data">
              <input type="hidden" id="question_id" name="question_id" value="<?=$question_details['id']?>">
              <input type="hidden" id="customer_id" name="customer_id" value="<?=$question_details['customer_id']?>">
              <div class="box-body">
              <?php if(!empty($question_details['image'])){ ?>
                <div class="form-group col-md-12">

                  <center><img style="height: 300px; width: auto;" src="<?=$question_details['image']?>" class="img-open"></center>

                </div>
              <?php } ?> 
                <div class="form-group col-md-6">
                  <label for="cate1">Choose Crop<span class="required_cls">*</span></label>
                  <select name="crop_id" id="crop" class="form-control">
                    <option value="0">Select Crop</option>
                    <?php
                    if(count($crop_list) > 0)
                    {
                      foreach($crop_list as $row)
                      {
                        ?>
                        <option value="<?php echo $row['id']; ?>" <?=$row['id'] == $question_details['crop_id'] ? ' selected="selected"' : '';?>><?php echo $row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label for="question">Question<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="question" name="question" placeholder="Question"  value="<?php echo $question_details['question']; ?>">
                </div>
                  <div class="form-group col-md-6">
                      <label for="answer_text">Answer<span class="required_cls">*</span></label>
                      <?php 
                        $answer = '';
                        if(!empty($question_details['answer'])){
                          $answer = $question_details['answer'];
                        }

                      ?>
                      <textarea class="form-control" id="answer_text" name="answer_text" placeholder="Answer"><?php echo $answer; ?></textarea>
                  </div>
                
                <div class="form-group col-md-6">
                  <label for="blood_group">Status<span class="required_cls">*</span></label>
                  <select class="form-control" name="status" id="status">
                    <option value="A"<?php if($question_details['status'] == 'A'){ echo "selected"; } ?>>Approved</option>
                    <option value="P"<?php if($question_details['status'] == 'P'){ echo "selected"; } ?>>Pending</option>
                  </select>
                </div>

                <div class="form-group col-md-12">
                  <div class="recommended_product_div">
                     <div class="addProductDiv">
                      <label for="select_product">Recommend Products</label>
                      <div id="add_suggested_product_controls" class="inline_flex">
                        <select id="select_product" class="form-control select_product_control">
                          <option value="0">Select Product</option>
                          <?php 
                            foreach ($products as $product) {
                          ?>
                            <option value="<?= $product->id ?>"><?= $product->title ?></option>
                          <?php
                            }
                          ?>
                        </select>
                        <div class="button_holder" id="add_product_button">
                          <button type="button" id="add_product" class="addProductButton btn btn-primary disabled_button" onclick="add_recommended_product()" disabled="disabled">Add Product</button>
                        </div>
                      </div>
                      <div id="suggested_products_container"></div>
                      <input type="hidden" name="suggested_products" id="suggested_products">
                     </div>
                  </div>
                </div>

              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">Update</button>
                <a href="<?php echo base_url('questions-list'); ?>">
                  <button type="button" class="btn btn-default pull-left">Cancel</button>
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>

  <?php 
    include APPPATH."views/modal_images.php";
  ?>
  
<script>

  $("#select_product").on("change",function(){
    if($(this).val()!=0)
    {
      $("#add_product").removeAttr("disabled");
      $("#add_product").removeClass("disabled_button");
    }
    else
    {
      $("#add_product").attr("disabled","disabled");
      $("#add_product").addClass("disabled_button");
    }
  });

  function add_recommended_product(){
    var product_id = $("#select_product").val();
    var ProductID = "SP"+product_id;
    var products = document.getElementsByClassName("products");
    var already_have = false;
    for(let i=0; i<products.length; i++)
    {
      var SPID = products[i].getAttribute("id");
      if(ProductID==SPID)
      {
        already_have = true;
      }
    }

    if(already_have)
    {
      var message = "Product Already Added!";
      swal(message);
    }
    else
    {
      $.ajax({
        url: "<?=base_url('get_recommended_product')?>/"+product_id,
        type: "GET",
        beforeSend : function(){
          $("#add_product").attr("disabled","disabled");
          $("#add_product").addClass("disabled_button");
        },
        complete : function(){
          $("#add_product").removeAttr("disabled");
          $("#add_product").removeClass("disabled_button");
        },
        error: function (a,b,c){
          console.log(a + ' ' + b + ' ' + c);
        },
        success: function(response){
          console.log(response);
          response = JSON.parse(response);
          if (response.success == true) {
            addProduct(response);
          }
          else {
            alert('Product Not Found');
          }
        }
      });
    }
  }

  function renderProductStructure(product)
  {
    var productHTML = `<div id="${'SP'+product.id}" class="products">
                        <div class="product-image"><img width="120px" height="120px" src="${product.image}"/></div>
                        <div class="product-details">
                          <span class="product-title">${product.title}</span>
                          <span class="product-remove-button" onclick="removeProduct('${product.id}')">
                            <img width="20px" height="20px" src="https://admin.surobhiagro.in/media/uploads/icon/delete.png"/>
                          </span>
                        </div>
                      </div>`;

    return productHTML;
  }

  function addProduct(response) {
    var product = renderProductStructure(response.product);
    var input = $("#suggested_products");
    var value = input.val();
    value += response.product.id+",";
    input.val(value);
    $("#suggested_products_container").append(product);
  }

  function removeProduct(ProductID)
  {
    var value = $("#suggested_products").val();
    var old_text = ProductID+",";
    var new_value = value.replace(old_text, "");
    $("#suggested_products").val(new_value);
    $("#SP"+ProductID).remove();
  }

</script>

  <script type="text/javascript">

    function edit_question_submit()
    {

      $('.form-control').removeClass('error_cls');
      var focusStatus = "N";
      var question = document.getElementById("question").value.trim();
      var answer_text = document.getElementById("answer_text").value.trim();
      var status = document.getElementById("status").value;
      var crop = document.getElementById("crop").value;

      if(crop == '0')
      {
        $('#crop').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#crop').focus();
            focusStatus = 'Y';
        }     
      }

      if(question == '')
      {
        $('#question').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#question').focus();
            focusStatus = 'Y';
        }     
      }
        if(answer_text == '')
        {
            $('#answer_text').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#answer_text').focus();
                focusStatus = 'Y';
            }
        }

        

      if(status == '')
      {
        $('#status').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#status').focus();
            focusStatus = 'Y';
        }     
      }

      if(focusStatus == "N")
      {

        // no validation error.. now submit the form
        $("#question-form").submit();
      }

      return false;
    }

  </script>


