<?php
require_once('includes/core.php');
if(isset($_SESSION['user_id'])) {
	if(isset($_POST['name'])) {
		$name=trim(stripslashes($_POST['name']));
		$description='';
		if(isset($_POST['description'])) {
			$description=trim(stripslashes($_POST['description']));
		}
		$artwork=array();
		if(isset($_FILES['artwork']) && ($_FILES['artwork']['error'] != 4)) {
			$artwork=upload($_FILES['artwork']['tmp_name'],$_FILES['artwork']['name']);
			if(!$artwork['image']) {
				$artwork=array();
			}
			//name, path, url, image
		}
		$genres='';
		if(isset($_POST['genres'])) {
			$genres=trim(stripslashes($_POST['genres']));
		}
		$sales_divs=array();
		if(isset($_POST['price'])) {
			$price=(double)trim(stripslashes($_POST['price']));
			$sales_divs[0]=$price;
		}
		if(isset($_POST['numsales']) and isset($_POST['prices']) and is_array($_POST['numsales']) and is_array($_POST['prices'])) {
			foreach($_POST['numsales'] as $k=>$v) {
				if(isset($_POST['prices'][$k])) {
					$sales='';$prices='';
					$sales=(int)trim(stripslashes($v));
					$prices=(double)trim(stripslashes($_POST['prices'][$k]));
					if($sales && $prices) {
						$sales_divs[$sales]=$prices;
					}
				}
			}
		}
		if($name && $artwork['name'] && count($sales_divs)) {
			$deadline=strtotime("+30 days");
			do {
				$album_id = generate_string(10);
			}while(mysql_num_rows(mysql_query("SELECT * FROM albums WHERE album_id='$album_id'")));
			$artist_id=$_SESSION['user_id'];
			mysql_query("INSERT INTO albums (album_id, artist_id, name, description, album_art, purchases, price, genre, deadline) VALUES ('$album_id', '$artist_id', '".mysql_real_escape_string($name)."', '".mysql_real_escape_string($description)."', '".mysql_real_escape_string($artwork['name'])."', 0, '".mysql_real_escape_string($sales_divs[0])."', '".mysql_real_escape_string($genres)."', ".date('Y-m-d H:i:s',$deadline).")");
			$query=array();
			foreach($sales_divs as $k=>$v) {
				$query[]="('$album_id','$k','$v')";
			}
			if(count($query)>0) {
				mysql_query("INSERT INTO deadlines (album_id, no_of_downloads, price) VALUES ".implode(',',$query));
			}
			header("Location: ".BASE_URI."dashboard/");
			exit();
		}
	}
	
	?>
	<form method="POST" enctype="multipart/form-data">
		<label>Name</label><input name="name" type="text" />
		<label>Description</label><textarea name="description"></textarea>
		<label>Album Artwork</label><input type="file" name="artwork" />
		<label>Genres</label><input name="genres" type="text" />
		<label>Starting Price</label><input name="price" type="text" />
		<div>After <input name="numsales[]" /> sales, the prices drops to &dollar;<input name="prices[]" /></div>
		<div>After <input name="numsales[]" /> sales, the prices drops to &dollar;<input name="prices[]" /></div>
		<div>After <input name="numsales[]" /> sales, the prices drops to &dollar;<input name="prices[]" /></div>
		<div>After <input name="numsales[]" /> sales, the prices drops to &dollar;<input name="prices[]" /></div>
		<input type="submit" value="Add Album" />
	</form>
<? }
?>