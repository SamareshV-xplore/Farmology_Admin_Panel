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

<!-- Bootstrap Modal Start  -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="min-width: 960px !important;">
    <div class="modal-content">
      <div id="modal_header" class="modal-header">
        <h4 id="modal_title" class="modal-title modal_custom_title" id="exampleModalLabel">Generate Soil Health Report</h4>
        <button type="button" class="close" onclick="reset_and_close_modal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modal_body" class="modal-body">
        <div id="alert_container"></div>
        <form id="modal_form" onsubmit="add_generated_report(event)">
          
          <input type="hidden" id="hash_id" name="hash_id"/>
          <input type="hidden" id="request_id" name="request_id"/>

          <h4 style="margin: 0px;">Soil Health Report</h4>
          <hr style="margin: 5px 0px 10px 0px;"/>
          <div class="form-group">
            <table class="table table-borderless">
              <thead>
                <tr>
                  <th scope="col">Name</th>
                  <th scope="col">Unit</th>
                  <th scope="col">Value</th>
                  <th scope="col">Ideal Value</th>
                  <th scope="col">Rating</th>
                  <th scope="col">Range</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Nitrogen</td>
                  <td><input type="text" name="nitrogen_unit" value="Kg/Acre" required/></td>
                  <td><input type="text" name="nitrogen_value" required/></td>
                  <td><input type="text" name="nitrogen_ideal_value" required/></td>
                  <td><input type="text" name="nitrogen_rating" required/></td>
                  <td>
                    <select class="modal_select_input" name="nitrogen_range" required>
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Potassium</td>
                  <td><input type="text" name="potassium_unit" value="Kg/Acre" required/></td>
                  <td><input type="number" name="potassium_value" required/></td>
                  <td><input type="text" name="potassium_ideal_value" required/></td>
                  <td><input type="text" name="potassium_rating" required/></td>
                  <td>
                    <select class="modal_select_input" name="potassium_range" required>
                      <option value="poor">Poor</option>
                      <option value="average">Average</option>
                      <option value="good">Good</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Phosphorus</td>
                  <td><input type="text" name="phosphorus_unit" value="Kg/Acre" required/></td>
                  <td><input type="number" name="phosphorus_value" required/></td>
                  <td><input type="text" name="phosphorus_ideal_value" required/></td>
                  <td><input type="text" name="phosphorus_rating" required/></td>
                  <td>
                    <select class="modal_select_input" name="phosphorus_range" required>
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Organic Carbon</td>
                  <td><input type="text" name="organic_carbon_unit" value="%" required/></td>
                  <td><input type="number" name="organic_carbon_value" required/></td>
                  <td><input type="text" name="organic_carbon_ideal_value" required/></td>
                  <td><input type="text" name="organic_carbon_rating" required/></td>
                  <td>
                    <select class="modal_select_input" name="organic_carbon_range" required>
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Cation Exchange</td>
                  <td><input type="text" name="cation_exchange_unit" value="Meq/100Gm" required/></td>
                  <td><input type="number" name="cation_exchange_value" required/></td>
                  <td><input type="text" name="cation_exchange_ideal_value" required/></td>
                  <td><input type="text" name="cation_exchange_rating" required/></td>
                  <td>
                    <select class="modal_select_input" name="cation_exchange_range" required>
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Clay Content</td>
                  <td><input type="text" name="clay_content_unit" value="%" required/></td>
                  <td><input type="number" name="clay_content_value" required/></td>
                  <td><input type="text" name="clay_content_ideal_value" required/></td>
                  <td><input type="text" name="clay_content_rating" required/></td>
                  <td>
                    <select class="modal_select_input" name="clay_content_range" required>
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <h4 style="margin: 0px;">Expert Advice</h4>
          <hr style="margin: 5px 0px 10px 0px;"/>
          <div class="form-group">
            <textarea name="expert_advice" style="width:100%;height:300px;resize:none;overflow-y:auto;"></textarea>
          </div>

          <h4 style="margin: 0px;">Suggested Products</h4>
          <hr style="margin: 5px 0px 10px 0px;"/>
          <div class="form-group">
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
                <button id="add_product" class="addProductButton btn btn-primary disabled_button" onclick="productAddClick()" disabled="disabled">Add Product</button>
              </div>
            </div>
            <div id="suggested_products_container"></div>
          </div>
        </form>
        <div class="form-group">
          <button form="modal_form" id="modal_form_submit_button" class="btn btn-success form-control">Generate Report</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap Modal End -->

