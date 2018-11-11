<?php
error_reporting(0);
$name=$_FILES['file']['name'];
 $type=$_FILES['file']['type'];
 $size=$_FILES['file']['size'];
 $tmp_name=$_FILES['file']['tmp_name'];
 $extend= strtolower(substr($name, strpos($name, '.')+1));

if (isset($name)){
	$location='upload/';
	if(!empty($name)){
		if(($extend=='jpg' || $extend=='jpeg' || $extend=='png') && ($type=='image/jpeg'|| $type=='image/jpg' || $type=='image/png')){
		
		if($size<=500000)	{
		if(move_uploaded_file($tmp_name, $location.$name)){
			echo'uploaded!';
		}else{
			echo"failure";
		}
		}else{
			echo'choose a file not greater than 5kb';
		}
		}else{
			echo'please choose a jpeg/jpg/png file';
		}
	}else{
		echo "please choose file";
	}
}


?>

<form action="upload.php" method="POST" enctype="multipart/form-data">
<input type="file" name="file">
<br /> <br />
<input type="submit" value="submit">

</form>