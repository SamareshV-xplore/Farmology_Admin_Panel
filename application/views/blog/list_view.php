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
  .home_page_show {
    width: 60px;
  }
</style>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Blog List</h1>
    </section>
    <!-- Main content -->
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
                    <option value="Y" <?php if($filter_data['status'] == 'Y') { ?> selected <?php } ?>>Active</option>
                    <option value="N" <?php if($filter_data['status'] == 'N') { ?> selected <?php } ?>>Inactive</option>
                  </select>
                </div>

                <div class="form-group col-md-2" <?php if($filter_data['status'] !="Y" && $filter_data['status'] !="N" ) { ?> style="display: none;" <?php } ?> >
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
              <table id="example2" class="table table-bordered table-striped">
               <thead>
                      <tr>
                      <th style="display: none;">Sl. No</th>
                          <th style="width: 25%">Title</th>
                          <th style="width: 10%">Content</th>
                          <th style="width: 15%">Image</th>
                          <th style="width: 10%">Created Date</th>
                          <th style="width: 10%">Updated Date</th>
                          <th style="width: 20%">Homepage Show Status</th>
                          <th style="width: 10%">Status</th>
                          <th style="width: 10%">Action</th>
                        
                      </tr>
                </thead>
                <tbody> 
                <?php
               
                  if(count($blog_list) > 0)
                  {

                    $rc = 0;
                    foreach($blog_list as $blog_row)
                    {
                      
                      ?>
                      <tr>
                        <td style="display: none;"><?=$rc?></td>
                        <td style="width: 25%">
                          
                          <?=$blog_row['title']?>
                            
                          </td>
                        <td style="width: 10%"><?=$blog_row['blog_content']?></td>
                        
                        <td style="width: 15%"><img style="height: 100px; width: 100px; object-fit: cover;" src="<?=$blog_row['image']?>" class="img-responsive"></td>
                        <td style="width: 10%">
                          <?php
                          echo date("d/m/y H:i", strtotime($blog_row['created_date']));
                          ?>                            
                          </td>
                        <td style="width: 10%">
                          <?php
                          if($blog_row['updated_date'] != NULL)
                          {
                            echo date("d/m/y H:i", strtotime($blog_row['updated_date']));
                          }
                          else
                          {
                            echo "Never";
                          }
                          ?>                            
                          </td>
                        <td style="width: 20%;">
                          <?php if ($blog_row["home_page_show"] == "Y")
                          {
                            $html = "<select class='home_page_show' id='".$blog_row["id"]."' onchange='changeHomepageBlog(this)'>
                                      <option value='N'>No</option>
                                      <option value='Y' selected='selected'>Yes</option>
                                    </select>";

                            echo $html;
                          }
                          else
                          {
                            $html = "<select class='home_page_show' id='".$blog_row["id"]."' onchange='changeHomepageBlog(this)'>
                                      <option value='N' selected='selected'>No</option>
                                      <option value='Y'>Yes</option>
                                    </select>";

                            echo $html;
                          }?>
                        </td>                          
                        <td style="width: 10%">
                          <?php
                          if($blog_row['status'] == 'Y')
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
                        <td style="width: 10%">
                          <?php
                          
                            $delete_title = "Delete Blog";
                            $disable_str = '';
                                           
                          ?>
                          
                            <button <?=$disable_str?> onclick="return delete_category(<?=$blog_row['id']?>);"  type="button" class="btn bg-red btn-sm pull-right sl_margin" title="<?=$delete_title?>"><i class="fa fa-trash"></i>
                            </button>
                          

                          <a href="<?php echo base_url('blog/edit/'.$blog_row['id']); ?>">
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
  <!-- /.content-wrapper -->

<form id="change_homepage_blog_form" method="POST" action="<?=base_url('blog/change_homepage_blog')?>" style="display:none;">
  <input type="hidden" name="id" id="blog_id"/>
  <input type="hidden" name="status" id="homepage_show_status"/>
</form>


<script type="text/javascript">
    function form_submit()
    {
      $("#filter_form").submit();
    }

    function delete_category(id)
    {
      var page_confirm = confirm('Are you sure want to delete this blog?');
      if(page_confirm)
      {
        window.location.replace("<?php echo base_url('blog/delete/'); ?>" + id);
      }
    }

    function changeHomepageBlog (select_element)
    {
      var id = select_element.id;
      var status = select_element.value;

      var form = document.getElementById("change_homepage_blog_form");
      var id_input = document.getElementById("blog_id");
      var status_input = document.getElementById("homepage_show_status");

      id_input.value = id;
      status_input.value = status;

      form.submit();
    }

  </script>