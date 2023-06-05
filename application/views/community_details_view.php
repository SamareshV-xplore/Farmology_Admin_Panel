<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Details of Ask Community</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Community Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="" id="question-form">
              
              <div class="box-body">
                <strong>User Name:</strong> <?=$community_details['user_name']?><span style="float: right;"><strong>Location :</strong><?=$community_details['location']?></span><br><br>
                
                <div class="clearfix">
                </div>
                <b>Problem Description :</b> &nbsp;<?=$community_details['problem_description']?>
                <br><br>
                <?php 
                if(count($community_details['image']) > 0){
                foreach($community_details['image'] as $img){ ?>
                  <div class="form-group col-md-6">
                    <?php
                        if(empty($img['image'])){
                            $imgURL = ASSETS_URL.'dist/img/default-user.png';
                        }else{
                            $imgURL = $img['image'];
                        }
                    ?>
                  <label for="first_name">Crop Image</label><br>
                  <img src="<?php echo $imgURL; ?>" width="120px" class = "img-open">
                </div>
              <?php }}else{ ?>
                <div class="form-group col-md-6">
                    <?php
                        
                      $imgURL = ASSETS_URL.'dist/img/default-user.png';
                        
                    ?>
                  <label for="first_name">Crop Image</label><br>
                  <img src="<?php echo $imgURL; ?>" width="120px" class = "img-open">
                </div>

              <?php } ?>

                <div style="width: 100%; display: flex; flex-flow: row;">
                  <div style="width: 50%; padding: 5px;">
                    <label for="first_name">Add Comment</label><br>
                    <textarea class="form-control shadow-none" rows="3" placeholder="Add Comment" id="comment_text"></textarea>
                  </div>  
                  <div style="width: 50%; padding: 5px;">
                    <label for="first_name">Add image</label><br>
                    <input type="file" class="form-control" id="image" name="image" placeholder="Image" accept="image/*" multiple><br>
                    <button class="btn btn-primary" onclick="submit_comment(event)">Add</button>
                  </div>     
                </div>         
              </div>
              <!-- /.box-body -->
              
            </form>
          </div>

        </div>

      
      </div>
      <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Comments Lists</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="display: none;">Sl. No</th>
                                <th>Customer Name</th>
                                <th>Comments</th>
                                 <th>Comments Image</th>
                                <th>Comments Date</th>
                                <th class="action_area_td">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($community_details['comments_list']) > 0)
                            {
                                $slno = 1;
                                foreach($community_details['comments_list'] as $list)
                                {

                                    ?>
                                    <tr>
                                        <td  style="display: none;"><?php echo $slno; ?></td>
                                        <td><?php echo $list['name']; ?></td>
                                        <td><?php echo $list['comments']; ?></td>
                                        <td style="width: 10%">
                                        <?php if(!empty($list['image'])){ ?>  
                                        <img style="height: 100px; width: 100px; object-fit: cover;" src="<?=$list['image']?>" class="img-responsive img-open">
                                        <?php }else{ ?>
                                          N/A
                                        <?php } ?>  
                                        </td>
                                        <td><?php echo date("l jS \of F Y h:i:s A", strtotime($list['comment_time'])); ?></td>
                                        <td>
                                          <a href="<?php echo base_url('Community/comments_delete/'.$list['comments_id'].'/'.$this->uri->segment('2')); ?>" onclick="return confirm('Are you sure want to delete this community comments?')">
                                                <button type="button" class="btn bg-red btn-sm pull-right sl_margin" title="Delete"><i class="fa fa-trash"></i>
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                    $slno++;
                                }
                            }
                            ?>
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
      <div class="box-footer">
                <a href="<?php echo base_url('communities-list'); ?>"><button type="button" class="btn btn-default pull-left">Back</button></a>
              </div>

    </section>
    <!-- /.content -->
  </div>
  <?php
    include "modal_images.php";
  ?>
  
  <script type="text/javascript">
    function submit_comment (e) {
      e.preventDefault();
      var comment = document.getElementById('comment_text').value;
      var formData = new FormData();
      formData.append('id', "<?= $community_details['id'] ?>")
      formData.append('comment', comment);
      var filesLength=document.getElementById('image').files.length;
      for(var i=0;i<filesLength;i++){
        formData.append('images[]', $('input[type=file]')[0].files[i]);
      }
      // formData.append('images[]', $('input[type=file]')[0].files);
      
      $.ajax({
          url: '<?= base_url('community/add_comment') ?>',
          type: 'post',
          data: formData,
          contentType: false, 
          processData: false,
          success: function (data) {
            data = JSON.parse(data);
            if (data.isSubmitted == true) {
              swal('Successful', 'Comment added successfully', 'success');
              location.reload();
            }
          }
        });
    }
  </script>


