<?php
if ( !isset( $_GET['type'] ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['type'] != 'Newly Uploaded' && $_GET['type'] != 'Unapproved' && $_GET['type'] != 'Approved' ) {
   header( 'Location: index.php' );
}

require_once 'includes/generalHeaderFile.php';

if ( $_GET['type'] == 'Newly Uploaded' ) {
   $descriptionOfType = 'newly uploaded items on RoarConnect that have not been either approved or unapproved';
}
else if ( $_GET['type'] == 'Unapproved' ) {
   $descriptionOfType = 'items uploaded on RoarConnect that were unapproved';
}
else if ( $_GET['type'] == 'Approved' ) {
   $descriptionOfType = 'items uploaded on RoarConnect that were approved';
}

displayMarkupsCommonToTopOfPages( $_GET['type'] . ' Items', DISPLAY_NAVIGATION_MENU, 'all_roarconnect_uploads.php' );

if ( !userIsLoggedInAsAdmin() ) {
   session_destroy();
   displayMarkupToIndicateThatAdminLoginIsRequired();
}

?>
            <header id="minorHeader">
               <h2>Manage <?php echo $_GET['type'] ?> Items</h2>
               <p>Here, you can manage all <?php echo $descriptionOfType ?>.</p>
            </header>

            <section id="wideContainerWithBorder">
               <header id="minorHeaderType2">
                  <h3><?php echo $_GET['type'] ?> Items of Various RoarConnect Users</h3>
               </header>
<?php
$query = 'SELECT * FROM photo_upload WHERE checks = "' . strtoupper( $_GET['type'] ) . '" AND people_id NOT LIKE "VENDOR_%" ORDER BY name_of_item, people_id';
$resultContainingItemData = mysqli_query( $db, $query );

if ( mysqli_num_rows( $resultContainingItemData ) == 0 ) {
?>
               <p id="mediumSizedText">No items.</p>
<?php
}
else {
   $rowContainingItemData = mysqli_fetch_assoc( $resultContainingItemData );
   while ( $rowContainingItemData != NULL ) {
      $directory = 'images/uploaded' . ucwords( $rowContainingItemData['category'] ) . 'Snapshots';
      $query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingItemData['people_id'];
      $resultContainingUserData = mysqli_query( $db, $query );
      $rowContainingUserData = mysqli_fetch_assoc( $resultContainingUserData );
?>
               <div id="looksLikeABigPaperCard">
               <div id="<?php echo $rowContainingItemData['id_new'] ?>">
                  <div id="headerOfPaperCard">
                     <a href="<?php echo $directory . '/' . $rowContainingItemData['people_id'] . '@' . $rowContainingItemData['image_size'] ?>"><img src ="<?php echo $directory . '/' . $rowContainingItemData['people_id'] . '@' . $rowContainingItemData['image_size'] ?>" alt="<?php echo 'Snapshot of ' . $rowContainingItemData['name_of_item'] ?>" /></a>
                     <h4><?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?></h4>
                  </div>

                  <div id="bodyOfPaperCard">
                     <p><span id="boldSmallSizedText">Category:</span> <?php echo ucwords( $rowContainingItemData['category'] ) ?></p>
                     <p><span id="boldSmallSizedText">Description:</span> <?php echo $rowContainingItemData['brief_descripition'] ?></p>
                     <p><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingItemData['price'] ?></p>
                     <p><span id="boldSmallSizedText">Negotiable?</span> <?php echo strtoupper( $rowContainingItemData['negotiable'] ) ?></p>
                     <p><span id="boldSmallSizedText">Name of Uploader:</span> <?php echo $rowContainingUserData['firstname'] ?></p>

                     <div class="text-center" id="tinyMargin">
<?php
      if ( $_GET['type'] == 'Newly Uploaded' ) {
?>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminApproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminUnapproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-remove"></span> Unapprove</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminDeletion&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
      }
      if ( $_GET['type'] == 'Unapproved' ) {
?>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminApproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminDeletion&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
      }
      if ( $_GET['type'] == 'Approved' ) {
?>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminUnapproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-remove"></span> Unapprove</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminDeletion&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
      }
?>
                     </div>
                  </div>
               </div>
               </div>
<?php
      $rowContainingItemData = mysqli_fetch_assoc( $resultContainingItemData );
   }
}
?>
            </section>
<?php

$query = 'SELECT vendor_id, vendor_name FROM vendors ORDER BY vendor_name';
$resultContainingVendorData = mysqli_query( $db, $query );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
while ( $rowContainingVendorData != NULL ) {
?>

            <section id="wideContainerWithBorder">
               <header id="minorHeaderType2">
                  <h3><?php echo $_GET['type'] . ' Items of ' . $rowContainingVendorData['vendor_name'] ?></h3>
               </header>
<?php
   $query = 'SELECT * FROM photo_upload WHERE checks = "' . strtoupper( $_GET['type'] ) . '" AND people_id = "VENDOR_' . $rowContainingVendorData['vendor_id'] . '" ORDER BY name_of_item';
   $resultContainingItemData = mysqli_query( $db, $query );
   if ( mysqli_num_rows( $resultContainingItemData ) == 0 ) {
?>
               <p id="mediumSizedText">No items.</p>
<?php
   }
   else {
      $rowContainingItemData = mysqli_fetch_assoc( $resultContainingItemData );
      while ( $rowContainingItemData != NULL ) {
         $directory = 'images/uploaded' . ucwords( $rowContainingItemData['category'] ) . 'Snapshots';
         $query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingItemData['people_id'];
         $resultContainingUserData = mysqli_query( $db, $query );
         $rowContainingUserData = mysqli_fetch_assoc( $resultContainingUserData );
?>
               <div id="looksLikeASmallPaperCard">
               <div id="<?php echo $rowContainingItemData['id_new'] ?>">
                  <div id="headerOfPaperCard">
                     <a href="<?php echo $directory . '/' . $rowContainingItemData['people_id'] . '@' . $rowContainingItemData['image_size'] ?>"><img src ="<?php echo $directory . '/' . $rowContainingItemData['people_id'] . '@' . $rowContainingItemData['image_size'] ?>" alt="<?php echo 'Snapshot of ' . $rowContainingItemData['name_of_item'] ?>" /></a>
                     <h4><?php echo ucwords( $rowContainingItemData['name_of_item'] ) ?></h4>
                  </div>

                  <div id="bodyOfPaperCard">
                     <p><span id="boldSmallSizedText">Category:</span> <?php echo ucwords( $rowContainingItemData['category'] ) ?></p>
                     <p><span id="boldSmallSizedText">Description:</span> <?php echo $rowContainingItemData['brief_descripition'] ?></p>
                     <p><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingItemData['price'] ?></p>
                     <p><span id="boldSmallSizedText">Negotiable?</span> <?php echo strtoupper( $rowContainingItemData['negotiable'] ) ?></p>

                     <div class="text-center" id="tinyMargin">
<?php
         if ( $_GET['type'] == 'Newly Uploaded' ) {
?>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminApproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminUnapproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-remove"></span> Unapprove</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminDeletion&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
         }
         if ( $_GET['type'] == 'Unapproved' ) {
?>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminApproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminDeletion&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
         }
         if ( $_GET['type'] == 'Approved' ) {
?>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminUnapproval&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-remove"></span> Unapprove</a>
                        <a href="perform_administrative_action_on_item.php?requiredAction=performAdminDeletion&idOfItem=<?php echo $rowContainingItemData['id_new'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
         }
?>
                     </div>
                  </div>
               </div>
               </div>
<?php
         $rowContainingItemData = mysqli_fetch_assoc( $resultContainingItemData );
      }
   }
?>
            </section>
<?php
   $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
}

displayMarkupsCommonToBottomOfPages( DISPLAY_NAVIGATION_MENU );
?>