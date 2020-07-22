<?php
/*
   NB: In this file, $_GET['i'] holds the id of the current blog post
   TODO: The code in this file will need to be extensively refactored to maximise its efficiency
*/
require_once 'includes/generalHeaderFile.php';
define('MAXIMUM_NUMBER_OF_RELATED_BLOG_POSTS_TO_DISPLAY', 6);

if ( !isset( $_GET['i'] ) || !consistsOfOnlyDigits( $_GET['i'] ) ) {
   header('Location: blog_home.php');
}

if ( currentUserPressedLikeButton() ) {
   indicateThatCurrentUserLikesCurrentBlogPost();
}
else if ( currentUserPressedUnlikeButton() ) {
   indicateThatCurrentUserDoesNotLikeCurrentBlogPost();
}
else if ( currentUserPressedLoveButton() ) {
   indicateThatCurrentUserLovesCurrentBlogPost();
}
else if ( currentUserPressedUnloveButton() ) {
   indicateThatCurrentUserDoesNotLoveCurrentBlogPost();
}
else if ( currentUserPostedComment() ) {
   insertCommentOfCurrentUserIntoDatabase();
}

$rowContainingCurrentBlogPost = getDataAboutApprovedBlogPost($_GET['i']);

if ( $rowContainingCurrentBlogPost == NULL ) {
	header('Location: blog_home.php');
}

displayMarkupsCommonToTopOfPages( $rowContainingCurrentBlogPost['blog_post_caption'], DISPLAY_NAVIGATION_MENU );
displayMarkupForSearchBar('search_for_blog_post.php', 'Search news and gists');
?>
            <div id="blogBodyContainer">
               <section>
                  <h1 id="blogBodyCaption"><?php echo $rowContainingCurrentBlogPost['blog_post_caption'] ?></h1>
                  <p id="blogBodySubdetails">Posted by <?php echo getFirstNameOfUser($rowContainingCurrentBlogPost['user_id_of_poster']) . ( isMainBloggerForThisCategory( $rowContainingCurrentBlogPost['user_id_of_poster'], $rowContainingCurrentBlogPost['blog_category_id'] ) ? ' (RoarConnect Special Blogger)' : '' ) ?> on <?php echo $rowContainingCurrentBlogPost['month_of_posting'] . ' ' . $rowContainingCurrentBlogPost['day_of_posting'] . ', ' . $rowContainingCurrentBlogPost['year_of_posting'] ?></p>
<?php
if ($rowContainingCurrentBlogPost['blog_post_image_filename'] != NULL) {
?>
                  <img src="assets/images/blogImages//<?php echo $rowContainingCurrentBlogPost['blog_post_image_filename'] ?>" id="blogBodyImage" />
<?php
}
?>
               </section>

               <section id="blogBodyDetails">
                  <?php echo $rowContainingCurrentBlogPost['blog_post_text'] ?>
               </section>
<?php
$globalBlogPostsDisplayedInCurrentPage[] = $rowContainingCurrentBlogPost;

if (currentBlogPostHasNeverBeenViewedByCurrentUser()) {
   indicateThatCurrentUserJustViewedCurrentBlogPost($rowContainingCurrentBlogPost['blog_category_id']);
}
?>

               <section>
                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLikeOrUnlikeButton">
<?php
if (currentUserIsAlreadyPassionateAboutCurrentBlogPost('like')) {
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
                     <span id="tinySizedText"><?php echo getSummaryOfUsersWhoArePassionateAboutCurrentBlogPost('like') ?></span>
                  </form>

                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" id="containerHoldingLoveOrUnloveButton">
<?php
if (currentUserIsAlreadyPassionateAboutCurrentBlogPost('love')) {
?>
                     <button type="submit" name="unloveButton" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-heart"></span> Unlove</button>
<?php
}
else {
?>
                     <button type="submit" name="loveButton" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-heart-empty"></span> Love</button>
<?php
}
?>

                     <span id="tinySizedText"><?php echo getSummaryOfUsersWhoArePassionateAboutCurrentBlogPost('love') ?></span>
                  </form>
               </section>

               <section id="commentArea">
                  <h3 id="blogBodySectionHeading"><?php echo getCommentHeading() ?></h3>

<?php
if (isset($_GET['viewComments'])) {
   displayCommentsOfCurrentBlogPost();
}
?>
                  <form method="POST" action="blog.php?<?php echo buildStringContainingAllDataFromGET() ?>" class="<?php echo currentUserIsLoggedIn() ? 'form-inline text-center' : '' ?>" id="commentBox">
<?php
if (currentUserIsLoggedIn()) {
?>
                     <input type="hidden" name="nameOfCommenter" value="<?php echo getFirstNameOfCurrentUser() ?>" />
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
               <h2 class="col-sm-12" id="blogBodySectionHeading">Related Gists</h2>
<?php
displayBlogPostsThatAreRelatedToCurrentBlogPost();
?>
            </div>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );


function currentUserPressedLikeButton()
{
   return isset( $_POST['likeButton'] );
}


function indicateThatCurrentUserLikesCurrentBlogPost()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
	$query = 'INSERT INTO likes_to_blog_posts ( blog_post_id, user_id_of_liker ) VALUES ( ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ' )';
	mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLikeOrUnlikeButton' );
}


