<?php
require_once 'includes/generalHeaderFile.php';

/*
    In this file, $_GET['i'] holds the id of the current blog post
*/

if ( !isset( $_GET['i'] ) ) {
   header('Location: index.php');
}

if ( isset( $_GET['i'] ) && !consistsOfOnlyDigits( $_GET['i'] ) ) {
	header( 'Location: index.php' );
}


if ( userHasPressedLikeButton() ) {
   indicateThatUserLikesCurrentBlogPost();
}
else if ( userHasPressedUnlikeButton() ) {
   indicateThatUserDoesNotLikeCurrentBlogPost();
}
else if ( userHasPressedLoveButton() ) {
   indicateThatUserLovesCurrentBlogPost();
}
else if ( userHasPressedUnloveButton() ) {
   indicateThatUserDoesNotLoveCurrentBlogPost();
}
else if (userHasBlogPostedComment()) {
   insertCommentIntoDatabase();
}

$rowContainingCurrentBlogPost = getDataAboutApprovedBlogPost($_GET['i']);

if ( $rowContainingCurrentBlogPost == NULL ) {
	header('Location: blog_home.php');
}

displayMarkupsCommonToTopOfPages( $rowContainingCurrentBlogPost['blog_post_caption'], DISPLAY_NAVIGATION_MENU );
displayMarkupForSearchBar('search_for_blog_post.php', 'Search news and gists');

$rowContainingPreviousBlogPost = getDataAboutApprovedPreviousBlogPost($rowContainingCurrentBlogPost['blog_post_time_of_posting']);
$rowContainingNextBlogPost = getDataAboutApprovedNextBlogPost($rowContainingCurrentBlogPost['blog_post_time_of_posting']);
?>
            <div id="blogBodyContainer">
               <section>
<?php
if ( $rowContainingPreviousBlogPost != NULL ) {
?>
                  <a href="blog.php?previousButton&i=<?php echo $rowContainingPreviousBlogPost['blog_post_id'] ?>" id="specialButtonFloatingToTheLeft"><span class="fa fa-angle-double-left"></span> Previous Gist: <?php echo $rowContainingPreviousBlogPost['blog_post_caption'] ?></a>
<?php
}

if ( $rowContainingNextBlogPost != NULL ) {
?>
                  <a href="blog.php?nextButton&i=<?php echo $rowContainingNextBlogPost['blog_post_id'] ?>" id="specialButtonFloatingToTheRight">Next Gist: <?php echo $rowContainingNextBlogPost['blog_post_caption'] ?> <span class="fa fa-angle-double-right"></span></a>
<?php
}
?>
               </section>

               <section id="notFloating">
                  <h1 id="blogBodyCaption"><?php echo $rowContainingCurrentBlogPost['blog_post_caption'] ?></h1>
                  <p id="blogBodySubdetails">Posted by <?php echo getFirstNameAssociatedWithUserId($rowContainingCurrentBlogPost['user_id_of_poster']) . ( isMainBloggerForThisCategory( $rowContainingCurrentBlogPost['user_id_of_poster'], $rowContainingCurrentBlogPost['blog_category_id'] ) ? ' (RoarConnect Special Blogger)' : '' ) ?> on <?php echo $rowContainingCurrentBlogPost['month_of_posting'] . ' ' . $rowContainingCurrentBlogPost['day_of_posting'] . ', ' . $rowContainingCurrentBlogPost['year_of_posting'] ?></p>
<?php
if ($rowContainingCurrentBlogPost['blog_post_image_filename'] != NULL) {
?>
                  <img src="images/blogImages/<?php echo $rowContainingCurrentBlogPost['blog_post_image_filename'] ?>" class="blogBodyImage" style="max-height: 300px; max-width: 80%;" />
<?php
}
?>
               </section>

               <section id="blogBodyDetails">
                  <?php echo $rowContainingCurrentBlogPost['blog_post_text'] ?>
               </section>
<?php
if (userHasNeverViewedCurrentBlogPost()) {
   indicateThatUserJustViewedCurrentBlogPost($rowContainingCurrentBlogPost['blog_category_id']);
}
?>

               <section>
                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLikeOrUnlikeButton">
<?php
if (userAlreadyLikesCurrentBlogPost()) {
?>
                     <button type="submit" name="unlikeButton" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-thumbs-down"></span> Unlike</button>
<?php
}
else {
?>
                     <button type="submit" name="likeButton" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-thumbs-up"></span> Like</button>
<?php
}
?>
                     <span id="tinySizedText"><?php echo getInformationAboutLikersOfCurrentPost() ?></span>
                  </form>
