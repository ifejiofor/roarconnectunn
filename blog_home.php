<?php
require_once 'in.php';
require_once 'require.php';
require_once 'markupsCommonToTopAndBottomOfPages.php';
define( 'MAXIMUM_ALLOWABLE_IMAGE_SIZE', 512000 ); // 512000 Bytes is equal to 500 MB
define( 'MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY', 10 );

if ( !isset( $_GET['category'] ) ) {
	header( 'Location: index.php' );
}

if ( $_GET['category'] != 'Aspirant Gists' && $_GET['category'] != 'Fresher Gists' && $_GET['category'] != 'Old Student Gists' &&
      $_GET['category'] != 'Scholarships' && $_GET['category'] != 'Other' &&
      $_GET['category'] != 'Football' && $_GET['category'] != 'Basketball' )
{
	header( 'Location: index.php' );
}

if ( isset( $_GET['offset'] ) && !consistsOfOnlyDigits( $_GET['offset'] ) ) {
	header( 'Location: index.php' );
}

$query = 'SELECT blog_category_id FROM blog_categories WHERE blog_category_name = "' . mysqli_real_escape_string( $db, $_GET['category'] ) . '"';
$resultContainingBlogCategory = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
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
		
		$query = 'INSERT INTO blog_posts ( blog_category_id, blog_post_caption, blog_post_text, user_id_of_poster, blog_post_time_of_posting, blog_post_approval_status ) VALUES ( ' . $rowContainingBlogCategory['blog_category_id'] . ', "' . mysqli_real_escape_string( $db, $_POST['caption'] ) . '", "' . mysqli_real_escape_string( $db, $_POST['mainText'] ) . '", ' . $_SESSION['user_id'] . ', NOW(), "' . ( isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogCategory['blog_category_id'] ) ? 'APPROVED' : 'NEWLY UPLOADED' ) . '" )';
		mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
		$idOfJustInsertedBlogPost = mysqli_insert_id( $db );

		if ( is_uploaded_file( $_FILES['image']['tmp_name'] ) ) {
			$uploadingOfImageIsSuccessful = move_uploaded_file( $_FILES['image']['tmp_name'], 'images/ImagesFor' . $_GET['category'] . 'Updates/' . $idOfJustInsertedBlogPost . '.jpg' );
			
			if ( $uploadingOfImageIsSuccessful ) {
				$query = 'UPDATE blog_posts SET blog_post_image_filename = "' . $idOfJustInsertedBlogPost . '.jpg" WHERE blog_post_id = ' . $idOfJustInsertedBlogPost;
		        mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
				
				if ( isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogCategory['blog_category_id'] ) ) {
					header( 'Location: blog_home.php?category=' . $_GET['category'] );
				}
				else {
					header( 'Location: your_blog_posts.php' );
				}
			}
			else {
				$query = 'DELETE FROM blog_posts WHERE blog_post_id = ' . $idOfJustInsertedBlogPost;
				mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
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

$customizedStyleForBodyElement = 'background-image: url( \'images/backgroundImages/' . $_GET['category'] . '.jpg\' ); background-size: cover;';
displayMarkupsCommonToTopOfPages( $_GET['category'] . ' Updates', DISPLAY_NAVIGATION_MENU, 'blog.php?category=' . $_GET['category'], $customizedStyleForBodyElement );

if ( $rowContainingBlogCategory == NULL ) {
	header( 'Location: index.php' );
}
?>
            <header id="minorHeader">
               <h1 id="minorHeader">Latest <?php echo $_GET['category'] ?> Updates</h1>
<?php
if ( loggin() ) {
?>
               <button id="postNewBlogUpdateButton" class="btn btn-primary">Post a New Update</button>
<?php
}
?>
			   
			   <div class="<?php echo $userInputContainsError ? 'show' : 'hide' ?>" id="containerHoldingPostNewBlogUpdateForm">
                  <?php echo $userInputContainsError ? '<p id="errorMessage">' . $errorMessage . '</p>' : '' ?>
				  
			      <form method="POST" action="blog_home.php?category=<?php echo $_GET['category'] ?>" enctype="multipart/form-data" class="form-horizontal text-left" id="smallContainerWithBorderAndAllowsOverflow">
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
            </header>
<?php
$currentOffset = isset( $_GET['offset'] ) ? $_GET['offset'] : 0;

$query = 'SELECT blog_post_id, blog_post_image_filename, blog_post_caption FROM blog_posts WHERE blog_category_id = ' . $rowContainingBlogCategory['blog_category_id'] . ' AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting DESC LIMIT ' . $currentOffset . ', ' .  MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY;
$resultContainingBlogPosts = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingBlogPosts = mysqli_fetch_assoc( $resultContainingBlogPosts );

$counter = 1;

while ( $rowContainingBlogPosts != NULL ) {
?>
            <a href="blog.php?category=<?php echo $_GET['category'] ?>&idOfRequiredPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" id="blogHeadlineContainer">
<?php
	if ( $rowContainingBlogPosts['blog_post_image_filename'] != NULL ) {
?>
			   <img src="images/ImagesFor<?php echo $_GET['category'] ?>Updates/<?php echo $rowContainingBlogPosts['blog_post_image_filename'] ?>" alt="Image of <?php echo $rowContainingBlogPosts['blog_post_caption'] ?>" id="<?php echo $counter % 2 == 0 ? 'blogHeadlineImageEven' : 'blogHeadlineImageOdd' ?>" />
<?php
	}
?>
               <h2 id="blogHeadlineText"><?php echo $rowContainingBlogPosts['blog_post_caption'] ?></h2>
            </a>
			
<?php
    $counter++;
	$rowContainingBlogPosts = mysqli_fetch_assoc( $resultContainingBlogPosts );
}

$query = 'SELECT blog_post_id FROM blog_posts WHERE blog_category_id = ' . $rowContainingBlogCategory['blog_category_id'] . ' AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting DESC LIMIT ' . ( $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ) . ', 1';
$resultContainingNextPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingNextPost = mysqli_fetch_assoc( $resultContainingNextPost );

if ( $rowContainingNextPost != NULL ) {
?>
            <a href="blog_home.php?category=<?php echo $_GET['category'] ?>&offset=<?php echo $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ?>" id="specialButtonFloatingToTheRight">View More Updates &gt;&gt;</a>
<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>