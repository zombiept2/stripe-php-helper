<?php
require_once('config.php');
require_once('functions.php');
require_once('lib/stripe/stripe-processor.php');
$data = new stdClass();
foreach ($_REQUEST as $key => $value)
{
    $data->$key = $value;
}
$sp = new StripeProcessor($data);
echo $sp->Process();