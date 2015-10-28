<?php

class groove {
	// INITIALIZE VARIABLES
	public $endpoint = 'https://api.groovehq.com/v1';
	public $sep = '/';
	public $method = array(
		// tickets
		'getTickets'=>['tickets', 'get', [], ['assignee', 'customer', 'page', 'per_page', 'state']],
		'getTicket'=>['tickets/$$ticket_number$$', 'get', [], []],
		'getTicketState'=>['tickets/$$ticket_number$$/state', 'get', [], []],
		'updateTicketState'=>['tickets/$$ticket_number$$/state', 'put', ['state'], []],
		'getTicketMessages'=>['tickets/$$ticket_number$$/messages', 'get', [], ['page', 'per_page']],
		'getTicketMessage'=>['messages/$$id$$', 'get', [], []],
		'createTicketMessage'=>['tickets/$$ticket_number$$/messages', 'post', ['body'], ['note']],
		// customers
		'getCustomers'=>['customers', 'get', [], ['page', 'per_page']],
		'getCustomer'=>['customers/$$customer_email$$', 'get', [], []],
		'updateCustomer'=>['customers/$$customer_email$$', 'put', ['email'], ['first_name','last_name','about','twitter_username','title','company_name','phone_number','location','linkedin_username']],
		// agent
		'getAgent'=>['agents/$$agent_email$$', 'get', [], []]
	);
	private $token;
	// CONSTRUCTOR
	public function __construct($token){
		if(is_null($token))$this->error('Access token required');
		$this->setAccessToken($token);
	}
	// ACCESS TOKEN
	public function setAccessToken($token){
		$this->token = $token;
	}
	// DEFAULT METHOD(USING PUBLIC METHODS VARIABLE)
	public function __call($name, $arguments){
		// get method parts
		$method = $this->method[$name];
		list($endpoint, $call_method, $required_params, $optional_params) = $method;
		// parse endpoint
		$arg_count = count($arguments);
		preg_match_all('/\$\$([^\$]+)\$\$/', $endpoint, $endpoint_vars);
		$endpoint_vars = $endpoint_vars[1];
		$endpoint_var_count = count($endpoint_vars);
		if($arg_count<$endpoint_var_count)
			return $this->error("Missing variable '".$endpoint_vars[$arg_count]."' in method $name");
		$endpoint_split = explode('$$', $endpoint);
		$endpoint_split_count = count($endpoint_split);
		$parsed_endpoint = '';
		for($a=0; $a<($endpoint_split_count); $a++){
			if($a%2)$parsed_endpoint .= array_shift($arguments);
			else $parsed_endpoint .= $endpoint_split[$a];
		}
		$full_endpoint = $this->endpoint.$this->sep.$parsed_endpoint;
		// get request data
		$arg_count = count($arguments);
		$required_params_count = count($required_params);
		if($arg_count<$required_params_count)
			return $this->error("Missing variable '".$required_params[$arg_count]."' in method $name");
		$full_arguments = array();
		foreach($arguments as $value){
			if(is_array($value)){
				$full_arguments = array_merge($full_arguments, $value);
			} else {
				if(!empty($required_params))$key = array_shift($required_params);
				else if(!empty($optional_params))$key = array_shift($optional_params);
				$full_arguments[$key] = $value;
			}
		}
		// do request
		$result = $this->request($full_endpoint, $call_method, $full_arguments);
		return $result;
	}
	// REQUEST CONTROL
	private function request($url, $method='get', $data=array()){
		$data['access_token'] = $this->token;
		$content = http_build_query($data);
		if($method=='get')
			$url = $url.'?'.$content;
		$header = "Content-type: application/x-www-form-urlencoded";
		$method = strtoupper($method);
		$protocol_version = "1.0";
		$opts = array('http' => compact('content', 'header', 'method'));
		$context = stream_context_create($opts);
		$result = @file_get_contents($url, false, $context);
		if($result === false)throw new Exception('Request failed: '.$url);
		$result = $this->handleResult($result);
		return $result;
	}
	private function handleResult($result){
		$result = $this->parseResult($result);
		if(isset($result->errors) && count($result->errors))
			$this->error($result->errors);
		return $result;
	}
	private function parseResult($result){
		$result = json_decode($result, false);
		return $result;
	}
	// ERROR CONTROL
	private function error($msg='Unknown error'){
		throw new Exception($msg);
		return false;
	}
}

?>
