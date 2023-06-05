<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<footer class="main-footer">
    <div class="hidden-xs text-center">
      Copyright © <?php echo date('Y') ?> <strong>FARMOLOGY</strong>. All Rights Reserved.
    </div>
    
  </footer>
</div>
<!-- ./wrapper -->
<!-- jQuery 3 -->
<script src="<?php echo ASSETS_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo ASSETS_URL; ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="<?php echo ASSETS_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo ASSETS_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo ASSETS_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?php echo ASSETS_URL; ?>bower_components/raphael/raphael.min.js"></script>
<script src="<?php echo ASSETS_URL; ?>bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo ASSETS_URL; ?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?php echo ASSETS_URL; ?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo ASSETS_URL; ?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo ASSETS_URL; ?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo ASSETS_URL; ?>bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo ASSETS_URL; ?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo ASSETS_URL; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo ASSETS_URL; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo ASSETS_URL; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo ASSETS_URL; ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo ASSETS_URL; ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo ASSETS_URL; ?>dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo ASSETS_URL; ?>dist/js/demo.js"></script>
<!-- CK Editor -->
<!-- <script src="<?php echo ASSETS_URL; ?>bower_components/ckeditor/ckeditor.js"></script> -->
<script src="//cdn.ckeditor.com/4.13.0/full/ckeditor.js"></script>
<script>
  $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('description')
    CKEDITOR.replace('short_description')
  })
</script>
<script>
  $(function () { 

    $('#reservation').daterangepicker();

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true,
      format: 'dd/mm/yyyy'
    });
    //Date picker
    $('#datepicker1').datepicker({
      autoclose: true,
      format: 'dd/mm/yyyy'
    });   
    $('#datepicker2').datepicker({
      autoclose: true,
      format: 'dd/mm/yyyy',
    });
      $('#datepicker3').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
      });
      $('#datepicker4').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
      });
      $('#start_date').datepicker({
          startDate: new Date(),
          autoclose: true,
          format: 'yyyy-mm-dd'
      }).on('changeDate', function (selected) {
          var minDate = new Date(selected.date.valueOf());
          $('#end_date').datepicker('setStartDate', minDate);
      });

      $('#end_date').datepicker({
          startDate: new Date(),
          autoclose: true,
          format: 'yyyy-mm-dd'
      }).on('changeDate', function (selected) {
          var maxDate = new Date(selected.date.valueOf());
          $('#start_date').datepicker('setEndDate', maxDate);
      });
  })
</script>

<script>
  $(function () {
    $('#example1').DataTable({
      'paging'      : false,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : true
    })
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true
    })
  })

  var editor = CKEDITOR.replace( 'description', {
      allowedContent: true,
  } );

  var editor = CKEDITOR.replace( 'short_description', {
      allowedContent: true,
  } );
</script>

</body>
</html>
