<style type="text/css">

  label {
    font-weight: 500;
  }

  .listing-div{
    border-right: 1px solid #707070;
  }

  .container-header {
    color: #777;
    font-size: 24px;
    font-weight: 600;
  }

  .promo-codes {
    background: #FFFFFF;
    border: 1px solid #CCCCCC;
    box-sizing: border-box;
    box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    font-family: 'Roboto';
    font-style: normal;
    margin-bottom: 10px;
    cursor: pointer;
  }

  .promo-code-details {
    display: flex;
    flex-flow: row;
    align-items: center;
    justify-content: space-between;
    margin: 5px;
  }

  .promo-code-title {
    font-weight: 500;
    font-size: 20px;
    color: #3A8A46;
  }

  .promo-code {
    white-space: nowrap;
    font-weight: 500;
    font-size: 18px;
    color: #999;
  }

  .promo-code-desc {
    font-weight: 400;
    font-size: 14px;
    color: #000000;
  }

  .promo-code-status {
    font-weight: 500;
    font-size: 18px;
    color: #248614;
  }

  #promo_code_desc {
    resize:none;
    overflow-y:auto;
  }

  .users_list_container_main {
    position: relative;
  }

  .users_list_container {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 300px;
    overflow-y: auto;
    border-radius: 0 0 3px 3px;
    background: whitesmoke;
    box-shadow: 0.2px 4px 8px rgba(0, 0, 0, 0.4);
    z-index: 1;
  }

  .user_details {
    margin: 3px 5px;
    padding: 3px 5px;
    background-color: white;
    cursor: pointer;
    border-radius: 3px;
  }

  .user_details h4 {
    margin: 0;
  }

  .selected_users_container {
    min-height: 40px;
    border-radius: 3px;
    background: whitesmoke;
    padding: 5px;
    display: flex;  
    flex-wrap: wrap;
    justify-content: start;
    align-items: flex-start;
  }

  .user_tag {
    margin: 3px 2px;
    padding: 2px 3px;
    border-radius: 3px;
    background: white;
    font-size: 16px;
    white-space: nowrap;
  }