<?php
$query = 'SELECT love_id FROM loves_to_blog_posts WHERE blog_post_id = ' . $rowContainingCurrentBlogPost['blog_post_id'] . ' AND user_id_of_lover = ' . $_SESSION['user_id'];
$resultContainingLoveByUser = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$userAlreadyLovesThisBlogPost = mysqli_num_rows( $resultContainingLoveByUser ) != 0;
$userDoesNotYetLoveThisBlogPost = !$userAlreadyLovesThisBlogPost;
?>
                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLoveOrUnloveButton">
                     <button type="submit" name="<?php echo $userAlreadyLovesThisBlogPost ? 'unloveButton' : 'loveButton' ?>" class="btn btn-sm <?php echo $userAlreadyLovesThisBlogPost ? 'btn-danger' : 'btn-default' ?>"><?php echo $userAlreadyLovesThisBlogPost ? '<span class="glyphicon glyphicon-heart"></span> Unlove' : '<span class="glyphicon glyphicon-heart-empty"></span> Love' ?></button>

                     <span id="tinySizedText">
<?php
$query = 'SELECT love_id FROM loves_to_blog_posts WHERE blog_post_id = ' . $rowContainingCurrentBlogPost['blog_post_id'];
$resultContainingAllLovesToBlogPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$numberOfUsersWhoLoveThisBlogPost = mysqli_num_rows( $resultContainingAllLovesToBlogPost );

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
               </section>
<?php
$query = 'SELECT comment_id, comment_text, user_id_of_commenter, firstname_of_commenter, MONTHNAME( time_of_commenting ) AS month_of_commenting, DAYOFMONTH( time_of_commenting ) AS day_of_commenting, YEAR( time_of_commenting ) AS year_of_commenting, HOUR( time_of_commenting ) AS hour_of_commenting, MINUTE( time_of_commenting ) AS minute_of_commenting FROM comments_to_blog_posts WHERE blog_post_id = ' . $rowContainingCurrentBlogPost['blog_post_id'] . ' ORDER BY time_of_commenting';
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
                     <h4>BlogPost Your Comment</h4>
                     <div class="form-group"><input type="text" name="nameOfCommenter" placeholder="Enter your first name" maxlength="20" class="form-control"/></div>
<?php
}
?>
                     <div class="form-group"><input type="text" name="commentText" placeholder="Enter your comment" maxlength="1000" class="form-control" /></div>
                     <button type="submit" name="commentButton" class="btn btn-default">BlogPost</button>
                  </form>
               </section>
            </div>

            <div>
               <h2>Related Gists</h2>
<?php
$query = 'SELECT blog_tag_id FROM relationship_between_tags_and_blog_posts WHERE blog_post_id = ' . $rowContainingCurrentBlogPost['blog_post_id'];
$resultContainingTagIdOfBlogPost = mysqli_query($db, $query);
$numberOfTagsOfBlogPost = mysqli_num_rows($resultContainingTagIdOfBlogPost);

$query = 'SELECT blog_posts.blog_post_id, blog_posts.blog_post_image_filename, blog_posts.blog_post_caption
   FROM relationship_between_tags_and_blog_posts INNER JOIN blog_posts ON relationship_between_tags_and_blog_posts.blog_post_id = blog_posts.blog_post_id
   WHERE blog_posts.blog_post_approval_status = "APPROVED" AND (0';
for ($rowContainingTagIdOfBlogPost = mysqli_fetch_assoc($resultContainingTagIdOfBlogPost); $rowContainingTagIdOfBlogPost != NULL; $rowContainingTagIdOfBlogPost = mysqli_fetch_assoc($resultContainingTagIdOfBlogPost)) {
   $query .= ' OR relationship_between_tags_and_blog_posts.blog_tag_id = ' . $rowContainingTagIdOfBlogPost['blog_tag_id'];
}

$query .= ') ORDER BY blog_posts.blog_post_inherent_relevance DESC LIMIT 0, ' . ($numberOfTagsOfBlogPost * 4);
$idOfLastDisplayedBlogPost = 0;
$numberOfBlogPostsDisplayedSoFar = 0;
$resultContainingRelatedBlogPosts = mysqli_query($db, $query);
$rowContainingRelatedBlogPost = mysqli_fetch_assoc($resultContainingRelatedBlogPosts);
while ($numberOfBlogPostsDisplayedSoFar < 4 && $rowContainingRelatedBlogPost != NULL) {
   if ($rowContainingRelatedBlogPost['blog_post_id'] != $rowContainingCurrentBlogPost['blog_post_id'] && $rowContainingRelatedBlogPost['blog_post_id'] != $idOfLastDisplayedBlogPost) {
?>
            <a href="blog.php?i=<?php echo $rowContainingRelatedBlogPost['blog_post_id'] ?>">
               <img src="images/blogImages/<?php echo $rowContainingRelatedBlogPost['blog_post_image_filename'] ?>" />
               <h3><?php echo $rowContainingRelatedBlogPost['blog_post_caption'] ?></h3>
            </a>
<?php
      $idOfLastDisplayedBlogPost = $rowContainingRelatedBlogPost['blog_post_id'];
      ++$numberOfBlogPostsDisplayedSoFar;
   }

   $rowContainingRelatedBlogPost = mysqli_fetch_assoc($resultContainingRelatedBlogPosts);
}
?>
            </div>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );


function userHasPressedLikeButton()
{
   return isset( $_POST['likeButton'] );
}


