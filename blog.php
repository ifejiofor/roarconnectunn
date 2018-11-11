<?php
require_once 'in.php';
require_once 'require.php';
require_once 'markupsCommonToTopAndBottomOfPages.php';

if ( !isset( $_GET['category'] ) ) {
	header( 'Location: index.php' );
}

if ( $_GET['category'] != 'Aspirant Gists' && $_GET['category'] != 'Fresher Gists' && $_GET['category'] != 'Old Student Gists' &&
      $_GET['category'] != 'Scholarships' && $_GET['category'] != 'Other' &&
      $_GET['category'] != 'Football' && $_GET['category'] != 'Basketball' )
{
	header( 'Location: index.php' );
}

if ( isset( $_GET['idOfRequiredPost'] ) && !consistsOfOnlyDigits( $_GET['idOfRequiredPost'] ) ) {
	header( 'Location: index.php' );
}


if ( isset( $_POST['likeButton'] ) ) {
	$query = 'INSERT INTO likes_to_blog_posts ( blog_post_id, user_id_of_liker ) VALUES ( ' . $_POST['idOfPostToBeAssociatedWithLike'] . ', ' . $_SESSION['user_id']. ' )';
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLikeOrUnlikeButton' );
}

if ( isset( $_POST['unlikeButton'] ) ) {
   $query = 'DELETE FROM likes_to_blog_posts WHERE blog_post_id = ' . $_POST['idOfPostToBeAssociatedWithLike'] . ' AND user_id_of_liker = ' . $_SESSION['user_id'];
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLikeOrUnlikeButton' );
}


if ( isset( $_POST['loveButton'] ) ) {
	$query = 'INSERT INTO loves_to_blog_posts ( blog_post_id, user_id_of_lover ) VALUES ( ' . $_POST['idOfPostToBeAssociatedWithLove'] . ', ' . $_SESSION['user_id']. ' )';
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLoveOrUnloveButton' );
}

if ( isset( $_POST['unloveButton'] ) ) {
   $query = 'DELETE FROM loves_to_blog_posts WHERE blog_post_id = ' . $_POST['idOfPostToBeAssociatedWithLove'] . ' AND user_id_of_lover = ' . $_SESSION['user_id'];
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLoveOrUnloveButton' );
}

if ( isset( $_POST['commentButton'] ) ) {
	$commentText = mysqli_real_escape_string( $db, trim( $_POST['commentText'] ) );
	if( !empty( $commentText ) ) {
		$query ='INSERT INTO comments_to_blog_posts ( comment_text, blog_post_id, user_id_of_commenter, time_of_commenting ) VALUES ( "' . $commentText . '", ' . $_POST['idOfPostToBeAssociatedWithComment'] . ', ' . $_SESSION['user_id']. ', NOW() )';
		mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
		header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#' . mysqli_insert_id( $db ) );
	}
}

$customizedStyleForBodyElement = 'background-image: url( \'images/backgroundImages/' . $_GET['category'] . '.jpg\' ); background-size: cover;';
displayMarkupsCommonToTopOfPages( $_GET['category'] . ' Updates', DISPLAY_NAVIGATION_MENU, 'blog.php?category=' . $_GET['category'], $customizedStyleForBodyElement );
	 
$query = 'SELECT blog_category_id FROM blog_categories WHERE blog_category_name = "' . mysqli_real_escape_string( $db, $_GET['category'] ) . '"';
$resultContainingBlogCategory = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingBlogCategory = mysqli_fetch_assoc( $resultContainingBlogCategory );

if ( $rowContainingBlogCategory == NULL ) {
	header( 'Location: index.php' );
}
?>
         <header id="minorHeaderType2">
			   <a href="blog_home.php?category=<?php echo $_GET['category'] ?>"><h1><?php echo $_GET['category'] ?> Updates on RoarConnect</h1></a>
			</header>

<?php
$query = 'SELECT blog_post_id, blog_post_image_filename, blog_post_caption, blog_post_text, user_id_of_poster, blog_post_approval_status, blog_post_time_of_posting, MONTHNAME( blog_post_time_of_posting ) AS month_of_posting, DAYOFMONTH( blog_post_time_of_posting ) AS day_of_posting FROM blog_posts';