function currentUserPressedUnlikeButton()
{
   return isset( $_POST['unlikeButton'] );
}


function indicateThatCurrentUserDoesNotLikeCurrentBlogPost()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'DELETE FROM likes_to_blog_posts WHERE blog_post_id = ' . $_GET['i'] . ' AND user_id_of_liker = ' . $_SESSION['user_id'];
	mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLikeOrUnlikeButton' );
}


function currentUserPressedLoveButton()
{
   return isset( $_POST['loveButton'] );
}


function indicateThatCurrentUserLovesCurrentBlogPost()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
	$query = 'INSERT INTO loves_to_blog_posts ( blog_post_id, user_id_of_lover ) VALUES ( ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ' )';
	mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLoveOrUnloveButton' );
}


function currentUserPressedUnloveButton()
{
   return isset( $_POST['unloveButton'] );
}


function indicateThatCurrentUserDoesNotLoveCurrentBlogPost()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'DELETE FROM loves_to_blog_posts WHERE blog_post_id = ' . $_GET['i'] . ' AND user_id_of_lover = ' . $_SESSION['user_id'];
	mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
	header( 'Location: blog.php?' . buildStringContainingAllDataFromGET() . '#containerHoldingLoveOrUnloveButton' );
}


function currentUserPostedComment()
{
   return isset($_POST['commentButton']) && trim($_POST['commentText']) != '';
}


function insertCommentOfCurrentUserIntoDatabase()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $firstnameOfCommenter = trim($_POST['nameOfCommenter']);

   if ($firstnameOfCommenter == '') {
      $query ='INSERT INTO comments_to_blog_posts ( comment_text, blog_post_id, user_id_of_commenter, time_of_commenting ) VALUES ( "' . mysqli_real_escape_string( $globalHandleToDatabase, trim( $_POST['commentText'] ) ) . '", ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ', NOW() )';
   }
   else {
      $query ='INSERT INTO comments_to_blog_posts ( comment_text, blog_post_id, user_id_of_commenter, firstname_of_commenter, time_of_commenting ) VALUES ( "' . mysqli_real_escape_string( $globalHandleToDatabase, trim( $_POST['commentText'] ) ) . '", ' . $_GET['i'] . ', ' . $_SESSION['user_id']. ', "' . $firstnameOfCommenter . '", NOW() )';
   }

   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   header( 'Location: ' . $_SERVER['PHP_SELF'] . '?viewComments=1' . buildStringContainingAllDataFromGET() . '#' . mysqli_insert_id( $globalHandleToDatabase ) );
}


function currentBlogPostHasNeverBeenViewedByCurrentUser()
{
   return blogPostHasNeverBeenViewedByCurrentUser($_GET['i']);
}


function indicateThatCurrentUserJustViewedCurrentBlogPost($idOfCurrentBlogCategory)
{
   insertDatabaseEntryAboutCurrentBlogPostView();
   updateNumberOfViewsAndInherentRelevanceOfCurrentBlogPost();
   insertDatabaseEntryAboutBlogCategoryView($idOfCurrentBlogCategory);
}


function insertDatabaseEntryAboutCurrentBlogPostView()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'INSERT INTO views_to_blog_posts (user_id_of_viewer, blog_post_id, time_of_viewing) VALUES ("' . $_SESSION['user_id'] . '", "' . $_GET['i'] . '", NOW())';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup);
}


function updateNumberOfViewsAndInherentRelevanceOfCurrentBlogPost()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'UPDATE blog_posts SET blog_post_number_of_views = (blog_post_number_of_views + 1), blog_post_relevance = (TIMESTAMPDIFF(minute, "2018-01-01 00:00:00", blog_post_time_of_posting) + (60 * blog_post_number_of_views)) WHERE blog_post_id = "' . $_GET['i'] . '"';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup);
}


