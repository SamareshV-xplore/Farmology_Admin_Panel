<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="<?=base_url('assets/css/farmology_new_pages.css')?>"/>

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
    height: 260px;
    background-color: #f9f9f9;
    margin-top: 15px;
    padding: 10px;
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

  .add_recommendation_icons {
    color: #fff;
    font-size: 10px;
    background: #45cc7b;
    padding: 5px 6px;
    border-radius: 20px;
  }

</style>

<!-- Bootstrap Modal Start  -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div id="modal_header" class="modal-header">
        <h4 id="modal_title" class="modal-title modal_custom_title" id="exampleModalLabel">Recommend Products for Common Issues</h4>
        <button type="button" class="close" onclick="reset_and_close_modal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modal_body" class="modal-body">
        <div id="alert_container"></div>
        <form id="modal_form" onsubmit="add_recommendation(event)">
          
          <input type="hidden" id="hash_id" name="hash_id"/>
          <div style="display:flex; flex-flow:row; align-items:center; justify-content:start; margin-bottom:20px;">
            <h3 style="margin:0;">
              <small>EPPO Code</small>
              <div id="eppo_code"></div>
            </h3>
            <h3 style="margin:0; margin-left:20px;">
              <small>PEAT ID</small>
              <div id="peat_id"></div>
            </h3>
          </div>

          <h3 style="margin: 0px;">
            <small>Recommend Products</small>
          </h3>
          <div class="form-group">
            <div id="add_suggested_product_controls" class="inline_flex" style="width: 100%;">
              <select id="select_product" class="form-control select_product_control" style="width: 80%;">
                <option value="0">Select Product</option>
                <?php 
                  foreach ($products as $product) {
                ?>
                  <option value="<?= $product->id ?>"><?= $product->title ?></option>
                <?php
                  }
                ?>
              </select>
              <div class="button_holder" id="add_product_button" style="width: 20%;">
                <button type="button" id="add_product" class="addProductButton btn btn-primary disabled_button" onclick="productAddClick()" disabled="disabled" style="width: 100%;">Add Product</button>
              </div>
            </div>
            <div id="suggested_products_container"></div>
          </div>
        </form>
        <div class="form-group" style="margin: 0px;">
          <button form="modal_form" id="modal_form_submit_button" class="btn btn-success">Add Recommendation</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap Modal End -->

