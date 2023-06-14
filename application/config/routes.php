<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['forget-password'] = 'forget_password';
$route['reset-password/(:any)'] = 'forget_password/reset_password/$1';
$route['profile'] = 'admin_profile';

$route['banner-add'] = 'banner/add';
$route['banner-list'] = 'banner';
$route['banner-edit/(:any)'] = 'banner/edit/$1';
$route['banner-delete/(:any)'] = 'banner/delete/$1';
$route["uploadCompressedImage"] = "banner/uploadCompressedImage";

$route['page-content'] = 'page_content/index';
$route['page-content-edit/(:any)'] = 'page_content/edit/$1';

$route['users-list'] = 'users';
$route['user-edit/(:any)'] = 'users/edit/$1';
$route['user-delete/(:any)'] = 'users/delete/$1';
$route["download-users-contact"] = "users/download_users_contact_details_in_csv";

$route['merchants'] = 'merchants';
$route['merchant-edit/(:any)'] = 'merchants/edit/$1';
$route['mrechant-delete/(:any)'] = 'merchants/delete/$1';

$route["merchant-earned-commissions"] = "merchant_earned_commissions";

$route['vendors-list'] = 'vendors';

// ===============================
// Product Management Routes Start
// ===============================
$route["change-product-variation-availability"] = "product/change_product_variation_availability";
// =============================
// Product Management Routes End
// =============================


// ============================
// Farm Management Routes Start
// ============================
$route['farm-management/farms-list'] = 'farm_management/farms_list';
$route["farm-management/report-subscriptions-list"] = "farm_management/report_subscriptions_list";
$route["farm-management/crop-health-reports"] = "farm_management/crop_health_reports";
$route['getProductByProductId'] = 'expert_advice/getProductByProductId';
$route['getFarmReportImages'] = 'expert_advice/getFarmReportImages';
$route['setRecommendedProducts'] = 'expert_advice/setRecommendedProducts';
$route["delete_farm/(:any)"] = "expert_advice/delete_farm_and_reports/$1";
$route['generate-report'] = 'expert_advice/generate_report';
$route['refresh-report-data/(:any)'] = 'expert_advice/refresh_report_data/$1';
$route['preview-generated-report'] = 'expert_advice/preview_generated_report';
$route['process-generated-report'] = 'expert_advice/process_generated_report';

$route['test_pdf_structure/(:any)'] = "expert_advice/test_pdf_structure/$1";
$route['get_crop_name/(:any)'] = "expert_advice/get_crop_name_by_farm_id/$1";
$route['get_sowing_date/(:any)'] = "expert_advice/get_sowing_date_by_farm_id/$1";

$route['get-list-of-possible-pest-and-diseases/(:any)'] = 'farm_management/getListOfPossiblePestAndDiseases/$1';
$route['get-weather-forecast'] = 'farm_management/getWeatherForecast';
// ==========================
// Farm Management Routes End
// ==========================

$route['promo-list'] = 'promo_code_management';
$route['promo-add'] = 'promo_code_management/add';
$route['promo-edit/(:any)'] = 'promo_code_management/edit/$1';
$route['promo-delete/(:any)'] = 'promo_code_management/delete/$1';

$route['city-list'] = 'city_management';
$route['city-add'] = 'city_management/add';
$route['city-edit/(:any)'] = 'city_management/edit/$1';
$route['city-delete/(:any)'] = 'city_management/delete/$1';

$route['state-management'] = 'state_management';
$route['change-state-availability'] = 'state_management/change_state_availability';
$route['delete-state'] = 'state_management/delete_state';

// ====================================
// Help and FAQ Management Routes Start
// ====================================
$route["help-and-faq-management"] = "help_and_faq_management";
$route["update-help-and-support-details"] = "help_and_faq_management/update_help_and_support_details";
$route["add-faq"] = "help_and_faq_management/add_FAQ";
$route["delete-faq"] = "help_and_faq_management/delete_FAQ";
// ====================================
// Help and FAQ Management Routes Start
// ====================================

// ================================
// District Management Routes Start
// ================================
$route['district-list'] = 'district_management';
$route['district-add'] = 'district_management/add';
$route['district-edit/(:any)'] = 'district_management/edit/$1';
$route['district-delete/(:any)'] = 'district_management/delete/$1';
// ==============================
// District Management Routes End
// ==============================

