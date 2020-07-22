<?php
require_once 'includes/generalHeaderFile.php';

if ( !currentUserIsLoggedInAsAdmin() ) {
   header( 'Location: index.php' );
}

if ( !isset( $_GET['idOfItem'] ) || !consistsOfOnlyDigits( $_GET['idOfItem'] ) ) {
   header( 'Location: index.php' );
}

if ( !isset( $_GET['requiredAction'] ) && !isset( $_GET['actionPerformed'] ) ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['requiredAction'] ) && ( $_GET['requiredAction'] != 'performAdminApproval' && $_GET['requiredAction'] != 'performAdminUnapproval' && $_GET['requiredAction'] != 'performAdminDeletion' ) ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['actionPerformed'] ) && ( $_GET['actionPerformed'] != 'adminApprovalPerformedSuccessfully' && $_GET['actionPerformed'] != 'adminUnapprovalPerformedSuccessfully' && $_GET['actionPerformed'] != 'adminDeletionPerformedSuccessfully' ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['requiredAction'] == 'performAdminApproval' || $_GET['actionPerformed'] == 'adminApprovalPerformedSuccessfully' ) {
   $requiredActionInConciseForm = 'Approve';
   $requiredActionInConciseNounForm = 'Approval';
}
else if ( $_GET['requiredAction'] == 'performAdminUnapproval' || $_GET['actionPerformed'] == 'adminUnapprovalPerformedSuccessfully' ) {
   $requiredActionInConciseForm = 'Unapprove';
   $requiredActionInConciseNounForm = 'Unapproval';
}
else if ( $_GET['requiredAction'] == 'performAdminDeletion' || $_GET['actionPerformed'] == 'adminDeletionPerformedSuccessfully' ) {
   $requiredActionInConciseForm = 'Delete';
   $requiredActionInConciseNounForm = 'Deletion';
}

$query = 'SELECT * FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
$resultContainingItemData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$rowContainingItemData = mysqli_fetch_assoc( $resultContainingItemData );

