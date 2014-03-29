<?php
require_once('includes/core.php');
require_once('pp_api/paypal.php');
require_once('pp_api/http_request.php');
//Use this form for production server 
//$r = new PayPal(true);
//Use this form for sandbox tests
$r = new PayPal();

$token = $_GET['token'];
$d = $r->getCheckoutDetails($token);
$final = $r->doPayment();
if ($final['ACK'] == 'Success') {
	$transaction_id=$final['TRANSACTIONID'];
	$album_id=$_SESSION['purchase_product_id'];
	$price_paid=$final['AMT'];
	$purchase_email=$d['EMAIL'];
	if(mysql_query("INSERT INTO purchases (transaction_id, album_id, price_paid, purchase_email) VALUES ('$transaction_id', '$album_id', '$price_paid', '$purchase_email')")) {
		mysql_query("UPDATE albums SET purchases=purchases+1 WHERE album_id='$album_id'");
		$result=mysql_query("SELECT artist_id FROM albums WHERE album_id='$album_id'");
		if(mysql_num_rows($result)==1) {
			list($artist_id) = mysql_fetch_array($result, MYSQL_NUM);
			$artist_amt=(double)$price_paid*(1.0-OUR_PERCENTAGE);
			mysql_query("UPDATE artist SET balance=balance+$artist_amt WHERE artist_id='$artist_id'");
		}
		$_SESSION['purchase_product_id']='';
		$_SESSION['purchased'][]=$album_id;
		header("Location: ".BASE_URI."a/$album_id/?p");
		exit();
	}
} else {
	echo "ERROR!";
}
?>