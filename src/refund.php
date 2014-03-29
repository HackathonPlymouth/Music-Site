<?php
require_once('pp_api/paypal.php'); //when needed
require_once('pp_api/http_request.php'); //when needed

//Use this form for production server 
//$r = new PayPal(true);

//Use this form for sandbox tests
$r = new PayPal();

if(isset($_GET['transaction_id'])) {
	$transaction_id=$_GET['transaction_id'];
	$response=$r->partial_refund($transaction_id, 5.00, 'USD', 'Final Sale Price');
	print_r($response);
}else{
	echo"Error";
}

?>