// ========================================
// District Pincode Management Routes Start
// ========================================
$route["district-pincodes-list/(:any)"] = "district_pincode_management/index/$1";
$route["add-district-pincode"] = "district_pincode_management/add_pincode";
$route["delete-district-pincode"] = "district_pincode_management/delete_pincode";
// ========================================
// District Pincode Management Routes Start
// ========================================

$route['zip-list/(:any)'] = 'zip_management/index/$1';
$route['zip-delete/(:any)'] = 'zip_management/delete/$1';

// ========================================
//  Orders Management Routes Start
// ========================================
$route['orders-list'] = 'order_management';
$route['orders-edit/(:any)'] = 'order_management/edit/$1';
$route['orders-invoice/(:any)'] = 'order_management/view_invoice/$1';
$route['download-invoice/(:any)'] = 'order_management/download_invoice/$1';
$route['download-order-invoice/(:any)'] = 'order_management/invoice_by_order_number/$1';

$route["get-delivery-drivers-list/(:any)"] = "order/get_delivery_drivers_list_by_order_no/$1";
$route["get-merchant-centers-list/(:any)"] = "order/get_merchant_centers_list_by_order_no/$1";
$route["assign-delivery-driver"] = "order/assign_delivery_driver";
// ========================================
// Orders Management Routes End
// ========================================

// ========================================
// Delivery Drivers Management Routes Start
// ========================================
$route["delivery-drivers-list"] = "delivery_drivers_management";
$route["add-delivery-driver"] = "delivery_drivers_management/add_delivery_driver";
$route["add-new-delivery-driver"] = "delivery_drivers_management/add_new_delivery_driver";
$route["get-districts-list-by-state/(:any)"] = "delivery_drivers_management/get_districts_list_by_state_id/$1";
$route["get-available-pincodes-list-by-district/(:any)"] = "delivery_drivers_management/get_available_pincodes_list_by_district_id/$1";
$route["edit-delivery-driver/(:any)"] = "delivery_drivers_management/edit_delivery_driver/$1";
$route["edit-existing-delivery-driver"] = "delivery_drivers_management/edit_existing_delivery_driver";
$route["delete-delivery-driver"] = "delivery_drivers_management/delete_delivery_driver";
// ======================================
// Delivery Drivers Management Routes End
// ======================================

// =====================================
// Delivery Date Management Routes Start
// =====================================
$route["delivery-date-management"] = "delivery_date_management";
$route["add-delivery-date"] = "delivery_date_management/add_delivery_date";
$route["delete-delivery-date"] = "delivery_date_management/delete_delivery_date";
// =====================================
// Delivery Date Management Routes Start
// =====================================

$route['meta-add'] = 'meta_data/add';
$route['meta-list'] = 'meta_data';
$route['meta-edit/(:any)'] = 'meta_data/edit/$1';
$route['meta-delete/(:any)'] = 'meta_data/delete/$1';

$route['push-notification'] = 'push_notification';

$route['master-settings'] = 'site_settings/index';
$route['test'] = 'site_settings/test';
$route['test1'] = 'site_settings/test1';
$route["update-delivery-driver-app-version-details"] = "site_settings/update_delivery_driver_app_version_details";

$route['notification/send_notification/(:any)'] = 'notification/send_notification/$1';
$route['review-list'] = 'product_comment/index';

$route['questions-list'] = 'questions';
$route['question-add'] = 'questions/add';
$route['question-edit/(:any)'] = 'questions/edit/$1';
$route['question-delete/(:any)'] = 'questions/delete/$1';
$route['get_recommended_product/(:any)'] = 'questions/get_recommended_product/$1';

// $route['sellproduces-list'] = 'sellproduces';
// $route['sellproduces-details/(:any)'] = 'sellproduces/details/$1';
// $route["delete_sell_produce/(:any)"] = "sellproduces/delete/$1";

$route['communities-list'] = 'Community';
$route['communities-details/(:any)'] = 'community/details/$1';

$route['referrals'] = 'Referrals';
$route['get_referrals_data'] = 'Referrals/get_referrals_data';

// Schedule Push Notification Page Routes
// =================================================================================================
$route["schedule-push-notification"] = "Schedule_push_notification";


