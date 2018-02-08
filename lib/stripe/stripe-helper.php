<?php
require_once('init.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/functions.php');
class StripeHelper
{
    var $stripe_status;
    var $stripe_api_key;
    var $stripe_public_api_key;
	var $stripe_webhook_signing_secret;
    var $stripe_test_secret_api_key;
    var $stripe_test_publishable_api_key;
    var $stripe_live_secret_api_key;
    var $stripe_live_publishable_api_key;
	var $stripe_test_webhook_signing_secret;
    var $stripe_live_webhook_signing_secret;
	public function __construct() 
	{
        if (defined('STRIPE_STATUS'))
		{
			$this->stripe_status = STRIPE_STATUS;
		}
        if (defined('STRIPE_TEST_SECRET_API_KEY'))
		{
			$this->stripe_test_secret_api_key = STRIPE_TEST_SECRET_API_KEY;
		}
        if (defined('STRIPE_TEST_PUBLISHABLE_API_KEY'))
		{
			$this->stripe_test_publishable_api_key = STRIPE_TEST_PUBLISHABLE_API_KEY;
		}
        if (defined('STRIPE_LIVE_SECRET_API_KEY'))
		{
			$this->stripe_live_secret_api_key = STRIPE_LIVE_SECRET_API_KEY;
		}
        if (defined('STRIPE_LIVE_PUBLISHABLE_API_KEY'))
		{
			$this->stripe_live_publishable_api_key = STRIPE_LIVE_PUBLISHABLE_API_KEY;
		}
		if (defined('STRIPE_TEST_WEBHOOK_SIGNING_SECRET'))
		{
			$this->stripe_test_webhook_signing_secret = STRIPE_TEST_WEBHOOK_SIGNING_SECRET;
		}
		if (defined('STRIPE_LIVE_WEBHOOK_SIGNING_SECRET'))
		{
			$this->stripe_live_webhook_signing_secret = STRIPE_LIVE_WEBHOOK_SIGNING_SECRET;
		}
        if ($this->stripe_status == 'live')
        {
            $this->stripe_api_key = $this->stripe_live_secret_api_key;
            $this->stripe_public_api_key = $this->stripe_live_publishable_api_key;
			$this->stripe_webhook_signing_secret = $this->stripe_live_webhook_signing_secret;
        }
        else
        {
            $this->stripe_api_key = $this->stripe_test_secret_api_key;
            $this->stripe_public_api_key = $this->stripe_test_publishable_api_key;
			$this->stripe_webhook_signing_secret = $this->stripe_test_webhook_signing_secret;
        }
        Stripe\Stripe::setApiKey($this->stripe_api_key);
    }
	//webhooks
	function ProcessWebhook()
	{
		$output = false;
		$status = 200;
		$event = '';
		$message = '';
		$error = '';
		$payload = @file_get_contents("php://input");
		$sig_header = '';
		if (isset($_SERVER["HTTP_STRIPE_SIGNATURE"])) 
		{
			$sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
		}
		$response = null;
		try 
		{
			$response = \Stripe\Webhook::constructEvent($payload, $sig_header, $this->stripe_webhook_signing_secret);
			$event = $response->type;
			$message = 'Event received';
		} 
		catch (\UnexpectedValueException $e) 
		{
			// Invalid payload
			$status = 400;
			$error = 'Invalid payload';
		} 
		catch (\Stripe\Error\SignatureVerification $e) 
		{
			// Invalid signature
			$status = 400;
			$error = 'Invalid signature';
		}
		$output = array(
			'event' => $event,
			'status' => $status,
			'message' => $message,
			'error' => $error,
			'response' => $response
		);
		return $output;
	}
	//charges
    public function Charge($amount, $token, $description)
    {
		$response = false;
		$error = '';
		
		if ($amount != '')
		{
			if ($token == '')
			{
				$token = "tok_visa";
			}
			try
			{
				$response = Stripe\Charge::create(array(
				  "amount" => ReturnPennies($amount),
				  "currency" => "usd",
				  "source" => $token, // obtained with Stripe.js
				  "description" => $description
				));
			}
			catch(Stripe_CardError $e) 
			{
				$error = $e->getMessage();
			} 
			catch (Stripe_InvalidRequestError $e) 
			{
				// Invalid parameters were supplied to Stripe's API
				$error = $e->getMessage();
			} 
			catch (Stripe_AuthenticationError $e) 
			{
				// Authentication with Stripe's API failed
				$error = $e->getMessage();
			} 
			catch (Stripe_ApiConnectionError $e) 
			{
				// Network communication with Stripe failed
				$error = $e->getMessage();
			} 
			catch (Stripe_Error $e) 
			{
				// Display a very generic error to the user, and maybe send
				// yourself an email
				$error = $e->getMessage();
			} 
			catch (Exception $e) 
			{
				// Something else happened, completely unrelated to Stripe
				$error = $e->getMessage();
			}
			if ($error != '')
			{
				$response['error'] = $error;
			}
		}
		return $response;
    }
	public function ListCharges($limit, $customer_id)
    {
		$response = false;
		$error = '';
		$data = [];
		if ($limit == '')
		{
			$limit = 10;
		}
		$data['limit'] = $limit;
		if ($customer_id != '')
		{
			$data['customer'] = $customer_id;
		}
		try
		{
			$response = Stripe\Charge::all($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
    }
	//balance
	public function ReturnBalance()
	{
		$response = false;
		$error = '';
		try
		{
			$response = Stripe\Balance::retrieve();
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	//plans
	public function GetPlan($plan_id)
	{
		$response = false;
		$error = '';
		try
		{
			$response = Stripe\Plan::retrieve($plan_id);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function ListPlans($limit)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($limit == '')
		{
			$limit = 10;
		}
		$data['limit'] = $limit;
		try
		{
			$response = Stripe\Plan::all($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	//coupons
	public function GetCoupon($coupon_id)
	{
		$response = false;
		$error = '';
		try
		{
			$response = Stripe\Coupon::retrieve($coupon_id);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function ListCoupons($limit)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($limit == '')
		{
			$limit = 10;
		}
		$data['limit'] = $limit;
		try
		{
			$response = Stripe\Coupon::all($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	//disputes
	public function GetDispute($dispute_id)
	{
		$response = false;
		$error = '';
		try
		{
			$response = Stripe\Dispute::retrieve($dispute_id);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function ListDisputes($limit)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($limit == '')
		{
			$limit = 10;
		}
		$data['limit'] = $limit;
		try
		{
			$response = Stripe\Dispute::all($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	//customers
	public function CreateCustomer($description, $token, $coupon, $email, $meta_data)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($token == '')
		{
			$token = 'tok_amex';
		}
		$data['source'] = $token;
		if ($description != '')
		{
			$data['description'] = $description;
		}
		if ($coupon != '')
		{
			$data['coupon'] = $coupon;
		}
		if ($email != '')
		{
			$data['email'] = $email;
		}
		if ($meta_data != '')
		{
			$data['metadata'] = $meta_data;
		}
		try
		{
			$response = Stripe\Customer::create($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function GetCustomer($customer_id)
	{
		$response = false;
		$error = '';
		try
		{
			$response = Stripe\Customer::retrieve($customer_id);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function UpdateCustomer($customer_id, $description, $coupon, $email, $meta_data)
	{
		$response = false;
		$error = '';
		try
		{
			$customer = Stripe\Customer::retrieve($customer_id);
			if ($description != '' && $description != 'null')
			{
				$customer->description = $description;
			}
			else if ($description == 'null')
			{
				$customer->description = null;
			}
			if ($coupon != '' && $coupon != 'null')
			{
				$customer->coupon = $coupon;
			}
			else if ($coupon == 'null')
			{
				$customer->coupon = null;
			}
			if ($email != '' && $email != 'null')
			{
				$customer->email = $email;
			}
			else if ($email == 'null')
			{
				$customer->email = null;
			}
			if ($meta_data != '' && $meta_data != 'null')
			{
				$customer->metadata = $meta_data;
			}
			else if ($meta_data == 'null')
			{
				$customer->metadata = null;
			}
			$response = $customer->save();
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function DeleteCustomer($customer_id)
	{
		$response = false;
		$error = '';
		try
		{
			$customer = Stripe\Customer::retrieve($customer_id);
			$response = $customer->delete();
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function ListCustomers($limit)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($limit == '')
		{
			$limit = 10;
		}
		$data['limit'] = $limit;
		try
		{
			$response = Stripe\Customer::all($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	//subscriptions
	public function CreateSubscription($customer_id, $coupon, $plan_id, $meta_data)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($customer_id != '')
		{
			$data['customer'] = $customer_id;
		}
		if ($coupon != '')
		{
			$data['coupon'] = $coupon;
		}
		if ($plan_id != '')
		{
			$data['items'] = array(
				array(
					'plan' => $plan_id
				),
			);
		}
		if ($meta_data != '')
		{
			$data['metadata'] = $meta_data;
		}
		try
		{
			$response = Stripe\Subscription::create($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function GetSubscription($subscription_id)
	{
		$response = false;
		$error = '';
		try
		{
			$response = Stripe\Subscription::retrieve($subscription_id);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function UpdateSubscription($subscription_id, $plan, $coupon, $meta_data)
	{
		$response = false;
		$error = '';
		try
		{
			$subscription = Stripe\Subscription::retrieve($subscription_id);
			if ($plan != '' && $plan != 'null')
			{
				$subscription->plan = $plan;
			}
			else if ($plan == 'null')
			{
				$subscription->plan = null;
			}
			if ($coupon != '' && $coupon != 'null')
			{
				$subscription->coupon = $coupon;
			}
			else if ($coupon == 'null')
			{
				$subscription->coupon = null;
			}
			if ($meta_data != '' && $meta_data != 'null')
			{
				$subscription->metadata = $meta_data;
			}
			else if ($meta_data == 'null')
			{
				$subscription->metadata = null;
			}
			$response = $subscription->save();
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function CancelSubscription($subscription_id, $immediate)
	{
		$response = false;
		$error = '';
		try
		{
			$subscription = Stripe\Subscription::retrieve($subscription_id);
			if ($immediate)
			{
				$response = $subscription->cancel();
			}
			else
			{
				$response = $subscription->cancel(['at_period_end' => true]);
			}
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function ListSubscriptions($limit, $customer_id, $plan_id, $status)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($limit == '')
		{
			$limit = 10;
		}
		$data['limit'] = $limit;
		if ($customer_id != '')
		{
			$data['customer'] = $customer_id;
		}
		if ($plan_id != '')
		{
			$data['plan'] = $plan_id;
		}
		if ($status != '')
		{
			$data['status'] = $status; //trialing, active, past_due, unpaid, canceled, or all
		}
		try
		{
			$response = Stripe\Subscription::all($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	//refunds
	public function CreateRefund($charge_id, $amount, $reason, $meta_data)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($charge_id != '')
		{
			$data['charge'] = $charge_id;
		}
		if ($amount != '')
		{
			$data['amount'] = $amount;
		}
		if ($reason != '')
		{
			$data['reason'] = $reason;
		}
		if ($meta_data != '')
		{
			$data['metadata'] = $meta_data;
		}
		try
		{
			$response = Stripe\Refund::create($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function GetRefund($refund_id)
	{
		$response = false;
		$error = '';
		try
		{
			$response = Stripe\Refund::retrieve($refund_id);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
	public function ListRefunds($limit)
	{
		$response = false;
		$error = '';
		$data = [];
		if ($limit == '')
		{
			$limit = 10;
		}
		$data['limit'] = $limit;
		try
		{
			$response = Stripe\Refund::all($data);
		}
		catch(Stripe_CardError $e) 
		{
			$error = $e->getMessage();
		} 
		catch (Stripe_InvalidRequestError $e) 
		{
			// Invalid parameters were supplied to Stripe's API
			$error = $e->getMessage();
		} 
		catch (Stripe_AuthenticationError $e) 
		{
			// Authentication with Stripe's API failed
			$error = $e->getMessage();
		} 
		catch (Stripe_ApiConnectionError $e) 
		{
			// Network communication with Stripe failed
			$error = $e->getMessage();
		} 
		catch (Stripe_Error $e) 
		{
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$error = $e->getMessage();
		} 
		catch (Exception $e) 
		{
			// Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
		}
		if ($error != '')
		{
			$response['error'] = $error;
		}
		return $response;
	}
}