<?php
//errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
//stripe
define("STRIPE_STATUS", "test"); //test or live
define("STRIPE_TEST_SECRET_API_KEY", "sk_test_KEY");
define("STRIPE_TEST_PUBLISHABLE_API_KEY", "pk_test_KEY");
define("STRIPE_LIVE_SECRET_API_KEY", "sk_live_KEY");
define("STRIPE_LIVE_PUBLISHABLE_API_KEY", "pk_live_KEY");