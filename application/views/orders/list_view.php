<style type="text/css">
    .required_cls {
        color: red;
    }

    .reset_btn {
        margin-top: 24px;
    }

    .high_label {
        font-size: 12px;
    }
    .action_area_td {
        width: 13%;
    }

    .pl-0 {
        padding-left: 0;
    }

    .pr-0 {
        padding-right: 0;
    }

    .px-0 {
        padding-left: 0;
        padding-right: 0;
    }

    .pt-5 {
        padding-top: 5px;
    }
</style>

<?php if (isset($_REQUEST['filter'])) {
    $filter_status = 1;
} else {
    $filter_status = 0;
}

if (isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])) {
    $start_date = 1;
    $start_date_value = $_REQUEST['start_date'];
} else {
    $start_date = 0;
    $start_date_value = '';
}

if (isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])) {
    $end_date = 1;
    $end_date_value = $_REQUEST['end_date'];
} else {
    $end_date = 0;
    $end_date_value = '';
}

/*echo 'filter'.$filter_status.'<br>';
echo 'start'.$start_date.'<br>';
echo 'end'.$end_date.'<br>';
exit;*/ ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Orders List</h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <form method="post" action="<?php echo base_url('order_management/export_order') ?>" id="export_order_data">
                        <input type="hidden" name="export_data" value="n" id="export_data">
                        <input type="hidden" name="order_start_date" id="order_start_date">
                        <input type="hidden" name="order_end_date" id="order_end_date">
                    </form>
                    <form method="get" action="" id="filter_form">
                        <input type="hidden" name="filter" value="true">

                        <div class="form-group col-md-2 pl-0">
                            <label for="official_email">Filter by Status </label>
                            <select name="status" id="status" class="form-control">
                                <option>All</option>
                                <option value="P" <?php if($filter_data['status'] == 'P') { ?> selected <?php } ?>>Processing</option>
                                <option value="S" <?php if($filter_data['status'] == 'S') { ?> selected <?php } ?>>Shipped</option>
                                <option value="D" <?php if($filter_data['status'] == 'D') { ?> selected <?php } ?>>Delivered</option>
                                <option value="C" <?php if($filter_data['status'] == 'C') { ?> selected <?php } ?>>Cancelled</option>
                                <option value="ONP" <?php if($filter_data['status'] == 'ONP') { ?> selected <?php } ?>>Order Not Placed</option>
                            </select>
                        </div>

                        <div class="form-group col-md-7 px-0">
                            <label for="official_email">Filter by Date </label>
                            <div class="row">
                                <div class="form-group col-md-6 pl-0">
                                    <div class="col-md-4 pt-5 pr-0">
                                        <span style="cursor: pointer;" id="start_date_status">Add Start Date</span>
                                    </div>
                                    <div class="col-md-8 pl-0" <?php if($start_date == 0) { ?> style="display: none;" <?php } ?> id="filter_start_date">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control" id="datepicker3" name="start_date" placeholder="Start Date" value="<?php echo $start_date_value; ?>">
                                        </div>
                                        <span style="color: red;" id="start_date_err" class="clearfix"></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 pl-0">
                                    <div class="col-md-4 pt-5 pr-0">
                                        <span style="cursor: pointer;" id="end_date_status">Add End Date</span></div>
                                    <div class="col-md-6 pl-0" <?php if($end_date == 0) { ?> style="display: none;" <?php } ?> id="filter_end_date">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control" id="datepicker4" name="end_date" placeholder="End Date" value="<?php echo $end_date_value; ?>">
                                        </div>
                                        <span style="color: red;" id="end_date_err" class="clearfix"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-1">
                            <button type="button" class="btn btn-block btn-primary reset_btn" onclick="return form_submit();">Search</button></div>
                        <div class="form-group col-md-1" <?php
                        if(($filter_status == 0 || $filter_data['status'] == 'All' || $filter_data['status'] == 'all') && $start_date == 0 && $end_date == 0) { ?> style="display: none;" <?php } ?> >
                            <a href="<?php echo base_url('orders-list'); ?>"><button type="button" class="btn btn-block btn-primary reset_btn">Reset</button></a>
                        </div>

                        <div class="form-group col-md-1">
                            <button type="button" class="btn btn-block btn-primary reset_btn" onclick="export_data();" title="Export to Excel"> <i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
                <span style="color: red;"><b>Note:</b> To download/export orders list into file, it's mandatory to select any <b>Start Date</b> and <b>End Date</b> within 60 days. </span>
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
            <table id="example2" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th style="display: none;">Sl. No</th>
                <th>Order Details</th>
                <th>User Details</th>
                <th>Item(s) Details</th>
                <th>Total Price</th>
                <th>Payment Info</th>
                <th>Status</th>
                <th class="action_area_td">Action</th>
            </tr>
            </thead>
            <tbody> 
            <?php
            /*echo "<pre>";
            print_r($orders_list);
            echo "</pre>"; exit;*/
                if(count($orders_list) > 0)
                {
                $slno = 1;
                foreach($orders_list as $list)
                {
                    
                    ?>
                    <tr>
                    <td  style="display: none;"><?php echo $slno; ?></td>
                    <td>
                        <?php
                            echo "<b>ID: </b>".$list['order_no']."<br>";
                            echo "<b>Date: </b>".date("d/m/y H:i", strtotime($list['created_date']))."<br>";
                            echo "<b>Delivary Date: </b>".date("d/m/y", strtotime($list['delivery_date'])).'<br> ';
                            if($list['delivery_start_time'] < 12){
                                echo $list['delivery_start_time'].'am - ';
                            }else if($list['delivery_start_time'] == 12){
                                echo $list['delivery_start_time'].'pm - ';
                            }else{
                                $time = $list['delivery_start_time'] - 12;
                                echo $time.'pm - ';
                            }
                            if($list['delivery_end_time'] < 12){
                                echo $list['delivery_end_time'].'am';
                            }else if($list['delivery_end_time'] == 12){
                                echo $list['delivery_end_time'].'pm';
                            }else{
                                $time = $list['delivery_end_time'] - 12;
                                echo $time.'pm';
                            }
                        ?>
                    </td>
                    <td><?=$list['id']?>
                        <?php

                            echo "<b>Name: </b>".$list['customer_name']."<br>";
                            echo "<b>Address: </b>".$list['address']."<br>";
                            echo "<b>Phone: </b>".$list['phone']."<br>";
                        ?>
                    </td>
                    <td>
                        <?php
                            echo "<b>".count($list['products'])."</b> Item(s)";
                            echo '<hr>';
                            foreach ($list['products'] as $product){
                                ?>
                                <ul>
                                    <li><?php echo $product['title'].'('.$product['variation_title'].' x '.$product['quantity'].')'; ?></li>
                                </ul>
                                <?php
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            echo "<b>Order Total: </b>".$list['total_price']."<br>";
                            echo "<b>Delivery Charges: </b>".$list['delivery_charge'].".00<br>";
                            echo "<b>Discount(-): </b>".$list['discount']."<br>";
                            echo "<b>Grand Total: </b>".$list['order_total']."<br>";
                        ?>
                    </td>
                    <td> <?php echo ($list['payment_method'] == 'cod') ? 'Pay on Delivery' : 'Online Payment'; ?> </td>
                    <td>
                        
                        
                        <select id="order_status_<?=$list['id']?>" class="form-control ord_status" onchange="return change_order_status(<?=$list['id']?>);">
                        <option value="ONP" <?php if($list['status'] == 'ONP') { ?> selected="selected" <?php } ?>>Order In Process / Failed</option>
                        <option value="P" <?php if($list['status'] == 'P') { ?> selected="selected" <?php } ?> >Processing Order</option>
                        <option value="S" <?php if($list['status'] == 'S') { ?> selected="selected" <?php } ?>>Out for Delivery</option>
                        <option value="D" <?php if($list['status'] == 'D') { ?> selected="selected" <?php } ?>>Completed Order</option>
                        <option value="C" <?php if($list['status'] == 'C') { ?> selected="selected" <?php } ?>>Cancelled Order</option>
                        </select>

                        <div class="text-center" style="display:none" id="status_loader_<?=$list['id']?>">
                        <img src="<?=base_url('assets/dist/img/loader-mini.gif')?>" style="width: 100px;">
                        </div>

                    </td>
                    <td>
                        <a href="<?php echo base_url('orders-edit/'.$list['id']); ?>"><button type="button" class="btn bg-yellow btn-sm" title="Edit Details"><i class="fa fa-edit"></i>
                        </button></a>
                        <a href="<?php echo base_url('orders-invoice/'.$list['id']); ?>" ><button type="button" class="btn bg-green btn-sm" title="View Invoice"><i class="fa fa-print"></i>
                        </button></a>
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

    function change_order_status(id)
    {
        
        $('#status_loader_'+id).show();
        $(".ord_status").prop('disabled', true);

        var status_val = $( "#order_status_" + id + " selected" ).val();
        alert(status_val);
        //var status_text = $( "#order_status_"+id+" selected" ).text();
        var dataString = 'id=' + id + '&status=' + status_val;

        $.ajax({
        type: "POST",
        url: "<?=base_url('order_management/update_order_status')?>",
        data: dataString,
        cache: false,
        success: function(html) {
            var obj = $.parseJSON(html);
            if(obj.status == "Y")
            {
            // notify call
            //var status_val = $( "#order_status_"+id+" selected" ).val();
            var dataString = 'id=' + id + '&status=' + status_val;
            alert(dataString);

                /*$.ajax({
                type: "POST",
                url: "<?=base_url('order_management/update_order_status')?>",
                data: dataString,
                cache: false,
                success: function(html) {
                var obj = $.parseJSON(html);
                if(obj.status == "Y")
                {
                    

                }
                else
                {
                    
                    $('#status_loader_'+id).hide();
                    $(".ord_status").prop('disabled', false);
                    alert(obj.message);

                }
                
                
                }
                });*/

            }
            else
            {
            
            $('#status_loader_'+id).hide();
            $(".ord_status").prop('disabled', false);
            alert(obj.message);

            }
            
            
        }
        });

    }

    function date_check(date_is)
    {
        return moment(date_is, 'YYYY/MM/DD',true).isValid();
    }

    $("#start_date_status").click(function(){
        $('#datepicker3').val("").datepicker("update");
        $("#filter_start_date").toggle(function () {
            if ($('#filter_start_date').css('display')=='block') {
                $("#start_date_status").addClass("required_cls");
                $("#start_date_status").text("Remove Start Date");
            }else{
                $("#start_date_status").removeClass("required_cls");
                $("#start_date_status").text("Add Start Date");
            }
        });

    });

    $("#end_date_status").click(function(){
        $('#datepicker4').val("").datepicker("update");
        $("#filter_end_date").toggle();
        if ($('#filter_end_date').css('display')=='block') {
            $("#end_date_status").addClass("required_cls");
            $("#end_date_status").text("Remove End Date");
        }else {
            $("#end_date_status").removeClass("required_cls");
            $("#end_date_status").text("Add End Date");
        }
    });

    function form_submit()
    {
        $('.form-control').removeClass('error_cls');
        var focusStatus = "N";

        var start_date = document.getElementById("datepicker3").value.trim();
        var check_start_date = date_check(start_date);
        var end_date = document.getElementById("datepicker4").value.trim();
        var check_end_date = date_check(end_date);

        if(start_date != '' && check_start_date == false)
        {
            $("#start_date_err").text('Please provide valid date followed by eg: YYYY/MM/DD.');
            $('#start_date').addClass('error_cls');
            if(focusStatus == 'N')
            {
                $('#start_date').focus();
                focusStatus = 'Y';
            }
        }

        if(end_date != '')
        {
            if(check_end_date == false){
                $("#end_date_err").text('Please provide valid date followed by eg: YYYY/MM/DD.');
                $('#datepicker4').addClass('error_cls');
                if(focusStatus == 'N')
                {
                    $('#datepicker4').focus();
                    focusStatus = 'Y';
                }
            }else if(start_date != ''){
                if(Date.parse(start_date) < Date.parse(end_date)){
                    //console.log('Start Date: '+start_date+', End Date: '+end_date)
                }else {
                    $("#end_date_err").text('End date mast be greater then start date.');
                    $('#datepicker4').addClass('error_cls');
                    if(focusStatus == 'N')
                    {
                        $('#datepicker4').focus();
                        focusStatus = 'Y';
                    }
                }
            }

        }

        if(focusStatus == "N")
        {
            // no validation error.. now submit the form
            $("#filter_form").submit();
        }

    }

    function export_data() {
        var start_date = document.getElementById("datepicker3").value.trim();
        var check_start_date = date_check(start_date);
        var end_date = document.getElementById("datepicker4").value.trim();
        var check_end_date = date_check(end_date);

        if((start_date != '' && check_start_date == true) && (end_date != '' && check_end_date == true)){
            if(Date.parse(start_date) < Date.parse(end_date)){
                const timeDiff  = (new Date(start_date)) - (new Date(end_date));
                const days      = timeDiff / (1000 * 60 * 60 * 24);
                var total_days = days.toString().replace(/-/g, '')
                if(total_days <= 60){
                    $("#export_data").val('y');
                    $("#order_start_date").val(start_date);
                    $("#order_end_date").val(end_date);
                    $("#export_order_data").submit();
                }else{
                    $("#export_data").val('n');
                    alert('Total number of dates cannot be more then 60 days.');
                }
            }else {
                $("#export_data").val('n');
                alert('End date mast be greater then start date.');
            }
        }else {
            $("#export_data").val('n');
            alert('Start date and End date required.');
        }
    }

</script>