if ( isset( $_GET['idOfRequiredPost'] ) ) {
	$query .= ' WHERE blog_post_id = ' . $_GET['idOfRequiredPost'];
}
else {
	$query .= ' WHERE blog_category_id = ' . $rowContainingBlogCategory['blog_category_id'] . ' AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting DESC LIMIT 1';
}

$resultContainingBlogPostToBeDisplayed = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingBlogPostToBeDisplayed = mysqli_fetch_assoc( $resultContainingBlogPostToBeDisplayed );

if ( $rowContainingBlogPostToBeDisplayed == NULL ) {
	echo '
            <p id="mediumSizedText">No post yet.</p>
	';
	displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
	exit( 0 );
}
?>
            <section id="containerWithBorderAndWithoutRoundedCorners">
<?php
if ( $rowContainingBlogPostToBeDisplayed['blog_post_approval_status'] == 'APPROVED' ) {
   // Get previous blog post from database
   $query = 'SELECT blog_post_id, blog_post_caption FROM blog_posts WHERE blog_post_time_of_posting > "' . $rowContainingBlogPostToBeDisplayed['blog_post_time_of_posting'] . '" AND blog_category_id = ' . $rowContainingBlogCategory['blog_category_id'] . ' AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting ASC LIMIT 1';
   $resultContainingPreviousBlogPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure . $query );
   $rowContainingPreviousBlogPost = mysqli_fetch_assoc( $resultContainingPreviousBlogPost );

   // Get next blog post from database
   $query = 'SELECT blog_post_id, blog_post_caption FROM blog_posts WHERE blog_post_time_of_posting < "' . $rowContainingBlogPostToBeDisplayed['blog_post_time_of_posting'] . '" AND blog_category_id = ' . $rowContainingBlogCategory['blog_category_id'] . ' AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting DESC LIMIT 1';
   $resultContainingNextBlogPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingNextBlogPost = mysqli_fetch_assoc( $resultContainingNextBlogPost );

   if ( $rowContainingPreviousBlogPost != NULL ) {
?>
               <a href="blog.php?previousButton&category=<?php echo $_GET['category'] ?>&idOfRequiredPost=<?php echo $rowContainingPreviousBlogPost['blog_post_id'] ?>" id="specialButtonFloatingToTheLeft">&lt;&lt; Previous Post: <?php echo $rowContainingPreviousBlogPost['blog_post_caption'] ?></a>
<?php
   }

   if ( $rowContainingNextBlogPost != NULL ) {
?>
               <a href="blog.php?nextButton&category=<?php echo $_GET['category'] ?>&idOfRequiredPost=<?php echo $rowContainingNextBlogPost['blog_post_id'] ?>" id="specialButtonFloatingToTheRight">Next Post: <?php echo $rowContainingNextBlogPost['blog_post_caption'] ?> &gt;&gt;</a>
<?php
   }
}

$query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingBlogPostToBeDisplayed['user_id_of_poster'];
$resultContainingDataAboutPoster = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingDataAboutPoster = mysqli_fetch_assoc( $resultContainingDataAboutPoster );
?>
               <p id="notFloating"></p>
               <h1 id="mediumSizedText"><?php echo $rowContainingBlogPostToBeDisplayed['blog_post_caption'] ?></h1>
               <p id="tinySizedText">Posted by <?php echo ucwords( $rowContainingDataAboutPoster['firstname'] ) . ( isMainBloggerForThisCategory( $rowContainingBlogPostToBeDisplayed['user_id_of_poster'], $rowContainingBlogCategory['blog_category_id'] ) ? ' (RoarConnect Special Blogger)' : '' ) ?> on <?php echo $rowContainingBlogPostToBeDisplayed['month_of_posting'] . ' ' . $rowContainingBlogPostToBeDisplayed['day_of_posting'] ?></p>
<?php
if ( $rowContainingBlogPostToBeDisplayed['blog_post_image_filename'] != NULL ) {
?>
               <div><img src="images/<?php echo 'ImagesFor' . $_GET['category'] . 'Updates/' . $rowContainingBlogPostToBeDisplayed['blog_post_image_filename'] ?>" alt="<?php echo 'Image of ' . $rowContainingBlogPostToBeDisplayed['blog_post_caption'] ?>" style="max-height: 300px; max-width: 80%;" /></div>
<?php
}
?>
               <div><?php echo $rowContainingBlogPostToBeDisplayed['blog_post_text'] ?></div>

