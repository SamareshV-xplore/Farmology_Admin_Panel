<script src="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css"></script>
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<style type="text/css">
  
  .required_cls
  {
    color: red;
  }

  .reset_btn{
    margin-top: 24px;
  }

  .high_label
  {
    font-size: 12px;
  }

  .action_area_td
  {
    width: 13%;
  }

  .sl_margin
  {
    margin-right: 5px;
  }

  .custom_scroll
  {
    height: 200px;
    overflow-x: auto;
  }

  .latest-check{
    height: 32px;    
    margin-left: 40%;
    cursor: pointer;

  }

  .fab {
    width: fit-content;
    height: 60px;
    background-color: #ffffff;
    border-radius: 50px;
    box-shadow: 0 2px 1px 0 #e3e3e3;
    transition: all 0.1s ease-in-out;
    font-size: 17px;
    color: #3c8dbc;
    font-weight: 700;
    text-align: center;
    line-height: 20px;
    /*position: fixed;*/
    padding: 20px;
    /*right: 50px;
    top: 120px;*/
    cursor: pointer;
    border: 1px solid #e1e1e1;
  }
  
  .fab:hover {
    box-shadow: 0 6px 14px 0 #666;
    transform: scale(1.05);
  }

  .check_latest {
    width: 25px;
    height: 25px;
    float: right;
    margin-right: 10px;
  }

  #save_changes {
    min-width: 120px;
    margin-top: 24px;
  }

  .product-variation-table {
    width: 100%;
    text-align: center;
  }

  .product-variation-table thead {
    text-align: center;
    background: #CCC;
  }

  .product-variation-table th {
    padding: 2px;
  }

  .product-variation-table td {
    padding: 4px 2px 4px 2px;
  }
  
</style>