// Soil Health Test Page and API Routes
// ==================================================================================================
$route["soil-health-test"] = "Soil_health_test";
$route["sample_received"] = "Soil_health_test/change_sample_received_status";
$route["generate_soil_health_report"] = "Soil_health_test/generate_soil_health_report";
$route["preview-soil-health-report"] = "Soil_health_test/preview_generated_soil_health_report";
$route["process_soil_health_report"] = "Soil_health_test/process_soil_health_report";
$route["soil_health_report_pdf_structure"] = "Soil_health_test/test_pdf_structure";
$route["soil_health_report_pdf_structure_in_html"] = "Soil_health_test/test_pdf_structure_in_html";



// Plantix Page and API Routes
// ===================================================================================================
$route["plantix"] = "Plantix";
$route["plantix/product-recommendation"] = "Plantix/product_recommendation";
$route["plantix/edit-product-recommendation"] = "Plantix/edit_product_recommendation";
$route["get_previously_suggested_products"] = "Plantix/get_previously_suggested_products";



// New Dashboard Page and API Routes
// ===================================================================================================
$route["dashboard"] = "Dashboard_new";
$route["get_dashboard_order_value_list"] = "Dashboard_new/get_12_month_order_value_list";



// New Service Coupons Page and API Routes
// ====================================================================================================
$route["service-coupons"] = "Service_coupons";
$route["add-service-coupon"] = "Service_coupons/add_service_coupon";
$route["delete-service-coupon"] = "Service_coupons/delete_service_coupon";

// New User App Referrals Page and API Routes
// =====================================================================================================
$route["user-referrals"] = "user_referrals";
$route["download-farmology"] = "user_referrals/download_farmology_apk";


$route['vendors'] = "Vendor";
$route['vendors/register'] = "Vendor/signup";
$route['vendors/sendOtp'] = "Vendor/sendOtp";
$route['vendors/submitUser'] = "Vendor/submitUser";
$route['vendors/dashboard'] = "Vendor/dashboard";
$route['vendors/products'] = "Vendor/products";
$route['vendors/orders'] = "Vendor/orders";
$route['vendors/profile'] = "Vendor/profile";
$route['vendors/add_product'] = "vendor/addProduct";
$route['vendors/add_new_product'] = "vendor/addNewProduct";
$route['vendors/edit_product/:num'] = "vendor/editProduct";
$route['vendors/edit_submit'] = "vendor/editSubmit";
$route['vendors/delete_product/:num'] = "vendor/deleteProduct";
$route['vendors/order_details/:num'] = "vendor/orderDetails";
$route['vendors/update_vendor'] = "vendor/updateVendor";
$route['vendors/logout'] = "vendor/logout";
$route['field-visit-request'] = 'Field_visit_request';
$route['Field_visit_request/update_request_status'] = 'Field_visit_request/update_request_status';
$route['merchants/commission'] = 'Merchants/commission';
$route['community/add_comment'] = 'Community/add_comment';

// Razorpay Webhook API Routes
// ===========================================================================
$route["payment-testing"] = "Payment/index";
$route["create-payment-order"] = "Payment/createOrder";
$route["paymentInformation"] = "Payment/savePaymentInformation";

// Plantix Subscription Plans Routes
// ===========================================================================
$route["plantix-subscription-plans"] = "plantix_subscription_controller/index";
$route["edit-subscription-plan"] = "plantix_subscription_controller/edit_existing_plan";

// Ledger PDF Template Viewing API Routes
// ============================================================================
$route["ledger-pdf-template"] = "farm_management/show_ledger_pdf_template";

// User Khata Management Routes
// ============================================================================
$route["khata-management/users-khata-list"] = "khata_management/users_khata_list_view";
$route["khata-management/user-khata-details/(:any)"] = "khata_management/user_khata_details_view/$1";
$route["get-list-of-crop-sales/(:any)"] = "khata_management/get_list_of_crop_sales/$1";
$route["get-list-of-other-incomes/(:any)"] = "khata_management/get_list_of_other_incomes/$1";
$route["get-list-of-product-expenses/(:any)"] = "khata_management/get_list_of_product_expenses/$1";
$route["get-list-of-farming-expenses/(:any)"] = "khata_management/get_list_of_farming_expenses/$1";
$route["get-list-of-other-expenses/(:any)"] = "khata_management/get_list_of_other_expenses/$1";
$route["get-filtered-khata-details/(:any)"] = "khata_management/get_filtered_khata_details/$1";
$route["download-user-khata-details/(:any)"] = "khata_management/get_filtered_khata_details_PDF/$1";