if ( isset( $_GET['requiredAction'] ) ) {
   if ( !isset( $_GET['confirmation'] ) ) {  // neither "Yes" nor "No" buttons have been clicked
      displayMarkupsCommonToTopOfPages( $requiredActionInConciseForm . ' Item', DISPLAY_NAVIGATION_MENU, 'perform_administrative_action_on_item.php' );
      $directory = 'assets/images/uploaded' . ucwords( $rowContainingItemData['category'] ) . 'Snapshots';
?>

            <div id="containerHoldingErrorMessage">
               <h2><?php echo $requiredActionInConciseForm ?> Item</h2>
               <p>Are you sure you want to <?php echo strtolower( $requiredActionInConciseForm ) ?> this item?</p>

               <img src="<?php echo $directory . '/' . $rowContainingItemData['people_id'] . '@' . $rowContainingItemData['image_size'] ?>" alt="Snapshot of <?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?>" width="auto" height="100px" id="floatedToTheLeftAndHasMarginOnLargeScreens" />

               <ul>
                  <li><span id="boldSmallSizedText">Name of Item:</span> <?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?></li>
                  <li><span id="boldSmallSizedText">Description of item:</span> <?php echo $rowContainingItemData['brief_descripition'] ?></li>
                  <li><span id="boldSmallSizedText">Category:</span> <?php echo ucwords( $rowContainingItemData['category'] ) ?></li>
                  <li><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingItemData['price'] ?></li>
                  <li><span id="boldSmallSizedText">Negotiable?</span> <?php echo strtoupper( $rowContainingItemData['negotiable'] ) ?></li>
                  <li><span id="boldSmallSizedText">Status:</span> <?php echo ucwords( strtolower( $rowContainingItemData['checks'] ) ) ?></li>
               </ul>

               <form method="GET" action="perform_administrative_action_on_item.php">
                  <input type="hidden" name="requiredAction" value="<?php echo $_GET['requiredAction'] ?>" />
                  <input type="hidden" name="idOfItem" value="<?php echo $_GET['idOfItem'] ?>" />

                  <input type="submit" name="confirmation" value="Yes" class="btn btn-danger btn-lg" id="tinyMargin" />
                  <input type="submit" name="confirmation" value="No" class="btn btn-danger btn-lg" id="tinyMargin" />
               </form>
            </div>
<?php
      displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   }
   else if ( $_GET['confirmation'] == 'Yes' ) {  // User clicked "Yes" button

      if ( $_GET['requiredAction'] == 'performAdminApproval' || $_GET['requiredAction'] == 'performAdminUnapproval' ) {
         $urlOfPageWhereActionWillBePerformed = 'approve_or_unapprove_item.php';
      }
      else if ( $_GET['requiredAction'] == 'performAdminDeletion' ) {
         $urlOfPageWhereActionWillBePerformed = 'delete_item.php';
      }

      if ( $_GET['requiredAction'] == 'performAdminApproval' ) {
         header( 'Location: ' . $urlOfPageWhereActionWillBePerformed . '?requiredAction=' . $_GET['requiredAction'] . '&idOfItem=' . $_GET['idOfItem'] . '&category=' . $rowContainingItemData['category'] );
      }
      else {
         displayMarkupsCommonToTopOfPages( $requiredActionInConciseForm . ' Item', DISPLAY_NAVIGATION_MENU, 'perform_administrative_action_on_item.php' );
         $directory = 'assets/images/uploaded' . ucwords( $rowContainingItemData['category'] ) . 'Snapshots';
?>

            <div class="panel panel-primary jumbotron" id="noPaddingOnSmallScreens">
               <h2 class="panel-heading text-center">You requested to <?php echo strtolower( $requiredActionInConciseForm ) ?> the following item:</h2>

               <div class="panel-body">
                  <img src="<?php echo $directory . '/' . $rowContainingItemData['people_id'] . '@' . $rowContainingItemData['image_size'] ?>" alt="Snapshot of <?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?>" width="auto" height="100px" id="floatedToTheLeftAndHasMarginOnLargeScreens" />

                  <ul class="text-left">
                     <li id="tinyPadding"><span id="boldSmallSizedText">Name of Item:</span> <?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Description of item:</span> <?php echo $rowContainingItemData['brief_descripition'] ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Category:</span> <?php echo ucwords( $rowContainingItemData['category'] ) ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingItemData['price'] ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Negotiable?</span> <?php echo strtoupper( $rowContainingItemData['negotiable'] ) ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Status:</span> <?php echo ucwords( strtolower( $rowContainingItemData['checks'] ) ) ?></li>
                  </ul>

                  <form method="GET" action="<?php echo $urlOfPageWhereActionWillBePerformed ?>" class="form-vertical" id="notFloating">
                     <input type="hidden" name="requiredAction" value="<?php echo $_GET['requiredAction'] ?>" />
                     <input type="hidden" name="idOfItem" value="<?php echo $_GET['idOfItem'] ?>" />
                     <input type="hidden" name="category" value="<?php echo ucwords( $rowContainingItemData['category'] ) ?>" />

                     <label for="reason" id="mediumSizedText">Why do you want to <?php echo strtolower( $requiredActionInConciseForm ) ?> this item?</label>
                     <p class="help-block" id="smallSizedText">It is necessary that you specify your reason so that the uploader of this item can perform the neccessary actions to avoid <?php echo strtolower( $requiredActionInConciseNounForm ) ?> next time.</p>
                     <input type="text" name="reason" class="form-control" id="reason" placeholder="Type your reason here..." required autofocus />
                     <button type="submit" class="btn btn-primary"><?php echo $requiredActionInConciseForm ?> Item</button>
                  </form>
               </div>
            </div>
<?php
         displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
      }

   }
   else if ( $_GET['confirmation'] == 'No' ) { // User Clicked "No" button
      header( 'Location: all_roarconnect_uploads.php?type=' . ucwords( strtolower( $rowContainingItemData['checks'] ) ) . '#' . $_GET['idOfItem'] );
   }
}
else if ( isset( $_GET['actionPerformed'] ) ) {
   displayMarkupsCommonToTopOfPages( $requiredActionInConciseForm . ' Item', DISPLAY_NAVIGATION_MENU, 'perform_administrative_action_on_item.php' );
   $directory = 'assets/images/uploaded' . ucwords( $rowContainingItemData['category'] ) . 'Snapshots';
?>
            <div id="containerHoldingSuccessMessage">
               <h2>Successfully <?php echo $requiredActionInConciseForm ?>d Item</h2>
               <p>The required item has been successfully <?php echo strtolower( $requiredActionInConciseForm ) ?>d.</p>

<?php
   if ( $_GET['actionPerformed'] == 'adminDeletionPerformedSuccessfully' ) {
      $userIdOfRecipient = uploaderOfItemIsAVendor( $_GET['idOfItemUploader'] ) ? getUserIdOfManagerOfVendor( $_GET['idOfItemUploader'] ) : $_GET['idOfItemUploader'];
      $notificationURL = uploaderOfItemIsAVendor( $_GET['idOfItemUploader'] ) ? 'your_uploads_as_manager_of_vendor.php' : 'your_upload.php?category=' . ucwords( $_GET['category'] );
      
      $query = 'INSERT INTO notifications( notification_text, user_id_of_recipient, notification_time_of_notifying, notification_url, reason_for_notification, id_of_item ) VALUES( "Your ' . ucwords( $_GET['category'] ) . ' Upload have been ' . $requiredActionInConciseForm . 'd", ' . $userIdOfRecipient . ', NOW(), "' . $notificationURL . '", "DELETION OF PHOTO UPLOAD", ' . $_GET['idOfItem'] . ' )';
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

?>
               <p><a href="all_roarconnect_uploads.php?type=<?php echo $_GET['type'] ?>" class="btn btn-default btn-sm">&lt;&lt; Click Here</a> to go back to the <?php echo $_GET['type'] ?> Items Page.</p>
<?php
   }
   else {
      $userIdOfRecipient = uploaderOfItemIsAVendor( $rowContainingItemData['people_id'] ) ? getUserIdOfManagerOfVendor( $rowContainingItemData['people_id'] ) : $rowContainingItemData['people_id'];
      $notificationURL = uploaderOfItemIsAVendor( $rowContainingItemData['people_id'] ) ? 'your_uploads_as_manager_of_vendor.php' : 'your_upload.php?category=' . ucwords( $rowContainingItemData['category'] );
      $reasonForNotification = $_GET['actionPerformed'] == 'adminApprovalPerformedSuccessfully' ? 'APPROVAL OF PHOTO UPLOAD' : 'UNAPPROVAL OF PHOTO UPLOAD';
      
      $query = 'INSERT INTO notifications( notification_text, user_id_of_recipient, notification_time_of_notifying, notification_url, reason_for_notification, id_of_item ) VALUES( "Your ' . ucwords( $rowContainingItemData['category'] ) . ' Upload have been ' . $requiredActionInConciseForm . 'd", ' . $userIdOfRecipient . ', NOW(), "' . $notificationURL . '", "' . $reasonForNotification . '", ' . $_GET['idOfItem'] . ' )';
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
?>
               <img src="<?php echo $directory . '/' . $rowContainingItemData['people_id'] . '@' . $rowContainingItemData['image_size'] ?>" alt="Snapshot of <?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?>" width="auto" height="100px" id="floatedToTheLeftAndHasMarginOnLargeScreens" />

               <ul>
                  <li><span id="boldSmallSizedText">Name of Item:</span> <?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?></li>
                  <li><span id="boldSmallSizedText">Description of item:</span> <?php echo $rowContainingItemData['brief_descripition'] ?></li>
                  <li><span id="boldSmallSizedText">Category:</span> <?php echo ucwords( $rowContainingItemData['category'] ) ?></li>
                  <li><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingItemData['price'] ?></li>
                  <li><span id="boldSmallSizedText">Negotiable?</span> <?php echo strtoupper( $rowContainingItemData['negotiable'] ) ?></li>
                  <li><span id="boldSmallSizedText">Status:</span> <?php echo $requiredActionInConciseForm ?>d</li>
               </ul>

               <p>The item can now be found among other <?php echo strtolower( $requiredActionInConciseForm ) ?>d items.</p>
               <p><a href="all_roarconnect_uploads.php?type=<?php echo $requiredActionInConciseForm . 'd#' . $_GET['idOfItem'] ?>" class="btn btn-default btn-sm">Click Here</a> to view the list of <?php echo strtolower( $requiredActionInConciseForm ) ?>d items.</p>
<?php
   }
?>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}


function uploaderOfItemIsAVendor( $idOfItemUploader )
{
   return preg_match( '/VENDOR_/', $idOfItemUploader );
}


function getUserIdOfManagerOfVendor( $idOfItemUploader )
{
   global $globalHandleToDatabase;
   
   if ( uploaderOfItemIsAVendor( $idOfItemUploader ) ) {
      $idOfVendor = substr( $idOfItemUploader, 7 );
      $query = 'SELECT user_id_of_vendor_manager FROM vendors WHERE vendor_id = ' . $idOfVendor;
      $resultContainingDataAboutVendorManager = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $rowContainingDataAboutVendorManager = mysqli_fetch_assoc( $resultContainingDataAboutVendorManager );
      return $rowContainingDataAboutVendorManager['user_id_of_vendor_manager'];
   }

   return $idOfItemUploader;
}
?>