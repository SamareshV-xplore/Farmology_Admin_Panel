<div class="content-wrapper">
    <section class="content-header">
        <h1><b>Payment Testing</b></h1>
    </section>
    <section class="content">
        <button id="pay_button" class="btn btn-primary" onclick="start_payment()">Pay ₹100</button>
    </section>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>

var razorpay_key_id = "<?=RAZORPAY_KEY_ID?>";
var amount = 100;
var currency = "INR";
var username = "Samaresh Adak";
var email = "samaresh@v-xplore.com";
var phone = "9748633568";

function start_payment()
{
    var postData = {amount: Math.round(amount*100), currency: currency};
    $.ajax({
        url: "<?=base_url('create-payment-order')?>",
        type: "POST",
        data: postData,
        error: function(a, b, c)
        {
            console.log(a);
            console.log(b);
            console.log(c);
        },
        success: function(data)
        {
            console.log(data);
            if(data.success)
            {
                open_payment_gateway(data.order_id);
            } 
            else
            {
                console.log(data.message);
                toast(data.message, 5000);
            }
        }
    })
}

function open_payment_gateway(order_id)
{
    var options = {
        "key": razorpay_key_id,
        "amount": amount,
        "currency": currency,
        "name": "Farmology",
        "description": "Payment Testing Charge",
        "image": "https://s3.amazonaws.com/rzp-mobile/images/rzp.png",
        "order_id": order_id,
        "handler": function (response){
            // on_payment_success(response);
        },
        "prefill": {
            "name": username,
            "email": email,
            "contact": phone
        },
        "theme": {
            "color": "#012652"
        }
    };

    var rzp1 = new Razorpay(options);
    rzp1.open();

    // rzp1.on('payment.failed', function (response){
    //     on_payment_failed(response);
    // });
}

function on_payment_success(response){
    console.log("Payment Success");
    console.log(response);
    // save_contact_details_sending_transaction(response, "success");
}

function on_payment_failed(response){
    console.log("Payment Failed");
    console.log(response);
    // save_contact_details_sending_transaction(response, "failed");
}

</script>