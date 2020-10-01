<?php
require_once('includes/core.php');
require_once('pp_api/paypal.php');
require_once('pp_api/http_request.php');
define('CURRENCY','USD');

//Use this form for production server 
//$r = new PayPal(true);

//Use this form for sandbox tests
//New Mode Of Payment 
//Online Transactions
$r = new PayPal();

if(isset($_GET['id'])) {
	$id=mysql_real_escape_string($_GET['id']);
	$result=mysql_query("SELECT albums.album_id, CONCAT(artist.name,': ',albums.name) AS name, price FROM albums, artist WHERE artist.artist_id=albums.artist_id AND albums.album_id='$id'");
	if(mysql_num_rows($result)) {
		list($album_id, $product_name, $product_price) = mysql_fetch_array($result, MYSQL_NUM);
		$_SESSION['purchase_product_id']=$album_id;
		$ret = ($r->doExpressCheckout($product_price, $product_name, '', CURRENCY));
		print_r($ret);
	}
}
?>
