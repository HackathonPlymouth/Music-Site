<?php
require_once('includes/core.php');
$album_id=$_SESSION['purchase_product_id'];
$_SESSION['purchase_product_id']='';
$_SESSION['purchased'][]=$album_id;
header("Location: ".BASE_URI."a/$album_id/?c");
?>