<?php
if ( loggin() ) {
?>               
               <!-- Like Button -->
               <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLikeOrUnlikeButton">
<?php
$query = 'SELECT like_id FROM likes_to_blog_posts WHERE blog_post_id = ' . $rowContainingBlogPostToBeDisplayed['blog_post_id'] . ' AND user_id_of_liker = ' . $_SESSION['user_id'];
$resultContainingLikeByUser = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$userAlreadyLikesThisBlogPost = mysqli_num_rows( $resultContainingLikeByUser ) != 0;
$userDoesNotYetLikeThisBlogPost = !$userAlreadyLikesThisBlogPost;

$query = 'SELECT like_id FROM likes_to_blog_posts WHERE blog_post_id = ' . $rowContainingBlogPostToBeDisplayed['blog_post_id'];
$resultContainingAllLikesToBlogPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$numberOfUsersWhoLikeThisBlogPost = mysqli_num_rows( $resultContainingAllLikesToBlogPost );
?>
                  <input type="hidden" name="idOfPostToBeAssociatedWithLike" value="<?php echo $rowContainingBlogPostToBeDisplayed['blog_post_id'] ?>" />
                  <button type="submit" name="<?php echo $userAlreadyLikesThisBlogPost ? 'unlikeButton' : 'likeButton' ?>" class="btn btn-sm <?php echo $userAlreadyLikesThisBlogPost ? 'btn-primary' : 'btn-default' ?>"><img src="images/icons/likeIcon.jpg" /> <?php echo $userAlreadyLikesThisBlogPost ? 'Unlike' : 'Like' ?></button>

                  <span id="tinySizedText">
<?php
if ( $numberOfUsersWhoLikeThisBlogPost == 1 && $userAlreadyLikesThisBlogPost ) {
   echo 'You like this post.';
}
else if ( $numberOfUsersWhoLikeThisBlogPost == 1 && $userDoesNotYetLikeThisBlogPost ) {
   echo '1 person likes this post.';
}
else if ( $numberOfUsersWhoLikeThisBlogPost > 1 && $userAlreadyLikesThisBlogPost ) {
   echo 'You and ' . ( ( $numberOfUsersWhoLikeThisBlogPost - 1 ) == 1 ? '1 other person' : ( $numberOfUsersWhoLikeThisBlogPost - 1 ) . ' other people' ) . ' like this post.';
}
else if ( $numberOfUsersWhoLikeThisBlogPost > 1 && $userDoesNotYetLikeThisBlogPost ) {
   echo $numberOfUsersWhoLikeThisBlogPost . ' people like this post.';
}
?>

                  </span>
               </form>
                  
               <!-- Love Button -->
               <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLoveOrUnloveButton">
<?php
$query = 'SELECT love_id FROM loves_to_blog_posts WHERE blog_post_id = ' . $rowContainingBlogPostToBeDisplayed['blog_post_id'] . ' AND user_id_of_lover = ' . $_SESSION['user_id'];
$resultContainingLoveByUser = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$userAlreadyLovesThisBlogPost = mysqli_num_rows( $resultContainingLoveByUser ) != 0;
$userDoesNotYetLoveThisBlogPost = !$userAlreadyLovesThisBlogPost;

$query = 'SELECT love_id FROM loves_to_blog_posts WHERE blog_post_id = ' . $rowContainingBlogPostToBeDisplayed['blog_post_id'];
$resultContainingAllLovesToBlogPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$numberOfUsersWhoLoveThisBlogPost = mysqli_num_rows( $resultContainingAllLovesToBlogPost );
?>
                  <input type="hidden" name="idOfPostToBeAssociatedWithLove" value="<?php echo $rowContainingBlogPostToBeDisplayed['blog_post_id'] ?>" />
                  <button type="submit" name="<?php echo $userAlreadyLovesThisBlogPost ? 'unloveButton' : 'loveButton' ?>" class="btn btn-sm <?php echo $userAlreadyLovesThisBlogPost ? 'btn-danger' : 'btn-default' ?>"><img src="images/icons/loveIcon.jpg" /> <?php echo $userAlreadyLovesThisBlogPost ? 'Unlove' : 'Love' ?></button>

                  <span id="tinySizedText">
<?php
if ( $numberOfUsersWhoLoveThisBlogPost == 1 && $userAlreadyLovesThisBlogPost ) {
   echo 'You love this post.';
}
else if ( $numberOfUsersWhoLoveThisBlogPost == 1 && $userDoesNotYetLoveThisBlogPost ) {
   echo '1 person loves this post.';
}
else if ( $numberOfUsersWhoLoveThisBlogPost > 1 && $userAlreadyLovesThisBlogPost ) {
   echo 'You and ' . ( ( $numberOfUsersWhoLoveThisBlogPost - 1 ) == 1 ? '1 other person' : ( $numberOfUsersWhoLoveThisBlogPost - 1 ) . ' other people' ) . ' love this post.';
}
else if ( $numberOfUsersWhoLoveThisBlogPost > 1 && $userDoesNotYetLoveThisBlogPost ) {
   echo $numberOfUsersWhoLoveThisBlogPost . ' people love this post.';
}
?>

                  </span>
               </form>
               
               <!-- Comment Box -->
               <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" class="form-inline text-center" id="searchBar">
                  <input type="hidden" name="idOfPostToBeAssociatedWithComment" value="<?php echo $rowContainingBlogPostToBeDisplayed['blog_post_id'] ?>" />
                  <input type="text" name="commentText" placeholder="Write a comment..." maxlength="1000" class="form-control" />
                  <button type="submit" name="commentButton" class="btn btn-primary">Comment</button>
               </form>
<?php
}
?>
            </section>
			 