</style>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

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
              <div class="row">

                <!-- Promo Codes List Container Start -->
                <div class="col-xs-6 listing-div">
                  <p class="container-header">All Coupons</p>
                  <div id="promo_code_container">
                  <?php 
                    foreach ($promo_codes as $code) {
                      if ($code->status == "Y")
                      {
                        $status = "<span color='#248614'>Active</span>";
                      }
                      else if ($code->status == "N")
                      {
                        $status = "<span color='#BEBEBE'>Inactive</span>";
                      }
                      else if ($code->status == "D")
                      {
                        $status = "<span color='#dc3545'>Deleted</span>";
                      }
                  ?>
                      <div id="<?=$code->promo_code?>" class="promo-codes" onclick="edit_coupon_details(this)">
                        <div class="promo-code-details">
                          <div class="promo-code-title"><?=$code->title?></div>
                          <div class="promo-code" onclick="copy_in_clipboard(this, '<?=$code->promo_code?>')" data-toggle="tooltip" data-placement="top" title="Copy">
                            <i class="fa fa-clipboard" aria-hidden="true"></i>
                            &nbsp;<?=$code->promo_code?>
                          </div>
                        </div>
                        <div class="promo-code-details">
                          <div class="promo-code-desc"><?=$code->description?></div>
                          <div class="promo-code-status">
                            <?=$status?>
                          </div>
                        </div>
                      </div>
                  <?php
                    }
                  ?>
                  </div>
                </div>
                <!-- Promo Codes List Container End -->

                <!-- Promo Code Form Container Start -->
                <div class="col-xs-6 new-coupon-div">
                  <p id="form_header" class="container-header">Generate New Coupon</p>
                  <form id="promo_code_form">

                    <div class="form-group">
                      <label for="title">Display Name</label>
                      <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>Description</label>
                      <textarea rows="6" id="promo_code_desc" name="promo_code_desc" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-sm-4">
                          <label for="min_order_price">Minimum Order Price</label>
                          <input type="text" name="min_order_price" id="min_order_price" class="form-control">
                        </div>
                        <div class="col-sm-4">
                          <label for="applicable_from">Code Applicable From</label>
                          <input type="date" name="applicable_from" id="applicable_from" class="form-control" required>
                        </div>
                        <div class="col-sm-4">
                          <label for="applicable_till">Code Applicable Till</label>
                          <input type="date" name="applicable_till" id="applicable_till" class="form-control" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-sm-4">
                            <label for="discount">Discount</label>
                            <input type="text" name="discount" id="discount" class="form-control" required>
                          </div>
                          <div class="col-sm-8">
                            <label for="discount_type">Discount Type</label>
                            <select name="discount_type" id="discount_type" class="form-control">
                              <option value="P">Percentage</option>
                              <option value="FL">Flat</option>
                              <option value="FR">Free Delivery</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="row">
                        <div class="col-sm-4">
                          <label for="max_discount_limit">Maximum Discount Limit</label>
                          <input type="text" name="max_discount_limit" id="max_discount_limit" class="form-control">
                        </div>
                        <div class="col-sm-8">
                          <label for="status">Status</label>
                          <select name="status" id="status" class="form-control">
                            <option value="Y">Active</option>
                            <option value="N">Inactive</option>
                            <option value="D">Deleted</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="row">
                        <div class="col-sm-4">
                          <label for="specific_user">For Specific User?</label>
                          <select name="specific_user" id="specific_user" class="form-control">
                            <option value="Y">Yes</option>
                            <option value="N" selected>No</option>
                          </select>
                        </div>
                        <div class="col-sm-8">
                          <label for="choose_specific_user">Choose Specific User</label>
                          <input id="choose_specific_user" class="form-control" autocomplete="off" disabled>
                          <div class="users_list_container_main">
                            <div class="users_list_container" style="display:none;"></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div id="selected_users_form_group" class="form-group" style="display:none;">
                      <label>Selected Users</label>
                      <div class="selected_users_container"></div>
                    </div>
                    
                    <div class="form-group">
                      <button type="submit" id="submit_button" class="btn btn-success" style="float:right;">Generate</button>
                    </div>
                    
                  </form>
                </div>
                <!-- Promo Code Form Container End -->

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


  function copy_in_clipboard (button, text)
  {
    event.stopPropagation();
    navigator.clipboard.writeText(text);
    $(button).attr("title", "Copied");
    $(button).attr("data-original-title", "Copied");
    $(button).tooltip("show");
  }


  function render_coupon (coupon_details)
  {
    if (coupon_details.status == "Y")
    {
      var status = "<span color='#248614'>Active</span>";
    }
    else if (coupon_details.status == "N")
    {
      var status = "<span color='#BEBEBE'>Inactive</span>";
    }
    else if (coupon_details.status == "D")
    {
      var status = "<span color='#dc3545'>Deleted</span>";
    }

    var coupon = `<div id="${coupon_details.promo_code}" class="promo-codes" onclick="edit_coupon_details(this)">
                    <div class="promo-code-details">
                      <div class="promo-code-title">${coupon_details.title}</div>
                      <div class="promo-code" onclick="copy_in_clipboard(this, '${coupon_details.promo_code}')" data-toggle="tooltip" data-placement="top" title="Copy">
                        <i class="fa fa-clipboard" aria-hidden="true"></i>
                        &nbsp;${coupon_details.promo_code}
                      </div>
                    </div>
                    <div class="promo-code-details">
                      <div class="promo-code-desc">${coupon_details.description}</div>
                      <div class="promo-code-status">
                        ${status}
                      </div>
                    </div>
                  </div>`;

    return coupon;
  }


  function render_coupon_list (coupon_list)
  {
    var list = "";
    for (let i=0; i<coupon_list.length; i++)
    {
      list += render_coupon(coupon_list[i]);
    }

    $("#promo_code_container").html(list);
  }


  function fill_editable_coupon_details (coupon_details)
  {
    $("#form_header").text("Edit Coupon Details");
    $("#promo_code_form").attr("id", "edit_promo_code_form");
    $("#title").val(coupon_details.title);

    if (coupon_details.hasOwnProperty("description"))
    {
      $("#promo_code_desc").val(coupon_details.description);
    }

    if (coupon_details.hasOwnProperty("eligible_order_price"))
    {
      $("#min_order_price").val(coupon_details.eligible_order_price);
    }
    
    $("#applicable_from").val(coupon_details.start_date);
    $("#applicable_till").val(coupon_details.end_date);
    $("#discount").val(coupon_details.discount_limit);
    $("#discount_type").val(coupon_details.discount_type);

    if (coupon_details.hasOwnProperty("max_limit"))
    {
      $("#max_discount_limit").val(coupon_details.max_limit);
    }
    
    $("#status").val(coupon_details.status);
    $("#specific_user").children("option").removeAttr("selected");
    $("#specific_user").val(coupon_details.user_specific);

    if (coupon_details.user_specific == "Y")
    {
      enable_specific_users_selection_controls();
      if (coupon_details.user_id != 0 || coupon_details.user_id != null)
      {
        get_customer_name(coupon_details.user_id);
      }
    }
    else
    {
      disable_specific_users_selection_controls();
    }

    var coupon_code = coupon_details.promo_code;
    var hidden_input = `<input type="hidden" name="coupon_code" value="${coupon_code}">`;
    $(hidden_input).insertBefore("#submit_button");

    $("#submit_button").toggleClass("btn-success btn-primary");
    $("#submit_button").text("Edit");
    $("#submit_button").attr("type","button");
    $("#submit_button").attr("onclick","edit_promo_code_form_submit()");
  }


  function edit_coupon_details (coupon)
  {
    $.ajax({
      url : "<?=base_url('code_generator/get_coupon_details_by_coupon_code/')?>"+coupon.id,
      type : "GET",
      error : function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success : function (res)
      {
        var coupon_details = JSON.parse(res);
        fill_editable_coupon_details(coupon_details);
      }
    });
  }

  function load_default_form ()
  {
    $("#form_header").text("Generate New Coupon");
    $("#edit_promo_code_form").attr("id", "promo_code_form");
    $("#specific_user").val("N");

    $("#choose_specific_user").attr("disabled","disabled");
    $(".users_list_container").attr("style","display:none");
    $(".users_list_container").html("");
    $("#selected_users_form_group").attr("style","display:none;");

    $("#submit_button").siblings("input:hidden[name=coupon_code]").remove();

    $("#submit_button").toggleClass("btn-primary btn-success");
    $("#submit_button").text("Generate");
    $("#submit_button").attr("type","submit");
    $("#submit_button").removeAttr("onclick");
  }

  $("#promo_code_form").on("submit", function (e) {

    e.preventDefault();
    var form = document.getElementById("promo_code_form");
    var postData = new FormData(form);

    if ($("#specific_user").val()=="Y")
    {
      postData.append("users_id_list", get_selected_users_id_list());
    }

    $.ajax({
      url : "<?=base_url('code_generator/add_new_coupon')?>",
      type : "POST",
      data : postData,
      processData : false,
      contentType : false,
      error : function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success : function (res)
      {
        var response = JSON.parse(res);
        if (response.success)
        {
          form.reset();
          load_default_form();
          render_coupon_list(response.coupon_list);
          swal(response.message);
        }
        else
        {
          swal(response.message);
        }
      }
    });
  });


  function edit_promo_code_form_submit ()
  {
    var form = document.getElementById("edit_promo_code_form");
    var postData = new FormData(form);

    if ($("#specific_user").val()=="Y")
    {
      postData.append("users_id_list", get_selected_users_id_list());
    }

    $.ajax({
      url : "<?=base_url('code_generator/edit_coupon_details')?>",
      type : "POST",
      data : postData,
      processData : false,
      contentType : false,
      error : function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success : function (res)
      {
        var response = JSON.parse(res);
        if (response.success)
        {
          form.reset();
          load_default_form();
          render_coupon_list(response.coupon_list);
          swal(response.message);
        }
        else
        {
          swal(response.message);
        }
      }
    });
  }


  function create_tag (user_id, user_name)
  {
    var tag_id = "TID"+Math.floor((Math.random() * 999) + 1);
    var tag = `<div id="${tag_id}" class="user_tag">
                      <div class="user_tag_details">
                        ${user_name}
                        &nbsp;<a href="javascript:remove_tag('${tag_id}')">&times;</a>
                      </div>
                      <div class="selected_user_id" style="display:none;">${user_id}</div>
                    </div>`;

    return tag;
  }


  function remove_tag (tag_id)
  {
    $("#"+tag_id).remove();
  }


  function get_customer_name (id)
  {
    
    $.ajax({
      url : "<?=base_url('code_generator/get_customer_name/')?>"+id,
      type : "GET",
      error : function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success : function (name)
      {
        var user_tag = create_tag(id, name);
        $(".selected_users_container").html(user_tag);
      }
    });
  }


  function check_user_id_selected (user_id)
  {
    var selection_status = false;
    var selected_user_id_list = document.getElementsByClassName("selected_user_id");
    for (let i=0; i<selected_user_id_list.length; i++)
    {
      if (selected_user_id_list[i].innerText == user_id)
      {
        selection_status = true;
      }
    }

    return selection_status;
  }


  function select_user (user)
  {
    var user_id = user.id;
    var user_name = $(user).children("h4").text();

    if (check_user_id_selected(user_id))
    {
      swal(user_name+" is already selected!");
    }
    else
    {
      var user_tag = create_tag(user_id, user_name);
      $(".selected_users_container").html(user_tag);
      $("#choose_specific_user").val("");
      $(".users_list_container").attr("style","display:none");
    }
  }


  function render_users_list (users_list)
  {
    var users_list_html = "";
    for (let i=0; i<users_list.length; i++)
    {
      var user_html = `<div id="${users_list[i].id}" class="user_details" onclick="select_user(this)">
                        <h4>${users_list[i].name}</h4>`;

      if (users_list[i].email!=null && users_list[i].email!="")
      {
        user_html += `<div>
                        Email: ${users_list[i].email}
                      </div>`;
      }

      if (users_list[i].phone!=null && users_list[i].phone!="")
      {
        user_html += `<div>
                        Phone: ${users_list[i].phone}
                      </div>`;
      }

      user_html += `</div>`;

      users_list_html += user_html;
    }

    $(".users_list_container").html(users_list_html);
  }


  function get_selected_users_id_list ()
  {
    var selected_users_id_list = [];
    var selected_users = document.getElementsByClassName("selected_user_id");
    for (let i=0; i<selected_users.length; i++)
    {
      selected_users_id_list[i] = selected_users[i].innerText;
    }

    return selected_users_id_list;
  }


  function render_default_customers_list ()
  {
    $.ajax({
      url: "<?=base_url('code_generator/get_customers_list')?>",
      type : "GET",
      error : function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success : function (res)
      {
        var users_list = JSON.parse(res);
        render_users_list(users_list);
      }
    });
  }


  function enable_specific_users_selection_controls ()
  {
    $("#choose_specific_user").removeAttr("disabled");
    $("#selected_users_form_group").attr("style","display:block;");
    $(".selected_users_container").html("");
    render_default_customers_list();
  }


  function disable_specific_users_selection_controls ()
  {
    $("#choose_specific_user").attr("disabled","disabled");
    $(".users_list_container").attr("style","display:none");
    $("#selected_users_form_group").attr("style","display:none;");
  }


  $("#specific_user").on("change", function () {
    var selected_value = $(this).val();
    if (selected_value == "Y")
    {
      enable_specific_users_selection_controls();
    }
    else
    {
      disable_specific_users_selection_controls();
    }
  });


  $("#choose_specific_user").focusin(function() {
      $(".users_list_container").attr("style","display:block;");
  });


  function apply_empty_list_styles ()
  {
    var styles = {
      "display" : "grid",
      "place-items" : "center"
    };

    $(".users_list_container").css(styles);
  }


  function remove_empty_list_styles ()
  {
    var styles = {};
    $(".users_list_container").css(styles);
  }


  function show_message_in_container (message)
  {
    $(".users_list_container").html(`<h4 style="width:100%; height:90%; display:grid; place-items:center">${message}</h4>`);
  }


  function search_and_render_customers_list (search_value)
  {
    $.ajax({
      url : "<?=base_url('code_generator/search_from_customers_list/')?>"+search_value,
      type : "GET",
      beforeSend : function ()
      {
        show_message_in_container("Searching...");
      },
      error : function (a, b, c)
      {
        console.log(a);
        console.log(b);
        console.log(c);
      },
      success : function (res)
      {
        if (res != "")
        {
          var users_list = JSON.parse(res);
          render_users_list(users_list);
        }
        else
        {
          show_message_in_container("Sorry, No User Found!");
        }
      }
    });
  }


  $("#choose_specific_user").keyup(function () {
    var search_value = $(this).val();
    if (search_value.length > 3)
    {
      search_and_render_customers_list(search_value);
    }
    else
    {
      render_default_customers_list();
    }
  });


</script>