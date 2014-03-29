<?php
define("BASE_URI",'/snippets/music/');
define("OUR_PERCENTAGE",'0.1');
$dbc = @mysql_connect ('188.121.40.83', 'hackmusic', 'S7a3e!aAu') OR die ();
@mysql_select_db ('hackmusic') OR die ();
function generate_string($length=32) {
	$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	$a=$chars{rand(0, 61)};
	for($i=1;$i<$length;$i=strlen($a)){
		$r=$chars{rand(0, 61)};
		if($r!=$a{$i - 1}) $a.=$r;
	}
	return $a;
}
function file_path($filename) {
	return '/home/content/29/10246429/html/snippets/music/uploads/'.substr($filename,0,2).'/'.substr($filename,2,2).'/'.substr($filename,4,2).'/'
	.substr($filename,6,2).'/'.substr($filename,8,2).'/'.substr($filename,10,2).'/'.substr($filename,12,2).
	'/'.substr($filename,14);
}
function file_url($filename) {
	return 'http://www.edmundgentle.com/snippets/music/uploads/'.substr($filename,0,2).'/'.substr($filename,2,2).'/'.substr($filename,4,2).'/'
	.substr($filename,6,2).'/'.substr($filename,8,2).'/'.substr($filename,10,2).'/'.substr($filename,12,2).
	'/'.substr($filename,14);
}
function create_folder($str) {
	$folders=explode('/',rtrim($str,'/'));
	$loc='';
	foreach($folders as $fol) {
		$loc.=$fol.'/';
		if(!file_exists($loc) and $loc!='..') {
			mkdir($loc);
			@chmod($loc,0777);
		}
	}
	return true;
}
function make_thumbnail($img_src,$target_width,$target_height,$loc=false) {
	$info = getimagesize($img_src);
	$factor = $target_width / $info[0];
	if($target_height<($factor * $info[1])) {
		$targetheight=$factor*$info[1];
		$targetwidth=$target_width;
		$yoff=($targetheight-$target_height)/2;
		$xoff=0;
	}else{
		$factor = $target_height / $info[1];
		$targetheight=$target_height;
		$targetwidth=$factor*$info[0];
		$xoff=($targetwidth-$target_width)/2;
		$yoff=0;
	}
	$mime = $info['mime'];
	$type = substr(strrchr($mime, '/'), 1);
	$typemaps=array(
		'jpeg'=>'ImageCreateFromJPEG',
		'pjpeg'=>'ImageCreateFromJPEG',
		'png'=>'ImageCreateFromPNG',
		'bmp'=>'ImageCreateFromBMP',
		'x-windows-bmp'=>'ImageCreateFromBMP',
		'vnd.wap.wbmp'=>'ImageCreateFromWBMP',
		'gif'=>'ImageCreateFromGIF',
		'x-xbitmap'=>'ImageCreateFromXBM',
		'x-xbm'=>'ImageCreateFromXBM',
		'xbm'=>'ImageCreateFromXBM',
	);
	$func=$typemaps['jpeg'];
	if(isset($typemaps[$type])) $func=$typemaps[$type];
	$thumb=imagecreatetruecolor($targetwidth,$targetheight);
	$white = imagecolorallocate($thumb, 255, 255, 255);
	imagefill($thumb, 0, 0, $white);
	$source = $func($img_src);
    imagecopyresampled($thumb,$source,0,0,0,0,$targetwidth,$targetheight,$info[0],$info[1]);
    $dest = imagecreatetruecolor($target_width,$target_height);
	imagecopy($dest,$thumb, 0, 0, $xoff,$yoff, $target_width, $target_height);
	if($loc) {
		imagepng($dest,$loc,9);
	}else{
		imagepng($dest);
	}
}
function upload($file,$name=false) {
	if(!$name) {
		$name=$file;
	}
	$xt=explode('.',$name);
	$ext=strtolower(end($xt));
	$image=false;
	do {
		$fn=generate_string(16);
		$filename=$fn.'.'.$ext;
		$path=file_path($filename);
	}while(file_exists($path));
	create_folder(substr($path,0,-(strlen($ext)+2)));
	if(copy($file,$path)) {
		if($info=getimagesize($path)) {
			if(substr($info['mime'],0,6)=='image/') {
				make_thumbnail($path,500,500,file_path($fn.'_r.png'));
				$filename=$fn.'_r.png';
				$image=true;
			}
		}
		return array('name'=>$filename,'path'=>$path,'url'=>file_url($filename),'image'=>$image);
	}else{
		return false;
	}
}
session_start();
?>