function insertDatabaseEntryAboutBlogCategoryView($blogCategoryId)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   if (blogPostCategoryHasNeverBeenViewedByCurrentUser($blogCategoryId)) {
      $query = 'INSERT INTO views_to_blog_categories (blog_category_id, user_id_of_viewer) VALUES ("' . $blogCategoryId . '", "' . $_SESSION['user_id'] . '")';
   }
   else {
      $query = 'UPDATE views_to_blog_categories SET number_of_blog_posts_viewed = number_of_blog_posts_viewed + 1 WHERE blog_category_id = "' . $blogCategoryId . '"';
   }

   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup);
}


function blogPostCategoryHasNeverBeenViewedByCurrentUser($blogCategoryId)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'SELECT blog_category_id FROM views_to_blog_categories WHERE blog_category_id = "' . $blogCategoryId . '" AND user_id_of_viewer = "' . $_SESSION['user_id'] . '"';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup);
   return mysqli_num_rows($result) == 0; 
}


function currentUserIsAlreadyPassionateAboutCurrentBlogPost($passionType)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'SELECT ' . $passionType . '_id FROM ' . $passionType . 's_to_blog_posts WHERE blog_post_id = "' . $_GET['i'] . '" AND user_id_of_' . $passionType . 'r = "' . $_SESSION['user_id'] . '"';
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   return mysqli_num_rows( $result ) != 0;
}


function getSummaryOfUsersWhoArePassionateAboutCurrentBlogPost($passionType)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'SELECT ' . $passionType . '_id FROM ' . $passionType . 's_to_blog_posts WHERE blog_post_id = "' . $_GET['i'] . '"';
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $numberOfPeopleWhoArePassionate = mysqli_num_rows( $result );

   if (currentUserIsAlreadyPassionateAboutCurrentBlogPost($passionType)) {
      if ($numberOfPeopleWhoArePassionate == 1) {
         return 'You ' . $passionType . ' this post.';
      }
      else if ($numberOfPeopleWhoArePassionate > 1) {
         return 'You and ' . (($numberOfPeopleWhoArePassionate - 1 == 1) ? ('1 other person') : (($numberOfPeopleWhoArePassionate - 1) . ' other people')) . ' ' . $passionType . ' this post.';
      }
   }
   else {
      if ($numberOfPeopleWhoArePassionate == 1) {
         return '1 person ' . $passionType . 's this post.';
      }
      else if ($numberOfPeopleWhoArePassionate > 1) {
         return $numberOfPeopleWhoArePassionate . ' people ' . $passionType . ' this post.';
      }
   }
}


function getCommentHeading()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'SELECT comment_id FROM comments_to_blog_posts WHERE blog_post_id = ' . $_GET['i'] . ' ORDER BY time_of_commenting';
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die($globalDatabaseErrorMarkup);
   $numberOfComments = mysqli_num_rows( $result );

   if ($numberOfComments == 1) {
      $commentHeading = '1 Comment';
   }
   else {
      $commentHeading = $numberOfComments . ' Comments';
   }

   if (isset($_GET['viewComments'])) {
      $commentHeading .= ' <a href="blog.php?i=' . $_GET['i'] . '#commentArea" class="btn btn-link"><span class="glyphicon glyphicon-eye-close"></span> Hide</a>';
   }
   else if ($numberOfComments > 0) {
      $commentHeading .= ' <a href="blog.php?i=' . $_GET['i'] . '&viewComments=1#commentArea" class="btn btn-link"><span class="glyphicon glyphicon-eye-open"></span> View</a>';
   }

   return $commentHeading;
}


function displayCommentsOfCurrentBlogPost()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'SELECT comment_id, comment_text, user_id_of_commenter, firstname_of_commenter, MONTHNAME( time_of_commenting ) AS month_of_commenting, DAYOFMONTH( time_of_commenting ) AS day_of_commenting, YEAR( time_of_commenting ) AS year_of_commenting, HOUR( time_of_commenting ) AS hour_of_commenting, MINUTE( time_of_commenting ) AS minute_of_commenting FROM comments_to_blog_posts WHERE blog_post_id = ' . $_GET['i'] . ' ORDER BY time_of_commenting';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup);
	
	for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