<?php if(isset($_REQUEST['filter'])) {
    $filter_status = 1;
} else {
    $filter_status = 0;
} ?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Product List</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <form method="get" action="" id="filter_form">
                <input type="hidden" name="filter" value="true">

                <!-- <div class="form-group col-md-3">
                  <label for="official_email">Filter by Category </label>
                <select name="cate1" id="cate1" class="form-control" onchange="return get_child_category(1);">
                    <option value="0">All Category</option>
                    <?php
                    if(count($main_parent) > 0)
                    {
                      foreach($main_parent as $parent_row)
                      {
                        ?>
                        <option value="<?php echo $parent_row['id']; ?>" <?php if($filter_data['cate1'] == $parent_row['id']) { ?> selected="selected" <?php } ?> ><?php echo $parent_row['title']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>

                </div>

                <div class="form-group col-md-3" id="sub_child_main_div_2" <?php if($filter_data['cate1'] == 0) { ?> style="display:none;" <?php } ?> >
                  <label for="cate_2">Child Category</label>
                  <div id="sub_child_sub_div_2">
                  <select name="cate2" id="cate2" class="form-control">
                    <option value="0">Select</option>
                    
                  </select>
                </div>
                </div> -->
              
                <div class="form-group col-md-2">
                  <label for="official_email">Filter by Status </label>
                  <select name="status" id="status" class="form-control">
                    <option value="all" <?php if($filter_data['status'] == 'all') { ?> selected <?php } ?>>All</option>
                    <option value="Y" <?php if($filter_data['status'] == 'Y') { ?> selected <?php } ?>>Active</option>
                    <option value="N" <?php if($filter_data['status'] == 'N') { ?> selected <?php } ?>>Inactive</option>
                  </select>
                </div>

                <div class="form-group col-md-2">
                  <button type="button" class="btn btn-block btn-primary reset_btn" onclick="return form_submit();">Filter</button>
                </div>

                <div class="form-group col-md-2" <?php if($filter_status == 0) { ?> style="display: none;" <?php } ?> >
                  <a href="<?php echo base_url('product'); ?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
                </div>

                <div id="save_changes_container" class="col-md-6 latest-product-container">
                  <button type='button' id='save_changes' class="btn btn-success my-auto" onclick='onSaveChanges()' disabled>Save Changes</button>
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
              <table id="products_list_table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th style="display:none;">Sl. No</th>
                    <th style="width:20%">Name</th>
                    <th style="width:10%">SKU</th>
                    <th style="width:10%">Image</th>
                    <th style="width:60%">Variation</th>
                    <th style="width:10%">Category</th>
                    <th style="width:10%">Crop</th>
                    <th style="width:10%">Date</th>
                    <th style="width:10%">Status</th>
                    <th style="width:5%">Order</th>
                    <th style="width:10%">Action</th>
                  </tr>
                </thead>
                <tbody> 
                <?php if (count($product_list) > 0) {
                foreach ($product_list as $i => $product_row) { ?>
                  <tr>
                    <td style="display:none;"><?=$i+1?></td>
                    <td style="width:20%;"><?=$product_row['name']?></td>

                    <td style="width:10%;">
                      <?=$product_row['SKU']?>
                    </td>

                    <td style="width:10%;">
                      <img style="height:100px; width:100px; object-fit:cover;" src="<?=$product_row['image_list'][0]['image']?>" class="img-responsive">
                    </td>

                    <td style="width:60%">
                      <!-- <ul class="list-group list-group-unbordered">                           
                      <?php foreach ($product_row['variation_list'] as $variation) { ?>
                          <li class="list-group-item">
                            <b><?=$variation['title']?></b>
                            <a class="pull-right"><i class="fa fa-rupee"></i><?=$variation['sale_price']?></a>
                          </li>
                      <?php } ?>
                      </ul> -->
                      <?php if (!empty($product_row["variation_list"])) { ?>
                        <table class="product-variation-table">
                          <thead>
                            <tr>
                              <th>Title</th>
                              <th>Price</th>
                              <th>State</th>
                              <th>Available</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($product_row["variation_list"] as $variation_details) { ?>
                              <tr>
                                <td><?=$variation_details['title']?></td>
                                <td><?="₹ ".$variation_details['sale_price']?></td>
                                <td><?=$variation_details['state']?></td>
                                <td>
                                  <?php $availability_status = $variation_details['is_available']; ?>
                                  <select onchange="change_product_variation_availability('<?=$product_row['id']?>', '<?=$variation_details['id']?>', '<?=$variation_details['state_id']?>', this.value)">
                                    <option value="YES" <?=($availability_status=="YES") ? "selected" : ""?>>Yes</option>
                                    <option value="NO" <?=($availability_status=="NO") ? "selected" : ""?>>No</option>
                                  </select>
                                </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      <?php } else { ?>
                        <tr><td colspan="4">No variations found.</td></tr>
                      <?php } ?>
                    </td>

                    <td style="width: 10%">
                      <ul class="list-group list-group-unbordered">
                      <?php foreach ($product_row['category_details'] as $cat) { ?>
                          <li class="list-group-item">
                            <b><?=$cat['title']?></b>
                          </li>
                      <?php } ?>
                      </ul>
                    </td>

                    <td style="width: 10%">
                      <ul class="list-group list-group-unbordered custom_scroll">
                      <?php foreach ($product_row['crop_details'] as $crop) { ?>
                          <li class="list-group-item">
                            <b><?=$crop['title']?></b>
                          </li>
                      <?php } ?>
                      </ul>
                    </td>

                    <td style="width: 10%">
                      <?php echo "<b>Published</b><br>".date("d/m/y H:i", strtotime($product_row['created_date']));
                      if ($product_row['updated_date'] != NULL) {
                          echo "<br><b>Last Updated</b><br>".date("d/m/y H:i", strtotime($product_row['updated_date']));
                      } else {
                          echo "<br><b>Last Updated</b> Never";
                      } ?>                            
                    </td>
                                                
                    <td style="width: 10%">
                      <?php if ($product_row['status'] == 'Y') { ?>
                          <center><span style="color:green"><b>Active</b></span></center>
                      <?php } else { ?>
                          <center><span style="color:red"><b>Inactive</b></span></center>
                      <?php } ?>
                    </td>

                    <th style="width: 5%">
                      <input type="number" id="ord_<?=$product_row['id']?>" class="form-control" style="width: 80px;" onblur="return order_change(<?=$product_row['id']?>);" value="<?=$product_row['ord_by']?>">
                    </th>

                    <td style="width: 10%">
                      <a href="<?php echo base_url('product/delete/'.$product_row['id']); ?>" onclick="return confirm('Are you sure want to delete this product?')">
                        <button type="button" class="btn bg-red btn-sm pull-right sl_margin" title="Delete">
                          <i class="fa fa-trash"></i>
                        </button>
                      </a>

                      <a href="<?php echo base_url('product/edit/'.$product_row['id']); ?>">
                        <button type="button" class="btn bg-yellow btn-sm pull-right sl_margin" title="Edit Details">
                          <i class="fa fa-edit"></i>
                        </button>
                      </a>

                      <?php if ($product_row["status"]=="Y") { ?>
                        <?php if ($product_row['is_latest']=='Y') { ?>
                            <input type="checkbox" id="<?=$product_row['id']?>" class="check_latest"  checked disabled/> 
                        <?php } else { ?>
                            <input type="checkbox" id="<?=$product_row['id']?>" class="check_latest" disabled/> 
                        <?php } ?>
                      <?php } ?>
                    </td>
                          
                  </tr>
                <?php }} ?>
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
    <!-- /.content -->
  </div>
<script>

  function onSaveChanges()
  {
    setLatestProducts();
  }

  function getLatestSelectedProducts()
  {
    var latest_products_data = {};
    var counter = 0;
    var checkboxes = document.getElementsByClassName("check_latest");
    for(let i=0; i<checkboxes.length; i++)
    {
      var checkbox_id = checkboxes[i].id;
      var checked_status = $("#"+checkbox_id).prop("checked");
      if(checked_status){
        latest_products_data[counter] = checkbox_id;
        counter++;
      }
    }
    return latest_products_data;
  }

  function setLatestProducts()
  {
    var data = getLatestSelectedProducts();
    var postData = {data:data};
    $.ajax({
      url: "<?=base_url('product/set_latest_products')?>",
      type: "POST",
      data: postData,
      beforeSend: function()
      {
        $("#save_changes").text("Saving...");
        $("#save_changes").attr("disabled","disabled");
      },
      complete: function()
      {
        $("#save_changes").text("Save Changes");
      },
      error: function(a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success: function(res)
      {
        console.log(res);
        var response = JSON.parse(res);
        if(response.success)
        {
          swal(response.message);
        }
        else
        {
          swal(response.message);
        }
      }
    });
  }

  function enableCheckboxes()
  {
    var checkboxes = document.getElementsByClassName("check_latest");
    for(let i=0; i<checkboxes.length; i++)
    {
      var checkbox = document.getElementById(checkboxes[i].id);
      checkbox.removeAttribute("disabled");
    }
  }

  $(document).ready(function(){

    $('#products_list_table').DataTable( {
      paging: false,
      order: [[ 7, 'desc' ]]
    });

    $.ajax({
      url: "<?=base_url('product/get_latest_products')?>",
      type: "POST",
      success: function(data)
      {
          var response = JSON.parse(data);
          if(response.success)
          {
            console.log(response.latest_products);
            enableCheckboxes();
          }
          else
          {
            swal(response.message);
          }
      }
    });

    $(".check_latest").on("change", function(e){
      var selected_checkbox_id = $(this).attr("id");
      var checkboxes = document.getElementsByClassName("check_latest");
      var checked_count = 0;
      for(let i=0; i<checkboxes.length; i++)
      {
        console.log(checkboxes[i].id);
        var checkbox_id = checkboxes[i].id;
        var checked_status = $("#"+checkbox_id).prop("checked");
        console.log(checked_status);
        if(checked_status){
          checked_count++;
        }
      }
      console.log("Checked Count: "+checked_count);
      if(checked_count>2)
      {
        $(this).prop("checked", false);
        var message = "You Can Choose Only Two Latest Products!";
        swal(message);
      }
      else
      {
        if(checked_count==2)
        {
          $("#save_changes").removeAttr("disabled");
        }
        else
        {
          $("#save_changes").attr("disabled","disabled");
        }
      }
    });

  });
</script>
<script type="text/javascript">

    function order_change(id)
    {
      var order_value = document.getElementById("ord_"+id).value;
      if(order_value == '' || order_value < 0)
      {
        document.getElementById("ord_"+id).value = '0';
        order_value = 0;
      }
      var dataString = 'id=' + id + '&order_value=' + order_value;

        $.ajax({
        type: "POST",
        url: "<?=base_url('product/update_product_order')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          //alert('success');
          
        }
        });

    }
  </script>

  <?php
 if($filter_data['cate1'] > 0)
 {
  ?>

  <script type="text/javascript">
    $(document).ready(function() 
    {
      get_child_category(1);

    });
  </script>
  <?php
 } 
?>
  <!-- /.content-wrapper -->
<script type="text/javascript">

  function select_cate_2() {
    $("#cate2").val(<?=$filter_data['cate2']?>);
    }
    function form_submit()
    {
      $("#filter_form").submit();
    }

    // parent change function
    function get_child_category(parent_level)
    {      
      var main_parent = document.getElementById("cate"+parent_level).value;
      
      if(main_parent > 0)
      {
        
        var dataString = 'parent_id=' + main_parent;

        $.ajax({
        type: "POST",
        url: "<?=base_url('product/ajax_get_category_list_by_parent_id')?>",
        data: dataString,
        cache: false,
        success: function(html) {          
          var obj = $.parseJSON(html);
          if(obj.status == 'Y')
          {
          if(parent_level == 1)    
          {
            $( "#sub_child_sub_div_2" ).empty();
            
            var row_html = '<select name="cate2" id="cate2" class="form-control">'+obj.html+'</select>';
            $( "#sub_child_sub_div_2" ).append( row_html );
            $( "#sub_child_main_div_2").show();

            <?php
            if($filter_data['cate2'] > 0)
            {
              ?>
              if(main_parent == <?=$filter_data['cate1']?>)
              {
                select_cate_2();
              }
              
              <?php
            }

            ?>
            
            

          }
          else
          {
            // do nothing
          }
           
            
          }
          
        }
        });
        
      }
      else
      {  
      
        if(parent_level == 1)         
        {
          
          $( "#sub_child_sub_div_2" ).empty();
          $( "#sub_child_sub_div_2" ).append( '<select name="cate2" id="cate2" class="form-control"><option value="0">Select Child</option></select>' );
          $( "#sub_child_main_div_2" ).hide();


        }
        else
        {
          // do nothing
        }

        
            
      }

      return false;
    }

    function checkboxToggle(id)
    {
      toggleLatestStatus(id);
    }

    var ret = null;

    function toggleCheckbox(id)
    {
      var checkbox = $("#".id);
      var checked = "<?=FRONT_URL?>uploads/chekboxes/checked.png";
      var unchecked = "<?= FRONT_URL?>uploads/chekboxes/uncheked.png";

      if(checkbox.attr("src")==checked)
      {
        checkbox.attr("src", unchecked);
      }
      else
      {
        checkbox.attr("src", checked);
      }
    }

    function toggleLatestStatus(id)
    {
      $.ajax({
          url: '<?=base_url('product/toggle_latest_status')?>',
          type: 'post',
          data: {product_id: id},
          success: function(res)
          {
              var response = JSON.parse(res);
              if(response.success)
              {
                toggleCheckbox(id);
              }
              else
              {
                swal(response.message);
              }
          }
        });
    }

    function getTotalLatestProductCount () {
      $.ajax({
          url: '<?=base_url('product/getLatestProductCount')?>',
          type: 'post',
          async : false,
          success: function (data) {
            ret = data;
          }
        });
    }

    $(document).ready(function(){
      
    });

    function writeLatestProductCount () {
      getTotalLatestProductCount();
      document.getElementsByClassName('fab')[0].innerHTML = ret + " Latest Products Selected";
      document.getElementsByClassName('fab')[0].style.color = (ret == 2) ? '#0c8b04' : 'red';
    }

    function change_product_variation_availability(product_id, variation_id, state_id, availability_status) {
      // console.log("Product ID: "+product_id);
      // console.log("Variation ID: "+variation_id);
      // console.log("State ID: "+state_id);
      // console.log("Availability Status: "+availability_status);

      var postData = {product_id: product_id, variation_id: variation_id, state_id: state_id, availability_status: availability_status};
      $.ajax({
        url: "<?=base_url('change-product-variation-availability')?>",
        type: "POST",
        data: postData,
        error: function(a, b, c)
        {
          toast("Something went wrong! Please try again later.", 3000);
          console.log(a);
          console.log(b);
          console.log(c);
        },
        success: function(response)
        {
          if (response.success == true) {
            toast(response.message, 1500);
            console.log(response.data);
          }
          else if (response.success == false) {
            toast(response.message, 3000);
          }
          else {
            toast("Something went wrong! Please try again later.", 3000);
            console.log(response);
          }
        }
      });
    }
  </script>