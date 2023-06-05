<style>
/* The snackbar - position it at the bottom and in the middle of the screen */
#snackbar {
  visibility: hidden; /* Hidden by default. Visible on click */
  min-width: 250px; /* Set a default minimum width */
  margin-left: -125px; /* Divide value of min-width by 2 */
  background-color: #333; /* Black background color */
  color: #fff; /* White text color */
  text-align: center; /* Centered text */
  border-radius: 2px; /* Rounded borders */
  padding: 16px; /* Padding */
  position: fixed; /* Sit on top of the screen */
  z-index: 1; /* Add a z-index if needed */
  left: 50%; /* Center the snackbar */
  bottom: 30px; /* 30px from the bottom */
}

/* Show the snackbar when clicking on a button (class added with JavaScript) */
#snackbar.show {
  visibility: visible; /* Show the snackbar */
  /* Add animation: Take 0.5 seconds to fade in and out the snackbar.
  However, delay the fade out process for 2.5 seconds */
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

/* Animations to fade the snackbar in and out */
@-webkit-keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

@-webkit-keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
}
</style>

<div class="content-wrapper">

  <!-- Snackbar Container Start -->
  <div id="snackbar">Note: please wait till all the records are fetched properly.</div>
  <!-- Snackbar Container End -->

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Referrals</h1>
  </section>
  <!-- Content Header (Page header) -->


  <!-- Main Content Start -->
  <section class="content">
    <div class="row">
        <div class="col-xs-12">
          <form id="referrals_data_form">
          <div class="box">
            <form id="referrals_data_form">
            <div class="box-header">
              <div style="width:180px; font-weight:500; display:flex; flex-flow:row; align-items:center; white-space:nowrap;">
                <div>Fetch Total</div>&nbsp;
                <select name="record_limit" id="record_limit" class="form-control input-sm" style="min-width:75px; padding:5px 10px;">
                  
                  <?php if(isset($record_limit_list))
                  { 
                    foreach($record_limit_list as $limit){
                      print "<option>$limit</option>";
                    }
                  } 
                  else 
                  {
                    print "<option>100</option>
                           <option>500</option>
                           <option>1000</option>
                           <option>2000</option>
                           <option>4000</option>";
                  }?>

                </select>&nbsp;
                <div>Records</div>
              </div>
            </div>
            </form>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
              <table id="referrals_data_table" class="table table-bordered table-striped">
               <thead>
                      <tr>
                          <th>Id</th>
                          <th>Name</th>
                          <th>Mobile</th>
                          <th>No. of Refer</th>
                          <th>Pincode</th>
                          <th>Order Value</th>
                          <th>Last Order</th>
                          <th>Refer Discount</th>
                          <th>Amount</th>
                          <th>Action</th>
                      </tr>
                </thead>
              </table>
            </div>
            </div>
            <!-- /.box-body -->
          </div>
          </form>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
  </section>
  <!-- Main Content End -->
</div>
<script>
var dataTable;
$(document).ready(function(){

  dataTable = $('#referrals_data_table').DataTable({

    ajax: {
      url: "<?=base_url('get_referrals_data')?>",
      type: "POST",
      data: {filter_data: function(){return $("#referrals_data_form").serialize();}},
      error: function(a,b,c){
        console.log(a);
        console.log(b);
        console.log(c);
      }
    },

    columns: [
      {data:'id'},
      {data:'name'},
      {data:'mobile'},
      {data:'no_of_refer'},
      {data:'pincode'},
      {data:'order_value'},
      {data:'last_order'},
      {data:'refer_discount'},
      {data:'amount'},
      {data:'action'}
    ],

    order: [[0,"desc"]]
  });


  $("#referrals_data_form").submit(function(e){
    e.preventDefault();
    dataTable.ajax.reload();
  });


  $("#record_limit").on("change",function(){
    showSnackbar();
    $("#referrals_data_form").submit();
  });

});
</script>
<script>
function showSnackbar(){
  // Get the snackbar DIV
  var x = document.getElementById("snackbar");

  // Add the "show" class to DIV
  x.className = "show";

  // After 3 seconds, remove the show class from DIV
  setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}
</script>