<div class="content-wrapper">
  <section class="content-header header-with-button">
    <h3>Soil Health Requests</h3>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">

            <div class="table-responsive">
              <table id="soil_health_requests_listing_table" class="table table-striped vertical-align-middle display">
                <thead>
                    <tr>
                        <th style="text-align:left;">#</th>
                        <th style="text-align:left;">Name</th>
                        <th>Mobile</th>
                        <th>Pincode</th>
                        <th>Farm Name</th>
                        <th>Land Size</th>
                        <th>Crop</th>
                        <th>Receipt</th>
                        <th>Sample</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Reports</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if (!empty($soil_health_test_requests)) {
                  for ($i=0; $i<count($soil_health_test_requests); $i++) { 
                  $request = $soil_health_test_requests[$i];?>
                    <tr id="<?=$request->hash_id?>">
                        <th style="text-align:left;"><?=$i+1?></th>
                        <td style="text-align:left;"><?=$request->name?></td>
                        <td><?=$request->mobile_number?></td>
                        <td><?=$request->pincode?></td>
                        <td><?=(!empty($request->farm_name)) ? $request->farm_name : $request->hash_id;?></td>
                        <td><?=$request->land_size?></td>
                        <td><?=$request->crop?></td>
                        <td><?=(!empty($request->receipt)) ? "<a href='".FRONT_URL.$request->receipt."' target='_blank'>view</a>" : "-";?></td>
                        <td class="on_off_btn_td">
                            <?php if (!empty($request->sample_received)) { ?>
                              <label for="checkbox_<?=$i+1?>" class="switch">
                                <input type="checkbox" id="checkbox_<?=$i+1?>" class="checkbox" onchange="sample_received(this,'<?=$request->hash_id?>')" checked>
                                <span class="slider round"></span>
                              </label>  
                            <?php } else { ?>
                              <label for="checkbox_<?=$i+1?>" class="switch">
                                <input type="checkbox" id="checkbox_<?=$i+1?>" onchange="sample_received(this,'<?=$request->hash_id?>')" class="checkbox">
                                <span class="slider round"></span>
                              </label> 
                            <?php } ?>
                        </td>
                        <td><?=(!empty($request->payment_amount)) ? $request->payment_amount : "-";?></td>
                        <td><?=(!empty($request->payment_date)) ? $request->payment_date : "-";?></td>
                        <td>
                          <?php if (!empty($request->payment_status)) { ?>
                            <button class="btn btn-success btn-sm rounded" onclick="generate_report('<?=$request->hash_id?>')">Generate Now</button>
                          <?php } else { ?>
                            <button class="btn btn-success btn-sm rounded" onclick="generate_report('<?=$request->hash_id?>')" disabled>Generate Now</button>
                          <?php } ?>
                        </td>
                        <td><?=ucwords($request->status)?></td>
                        <td>
                            <a href="javascript:delete_request('<?=$request->hash_id?>')"><i class="fa-solid fa-xmark cross_icons"></i></a>
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
    $("#soil_health_requests_listing_table").DataTable({
      "language": {
        "emptyTable": "No soil health test requests is available"
      },
      "order": [[0, "desc"]]
    });
  });

  function sample_received (checkbox, request_id) {
    var checkbox = $(checkbox);
    if (checkbox.prop("checked") == true)
    {
      var sample_received = 1;
    } 
    else 
    {
      var sample_received = 0;
    }
    var postData = {request_id: request_id, sample_received: sample_received};
    $.ajax({
      url: "<?=base_url("sample_received")?>",
      type: "POST",
      data: postData,
      error: function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function (data)
      {
        console.log(data);
        if (data.success)
        {
          location.reload();
        }
        else
        {
          console.log(data.message);
        }
      }
    });
  }

  function delete_request (request_id) {
    console.log(request_id);
  }

  function reset_and_close_modal ()
  {
    $("#modal").modal("hide");
    $("#modal_form")[0].reset();
    $("#hash_id").val();
    $("#modal_form").attr("onsubmit", "add_generated_report(event)");
    $("#modal_form_submit_button").removeClass("btn-primary");
    $("#modal_form_submit_button").addClass("btn-success");
    $("#modal_form_submit_button").text("Generate Report");
  }

  function generate_report (request_id)
  {
    $("#request_id").val(request_id);
    show_add_generate_report_modal();
  }

  function show_add_generate_report_modal ()
  {
    $("#modal").modal("show");
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

  function add_generated_report (e)
  {
    e.preventDefault();
    var form = document.getElementById("modal_form");
    var postData = new FormData(form);
    var suggested_products = getSuggestedProducts();

    if(Object.keys(suggested_products).length > 0)
    {
      postData.append("suggested_products", JSON.stringify(suggested_products));
    }
    
    $.ajax({
      url: "<?=base_url('generate_soil_health_report')?>",
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
        console.log(data);
        if (data.success)
        {
          $("#modal_form")[0].reset();
          $("#suggested_products_container").html("");
          previewSoilHealthReport(data.data);
          show_modal_alert("success", data.message);
          setTimeout(() => {
            reset_and_close_modal();
          }, 2000);
          setTimeout(() => {
            $("#modal_alert").fadeOut();
          }, 5000);
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

  function previewSoilHealthReport(report_data)
  {
    var form = document.createElement("form");
    form.method = "POST";
    form.action = "<?=base_url('preview-soil-health-report')?>"; 

    var input = document.createElement("input");
    input.type = "hidden";  
    input.value = report_data.report_id;
    input.name = "report_id";
    form.appendChild(input);  

    var input2 = document.createElement("input");
    input2.type = "hidden";  
    input2.value = report_data.report_pdf;
    input2.name = "report_pdf";
    form.appendChild(input2);
    
    var input3 = document.createElement("input");
    input3.type = "hidden";  
    input3.value = report_data.request_id;
    input3.name = "request_id";
    form.appendChild(input3); 

    document.body.appendChild(form);
    form.submit();
  }

</script>