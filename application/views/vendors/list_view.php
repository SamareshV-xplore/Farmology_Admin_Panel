<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Vendors List</h1>
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
                            <table id="vendors_list_table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Shop Name</th>
                                <th>Service Area</th>
                                <th>Created Date</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (isset($vendors_list))
                                    {
                                        $counter = 1;
                                        foreach ($vendors_list as $vendor)
                                        {
                                            echo '<tr>';
                                            echo '<td>'.$counter.'</td>';
                                            echo '<td>'.$vendor->name.'</td>';
                                            echo '<td>'.$vendor->email.'</td>';
                                            echo '<td>'.$vendor->phone.'</td>';
                                            echo '<td>'.$vendor->shop_name.'</td>';

                                            $service_area = json_decode($vendor->service_area, true);
                                            echo '<td><ul style="list-style:none;">';
                                            foreach ($service_area as $area)
                                            {
                                                echo '<li>'.$area.'</li>';
                                            }
                                            echo '</ul></td>';

                                            echo '<td>'.$vendor->created_date.'</td>';
                                            if ($vendor->status == "A")
                                            {
                                                echo '<td>
                                                        <b class="text-success">Active</b>
                                                    </td>';
                                            }
                                            else
                                            {
                                                echo '<td>
                                                        <b class="text-danger">Inactive</b>
                                                    </td>';
                                            }
                                            echo '</tr>';

                                            $counter++;
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
<script>

    $(document).ready(function(){

        $("#vendors_list_table").DataTable({
            "order": [[0, "DESC"]]
        })

    });

</script>