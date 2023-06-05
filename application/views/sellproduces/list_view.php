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
        <h1>Sell Produces Lists</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!--<div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <form method="get" action="" id="filter_form">
                            <input type="hidden" name="filter" value="true">

                            <div class="form-group col-md-5">
                                <label for="official_email">Filter by Status </label>
                                <select name="status" id="status" class="form-control" onchange="return form_submit();">
                                    <option value="all" <?php if($filter_data['status'] == 'all') { ?> selected <?php } ?>>All</option>
                                    <option value="A" <?php if($filter_data['status'] == 'A') { ?> selected <?php } ?>>Approved</option>
                                    <option value="P" <?php if($filter_data['status'] == 'P') { ?> selected <?php } ?>>Pending</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2" <?php if($filter_data['status'] !="A" && $filter_data['status'] !="P" ) { ?> style="display: none;" <?php } ?> >
                                <a href="<?php echo base_url('questions-list'); ?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
                            </div>

                        </form>


                    </div>
                </div>
            </div>
        </div>-->
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
                                <th>Crop Name</th>
                                <th>Variety</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Available Date</th>
                                <th>Created Date</th>
                                <th>Status</th>
                                <th class="action_area_td">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($sellproduces_list) > 0)
                            {
                                $slno = 1;
                                foreach($sellproduces_list as $list)
                                {

                                    ?>
                                    <tr>
                                        <td  style="display: none;"><?php echo $slno; ?></td>
                                        <td><?php echo $list['customer_name']; ?></td>
                                        <td><?php echo $list['crop_name']; ?></td>
                                        <td><?php echo $list['variety']; ?></td>
                                        <td><?php echo $list['qty']; ?></td>
                                        <td><?php echo $list['price']; ?></td>
                                        <td><?php echo $list['available_date']; ?>
                                        <td><?php echo $list['created_date']; ?></td>
                                        <td>
                                            <?php
                                            if($list['status'] == 'A')
                                            {
                                                echo "<label style='color:green'><b>Active<b></label>";
                                            }
                                            else
                                            {
                                                //echo "<p style='color:red'>Inactive</p>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?=base_url('sellproduces-details/'.$list['id'])?>"><button type="button" class="btn bg-yellow btn-sm" title="View Details"><i class="fa fa-eye"></i>
                                                </button></a>
                                            &nbsp; &nbsp;
                                            <a href="<?=base_url('delete_sell_produce/'.$list['id']); ?>"><button type="button" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button></a>
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
