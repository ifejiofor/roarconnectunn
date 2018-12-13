<?php
require'includes/generalHeaderFile.php';

if(!currentUserIsLoggedIn()){
	header('location:index.php');
}else{
		$name=$_FILES['file']['name'];
	 $type=$_FILES['file']['type'];
	 $size=$_FILES['file']['size'];
	 $tmp_name=$_FILES['file']['tmp_name'];
	 
	 $request = "SELECT id FROM scholarships";
	if($request_new=mysqli_query($globalHandleToDatabase, $request)){
		$check=mysqli_fetch_assoc($request_new);
	}
	else {
		die( "Unexpected error" );
	}
	
	if ( mysqli_num_rows( $request_new ) < 1  ) {
		echo 'No post yet.';
	}
	else {
		$numberOfPostsInDatabase = mysqli_num_rows( $request_new );
		
	
	 
	 if (!isset($_POST['nextButton']) && !isset($_POST['previousButton']) ) {
	   $request = "SELECT id FROM scholarships ORDER BY id DESC";
		if($request_new=mysqli_query($globalHandleToDatabase, $request)){
			$check=mysqli_fetch_array($request_new);
			$idOfPostToBeDisplayed = $check['id'];
		}
		else {
			die( "Unexpected error" );
		}
	 }
	 else {
		 $idOfPostToBeDisplayed = $_POST['idOfNextPost'];
	 }
	 
	 // display previous button if applicable
     if ( $numberOfPostsInDatabase > $idOfPostToBeDisplayed ) {
?>
    <form method="POST" action="scholarship.php">
	    <input type="hidden" name="idOfNextPost" value="<?php echo $idOfPostToBeDisplayed + 1 ?>"/>
		<input type="submit" name="previousButton" value="Previous"/>
	</form>	
<?php
	 }
	 
	 // display next button if applicable 
	if( $idOfPostToBeDisplayed > 1 ){
?>
    <form method="POST" action="scholarship.php">
	    <input type="hidden" name="idOfNextPost" value="<?php echo $idOfPostToBeDisplayed - 1 ?>"/>
		<input type="submit" name="nextButton" value="Next"/>
	</form>	 
<?php
	}
	
	$request = 'SELECT `image_name`, `caption`, `text` FROM scholarships WHERE id = ' . $idOfPostToBeDisplayed;
	if($request_new=mysqli_query($globalHandleToDatabase, $request)){
		$check=mysqli_fetch_array($request_new);
?>
<div>
   <h1><?php echo $check['caption'] ?></h1>
   <img src="scholarship/<?php echo $check['image_name'] ?>" alt="<?php echo $check['image_name'] ?>" />
   <p><?php echo $check['text'] ?></p>
</div>
<?php
	}
	else {
		die( "Unexpected error" );
	}
}

	$request="SELECT `username`, `password` FROM `users` WHERE `id`='".$_SESSION['user_id']."'";
		if($request_new=mysqli_query($globalHandleToDatabase, $request)){
			$request_query=mysqli_fetch_array($request_new);
		     $user=$request_query['username'];
			 $pass=$request_query['password'];
		}
		
		if($user=="hero12" && $pass=="hero12"){//this is the admin
			if (isset($name) && isset($_POST['caption']) && isset($_POST['text'])){
			$caption=$_POST['caption'];
			$text=$_POST['text'];
				if(!empty($name) || !empty($caption) || !empty($text)){
					if(!empty($name)){
					if($size<=102400)	{
					$upload="INSERT INTO `scholarships`(`image_name`, `caption`, `text`) VALUES ('$name','$caption','$text')";
		if($upload_run=mysqli_query($globalHandleToDatabase, $upload)){
			$location='scholarship/';
			if((move_uploaded_file($tmp_name, $location.$name))){
			header('Location: scholarship.php');
			}else{
				echo 'An error occurred try again later!';
			}
	
		}else{
			'Upload Unsuccessful, Try again later.';
			 
		}
			}else{
				echo 'Image size should not be greater than 100kb';
			}
				}else{
			$upload="INSERT INTO `scholarships`(`caption`, `text`) VALUES ('$caption','$text')";
		if($upload_run=mysqli_query($globalHandleToDatabase, $upload)){
			header('Location: scholarship.php');
			
				}
				}
			
				}else{
					echo 'Enter a field!';
				}
			
			}
		?>
		<br /> <br />
	<form action="scholarship.php" method="POST" enctype="multipart/form-data">
	BLOG CAPTION:<input type="text" name="caption" maxlength="70"><br /> <br />
Enter image if required<input type="file" name="file" value="<?php if(isset($_POST['name'])){echo $name;} ?>"><br /> <br />
BLOG TEXT:<input type="text" name="text" maxlength="5000"><br /> <br />
<input type="submit" value="Post">


</form>
		
		<?php		
			
		}else{
		}
			 
}


?>