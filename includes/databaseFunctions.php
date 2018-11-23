<?php
require_once 'performBasicInitializations.php';

function getDataAboutApprovedBlogPost($blogPostId)
{
   global $db;
   $query = 'SELECT *, MONTHNAME( blog_post_time_of_posting ) AS month_of_posting, DAYOFMONTH( blog_post_time_of_posting ) AS day_of_posting, YEAR( blog_post_time_of_posting ) AS year_of_posting FROM blog_posts WHERE blog_post_id = "' . $blogPostId . '" AND blog_post_approval_status = "APPROVED"';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   return mysqli_fetch_assoc($result);
}


function getFirstNameAssociatedWithUserId($userId)
{
   global $db;
   $query = 'SELECT firstname FROM users WHERE id = "' . $userId . '"';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   return $row == NULL ? '' : $row['firstname'];
}


function userHasNeverViewedThisBlogPost($blogPostId)
{
   global $db;
   $query = 'SELECT * FROM views_to_blog_posts WHERE user_id_of_viewer = "' . $_SESSION['user_id'] . '" AND blog_post_id = "' . $blogPostId . '"';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   return mysqli_num_rows($result) == 0;
}


function userHasPreviouslyViewedThisBlogPost($blogPostId)
{
   return !userHasNeverViewedThisBlogPost($blogPostId);
}


function getMetadataTable($metadata)
{
   if ($metadata == BLOGPOST_LIKES) {
      return 'likes_to_blog_posts';
   }
   else if ($metadata == BLOGPOST_LOVES) {
      return 'loves_to_blog_posts';
   }
   else if ($metadata == BLOGPOST_VIEWS) {
      return 'views_to_blog_posts';
   }

   return '';
}


function getMetadataUserIdColumn($metadata)
{
   if ($metadata == BLOGPOST_LIKES) {
      return 'user_id_of_liker';
   }
   else if ($metadata == BLOGPOST_LOVES) {
      return 'user_id_of_lover';
   }
   else if ($metadata == BLOGPOST_VIEWS) {
      return 'user_id_of_viewer';
   }

   return '';
}
?>