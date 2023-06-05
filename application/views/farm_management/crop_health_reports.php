<link rel="stylesheet" href="<?=ASSETS_URL."css/expert_advice_view_style.css"?>"/>
<div class="content-wrapper">
  <section class="content-header"><h1>Crop Health Reports</h1></section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
            <form method="get" action="" id="filter_form">
              <input type="hidden" name="filter" value="true">
            
              <div class="form-group col-md-5">
                <label for="official_email">Filter by Status </label>
                <select name="status" id="status" class="form-control" onchange="return form_submit();">
                  <option value="all" <?php if($filter_data['status'] == 'all') { ?> selected <?php } ?>>All</option>
                  <option value="N" <?php if($filter_data['status'] == 'N') { ?> selected <?php } ?>>Incomplete</option>
                  <option value="C" <?php if($filter_data['status'] == 'C') { ?> selected <?php } ?>>Completed</option>
                  <!-- <option value="all">All</option>
                  <option value="new">Pending</option>
                  <option value="completed">Completed</option> -->
                </select>
              </div>

              <div class="form-group col-md-2" <?php if($filter_data['status'] !="N" && $filter_data['status'] !="C" ) { ?> style="display: none;" <?php } ?> >
                <a href="<?php echo base_url('blog'); ?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
              </div>

            </form>


          </div>
        </div>
      </div>
    </div>
    

    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <!--<h3 class="box-title">Data Table With Full Features</h3>-->
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="table-responsive">
            <table id="farm_report_table" class="table table-bordered table-striped">
              <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Customer Name</th>
                        <th style="width: 20%">Farm Name</th>
                        <th style="width: 10%">Crop Type</th>
                        <th style="width: 10%">Crop Stage</th>
                        <th style="width: 10%">Sowing Date</th>
                        <th style="width: 10%">NDVI (Crop Health)</th>
                        <th style="width: 10%">SAVI (Crop Growth)</th>
                        <th style="width: 10%">NDWI (Plant Moisture)</th>
                        <th style="width: 15%">Request Date</th>
                        <th style="width: 10%">Option</th>
                      
                    </tr>
              </thead>
              <tbody> 
              <?php if(count($request_list))
              {
                $rc = 0;
                foreach ($request_list as $i => $req_row)
                { ?>
                  <tr id="RPID<?=$req_row['id']?>">

                    <td style="width:5%"><?=$i+1?></td>

                    <td style="width:20%"><?=$req_row['customer_name']?></td>

                    <td style="width:20%"><?=$req_row['farm_name']?></td>

                    <td style="width:10%"><?=$req_row['crop_type']?></td>

                    <td style="width:10%"><?=$req_row['crop_stage']?></td>

                    <td style="width:10%"><?=$req_row['sowing_date']?></td>

                    <td style="width:10%">
                      <?php if (!empty($req_row['ndvi'])) { ?>
                        <span class="text-primary show-data" onclick="showData('ndvi', '<?=$req_row['ndvi']?>', '<?=$req_row['farm_id']?>')">
                          Show Data
                        </span>
                      <?php } else { ?>
                        <span class="text-muted">N/A</span>
                      <?php } ?>
                    </td>

                    <td style="width:10%">
                      <?php if (!empty($req_row['savi'])) { ?>
                        <span class="text-primary show-data" onclick="showData('savi', '<?=$req_row['savi']?>', '<?=$req_row['farm_id']?>')">
                          Show Data
                        </span>
                      <?php } else { ?>
                        <span class="text-muted">N/A</span>
                      <?php } ?>
                    </td>

                    <td style="width:10%">
                      <?php if (!empty($req_row['ndwi'])) { ?>
                        <span class="text-primary show-data" onclick="showData('ndwi', '<?=$req_row['ndwi']?>', '<?=$req_row['farm_id']?>')">
                          Show Data
                        </span>
                      <?php } else { ?>
                        <span class="text-muted">N/A</span>
                      <?php } ?>
                    </td>

                    <td style="width: 15%"><?=$req_row['req_date']?></td>

                    <td style="width: 10%; text-align: center;">

                    <!-- Farm Report Related Option -->
                    <?php if($req_row['status']=='P'){ ?>
                        
                        <img src="<?=FRONT_URL.'uploads/icon/report.png'?>" 
                        title="Generate Report" onclick="reportGenerate('<?=$req_row['id']?>')" 
                        style="cursor:pointer; height:30px;">
                
                          <?php } else { ?>
                                    
                        <img src="<?=FRONT_URL.'uploads/icon/complete.png'?>" 
                        alt="Completed" style="height:30px; margin:auto;"/>

                    <?php } ?>

                      <!-- Farm Report and Farm Delete Option -->
                      <a href="<?=base_url('delete_farm/'.$req_row['farm_id'])?>" onclick="return confirm('Are you sure want to permanently delete this farm and all of it\'s generated reports ?')">
                          <button type="button" id="farm_delete_button" class="btn bg-red btn-sm" title="Delete Farm">
                            <i class="fa fa-trash"></i>
                          </button>
                      </a>
                    </td>
                  </tr>
                <?php 
                $rc++;
                }
              }?> 
              </tbody>
              
            </table>
          </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
</div>

<!-- Show Data Modal Start -->
<div class="modal fade" id="show_data_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div>
    <div>
      <div class="show-data-modal-header">
        <h4 class="modal-title" id="show_data_modal_title"></h4>
        <button type="button" class="close" onclick="close_show_data_modal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="value_container" class="row" style="display:none;">
          <div class="col-md-12">
            <h2 id="data_value" class="data-values"></h2>
          </div>
          <div id="report_images_container"></div>
        </div>
        <img id="loading_image" src="<?=FRONT_URL.'uploads/images/loading.gif'?>" alt="Loading GIF" class="data-images" style="display:block;"/>
      </div>
    </div>
  </div>