<?php
$query = 'SELECT comment_id, comment_text, user_id_of_commenter, MONTHNAME( time_of_commenting ) AS month_of_commenting, DAYOFMONTH( time_of_commenting ) AS day_of_commenting, HOUR( time_of_commenting ) AS hour_of_commenting, MINUTE( time_of_commenting ) AS minute_of_commenting FROM comments_to_blog_posts WHERE blog_post_id = ' . $rowContainingBlogPostToBeDisplayed['blog_post_id'] . ' ORDER BY time_of_commenting';
$resultContainingComments = mysqli_query( $db, $query );

if ( mysqli_num_rows( $resultContainingComments ) > 0 ) {
?>
            <section>
               <h3>Comments on this post:</h3>
			
               <div>
<?php
	$rowContainingComments = mysqli_fetch_assoc( $resultContainingComments );

	while ( $rowContainingComments != NULL ) {
		$query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingComments['user_id_of_commenter'];
		$resultContainingDataAboutCommenter = mysqli_query( $db, $query );
		$rowContainingDataAboutCommenter = mysqli_fetch_assoc( $resultContainingDataAboutCommenter );
?>
                  <div id ="<?php echo $rowContainingComments['user_id_of_commenter'] == $_SESSION['user_id'] ? 'containerHoldingCommentBoxFloatedToTheRight' : 'containerHoldingCommentBoxFloatedToTheLeft' ?>">
                     <div id ="<?php echo $rowContainingComments['user_id_of_commenter'] == $_SESSION['user_id'] ? 'commentBoxFloatedToTheRight' : 'commentBoxFloatedToTheLeft' ?>">
                        <p id="<?php echo $rowContainingComments['comment_id'] ?>"><?php echo ucwords( $rowContainingDataAboutCommenter['firstname'] ) . ',' ?> <span id="tinySizedText"><?php echo 'at ' . formatTimeAsAmOrPm( $rowContainingComments['hour_of_commenting'] + 5, $rowContainingComments['minute_of_commenting'] ) . ' on ' . $rowContainingComments['month_of_commenting'] . ' ' . $rowContainingComments['day_of_commenting'] ?></span></p>
                        <p><?php echo $rowContainingComments['comment_text'] ?></p>
                     </div>
                  </div>

<?php
		$rowContainingComments = mysqli_fetch_assoc( $resultContainingComments );
	}
?>
               </div>
			</section>
<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>