function indicateThatUserLikesCurrentBlogPost()
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


function indicateThatUserDoesNotLikeCurrentBlogPost()
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


function indicateThatUserLovesCurrentBlogPost()
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


function indicateThatUserDoesNotLoveCurrentBlogPost()
{
   global $db;
   $query = 'DELETE FROM loves_to_blog_posts WHERE blog_post_id = ' . $_GET['i'] . ' AND user_id_of_lover = ' . $_SESSION['user_id'];
	mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLoveOrUnloveButton' );
}


function userHasBlogPostedComment()
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


function getDataAboutApprovedPreviousBlogPost($timeOfPostingOfCurrentBlogPost)
{
   global $db;
   $query = 'SELECT blog_post_id, blog_post_caption FROM blog_posts WHERE blog_post_time_of_posting > "' . $timeOfPostingOfCurrentBlogPost . '" AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting ASC LIMIT 1';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   return mysqli_fetch_assoc( $result );
}


function getDataAboutApprovedNextBlogPost($timeOfPostingOfCurrentBlogPost)
{
   global $db;
   $query = 'SELECT blog_post_id, blog_post_caption FROM blog_posts WHERE blog_post_time_of_posting < "' . $timeOfPostingOfCurrentBlogPost . '" AND blog_post_approval_status = "APPROVED" ORDER BY blog_post_time_of_posting DESC LIMIT 1';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   return mysqli_fetch_assoc( $result );
}


function userHasNeverViewedCurrentBlogPost()
{
   return userHasNeverViewedThisBlogPost($_GET['i']);
}


function indicateThatUserJustViewedCurrentBlogPost($idOfCurrentBlogCategory)
{
   insertDatabaseEntryAboutViewToCurrentBlogPost();
   updateNumberOfViewsAndInherentRelevanceOfCurrentBlogPost();
   insertDatabaseEntryAboutViewToThisBlogCategory($idOfCurrentBlogCategory);
}


function insertDatabaseEntryAboutViewToCurrentBlogPost()
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $query = 'INSERT INTO views_to_blog_posts (user_id_of_viewer, blog_post_id) VALUES ("' . $_SESSION['user_id'] . '", "' . $_GET['i'] . '")';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function updateNumberOfViewsAndInherentRelevanceOfCurrentBlogPost()
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $query = 'UPDATE blog_posts SET blog_post_number_of_views = (blog_post_number_of_views + 1), blog_post_inherent_relevance = (TIMESTAMPDIFF(minute, "2018-01-01 00:00:00", blog_post_time_of_posting) + (60 * blog_post_number_of_views)) WHERE blog_post_id = "' . $_GET['i'] . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function insertDatabaseEntryAboutViewToThisBlogCategory($blogCategoryId)
{
   global $db, $markupIndicatingDatabaseQueryFailure;

   if (userHasNeverViewedThisCategoryOfBlogPosts($blogCategoryId)) {
      $query = 'INSERT INTO views_to_blog_categories (blog_category_id, user_id_of_viewer) VALUES ("' . $blogCategoryId . '", "' . $_SESSION['user_id'] . '")';
   }
   else {
      $query = 'UPDATE views_to_blog_categories SET number_of_blog_posts_viewed = number_of_blog_posts_viewed + 1 WHERE blog_category_id = "' . $blogCategoryId . '"';
   }

   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function userHasNeverViewedThisCategoryOfBlogPosts($blogCategoryId)
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $query = 'SELECT blog_category_id FROM views_to_blog_categories WHERE blog_category_id = "' . $blogCategoryId . '" AND user_id_of_viewer = "' . $_SESSION['user_id'] . '"';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   return mysqli_num_rows($result) == 0; 
}


function userAlreadyLikesCurrentBlogPost()
{
    global $db, $markupIndicatingDatabaseQueryFailure;
    $query = 'SELECT like_id FROM likes_to_blog_posts WHERE blog_post_id = "' . $_GET['i'] . '" AND user_id_of_liker = "' . $_SESSION['user_id'] . '"';
    $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
    return mysqli_num_rows( $result ) != 0;
}


function getInformationAboutLikersOfCurrentPost()
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $query = 'SELECT like_id FROM likes_to_blog_posts WHERE blog_post_id = "' . $_GET['i'] . '"';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $numberOfLikersToCurrentPost = mysqli_num_rows( $result );

   if (userAlreadyLikesCurrentBlogPost()) {
      if ($numberOfLikersToCurrentPost == 1) {
         return 'You like this post.';
      }
      else if ($numberOfLikersToCurrentPost > 1) {
         return 'You and ' . (($numberOfLikersToCurrentPost - 1 == 1) ? ('1 other person') : (($numberOfLikersToCurrentPost - 1) . ' other people')) . ' like this post.';
      }
   }
   else {
      if ($numberOfLikersToCurrentPost == 1) {
         return '1 person likes this post.';
      }
      else if ($numberOfLikersToCurrentPost > 1) {
         return $numberOfLikersToCurrentPost . ' people like this post.';
      }
   }
}
?>