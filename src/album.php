<?php
require_once('includes/core.php');
if(isset($_GET['id'])) {
	$id=mysql_real_escape_string($_GET['id']);
	$result=mysql_query("SELECT albums.album_id, artist.name AS artist_name, albums.name, description, album_art, purchases, price, genre, UNIX_TIMESTAMP(deadline), date_added FROM albums, artist WHERE artist.artist_id=albums.artist_id AND albums.album_id='$id'");
	if(mysql_num_rows($result)) {
		list($album_id, $artist_name, $name, $description, $album_art, $purchases, $price, $genre, $deadline, $date_added) = mysql_fetch_array($result, MYSQL_NUM);
		$deadlines=array();
		$result=mysql_query("SELECT no_of_downloads, price FROM deadlines WHERE album_id='$id'");
		while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$deadlines[$row['no_of_downloads']]=$row['price'];
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<link rel="stylesheet" href="<? echo BASE_URI;?>stylesheets/main.css" type="text/css" charset="utf-8">
			<title>Crowd30</title>
			<script type="text/javascript" src="<? echo BASE_URI;?>js/cufon-yui.js"></script>
			<script type="text/javascript" src="<? echo BASE_URI;?>js/Multicolore_700.font.js"></script>
			<script type="text/javascript">
				Cufon.replace('h1.logo', {textShadow: '1px 1px 1px #42b8a3'});
			</script>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<div class="fl">
						<h1 class="logo">Crowd30</h1>
					</div>
					<div class="fr">
						<form action="#" method="get">
							<input type="text" class="input_search" value="Search..." name="search" onfocus="if (this.value == 'Search...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search...';}" />
							<input type="hidden" class="submission" value="true" />
						</form>
					</div>
				</div>
				<div class="main_content_top"><img src="<? echo BASE_URI;?>images/main_content_top.png" /></div>
				<div class="content">
					<?
					if(isset($_GET['p'])) {?>
						<div class="notification">You have successfully purchased this album.</div>
					<? }
					if(isset($_GET['c'])) {?>
						<div class="notification">Your order has been cancelled.</div>
					<? }
					?>
					<div class="fl_main_eigth">
						<h1 class="album_title"><? echo $artist_name;?> - <? echo htmlspecialchars($name);?></h1>
						<div class="album_cover"><img src="<? echo file_url($album_art);?>" /></div>
						<div class="right_mid_holder">
							<div class="buttons">
								<?php
								if(isset($_SESSION['purchased']) and is_array($_SESSION['purchased']) and in_array($album_id,$_SESSION['purchased'])) {
									echo "<a href=\"\" class=\"blue_button\">Download</a>";
								}else{
									echo"<a href=\"".BASE_URI."a/$album_id/purchase/\" class=\"blue_button\">Purchase</a>";
								}
								?>
								<a href="#" class="purple_button">Add to iTunes</a>
							</div>
							<div class="description">
								<p><? echo nl2br(htmlspecialchars($description));?></p>
							</div>
						</div>
					</div>
					<div class="right_side">
						<div class="vertical_div_holder">
							<div class="sales">
								<p class="big"><? echo number_format($purchases,0);?></p>
								<p class="smaller">Current Sales</p>
							</div>
							<div class="current_price">
								<p class="big">&dollar;<? echo number_format($price,2);?></p>
								<p class="smaller">Current Price</p>
							</div>
							<div class="days_left">
								<p class="big"><?php echo floor(($deadline-time())/(60*60*24));?></p>
								<p class="smaller">Days Left</p>
							</div>
						</div>
					</div>
				</div>
				<div class="main_content_bottom"><img src="<? echo BASE_URI;?>images/main_content_bottom.png" /></div>

				<div class="second_holder">
					<div class="main_content_top"><img src="<? echo BASE_URI;?>images/main_content_top.png" /></div>
					<div class="content">
						<div class="progress_bar">
							<div class="holder_progress">
								<div class="fl"></div>
								<div class="fr"></div>
							</div>
							<?php

							foreach($deadlines as $d=>$p) {?>
								<div class="twf">
									<p class="price_progress"><? echo number_format($d,0);?> Sales</p>
									<p class="big_price_progress">&dollar;<? echo number_format($p,2);?></p>
									<p class="small_price_progress">Sale Price</p>
								</div>
							<?
							}
							?>
						</div>
					</div>
					<div class="main_content_bottom"><img src="<? echo BASE_URI;?>images/main_content_bottom.png" /></div>
				</div>

			</div>
		</body>
		</html>
	<? }
}
?>