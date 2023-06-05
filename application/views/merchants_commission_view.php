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
    .copy-button{
        float: right;
        color: #1d56d7;
        text-decoration: underline;
        cursor: pointer;
    }

    .copy-button:hover{
        color: #d3ab19;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Merchants Commissions</h1>
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
                                <th>Product Name</th>
                                <th>Variation Name</th>
                                <th>State</th>
                                <th>Commission</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($commissions) > 0)
                            {
                                $slno = 1;
                                foreach($commissions as $list)
                                {

                                    ?>
                                    <tr>
                                        <td  style="display: none;"><?php echo $slno; ?></td>
                                        <td><?php echo $list->product_name; ?></td>
                                        <td><?php echo $list->variation_name; ?></td>
                                        <td><?php echo $list->state; ?></td>
                                        <td><strong>₹ </strong><input type="number" id="<?= $list->id ?>" onchange= "onValueChange('<?= $list->id ?>')" value="<?php echo $list->commission; ?>" class="form-control shadow-none">
                                            <button class="btn btn-success" style="display: none; float: right;" onclick="submitValue('<?= $list->id ?>')" id="btn-<?= $list->id ?>">Change</button>
                                            <div class="text-center" style="display:none" id="status_loader_<?=$list->id?>">
                                              <img src="<?=base_url('assets/dist/img/loader-mini.gif')?>" style="width: 100px;">
                                            </div>
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
    function onValueChange (id) {
        document.getElementById('btn-'+id).style.display = 'block';
    }

    function submitValue (id) {
        $('#status_loader_'+id).show();
        var newCommission = document.getElementById(id).value;
        $.ajax({
            url: 'updateCommission',
            type: 'post',
            data: {id: id, commission: newCommission},
            success: function (data) {
                data = JSON.parse(data);
                if (data.isUpdated == true) {
                    location.reload();
                }
            }
        });
    }
</script>
