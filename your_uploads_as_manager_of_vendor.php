<?php
require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

if ( !userIsLoggedIn() ) {
   header( 'Location: index.php' );
}

$query = 'SELECT vendor_id, vendor_name, vendor_category FROM vendors WHERE user_id_of_vendor_manager = ' . $_SESSION['user_id'];
$resultContainingDataAboutVendor = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
if ( mysqli_num_rows( $resultContainingDataAboutVendor ) == 0 ) {
   header( 'Location: index.php' );
}

displayMarkupsCommonToTopOfPages( 'Your Vendor Uploads', DISPLAY_NAVIGATION_MENU, 'your__uploads_as_manager_of_vendor.php' );
?>
            <header id="minorHeader">
               <h2>Welcome to RoarConnect Vendor Management Portal</h2>
               <p>As a manager, this is the place where you can manage your vendor's RoarConnect store outlet.</p>
            </header>
<?php
$rowContainingDataAboutVendor = mysqli_fetch_assoc( $resultContainingDataAboutVendor );

while ( $rowContainingDataAboutVendor != NULL ) {
   if ( $rowContainingDataAboutVendor['vendor_category'] == 'gadgets' ) {
      $descriptionOfItemSold = 'gadgets that are up for sale';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'wears' ) {
      $descriptionOfItemSold = 'wears that are up for sale';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'books' ) {
      $descriptionOfItemSold = 'books that are for sale';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'rooms' ) {
      $descriptionOfItemSold = 'hostel bedspaces and rooms that are up for rent';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'catering' ) {
      $descriptionOfItemSold = 'cakes, snacks, and other catering jobs you have done';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'electricalWorks' ) {
      $descriptionOfItemSold = 'electrical jobs you have done';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'graphicsDesigning' ) {
      $descriptionOfItemSold = 'graphics designing and video editing jobs you have done';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'painting' ) {
      $descriptionOfItemSold = 'painting jobs you have done';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'foods' ) {
      $descriptionOfItemSold = 'foods, drinks, or desserts you sell';
   }
?>

            <section id="wideContainerWithBorder">
               <header id="minorHeaderType2">
                  <h3>You are the manager of <?php echo $rowContainingDataAboutVendor['vendor_name'] ?></h3>
                  <p><a href="upload_item.php?uploadItemForVendor&idOfVendor=<?php echo $rowContainingDataAboutVendor['vendor_id'] ?>" class="btn btn-default btn-sm">Click Here</a> to upload <?php echo $descriptionOfItemSold ?> at <?php echo $rowContainingDataAboutVendor['vendor_name'] ?></p>
               </header>
<?php
   $query = 'SELECT id_new, name_of_item, brief_descripition, price, negotiable, image_size, category, checks FROM photo_upload WHERE people_id = "VENDOR_' . $rowContainingDataAboutVendor['vendor_id'] . '"';
   $resultContainingDataAboutUpload = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if ( mysqli_num_rows( $resultContainingDataAboutUpload ) == 0 ) {
?>
               <p id="mediumSizedText">You have not uploaded anything yet for <?php echo $rowContainingDataAboutVendor['vendor_name'] ?></p>
<?php
   }
   else {
?>

               <h4 class="text-center" id="boldSmallSizedText">Below are the current uploads you have made for <?php echo $rowContainingDataAboutVendor['vendor_name'] ?></h4>
<?php
      $rowContainingDataAboutUpload = mysqli_fetch_assoc( $resultContainingDataAboutUpload );
      while ( $rowContainingDataAboutUpload != NULL ) {
         $filePathOfSnapshot = 'images/uploaded' . ucwords( $rowContainingDataAboutUpload['category'] ) . 'Snapshots/VENDOR_' . $rowContainingDataAboutVendor['vendor_id'] . '@' . $rowContainingDataAboutUpload['image_size'];
?>

               <div id="looksLikeASmallPaperCard">
                  <div id="headerOfPaperCard">
                     <img src ="<?php echo $filePathOfSnapshot ?>" alt="<?php echo 'Snapshot of ' . $rowContainingDataAboutUpload['name_of_item'] ?>" />
                     <h4><?php echo ucwords( $rowContainingDataAboutUpload['name_of_item'] ) ?></h4>
                  </div>
                  <div id="bodyOfPaperCard">
                     <p><span id="boldSmallSizedText">Description:</span> <?php echo $rowContainingDataAboutUpload['brief_descripition'] ?></p>
                     <p><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingDataAboutUpload['price'] ?></p>
                     <p><span id="boldSmallSizedText">Category:</span> <?php echo getCategoryInSentenceForm( $rowContainingDataAboutUpload['category'] ) ?></p>
                     <p id="tinySizedText"><?php if ( $rowContainingDataAboutUpload['checks'] == 'APPROVED' ) { echo 'Approved by RoarConnect\'s admin.'; } else if ( $rowContainingDataAboutUpload['checks'] == 'UNAPPROVED' ) { echo 'Your upload was not approved by RoarConnect\'s admin. <a href="#" data-toggle="modal" data-target="#reasonForAdminAction' . $rowContainingDataAboutUpload['id_new'] . '">Click Here</a> to know why.'; } else { echo 'Waiting for approval by RoarConnect\'s admin. Don\'t worry this upload will be approved as soon as it is confirmed genuine.'; } ?></p>
                     <div class="text-center" id="tinyMargin">
                        <a href="edit_item.php?editItemForVendor&idOfItem=<?php echo $rowContainingDataAboutUpload['id_new'] ?>&idOfVendor=<?php echo $rowContainingDataAboutVendor['vendor_id'] ?>&category=<?php echo ucwords( $rowContainingDataAboutUpload['category'] ) ?>" name="editButton" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                        <a href="delete_item.php?deleteItemForVendor&idOfItem=<?php echo $rowContainingDataAboutUpload['id_new'] ?>&idOfVendor=<?php echo $rowContainingDataAboutVendor['vendor_id'] ?>&category=<?php echo ucwords( $rowContainingDataAboutUpload['category'] ) ?>" name="deleteButton" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                     </div>
                  </div>
               </div>
<?php
         if ( $rowContainingDataAboutUpload['checks'] == 'UNAPPROVED' ) {
            $queryToRetrieveReasonForUnapproval = 'SELECT reason FROM reasons_for_admin_actions_on_items WHERE type_of_item = "PHOTO UPLOAD" AND id_of_item = ' . $rowContainingDataAboutUpload['id_new'];
            $resultContainingReasonForUnapproval = mysqli_query( $db, $queryToRetrieveReasonForUnapproval ) or die( $markupIndicatingDatabaseQueryFailure );
            $rowContainingReasonForUnapproval = mysqli_fetch_assoc( $resultContainingReasonForUnapproval );
            displayMarkupForReasonForAdminActionModal( 'Unapproval', $rowContainingReasonForUnapproval['reason'], 'reasonForAdminAction' . $rowContainingDataAboutUpload['id_new'] );
         }
            
         $rowContainingDataAboutUpload = mysqli_fetch_assoc( $resultContainingDataAboutUpload );
      }
   }
?>

            </section>
<?php
   $rowContainingDataAboutVendor = mysqli_fetch_assoc( $resultContainingDataAboutVendor );
}

displayMarkupscommonToBottomOfPages( DISPLAY_FOOTER );


function getCategoryInSentenceForm( $categoryRetrievedFromDatabase )
{
   if ( $categoryRetrievedFromDatabase == 'electricalWorks' ) {
      return 'electrical works';
   }
   else if ( $categoryRetrievedFromDatabase == 'graphicsDesigning' ) {
      return 'graphics designing and video editing';
   }

   return $categoryRetrievedFromDatabase;
}
?>