<?php defined("BASEPATH") or exit("No direct script access allowed");
include APPPATH."third_party/vendor/autoload.php";
use Razorpay\Api\Api;

class Payment extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->razorpay = new Api(RAZORPAY_KEY_ID, RAZORPAY_SECRET_KEY);
    }

    public function response($data, $status)
    {
        return $this->output->set_content_type("application/json")
                            ->set_status_header($status)
                            ->set_output(json_encode($data));
    }

    public function index()
    {
        $header_data = array();
        $page_data = array();
        $left_data = array();
        $footer_data = array();

        $header_data['title'] = "Payment Testing";

        // check login or not
        if($this->common_model->user_login_check())
        {
            // allow and get admin details
            $admin_details = $this->common_model->get_admin_user_details();
            $header_data['admin_details'] = $admin_details;
            $left_data['admin_details'] = $admin_details;
        }
        else
        {
            redirect(base_url(''));
        }

        $this->load->view('includes/header_view', $header_data);
        $this->load->view('includes/left_view', $left_data);
        $this->load->view('payment_testing', $page_data);
        $this->load->view('includes/footer_view', $footer_data);
    }

    public function createOrder()
    {
        if (!empty($_POST["amount"]) && !empty($_POST["currency"]))
        {
            $order = $this->razorpay->order->create(["amount" => $_POST["amount"], "currency" => $_POST["currency"]]);
            $response = ["success" => true, "message" => "order created successfully.", "order_id" => $order["id"]];
        }
        else
        {
            $response = ["success" => false, "message" => "amount or currency is not given!"];
        }

        $this->response($response, 200);
    }

    public function savePaymentInformation()
    {        
        $paymentDataPayload = file_get_contents("php://input");
        $file_name = time()."-".mt_rand().".txt";
        $dataPayloadFile = fopen(FCPATH."assets/payment_payload_response_files/".$file_name, "w");
        fwrite($dataPayloadFile, $paymentDataPayload);
        fclose($dataPayloadFile);

        $data = json_decode($paymentDataPayload);
        if (isset($data->payload->payment->entity->captured) && isset($data->payload->payment->entity->order_id))
        {
            $razorpay_payment_id = $data->payload->payment->entity->id;
            $razorpay_order_id = $data->payload->payment->entity->order_id;
            $captured = $data->payload->payment->entity->captured;
            $error_description = $data->payload->payment->entity->error_description;

            if ($captured == true)
            {
                $payment_transaction_condition = ["razorpay_order_id" => $razorpay_order_id];
                $payment_transaction_data = ["razorpay_payment_id" => $razorpay_payment_id, "status" => "SUCCESS"];
                $payment_transaction_updated = $this->db->set($payment_transaction_data)->where($payment_transaction_condition)->update("FM_plant_diagnosis_payment_transactions");
                $subscription_status = "VALID";
            }
            elseif ($captured == false)
            {
                $payment_transaction_condition = ["razorpay_order_id" => $razorpay_order_id];
                $payment_transaction_data = ["razorpay_payment_id" => $razorpay_payment_id, "status" => "FAILED", "error_description" => $error_description];
                $payment_transaction_updated = $this->db->set($payment_transaction_data)->where($payment_transaction_condition)->update("FM_plant_diagnosis_payment_transactions");
                $subscription_status = "CANCELLED";
            }

            if (!empty($payment_transaction_updated) && !empty($subscription_status))
            {
                $condition = ["razorpay_order_id" => $razorpay_order_id, "razorpay_payment_id" => $razorpay_payment_id];
                $transaction_data = $this->db->from("FM_plant_diagnosis_payment_transactions")->where($condition)->order_by("id", "DESC")->get()->row();

                $subscription_id = (!empty($transaction_data->subscription_id)) ? $transaction_data->subscription_id : NULL;
                if (!empty($subscription_id))
                {
                    $this->db->set(["status" => $subscription_status])->where(["hash_id" => $subscription_id])->update("FM_plant_diagnosis_subscriptions");
                }
            }
        }
    }

    // public function savePaymentInformation(){
    //     $paymentDataPayload = file_get_contents("php://input");
    //     $paymentDataPayload = json_encode($paymentDataPayload);
    //     // $sql = "INSERT INTO test (text) VALUES('{$paymentDataPayload}')";
    //     // $this->db->query($sql);

    //     $file_name = time()."-".mt_rand().".txt";
    //     $dataPayloadFile = fopen(FCPATH."assets/payment_payload_response_files/".$file_name, "w");
    //     fwrite($dataPayloadFile, $paymentDataPayload);
    //     fclose($dataPayloadFile);
    // }

}