<?php
require_once 'includes/utilityFunctions.php';
require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

if ( !isset( $_GET['i'] ) ) {
   header('Location: index.php');
}

if ( isset( $_GET['i'] ) && !consistsOfOnlyDigits( $_GET['i'] ) ) {
	header( 'Location: index.php' );
}


if ( userHasPressedLikeButton() ) {
   indicateThatUserLikesCurrentPost();
}
else if ( userHasPressedUnlikeButton() ) {
   indicateThatUserDoesNotLikeCurrentPost();
}
else if ( userHasPressedLoveButton() ) {
   indicateThatUserLovesCurrentPost();
}
else if ( userHasPressedUnloveButton() ) {
   indicateThatUserDoesNotLoveCurrentPost();
}
else if (userHasPostedComment()) {
   insertCommentIntoDatabase();
}

$query = 'SELECT *, MONTHNAME( blog_post_time_of_posting ) AS month_of_posting, DAYOFMONTH( blog_post_time_of_posting ) AS day_of_posting, YEAR( blog_post_time_of_posting ) AS year_of_posting FROM blog_posts WHERE blog_post_id = "' . $_GET['i'] . '" AND blog_post_approval_status = "APPROVED"';
$resultContainingPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingPost = mysqli_fetch_assoc( $resultContainingPost );

if ( $rowContainingPost == NULL ) {
	header('Location: blog_home.php');
}

displayMarkupsCommonToTopOfPages( $rowContainingPost['blog_post_caption'], DISPLAY_NAVIGATION_MENU );
displayMarkupForSearchBar('search_for_blog_post.php', 'Search news and gists');	 
?>

            <div id="blogBodyContainer">
<?php
// Get previous blog post from database
$query = 'SELECT blog_post_id, blog_post_caption FROM blog_posts WHERE blog_post_time_of_posting > "' . $rowContainingPost['blog_post_time_of_posting'] . '" AND blog_category_id = ' . $rowContainingPost['blog_category_id'] . ' AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting ASC LIMIT 1';
$resultContainingPreviousPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure . $query );
$rowContainingPreviousPost = mysqli_fetch_assoc( $resultContainingPreviousPost );

// Get next blog post from database
$query = 'SELECT blog_post_id, blog_post_caption FROM blog_posts WHERE blog_post_time_of_posting < "' . $rowContainingPost['blog_post_time_of_posting'] . '" AND blog_category_id = ' . $rowContainingPost['blog_category_id'] . ' AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting DESC LIMIT 1';
$resultContainingNextPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingNextPost = mysqli_fetch_assoc( $resultContainingNextPost );
?>
               <section>
<?php
if ( $rowContainingPreviousPost != NULL ) {
?>
                  <a href="blog.php?previousButton&i=<?php echo $rowContainingPreviousPost['blog_post_id'] ?>" id="specialButtonFloatingToTheLeft"><span class="fa fa-angle-double-left"></span> Previous Post: <?php echo $rowContainingPreviousPost['blog_post_caption'] ?></a>
<?php
}

if ( $rowContainingNextPost != NULL ) {
?>
                  <a href="blog.php?nextButton&i=<?php echo $rowContainingNextPost['blog_post_id'] ?>" id="specialButtonFloatingToTheRight">Next Post: <?php echo $rowContainingNextPost['blog_post_caption'] ?> <span class="fa fa-angle-double-right"></span></a>
<?php
}
?>
               </section>
<?php
$query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingPost['user_id_of_poster'];
$resultContainingDataAboutPoster = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingDataAboutPoster = mysqli_fetch_assoc( $resultContainingDataAboutPoster );
?>

               <section id="notFloating">
                  <h1 id="blogBodyCaption"><?php echo $rowContainingPost['blog_post_caption'] ?></h1>
                  <p id="blogBodySubdetails">Posted by <?php echo ucwords( $rowContainingDataAboutPoster['firstname'] ) . ( isMainBloggerForThisCategory( $rowContainingPost['user_id_of_poster'], $rowContainingPost['blog_category_id'] ) ? ' (RoarConnect Special Blogger)' : '' ) ?> on <?php echo $rowContainingPost['month_of_posting'] . ' ' . $rowContainingPost['day_of_posting'] . ', ' . $rowContainingPost['year_of_posting'] ?></p>
                  <img src="images/blogImages/<?php echo $rowContainingPost['blog_post_image_filename'] ?>" class="blogBodyImage" style="max-height: 300px; max-width: 80%;" />
               </section>

               <section id="blogBodyDetails">
                  <?php echo $rowContainingPost['blog_post_text'] ?>
               </section>

               <section>
