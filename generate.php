<?php
session_start();
header('content-type: image/jpeg');
$text=$_SESSION['captcha'];
$font_size=40;
$image_width=160;
$image_height=50;
$image=imagecreate($image_width, $image_height); 
imagecolorallocate($image, 255, 153, 51);
$text_color=imagecolorallocate($image, 255, 255, 255);
$line_color=imagecolorallocate($image, 0, 0, 0);
for($x=1; $x<=30; $x++){
	$x1= rand(5, 100);
	$y1= rand(5, 100);
	$x2= rand(5, 200);
	$y2= rand(5, 200);
	imageline($image, $x1, $y1, $x2, $y2, $line_color);
}
imagettftext($image, $font_size, 8, 15, 40, $text_color, 'font1.TTF', $text);
imagejpeg($image);
?>