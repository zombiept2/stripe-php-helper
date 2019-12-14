<?php
require_once('functions.php');
require_once('lib/stripe/stripe-helper.php');
class StripeProcessor
{
    var $data;
	var $method;
    var $sh;
    var $status;
    var $message;
    var $output;
	public function __construct($data = '') 
	{
		$data = json_decode(json_encode($data), FALSE);
        if ($data != "")
		{
			$this->data = $data;
		}
        if (property_exists($data, 'method'))
		{
			$this->method = $data->method;
		}
        $this->sh = new StripeHelper();
	}
    public function Process()
    {
        if ($this->method != "")
        {
            switch ($this->method)
            {
                //charges
                case 'list_charges':
                    $limit = '';
                    if (property_exists($this->data, 'limit'))
                    {
                        $limit = $this->data->limit;
                    }
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$start_date = '';
					if (property_exists($this->data, 'start_date'))
					{
						$start_date = $this->data->start_date;
					}
					$end_date = '';
					if (property_exists($this->data, 'end_date'))
					{
						$end_date = $this->data->end_date;
					}
                    $charges = $this->sh->ListCharges($limit, $customer_id, $start_date, $end_date);
                    $this->status = 200;
                    $this->message = 'Charges found';
                    $this->output = $charges;
                    break;
                case 'charge':
                    $amount = '';
                    if (property_exists($this->data, 'amount'))
                    {
                        $amount = $this->data->amount;
                    }
                    $token = '';
                    if (property_exists($this->data, 'token'))
                    {
                        $token = $this->data->token;
                    }
                    $description = '';
                    if (property_exists($this->data, 'description'))
                    {
                        $description = $this->data->description;
                    }
                    $charge = $this->sh->Charge($amount, $token, $description);
                    $this->status = 200;
                    if ($charge)
                    {
                        $this->message = 'Charge processed';
                    }
                    else
                    {
                        $this->message = 'Charge failed';
                    }
                    $this->output = $charge;
                    break;
                case 'charge_customer':
                    $amount = '';
                    if (property_exists($this->data, 'amount'))
                    {
                        $amount = $this->data->amount;
                    }
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
                    $description = '';
                    if (property_exists($this->data, 'description'))
                    {
                        $description = $this->data->description;
                    }
                    $charge = $this->sh->ChargeCustomer($customer_id, $amount, $description);
                    $this->status = 200;
                    if ($charge)
                    {
                        $this->message = 'Charge processed';
                    }
                    else
                    {
                        $this->message = 'Charge failed';
                    }
                    $this->output = $charge;
                    break;
                //balance
                case 'balance':
                    $balance = $this->sh->ReturnBalance();
                    $this->status = 200;
                    $this->message = 'Balance found';
                    $this->output = $balance;
                    break;
                //plans
                case 'get_plan':
                    $plan_id = '';
                    if (property_exists($this->data, 'plan_id'))
                    {
                        $plan_id = $this->data->plan_id;
                    }
                    $plan = $this->sh->GetPlan($plan_id);
                    $this->status = 200;
                    $this->message = 'Plan found';
                    $this->output = $plan;
                    break; 
                case 'list_plans':
                    $limit = '';
                    if (property_exists($this->data, 'limit'))
                    {
                        $limit = $this->data->limit;
                    }
                    $plans = $this->sh->ListPlans($limit);
                    $this->status = 200;
                    $this->message = 'Plans found';
                    $this->output = $plans;
                    break;  
                //coupons
                case 'get_coupon':
                    $coupon_id = '';
                    if (property_exists($this->data, 'coupon_id'))
                    {
                        $coupon_id = $this->data->coupon_id;
                    }
                    $coupon = $this->sh->GetCoupon($coupon_id);
                    $this->status = 200;
                    $this->message = 'Coupon found';
                    $this->output = $coupon;
                    break; 
                case 'list_coupons':
                    $limit = '';
                    if (property_exists($this->data, 'limit'))
                    {
                        $limit = $this->data->limit;
                    }
                    $coupons = $this->sh->ListCoupons($limit);
                    $this->status = 200;
                    $this->message = 'Coupons found';
                    $this->output = $coupons;
                    break;  
                //disputes
                case 'get_dispute':
                    $dispute_id = '';
                    if (property_exists($this->data, 'dispute_id'))
                    {
                        $dispute_id = $this->data->dispute_id;
                    }
                    $dispute = $this->sh->GetDispute($dispute_id);
                    $this->status = 200;
                    $this->message = 'Dispute found';
                    $this->output = $dispute;
                    break; 
                case 'list_disputes':
                    $limit = '';
                    if (property_exists($this->data, 'limit'))
                    {
                        $limit = $this->data->limit;
                    }
                    $disputes = $this->sh->ListDisputes($limit);
                    $this->status = 200;
                    $this->message = 'Disputes found';
                    $this->output = $disputes;
                    break;  
				//customer
				case 'create_customer':
					$description = '';
                    if (property_exists($this->data, 'description'))
                    {
                        $description = $this->data->description;
                    }
					$email = '';
                    if (property_exists($this->data, 'email'))
                    {
                        $email = $this->data->email;
                    }
					$meta_data = '';
                    $customer = $this->sh->CreateCustomer($description, $email, $meta_data);
                    $this->status = 200;
                    $this->message = 'Customer created';
                    $this->output = $customer;
					break;
                case 'create_customer_with_card':
					$description = '';
                    if (property_exists($this->data, 'description'))
                    {
                        $description = $this->data->description;
                    }
					$token = '';
					if (property_exists($this->data, 'token'))
                    {
                        $token = $this->data->token;
                    }
					$coupon = '';
                    if (property_exists($this->data, 'coupon'))
                    {
                        $coupon = $this->data->coupon;
                    }
					$email = '';
                    if (property_exists($this->data, 'email'))
                    {
                        $email = $this->data->email;
                    }
					$meta_data = '';
                    $customer = $this->sh->CreateCustomerWithCard($description, $token, $coupon, $email, $meta_data);
                    $this->status = 200;
                    $this->message = 'Customer created';
                    $this->output = $customer;
					break;
				case 'get_customer':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
                    $customer = $this->sh->GetCustomer($customer_id);
                    $this->status = 200;
                    $this->message = 'Customer found';
                    $this->output = $customer;
                    break; 
				case 'update_customer':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$description = '';
                    if (property_exists($this->data, 'description'))
                    {
						if ($this->data->description != '' && $this->data->description != 'null')
						{
							$description = $this->data->description;
						}
						else if ($this->data->description == 'null')
						{
							$description = null;
						}
                    }
					$coupon = '';
                    if (property_exists($this->data, 'coupon'))
                    {
						if ($this->data->coupon != '' && $this->data->coupon != 'null')
						{
							$coupon = $this->data->coupon;
						}
						else if ($this->data->coupon == 'null')
						{
							$coupon = null;
						}
                    }
					$email = '';
                    if (property_exists($this->data, 'email'))
                    {
						if ($this->data->email != '' && $this->data->email != 'null')
						{
							$email = $this->data->email;
						}
						else if ($this->data->email == 'null')
						{
							$email = null;
						}
                    }
					$meta_data = '';
                    $customer = $this->sh->UpdateCustomer($customer_id, $description, $coupon, $email, $meta_data);
                    $this->status = 200;
                    $this->message = 'Customer updated';
                    $this->output = $customer;
                    break; 
				case 'delete_customer':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
                    $customer = $this->sh->DeleteCustomer($customer_id);
                    $this->status = 200;
                    $this->message = 'Customer deleted';
                    $this->output = $customer;
                    break; 
				case 'list_customers':
                    $limit = '';
                    if (property_exists($this->data, 'limit'))
                    {
                        $limit = $this->data->limit;
                    }
                    $customers = $this->sh->ListCustomers($limit);
                    $this->status = 200;
                    $this->message = 'Customers found';
                    $this->output = $customers;
                    break;
				//cards
				case 'list_cards':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
                    $cards = $this->sh->ListCards($customer_id);
                    $this->status = 200;
                    $this->message = 'Cards found';
                    $this->output = $cards;
                    break;
				case 'add_card':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$token = '';
                    if (property_exists($this->data, 'token'))
                    {
                        $token = $this->data->token;
                    }
                    $card = $this->sh->AddCard($customer_id, $token);
                    $this->status = 200;
                    $this->message = 'Card added';
                    $this->output = $card;
                    break;
				case 'get_card':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$card_id = '';
                    if (property_exists($this->data, 'card_id'))
                    {
                        $card_id = $this->data->card_id;
                    }
                    $card = $this->sh->GetCard($customer_id, $card_id);
                    $this->status = 200;
                    $this->message = 'Card found';
                    $this->output = $card;
                    break;
				case 'update_card':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$card_id = '';
                    if (property_exists($this->data, 'card_id'))
                    {
                        $card_id = $this->data->card_id;
                    }
					$address_city = '';
                    if (property_exists($this->data, 'address_city'))
                    {
                        $address_city = $this->data->address_city;
                    }
					$address_country = '';
                    if (property_exists($this->data, 'address_country'))
                    {
                        $address_country = $this->data->address_country;
                    }
					$address_line1 = '';
                    if (property_exists($this->data, 'address_line1'))
                    {
                        $address_line1 = $this->data->address_line1;
                    }
					$address_line2 = '';
                    if (property_exists($this->data, 'address_line2'))
                    {
                        $address_line2 = $this->data->address_line2;
                    }
					$address_state = '';
                    if (property_exists($this->data, 'address_state'))
                    {
                        $address_state = $this->data->address_state;
                    }
					$address_zip = '';
                    if (property_exists($this->data, 'address_zip'))
                    {
                        $address_zip = $this->data->address_zip;
                    }
					$exp_month = '';
                    if (property_exists($this->data, 'exp_month'))
                    {
                        $exp_month = $this->data->exp_month;
                    }
					$exp_year = '';
                    if (property_exists($this->data, 'exp_year'))
                    {
                        $exp_year = $this->data->exp_year;
                    }
					$name = '';
                    if (property_exists($this->data, 'name'))
                    {
                        $name = $this->data->name;
                    }
					$meta_data = '';
                    if (property_exists($this->data, 'meta_data'))
                    {
                        $meta_data = $this->data->meta_data;
                    }
                    $card = $this->sh->UpdateCard($customer_id, $card_id, $address_city, $address_country, $address_line1, $address_line2, $address_state, $address_zip, $exp_month, $exp_year, $name, $meta_data);
                    $this->status = 200;
                    $this->message = 'Card updated';
                    $this->output = $card;
                    break;
				case 'remove_card':
                    $customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$card_id = '';
                    if (property_exists($this->data, 'card_id'))
                    {
                        $card_id = $this->data->card_id;
                    }
                    $card = $this->sh->RemoveCard($customer_id, $card_id);
                    $this->status = 200;
                    $this->message = 'Card found';
                    $this->output = $card;
                    break;
				//subscriptions
				case 'create_subscription':
					$customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$coupon = '';
                    if (property_exists($this->data, 'coupon'))
                    {
                        $coupon = $this->data->coupon;
                    }
					$plan_id = '';
                    if (property_exists($this->data, 'plan_id'))
                    {
                        $plan_id = $this->data->plan_id;
                    }
					$meta_data = '';
                    $subscription = $this->sh->CreateSubscription($customer_id, $coupon, $plan_id, $meta_data);
                    $this->status = 200;
                    $this->message = 'Subscription created';
                    $this->output = $subscription;
					break;
				case 'get_subscription':
                    $subscription_id = '';
                    if (property_exists($this->data, 'subscription_id'))
                    {
                        $subscription_id = $this->data->subscription_id;
                    }
                    $subscription = $this->sh->GetSubscription($subscription_id);
                    $this->status = 200;
                    $this->message = 'Subscription found';
                    $this->output = $subscription;
                    break; 
				case 'update_subscription':
                    $subscription_id = '';
                    if (property_exists($this->data, 'subscription_id'))
                    {
                        $subscription_id = $this->data->subscription_id;
                    }
					$plan = '';
					if (property_exists($this->data, 'plan'))
                    {
						if ($this->data->plan != '' && $this->data->plan != 'null')
						{
							$plan = $this->data->plan;
						}
						else if ($this->data->plan == 'null')
						{
							$plan = null;
						}
                    }
					$coupon = '';
                    if (property_exists($this->data, 'coupon'))
                    {
						if ($this->data->coupon != '' && $this->data->coupon != 'null')
						{
							$coupon = $this->data->coupon;
						}
						else if ($this->data->coupon == 'null')
						{
							$coupon = null;
						}
                    }
					$meta_data = '';
                    $subscription = $this->sh->UpdateSubscription($subscription_id, $plan, $coupon, $meta_data);
                    $this->status = 200;
                    $this->message = 'Subscription updated';
                    $this->output = $subscription;
                    break; 
				case 'cancel_subscription':
                    $subscription_id = '';
                    if (property_exists($this->data, 'subscription_id'))
                    {
                        $subscription_id = $this->data->subscription_id;
                    }
					$immediate = false;
                    if (property_exists($this->data, 'immediate'))
                    {
                        $immediate = $this->data->immediate;
						if ($immediate == 'true') 
						{
							$immediate = true;
						}
						else
						{
							$immediate = false;
						}
                    }
                    $subscription = $this->sh->CancelSubscription($subscription_id, $immediate);
                    $this->status = 200;
                    $this->message = 'Subscription deleted';
                    $this->output = $subscription;
                    break; 
				case 'list_subscriptions':
                    $limit = '';
                    if (property_exists($this->data, 'limit'))
                    {
                        $limit = $this->data->limit;
                    }
					$customer_id = '';
                    if (property_exists($this->data, 'customer_id'))
                    {
                        $customer_id = $this->data->customer_id;
                    }
					$plan_id = '';
                    if (property_exists($this->data, 'plan_id'))
                    {
                        $plan_id = $this->data->plan_id;
                    }
					$status = '';
                    if (property_exists($this->data, 'status'))
                    {
                        $status = $this->data->status;
                    }
                    $subscriptions = $this->sh->ListSubscriptions($limit, $customer_id, $plan_id, $status);
                    $this->status = 200;
                    $this->message = 'Subscriptions found';
                    $this->output = $subscriptions;
                    break;
				//refunds
				case 'create_refund':
					$charge_id = '';
                    if (property_exists($this->data, 'charge_id'))
                    {
                        $charge_id = $this->data->charge_id;
                    }
					$amount = '';
                    if (property_exists($this->data, 'amount'))
                    {
                        $amount = $this->data->amount;
                    }
					$reason = '';
                    if (property_exists($this->data, 'reason'))
                    {
                        $reason = $this->data->reason;
                    }
					$meta_data = '';
                    $refund = $this->sh->CreateRefund($charge_id, $amount, $reason, $meta_data);
                    $this->status = 200;
                    $this->message = 'Refund created';
                    $this->output = $refund;
					break;
				case 'get_refund':
                    $refund_id = '';
                    if (property_exists($this->data, 'refund_id'))
                    {
                        $refund_id = $this->data->refund_id;
                    }
                    $refund = $this->sh->GetRefund($refund_id);
                    $this->status = 200;
                    $this->message = 'Refund found';
                    $this->output = $refund;
                    break; 
				case 'list_refunds':
                    $limit = '';
                    if (property_exists($this->data, 'limit'))
                    {
                        $limit = $this->data->limit;
                    }
                    $refunds = $this->sh->ListRefunds($limit);
                    $this->status = 200;
                    $this->message = 'Refunds found';
                    $this->output = $refunds;
                    break;
                default:
                    $this->message = 'No method found';
                    $this->status = 405;
                    $this->output = null;
                    break;
            }
            $output = array(
                'method' => $this->method,
                'status' => $this->status,
                'message' => $this->message,
                'output' => $this->output,
                'data' => $this->data
            );
            return $this->_response($output, $this->status);
        }
    }
    private function _response($data, $status = 200) 
    {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return json_encode($data);
    }
    private function _cleanInputs($data) 
    {
        $clean_input = Array();
        if (is_array($data)) 
        {
            foreach ($data as $k => $v) 
            {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } 
        else 
        {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    private function _requestStatus($code) 
    {
        $status = array(  
            200 => 'OK',
            403 => 'Invalid',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }
}