<?php
$query = 'SELECT * FROM views_to_blog_posts WHERE user_id_of_viewer = "' . $_SESSION['user_id'] . '" AND blog_post_id = ' . $rowContainingPost['blog_post_id'];
$resultContainingViewsToPost = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure.$query);

if (mysqli_num_rows($resultContainingViewsToPost) == 0) {
   $query = 'INSERT INTO views_to_blog_posts (user_id_of_viewer, blog_post_id) VALUES ("' . $_SESSION['user_id'] . '", "' . $rowContainingPost['blog_post_id'] . '")';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure.$query);
   $query = 'UPDATE blog_posts SET blog_post_number_of_views = blog_post_number_of_views + 1 WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'];
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure.$query);
   $query = 'UPDATE blog_posts SET blog_post_inherent_relevance = (TIMESTAMPDIFF(minute, "2018-01-01 00:00:00", blog_post_time_of_posting) + blog_post_number_of_views) WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'];
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure.$query);
}
$query = 'SELECT like_id FROM likes_to_blog_posts WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'] . ' AND user_id_of_liker = ' . $_SESSION['user_id'];
$resultContainingLikeByUser = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$userAlreadyLikesThisPost = mysqli_num_rows( $resultContainingLikeByUser ) != 0;
$userDoesNotYetLikeThisPost = !$userAlreadyLikesThisPost;
?>
                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLikeOrUnlikeButton">
                     <button type="submit" name="<?php echo $userAlreadyLikesThisPost ? 'unlikeButton' : 'likeButton' ?>" class="btn btn-sm <?php echo $userAlreadyLikesThisPost ? 'btn-primary' : 'btn-default' ?>"><?php echo $userAlreadyLikesThisPost ? '<span class="glyphicon glyphicon-thumbs-down"></span> Unlike' : '<span class="glyphicon glyphicon-thumbs-up"></span> Like' ?></button>

                     <span id="tinySizedText">
<?php
$query = 'SELECT like_id FROM likes_to_blog_posts WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'];
$resultContainingAllLikesToPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$numberOfUsersWhoLikeThisPost = mysqli_num_rows( $resultContainingAllLikesToPost );

if ( $numberOfUsersWhoLikeThisPost == 1 && $userAlreadyLikesThisPost ) {
   echo 'You like this post.';
}
else if ( $numberOfUsersWhoLikeThisPost == 1 && $userDoesNotYetLikeThisPost ) {
   echo '1 person likes this post.';
}
else if ( $numberOfUsersWhoLikeThisPost > 1 && $userAlreadyLikesThisPost ) {
   echo 'You and ' . ( ( $numberOfUsersWhoLikeThisPost - 1 ) == 1 ? '1 other person' : ( $numberOfUsersWhoLikeThisPost - 1 ) . ' other people' ) . ' like this post.';
}
else if ( $numberOfUsersWhoLikeThisPost > 1 && $userDoesNotYetLikeThisPost ) {
   echo $numberOfUsersWhoLikeThisPost . ' people like this post.';
}
?>

                     </span>
                  </form>
<?php
$query = 'SELECT love_id FROM loves_to_blog_posts WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'] . ' AND user_id_of_lover = ' . $_SESSION['user_id'];
$resultContainingLoveByUser = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$userAlreadyLovesThisPost = mysqli_num_rows( $resultContainingLoveByUser ) != 0;
$userDoesNotYetLoveThisPost = !$userAlreadyLovesThisPost;
?>
                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLoveOrUnloveButton">
                     <button type="submit" name="<?php echo $userAlreadyLovesThisPost ? 'unloveButton' : 'loveButton' ?>" class="btn btn-sm <?php echo $userAlreadyLovesThisPost ? 'btn-danger' : 'btn-default' ?>"><?php echo $userAlreadyLovesThisPost ? '<span class="glyphicon glyphicon-heart"></span> Unlove' : '<span class="glyphicon glyphicon-heart-empty"></span> Love' ?></button>

                     <span id="tinySizedText">
<?php
$query = 'SELECT love_id FROM loves_to_blog_posts WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'];
$resultContainingAllLovesToPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$numberOfUsersWhoLoveThisPost = mysqli_num_rows( $resultContainingAllLovesToPost );

