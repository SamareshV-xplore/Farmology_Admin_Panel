<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Details of Sell Produce Request By Customer</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Sell Produce Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="" id="question-form">
              <input type="hidden" id="sellproduce_id" name="sellproduce_id" value="<?=$sellproduce_details['id']?>">
              <div class="box-body">
               
                <div class="form-group col-md-6">
                  <label for="question">Customer Name</label>
                  <input type="text" readonly="readonly" class="form-control"  value="<?php echo $sellproduce_details['customer_name']; ?>">
                </div>
                  <div class="form-group col-md-6">
                      <label for="answer_text">Crop name</label>
                      <input type="text" readonly="readonly" class="form-control"  value="<?php echo $sellproduce_details['crop_name']; ?>">
                  </div>

                  <div class="form-group col-md-6">
                      <label for="answer_text">Variety</label>
                      <input type="text" readonly="readonly" class="form-control"  value="<?php echo $sellproduce_details['variety']; ?>">
                  </div>
                  <div class="form-group col-md-6">
                      <label for="answer_text">Qty</label>
                      <input type="text" readonly="readonly" class="form-control"  value="<?php echo $sellproduce_details['qty'].' '.$sellproduce_details['qty_unit']; ?>">
                  </div>
                  <div class="form-group col-md-6">
                      <label for="answer_text">Available Date</label>
                      <input type="text" readonly="readonly" class="form-control"  value="<?php echo $sellproduce_details['available_date']; ?>">
                  </div>
                  <div class="form-group col-md-6">
                      <label for="answer_text">Available In Days</label>
                      <input type="text" readonly="readonly" class="form-control"  value="<?php echo $sellproduce_details['available_in_days']; ?>">
                  </div>

                  <div class="form-group col-md-6">
                      <label for="answer_text">Note</label>
                      <?php
                        $note = '';
                        if(!empty($sellproduce_details['note'])){
                          $note = $sellproduce_details['note'];
                        }

                       ?>
                      <input type="text" readonly="readonly" class="form-control"  value="<?php echo $note; ?>">
                  </div>
                  <div class="form-group col-md-6">
                      <label for="answer_text">Price</label>
                      <input type="text" readonly="readonly" class="form-control"  value="<?php echo $sellproduce_details['price']; ?>">
                  </div>
                  <div class="form-group col-md-6">
                  <label for="blood_group">Status</label>
                  <?php
                  $status = '';
                  if($sellproduce_details['status'] == 'A'){
                    $status = 'Active';
                  }else{
                    $status = 'Solved';
                  }

                   ?>
                  <input type="text" readonly="readonly" class="form-control"  value="<?php echo $status; ?>">
                </div>
                <div class="clearfix">
                </div>
                <?php 
                if(count($sellproduce_details['images']) > 0){
                foreach($sellproduce_details['images'] as $img){ ?>
                  <div class="form-group col-md-6">
                    <?php
                        if(empty($img['image'])){
                            $imgURL = ASSETS_URL.'dist/img/default-user.png';
                        }else{
                            $imgURL = $img['image'];
                        }
                    ?>
                  <label for="first_name">Crop Image</label><br>
                  <img width="200px" src="<?=$imgURL?>" class="img-open">
                </div>
              <?php }}else{ ?>
                  <div class="form-group col-md-6">
                    <?php
                        
                      $imgURL = ASSETS_URL.'dist/img/default-user.png';
                        
                    ?>
                  <label for="first_name">Crop Image</label><br>
                  <img width="200px" src="<?=$imgURL?>" class="img-open">
                </div>
              <?php } ?>
                
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <a href="<?php echo base_url('sellproduces-list'); ?>"><button type="button" class="btn btn-default pull-left">Back</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <!-- including image popup modal library -->
  <?php include APPPATH."views/modal_images.php"; ?>

  <script type="text/javascript">
    
  </script>


