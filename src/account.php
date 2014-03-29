<?php
require_once('includes/core.php');
if(isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
	$result = mysql_query("SELECT albums.album_id, artist.name AS artist_name, albums.name, description, album_art, purchases, price, genre, deadline, date_added FROM albums, artist WHERE artist.artist_id=albums.artist_id AND artist.artist_id='$user_id'");
	while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {?>
		<div><h1><a href="<?php echo BASE_URI;?>a/<?php echo $row['album_id'];?>/"><? echo $row['name'];?></a></h1>
		<img src="<? echo file_url($row['album_art']);?>" />
		<p><? echo htmlspecialchars($row['description']);?></p>
		<p><strong>Price: </strong>&dollar;<? echo number_format($row['price'],2);?></p></div>
	<? }?>
	<p><a href="<?php echo BASE_URI;?>dashboard/account/">Account Settings</a> | <a href="<?php echo BASE_URI;?>dashboard/add/">Add Album</a></p>
<? }
?>