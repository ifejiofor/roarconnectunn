<?php
   require_once 'includes/generalHeaderFile.php';

   if ( !isset( $_GET['requiredAction'] ) || ( $_GET['requiredAction'] != 'performAdminApproval' && $_GET['requiredAction'] != 'performAdminUnapproval' ) ) {
      header( 'Location: index.php' );
   }

   if ( !isset( $_GET['idOfBlogPost'] ) || !consistsOfOnlyDigits( $_GET['idOfBlogPost'] ) ) {
      header( 'Location: index.php' );
   }
   
   $query = 'SELECT blog_category_id FROM blog_posts WHERE blog_post_id = ' . $_GET['idOfBlogPost'];
   $resultContainingBlogPostData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingBlogPostData = mysqli_fetch_assoc( $resultContainingBlogPostData );

   if ( !isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogPostData['blog_category_id'] ) ) {
      header( 'Location: index.php' );
   }

   if ( $_GET['requiredAction'] == 'performAdminApproval' ) {
      $query = 'UPDATE blog_posts SET blog_post_approval_status = "APPROVED" WHERE blog_post_id = ' . $_GET['idOfBlogPost'];
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      header( 'Location: perform_administrative_action_on_blog_post.php?actionPerformed=adminApprovalPerformedSuccessfully&idOfBlogPost=' . $_GET['idOfBlogPost'] );
   }
   else if ( $_GET['requiredAction'] == 'performAdminUnapproval' ) {
      $query = 'UPDATE blog_posts SET blog_post_approval_status = "UNAPPROVED" WHERE blog_post_id = ' . $_GET['idOfBlogPost'];
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      
      $query = 'DELETE FROM reasons_for_admin_actions_on_items WHERE type_of_item = "BLOG POST" AND id_of_item = ' . $_GET['idOfBlogPost'];
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

      $query = 'INSERT INTO reasons_for_admin_actions_on_items( type_of_item, id_of_item, reason ) VALUES( "BLOG POST", ' . $_GET['idOfBlogPost'] . ', "' . trim( htmlentities( $_GET['reason'] ) ) . '")';
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      
      header( 'Location: perform_administrative_action_on_blog_post.php?actionPerformed=adminUnapprovalPerformedSuccessfully&idOfBlogPost=' . $_GET['idOfBlogPost'] );
   }
?>