</div>
<!-- Show Data Modal End -->

<!-- Farm Report Modal Start -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Expert Advice</h3>
        <hr>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -70px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body container-fluid">
        <div class="row">
           <div class="col-md-6">
              <div class="advice-div">
                <h4>Add Report</h4>
                <textarea class="form-control" rows="18" id="reportText"></textarea>
              </div>
           </div>
           <div class="col-md-6">
              <div class="recommended_product_div">
                 <div class="addProductDiv">
                    <div class="row">
                      <div class="col-md-12 col-sm-12">
                        <h4>Add Recommended Product</h4>
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
                    </div>
                 </div>
              </div>
           </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="generate_report" class="btn btn-success">Generate Report</button>
      </div>
    </div>
  </div>
</div>
<!-- Farm Report Modal End -->

<script type="text/javascript">
  
  $(document).ready(function(){
    
    $("#farm_report_table").DataTable({
      "order": [[0, 'desc']]
    });

  });

  function form_submit()
  {
    $("#filter_form").submit();
  }
  
  function showData(type, value, farm_id)
  {
    $("#loading_image").show();
    $("#value_container").hide();
    $("#report_images_container").html("");
    
    if (type == "ndvi")
    {
      $("#show_data_modal_title").text("NDVI Data (Crop Health)");
    }
    else if (type == "savi")
    {
      $("#show_data_modal_title").text("SAVI Data (Crop Growth)");
    }
    else if (type == "ndwi")
    {
      $("#show_data_modal_title").text("NDWI Data (Plant Moisture)");
    }

    $("#data_value").text(value+" out of 100");
    $("#show_data_modal").modal("show");

    var postData = {"farm_id": farm_id, "image_type": type};
    $.ajax({
      url: "<?=base_url('getFarmReportImages')?>",
      type: "POST",
      data: postData,
      error: function(a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
        toast("Something went wrong! Please try again later.", 1500);
        renderReportImages();
      },
      success: function(response)
      {
        if (response.success = true)
        {
          renderReportImages(response.reportImages);
        }
        else if (response.success = false)
        {
          toast(response.message, 1500);
          renderReportImages();
        }
        else
        {
          toast("Something went wrong! Please try again later.", 1500);
          console.log(response);
        }
      }
    });
  }

  function renderReportImages(reportImages = null)
  {
    if (reportImages)
    {
      var report_images_html = "";
      for (let i=0; i<reportImages.length; i++)
      {
        var image_details = reportImages[i];
        var image_html = `<div class="col-md-4">
                            <img src="${image_details.image}" alt="${image_details.label}" class="data-images"/>
                            <label class="data-images-label">${image_details.label}</label>
                          </div>`;
        report_images_html += image_html;
      }
    }
    else
    {
      var report_images_html = "<div class='col-md-12 empty-container'><h3>No Report Images Available!</h3></div>";
    }

    $("#report_images_container").html(report_images_html);
    $("#value_container").show();
    $("#loading_image").hide();
  }

  function close_show_data_modal()
  {
    $("#show_data_modal").modal("hide");
  }

  function reportGenerate(id)
  {
    $('#reportModal').modal('show');
    $('#reportModal').attr('request_id', id);
  }

  var select = document.getElementById('select_product');
  var add_product_button = document.getElementById('add_product_button');
  var product_div_1 = document.getElementById('product_1');
  var product_div_2 = document.getElementById('product_2');

  $("#select_product").on("change", function(){
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

  function productAddClick()
  {
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
        url: "<?=base_url('getProductByProductId')?>",
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

  function addProduct(response)
  {
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

  function previewGeneratedReport(report_data)
  {
    var form = document.createElement("form");
    form.method = "POST";
    form.action = "<?=base_url('preview-generated-report')?>"; 

    var input = document.createElement("input");
    input.type = "hidden";  
    input.value = report_data.id;
    input.name = "report_id";
    form.appendChild(input);  

    var input2 = document.createElement("input");
    input2.type = "hidden";  
    input2.value = report_data.url;
    input2.name = "report_url";
    form.appendChild(input2); 

    document.body.appendChild(form);
    form.submit();
  }

  $("#generate_report").on("click", function(){

    var reportId = $("#reportModal").attr("request_id");
    var expertAdvice = $("#reportText").val().trim();
    var suggestedProducts = getSuggestedProducts();
    var URL = "<?=base_url('generate-report')?>";
    var postData = {};

    if (reportId!="")
    {
      postData["report_id"] = reportId;
    }

    if (expertAdvice!="")
    {
      postData["expert_advice"] = expertAdvice;
    }

    if (Object.keys(suggestedProducts).length>0)
    {
      postData["suggested_products"] = suggestedProducts;
    }

    $.ajax({
      url: URL,
      type: "POST",
      data: postData,
      beforeSend : function(){
        $("#generate_report").attr("disabled","disabled");
        $("#generate_report").addClass("disabled_button");
      },
      complete : function(){
        $("#generate_report").removeAttr("disabled");
        $("#generate_report").removeClass("disabled_button");
      },
      error: function(a,b,c){
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function(response)
      {
        console.log(response);
        if(response.success)
        {
          var report_data = response.report_data;
          previewGeneratedReport(report_data);
        }
        else
        {
          swal(response.message);
        }
      }
    });
  });

</script>