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
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Ask Community List</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        
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
                                <th>Customer Name</th>
                                <th>Question</th>
                                <th>Problem Description</th>
                                <th>Answer's count</th>
                                <th>Status</th>
                                <th class="action_area_td">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($community_list) > 0)
                            {
                                $slno = 1;
                                foreach($community_list as $list)
                                {

                                    ?>
                                    <tr>
                                        <td  style="display: none;"><?php echo $slno; ?></td>
                                        <td><?php echo (isset($list['user_details']['full_name']) ? $list['user_details']['full_name'] : ''); ?></td>
                                        <td><?php echo $list['quesstion']; ?></td>
                                        <td><?php echo $list['problem_description']; ?></td>
                                        <td><?php echo $list['comments_count']; ?></td>
                                        <td>
                                            <?php
                                            if($list['status'] == 'A')
                                            {
                                                echo "<label style='color:green'><b>Active<b></label>";
                                            }
                                            else
                                            {
                                                echo "<p style='color:red'>Solved</p>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('communities-details/'.$list['id']); ?>"><button type="button" class="btn bg-yellow btn-sm" title="Details"><i class="fa fa-eye"></i>
                                                </button></a>
                                            <a href="<?php echo base_url('Community/delete/'.$list['id']); ?>" onclick="return confirm('Are you sure want to delete this community?')">
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
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script type="text/javascript">
    function form_submit()
    {
        $("#filter_form").submit();
    }
</script>