if ( $numberOfUsersWhoLoveThisPost == 1 && $userAlreadyLovesThisPost ) {
   echo 'You love this post.';
}
else if ( $numberOfUsersWhoLoveThisPost == 1 && $userDoesNotYetLoveThisPost ) {
   echo '1 person loves this post.';
}
else if ( $numberOfUsersWhoLoveThisPost > 1 && $userAlreadyLovesThisPost ) {
   echo 'You and ' . ( ( $numberOfUsersWhoLoveThisPost - 1 ) == 1 ? '1 other person' : ( $numberOfUsersWhoLoveThisPost - 1 ) . ' other people' ) . ' love this post.';
}
else if ( $numberOfUsersWhoLoveThisPost > 1 && $userDoesNotYetLoveThisPost ) {
   echo $numberOfUsersWhoLoveThisPost . ' people love this post.';
}
?>
                     </span>
                  </form>
               </section>
<?php
$query = 'SELECT comment_id, comment_text, user_id_of_commenter, firstname_of_commenter, MONTHNAME( time_of_commenting ) AS month_of_commenting, DAYOFMONTH( time_of_commenting ) AS day_of_commenting, YEAR( time_of_commenting ) AS year_of_commenting, HOUR( time_of_commenting ) AS hour_of_commenting, MINUTE( time_of_commenting ) AS minute_of_commenting FROM comments_to_blog_posts WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'] . ' ORDER BY time_of_commenting';
$resultContainingComments = mysqli_query( $db, $query );
$numberOfComments = mysqli_num_rows( $resultContainingComments );
?>

               <section id="commentArea">
                  <h3>
                     <?php echo $numberOfComments == 1 ? '1 Comment' : ($numberOfComments . ' Comments') ?>
<?php
if (isset($_GET['viewComments'])) {
   echo ' <a href="blog.php?i=' . $_GET['i'] . '#commentArea" class="btn btn-link"><span class="glyphicon glyphicon-eye-close"></span> Hide</a>';
}
else if ($numberOfComments > 0) {
   echo ' <a href="blog.php?i=' . $_GET['i'] . '&viewComments=1#commentArea" class="btn btn-link"><span class="glyphicon glyphicon-eye-open"></span> View</a>';
}
?>

                  </h3>

<?php
if (isset($_GET['viewComments'])) {
	$rowContainingComments = mysqli_fetch_assoc( $resultContainingComments );
	while ( $rowContainingComments != NULL ) {
?>
                  <div id ="<?php echo $rowContainingComments['user_id_of_commenter'] == $_SESSION['user_id'] ? 'containerHoldingCommentBoxFloatedToTheRight' : 'containerHoldingCommentBoxFloatedToTheLeft' ?>">
                     <div id ="<?php echo $rowContainingComments['user_id_of_commenter'] == $_SESSION['user_id'] ? 'commentBoxFloatedToTheRight' : 'commentBoxFloatedToTheLeft' ?>">
                        <p id="<?php echo $rowContainingComments['comment_id'] ?>"><?php echo ucwords( $rowContainingComments['firstname_of_commenter'] ) . ',' ?> <span id="tinySizedText"><?php echo 'at ' . formatTimeAsAmOrPm( $rowContainingComments['hour_of_commenting'] + 5, $rowContainingComments['minute_of_commenting'] ) . ' on ' . $rowContainingComments['month_of_commenting'] . ' ' . $rowContainingComments['day_of_commenting'] ?></span></p>
                        <p><?php echo $rowContainingComments['comment_text'] ?></p>
                     </div>
                  </div>

<?php
		$rowContainingComments = mysqli_fetch_assoc( $resultContainingComments );
	}
}
?>
                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" class="<?php echo userIsLoggedIn() ? 'form-inline' : '' ?>" id="commentBox">
<?php
if (userIsLoggedIn()) {
?>
                     <input type="hidden" name="nameOfCommenter" value="<?php echo getFirstNameOfUser() ?>" />
<?php
}
else {
?>
                     <h4>Post Your Comment</h4>
                     <div class="form-group"><input type="text" name="nameOfCommenter" placeholder="Enter your first name" maxlength="20" class="form-control"/></div>
<?php
}
?>
                     <div class="form-group"><input type="text" name="commentText" placeholder="Enter your comment" maxlength="1000" class="form-control" /></div>
                     <button type="submit" name="commentButton" class="btn btn-default">Post</button>
                  </form>
               </section>
            </div>

            <div>
               <h2>Related Gists</h2>
<?php
$query = 'SELECT blog_tag_id FROM relationship_between_tags_and_blog_posts WHERE blog_post_id = ' . $rowContainingPost['blog_post_id'];
$resultContainingTagIdOfPost = mysqli_query($db, $query);
$numberOfTagsOfPost = mysqli_num_rows($resultContainingTagIdOfPost);

