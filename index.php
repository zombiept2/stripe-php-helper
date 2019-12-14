<?php
require_once('config.php');
require_once('functions.php');
require_once('lib/stripe/stripe-helper.php');
//variables
$pageName = "Stripe Helper";
$stripe_status = 'test';
$stripe_test_secret_api_key = '';
$stripe_test_publishable_api_key = '';
$stripe_live_secret_api_key = '';
$stripe_live_publishable_api_key = '';
$stripe_api_key = '';
$stripe_public_api_key = '';
if (defined('STRIPE_STATUS'))
{
    $stripe_status = STRIPE_STATUS;
}
if (defined('STRIPE_TEST_SECRET_API_KEY'))
{
    $stripe_test_secret_api_key = STRIPE_TEST_SECRET_API_KEY;
}
if (defined('STRIPE_TEST_PUBLISHABLE_API_KEY'))
{
    $stripe_test_publishable_api_key = STRIPE_TEST_PUBLISHABLE_API_KEY;
}
if (defined('STRIPE_LIVE_SECRET_API_KEY'))
{
    $stripe_live_secret_api_key = STRIPE_LIVE_SECRET_API_KEY;
}
if (defined('STRIPE_LIVE_PUBLISHABLE_API_KEY'))
{
    $stripe_live_publishable_api_key = STRIPE_LIVE_PUBLISHABLE_API_KEY;
}
if ($stripe_status == 'live')
{
    $stripe_api_key = $stripe_live_secret_api_key;
    $stripe_public_api_key = $stripe_live_publishable_api_key;
}
else
{
    $stripe_api_key = $stripe_test_secret_api_key;
    $stripe_public_api_key = $stripe_test_publishable_api_key;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <title><?php echo $pageName; ?></title>
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="lib/sweetalert/sweetalert.css">
        <script src="lib/sweetalert/sweetalert.min.js"></script>
        <script src="lib/validate/jquery.validate.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            html, body {
                height: 100%;
                width: 100%;
            }
            body {
                padding-top: 50px;
            }
            .main-content {
                padding: 20px 15px;
                border-left: solid 1px #ccc;
                border-right: solid 1px #ccc;
                height: 100%;
            }
            .content_block {
                display: none;
            }
            .content_block .row {
                
            }
            .submit_column {
                
            }
            .panel-footer {
                text-align: center;
            }
            .submit_btn {
                text-align: center;
                text-transform: uppercase;
            }
            .output {
                display: none;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?php echo $pageName; ?></a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <?php /*
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                    */ ?>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container main-content">
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('charge_content');" type="button" class="btn btn-primary btn-lg btn-block">Charge</button>
                    <br />
                    <div id="charge_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="charge_form" class="form-horizontal" action="javascript:Process('charge');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="charge_cc" class="col-sm-2 control-label">Card Number</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="charge_cc" placeholder="Card Number" data-stripe="number">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="charge_exp_month" class="col-sm-2 control-label">Expiration Month</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="charge_exp_month" placeholder="MM" size="2" maxlength="2"  data-stripe="exp-month">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="charge_exp_year" class="col-sm-2 control-label">Expiration Year</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="charge_exp_year" placeholder="YYYY" size="4" maxlength="4"  data-stripe="exp-year">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="charge_cvc" class="col-sm-2 control-label">CVC</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="charge_cvc" placeholder="CVC" size="4" maxlength="4"  data-stripe="cvc">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="charge_amount" class="col-sm-2 control-label">Amount</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="charge_amount" placeholder="Amount in dollars (i.e. 10.00)">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="charge_description" class="col-sm-2 control-label">Description</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="charge_description" placeholder="Description of charge">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_charge" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $amount - dollar amount with decimal (i.e. 10.00)
// $token - token from stripe.js
// $description - description of charge
$charge = $sh->Charge($amount, $token, $description);
print_r($charge);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('charge_customer_content');" type="button" class="btn btn-primary btn-lg btn-block">Charge Customer</button>
                    <br />
                    <div id="charge_customer_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="charge_customer_form" class="form-horizontal" action="javascript:Process('charge_customer');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="charge_customer_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="charge_customer_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="charge_customer_amount" class="col-sm-2 control-label">Amount</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="charge_customer_amount" placeholder="Amount in dollars (i.e. 10.00)">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="charge_customer_description" class="col-sm-2 control-label">Description</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="charge_customer_description" placeholder="Description of charge">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_charge_customer" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - ID of customer in Stripe
// $amount - dollar amount with decimal (i.e. 10.00)
// $description - description of charge
$charge = $sh->ChargeCustomer($customer_id, $amount, $description);
print_r($charge);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_charges_content');" type="button" class="btn btn-primary btn-lg btn-block">List Charges</button>
                    <br />
                    <div id="list_charges_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_charges_form" class="form-horizontal" action="javascript:Process('list_charges');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_charges_limit" class="col-sm-2 control-label">Limit</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="list_charges_limit" placeholder="Limit (from 1 to 100)">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="list_charges_customer_id" class="col-sm-2 control-label">Customer</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="list_charges_customer_id" placeholder="Customer ID (ex: cus_Boey6SXoqlmba8)">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="list_charges_start_date" class="col-sm-2 control-label">Start Date</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="list_charges_start_date" placeholder="Start Date (ex: mm/dd/yyyy)">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="list_charges_end_date" class="col-sm-2 control-label">End Date</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="list_charges_end_date" placeholder="End Date (ex: mm/dd/yyyy)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_charges" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $limit - integer value (from 1 to 100)
// $customer_id - customer id
// $start_date - min transaction date
// $end_date - max transaction date
$charges = $sh->ListCharges($limit, $customer_id, $start_date, $end_date);
print_r($charges);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('balance_content');" type="button" class="btn btn-primary btn-lg btn-block">Balance</button>
                    <br />
                    <div id="balance_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="balance_form" class="form-horizontal" action="javascript:Process('balance');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            N/A
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_balance" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
$balance = $sh->ReturnBalance();
print_r($balance);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('get_plan_content');" type="button" class="btn btn-primary btn-lg btn-block">Get Plan</button>
                    <br />
                    <div id="get_plan_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="get_plan_form" class="form-horizontal" action="javascript:Process('get_plan');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="get_plan_id" class="col-sm-2 control-label">Plan ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_plan_id" placeholder="Plan ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_get_plan" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $plan_id 
$plan = $sh->GetPlan($plan_id);
print_r($plan);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_plans_content');" type="button" class="btn btn-primary btn-lg btn-block">List Plans</button>
                    <br />
                    <div id="list_plans_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_plans_form" class="form-horizontal" action="javascript:Process('list_plans');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_plans_limit" class="col-sm-2 control-label">Limit</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="list_plans_limit" placeholder="Limit (from 1 to 100)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_plans" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $limit - integer value (1-100) 
$plans = $sh->ListPlan($limit);
print_r($plans);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('get_coupon_content');" type="button" class="btn btn-primary btn-lg btn-block">Get Coupon</button>
                    <br />
                    <div id="get_coupon_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="get_coupon_form" class="form-horizontal" action="javascript:Process('get_coupon');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="get_coupon_id" class="col-sm-2 control-label">Coupon ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_coupon_id" placeholder="Coupon ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_get_coupon" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $coupon_id 
$coupon = $sh->GetCoupon($coupon_id);
print_r($coupon);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_coupons_content');" type="button" class="btn btn-primary btn-lg btn-block">List Coupons</button>
                    <br />
                    <div id="list_coupons_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_coupons_form" class="form-horizontal" action="javascript:Process('list_coupons');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_coupons_limit" class="col-sm-2 control-label">Limit</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="list_coupons_limit" placeholder="Limit (from 1 to 100)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_coupons" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $limit - integer value (1-100) 
$coupons = $sh->ListCoupons($limit);
print_r($coupons);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('get_dispute_content');" type="button" class="btn btn-primary btn-lg btn-block">Get Dispute</button>
                    <br />
                    <div id="get_dispute_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="get_dispute_form" class="form-horizontal" action="javascript:Process('get_dispute');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="get_dispute_id" class="col-sm-2 control-label">Dispute ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_dispute_id" placeholder="Dispute ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_get_dispute" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $coupon_id 
$dispute = $sh->GetDispute($dispute_id);
print_r($dispute);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_disputes_content');" type="button" class="btn btn-primary btn-lg btn-block">List Disputes</button>
                    <br />
                    <div id="list_disputes_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_disputes_form" class="form-horizontal" action="javascript:Process('list_disputes');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_disputes_limit" class="col-sm-2 control-label">Limit</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="list_disputes_limit" placeholder="Limit (from 1 to 100)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_disputes" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $limit - integer value (1-100) 
$disputes = $sh->ListDisputes($limit);
print_r($disputes);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('create_customer_content');" type="button" class="btn btn-primary btn-lg btn-block">Create Customer</button>
                    <br />
                    <div id="create_customer_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="create_customer_form" class="form-horizontal" action="javascript:Process('create_customer');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="create_customer_description" class="col-sm-2 control-label">Description</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_customer_description" placeholder="Description">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_customer_email" class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_customer_email" placeholder="Email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_create_customer" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $description - text to describe user
// $email - customer email address
// $meta_data - set of key/value pairs that you can attach to a customer object
$customer = $sh->CreateCustomer($description, $email, $meta_data);
print_r($customer);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('create_customer_with_card_content');" type="button" class="btn btn-primary btn-lg btn-block">Create Customer with Card</button>
                    <br />
                    <div id="create_customer_with_card_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="create_customer_with_card_form" class="form-horizontal" action="javascript:Process('create_customer_with_card');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="create_customer_with_card_description" class="col-sm-2 control-label">Description</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_customer_with_card_description" placeholder="Description">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_customer_with_card_coupon" class="col-sm-2 control-label">Coupon</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_customer_with_card_coupon" placeholder="Coupon">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_customer_with_card_email" class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_customer_with_card_email" placeholder="Email">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_customer_with_card_cc" class="col-sm-2 control-label">Card Number</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_customer_with_card_cc" placeholder="Card Number" data-stripe="number">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_customer_with_card_exp_month" class="col-sm-2 control-label">Expiration Month</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="create_customer_with_card_exp_month" placeholder="MM" size="2" maxlength="2"  data-stripe="exp-month">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_customer_with_card_exp_year" class="col-sm-2 control-label">Expiration Year</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="create_customer_with_card_exp_year" placeholder="YYYY" size="4" maxlength="4"  data-stripe="exp-year">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_customer_with_card_cvc" class="col-sm-2 control-label">CVC</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="create_customer_with_card_cvc" placeholder="CVC" size="4" maxlength="4"  data-stripe="cvc">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_create_customer_with_card" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $description - text to describe user
// $token - from stripe.js
// $coupon - coupon name to give ongoing discount to customer on recurring charges
// $email - customer email address
// $meta_data - set of key/value pairs that you can attach to a customer object
$customer = $sh->CreateCustomerWithCard($description, $token, $coupon, $email, $meta_data);
print_r($customer);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('get_customer_content');" type="button" class="btn btn-primary btn-lg btn-block">Get Customer</button>
                    <br />
                    <div id="get_customer_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="get_customer_form" class="form-horizontal" action="javascript:Process('get_customer');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="get_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_get_customer" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID
$customer = $sh->GetCustomer($customer_id);
print_r($customer);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('update_customer_content');" type="button" class="btn btn-primary btn-lg btn-block">Update Customer</button>
                    <br />
                    <div id="update_customer_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="update_customer_form" class="form-horizontal" action="javascript:Process('update_customer');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="update_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_customer_description" class="col-sm-2 control-label">Description</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_customer_description" placeholder="Description">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_customer_coupon" class="col-sm-2 control-label">Coupon</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_customer_coupon" placeholder="Coupon">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_customer_email" class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_customer_email" placeholder="Email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_update_customer" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID
// $description - text to describe user
// $coupon - coupon name to give ongoing discount to customer on recurring charges
// $email - customer email address
// $meta_data - set of key/value pairs that you can attach to a customer object
$customer = $sh->UpdateCustomer($customer_id, $description, $coupon, $email, $meta_data);
print_r($customer);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('delete_customer_content');" type="button" class="btn btn-primary btn-lg btn-block">Delete Customer</button>
                    <br />
                    <div id="delete_customer_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="delete_customer_form" class="form-horizontal" action="javascript:Process('delete_customer');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="delete_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="delete_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_delete_customer" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID
$customer = $sh->DeleteCustomer($customer_id);
print_r($customer);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_customers_content');" type="button" class="btn btn-primary btn-lg btn-block">List Customers</button>
                    <br />
                    <div id="list_customers_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_customers_form" class="form-horizontal" action="javascript:Process('list_customers');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_customers_limit" class="col-sm-2 control-label">Limit</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="list_customers_limit" placeholder="Limit (from 1 to 100)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_customers" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $limit - integer value (1-100) 
$customers = $sh->ListCustomers($limit);
print_r($customers);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_cards_content');" type="button" class="btn btn-primary btn-lg btn-block">List Cards</button>
                    <br />
                    <div id="list_cards_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_cards_form" class="form-horizontal" action="javascript:Process('list_cards');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_cards_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="list_cards_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_cards" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID to list cards
$cards = $sh->ListCards($customer_id);
print_r($cards);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('add_card_content');" type="button" class="btn btn-primary btn-lg btn-block">Add Card</button>
                    <br />
                    <div id="add_card_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="add_card_form" class="form-horizontal" action="javascript:Process('add_card');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="add_card_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="add_card_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="add_card_cc" class="col-sm-2 control-label">Card Number</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="add_card_cc" placeholder="Card Number" data-stripe="number">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="add_card_exp_month" class="col-sm-2 control-label">Expiration Month</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="add_card_exp_month" placeholder="MM" size="2" maxlength="2"  data-stripe="exp-month">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="add_card_exp_year" class="col-sm-2 control-label">Expiration Year</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="add_card_exp_year" placeholder="YYYY" size="4" maxlength="4"  data-stripe="exp-year">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="add_card_cvc" class="col-sm-2 control-label">CVC</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="add_card_cvc" placeholder="CVC" size="4" maxlength="4"  data-stripe="cvc">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_add_card" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID to list cards
// $token - token from stripe.js
$card = $sh->AddCard($customer_id, $token);
print_r($card);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('get_card_content');" type="button" class="btn btn-primary btn-lg btn-block">Get Card</button>
                    <br />
                    <div id="get_card_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="get_card_form" class="form-horizontal" action="javascript:Process('get_card');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="get_card_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_card_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="get_card_card_id" class="col-sm-2 control-label">Card ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_card_card_id" placeholder="Card ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_get_card" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID
// $card_id - card ID
$card = $sh->GetCard($customer_id, $card_id);
print_r($card);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('update_card_content');" type="button" class="btn btn-primary btn-lg btn-block">Update Card</button>
                    <br />
                    <div id="update_card_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="update_card_form" class="form-horizontal" action="javascript:Process('update_card');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="update_card_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_card_id" class="col-sm-2 control-label">Card ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_card_id" placeholder="Card ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_address_city" class="col-sm-2 control-label">City</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_address_city" placeholder="City">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_address_country" class="col-sm-2 control-label">Country</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_address_country" placeholder="Country">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_address_line1" class="col-sm-2 control-label">Address Line 1</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_address_line1" placeholder="Address Line 1">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_address_line2" class="col-sm-2 control-label">Address Line 2</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_address_line2" placeholder="Address Line 2">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_address_state" class="col-sm-2 control-label">State</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_address_state" placeholder="State">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_address_zip" class="col-sm-2 control-label">Zip</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_address_zip" placeholder="Zip">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_exp_month" class="col-sm-2 control-label">Expiration Month</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_exp_month" placeholder="Expiration Month">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_exp_year" class="col-sm-2 control-label">Expiration Year</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_exp_year" placeholder="Expiration Year">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_card_name" class="col-sm-2 control-label">Cardholder Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_card_name" placeholder="Cardholder Name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_update_card" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID
// $card_id - card ID
// $address_city - city
// $address_country - country
// $address_line1 - address 1
// $address_line2 - address 2
// $address_state - state
// $address_zip - zip
// $exp_month - month
// $exp_year - year
// $name - card holder name
// $meta_data - key/value pairs
$card = $sh->UpdateCard($customer_id, $card_id, $address_city, $address_country, $address_line1, $address_line2, $address_state, $address_zip, $exp_month, $exp_year, $name, $meta_data);
print_r($card);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('remove_card_content');" type="button" class="btn btn-primary btn-lg btn-block">Remove Card</button>
                    <br />
                    <div id="remove_card_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="remove_card_form" class="form-horizontal" action="javascript:Process('remove_card');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="remove_card_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="remove_card_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="remove_card_card_id" class="col-sm-2 control-label">Card ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="remove_card_card_id" placeholder="Card ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_remove_card" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID
// $card_id - card ID
$card = $sh->RemoveCard($customer_id, $card_id);
print_r($card);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('create_subscription_content');" type="button" class="btn btn-primary btn-lg btn-block">Create Subscription</button>
                    <br />
                    <div id="create_subscription_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="create_subscription_form" class="form-horizontal" action="javascript:Process('create_subscription');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="create_subscription_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_subscription_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_subscription_coupon" class="col-sm-2 control-label">Coupon</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_subscription_coupon" placeholder="Coupon">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_subscription_plan_id" class="col-sm-2 control-label">Plan ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_subscription_plan_id" placeholder="Plan ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_create_subscription" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $customer_id - customer ID to associate plan with subscription
// $coupon - coupon name to give ongoing discount to customer on recurring charges
// $plan_id - subscription plan ID to enroll customer in 
// $meta_data - set of key/value pairs that you can attach to a customer object
$subscription = $sh->CreateSubscription($customer_id, $coupon, $plan_id, $meta_data);
print_r($subscription);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('get_subscription_content');" type="button" class="btn btn-primary btn-lg btn-block">Get Subscription</button>
                    <br />
                    <div id="get_subscription_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="get_subscription_form" class="form-horizontal" action="javascript:Process('get_subscription');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="get_subscription_id" class="col-sm-2 control-label">Subscription ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_subscription_id" placeholder="Subscription ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_get_subscription" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $subscription_id - subscription ID
$subscription = $sh->GetSubscription($customer_id);
print_r($subscription);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('update_subscription_content');" type="button" class="btn btn-primary btn-lg btn-block">Update Subscription</button>
                    <br />
                    <div id="update_subscription_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="update_subscription_form" class="form-horizontal" action="javascript:Process('update_subscription');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="update_subscription_id" class="col-sm-2 control-label">Subscription ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_subscription_id" placeholder="Subscription ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_subscription_plan" class="col-sm-2 control-label">Plan ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_subscription_plan" placeholder="Plan ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_subscription_coupon" class="col-sm-2 control-label">Coupon</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="update_subscription_coupon" placeholder="Coupon">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_update_customer" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $subscription_id - subscription ID
// $plan = plan ID
// $coupon - coupon name to give ongoing discount to customer on recurring charges
// $meta_data - set of key/value pairs that you can attach to a customer object
$subscription = $sh->UpdateSubscription($subscription_id, $plan, $coupon, $prorate, $meta_data);
print_r($subscription);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('cancel_subscription_content');" type="button" class="btn btn-primary btn-lg btn-block">Cancel Subscription</button>
                    <br />
                    <div id="cancel_subscription_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="cancel_subscription_form" class="form-horizontal" action="javascript:Process('cancel_subscription');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="cancel_subscription_id" class="col-sm-2 control-label">Subscription ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="cancel_subscription_id" placeholder="Subscription ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="cancel_subscription_immediate" class="col-sm-2 control-label">Immediate</label>
                                                <div class="col-sm-10">
                                                    <input type="checkbox" id="cancel_subscription_immediate" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_cancel_subscription" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $subscription_id - subscription ID
// $immediate - cancel immediately instead of waiting until the end of the billing cycle
$subscription = $sh->CancelSubscription($subscription_id, $immediate);
print_r($subscription);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_subscriptions_content');" type="button" class="btn btn-primary btn-lg btn-block">List Subscriptions</button>
                    <br />
                    <div id="list_subscriptions_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_subscriptions_form" class="form-horizontal" action="javascript:Process('list_subscriptions');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_subscriptions_limit" class="col-sm-2 control-label">Limit</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="list_subscriptions_limit" placeholder="Limit (from 1 to 100)">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="list_subscriptions_customer_id" class="col-sm-2 control-label">Customer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="list_subscriptions_customer_id" placeholder="Customer ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="list_subscriptions_plan_id" class="col-sm-2 control-label">Plan ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="list_subscriptions_plan_id" placeholder="Plan ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="list_subscriptions_limit" class="col-sm-2 control-label">Status</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control" id="list_subscriptions_status">
                                                        <option value="all">all</option>
                                                        <option value="trialing">trialing</option>
                                                        <option value="active">active</option>
                                                        <option value="past_due">past_due</option>
                                                        <option value="unpaid">unpaid</option>
                                                        <option value="canceled">canceled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_subscriptions" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $limit - integer value (1-100) 
// $customer_id - customer id (optional)
// $plan_id - subscription plan id (optional)
// $status - subscription status 
$subscriptions = $sh->ListSubscriptions($limit, $customer_id, $plan_id, $status);
print_r($subscriptions);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('create_refund_content');" type="button" class="btn btn-primary btn-lg btn-block">Create Refund</button>
                    <br />
                    <div id="create_refund_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="create_refund_form" class="form-horizontal" action="javascript:Process('create_refund');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="create_refund_charge_id" class="col-sm-2 control-label">Charge ID *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_refund_charge_id" placeholder="Charge ID">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_refund_amount" class="col-sm-2 control-label">Amount</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_refund_amount" placeholder="Amount">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="create_refund_reason" class="col-sm-2 control-label">Reason</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="create_refund_reason" placeholder="Reason">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_create_refund" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $charge_id - required - identifier of the charge to refund
// $amount - positive integer in cents representing how much of this charge to refund
// $reason - string indicating the reason for the refund. If set, possible values are duplicate, fraudulent, and requested_by_customer
// $meta_data - set of key/value pairs that you can attach to a refund object
$refund = $sh->CreateRefund($refund_id, $amount, $reason, $meta_data);
print_r($refund);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('get_refund_content');" type="button" class="btn btn-primary btn-lg btn-block">Get Refund</button>
                    <br />
                    <div id="get_refund_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="get_refund_form" class="form-horizontal" action="javascript:Process('get_refund');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="get_refund_id" class="col-sm-2 control-label">Refund ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="get_refund_id" placeholder="Refund ID">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_get_refund" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $refund_id - refund ID
$refund = $sh->GetRefund($refund_id);
print_r($refund);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="OpenContent('list_refunds_content');" type="button" class="btn btn-primary btn-lg btn-block">List Refunds</button>
                    <br />
                    <div id="list_refunds_content" class="content_block">
                        <div class="row">
                            <div class="col-md-6 input">
                                <form id="list_refunds_form" class="form-horizontal" action="javascript:Process('list_refunds');">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Input</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="list_refunds_limit" class="col-sm-2 control-label">Limit</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" id="list_refunds_limit" placeholder="Limit (from 1 to 100)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button id="process_button_list_refunds" type="submit" class="submit_btn btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Code Usage</h3>
                                    </div>
                                    <div class="panel-body">
<pre>
$sh = new StripeHelper();
// $limit - integer value (1-100) 
$refunds = $sh->ListRefunds($limit);
print_r($refunds);</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 output"> 
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Output</h3>
                                    </div>
                                    <div class="panel-body output_content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
        <script type="text/javascript" src="lib/stripe/stripe-tokenizer.js"></script>
        <script>
            var current_content = '';
            $(document).ready(function() {
                $('#charge_form').validate();
                $('#charge_form').stripetokenizer({
                    key: '<?php echo $stripe_public_api_key; ?>',
                    cc_field: 'charge_cc',
                    cc_exp_month_field: 'charge_exp_month',
                    cc_exp_year_field: 'charge_exp_year',
                    cc_cvc_field: 'charge_cvc'
                });
                $('#create_customer_with_card_form').validate();
                $('#create_customer_with_card_form').stripetokenizer({
                    key: '<?php echo $stripe_public_api_key; ?>',
                    cc_field: 'create_customer_cc',
                    cc_exp_month_field: 'create_customer_exp_month',
                    cc_exp_year_field: 'create_customer_exp_year',
                    cc_cvc_field: 'create_customer_cvc'
                });
                $('#add_card_form').validate();
                $('#add_card_form').stripetokenizer({
                    key: '<?php echo $stripe_public_api_key; ?>',
                    cc_field: 'add_card_cc',
                    cc_exp_month_field: 'add_card_exp_month',
                    cc_exp_year_field: 'add_card_exp_year',
                    cc_cvc_field: 'add_card_cvc'
                });
            });
            function OpenContent(id) {
                CloseContents();
                if (id == current_content) {
                    $('#'+id).hide();
                    current_content = '';
                }
                else {
                    $('#'+id).show();
                    current_content = id;
                }
            }
            function CloseContents() {
                $('.content_block').hide();
            }
            function Process(method) {
                var valid = true;
                var errorMessage = '';
                $('#'+method+'_content .output_content').html('');
                switch (method) {
                    case 'list_charges':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    limit: $('#'+method+'_limit').val(),
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    start_date: $('#'+method+'_start_date').val(),
                                    end_date: $('#'+method+'_end_date').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_limit').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'charge':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    amount: $('#'+method+'_amount').val(),
                                    token: $('#'+method+'_form .stripe_token').val(),
                                    description: $('#'+method+'_description').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_amount').val('');
                                        $('#'+method+'_description').val('');
                                        $('#'+method+'_form .stripe_token').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'charge_customer':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    amount: $('#'+method+'_amount').val(),
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    description: $('#'+method+'_description').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_amount').val('');
                                        $('#'+method+'_description').val('');
                                        $('#'+method+'_customer_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'balance':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'get_plan':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    plan_id: $('#'+method+'_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'list_plans':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    limit: $('#'+method+'_limit').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_limit').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'get_coupon':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    coupon_id: $('#'+method+'_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'list_coupons':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    limit: $('#'+method+'_limit').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_limit').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'get_dispute':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    dispute_id: $('#'+method+'_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'list_disputes':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    limit: $('#'+method+'_limit').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_limit').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'create_customer':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    description: $('#'+method+'_description').val(),
                                    email: $('#'+method+'_email').val(),
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_description').val('');
                                        $('#'+method+'_coupon').val('');
                                        $('#'+method+'_email').val('');
                                        $('#'+method+'_form .stripe_token').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'create_customer_with_card':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    description: $('#'+method+'_description').val(),
                                    coupon: $('#'+method+'_coupon').val(),
                                    email: $('#'+method+'_email').val(),
                                    token: $('#'+method+'_form .stripe_token').val(),
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_description').val('');
                                        $('#'+method+'_coupon').val('');
                                        $('#'+method+'_email').val('');
                                        $('#'+method+'_form .stripe_token').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'get_customer':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'delete_customer':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'update_customer':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_id').val(),
                                    description: $('#'+method+'_description').val(),
                                    coupon: $('#'+method+'_coupon').val(),
                                    email: $('#'+method+'_email').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        $('#'+method+'_description').val('');
                                        $('#'+method+'_coupon').val('');
                                        $('#'+method+'_email').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'list_customers':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    limit: $('#'+method+'_limit').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_limit').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'list_cards':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_customer_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_customer_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'add_card':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    token: $('#'+method+'_form .stripe_token').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_customer_id').val('');
                                        $('#'+method+'_form .stripe_token').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'get_card':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    card_id: $('#'+method+'_card_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_customer_id').val('');
                                        $('#'+method+'_card_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'update_card':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    card_id: $('#'+method+'_card_id').val(),
                                    address_city: $('#'+method+'_address_city').val(),
                                    address_country: $('#'+method+'_address_country').val(),
                                    address_line1: $('#'+method+'_address_line1').val(),
                                    address_line2: $('#'+method+'_address_line2').val(),
                                    address_state: $('#'+method+'_address_state').val(),
                                    address_zip: $('#'+method+'_address_zip').val(),
                                    exp_month: $('#'+method+'_exp_month').val(),
                                    exp_year: $('#'+method+'_exp_year').val(),
                                    name: $('#'+method+'_name').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        /*
                                        $('#'+method+'_customer_id').val('');
                                        $('#'+method+'_card_id').val('');
                                        $('#'+method+'_address_city').val('');
                                        $('#'+method+'_address_country').val('');
                                        $('#'+method+'_address_line1').val('');
                                        $('#'+method+'_address_line2').val('');
                                        $('#'+method+'_address_state').val('');
                                        $('#'+method+'_address_zip').val('');
                                        $('#'+method+'_exp_month').val('');
                                        $('#'+method+'_exp_year').val('');
                                        $('#'+method+'_name').val('');
                                        */
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'remove_card':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    card_id: $('#'+method+'_card_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_customer_id').val('');
                                        $('#'+method+'_card_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'create_subscription':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    coupon: $('#'+method+'_coupon').val(),
                                    plan_id: $('#'+method+'_plan_id').val(),
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_customer_id').val('');
                                        $('#'+method+'_coupon').val('');
                                        $('#'+method+'_plan_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'get_subscription':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    subscription_id: $('#'+method+'_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'cancel_subscription':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            var immediate = false;
                            if ($('#'+method+'_immediate').is(":checked")) {
                                immediate = true;
                            }
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    subscription_id: $('#'+method+'_id').val(),
                                    immediate: immediate
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        $('#'+method+'_immediate').prop('checked', false);
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'update_subscription':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    subscription_id: $('#'+method+'_id').val(),
                                    plan: $('#'+method+'_plan').val(),
                                    coupon: $('#'+method+'_coupon').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        $('#'+method+'_plan').val('');
                                        $('#'+method+'_coupon').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'list_subscriptions':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    limit: $('#'+method+'_limit').val(),
                                    customer_id: $('#'+method+'_customer_id').val(),
                                    plan_id: $('#'+method+'_plan_id').val(),
                                    status: $('#'+method+'_status').val(),
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_limit').val('');
                                        $('#'+method+'_customer_id').val('');
                                        $('#'+method+'_plan_id').val('');
                                        $('#'+method+'_status').val('all');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'create_refund':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    charge_id: $('#'+method+'_charge_id').val(),
                                    amount: $('#'+method+'_amount').val(),
                                    reason: $('#'+method+'_reason').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_charge_id').val('');
                                        $('#'+method+'_amount').val('');
                                        $('#'+method+'_reason').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'get_refund':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    refund_id: $('#'+method+'_id').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_id').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                    case 'list_refunds':
                        // validations
                        if (valid) {
                            ActivateButton(method);
                            $.ajax({
                                type: 'POST',
                                url: 'processor.php',
                                dataType: 'json',
                                data: {
                                    method: method,
                                    limit: $('#'+method+'_limit').val()
                                }
                            }).done(function(data) {
                                if (data) {
                                    if (data.status == 200) {
                                        $('#'+method+'_content .output_content').html('<pre>'+JSON.stringify(data.output,null,'  ')+'</pre>');
                                        $('#'+method+'_content .output').show();
                                        $('#'+method+'_limit').val('');
                                        ResetButton(method);
                                    }
                                    else {
                                        $('#'+method+'_content .output_content').html('');
                                        $('#'+method+'_content .output').hide();
                                        ShowMessage(data.message, 'Oops!', 'OK', 'error');
                                    }
                                }
                                else {
                                    ShowMessage("Unable to process", 'Oops!', 'OK', 'error');
                                }
                                ResetButton(method);
                            }).fail(function() {
                                ResetButton(method);
                            });
                        }
                        else {
                            ShowMessage(errorMessage, 'Oops!', 'OK', 'error');
                        }
                        break;
                }
            }
            function ActivateButton(method, text) {
                if (text == '') {
                    text = 'Processing...';
                }
                SetButtonActive('process_button_' + method,text);
            }
            function ResetButton(method, text) {
                if (text == '') {
                    text = 'Submit';
                }
                SetButtonInactive('process_button_' + method,text);
            }
            function ShowConfirm(message, title, buttonText, type, callback) {
                swal({
                    title: title,
                    text: message,
                    type: type,
                    confirmButtonText: buttonText,
                    html: true,
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    closeOnConfirm: false
                }, function() {
                    callback();
                });
            }
            function ShowSimpleMessage(message, title) {
                swal({
                    title: title,
                    text: message,
                    type: 'warning',
                    confirmButtonText: 'OK',
                    html: true
                });
            }
            function ShowMessage(message, title, buttonText, type) {
                swal({
                    title: title,
                    text: message,
                    type: type,
                    confirmButtonText: buttonText,
                    html: true
                });
            }
            function ShowMessageCallback(message, title, buttonText, type, callback) {
                swal({
                    title: title,
                    text: message,
                    type: type,
                    confirmButtonText: buttonText,
                    html: true
                }, function() {
                    callback();
                });
            }
            function SelectAllText(id) {
                var textbox = document.getElementById(id);
                textbox.focus();
                textbox.select();
            }
            function ScrollTo(id, speed) {
                speed = speed || 500;
                $('html, body').animate({
                    scrollTop: $('#'+id).offset().top
                }, speed);
            }
            function SetButtonActive(id, buttonText) {
                $('#'+id).addClass('disabled');
                $('#'+id).val(buttonText);
                $('#'+id).text(buttonText);
            }
            function SetButtonInactive(id, buttonText) {
                $('#'+id).val(buttonText);
                $('#'+id).text(buttonText);
                $('#'+id).removeClass('disabled');
            }
            function SelectField(id) {
                var textbox = document.getElementById(id);
                textbox.focus();
            }
        </script>
    </body>
</html>