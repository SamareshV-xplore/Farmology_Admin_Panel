<style type="text/css">
  .required_cls
  {
    color: red;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Upload New Video</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Video Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" role="form" action="<?php echo base_url('video/add_submit') ?>" id="video-form" enctype="multipart/form-data">
              <input type="hidden" name="video_form" value="1">
              <div class="box-body">            

                <div class="form-group col-md-6">
                  <label for="title">Title<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="title" name="title" placeholder="Title" >
                </div>

								<div class="form-group col-md-12">
                  <label for="last_name" >Description<span class="required_cls">*</span></label>
                  <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group col-md-6">
                  <label for="yt_video_id">Youtube Video ID<span class="required_cls">*</span></label>
                  <input type="text" class="form-control" id="yt_video_id" name="yt_video_id" placeholder="eg: 7n47zBdQ6qg" >
								</div>  
								
								

                <div class="form-group col-md-6">
                  <label for="blood_group">Status<span class="required_cls">*</span></label>
                  <select class="form-control" name="status" id="status">
                    <option value="Y">Active</option>
                    <option value="N">Inactive</option>
                  </select>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn btn-primary pull-right" onclick="return upload_video_submit();">Upload Video</button>
                <a href="<?php echo base_url('video'); ?>"><button type="button" class="btn btn-primary pull-left">Cancel</button></a>
              </div>
            </form>
          </div>

        </div>

      
      </div>
      

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">
    // date check start
    function date_check(date_is)
    {
      return moment(date_is, 'DD/MM/YYYY',true).isValid();
    }
    // date check end

    function upload_video_submit()
    {
      $('.form-control').removeClass('error_cls');
			$("#cke_description").removeClass('error_cls');
      var focusStatus = "N";
      
      var title = document.getElementById("title").value.trim();
			var description = $("#cke_description iframe").contents().find("body").text();
      var yt_video_id = document.getElementById("yt_video_id").value.trim();
      var status = document.getElementById("status").value;

      if(title == '')
      {
        $('#title').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#title').focus();
            focusStatus = 'Y';
        }     
      }

			if(description == '')
      {
        
				$("#cke_description").addClass('error_cls');
        if(focusStatus == 'N')
        {
            
						
            focusStatus = 'Y';
        }     
      }

      if(yt_video_id == '')
      {
        $('#yt_video_id').addClass('error_cls');
        if(focusStatus == 'N')
        {
            $('#yt_video_id').focus();
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
        $("#video-form").submit();
      }

      return false;
    }
  </script>