$query = 'SELECT blog_posts.blog_post_id, blog_posts.blog_post_image_filename, blog_posts.blog_post_caption
   FROM relationship_between_tags_and_blog_posts INNER JOIN blog_posts ON relationship_between_tags_and_blog_posts.blog_post_id = blog_posts.blog_post_id
   WHERE blog_posts.blog_post_approval_status = "APPROVED" AND (0';
for ($rowContainingTagIdOfPost = mysqli_fetch_assoc($resultContainingTagIdOfPost); $rowContainingTagIdOfPost != NULL; $rowContainingTagIdOfPost = mysqli_fetch_assoc($resultContainingTagIdOfPost)) {
   $query .= ' OR relationship_between_tags_and_blog_posts.blog_tag_id = ' . $rowContainingTagIdOfPost['blog_tag_id'];
}

$query .= ') ORDER BY blog_posts.blog_post_inherent_relevance DESC LIMIT 0, ' . ($numberOfTagsOfPost * 4);
$idOfLastDisplayedPost = 0;
$numberOfPostsDisplayedSoFar = 0;
$resultContainingRelatedPosts = mysqli_query($db, $query);
$rowContainingRelatedPost = mysqli_fetch_assoc($resultContainingRelatedPosts);
while ($numberOfPostsDisplayedSoFar < 4 && $rowContainingRelatedPost != NULL) {
   if ($rowContainingRelatedPost['blog_post_id'] != $rowContainingPost['blog_post_id'] && $rowContainingRelatedPost['blog_post_id'] != $idOfLastDisplayedPost) {
?>
            <a href="blog.php?i=<?php echo $rowContainingRelatedPost['blog_post_id'] ?>">
               <img src="images/blogImages/<?php echo $rowContainingRelatedPost['blog_post_image_filename'] ?>" />
               <h3><?php echo $rowContainingRelatedPost['blog_post_caption'] ?></h3>
            </a>
<?php
      $idOfLastDisplayedPost = $rowContainingRelatedPost['blog_post_id'];
      ++$numberOfPostsDisplayedSoFar;
   }

   $rowContainingRelatedPost = mysqli_fetch_assoc($resultContainingRelatedPosts);
}
?>
            </div>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );


function userHasPressedLikeButton()
{
   return isset( $_POST['likeButton'] );
}


function indicateThatUserLikesCurrentPost()
{
   global $db;
	$query = 'INSERT INTO likes_to_blog_posts ( blog_post_id, user_id_of_liker ) VALUES ( ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ' )';
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLikeOrUnlikeButton' );
}


function userHasPressedUnlikeButton()
{
   return isset( $_POST['unlikeButton'] );
}


function indicateThatUserDoesNotLikeCurrentPost()
{
   global $db;
   $query = 'DELETE FROM likes_to_blog_posts WHERE blog_post_id = ' . $_GET['i'] . ' AND user_id_of_liker = ' . $_SESSION['user_id'];
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLikeOrUnlikeButton' );
}


function userHasPressedLoveButton()
{
   return isset( $_POST['loveButton'] );
}


function indicateThatUserLovesCurrentPost()
{
   global $db;
	$query = 'INSERT INTO loves_to_blog_posts ( blog_post_id, user_id_of_lover ) VALUES ( ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ' )';
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLoveOrUnloveButton' );
}


function userHasPressedUnloveButton()
{
   return isset( $_POST['unloveButton'] );
}


function indicateThatUserDoesNotLoveCurrentPost()
{
   global $db;
   $query = 'DELETE FROM loves_to_blog_posts WHERE blog_post_id = ' . $_GET['i'] . ' AND user_id_of_lover = ' . $_SESSION['user_id'];
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLoveOrUnloveButton' );
}


function userHasPostedComment()
{
   return isset($_POST['commentButton']) && trim($_POST['commentText']) != '';
}


function insertCommentIntoDatabase()
{
   global $db;
   $firstnameOfCommenter = trim($_POST['nameOfCommenter']);

   if ($firstnameOfCommenter == '') {
      $query ='INSERT INTO comments_to_blog_posts ( comment_text, blog_post_id, user_id_of_commenter, time_of_commenting ) VALUES ( "' . mysqli_real_escape_string( $db, trim( $_POST['commentText'] ) ) . '", ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ', NOW() )';
   }
   else {
      $query ='INSERT INTO comments_to_blog_posts ( comment_text, blog_post_id, user_id_of_commenter, firstname_of_commenter, time_of_commenting ) VALUES ( "' . mysqli_real_escape_string( $db, trim( $_POST['commentText'] ) ) . '", ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ', "' . $firstnameOfCommenter . '", NOW() )';
   }

   mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   header( 'Location: ' . $_SERVER['PHP_SELF'] . '?' . buildStringContainingAllDataFromGET() . '#' . mysqli_insert_id( $db ) );
}
?>