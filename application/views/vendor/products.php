<table id="products_list_table" class="table table-bordered table-striped">
 <thead>
        <tr>
            <th style="display: none;">Sl. No</th>
            <th style="width: 20%">Name</th>
            <th style="width: 10%">SKU</th>
            <th style="width: 10%">Image</th>
            <th style="width: 10%">Variation</th>
            <th style="width: 10%">Category</th>
            <th style="width: 10%">Crop</th>
            <th style="width: 10%">Date</th>
            <th style="width: 10%">Status</th>
            <th style="width: 10%">Order</th>
            <th style="width: 10%">Action</th>
          
        </tr>
  </thead>
  <tbody> 
  <?php
 
    if(count($product_list) > 0)
    {
      $rc = 0;
      foreach($product_list as $product_row)
      {
        
        ?>
        <tr>
          <td style="display: none;"><?=$rc?></td>
          <td style="width: 20%">
            <?=$product_row['name']?>
          </td>
          <td style="width: 10%">
            <?=$product_row['SKU']?>
          </td>
          <td style="width: 10%">
            <img style="height: 100px; width: 100px; object-fit: cover;" src="<?=$product_row['image_list'][0]['image']?>" class="img-responsive">
          </td>

          <td style="width: 10%">

            <ul class="list-group list-group-unbordered">                           
            <?php
            foreach($product_row['variation_list'] as $variation)
            {
              ?>
              <li class="list-group-item">
              <b><?=$variation['title']?></b>
              <a class="pull-right"><i class="fa fa-rupee"></i><?=$variation['sale_price']?></a></li>
              <?php
            }                           
            ?>

            </ul>
          </td>

          <td style="width: 10%">
            <ul class="list-group list-group-unbordered">
              <?php
                foreach($product_row['category_details'] as $cat){
                  ?>
                  <li class="list-group-item">
                  <b><?=$cat['title']?></b>
              </li>
                <?php } ?>
            </ul>
          </td>
          <td style="width: 10%">
            <ul class="list-group list-group-unbordered custom_scroll">
              <?php
                foreach($product_row['crop_details'] as $crop){
                  ?>
                  <li class="list-group-item">
                  <b><?=$crop['title']?></b>
              </li>
                <?php } ?>
            </ul>
          </td>
          <td style="width: 10%">
            <?php
            echo "<b>Published</b><br>".date("d/m/y H:i", strtotime($product_row['created_date']));
            if($product_row['updated_date'] != NULL)
            {
              echo "<br><b>Last Updated</b><br>".date("d/m/y H:i", strtotime($product_row['updated_date']));
            }
            else
            {
              echo "<br><b>Last Updated</b> Never";
            }
            ?>                            
            </td>
                                  
          <td style="width: 10%">
            <?php
            if($product_row['status'] == 'Y')
            {
              ?>
              <center><span style="color:green"><b>Active</b></span></center>
              <?php
            }
            else
            {
              ?>
              <center><span style="color:red"><b>Inactive</b></span></center>
              <?php
            }
            ?>
          </td>

          <th style="width: 10%"><input type="number" id="ord_<?=$product_row['id']?>" class="form-control" style="width: 80px;" onblur="return order_change(<?=$product_row['id']?>);" value="<?=$product_row['ord_by']?>"></th>

          <td style="width: 10%">
            <a href="<?php echo base_url('vendors/delete_product/'.$product_row['id']); ?>" onclick="return confirm('Are you sure want to delete this product?')">
              <button type="button" class="btn bg-red btn-sm pull-right sl_margin" title="Delete"><i class="fa fa-trash"></i>
              </button>
            </a>

            <a href="<?php echo base_url('vendors/edit_product/'.$product_row['id']); ?>">
              <button type="button" class="btn bg-yellow btn-sm pull-right sl_margin" title="Edit Details"><i class="fa fa-edit"></i>
              </button>
            </a>
          </td>
            
          </tr>
        <?php
        $rc++;
      }
    }
  ?>
  </tbody>
  
</table>
<a class="fab" href="<?= base_url('vendors/add_new_product') ?>">+</a>

<script type="text/javascript">
  $(document).ready(function(){
      $('#products_list_table').DataTable( {
        paging: false,
        order: [[ 7, 'desc' ]]
      });
  });
</script>