<?php
define( 'MAXIMUM_ALLOWABLE_IMAGE_SIZE', 512000 ); // 512000 Bytes is equal to 500 MB

$query = 'SELECT blog_category_id FROM blog_categories WHERE blog_category_name = "' . mysqli_real_escape_string( $globalHandleToDatabase, $_GET['category'] ) . '"';
$resultContainingBlogCategory = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$rowContainingBlogCategory = mysqli_fetch_assoc( $resultContainingBlogCategory );

// If an update has been posted, insert it into the database
if ( isset( $_POST['postButton'] ) ) {
	$userInputContainsError = false;
	if ( empty( $_POST['caption'] ) || empty( $_POST['mainText'] ) ) {
		$errorMessage = 'Some fields are blank.';
		$userInputContainsError = true;
	}
	else if ( is_uploaded_file( $_FILES['image']['tmp_name'] ) && $_FILES['image']['size'] > MAXIMUM_ALLOWABLE_IMAGE_SIZE ) {
		$errorMessage = 'File is too large. File size must not be more than ' . ( MAXIMUM_ALLOWABLE_IMAGE_SIZE / 1024 ) . ' Kilobytes.';
		$userInputContainsError = true;
	}
	else if ( is_uploaded_file( $_FILES['image']['tmp_name'] ) && $_FILES['image']['type'] != 'image/jpeg' ) {
		$errorMessage = 'Invalid file type. Only JPG images allowed.';
		$userInputContainsError = true;
	}
	else {
		$_POST['caption'] = trim( htmlentities( $_POST['caption'] ) );
		$_POST['mainText'] = separateAllLinesOfTextWithParagraphTags( trim( $_POST['mainText'] ) );
		
		$query = 'INSERT INTO blog_posts ( blog_category_id, blog_post_caption, blog_post_text, user_id_of_poster, blog_post_time_of_posting, blog_post_approval_status ) VALUES ( ' . $rowContainingBlogCategory['blog_category_id'] . ', "' . mysqli_real_escape_string( $globalHandleToDatabase, $_POST['caption'] ) . '", "' . mysqli_real_escape_string( $globalHandleToDatabase, $_POST['mainText'] ) . '", ' . $_SESSION['user_id'] . ', NOW(), "' . ( isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogCategory['blog_category_id'] ) ? 'APPROVED' : 'NEWLY UPLOADED' ) . '" )';
		mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
		$idOfJustInsertedBlogPost = mysqli_insert_id( $globalHandleToDatabase );

		if ( is_uploaded_file( $_FILES['image']['tmp_name'] ) ) {
			$uploadingOfImageIsSuccessful = move_uploaded_file( $_FILES['image']['tmp_name'], 'assets/images/ImagesFor' . $_GET['category'] . 'Updates/' . $idOfJustInsertedBlogPost . '.jpg' );
			
			if ( $uploadingOfImageIsSuccessful ) {
				$query = 'UPDATE blog_posts SET blog_post_image_filename = "' . $idOfJustInsertedBlogPost . '.jpg" WHERE blog_post_id = ' . $idOfJustInsertedBlogPost;
		        mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
				
				if ( isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogCategory['blog_category_id'] ) ) {
					header( 'Location: blog_home.php?category=' . $_GET['category'] );
				}
				else {
					header( 'Location: your_blog_posts.php' );
				}
			}
			else {
				$query = 'DELETE FROM blog_posts WHERE blog_post_id = ' . $idOfJustInsertedBlogPost;
				mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
				$errorMessage = 'An error occurred while uploading your update. Please try again later.';
				$userInputContainsError = true;
			}
		}
		else {
			if ( isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogCategory['blog_category_id'] ) ) {
				header( 'Location: blog_home.php?category=' . $_GET['category'] );
			}
			else {
				header( 'Location: your_blog_posts.php' );
			}
		}
	}
}

?>

<?php
if ( currentUserIsLoggedIn() ) {
?>
               <button id="postNewBlogUpdateButton" class="btn btn-primary">Post a New Update</button>
<?php
}
?>
			   <div class="<?php echo $userInputContainsError ? 'show' : 'hide' ?>" id="containerHoldingPostNewBlogUpdateForm">
                  <?php echo $userInputContainsError ? '<p id="errorMessage">' . $errorMessage . '</p>' : '' ?>
				  
			      <form method="POST" action="blog_home.php?category=<?php echo $_GET['category'] ?>" enctype="multipart/form-data" class="form-horizontal text-left" id="narrowGenericSection">
			         <div class="form-group">
				        <label for="caption" class="control-label col-sm-4">Caption of Update:</label>
					    <div class="col-sm-8">
					       <input type="text" name="caption" maxlength="100" value="<?php echo isset( $_POST['caption'] ) ? $_POST['caption'] : '' ?>" class="form-control" id="caption" />
					    </div>
				     </div>
				  
				     <div class="form-group">
                        <label for="blogMainText" class="control-label col-sm-4">Main Text of Update:</label>
					    <div class="col-sm-8">
					       <textarea name="mainText" maxlength="5000" class="form-control" id="bigSizedTextArea"><?php echo isset( $_POST['mainText'] ) ? $_POST['mainText'] : '' ?></textarea>
					    </div>
			         </div>
				  
				     <div class="form-group">
                        <label for="image" class="control-label col-sm-4">(Optional) Upload Image:</label>
					    <div class="col-sm-8">
					       <input type="file" name="image" class="form-control" id="image" />
					    </div>
				     </div>

                     <div class="form-group">
				        <div class="col-sm-offset-4 col-sm-8">
				           <input type="submit" name="postButton" value="Post" class="btn btn-primary btn-lg" />
				        </div>
				     </div>
			      </form>
			   </div>