?>
                  <div id ="<?php echo $row['user_id_of_commenter'] == $_SESSION['user_id'] ? 'containerHoldingCommentBoxFloatedToTheRight' : 'containerHoldingCommentBoxFloatedToTheLeft' ?>">
                     <div id ="<?php echo $row['user_id_of_commenter'] == $_SESSION['user_id'] ? 'commentBoxFloatedToTheRight' : 'commentBoxFloatedToTheLeft' ?>">
                        <p id="<?php echo $row['comment_id'] ?>"><?php echo ucwords( $row['firstname_of_commenter'] ) . ',' ?> <span id="tinySizedText"><?php echo 'at ' . formatTimeAsAmOrPm( $row['hour_of_commenting'] + 5, $row['minute_of_commenting'] ) . ' on ' . substr($row['month_of_commenting'], 0, 3) . ' ' . $row['day_of_commenting'] . ', ' . $row['year_of_commenting'] ?></span></p>
                        <p><?php echo $row['comment_text'] ?></p>
                     </div>
                  </div>

<?php
	}
}


function displayBlogPostsThatAreRelatedToCurrentBlogPost()
{
   global $globalBlogPostsDisplayedInCurrentPage;

   $numberOfBlogPostsDisplayedSoFar = 0;
   $dataAboutRelatedBlogPosts = getArrayOfDataAboutBlogPostsThatAreRelatedToCurrentBlogPost();

   foreach ($dataAboutRelatedBlogPosts as $blogPost) {
      if ($blogPost['blog_post_id'] != $_GET['i']) {
         displayBlogPostAsSummary($blogPost);
         $globalBlogPostsDisplayedInCurrentPage[] = $blogPost;
         $numberOfBlogPostsDisplayedSoFar++;
      }

      if ($numberOfBlogPostsDisplayedSoFar == MAXIMUM_NUMBER_OF_RELATED_BLOG_POSTS_TO_DISPLAY) {
         return;
      }
   }

   $dataAboutInterestingBlogPosts = getArrayOfDataAboutInterestingBlogPosts(MAXIMUM_NUMBER_OF_RELATED_BLOG_POSTS_TO_DISPLAY - $numberOfBlogPostsDisplayedSoFar);

   foreach ($dataAboutInterestingBlogPosts as $blogPost) {
      if (blogPostIsNotInArray($blogPost, $dataAboutRelatedBlogPosts) && $blogPost['blog_post_id'] != $_GET['i']) {
         displayBlogPostAsSummary($blogPost);
         $globalBlogPostsDisplayedInCurrentPage[] = $blogPost;
         $numberOfBlogPostsDisplayedSoFar++;
      }

      if ($numberOfBlogPostsDisplayedSoFar == MAXIMUM_NUMBER_OF_RELATED_BLOG_POSTS_TO_DISPLAY) {
         return;
      }
   }
}


function getArrayOfDataAboutBlogPostsThatAreRelatedToCurrentBlogPost()
{
   $result = getResultContainingDataAboutBlogPostsThatAreRelatedToCurrentBlogPost();
   return getArrayOfBlogPostDataFetchedFromResult($result);
}


function getResultContainingDataAboutBlogPostsThatAreRelatedToCurrentBlogPost()
{
   $tagIdsOfCurrentBlogPost = getArrayOfTagIdsOfCurrentBlogPost();
   return getResultContainingDataAboutBlogPostsUsingTags($tagIdsOfCurrentBlogPost, MAXIMUM_NUMBER_OF_RELATED_BLOG_POSTS_TO_DISPLAY);
}


function getArrayOfTagIdsOfCurrentBlogPost()
{
   return getArrayOfTagIdsOfBlogPosts(array($_GET['i']));
}


function displayBlogPostAsSummary($rowContainingRelatedBlogPost)
{
?>

               <div class="col-sm-6">
                  <a href="blog.php?i=<?php echo $rowContainingRelatedBlogPost['blog_post_id'] ?>" class="container-fluid" id="relatedBlogPost">
<?php
   if ($rowContainingRelatedBlogPost['blog_post_image_filename'] != NULL) {
?>
                     <img src="assets/images/blogImages//<?php echo $rowContainingRelatedBlogPost['blog_post_image_filename'] ?>" width="auto" height="50px" id="relatedBlogImage" />
<?php
   }
?>
                     <h3 id="relatedBlogCaption"><?php echo $rowContainingRelatedBlogPost['blog_post_caption'] ?></h3>
                  </a>
               </div>
<?php
}
?>