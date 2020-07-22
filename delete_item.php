<?php
require_once 'includes/generalHeaderFile.php';

if ( !currentUserIsLoggedIn() ) {
   header('Location: index.php');
}

if ( !isset( $_GET['category'] ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['category'] != 'Books' && $_GET['category'] != 'Gadgets' && $_GET['category'] != 'Wears' && $_GET['category'] != 'Rooms' &&
   $_GET['category'] != 'Painting' && $_GET['category'] != 'Catering' && $_GET['category'] != 'GraphicDesigning' && $_GET['category'] != 'ElectricalWorks' )
{
   header( 'Location:index.php' );
}

if ( isset( $_GET['requiredAction'] ) && isset( $_GET['idOfItem'] ) && $_GET['requiredAction'] == 'performAdminDeletion' ) {
   if ( !currentUserIsLoggedInAsAdmin() ) {
      header( 'Location: index.php' );
   }

   if ( !consistsOfOnlyDigits( $_GET['idOfItem'] ) ) {
      header( 'Location: index.php' );
   }

   $query = 'SELECT category, checks FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
   $result = mysqli_query( $globalHandleToDatabase, $query );
   $row = mysqli_fetch_assoc( $result );
   if ( $row['category'] != strtolower( $_GET['category'] ) ) {
      header( 'Location: index.php' );
   }

   $typeOfItem = ucwords( strtolower( $row['checks'] ) );
}

if ( isset( $_GET['deleteItemForVendor'] ) && isset( $_GET['idOfItem'] ) && isset( $_GET['idOfVendor'] ) ) {
   if ( !consistsOfOnlyDigits( $_GET['idOfItem'] ) || !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
     header( 'Location: index.php' );
   }

   $query = 'SELECT user_id_of_vendor_manager FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
   $result = mysqli_query( $globalHandleToDatabase, $query );
   $row = mysqli_fetch_assoc( $result );
   if ( $row['user_id_of_vendor_manager'] != $_SESSION['user_id'] ) {
      header( 'Location: index.php' );
   }

   $query = 'SELECT people_id, category FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
   $result = mysqli_query( $globalHandleToDatabase, $query );
   $row = mysqli_fetch_assoc( $result );
   if ( $row['people_id'] != 'VENDOR_' . $_GET['idOfVendor'] || $row['category'] != strtolower( $_GET['category'] ) ) {
      header( 'Location: index.php' );
   }
}

if ( isset( $_GET['requiredAction'] ) && $_GET['requiredAction'] == 'performAdminDeletion' ) {
   $query = 'SELECT people_id FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $row = mysqli_fetch_assoc( $result );
   $idOfItemUploader = $row['people_id'];
   $query = 'SELECT image_size FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
}
else if ( isset( $_GET['deleteItemForVendor'] ) ) {
   $idOfItemUploader = 'VENDOR_' . $_GET['idOfVendor'];
   $query = 'SELECT image_size FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
}
else {
   $idOfItemUploader = $_SESSION['user_id'];
   $query = 'SELECT image_size FROM photo_upload WHERE people_id = ' . $_SESSION['user_id'] . ' AND category = "' . $_GET['category'] . '"';
}

$result = mysqli_query( $globalHandleToDatabase, $query );
$row = mysqli_fetch_assoc( $result );
$filePathOfItemSnapshot = 'assets/images/uploaded' . ucwords( $_GET['category'] ) . 'Snapshots/' . $idOfItemUploader . '@' . $row['image_size'];

if ( ( isset( $_GET['requiredAction'] ) && $_GET['requiredAction'] == 'performAdminDeletion' ) || isset( $_GET['deleteItemForVendor'] ) ) {
   $query = 'DELETE FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
}
else {
   $query="DELETE FROM `photo_upload` WHERE `people_id`= '".$_SESSION['user_id']."' AND `Category`= '" . $_GET['category'] . "'";
}

mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
unlink( $filePathOfItemSnapshot );

if ( isset( $_GET['requiredAction'] ) && $_GET['requiredAction'] == 'performAdminDeletion' ) {
   $query = 'DELETE FROM reasons_for_admin_actions_on_items WHERE type_of_item = "PHOTO UPLOAD" AND id_of_item = ' . $_GET['idOfItem'];
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      
   $query = 'INSERT INTO reasons_for_admin_actions_on_items( type_of_item, id_of_item, reason ) VALUES( "PHOTO UPLOAD", ' . $_GET['idOfItem'] . ', "' . trim( htmlentities( $_GET['reason'] ) ) . '")';
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   
   header( 'Location: perform_administrative_action_on_item.php?actionPerformed=adminDeletionPerformedSuccessfully&idOfItem=' . $_GET['idOfItem'] . '&type=' . $typeOfItem . '&category=' . $_GET['category'] . '&idOfItemUploader=' . $idOfItemUploader );
}
else if ( isset( $_GET['deleteItemForVendor'] ) ) {
   header( 'Location: your_uploads_as_manager_of_vendor.php' );
}
else {
   header( 'Location: your_upload.php?category=' . $_GET['category'] );
}
?>