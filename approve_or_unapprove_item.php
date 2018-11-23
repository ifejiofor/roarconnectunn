<?php
   require_once 'includes/generalHeaderFile.php';

   if ( !userIsLoggedInAsAdmin() ) {
      header( 'Location: index.php' );
   }

   if ( !isset( $_GET['requiredAction'] ) || ( $_GET['requiredAction'] != 'performAdminApproval' && $_GET['requiredAction'] != 'performAdminUnapproval' ) ) {
      header( 'Location: index.php' );
   }

   if ( !isset( $_GET['idOfItem'] ) || !consistsOfOnlyDigits( $_GET['idOfItem'] ) ) {
      header( 'Location: index.php' );
   }

   if ( $_GET['requiredAction'] == 'performAdminApproval' ) {
      $query = 'UPDATE photo_upload SET checks = "APPROVED" WHERE id_new = ' . $_GET['idOfItem'];
      mysqli_query( $db, $query );
      header( 'Location: perform_administrative_action_on_item.php?actionPerformed=adminApprovalPerformedSuccessfully&idOfItem=' . $_GET['idOfItem'] );
   }
   else if ( $_GET['requiredAction'] == 'performAdminUnapproval' ) {
      $query = 'UPDATE photo_upload SET checks = "UNAPPROVED" WHERE id_new = ' . $_GET['idOfItem'];
      mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
      
      $query = 'DELETE FROM reasons_for_admin_actions_on_items WHERE type_of_item = "PHOTO UPLOAD" AND id_of_item = ' . $_GET['idOfItem'];
      mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

      $query = 'INSERT INTO reasons_for_admin_actions_on_items( type_of_item, id_of_item, reason ) VALUES( "PHOTO UPLOAD", ' . $_GET['idOfItem'] . ', "' . trim( htmlentities( $_GET['reason'] ) ) . '")';
      mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

      header( 'Location: perform_administrative_action_on_item.php?actionPerformed=adminUnapprovalPerformedSuccessfully&idOfItem=' . $_GET['idOfItem'] );
   }
?>