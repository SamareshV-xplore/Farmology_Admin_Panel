<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="<?=base_url('assets/css/farmology_new_pages.css')?>"/>
<style>

    .header-with-button {
        margin: 0;
        padding: 0;
        display: flex;
        flex-flow: row;
        align-items: center;
        justify-content: space-between;
    }

    .settings_button {
        width: 40px;
        height: 40px;
        margin: 0;
        padding: 0;
        font-size: 20px;
        border-radius: 50%;
        color: black;
        background-color: white;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        display: grid;
        place-items: center;
    }

    .btn:focus {
        outline: none !important;
    }

    a {
        text-decoration: none !important;
        color: black;
    }

</style>


<div class="content-wrapper">
  <section class="content-header">
    <h3 class="header-with-button">
        <span>Plantix Requests</span>
        <a href="<?=base_url('plantix/product-recommendation')?>" target="_blank">
            <button class="btn btn-light settings_button">
                <i class="fa fa-cog" aria-hidden="true"></i>
            </button>
        </a>
    </h3>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">

            <div class="table-responsive">
              <table id="plantix_requests_listing_table" class="table table-striped vertical-align-middle display">
                <thead>
                    <tr>
                        <th style="text-align:left;">#</th>
                        <th>Request ID</th>
                        <th>Crop</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if (!empty($plant_diagnosis_requests)) {
                  for ($i=0; $i<count($plant_diagnosis_requests); $i++) { 
                  $request = $plant_diagnosis_requests[$i];?>
                    <tr id="<?=$request->hash_id?>">
                        <td style="text-align:left;"><?=$i+1?></td>
                        <td><?=$request->hash_id?></td>
                        <td><?=$request->crop?></td>
                        <td><?=date("jS F Y", strtotime($request->request_date))." at ".date("h:i A", strtotime($request->request_date))?></td>
                        <td><?=(!empty($request->report_id)) ? "<span class='text-success' style='font-weight:600;'>Completed</span>" : "<span class='text-danger' style='font-weight:600;'>Pending</span>";?></td>
                        <td>
                            <a href="javascript:delete_request('<?=$request->hash_id?>')"><i class="fa-solid fa-xmark cross_icons"></i></a>
                        </td>
                    </tr>
                  <?php }} ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
  
  $(document).ready(function(){
    $("#plantix_requests_listing_table").DataTable({
      "language": {
        "emptyTable": "No plantix requests is available"
      },
      "order": [[0, "desc"]]
    });
  });

  function delete_request (request_id) {
    console.log(request_id);
  }

</script>