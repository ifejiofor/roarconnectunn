<?php
   require_once 'includes/generalHeaderFile.php';

   if ( !isset( $_GET['requiredAction'] ) || ( $_GET['requiredAction'] != 'performAdminApproval' && $_GET['requiredAction'] != 'performAdminUnapproval' ) ) {
      header( 'Location: index.php' );
   }

   if ( !isset( $_GET['idOfBlogPost'] ) || !consistsOfOnlyDigits( $_GET['idOfBlogPost'] ) ) {
      header( 'Location: index.php' );
   }
   
   $query = 'SELECT user_id_of_poster, blog_category_id, blog_post_approval_status, blog_post_image_filename FROM blog_posts WHERE blog_post_id = ' . $_GET['idOfBlogPost'];
   $resultContainingBlogPostData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingBlogPostData = mysqli_fetch_assoc( $resultContainingBlogPostData );

   if ( $rowContainingBlogPostData['user_id_of_poster'] != $_SESSION['user_id'] &&
      !isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogPostData['blog_category_id'] ) )
   {
      header( 'Location: index.php' );
   }

   $typeOfBlogPost = ucwords( strtolower( $rowContainingBlogPostData['blog_post_approval_status'] ) );
   
   $query = 'DELETE FROM comments_to_blog_posts WHERE blog_post_id = ' . $_GET['idOfBlogPost'];
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   
   $query = 'DELETE FROM blog_posts WHERE blog_post_id = ' . $_GET['idOfBlogPost'];
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   
   $query = 'SELECT blog_category_name FROM blog_categories WHERE blog_category_id = ' . $rowContainingBlogPostData['blog_category_id'];
   $resultContainingBlogCategoryData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingBlogCategoryData = mysqli_fetch_assoc( $resultContainingBlogCategoryData );
   
   $filePathOfBlogPostImage = 'assets/images/ImagesFor' . $rowContainingBlogCategoryData['blog_category_name'] . 'Updates/' . $rowContainingBlogPostData['blog_post_image_filename'];
   unlink( $filePathOfBlogPostImage );
   
   $query = 'DELETE FROM reasons_for_admin_actions_on_items WHERE type_of_item = "BLOG POST" AND id_of_item = ' . $_GET['idOfBlogPost'];
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      
   $query = 'INSERT INTO reasons_for_admin_actions_on_items( type_of_item, id_of_item, reason ) VALUES( "BLOG POST", ' . $_GET['idOfBlogPost'] . ', "' . trim( htmlentities( $_GET['reason'] ) ) . '")';
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   
   header( 'Location: perform_administrative_action_on_blog_post.php?actionPerformed=adminDeletionPerformedSuccessfully&idOfBlogPost=' . $_GET['idOfBlogPost'] . '&type=' . $typeOfBlogPost . '&category=' . $rowContainingBlogCategoryData['blog_category_name'] . '&idOfPoster=' . $rowContainingBlogPostData['user_id_of_poster'] . ( isset( $_GET['urlOfSourcePage'] ) ? '&urlOfSourcePage=' . $_GET['urlOfSourcePage'] : '' ) );
?>