<div class="content-wrapper">
  <section class="content-header header-with-button">
    <h3 class="m-0">Plantix Product Recommendations</h3>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">

            <div class="table-responsive">
              <table id="product_recommendations_listing_table" class="table table-striped vertical-align-middle display">
                <thead>
                    <tr>
                        <th width="5%" style="text-align:left;">#</th>
                        <th width="10%" style="text-align:left;">EPPO Code</th>
                        <th width="10%" style="text-align:left;">PEAT ID</th>
                        <th width="55%" style="text-align:left;">Recommended Products</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if (!empty($plant_diagnosis_product_recommendations)) {
                  for ($i=0; $i<count($plant_diagnosis_product_recommendations); $i++) { 
                  $recommendations = $plant_diagnosis_product_recommendations[$i];?>
                    <tr id="<?=$recommendations->hash_id?>">
                        <td width="5%" style="text-align:left;"><?=$i+1?></td>
                        <td width="10%" style="text-align:left;" class="eppo_code">
                          <?=(!empty($recommendations->eppo_code)) ? $recommendations->eppo_code : "<span class='text-muted' class='font-weight:600;'>N/A</span>"?>
                        </td>
                        <td width="10%" style="text-align:left;" class="peat_id">
                          <?=(!empty($recommendations->peat_id)) ? $recommendations->peat_id : "<span class='text-muted' class='font-weight:600;'>N/A</span>"?>
                        </td>
                        <input type="hidden" class="recommended_products" value="<?=$recommendations->recommended_products?>"/>
                        <td width="55%" style="text-align:left;">
                            <?php if (!empty($recommendations->recommended_product_names)) {
                                echo $recommendations->recommended_product_names;
                            } else {
                                echo "-";
                            }?>
                        </td>
                        <td width="10%">
                          <?php if (!empty($recommendations->recommended_products)) { ?>
                            <a href="javascript:edit_recommendation('<?=$recommendations->hash_id?>')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                          <?php } else { ?>
                            <a href="javascript:edit_recommendation('<?=$recommendations->hash_id?>')"><i class="fa fa-plus add_recommendation_icons" aria-hidden="true"></i></a>
                          <?php } ?>
                            <a href="javascript:delete_recommendation('<?=$recommendations->hash_id?>')"><i class="fa-solid fa-xmark cross_icons"></i></a>
                        </td>
                    </tr>
                  <?php }} ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
  
  $(document).ready(function(){
    $("#product_recommendations_listing_table").DataTable({
      "language": {
        "emptyTable": "No product recommendations is available"
      },
      "order": [[0, "desc"]]
    });
  });

  function add_recommendation(e)
  {
    e.preventDefault();
    var form = document.getElementById("modal_form");
    var postData = new FormData(form);
    var suggested_products = getSuggestedProducts();

    if(Object.keys(suggested_products).length > 0)
    {
      postData.append("recommended_products", JSON.stringify(suggested_products));
    }
    
    $.ajax({
      url: "<?=base_url('plantix/edit-product-recommendation')?>",
      type: "POST",
      data: postData,
      contentType: false,
      processData: false,
      error: function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function (data)
      {
        if (data.success)
        {
          location.reload();
        }
        else
        {
          show_modal_alert("failed", data.message);
          setTimeout(() => {
            $("#modal_alert").fadeOut();
          }, 5000);
        }
      }
    })
  }

  function edit_recommendation(id)
  {
    var eppo_code = $("#"+id).children(".eppo_code").text();
    var peat_id = $("#"+id).children(".peat_id").text();
    var recommended_products = $("#"+id).children(".recommended_products").val();
    $("#hash_id").val(id);
    $("#eppo_code").text(eppo_code);
    $("#peat_id").text(peat_id);

    if (recommended_products.length > 0)
    {
      getPreviouslySuggestedProducts(recommended_products);
      $("#modal_form_submit_button").removeClass("btn-success");
      $("#modal_form_submit_button").addClass("btn-primary");
      $("#modal_form_submit_button").text("Edit Recommendation");
    }
    else
    {
      $("#modal_form_submit_button").removeClass("btn-primary");
      $("#modal_form_submit_button").addClass("btn-success");
      $("#modal_form_submit_button").text("Add Recommendation");
    }

    $("#modal").modal("show");
  }

  function delete_recommendation (id)
  {
    console.log(id);
  }

  function reset_and_close_modal ()
  {
    $("#modal").modal("hide");
    $("#modal_form")[0].reset();
    $("#hash_id").val();
    $("#suggested_products_container").html("");
  }

  function show_modal_alert (type, message)
  {
    var alert = `<div id="modal_alert" class="alert alert-info alert-dismissible">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  ${message}
                </div>`;

    if (type == "success")
    {
      alert = `<div id="modal_alert" class="alert alert-success alert-dismissible">
                <a class="close" data-dismiss="alert" aria-label="close">&times;</a>
                ${message}
              </div>`;
    }
    else if (type == "failed")
    {
      alert = `<div id="modal_alert" class="alert alert-danger alert-dismissible">
                <a class="close" data-dismiss="alert" aria-label="close">&times;</a>
                ${message}
              </div>`;
    }

    $("#alert_container").html(alert);
  }

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

  function productAddClick(){
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
        url: '<?=base_url("getProductByProductId")?>',
        type: 'post',
        data: {id : product_id},
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
        success: function (response) {
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
    $("#suggested_products_container").append(product);
  }

  function removeProduct(ProductID)
  {
    $("#SP"+ProductID).remove();
  }

  function getSuggestedProducts()
  {
    var suggestedProducts = {};
    var products = document.getElementsByClassName("products");
    for(let i=0; i<products.length; i++)
    {
      var ProductID = products[i].getAttribute("id");
      suggestedProducts[i] = ProductID.replace("SP","");
    }
    
    return suggestedProducts;
  }

  function getPreviouslySuggestedProducts (product_ids)
  {
    $.ajax({
      url: "<?=base_url("get_previously_suggested_products")?>",
      type: "POST",
      data: "product_ids="+product_ids,
      error: function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function (data)
      {
        if (data.success)
        {
          addPreviouslySuggestedProducts(data.products);
        }
      }
    });
  }

  function addPreviouslySuggestedProducts (products)
  {
    var products_html = "";
    for (let i=0; i<products.length; i++)
    {
      products_html += renderProductStructure(products[i]);
    }
    $("#suggested_products_container").html(products_html);
  }

</script>